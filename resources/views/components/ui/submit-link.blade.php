<a x-data="{ loading: false }"
    :class="['inline-flex items-center w-full transition-colors duration-150', loading ? 'opacity-60 cursor-not-allowed' : '',
        $el.dataset.activeClass || ''
    ]"
    :href="loading ? null : $el.dataset.href"
    @click.prevent="
        if (!loading) {
            loading = true;
            window.location = $el.dataset.href;
        }
    "
    :disabled="loading" :aria-disabled="loading.toString()" data-href="{{ $href ?? '#' }}"
    data-active-class="{{ $activeClass ?? '' }}"
    class="inline-flex items-center w-full transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200">
    @if (isset($icon))
        <i class="{{ $icon }} w-5 h-5"></i>
    @endif
    {{ $slot }}
</a>
