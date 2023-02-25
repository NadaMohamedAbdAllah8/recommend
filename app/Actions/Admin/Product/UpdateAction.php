<?php

namespace App\Actions\Admin\Product;

use App\Models\Product;
use Illuminate\Http\Request;

class UpdateAction
{
    public function execute(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $product->update([
            'name' => $request->name,
            'price' => $request->price,
        ]);

        return $product;
    }
}
