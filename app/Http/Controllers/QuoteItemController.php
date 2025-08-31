<?php

namespace App\Http\Controllers;

use App\Models\QuoteItem;
use Illuminate\Http\Request;

class QuoteItemController extends Controller
{
    public function index(Request $request)
    {
        try {
            $limit = (int) $request->input('limit', 10);

            $quoteItems = QuoteItem::with('quote')->paginate($limit);

            return response()->json([
                'success' => true,
                'data'    => $quoteItems,
                'error'   => null,
                'message' => 'Quote items retrieved successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data'    => null,
                'error'   => $e->getMessage(),
                'message' => 'Failed to fetch quote items'
            ], 500);
        }
    }


    public function store(Request $request) {
        $item = QuoteItem::create($request->all());
        return response()->json($item, 201);
    }

    public function show($id) {
        return QuoteItem::with('quote')->findOrFail($id);
    }

    public function update(Request $request, $id) {
        $item = QuoteItem::findOrFail($id);
        $item->update($request->all());
        return response()->json($item, 200);
    }

    public function destroy($id) {
        QuoteItem::destroy($id);
        return response()->json(null, 204);
    }
}
