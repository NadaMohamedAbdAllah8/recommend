<?php
namespace App\Actions\Admin\Product;

use App\Models\Product;

class DestroyAction
{
    public function execute($id)
    {
        $product = Product::findOrFail($id);

        $product->delete();

    }
}
