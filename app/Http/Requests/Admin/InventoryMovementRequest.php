<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class InventoryMovementRequest extends FormRequest
{
    public function authorize()
    {
        // Puedes personalizar la autorización si lo necesitas
        return true;
    }

    public function rules()
    {
        return [
            'inventory_id' => 'required|exists:inventories,id',
            'type' => 'required|string',
            'quantity' => 'required|integer|min:1',
            'unit_price' => 'nullable|numeric|min:0',
            'reference' => 'nullable|string',
            'notes' => 'nullable|string',
        ];
    }

    public function messages()
    {
        return [
            'inventory_id.required' => 'El inventario es obligatorio.',
            'inventory_id.exists' => 'El inventario seleccionado no existe.',
            'type.required' => 'El tipo de movimiento es obligatorio.',
            'quantity.required' => 'La cantidad es obligatoria.',
            'quantity.integer' => 'La cantidad debe ser un número entero.',
            'quantity.min' => 'La cantidad debe ser al menos 1.',
            'unit_price.numeric' => 'El precio unitario debe ser un número.',
            'unit_price.min' => 'El precio unitario no puede ser negativo.',
        ];
    }
}
