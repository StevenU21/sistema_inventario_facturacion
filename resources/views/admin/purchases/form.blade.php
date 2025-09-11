<div
    class="px-4 py-3 mb-8 bg-white rounded-lg shadow-md dark:bg-gray-800 border border-gray-200 dark:border-gray-700 w-full">

    <!-- Totales y usuario ahora se calculan/inyectan en el servidor -->

    <hr class="my-6 border-gray-200 dark:border-gray-700">

    <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-200 mb-4">Producto</h3>
    <!-- Fila 1: Nombre - Proveedor - Almacén -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <label class="block text-sm w-full">
            <span class="text-gray-700 dark:text-gray-200">Nombre del producto</span>
            <input type="text" name="product[name]" value="{{ old('product.name', optional($product)->name) }}"
                class="block w-full mt-1 px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700" placeholder="Nombre del producto">
        </label>

        <label class="block text-sm w-full">
            <span class="text-gray-700 dark:text-gray-200">Proveedor</span>
            <select name="entity_id"
                class="block w-full mt-1 px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 @error('entity_id') border-red-600 @enderror"
                required>
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
            <select name="warehouse_id"
                class="block w-full mt-1 px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 @error('warehouse_id') border-red-600 @enderror"
                required>
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
    </div>

    <!-- Fila 2: Categoría - Marca - Referencia -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
        <label class="block text-sm w-full">
            <span class="text-gray-700 dark:text-gray-200">Categoría</span>
            <select name="product[category_id]"
                class="block w-full mt-1 px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700">
                <option value="">Seleccionar Categoría</option>
                @foreach ($categories ?? [] as $id => $name)
                    <option value="{{ $id }}"
                        {{ old('product.category_id', optional($product)->category_id) == $id ? 'selected' : '' }}>
                        {{ $name }}</option>
                @endforeach
            </select>
        </label>

        <label class="block text-sm w-full">
            <span class="text-gray-700 dark:text-gray-200">Marca</span>
            <select name="product[brand_id]"
                class="block w-full mt-1 px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700">
                <option value="">Seleccionar Marca</option>
                @foreach ($brands ?? [] as $id => $name)
                    <option value="{{ $id }}"
                        {{ old('product.brand_id', optional($product)->brand_id) == $id ? 'selected' : '' }}>
                        {{ $name }}</option>
                @endforeach
            </select>
        </label>

        <label class="block text-sm w-full">
            <span class="text-gray-700 dark:text-gray-200">Referencia</span>
            <input type="text" name="reference" value="{{ old('reference', $purchase->reference ?? '') }}"
                class="block w-full mt-1 px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 @error('reference') border-red-600 @enderror"
                placeholder="Opcional...">
            @error('reference')
                <span class="text-xs text-red-600 dark:text-red-400">{{ $message }}</span>
            @enderror
        </label>
    </div>

    <!-- Fila 3: Método de pago - Impuesto -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
        <label class="block text-sm w-full">
            <span class="text-gray-700 dark:text-gray-200">Método de pago</span>
            <select name="payment_method_id"
                class="block w-full mt-1 px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 @error('payment_method_id') border-red-600 @enderror"
                required>
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

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <label class="block text-sm w-full">
                <span class="text-gray-700 dark:text-gray-200">Impuesto</span>
                <select name="product[tax_id]"
                    class="block w-full mt-1 px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700">
                    <option value="">Seleccionar Impuesto</option>
                    @foreach ($taxes ?? [] as $id => $name)
                        <option value="{{ $id }}"
                            {{ old('product.tax_id', optional($product)->tax_id) == $id ? 'selected' : '' }}>
                            {{ $name }}</option>
                    @endforeach
                </select>
            </label>

            <label class="block text-sm w-full">
                <span class="text-gray-700 dark:text-gray-200">Unidad de medida</span>
                <select name="product[unit_measure_id]"
                    class="block w-full mt-1 px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700">
                    <option value="">Seleccionar Unidad de Medida</option>
                    @foreach ($units ?? [] as $id => $name)
                        <option value="{{ $id }}"
                            {{ old('product.unit_measure_id', optional($product)->unit_measure_id) == $id ? 'selected' : '' }}>
                            {{ $name }}</option>
                    @endforeach
                </select>
            </label>
        </div>
    </div>

    <!-- Descripción -->
    <div class="mt-6">
        <label class="block text-sm w-full">
            <span class="text-gray-700 dark:text-gray-200">Descripción</span>
            <textarea name="product[description]" rows="3" placeholder="Opcional..."
                class="block w-full mt-1 px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700">{{ old('product.description', optional($product)->description) }}</textarea>
        </label>
    </div>

    <div class="mt-6" x-data="purchaseForm()" x-init="init()">
        <div class="flex items-center justify-between mb-3">
            <h4 class="text-base font-semibold text-gray-800 dark:text-gray-100">Variantes / Líneas</h4>
            <button type="button" x-ref="addBtn"
                class="inline-flex items-center gap-2 px-3 py-1.5 text-sm rounded-lg bg-purple-600 hover:bg-purple-700 text-white shadow focus:outline-none focus:ring-2 focus:ring-purple-500 min-h-[36px] font-semibold">
                <i class="fas fa-plus fa-sm mr-2"></i>
                Agregar línea
            </button>
        </div>
        <div id="lines" x-ref="lines" class="space-y-3">
            <!-- Lines will be added here -->
            @php
                $oldDetails = old('details');
                $detailsToShow =
                    is_array($oldDetails) && count($oldDetails)
                        ? $oldDetails
                        : (isset($prefillDetails) && is_array($prefillDetails) && count($prefillDetails)
                            ? $prefillDetails
                            : []);
                $initialCount =
                    is_array($oldDetails) && count($oldDetails)
                        ? count($oldDetails)
                        : (is_array($detailsToShow)
                            ? count($detailsToShow)
                            : 0);
            @endphp
            @if (is_array($detailsToShow) && count($detailsToShow))
                @foreach ($detailsToShow as $i => $line)
                    <div class="grid grid-cols-1 md:grid-cols-7 gap-4 items-end border rounded p-3" data-line>
                        <div class="col-span-1">
                            <label class="block text-sm"><span class="text-gray-700 dark:text-gray-200">Color</span>
                                <select name="details[{{ $i }}][color_id]"
                                    class="block w-full mt-1 px-3 py-2 text-sm border rounded-lg dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700"
                                    placeholder="Color">
                                    <option value="">Ninguno</option>
                                    @foreach ($colors ?? [] as $id => $name)
                                        <option value="{{ $id }}"
                                            {{ (string) old("details.$i.color_id", $line['color_id'] ?? '') === (string) $id ? 'selected' : '' }}>
                                            {{ $name }}</option>
                                    @endforeach
                                </select>
                            </label>
                            @error('details.' . $i . '.color_id')
                                <span class="text-xs text-red-600 dark:text-red-400">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-span-1">
                            <label class="block text-sm"><span class="text-gray-700 dark:text-gray-200">Talla</span>
                                <select name="details[{{ $i }}][size_id]"
                                    class="block w-full mt-1 px-3 py-2 text-sm border rounded-lg dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700"
                                    placeholder="Talla">
                                    <option value="">Ninguna</option>
                                    @foreach ($sizes ?? [] as $id => $name)
                                        <option value="{{ $id }}"
                                            {{ (string) old("details.$i.size_id", $line['size_id'] ?? '') === (string) $id ? 'selected' : '' }}>
                                            {{ $name }}</option>
                                    @endforeach
                                </select>
                            </label>
                            @error('details.' . $i . '.size_id')
                                <span class="text-xs text-red-600 dark:text-red-400">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-span-1">
                            <label class="block text-sm"><span
                                    class="text-gray-700 dark:text-gray-200">Cantidad</span>
                                <input type="number" min="1" step="1"
                                    name="details[{{ $i }}][quantity]"
                                    value="{{ old("details.$i.quantity", $line['quantity'] ?? '') }}"
                                    class="block w-full mt-1 px-3 py-2 text-sm border rounded-lg dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700"
                                    placeholder="Cantidad">
                            </label>
                            @error('details.' . $i . '.quantity')
                                <span class="text-xs text-red-600 dark:text-red-400">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-span-1">
                            <label class="block text-sm"><span class="text-gray-700 dark:text-gray-200">Precio
                                    unitario</span>
                                <input type="number" min="0" step="0.01"
                                    name="details[{{ $i }}][unit_price]"
                                    value="{{ old("details.$i.unit_price", $line['unit_price'] ?? '') }}"
                                    class="block w-full mt-1 px-3 py-2 text-sm border rounded-lg dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700"
                                    placeholder="Precio unitario">
                            </label>
                            @error('details.' . $i . '.unit_price')
                                <span class="text-xs text-red-600 dark:text-red-400">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-span-1">
                            <label class="block text-sm"><span class="text-gray-700 dark:text-gray-200">Precio
                                    venta</span>
                                <input type="number" min="0" step="0.01"
                                    name="details[{{ $i }}][sale_price]"
                                    value="{{ old("details.$i.sale_price", $line['sale_price'] ?? '') }}"
                                    class="block w-full mt-1 px-3 py-2 text-sm border rounded-lg dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700"
                                    placeholder="Precio venta">
                            </label>
                            @error('details.' . $i . '.sale_price')
                                <span class="text-xs text-red-600 dark:text-red-400">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-span-1">
                            <label class="block text-sm"><span class="text-gray-700 dark:text-gray-200">Stock
                                    mínimo</span>
                                <input type="number" min="0" step="1"
                                    name="details[{{ $i }}][min_stock]"
                                    value="{{ old("details.$i.min_stock", $line['min_stock'] ?? '') }}"
                                    class="block w-full mt-1 px-3 py-2 text-sm border rounded-lg dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700"
                                    placeholder="Stock mínimo (opcional)">
                            </label>
                            @error('details.' . $i . '.min_stock')
                                <span class="text-xs text-red-600 dark:text-red-400">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-span-1 flex gap-2">
                            <button type="button"
                                class="remove-line w-full px-0 py-0 text-sm rounded-lg bg-red-600 hover:bg-red-700 text-white flex items-center justify-center gap-2 font-semibold min-h-[36px]">
                                <i class="fas fa-trash fa-sm mr-2"></i> Quitar
                            </button>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
        <!-- Template must be inside the Alpine component to be accessible via $refs -->
        <template id="line-template" x-ref="tpl">
            <div class="grid grid-cols-1 md:grid-cols-7 gap-4 items-end border rounded p-3" data-line>
                <div class="col-span-1">
                    <label class="block text-sm"><span class="text-gray-700 dark:text-gray-200">Color</span>
                        <select name="details[__INDEX__][color_id]"
                            class="block w-full mt-1 px-3 py-2 text-sm border rounded-lg dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700"
                            placeholder="Color">
                            <option value="">Ninguno</option>
                            @foreach ($colors ?? [] as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </label>
                </div>
                <div class="col-span-1">
                    <label class="block text-sm"><span class="text-gray-700 dark:text-gray-200">Talla</span>
                        <select name="details[__INDEX__][size_id]"
                            class="block w-full mt-1 px-3 py-2 text-sm border rounded-lg dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700"
                            placeholder="Talla">
                            <option value="">Ninguna</option>
                            @foreach ($sizes ?? [] as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </label>
                </div>
                <div class="col-span-1">
                    <label class="block text-sm"><span class="text-gray-700 dark:text-gray-200">Cantidad</span>
                        <input type="number" min="1" step="1" name="details[__INDEX__][quantity]"
                            class="block w-full mt-1 px-3 py-2 text-sm border rounded-lg dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700"
                            placeholder="Cantidad">
                    </label>
                </div>
                <div class="col-span-1">
                    <label class="block text-sm"><span class="text-gray-700 dark:text-gray-200">Precio unitario</span>
                        <input type="number" min="0" step="0.01" name="details[__INDEX__][unit_price]"
                            class="block w-full mt-1 px-3 py-2 text-sm border rounded-lg dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700"
                            placeholder="Precio unitario">
                    </label>
                </div>
                <div class="col-span-1">
                    <label class="block text-sm"><span class="text-gray-700 dark:text-gray-200">Precio venta</span>
                        <input type="number" min="0" step="0.01" name="details[__INDEX__][sale_price]"
                            class="block w-full mt-1 px-3 py-2 text-sm border rounded-lg dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700"
                            placeholder="Precio venta">
                    </label>
                </div>
                <div class="col-span-1">
                    <label class="block text-sm"><span class="text-gray-700 dark:text-gray-200">Stock mínimo</span>
                        <input type="number" min="0" step="1" name="details[__INDEX__][min_stock]"
                            class="block w-full mt-1 px-3 py-2 text-sm border rounded-lg dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700"
                            placeholder="Stock mínimo (opcional)">
                    </label>
                </div>
                <div class="col-span-1 flex gap-2">
                    <button type="button"
                        class="remove-line w-full px-0 py-0 text-sm rounded-lg bg-red-600 hover:bg-red-700 text-white flex items-center justify-center gap-2 font-semibold min-h-[40px]">
                        <i class="fas fa-trash fa-sm mr-2"></i> Quitar
                    </button>
                </div>
            </div>
        </template>
    </div>
    <script>
        function purchaseForm() {
            return {
                idx: Number(@json($initialCount)),
                linesEl: null,
                tplHtml: null,
                init() {
                    this.linesEl = this.$refs.lines;
                    this.tplHtml = this.$refs.tpl ? this.$refs.tpl.innerHTML : (document.getElementById('line-template')
                        ?.innerHTML || '');

                    // Crear elemento para mensajes si no existe
                    if (!document.getElementById('line-message')) {
                        const msg = document.createElement('div');
                        msg.id = 'line-message';
                        msg.className = 'text-sm text-red-600 dark:text-red-400 my-2 hidden';
                        this.linesEl.parentNode.insertBefore(msg, this.linesEl);
                    }
                    this.messageEl = document.getElementById('line-message');

                    // Delegación para quitar líneas (sirve para líneas renderizadas por Blade y nuevas)
                    this.linesEl.addEventListener('click', (e) => {
                        const btn = e.target.closest('.remove-line');
                        if (btn) {
                            const line = btn.closest('[data-line]');
                            const total = this.linesEl.querySelectorAll('[data-line]').length;
                            if (line && total > 1) {
                                line.remove();
                            } else if (line && total === 1) {
                                this.showMessage('No es posible quitar la única línea.');
                            }
                        }
                    });

                    // Botón agregar
                    this.$refs.addBtn.addEventListener('click', () => this.addLine());

                    // Agregar una línea vacía solo si no hay previas por validación ni precargadas
                    const hasOld = @json(is_array(old('details')) && count(old('details')) ? true : false);
                    const hasPrefill = @json(is_array($detailsToShow) && count($detailsToShow) ? true : false);
                    if (!hasOld && !hasPrefill) {
                        this.addLine();
                    }
                },
                showMessage(msg) {
                    if (this.messageEl) {
                        this.messageEl.textContent = msg;
                        this.messageEl.classList.remove('hidden');
                        setTimeout(() => {
                            this.messageEl.classList.add('hidden');
                        }, 2500);
                    }
                },
                addLine(prefill = {}) {
                    let html = this.tplHtml.replace(/__INDEX__/g, this.idx);
                    html = html.replace(/\$\$INDEX\$\$/g, this.idx); // compat para errores si se requiere
                    const wrapper = document.createElement('div');
                    wrapper.innerHTML = html.trim();
                    const node = wrapper.firstChild;
                    this.linesEl.appendChild(node);

                    // Prefill opcional
                    if (prefill.quantity) node.querySelector(`[name="details[${this.idx}][quantity]"]`).value = prefill
                        .quantity;
                    if (prefill.unit_price) node.querySelector(`[name="details[${this.idx}][unit_price]"]`).value = prefill
                        .unit_price;
                    if (prefill.sale_price) node.querySelector(`[name="details[${this.idx}][sale_price]"]`).value = prefill
                        .sale_price;
                    if (prefill.color_id) node.querySelector(`[name="details[${this.idx}][color_id]"]`).value = prefill
                        .color_id;
                    if (prefill.size_id) node.querySelector(`[name="details[${this.idx}][size_id]"]`).value = prefill
                        .size_id;
                    if (prefill.min_stock) node.querySelector(`[name="details[${this.idx}][min_stock]"]`).value = prefill
                        .min_stock;

                    this.idx++;
                }
            }
        }
    </script>

    <div class="mt-6 flex gap-2">
        <button type="submit"
            class="flex items-center px-4 py-2 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-purple-600 border border-transparent rounded-lg hover:bg-purple-700 focus:outline-none focus:shadow-outline-purple active:bg-purple-600">
            <i class="fas fa-paper-plane mr-2"></i> {{ isset($color) ? 'Actualizar' : 'Guardar' }}
        </button>
    </div>
</div>
