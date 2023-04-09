<?php

namespace App\Actions\Admin\Product;

class DestroyAction
{
    public function execute($product)
    {
        $product->delete();
    }
}
