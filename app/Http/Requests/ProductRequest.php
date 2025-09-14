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
            'code' => ['nullable', 'string', 'max:255', Rule::unique('products')->ignore($this->route('product'))],
            'sku' => ['nullable', 'string', 'max:255', Rule::unique('products')->ignore($this->route('product'))],
            'brand_id' => ['required', 'exists:brands,id'],
            'tax_id' => ['required', 'exists:taxes,id'],
            'unit_measure_id' => ['required', 'exists:unit_measures,id'],
            'entity_id' => ['required', 'exists:entities,id'],
        ];

        // Permitir detalles y variantes tanto en create (POST) como en update (PUT/PATCH)
        $rules = array_merge($rules, [
            'details' => ['nullable', 'array'],
            'details.*.color_id' => ['nullable', 'exists:colors,id'],
            'details.*.size_id' => ['nullable', 'exists:sizes,id'],
            'details.*.sku' => ['nullable', 'string', 'max:255'],
            'details.*.code' => ['nullable', 'string', 'max:255'],
        ]);

        return $rules;

    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'name.required' => 'El nombre es obligatorio.',
            'name.string' => 'El nombre debe ser una cadena de texto.',
            'name.min' => 'El nombre debe tener al menos :min caracteres.',
            'name.max' => 'El nombre no debe exceder de :max caracteres.',
            'image.image' => 'La imagen debe ser un archivo de imagen.',
            'image.max' => 'La imagen no debe superar los :max kilobytes.',
            'image.mimes' => 'La imagen debe ser de tipo: :values.',
            'description.string' => 'La descripción debe ser una cadena de texto.',
            'description.max' => 'La descripción no debe exceder de :max caracteres.',
            'code.string' => 'El código debe ser una cadena de texto.',
            'code.max' => 'El código no debe exceder de :max caracteres.',
            'code.unique' => 'El código ya está en uso.',
            'sku.string' => 'El SKU debe ser una cadena de texto.',
            'sku.max' => 'El SKU no debe exceder de :max caracteres.',
            'sku.unique' => 'El SKU ya está en uso.',
            'brand_id.required' => 'La marca es obligatoria.',
            'brand_id.exists' => 'La marca seleccionada no existe.',
            'tax_id.required' => 'El impuesto es obligatorio.',
            'tax_id.exists' => 'El impuesto seleccionado no existe.',
            'unit_measure_id.required' => 'La unidad de medida es obligatoria.',
            'unit_measure_id.exists' => 'La unidad de medida seleccionada no existe.',
            'entity_id.required' => 'El proveedor es obligatorio.',
            'entity_id.exists' => 'El proveedor seleccionado no existe.',
            'details.array' => 'Los detalles deben ser un arreglo.',
            'details.*.color_id.exists' => 'El color seleccionado no existe.',
            'details.*.size_id.exists' => 'La talla seleccionada no existe.',
            'details.*.sku.string' => 'El SKU debe ser una cadena de texto.',
            'details.*.sku.max' => 'El SKU no debe exceder de :max caracteres.',
            'details.*.code.string' => 'El código debe ser una cadena de texto.',
            'details.*.code.max' => 'El código no debe exceder de :max caracteres.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes(): array
    {
        return [
            'name' => 'nombre',
            'image' => 'imagen',
            'description' => 'descripción',
            'code' => 'código',
            'sku' => 'SKU',
            'brand_id' => 'marca',
            'tax_id' => 'impuesto',
            'unit_measure_id' => 'unidad de medida',
            'entity_id' => 'proveedor',
            'details' => 'detalles',
            'details.*.color_id' => 'color',
            'details.*.size_id' => 'talla',
            'details.*.sku' => 'SKU',
            'details.*.code' => 'código',
        ];
    }
}
