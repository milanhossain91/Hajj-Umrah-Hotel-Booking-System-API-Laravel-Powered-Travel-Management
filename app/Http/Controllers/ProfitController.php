<?php

namespace App\Http\Controllers;

use App\Models\Profit;
use Illuminate\Http\Request;
use Exception;

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class ProfitController extends Controller
{
    public function index(Request $request)
    {
        try {
            $limit = (int) $request->input('limit', 10);

            $profits = Profit::with('user')->orderBy('id', 'DESC')->paginate($limit);

            return response()->json([
                'success' => true,
                'data'    => $profits,
                'message' => 'Profits retrieved successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error'   => $e->getMessage(),
                'message' => 'Failed to fetch profits'
            ], 500);
        }
    }


    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'item_type'        => 'required|string',
                'percentage_markup'=> 'required|numeric',
                'fixed_markup'     => 'required|numeric',
            ]);

            $validated['user_id'] = Auth::id();

            $profit = Profit::create($validated);

            return response()->json([
                'success' => true,
                'data'    => $profit,
                'message' => 'Profit created successfully'
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors'  => $e->errors(),
                'message' => 'Validation failed'
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error'   => $e->getMessage(),
                'message' => 'Failed to create profit'
            ], 500);
        }
    }


    public function show($id)
    {
        try {
            $profit = Profit::with('user')->findOrFail($id);

            return response()->json([
                'success' => true,
                'data'    => $profit,

                'message' => 'Profit retrieved successfully'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,

                'error'   => $e->getMessage(),
                'message' => 'Failed to show profit'
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $profit = Profit::findOrFail($id);
           // dd($profit);
            $validated = $request->validate([
                'item_type'        => 'required',
                'percentage_markup'=> 'required|numeric',
                'fixed_markup'     => 'nullable|numeric',
            ]);
            $validated['user_id'] = Auth::id();

            $profit->update($validated);

            return response()->json([
                'success' => true,
                'data'    => $profit,
                'message' => 'Profit updated successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error'   => $e->getMessage(),
                'message' => 'Failed to update profit'
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $profit = Profit::findOrFail($id);
            $profit->delete();

            return response()->json([
                'success' => true,
                'message' => 'Profit deleted successfully'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,

                'error'   => $e->getMessage(),
                'message' => 'Failed to delete profit'
            ], 500);
        }
    }
}
