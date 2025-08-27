<?php

namespace App\Http\Requests;

use App\Models\Municipality;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MunicipalityRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        if ($this->isMethod('post')) {
            return $this->user()->can('create', Municipality::class);
        }

        if ($this->isMethod('put') || $this->isMethod('patch')) {
            return $this->user()->can('update', $this->route('municipality'));
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
            'name' => ['required', 'string', 'min:3', 'max:60', Rule::unique('municipalities')->ignore($this->municipality)],
            'description' => ['nullable', 'string', 'max:120'],
            'department_id' => ['required', 'exists:departments,id'],
        ];
    }
}
