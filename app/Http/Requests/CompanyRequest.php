<?php

namespace App\Http\Requests;

use App\Models\Company;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CompanyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        if ($this->isMethod('post')) {
            return $this->user()->can('create', Company::class);
        }

        if ($this->isMethod('put') || $this->isMethod('patch')) {
            return $this->user()->can('update', $this->route('company'));
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
            'name' => ['required', 'string', 'min:3', 'max:60'],
            'ruc' => ['nullable', 'string', 'min:11', 'max:30', Rule::unique('companies')->ignore($this->company)],
            'logo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:4048'],
            'description' => ['nullable', 'string', 'min:10', 'max:100'],
            'address' => ['required', 'string', 'min:10', 'max:100',],
            'phone' => ['nullable', 'string', 'min:7', 'max:20', Rule::unique('companies')->ignore($this->company)],
            'email' => ['required', 'string', 'email', 'max:100', Rule::unique('companies')->ignore($this->company)]
        ];
    }
}
