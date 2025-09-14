<button
    type="submit"
    x-data="{ loading: false }"
    x-bind:disabled="loading"
    @click.prevent="
        if (!loading) {
            loading = true;
            $el.closest('form').submit();
        }
    "
    class="flex items-center px-4 py-2 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-purple-600 border border-transparent rounded-lg hover:bg-purple-700 focus:outline-none focus:shadow-outline-purple active:bg-purple-600 disabled:opacity-60 disabled:cursor-not-allowed"
>
    <span class="mr-2 flex items-center">
        <i class="fas fa-paper-plane"></i>
        <template x-if="loading">
            <svg class="animate-spin h-4 w-4 ml-2 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
            </svg>
        </template>
    </span>
    <span x-text="$el.dataset.label || 'Guardar'"></span>
</button>
