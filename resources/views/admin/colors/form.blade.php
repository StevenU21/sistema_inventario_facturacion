<!-- Name Field -->
<label class="block text-sm">
    <span class="text-gray-700 dark:text-gray-400">Nombre</span>
    <div class="relative text-gray-500 focus-within:text-purple-600 dark:focus-within:text-purple-400">
        <input name="name"
            class="block w-full pl-10 mt-1 text-sm dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 form-input focus:border-purple-400 focus:shadow-outline-purple dark:focus:shadow-outline-gray"
            placeholder="Escribe un nombre..."
            @if (isset($alpine) && $alpine) x-model="editColor.name"
                :value="editColor.name"
            @else
                value="{{ old('name', isset($color) && is_object($color) ? $color->name : '') }}" @endif
            required />
        <div class="absolute inset-y-0 flex items-center ml-3 pointer-events-none">
            <i class="fas fa-user w-5 h-5"></i>
        </div>
    </div>
    @error('name')
        <span class="text-xs text-red-600 dark:text-red-400">{{ $message }}</span>
    @enderror
</label>

<!-- Hex Code Field -->
<label class="block text-sm">
    <span class="text-gray-700 dark:text-gray-400">Código Hexadecimal</span>
    <div class="relative text-gray-500 focus-within:text-purple-600 dark:focus-within:text-purple-400">
        <input name="hex_code"
            class="block w-full pl-10 mt-1 text-sm dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 form-input focus:border-purple-400 focus:shadow-outline-purple dark:focus:shadow-outline-gray"
            placeholder="Escribe un código hexadecimal..."
            @if (isset($alpine) && $alpine) x-model="editColor.hex_code"
                :value="editColor.hex_code"
            @else
                value="{{ old('hex_code', isset($color) && is_object($color) ? $color->hex_code : '') }}" @endif />
        <div class="absolute inset-y-0 flex items-center ml-3 pointer-events-none">
            <i class="fas fa-user w-5 h-5"></i>
        </div>
    </div>
    @error('hex_code')
        <span class="text-xs text-red-600 dark:text-red-400">{{ $message }}</span>
    @enderror
</label>

<!-- Submit Button -->
<div class="mt-6 flex space-x-4">
    <x-ui.submit-button>
        <i class="fas fa-paper-plane mr-2"></i> {{ isset($entity) ? 'Actualizar' : 'Guardar' }}
    </x-ui.submit-button>
</div>
