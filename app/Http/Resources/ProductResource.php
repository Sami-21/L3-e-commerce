<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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
            "id" =>  $this->id,
            "status" => $this->status,
            "name" =>  $this->name,
            "price" =>  $this->price,
            "promotion_price" => $this->promotion_price,
            "features" =>  $this->features,
            "rating" => $this->rating,
            "category" => [
                "id" => $this->category->id,
                "name" => $this->category->name
            ]
        ];
    }
}
