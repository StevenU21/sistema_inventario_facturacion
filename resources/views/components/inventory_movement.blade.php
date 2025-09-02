<script>
    document.addEventListener('DOMContentLoaded', function() {
        const movementType = document.getElementById('movement_type');
        const movementFields = document.getElementById('movement_fields');

        function renderTransferFields() {
            // Blade variables para old() y almacén actual
            const oldWarehouse = `{{ old('destination_warehouse_id', isset($inventory) ? $inventory->warehouse_id : '') }}`;
            const oldQuantity = `{{ old('quantity') }}`;
            const oldUnitPrice = `{{ old('unit_price') }}`;
            const oldReference = `{{ old('reference') }}`;
            let html = `
                <div class="mt-4">
                    <label class="block text-sm w-full">
                        <span class="text-gray-700 dark:text-gray-400">Almacén de destino</span>
                        <div class="relative text-gray-500 focus-within:text-purple-600 dark:focus-within:text-purple-400">
                            <select name="destination_warehouse_id" class="block w-full pl-10 mt-1 text-sm dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 form-select focus:border-purple-400 focus:shadow-outline-purple dark:focus:shadow-outline-gray">
                                <option value="">Seleccionar almacén</option>
                                ${Object.entries(@json($warehouses)).map(([id, name]) => `<option value="${id}" ${oldWarehouse == id ? 'selected' : ''}>${name}</option>`).join('')}
                            </select>
                            <div class="absolute inset-y-0 flex items-center ml-3 pointer-events-none">
                                <i class="fas fa-warehouse w-5 h-5"></i>
                            </div>
                        </div>
                        @error('destination_warehouse_id')
                            <span class="text-xs text-red-600">{{ $message }}</span>
                        @enderror
                    </label>
                </div>
                <div class="flex flex-col md:flex-row gap-4 mt-4">
                    <label class="block text-sm w-full">
                        <span class="text-gray-700 dark:text-gray-400">Cantidad a transferir <span class="text-xs text-gray-500">(opcional, si se omite se transfiere todo)</span></span>
                        <div class="relative text-gray-500 focus-within:text-purple-600 dark:focus-within:text-purple-400">
                            <input type="number" name="quantity" min="1" value="${oldQuantity}" class="block w-full pl-10 mt-1 text-sm dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 form-input focus:border-purple-400 focus:shadow-outline-purple dark:focus:shadow-outline-gray" placeholder="Ej: 10">
                            <div class="absolute inset-y-0 flex items-center ml-3 pointer-events-none">
                                <i class="fas fa-sort-numeric-up w-5 h-5"></i>
                            </div>
                        </div>
                        @error('quantity')
                            <span class="text-xs text-red-600">{{ $message }}</span>
                        @enderror
                    </label>
                    <label class="block text-sm w-full">
                        <span class="text-gray-700 dark:text-gray-400">Precio unitario <span class="text-xs text-gray-500">(opcional)</span></span>
                        <div class="relative text-gray-500 focus-within:text-purple-600 dark:focus-within:text-purple-400">
                            <input name="unit_price" type="number" step="0.01" min="0" value="${oldUnitPrice}" class="block w-full pl-10 mt-1 text-sm dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 form-input focus:border-purple-400 focus:shadow-outline-purple dark:focus:shadow-outline-gray" placeholder="Ej: 5.00" />
                            <div class="absolute inset-y-0 flex items-center ml-3 pointer-events-none">
                                <i class="fas fa-money-bill-wave w-5 h-5"></i>
                            </div>
                        </div>
                        @error('unit_price')
                            <span class="text-xs text-red-600">{{ $message }}</span>
                        @enderror
                    </label>
                </div>
                <div class="mt-4">
                    <label class="block text-sm w-full">
                        <span class="text-gray-700 dark:text-gray-400">Referencia <span class="text-xs text-gray-500">(opcional)</span></span>
                        <div class="relative text-gray-500 focus-within:text-purple-600 dark:focus-within:text-purple-400">
                            <input type="text" name="reference" value="${oldReference}" class="block w-full pl-10 mt-1 text-sm dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 form-input focus:border-purple-400 focus:shadow-outline-purple dark:focus:shadow-outline-gray" placeholder="Ej: Traslado por inventario">
                            <div class="absolute inset-y-0 flex items-center ml-3 pointer-events-none">
                                <i class="fas fa-file-alt w-5 h-5"></i>
                            </div>
                        </div>
                        @error('reference')
                            <span class="text-xs text-red-600">{{ $message }}</span>
                        @enderror
                    </label>
                </div>
            `;
            movementFields.innerHTML = html;
        }

        movementType.addEventListener('change', function() {
            if (this.value === 'transfer') {
                renderTransferFields();
            } else {
                movementFields.innerHTML = '';
            }
        });
    });
</script>
