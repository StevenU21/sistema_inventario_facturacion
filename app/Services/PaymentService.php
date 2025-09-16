<?php

namespace App\Services;

use App\Models\AccountReceivable;
use App\Models\Payment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PaymentService
{
    /**
     * Crea un pago para una cuenta por cobrar.
     *
     * Payload esperado:
     * - account_receivable_id: int
     * - payment_method_id: int
     * - amount: float
     * - payment_date?: Y-m-d
     *
     * @return array{accountReceivable: AccountReceivable, payment: Payment}
     * @throws ValidationException
     */
    public function createPayment(array $payload): array
    {
        return DB::transaction(function () use ($payload) {
            $userId = Auth::id();
            $ar = $this->getAccountReceivable($payload['account_receivable_id']);
            $amount = round((float) ($payload['amount'] ?? 0), 2);
            $payment = $this->createPaymentRecord($ar, $payload, $amount, $userId);
            $this->updateAccountReceivable($ar, $amount);
            return [
                'accountReceivable' => $ar->fresh(['payments', 'entity', 'sale']),
                'payment' => $payment,
            ];
        });
    }

    private function getAccountReceivable($id): AccountReceivable
    {
        return AccountReceivable::where('id', $id)
            ->lockForUpdate()
            ->firstOrFail();
    }

    private function createPaymentRecord(AccountReceivable $ar, array $payload, float $amount, $userId): Payment
    {
        return Payment::create([
            'amount' => $amount,
            'payment_date' => $payload['payment_date'] ?? now()->toDateString(),
            'account_receivable_id' => $ar->id,
            'payment_method_id' => $payload['payment_method_id'],
            'entity_id' => $ar->entity_id,
            'user_id' => $userId,
        ]);
    }

    private function updateAccountReceivable(AccountReceivable $ar, float $amount): void
    {
        $ar->amount_paid = round(((float) $ar->amount_paid) + $amount, 2);
        $ar->status = $this->calculateAccountReceivableStatus($ar);
        if ($ar->status === 'paid') {
            $ar->amount_paid = $ar->amount_due;
        }
        $ar->save();
    }

    private function calculateAccountReceivableStatus(AccountReceivable $ar): string
    {
        if ($ar->amount_paid >= $ar->amount_due) {
            return 'paid';
        } elseif ($ar->amount_paid > 0) {
            return 'partially_paid';
        } else {
            return 'pending';
        }
    }
}
