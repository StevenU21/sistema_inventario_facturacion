<?php

namespace App\Http\Requests;

use App\Models\Purchase;
use Illuminate\Foundation\Http\FormRequest;

class PurchaseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        if ($this->isMethod('post')) {
            return $this->user()->can('create', Purchase::class);
        }

        if ($this->isMethod('put') || $this->isMethod('patch')) {
            return $this->user()->can('update', $this->route('purchase'));
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
            // Datos de la compra
            'reference' => ['nullable', 'string', 'min:3', 'max:255'],
            // subtotal y total se calculan en el servidor
            'entity_id' => ['required', 'exists:entities,id'],
            'warehouse_id' => ['required', 'exists:warehouses,id'],
            'payment_method_id' => ['required', 'exists:payment_methods,id'],

            // Modo de producto: nuevo o existente
            'product_mode' => ['required', 'in:new,existing'],

            // Producto (opcional si se envía product.id); si no existe, se crea
            'product.id' => [
                'nullable',
                'exists:products,id',
                // Si el modo es existente, el id es requerido
                'required_if:product_mode,existing',
                // Si el modo es nuevo, no se permite enviar id
                'prohibited_if:product_mode,new',
            ],
            // Si el modo es nuevo, se requieren los campos; si es existente, se prohíben para evitar mezcla
            'product.name' => ['required_if:product_mode,new', 'prohibited_if:product_mode,existing', 'string', 'max:255'],
            'product.description' => ['nullable', 'string'],
            'product.barcode' => ['nullable', 'string', 'max:255'],
            'product.code' => ['nullable', 'string', 'max:255'],
            'product.sku' => ['nullable', 'string', 'max:255'],
            'product.status' => ['nullable', 'in:available,discontinued,out_of_stock'],
            // Estos campos son obligatorios cuando se crea un producto nuevo (no se envía product.id)
            'product.brand_id' => ['required_if:product_mode,new', 'prohibited_if:product_mode,existing', 'exists:brands,id'],
            'product.category_id' => ['required_if:product_mode,new', 'prohibited_if:product_mode,existing', 'exists:categories,id'],
            'product.tax_id' => ['required_if:product_mode,new', 'prohibited_if:product_mode,existing', 'exists:taxes,id'],
            'product.unit_measure_id' => ['required_if:product_mode,new', 'prohibited_if:product_mode,existing', 'exists:unit_measures,id'],

            // Detalles (variantes)
            'details' => ['required', 'array', 'min:1'],
            'details.*.color_id' => ['required', 'exists:colors,id'],
            'details.*.size_id' => ['required', 'exists:sizes,id'],
            'details.*.quantity' => ['required', 'integer', 'min:1'],
            'details.*.unit_price' => [
                'required',
                'numeric',
                'min:0',
                function ($attribute, $value, $fail) {
                    $index = explode('.', $attribute)[1];
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
                    if ($value === null)
                        return;
                    $index = explode('.', $attribute)[1];
                    $unitPrice = $this->input("details.{$index}.unit_price");
                    if ($unitPrice !== null && $value < $unitPrice) {
                        $fail('El precio de venta no puede ser menor al precio unitario.');
                    }
                }
            ],
            'details.*.min_stock' => ['nullable', 'integer', 'min:0'],
            'details.*.sku' => ['nullable', 'string', 'max:255'],
            'details.*.code' => ['nullable', 'string', 'max:255'],
        ];
    }
}
