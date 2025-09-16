<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Pagos</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ccc; padding: 6px; }
        th { background: #f0f0f0; text-transform: uppercase; font-size: 11px; }
        h1 { margin: 0 0 10px 0; font-size: 18px; }
        .meta { margin-bottom: 10px; font-size: 12px; }
    </style>
</head>
<body>
    <h1>{{ $company?->name ?? 'Empresa' }} - Reporte de Pagos</h1>
    <div class="meta">
        Fecha de generación: {{ now()->format('d/m/Y H:i:s') }}
    </div>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Cliente</th>
                <th>Venta</th>
                <th>Método</th>
                <th>Monto</th>
                <th>Fecha Pago</th>
                <th>Usuario</th>
            </tr>
        </thead>
        <tbody>
            @foreach($payments as $p)
                <tr>
                    <td>{{ $p->id }}</td>
                    <td>{{ $p->entity?->short_name ?: trim(($p->entity->first_name ?? '') . ' ' . ($p->entity->last_name ?? '')) }}</td>
                    <td>#{{ optional($p->accountReceivable?->sale)->id }}</td>
                    <td>{{ $p->paymentMethod->name ?? '-' }}</td>
                    <td>${{ number_format($p->amount, 2) }}</td>
                    <td>{{ $p->payment_date ? \Carbon\Carbon::parse($p->payment_date)->format('d/m/Y') : '' }}</td>
                    <td>{{ $p->user?->name ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
