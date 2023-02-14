<?php
namespace App\Actions\Admin\ShopProduct;

use App\Models\Product;

class DestroyAction
{
    public function execute($id)
    {
        $product = Product::findOrFail($id);

        $product->delete();

    }
}
