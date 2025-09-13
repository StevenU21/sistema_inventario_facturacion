<div x-data="{
    movementType: @js(old('movement_type', 'adjustment')),
    productId: @js(old('product_id', $inventory->productVariant->product_id ?? '')),
    variantId: @js(old('product_variant_id', $inventory->product_variant_id ?? '')),
    variantsByProduct: @js($variantsByProduct)
}"
     x-effect="if(!productId||!(variantsByProduct[productId]||[]).some(v=>String(v.id)===String(variantId))){variantId='';}">
    <div class="px-4 py-3 mb-8 bg-white rounded-lg shadow-md dark:bg-gray-800">
        <h3 class="text-lg font-semibold mb-4 text-gray-700 dark:text-gray-200">Registrar Movimiento</h3>
        <div class="flex flex-col md:flex-row gap-4">
            <label class="block text-sm w-full">
                <span class="text-gray-700 dark:text-gray-400">Producto</span>
                <div class="relative text-gray-500 focus-within:text-purple-600 dark:focus-within:text-purple-400">
                    <select x-model="productId" name="product_id" class="block w-full min-w-[200px] pl-10 mt-1 text-sm dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 form-select focus:border-purple-400 focus:shadow-outline-purple dark:focus:shadow-outline-gray">
                        <option value="">Seleccione</option>
                        @foreach ($products as $id => $name)
                            <option value="{{ $id }}" {{ old('product_id', $inventory->productVariant->product_id ?? '') == $id ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 flex items-center ml-3 pointer-events-none">
                        <i class="fas fa-box w-5 h-5"></i>
                    </div>
                </div>
            </label>
            <label class="block text-sm w-full">
                <span class="text-gray-700 dark:text-gray-400">Variante</span>
                <div class="relative text-gray-500 focus-within:text-purple-600 dark:focus-within:text-purple-400">
                    <select x-model="variantId" name="product_variant_id" required class="block w-full min-w-[260px] pl-10 mt-1 text-sm dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 form-select focus:border-purple-400 focus:shadow-outline-purple dark:focus:shadow-outline-gray @error('product_variant_id') border-red-600 @enderror">
                        <option value="">Seleccione</option>
                        <template x-for="variant in (variantsByProduct[productId] || [])" :key="variant.id">
                            <option :value="variant.id" x-text="variant.label" :selected="variantId == variant.id"></option>
                        </template>
                    </select>
                    <div class="absolute inset-y-0 flex items-center ml-3 pointer-events-none">
                        <i class="fas fa-box w-5 h-5"></i>
                    </div>
                </div>
                @error('product_variant_id')
                    <span class="text-xs text-red-600 dark:text-red-400">{{ $message }}</span>
                @enderror
            </label>
            <!-- Movement type as radio card toggles (like purchases new/existing) -->
            <div class="w-full">
                <span class="block text-sm text-gray-700 dark:text-gray-400 mb-1">Tipo de movimiento</span>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <label class="relative cursor-pointer">
                        <input type="radio" name="movement_type" value="adjustment" x-model="movementType" class="sr-only" />
                        <div class="rounded-lg border p-4 h-full transition-colors select-none"
                            :class="movementType === 'adjustment' ? 'border-purple-500 ring-2 ring-purple-200 dark:ring-purple-900/50 bg-purple-50 dark:bg-purple-900/10' : 'border-gray-200 dark:border-gray-700'">
                            <div class="flex items-start gap-3">
                                <div class="flex h-10 w-10 items-center justify-center rounded-md bg-purple-600 text-white">
                                    <i class="fas fa-sliders-h"></i>
                                </div>
                                <div>
                                    <div class="font-semibold text-gray-800 dark:text-gray-100">Ajuste</div>
                                    <div class="text-sm text-gray-600 dark:text-gray-300">Modifica el stock actual en un almacén.</div>
                                </div>
                            </div>
                        </div>
                    </label>
                    <label class="relative cursor-pointer">
                        <input type="radio" name="movement_type" value="transfer" x-model="movementType" class="sr-only" />
                        <div class="rounded-lg border p-4 h-full transition-colors select-none"
                            :class="movementType === 'transfer' ? 'border-purple-500 ring-2 ring-purple-200 dark:ring-purple-900/50 bg-purple-50 dark:bg-purple-900/10' : 'border-gray-200 dark:border-gray-700'">
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
            </div>
        </div>
    <!-- Campos dinámicos -->
    <fieldset x-bind:disabled="movementType !== 'adjustment'" x-show="movementType === 'adjustment'">
            @include('components.adjust_inventory', [
                'warehouses' => $warehouses,
                'inventory' => $inventory ?? null,
            ])
    </fieldset>
    <fieldset x-bind:disabled="movementType !== 'transfer'" x-show="movementType === 'transfer'">
            @include('components.transfer_inventory', [
                'warehouses' => $warehouses,
                'inventory' => $inventory ?? null,
            ])
    </fieldset>

        <!-- Botón enviar -->
        <div class="mt-6">
            <button type="submit"
                class="flex items-center px-4 py-2 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-purple-600 border border-transparent rounded-lg hover:bg-purple-700 focus:outline-none focus:shadow-outline-purple active:bg-purple-600">
                <i class="fas fa-paper-plane mr-2"></i> {{ isset($inventory) ? 'Actualizar' : 'Guardar' }}
            </button>
        </div>
    </div>
</div>
