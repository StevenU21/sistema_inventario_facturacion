<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        if ($this->isMethod('post')) {
            return $this->user()->can('create', User::class);
        }

        if ($this->isMethod('put') || $this->isMethod('patch')) {
            return $this->user()->can('update', $this->route('user'));
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
            'email' => ['required', 'string', 'email', 'max:30', Rule::unique('users')->ignore($this->user)],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ];
    }
}
