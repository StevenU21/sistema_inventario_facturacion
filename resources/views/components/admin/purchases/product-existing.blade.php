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
        filters: {
            entity_id: @js(old('existing.entity_id', old('entity_id', $purchase->entity_id ?? ''))),
            warehouse_id: @js(old('existing.warehouse_id', old('warehouse_id', $purchase->warehouse_id ?? ''))),
            category_id: @js(old('filter.category_id')),
            brand_id: @js(old('filter.brand_id')),
            q: @js(old('filter.q')),
        },
        purchase: {
            payment_method_id: @js(old('existing.payment_method_id', old('payment_method_id', $purchase->payment_method_id ?? ''))),
            reference: @js(old('existing.reference', old('reference', $purchase->reference ?? ''))),
        },
        results: [],
        loading: false,
        async searchProducts() {
            this.loading = true;
            try {
                const url = new URL(@js(route('purchases.productSearch')));
                Object.entries(this.filters).forEach(([k, v]) => {
                    if (v !== undefined && v !== null && String(v).trim() !== '') url.searchParams.set(k, v);
                });
                const res = await fetch(url.toString(), { headers: { 'Accept': 'application/json' } });
                const data = await res.json();
                this.results = Array.isArray(data) ? data : (data.data || []);
            } catch (e) {
                console.error(e);
                this.results = [];
            } finally {
                this.loading = false;
            }
        }
    }">
        <!-- Datos de la compra (usados para guardar y también filtrar) -->
        <div class="rounded-lg border border-gray-200 dark:border-gray-700 p-4">
            <!-- Fila 1: Nombre (buscar) -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <label class="block text-sm w-full md:col-span-2">
                    <span class="text-gray-700 dark:text-gray-200">Nombre (buscar)</span>
                    <input type="text" x-model="filters.q"
                        class="block w-full mt-1 px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700"
                        placeholder="Nombre, código, SKU o barras">
                </label>
                <div class="flex items-end">
                    <button type="button" @click="searchProducts()"
                        class="inline-flex items-center gap-2 px-3 py-2 text-sm rounded-lg bg-purple-600 hover:bg-purple-700 text-white shadow focus:outline-none focus:ring-2 focus:ring-purple-500 min-h-[40px] font-semibold w-full md:w-auto">
                        <i class="fas fa-search fa-sm mr-1"></i> Buscar
                    </button>
                </div>
            </div>

            <!-- Fila 2: Proveedor - Almacén - Categoría - Marca -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mt-6">
                <label class="block text-sm w-full">
                    <span class="text-gray-700 dark:text-gray-200">Proveedor</span>
                    <select name="existing[entity_id]" x-model="filters.entity_id" required
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
                    <select name="existing[warehouse_id]" x-model="filters.warehouse_id" required
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
                    <span class="text-gray-700 dark:text-gray-200">Categoría</span>
                    <select x-model="filters.category_id"
                        class="block w-full mt-1 px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700">
                        <option value="">Todas</option>
                        @foreach ($categories ?? [] as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="block text-sm w-full">
                    <span class="text-gray-700 dark:text-gray-200">Marca</span>
                    <select x-model="filters.brand_id"
                        class="block w-full mt-1 px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700">
                        <option value="">Todas</option>
                        @foreach ($brands ?? [] as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </label>
            </div>

            <!-- Fila 3: Seleccionar Producto -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
                <label class="block text-sm w-full md:col-span-2">
                    <span class="text-gray-700 dark:text-gray-200">Seleccionar producto</span>
                    <select name="product[id]"
                        class="block w-full mt-1 px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700">
                        <option value="">-- Selecciona --</option>
                        <template x-for="p in results" :key="p.id">
                            <option :value="p.id" x-text="p.text"></option>
                        </template>
                    </select>
                    @error('product.id')
                        <div class="text-xs text-red-600 dark:text-red-400 mt-1">{{ $message }}</div>
                    @enderror
                </label>
            </div>

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
