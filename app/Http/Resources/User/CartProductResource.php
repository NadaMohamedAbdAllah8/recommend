<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Resources\Json\JsonResource;

class CartProductResource extends JsonResource
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
            'product_name' => $this->product->name,
            'quantity' => $this->quantity,
            'piece_price' => $this->product->price,
            'total' => round($this->quantity * $this->product->price, 2),
        ];
    }
}
