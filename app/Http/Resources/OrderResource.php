<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
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
            'id' =>  $this->id,
            'firstname' => $this->when($this->client_id, function () {
                return $this->client->user->firstname;
            }, $this->firstname),
            'lastname' => $this->when($this->client_id, function () {
                return $this->client->user->lastname;
            }, $this->lastname),
            'phone' => $this->phone,
            'wilaya' => $this->wilaya,
            'city' => $this->city,
            'address' => $this->address,
            'status' => $this->status,
            'notes' => $this->notes,
            'total' => $this->total,
            'products' => $this->products
        ];
    }
}