<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PurchaseListResource extends JsonResource
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
            'code' => $this->purchase_code,
            'purchaseNo' => $this->purchase_code,
            'slug' => $this->slug,
            'supplierName' => optional($this->supplier)->name,
            'supplierPhone' => optional($this->supplier)->phone,
            'products' => PurchaseProductResource::collection($this->purchaseProducts),

            'transport' => $this->transport > 0 ? $this->transport : 0,
            'tax' => round($this->taxAmount(), 2),
            'taxRate' => optional($this->purchaseTax)->rate ?? 0,

            'subTotal' => round($this->sub_total ?? 0, 2),
            'Total' => round($this->total ?? 0, 2),
            'purchaseTotal' => round($this->purchaseTotal(), 2),
            'totalDiscount' => round($this->discount > 0 ? $this->discount : 0, 2),
            'totalPaid' => round($this->paid_amount ?? 0, 2),
            'due' => round($this->amount_due ?? 0, 2),

            'purchaseDate' => $this->purchase_date,
            'note' => $this->note,
            'status' => (int) $this->status,
        ];
    }
}
