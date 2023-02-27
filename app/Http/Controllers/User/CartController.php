<?php

namespace App\Http\Controllers\User;

use App\Actions\User\Cart\AddCartProductsAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\Cart\AddToCartRequest;
use App\Http\Resources\User\CartProductResource;
use App\Models\Cart;
use App\Models\CartProduct;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    public function add(AddToCartRequest $request,
        AddCartProductsAction $add_cart_items_action
    ) {

        DB::beginTransaction();

        try {
            $cart = Cart::updateOrCreate(
                // condition
                ['user_id' => auth()->user()->id],
                // values
                ['user_id' => auth()->user()->id]
            );

            // add cart items

            $add_cart_items_action->execute($request, $cart->id);

            DB::commit();

            $cart_products = CartProduct::where('cart_id', $cart->id)->get();
            return response()->json([
                'code' => 200,
                'message' => 'Added Successfully!',
                'validation' => null,
                'data' => ['cart' => CartProductResource::collection($cart_products)],
            ]);

        } catch (\Exception$exception) {
            echo $exception->getMessage();
            dd($exception->getTraceAsString());

            DB::rollback();

            return response()->json([
                'code' => 500,
                'message' => 'Error!',
                'validation' => null,
                'data' => [],
            ]);

        }

    }

    function empty() {
        DB::beginTransaction();

        try {

            $cart = Cart::where('user_id', auth()->user()->id)->first();

            CartProduct::where('cart_id', $cart->id)->delete();
            $cart->delete();

            DB::commit();

            return response()->json([
                'code' => 200,
                'message' => 'Emptied Successfully!',
                'validation' => null,
                'data' => ['cart' => null],
            ]);

        } catch (\Exception$exception) {
            dd($exception->getMessage());
            DB::rollback();

            return response()->json([
                'code' => 500,
                'message' => 'Error!',
                'validation' => null,
                'data' => [],
            ]);

        }

    }

    public function show()
    {
        try {
            $cart = Cart::where('user_id', auth()->user()->id)->first();

            if (is_null($cart)) {
                return response()->json([
                    'code' => 200,
                    'message' => 'Cart',
                    'validation' => null,
                    'data' => ['cart' => null],
                ]);

            }

            $cart_products = CartProduct::where('cart_id', $cart->id)->get();

            return response()->json([
                'code' => 200,
                'message' => 'Cart',
                'validation' => null,
                'data' => ['cart' => CartProductResource::collection($cart_products)],
            ]);

        } catch (\Exception$exception) {
            return response()->json([
                'code' => 500,
                'message' => 'Error!',
                'validation' => null,
                'data' => [],
            ]);

        }
    }

}
