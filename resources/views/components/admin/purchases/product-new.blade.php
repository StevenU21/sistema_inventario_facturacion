@props([
    'entities' => [],
    'warehouses' => [],
    'methods' => [],
    'categories' => [],
    'brands' => [],
    'taxes' => [],
    'units' => [],
    'purchase' => null,
    'product' => null,
])

<fieldset x-ref="newFields" x-bind:disabled="mode === 'existing'" {{ $attributes }}>
    <!-- Datos de la compra (para guardar) -->
    <div class="rounded-lg border border-gray-200 dark:border-gray-700 p-4 mb-4">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <label class="block text-sm w-full">
                <span class="text-gray-700 dark:text-gray-200">Proveedor</span>
                <select name="entity_id" required
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
                <select name="warehouse_id" required
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

    <div class="rounded-lg border border-gray-200 dark:border-gray-700 p-4">
        <!-- Fila 1: Nombre -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <label class="block text-sm w-full">
                <span class="text-gray-700 dark:text-gray-200">Nombre del producto</span>
                <input type="text" name="product[name]"
                    value="{{ old('product.name', optional($product)->name) }}"
                    class="block w-full mt-1 px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700"
                    placeholder="Nombre del producto">
            </label>
        </div>

        <!-- Fila 2: Categoría - Marca -->
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

            <!-- La referencia está arriba como dato de la compra -->
        </div>

        <!-- Descripción -->
        <div class="mt-4">
            <label class="block text-sm w-full">
                <span class="text-gray-700 dark:text-gray-200">Descripción</span>
                <textarea name="product[description]" rows="3" placeholder="Opcional..."
                    class="block w-full mt-1 px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700">{{ old('product.description', optional($product)->description) }}</textarea>
            </label>
        </div>
    </div>

    <!-- Impuesto y unidad de medida -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
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
</fieldset>
