<?php

namespace App\Http\Controllers\Vendor;

use App\Actions\Vendor\ShopProduct\DataAction;
use App\Actions\Vendor\ShopProduct\DestroyAction;
use App\Actions\Vendor\ShopProduct\StoreAction;
use App\Actions\Vendor\ShopProduct\ToggleFeaturedAction;
use App\Actions\Vendor\ShopProduct\UnapprovedAction;
use App\Actions\Vendor\ShopProduct\UpdateAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdatePricesExcel;
use App\Http\Requests\Vendor\ShopProduct\DestroyRequest;
use App\Http\Requests\Vendor\ShopProduct\StoreRequest;
use App\Http\Requests\Vendor\ShopProduct\UpdateRequest;
use App\Imports\Vendor\UpdatePricesExcelImport;
use App\Models\Brand;
use App\Models\Category;
use App\Models\QuantityType;
use App\Models\Product;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ShopProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $main_title = 'Shop Products';

        $sub_title = 'View';

        $shop_count = Product::vendorShopProducts()->count('id');

        return view('Vendor.shop_products.index',
            compact('main_title', 'sub_title', 'shop_count')
        );
    }

    //get assigned products data from DB, then return data in html table
    public function data(Request $request, DataAction $data_action)
    {
        //ini_set('memory_limit', '1024M'); // or you could use 1G

        $data = $data_action->execute($request);

        return response()->json(['result' => $data['result'],
            'links' => $data['products']->links()->render()], 200);
    }

    // /**
    //  * Show the form for creating a new resource.
    //  */
    // public function create()
    // {
    //     $main_title = 'Shop Products';

    //     $sub_title = 'Add';

    //     $level3Categories = Category::where('level', 'level3')->get();

    //     $brands = Brand::all();

    //     $quantityTypes = QuantityType::all();

    //     return view(
    //         'Vendor.shop_products.create',
    //         compact('main_title', 'sub_title', 'level3Categories', 'brands', 'quantityTypes')
    //     );
    // }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request, StoreAction $store_action)
    {
        DB::beginTransaction();

        try {
            $store_action->execute($request);

            DB::commit();

            session()->flash('_added', 'Shop Product data has been created successfully');

            return redirect()->route('vendor.shop-products.index');
        } catch (\Exception $exception) {
            DB::rollback();

            session()->flash('error', 'Specification does not exist');

            return redirect()->route('vendor.shop-products.index');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $main_title = 'Shop Products';

        $sub_title = 'Edit';

        $level3Categories = Category::where('level', 'level3')->get();

        try {
            $product = Product::vendorShopProducts()->findOrFail($id);
        } catch (ModelNotFoundException $e) {
            session()->flash('error', 'Product does not exist');

            return redirect()->route('vendor.shop-products.index');
        }

        $brands = Brand::all();

        $quantityTypes = QuantityType::all();

        return view(
            'Vendor.shop_products.edit',
            compact('main_title', 'sub_title', 'product', 'level3Categories', 'brands', 'quantityTypes')
        );
    }

    /**
     * Update the specified resource in storage.
     *
     */
    public function update(UpdateRequest $request, UpdateAction $update_action,
        UnapprovedAction $unapproved_action, $id) {
        try {
            Product::vendorShopProducts()->findOrFail($id);
        } catch (ModelNotFoundException $e) {
            session()->flash('error', 'Product does not exist');

            return redirect()->route('vendor.shop-products.index');
        }

        DB::beginTransaction();

        try {
            $update_action->execute($request, $id);

            $unapproved_action->execute($id);

            DB::commit();

            session()->flash('_added', 'Product data has been updated successfully');

            return back();
        } catch (\Exception $exception) {
            DB::rollback();

            session()->flash('error', 'Specification does not exist');

            return redirect()->route('vendor.shop-products.index');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DestroyRequest $request, DestroyAction $destroy_action, $id)
    {
        try {
            $product = Product::vendorShopProducts()->findOrFail($id);

        } catch (ModelNotFoundException $e) {

            return response()->json([], 101);
        }

        DB::beginTransaction();

        try {

            $destroy_action->execute($id);

            DB::commit();

            return response()->json(['id' => $id], 200);

        } catch (\Exception $exception) {
            DB::rollback();

            return response();
        }
    }

    // end of CRUD

    // deleting
    public function deleteSelected(Request $request, DestroyAction $destroy_action)
    {
        DB::beginTransaction();

        try {
            $products = Product::vendorShopProducts()
                ->whereIn('id', $request->input('products', []))
                ->get();

            foreach ($products as $product) {

                try {
                    Product::vendorShopProducts()->findOrFail($product->id);
                } catch (ModelNotFoundException $e) {
                    DB::rollback();

                    session()->flash('error', 'Product does not exist');

                    return redirect()->route('vendor.shop-products.index');
                }

                $destroy_action->execute($product->id);
            }

            DB::commit();

            session()->flash('_added', 'Products data has been deleted successfully');

            return back();
        } catch (\Exception $exception) {
            DB::rollback();

            return response();
        }
    }

    public function deleteAll(DestroyAction $destroy_action)
    {
        DB::beginTransaction();

        try {

            $products = Product::vendorShopProducts()->get();

            foreach ($products as $product) {

                try {
                    Product::vendorShopProducts()->findOrFail($product->id);
                } catch (ModelNotFoundException $e) {
                    DB::rollback();

                    session()->flash('error', 'Product does not exist');

                    return redirect()->route('vendor.shop-products.index');
                }

                $destroy_action->execute($product->id);
            }

            DB::commit();

            session()->flash('_added', 'Products data has been deleted successfully');

            return back();
        } catch (\Exception $exception) {
            DB::rollback();

            return response();
        }
    }

    // end of deleting

    // start of more products features

    /**
     * Display a listing of the resource.
     */
    public function specifications($id)
    {
        $main_title = 'Shop Products';

        $sub_title = 'Specifications';

        try {
            $product = Product::vendorShopProducts()->findOrFail($id);
        } catch (ModelNotFoundException $e) {
            session()->flash('error', 'Product does not exist');

            return redirect()->route('vendor.shop-products.index');
        }
        return view('Vendor.shop_products.specifications',
            compact('main_title', 'sub_title', 'product'));
    }

    /**
     * Toggle featured products in home page
     */
    public function toggleFeatured(ToggleFeaturedAction $toggle_featured_action, $id)
    {
        try {
            $product = Product::vendorShopProducts()->findOrFail($id);
        } catch (ModelNotFoundException $e) {
            session()->flash('error', 'Product does not exist');

            return redirect()->route('vendor.shop-products.index');
        }

        DB::beginTransaction();

        try {

            $data = $toggle_featured_action->execute($id);

            DB::commit();

            return response()->json(['id' => $id,
                'featured' => $data['featured'],
                'success' => $data['success']]);

        } catch (\Exception $exception) {
            DB::rollback();

            session()->flash('error', 'Specification does not exist');

            return redirect()->route('vendor.specifications.index');
        }
    }

    // end of more products features

    /**
     * Show the form for upload CSV file.
     */
    public function updatePrices()
    {
        $main_title = 'Shop Products';

        $sub_title = 'Import Shop Products Prices';

        return view('Vendor.shop_products.update_prices', compact('main_title', 'sub_title'));
    }
    /**
     * Update a keywords of products.
     *
     * @param UpdatePricesExcel $request
     * @return RedirectResponse
     */
    public function updatePricesExcel(UpdatePricesExcel $request)
    {
        set_time_limit(0);

        DB::beginTransaction();

        try {
            Excel::import(new UpdatePricesExcelImport(), $request->file('file'));

            DB::commit();

            return redirect()->route('vendor.shop-products.index')->with('_added',
                'Products have been successfully updated.');

        } catch (\Exception $ex) {
            DB::rollBack();

            return redirect()->back()->with('error',
                'Error occurred, Please try again later!.' . $ex->getMessage());
        }
    }

    /**
     * Show the form for upload CSV file.
     */
    public function import()
    {
        $main_title = 'Shop Products';

        $sub_title = 'Import Shop Products from CSV ';

        return view('Vendor.shop_products.import', compact('main_title', 'sub_title'));
    }
}