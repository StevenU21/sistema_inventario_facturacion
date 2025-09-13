@props([
    'colors' => [],
    'sizes' => [],
    'warehouses' => [],
])

<fieldset x-data="{
    // filtros básicos para buscar variantes
    filters: {
        q: @js(old('filter.q')),
        color_id: @js(old('filter.color_id')),
        size_id: @js(old('filter.size_id')),
    },
    results: [],
    loading: false,
    page: 1,
    lastPage: 1,
    total: 0,
    perPage: 10,
    selectedVariantId: @js(old('product_variant_id')) || null,
    async search(page = null) {
        if (page) this.page = page;
        this.loading = true;
        try {
            const url = new URL(@js(route('inventories.variantSearch')));
            Object.entries(this.filters).forEach(([k, v]) => {
                if (v !== undefined && v !== null && String(v).trim() !== '') url.searchParams.set(k, v);
            });
            if (this.page) url.searchParams.set('page', this.page);
            if (this.perPage) url.searchParams.set('per_page', this.perPage);
            const res = await fetch(url.toString(), { headers: { 'Accept': 'application/json' } });
            const data = await res.json();
            const items = Array.isArray(data) ? data : (data.data || []);
            this.results = items;
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
    selectVariant(row) {
        this.selectedVariantId = row?.id ?? null;
        // Escribir al input oculto de variant + mostrar etiqueta
        const hidden = $refs.variantHidden;
        if (hidden) hidden.value = this.selectedVariantId ?? '';
        const badge = $refs.variantBadge;
        if (badge) badge.textContent = row?.label || `${row?.product_name || ''}`;
    },
}"
class="rounded-lg border border-gray-200 dark:border-gray-700 p-4">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
        <label class="block text-sm w-full md:col-span-2">
            <span class="text-gray-700 dark:text-gray-200">Buscar producto/variante</span>
            <input type="text" x-model="filters.q" @keydown.enter.prevent="search(1)"
                class="block w-full mt-1 px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700"
                placeholder="Nombre, SKU o barras">
        </label>
        <label class="block text-sm w-full">
            <span class="text-gray-700 dark:text-gray-200">Color</span>
            <select x-model="filters.color_id" class="block w-full mt-1 px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700">
                <option value="">Todos</option>
                @foreach ($colors as $id => $name)
                    <option value="{{ $id }}">{{ $name }}</option>
                @endforeach
            </select>
        </label>
        <label class="block text-sm w-full">
            <span class="text-gray-700 dark:text-gray-200">Talla</span>
            <select x-model="filters.size_id" class="block w-full mt-1 px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700">
                <option value="">Todas</option>
                @foreach ($sizes as $id => $name)
                    <option value="{{ $id }}">{{ $name }}</option>
                @endforeach
            </select>
        </label>
    </div>
    <div class="mt-3 flex items-center gap-2">
        <button type="button" @click="search(1)"
            class="inline-flex items-center gap-2 px-3 py-2 text-sm rounded-lg bg-purple-600 hover:bg-purple-700 text-white shadow focus:outline-none focus:ring-2 focus:ring-purple-500 min-h-[40px] font-semibold">
            <i class="fas fa-search fa-sm mr-1"></i> Buscar
        </button>
        <template x-if="selectedVariantId">
            <span class="text-xs px-2 py-1 rounded-full bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-200" x-ref="variantBadge"></span>
        </template>
    </div>

    <div class="mt-4">
        <div class="overflow-x-auto border border-gray-200 dark:border-gray-700 rounded-lg">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                <thead class="bg-gray-50 dark:bg-gray-800/50">
                    <tr class="text-left text-gray-600 dark:text-gray-300">
                        <th class="px-3 py-2">Producto</th>
                        <th class="px-3 py-2">Variante</th>
                        <th class="px-3 py-2">SKU</th>
                        <th class="px-3 py-2">Barras</th>
                        <th class="px-3 py-2">Acción</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700" x-show="!loading">
                    <template x-if="results.length === 0">
                        <tr>
                            <td colspan="5" class="px-3 py-6 text-center text-gray-500">Sin resultados</td>
                        </tr>
                    </template>
                    <template x-for="row in results" :key="row.id">
                        <tr>
                            <td class="px-3 py-2" x-text="row.product_name"></td>
                            <td class="px-3 py-2" x-text="row.label"></td>
                            <td class="px-3 py-2" x-text="row.sku || '-' "></td>
                            <td class="px-3 py-2" x-text="row.barcode || '-' "></td>
                            <td class="px-3 py-2">
                                <button type="button" @click="selectVariant(row)"
                                    class="inline-flex items-center gap-2 px-3 py-1.5 text-xs rounded-md bg-purple-600 hover:bg-purple-700 text-white">
                                    <i class="fas fa-check"></i> Seleccionar
                                </button>
                            </td>
                        </tr>
                    </template>
                </tbody>
                <tbody x-show="loading">
                    <tr>
                        <td colspan="5" class="px-3 py-6 text-center text-gray-500">Cargando...</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <!-- Paginación -->
        <div class="mt-3 flex items-center justify-between text-sm">
            <div class="text-gray-600 dark:text-gray-300">
                <span x-text="`Página ${page} de ${lastPage}`"></span>
                <template x-if="total">
                    <span class="ml-2 text-xs text-gray-500">(<span x-text="total"></span> resultados)</span>
                </template>
            </div>
            <div class="space-x-2">
                <button type="button" @click="page>1 && search(page-1)" :disabled="page <= 1"
                    class="px-3 py-1.5 rounded-md border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 disabled:opacity-50 disabled:cursor-not-allowed">Anterior</button>
                <button type="button" @click="page<lastPage && search(page+1)" :disabled="page >= lastPage"
                    class="px-3 py-1.5 rounded-md border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 disabled:opacity-50 disabled:cursor-not-allowed">Siguiente</button>
            </div>
        </div>
    </div>

    <!-- Campo oculto canónico para enviar la variante seleccionada -->
    <input type="hidden" name="product_variant_id" x-ref="variantHidden" value="{{ old('product_variant_id') }}">
    @error('product_variant_id')
        <div class="text-xs text-red-600 dark:text-red-400 mt-1">{{ $message }}</div>
    @enderror
</fieldset>
