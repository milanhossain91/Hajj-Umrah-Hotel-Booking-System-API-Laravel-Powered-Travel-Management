<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use Exception;
use Illuminate\Http\Request;

class ReservationController extends Controller
{

    public function index(Request $request)
        {
            try {
                $limit  = (int) $request->input('limit', 10);
                $search = $request->input('search');

                // Fetch reservations with optional relationships
                $reservations = Reservation::when($search, function ($query, $search) {
                        $query->where('reservation_ref', 'LIKE', "%{$search}%");
                    })
                    ->orderBy('id', 'DESC')
                    ->paginate($limit);

                return response()->json([
                    'success' => true,
                    'data'    => $reservations,
                    'message' => 'Reservations retrieved successfully',
                ], 200);

            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'error'   => $e->getMessage(),
                    'message' => 'Failed to fetch reservations',
                ], 500);
            }
        }



    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'reservation_ref' => 'required|string|max:255|unique:reservations,reservation_ref',
                'check_in'        => 'required|date_format:H:i:s',
                'check_out'       => 'required|date_format:H:i:s',
            ]);

            $reservation = Reservation::create($validated);

            return response()->json([
                'success' => true,
                'data'    => $reservation,
                'message' => 'Reservation created successfully'
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors'  => $e->errors(),
                'message' => 'Validation failed'
            ], 422);

        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->errorInfo[1] == 1062) {
                return response()->json([
                    'success' => false,
                    'message' => 'Reservation reference already exists'
                ], 409);
            }

            return response()->json([
                'success' => false,
                'error'   => $e->getMessage(),
                'message' => 'Database error occurred'
            ], 500);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error'   => $e->getMessage(),
                'message' => 'Failed to create reservation'
            ], 500);
        }
    }



    public function show($id)
    {
        try {
            $reservation = Reservation::findOrFail($id);

            return response()->json([
                'success' => true,
                'data'    => $reservation,
                'message' => 'Reservation retrieved successfully'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error'   => $e->getMessage(),
                'message' => 'Failed to show reservation'
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $reservation = Reservation::findOrFail($id);
            $reservation->update($request->all());

            return response()->json([
                'success' => true,
                'data'    => $reservation,

                'message' => 'Reservation updated successfully'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,

                'error'   => $e->getMessage(),
                'message' => 'Failed to update reservation'
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            Reservation::destroy($id);

            return response()->json([
                'success' => true,
                'data'    => null,

                'message' => 'Reservation deleted successfully'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,

                'error'   => $e->getMessage(),
                'message' => 'Failed to delete reservation'
            ], 500);
        }
    }
}
