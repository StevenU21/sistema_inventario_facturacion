    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const movementType = document.getElementById('movement_type');
            const movementFields = document.getElementById('movement_fields');

            function renderFields(type) {
                let html = '';
                if (type === 'transfer') {
                    html += `
                    <div class="mt-4">
                        <label class="block text-sm w-full">
                            <span class="text-gray-700 dark:text-gray-400">Destination Warehouse</span>
                            <div class="relative text-gray-500 focus-within:text-purple-600 dark:focus-within:text-purple-400">
                                <select name="destination_warehouse_id" class="block w-full pl-10 mt-1 text-sm dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 form-select focus:border-purple-400 focus:shadow-outline-purple dark:focus:shadow-outline-gray">
                                    <option value="">Select warehouse</option>
                                    ${Object.entries(@json($warehouses)).map(([id, name]) => `<option value="${id}">${name}</option>`).join('')}
                                </select>
                                <div class="absolute inset-y-0 flex items-center ml-3 pointer-events-none">
                                    <i class="fas fa-warehouse w-5 h-5"></i>
                                </div>
                            </div>
                        </label>
                    </div>
                    `;
                }
                if (type === 'adjustment' || type === 'in') {
                    html += `
                    <div class="flex flex-col md:flex-row gap-4 mt-4">
                        <label class="block text-sm w-full">
                            <span class="text-gray-700 dark:text-gray-400">Stock</span>
                            <div class="relative text-gray-500 focus-within:text-purple-600 dark:focus-within:text-purple-400">
                                <input name="stock" type="number" min="0" class="block w-full pl-10 mt-1 text-sm dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 form-input focus:border-purple-400 focus:shadow-outline-purple dark:focus:shadow-outline-gray" placeholder="Stock..." />
                                <div class="absolute inset-y-0 flex items-center ml-3 pointer-events-none">
                                    <i class="fas fa-cubes w-5 h-5"></i>
                                </div>
                            </div>
                        </label>
                        <label class="block text-sm w-full">
                            <span class="text-gray-700 dark:text-gray-400">Min Stock</span>
                            <div class="relative text-gray-500 focus-within:text-purple-600 dark:focus-within:text-purple-400">
                                <input name="min_stock" type="number" min="0" class="block w-full pl-10 mt-1 text-sm dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 form-input focus:border-purple-400 focus:shadow-outline-purple dark:focus:shadow-outline-gray" placeholder="Min stock..." />
                                <div class="absolute inset-y-0 flex items-center ml-3 pointer-events-none">
                                    <i class="fas fa-exclamation-triangle w-5 h-5"></i>
                                </div>
                            </div>
                        </label>
                    </div>
                    <div class="flex flex-col md:flex-row gap-4 mt-4">
                        <label class="block text-sm w-full">
                            <span class="text-gray-700 dark:text-gray-400">Unit Price</span>
                            <div class="relative text-gray-500 focus-within:text-purple-600 dark:focus-within:text-purple-400">
                                <input name="unit_price" type="number" step="0.01" min="0" class="block w-full pl-10 mt-1 text-sm dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 form-input focus:border-purple-400 focus:shadow-outline-purple dark:focus:shadow-outline-gray" placeholder="Unit price..." />
                                <div class="absolute inset-y-0 flex items-center ml-3 pointer-events-none">
                                    <i class="fas fa-money-bill-wave w-5 h-5"></i>
                                </div>
                            </div>
                        </label>
                        <label class="block text-sm w-full">
                            <span class="text-gray-700 dark:text-gray-400">Sale Price</span>
                            <div class="relative text-gray-500 focus-within:text-purple-600 dark:focus-within:text-purple-400">
                                <input name="sale_price" type="number" step="0.01" min="0" class="block w-full pl-10 mt-1 text-sm dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 form-input focus:border-purple-400 focus:shadow-outline-purple dark:focus:shadow-outline-gray" placeholder="Sale price..." />
                                <div class="absolute inset-y-0 flex items-center ml-3 pointer-events-none">
                                    <i class="fas fa-dollar-sign w-5 h-5"></i>
                                </div>
                            </div>
                        </label>
                    </div>
                    `;
                }
                if (type === 'out') {
                    html += `
                    <div class="flex flex-col md:flex-row gap-4 mt-4">
                        <label class="block text-sm w-full">
                            <span class="text-gray-700 dark:text-gray-400">Quantity</span>
                            <div class="relative text-gray-500 focus-within:text-purple-600 dark:focus-within:text-purple-400">
                                <input type="number" name="quantity" min="1" class="block w-full pl-10 mt-1 text-sm dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 form-input focus:border-purple-400 focus:shadow-outline-purple dark:focus:shadow-outline-gray">
                                <div class="absolute inset-y-0 flex items-center ml-3 pointer-events-none">
                                    <i class="fas fa-sort-numeric-up w-5 h-5"></i>
                                </div>
                            </div>
                        </label>
                    </div>
                    `;
                }
                // Reference and notes for all types
                if (type) {
                    html += `
                    <div class="mt-4">
                        <label class="block text-sm w-full">
                            <span class="text-gray-700 dark:text-gray-400">Reference</span>
                            <div class="relative text-gray-500 focus-within:text-purple-600 dark:focus-within:text-purple-400">
                                <input type="text" name="reference" class="block w-full pl-10 mt-1 text-sm dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 form-input focus:border-purple-400 focus:shadow-outline-purple dark:focus:shadow-outline-gray">
                                <div class="absolute inset-y-0 flex items-center ml-3 pointer-events-none">
                                    <i class="fas fa-file-alt w-5 h-5"></i>
                                </div>
                            </div>
                        </label>
                    </div>
                    <div class="mt-4">
                        <label class="block text-sm w-full">
                            <span class="text-gray-700 dark:text-gray-400">Notes</span>
                            <div class="relative text-gray-500 focus-within:text-purple-600 dark:focus-within:text-purple-400">
                                <textarea name="notes" class="block w-full pl-10 mt-1 text-sm dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 form-textarea focus:border-purple-400 focus:shadow-outline-purple dark:focus:shadow-outline-gray" rows="2"></textarea>
                                <div class="absolute inset-y-0 left-0 flex items-center ml-3 pointer-events-none">
                                    <i class="fas fa-align-left w-5 h-5"></i>
                                </div>
                            </div>
                        </label>
                    </div>
                    `;
                }
                movementFields.innerHTML = html;
            }

            movementType.addEventListener('change', function() {
                renderFields(this.value);
            });
        });
    </script>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const movementType = document.getElementById('movement_type');
            const transferContainer = document.getElementById('transfer_warehouse_container');
            movementType.addEventListener('change', function() {
                if (this.value === 'transfer') {
                    transferContainer.style.display = '';
                } else {
                    transferContainer.style.display = 'none';
                }
            });
        });
    </script>
