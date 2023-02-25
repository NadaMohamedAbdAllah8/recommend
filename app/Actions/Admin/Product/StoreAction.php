<?php

namespace App\Actions\Admin\Product;

use App\Models\Product;
use Illuminate\Http\Request;

class StoreAction
{
    public function execute(Request $request)
    {
        return Product::create([
            'name' => $request->name,
            'price' => $request->price,
        ]);
    }
}
