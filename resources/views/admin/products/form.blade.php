<div
    class="px-4 py-3 mb-8 bg-white rounded-lg shadow-md dark:bg-gray-800 border border-gray-200 dark:border-gray-700 w-full">
    <!-- Nombre y Imagen -->
    <div class="mb-4 grid grid-cols-1 md:grid-cols-2 gap-4">
        <label class="block text-sm w-full md:col-span-2">
            <span class="text-gray-700 dark:text-gray-400">Nombre</span>
            <div class="relative text-gray-500 focus-within:text-purple-600 dark:focus-within:text-purple-400">
                <input name="name" type="text"
                    class="block w-full pl-10 mt-1 text-sm dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 form-input focus:border-purple-400 focus:shadow-outline-purple dark:focus:shadow-outline-gray @error('name') border-red-600 @enderror"
                    placeholder="Nombre..."
                    @if (isset($alpine) && $alpine) x-model="editProduct.name" :value="editProduct.name"
                    @else
                        value="{{ old('name', $product->name ?? '') }}" @endif
                    required />
                <div class="absolute inset-y-0 flex items-center ml-3 pointer-events-none">
                    <i class="fas fa-box w-5 h-5"></i>
                </div>
            </div>
            @error('name')
                <span class="text-xs text-red-600 dark:text-red-400">{{ $message }}</span>
            @enderror
        </label>
    </div>
    <div class="mb-4">
        <label class="block text-sm w-full">
            <span class="text-gray-700 dark:text-gray-400">Imagen</span>
            <div
                class="relative flex items-center text-gray-500 focus-within:text-purple-600 dark:focus-within:text-purple-400 mt-1">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <i class="fas fa-image w-5 h-5"></i>
                </div>
                <input name="image" type="file" accept="image/*"
                    class="block w-full pl-10 text-sm dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 form-input file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100 focus:border-purple-400 focus:shadow-outline-purple dark:focus:shadow-outline-gray @error('image') border-red-600 @enderror"
                    id="{{ isset($alpine) && $alpine ? 'imageInputEdit' : 'imageInputCreate' }}" />
            </div>
            <div class="mt-2">
                <img id="{{ isset($alpine) && $alpine ? 'imagePreviewEdit' : 'imagePreviewCreate' }}"
                    @if (isset($alpine) && $alpine) :src="editProduct.image_url"
                        x-show="editProduct.image_url"
                    @else
                        src="{{ isset($product) && $product->image ? $product->image_url : '' }}"
                        style="display: {{ isset($product) && $product->image ? 'block' : 'none' }};" @endif
                    alt="Vista previa" width="80" class="rounded">
            </div>
            @error('image')
                <span class="text-xs text-red-600 dark:text-red-400">{{ $message }}</span>
            @enderror
        </label>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const pairs = [{
                    inputId: 'imageInputCreate',
                    previewId: 'imagePreviewCreate'
                },
                {
                    inputId: 'imageInputEdit',
                    previewId: 'imagePreviewEdit'
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
                                    // Alpine will handle visibility; just set src
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
            });
        });
    </script>

    <!-- Marca, Categoría, Impuesto -->
    <script>
        // Asegura que el mapa esté disponible globalmente antes de que Alpine lo use
        window.brandsByCategory = window.brandsByCategory || @json($brandsByCategory ?? []);
    </script>
    <div class="flex flex-col md:flex-row gap-4 mt-4">
        <label class="block text-sm w-full">
            <span class="text-gray-700 dark:text-gray-400">Categoría</span>
            <div class="relative text-gray-500 focus-within:text-purple-600 dark:focus-within:text-purple-400">
                <select id="{{ isset($alpine) && $alpine ? 'category_id_select_edit' : 'category_id_select_create' }}"
                    class="block w-full pl-10 mt-1 text-sm dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 form-select focus:border-purple-400 focus:shadow-outline-purple dark:focus:shadow-outline-gray"
                    @if (isset($alpine) && $alpine) x-model="editProduct.category_id" @endif>
                    <option value="">Seleccione</option>
                    @foreach ($categories as $id => $name)
                        <option value="{{ $id }}"
                            @if (!isset($alpine) || !$alpine) {{ old('category_id', old('category_id', optional($product->brand)->category_id ?? '')) == $id ? 'selected' : '' }} @endif>
                            {{ $name }}</option>
                    @endforeach
                </select>
                <div class="absolute inset-y-0 flex items-center ml-3 pointer-events-none">
                    <i class="fas fa-list-alt w-5 h-5"></i>
                </div>
            </div>
        </label>
        <label class="block text-sm w-full">
            <span class="text-gray-700 dark:text-gray-400">Marca</span>
            <div class="relative text-gray-500 focus-within:text-purple-600 dark:focus-within:text-purple-400">
                <select name="brand_id"
                    id="{{ isset($alpine) && $alpine ? 'brand_id_select_edit' : 'brand_id_select_create' }}"
                    class="block w-full pl-10 mt-1 text-sm dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 form-select focus:border-purple-400 focus:shadow-outline-purple dark:focus:shadow-outline-gray @error('brand_id') border-red-600 @enderror"
                    @if (isset($alpine) && $alpine) x-model="editProduct.brand_id" @endif required>
                    <option value="">Seleccione</option>
                    @php
                        $selectedCategory = old('category_id', optional($product->brand)->category_id ?? '');
                        $brandsForCategory = $brandsByCategory[$selectedCategory] ?? [];
                        $selectedBrand = old('brand_id', $product->brand_id ?? '');
                    @endphp
                    @foreach ($brandsForCategory as $id => $name)
                        <option value="{{ $id }}" {{ $selectedBrand == $id ? 'selected' : '' }}>
                            {{ $name }}</option>
                    @endforeach
                </select>
                <div class="absolute inset-y-0 flex items-center ml-3 pointer-events-none">
                    <i class="fas fa-tags w-5 h-5"></i>
                </div>
            </div>
            @error('brand_id')
                <span class="text-xs text-red-600 dark:text-red-400">{{ $message }}</span>
            @enderror
        </label>
        <label class="block text-sm w-full">
            <span class="text-gray-700 dark:text-gray-400">Unidad de Medida</span>
            <div class="relative text-gray-500 focus-within:text-purple-600 dark:focus-within:text-purple-400">
                <select name="unit_measure_id"
                    class="block w-full pl-10 mt-1 text-sm dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 form-select focus:border-purple-400 focus:shadow-outline-purple dark:focus:shadow-outline-gray @error('unit_measure_id') border-red-600 @enderror"
                    @if (isset($alpine) && $alpine) x-model="editProduct.unit_measure_id" @endif required>
                    <option value="">Seleccione</option>
                    @foreach ($units as $id => $name)
                        <option value="{{ $id }}"
                            @if (!isset($alpine) || !$alpine) {{ old('unit_measure_id', $product->unit_measure_id ?? '') == $id ? 'selected' : '' }} @endif>
                            {{ $name }}</option>
                    @endforeach
                </select>
                <div class="absolute inset-y-0 flex items-center ml-3 pointer-events-none">
                    <i class="fas fa-ruler w-5 h-5"></i>
                </div>
            </div>
            @error('unit_measure_id')
                <span class="text-xs text-red-600 dark:text-red-400">{{ $message }}</span>
            @enderror
        </label>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            // Para el modal de edición, Alpine ya maneja x-model y la vista trae todas las marcas.
        });

        document.addEventListener('DOMContentLoaded', function() {
            // Poblar marcas en base a categoría para ambos modos (create/edit)
            const pairs = [{
                    catId: 'category_id_select_create',
                    brandId: 'brand_id_select_create',
                    preCat: '{{ old('category_id', optional($product->brand)->category_id ?? '') }}',
                    preBrand: '{{ old('brand_id', $product->brand_id ?? '') }}'
                },
                {
                    catId: 'category_id_select_edit',
                    brandId: 'brand_id_select_edit',
                    preCat: null,
                    preBrand: null
                },
            ];

            // window.brandsByCategory ya fue inyectado arriba

            function populateBrandsFor(catSelect, brandSelect, preselected) {
                if (!catSelect || !brandSelect) return;
                const map = window.brandsByCategory || {};
                const options = map[catSelect.value] || {};
                const current = brandSelect.value;
                brandSelect.innerHTML = '<option value="">Seleccione</option>';
                Object.entries(options).forEach(([id, name]) => {
                    const opt = document.createElement('option');
                    opt.value = id;
                    opt.textContent = name;
                    brandSelect.appendChild(opt);
                });
                const toSelect = preselected ?? current;
                if (toSelect && brandSelect.querySelector(`option[value="${toSelect}"]`)) {
                    brandSelect.value = String(toSelect);
                    brandSelect.dispatchEvent(new Event('change'));
                }
            }

            pairs.forEach(({
                catId,
                brandId,
                preCat,
                preBrand
            }) => {
                const catSelect = document.getElementById(catId);
                const brandSelect = document.getElementById(brandId);
                if (!catSelect || !brandSelect) return;
                // Si hay categoría precargada, poblar
                const initialCat = preCat ?? catSelect.value;
                if (initialCat) {
                    populateBrandsFor(catSelect, brandSelect, preBrand);
                }
                catSelect.addEventListener('change', function() {
                    populateBrandsFor(catSelect, brandSelect, null);
                });
            });
        });
    </script>

    <!-- Unidad de Medida, Proveedor, Estado -->
    <div class="flex flex-col md:flex-row gap-4 mt-4">
        <label class="block text-sm w-full">
            <span class="text-gray-700 dark:text-gray-400">Proveedor</span>
            <div class="relative text-gray-500 focus-within:text-purple-600 dark:focus-within:text-purple-400">
                <select name="entity_id"
                    class="block w-full pl-10 mt-1 text-sm dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 form-select focus:border-purple-400 focus:shadow-outline-purple dark:focus:shadow-outline-gray @error('entity_id') border-red-600 @enderror"
                    @if (isset($alpine) && $alpine) x-model="editProduct.entity_id" @endif required>
                    <option value="">Seleccione</option>
                    @foreach ($entities as $id => $name)
                        <option value="{{ $id }}"
                            @if (!isset($alpine) || !$alpine) {{ old('entity_id', $product->entity_id ?? '') == $id ? 'selected' : '' }} @endif>
                            {{ $name }}</option>
                    @endforeach
                </select>
                <div class="absolute inset-y-0 flex items-center ml-3 pointer-events-none">
                    <i class="fas fa-ruler w-5 h-5"></i>
                </div>
            </div>
            @error('entity_id')
                <span class="text-xs text-red-600 dark:text-red-400">{{ $message }}</span>
            @enderror
        </label>
        <label class="block text-sm w-full">
            <span class="text-gray-700 dark:text-gray-400">Impuesto</span>
            <div class="relative text-gray-500 focus-within:text-purple-600 dark:focus-within:text-purple-400">
                <select name="tax_id"
                    class="block w-full pl-10 mt-1 text-sm dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 form-select focus:border-purple-400 focus:shadow-outline-purple dark:focus:shadow-outline-gray @error('tax_id') border-red-600 @enderror"
                    @if (isset($alpine) && $alpine) x-model="editProduct.tax_id" @endif required>
                    <option value="">Seleccione</option>
                    @foreach ($taxes as $id => $name)
                        <option value="{{ $id }}"
                            @if (!isset($alpine) || !$alpine) {{ old('tax_id', $product->tax_id ?? '') == $id ? 'selected' : '' }} @endif>
                            {{ $name }}</option>
                    @endforeach
                </select>
                <div class="absolute inset-y-0 flex items-center ml-3 pointer-events-none">
                    <i class="fas fa-percent w-5 h-5"></i>
                </div>
            </div>
            @error('tax_id')
                <span class="text-xs text-red-600 dark:text-red-400">{{ $message }}</span>
            @enderror
        </label>
    </div>

    <!-- Descripción -->
    <label class="block mt-4 text-sm w-full">
        <span class="text-gray-700 dark:text-gray-400">Descripción</span>
        <div class="relative text-gray-500 focus-within:text-purple-600 dark:focus-within:text-purple-400">
            <textarea name="description"
                class="block w-full pl-10 mt-1 text-sm dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 form-textarea focus:border-purple-400 focus:shadow-outline-purple dark:focus:shadow-outline-gray @error('description') border-red-600 @enderror"
                rows="2" maxlength="255" placeholder="Descripción..."
                @if (isset($alpine) && $alpine) x-model="editProduct.description" @endif>
@if (!isset($alpine) || !$alpine)
{{ old('description', $product->description ?? '') }}
@endif
</textarea>
            <div class="absolute inset-y-0 left-0 flex items-center ml-3 pointer-events-none">
                <i class="fas fa-align-left w-5 h-5"></i>
            </div>
        </div>
        @error('description')
            <span class="text-xs text-red-600 dark:text-red-400">{{ $message }}</span>
        @enderror
    </label>

    <!-- Variantes: solo color y talla -->
    <x-admin.purchases.variants-lines :colors="$colors ?? []" :sizes="$sizes ?? []" :old-details="old('details')" :prefill-details="$prefillDetails ?? []"
        :only-color-size="true" />

    <!-- Submit Button -->
    <div class="mt-6">
        <x-ui.submit-button :data-label="isset($color) ? 'Actualizar' : 'Guardar'" />

    </div>

</div>
