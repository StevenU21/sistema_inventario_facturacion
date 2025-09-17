<?php

namespace App\Http\Requests;

use App\Models\Inventory;
use App\Models\Sale;
use Illuminate\Foundation\Http\FormRequest;

class SaleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        if ($this->isMethod('post')) {
            return $this->user()->can('create', Sale::class);
        }

        if ($this->isMethod('put') || $this->isMethod('patch')) {
            return $this->user()->can('update', $this->route('sale'));
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
            'entity_id' => ['required', 'integer', 'exists:entities,id'],
            // En ventas a crédito, el método de pago no es obligatorio
            'payment_method_id' => ['required_unless:is_credit,1', 'nullable', 'integer', 'exists:payment_methods,id'],
            'is_credit' => ['required', 'boolean'],
            'sale_date' => ['nullable', 'date'],
            'warehouse_id' => ['required', 'integer', 'exists:warehouses,id'],

            'items' => ['required', 'array', 'min:1'],
            'items.*.product_variant_id' => ['required', 'integer', 'exists:product_variants,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.discount' => ['nullable', 'boolean'],
            'items.*.discount_amount' => ['nullable', 'numeric', 'min:0'],
        ];
    }

    /**
     * Custom validation for inventory, stock and discounts.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $items = $this->input('items', []);
            $warehouseId = $this->input('warehouse_id');

            foreach ($items as $i => $row) {
                // Validación de existencia de inventario y stock
                $variantId = $row['product_variant_id'] ?? null;
                $qty = $row['quantity'] ?? 0;
                if ($variantId && $warehouseId) {
                    $inventory = Inventory::where('product_variant_id', $variantId)
                        ->where('warehouse_id', $warehouseId)
                        ->first();
                    if (!$inventory) {
                        $validator->errors()->add("items.$i.product_variant_id", 'No existe inventario para la variante seleccionada en el almacén especificado.');
                    } elseif ($inventory->stock < $qty) {
                        $validator->errors()->add("items.$i.quantity", 'No hay suficiente stock disponible para la variante seleccionada.');
                    }
                }

                // Validación de descuento
                $hasDiscount = $row['discount'] ?? false;
                $discountAmount = $row['discount_amount'] ?? 0;
                if ($hasDiscount && $discountAmount > 0) {
                    // Opcional: verificar que el descuento no exceda el total de la línea
                    $unitSale = $inventory->sale_price ?? 0;
                    $lineTotal = $unitSale * $qty;
                    if ($discountAmount > $lineTotal) {
                        $validator->errors()->add("items.$i.discount_amount", 'El monto de descuento no puede ser mayor al total de la línea.');
                    }
                }
            }
        });
    }
}
