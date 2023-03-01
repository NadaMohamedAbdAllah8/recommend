<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartProduct;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Product;

class CheckoutService
{
    public function checkout(Cart $cart): Order
    {
        $cart_products = CartProduct::where('cart_id', $cart->id)->get();

        // save cart into order table
        $order = $this->storeOrder($cart, $cart_products);

        // save current cart product into order product table
        $this->storeOrderProducts($order, $cart_products);

        return $order;
    }

    private function storeOrder($cart, $cart_products): Order
    {
        return Order::create([
            'total' => $this->getTotal($cart_products),
            'user_id' => $cart->user_id,
        ]);
    }

    private function storeOrderProducts($order, $cart_products): void
    {
        foreach ($cart_products as $cart_product) {
            $product = Product::findOrFail($cart_product->product_id);

            OrderProduct::create([
                'order_id' => $order->id,
                'product_id' => $cart_product->product_id,
                'quantity' => $cart_product->quantity,
                'price' => $product->price,
            ]);
        }
    }

    private function getTotal($cart_products): float
    {
        $total = 0.0;

        foreach ($cart_products as $cart_product) {
            $product = Product::findOrFail($cart_product->product_id);
            $total += $product->price * $cart_product->quantity;
        }

        return $total;
    }
}
