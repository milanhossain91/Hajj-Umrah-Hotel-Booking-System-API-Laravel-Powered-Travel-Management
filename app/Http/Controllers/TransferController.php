<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Transfer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TransferController extends Controller
{
   public function index(Request $request)
    {
        $limit  = (int) $request->input('limit', 10);
        $search = $request->input('search');

        $transfers = Transfer::with(['transport', 'fromLocation', 'toLocation'])
            ->when($search, function ($query, $search) {
                $query->whereHas('transport', function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%");
                })
                ->orWhereHas('fromLocation', function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%");
                })
                ->orWhereHas('toLocation', function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%");
                });
            })
            ->paginate($limit);

        return response()->json([
            'success' => true,
            'data' => $transfers,
            'message' => 'Transfers retrieved successfully'
        ]);
    }


    // Store new transfer
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'from_location'   => 'required',
            'to_location'     => 'required',
            'transport_id'    => 'required|exists:transports,id',
            'rate'            => 'required|numeric|min:0',
            'period_from'     => 'required|date',
            'period_till'     => 'required|date'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
                'message' => 'Validation failed'
            ], 422);
        }

        $transfer = Transfer::create($request->all());

        return response()->json([
            'success' => true,
            'data' => $transfer,
            'message' => 'Transfer created successfully'
        ]);
    }

    // Show single transfer
    public function show($id)
    {
        $transfer = Transfer::with('transport')->find($id);

        if (!$transfer) {
            return response()->json([
                'success' => false,
                'message' => 'Transfer not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $transfer
        ]);
    }

    // Update transfer
    public function update(Request $request, $id)
    {
        $transfer = Transfer::find($id);
        if (!$transfer) {
            return response()->json([
                'success' => false,
                'message' => 'Transfer not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'from_location'   => 'required',
            'to_location'     => 'required',
            'transport_id'    => 'required|exists:transports,id',
            'rate'            => 'required|numeric|min:0',
            'capacity_people' => 'required|integer|min:1',
            'capacity_bags'   => 'required|integer|min:0',
            'period_from'     => 'required|date',
            'period_till'     => 'required|date',
            'description'     => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
                'message' => 'Validation failed'
            ], 422);
        }

        $transfer->update($request->all());

        return response()->json([
            'success' => true,
            'data' => $transfer,
            'message' => 'Transfer updated successfully'
        ]);
    }

    // Delete transfer
    public function destroy($id)
    {
        $transfer = Transfer::find($id);

        if (!$transfer) {
            return response()->json([
                'success' => false,
                'message' => 'Transfer not found'
            ], 404);
        }

        $transfer->delete();

        return response()->json([
            'success' => true,
            'message' => 'Transfer deleted successfully'
        ]);
    }

    public function searchTransportPackage(Request $request)
    {
        $search = $request->input('search');
        $transport = Transfer::with(['transport', 'fromLocation', 'toLocation'])
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->whereHas('transport', function ($q2) use ($search) {
                        $q2->where('name', 'LIKE', "%{$search}%");
                    })
                    ->orWhere('rate', 'LIKE', "%{$search}%")
                    ->orWhereHas('fromLocation', function ($q2) use ($search) {
                        $q2->where('name', 'LIKE', "%{$search}%");
                    })
                    ->orWhereHas('toLocation', function ($q2) use ($search) {
                        $q2->where('name', 'LIKE', "%{$search}%");
                    });
                });
            })
            ->orderBy('id', 'DESC')
            ->take(10)
            ->get();

        return response()->json([
            'success' => true,
            'data'    => $transport,
        ]);
    }


}
