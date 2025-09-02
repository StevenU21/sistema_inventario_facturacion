<div class="px-4 py-3 mb-8 bg-white rounded-lg shadow-md dark:bg-gray-800">
    <h3 class="text-lg font-semibold mb-4 text-gray-700 dark:text-gray-200">Registrar Movimiento</h3>
    <div class="flex flex-col md:flex-row gap-4">
        <label class="block text-sm w-full">
            <span class="text-gray-700 dark:text-gray-400">Movement Type</span>
            <div class="relative text-gray-500 focus-within:text-purple-600 dark:focus-within:text-purple-400">
                <select name="movement_type" id="movement_type"
                    class="block w-full pl-10 mt-1 text-sm dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 form-select focus:border-purple-400 focus:shadow-outline-purple dark:focus:shadow-outline-gray">
                    <option value="">Seleccionar</option>
                    <option value="adjustment">Ajuste</option>
                    <option value="transfer">Transferencia de Almacén</option>
                </select>
                <div class="absolute inset-y-0 flex items-center ml-3 pointer-events-none">
                    <i class="fas fa-exchange-alt w-5 h-5"></i>
                </div>
            </div>
        </label>
    </div>
    <!-- Campos dinámicos -->
    <div id="movement_fields"></div>

    <!-- Botón enviar -->
    <div class="mt-6">
        <button type="submit"
            class="flex items-center px-4 py-2 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-purple-600 border border-transparent rounded-lg hover:bg-purple-700 focus:outline-none focus:shadow-outline-purple active:bg-purple-600">
            <i class="fas fa-paper-plane mr-2"></i> {{ isset($inventory) ? 'Actualizar' : 'Guardar' }}
        </button>
    </div>

    <x-inventory_movement :warehouses="$warehouses" />
