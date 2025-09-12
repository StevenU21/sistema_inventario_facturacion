@props([
    'entities' => [],
    'warehouses' => [],
    'methods' => [],
    'categories' => [],
    'brands' => [],
    'purchase' => null,
])

<fieldset x-ref="existingFields" x-bind:disabled="mode === 'new'"
    {{ $attributes->merge(['class' => 'mt-4']) }}>
    <div x-data="{
        filters: {
            entity_id: @js(old('entity_id', $purchase->entity_id ?? '')),
            warehouse_id: @js(old('warehouse_id', $purchase->warehouse_id ?? '')),
            category_id: @js(old('filter.category_id')),
            brand_id: @js(old('filter.brand_id')),
            q: @js(old('filter.q')),
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
    <div class="rounded-lg border border-gray-200 dark:border-gray-700 p-4 mb-4">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <label class="block text-sm w-full">
                <span class="text-gray-700 dark:text-gray-200">Proveedor</span>
                <select name="entity_id" x-model="filters.entity_id" required
                    class="block w-full mt-1 px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 @error('entity_id') border-red-600 @enderror"
                    >
                    <option value="">Seleccionar Proveedor</option>
                    @foreach ($entities ?? [] as $id => $name)
                        <option value="{{ $id }}"
                            {{ (string) old('entity_id', $purchase->entity_id ?? '') === (string) $id ? 'selected' : '' }}>
                            {{ $name }}</option>
                    @endforeach
                </select>
                @error('entity_id')
                    <span class="text-xs text-red-600 dark:text-red-400">{{ $message }}</span>
                @enderror
            </label>
            <label class="block text-sm w-full">
                <span class="text-gray-700 dark:text-gray-200">Almacén</span>
                <select name="warehouse_id" x-model="filters.warehouse_id" required
                    class="block w-full mt-1 px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 @error('warehouse_id') border-red-600 @enderror"
                    >
                    <option value="">Seleccionar Almacén</option>
                    @foreach ($warehouses ?? [] as $id => $name)
                        <option value="{{ $id }}"
                            {{ (string) old('warehouse_id', $purchase->warehouse_id ?? '') === (string) $id ? 'selected' : '' }}>
                            {{ $name }}</option>
                    @endforeach
                </select>
                @error('warehouse_id')
                    <span class="text-xs text-red-600 dark:text-red-400">{{ $message }}</span>
                @enderror
            </label>
            <label class="block text-sm w-full">
                <span class="text-gray-700 dark:text-gray-200">Método de pago</span>
                <select name="payment_method_id" required
                    class="block w-full mt-1 px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 @error('payment_method_id') border-red-600 @enderror"
                    >
                    <option value="">Seleccionar Método de Pago</option>
                    @foreach ($methods ?? [] as $id => $name)
                        <option value="{{ $id }}"
                            {{ (string) old('payment_method_id', $purchase->payment_method_id ?? '') === (string) $id ? 'selected' : '' }}>
                            {{ $name }}</option>
                    @endforeach
                </select>
                @error('payment_method_id')
                    <span class="text-xs text-red-600 dark:text-red-400">{{ $message }}</span>
                @enderror
            </label>
            <label class="block text-sm w-full">
                <span class="text-gray-700 dark:text-gray-200">Referencia</span>
                <input type="text" name="reference"
                    value="{{ old('reference', $purchase->reference ?? '') }}"
                    class="block w-full mt-1 px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 @error('reference') border-red-600 @enderror"
                    placeholder="Opcional...">
                @error('reference')
                    <span class="text-xs text-red-600 dark:text-red-400">{{ $message }}</span>
                @enderror
            </label>
        </div>
    </div>

    <!-- Buscar producto -->
    <div class="text-sm text-gray-600 dark:text-gray-300 mb-2">Usa los filtros para encontrar el producto.</div>
    <div class="rounded-lg border border-gray-200 dark:border-gray-700 p-4">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
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
            <label class="block text-sm w-full md:col-span-2">
                <span class="text-gray-700 dark:text-gray-200">Buscar (nombre, código, SKU, barras)</span>
                <input type="text" x-model="filters.q"
                    class="block w-full mt-1 px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700"
                    placeholder="Texto de búsqueda">
            </label>
        </div>
        <div class="flex items-end gap-3 mt-3">
            <button type="button" @click="searchProducts()"
                class="inline-flex items-center gap-2 px-3 py-1.5 text-sm rounded-lg bg-purple-600 hover:bg-purple-700 text-white shadow focus:outline-none focus:ring-2 focus:ring-purple-500 min-h-[36px] font-semibold">
                <i class="fas fa-search fa-sm mr-1"></i> Buscar
            </button>
            <span x-show="loading" class="text-sm text-gray-600 dark:text-gray-300">Buscando...</span>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-4">
            <label class="block text-sm w-full">
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
    </div>
    </div>
</fieldset>
