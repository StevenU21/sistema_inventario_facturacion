@if ($errors->any())
    <div class="mb-4">
        <ul class="text-sm text-red-600">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
<div class="flex flex-col md:flex-row gap-4 mt-4">
    <label class="block text-sm w-full md:w-1/2">
        <span class="text-gray-700 dark:text-gray-400">Bodega actual (Solo lectura)</span>
        <div class="relative text-gray-500 focus-within:text-purple-600 dark:focus-within:text-purple-400">
            <select name="current_warehouse_id"
                class="block w-full pl-10 mt-1 text-sm dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 form-select"
                disabled readonly>
                @foreach ($warehouses as $id => $name)
                    <option value="{{ $id }}"
                        {{ isset($inventory) && $inventory->warehouse_id == $id ? 'selected' : '' }}>
                        {{ $name }}</option>
                @endforeach
            </select>
            <div class="absolute inset-y-0 flex items-center ml-3 pointer-events-none">
                <i class="fas fa-warehouse w-5 h-5"></i>
            </div>
        </div>
    </label>
    <label class="block text-sm w-full md:w-1/2">
        <span class="text-gray-700 dark:text-gray-400">Bodega de destino</span>
        <div class="relative text-gray-500 focus-within:text-purple-600 dark:focus-within:text-purple-400">
            <select name="destination_warehouse_id"
                class="block w-full pl-10 mt-1 text-sm dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 form-select">
                <option value="">Seleccionar almacén</option>
                @foreach ($warehouses as $id => $name)
                    <option value="{{ $id }}"
                        {{ old('destination_warehouse_id', isset($inventory) ? $inventory->warehouse_id : '') == $id ? 'selected' : '' }}>
                        {{ $name }}</option>
                @endforeach
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
        <span class="text-gray-700 dark:text-gray-400">Cantidad a transferir <span
                class="text-xs text-gray-500">(opcional, si se omite se transfiere todo)</span></span>
        <div class="relative text-gray-500 focus-within:text-purple-600 dark:focus-within:text-purple-400">
            <input type="number" name="quantity" value="{{ old('quantity') }}"
                class="block w-full pl-10 mt-1 text-sm dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 form-input"
                placeholder="Ej: 10">
            <div class="absolute inset-y-0 flex items-center ml-3 pointer-events-none">
                <i class="fas fa-boxes w-5 h-5"></i>
            </div>
        </div>
        @error('quantity')
            <span class="text-xs text-red-600">{{ $message }}</span>
        @enderror
    </label>
</div>
