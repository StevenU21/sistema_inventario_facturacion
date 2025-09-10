@extends('layouts.app')
@section('title', 'Editar Compra')

@section('content')
    <div class="container px-6 mx-auto grid">
        <h2 class="my-6 text-2xl font-semibold text-gray-700 dark:text-gray-200">Editar Compra #{{ $purchase->id }}</h2>
        <x-session-message />

        <div class="mb-4 flex justify-end">
            <a href="{{ route('purchases.index') }}"
                class="flex items-center px-4 py-2 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-purple-600 border border-transparent rounded-lg active:bg-purple-600 hover:bg-purple-700 focus:outline-none focus:shadow-outline-purple">
                <i class="fas fa-arrow-left mr-2"></i> Volver
            </a>
        </div>

        <form method="POST" action="{{ route('purchases.update', $purchase) }}">
            @csrf
            @method('PUT')
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 max-w-4xl mb-6">
                @include('admin.purchases.form')
            </div>
            <div class="mt-4 flex gap-2">
                <a href="{{ route('purchases.index') }}" class="px-4 py-2 rounded bg-gray-200 text-gray-800">Volver</a>
                <button type="submit" class="px-4 py-2 rounded bg-purple-600 text-white">Guardar</button>
            </div>
        </form>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 max-w-4xl">
            <h3 class="text-lg font-semibold mb-3">Detalles</h3>
            <form method="POST" action="{{ route('purchases.details.store', $purchase) }}" class="grid grid-cols-1 md:grid-cols-4 gap-3 items-end">
                @csrf
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium">Producto / Variante</label>
                    <select name="product_variant_id" class="w-full px-3 py-2 border rounded-lg text-sm" required>
                        @foreach(($variants ?? []) as $id=>$label)
                            <option value="{{ $id }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium">Cantidad</label>
                    <input type="number" min="1" name="quantity" value="1" class="w-full px-3 py-2 border rounded-lg text-sm" required />
                </div>
                <div>
                    <label class="block text-sm font-medium">Precio unit.</label>
                    <input type="number" step="0.01" min="0" name="unit_price" value="0" class="w-full px-3 py-2 border rounded-lg text-sm" required />
                </div>
                <div class="md:col-span-4">
                    <button type="submit" class="px-4 py-2 rounded bg-green-600 text-white">Agregar detalle</button>
                </div>
            </form>

            <div class="w-full overflow-x-auto mt-4">
                <table class="w-full whitespace-no-wrap">
                    <thead>
                        <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b dark:border-gray-700 bg-gray-50 dark:text-gray-400 dark:bg-gray-800">
                            <th class="px-4 py-3">Producto</th>
                            <th class="px-4 py-3">Variante</th>
                            <th class="px-4 py-3 text-right">Cant.</th>
                            <th class="px-4 py-3 text-right">P. Unit</th>
                            <th class="px-4 py-3 text-right">Importe</th>
                            <th class="px-4 py-3">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y dark:divide-gray-700 dark:bg-gray-800">
                        @forelse($details as $d)
                            <tr class="text-gray-700 dark:text-gray-400">
                                <td class="px-4 py-3 text-sm">{{ $d->productVariant->product->name }}</td>
                                <td class="px-4 py-3 text-sm">
                                    @php
                                        $c = $d->productVariant->color->name ?? null;
                                        $s = $d->productVariant->size->name ?? null;
                                    @endphp
                                    {{ $c || $s ? ($c.' / '.$s) : 'Simple' }}
                                </td>
                                <td class="px-4 py-3 text-sm text-right">{{ $d->quantity }}</td>
                                <td class="px-4 py-3 text-sm text-right">C$ {{ number_format($d->unit_price, 2) }}</td>
                                <td class="px-4 py-3 text-sm text-right">C$ {{ number_format($d->quantity * $d->unit_price, 2) }}</td>
                                <td class="px-4 py-3">
                                    <form method="POST" action="{{ route('purchases.details.destroy', [$purchase, $d]) }}" onsubmit="return confirm('¿Quitar detalle?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600"><i class="fas fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-3 text-center text-gray-400 dark:text-gray-500">Sin detalles</td>
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
    </div>
@endsection
