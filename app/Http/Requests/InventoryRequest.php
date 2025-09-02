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

        // Si es movimiento, validar según el tipo
        $type = $this->input('movement_type');
        if ($type === 'transfer') {
            return [
                'movement_type' => ['required'],
                'destination_warehouse_id' => ['required', 'exists:warehouses,id'],
                'quantity' => [
                    'nullable',
                    function ($attribute, $value, $fail) {
                        $inventory = $this->route('inventory');
                        $stock = $inventory ? $inventory->stock : 0;
                        $destWarehouseId = $this->input('destination_warehouse_id');
                        $productId = $inventory ? $inventory->product_id : null;
                        $destInventory = null;
                        if ($productId && $destWarehouseId) {
                            $destInventory = \App\Models\Inventory::where('product_id', $productId)
                                ->where('warehouse_id', $destWarehouseId)
                                ->first();
                        }
                        // Si el producto ya existe en el almacén destino, no permitir transferir si ya está todo transferido
                        if ($destInventory && $destInventory->id != $inventory->id) {
                            // Si se envía cantidad, verificar que no exceda el stock origen y que no duplique el stock destino
                            $transferQty = ($value !== null && $value !== '') ? intval($value) : $stock;
                            if ($transferQty < 1) {
                                $fail('La cantidad debe ser un número entero mayor a 0.');
                            }
                            if ($transferQty > $stock) {
                                $fail('Stock insuficiente para transferir la cantidad solicitada.');
                            }
                            // Si ya está todo transferido
                            if ($stock < 1) {
                                $fail('No hay stock disponible para transferir.');
                            }
                            // Si el destino ya tiene el mismo stock que el origen, no permitir
                            if ($destInventory->stock >= $stock) {
                                $fail('El almacén destino ya tiene el stock máximo posible para este producto.');
                            }
                        } else {
                            // Si se envía cantidad, debe ser entero y mayor a 0 y no exceder el stock
                            if ($value !== null && $value !== '') {
                                if (!is_numeric($value) || intval($value) < 1) {
                                    $fail('La cantidad debe ser un número entero mayor a 0.');
                                } elseif (intval($value) > $stock) {
                                    $fail('Stock insuficiente para transferir la cantidad solicitada.');
                                }
                            } else {
                                if ($stock < 1) {
                                    $fail('No hay stock disponible para transferir.');
                                }
                            }
                        }
                    }
                ],
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
