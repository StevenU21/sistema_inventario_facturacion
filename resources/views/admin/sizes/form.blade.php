<!-- Name Field -->
<label class="block text-sm">
    <span class="text-gray-700 dark:text-gray-400">Nombre</span>
    <div class="relative text-gray-500 focus-within:text-purple-600 dark:focus-within:text-purple-400">
        <input name="name"
            class="block w-full pl-10 mt-1 text-sm dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 form-input focus:border-purple-400 focus:shadow-outline-purple dark:focus:shadow-outline-gray"
            placeholder="Escribe un nombre..."
            @if (isset($alpine) && $alpine) x-model="editSize.name"
                :value="editSize.name"
            @else
                value="{{ old('name', isset($size) ? $size->name : '') }}" @endif
            required />
        <div class="absolute inset-y-0 flex items-center ml-3 pointer-events-none">
            <i class="fas fa-user w-5 h-5"></i>
        </div>
    </div>
    @error('name')
        <span class="text-xs text-red-600 dark:text-red-400">{{ $message }}</span>
    @enderror
</label>

<!-- Campo Descripción -->
<label class="block mt-4 text-sm">
    <span class="text-gray-700 dark:text-gray-400">Descripción</span>
    <div class="relative text-gray-500 focus-within:text-purple-600 dark:focus-within:text-purple-400">
        <textarea name="description"
            class="block w-full pl-10 mt-1 text-sm dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 form-textarea focus:border-purple-400 focus:shadow-outline-purple dark:focus:shadow-outline-gray"
            rows="3" placeholder="Escribe una descripción..."
            @if (isset($alpine) && $alpine) x-model="editSize.description" @endif>
@if (!isset($alpine) || !$alpine)
{{ old('description', isset($size) ? $size->description : '') }}
@endif
</textarea>
        <div class="absolute inset-y-0 left-0 flex items-center ml-3 pointer-events-none">
            <i class="fas fa-comment w-5 h-5"></i>
        </div>
    </div>
    @error('description')
        <span class="text-xs text-red-600 dark:text-red-400">{{ $message }}</span>
    @enderror
</label>

<!-- Submit Button -->
<div class="mt-6 flex space-x-4">
    <x-ui.submit-button>
        <i class="fas fa-paper-plane mr-2"></i> {{ isset($entity) ? 'Actualizar' : 'Guardar' }}
    </x-ui.submit-button>
</div>
