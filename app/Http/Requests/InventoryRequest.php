<?php

namespace App\Http\Requests;

use App\Models\Inventory;
use Illuminate\Foundation\Http\FormRequest;

class InventoryRequest extends FormRequest
{
    /**
     * Atributos personalizados para los mensajes de validación.
     *
     * @return array
     */
    public function attributes(): array
    {
        return [
            'stock' => 'stock',
            'min_stock' => 'stock mínimo',
            'purchase_price' => 'precio de compra',
            'sale_price' => 'precio de venta',
            'product_variant_id' => 'variante de producto',
            'warehouse_id' => 'almacén',
            'movement_type' => 'tipo de movimiento',
            'destination_warehouse_id' => 'almacén de destino',
            'quantity' => 'cantidad',
            'adjustment_reason' => 'motivo de ajuste',
            'product_id' => 'producto',
        ];
    }
    protected function prepareForValidation()
    {
        if ($this->has('quantity') && $this->input('quantity') === '') {
            $this->merge(['quantity' => null]);
        }
        // Normalizar purchase_price y sale_price para evitar null o string vacío
        if ($this->has('purchase_price') && ($this->input('purchase_price') === null || $this->input('purchase_price') === '')) {
            $this->merge(['purchase_price' => 0]);
        }
        if ($this->has('sale_price') && ($this->input('sale_price') === null || $this->input('sale_price') === '')) {
            $this->merge(['sale_price' => 0]);
        }
    }

    /**
     * Mensajes personalizados de validación en español
     */
    public function messages()
    {
        return [
            'stock.required' => 'El stock es obligatorio.',
            'stock.integer' => 'El stock debe ser un número entero.',
            'stock.min' => 'El stock no puede ser menor que :min.',
            'min_stock.required' => 'El stock mínimo es obligatorio.',
            'min_stock.integer' => 'El stock mínimo debe ser un número entero.',
            'min_stock.min' => 'El stock mínimo no puede ser menor que :min.',
            'min_stock.lte' => 'El stock mínimo no puede ser mayor que el stock.',
            'purchase_price.required' => 'El precio de compra es obligatorio.',
            'purchase_price.numeric' => 'El precio de compra debe ser un valor numérico.',
            'purchase_price.min' => 'El precio de compra no puede ser menor que :min.',
            'sale_price.required' => 'El precio de venta es obligatorio.',
            'sale_price.numeric' => 'El precio de venta debe ser un valor numérico.',
            'sale_price.min' => 'El precio de venta no puede ser menor que :min.',
            'sale_price.gte' => 'El precio de venta no puede ser menor que el precio de compra.',
            'product_variant_id.required' => 'La variante de producto es obligatoria.',
            'product_variant_id.exists' => 'La variante de producto seleccionada no existe.',
            'warehouse_id.required' => 'El almacén es obligatorio.',
            'warehouse_id.exists' => 'El almacén seleccionado no existe.',
            'movement_type.required' => 'El tipo de movimiento es obligatorio.',
            'destination_warehouse_id.required' => 'El almacén de destino es obligatorio.',
            'destination_warehouse_id.exists' => 'El almacén de destino seleccionado no existe.',
            'quantity.required' => 'La cantidad es obligatoria.',
            'quantity.integer' => 'La cantidad debe ser un número entero.',
            'quantity.min' => 'La cantidad debe ser al menos :min.',
            'adjustment_reason.required' => 'El motivo de ajuste es obligatorio.',
            'prohibited' => 'Este campo no debe estar presente.',
            'nullable' => 'Este campo puede estar vacío.',
            'unique' => 'Este valor ya está registrado.',
            'purchase_price.custom' => 'El precio de compra no puede ser mayor que el precio de venta.',
            'sale_price.custom' => 'El precio de venta no puede ser menor que el precio de compra.',
        ];
    }
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        if ($this->isMethod('post')) {
            return $this->user()->can('create', Inventory::class);
        }

        if ($this->isMethod('put') || $this->isMethod('patch')) {
            return $this->user()->can('update', $this->route('inventory'));
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
        // Si es creación de inventario, validar todos los campos
        if ($this->isMethod('post')) {
            return [
                'stock' => ['required', 'integer', 'min:0'],
                'min_stock' => ['required', 'integer', 'min:0', 'lte:stock'],
                'purchase_price' => ['required', 'numeric', 'min:0'],
                'sale_price' => ['required', 'numeric', 'min:0', 'gte:purchase_price'],
                'product_variant_id' => ['required', 'exists:product_variants,id'],
                'warehouse_id' => ['required', 'exists:warehouses,id'],
            ];
        }

        // Si es transferencia, solo validar los campos del form
        $type = $this->input('movement_type');
        if ($type === 'transfer') {
            return [
                'movement_type' => ['required'],
                'destination_warehouse_id' => [
                    'required',
                    'exists:warehouses,id',
                    function ($attribute, $value, $fail) {
                        // No permitir transferir al mismo almacén
                        $inventory = $this->route('inventory');
                        if ($inventory && $inventory->warehouse_id == $value) {
                            $fail('No puedes transferir al mismo almacén de origen.');
                        }
                    }
                ],
                'quantity' => [
                    'nullable',
                    'integer',
                    'min:1',
                    function ($attribute, $value, $fail) {
                        $inventory = $this->route('inventory');
                        if ($inventory && $value !== null && $value > $inventory->stock) {
                            $fail('No puedes transferir más cantidad que el stock disponible.');
                        }
                    }
                ],
            ];
        }

        // Si es ajuste, validar los campos del form
        if ($type === 'adjustment') {
            $reason = $this->input('adjustment_reason');
            $rules = [
                'movement_type' => ['required'],
                'adjustment_reason' => ['required'],
            ];
            if (in_array($reason, ['damage', 'theft'])) {
                $rules['quantity'] = [
                    'required',
                    'integer',
                    'min:1',
                    function ($attribute, $value, $fail) {
                        $inventory = $this->route('inventory');
                        if ($inventory && $value > $inventory->stock) {
                            $fail('No puedes registrar una cantidad mayor al stock disponible (' . $inventory->stock . ').');
                        }
                    }
                ];
                $rules['purchase_price'] = ['nullable', 'prohibited'];
                $rules['sale_price'] = ['nullable', 'prohibited'];
            } elseif (in_array($reason, ['correction', 'physical_count'])) {
                $rules['quantity'] = ['required', 'integer', 'min:1'];
                $rules['purchase_price'] = ['nullable', 'prohibited'];
                $rules['sale_price'] = ['nullable', 'prohibited'];
            } elseif ($reason === 'purchase_price') {
                $rules['quantity'] = ['nullable', 'prohibited'];
                $rules['purchase_price'] = [
                    'required',
                    'numeric',
                    'min:0',
                    function ($attribute, $value, $fail) {
                        $salePrice = $this->input('sale_price');
                        if ($salePrice !== null && $value > $salePrice) {
                            $fail('El precio de compra no puede ser mayor que el precio de venta.');
                        }
                    }
                ];
                $rules['sale_price'] = ['nullable', 'prohibited'];
            } elseif ($reason === 'sale_price') {
                $rules['quantity'] = ['nullable', 'prohibited'];
                $rules['purchase_price'] = ['nullable', 'prohibited'];
                $rules['sale_price'] = [
                    'required',
                    'numeric',
                    'min:0',
                    function ($attribute, $value, $fail) {
                        $purchasePrice = $this->input('purchase_price');
                        if ($purchasePrice !== null && $value < $purchasePrice) {
                            $fail('El precio de venta no puede ser menor que el precio de compra.');
                        }
                    }
                ];
            } else {
                $rules['quantity'] = ['nullable', 'prohibited'];
                $rules['purchase_price'] = ['nullable', 'prohibited'];
                $rules['sale_price'] = ['nullable', 'prohibited'];
            }
            return $rules;
        }

        // Si no hay movimiento, no validar nada extra
        return [];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->product_variant_id && $this->warehouse_id && $this->isMethod('post')) {
                $exists = Inventory::where('product_variant_id', $this->product_variant_id)
                    ->where('warehouse_id', $this->warehouse_id)
                    ->exists();
                if ($exists) {
                    $validator->errors()->add('product_id', 'Ya existe este producto en el almacén seleccionado.');
                }
            }
        });
    }
}
