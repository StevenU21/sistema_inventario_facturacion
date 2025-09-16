<?php

namespace App\Exports;

use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PaymentsExport implements FromCollection, WithHeadings
{
    /** @var Builder */
    protected $query;

    public function __construct($query)
    {
        $this->query = $query;
    }

    public function collection()
    {
        $payments = $this->query->get();

        return $payments->map(function ($payment) {
            $client = $payment->entity?->short_name
                ?: trim(($payment->entity->first_name ?? '') . ' ' . ($payment->entity->last_name ?? ''))
                ?: '-';
            $user = $payment->user?->short_name ?? ($payment->user?->name ?? '-');
            return [
                'id' => $payment->id,
                'cliente' => $client,
                'venta_id' => optional($payment->accountReceivable?->sale)->id,
                'metodo' => $payment->paymentMethod->name ?? '-',
                'monto' => (float) ($payment->amount ?? 0),
                'fecha_pago' => $payment->payment_date ? \Illuminate\Support\Carbon::parse($payment->payment_date)->format('d/m/Y') : null,
                'usuario' => $user,
                'creado' => optional($payment->created_at)->format('d/m/Y H:i:s'),
                'actualizado' => optional($payment->updated_at)->format('d/m/Y H:i:s'),
            ];
        });
    }

    public function headings(): array
    {
        return [
            'ID',
            'Cliente',
            'Venta ID',
            'Método de pago',
            'Monto',
            'Fecha de pago',
            'Usuario',
            'Creado',
            'Actualizado',
        ];
    }
}
