<div x-data="{
    mode: @js(old('product_mode', isset($product) && $product ? 'existing' : 'new'))
}"
    class="px-4 py-3 mb-8 bg-white rounded-lg shadow-md dark:bg-gray-800 border border-gray-200 dark:border-gray-700 w-full">

    <!-- Totales y usuario ahora se calculan/inyectan en el servidor -->

    <hr class="my-6 border-gray-200 dark:border-gray-700">

    <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-200 mb-3">Producto</h3>

    <!-- Selector de modo -->
    <div class="mt-2 mb-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <label class="relative cursor-pointer">
                <input type="radio" name="product_mode" value="new" x-model="mode" class="sr-only" />
                <div class="rounded-lg border p-4 h-full transition-colors select-none"
                    :class="mode === 'new' ?
                        'border-purple-500 ring-2 ring-purple-200 dark:ring-purple-900/50 bg-purple-50 dark:bg-purple-900/10' :
                        'border-gray-200 dark:border-gray-700'">
                    <div class="flex items-start gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-md bg-purple-600 text-white">
                            <i class="fas fa-plus"></i>
                        </div>
                        <div>
                            <div class="font-semibold text-gray-800 dark:text-gray-100">Producto nuevo</div>
                            <div class="text-sm text-gray-600 dark:text-gray-300">Crea el producto y registra sus
                                variantes.</div>
                        </div>
                    </div>
                </div>
            </label>
            <label class="relative cursor-pointer">
                <input type="radio" name="product_mode" value="existing" x-model="mode" class="sr-only" />
                <div class="rounded-lg border p-4 h-full transition-colors select-none"
                    :class="mode === 'existing' ?
                        'border-purple-500 ring-2 ring-purple-200 dark:ring-purple-900/50 bg-purple-50 dark:bg-purple-900/10' :
                        'border-gray-200 dark:border-gray-700'">
                    <div class="flex items-start gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-md bg-purple-600 text-white">
                            <i class="fas fa-search"></i>
                        </div>
                        <div>
                            <div class="font-semibold text-gray-800 dark:text-gray-100">Producto existente</div>
                            <div class="text-sm text-gray-600 dark:text-gray-300">Busca un producto y agrega sus
                                variantes.</div>
                        </div>
                    </div>
                </div>
            </label>
        </div>
        @error('product_mode')
            <div class="text-xs text-red-600 dark:text-red-400 mt-1">{{ $message }}</div>
        @enderror

        <!-- Producto existente: filtros + resultados extraídos a componente -->
        <x-admin.purchases.product-existing :entities="$entities ?? []" :warehouses="$warehouses ?? []" :methods="$methods ?? []" :categories="$categories ?? []"
            :brands="$brands ?? []" :purchase="$purchase ?? null" x-show="mode==='existing'" x-cloak />

        <!-- Campos de producto nuevo extraídos a componente -->
        <x-admin.purchases.product-new :entities="$entities ?? []" :warehouses="$warehouses ?? []" :methods="$methods ?? []" :categories="$categories ?? []"
            :brands="$brands ?? []" :taxes="$taxes ?? []" :units="$units ?? []" :purchase="$purchase ?? null" :product="$product ?? null"
            x-show="mode==='new'" x-cloak />
    </div>
    <!-- Variantes/Líneas extraídas a componente -->
    <x-admin.purchases.variants-lines :colors="$colors ?? []" :sizes="$sizes ?? []" :old-details="old('details')" :prefill-details="isset($prefillDetails) ? $prefillDetails : null" />

    <div class="mt-6 flex gap-2">
        <button type="submit"
            class="flex items-center px-4 py-2 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-purple-600 border border-transparent rounded-lg hover:bg-purple-700 focus:outline-none focus:shadow-outline-purple active:bg-purple-600">
            <i class="fas fa-paper-plane mr-2"></i> {{ isset($color) ? 'Actualizar' : 'Guardar' }}
        </button>
    </div>
</div>
