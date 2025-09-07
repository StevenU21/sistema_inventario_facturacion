<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ccc; padding: 6px; }
        th { background: #f3f4f6; }
        h1 { font-size: 18px; margin: 0 0 10px 0; }
        .meta p { margin: 2px 0; }
    </style>
</head>
<body>
    <h1 style="font-size:22px; margin-bottom:2px;">{{ $company->name ?? 'Kardex' }}</h1>
    <h2 style="font-size:16px; margin-top:0; margin-bottom:10px;">Kardex - Costo Promedio Ponderado</h2>
    <div class="meta">
        <p><strong>Producto:</strong> {{ $kardexModel->product->name }}</p>
        <p><strong>Almacén:</strong> {{ $kardexModel->warehouse->name ?? 'Todos' }}</p>
        <p><strong>Rango:</strong> {{ $kardexModel->date_from }} a {{ $kardexModel->date_to }}</p>
    </div>

    <p><strong>Saldo inicial:</strong> Cantidad {{ $kardexModel->initial['qty'] }}, Costo Promedio {{ number_format($kardexModel->initial['unit_cost'], 2) }}, Total {{ number_format($kardexModel->initial['total'], 2) }}</p>

    <table>
        <thead>
            <tr>
                <th>Fecha y hora</th>
                <th>Concepto</th>
                <th>Almacén</th>
                <th style="text-align:right">Entrada (Cant.)</th>
                <th style="text-align:right">Salida (Cant.)</th>
                <th style="text-align:right">Existencias</th>
                <th style="text-align:right">Costo unitario</th>
                <th style="text-align:right">Costo promedio</th>
                <th style="text-align:right">Debe</th>
                <th style="text-align:right">Haber</th>
                <th style="text-align:right">Saldo</th>
            </tr>
        </thead>
        <tbody>
            @forelse($kardexModel->rows as $r)
                <tr>
                    <td>{{ $r['date'] }}</td>
                    <td>{{ $r['concept'] ?? '' }}</td>
                    <td>{{ $r['warehouse'] }}</td>
                    <td style="text-align:right">{{ $r['entry_qty'] }}</td>
                    <td style="text-align:right">{{ $r['exit_qty'] }}</td>
                    <td style="text-align:right">{{ $r['balance_qty'] }}</td>
                    <td style="text-align:right">{{ number_format($r['unit_cost'], 2) }}</td>
                    <td style="text-align:right">{{ number_format($r['avg_cost'], 2) }}</td>
                    <td style="text-align:right">{{ number_format($r['debe'], 2) }}</td>
                    <td style="text-align:right">{{ number_format($r['haber'], 2) }}</td>
                    <td style="text-align:right">{{ number_format($r['saldo'], 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="11">Sin movimientos en el rango.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <p><strong>Determinación final del inventario:</strong>
        Unidades finales {{ $kardexModel->final['qty'] }} × Costo promedio {{ number_format($kardexModel->final['unit_cost'], 2) }}
        = <strong>{{ number_format($kardexModel->final['qty'] * $kardexModel->final['unit_cost'], 2) }}</strong>
    </p>
    <p>Saldo final reportado: <strong>{{ number_format($kardexModel->final['total'], 2) }}</strong></p>
</body>
</html>
