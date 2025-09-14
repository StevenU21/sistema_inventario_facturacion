<div class="px-4 py-3 mb-8 bg-white rounded-lg shadow-md dark:bg-gray-800">

    <!-- Nombres | Apellidos -->
    <div class="flex flex-col md:flex-row gap-4">
        <!-- Nombre -->
        <label class="block text-sm w-full">
            <span class="text-gray-700 dark:text-gray-400">Nombres</span>
            <div class="relative text-gray-500 focus-within:text-purple-600 dark:focus-within:text-purple-400">
                <input name="first_name" type="text"
                    class="block w-full pl-10 mt-1 text-sm dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 form-input focus:border-purple-400 focus:shadow-outline-purple dark:focus:shadow-outline-gray @error('first_name') border-red-600 @enderror"
                    placeholder="Nombre..."
                    @if (isset($alpine) && $alpine) x-model="editUser.first_name" @else value="{{ old('first_name', isset($user) ? $user->first_name ?? '' : '') }}" @endif />
                <div class="absolute inset-y-0 flex items-center ml-3 pointer-events-none">
                    <i class="fas fa-user w-5 h-5"></i>
                </div>
            </div>
            @error('first_name')
                <span class="text-xs text-red-600 dark:text-red-400">{{ $message }}</span>
            @enderror
        </label>
        <!-- Apellido -->
        <label class="block text-sm w-full">
            <span class="text-gray-700 dark:text-gray-400">Apellidos</span>
            <div class="relative text-gray-500 focus-within:text-purple-600 dark:focus-within:text-purple-400">
                <input name="last_name" type="text"
                    class="block w-full pl-10 mt-1 text-sm dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 form-input focus:border-purple-400 focus:shadow-outline-purple dark:focus:shadow-outline-gray @error('last_name') border-red-600 @enderror"
                    placeholder="Apellido..."
                    @if (isset($alpine) && $alpine) x-model="editUser.last_name" @else value="{{ old('last_name', isset($user) ? $user->last_name ?? '' : '') }}" @endif />
                <div class="absolute inset-y-0 flex items-center ml-3 pointer-events-none">
                    <i class="fas fa-user-tag w-5 h-5"></i>
                </div>
            </div>
            @error('last_name')
                <span class="text-xs text-red-600 dark:text-red-400">{{ $message }}</span>
            @enderror
        </label>
    </div>

    <!-- Cédula | Teléfono | Género -->
    <div class="flex flex-col md:flex-row gap-4 mt-4">
        <!-- Cédula de Identidad -->
        <label class="block text-sm w-full">
            <span class="text-gray-700 dark:text-gray-400">Cédula de Identidad</span>
            <div class="relative text-gray-500 focus-within:text-purple-600 dark:focus-within:text-purple-400">
                <input name="identity_card" type="text"
                    class="block w-full pl-10 mt-1 text-sm dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 form-input focus:border-purple-400 focus:shadow-outline-purple dark:focus:shadow-outline-gray @error('identity_card') border-red-600 @enderror"
                    placeholder="Cédula..."
                    @if (isset($alpine) && $alpine) x-model="editUser.identity_card" @else value="{{ old('identity_card', isset($user) && isset($user->profile) ? $user->profile->identity_card ?? '' : '') }}" @endif />
                <div class="absolute inset-y-0 flex items-center ml-3 pointer-events-none">
                    <i class="fas fa-id-card w-5 h-5"></i>
                </div>
            </div>
            @error('identity_card')
                <span class="text-xs text-red-600 dark:text-red-400">{{ $message }}</span>
            @enderror
        </label>
        <!-- Teléfono -->
        <label class="block text-sm w-full">
            <span class="text-gray-700 dark:text-gray-400">Teléfono</span>
            <div class="relative text-gray-500 focus-within:text-purple-600 dark:focus-within:text-purple-400">
                <input name="phone" type="text"
                    class="block w-full pl-10 mt-1 text-sm dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 form-input focus:border-purple-400 focus:shadow-outline-purple dark:focus:shadow-outline-gray @error('phone') border-red-600 @enderror"
                    placeholder="Teléfono..."
                    @if (isset($alpine) && $alpine) x-model="editUser.phone" @else value="{{ old('phone', isset($user) && isset($user->profile) ? $user->profile->phone ?? '' : '') }}" @endif />
                <div class="absolute inset-y-0 flex items-center ml-3 pointer-events-none">
                    <i class="fas fa-phone w-5 h-5"></i>
                </div>
            </div>
            @error('phone')
                <span class="text-xs text-red-600 dark:text-red-400">{{ $message }}</span>
            @enderror
        </label>
        <!-- Género -->
        <label class="block text-sm w-full">
            <span class="text-gray-700 dark:text-gray-400">Género</span>
            <div class="relative text-gray-500 focus-within:text-purple-600 dark:focus-within:text-purple-400">
                <select name="gender"
                    class="block w-full pl-10 mt-1 text-sm dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 form-select focus:border-purple-400 focus:shadow-outline-purple dark:focus:shadow-outline-gray @error('gender') border-red-600 @enderror"
                    @if (isset($alpine) && $alpine) x-model="editUser.gender" @endif>
                    <option value="">Selecciona un género</option>
                    <option value="male"
                        @if (!isset($alpine) || !$alpine) {{ old('gender', isset($user) && isset($user->profile) ? $user->profile->gender ?? '' : '') == 'male' ? 'selected' : '' }} @endif>
                        Masculino</option>
                    <option value="female"
                        @if (!isset($alpine) || !$alpine) {{ old('gender', isset($user) && isset($user->profile) ? $user->profile->gender ?? '' : '') == 'female' ? 'selected' : '' }} @endif>
                        Femenino</option>
                </select>
                <div class="absolute inset-y-0 flex items-center ml-3 pointer-events-none">
                    <i class="fas fa-venus-mars w-5 h-5"></i>
                </div>
            </div>
            @error('gender')
                <span class="text-xs text-red-600 dark:text-red-400">{{ $message }}</span>
            @enderror
        </label>
    </div>

    <!-- Correo | Rol -->
    <div class="flex flex-col md:flex-row gap-4 mt-4">
        <!-- Email -->
        <label class="block text-sm w-full">
            <span class="text-gray-700 dark:text-gray-400">Email</span>
            <div class="relative text-gray-500 focus-within:text-purple-600 dark:focus-within:text-purple-400">
                <input name="email" type="email"
                    class="block w-full pl-10 mt-1 text-sm dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 form-input focus:border-purple-400 focus:shadow-outline-purple dark:focus:shadow-outline-gray @error('email') border-red-600 @enderror"
                    placeholder="Correo electrónico..."
                    @if (isset($alpine) && $alpine) x-model="editUser.email" @else value="{{ old('email', isset($user) ? $user->email ?? '' : '') }}" @endif />
                <div class="absolute inset-y-0 flex items-center ml-3 pointer-events-none">
                    <i class="fas fa-envelope w-5 h-5"></i>
                </div>
            </div>
            @error('email')
                <span class="text-xs text-red-600 dark:text-red-400">{{ $message }}</span>
            @enderror
        </label>
        <!-- Rol -->
        <label class="block text-sm w-full">
            <span class="text-gray-700 dark:text-gray-400">Rol</span>
            <div class="relative text-gray-500 focus-within:text-purple-600 dark:focus-within:text-purple-400">
                <select name="role"
                    class="block w-full pl-10 mt-1 text-sm dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 form-select focus:border-purple-400 focus:shadow-outline-purple dark:focus:shadow-outline-gray @error('role') border-red-600 @enderror"
                    @if (isset($alpine) && $alpine) x-model="editUser.role" @endif>
                    <option value="">Selecciona un rol</option>
                    @foreach ($roles as $role)
                        <option value="{{ $role->name }}"
                            @if (!isset($alpine) || !$alpine) {{ old('role', isset($user) ? optional($user->roles->first())->name ?? '' : '') == $role->name ? 'selected' : '' }} @endif>
                            @if ($role->name === 'admin')
                                Administrador
                            @elseif($role->name === 'cashier')
                                Cajero
                            @else
                                {{ $role->name }}
                            @endif
                        </option>
                    @endforeach
                </select>
                <div class="absolute inset-y-0 flex items-center ml-3 pointer-events-none">
                    <i class="fas fa-user-shield w-5 h-5"></i>
                </div>
            </div>
            @error('role')
                <span class="text-xs text-red-600 dark:text-red-400">{{ $message }}</span>
            @enderror
        </label>
    </div>

    <!-- Avatar -->
    <label class="block mt-4 text-sm w-full">
        <span class="text-gray-700 dark:text-gray-400">Avatar (imagen)</span>
        <div
            class="relative flex items-center text-gray-500 focus-within:text-purple-600 dark:focus-within:text-purple-400 mt-1">
            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                <i class="fas fa-image w-5 h-5"></i>
            </div>
            <input name="avatar" type="file" accept="image/*"
                class="block w-full pl-10 text-sm dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 form-input file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100 focus:border-purple-400 focus:shadow-outline-purple dark:focus:shadow-outline-gray @error('avatar') border-red-600 @enderror"
                id="{{ isset($alpine) && $alpine ? 'avatarInputEdit' : 'avatarInputCreate' }}" />
        </div>
        <div class="mt-2">
            <img id="{{ isset($alpine) && $alpine ? 'avatarPreviewEdit' : 'avatarPreviewCreate' }}"
                @if (isset($alpine) && $alpine) :src="editUser.avatar_url" x-show="editUser.avatar_url"
                @else
                    src="{{ isset($user) && isset($user->profile) && $user->profile->avatar ? asset('storage/' . $user->profile->avatar) : '' }}"
                    style="display: {{ isset($user) && isset($user->profile) && $user->profile->avatar ? 'block' : 'none' }};" @endif
                alt="Vista previa" width="80" class="rounded">
        </div>
        @error('avatar')
            <span class="text-xs text-red-600 dark:text-red-400">{{ $message }}</span>
        @enderror
    </label>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const pairs = [{
                    inputId: 'avatarInputCreate',
                    previewId: 'avatarPreviewCreate'
                },
                {
                    inputId: 'avatarInputEdit',
                    previewId: 'avatarPreviewEdit'
                },
            ];
            pairs.forEach(({
                inputId,
                previewId
            }) => {
                const input = document.getElementById(inputId);
                const preview = document.getElementById(previewId);
                if (input && preview) {
                    input.addEventListener('change', function(e) {
                        const file = e.target.files[0];
                        if (file) {
                            const reader = new FileReader();
                            reader.onload = function(ev) {
                                preview.src = ev.target.result;
                                if (preview.hasAttribute('x-show')) {
                                    // Alpine controla la visibilidad; solo se asigna el src
                                } else {
                                    preview.style.display = 'block';
                                }
                            };
                            reader.readAsDataURL(file);
                        } else {
                            preview.src = '';
                            if (!preview.hasAttribute('x-show')) {
                                preview.style.display = 'none';
                            }
                        }
                    });
                }
            })
        });
    </script>

    <!-- Dirección -->
    <label class="block mt-4 text-sm">
        <span class="text-gray-700 dark:text-gray-400">Dirección</span>
        <div class="relative text-gray-500 focus-within:text-purple-600 dark:focus-within:text-purple-400">
            <input name="address" type="text"
                class="block w-full pl-10 mt-1 text-sm dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 form-input focus:border-purple-400 focus:shadow-outline-purple dark:focus:shadow-outline-gray @error('address') border-red-600 @enderror"
                placeholder="Dirección..."
                @if (isset($alpine) && $alpine) x-model="editUser.address" @else value="{{ old('address', isset($user) && isset($user->profile) ? $user->profile->address ?? '' : '') }}" @endif />
            <div class="absolute inset-y-0 flex items-center ml-3 pointer-events-none">
                <i class="fas fa-map-marker-alt w-5 h-5"></i>
            </div>
        </div>
        @error('address')
            <span class="text-xs text-red-600 dark:text-red-400">{{ $message }}</span>
        @enderror
    </label>

    <!-- Contraseña -->
    <label class="block mt-4 text-sm">
        <span class="text-gray-700 dark:text-gray-400">Contraseña</span>
        <div class="relative text-gray-500 focus-within:text-purple-600 dark:focus-within:text-purple-400">
            <input name="password" type="password"
                class="block w-full pl-10 mt-1 text-sm dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 form-input focus:border-purple-400 focus:shadow-outline-purple dark:focus:shadow-outline-gray @error('password') border-red-600 @enderror"
                placeholder="Contraseña..." />
            <div class="absolute inset-y-0 flex items-center ml-3 pointer-events-none">
                <i class="fas fa-lock w-5 h-5"></i>
            </div>
        </div>
        @error('password')
            <span class="text-xs text-red-600 dark:text-red-400">{{ $message }}</span>
        @enderror
    </label>

    <!-- Confirmar Contraseña -->
    <label class="block mt-4 text-sm">
        <span class="text-gray-700 dark:text-gray-400">Confirmar Contraseña</span>
        <div class="relative text-gray-500 focus-within:text-purple-600 dark:focus-within:text-purple-400">
            <input name="password_confirmation" type="password"
                class="block w-full pl-10 mt-1 text-sm dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 form-input focus:border-purple-400 focus:shadow-outline-purple dark:focus:shadow-outline-gray @error('password_confirmation') border-red-600 @enderror"
                placeholder="Confirmar Contraseña..." />
            <div class="absolute inset-y-0 flex items-center ml-3 pointer-events-none">
                <i class="fas fa-lock w-5 h-5"></i>
            </div>
        </div>
        @error('password_confirmation')
            <span class="text-xs text-red-600 dark:text-red-400">{{ $message }}</span>
        @enderror
    </label>

    <!-- Botón enviar -->
    <div class="mt-6">
        <x-ui.submit-button>
            <i class="fas fa-paper-plane mr-2"></i> {{ isset($entity) ? 'Actualizar' : 'Guardar' }}
        </x-ui.submit-button>
    </div>
</div>
