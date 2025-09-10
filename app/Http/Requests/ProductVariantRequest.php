<?php

namespace App\Http\Requests;

use App\Models\ProductVariant;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductVariantRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        if ($this->isMethod('post')) {
            return $this->user()->can('create', ProductVariant::class);
        }

        if ($this->isMethod('put') || $this->isMethod('patch')) {
            return $this->user()->can('update', $this->route('product_variant'));
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
        $productVariantId = $this->route('product_variant')?->id;
        return [
            'product_id' => [
                'required',
                'exists:products,id',
                // composite uniqueness: product + size + color
                Rule::unique('product_variants')
                    ->where(function ($query) {
                        $query->where('product_id', $this->input('product_id'));
                        $sizeId = $this->input('size_id');
                        $colorId = $this->input('color_id');
                        is_null($sizeId) ? $query->whereNull('size_id') : $query->where('size_id', $sizeId);
                        is_null($colorId) ? $query->whereNull('color_id') : $query->where('color_id', $colorId);
                        return $query;
                    })
                    ->ignore($productVariantId),
            ],
            'size_id' => ['nullable', 'exists:sizes,id'],
            'color_id' => ['nullable', 'exists:colors,id'],
            'sku' => ['nullable', 'string', 'max:100', Rule::unique('product_variants')->ignore($productVariantId)],
            'code' => ['nullable', 'string', 'max:100', Rule::unique('product_variants')->ignore($productVariantId)],
        ];
    }

    /**
     * Custom messages
     */
    public function messages(): array
    {
        return [
            'product_id.unique' => 'Ya existe una variante con la misma combinación de producto, talla y color.',
        ];
    }
}
