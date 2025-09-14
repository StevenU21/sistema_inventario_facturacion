<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class RoleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        if ($this->isMethod('post')) {
            return $this->user()->can('create', Role::class);
        }

        if ($this->isMethod('put') || $this->isMethod('patch')) {
            return $this->user()->can('update', $this->route('role'));
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
            'name' => ['required', 'string', 'min:3', 'max:60', Rule::unique('roles')->ignore($this->role)],
            'permissions' => ['required', 'array', 'min:1'],
            'permissions.*' => ['exists:permissions,id'],
        ];
    }

    /**
     * Mensajes personalizados de validación en español.
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
            'name.unique' => 'El nombre ya está en uso.',
            'permissions.required' => 'Debe seleccionar al menos un permiso.',
            'permissions.array' => 'Los permisos deben ser un arreglo.',
            'permissions.min' => 'Debe seleccionar al menos un permiso.',
            'permissions.*.exists' => 'El permiso seleccionado no existe.',
        ];
    }

    /**
     * Atributos personalizados para los mensajes de validación.
     *
     * @return array
     */
    public function attributes(): array
    {
        return [
            'name' => 'nombre',
            'permissions' => 'permisos',
            'permissions.*' => 'permiso',
        ];
    }
}
