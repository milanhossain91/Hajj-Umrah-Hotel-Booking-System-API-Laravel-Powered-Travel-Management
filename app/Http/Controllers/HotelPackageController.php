<?php

namespace App\Http\Controllers;

use App\Models\Hotel;
use App\Models\HotelPackage;
use App\Models\HotelPackageRate;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Validator;
class HotelPackageController extends Controller
{
    public function index(Request $request)
    {
        try {
            $limit  = (int) $request->input('limit', 10);
            $search = $request->input('search');

            $hotels = HotelPackage::with(['hotelpackageitems', 'supplier', 'hotel'])
                ->when($search, function ($query, $search) {
                    $query->whereHas('hotel', function ($q) use ($search) {
                        $q->where('name', 'LIKE', "%{$search}%");
                    });
                })
                ->orderBy('id', 'DESC')
                ->paginate($limit);

            return response()->json([
                'success' => true,
                'data'    => $hotels,
                'error'   => null,
                'message' => 'Hotels retrieved successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data'    => null,
                'error'   => $e->getMessage(),
                'message' => 'Failed to fetch hotels'
            ], 500);
        }
    }



    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'supplier_id'      => 'required',
            'hotel_id'         => 'required',
            'res_no'           => 'required',
            'city'             => 'required',
            'suppliment_haram' => 'nullable|numeric',
            'suppliment_kaaba' => 'nullable|numeric',
            'meal_plan_bb'     => 'nullable',
            'meal_plan_ld'     => 'nullable',
            'description'      => 'nullable|string',
            'rates'            => 'required|json'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors'  => $validator->errors()
            ], 422);
        }

        $ratesArray = is_string($request->input('rates'))
            ? json_decode($request->input('rates'), true)
            : $request->input('rates');

        if (!is_array($ratesArray) || empty($ratesArray)) {
            return response()->json([
                'success' => false,
                'message' => 'Rates should be a non-empty JSON array'
            ], 422);
        }

        $rateRules = [
            'room_type'       => 'required|string',
            'days_wd'         => 'required|numeric',
            'days_we'         => 'nullable|numeric',
            'extra_bed_rate'  => 'nullable|numeric',
            'suppliment_haram'=> 'nullable|numeric',
            'suppliment_kaaba'=> 'nullable|numeric',
            'mealn_plan_bb'   => 'nullable',
            'mealn_plan_ld'   => 'nullable',
            'period_from'     => 'nullable|date',
            'period_till'     => 'nullable|date',
            'numberofdays'    => 'nullable|numeric',
            'adults'          => 'nullable|numeric',
            'children'        => 'nullable|numeric',
            'infant'          => 'nullable|numeric',
            'currency'        => 'nullable|string'
        ];

        foreach ($ratesArray as $index => $rate) {
            $rateValidator = Validator::make($rate, $rateRules);
            if ($rateValidator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => "Validation errors in rates[{$index}]",
                    'errors'  => $rateValidator->errors()
                ], 422);
            }

            foreach ($rateRules as $field => $rule) {
                if (!isset($rate[$field]) || $rate[$field] === null) {
                    $ratesArray[$index][$field] = is_numeric($rate[$field] ?? 0) ? 0 : '';
                }
            }
        }

        $hotel = HotelPackage::create([
            'supplier_id'      => $request->supplier_id,
            'hotel_id'         => $request->hotel_id,
            'res_no'           => $request->res_no,
            'city'             => $request->city,
            'description'      => $request->description,
        ]);

        $createdRates = [];
        foreach ($ratesArray as $rateData) {
            $rateData['hotel_package_id'] = $hotel->id;

            $rateData = array_merge([
                'days_wd'         => 0,
                'days_we'         => 0,
                'extra_bed_rate'  => 0,
                'suppliment_haram'=> 0,
                'suppliment_kaaba'=> 0,
                'mealn_plan_bb'   => 0,
                'mealn_plan_ld'   => 0,
                'numberofdays'    => 0,
                'adults'          => 0,
                'children'        => 0,
                'infant'          => 0,
            ], $rateData);

            $createdRates[] = HotelPackageRate::create($rateData);
        }

        return response()->json([
            'success' => true,
            'message' => 'Hotel and rates created successfully',
            'hotel'   => $hotel,
            'rates'   => $createdRates
        ], 201);
    }


    public function show($id)
    {
        try {
            $hotel = HotelPackage::with('hotelpackageitems', 'supplier', 'hotel')->findOrFail($id);

            return response()->json([
                'success' => true,
                'data'    => $hotel,
                'error'   => null,
                'message' => 'Hotel retrieved successfully'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'data'    => null,
                'error'   => $e->getMessage(),
                'message' => 'Failed to show hotel'
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'supplier_id'      => 'required',
            'hotel_id'         => 'required',
            'res_no'           => 'nullable',
            'city'             => 'nullable',
            'suppliment_haram' => 'nullable|numeric',
            'suppliment_kaaba' => 'nullable|numeric',
            'meal_plan_bb'     => 'nullable|numeric',
            'meal_plan_ld'     => 'nullable|numeric',
            'description'      => 'nullable|string',
            'rates'            => 'required|json'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors'  => $validator->errors()
            ], 422);
        }

        $ratesArray = is_string($request->input('rates'))
            ? json_decode($request->input('rates'), true)
            : $request->input('rates');

        if (!is_array($ratesArray) || empty($ratesArray)) {
            return response()->json([
                'success' => false,
                'message' => 'Rates should be a non-empty JSON array'
            ], 422);
        }

        $rateRules = [
            'room_type'       => 'required|string',
            'days_wd'         => 'required|numeric',
            'days_we'         => 'nullable|numeric',
            'extra_bed_rate'  => 'nullable|numeric',
            'suppliment_haram'=> 'nullable|numeric',
            'suppliment_kaaba'=> 'nullable|numeric',
            'mealn_plan_bb'   => 'nullable|numeric',
            'mealn_plan_ld'   => 'nullable|numeric',
            'period_from'     => 'nullable|date',
            'period_till'     => 'nullable|date',
            'numberofdays'    => 'nullable|numeric',
            'adults'          => 'nullable|numeric',
            'children'        => 'nullable|numeric',
            'infant'          => 'nullable|numeric',
            'currency'        => 'nullable|string',
            'valid_until'     => 'nullable|date',
        ];

        foreach ($ratesArray as $index => $rate) {
            $rateValidator = Validator::make($rate, $rateRules);
            if ($rateValidator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => "Validation errors in rates[{$index}]",
                    'errors'  => $rateValidator->errors()
                ], 422);
            }

            foreach ($rateRules as $field => $rule) {
                if (!isset($rate[$field]) || $rate[$field] === null) {
                    $ratesArray[$index][$field] = is_numeric($rate[$field] ?? 0) ? 0 : '';
                }
            }
        }

        $hotel = HotelPackage::find($id);

        if (!$hotel) {
            return response()->json([
                'success' => false,
                'message' => 'Hotel package not found'
            ], 404);
        }

        $hotel->update([
            'supplier_id'      => $request->supplier_id,
            'hotel_id'         => $request->hotel_id,
            'res_no'           => $request->res_no,
            'city'             => $request->city,
            'description'      => $request->description,
        ]);

        HotelPackageRate::where('hotel_package_id', $hotel->id)->delete();

        $updatedRates = [];
        foreach ($ratesArray as $rateData) {
            $rateData['hotel_package_id'] = $hotel->id;

            $rateData = array_merge([
                'days_wd'         => 0,
                'days_we'         => 0,
                'extra_bed_rate'  => 0,
                'suppliment_haram'=> 0,
                'suppliment_kaaba'=> 0,
                'mealn_plan_bb'   => 0,
                'mealn_plan_ld'   => 0,
                'numberofdays'    => 0,
                'adults'          => 0,
                'children'        => 0,
                'infant'          => 0,
            ], $rateData);

            $updatedRates[] = HotelPackageRate::create($rateData);
        }

        return response()->json([
            'success' => true,
            'message' => 'Hotel and rates updated successfully',
            'hotel'   => $hotel,
            'rates'   => $updatedRates
        ], 200);
    }



    public function destroy($id)
    {
        try {
            HotelPackage::destroy($id);

            return response()->json([
                'success' => true,
                'message' => 'Hotel Package deleted successfully'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'data'    => null,
                'error'   => $e->getMessage(),
                'message' => 'Failed to delete hotel'
            ], 500);
        }
    }

    public function customersInvoice(Request $request)
        {
            $customerid = $request->customer_id;
            try {
                $hotel = HotelPackage::with('hotelpackageitems', 'supplier', 'hotel')->where('supplier_id', $customerid)->get();

                return response()->json([
                    'success' => true,
                    'data'    => $hotel,
                    'error'   => null,
                    'message' => 'Customers invoice successfully'
                ], 200);
            } catch (Exception $e) {
                return response()->json([
                    'success' => false,
                    'data'    => null,
                    'error'   => $e->getMessage(),
                    'message' => 'Failed to show customers invoice'
                ], 404);
            }
        }

    public function searchHotelPackage(Request $request)
    {
        try {
            $search = $request->input('search');

            $hotels = HotelPackage::with(['hotelpackageitems', 'supplier', 'hotel'])
                ->when($search, function ($query, $search) {
                    $query->where('res_no', 'LIKE', "%{$search}%")
                        ->orWhereHas('hotel', function ($q) use ($search) {
                            $q->where('name', 'LIKE', "%{$search}%");
                        })
                        ->orWhereHas('supplier', function ($q) use ($search) {
                            $q->where('name', 'LIKE', "%{$search}%");
                        });
                })
                ->orderBy('id', 'DESC')
                ->take(10)
                ->get();

            return response()->json([
                'success' => true,
                'data'    => $hotels,
                'error'   => null,
                'message' => 'Hotels retrieved successfully'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data'    => null,
                'error'   => $e->getMessage(),
                'message' => 'Failed to fetch hotels'
            ], 500);
        }
    }

}
