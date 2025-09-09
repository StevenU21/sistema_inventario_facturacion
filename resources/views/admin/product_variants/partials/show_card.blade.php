<div class="w-full overflow-hidden rounded-lg bg-white dark:bg-gray-800 shadow-md">
  <div class="p-3">
    <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
      <div class="p-2 rounded border dark:border-gray-700">
        <p class="text-[10px] uppercase tracking-wide text-gray-500 dark:text-gray-400">SKU</p>
        <p class="text-sm font-semibold text-gray-800 dark:text-gray-100 flex items-center">
          <i class="fas fa-barcode text-purple-600 dark:text-purple-400 mr-2"></i>
          <span x-text="showVariant.sku"></span>
        </p>
      </div>
      <div class="p-2 rounded border dark:border-gray-700">
        <p class="text-[10px] uppercase tracking-wide text-gray-500 dark:text-gray-400">Código</p>
        <p class="text-sm font-semibold text-gray-800 dark:text-gray-100 flex items-center">
          <i class="fas fa-qrcode text-purple-600 dark:text-purple-400 mr-2"></i>
          <span x-text="showVariant.barcode"></span>
        </p>
      </div>
      <div class="p-2 rounded border dark:border-gray-700">
        <p class="text-[10px] uppercase tracking-wide text-gray-500 dark:text-gray-400">Producto</p>
        <p class="text-sm font-semibold text-gray-800 dark:text-gray-100 flex items-center">
          <i class="fas fa-box text-purple-600 dark:text-purple-400 mr-2"></i>
          <span x-text="showVariant.product"></span>
        </p>
      </div>
      <div class="p-2 rounded border dark:border-gray-700">
        <p class="text-[10px] uppercase tracking-wide text-gray-500 dark:text-gray-400">Color</p>
        <p class="text-sm font-semibold text-gray-800 dark:text-gray-100 flex items-center">
          <i class="fas fa-palette text-purple-600 dark:text-purple-400 mr-2"></i>
          <span x-text="showVariant.color || '-' "></span>
        </p>
      </div>
      <div class="p-2 rounded border dark:border-gray-700">
        <p class="text-[10px] uppercase tracking-wide text-gray-500 dark:text-gray-400">Talla</p>
        <p class="text-sm font-semibold text-gray-800 dark:text-gray-100 flex items-center">
          <i class="fas fa-ruler text-purple-600 dark:text-purple-400 mr-2"></i>
          <span x-text="showVariant.size || '-' "></span>
        </p>
      </div>
      <div class="p-2 rounded border dark:border-gray-700">
        <p class="text-[10px] uppercase tracking-wide text-gray-500 dark:text-gray-400">Fechas</p>
        <p class="text-sm font-semibold text-gray-800 dark:text-gray-100">
          <i class="far fa-clock text-purple-600 dark:text-purple-400 mr-2"></i>
          <span x-text="'Creado: ' + (showVariant.created_at || '-')"></span>
          <br />
          <span x-text="'Actualizado: ' + (showVariant.updated_at || '-')"></span>
        </p>
      </div>
    </div>
  </div>
</div>
