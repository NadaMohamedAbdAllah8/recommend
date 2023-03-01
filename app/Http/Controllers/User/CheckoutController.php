<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Cart;
use App\Services\CheckoutService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function __invoke(CheckoutService $checkout_service)
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

            DB::commit();

            return response()->json([
                'code' => 200,
                'message' => 'Checked out Successfully!',
                'validation' => null,
                'data' => ['order' => new OrderResource($order)],
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
