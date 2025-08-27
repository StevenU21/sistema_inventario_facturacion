<div class="px-4 py-3 mb-8 bg-white rounded-lg shadow-md dark:bg-gray-800">
    <!-- Name Field -->
    <label class="block text-sm">
        <span class="text-gray-700 dark:text-gray-400">Nombre</span>
        <div class="relative text-gray-500 focus-within:text-purple-600 dark:focus-within:text-purple-400">
            <input name="name"
                class="block w-full pl-10 mt-1 text-sm dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 form-input focus:border-purple-400 focus:shadow-outline-purple dark:focus:shadow-outline-gray"
                placeholder="Escribe un nombre..." value="{{ old('name', isset($tax) ? $tax->name : '') }}" required />
            <div class="absolute inset-y-0 flex items-center ml-3 pointer-events-none">
                <i class="fas fa-user w-5 h-5"></i>
            </div>
        </div>
        @error('name')
            <span class="text-xs text-red-600 dark:text-red-400">{{ $message }}</span>
        @enderror
    </label>

    <!-- Campo Percentaje -->
    <label class="block mt-4 text-sm">
        <span class="text-gray-700 dark:text-gray-400">Percentaje</span>
        <div class="relative text-gray-500 focus-within:text-purple-600 dark:focus-within:text-purple-400">
            <input name="percentage" type="number" min="0" max="100"
                class="block w-full pl-10 mt-1 text-sm dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 form-input focus:border-purple-400 focus:shadow-outline-purple dark:focus:shadow-outline-gray"
                placeholder="Escribe un porcentaje..."
                value="{{ old('percentage', isset($tax) ? $tax->percentage : '') }}" required />
            <div class="absolute inset-y-0 flex items-center ml-3 pointer-events-none">
                <i class="fas fa-percent w-5 h-5"></i>
            </div>
        </div>
        @error('percentage')
            <span class="text-xs text-red-600 dark:text-red-400">{{ $message }}</span>
        @enderror
    </label>

    <!-- Submit Button -->
    <div class="mt-6">
        <button type="submit"
            class="flex items-center px-4 py-2 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-purple-600 border border-transparent rounded-lg hover:bg-purple-700 focus:outline-none focus:shadow-outline-purple active:bg-purple-600">
            <i class="fas fa-paper-plane mr-2"></i> {{ isset($tax) ? 'Actualizar' : 'Guardar' }}
        </button>
    </div>
</div>
