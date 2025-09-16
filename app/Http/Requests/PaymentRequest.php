<?php

namespace App\Http\Requests;

use App\Models\Payment;
use App\Models\AccountReceivable;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PaymentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        if ($this->isMethod('post')) {
            return $this->user()->can('create', Payment::class);
        }

        if ($this->isMethod('put') || $this->isMethod('patch')) {
            return $this->user()->can('update', $this->route('payment'));
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
        if ($this->isMethod('post')) {
            return [
                'account_receivable_id' => ['required', 'integer', 'exists:account_receivables,id'],
                'payment_method_id' => ['required', 'integer', 'exists:payment_methods,id'],
                'amount' => ['required', 'numeric', 'min:0.01'],
                'payment_date' => ['nullable', 'date'],
            ];
        }

        // Para updates (si se implementan)
        return [];
    }

    public function attributes(): array
    {
        return [
            'account_receivable_id' => 'cuenta por cobrar',
            'payment_method_id' => 'método de pago',
            'amount' => 'monto',
            'payment_date' => 'fecha de pago',
        ];
    }

    public function messages(): array
    {
        return [
            'account_receivable_id.required' => 'La :attribute es obligatoria.',
            'account_receivable_id.exists' => 'La :attribute seleccionada no existe.',
            'payment_method_id.required' => 'El :attribute es obligatorio.',
            'payment_method_id.exists' => 'El :attribute seleccionado no existe.',
            'amount.required' => 'El :attribute es obligatorio.',
            'amount.numeric' => 'El :attribute debe ser un número válido.',
            'amount.min' => 'El :attribute debe ser mayor que 0.',
            'payment_date.date' => 'La :attribute no tiene un formato de fecha válido.',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if (!$this->isMethod('post')) {
                return;
            }
            $arId = $this->input('account_receivable_id');
            $amount = (float) $this->input('amount', 0);
            if ($arId) {
                $ar = AccountReceivable::find($arId);
                if ($ar) {
                    if ($ar->status === 'paid') {
                        $validator->errors()->add('account_receivable_id', 'La cuenta por cobrar ya está pagada en su totalidad.');
                    }
                    $remaining = round((float) $ar->amount_due - (float) $ar->amount_paid, 2);
                    if ($amount > 0 && $amount > $remaining) {
                        $validator->errors()->add('amount', 'El monto no puede exceder el saldo pendiente (' . number_format($remaining, 2) . ').');
                    }
                }
            }
        });
    }
}
