<?php

namespace App\Http\Controllers;

use App\Models\TransferRate;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;

class TransferRateController extends Controller
{
    public function index(Request $request)
    {
        try {
            $limit = (int) $request->input('limit', 10);

            $rates = TransferRate::with('transfer')->paginate($limit);

            return response()->json([
                'success' => true,
                'data'    => $rates,
                'error'   => null,
                'message' => 'Transfer rates retrieved successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data'    => null,
                'error'   => $e->getMessage(),
                'message' => 'Failed to fetch transfer rates'
            ], 500);
        }
    }


    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'transfer_id'   => 'required|exists:transfers,id',
                'from_location' => 'required|string|max:255',
                'to_location'   => 'required|string|max:255',
                'rate'          => 'required|numeric|min:0',
                'currency'      => 'required|string|max:10',
                'valid_until'   => 'nullable|date',
            ]);

            $rate = TransferRate::create($validated);

            return response()->json([
                'success' => true,
                'data'    => $rate,
                'error'   => null,
                'message' => 'Transfer rate created successfully'
            ], 201);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'data'    => null,
                'error'   => $e->getMessage(),
                'message' => 'Failed to create transfer rate'
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $rate = TransferRate::with('transfer')->findOrFail($id);

            return response()->json([
                'success' => true,
                'data'    => $rate,
                'error'   => null,
                'message' => 'Transfer rate retrieved successfully'
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'data'    => null,
                'error'   => 'Transfer rate not found',
                'message' => 'Failed to show transfer rate'
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'data'    => null,
                'error'   => $e->getMessage(),
                'message' => 'Failed to fetch transfer rate'
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'transfer_id'   => 'sometimes|exists:transfers,id',
                'from_location' => 'sometimes|string|max:255',
                'to_location'   => 'sometimes|string|max:255',
                'rate'          => 'sometimes|numeric|min:0',
                'currency'      => 'sometimes|string|max:10',
                'valid_until'   => 'nullable|date',
            ]);

            $rate = TransferRate::findOrFail($id);
            $rate->update($validated);

            return response()->json([
                'success' => true,
                'data'    => $rate,
                'error'   => null,
                'message' => 'Transfer rate updated successfully'
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'data'    => null,
                'error'   => 'Transfer rate not found',
                'message' => 'Failed to update transfer rate'
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'data'    => null,
                'error'   => $e->getMessage(),
                'message' => 'Failed to update transfer rate'
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $rate = TransferRate::findOrFail($id);
            $rate->delete();

            return response()->json([
                'success' => true,
                'data'    => null,
                'error'   => null,
                'message' => 'Transfer rate deleted successfully'
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'data'    => null,
                'error'   => 'Transfer rate not found',
                'message' => 'Failed to delete transfer rate'
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'data'    => null,
                'error'   => $e->getMessage(),
                'message' => 'Failed to delete transfer rate'
            ], 500);
        }
    }
}
