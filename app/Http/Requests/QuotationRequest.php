<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class QuotationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'entity_id' => ['required', 'integer', 'exists:entities,id'],
            'quotation_date' => ['nullable', 'date'],

            'items' => ['required', 'array', 'min:1'],
            'items.*.product_variant_id' => ['required', 'integer', 'exists:product_variants,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.discount' => ['nullable', 'boolean'],
            'items.*.discount_amount' => ['nullable', 'numeric', 'min:0'],
        ];
    }

    public function attributes(): array
    {
        return [
            'entity_id' => 'cliente',
            'quotation_date' => 'fecha de cotización',
            'items' => 'items',
            'items.*.product_variant_id' => 'variante de producto',
            'items.*.quantity' => 'cantidad',
            'items.*.discount' => 'aplica descuento',
            'items.*.discount_amount' => 'monto de descuento',
        ];
    }

    public function messages(): array
    {
        return [
            'entity_id.required' => 'El :attribute es obligatorio.',
            'entity_id.exists' => 'El :attribute seleccionado no existe.',
            'quotation_date.date' => 'La :attribute no tiene un formato de fecha válido.',
            'items.required' => 'Debe agregar al menos un producto a la cotización.',
            'items.array' => 'Los :attribute deben ser un arreglo válido.',
            'items.min' => 'Debe agregar al menos un producto a la cotización.',
            'items.*.product_variant_id.required' => 'La :attribute es obligatoria.',
            'items.*.product_variant_id.exists' => 'La :attribute seleccionada no existe.',
            'items.*.quantity.required' => 'La :attribute es obligatoria.',
            'items.*.quantity.integer' => 'La :attribute debe ser un número entero.',
            'items.*.quantity.min' => 'La :attribute debe ser al menos 1.',
            'items.*.discount.boolean' => 'El campo :attribute debe ser verdadero o falso.',
            'items.*.discount_amount.numeric' => 'El :attribute debe ser un número válido.',
            'items.*.discount_amount.min' => 'El :attribute no puede ser negativo.',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $items = $this->input('items', []);

            foreach ($items as $i => $row) {
                $inventory = null;
                $variantId = $row['product_variant_id'] ?? null;
                $qty = (int) ($row['quantity'] ?? 0);
                if ($variantId) {
                    $rowWarehouseId = $row['warehouse_id'] ?? null;
                    $inventory = \App\Models\Inventory::where('product_variant_id', $variantId)
                        ->when($rowWarehouseId, fn($q) => $q->where('warehouse_id', $rowWarehouseId))
                        ->first();
                    if (!$inventory) {
                        $validator->errors()->add("items.$i.product_variant_id", 'No existe inventario para la variante seleccionada.');
                    } elseif ($inventory->stock < $qty) {
                        $validator->errors()->add("items.$i.quantity", 'No hay suficiente stock disponible para la variante seleccionada.');
                    }
                }

                $hasDiscount = (bool) ($row['discount'] ?? false);
                $discountAmount = (float) ($row['discount_amount'] ?? 0);
                if ($hasDiscount && $discountAmount > 0) {
                    $unitSale = $inventory ? ($inventory->sale_price ?? 0) : 0;
                    $lineTotal = $unitSale * $qty;
                    if ($discountAmount > $lineTotal) {
                        $validator->errors()->add("items.$i.discount_amount", 'El monto de descuento no puede ser mayor al total de la línea.');
                    }
                }
            }
        });
    }
}
