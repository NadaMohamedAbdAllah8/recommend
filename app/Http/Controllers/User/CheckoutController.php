<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Http\Resources\User\ProductResource;
use App\Models\Cart;
use App\Models\Product;
use App\Services\CheckoutService;
use App\Services\RecommendService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function __invoke(CheckoutService $checkout_service, RecommendService $recommend_service)
    {
        DB::beginTransaction();

        try {
            $cart = Cart::firstOrFail()->where('user_id', auth()->user()->id)->first();
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'code' => 101,
                'message' => 'Error!',
                'validation' => 'The user has no cart',
                'data' => [],
            ]);
        }

        try {
            $order = $checkout_service->checkout($cart);
            $product_ids = $recommend_service->recommend($order);

            DB::commit();

            return response()->json([
                'code' => 200,
                'message' => 'Checked out Successfully!',
                'validation' => null,
                'data' => ['order' => new OrderResource($order),
                    'recommended' => ProductResource::collection(Product::whereIn('id', $product_ids)->get())],
            ]);
        } catch (\Exception$exception) {
            DB::rollback();

            return response()->json([
                'code' => 500,
                'message' => 'Error!',
                'validation' => null,
                'data' => [],
            ]);
        }
    }
}
