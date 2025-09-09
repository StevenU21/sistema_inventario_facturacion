<div class="w-full overflow-hidden rounded-lg bg-white dark:bg-gray-800 shadow-md">
    <div class="p-3">
        <!-- Header -->
        <div class="flex items-start gap-2">
            <div class="flex-shrink-0">
                <div class="w-[100px] h-[80px] rounded border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 flex items-center justify-center overflow-hidden p-0">
                    @if ($product->image_url)
                        <img src="{{ $product->image_url }}" alt="Imagen del producto"
                            style="width:90px!important;height:70px!important;object-fit:contain;display:block;margin:auto;" />
                    @else
                        <i class="fas fa-image text-gray-400 text-xl mx-auto my-auto"></i>
                    @endif
                </div>
            </div>
            <div class="min-w-0 flex-1">
                <div class="flex flex-wrap items-center gap-1">
                    <h3 class="text-base font-semibold text-gray-800 dark:text-gray-100">
                        {{ $product->name ?? 'Producto' }}</h3>
                    <span
                        class="px-2 py-0.5 text-[10px] rounded-full bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300">ID
                        #{{ $product->id }}</span>
                </div>
                <div class="mt-0.5 text-xs text-gray-500 dark:text-gray-400 flex items-center gap-2">
                    @if (!empty($product->barcode))
                        <span class="flex items-center"><i
                                class="fas fa-barcode text-purple-600 dark:text-purple-400 mr-1"></i>{{ $product->barcode }}</span>
                    @endif
                    @php($providerName = optional($product->entity)->name ?: optional($product->entity)->short_name)
                    @if (!empty($providerName))
                        <span class="flex items-center"><i
                                class="fas fa-user-tie text-purple-600 dark:text-purple-400 mr-1"></i>{{ $providerName }}</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Product & meta info -->
        <div class="mt-3 grid grid-cols-2 md:grid-cols-3 gap-2">
            <div class="p-2 rounded border dark:border-gray-700">
                <p class="text-[10px] uppercase tracking-wide text-gray-500 dark:text-gray-400">Categoría</p>
                <p class="text-sm font-semibold text-gray-800 dark:text-gray-100 flex items-center"><i
                        class="fas fa-cubes text-purple-600 dark:text-purple-400 mr-2"></i>{{ optional($product->category)->name ?? '-' }}
                </p>
            </div>
            <div class="p-2 rounded border dark:border-gray-700">
                <p class="text-[10px] uppercase tracking-wide text-gray-500 dark:text-gray-400">Marca</p>
                <p class="text-sm font-semibold text-gray-800 dark:text-gray-100 flex items-center"><i
                        class="fas fa-exclamation-triangle text-purple-600 dark:text-purple-400 mr-2"></i>{{ optional($product->brand)->name ?? '-' }}
                </p>
            </div>
            <div class="p-2 rounded border dark:border-gray-700">
                <p class="text-[10px] uppercase tracking-wide text-gray-500 dark:text-gray-400">Impuesto</p>
                <p class="text-sm font-semibold text-gray-800 dark:text-gray-100 flex items-center"><i
                        class="fas fa-money-bill-wave text-purple-600 dark:text-purple-400 mr-2"></i>
                    {{ $product->tax->name }}</p>
            </div>

            <div class="p-2 rounded border dark:border-gray-700">
                <p class="text-[10px] uppercase tracking-wide text-gray-500 dark:text-gray-400">Proveedor</p>
                <p class="text-sm font-semibold text-gray-800 dark:text-gray-100 flex items-center"><i
                        class="fas fa-money-bill-wave text-purple-600 dark:text-purple-400 mr-2"></i>
                    {{ $product->entity->full_name }}</p>
            </div>

            <div class="p-2 rounded border dark:border-gray-700">
                <p class="text-[10px] uppercase tracking-wide text-gray-500 dark:text-gray-400">Unidad de Medida</p>
                <p class="text-sm font-semibold text-gray-800 dark:text-gray-100 flex items-center"><i
                        class="fas fa-money-bill-wave text-purple-600 dark:text-purple-400 mr-2"></i>
                    {{ $product->unitMeasure->name }}</p>
            </div>

            <div class="p-2 rounded border dark:border-gray-700">
                <p class="text-[10px] uppercase tracking-wide text-gray-500 dark:text-gray-400">Estado</p>
                <p class="text-sm font-semibold text-gray-800 dark:text-gray-100 flex items-center"><i
                        class="fas fa-money-bill-wave text-purple-600 dark:text-purple-400 mr-2"></i>
                    {{ $product->status == 'available' ? 'Disponible' : ($product->status == 'unavailable' ? 'No Disponible' : ($product->status == 'out_of_stock' ? 'Agotado' : $product->status)) }}</p>
            </div>
        </div>
    </div>
</div>
