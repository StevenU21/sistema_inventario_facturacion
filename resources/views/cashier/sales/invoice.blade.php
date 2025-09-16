<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Factura #{{ $sale->id }}</title>
    <style>
        @page {
            size: 80mm auto;
            margin: 2mm;
        }

        html,
        body {
            margin: 0;
            padding: 0;
            background: #fff;
        }

        body {
            margin-top: 10mm;
        }

        body {
            font-family: "Courier New", Courier, monospace;
            color: #111;
            font-size: 11px;
            -webkit-print-color-adjust: exact;
        }

        .voucher-box {
            width: 65mm;
            max-width: 65mm;
            margin: 0 auto;
            box-sizing: border-box;
            padding: 6px 6px 12px 6px;
            background: #fff;
        }

        .center {
            text-align: center;
        }

        .right {
            text-align: right;
        }

        .left {
            text-align: left;
        }

        .bold {
            font-weight: 700;
        }

        .muted {
            font-size: 9px;
            color: #333;
        }

        .small {
            font-size: 9.5px;
        }

        .tiny {
            font-size: 8.5px;
        }

        .company-title {
            font-size: 22px;
            /* mucho más grande */
            letter-spacing: 0.5px;
            text-transform: uppercase;
            /* todo en mayúsculas */
        }

        .divider {
            border-top: 1px dashed #222;
            margin: 6px 0;
            height: 0;
        }

        .items {
            width: 100%;
            border-collapse: collapse;
            margin-top: 2px;
        }

        .items th,
        .items td {
            padding: 3px 0;
            vertical-align: middle;
        }

        .items thead th {
            font-size: 10.5px;
            /* ligeramente menor que el cuerpo */
            font-weight: 400;
            /* sin bold en encabezados */
            color: inherit;
            /* sin color especial, usa el estándar del documento */
        }

        /* divisor punteado entre encabezado (thead) y cuerpo (tbody) */
        .items tbody tr:first-child td {
            border-top: 1px dashed #222;
        }

        .cell {
            font-size: 11px;
            /* armoniza con el tamaño base del documento */
        }

        .col-center {
            text-align: center;
        }

        .col-right {
            text-align: right;
        }

        .item-desc {
            font-size: 10.5px;
            word-break: break-word;
            padding-right: 6px;
            width: 32mm;
            max-width: 32mm;
        }

        .num {
            font-size: 10px;
            /* un poco más grande para números sueltos */
        }

        .item-qty {
            width: 8mm;
            text-align: center;
            font-size: 10px;
        }

        .item-unit {
            width: 12mm;
            text-align: right;
            font-size: 10px;
        }

        .item-subtotal {
            width: 13mm;
            text-align: right;
            font-size: 10px;
        }

        .totals {
            margin-top: 6px;
            width: 100%;
        }

        .totals .row {
            display: flex;
            justify-content: space-between;
            padding: 2px 0;
            font-size: 10.5px;
        }

        .totals .total-amount {
            font-size: 13px;
            font-weight: 700;
            margin-top: 4px;
            margin-bottom: 4px;
        }

        .tear {
            border-top: 1px dotted #222;
            margin: 8px 0 6px 0;
        }

        @media print {
            .voucher-box {
                box-shadow: none;
            }
        }
    </style>
    @php $currency = 'C$'; @endphp
</head>

<body>
    <div class="voucher-box">
        <div class="center company-title bold">{{ strtoupper($company->name ?? 'EMPRESA') }}</div>
        <div class="center tiny muted">{{ $company->address ?? '' }}</div>
        <div class="center tiny">Tel: {{ $company->phone ?? '' }} RUC: {{ $company->tax_id ?? '' }}</div>

        <div class="center bold" style="font-size:14px;">FACTURA: {{ $sale->id }}</div>
        <div class="center tiny">
            Contado:
            @if ($sale->is_credit == true || $sale->is_credit == '1' || $sale->is_credit == 1)
                [ ]
                &nbsp;&nbsp;
                Crédito: [X]
            @else
                [X]
                &nbsp;&nbsp;
                Crédito: [ ]
            @endif
        </div>
        <div class="center tiny">
            Fecha: {{ \Carbon\Carbon::parse($sale->sale_date ?? $sale->created_at)->format('d/m/Y h:i a') }}
        </div>
        <div class="center tiny mb-2">Vendedor: {{ $sale->user?->full_name ?? '-' }}</div>
        <br>
        <div class="small"><strong>Cliente:</strong> {{ $sale->entity?->full_name ?? 'CLIENTE DE CONTADO' }}</div>

        <div class="divider"></div>

        <div class="bold" style="margin-bottom:2px;">Nombre</div>

        <table class="items">
            <colgroup>
                <col style="width:26%">
                <col style="width:18%">
                <col style="width:14%">
                <col style="width:21%">
                <col style="width:21%">
            </colgroup>
            <thead>
                <tr class="col-header">
                    <th class="col-center">Color</th>
                    <th class="col-center">Talla</th>
                    <th class="col-center">Cantidad</th>
                    <th class="col-right">Precio</th>
                    <th class="col-right">Subtotal</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($details as $d)
                    <tr>
                        <td colspan="5" class="item-desc left">
                            <span
                                class="bold">{{ $d->productVariant?->product?->name ?? ($d->productVariant?->sku ?? '-') }}</span>
                        </td>
                    </tr>
                    <tr>
                        @php
                            $color = $d->productVariant?->color?->name;
                            $size = $d->productVariant?->size?->name;
                        @endphp
                        <td class="cell col-center">{{ $color ?? '-' }}</td>
                        <td class="cell col-center">{{ $size ?? '-' }}</td>
                        <td class="cell col-center">{{ intval($d->quantity) }}</td>
                        <td class="cell col-right">{{ $currency }}{{ number_format($d->unit_price, 2) }}</td>
                        <td class="cell col-right">{{ $currency }}{{ number_format($d->sub_total, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="divider"></div>

        <div class="totals">
            <div class="row">
                <div class="left bold">Total:</div>
                <div class="right small bold total-amount">{{ $currency }}{{ number_format($sale->total, 2) }}
                </div>
            </div>

            @if (!empty($sale->tax_amount) && $sale->tax_amount != 0)
                <div class="row small">
                    <div class="left">IVA:</div>
                    <div class="right">{{ $currency }}{{ number_format($sale->tax_amount, 2) }}</div>
                </div>
            @endif
        </div>

        <div class="small meta">Cantidad de Artículos: {{ $details->sum('quantity') }}</div>
        <div class="small">Paga con: </div>
        <div class="small mb-2">Vuelto: </div>

        <div class="tear"></div>

        <div class="center tiny">{{ $company->name ?? 'EMPRESA' }}</div>
        <div class="center tiny muted">Gracias por su compra, es un placer servirle.</div>

        <div class="divider"></div>
        <div class="small" style="margin-top:8px;">
            <table style="width:90%;margin:0 auto;">
                <tr>
                    <td style="width:45%;text-align:center;">
                        <span style="display:inline-block;width:90%;border-bottom:1px solid #222;">&nbsp;</span><br>
                        <span class="tiny">Firma Vendedor</span>
                    </td>
                    <td style="width:10%;"></td>
                    <td style="width:45%;text-align:center;">
                        <span style="display:inline-block;width:90%;border-bottom:1px solid #222;">&nbsp;</span><br>
                        <span class="tiny">Firma Cliente</span>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</body>

</html>
