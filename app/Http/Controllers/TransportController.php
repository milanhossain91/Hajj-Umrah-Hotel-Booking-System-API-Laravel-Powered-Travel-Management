<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Transport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Exception;

class TransportController extends Controller
{
     /**
     * Display a listing of all transportation prices.
     */
    public function index(Request $request)
    {
        try {
            $search = $request->input('search');
            $limit  = (int) $request->input('limit', 10);
            $query = Transport::query();

            if ($search) {
                $query->where('name', 'like', "%{$search}%");
            }
            $prices = $query->orderBy('id', 'DESC')->paginate($limit);

            return response()->json([
                'success' => true,
                'data'    => $prices,
                'message' => 'Transportation prices retrieved successfully'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch transportation prices',
                'error'   => $e->getMessage()
            ], 500);
        }
    }


    /**
     * Store a newly created transportation price.
     */
    public function store(Request $request)
    {
        $rules = [
            'name'     => 'required|string|max:255',
        ];

        $messages = [
            'name.required' => 'Name is required',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            $error = $validator->errors()->first();
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        try {
            $data = $request->only(array_keys($rules));
            foreach (['name'] as $field) {
                $data[$field] = $data[$field] ?? 0;
            }

            $price = Transport::create($data);

            return response()->json([
                'success' => true,
                'data'    => $price,
                'message' => 'Transportation price created successfully'
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create transportation price',
                'error'   => $e->getMessage()
            ], 500);
        }
    }


    /**
     * Display the specified transportation price.
     */
    public function show($id)
    {
        try {
            $price = Transport::findOrFail($id);
            return response()->json([
                'success' => true,
                'data'    => $price,
                'message' => 'Transportation price retrieved successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Transportation price not found',
                'error'   => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Update the specified transportation price.
     */
    public function update(Request $request, $id)
    {
        $rules = [
            'name'     => 'required|string|max:255',
        ];

        $messages = [
            'name.required' => 'Name is required',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            $error = $validator->errors()->first();
            return response()->json([
                'success' => false,
                'message' => $error
            ], 422);
        }

        try {
            $price = Transport::findOrFail($id);
            $data = $request->only(array_keys($rules));
            foreach (['name'] as $field) {
                $data[$field] = $data[$field] ?? 0;
            }

            $price->update($data);

            return response()->json([
                'success' => true,
                'data'    => $price,
                'message' => 'Transportation price updated successfully'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update transportation price',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified transportation price.
     */
    public function destroy($id)
    {
        try {
            $price = Transport::findOrFail($id);
            $price->delete();

            return response()->json([
                'success' => true,
                'message' => 'Transportation price deleted successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete transportation price',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

}
