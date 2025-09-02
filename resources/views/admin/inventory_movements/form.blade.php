<div class="px-4 py-3 mb-8 bg-white rounded-lg shadow-md dark:bg-gray-800">
    <!-- Producto y Almacén -->
    <div class="mb-4">
        <label class="block text-sm w-full">
            <span class="text-gray-700 dark:text-gray-400">Producto</span>
            <div class="relative text-gray-500 focus-within:text-purple-600 dark:focus-within:text-purple-400">
                <input type="text"
                    class="block w-full pl-10 mt-1 text-sm dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 form-input focus:border-purple-400 focus:shadow-outline-purple dark:focus:shadow-outline-gray"
                    value="{{ $inventory->product->name ?? '-' }}" disabled>
                <div class="absolute inset-y-0 flex items-center ml-3 pointer-events-none">
                    <i class="fas fa-box w-5 h-5"></i>
                </div>
            </div>
        </label>
    </div>
    <div class="mb-4">
        <label class="block text-sm w-full">
            <span class="text-gray-700 dark:text-gray-400">Almacén</span>
            <div class="relative text-gray-500 focus-within:text-purple-600 dark:focus-within:text-purple-400">
                <input type="text"
                    class="block w-full pl-10 mt-1 text-sm dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 form-input focus:border-purple-400 focus:shadow-outline-purple dark:focus:shadow-outline-gray"
                    value="{{ $inventory->warehouse->name ?? '-' }}" disabled>
                <div class="absolute inset-y-0 flex items-center ml-3 pointer-events-none">
                    <i class="fas fa-warehouse w-5 h-5"></i>
                </div>
            </div>
        </label>
    </div>
    <input type="hidden" name="inventory_id" value="{{ $inventory->id }}">

    <!-- Tipo de Movimiento, Cantidad, Precio Unitario -->
    <div class="flex flex-col md:flex-row gap-4 mt-4">
        <label class="block text-sm w-full">
            <span class="text-gray-700 dark:text-gray-400">Tipo de Movimiento</span>
            <div class="relative text-gray-500 focus-within:text-purple-600 dark:focus-within:text-purple-400">
                <select name="type"
                    class="block w-full pl-10 mt-1 text-sm dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 form-select focus:border-purple-400 focus:shadow-outline-purple dark:focus:shadow-outline-gray"
                    required>
                    <option value="entrada">Entrada</option>
                    <option value="salida">Salida</option>
                    <option value="ajuste">Ajuste</option>
                    <option value="devolucion">Devolución</option>
                </select>
                <div class="absolute inset-y-0 flex items-center ml-3 pointer-events-none">
                    <i class="fas fa-exchange-alt w-5 h-5"></i>
                </div>
            </div>
        </label>
        <label class="block text-sm w-full">
            <span class="text-gray-700 dark:text-gray-400">Cantidad</span>
            <div class="relative text-gray-500 focus-within:text-purple-600 dark:focus-within:text-purple-400">
                <input type="number" name="quantity"
                    class="block w-full pl-10 mt-1 text-sm dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 form-input focus:border-purple-400 focus:shadow-outline-purple dark:focus:shadow-outline-gray"
                    min="1" required>
                <div class="absolute inset-y-0 flex items-center ml-3 pointer-events-none">
                    <i class="fas fa-sort-numeric-up w-5 h-5"></i>
                </div>
            </div>
        </label>
        <label class="block text-sm w-full">
            <span class="text-gray-700 dark:text-gray-400">Precio Unitario</span>
            <div class="relative text-gray-500 focus-within:text-purple-600 dark:focus-within:text-purple-400">
                <input type="number" name="unit_price"
                    class="block w-full pl-10 mt-1 text-sm dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 form-input focus:border-purple-400 focus:shadow-outline-purple dark:focus:shadow-outline-gray"
                    min="0" step="0.01">
                <div class="absolute inset-y-0 flex items-center ml-3 pointer-events-none">
                    <i class="fas fa-dollar-sign w-5 h-5"></i>
                </div>
            </div>
        </label>
    </div>

    <!-- Referencia -->
    <div class="mt-4">
        <label class="block text-sm w-full">
            <span class="text-gray-700 dark:text-gray-400">Referencia</span>
            <div class="relative text-gray-500 focus-within:text-purple-600 dark:focus-within:text-purple-400">
                <input type="text" name="reference"
                    class="block w-full pl-10 mt-1 text-sm dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 form-input focus:border-purple-400 focus:shadow-outline-purple dark:focus:shadow-outline-gray">
                <div class="absolute inset-y-0 flex items-center ml-3 pointer-events-none">
                    <i class="fas fa-file-alt w-5 h-5"></i>
                </div>
            </div>
        </label>
    </div>

    <!-- Notas -->
    <div class="mt-4">
        <label class="block text-sm w-full">
            <span class="text-gray-700 dark:text-gray-400">Notas</span>
            <div class="relative text-gray-500 focus-within:text-purple-600 dark:focus-within:text-purple-400">
                <textarea name="notes"
                    class="block w-full pl-10 mt-1 text-sm dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 form-textarea focus:border-purple-400 focus:shadow-outline-purple dark:focus:shadow-outline-gray"
                    rows="2"></textarea>
                <div class="absolute inset-y-0 left-0 flex items-center ml-3 pointer-events-none">
                    <i class="fas fa-align-left w-5 h-5"></i>
                </div>
            </div>
        </label>
    </div>

    <!-- Submit Button -->
    <div class="mt-6">
        <button type="submit"
            class="flex items-center px-4 py-2 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-purple-600 border border-transparent rounded-lg hover:bg-purple-700 focus:outline-none focus:shadow-outline-purple active:bg-purple-600">
            <i class="fas fa-paper-plane mr-2"></i> Registrar Movimiento
        </button>
    </div>
</div>
