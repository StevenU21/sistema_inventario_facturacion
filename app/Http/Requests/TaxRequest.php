<?php

namespace App\Http\Requests;

use App\Models\Tax;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TaxRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        if ($this->isMethod('post')) {
            return $this->user()->can('create', Tax::class);
        }

        if ($this->isMethod('put') || $this->isMethod('patch')) {
            return $this->user()->can('update', $this->route('tax'));
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
            'name' => ['required', 'string', 'max:255', Rule::unique('taxes')->ignore($this->tax)],
            'percentaje' => ['required', 'numeric', 'min:0', 'max:100'],
            'is_default' => ['nullable', 'boolean']
        ];
    }
}
    