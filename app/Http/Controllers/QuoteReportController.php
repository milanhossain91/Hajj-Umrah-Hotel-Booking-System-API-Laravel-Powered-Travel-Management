<?php

namespace App\Http\Controllers;

use App\Models\Quote;
use Illuminate\Http\Request;

class QuoteReportController extends Controller
{
    /**
     * Display a detailed report for a quote as API JSON.
     */
    public function show($id)
    {
        // Load the quote with related items, hotels, transfers, visas, and rates
        $quote = Quote::with([
            'items.hotel.rates',
            'items.transfer.rates',
            'items.visa'
        ])->findOrFail($id);

        $report = [];
        $grandTotal = 0;

        foreach ($quote->items as $item) {
            // Determine room type if hotel exists
            $roomType = $item->hotel ? ($item->hotel->rates[0]->room_type ?? 'unknown') : 'general';

            if (!isset($report[$roomType])) {
                $report[$roomType] = [
                    'room_type' => $roomType,
                    'pp' => 0,
                    'people' => 0,
                    'makka' => 0,
                    'madeenah' => 0,
                    'transfer' => 0,
                    'visa' => 0,
                    'profit' => 0,
                    'total' => 0,
                    'pp_final' => 0,
                    'hotels' => [],
                    'transfers' => []
                ];
            }

            /** ---------------- HOTELS ---------------- */
            if ($item->hotel) {
                $hotelData = [
                    'hotel_name' => $item->hotel->name,
                    'city'       => $item->hotel->city,
                    'meal_plan'  => $item->hotel->meal_plan,
                    'rate'       => $item->unit_cost,
                    'quantity'   => $item->quantity,
                    'total'      => $item->total_cost,
                    'rates'      => $item->hotel->rates
                ];

                $report[$roomType]['hotels'][] = $hotelData;

                if (strtolower($item->hotel->city) == 'makkah') {
                    $report[$roomType]['makka'] += $item->total_cost;
                }
                if (strtolower($item->hotel->city) == 'madeenah') {
                    $report[$roomType]['madeenah'] += $item->total_cost;
                }
            }

            /** ---------------- TRANSFERS ---------------- */
            if ($item->transfer) {
                $transferData = [
                    'transfer_name' => $item->transfer->vehicle_name,
                    'capacity_people' => $item->transfer->capacity_people,
                    'capacity_bags' => $item->transfer->capacity_bags,
                    'rate'          => $item->unit_cost,
                    'quantity'      => $item->quantity,
                    'total'         => $item->total_cost,
                    'rates'         => $item->transfer->rates
                ];

                $report[$roomType]['transfers'][] = $transferData;
                $report[$roomType]['transfer'] += $item->total_cost;
            }

            /** ---------------- VISAS ---------------- */
            if ($item->visa) {
                $report[$roomType]['visa'] += $item->total_cost;
            }

            /** ---------------- PROFIT & PP ---------------- */
            $report[$roomType]['profit'] += $quote->profit_margin ?? 25;
            $report[$roomType]['pp'] = $item->cost_per_person ?? 0;
            $report[$roomType]['people'] = $item->people ?? 0;
            $report[$roomType]['total'] += $item->total_cost ?? 0;
            $report[$roomType]['pp_final'] = $item->final_cost_per_person ?? 0;

            $grandTotal += $item->total_cost ?? 0;
        }

        return response()->json([
            'report' => array_values($report), // reset keys to numeric
            'grand_total' => $grandTotal
        ]);
    }
}
