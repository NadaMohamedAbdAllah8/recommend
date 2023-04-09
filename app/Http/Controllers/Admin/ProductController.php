<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Admin\Product\DestroyAction;
use App\Actions\Admin\Product\StoreAction;
use App\Actions\Admin\Product\UpdateAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Product\StoreRequest;
use App\Http\Requests\Admin\Product\UpdateRequest;
use App\Http\Resources\Admin\ProductResource;
use App\Models\Product;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json([
            'code' => 200,
            'message' => 'Products',
            'validation' => null,
            'data' => [
                'products' => ProductResource::collection(Product::select('id', 'name', 'price', 'created_at',
                    'updated_at')->get()),
            ],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRequest $request, StoreAction $store_action)
    {
        DB::beginTransaction();
        try {
            $product = $store_action->execute($request);
            DB::commit();

            return response()->json([
                'code' => 200,
                'message' => 'Created!',
                'validation' => null,
                'data' => [
                    'product' => new ProductResource($product),
                ],
            ]);
        } catch (\Exception$e) {
            DB::rollback();

            return response()->json([
                'code' => 500,
                'message' => 'Error! ' . $e->getMessage(),
                'validation' => null,
                'data' => [],
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        try {
            return response()->json([
                'code' => 200,
                'message' => 'Product',
                'validation' => null,
                'data' => [
                    'product' => new ProductResource($product),
                ],
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'code' => 101,
                'message' => 'Not found',
                'validation' => null,
                'data' => [],
            ]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRequest $request, Product $product, UpdateAction $update_action)
    {
        DB::beginTransaction();
        try {
            $update_action->execute($request, $product);
            DB::commit();

            return response()->json([
                'code' => 200,
                'message' => 'Updated!',
                'validation' => null,
                'data' => [
                    'product' => new ProductResource($product),
                ],
            ]);
        } catch (\Exception$e) {
            DB::rollback();

            return response()->json([
                'code' => 500,
                'message' => 'Error! ' . $e->getMessage(),
                'validation' => null,
                'data' => [],
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(DestroyAction $destroy_action, Product $product)
    {
        DB::beginTransaction();
        try {
            $destroy_action->execute($product);
            DB::commit();

            return response()->json([
                'code' => 200,
                'message' => 'Deleted!',
                'validation' => null,
                'data' => [],
            ]);
        } catch (\Exception$e) {
            DB::rollback();

            return response()->json([
                'code' => 500,
                'message' => $e->getMessage(),
                'validation' => null,
                'data' => [],
            ]);
        }
    }
}
