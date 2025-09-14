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
            'ruc' => ['nullable', 'string', 'max:20', Rule::unique('entities')->ignore($this->entity)],
            'email' => ['nullable', 'string', 'email', 'max:255', Rule::unique('entities')->ignore($this->entity)],
            'phone' => ['required', 'string', 'max:20', Rule::unique('entities')->ignore($this->entity)],
            'address' => ['nullable', 'string', 'min:5', 'max:255'],
            'description' => ['nullable', 'string', 'min:1', 'max:120'],
            'is_client' => ['required', 'boolean'],
            'is_supplier' => ['required', 'boolean'],
            'is_active' => ['required', 'boolean'],
            'municipality_id' => ['required', 'exists:municipalities,id'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'first_name.required' => 'El nombre es obligatorio.',
            'first_name.string' => 'El nombre debe ser una cadena de texto.',
            'first_name.min' => 'El nombre debe tener al menos :min caracteres.',
            'first_name.max' => 'El nombre no debe exceder de :max caracteres.',
            'last_name.required' => 'El apellido es obligatorio.',
            'last_name.string' => 'El apellido debe ser una cadena de texto.',
            'last_name.min' => 'El apellido debe tener al menos :min caracteres.',
            'last_name.max' => 'El apellido no debe exceder de :max caracteres.',
            'identity_card.required' => 'La cédula es obligatoria.',
            'identity_card.string' => 'La cédula debe ser una cadena de texto.',
            'identity_card.max' => 'La cédula no debe exceder de :max caracteres.',
            'identity_card.unique' => 'La cédula ya está en uso.',
            'ruc.string' => 'El RUC debe ser una cadena de texto.',
            'ruc.max' => 'El RUC no debe exceder de :max caracteres.',
            'ruc.unique' => 'El RUC ya está en uso.',
            'email.string' => 'El correo electrónico debe ser una cadena de texto.',
            'email.email' => 'El correo electrónico debe ser válido.',
            'email.max' => 'El correo electrónico no debe exceder de :max caracteres.',
            'email.unique' => 'El correo electrónico ya está en uso.',
            'phone.required' => 'El teléfono es obligatorio.',
            'phone.string' => 'El teléfono debe ser una cadena de texto.',
            'phone.max' => 'El teléfono no debe exceder de :max caracteres.',
            'phone.unique' => 'El teléfono ya está en uso.',
            'address.string' => 'La dirección debe ser una cadena de texto.',
            'address.min' => 'La dirección debe tener al menos :min caracteres.',
            'address.max' => 'La dirección no debe exceder de :max caracteres.',
            'description.string' => 'La descripción debe ser una cadena de texto.',
            'description.min' => 'La descripción debe tener al menos :min caracteres.',
            'description.max' => 'La descripción no debe exceder de :max caracteres.',
            'is_client.required' => 'El campo cliente es obligatorio.',
            'is_client.boolean' => 'El campo cliente debe ser verdadero o falso.',
            'is_supplier.required' => 'El campo proveedor es obligatorio.',
            'is_supplier.boolean' => 'El campo proveedor debe ser verdadero o falso.',
            'is_active.required' => 'El campo activo es obligatorio.',
            'is_active.boolean' => 'El campo activo debe ser verdadero o falso.',
            'municipality_id.required' => 'El municipio es obligatorio.',
            'municipality_id.exists' => 'El municipio seleccionado no existe.',
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
            'first_name' => 'nombre',
            'last_name' => 'apellido',
            'identity_card' => 'cédula',
            'ruc' => 'RUC',
            'email' => 'correo electrónico',
            'phone' => 'teléfono',
            'address' => 'dirección',
            'description' => 'descripción',
            'is_client' => 'cliente',
            'is_supplier' => 'proveedor',
            'is_active' => 'activo',
            'municipality_id' => 'municipio',
        ];
    }
}
