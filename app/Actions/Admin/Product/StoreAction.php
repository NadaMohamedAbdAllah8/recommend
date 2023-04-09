<?php

namespace App\Actions\Admin\Product;

use App\Http\Requests\Admin\Product\StoreRequest;
use App\Models\Product;

class StoreAction
{
    public function execute(StoreRequest $request)
    {
        return Product::create([
            'name' => $request->name,
            'price' => $request->price,
        ]);
    }
}
