<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 10px; }
        .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th, td { border: 1px solid #ccc; padding: 6px; text-align: left; }
        th { background: #f2f2f2; }
    </style>
    <title>Cuenta por Cobrar #{{ $ar->id }}</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta name="csrf-token" content="{{ csrf_token() }}"/>
    <meta name="robots" content="noindex, nofollow"/>
</head>
<body>
    <div class="header">
        <h2>{{ $company?->name ?? 'Empresa' }}</h2>
        <div>{{ $company?->address ?? '' }}</div>
        <div>{{ $company?->phone ?? '' }}</div>
        <h3>Cuenta por Cobrar #{{ $ar->id }}</h3>
    </div>

    <div class="grid">
        <div>
            <h4>Cliente</h4>
            @php $e = $ar->entity; @endphp
            <div><strong>Nombre:</strong> {{ $e?->short_name ?: trim(($e->first_name ?? '') . ' ' . ($e->last_name ?? '')) }}</div>
            <div><strong>Documento:</strong> {{ $e?->document_number ?? '-' }}</div>
            <div><strong>Teléfono:</strong> {{ $e?->phone ?? '-' }}</div>
            <div><strong>Email:</strong> {{ $e?->email ?? '-' }}</div>
        </div>
        <div>
            <h4>Resumen</h4>
            @php $balance = round(($ar->amount_due ?? 0) - ($ar->amount_paid ?? 0), 2); @endphp
            <div><strong>Venta:</strong> #{{ $ar->sale?->id }} ({{ $ar->sale?->sale_date }})</div>
            <div><strong>Monto total:</strong> {{ number_format($ar->amount_due, 2) }}</div>
            <div><strong>Pagado:</strong> {{ number_format($ar->amount_paid, 2) }}</div>
            <div><strong>Saldo:</strong> {{ number_format($balance, 2) }}</div>
            <div><strong>Estado:</strong> {{ $ar->translated_status }}</div>
        </div>
    </div>

    <h4>Detalle de Venta</h4>
    <table>
        <thead>
            <tr>
                <th>Producto</th>
                <th>Cant.</th>
                <th>P. Unit</th>
                <th>Sub Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($ar->sale?->saleDetails ?? [] as $d)
                <tr>
                    <td>{{ $d->productVariant?->product?->name }} {{ $d->productVariant?->sku ? '(' . $d->productVariant->sku . ')' : '' }}</td>
                    <td>{{ $d->quantity }}</td>
                    <td>{{ number_format($d->unit_price, 2) }}</td>
                    <td>{{ number_format($d->sub_total, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h4>Pagos</h4>
    <table>
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Método</th>
                <th>Monto</th>
                <th>Usuario</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($ar->payments as $p)
                <tr>
                    <td>{{ \Illuminate\Support\Carbon::parse($p->payment_date)->format('d/m/Y') }}</td>
                    <td>{{ $p->paymentMethod?->name ?? '-' }}</td>
                    <td>{{ number_format($p->amount, 2) }}</td>
                    <td>{{ $p->user?->name ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" style="text-align:center;color:#777">Sin pagos aún</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
