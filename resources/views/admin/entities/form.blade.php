<div class="px-4 py-3 mb-8 bg-white rounded-lg shadow-md dark:bg-gray-800">
    <!-- Nombres | Apellidos -->
    <div class="flex flex-col md:flex-row gap-4">
        <!-- Nombres -->
        <label class="block text-sm w-full">
            <span class="text-gray-700 dark:text-gray-400">Nombres</span>
            <div class="relative text-gray-500 focus-within:text-purple-600 dark:focus-within:text-purple-400">
                <input name="first_name" type="text"
                    class="block w-full pl-10 mt-1 text-sm dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 form-input focus:border-purple-400 focus:shadow-outline-purple dark:focus:shadow-outline-gray @error('first_name') border-red-600 @enderror"
                    placeholder="Escribe los nombres..."
                    value="{{ old('first_name', isset($entity) ? $entity->first_name : '') }}" required />
                <div class="absolute inset-y-0 flex items-center ml-3 pointer-events-none">
                    <i class="fas fa-user w-5 h-5"></i>
                </div>
            </div>
            @error('first_name')
                <span class="text-xs text-red-600 dark:text-red-400">{{ $message }}</span>
            @enderror
        </label>
        <!-- Apellidos -->
        <label class="block text-sm w-full">
            <span class="text-gray-700 dark:text-gray-400">Apellidos</span>
            <div class="relative text-gray-500 focus-within:text-purple-600 dark:focus-within:text-purple-400">
                <input name="last_name" type="text"
                    class="block w-full pl-10 mt-1 text-sm dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 form-input focus:border-purple-400 focus:shadow-outline-purple dark:focus:shadow-outline-gray @error('last_name') border-red-600 @enderror"
                    placeholder="Escribe los apellidos..."
                    value="{{ old('last_name', isset($entity) ? $entity->last_name : '') }}" required />
                <div class="absolute inset-y-0 flex items-center ml-3 pointer-events-none">
                    <i class="fas fa-user-tag w-5 h-5"></i>
                </div>
            </div>
            @error('last_name')
                <span class="text-xs text-red-600 dark:text-red-400">{{ $message }}</span>
            @enderror
        </label>
    </div>

    <!-- Cédula | RUC | Teléfono -->
    <div class="flex flex-col md:flex-row gap-4 mt-4">
        <!-- Cédula de Identidad -->
        <label class="block text-sm w-full">
            <span class="text-gray-700 dark:text-gray-400">Cédula de Identidad</span>
            <div class="relative text-gray-500 focus-within:text-purple-600 dark:focus-within:text-purple-400">
                <input name="identity_card" type="text"
                    class="block w-full pl-10 mt-1 text-sm dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 form-input focus:border-purple-400 focus:shadow-outline-purple dark:focus:shadow-outline-gray @error('identity_card') border-red-600 @enderror"
                    placeholder="Cédula de Identidad..."
                    value="{{ old('identity_card', isset($entity) ? $entity->identity_card : '') }}" required />
                <div class="absolute inset-y-0 flex items-center ml-3 pointer-events-none">
                    <i class="fas fa-id-card w-5 h-5"></i>
                </div>
            </div>
            @error('identity_card')
                <span class="text-xs text-red-600 dark:text-red-400">{{ $message }}</span>
            @enderror
        </label>
        <!-- RUC -->
        <label class="block text-sm w-full">
            <span class="text-gray-700 dark:text-gray-400">RUC</span>
            <div class="relative text-gray-500 focus-within:text-purple-600 dark:focus-within:text-purple-400">
                <input name="ruc" type="text"
                    class="block w-full pl-10 mt-1 text-sm dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 form-input focus:border-purple-400 focus:shadow-outline-purple dark:focus:shadow-outline-gray @error('ruc') border-red-600 @enderror"
                    placeholder="RUC..." value="{{ old('ruc', isset($entity) ? $entity->ruc : '') }}" required />
                <div class="absolute inset-y-0 flex items-center ml-3 pointer-events-none">
                    <i class="fas fa-id-card w-5 h-5"></i>
                </div>
            </div>
            @error('ruc')
                <span class="text-xs text-red-600 dark:text-red-400">{{ $message }}</span>
            @enderror
        </label>
        <!-- Teléfono -->
        <label class="block text-sm w-full">
            <span class="text-gray-700 dark:text-gray-400">Teléfono</span>
            <div class="relative text-gray-500 focus-within:text-purple-600 dark:focus-within:text-purple-400">
                <input name="phone" type="text"
                    class="block w-full pl-10 mt-1 text-sm dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 form-input focus:border-purple-400 focus:shadow-outline-purple dark:focus:shadow-outline-gray @error('phone') border-red-600 @enderror"
                    placeholder="Teléfono..." value="{{ old('phone', isset($entity) ? $entity->phone : '') }}" />
                <div class="absolute inset-y-0 flex items-center ml-3 pointer-events-none">
                    <i class="fas fa-phone w-5 h-5"></i>
                </div>
            </div>
            @error('phone')
                <span class="text-xs text-red-600 dark:text-red-400">{{ $message }}</span>
            @enderror
        </label>
    </div>

    <!-- Email -->
    <label class="block mt-4 text-sm w-full">
        <span class="text-gray-700 dark:text-gray-400">Email</span>
        <div class="relative text-gray-500 focus-within:text-purple-600 dark:focus-within:text-purple-400">
            <input type="email" name="email"
                class="block w-full pl-10 mt-1 text-sm dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 form-input focus:border-purple-400 focus:shadow-outline-purple dark:focus:shadow-outline-gray @error('email') border-red-600 @enderror"
                placeholder="Correo electrónico..." value="{{ old('email', isset($entity) ? $entity->email : '') }}"
                required />
            <div class="absolute inset-y-0 flex items-center ml-3 pointer-events-none">
                <i class="fas fa-envelope w-5 h-5"></i>
            </div>
        </div>
        @error('email')
            <span class="text-xs text-red-600 dark:text-red-400">{{ $message }}</span>
        @enderror
    </label>

    <!-- Dirección -->
    <label class="block mt-4 text-sm w-full">
        <span class="text-gray-700 dark:text-gray-400">Dirección</span>
        <div class="relative text-gray-500 focus-within:text-purple-600 dark:focus-within:text-purple-400">
            <input name="address" type="text"
                class="block w-full pl-10 mt-1 text-sm dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 form-input focus:border-purple-400 focus:shadow-outline-purple dark:focus:shadow-outline-gray @error('address') border-red-600 @enderror"
                placeholder="Dirección..." value="{{ old('address', isset($entity) ? $entity->address : '') }}" />
            <div class="absolute inset-y-0 flex items-center ml-3 pointer-events-none">
                <i class="fas fa-map-marker-alt w-5 h-5"></i>
            </div>
        </div>
        @error('address')
            <span class="text-xs text-red-600 dark:text-red-400">{{ $message }}</span>
        @enderror
    </label>

    <!-- Descripción -->
    <label class="block mt-4 text-sm w-full">
        <span class="text-gray-700 dark:text-gray-400">Descripción</span>
        <div class="relative text-gray-500 focus-within:text-purple-600 dark:focus-within:text-purple-400">
            <textarea name="description"
                class="block w-full pl-10 mt-1 text-sm dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 form-textarea focus:border-purple-400 focus:shadow-outline-purple dark:focus:shadow-outline-gray @error('description') border-red-600 @enderror"
                rows="3" placeholder="Escribe una descripción...">{{ old('description', isset($entity) ? $entity->description : '') }}</textarea>
            <div class="absolute inset-y-0 left-0 flex items-center ml-3 pointer-events-none">
                <i class="fas fa-comment w-5 h-5"></i>
            </div>
        </div>
        @error('description')
            <span class="text-xs text-red-600 dark:text-red-400">{{ $message }}</span>
        @enderror
    </label>

    <!-- Cliente | Proveedor | Activo -->
    <div class="flex flex-col md:flex-row gap-4 mt-4">
        <label class="flex items-center text-sm w-full">
            <input type="hidden" name="is_client" value="0" />
            <input type="checkbox" name="is_client" value="1"
                {{ old('is_client', isset($entity) ? $entity->is_client : false) ? 'checked' : '' }}
                class="form-checkbox text-purple-600" />
            <span class="ml-2 text-gray-700 dark:text-gray-400">Cliente</span>
        </label>
        <label class="flex items-center text-sm w-full">
            <input type="hidden" name="is_supplier" value="0" />
            <input type="checkbox" name="is_supplier" value="1"
                {{ old('is_supplier', isset($entity) ? $entity->is_supplier : false) ? 'checked' : '' }}
                class="form-checkbox text-purple-600" />
            <span class="ml-2 text-gray-700 dark:text-gray-400">Proveedor</span>
        </label>
        <label class="flex items-center text-sm w-full">
            <input type="hidden" name="is_active" value="0" />
            <input type="checkbox" name="is_active" value="1"
                {{ old('is_active', isset($entity) ? $entity->is_active : true) ? 'checked' : '' }}
                class="form-checkbox text-purple-600" />
            <span class="ml-2 text-gray-700 dark:text-gray-400">Activo</span>
        </label>
    </div>

    <!-- Botón enviar -->
    <div class="mt-6">
        <button type="submit"
            class="flex items-center px-4 py-2 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-purple-600 border border-transparent rounded-lg hover:bg-purple-700 focus:outline-none focus:shadow-outline-purple active:bg-purple-600">
            <i class="fas fa-paper-plane mr-2"></i> {{ isset($entity) ? 'Actualizar' : 'Guardar' }}
        </button>
    </div>
</div>
