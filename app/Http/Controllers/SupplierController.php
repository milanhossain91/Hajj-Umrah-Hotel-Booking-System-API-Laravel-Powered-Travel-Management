<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Validation\Rule;

use Illuminate\Support\Facades\Validator;

class SupplierController extends Controller
{
   public function index(Request $request)
    {
        try {
            $limit = (int) $request->input('limit', 10);
            $name  = $request->input('name');

            $query = Supplier::query();

            if (!empty($name)) {
                $query->where('name', 'like', '%' . $name . '%');
            }

            $Suppliers = $query->orderBy('id', 'DESC')->paginate($limit);

            return response()->json([
                'success' => true,
                'data'    => $Suppliers,
                'error'   => null,
                'message' => 'Suppliers retrieved successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data'    => null,
                'error'   => $e->getMessage(),
                'message' => 'Failed to fetch Suppliers'
            ], 500);
        }
    }


    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name'  => 'required|string|max:255|unique:Suppliers,name',
                'email' => 'required|email|unique:Suppliers,email',
                'phone' => [
                    'nullable',
                    'regex:/^[0-9]+$/',
                    'min:7',
                    'max:20'
                ]
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'data'    => null,
                    'error'   => $validator->errors(),
                    'message' => 'Validation error'
                ], 422);
            }

            $Supplier = Supplier::create($request->all());

            return response()->json([
                'success' => true,
                'data'    => $Supplier,
                'error'   => null,
                'message' => 'Supplier created successfully'
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data'    => null,
                'error'   => $e->getMessage(),
                'message' => 'Failed to create Supplier'
            ], 500);
        }
    }



    public function show($id)
    {
        try {
            $Supplier = Supplier::findOrFail($id);

            return response()->json([
                'success' => true,
                'data'    => $Supplier,
                'error'   => null,
                'message' => 'Supplier retrieved successfully'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'data'    => null,
                'error'   => $e->getMessage(),
                'message' => 'Failed to show Supplier'
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('Suppliers')->ignore($id),
                ],
                'email' => [
                    'required',
                    'email',
                    Rule::unique('Suppliers')->ignore($id),
                ],
                'phone' => [
                    'nullable',
                    'regex:/^[0-9]+$/',
                    'min:7',
                    'max:20'
                ]
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'data'    => null,
                    'error'   => $validator->errors(),
                    'message' => 'Validation error'
                ], 422);
            }

            $Supplier = Supplier::findOrFail($id);
            $Supplier->update($request->all());

            return response()->json([
                'success' => true,
                'data'    => $Supplier,
                'error'   => null,
                'message' => 'Supplier updated successfully'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data'    => null,
                'error'   => $e->getMessage(),
                'message' => 'Failed to update Supplier'
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            Supplier::destroy($id);

            return response()->json([
                'success' => true,
                'data'    => null,
                'error'   => null,
                'message' => 'Supplier deleted successfully'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'data'    => null,
                'error'   => $e->getMessage(),
                'message' => 'Failed to delete Supplier'
            ], 500);
        }
    }
}
