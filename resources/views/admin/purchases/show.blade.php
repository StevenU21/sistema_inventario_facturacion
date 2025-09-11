@extends('layouts.app')
@section('title', 'Compra #' . $purchase->id)

@section('content')
    <div class="container grid px-6 mx-auto">
        <x-session-message />

        @php
            $supplierFullName = trim(
                ($purchase->entity->first_name ?? '') . ' ' . ($purchase->entity->last_name ?? ''),
            );
            $supplierLabel = $supplierFullName !== '' ? $supplierFullName : $purchase->entity->short_name ?? '-';
        @endphp

        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 my-6">
            <div>
                <h2 class="text-2xl font-semibold text-gray-800 dark:text-gray-100">Compra #{{ $purchase->id }}</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">Referencia: {{ $purchase->reference ?: '—' }}</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('purchases.index') }}"
                    class="px-4 py-2 rounded-lg text-sm font-medium border border-gray-300 text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-700">
                    <i class="fas fa-arrow-left mr-2"></i>Volver
                </a>
                <a href="{{ route('purchases.exportDetails', $purchase) }}"
                    class="px-4 py-2 rounded-lg text-sm font-medium bg-green-600 hover:bg-green-700 text-white">
                    <i class="fas fa-file-excel mr-2"></i>Exportar Excel
                </a>
            </div>
        </div>

        <!-- Meta info -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-100 dark:border-gray-700">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 p-5 text-sm">
                <div class="flex items-start gap-3">
                    <div
                        class="shrink-0 h-9 w-9 flex items-center justify-center rounded-lg bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300">
                        <i class="fas fa-user"></i>
                    </div>
                    <div>
                        <div class="text-gray-500 dark:text-gray-400">Proveedor</div>
                        <div class="font-medium text-gray-800 dark:text-gray-100">{{ $supplierLabel }}</div>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <div
                        class="shrink-0 h-9 w-9 flex items-center justify-center rounded-lg bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300">
                        <i class="fas fa-warehouse"></i>
                    </div>
                    <div>
                        <div class="text-gray-500 dark:text-gray-400">Almacén</div>
                        <div class="font-medium text-gray-800 dark:text-gray-100">{{ $purchase->warehouse?->name ?? '-' }}
                        </div>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <div
                        class="shrink-0 h-9 w-9 flex items-center justify-center rounded-lg bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300">
                        <i class="fas fa-money-check-alt"></i>
                    </div>
                    <div>
                        <div class="text-gray-500 dark:text-gray-400">Método de pago</div>
                        <div class="font-medium text-gray-800 dark:text-gray-100">
                            {{ $purchase->paymentMethod?->name ?? '-' }}</div>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <div
                        class="shrink-0 h-9 w-9 flex items-center justify-center rounded-lg bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300">
                        <i class="fas fa-user-circle"></i>
                    </div>
                    <div>
                        <div class="text-gray-500 dark:text-gray-400">Usuario</div>
                        <div class="font-medium text-gray-800 dark:text-gray-100">{{ $purchase->user?->short_name ?? '-' }}
                        </div>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <div
                        class="shrink-0 h-9 w-9 flex items-center justify-center rounded-lg bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-300">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <div>
                        <div class="text-gray-500 dark:text-gray-400">Fecha</div>
                        <div class="font-medium text-gray-800 dark:text-gray-100">
                            {{ $purchase->formatted_created_at ?? $purchase->created_at }}</div>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <div
                        class="shrink-0 h-9 w-9 flex items-center justify-center rounded-lg bg-fuchsia-100 text-fuchsia-700 dark:bg-fuchsia-900/30 dark:text-fuchsia-300">
                        <i class="fas fa-receipt"></i>
                    </div>
                    <div>
                        <div class="text-gray-500 dark:text-gray-400">Total</div>
                        <div class="font-semibold text-gray-900 dark:text-gray-100">C$
                            {{ number_format($purchase->total, 2) }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Details and totals -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-6">
            <!-- Details table -->
            <div
                class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-100 dark:border-gray-700">
                <div class="px-5 pt-5 pb-3 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Detalles</h3>
                </div>
                <div class="w-full overflow-x-auto" x-data="purchaseDetailsFilter()">
                    <div class="flex flex-wrap gap-4 mb-5 px-2">
                        <div class="w-40">
                            <label for="color-filter" class="block text-xs font-semibold uppercase tracking-wide text-gray-600 dark:text-gray-300 mb-1">Color</label>
                            <select x-model="color" id="color-filter"
                                class="block w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-sm text-gray-800 dark:text-gray-100 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                                <option value="">Todos los colores</option>
                                @foreach ($colors as $id => $name)
                                    <option value="{{ $name }}">{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="w-40">
                            <label for="size-filter" class="block text-xs font-semibold uppercase tracking-wide text-gray-600 dark:text-gray-300 mb-1">Talla</label>
                            <select x-model="size" id="size-filter"
                                class="block w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-sm text-gray-800 dark:text-gray-100 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                                <option value="">Todas las tallas</option>
                                @foreach ($sizes as $id => $name)
                                    <option value="{{ $name }}">{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <table class="w-full whitespace-nowrap">
                        <thead>
                            <tr
                                class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-y dark:border-gray-700 bg-gray-50 dark:text-gray-400 dark:bg-gray-800/60">
                                <th class="px-5 py-3">Producto</th>
                                <th class="px-5 py-3">Variante</th>
                                <th class="px-5 py-3 text-right">Cant.</th>
                                <th class="px-5 py-3 text-right">P. Unit</th>
                                <th class="px-5 py-3 text-right">Importe</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y dark:divide-gray-700 dark:bg-gray-800">
                            <template x-for="d in filteredDetails()" :key="d.id">
                                <tr class="text-gray-700 dark:text-gray-300">
                                    <td class="px-5 py-3 text-sm" x-text="d.product"></td>
                                    <td class="px-5 py-3 text-sm" x-text="d.variant"></td>
                                    <td class="px-5 py-3 text-sm text-right" x-text="d.quantity"></td>
                                    <td class="px-5 py-3 text-sm text-right">C$ <span
                                            x-text="parseFloat(d.unit_price).toFixed(2)"></span></td>
                                    <td class="px-5 py-3 text-sm text-right">C$ <span
                                            x-text="parseFloat(d.amount).toFixed(2)"></span></td>
                                </tr>
                            </template>
                            <template x-if="filteredDetails().length === 0">
                                <tr>
                                    <td colspan="5" class="px-5 py-6 text-center text-gray-400 dark:text-gray-500">Sin
                                        detalles</td>
                                </tr>
                            </template>
                        </tbody>
                        <tfoot>
                            <tr class="border-t dark:border-gray-700">
                                <td colspan="4"
                                    class="px-5 py-3 text-right text-sm font-medium text-gray-600 dark:text-gray-400">
                                    Subtotal</td>
                                <td class="px-5 py-3 text-right text-sm font-semibold text-gray-900 dark:text-gray-100">C$
                                    {{ number_format($purchase->subtotal, 2) }}</td>
                            </tr>
                            <tr>
                                <td colspan="4"
                                    class="px-5 py-3 text-right text-sm font-medium text-gray-600 dark:text-gray-400">Total
                                </td>
                                <td class="px-5 py-3 text-right text-sm font-semibold text-gray-900 dark:text-gray-100">C$
                                    {{ number_format($purchase->total, 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                @php
                    $jsDetails = $details
                        ->map(function ($d) {
                            $color = $d->productVariant->color->name ?? null;
                            $size = $d->productVariant->size->name ?? null;
                            $variant =
                                $color || $size
                                    ? trim(($color ?: '') . ($color && $size ? ' / ' : '') . ($size ?: ''))
                                    : 'Simple';
                            $amount = (float) $d->quantity * (float) $d->unit_price;
                            return [
                                'id' => $d->id,
                                'product' => $d->productVariant->product->name,
                                'variant' => $variant,
                                'color' => $color,
                                'size' => $size,
                                'quantity' => (int) $d->quantity,
                                'unit_price' => (float) $d->unit_price,
                                'amount' => (float) $amount,
                            ];
                        })
                        ->values();
                @endphp
                <script>
                    function purchaseDetailsFilter() {
                        return {
                            color: '',
                            size: '',
                            details: @json($jsDetails),
                            filteredDetails() {
                                return this.details.filter(d => {
                                    return (this.color === '' || d.color === this.color) &&
                                        (this.size === '' || d.size === this.size);
                                });
                            }
                        }
                    }
                </script>
            </div>

            <!-- Totals card -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-100 dark:border-gray-700 p-5 h-max">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">Resumen</h3>
                <div class="space-y-3 text-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Subtotal</span>
                        <span class="font-medium text-gray-900 dark:text-gray-100">C$
                            {{ number_format($purchase->subtotal, 2) }}</span>
                    </div>
                    <div class="flex items-center justify-between border-t pt-3 dark:border-gray-700">
                        <span class="text-gray-700 dark:text-gray-300">Total</span>
                        <span class="text-lg font-semibold text-gray-900 dark:text-gray-100">C$
                            {{ number_format($purchase->total, 2) }}</span>
                    </div>
                </div>
                <div class="mt-5 flex flex-col gap-2">
                    <a href="{{ route('purchases.edit', $purchase) }}"
                        class="px-4 py-2 rounded-lg text-sm font-medium bg-purple-600 hover:bg-purple-700 text-white text-center">
                        <i class="fas fa-edit mr-2"></i>Editar compra
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
