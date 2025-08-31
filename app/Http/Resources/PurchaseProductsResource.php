<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;

class PurchaseProductsResource extends JsonResource
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
            'purchaseNo' => $this->purchase_no,
            'slug' => $this->slug,
            'supplier' => optional($this->supplier)->name,
            'transport' => $this->transport,
            'tax' => round($this->taxAmount()),
            'taxType' => $this->purchaseTax,
            'subTotal' => round($this->sub_total),
            'purchaseTotal' => round($this->purchaseTotal(), 2),
            'totalDiscount' => round($this->discount, 2),
            'totalPaid' => $this->purchaseTotalPaid(),
            'due' => round($this->totalDue()),
            'poReference' => $this->po_reference,
            'paymentTerms' => $this->payment_terms,
            'poDate' => $this->po_date,
            'purchaseDate' => $this->purchase_date,
            'payments' => $this->purchasePayments,
            'products' => PurchaseProductResource::collection($this->purchaseProducts),
            'accountReceivable' => isset($this->purchaseReturn->returnTransaction) ? $this->purchaseReturn->returnTransaction->amount : null,
            'note' => $this->note,
            'status' => (int) $this->status,
            'createdBy' => $this->user->name,
        ];
    }
}
