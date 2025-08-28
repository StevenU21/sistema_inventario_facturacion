<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EntityRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        if (!$user)
            return false;

        $isClient = $this->boolean('is_client');
        $isSupplier = $this->boolean('is_supplier');

        if ($this->isMethod('post')) {
            if (($isClient && !$user->can('create clients')) || ($isSupplier && !$user->can('create suppliers'))) {
                return false;
            }
        }

        if ($this->isMethod('put')) {
            if (($isClient && !$user->can('update clients')) || ($isSupplier && !$user->can('update suppliers'))) {
                return false;
            }
        }

        return true;
    }

    protected function failedAuthorization()
    {
        throw \Illuminate\Validation\ValidationException::withMessages([
            'error' => 'No tiene permisos para crear o editar clientes/proveedores.'
        ]);
    }

    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'min:2', 'max:60'],
            'last_name' => ['required', 'string', 'min:2', 'max:60'],
            'identity_card' => ['required', 'string', 'max:30', Rule::unique('entities')->ignore($this->entity)],
            'ruc' => ['required', 'string', 'max:20', Rule::unique('entities')->ignore($this->entity)],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('entities')->ignore($this->entity)],
            'phone' => ['nullable', 'string', 'max:20', Rule::unique('entities')->ignore($this->entity)],
            'address' => ['nullable', 'string', 'min:5', 'max:255'],
            'description' => ['nullable', 'string', 'min:5', 'max:120'],
            'is_client' => ['required', 'boolean'],
            'is_supplier' => ['required', 'boolean'],
            'is_active' => ['required', 'boolean'],
            'municipality_id' => ['required', 'exists:municipalities,id'],
        ];
    }
}
