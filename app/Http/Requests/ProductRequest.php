<?php

namespace App\Http\Requests;

use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        if ($this->isMethod('post')) {
            return $this->user()->can('create', Product::class);
        }

        if ($this->isMethod('put') || $this->isMethod('patch')) {
            return $this->user()->can('update', $this->route('product'));
        }

        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:3', 'max:255'],
            'image' => [
                'nullable',
                'image',
                'max:4096',
                'mimes:jpeg,png,jpg,webp',
            ],
            'description' => ['nullable', 'string', 'max:255'],
            'status' => [
                'required',
                'string',
                'in:available,discontinued,out_of_stock'
            ],
            'barcode' => ['nullable', 'string', 'max:255', Rule::unique('products')->ignore($this->route('product'))],
            'brand_id' => ['required', 'exists:brands,id'],
            'category_id' => ['required', 'exists:categories,id'],
            'tax_id' => ['required', 'exists:taxes,id'],
            'unit_measure_id' => ['required', 'exists:unit_measures,id'],
            'entity_id' => ['required', 'exists:entities,id'],
        ];
    }
}
