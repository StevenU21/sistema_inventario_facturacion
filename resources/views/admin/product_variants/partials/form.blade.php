<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
  <label class="block text-sm w-full">
    <span class="text-gray-700 dark:text-gray-400">Producto</span>
    <div class="relative text-gray-500 focus-within:text-purple-600 dark:focus-within:text-purple-400">
      <select name="product_id" class="block w-full pl-10 mt-1 text-sm dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 form-select focus:border-purple-400 focus:shadow-outline-purple dark:focus:shadow-outline-gray" required
        @if(isset($alpine) && $alpine) x-model="editVariant.product_id" @endif>
        <option value="">Seleccione</option>
        @foreach ($products as $id => $name)
          <option value="{{ $id }}" @if(!isset($alpine) || !$alpine) {{ old('product_id') == $id ? 'selected' : '' }} @endif>{{ $name }}</option>
        @endforeach
      </select>
      <div class="absolute inset-y-0 flex items-center ml-3 pointer-events-none">
        <i class="fas fa-box"></i>
      </div>
    </div>
    @error('product_id')<span class="text-xs text-red-600 dark:text-red-400">{{ $message }}</span>@enderror
  </label>

  <label class="block text-sm w-full">
    <span class="text-gray-700 dark:text-gray-400">SKU</span>
    <div class="relative text-gray-500 focus-within:text-purple-600 dark:focus-within:text-purple-400">
      <input name="sku" type="text" class="block w-full pl-10 mt-1 text-sm dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 form-input focus:border-purple-400 focus:shadow-outline-purple dark:focus:shadow-outline-gray @error('sku') border-red-600 @enderror"
             placeholder="SKU..." @if(isset($alpine) && $alpine) x-model="editVariant.sku" :value="editVariant.sku" @else value="{{ old('sku') }}" @endif />
      <div class="absolute inset-y-0 flex items-center ml-3 pointer-events-none">
        <i class="fas fa-barcode"></i>
      </div>
    </div>
    @error('sku')<span class="text-xs text-red-600 dark:text-red-400">{{ $message }}</span>@enderror
  </label>

  <label class="block text-sm w-full">
    <span class="text-gray-700 dark:text-gray-400">Código de Barras</span>
    <div class="relative text-gray-500 focus-within:text-purple-600 dark:focus-within:text-purple-400">
      <input name="barcode" type="text" class="block w-full pl-10 mt-1 text-sm dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 form-input focus:border-purple-400 focus:shadow-outline-purple dark:focus:shadow-outline-gray @error('barcode') border-red-600 @enderror"
             placeholder="Código..." @if(isset($alpine) && $alpine) x-model="editVariant.barcode" :value="editVariant.barcode" @else value="{{ old('barcode') }}" @endif />
      <div class="absolute inset-y-0 flex items-center ml-3 pointer-events-none">
        <i class="fas fa-qrcode"></i>
      </div>
    </div>
    @error('barcode')<span class="text-xs text-red-600 dark:text-red-400">{{ $message }}</span>@enderror
  </label>

  <label class="block text-sm w-full">
    <span class="text-gray-700 dark:text-gray-400">Color</span>
    <div class="relative text-gray-500 focus-within:text-purple-600 dark:focus-within:text-purple-400">
      <select name="color_id" class="block w-full pl-10 mt-1 text-sm dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 form-select focus:border-purple-400 focus:shadow-outline-purple dark:focus:shadow-outline-gray"
        @if(isset($alpine) && $alpine) x-model="editVariant.color_id" @endif>
        <option value="">Sin color</option>
        @foreach ($colors as $id => $name)
          <option value="{{ $id }}" @if(!isset($alpine) || !$alpine) {{ old('color_id') == $id ? 'selected' : '' }} @endif>{{ $name }}</option>
        @endforeach
      </select>
      <div class="absolute inset-y-0 flex items-center ml-3 pointer-events-none">
        <i class="fas fa-palette"></i>
      </div>
    </div>
    @error('color_id')<span class="text-xs text-red-600 dark:text-red-400">{{ $message }}</span>@enderror
  </label>

  <label class="block text-sm w-full">
    <span class="text-gray-700 dark:text-gray-400">Talla</span>
    <div class="relative text-gray-500 focus-within:text-purple-600 dark:focus-within:text-purple-400">
      <select name="size_id" class="block w-full pl-10 mt-1 text-sm dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 form-select focus:border-purple-400 focus:shadow-outline-purple dark:focus:shadow-outline-gray"
        @if(isset($alpine) && $alpine) x-model="editVariant.size_id" @endif>
        <option value="">Sin talla</option>
        @foreach ($sizes as $id => $name)
          <option value="{{ $id }}" @if(!isset($alpine) || !$alpine) {{ old('size_id') == $id ? 'selected' : '' }} @endif>{{ $name }}</option>
        @endforeach
      </select>
      <div class="absolute inset-y-0 flex items-center ml-3 pointer-events-none">
        <i class="fas fa-ruler"></i>
      </div>
    </div>
    @error('size_id')<span class="text-xs text-red-600 dark:text-red-400">{{ $message }}</span>@enderror
  </label>
</div>
