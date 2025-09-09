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
        return [
            'product_id' => ['required', 'exists:products,id'],
            'size_id' => ['nullable', 'exists:sizes,id'],
            'color_id' => ['nullable', 'exists:colors,id'],
            'sku' => ['nullable', 'string', 'max:100', Rule::unique('product_variants')->ignore($this->route('product_variant'))],
        ];
    }
}
