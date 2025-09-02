@if ($errors->any())
    <div class="mb-4">
        <ul class="text-sm text-red-600">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
<div class="flex flex-col gap-4 mt-4">
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
                class="block w-full pl-10 mt-1 text-sm text-gray-500 bg-gray-200 dark:text-gray-400 dark:bg-gray-800 dark:border-gray-600 form-input cursor-not-allowed"
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
                class="block w-full pl-10 mt-1 text-sm text-gray-500 bg-gray-200 dark:text-gray-400 dark:bg-gray-800 dark:border-gray-600 form-input cursor-not-allowed"
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
                class="block w-full pl-10 mt-1 text-sm text-gray-500 bg-gray-200 dark:text-gray-400 dark:bg-gray-800 dark:border-gray-600 form-input cursor-not-allowed"
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

        // Estilos visuales para inputs deshabilitados
        function setDisabledStyles(input, isDisabled) {
            if (isDisabled) {
                input.classList.add('bg-gray-200', 'text-gray-500', 'cursor-not-allowed');
            } else {
                input.classList.remove('bg-gray-200', 'text-gray-500', 'cursor-not-allowed');
            }
        }

        function toggleFields() {
            var reason = reasonSelect.value;
            // Por defecto, habilitar todos los inputs editables
            quantityInput.disabled = false;
            purchaseInput.disabled = false;
            saleInput.disabled = false;

            // Solo deshabilitar los que no corresponden a la razón
            if (['correction', 'physical_count', 'damage', 'theft'].includes(reason)) {
                purchaseInput.disabled = true;
                saleInput.disabled = true;
            } else if (reason === 'purchase_price') {
                quantityInput.disabled = true;
                saleInput.disabled = true;
            } else if (reason === 'sale_price') {
                quantityInput.disabled = true;
                purchaseInput.disabled = true;
            } else {
                // Si no hay razón, deshabilitar todos
                quantityInput.disabled = true;
                purchaseInput.disabled = true;
                saleInput.disabled = true;
            }
            // Aplicar estilos visuales
            setDisabledStyles(quantityInput, quantityInput.disabled);
            setDisabledStyles(purchaseInput, purchaseInput.disabled);
            setDisabledStyles(saleInput, saleInput.disabled);
        }
        reasonSelect.addEventListener('change', toggleFields);
        toggleFields(); // inicializa estado al cargar
    });
</script>
