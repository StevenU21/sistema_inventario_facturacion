<div class="px-4 py-3 mb-8 bg-white rounded-lg shadow-md dark:bg-gray-800">
    <!-- Name Field -->
    <label class="block text-sm">
        <span class="text-gray-700 dark:text-gray-400">Nombre</span>
        <div class="relative text-gray-500 focus-within:text-purple-600 dark:focus-within:text-purple-400">
            <input name="name"
                class="block w-full pl-10 mt-1 text-sm dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 form-input focus:border-purple-400 focus:shadow-outline-purple dark:focus:shadow-outline-gray"
                placeholder="Escribe un nombre..." value="{{ old('name', isset($role) ? $role->name : '') }}" required />
            <div class="absolute inset-y-0 flex items-center ml-3 pointer-events-none">
                <i class="fas fa-user w-5 h-5"></i>
            </div>
        </div>
        @error('name')
            <span class="text-xs text-red-600 dark:text-red-400">{{ $message }}</span>
        @enderror
    </label>


    <!-- Permisos (diseño tipo tabla, 4 por fila) -->
    <div class="mt-4">
        <span class="text-gray-700 dark:text-gray-400 font-semibold">Permisos</span>
        <div class="overflow-x-auto mt-2">
            <table class="w-full table-fixed">
                <tbody>
                    @foreach ($permissions->chunk(4) as $row)
                        <tr>
                            @foreach ($row as $permission)
                                <td class="px-2 py-2 align-middle">
                                    <label
                                        class="flex items-center space-x-2 font-semibold text-gray-700 dark:text-gray-300">
                                        <input type="checkbox" name="permissions[]" value="{{ $permission->id }}"
                                            class="form-checkbox text-purple-600"
                                            @if (is_array(old('permissions'))) {{ in_array($permission->id, old('permissions')) ? 'checked' : '' }}
                                            @elseif(isset($rolePermissions) && in_array($permission->id, $rolePermissions))
                                                checked @endif>
                                        <span
                                            class="uppercase">{{ $translatedPermissions[$permission->name] ?? $permission->name }}</span>
                                    </label>
                                </td>
                            @endforeach
                            @for ($i = $row->count(); $i < 4; $i++)
                                <td></td>
                            @endfor
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @error('permissions')
            <span class="text-xs text-red-600 dark:text-red-400">{{ $message }}</span>
        @enderror
    </div>

    <!-- Submit Button -->
    <div class="mt-6">
        <x-ui.submit-button>
            <i class="fas fa-paper-plane mr-2"></i> {{ isset($entity) ? 'Actualizar' : 'Guardar' }}
        </x-ui.submit-button>
    </div>
</div>
