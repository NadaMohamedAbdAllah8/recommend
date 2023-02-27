<?php

namespace App\Actions\User\Cart;

use App\Models\CartProduct;
use Illuminate\Http\Request;

class AddCartProductsAction
{
    public function execute(Request $request, $cart_id)
    {
        $product_ids = $request->id;
        $quantity = $request->quantity;

        foreach ($product_ids as $key => $product_id) {
            CartProduct::updateOrCreate(
                // condition
                ['product_id' => $product_id, 'cart_id' => $cart_id],
                // values
                [
                    'product_id' => $product_id,
                    'quantity' => $quantity[$key],
                    'cart_id' => $cart_id,
                ]
            );
        }

    }
}
