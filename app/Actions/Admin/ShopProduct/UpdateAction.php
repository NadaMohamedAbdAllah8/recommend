<?php

namespace App\Actions\Admin\ShopProduct;

use App\Models\Product;
use Illuminate\Http\Request;

class UpdateAction
{
    public function execute(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $product->update([
            'name' => $request->input('name'),
            'price' => $request->input('price'),
        ]);

    }
}
