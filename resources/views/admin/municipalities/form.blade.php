<div class="px-4 py-3 mb-8 bg-white rounded-lg shadow-md dark:bg-gray-800">
    <!-- Name Field -->
    <label class="block text-sm">
        <span class="text-gray-700 dark:text-gray-400">Nombre</span>
        <div class="relative text-gray-500 focus-within:text-purple-600 dark:focus-within:text-purple-400">
            <input name="name"
                class="block w-full pl-10 mt-1 text-sm dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 form-input focus:border-purple-400 focus:shadow-outline-purple dark:focus:shadow-outline-gray"
                placeholder="Escribe un nombre..."
                value="{{ old('name', isset($municipality) ? $municipality->name : '') }}" required />
            <div class="absolute inset-y-0 flex items-center ml-3 pointer-events-none">
                <i class="fas fa-user w-5 h-5"></i>
            </div>
        </div>
        @error('name')
            <span class="text-xs text-red-600 dark:text-red-400">{{ $message }}</span>
        @enderror
    </label>

    <label class="block text-sm w-full">
        <span class="text-gray-700 dark:text-gray-400">Departamento</span>
        <div class="relative text-gray-500 focus-within:text-purple-600 dark:focus-within:text-purple-400">
            <select name="department_id"
                class="block w-full pl-10 mt-1 text-sm dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 form-select focus:border-purple-400 focus:shadow-outline-purple dark:focus:shadow-outline-gray @error('department_id') border-red-600 @enderror">
                <option value="">Selecciona un departamento</option>
                @foreach ($departments as $id => $name)
                    <option value="{{ $id }}"
                        {{ old('department_id', isset($municipality) ? $municipality->department_id : '') == $id ? 'selected' : '' }}>
                        {{ $name }}</option>
                @endforeach
            </select>
            <div class="absolute inset-y-0 flex items-center ml-3 pointer-events-none">
                <i class="fas fa-venus-mars w-5 h-5"></i>
            </div>
        </div>
        @error('department_id')
            <span class="text-xs text-red-600 dark:text-red-400">{{ $message }}</span>
        @enderror
    </label>

    <!-- Campo Descripción -->
    <label class="block mt-4 text-sm">
        <span class="text-gray-700 dark:text-gray-400">Descripción</span>
        <div class="relative text-gray-500 focus-within:text-purple-600 dark:focus-within:text-purple-400">
            <textarea name="description"
                class="block w-full pl-10 mt-1 text-sm dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 form-textarea focus:border-purple-400 focus:shadow-outline-purple dark:focus:shadow-outline-gray"
                rows="3" placeholder="Escribe una descripción...">{{ old('description', isset($municipality) ? $municipality->description : '') }}</textarea>
            <div class="absolute inset-y-0 left-0 flex items-center ml-3 pointer-events-none">
                <i class="fas fa-comment w-5 h-5"></i>
            </div>
        </div>
        @error('description')
            <span class="text-xs text-red-600 dark:text-red-400">{{ $message }}</span>
        @enderror
    </label>


</div>
