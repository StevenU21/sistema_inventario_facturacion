@extends('layouts.app')
@section('title', 'Compra #'.$purchase->id)

@section('content')
    <div class="container grid px-6 mx-auto">
        <h2 class="my-6 text-2xl font-semibold text-gray-700 dark:text-gray-200">Compra #{{ $purchase->id }}</h2>
        <x-session-message />

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 max-w-4xl mb-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div><strong>Referencia:</strong> {{ $purchase->reference ?? '-' }}</div>
                <div><strong>Proveedor:</strong> {{ $purchase->entity?->short_name ?? '-' }}</div>
                <div><strong>Almacén:</strong> {{ $purchase->warehouse?->name ?? '-' }}</div>
                <div><strong>Método:</strong> {{ $purchase->paymentMethod?->name ?? '-' }}</div>
                <div><strong>Usuario:</strong> {{ $purchase->user?->short_name ?? '-' }}</div>
                <div><strong>Fecha:</strong> {{ $purchase->formatted_created_at ?? $purchase->created_at }}</div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 max-w-4xl">
            <h3 class="text-lg font-semibold mb-3">Detalles</h3>
            <div class="w-full overflow-x-auto mt-2">
                <table class="w-full whitespace-no-wrap">
                    <thead>
                        <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b dark:border-gray-700 bg-gray-50 dark:text-gray-400 dark:bg-gray-800">
                            <th class="px-4 py-3">Producto</th>
                            <th class="px-4 py-3">Variante</th>
                            <th class="px-4 py-3 text-right">Cant.</th>
                            <th class="px-4 py-3 text-right">P. Unit</th>
                            <th class="px-4 py-3 text-right">Importe</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y dark:divide-gray-700 dark:bg-gray-800">
                        @forelse($details as $d)
                            <tr class="text-gray-700 dark:text-gray-400">
                                <td class="px-4 py-3 text-sm">{{ $d->productVariant->product->name }}</td>
                                <td class="px-4 py-3 text-sm">
                                    @php $c=$d->productVariant->color->name??null; $s=$d->productVariant->size->name??null; @endphp
                                    {{ $c || $s ? ($c.' / '.$s) : 'Simple' }}
                                </td>
                                <td class="px-4 py-3 text-sm text-right">{{ $d->quantity }}</td>
                                <td class="px-4 py-3 text-sm text-right">C$ {{ number_format($d->unit_price,2) }}</td>
                                <td class="px-4 py-3 text-sm text-right">C$ {{ number_format($d->quantity*$d->unit_price,2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-3 text-center text-gray-400 dark:text-gray-500">Sin detalles</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4 text-right">
                <div class="text-sm">Subtotal: <strong>C$ {{ number_format($purchase->subtotal, 2) }}</strong></div>
                <div class="text-sm">Total: <strong>C$ {{ number_format($purchase->total, 2) }}</strong></div>
            </div>
        </div>

        <div class="mt-4">
            <a href="{{ route('purchases.index') }}" class="px-4 py-2 rounded bg-gray-200 text-gray-800">Volver</a>
            <a href="{{ route('purchases.edit', $purchase) }}" class="px-4 py-2 rounded bg-purple-600 text-white">Editar</a>
        </div>
    </div>
@endsection
