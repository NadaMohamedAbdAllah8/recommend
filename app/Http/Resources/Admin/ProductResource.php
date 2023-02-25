<?php

namespace App\Http\Resources\Admin;

use Carbon\Carbon;
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
        // dd($this->'created_at);
        // return parent::toArray($request);

        $created_at = new Carbon($this->created_at);

        $updated_at = new Carbon($this->updated_at);

        return [
            'id' => $this->id,
            'name' => $this->name,
            'price' => $this->price,
            'created_at' => $created_at->toDayDateTimeString(),
            'updated_at' => $updated_at->toDayDateTimeString(),
        ];

    }
}
