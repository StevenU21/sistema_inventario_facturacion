@props([
    'name' => 'search',
    'value' => '',
    'placeholder' => 'Buscar...',
    'url' => null,
    'min' => 2,
    'debounce' => 250,
])

@php
    $id = $attributes->get('id') ?: 'ac_' . \Illuminate\Support\Str::random(6);
@endphp

<div x-data="autocompleteComponent({ url: '{{ $url }}', min: {{ (int) $min }}, debounce: {{ (int) $debounce }}, initial: @js($value) })" @click.away="open = false" {{ $attributes->merge(['class' => 'relative w-full']) }}>
    <input
        id="{{ $id }}"
        name="{{ $name }}"
        type="text"
        x-model="query"
        x-on:input="onInput"
        x-on:keydown.arrow-down.prevent="highlightNext()"
        x-on:keydown.arrow-up.prevent="highlightPrev()"
        x-on:keydown.enter.prevent="applyHighlighted()"
        placeholder="{{ $placeholder }}"
        class="block w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-sm text-gray-800 dark:text-gray-100 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
        autocomplete="off"
    />

    <template x-if="open && suggestions.length">
        <ul class="absolute z-50 mt-1 w-full bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-md shadow-lg max-h-60 overflow-auto">
            <template x-for="(item, index) in suggestions" :key="item.text">
                <li
                    :class="{'bg-purple-50 dark:bg-gray-700': index === highlighted}"
                    class="px-3 py-2 text-sm text-gray-800 dark:text-gray-100 cursor-pointer hover:bg-purple-50 dark:hover:bg-gray-700"
                    x-on:click="select(item)"
                >
                    <span x-text="item.text"></span>
                    <span x-show="item.type" class="ml-2 text-xs text-gray-400" x-text="'(' + item.type + ')'"/>
                </li>
            </template>
        </ul>
    </template>
</div>

@once
<script>
    function autocompleteComponent({ url, min, debounce, initial }) {
        return {
            query: initial || '',
            open: false,
            suggestions: [],
            highlighted: -1,
            timer: null,
            onInput(e) {
                this.highlighted = -1;
                if (!url) return;
                const q = this.query.trim();
                if (q.length < min) {
                    this.open = false;
                    this.suggestions = [];
                    return;
                }
                clearTimeout(this.timer);
                this.timer = setTimeout(async () => {
                    try {
                        const params = new URLSearchParams({ q });
                        const res = await fetch(`${url}?${params.toString()}`, { headers: { 'Accept': 'application/json' } });
                        if (!res.ok) throw new Error('Network');
                        const data = await res.json();
                        this.suggestions = (data.data || []).slice(0, 10);
                        this.open = this.suggestions.length > 0;
                    } catch (err) {
                        this.open = false;
                        this.suggestions = [];
                    }
                }, debounce);
            },
            select(item) {
                this.query = item.text || '';
                this.open = false;
                this.suggestions = [];
                // Submit surrounding form if any
                const form = this.$el.closest('form');
                if (form) {
                    if (typeof form.requestSubmit === 'function') form.requestSubmit();
                    else form.submit();
                }
            },
            highlightNext() {
                if (!this.open) return;
                this.highlighted = (this.highlighted + 1) % this.suggestions.length;
            },
            highlightPrev() {
                if (!this.open) return;
                this.highlighted = (this.highlighted - 1 + this.suggestions.length) % this.suggestions.length;
            },
            applyHighlighted() {
                if (!this.open || this.highlighted < 0) return;
                this.select(this.suggestions[this.highlighted]);
            }
        }
    }
</script>
@endonce
