<div class="px-4 py-3 mb-8 bg-white rounded-lg shadow-md dark:bg-gray-800">
    <!-- Name Field -->
    <label class="block text-sm">
        <span class="text-gray-700 dark:text-gray-400">Nombre</span>
        <div class="relative text-gray-500 focus-within:text-purple-600 dark:focus-within:text-purple-400">
            <input name="name"
                class="block w-full pl-10 mt-1 text-sm dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 form-input focus:border-purple-400 focus:shadow-outline-purple dark:focus:shadow-outline-gray"
                placeholder="Escribe un nombre..."
                @if (isset($alpine) && $alpine) x-model="editTax.name" :value="editTax.name"
                @else value="{{ old('name', isset($tax) ? $tax->name : '') }}" @endif
                required />
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
                @if (isset($alpine) && $alpine) x-model="editTax.percentage" :value="editTax.percentage"
                @else value="{{ old('percentage', isset($tax) ? $tax->percentage : '') }}" @endif
                required step="0.01" inputmode="decimal" pattern="[0-9]+([\.,][0-9]{1,2})?" />
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
        <x-ui.submit-button>
            <i class="fas fa-paper-plane mr-2"></i> {{ isset($entity) ? 'Actualizar' : 'Guardar' }}
        </x-ui.submit-button>
    </div>
</div>
