<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductListingResource extends JsonResource
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
            'name' => $this->name,
            'subCategory' => new ProductSubCategoryResource($this->whenLoaded('proSubCategory')),
            'category' => new ProductCategoryResource($this->proSubCategory->category),
            'mfgdate' => $this->mfgdate,
            'exp_date' => $this->exp_date,
            'modelno' => $this->modelno,
            'quantity' => $this->quantity,
            'buyprice' => $this->buyprice,
            'sell_price' => $this->sell_price,
            'remark' => $this->remark,
            'status' => (int) $this->status,
            'image' => $this->image ? asset('/images/products/' . $this->image) : '',
        ];
    }
}
