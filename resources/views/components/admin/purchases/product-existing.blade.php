@props([
    'entities' => [],
    'warehouses' => [],
    'methods' => [],
    'categories' => [],
    'brands' => [],
    'purchase' => null,
])

<fieldset x-ref="existingFields" x-bind:disabled="$el.dataset.mode === 'new'"
    {{ $attributes->merge(['class' => 'mt-4']) }}>
    <div x-data="{
        // Filtros de búsqueda
        filters: {
            entity_id: @js(old('existing.entity_id', old('entity_id', $purchase->entity_id ?? ''))),
            warehouse_id: @js(old('existing.warehouse_id', old('warehouse_id', $purchase->warehouse_id ?? ''))),
            category_id: @js(old('filter.category_id')),
            brand_id: @js(old('filter.brand_id')),
            q: @js(old('filter.q')),
        },
        // Datos de compra (compatibilidad backend)
        purchase: {
            payment_method_id: @js(old('existing.payment_method_id', old('payment_method_id', $purchase->payment_method_id ?? ''))),
            reference: @js(old('existing.reference', old('reference', $purchase->reference ?? ''))),
        },
        // Estado UI
        filtersOpen: false,
        selectedProductId: @js(old('product.id')) || null,
        results: [],
        loading: false,
        // Paginación simple
        page: 1,
        lastPage: 1,
        total: 0,
        perPage: 10,
        // Métodos
        async searchProducts(page = null) {
            if (page) this.page = page;
            this.loading = true;
            try {
                const url = new URL(@js(route('purchases.productSearch')));
                Object.entries(this.filters).forEach(([k, v]) => {
                    if (v !== undefined && v !== null && String(v).trim() !== '') url.searchParams.set(k, v);
                });
                if (this.page) url.searchParams.set('page', this.page);
                if (this.perPage) url.searchParams.set('per_page', this.perPage);
                const res = await fetch(url.toString(), { headers: { 'Accept': 'application/json' } });
                const data = await res.json();
                // Soporta respuesta como array plano o como paginada { data, meta }
                const items = Array.isArray(data) ? data : (data.data || []);
                // Normaliza al shape esperado por la tabla (prioriza campos estructurados del API)
                const normalize = (raw) => ({
                    id: raw?.id ?? raw?.product_id ?? raw?.value ?? null,
                    name: raw?.name ?? raw?.product_name ?? raw?.title ?? raw?.text ?? '-',
                    category: raw?.category_name ??
                        (raw?.category && (raw.category.name || raw.category.title)) ??
                        (raw?.product && raw.product.category && (raw.product.category.name || raw.product.category.title)) ??
                        raw?.category ??
                        '-',
                    brand: raw?.brand_name ??
                        (raw?.brand && (raw.brand.name || raw.brand.title)) ??
                        (raw?.product && raw.product.brand && (raw.product.brand.name || raw.product.brand.title)) ??
                        raw?.brand ??
                        '-',
                    code: raw?.code ?? (raw?.product && raw.product.code) ?? null,
                    sku: raw?.sku ?? (raw?.product && raw.product.sku) ?? null,
                    barcode: raw?.barcode ?? (raw?.product && raw.product.barcode) ?? null,
                    stock: raw?.stock ?? raw?.current_stock ?? raw?.quantity ?? (raw?.inventory && raw.inventory.stock) ?? raw?.total_stock ?? 0,
                    entity_id: raw?.entity_id ?? raw?.supplier_id ?? raw?.provider_id ?? (raw?.product && raw.product.entity_id) ?? null,
                    entity_name: raw?.entity_name ?? raw?.entity_short_name ?? raw?.entity_first_name ?? null,
                    warehouse_id: raw?.warehouse_id ??
                        raw?.inventory_warehouse_id ??
                        (raw?.inventory && raw.inventory.warehouse_id) ??
                        raw?.default_warehouse_id ??
                        (Array.isArray(raw?.inventories) && raw.inventories.length ? raw.inventories[0].warehouse_id : null) ??
                        null,
                    raw,
                });
                this.results = items.map(normalize);
                const meta = data.meta || {};
                this.page = meta.current_page || this.page || 1;
                this.lastPage = meta.last_page || 1;
                this.total = meta.total || (Array.isArray(data) ? this.results.length : items.length);
                this.perPage = meta.per_page || this.perPage;
            } catch (e) {
                console.error(e);
                this.results = [];
                this.page = 1;
                this.lastPage = 1;
                this.total = 0;
            } finally {
                this.loading = false;
            }
        },
        selectProduct(row) {
            this.selectedProductId = row?.id ?? null;
            // Emite evento a nivel window (usar @product-selected.window en el padre)
            window.dispatchEvent(new CustomEvent('product-selected', { detail: { id: this.selectedProductId, product: row?.raw ?? row } }));
            // Autorrellenar proveedor/almacén si vienen del producto y si no hay valor seleccionado todavía
            const inferredEntity = row?.entity_id ?? row?.raw?.entity_id ?? row?.raw?.supplier_id ?? row?.raw?.provider_id;
            if (!this.filters.entity_id && inferredEntity) {
                this.filters.entity_id = String(inferredEntity);
            }
            const inferredWarehouse = row?.warehouse_id ??
                row?.raw?.warehouse_id ??
                row?.raw?.inventory_warehouse_id ??
                (row?.raw?.inventory && row.raw.inventory.warehouse_id) ??
                row?.raw?.default_warehouse_id ??
                (Array.isArray(row?.raw?.inventories) && row.raw.inventories.length ? row.raw.inventories[0].warehouse_id : null);
            if (!this.filters.warehouse_id && inferredWarehouse) {
                this.filters.warehouse_id = String(inferredWarehouse);
            }
        },
        nextPage() { if (this.page < this.lastPage) this.searchProducts(this.page + 1); },
        prevPage() { if (this.page > 1) this.searchProducts(this.page - 1); },
    }" x-init="/* Auto-búsqueda si hay filtros esenciales */
    (filters.entity_id && filters.warehouse_id) && searchProducts()">
        <!-- Datos de la compra (usados para guardar y también filtrar) -->
        <div class="rounded-lg border border-gray-200 dark:border-gray-700 p-4">
            <!-- Buscador + botón filtros -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                <label class="block text-sm w-full md:col-span-2">
                    <span class="text-gray-700 dark:text-gray-200">Buscar producto</span>
                    <input type="text" x-model="filters.q" @keydown.enter.prevent="searchProducts(1)"
                        class="block w-full mt-1 px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700"
                        placeholder="Nombre, código, SKU o barras">
                </label>
                <div class="flex items-end gap-2">
                    <button type="button" @click="searchProducts(1)"
                        class="inline-flex items-center gap-2 px-3 py-2 text-sm rounded-lg bg-purple-600 hover:bg-purple-700 text-white shadow focus:outline-none focus:ring-2 focus:ring-purple-500 min-h-[40px] font-semibold w-full md:w-auto">
                        <i class="fas fa-search fa-sm mr-1"></i> Buscar
                    </button>
                    <div class="relative" x-on:keydown.escape.window="filtersOpen=false">
                        <button type="button" @click="filtersOpen = !filtersOpen"
                            class="inline-flex items-center gap-2 px-3 py-2 text-sm rounded-lg bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-800 dark:text-gray-200 border dark:border-gray-700 min-h-[40px] w-full md:w-auto">
                            <i class="fas fa-filter fa-sm"></i>
                            Filtros
                            <i class="fas fa-chevron-down fa-xs ml-1" :class="{ 'rotate-180': filtersOpen }"></i>
                        </button>
                        <!-- Dropdown filtros avanzados -->
                        <div x-show="filtersOpen" x-cloak @click.away="filtersOpen = false"
                            class="absolute right-0 z-30 mt-2 w-[90vw] max-w-3xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-lg p-4">
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                <label class="block text-sm w-full md:col-span-2">
                                    <span class="text-gray-700 dark:text-gray-200">Proveedor</span>
                                    <select name="existing[entity_id]" x-model="filters.entity_id"
                                        @change="searchProducts(1)"
                                        class="block w-full mt-1 px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 {{ $errors->has('existing.entity_id') || $errors->has('entity_id') ? 'border-red-600' : '' }}">
                                        <option value="">Seleccionar Proveedor</option>
                                        @foreach ($entities ?? [] as $id => $name)
                                            <option value="{{ $id }}"
                                                {{ (string) old('existing.entity_id', old('entity_id', $purchase->entity_id ?? '')) === (string) $id ? 'selected' : '' }}>
                                                {{ $name }}</option>
                                        @endforeach
                                    </select>
                                    @php($entityError = $errors->first('existing.entity_id') ?: $errors->first('entity_id'))
                                    @if ($entityError)
                                        <span class="text-xs text-red-600 dark:text-red-400">{{ $entityError }}</span>
                                    @endif
                                </label>
                                <label class="block text-sm w-full md:col-span-2">
                                    <span class="text-gray-700 dark:text-gray-200">Almacén</span>
                                    <select name="existing[warehouse_id]" x-model="filters.warehouse_id"
                                        @change="searchProducts(1)"
                                        class="block w-full mt-1 px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 {{ $errors->has('existing.warehouse_id') || $errors->has('warehouse_id') ? 'border-red-600' : '' }}">
                                        <option value="">Seleccionar Almacén</option>
                                        @foreach ($warehouses ?? [] as $id => $name)
                                            <option value="{{ $id }}"
                                                {{ (string) old('existing.warehouse_id', old('warehouse_id', $purchase->warehouse_id ?? '')) === (string) $id ? 'selected' : '' }}>
                                                {{ $name }}</option>
                                        @endforeach
                                    </select>
                                    @php($warehouseError = $errors->first('existing.warehouse_id') ?: $errors->first('warehouse_id'))
                                    @if ($warehouseError)
                                        <span
                                            class="text-xs text-red-600 dark:text-red-400">{{ $warehouseError }}</span>
                                    @endif
                                </label>
                                <label class="block text-sm w-full">
                                    <span class="text-gray-700 dark:text-gray-200">Categoría</span>
                                    <select x-model="filters.category_id" @change="searchProducts(1)"
                                        class="block w-full mt-1 px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700">
                                        <option value="">Todas</option>
                                        @foreach ($categories ?? [] as $id => $name)
                                            <option value="{{ $id }}">{{ $name }}</option>
                                        @endforeach
                                    </select>
                                </label>
                                <label class="block text-sm w-full">
                                    <span class="text-gray-700 dark:text-gray-200">Marca</span>
                                    <select x-model="filters.brand_id" @change="searchProducts(1)"
                                        class="block w-full mt-1 px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700">
                                        <option value="">Todas</option>
                                        @foreach ($brands ?? [] as $id => $name)
                                            <option value="{{ $id }}">{{ $name }}</option>
                                        @endforeach
                                    </select>
                                </label>
                            </div>
                            <div class="mt-4 flex justify-end gap-2">
                                <button type="button"
                                    @click="filters = { ...filters, category_id: '', brand_id: '' }; searchProducts(1)"
                                    class="px-3 py-2 text-sm rounded-lg border bg-white dark:bg-gray-800 dark:border-gray-700">Limpiar</button>
                                <button type="button" @click="filtersOpen=false; searchProducts(1)"
                                    class="px-3 py-2 text-sm rounded-lg bg-purple-600 hover:bg-purple-700 text-white">Aplicar</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Datos de la compra: Proveedor, Almacén, Método de pago, Referencia -->
            <div class="mt-4 grid grid-cols-1 md:grid-cols-4 gap-4">
                <label class="block text-sm w-full">
                    <span class="text-gray-700 dark:text-gray-200">Proveedor</span>
                    <select name="existing[entity_id]" x-model="filters.entity_id"
                        class="block w-full mt-1 px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 {{ $errors->has('existing.entity_id') || $errors->has('entity_id') ? 'border-red-600' : '' }}">
                        <option value="">Seleccionar Proveedor</option>
                        @foreach ($entities ?? [] as $id => $name)
                            <option value="{{ $id }}"
                                {{ (string) old('existing.entity_id', old('entity_id', $purchase->entity_id ?? '')) === (string) $id ? 'selected' : '' }}>
                                {{ $name }}</option>
                        @endforeach
                    </select>
                    @php($entityError = $errors->first('existing.entity_id') ?: $errors->first('entity_id'))
                    @if ($entityError)
                        <span class="text-xs text-red-600 dark:text-red-400">{{ $entityError }}</span>
                    @endif
                </label>

                <label class="block text-sm w-full">
                    <span class="text-gray-700 dark:text-gray-200">Almacén</span>
                    <select name="existing[warehouse_id]" x-model="filters.warehouse_id"
                        class="block w-full mt-1 px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 {{ $errors->has('existing.warehouse_id') || $errors->has('warehouse_id') ? 'border-red-600' : '' }}">
                        <option value="">Seleccionar Almacén</option>
                        @foreach ($warehouses ?? [] as $id => $name)
                            <option value="{{ $id }}"
                                {{ (string) old('existing.warehouse_id', old('warehouse_id', $purchase->warehouse_id ?? '')) === (string) $id ? 'selected' : '' }}>
                                {{ $name }}</option>
                        @endforeach
                    </select>
                    @php($warehouseError = $errors->first('existing.warehouse_id') ?: $errors->first('warehouse_id'))
                    @if ($warehouseError)
                        <span class="text-xs text-red-600 dark:text-red-400">{{ $warehouseError }}</span>
                    @endif
                </label>

                <label class="block text-sm w-full">
                    <span class="text-gray-700 dark:text-gray-200">Método de pago</span>
                    <select name="existing[payment_method_id]" x-model="purchase.payment_method_id"
                        x-bind:required="$el.closest('fieldset')?.dataset?.mode === 'existing'"
                        class="block w-full mt-1 px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 {{ $errors->has('existing.payment_method_id') || $errors->has('payment_method_id') ? 'border-red-600' : '' }}">
                        <option value="">Seleccionar Método de Pago</option>
                        @foreach ($methods ?? [] as $id => $name)
                            <option value="{{ $id }}"
                                {{ (string) old('existing.payment_method_id', old('payment_method_id', $purchase->payment_method_id ?? '')) === (string) $id ? 'selected' : '' }}>
                                {{ $name }}</option>
                        @endforeach
                    </select>
                    @php($methodError = $errors->first('existing.payment_method_id') ?: $errors->first('payment_method_id'))
                    @if ($methodError)
                        <span class="text-xs text-red-600 dark:text-red-400">{{ $methodError }}</span>
                    @endif
                </label>

                <label class="block text-sm w-full">
                    <span class="text-gray-700 dark:text-gray-200">Referencia</span>
                    <input type="text" name="existing[reference]" x-model="purchase.reference"
                        class="block w-full mt-1 px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 {{ $errors->has('existing.reference') || $errors->has('reference') ? 'border-red-600' : '' }}"
                        placeholder="Opcional..." />
                    @php($referenceError = $errors->first('existing.reference') ?: $errors->first('reference'))
                    @if ($referenceError)
                        <span class="text-xs text-red-600 dark:text-red-400">{{ $referenceError }}</span>
                    @endif
                </label>
            </div>

            <!-- Tabla de resultados -->
            <div class="mt-4">
                <div class="overflow-x-auto border border-gray-200 dark:border-gray-700 rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-800/50">
                            <tr class="text-left text-gray-600 dark:text-gray-300">
                                <th class="px-4 py-2 font-semibold">ID</th>
                                <th class="px-4 py-2 font-semibold">Nombre</th>
                                <th class="px-4 py-2 font-semibold">Proveedor</th>
                                <th class="px-4 py-2 font-semibold">Categoría</th>
                                <th class="px-4 py-2 font-semibold">Marca</th>
                                <th class="px-4 py-2 font-semibold">Stock actual</th>
                                <th class="px-4 py-2"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700" x-show="!loading">
                            <template x-if="results.length === 0">
                                <tr>
                                    <td colspan="6" class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">
                                        Sin resultados. Ajusta tu búsqueda o filtros.</td>
                                </tr>
                            </template>
                            <template x-for="p in results" :key="p.id">
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/60">
                                    <td class="px-4 py-2 text-gray-900 dark:text-gray-100" x-text="p.id"></td>
                                    <td class="px-4 py-2">
                                        <div class="font-medium text-gray-900 dark:text-gray-100" x-text="p.name">
                                        </div>
                                    </td>
                                    <td class="px-4 py-2 text-gray-900 dark:text-gray-100" x-text="p.entity_name">
                                    </td>
                                    <td class="px-4 py-2 text-gray-900 dark:text-gray-100" x-text="p.category"></td>
                                    <td class="px-4 py-2 text-gray-900 dark:text-gray-100" x-text="p.brand"></td>
                                    <td class="px-4 py-2 text-gray-900 dark:text-gray-100" x-text="p.stock"></td>
                                    <td class="px-4 py-2">
                                        <button type="button" @click="selectProduct(p)"
                                            :class="{
                                                'bg-green-600 hover:bg-green-700 text-white': selectedProductId === p
                                                    .id,
                                                'bg-blue-600 hover:bg-blue-700 text-white': selectedProductId !== p
                                                    .id
                                            }"
                                            class="px-3 py-1.5 rounded-md text-xs font-semibold">
                                            <span x-show="selectedProductId===p.id">Seleccionado</span>
                                            <span x-show="selectedProductId!==p.id">Seleccionar</span>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                        <tbody x-show="loading">
                            <tr>
                                <td colspan="6" class="px-4 py-6 text-center">
                                    <span class="inline-flex items-center gap-2 text-gray-600 dark:text-gray-300">
                                        <svg class="animate-spin h-5 w-5 text-purple-600"
                                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10"
                                                stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor"
                                                d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                                        </svg>
                                        Cargando...
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Paginación simple -->
                <div class="mt-3 flex items-center justify-between text-sm">
                    <div class="text-gray-600 dark:text-gray-300">
                        <span x-text="`Página ${page} de ${lastPage}`"></span>
                        <template x-if="total">
                            <span class="ml-2 text-xs text-gray-500">(<span x-text="total"></span> resultados)</span>
                        </template>
                    </div>
                    <div class="space-x-2">
                        <button type="button" @click="prevPage()" :disabled="page <= 1"
                            class="px-3 py-1.5 rounded-md border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 disabled:opacity-50 disabled:cursor-not-allowed">Anterior</button>
                        <button type="button" @click="nextPage()" :disabled="page >= lastPage"
                            class="px-3 py-1.5 rounded-md border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 disabled:opacity-50 disabled:cursor-not-allowed">Siguiente</button>
                    </div>
                </div>
            </div>

            <!-- Campo oculto para el producto seleccionado -->
            <input type="hidden" name="product[id]" :value="selectedProductId">
            @error('product.id')
                <div class="text-xs text-red-600 dark:text-red-400 mt-1">{{ $message }}</div>
            @enderror

            <!-- Campos ocultos canónicos para mantener compatibilidad con el backend cuando este modo está activo -->
            <template x-if="$el.closest('fieldset')?.dataset?.mode === 'existing'">
                <div>
                    <input type="hidden" name="entity_id" :value="filters.entity_id">
                    <input type="hidden" name="warehouse_id" :value="filters.warehouse_id">
                    <input type="hidden" name="payment_method_id" :value="purchase.payment_method_id">
                    <input type="hidden" name="reference" :value="purchase.reference">
                </div>
            </template>
        </div>
</fieldset>
