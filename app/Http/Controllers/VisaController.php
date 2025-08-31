<?php

namespace App\Http\Controllers;

use App\Models\Visa;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;

class VisaController extends Controller
{
    public function index(Request $request)
    {
            try {
                $limit = (int) $request->input('limit', 10);

                $visas = Visa::orderBy('id', 'DESC')->paginate($limit);

                return response()->json([
                    'success' => true,
                    'data'    => $visas,
                    'error'   => null,
                    'message' => 'Visas retrieved successfully'
                ], 200);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'data'    => null,
                    'error'   => $e->getMessage(),
                    'message' => 'Failed to fetch visas'
                ], 500);
            }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'visa_type' => 'required|string|max:255',
                'cost'      => 'required|numeric|min:0',
                'currency'  => 'required|string|max:10',
            ], [
                'visa_type.required' => 'Visa type is required',
                'cost.required'      => 'Cost is required',
                'cost.numeric'       => 'Cost must be a valid number',
                'currency.required'  => 'Currency is required',
            ]);

            $visa = Visa::create($validated);

            return response()->json([
                'success' => true,
                'data'    => $visa,
                'error'   => null,
                'message' => 'Visa created successfully'
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'data'    => null,
                'error'   => $e->errors(),
                'message' => 'Validation failed'
            ], 422);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'data'    => null,
                'error'   => $e->getMessage(),
                'message' => 'Failed to create visa'
            ], 500);
        }
    }


    public function show($id)
    {
        try {
            $visa = Visa::findOrFail($id);

            return response()->json([
                'success' => true,
                'data'    => $visa,
                'error'   => null,
                'message' => 'Visa retrieved successfully'
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'data'    => null,
                'error'   => 'Visa not found',
                'message' => 'Failed to show visa'
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'data'    => null,
                'error'   => $e->getMessage(),
                'message' => 'Failed to fetch visa'
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'visa_type' => 'required|string|max:255',
                'cost'      => 'required|numeric|min:0',
                'currency'  => 'required|string|max:10',
            ]);

            $visa = Visa::findOrFail($id);
            $visa->update($validated);

            return response()->json([
                'success' => true,
                'data'    => $visa,
                'error'   => null,
                'message' => 'Visa updated successfully'
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'data'    => null,
                'error'   => 'Visa not found',
                'message' => 'Failed to update visa'
            ], 404);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'data'    => null,
                'error'   => $e->errors(),
                'message' => 'Validation error'
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data'    => null,
                'error'   => $e->getMessage(),
                'message' => 'Failed to update visa'
            ], 500);
        }
    }


    public function destroy($id)
    {
        try {
            $visa = Visa::findOrFail($id);
            $visa->delete();

            return response()->json([
                'success' => true,
                'data'    => null,
                'error'   => null,
                'message' => 'Visa deleted successfully'
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'data'    => null,
                'error'   => 'Visa not found',
                'message' => 'Failed to delete visa'
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'data'    => null,
                'error'   => $e->getMessage(),
                'message' => 'Failed to delete visa'
            ], 500);
        }
    }
}

