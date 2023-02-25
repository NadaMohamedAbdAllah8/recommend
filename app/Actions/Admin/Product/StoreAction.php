<?php
namespace App\Actions\Admin\Product;

use App\Models\Product;
use Illuminate\Http\Request;

class StoreAction
{
    public function execute(Request $request)
    {
        Product::create([
            'name' => $request->input('name'),
            'price' => $request->input('price'),
        ]);

    }
}
