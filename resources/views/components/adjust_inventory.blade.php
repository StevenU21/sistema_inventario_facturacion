<div x-data="{
    reason: @json(old('adjustment_reason')),
    get showInfo() { return ['correction', 'physical_count', 'damage', 'theft'].includes(this.reason); },
    isQtyReason() { return ['correction', 'physical_count', 'damage', 'theft'].includes(this.reason); },
    isPurchasePriceReason() { return this.reason === 'purchase_price'; },
    isSalePriceReason() { return this.reason === 'sale_price'; }
}">
    @if ($errors->any())
        <div x-data="{ show: true }" x-show="show"
            class="relative mb-4 text-sm font-medium text-red-700 bg-red-100 rounded-lg dark:bg-red-700 dark:text-red-100 px-4 py-3 flex items-center justify-between">
            <div>
                <i class="fas fa-exclamation-circle mr-2"></i>
                <span class="font-semibold">Error:</span>
                <ul class="list-disc ml-4">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            <button @click="show = false"
                class="text-red-700 dark:text-red-100 hover:text-red-900 dark:hover:text-red-300 focus:outline-none transition-colors duration-150 ml-2">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

    <div class="flex flex-col gap-4 mt-4">
        <div x-show="showInfo"
            class="relative mb-4 text-sm font-medium text-green-700 bg-green-100 rounded-lg dark:bg-green-700 dark:text-green-100 px-4 py-3 flex items-center justify-between">
            <div>
                <i class="fas fa-info-circle mr-2"></i>
                <span class="font-semibold">Nota:</span>
                <span>Las siguientes razones <span class="font-bold">sumarán</span> al stock: <span
                        class="font-bold">corrección</span>, <span class="font-bold">conteo físico</span>. </span>
                <span>Las siguientes razones <span class="font-bold">restarán</span> al stock: <span
                        class="font-bold">daño</span>, <span class="font-bold">robo</span>.</span>
            </div>
        </div>
        <label class="block text-sm w-full">
            <span class="text-gray-700 dark:text-gray-400">Razón del ajuste</span>
            <div class="relative text-gray-500 focus-within:text-purple-600 dark:focus-within:text-purple-400">
                <select name="adjustment_reason"
                    class="block w-full pl-10 mt-1 text-sm dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 form-select"
                    x-model="reason">
                    <option value="">Seleccionar razón</option>
                    <option value="correction">Corrección</option>
                    <option value="physical_count">Conteo físico</option>
                    <option value="damage">Daño</option>
                    <option value="theft">Robo</option>
                    <option value="purchase_price">Precio de compra</option>
                    <option value="sale_price">Precio de venta</option>
                </select>
                <div class="absolute inset-y-0 flex items-center ml-3 pointer-events-none"></div>
            </div>
            @error('adjustment_reason')
                <span class="text-xs text-red-600">{{ $message }}</span>
            @enderror
        </label>
    </div>

    <div class="flex flex-col md:flex-row gap-4 mt-4">
        <label class="block text-sm w-full md:w-1/2">
            <span class="text-gray-700 dark:text-gray-400">Stock actual (Solo lectura)</span>
            <div class="relative text-gray-500 focus-within:text-purple-600 dark:focus-within:text-purple-400">
                <input type="number" name="current_stock" value="{{ isset($inventory) ? $inventory->stock : '' }}"
                    class="block w-full pl-10 mt-1 text-sm dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 form-input" disabled readonly>
                <div class="absolute inset-y-0 flex items-center ml-3 pointer-events-none">
                    <i class="fas fa-cubes w-5 h-5"></i>
                </div>
            </div>
        </label>
        <label class="block text-sm w-full md:w-1/2">
            <span class="text-gray-700 dark:text-gray-400">Cantidad a ajustar</span>
            <div class="relative text-gray-500 focus-within:text-purple-600 dark:focus-within:text-purple-400">
                <input type="number" name="quantity" min="1" value="{{ old('quantity') }}" placeholder="Ej: 10"
                    class="block w-full pl-10 mt-1 text-sm dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 form-input" :readonly="!isQtyReason()" :disabled="!isQtyReason()">
                <div class="absolute inset-y-0 flex items-center ml-3 pointer-events-none">
                    <i class="fas fa-sort-numeric-up w-5 h-5"></i>
                </div>
            </div>
            @error('quantity')
                <span class="text-xs text-red-600">{{ $message }}</span>
            @enderror
        </label>
    </div>

    <div class="flex flex-col md:flex-row gap-4 mt-4">
        <label class="block text-sm w-full md:w-1/2">
            <span class="text-gray-700 dark:text-gray-400">Precio de compra actual (Solo lectura)</span>
            <div class="relative text-gray-500 focus-within:text-purple-600 dark:focus-within:text-purple-400">
                <input type="number" step="0.01" name="current_purchase_price"
                    value="{{ isset($inventory) ? $inventory->purchase_price : '' }}" class="block w-full pl-10 mt-1 text-sm dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 form-input" disabled readonly>
                <div class="absolute inset-y-0 flex items-center ml-3 pointer-events-none">
                    <i class="fas fa-money-bill-wave w-5 h-5"></i>
                </div>
            </div>
        </label>
        <label class="block text-sm w-full md:w-1/2">
            <span class="text-gray-700 dark:text-gray-400">Precio de compra</span>
            <div class="relative text-gray-500 focus-within:text-purple-600 dark:focus-within:text-purple-400">
                <input type="number" step="0.01" name="purchase_price" value="{{ old('purchase_price') }}"
                    placeholder="Ej: 100.00" class="block w-full pl-10 mt-1 text-sm dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 form-input" :readonly="!isPurchasePriceReason()" :disabled="!isPurchasePriceReason()">
                <div class="absolute inset-y-0 flex items-center ml-3 pointer-events-none">
                    <i class="fas fa-money-bill-wave w-5 h-5"></i>
                </div>
            </div>
            @error('purchase_price')
                <span class="text-xs text-red-600">{{ $message }}</span>
            @enderror
        </label>
    </div>

    <div class="flex flex-col md:flex-row gap-4 mt-4">
        <label class="block text-sm w-full md:w-1/2">
            <span class="text-gray-700 dark:text-gray-400">Precio de venta actual (Solo lectura)</span>
            <div class="relative text-gray-500 focus-within:text-purple-600 dark:focus-within:text-purple-400">
                <input type="number" step="0.01" name="current_sale_price"
                    value="{{ isset($inventory) ? $inventory->sale_price : '' }}" class="block w-full pl-10 mt-1 text-sm dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 form-input" disabled readonly>
                <div class="absolute inset-y-0 flex items-center ml-3 pointer-events-none">
                    <i class="fas fa-dollar-sign w-5 h-5"></i>
                </div>
            </div>
        </label>
        <label class="block text-sm w-full md:w-1/2">
            <span class="text-gray-700 dark:text-gray-400">Precio de venta</span>
            <div class="relative text-gray-500 focus-within:text-purple-600 dark:focus-within:text-purple-400">
                <input type="number" step="0.01" name="sale_price" value="{{ old('sale_price') }}"
                    placeholder="Ej: 150.00" class="block w-full pl-10 mt-1 text-sm dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 form-input" :readonly="!isSalePriceReason()" :disabled="!isSalePriceReason()">
                <div class="absolute inset-y-0 flex items-center ml-3 pointer-events-none">
                    <i class="fas fa-dollar-sign w-5 h-5"></i>
                </div>
            </div>
            @error('sale_price')
                <span class="text-xs text-red-600">{{ $message }}</span>
            @enderror
        </label>
    </div>
</div>
