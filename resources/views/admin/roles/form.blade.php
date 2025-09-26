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


    <!-- Permisos (agrupados y con buscador) -->
    <div class="mt-4">
        <span class="text-gray-700 dark:text-gray-400 font-semibold">Permisos</span>
        @php
            $groupedRolePermissions = $permissions->groupBy(function ($perm) {
                return str_contains($perm->name, '.') ? \Illuminate\Support\Str::before($perm->name, '.') : 'otros';
            })->sortKeys();
        @endphp

        <!-- Buscador -->
        <div class="mt-2 mb-4 relative">
            <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                <i class="fas fa-search"></i>
            </span>
            <input id="role-permission-filter" type="text" placeholder="Filtrar permisos..."
                class="w-full pl-10 pr-9 py-2 text-sm text-gray-600 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-600 focus:border-purple-600">
            <button type="button" id="role-permission-filter-clear" aria-label="Limpiar filtro"
                class="hidden absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 focus:outline-none">
                <i class="fas fa-times-circle text-sm"></i>
            </button>
        </div>

        <!-- Grupos -->
        <div class="space-y-6" id="role-permission-groups-wrapper">
            @foreach ($groupedRolePermissions as $group => $permsGroup)
                <fieldset data-permission-group class="border border-gray-200 dark:border-gray-700 rounded-md p-4">
                    <legend class="px-2 text-sm font-semibold text-purple-700 dark:text-purple-400 uppercase tracking-wide">
                        {{ (string) \Illuminate\Support\Str::of($group)->replace('_', ' ')->title() }}
                    </legend>
                    <div class="flex flex-wrap -mx-2">
                        @foreach ($permsGroup->sortBy('name') as $permission)
                            @php
                                $translated = $translatedPermissions[$permission->name] ?? $permission->name;
                                $checked = false;
                                if(is_array(old('permissions'))){
                                    $checked = in_array($permission->id, old('permissions'));
                                } elseif(isset($rolePermissions) && in_array($permission->id, $rolePermissions)) {
                                    $checked = true;
                                }
                            @endphp
                            <div class="w-1/2 md:w-1/3 lg:w-1/4 px-2 py-1">
                                <label data-permission-label="{{ \Illuminate\Support\Str::lower($translated) }}" class="flex items-center space-x-2 text-xs md:text-sm font-semibold text-gray-700 dark:text-gray-300">
                                    <input type="checkbox" name="permissions[]" value="{{ $permission->id }}" class="form-checkbox h-4 w-4 text-purple-600" @if($checked) checked @endif>
                                    <span class="uppercase leading-snug">{{ $translated }}</span>
                                </label>
                            </div>
                        @endforeach
                    </div>
                </fieldset>
            @endforeach
        </div>

        @error('permissions')
            <span class="text-xs text-red-600 dark:text-red-400">{{ $message }}</span>
        @enderror
    </div>

    @push('scripts')
        <script>
            (function () {
                const input = document.getElementById('role-permission-filter');
                const clearBtn = document.getElementById('role-permission-filter-clear');
                if(!input) return;
                function applyFilter(){
                    const term = input.value.trim().toLowerCase();
                    if(clearBtn){
                        if(term.length) clearBtn.classList.remove('hidden'); else clearBtn.classList.add('hidden');
                    }
                    const groups = document.querySelectorAll('#role-permission-groups-wrapper [data-permission-group]');
                    groups.forEach(groupEl => {
                        let anyVisibleInGroup = false;
                        const items = groupEl.querySelectorAll('[data-permission-label]');
                        items.forEach(label => {
                            const text = label.getAttribute('data-permission-label');
                            const container = label.closest('div');
                            if(!term || text.includes(term)) {
                                container.classList.remove('hidden');
                                anyVisibleInGroup = true;
                            } else {
                                container.classList.add('hidden');
                            }
                        });
                        groupEl.classList.toggle('hidden', !anyVisibleInGroup);
                    });
                }
                input.addEventListener('input', applyFilter);
                if(clearBtn){
                    clearBtn.addEventListener('click', () => { input.value=''; applyFilter(); input.focus(); });
                }
            })();
        </script>
    @endpush

    <!-- Submit Button -->
    <div class="mt-6">
        <x-ui.submit-button>
            <i class="fas fa-paper-plane mr-2"></i> {{ isset($entity) ? 'Actualizar' : 'Guardar' }}
        </x-ui.submit-button>
    </div>
</div>
