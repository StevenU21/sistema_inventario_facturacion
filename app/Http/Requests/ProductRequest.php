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
        $rules = [
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
            'code' => ['nullable', 'string', 'max:255', Rule::unique('products')->ignore($this->route('product'))],
            'sku' => ['nullable', 'string', 'max:255', Rule::unique('products')->ignore($this->route('product'))],
            'brand_id' => ['required', 'exists:brands,id'],
            'tax_id' => ['required', 'exists:taxes,id'],
            'unit_measure_id' => ['required', 'exists:unit_measures,id'],
            'entity_id' => ['required', 'exists:entities,id'],
        ];

        // Reglas adicionales sólo en creación para variantes e inventario inicial
        if ($this->isMethod('post')) {
            $rules = array_merge($rules, [
                'warehouse_id' => ['required', 'exists:warehouses,id'],
                'details' => ['required', 'array', 'min:1'],
                'details.*.color_id' => ['nullable', 'exists:colors,id'],
                'details.*.size_id' => ['nullable', 'exists:sizes,id'],
                'details.*.quantity' => ['required', 'integer', 'min:1'],
                'details.*.unit_price' => [
                    'required',
                    'numeric',
                    'min:0',
                    function ($attribute, $value, $fail) {
                        $index = explode('.', $attribute)[1] ?? null;
                        if ($index === null) return;
                        $salePrice = $this->input("details.{$index}.sale_price");
                        if ($salePrice !== null && $value > $salePrice) {
                            $fail('El precio unitario no puede ser mayor al precio de venta.');
                        }
                    }
                ],
                'details.*.sale_price' => [
                    'nullable',
                    'numeric',
                    'min:0',
                    function ($attribute, $value, $fail) {
                        if ($value === null) return;
                        $index = explode('.', $attribute)[1] ?? null;
                        if ($index === null) return;
                        $unitPrice = $this->input("details.{$index}.unit_price");
                        if ($unitPrice !== null && $value < $unitPrice) {
                            $fail('El precio de venta no puede ser menor al precio unitario.');
                        }
                    }
                ],
                'details.*.min_stock' => ['nullable', 'integer', 'min:0'],
                'details.*.sku' => ['nullable', 'string', 'max:255'],
                'details.*.code' => ['nullable', 'string', 'max:255'],
            ]);
        }

        return $rules;
    }
}
