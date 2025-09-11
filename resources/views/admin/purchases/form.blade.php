<div
    class="px-4 py-3 mb-8 bg-white rounded-lg shadow-md dark:bg-gray-800 border border-gray-200 dark:border-gray-700 w-full">

    <!-- Totales y usuario ahora se calculan/inyectan en el servidor -->

    <hr class="my-6 border-gray-200 dark:border-gray-700">

    <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-200 mb-4">Producto</h3>
    <!-- Fila 1: Nombre - Proveedor - Almacén -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <label class="block text-sm w-full">
            <span class="text-gray-700 dark:text-gray-200">Nombre del producto</span>
            <input type="text" name="product[name]" value="{{ old('product.name') }}"
                class="block w-full mt-1 px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700">
        </label>

        <label class="block text-sm w-full">
            <span class="text-gray-700 dark:text-gray-200">Proveedor</span>
            <select name="entity_id"
                class="block w-full mt-1 px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 @error('entity_id') border-red-600 @enderror"
                required>
                <option value="">Seleccionar</option>
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
                <option value="">Seleccionar</option>
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
                <option value="">Seleccionar</option>
                @foreach ($categories ?? [] as $id => $name)
            <option value="{{ $id }}" {{ old('product.category_id') == $id ? 'selected' : '' }}>
                        {{ $name }}</option>
                @endforeach
            </select>
        </label>

        <label class="block text-sm w-full">
            <span class="text-gray-700 dark:text-gray-200">Marca</span>
        <select name="product[brand_id]"
                class="block w-full mt-1 px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700">
                <option value="">Seleccionar</option>
                @foreach ($brands ?? [] as $id => $name)
            <option value="{{ $id }}" {{ old('product.brand_id') == $id ? 'selected' : '' }}>
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
                <option value="">Seleccionar</option>
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
                    <option value="">Seleccionar</option>
                    @foreach ($taxes ?? [] as $id => $name)
                        <option value="{{ $id }}" {{ old('product.tax_id') == $id ? 'selected' : '' }}>
                            {{ $name }}</option>
                    @endforeach
                </select>
            </label>

            <label class="block text-sm w-full">
                <span class="text-gray-700 dark:text-gray-200">Unidad de medida</span>
                <select name="product[unit_measure_id]"
                    class="block w-full mt-1 px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700">
                    <option value="">Seleccionar</option>
                    @foreach ($units ?? [] as $id => $name)
                        <option value="{{ $id }}" {{ old('product.unit_measure_id') == $id ? 'selected' : '' }}>
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
            <textarea name="product[description]" rows="3"
                class="block w-full mt-1 px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700">{{ old('product_description') }}</textarea>
        </label>
    </div>

    <div class="mt-6">
        <div class="flex items-center justify-between mb-2">
            <h4 class="font-medium text-gray-700 dark:text-gray-200">Variantes / Líneas</h4>
            <button type="button" id="add-line" class="px-3 py-1.5 text-sm rounded bg-green-600 text-white">Agregar
                línea</button>
        </div>
    <div id="lines" class="space-y-3">
            <!-- Lines will be added here -->
        @php $oldDetails = old('details', []); @endphp
        @if (is_array($oldDetails) && count($oldDetails))
        @foreach ($oldDetails as $i => $line)
                    <div class="grid grid-cols-1 md:grid-cols-8 gap-4 items-end border rounded p-3" data-line>
                        <div>
                            <label class="block text-sm"><span class="text-gray-700 dark:text-gray-200">Color</span>
                <select name="details[{{ $i }}][color_id]"
                                    class="block w-full mt-1 px-3 py-2 text-sm border rounded-lg dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700">
                                    <option value="">Ninguno</option>
                                    @foreach ($colors ?? [] as $id => $name)
                    <option value="{{ $id }}" {{ (string) old("details.$i.color_id") === (string) $id ? 'selected' : '' }}>
                                            {{ $name }}</option>
                                    @endforeach
                                </select>
                            </label>
                @error('details.' . $i . '.color_id')
                                <span class="text-xs text-red-600 dark:text-red-400">{{ $message }}</span>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm"><span class="text-gray-700 dark:text-gray-200">Talla</span>
                <select name="details[{{ $i }}][size_id]"
                                    class="block w-full mt-1 px-3 py-2 text-sm border rounded-lg dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700">
                                    <option value="">Ninguna</option>
                                    @foreach ($sizes ?? [] as $id => $name)
                    <option value="{{ $id }}" {{ (string) old("details.$i.size_id") === (string) $id ? 'selected' : '' }}>
                                            {{ $name }}</option>
                                    @endforeach
                                </select>
                            </label>
                @error('details.' . $i . '.size_id')
                                <span class="text-xs text-red-600 dark:text-red-400">{{ $message }}</span>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm"><span class="text-gray-700 dark:text-gray-200">Cantidad</span>
                <input type="number" min="1" step="1"
                    name="details[{{ $i }}][quantity]"
                    value="{{ old("details.$i.quantity") }}"
                                    class="block w-full mt-1 px-3 py-2 text-sm border rounded-lg dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700">
                            </label>
                @error('details.' . $i . '.quantity')
                                <span class="text-xs text-red-600 dark:text-red-400">{{ $message }}</span>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm"><span class="text-gray-700 dark:text-gray-200">Precio
                                    unitario</span>
                <input type="number" min="0" step="0.01"
                    name="details[{{ $i }}][unit_price]"
                    value="{{ old("details.$i.unit_price") }}"
                                    class="block w-full mt-1 px-3 py-2 text-sm border rounded-lg dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700">
                            </label>
                @error('details.' . $i . '.unit_price')
                                <span class="text-xs text-red-600 dark:text-red-400">{{ $message }}</span>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm"><span class="text-gray-700 dark:text-gray-200">Precio
                                    venta</span>
                                <input type="number" min="0" step="0.01"
                    name="details[{{ $i }}][sale_price]"
                    value="{{ old("details.$i.sale_price") }}"
                                    class="block w-full mt-1 px-3 py-2 text-sm border rounded-lg dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700"
                                    placeholder="Venta">
                            </label>
                @error('details.' . $i . '.sale_price')
                                <span class="text-xs text-red-600 dark:text-red-400">{{ $message }}</span>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm"><span class="text-gray-700 dark:text-gray-200">Stock mínimo</span>
                                <input type="number" min="0" step="1"
                                    name="details[{{ $i }}][min_stock]"
                                    value="{{ old("details.$i.min_stock") }}"
                                    class="block w-full mt-1 px-3 py-2 text-sm border rounded-lg dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700"
                                    placeholder="Opcional">
                            </label>
                            @error('details.' . $i . '.min_stock')
                                <span class="text-xs text-red-600 dark:text-red-400">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="flex gap-2">
                            <button type="button"
                                class="remove-line px-3 py-2 text-sm rounded bg-red-600 text-white w-full">Quitar</button>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>

    <template id="line-template">
    <div class="grid grid-cols-1 md:grid-cols-8 gap-4 items-end border rounded p-3" data-line>
            <div>
                <label class="block text-sm"><span class="text-gray-700 dark:text-gray-200">Color</span>
            <select name="details[__INDEX__][color_id]"
                        class="block w-full mt-1 px-3 py-2 text-sm border rounded-lg dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700">
                        <option value="">Ninguno</option>
                        @foreach ($colors ?? [] as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </label>
            </div>
            <div>
                <label class="block text-sm"><span class="text-gray-700 dark:text-gray-200">Talla</span>
            <select name="details[__INDEX__][size_id]"
                        class="block w-full mt-1 px-3 py-2 text-sm border rounded-lg dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700">
                        <option value="">Ninguna</option>
                        @foreach ($sizes ?? [] as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </label>
            </div>
            <div>
                <label class="block text-sm"><span class="text-gray-700 dark:text-gray-200">Cantidad</span>
            <input type="number" min="1" step="1" name="details[__INDEX__][quantity]"
                        class="block w-full mt-1 px-3 py-2 text-sm border rounded-lg dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700">
                </label>
            </div>
            <div>
                <label class="block text-sm"><span class="text-gray-700 dark:text-gray-200">Precio unitario</span>
            <input type="number" min="0" step="0.01" name="details[__INDEX__][unit_price]"
                        class="block w-full mt-1 px-3 py-2 text-sm border rounded-lg dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700">
                </label>
            </div>
            <div>
                <label class="block text-sm"><span class="text-gray-700 dark:text-gray-200">Precio venta</span>
            <input type="number" min="0" step="0.01" name="details[__INDEX__][sale_price]"
                        class="block w-full mt-1 px-3 py-2 text-sm border rounded-lg dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700"
                        placeholder="Venta">
                </label>
            </div>
            <div>
                <label class="block text-sm"><span class="text-gray-700 dark:text-gray-200">Stock mínimo</span>
            <input type="number" min="0" step="1" name="details[__INDEX__][min_stock]"
                        class="block w-full mt-1 px-3 py-2 text-sm border rounded-lg dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700"
                        placeholder="Opcional">
                </label>
            </div>
            <div class="flex gap-2">
                <button type="button"
                    class="remove-line px-3 py-2 text-sm rounded bg-red-600 text-white w-full">Quitar</button>
            </div>
        </div>
    </template>

    <script>
        (function() {
            const lines = document.getElementById('lines');
            const tpl = document.getElementById('line-template').innerHTML;
            const addBtn = document.getElementById('add-line');
            let idx = {{ is_array(old('details')) ? count(old('details')) : 0 }};

            function addLine(prefill = {}) {
                let html = tpl.replace(/__INDEX__/g, idx);
                html = html.replace(/\$\$INDEX\$\$/g, idx); // para los errores
                const wrapper = document.createElement('div');
                wrapper.innerHTML = html.trim();
                const node = wrapper.firstChild;
                lines.appendChild(node);
                if (prefill.quantity) node.querySelector(`[name="details[${idx}][quantity]"]`).value = prefill.quantity;
                if (prefill.unit_price) node.querySelector(`[name="details[${idx}][unit_price]"]`).value = prefill
                    .unit_price;
                if (prefill.sale_price) node.querySelector(`[name="details[${idx}][sale_price]"]`).value = prefill
                    .sale_price;
                if (prefill.color_id) node.querySelector(`[name="details[${idx}][color_id]"]`).value = prefill.color_id;
                if (prefill.size_id) node.querySelector(`[name="details[${idx}][size_id]"]`).value = prefill.size_id;
                if (prefill.min_stock) node.querySelector(`[name="details[${idx}][min_stock]"]`).value = prefill.min_stock;
                idx++;
            }
            // Delegación para botones quitar (funciona para líneas renderizadas por Blade y nuevas)
            lines.addEventListener('click', (e) => {
                const btn = e.target.closest('.remove-line');
                if (btn) {
                    const line = btn.closest('[data-line]');
                    if (line) line.remove();
                }
            });
            addBtn.addEventListener('click', () => addLine());
            // Agregar una línea vacía solo si no hay líneas previas por validación
            const hasOld = {{ is_array(old('details')) && count(old('details')) ? 'true' : 'false' }};
            if (!hasOld) {
                addLine();
            }
        })();
    </script>

    <div class="mt-6 flex gap-2">
        <a href="{{ route('purchases.index') }}" class="px-4 py-2 rounded bg-gray-200 text-gray-800">Cancelar</a>
        <button type="submit" class="px-4 py-2 rounded bg-purple-600 text-white">Guardar</button>
    </div>
</div>
