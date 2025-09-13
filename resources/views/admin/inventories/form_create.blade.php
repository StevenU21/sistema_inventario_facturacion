<div class="px-4 py-3 mb-8 bg-white rounded-lg shadow-md dark:bg-gray-800">
    <!-- Buscar y seleccionar variante -->
    <div class="mb-4">
        <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-200 mb-3">Selecciona la variante</h3>
        <x-admin.inventories.variant-picker :colors="$colors ?? []" :sizes="$sizes ?? []" :categories="$categories ?? []" :brands="$brands ?? []"
            :entities="$entities ?? []" />
    </div>

    <!-- Bodega -->
    <div class="flex flex-col md:flex-row gap-4">
        <label class="block text-sm w-full">
            <span class="text-gray-700 dark:text-gray-400">Bodega</span>
            <div class="relative text-gray-500 focus-within:text-purple-600 dark:focus-within:text-purple-400">
                <select name="warehouse_id" required
                    class="block w-full min-w-[200px] pl-10 mt-1 text-sm dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 form-select focus:border-purple-400 focus:shadow-outline-purple dark:focus:shadow-outline-gray @error('warehouse_id') border-red-600 @enderror">
                    <option value="">Seleccione</option>
                    @foreach ($warehouses as $id => $name)
                        <option value="{{ $id }}" {{ old('warehouse_id') == $id ? 'selected' : '' }}>
                            {{ $name }}</option>
                    @endforeach
                </select>
                <div class="absolute inset-y-0 flex items-center ml-3 pointer-events-none">
                    <i class="fas fa-warehouse w-5 h-5"></i>
                </div>
            </div>
            @error('warehouse_id')
                <span class="text-xs text-red-600 dark:text-red-400">{{ $message }}</span>
            @enderror
        </label>
    </div>

    <!-- Stock y Stock mínimo -->
    <div class="flex flex-col md:flex-row gap-4 mt-4">
        <label class="block text-sm w-full">
            <span class="text-gray-700 dark:text-gray-400">Stock</span>
            <div class="relative text-gray-500 focus-within:text-purple-600 dark:focus-within:text-purple-400">
                <input type="number" name="stock" min="0" required value="{{ old('stock') }}"
                    class="block w-full min-w-[260px] pl-10 mt-1 text-sm dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 form-input focus:border-purple-400 focus:shadow-outline-purple dark:focus:shadow-outline-gray @error('stock') border-red-600 @enderror" />
                <div class="absolute inset-y-0 flex items-center ml-3 pointer-events-none">
                    <i class="fas fa-cubes w-5 h-5"></i>
                </div>
            </div>
            @error('stock')
                <span class="text-xs text-red-600 dark:text-red-400">{{ $message }}</span>
            @enderror
        </label>
        <label class="block text-sm w-full">
            <span class="text-gray-700 dark:text-gray-400">Stock mínimo</span>
            <div class="relative text-gray-500 focus-within:text-purple-600 dark:focus-within:text-purple-400">
                <input type="number" name="min_stock" min="0" required value="{{ old('min_stock') }}"
                    class="block w-full min-w-[260px] pl-10 mt-1 text-sm dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 form-input focus:border-purple-400 focus:shadow-outline-purple dark:focus:shadow-outline-gray @error('min_stock') border-red-600 @enderror" />
                <div class="absolute inset-y-0 flex items-center ml-3 pointer-events-none">
                    <i class="fas fa-exclamation-triangle w-5 h-5"></i>
                </div>
            </div>
            @error('min_stock')
                <span class="text-xs text-red-600 dark:text-red-400">{{ $message }}</span>
            @enderror
        </label>
    </div>

    <!-- Precios -->
    <div class="flex flex-col md:flex-row gap-4 mt-4">
        <label class="block text-sm w-full">
            <span class="text-gray-700 dark:text-gray-400">Precio compra</span>
            <div class="relative text-gray-500 focus-within:text-purple-600 dark:focus-within:text-purple-400">
                <input type="number" name="purchase_price" min="0" step="0.01" required
                    value="{{ old('purchase_price') }}"
                    class="block w-full min-w-[260px] pl-10 mt-1 text-sm dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 form-input focus:border-purple-400 focus:shadow-outline-purple dark:focus:shadow-outline-gray @error('purchase_price') border-red-600 @enderror" />
                <div class="absolute inset-y-0 flex items-center ml-3 pointer-events-none">
                    <i class="fas fa-money-bill-wave w-5 h-5"></i>
                </div>
            </div>
            @error('purchase_price')
                <span class="text-xs text-red-600 dark:text-red-400">{{ $message }}</span>
            @enderror
        </label>
        <label class="block text-sm w-full">
            <span class="text-gray-700 dark:text-gray-400">Precio venta</span>
            <div class="relative text-gray-500 focus-within:text-purple-600 dark:focus-within:text-purple-400">
                <input type="number" name="sale_price" min="0" step="0.01" required
                    value="{{ old('sale_price') }}"
                    class="block w-full min-w-[260px] pl-10 mt-1 text-sm dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 form-input focus:border-purple-400 focus:shadow-outline-purple dark:focus:shadow-outline-gray @error('sale_price') border-red-600 @enderror" />
                <div class="absolute inset-y-0 flex items-center ml-3 pointer-events-none">
                    <i class="fas fa-dollar-sign w-5 h-5"></i>
                </div>
            </div>
            @error('sale_price')
                <span class="text-xs text-red-600 dark:text-red-400">{{ $message }}</span>
            @enderror
        </label>
    </div>

    <!-- Botón enviar -->
    <div class="mt-6">
        <button type="submit"
            class="flex items-center px-4 py-2 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-purple-600 border border-transparent rounded-lg hover:bg-purple-700 focus:outline-none focus:shadow-outline-purple active:bg-purple-600">
            <i class="fas fa-paper-plane mr-2"></i> Guardar
        </button>
    </div>
</div>
