<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;

class PurchaseProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'purchasePrice' => $this->purchase_price,
            'unitCost' => $this->unit_cost,
            'taxAmount' => $this->tax_amount,
            'quantity' => $this->quantity,
            'purchasePricetotal' => $this->quantity * $this->purchase_price,
            'unitCostTotal' => $this->quantity * $this->unit_cost,
            'taxTotal' => $this->quantity * $this->tax_amount,
            'productID' => $this->product->id,
           // 'productSlug' => $this->product->slug,
           // 'productCode' => $this->product->code,
            'productName' => $this->product->name,
            //'productModel' => $this->product->model,
           // 'avgPurchasePrice' => $this->product->purchase_price,
          //  'productUnit' => $this->product->productUnit->code,
           // 'taxType' => $this->product->tax_type,
           // 'taxRate' => $this->product->productTax->rate,
            //'stockQty' => $this->product->inventory_count,
        ];
    }
}
