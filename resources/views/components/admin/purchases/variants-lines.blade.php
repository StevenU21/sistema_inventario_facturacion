@props([
    'colors' => [],
    'sizes' => [],
    'oldDetails' => null,
    'prefillDetails' => null,
])
@php
    $oldDetails = $oldDetails ?? old('details');
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
                        <label class="block text-sm"><span class="text-gray-700 dark:text-gray-200">Cantidad</span>
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
                        <label class="block text-sm"><span class="text-gray-700 dark:text-gray-200">Precio unitario</span>
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
                        <label class="block text-sm"><span class="text-gray-700 dark:text-gray-200">Precio venta</span>
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
                        <label class="block text-sm"><span class="text-gray-700 dark:text-gray-200">Stock mínimo</span>
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
                    if (prefill.quantity) {
                        const el = node.querySelector(`[name="details[${this.idx}][quantity]"]`);
                        if (el) el.value = prefill.quantity;
                    }
                    if (prefill.unit_price) {
                        const el = node.querySelector(`[name="details[${this.idx}][unit_price]"]`);
                        if (el) el.value = prefill.unit_price;
                    }
                    if (prefill.sale_price) {
                        const el = node.querySelector(`[name="details[${this.idx}][sale_price]"]`);
                        if (el) el.value = prefill.sale_price;
                    }
                    if (prefill.color_id) {
                        const el = node.querySelector(`[name="details[${this.idx}][color_id]"]`);
                        if (el) el.value = prefill.color_id;
                    }
                    if (prefill.size_id) {
                        const el = node.querySelector(`[name="details[${this.idx}][size_id]"]`);
                        if (el) el.value = prefill.size_id;
                    }
                    if (prefill.min_stock) {
                        const el = node.querySelector(`[name="details[${this.idx}][min_stock]"]`);
                        if (el) el.value = prefill.min_stock;
                    }

                    this.idx++;
                }
            }
        }
    </script>
</div>
