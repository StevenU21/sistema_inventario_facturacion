<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proforma</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 6px; }
        th { background: #f5f5f5; text-align: left; }
        .text-right { text-align: right; }
        .mb-2 { margin-bottom: 8px; }
        .mb-4 { margin-bottom: 16px; }
    </style>
    </head>
<body>
    <h2 class="mb-2">Proforma</h2>
    <div class="mb-4">
        <div><strong>Empresa:</strong> {{ $company?->name ?? 'Mi Empresa' }}</div>
        <div><strong>Cliente:</strong> {{ $entity?->name ?? 'N/D' }}</div>
        <div><strong>Fecha:</strong> {{ \Illuminate\Support\Carbon::parse($quotation_date)->format('d/m/Y') }}</div>
        <div><strong>Vendedor:</strong> {{ $user?->name ?? 'N/D' }}</div>
    </div>

    <table class="mb-4">
        <thead>
            <tr>
                <th>Producto</th>
                <th>Variante</th>
                <th class="text-right">Cantidad</th>
                <th class="text-right">Precio unit.</th>
                <th class="text-right">Impuesto unit.</th>
                <th class="text-right">Descuento</th>
                <th class="text-right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($details as $d)
                <tr>
                    <td>{{ $d['variant']->product?->name ?? 'Producto' }}</td>
                    <td>{{ $d['variant']->sku ?? $d['variant']->code ?? 'Variante' }}</td>
                    <td class="text-right">{{ number_format($d['quantity'], 0) }}</td>
                    <td class="text-right">{{ number_format($d['unit_price'] - $d['unit_tax_amount'], 2) }}</td>
                    <td class="text-right">{{ number_format($d['unit_tax_amount'], 2) }}</td>
                    <td class="text-right">{{ $d['discount'] ? number_format($d['discount_amount'], 2) : '0.00' }}</td>
                    <td class="text-right">{{ number_format($d['sub_total'], 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table>
        <tbody>
            <tr>
                <th style="border:none">Impuestos</th>
                <td class="text-right">{{ number_format($totals['totalTax'] ?? 0, 2) }}</td>
            </tr>
            <tr>
                <th style="border:none">Total</th>
                <td class="text-right"><strong>{{ number_format($totals['total'] ?? 0, 2) }}</strong></td>
            </tr>
        </tbody>
    </table>
</body>
</html>
