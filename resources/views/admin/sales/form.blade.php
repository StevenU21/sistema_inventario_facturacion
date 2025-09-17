@php
    // helpers for old values
    $oldItems = old('items', []);
@endphp
<div x-data="saleForm()" x-init="init()"
    class="px-4 py-3 mb-8 bg-white rounded-lg shadow-md dark:bg-gray-800 border border-gray-200 dark:border-gray-700 w-full">

    <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-200 mb-3">Datos de la venta</h3>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <label class="block text-sm w-full">
            <span class="text-gray-700 dark:text-gray-200">Cliente</span>
            <select name="entity_id" x-model="sale.entity_id"
                class="block w-full mt-1 px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 {{ $errors->has('entity_id') ? 'border-red-600' : '' }}">
                <option value="">Seleccionar cliente</option>
                @foreach ($entities ?? [] as $id => $name)
                    <option value="{{ $id }}" {{ old('entity_id') == $id ? 'selected' : '' }}>
                        {{ $name }}</option>
                @endforeach
            </select>
            @error('entity_id')
                <span class="text-xs text-red-600 dark:text-red-400">{{ $message }}</span>
            @enderror
        </label>
        <label class="block text-sm w-full">
            <span class="text-gray-700 dark:text-gray-200">Método de pago</span>
            <select name="payment_method_id" x-model="sale.payment_method_id"
                class="block w-full mt-1 px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 {{ $errors->has('payment_method_id') ? 'border-red-600' : '' }}">
                <option value="">Seleccionar método</option>
                @foreach ($methods ?? [] as $id => $name)
                    <option value="{{ $id }}" {{ old('payment_method_id') == $id ? 'selected' : '' }}>
                        {{ $name }}</option>
                @endforeach
            </select>
            @error('payment_method_id')
                <span class="text-xs text-red-600 dark:text-red-400">{{ $message }}</span>
            @enderror
        </label>
    </div>

    <div class="mt-4 grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
        <div class="md:col-span-1">
            <label class="block text-sm w-full">
                <span class="text-gray-700 dark:text-gray-200">Tipo de pago</span>
            </label>
            <div class="flex items-center gap-4 mt-1">
                <label class="inline-flex items-center gap-2 text-sm">
                    <input type="radio" name="is_credit" value="0" x-model="sale.is_credit"
                        :checked="!sale.is_credit">
                    <span>Contado</span>
                </label>
                <label class="inline-flex items-center gap-2 text-sm">
                    <input type="radio" name="is_credit" value="1" x-model="sale.is_credit">
                    <span>Crédito</span>
                </label>
            </div>
            @error('is_credit')
                <span class="text-xs text-red-600 dark:text-red-400">{{ $message }}</span>
            @enderror
        </div>
    </div>

    <hr class="my-6 border-gray-200 dark:border-gray-700">

    <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-200 mb-3">Agregar productos</h3>

    <div class="rounded-lg border border-gray-200 dark:border-gray-700 p-4" x-data="variantSearch()"
        x-init="init()">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-3">
            <div class="md:col-span-9 col-span-1 flex flex-col">
                <label class="block text-sm w-full">
                    <span class="text-gray-700 dark:text-gray-200">Buscar producto/variante</span>
                    <x-autocomplete name="variant_search" id="variant_search"
                        url="{{ route('inventories.variantSearch') }}" placeholder="Nombre del producto..." min="2"
                        debounce="250" submit="0" event="variant-search" />
                </label>
            </div>
            <div class="md:col-span-3 col-span-1 flex items-end">
                <button type="button" @click="search(1)"
                    class="inline-flex items-center gap-2 px-3 py-2 text-sm rounded-lg bg-purple-600 hover:bg-purple-700 text-white shadow focus:outline-none focus:ring-2 focus:ring-purple-500 min-h-[40px] font-semibold w-full">
                    <i class="fas fa-search fa-sm mr-1"></i> Buscar
                </button>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-6 gap-3 mt-3">
            <label class="block text-sm w-full">
                <span class="text-gray-700 dark:text-gray-200">Proveedor</span>
                <select x-model="filters.entity_id" @change="search(1)"
                    class="block w-full mt-1 px-3 py-2 text-sm border rounded-lg dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700">
                    <option value="">Todos</option>
                    @foreach ($suppliers ?? [] as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
            </label>
            <label class="block text-sm w-full">
                <span class="text-gray-700 dark:text-gray-200">Color</span>
                <select x-model="filters.color_id" @change="search(1)"
                    class="block w-full mt-1 px-3 py-2 text-sm border rounded-lg dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700">
                    <option value="">Todos</option>
                    @foreach ($colors ?? [] as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
            </label>
            <label class="block text-sm w-full">
                <span class="text-gray-700 dark:text-gray-200">Talla</span>
                <select x-model="filters.size_id" @change="search(1)"
                    class="block w-full mt-1 px-3 py-2 text-sm border rounded-lg dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700">
                    <option value="">Todas</option>
                    @foreach ($sizes ?? [] as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
            </label>
            <label class="block text-sm w-full">
                <span class="text-gray-700 dark:text-gray-200">Almacén</span>
                <select x-model="filters.warehouse_id" @change="$dispatch('warehouse-changed', $event.target.value); search(1)"
                    class="block w-full mt-1 px-3 py-2 text-sm border rounded-lg dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700">
                    <option value="">Seleccione</option>
                    @foreach ($warehouses ?? [] as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
            </label>
            <label class="block text-sm w-full">
                <span class="text-gray-700 dark:text-gray-200">Categoría</span>
                <select x-model="filters.category_id" @change="onCategoryChange"
                    class="block w-full mt-1 px-3 py-2 text-sm border rounded-lg dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700">
                    <option value="">Todas</option>
                    @foreach ($categories ?? [] as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
            </label>
            <label class="block text-sm w-full">
                <span class="text-gray-700 dark:text-gray-200">Marca</span>
                <select x-model="filters.brand_id" @change="search(1)"
                    class="block w-full mt-1 px-3 py-2 text-sm border rounded-lg dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700">
                    <option value="">Todas</option>
                    <template x-for="b in brandOptions" :key="b.id">
                        <option :value="b.id" x-text="b.name"></option>
                    </template>
                </select>
            </label>
        </div>
        <div class="mt-3 overflow-x-auto border border-gray-200 dark:border-gray-700 rounded-lg">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                <thead class="bg-gray-50 dark:bg-gray-800/50">
                    <tr class="text-left text-gray-600 dark:text-gray-300">
                        <th class="px-3 py-2">Producto</th>
                        <th class="px-3 py-2">Color</th>
                        <th class="px-3 py-2">Talla</th>
                        <th class="px-3 py-2">Marca</th>
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
                            <td class="px-3 py-2 text-gray-700 dark:text-gray-200" x-text="row.product_name"></td>
                            <td class="px-3 py-2 text-gray-700 dark:text-gray-200" x-text="row.color_name || '-' ">
                            </td>
                            <td class="px-3 py-2 text-gray-700 dark:text-gray-200" x-text="row.size_name || '-' ">
                            </td>
                            <td class="px-3 py-2 text-gray-700 dark:text-gray-200" x-text="row.brand_name || '-' ">
                            </td>
                            <td class="px-3 py-2">
                                <button type="button" @click="$dispatch('add-item', { id: row.id })"
                                    class="inline-flex items-center gap-2 px-3 py-1.5 text-xs rounded-md bg-purple-600 hover:bg-purple-700 text-white">
                                    <i class="fas fa-plus"></i> Agregar
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
    </div>

    <div class="mt-6">
        <h4 class="text-base font-semibold text-gray-800 dark:text-gray-100">Detalle de la venta</h4>
        <div class="mt-3 overflow-x-auto border border-gray-200 dark:border-gray-700 rounded-lg">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                <thead class="bg-gray-50 dark:bg-gray-800/50">
                    <tr class="text-left text-gray-600 dark:text-gray-300">
                        <th class="px-3 py-2">Producto</th>
                        <th class="px-3 py-2 text-right">Precio (c/imp)</th>
                        <th class="px-3 py-2 text-right">Stock</th>
                        <th class="px-3 py-2 text-right">Cantidad</th>
                        <th class="px-3 py-2 text-right">Descuento</th>
                        <th class="px-3 py-2 text-right">Subtotal</th>
                        <th class="px-3 py-2"></th>
                    </tr>
                </thead>
                <tbody>
                    <template x-if="items.length === 0">
                        <tr>
                            <td colspan="7" class="px-3 py-6 text-center text-gray-500">No hay productos en la
                                venta.</td>
                        </tr>
                    </template>
                    <template x-for="(it, idx) in items" :key="it.key">
                        <tr class="align-top">
                            <td class="px-3 py-2 text-gray-700 dark:text-gray-200">
                                <div class="font-medium text-gray-700 dark:text-gray-200" x-text="it.label"></div>
                                <input type="hidden" :name="`items[${idx}][product_variant_id]`"
                                    :value="it.product_variant_id">
                            </td>
                            <td class="px-3 py-2 text-right tabular-nums text-gray-700 dark:text-gray-200">
                                <span x-text="currency(it.unit_price)"></span>
                            </td>
                            <td class="px-3 py-2 text-right tabular-nums text-gray-700 dark:text-gray-200">
                                <span x-text="it.stock"></span>
                            </td>
                            <td class="px-3 py-2 text-right">
                                <input type="number" min="1" step="1"
                                    class="w-24 text-right px-2 py-1 border rounded" :name="`items[${idx}][quantity]`"
                                    x-model.number="it.quantity" @input="recalc(idx)" />
                                @error('items.*.quantity')
                                    <div class="text-xs text-red-600 dark:text-red-400">{{ $message }}</div>
                                @enderror
                            </td>
                            <td class="px-3 py-2 text-right">
                                <label class="inline-flex items-center gap-2 text-xs">
                                    <input type="checkbox" :name="`items[${idx}][discount]`" value="1"
                                        x-model="it.discount" @change="recalc(idx)">
                                    <span>Aplica</span>
                                </label>
                                <div class="mt-1">
                                    <input type="number" min="0" step="0.01"
                                        class="w-28 text-right px-2 py-1 border rounded"
                                        :name="`items[${idx}][discount_amount]`" x-model.number="it.discount_amount"
                                        :disabled="!it.discount" @input="recalc(idx)" />
                                </div>
                            </td>
                            <td class="px-3 py-2 text-right tabular-nums text-gray-700 dark:text-gray-200">
                                <span x-text="currency(it.sub_total)"></span>
                            </td>
                            <td class="px-3 py-2 text-right">
                                <button type="button" @click="remove(idx)" class="text-red-600 hover:text-red-700">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    </template>
                </tbody>
                <tfoot class="bg-gray-50 dark:bg-gray-800/50">
                    <tr>
                        <td class="px-3 py-2" colspan="4"></td>
                        <td class="px-3 py-2 text-right font-semibold text-gray-700 dark:text-gray-200">Impuesto</td>
                        <td class="px-3 py-2 text-right tabular-nums text-gray-700 dark:text-gray-200"><span
                                x-text="currency(totals.tax)"></span></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td class="px-3 py-2" colspan="4"></td>
                        <td class="px-3 py-2 text-right font-bold text-gray-700 dark:text-gray-200">Total</td>
                        <td class="px-3 py-2 text-right tabular-nums font-bold text-gray-700 dark:text-gray-200"><span
                                x-text="currency(totals.total)"></span></td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <div class="mt-6 flex gap-2">
        <x-ui.submit-button data-label="Guardar" />
    </div>

    <script>
        function saleForm() {
            return {
                sale: {
                    entity_id: @json(old('entity_id')),
                    payment_method_id: @json(old('payment_method_id')),
                    is_credit: @json(old('is_credit', 0)) == 1,
                },
                items: [],
                totals: {
                    tax: 0,
                    total: 0
                },
                init() {
                    // Cargar items viejos si hubo validación
                    const oldItems = @json($oldItems);
                    if (Array.isArray(oldItems) && oldItems.length) {
                        this.items = oldItems.map((row, i) => ({
                            key: i + '_' + Date.now(),
                            product_variant_id: Number(row.product_variant_id),
                            label: row.label || `Variante #${row.product_variant_id}`,
                            unit_price: Number(row.unit_price || 0),
                            stock: Number(row.stock || 0),
                            quantity: Number(row.quantity || 1),
                            discount: !!row.discount,
                            discount_amount: Number(row.discount_amount || 0),
                            sub_total: Number(row.sub_total || 0),
                        }));
                        this.recalcAll();
                    }
                    // Sincronizar almacén seleccionado desde el buscador
                    this.sale.warehouse_id = Number(@json(old('warehouse_id'))) || null;
                    window.addEventListener('warehouse-changed', (e) => {
                        this.sale.warehouse_id = Number(e.detail) || null;
                    });
                    // Inicializar valor en el buscador (si venimos de validación)
                    if (this.sale.warehouse_id) {
                        window.dispatchEvent(new CustomEvent('set-warehouse', {
                            detail: this.sale.warehouse_id
                        }));
                    }
                    // Escuchar evento para agregar variante
                    window.addEventListener('add-item', (e) => {
                        const variantId = Number(e.detail?.id || 0);
                        if (!variantId || !this.sale.warehouse_id) {
                            alert('Seleccione primero un almacén.');
                            return;
                        }
                        this.fetchInventory(variantId);
                    });
                },
                async fetchInventory(variantId) {
                    try {
                        const url = new URL(@json(route('admin.sales.inventory')));
                        url.searchParams.set('product_variant_id', variantId);
                        url.searchParams.set('warehouse_id', this.sale.warehouse_id);
                        const res = await fetch(url.toString(), {
                            headers: {
                                'Accept': 'application/json'
                            }
                        });
                        if (!res.ok) {
                            const j = await res.json().catch(() => ({}));
                            throw new Error(j.message || 'No se pudo obtener el inventario');
                        }
                        const data = await res.json();
                        const exists = this.items.find(it => it.product_variant_id === data.product_variant_id);
                        if (exists) {
                            exists.quantity += 1;
                            this.recalcItem(exists);
                            return;
                        }
                        const it = {
                            key: Date.now() + '_' + data.product_variant_id,
                            product_variant_id: data.product_variant_id,
                            label: data.label,
                            unit_price: Number(data.unit_price_with_tax || data.sale_price || 0),
                            stock: Number(data.stock || 0),
                            quantity: 1,
                            discount: false,
                            discount_amount: 0,
                            sub_total: 0,
                        };
                        this.items.push(it);
                        this.recalcItem(it);
                    } catch (e) {
                        alert(e.message);
                    }
                },
                recalc(idx) {
                    const it = this.items[idx];
                    if (it) this.recalcItem(it);
                },
                recalcItem(it) {
                    const qty = Math.max(1, Number(it.quantity || 1));
                    const unit = Number(it.unit_price || 0);
                    const disc = it.discount ? Math.max(0, Number(it.discount_amount || 0)) : 0;
                    it.sub_total = Math.max(0, (unit * qty) - disc);
                    this.recalcAll();
                },
                recalcAll() {
                    let total = 0,
                        tax = 0;
                    this.items.forEach(it => {
                        total += Number(it.sub_total || 0);
                        // No tenemos porcentaje preciso por línea en el cliente; solo aproximamos si se conoce por fetch
                        // Si llegara tax_percentage/ unit_tax_amount en el endpoint, se podría usar por línea.
                    });
                    this.totals.total = round2(total);
                    this.totals.tax = 0; // El backend calculará exacto; lo mostramos 0 o podríamos aproximar.
                },
                remove(idx) {
                    this.items.splice(idx, 1);
                    this.recalcAll();
                },
                currency(v) {
                    return 'C$ ' + Number(v || 0).toFixed(2);
                },
            }
        }

        function variantSearch() {
            return {
                filters: {
                    q: '',
                    entity_id: '',
                    category_id: '',
                    brand_id: '',
                    color_id: '',
                    size_id: '',
                    warehouse_id: Number(@json(old('warehouse_id'))) || ''
                },
                brandOptions: @json(collect($brands ?? [])->map(fn($name,$id)=>['id'=>$id,'name'=>$name])->values()),
                results: [],
                loading: false,
                init() {
                    window.addEventListener('variant-search', (e) => {
                        this.filters.q = e.detail?.text || '';
                        this.search(1);
                    });
                    // Recibir almacén inicial desde el padre
                    window.addEventListener('set-warehouse', (e) => {
                        this.filters.warehouse_id = Number(e.detail) || '';
                    });
                },
                async onCategoryChange(e) {
                    // Reset brand and fetch brands for selected category
                    this.filters.category_id = e.target.value || '';
                    this.filters.brand_id = '';
                    try {
                        const url = new URL(@json(route('admin.sales.brandsByCategory')));
                        if (this.filters.category_id) {
                            url.searchParams.set('category_id', this.filters.category_id);
                        }
                        const res = await fetch(url.toString(), { headers: { 'Accept': 'application/json' } });
                        const data = await res.json();
                        this.brandOptions = (data && Array.isArray(data.data)) ? data.data : [];
                    } catch (_) {
                        this.brandOptions = [];
                    } finally {
                        // Trigger search after options are updated
                        this.search(1);
                    }
                },
                async search(page = 1) {
                    this.loading = true;
                    try {
                        const url = new URL(@json(route('admin.sales.productSearch')));
                        Object.entries(this.filters).forEach(([k, v]) => {
                            if (v) url.searchParams.set(k, v);
                        });
                        url.searchParams.set('page', page);
                        const res = await fetch(url.toString(), {
                            headers: {
                                'Accept': 'application/json'
                            }
                        });
                        const data = await res.json();
                        this.results = Array.isArray(data) ? data : (data.data || []);
                    } catch (e) {
                        this.results = [];
                    } finally {
                        this.loading = false;
                    }
                }
            }
        }

        function round2(n) {
            return Math.round((Number(n || 0) + Number.EPSILON) * 100) / 100;
        }
    </script>
    <!-- Enviar almacén seleccionado al backend -->
    <input type="hidden" name="warehouse_id" :value="sale.warehouse_id">
</div>
