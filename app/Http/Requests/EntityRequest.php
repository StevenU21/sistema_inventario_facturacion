<?php

namespace App\Http\Requests;

use App\Models\Company;
use App\Models\Entity;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EntityRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        if ($this->isMethod('post')) {
            return $this->user()->can('create', Entity::class);
        }

        if ($this->isMethod('put') || $this->isMethod('patch')) {
            return $this->user()->can('update', $this->route('entity'));
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
