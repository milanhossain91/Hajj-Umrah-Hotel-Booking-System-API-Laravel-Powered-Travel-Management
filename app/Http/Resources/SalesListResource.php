<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SalesListResource extends JsonResource
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
            'customerName' => $this->customer->name,
            'products' => PurchaseProductResource::collection($this->salesAdd),
            'supplierPhone' => $this->customer->phone,
            'subTotal' => round($this->gross_amount, 2),
            'totalDiscount' => round($this->discount > 0 ? $this->discount : 0, 2),
            'netAmount' => round($this->net_amount > 0 ? $this->net_amount : 0, 2),
            'paidAmount' => round($this->paidamount > 0 ? $this->paidamount : 0, 2),
            'dueamount' => round($this->dueamount > 0 ? $this->dueamount : 0, 2),
           // 'totalPaid' => round($this->purchaseTotalPaid(), 2),
          //  'due' => round($this->totalDue() > 0 ? $this->totalDue() : 0, 2),
            'salesDate' => $this->date,
        ];
    }
}
