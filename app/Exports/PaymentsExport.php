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
        // Eager load related models for performance
        $payments = $this->query
            ->with([
                'user',
                'entity',
                'paymentMethod',
                'accountReceivable.sale.saleDetails.productVariant.product',
            ])
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->get();

        return $payments->map(function ($payment) {
            $client = $payment->entity?->short_name
                ?: trim(($payment->entity->first_name ?? '') . ' ' . ($payment->entity->last_name ?? ''))
                ?: '-';
            $user = $payment->user?->short_name ?? ($payment->user?->full_name ?? '-');
            $product = $payment->accountReceivable?->sale?->saleDetails?->first()?->productVariant?->product?->name ?? '-';
            return [
                'id' => $payment->id,
                'usuario' => $user,
                'cliente' => $client,
                'producto' => $product,
                'metodo' => $payment->paymentMethod->name ?? '-',
                'monto' => (float) ($payment->amount ?? 0),
                'fecha' => $payment->formatted_created_at ?? null,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'ID',
            'Usuario',
            'Cliente',
            'Producto',
            'Método de pago',
            'Monto',
            'Fecha',
        ];
    }
}
