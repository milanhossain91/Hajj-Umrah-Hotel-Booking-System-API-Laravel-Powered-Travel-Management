<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreApplicationRequest;
use App\Http\Requests\UpdateApplicationRequest;
use App\Models\Application;
use App\Models\ApplicationChild;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ApplicationController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = Application::with(['children', 'payments']);

            $limit        = $request->input('limit', 10);
            $applications = $query->paginate((int) $limit);

            return response()->json([
                'success' => true,
                'message' => 'Applications retrieved successfully.',
                'data'    => $applications,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while retrieving applications.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function store(StoreApplicationRequest $request)
    {
        $data = $request->validated();
    
        DB::beginTransaction();
    
        try {
            // Store applicant photo if provided
            if ($request->hasFile('photo')) {
                $data['photo_path'] = $request->file('photo')->store('application-photos', 'public');
            }
    
            // Create the main application record
            $application = Application::create($data);
    
            // Save child records
            if ($request->has('children')) {
                foreach ($request->file('children', []) as $index => $childFiles) {
                    $childData = $request->input("children.$index");
    
                    $child = new ApplicationChild([
                        'name' => $childData['name'] ?? '',
                        'registration_number' => $childData['registration_number'] ?? null,
                        'thana' => $childData['thana'] ?? null,
                    ]);
    
                    if (isset($childFiles['photo_path']) && $childFiles['photo_path']->isValid()) {
                        $child->photo_path = $childFiles['photo_path']->store('child-photos', 'public');
                    }
    
                    $application->children()->save($child);
                }
            }
    
            // Save payment records
            if ($request->has('payments')) {
                foreach ($request->input('payments', []) as $payment) {
                    $application->payments()->create([
                        'payment_method' => $payment['payment_method'] ?? '',
                        'amount' => $payment['amount'] ?? null,
                        'date' => $payment['date'] ?? now()->toDateString(),
                    ]);
                }
            }
    
            // Create user if not already exists
            if (!User::where('email', $request->email)->exists()) {
                User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                ]);
            }
    
            DB::commit();
    
            return response()->json([
                'success' => true,
                'message' => 'Application created successfully.',
                'data' => $application->load(['children', 'payments']),
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
    
            return response()->json([
                'success' => false,
                'message' => 'Failed to create application.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function show(Application $application)
    {
        return response()->json($application->load(['children', 'payments']));
    }

    public function update(UpdateApplicationRequest $request, Application $application)
    {


        $data     = $request->validated();
        $children = json_decode($request->input('children', '[]'), true);
        $payments = json_decode($request->input('payments', '[]'), true);

        DB::beginTransaction();

        try {
            // Update photo if uploaded
            if ($request->hasFile('photo')) {
                if ($application->photo_path) {
                    Storage::disk('public')->delete($application->photo_path);
                }
                $data['photo_path'] = $request->file('photo')->store('application-photos', 'public');
            }

            $application->update($data);

            // Replace children
            $application->children()->delete();
            foreach ($children as $index => $childData) {
                $child = new ApplicationChild([
                    'name'                => $childData['name'] ?? '',
                    'registration_number' => $childData['registration_number'] ?? null,
                    'thana'               => $childData['thana'] ?? null,
                ]);



                if ($request->hasFile("children.$index.photo")) {
                    $childPhoto = $request->file("children.$index.photo");
                    if ($childPhoto->isValid()) {
                        $child->photo_path = $childPhoto->store('child-photos', 'public');
                    }
                }

                $application->children()->save($child);
            }

            // Replace payments
            $application->payments()->delete();
            foreach ($payments as $paymentData) {
                $application->payments()->create([
                    'payment_method' => $paymentData['payment_method'] ?? '',
                    'amount'         => $paymentData['amount'] ?? null,
                    'date'           => $paymentData['date'] ?? now(),
                ]);
            }

            // Conditionally create user if password given and user does not exist
            if ($request->filled('password') && User::where('email', $request->email)->exists()) {
                User::where('email', $request->email)->update([
                    'password' => Hash::make($request->password),
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Application updated successfully.',
                'data'    => $application->load(['children', 'payments']),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Application update failed.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy(Application $application)
    {
        // Delete main photo if exists
        if ($application->photo_path) {
            Storage::disk('public')->delete($application->photo_path);
        }

        // Delete children photos
        foreach ($application->children as $child) {
            if ($child->photo_path) {
                Storage::disk('public')->delete($child->photo_path);
            }
        }

        // Delete all child and payment records explicitly (if not handled by cascade)
        $application->children()->delete();
        $application->payments()->delete();

        // Finally delete the main application record
        $application->delete();

        return response()->json([
            'success' => true,
            'message' => 'Application and associated records deleted successfully.',
        ], 200);
    }
}
