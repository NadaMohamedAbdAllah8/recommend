<?php

namespace App\Actions\Admin\Product;

use App\Http\Requests\Admin\Product\UpdateRequest;

class UpdateAction
{
    public function execute(UpdateRequest $request, $product)
    {
        $product->update($request->validated());
    }
}
