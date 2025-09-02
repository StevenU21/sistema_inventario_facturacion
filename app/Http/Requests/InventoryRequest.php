<?php

namespace App\Http\Requests;

use App\Models\Inventory;
use Illuminate\Foundation\Http\FormRequest;

class InventoryRequest extends FormRequest
{
    protected function prepareForValidation()
    {
        if ($this->has('quantity') && $this->input('quantity') === '') {
            $this->merge(['quantity' => null]);
        }
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
                'product_id' => ['required', 'exists:products,id'],
                'warehouse_id' => ['required', 'exists:warehouses,id'],
            ];
        }

        // Si es transferencia, solo validar los campos del form
        $type = $this->input('movement_type');
        if ($type === 'transfer') {
            return [
                'movement_type' => ['required'],
                'destination_warehouse_id' => ['required', 'exists:warehouses,id'],
                'quantity' => ['nullable', 'integer', 'min:1'],
            ];
        }
        // Si no hay movimiento, no validar nada extra
        return [];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->product_id && $this->warehouse_id && $this->isMethod('post')) {
                $exists = Inventory::where('product_id', $this->product_id)
                    ->where('warehouse_id', $this->warehouse_id)
                    ->exists();
                if ($exists) {
                    $validator->errors()->add('product_id', 'Este producto ya existe en el almacén seleccionado.');
                }
            }
        });
    }
}
