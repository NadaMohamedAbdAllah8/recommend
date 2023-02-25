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
use Illuminate\Http\Request;
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
                'products' => new ProductResource(Product::all()),
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
                'message' => 'Error!',
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
    public function show($id)
    {
        try {
            $product = Product::findOrFail($id);

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
    public function update(UpdateRequest $request, UpdateAction $update_action, $id)
    {
        DB::beginTransaction();

        try {
            $product = $update_action->execute($request, $id);

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
                'message' => 'Error!',
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
    public function destroy(DestroyAction $destroy_action, $id)
    {
        DB::beginTransaction();

        try {
            $destroy_action->execute($id);

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
                'message' => 'Error!',
                'validation' => null,
                'data' => [],
            ]);
        }

    }
}
