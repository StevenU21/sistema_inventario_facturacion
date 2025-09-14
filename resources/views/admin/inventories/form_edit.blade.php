<div x-data="{ mode: @js(old('movement_type', 'adjustment')), productId: @js(old('product_id', $inventory->productVariant->product_id ?? '')), variantId: @js(old('product_variant_id', $inventory->product_variant_id ?? '')), variantsByProduct: @js($variantsByProduct) }"
    class="px-4 py-3 mb-8 bg-white rounded-lg shadow-md dark:bg-gray-800 border border-gray-200 dark:border-gray-700 w-full"
    x-effect="if(!productId||!(variantsByProduct[productId]||[]).some(v=>String(v.id)===String(variantId))){variantId='';}">

    <style>[x-cloak]{display:none!important}</style>

    <hr class="my-6 border-gray-200 dark:border-gray-700">

    <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-200 mb-3">Movimiento de Inventario</h3>

    <!-- Selector de modo (ajuste/transferencia) -->
    <div class="mt-2 mb-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <label class="relative cursor-pointer" @click="mode='adjustment'">
                <input type="radio" name="movement_type" value="adjustment" x-model="mode" class="sr-only" />
                <div class="rounded-lg border p-4 h-full transition-colors select-none"
                    :class="mode === 'adjustment' ?
                        'border-purple-500 ring-2 ring-purple-200 dark:ring-purple-900/50 bg-purple-50 dark:bg-purple-900/10' :
                        'border-gray-200 dark:border-gray-700'">
                    <div class="flex items-start gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-md bg-purple-600 text-white">
                            <i class="fas fa-sliders-h"></i>
                        </div>
                        <div>
                            <div class="font-semibold text-gray-800 dark:text-gray-100">Ajuste</div>
                            <div class="text-sm text-gray-600 dark:text-gray-300">Modifica el stock actual en un
                                almacén.</div>
                        </div>
                    </div>
                </div>
            </label>
            <label class="relative cursor-pointer" @click="mode='transfer'">
                <input type="radio" name="movement_type" value="transfer" x-model="mode" class="sr-only" />
                <div class="rounded-lg border p-4 h-full transition-colors select-none"
                    :class="mode === 'transfer' ?
                        'border-purple-500 ring-2 ring-purple-200 dark:ring-purple-900/50 bg-purple-50 dark:bg-purple-900/10' :
                        'border-gray-200 dark:border-gray-700'">
                    <div class="flex items-start gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-md bg-purple-600 text-white">
                            <i class="fas fa-exchange-alt"></i>
                        </div>
                        <div>
                            <div class="font-semibold text-gray-800 dark:text-gray-100">Transferencia</div>
                            <div class="text-sm text-gray-600 dark:text-gray-300">Mueve stock entre almacenes.</div>
                        </div>
                    </div>
                </div>
            </label>
        </div>
        @error('movement_type')
            <div class="text-xs text-red-600 dark:text-red-400 mt-1">{{ $message }}</div>
        @enderror

        <!-- Mantener ambos fieldsets en el DOM, pero mostrar/ocultar y deshabilitar el inactivo para evitar duplicaciones -->
        <fieldset x-show="mode==='adjustment'" x-cloak :disabled="mode!=='adjustment'">
            @include('components.adjust_inventory', [
                'warehouses' => $warehouses,
                'inventory' => $inventory ?? null,
            ])
        </fieldset>

        <fieldset x-show="mode==='transfer'" x-cloak :disabled="mode!=='transfer'">
            @include('components.transfer_inventory', [
                'warehouses' => $warehouses,
                'inventory' => $inventory ?? null,
            ])
        </fieldset>
    </div>

    <div class="mt-6 flex gap-2">
        <x-ui.submit-button :data-label="isset($color) ? 'Actualizar' : 'Guardar'" />
    </div>
</div>
