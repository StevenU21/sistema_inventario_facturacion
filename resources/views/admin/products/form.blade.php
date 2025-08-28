    <div class="px-4 py-3 mb-8 bg-white rounded-lg shadow-md dark:bg-gray-800">
        <!-- Nombre y Imagen -->
        <div class="mb-4">
            <label class="block text-sm w-full">
                <span class="text-gray-700 dark:text-gray-400">Nombre</span>
                <div class="relative text-gray-500 focus-within:text-purple-600 dark:focus-within:text-purple-400">
                    <input name="name" type="text"
                        class="block w-full pl-10 mt-1 text-sm dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 form-input focus:border-purple-400 focus:shadow-outline-purple dark:focus:shadow-outline-gray @error('name') border-red-600 @enderror"
                        placeholder="Nombre..." value="{{ old('name', $product->name ?? '') }}" required />
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
                        class="block w-full pl-10 text-sm dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 form-input file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100 focus:border-purple-400 focus:shadow-outline-purple dark:focus:shadow-outline-gray @error('image') border-red-600 @enderror" />
                </div>
                @if (isset($product) && $product->image)
                    <img src="{{ $product->image_url }}" alt="Imagen actual" width="80" class="mt-2 rounded">
                @endif
                @error('image')
                    <span class="text-xs text-red-600 dark:text-red-400">{{ $message }}</span>
                @enderror
            </label>
        </div>

        <!-- Marca, Categoría, Impuesto -->
        <div class="flex flex-col md:flex-row gap-4 mt-4">
            <label class="block text-sm w-full">
                <span class="text-gray-700 dark:text-gray-400">Categoría</span>
                <div class="relative text-gray-500 focus-within:text-purple-600 dark:focus-within:text-purple-400">
                    <select name="category_id"
                        class="block w-full pl-10 mt-1 text-sm dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 form-select focus:border-purple-400 focus:shadow-outline-purple dark:focus:shadow-outline-gray @error('category_id') border-red-600 @enderror"
                        required>
                        <option value="">Seleccione</option>
                        @foreach (App\Models\Category::all() as $category)
                            <option value="{{ $category->id }}"
                                {{ old('category_id', $product->category_id ?? '') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}</option>
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 flex items-center ml-3 pointer-events-none">
                        <i class="fas fa-list-alt w-5 h-5"></i>
                    </div>
                </div>
                @error('category_id')
                    <span class="text-xs text-red-600 dark:text-red-400">{{ $message }}</span>
                @enderror
            </label>
            <label class="block text-sm w-full">
                <span class="text-gray-700 dark:text-gray-400">Marca</span>
                <div class="relative text-gray-500 focus-within:text-purple-600 dark:focus-within:text-purple-400">
                    <select name="brand_id"
                        class="block w-full pl-10 mt-1 text-sm dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 form-select focus:border-purple-400 focus:shadow-outline-purple dark:focus:shadow-outline-gray @error('brand_id') border-red-600 @enderror"
                        required>
                        <option value="">Seleccione</option>
                        @foreach (App\Models\Brand::all() as $brand)
                            <option value="{{ $brand->id }}"
                                {{ old('brand_id', $product->brand_id ?? '') == $brand->id ? 'selected' : '' }}>
                                {{ $brand->name }}</option>
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
                        required>
                        <option value="">Seleccione</option>
                        @foreach (App\Models\UnitMeasure::all() as $unit)
                            <option value="{{ $unit->id }}"
                                {{ old('unit_measure_id', $product->unit_measure_id ?? '') == $unit->id ? 'selected' : '' }}>
                                {{ $unit->name }}</option>
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

        <label class="block text-sm w-full">
            <span class="text-gray-700 dark:text-gray-400">Entidad</span>
            <div class="relative text-gray-500 focus-within:text-purple-600 dark:focus-within:text-purple-400">
                <select name="entity_id"
                    class="block w-full pl-10 mt-1 text-sm dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 form-select focus:border-purple-400 focus:shadow-outline-purple dark:focus:shadow-outline-gray @error('entity_id') border-red-600 @enderror"
                    required>
                    <option value="">Seleccione</option>
                    @foreach (App\Models\Entity::all() as $entity)
                        <option value="{{ $entity->id }}"
                            {{ old('entity_id', $product->entity_id ?? '') == $entity->id ? 'selected' : '' }}>
                            {{ $entity->name }}</option>
                    @endforeach
                </select>
                <div class="absolute inset-y-0 flex items-center ml-3 pointer-events-none">
                    <i class="fas fa-building w-5 h-5"></i>
                </div>
            </div>
            @error('entity_id')
                <span class="text-xs text-red-600 dark:text-red-400">{{ $message }}</span>
            @enderror
        </label>

        <!-- Precios y Stock -->
        <div class="flex flex-col md:flex-row gap-4 mt-4">
            <label class="block text-sm w-full">
                <span class="text-gray-700 dark:text-gray-400">Precio Compra</span>
                <div class="relative text-gray-500 focus-within:text-purple-600 dark:focus-within:text-purple-400">
                    <input name="purchase_price" type="number" min="0" step="0.01"
                        class="block w-full pl-10 mt-1 text-sm dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 form-input focus:border-purple-400 focus:shadow-outline-purple dark:focus:shadow-outline-gray @error('purchase_price') border-red-600 @enderror"
                        placeholder="Precio de compra..."
                        value="{{ old('purchase_price', $product->purchase_price ?? '') }}" required />
                    <div class="absolute inset-y-0 flex items-center ml-3 pointer-events-none">
                        <i class="fas fa-dollar-sign w-5 h-5"></i>
                    </div>
                </div>
                @error('purchase_price')
                    <span class="text-xs text-red-600 dark:text-red-400">{{ $message }}</span>
                @enderror
            </label>
            <label class="block text-sm w-full">
                <span class="text-gray-700 dark:text-gray-400">Precio Venta</span>
                <div class="relative text-gray-500 focus-within:text-purple-600 dark:focus-within:text-purple-400">
                    <input name="sale_price" type="number" min="0" step="0.01"
                        class="block w-full pl-10 mt-1 text-sm dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 form-input focus:border-purple-400 focus:shadow-outline-purple dark:focus:shadow-outline-gray @error('sale_price') border-red-600 @enderror"
                        placeholder="Precio de venta..." value="{{ old('sale_price', $product->sale_price ?? '') }}"
                        required />
                    <div class="absolute inset-y-0 flex items-center ml-3 pointer-events-none">
                        <i class="fas fa-dollar-sign w-5 h-5"></i>
                    </div>
                </div>
                @error('sale_price')
                    <span class="text-xs text-red-600 dark:text-red-400">{{ $message }}</span>
                @enderror
            </label>
            <label class="block text-sm w-full">
                <span class="text-gray-700 dark:text-gray-400">Stock</span>
                <div class="relative text-gray-500 focus-within:text-purple-600 dark:focus-within:text-purple-400">
                    <input name="stock" type="number" min="0"
                        class="block w-full pl-10 mt-1 text-sm dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 form-input focus:border-purple-400 focus:shadow-outline-purple dark:focus:shadow-outline-gray @error('stock') border-red-600 @enderror"
                        placeholder="Stock..." value="{{ old('stock', $product->stock ?? '') }}" required />
                    <div class="absolute inset-y-0 flex items-center ml-3 pointer-events-none">
                        <i class="fas fa-cubes w-5 h-5"></i>
                    </div>
                </div>
                @error('stock')
                    <span class="text-xs text-red-600 dark:text-red-400">{{ $message }}</span>
                @enderror
            </label>
            <label class="block text-sm w-full">
                <span class="text-gray-700 dark:text-gray-400">Stock Mínimo</span>
                <div class="relative text-gray-500 focus-within:text-purple-600 dark:focus-within:text-purple-400">
                    <input name="min_stock" type="number" min="0"
                        class="block w-full pl-10 mt-1 text-sm dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 form-input focus:border-purple-400 focus:shadow-outline-purple dark:focus:shadow-outline-gray @error('min_stock') border-red-600 @enderror"
                        placeholder="Stock mínimo..." value="{{ old('min_stock', $product->min_stock ?? '') }}"
                        required />
                    <div class="absolute inset-y-0 flex items-center ml-3 pointer-events-none">
                        <i class="fas fa-sort-numeric-down w-5 h-5"></i>
                    </div>
                </div>
                @error('min_stock')
                    <span class="text-xs text-red-600 dark:text-red-400">{{ $message }}</span>
                @enderror
            </label>
        </div>

        <!-- Unidad de Medida, Entidad, Estado -->
        <div class="flex flex-col md:flex-row gap-4 mt-4">
            <label class="block text-sm w-full">
                <span class="text-gray-700 dark:text-gray-400">Unidad de Medida</span>
                <div class="relative text-gray-500 focus-within:text-purple-600 dark:focus-within:text-purple-400">
                    <select name="unit_measure_id"
                        class="block w-full pl-10 mt-1 text-sm dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 form-select focus:border-purple-400 focus:shadow-outline-purple dark:focus:shadow-outline-gray @error('unit_measure_id') border-red-600 @enderror"
                        required>
                        <option value="">Seleccione</option>
                        @foreach (App\Models\UnitMeasure::all() as $unit)
                            <option value="{{ $unit->id }}"
                                {{ old('unit_measure_id', $product->unit_measure_id ?? '') == $unit->id ? 'selected' : '' }}>
                                {{ $unit->name }}</option>
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
            <label class="block text-sm w-full">
                <span class="text-gray-700 dark:text-gray-400">Impuesto</span>
                <div class="relative text-gray-500 focus-within:text-purple-600 dark:focus-within:text-purple-400">
                    <select name="tax_id"
                        class="block w-full pl-10 mt-1 text-sm dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 form-select focus:border-purple-400 focus:shadow-outline-purple dark:focus:shadow-outline-gray @error('tax_id') border-red-600 @enderror"
                        required>
                        <option value="">Seleccione</option>
                        @foreach (App\Models\Tax::all() as $tax)
                            <option value="{{ $tax->id }}"
                                {{ old('tax_id', $product->tax_id ?? '') == $tax->id ? 'selected' : '' }}>
                                {{ $tax->name }}</option>
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
            <label class="block text-sm w-full">
                <span class="text-gray-700 dark:text-gray-400">Estado</span>
                <div class="relative text-gray-500 focus-within:text-purple-600 dark:focus-within:text-purple-400">
                    <select name="product_status_id"
                        class="block w-full pl-10 mt-1 text-sm dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 form-select focus:border-purple-400 focus:shadow-outline-purple dark:focus:shadow-outline-gray @error('product_status_id') border-red-600 @enderror">
                        <option value="">Seleccione</option>
                        @foreach (App\Models\ProductStatus::all() as $status)
                            <option value="{{ $status->id }}"
                                {{ old('product_status_id', $product->product_status_id ?? '') == $status->id ? 'selected' : '' }}>
                                {{ $status->name }}</option>
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 flex items-center ml-3 pointer-events-none">
                        <i class="fas fa-check w-5 h-5"></i>
                    </div>
                </div>
                @error('product_status_id')
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
                    rows="2" maxlength="255" placeholder="Descripción...">{{ old('description', $product->description ?? '') }}</textarea>
                <div class="absolute inset-y-0 left-0 flex items-center ml-3 pointer-events-none">
                    <i class="fas fa-align-left w-5 h-5"></i>
                </div>
            </div>
            @error('description')
                <span class="text-xs text-red-600 dark:text-red-400">{{ $message }}</span>
            @enderror
        </label>

        <!-- Submit Button -->
        <div class="mt-6">
            <button type="submit"
                class="flex items-center px-4 py-2 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-purple-600 border border-transparent rounded-lg hover:bg-purple-700 focus:outline-none focus:shadow-outline-purple active:bg-purple-600">
                <i class="fas fa-paper-plane mr-2"></i> {{ isset($municipality) ? 'Actualizar' : 'Guardar' }}
            </button>
        </div>
    </div>
