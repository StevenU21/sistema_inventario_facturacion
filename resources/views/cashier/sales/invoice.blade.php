<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Factura #{{ $sale->id }}</title>
    <style>
        body {
            font-family: DejaVu Sans, Arial, Helvetica, sans-serif;
            font-size: 12px;
            color: #111;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .company {
            font-size: 14px;
            font-weight: bold;
        }

        .meta {
            text-align: right;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 6px;
        }

        th {
            background: #f5f5f5;
        }

        .totals {
            margin-top: 10px;
            width: 100%;
        }

        .totals td {
            padding: 4px 6px;
        }

        .right {
            text-align: right;
        }
    </style>
    @php
        $currency = '$';
    @endphp
</head>

<body>
    <div class="header">
        <div class="company">
            @if ($company?->logo)
                <img src="{{ public_path('storage/' . $company->logo) }}" alt="logo" height="50">
            @endif
            <div>{{ $company->name ?? 'Empresa' }}</div>
            <div>{{ $company->address ?? '' }}</div>
            <div>{{ $company->phone ?? '' }}</div>
        </div>
        <div class="meta">
            <div><strong>Factura:</strong> #{{ $sale->id }}</div>
            <div><strong>Fecha:</strong>
                {{ \Carbon\Carbon::parse($sale->sale_date ?? $sale->created_at)->format('d/m/Y') }}</div>
            <div><strong>Cajero:</strong> {{ $sale->user?->name }}</div>
        </div>
    </div>
    <div>
        <strong>Cliente:</strong> {{ $sale->entity?->name }}<br>
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Producto</th>
                <th>Variante</th>
                <th class="right">Cant</th>
                <th class="right">P. Unit</th>
                <th class="right">Desc</th>
                <th class="right">Sub Total</th>
            </tr>
        </thead>
        <tbody>
            @php $i=1; @endphp
            @foreach ($details as $d)
                @php
                    $prod = $d->productVariant?->product;
                    $variantLabel = trim(
                        ($d->productVariant?->color?->name ?? '') . ' ' . ($d->productVariant?->size?->name ?? ''),
                    );
                @endphp
                <tr>
                    <td>{{ $i++ }}</td>
                    <td>{{ $prod?->name }}</td>
                    <td>{{ $variantLabel }}</td>
                    <td class="right">{{ $d->quantity }}</td>
                    <td class="right">{{ $currency }} {{ number_format($d->unit_price, 2) }}</td>
                    <td class="right">{{ $currency }} {{ number_format($d->discount_amount ?? 0, 2) }}</td>
                    <td class="right">{{ $currency }} {{ number_format($d->sub_total, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="totals">
        <tr>
            <td class="right"><strong>Impuesto:</strong></td>
            <td class="right" style="width:120px;">{{ $currency }} {{ number_format($sale->tax_amount ?? 0, 2) }}
            </td>
        </tr>
        <tr>
            <td class="right"><strong>Total:</strong></td>
            <td class="right">{{ $currency }} {{ number_format($sale->total, 2) }}</td>
        </tr>
        @if ($sale->is_credit)
            <tr>
                <td class="right"><strong>Condición:</strong></td>
                <td class="right">Crédito</td>
            </tr>
        @endif
    </table>

    <p style="margin-top: 20px;">Gracias por su compra.</p>
</body>

</html>
