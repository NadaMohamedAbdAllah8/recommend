<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\User\ProductResource;
use App\Models\Product;
use Illuminate\Database\Eloquent\ModelNotFoundException;

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
                'products' => ProductResource::collection(Product::select('id', 'name', 'price')->get()),
            ],
        ]);
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
            $product = Product::select('id', 'name', 'price')->findOrFail($id);

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
}
