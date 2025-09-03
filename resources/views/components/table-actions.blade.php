<div class="flex items-center space-x-4 text-sm">
    <a href="{{ $show }}"
       class="flex items-center justify-between px-2 py-2 text-sm font-medium leading-5 text-purple-600 rounded-lg dark:text-gray-400 focus:outline-none focus:shadow-outline-gray"
       aria-label="Ver">
        <i class="fas fa-eye"></i>
    </a>
    <a href="{{ $edit }}"
       class="flex items-center justify-between px-2 py-2 text-sm font-medium leading-5 text-purple-600 rounded-lg dark:text-gray-400 focus:outline-none focus:shadow-outline-gray"
       aria-label="Editar">
        <i class="fas fa-edit"></i>
    </a>
    <form action="{{ $delete }}" method="POST"
          onsubmit="return confirm('¿Estás seguro de eliminar este registro?');">
        @csrf
        @method('DELETE')
        <button type="submit"
                class="flex items-center justify-between px-2 py-2 text-sm font-medium leading-5 text-purple-600 rounded-lg dark:text-gray-400 focus:outline-none focus:shadow-outline-gray"
                aria-label="Eliminar">
            <i class="fas fa-trash"></i>
        </button>
    </form>
</div>
