<div class="w-full overflow-hidden rounded-lg bg-white dark:bg-gray-800 shadow-md">
    <div class="p-3">
        <!-- Header -->
        <div class="flex items-start gap-2">
            <div class="flex-shrink-0">
                <div
                    class="w-[100px] h-[80px] rounded border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 flex items-center justify-center overflow-hidden p-0">
                    @if ($inventory->productVariant && $inventory->productVariant->product && $inventory->productVariant->product->image_url)
                        <img src="{{ $inventory->productVariant->product->image_url }}" alt="Imagen del producto"
                            style="width:260px!important;height:180px!important;object-fit:contain;display:block;margin:auto;" />
                    @else
                        <i class="fas fa-image text-gray-400 text-xl mx-auto my-auto"></i>
                    @endif
                </div>
            </div>
            <div class="min-w-0 flex-1">
                <div class="flex flex-wrap items-center gap-1">
                    <h3 class="text-base font-semibold text-gray-800 dark:text-gray-100">
                        {{ $inventory->productVariant->product->name ?? 'Producto' }}
                        <span class="block text-xs text-gray-500">{{ $inventory->productVariant->name ?? 'Variante' }}</span>
                    </h3>
                    <span
                        class="px-2 py-0.5 text-[10px] rounded-full bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300">ID
                        #{{ $inventory->id }}</span>
                    @php($isLow = $inventory->stock <= $inventory->min_stock)
                    <span
                        class="px-2 py-0.5 text-[10px] rounded-full {{ $isLow ? 'bg-red-100 text-red-700 dark:bg-red-700 dark:text-red-100' : 'bg-green-100 text-green-700 dark:bg-green-700 dark:text-green-100' }}">
                        {{ $isLow ? 'Bajo stock' : 'En stock' }}
                    </span>
                </div>
                <div class="mt-0.5 text-xs text-gray-500 dark:text-gray-400 flex items-center gap-2">
                    <span class="flex items-center"><i
                            class="fas fa-warehouse text-purple-600 dark:text-purple-400 mr-1"></i>{{ $inventory->warehouse->name ?? 'Almacén' }}</span>
                    @if (optional($inventory->productVariant->product)->barcode)
                        <span class="flex items-center"><i
                                class="fas fa-barcode text-purple-600 dark:text-purple-400 mr-1"></i>{{ $inventory->productVariant->product->barcode }}</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Stats -->
        <div class="mt-3 grid grid-cols-2 md:grid-cols-3 gap-2">
            <div class="p-2 rounded border dark:border-gray-700">
                <p class="text-[10px] uppercase tracking-wide text-gray-500 dark:text-gray-400">Stock</p>
                <p class="text-sm font-semibold text-gray-800 dark:text-gray-100 flex items-center"><i
                        class="fas fa-cubes text-purple-600 dark:text-purple-400 mr-2"></i>{{ $inventory->stock }}</p>
            </div>
            <div class="p-2 rounded border dark:border-gray-700">
                <p class="text-[10px] uppercase tracking-wide text-gray-500 dark:text-gray-400">Mínimo</p>
                <p class="text-sm font-semibold text-gray-800 dark:text-gray-100 flex items-center"><i
                        class="fas fa-exclamation-triangle text-purple-600 dark:text-purple-400 mr-2"></i>{{ $inventory->min_stock }}
                </p>
            </div>
            <div class="p-2 rounded border dark:border-gray-700">
                <p class="text-[10px] uppercase tracking-wide text-gray-500 dark:text-gray-400">Compra</p>
                <p class="text-sm font-semibold text-gray-800 dark:text-gray-100 flex items-center"><i
                        class="fas fa-money-bill-wave text-purple-600 dark:text-purple-400 mr-2"></i>C$
                    {{ number_format($inventory->purchase_price, 2) }}</p>
            </div>
            <div class="p-2 rounded border dark:border-gray-700">
                <p class="text-[10px] uppercase tracking-wide text-gray-500 dark:text-gray-400">Venta</p>
                <p class="text-sm font-semibold text-gray-800 dark:text-gray-100 flex items-center"><i
                        class="fas fa-dollar-sign text-purple-600 dark:text-purple-400 mr-2"></i>C$
                    {{ number_format($inventory->sale_price, 2) }}</p>
            </div>
            <div class="p-2 rounded border dark:border-gray-700">
                <p class="text-[10px] uppercase tracking-wide text-gray-500 dark:text-gray-400">Valor en bodega</p>
                <p class="text-sm font-semibold text-gray-800 dark:text-gray-100">C$
                    {{ number_format($inventory->stock * $inventory->purchase_price, 2) }}</p>
            </div>
            <div class="p-2 rounded border dark:border-gray-700">
                <p class="text-[10px] uppercase tracking-wide text-gray-500 dark:text-gray-400">Ingreso potencial</p>
                <p class="text-sm font-semibold text-gray-800 dark:text-gray-100">C$
                    {{ number_format($inventory->stock * $inventory->sale_price, 2) }}</p>
            </div>
        </div>

        <!-- Product & meta info -->
        <div class="mt-3 grid grid-cols-1 md:grid-cols-2 gap-2">
            <div class="p-2 rounded bg-gray-50 dark:bg-gray-900">
                <p class="text-[10px] uppercase tracking-wide text-gray-500 dark:text-gray-400 mb-1">Producto</p>
                <div class="space-y-0.5 text-xs text-gray-700 dark:text-gray-300">
                    <p class="flex items-center"><span class="w-20 text-gray-500 dark:text-gray-400">Marca:</span> <span
                            class="font-medium">{{ optional(optional($inventory->productVariant->product)->brand)->name ?? '-' }}</span>
                    </p>
                    <p class="flex items-center"><span class="w-20 text-gray-500 dark:text-gray-400">Categoría:</span>
                        <span
                            class="font-medium">{{ optional(optional(optional($inventory->productVariant->product)->brand)->category)->name ?? '-' }}</span>
                    </p>
                    <p class="flex items-center"><span class="w-20 text-gray-500 dark:text-gray-400">Impuesto:</span>
                        <span
                            class="font-medium">{{ optional(optional($inventory->productVariant->product)->tax)->name ?? '-' }}</span>
                    </p>
                    <p class="flex items-center"><span class="w-20 text-gray-500 dark:text-gray-400">Unidad:</span>
                        <span
                            class="font-medium">{{ optional(optional($inventory->productVariant->product)->unitMeasure)->name ?? '-' }}</span>
                    </p>
                </div>
            </div>
            <div class="p-2 rounded bg-gray-50 dark:bg-gray-900">
                <p class="text-[10px] uppercase tracking-wide text-gray-500 dark:text-gray-400 mb-1">Trazabilidad</p>
                <div class="grid grid-cols-1 gap-1 text-[11px] text-gray-600 dark:text-gray-400">
                    <p class="flex items-center"><i
                            class="fas fa-calendar-alt text-purple-600 dark:text-purple-400 mr-2"></i><span
                            class="font-medium mr-1">Creado:</span>
                        {{ $inventory->formatted_created_at ?? $inventory->created_at }}</p>
                    <p class="flex items-center"><i
                            class="fas fa-clock text-purple-600 dark:text-purple-400 mr-2"></i><span
                            class="font-medium mr-1">Actualizado:</span>
                        {{ $inventory->formatted_updated_at ?? $inventory->updated_at }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
