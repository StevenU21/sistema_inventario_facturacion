<?php

namespace App\Exports;

use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AccountReceivablesExport implements FromCollection, WithHeadings
{
    /** @var Builder */
    protected $query;

    public function __construct($query)
    {
        $this->query = $query;
    }

    public function collection()
    {
        $accounts = $this->query->get();

        return $accounts->map(function ($ar) {
            $client = $ar->entity?->short_name
                ?: trim(($ar->entity->first_name ?? '') . ' ' . ($ar->entity->last_name ?? ''))
                ?: '-';
            $saleId = $ar->sale?->id;
            $status = $ar->translated_status ?? $ar->status;
            $amountDue = (float) ($ar->amount_due ?? 0);
            $amountPaid = (float) ($ar->amount_paid ?? 0);
            $balance = round($amountDue - $amountPaid, 2);
            $saleDate = $ar->sale?->sale_date ? \Illuminate\Support\Carbon::parse($ar->sale->sale_date)->format('d/m/Y') : null;

            return [
                'id' => $ar->id,
                'cliente' => $client,
                'venta_id' => $saleId,
                'fecha_venta' => $saleDate,
                'monto_total' => $amountDue,
                'monto_pagado' => $amountPaid,
                'saldo' => $balance,
                'estado' => $status,
                'creado' => optional($ar->created_at)->format('d/m/Y H:i:s'),
                'actualizado' => optional($ar->updated_at)->format('d/m/Y H:i:s'),
            ];
        });
    }

    public function headings(): array
    {
        return [
            'ID',
            'Cliente',
            'Venta ID',
            'Fecha venta',
            'Monto total',
            'Monto pagado',
            'Saldo',
            'Estado',
            'Creado',
            'Actualizado',
        ];
    }
}
