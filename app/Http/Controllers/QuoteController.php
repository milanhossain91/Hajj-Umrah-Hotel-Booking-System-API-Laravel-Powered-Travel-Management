<?php

namespace App\Http\Controllers;

use App\Models\Quote;
use App\Models\QuoteItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Exception;

class QuoteController extends Controller
{
  public function index(Request $request)
    {
        try {
            $limit = (int) $request->input('limit', 10);
            $search = $request->input('search');
            $quotes = Quote::with(['items', 'transports', 'visas', 'profits', 'transportpackages.fromLocation' , 'transportpackages.toLocation'])
                ->when($search, function ($query, $search) {
                    $query->where('name', 'LIKE', "%{$search}%");
                })
                ->orderBy('id', 'DESC')
                ->paginate($limit);

            return response()->json([
                'success' => true,
                'data'    => $quotes,
                'error'   => null,
                'message' => 'Quotes retrieved successfully'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data'    => null,
                'error'   => $e->getMessage(),
                'message' => 'Failed to fetch quotes'
            ], 500);
        }
    }


    public function store(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'name'   => 'required',
                'transport_packages_id' => 'required',
                'transport_id'  => 'required',
                'rate'          => 'required|numeric',
                'visa_id'       => 'required',
                'profit_id'     => 'required',
                'adult'     => 'nullable',
                'children'     => 'nullable',
                'infant'     => 'nullable',
                'items'         => 'required|json'
            ],
            [
                'name.required'          => 'The Customer Name is required.',
                'transport_packages_id.required' => 'The Transport Package is required.',
                'transport_id.required'  => 'The Transport is required.',
                'rate.required'          => 'The Rate is required.',
                'visa_id.required'       => 'The Visa is required.',
                'profit_id.required'     => 'The Profit is required.',
                'items.required'         => 'The Items are required.',
                'items.json'             => 'The Items field must be a valid JSON array.'
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        $itemsArray = json_decode($request->input('items'), true);
        if (!is_array($itemsArray)) {
            return response()->json(['error' => 'Invalid items format. Must be a JSON array.'], 422);
        }

        foreach ($itemsArray as $index => $item) {
            $itemValidator = Validator::make($item, [
                'hotel_packages_id'        => 'required|integer',
            ], [
                'hotel_packages_id.required'        => "Hotel Package ID is required for item #".($index+1),
            ]);

            if ($itemValidator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors'  => $itemValidator->errors(),
                    'item_index' => $index
                ], 422);
            }
        }

        $quote = Quote::create($request->only([
            'name', 'transport_packages_id', 'transport_id',
            'rate', 'visa_id', 'profit_id', 'adult', 'children', 'infant'
        ]));

        $createdItems = [];
        foreach ($itemsArray as $itemData) {
            $itemData['quote_id'] = $quote->id;
            $createdItems[] = QuoteItem::create($itemData);
        }

        return response()->json([
            'success' => true,
            'message' => 'Quote and items created successfully',
            'quote'   => $quote,
            'items'   => $createdItems
        ], 201);
    }



    public function show($id)
    {
        $quote = Quote::with([
            'suppliers',
            'from_location',
            'to_location',
            'transports',
            'visas',
            'profits',
            'items.customers',
            'items.reservations',
            'items',
            'items.hotel',
            'items.hotel.rates'
        ])->findOrFail($id);

        return response()->json($quote);
    }


    public function update(Request $request, $id)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'name'   => 'required',
                'transport_packages_id' => 'required',
                'transport_id'  => 'required',
                'rate'          => 'required|numeric',
                'visa_id'       => 'required',
                'profit_id'     => 'required',
                'adult'     => 'nullable',
                'children'     => 'nullable',
                'infant'     => 'nullable',
                'items'         => 'required|json'
            ],
            [
                'name.required'          => 'The Quote Name is required.',
                'customer_id.required'   => 'The Customer is required.',
                'transport_packages_id.required' => 'The Transport Package is required.',
                'transport_id.required'  => 'The Transport is required.',
                'rate.required'          => 'The Rate is required.',
                'visa_id.required'       => 'The Visa is required.',
                'profit_id.required'     => 'The Profit is required.',
                'items.required'         => 'The Items are required.',
                'items.json'             => 'The Items field must be a valid JSON array.'
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        // Find the Quote
        $quote = Quote::find($id);
        if (!$quote) {
            return response()->json([
                'success' => false,
                'message' => 'Quote not found'
            ], 404);
        }

        // Decode items JSON
        $itemsArray = json_decode($request->input('items'), true);
        if (!is_array($itemsArray)) {
            return response()->json(['error' => 'Invalid items format. Must be a JSON array.'], 422);
        }

        // Validate each item
        foreach ($itemsArray as $index => $item) {
            $itemValidator = Validator::make($item, [
                'hotel_packages_id' => 'required|integer',
                'reservations_id'   => 'required|integer',
            ], [
                'hotel_packages_id.required' => "Hotel Package ID is required for item #".($index+1),
                'reservations_id.required'   => "Reservations ID is required for item #".($index+1),
            ]);

            if ($itemValidator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors'  => $itemValidator->errors(),
                    'item_index' => $index
                ], 422);
            }
        }

        // Update Quote
        $quote->update($request->only([
            'name', 'customer_id', 'transport_packages_id', 'transport_id',
            'rate', 'visa_id', 'profit_id', 'valid_until', 'status'
        ]));

        // Delete old items
        QuoteItem::where('quote_id', $quote->id)->delete();

        // Insert new items
        $updatedItems = [];
        foreach ($itemsArray as $itemData) {
            $itemData['quote_id'] = $quote->id;
            $updatedItems[] = QuoteItem::create($itemData);
        }

        return response()->json([
            'success' => true,
            'message' => 'Quote and items updated successfully',
            'quote'   => $quote,
            'items'   => $updatedItems
        ], 200);
    }



    public function destroy($id) {
        Quote::destroy($id);
        return response()->json(null, 204);
    }

        public function customersReport(Request $request)
        {
            $quote_id = $request->quote_id;
            try {
                $hotel = Quote::with([
                    'transports',
                    'visas',
                    'transportpackages',
                    'profits',
                    'items',
                    'items.hotelpackages',
                    'items.hotelpackages.hotelpackageitems',
                    'items.hotelpackages.hotel',
                    'items.hotelpackages.supplier',
                ])->where('id', $quote_id)->get();

                return response()->json([
                    'success' => true,
                    'data'    => $hotel,
                    'error'   => null,
                    'message' => 'Customers report successfully'
                ], 200);
            } catch (Exception $e) {
                return response()->json([
                    'success' => false,
                    'data'    => null,
                    'error'   => $e->getMessage(),
                    'message' => 'Failed to show customers report'
                ], 404);
            }
        }

        public function customersInvoice(Request $request)
        {
            $quote_id = $request->quote_id;
            try {
                $hotel = Quote::with([
                    'transports',
                    'visas',
                    'transportpackages',
                    'profits',
                    'items',
                    'items.hotelpackages',
                    'items.hotelpackages.hotelpackageitems',
                    'items.hotelpackages.hotel',
                    'items.hotelpackages.supplier',
                ])->where('id', $quote_id)->get();

                return response()->json([
                    'success' => true,
                    'data'    => $hotel,
                    'error'   => null,
                    'message' => 'Customers report successfully'
                ], 200);
            } catch (Exception $e) {
                return response()->json([
                    'success' => false,
                    'data'    => null,
                    'error'   => $e->getMessage(),
                    'message' => 'Failed to show customers report'
                ], 404);
            }
        }



    // public function customersReport(Request $request)
    // {
    //     $customerid = $request->customer_id;

    //     try {
    //         $quotes = Quote::with([
    //             'customer',
    //             'transports',
    //             'visas',
    //             'profits',
    //             'items.suppliers',
    //             'items.reservations',
    //             'items.hotel.rates'
    //         ])->where('customer_id', $customerid)->get();

    //         $reportData = [];

    //         foreach ($quotes as $quote) {
    //             $tickets = [
    //                 'adults'   => 0,
    //                 'children' => 0,
    //                 'infant'   => 0,
    //             ];

    //             $subtotal = 0;
    //             $visaCost = $quote->visas ? $quote->visas->cost : 0;
    //             $subtotal += $visaCost;
    //             $transportCost = $quote->transports ? $quote->transports->cost : 0;
    //             $subtotal += $transportCost;

    //             $hotelsData = [];

    //             foreach ($quote->items as $item) {
    //                 if ($item->hotel) {
    //                     $hotelRates = [];
    //                     foreach ($item->hotel->rates as $rate) {
    //                         $roomCost = $rate->rate_per_night + $rate->extra_bed_rate + $rate->weekend_rate;
    //                         $hotelRates[] = [
    //                             'room_type' => ucfirst($rate->room_type),
    //                             'adults'       => $rate->adults ?? 0,
    //                             'children'       => $rate->children ?? 0,
    //                             'infant'       => $rate->infant ?? 0,
    //                             'rate_per_night'      => $rate->rate_per_night,
    //                             'extra_bed_rate'      => $rate->extra_bed_rate,
    //                             'weekend_rate'      => $rate->weekend_rate,
    //                             'arrival'      => $rate->arrival,
    //                             'depart'      => $rate->depart,
    //                             'days'    => $rate->numberofdays,
    //                             'total'     => $roomCost,
    //                         ];
    //                         $subtotal += $roomCost;
    //                     }

    //                     $hotelsData[] = [
    //                         'hotel_name'      => $item->hotel->name,
    //                         'supplier'        => $item->suppliers ? $item->suppliers->name : null,
    //                         'reservation_ref' => $item->reservations ? $item->reservations->reservation_ref : null,
    //                         'check_in'        => $item->reservations ? $item->reservations->check_in : null,
    //                         'check_out'       => $item->reservations ? $item->reservations->check_out : null,
    //                         'rates'           => $hotelRates
    //                     ];
    //                 }
    //             }

    //             $markup = round(((float)$subtotal + (float)$visaCost + (float)$transportCost), 2);




    //             $retail = $subtotal + $markup;

    //             $reportData[] = [
    //                 'customer_name' => $quote->customer->name,
    //                 'visa_cost'     => $visaCost,
    //                 'transport_cost'=> $transportCost,
    //                 'hotels'        => $hotelsData,
    //                 'subtotal'      => $subtotal,
    //                 'markup'        => $markup,
    //                 'retail'        => $retail
    //             ];
    //         }

    //         return response()->json([
    //             'success' => true,
    //             'report'  => $reportData,
    //             'message' => 'Customers report successfully'
    //         ], 200);

    //     } catch (Exception $e) {
    //         return response()->json([
    //             'success' => false,
    //             'data'    => null,
    //             'error'   => $e->getMessage(),
    //             'message' => 'Failed to show customers report'
    //         ], 404);
    //     }
    // }

    public function quoteSearch(Request $request)
    {
        $search = $request->search;

        try {
            $hotel = Quote::when($search, function ($query, $search) {
                $query->where('name', 'LIKE', "%{$search}%");
            })
            ->take(10) // Limit results to 10
            ->get();

            return response()->json([
                'success' => true,
                'data'    => $hotel,
                'error'   => null,
                'message' => 'Quote report successfully'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'data'    => null,
                'error'   => $e->getMessage(),
                'message' => 'Failed to show quote report'
            ], 404);
        }
    }

}
