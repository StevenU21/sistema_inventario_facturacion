<div class="px-4 py-3 mb-8 bg-white rounded-lg shadow-md dark:bg-gray-800">
    <div class="flex flex-col md:flex-row gap-4">
        <label class="block text-sm w-full">
            <span class="text-gray-700 dark:text-gray-400">Producto</span>
            <div class="relative text-gray-500 focus-within:text-purple-600 dark:focus-within:text-purple-400">
                <select name="product_id"
                    class="block w-full pl-10 mt-1 text-sm dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 form-select focus:border-purple-400 focus:shadow-outline-purple dark:focus:shadow-outline-gray @error('product_id') border-red-600 @enderror"
                    required>
                    <option value="">Seleccione</option>
                    @foreach (App\Models\Product::all() as $product)
                        <option value="{{ $product->id }}" {{ old('product_id', $inventory->product_id ?? '') == $product->id ? 'selected' : '' }}>{{ $product->name }}</option>
                    @endforeach
                </select>
                <div class="absolute inset-y-0 flex items-center ml-3 pointer-events-none">
                    <i class="fas fa-box w-5 h-5"></i>
                </div>
            </div>
            @error('product_id')
                <span class="text-xs text-red-600 dark:text-red-400">{{ $message }}</span>
            @enderror
        </label>
        <label class="block text-sm w-full">
            <span class="text-gray-700 dark:text-gray-400">Almacén</span>
            <div class="relative text-gray-500 focus-within:text-purple-600 dark:focus-within:text-purple-400">
                <select name="warehouse_id"
                    class="block w-full pl-10 mt-1 text-sm dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 form-select focus:border-purple-400 focus:shadow-outline-purple dark:focus:shadow-outline-gray @error('warehouse_id') border-red-600 @enderror"
                    required>
                    <option value="">Seleccione</option>
                    @foreach (App\Models\Warehouse::all() as $warehouse)
                        <option value="{{ $warehouse->id }}" {{ old('warehouse_id', $inventory->warehouse_id ?? '') == $warehouse->id ? 'selected' : '' }}>{{ $warehouse->name }}</option>
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
    <div class="flex flex-col md:flex-row gap-4 mt-4">
        <label class="block text-sm w-full">
            <span class="text-gray-700 dark:text-gray-400">Stock</span>
            <div class="relative text-gray-500 focus-within:text-purple-600 dark:focus-within:text-purple-400">
                <input name="stock" type="number" min="0"
                    class="block w-full pl-10 mt-1 text-sm dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 form-input focus:border-purple-400 focus:shadow-outline-purple dark:focus:shadow-outline-gray @error('stock') border-red-600 @enderror"
                    placeholder="Stock..." value="{{ old('stock', $inventory->stock ?? '') }}" required />
                <div class="absolute inset-y-0 flex items-center ml-3 pointer-events-none">
                    <i class="fas fa-cubes w-5 h-5"></i>
                </div>
            </div>
            @error('stock')
                <span class="text-xs text-red-600 dark:text-red-400">{{ $message }}</span>
            @enderror
        </label>
        <label class="block text-sm w-full">
            <span class="text-gray-700 dark:text-gray-400">Stock Mínimo</span>
            <div class="relative text-gray-500 focus-within:text-purple-600 dark:focus-within:text-purple-400">
                <input name="min_stock" type="number" min="0"
                    class="block w-full pl-10 mt-1 text-sm dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 form-input focus:border-purple-400 focus:shadow-outline-purple dark:focus:shadow-outline-gray @error('min_stock') border-red-600 @enderror"
                    placeholder="Stock mínimo..." value="{{ old('min_stock', $inventory->min_stock ?? '') }}" required />
                <div class="absolute inset-y-0 flex items-center ml-3 pointer-events-none">
                    <i class="fas fa-exclamation-triangle w-5 h-5"></i>
                </div>
            </div>
            @error('min_stock')
                <span class="text-xs text-red-600 dark:text-red-400">{{ $message }}</span>
            @enderror
        </label>
    </div>
    <div class="flex flex-col md:flex-row gap-4 mt-4">
        <label class="block text-sm w-full">
            <span class="text-gray-700 dark:text-gray-400">Precio Compra</span>
            <div class="relative text-gray-500 focus-within:text-purple-600 dark:focus-within:text-purple-400">
                <input name="purchase_price" type="number" step="0.01" min="0"
                    class="block w-full pl-10 mt-1 text-sm dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 form-input focus:border-purple-400 focus:shadow-outline-purple dark:focus:shadow-outline-gray @error('purchase_price') border-red-600 @enderror"
                    placeholder="Precio compra..." value="{{ old('purchase_price', $inventory->purchase_price ?? '') }}" required />
                <div class="absolute inset-y-0 flex items-center ml-3 pointer-events-none">
                    <i class="fas fa-money-bill-wave w-5 h-5"></i>
                </div>
            </div>
            @error('purchase_price')
                <span class="text-xs text-red-600 dark:text-red-400">{{ $message }}</span>
            @enderror
        </label>
        <label class="block text-sm w-full">
            <span class="text-gray-700 dark:text-gray-400">Precio Venta</span>
            <div class="relative text-gray-500 focus-within:text-purple-600 dark:focus-within:text-purple-400">
                <input name="sale_price" type="number" step="0.01" min="0"
                    class="block w-full pl-10 mt-1 text-sm dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 form-input focus:border-purple-400 focus:shadow-outline-purple dark:focus:shadow-outline-gray @error('sale_price') border-red-600 @enderror"
                    placeholder="Precio venta..." value="{{ old('sale_price', $inventory->sale_price ?? '') }}" required />
                <div class="absolute inset-y-0 flex items-center ml-3 pointer-events-none">
                    <i class="fas fa-dollar-sign w-5 h-5"></i>
                </div>
            </div>
            @error('sale_price')
                <span class="text-xs text-red-600 dark:text-red-400">{{ $message }}</span>
            @enderror
        </label>
    </div>
    <div class="mt-6">
        <button type="submit"
            class="flex items-center px-4 py-2 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-purple-600 border border-transparent rounded-lg hover:bg-purple-700 focus:outline-none focus:shadow-outline-purple active:bg-purple-600">
            <i class="fas fa-paper-plane mr-2"></i> {{ isset($inventory) ? 'Actualizar' : 'Guardar' }}
        </button>
    </div>
</div>
