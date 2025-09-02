@if ($errors->any())
    <div id="error-info" x-data="{ show: true }" x-show="show"
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
    <div id="ajuste-info" x-data="{ show: true }" x-show="show"
        class="relative mb-4 text-sm font-medium text-green-700 bg-green-100 rounded-lg dark:bg-green-700 dark:text-green-100 px-4 py-3 flex items-center justify-between hidden">
        <div>
            <i class="fas fa-info-circle mr-2"></i>
            <span class="font-semibold">Nota:</span>
            <span>Las siguientes razones <span class="font-bold">sumarán</span> al stock: <span
                    class="font-bold">corrección</span>, <span class="font-bold">conteo físico</span>. </span>
            <span>Las siguientes razones <span class="font-bold">restarán</span> al stock: <span
                    class="font-bold">daño</span>, <span class="font-bold">robo</span>.</span>
        </div>
        <button @click="show = false"
            class="text-green-700 dark:text-green-100 hover:text-green-900 dark:hover:text-green-300 focus:outline-none transition-colors duration-150 ml-2">
            <i class="fas fa-times"></i>
        </button>
    </div>
    <label class="block text-sm w-full">
        <span class="text-gray-700 dark:text-gray-400">Razón del ajuste</span>
        <div class="relative text-gray-500 focus-within:text-purple-600 dark:focus-within:text-purple-400">
            <select name="adjustment_reason"
                class="block w-full pl-10 mt-1 text-sm dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 form-select"
                id="adjustment-reason-select">
                <option value="">Seleccionar razón</option>
                <option value="correction" {{ old('adjustment_reason') == 'correction' ? 'selected' : '' }}>Corrección
                </option>
                <option value="physical_count" {{ old('adjustment_reason') == 'physical_count' ? 'selected' : '' }}>
                    Conteo físico</option>
                <option value="damage" {{ old('adjustment_reason') == 'damage' ? 'selected' : '' }}>Daño</option>
                <option value="theft" {{ old('adjustment_reason') == 'theft' ? 'selected' : '' }}>Robo</option>
                <option value="purchase_price" {{ old('adjustment_reason') == 'purchase_price' ? 'selected' : '' }}>
                    Actualizar precio de compra</option>
                <option value="sale_price" {{ old('adjustment_reason') == 'sale_price' ? 'selected' : '' }}>Actualizar
                    precio de venta</option>
            </select>
            <div class="absolute inset-y-0 flex items-center ml-3 pointer-events-none">
                <i class="fas fa-clipboard-list w-5 h-5"></i>
            </div>
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
                class="block w-full pl-10 mt-1 text-sm dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 form-input"
                disabled readonly>
            <div class="absolute inset-y-0 flex items-center ml-3 pointer-events-none">
                <i class="fas fa-boxes w-5 h-5"></i>
            </div>
        </div>
    </label>
    <label class="block text-sm w-full md:w-1/2">
        <span class="text-gray-700 dark:text-gray-400">Cantidad a ajustar</span>
        <div class="relative text-gray-500 focus-within:text-purple-600 dark:focus-within:text-purple-400">
            <input type="number" name="quantity" min="1" value="{{ old('quantity') }}"
                class="block w-full pl-10 mt-1 text-sm dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 form-input"
                placeholder="Ej: 10" id="quantity-input">
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
                value="{{ isset($inventory) ? $inventory->purchase_price : '' }}"
                class="block w-full pl-10 mt-1 text-sm dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 form-input"
                disabled readonly>
            <div class="absolute inset-y-0 flex items-center ml-3 pointer-events-none">
                <i class="fas fa-dollar-sign w-5 h-5"></i>
            </div>
        </div>
    </label>
    <label class="block text-sm w-full md:w-1/2">
        <span class="text-gray-700 dark:text-gray-400">Precio de compra</span>
        <div class="relative text-gray-500 focus-within:text-purple-600 dark:focus-within:text-purple-400">
            <input type="number" step="0.01" name="purchase_price" value="{{ old('purchase_price') }}"
                class="block w-full pl-10 mt-1 text-sm dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 form-input"
                placeholder="Ej: 100.00">
            <div class="absolute inset-y-0 flex items-center ml-3 pointer-events-none">
                <i class="fas fa-dollar-sign w-5 h-5"></i>
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
                value="{{ isset($inventory) ? $inventory->sale_price : '' }}"
                class="block w-full pl-10 mt-1 text-sm dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 form-input"
                disabled readonly>
            <div class="absolute inset-y-0 flex items-center ml-3 pointer-events-none">
                <i class="fas fa-dollar-sign w-5 h-5"></i>
            </div>
        </div>
    </label>
    <label class="block text-sm w-full md:w-1/2">
        <span class="text-gray-700 dark:text-gray-400">Precio de venta</span>
        <div class="relative text-gray-500 focus-within:text-purple-600 dark:focus-within:text-purple-400">
            <input type="number" step="0.01" name="sale_price" value="{{ old('sale_price') }}"
                class="block w-full pl-10 mt-1 text-sm dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 form-input"
                placeholder="Ej: 150.00">
            <div class="absolute inset-y-0 flex items-center ml-3 pointer-events-none">
                <i class="fas fa-dollar-sign w-5 h-5"></i>
            </div>
        </div>
        @error('sale_price')
            <span class="text-xs text-red-600">{{ $message }}</span>
        @enderror
    </label>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var reasonSelect = document.getElementById('adjustment-reason-select');
        var quantityInput = document.getElementById('quantity-input');
        var purchaseInput = document.querySelector('input[name="purchase_price"]');
        var saleInput = document.querySelector('input[name="sale_price"]');
        var infoDiv = document.getElementById('ajuste-info');

        function toggleFields() {
            var reason = reasonSelect.value;
            // Si no hay razón seleccionada, deshabilitar todos
            if (!reason) {
                quantityInput.disabled = false;
                purchaseInput.disabled = false;
                saleInput.disabled = false;
                quantityInput.value = '';
                purchaseInput.value = '';
                saleInput.value = '';
                infoDiv.classList.add('hidden');
                infoDiv.style.display = 'none';
                quantityInput.readOnly = true;
                purchaseInput.readOnly = true;
                saleInput.readOnly = true;
                return;
            }
            // Por defecto, deshabilitar todos
            quantityInput.disabled = true;
            purchaseInput.disabled = true;
            saleInput.disabled = true;
            quantityInput.readOnly = true;
            purchaseInput.readOnly = true;
            saleInput.readOnly = true;

            // Mostrar/ocultar info solo para motivos de cantidad
            if (["correction", "physical_count", "damage", "theft"].includes(reason)) {
                infoDiv.classList.remove('hidden');
                infoDiv.style.display = '';
                quantityInput.disabled = false;
                quantityInput.readOnly = false;
            } else {
                infoDiv.classList.add('hidden');
                infoDiv.style.display = 'none';
            }
            if (reason === "purchase_price") {
                purchaseInput.disabled = false;
                purchaseInput.readOnly = false;
            } else if (reason === "sale_price") {
                saleInput.disabled = false;
                saleInput.readOnly = false;
            }
        }
        reasonSelect.addEventListener('change', toggleFields);
        toggleFields(); // inicializa estado al cargar
    });
</script>
