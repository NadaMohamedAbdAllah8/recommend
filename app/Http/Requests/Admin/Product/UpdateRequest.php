<?php

namespace App\Http\Requests\Admin\Product;

use App\Models\Category;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {

        return [
            'name_en' => 'required|string|min:2|max:191',
            'name_ar' => 'required|string|min:2|max:191',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,gif,bmp,webp',
            'mobile_img' => 'nullable|image|mimes:jpg,jpeg,png,gif,bmp,webp',
            'pdf' => 'nullable|file|mimes:pdf',
            'alt_en' => 'nullable|string|min:2|max:191',
            'alt_ar' => 'nullable|string|min:2|max:191',
            'title_en' => 'nullable|string|min:2|max:191',
            'title_ar' => 'nullable|string|min:2|max:191',
            'video' => 'nullable|url|min:2|max:191',
            'keywords_ar' => 'nullable|string|min:2',
            'keywords_en' => 'nullable|string|min:2',
            'keywords_meta_en' => 'nullable|string|min:2',
            'keywords_meta_ar' => 'nullable|string|min:2',
            'description_ar' => 'nullable|string|min:2',
            'description_en' => 'nullable|string|min:2',
            'description_meta_ar' => 'nullable|string|min:2',
            'description_meta_en' => 'nullable|string|min:2',
            'sku_code' => 'nullable|string|min:2|max:191',
            'category_id' => ['required', Rule::in(Category::where('level', 'level3')->pluck('id'))],
            'brand_id' => 'required|exists:brands,id',
            'old_price' => 'nullable|string|min:1',
            'new_price' => 'required|string|min:1',
            'qty' => 'required|regex:/^\d+(\.\d{1,2})?$/',
            'qty_type' => 'required|exists:quantity_types,id',
            'sold_by_souq' => 'nullable|boolean',
        ];
    }
}
