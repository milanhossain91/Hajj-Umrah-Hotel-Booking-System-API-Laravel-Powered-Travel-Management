<?php

namespace App\Http\Controllers;

use App\Models\HotelRate;
use Illuminate\Http\Request;
use Exception;

class HotelRateController extends Controller
{
    public function index(Request $request)
    {
        try {
            $limit = (int) $request->input('limit', 10);

            $rates = HotelRate::with(['hotel', 'supplier'])->paginate($limit);

            return response()->json([
                'success' => true,
                'data'    => $rates,
                'error'   => null,
                'message' => 'Hotel rates retrieved successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data'    => null,
                'error'   => $e->getMessage(),
                'message' => 'Failed to fetch hotel rates'
            ], 500);
        }
    }


    public function store(Request $request)
    {
        try {
            $rate = HotelRate::create($request->all());

            return response()->json([
                'success' => true,
                'data'    => $rate,
                'error'   => null,
                'message' => 'Hotel rate created successfully'
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'data'    => null,
                'error'   => $e->getMessage(),
                'message' => 'Failed to create hotel rate'
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $rate = HotelRate::with(['hotel', 'supplier'])->findOrFail($id);

            return response()->json([
                'success' => true,
                'data'    => $rate,
                'error'   => null,
                'message' => 'Hotel rate retrieved successfully'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'data'    => null,
                'error'   => $e->getMessage(),
                'message' => 'Failed to show hotel rate'
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $rate = HotelRate::findOrFail($id);
            $rate->update($request->all());

            return response()->json([
                'success' => true,
                'data'    => $rate,
                'error'   => null,
                'message' => 'Hotel rate updated successfully'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'data'    => null,
                'error'   => $e->getMessage(),
                'message' => 'Failed to update hotel rate'
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            HotelRate::destroy($id);

            return response()->json([
                'success' => true,
                'data'    => null,
                'error'   => null,
                'message' => 'Hotel rate deleted successfully'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'data'    => null,
                'error'   => $e->getMessage(),
                'message' => 'Failed to delete hotel rate'
            ], 500);
        }
    }
}
