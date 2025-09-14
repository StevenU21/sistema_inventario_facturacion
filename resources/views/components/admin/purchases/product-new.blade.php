@props([
    'entities' => [],
    'warehouses' => [],
    'methods' => [],
    'categories' => [],
    'brands' => [],
    'brandsByCategory' => [],
    'taxes' => [],
    'units' => [],
    'purchase' => null,
    'product' => null,
])

<fieldset x-ref="newFields" x-bind:disabled="$el.dataset.mode === 'existing'" {{ $attributes }}>


































































    <div class="rounded-lg border border-gray-200 dark:border-gray-700 p-4" x-data="{
        newPurchase: {
            entity_id: @js(old('new.entity_id', old('entity_id', $purchase->entity_id ?? ''))),
            warehouse_id: @js(old('new.warehouse_id', old('warehouse_id', $purchase->warehouse_id ?? ''))),
            payment_method_id: @js(old('new.payment_method_id', old('payment_method_id', $purchase->payment_method_id ?? ''))),
            reference: @js(old('new.reference', old('reference', $purchase->reference ?? ''))),
        },
        // Dependencias categoría -> marcas
        productCategoryId: @js(old('product.category_id', optional($product)->category_id)),
        productBrandId: @js(old('product.brand_id', optional($product)->brand_id)),
        brandsByCategory: @js($brandsByCategory ?? []),
    allBrands: @js($brands ?? []),
    brandsList: [],
        init() {
            this.refreshBrands();
        },
        refreshBrands() {
            const catId = this.productCategoryId?.toString() || '';
            const source = (catId && this.brandsByCategory && this.brandsByCategory[catId])
                ? this.brandsByCategory[catId]
                : this.allBrands;
            const obj = source || {};
            // Convertir objeto {id: name} a arreglo [{id, name}]
            this.brandsList = Object.entries(obj).map(([id, name]) => ({ id, name }));
            // Reset brand if it's not in the filtered list
            const bId = this.productBrandId?.toString() || '';
            if (!bId || !this.brandsList.some(o => o.id.toString() === bId)) {
                this.productBrandId = '';
            }
        }
    }">
        <!-- Fila 1: Nombre - Proveedor - Almacén -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <label class="block text-sm w-full">
                <span class="text-gray-700 dark:text-gray-200">Nombre del producto</span>
                <input type="text" name="product[name]" value="{{ old('product.name', optional($product)->name) }}"
                    x-bind:disabled="$el.closest('fieldset')?.dataset?.mode !== 'new'"
                    class="block w-full mt-1 px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 {{ $errors->has('product.name') ? 'border-red-600' : '' }}"
                    placeholder="Nombre del producto">
                @error('product.name')
                    <span class="text-xs text-red-600 dark:text-red-400">{{ $message }}</span>
                @enderror
            </label>
            <label class="block text-sm w-full">
                <span class="text-gray-700 dark:text-gray-200">Proveedor</span>
                <select name="new[entity_id]" x-model="newPurchase.entity_id"
                    x-bind:required="$el.closest('fieldset')?.dataset?.mode === 'new'"
                    x-bind:disabled="$el.closest('fieldset')?.dataset?.mode !== 'new'"
                    class="block w-full mt-1 px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 {{ $errors->has('new.entity_id') || $errors->has('entity_id') ? 'border-red-600' : '' }}">
                    <option value="">Seleccionar Proveedor</option>
                    @foreach ($entities ?? [] as $id => $name)
                        <option value="{{ $id }}"
                            {{ (string) old('new.entity_id', $purchase->entity_id ?? '') === (string) $id ? 'selected' : '' }}>
                            {{ $name }}</option>
                    @endforeach
                </select>
                @php($entityError = $errors->first('new.entity_id') ?: $errors->first('entity_id'))
                @if ($entityError)
                    <span class="text-xs text-red-600 dark:text-red-400">{{ $entityError }}</span>
                @endif
            </label>
            <label class="block text-sm w-full">
                <span class="text-gray-700 dark:text-gray-200">Almacén</span>
                <select name="new[warehouse_id]" x-model="newPurchase.warehouse_id"
                    x-bind:required="$el.closest('fieldset')?.dataset?.mode === 'new'"
                    x-bind:disabled="$el.closest('fieldset')?.dataset?.mode !== 'new'"
                    class="block w-full mt-1 px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 {{ $errors->has('new.warehouse_id') || $errors->has('warehouse_id') ? 'border-red-600' : '' }}">
                    <option value="">Seleccionar Almacén</option>
                    @foreach ($warehouses ?? [] as $id => $name)
                        <option value="{{ $id }}"
                            {{ (string) old('new.warehouse_id', $purchase->warehouse_id ?? '') === (string) $id ? 'selected' : '' }}>
                            {{ $name }}</option>
                    @endforeach
                </select>
                @php($warehouseError = $errors->first('new.warehouse_id') ?: $errors->first('warehouse_id'))
                @if ($warehouseError)
                    <span class="text-xs text-red-600 dark:text-red-400">{{ $warehouseError }}</span>
                @endif
            </label>
        </div>

        <!-- Fila 2: Categoría - Marca - Referencia -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
            <label class="block text-sm w-full">
                <span class="text-gray-700 dark:text-gray-200">Categoría</span>
                <select x-model="productCategoryId" @change="refreshBrands()"
                    x-bind:disabled="$el.closest('fieldset')?.dataset?.mode !== 'new'"
                    class="block w-full mt-1 px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 {{ $errors->has('product.category_id') ? 'border-red-600' : '' }}">
                    <option value="">Seleccionar Categoría</option>
                    @foreach ($categories ?? [] as $id => $name)
                        <option value="{{ $id }}"
                            {{ old('product.category_id', optional($product)->category_id) == $id ? 'selected' : '' }}>
                            {{ $name }}</option>
                    @endforeach
                </select>
                @error('product.category_id')
                    <span class="text-xs text-red-600 dark:text-red-400">{{ $message }}</span>
                @enderror
            </label>
            <label class="block text-sm w-full">
                <span class="text-gray-700 dark:text-gray-200">Marca</span>
                <select name="product[brand_id]" x-model="productBrandId"
                    x-bind:disabled="$el.closest('fieldset')?.dataset?.mode !== 'new'"
                    class="block w-full mt-1 px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 {{ $errors->has('product.brand_id') ? 'border-red-600' : '' }}">
                    <option value="">Seleccionar Marca</option>
                    <template x-for="opt in brandsList" :key="opt.id">
                        <option :value="opt.id" x-text="opt.name"></option>
                    </template>
                </select>
                @error('product.brand_id')
                    <span class="text-xs text-red-600 dark:text-red-400">{{ $message }}</span>
                @enderror
            </label>
            <label class="block text-sm w-full">
                <span class="text-gray-700 dark:text-gray-200">Referencia</span>
                <input type="text" name="new[reference]" x-model="newPurchase.reference"
                    x-bind:disabled="$el.closest('fieldset')?.dataset?.mode !== 'new'"
                    value="{{ old('new.reference', old('reference', $purchase->reference ?? '')) }}"
                    class="block w-full mt-1 px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 {{ $errors->has('new.reference') || $errors->has('reference') ? 'border-red-600' : '' }}"
                    placeholder="Opcional...">
                @error('new.reference')
                    <span class="text-xs text-red-600 dark:text-red-400">{{ $message }}</span>
                @enderror
                @error('reference')
                    <span class="text-xs text-red-600 dark:text-red-400">{{ $message }}</span>
                @enderror
            </label>
        </div>

        <!-- Fila 3: Método de pago - Impuesto - Unidad de Medida -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
            <label class="block text-sm w-full">
                <span class="text-gray-700 dark:text-gray-200">Método de pago</span>
                <select name="new[payment_method_id]" x-model="newPurchase.payment_method_id"
                    x-bind:required="$el.closest('fieldset')?.dataset?.mode === 'new'"
                    x-bind:disabled="$el.closest('fieldset')?.dataset?.mode !== 'new'"
                    class="block w-full mt-1 px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 {{ $errors->has('new.payment_method_id') || $errors->has('payment_method_id') ? 'border-red-600' : '' }}">
                    <option value="">Seleccionar Método de Pago</option>
                    @foreach ($methods ?? [] as $id => $name)
                        <option value="{{ $id }}"
                            {{ (string) old('new.payment_method_id', $purchase->payment_method_id ?? '') === (string) $id ? 'selected' : '' }}>
                            {{ $name }}</option>
                    @endforeach
                </select>
                @error('new.payment_method_id')
                    <span class="text-xs text-red-600 dark:text-red-400">{{ $message }}</span>
                @enderror
                @error('payment_method_id')
                    <span class="text-xs text-red-600 dark:text-red-400">{{ $message }}</span>
                @enderror
            </label>
            <label class="block text-sm w-full">
                <span class="text-gray-700 dark:text-gray-200">Impuesto</span>
                <select name="product[tax_id]"
                    x-bind:disabled="$el.closest('fieldset')?.dataset?.mode !== 'new'"
                    class="block w-full mt-1 px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 {{ $errors->has('product.tax_id') ? 'border-red-600' : '' }}">
                    <option value="">Seleccionar Impuesto</option>
                    @foreach ($taxes ?? [] as $id => $name)
                        <option value="{{ $id }}"
                            {{ old('product.tax_id', optional($product)->tax_id) == $id ? 'selected' : '' }}>
                            {{ $name }}</option>
                    @endforeach
                </select>
                @error('product.tax_id')
                    <span class="text-xs text-red-600 dark:text-red-400">{{ $message }}</span>
                @enderror
            </label>
            <label class="block text-sm w-full">
                <span class="text-gray-700 dark:text-gray-200">Unidad de medida</span>
                <select name="product[unit_measure_id]"
                    x-bind:disabled="$el.closest('fieldset')?.dataset?.mode !== 'new'"
                    class="block w-full mt-1 px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 {{ $errors->has('product.unit_measure_id') ? 'border-red-600' : '' }}">
                    <option value="">Seleccionar Unidad de Medida</option>
                    @foreach ($units ?? [] as $id => $name)
                        <option value="{{ $id }}"
                            {{ old('product.unit_measure_id', optional($product)->unit_measure_id) == $id ? 'selected' : '' }}>
                            {{ $name }}</option>
                    @endforeach
                </select>
                @error('product.unit_measure_id')
                    <span class="text-xs text-red-600 dark:text-red-400">{{ $message }}</span>
                @enderror
            </label>
        </div>

        <!-- Fila 4: Descripción -->
        <div class="mt-6">
            <label class="block text-sm w-full">
                <span class="text-gray-700 dark:text-gray-200">Descripción</span>
                <textarea name="product[description]" rows="3" placeholder="Opcional..."
                    x-bind:disabled="$el.closest('fieldset')?.dataset?.mode !== 'new'"
                    class="block w-full mt-1 px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 {{ $errors->has('product.description') ? 'border-red-600' : '' }}">{{ old('product.description', optional($product)->description) }}</textarea>
                @error('product.description')
                    <span class="text-xs text-red-600 dark:text-red-400">{{ $message }}</span>
                @enderror
            </label>
        </div>

        <!-- Campos ocultos canónicos cuando el modo es 'new' -->
    <template x-if="$el.closest('fieldset')?.dataset?.mode === 'new'">
            <div>
                <input type="hidden" name="entity_id" :value="newPurchase.entity_id">
                <input type="hidden" name="warehouse_id" :value="newPurchase.warehouse_id">
                <input type="hidden" name="payment_method_id" :value="newPurchase.payment_method_id">
                <input type="hidden" name="reference" :value="newPurchase.reference">
            </div>
        </template>
    </div>
</fieldset>
