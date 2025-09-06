<div class="w-full overflow-hidden rounded-lg bg-white dark:bg-gray-800 shadow-md">
    @php($e = $entity ?? null)
    @php($se = $showEntity ?? null)
    <div class="p-3">
        <div class="flex items-start gap-2">
            <div class="min-w-0 flex-1">
                <div class="flex flex-wrap items-center gap-1">
                    <h3 class="text-base font-semibold text-gray-800 dark:text-gray-100"
                        x-text="((showEntity?.first_name||'')+' '+(showEntity?.last_name||'')).trim()">
                        {{ optional($e)->full_name ?? trim(data_get($se, 'first_name', '') . ' ' . data_get($se, 'last_name', '')) }}
                    </h3>
                    <span
                        class="px-2 py-0.5 text-[10px] rounded-full bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300">
                        ID #<span x-text="showEntity?.id || ''">{{ optional($e)->id ?? '' }}</span>
                    </span>
                    @php($active = optional($e)->is_active ?? (bool) data_get($se, 'is_active', false))
                    <span
                        class="px-2 py-0.5 text-[10px] rounded-full {{ $active ? 'bg-green-100 text-green-700 dark:bg-green-700 dark:text-green-100' : 'bg-red-100 text-red-700 dark:bg-red-700 dark:text-red-100' }}"
                        :class="(showEntity?.is_active ? 'bg-green-100 text-green-700 dark:bg-green-700 dark:text-green-100' :
                            'bg-red-100 text-red-700 dark:bg-red-700 dark:text-red-100')"
                        x-text="showEntity?.is_active ? 'Activo' : 'Inactivo'">
                        {{ $active ? 'Activo' : 'Inactivo' }}
                    </span>
                </div>
                <div class="mt-0.5 text-xs text-gray-500 dark:text-gray-400 flex items-center gap-2">
                    <span class="flex items-center"><i
                            class="fas fa-envelope text-purple-600 dark:text-purple-400 mr-1"></i>
                        <span
                            x-text="showEntity?.email || '-'">{{ optional($e)->email ?? (data_get($se, 'email', '-') ?: '-') }}</span>
                    </span>
                    <span class="flex items-center"><i
                            class="fas fa-phone text-purple-600 dark:text-purple-400 mr-1"></i>
                        <span
                            x-text="showEntity?.phone || '-'">{{ optional($e)->formatted_phone ?? (data_get($se, 'phone', '-') ?: '-') }}</span>
                    </span>
                </div>
            </div>
        </div>

        <div class="mt-3 grid grid-cols-2 md:grid-cols-3 gap-2">
            <div class="p-2 rounded border dark:border-gray-700">
                <p class="text-[10px] uppercase tracking-wide text-gray-500 dark:text-gray-400">Cédula</p>
                <p class="text-sm font-semibold text-gray-800 dark:text-gray-100 flex items-center"><i
                        class="fas fa-id-card text-purple-600 dark:text-purple-400 mr-2"></i><span
                        x-text="showEntity?.identity_card || '-'">{{ optional($e)->formatted_identity_card ?? (data_get($se, 'identity_card', '-') ?: '-') }}</span>
                </p>
            </div>
            <div class="p-2 rounded border dark:border-gray-700">
                <p class="text-[10px] uppercase tracking-wide text-gray-500 dark:text-gray-400">RUC</p>
                <p class="text-sm font-semibold text-gray-800 dark:text-gray-100 flex items-center"><i
                        class="fas fa-id-badge text-purple-600 dark:text-purple-400 mr-2"></i><span
                        x-text="showEntity?.ruc || '-'">{{ optional($e)->ruc ?? (data_get($se, 'ruc', '-') ?: '-') }}</span>
                </p>
            </div>
            <div class="p-2 rounded border dark:border-gray-700">
                <p class="text-[10px] uppercase tracking-wide text-gray-500 dark:text-gray-400">Municipio</p>
                <p class="text-sm font-semibold text-gray-800 dark:text-gray-100 flex items-center"><i
                        class="fas fa-map-marked-alt text-purple-600 dark:text-purple-400 mr-2"></i><span
                        x-text="showEntity?.municipality || '-'">{{ data_get($e, 'municipality.name') ?? (data_get($se, 'municipality', '-') ?: '-') }}</span>
                </p>
            </div>
            <div class="p-2 rounded border dark:border-gray-700">
                <p class="text-[10px] uppercase tracking-wide text-gray-500 dark:text-gray-400">Cliente</p>
                @php($isClient = optional($e)->is_client ?? (bool) data_get($se, 'is_client', false))
                <p class="text-sm font-semibold text-gray-800 dark:text-gray-100"><span
                        class="px-2 py-0.5 rounded-full {{ $isClient ? 'bg-green-100 text-green-700 dark:bg-green-700 dark:text-green-100' : 'bg-red-100 text-red-700 dark:bg-red-700 dark:text-red-100' }}"
                        :class="(showEntity?.is_client ? 'bg-green-100 text-green-700 dark:bg-green-700 dark:text-green-100' :
                            'bg-red-100 text-red-700 dark:bg-red-700 dark:text-red-100')"
                        x-text="showEntity?.is_client ? 'Sí' : 'No'">{{ $isClient ? 'Sí' : 'No' }}</span></p>
            </div>
            <div class="p-2 rounded border dark:border-gray-700">
                <p class="text-[10px] uppercase tracking-wide text-gray-500 dark:text-gray-400">Proveedor</p>
                @php($isSupplier = optional($e)->is_supplier ?? (bool) data_get($se, 'is_supplier', false))
                <p class="text-sm font-semibold text-gray-800 dark:text-gray-100"><span
                        class="px-2 py-0.5 rounded-full {{ $isSupplier ? 'bg-green-100 text-green-700 dark:bg-green-700 dark:text-green-100' : 'bg-red-100 text-red-700 dark:bg-red-700 dark:text-red-100' }}"
                        :class="(showEntity?.is_supplier ? 'bg-green-100 text-green-700 dark:bg-green-700 dark:text-green-100' :
                            'bg-red-100 text-red-700 dark:bg-red-700 dark:text-red-100')"
                        x-text="showEntity?.is_supplier ? 'Sí' : 'No'">{{ $isSupplier ? 'Sí' : 'No' }}</span></p>
            </div>
        </div>

        <div class="mt-3 grid grid-cols-1 md:grid-cols-2 gap-2">
            <div class="p-2 rounded bg-gray-50 dark:bg-gray-900">
                <p class="text-[10px] uppercase tracking-wide text-gray-500 dark:text-gray-400 mb-1">Contacto</p>
                <div class="space-y-0.5 text-xs text-gray-700 dark:text-gray-300">
                    <p class="flex items-center"><span class="w-24 text-gray-500 dark:text-gray-400">Dirección:</span>
                        <span class="font-medium"
                            x-text="showEntity?.address || '-'">{{ optional($e)->address ?? (data_get($se, 'address', '-') ?: '-') }}</span>
                    </p>
                    <p class="flex items-center"><span class="w-24 text-gray-500 dark:text-gray-400">Correo:</span>
                        <span class="font-medium"
                            x-text="showEntity?.email || '-'">{{ optional($e)->email ?? (data_get($se, 'email', '-') ?: '-') }}</span>
                    </p>
                    <p class="flex items-center"><span class="w-24 text-gray-500 dark:text-gray-400">Teléfono:</span>
                        <span class="font-medium"
                            x-text="showEntity?.phone || '-'">{{ optional($e)->formatted_phone ?? (data_get($se, 'phone', '-') ?: '-') }}</span>
                    </p>
                </div>
            </div>
            <div class="p-2 rounded bg-gray-50 dark:bg-gray-900">
                <p class="text-[10px] uppercase tracking-wide text-gray-500 dark:text-gray-400 mb-1">Trazabilidad</p>
                <div class="grid grid-cols-1 gap-1 text-[11px] text-gray-600 dark:text-gray-400">
                    <p class="flex items-center"><i
                            class="fas fa-calendar-alt text-purple-600 dark:text-purple-400 mr-2"></i><span
                            class="font-medium mr-1">Creado:</span> <span
                            x-text="showEntity?.formatted_created_at || '-'">{{ optional($e)->formatted_created_at ?? (data_get($se, 'formatted_created_at', '-') ?: '-') }}</span>
                    </p>
                    <p class="flex items-center"><i
                            class="fas fa-clock text-purple-600 dark:text-purple-400 mr-2"></i><span
                            class="font-medium mr-1">Actualizado:</span> <span
                            x-text="showEntity?.formatted_updated_at || '-'">{{ optional($e)->formatted_updated_at ?? (data_get($se, 'formatted_updated_at', '-') ?: '-') }}</span>
                    </p>
                </div>
            </div>
            <div class="md:col-span-2 p-2 rounded bg-gray-50 dark:bg-gray-900"
                x-show="(showEntity?.description||'') !== ''">
                <p class="text-[10px] uppercase tracking-wide text-gray-500 dark:text-gray-400 mb-1">Descripción</p>
                <p class="text-xs text-gray-700 dark:text-gray-300" x-text="showEntity?.description || ''">
                    {{ optional($e)->description ?? data_get($se, 'description', '') }}</p>
            </div>
        </div>
    </div>
</div>
