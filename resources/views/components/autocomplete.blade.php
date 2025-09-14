@props([
    'name' => 'search',
    'value' => '',
    'placeholder' => 'Buscar...',
    'url' => null,
    'min' => 2,
    'debounce' => 250,
    'submit' => true, // si true, busca y envía el form al seleccionar
    'event' => null,  // si se provee, despacha este evento personalizado al seleccionar
])

@php
    $id = $attributes->get('id') ?: 'ac_' . \Illuminate\Support\Str::random(6);
@endphp

<div x-data="autocompleteComponent({ url: '{{ $url }}', min: {{ (int) $min }}, debounce: {{ (int) $debounce }}, initial: @js($value), submit: {{ $submit ? 'true' : 'false' }}, event: @js($event) })" x-modelable="query" @click.away="open = false" {{ $attributes->merge(['class' => 'relative w-full z-30']) }}>
    <input id="{{ $id }}" name="{{ $name }}" type="text" x-model="query" x-ref="input"
        x-on:input="onInput" x-on:keydown.arrow-down.prevent="highlightNext()"
        x-on:keydown.arrow-up.prevent="highlightPrev()" x-on:keydown.enter.prevent="applyHighlighted()"
        placeholder="{{ $placeholder }}"
    class="block w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-sm text-gray-800 dark:text-gray-100 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 relative z-40"
        autocomplete="off" />

    <template x-if="open">
        <ul
            class="absolute z-50 mt-1 w-full bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-md shadow-lg max-h-60 overflow-auto">
            <template x-for="(item, index) in suggestions" :key="item.id ?? item.text">
                <li :class="{ 'bg-purple-50 dark:bg-gray-700': index === highlighted }"
                    class="px-3 py-2 text-sm text-gray-800 dark:text-gray-100 cursor-pointer hover:bg-purple-50 dark:hover:bg-gray-700"
                    x-on:click="select(item)">
                    <span x-text="item.text"></span>
                    <span x-show="item.type" class="ml-2 text-xs text-gray-400" x-text="'(' + item.type + ')'" />
                </li>
            </template>
            <template x-if="!suggestions.length">
                <li class="px-3 py-2 text-sm text-gray-500 dark:text-gray-400">Sin coincidencias</li>
            </template>
        </ul>
    </template>
</div>

@once
    <script>
        function autocompleteComponent({ url, min, debounce, initial, submit, event }) {
            return {
                query: initial || '',
                open: false,
                suggestions: [],
                highlighted: -1,
                timer: null,
                async fetchData(q) {
                    if (!url) return [];
                    const params = new URLSearchParams({ q, term: q, search: q });
                    try {
                        const res = await fetch(`${url}?${params.toString()}` , {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            credentials: 'same-origin'
                        });
                        if (!res.ok) {
                            console.warn('Autocomplete request failed', res.status, res.statusText);
                            return [];
                        }
                        const contentType = (res.headers.get('content-type') || '').toLowerCase();
                        if (!contentType.includes('application/json')) {
                            console.warn('Autocomplete response is not JSON');
                            return [];
                        }
                        const data = await res.json();
                        const items = (data?.data ?? data ?? []);
                        return items
                            .map(it => ({ id: it?.id ?? it?.value ?? it?.text, text: it?.text ?? it?.name ?? it?.label ?? String(it) }))
                            .filter(it => it.text && it.text.length);
                    } catch (err) {
                        console.error('Autocomplete fetch error', err);
                        return [];
                    }
                },
                onInput(e) {
                    this.highlighted = -1;
                    const q = this.query.trim();
                    // Propaga el valor hacia el padre usando un evento personalizado que hace bubble
                    if (this.$el) {
                        this.$el.dispatchEvent(new CustomEvent('ac-input', { bubbles: true, detail: this.query }));
                    }
                    if (q.length < min) {
                        this.open = false;
                        this.suggestions = [];
                        return;
                    }
                    clearTimeout(this.timer);
                    this.timer = setTimeout(async () => {
                        try {
                            this.suggestions = await this.fetchData(q);
                            this.open = true;
                        } catch (err) {
                            this.open = false;
                            this.suggestions = [];
                        }
                    }, debounce);
                },
                select(item) {
                    const value = item.text || '';
                    this.query = value;
                    this.open = false;
                    this.suggestions = [];
                    this.highlighted = -1;
                    // Propaga el valor hacia el padre con un evento personalizado
                    if (this.$el) {
                        this.$el.dispatchEvent(new CustomEvent('ac-input', { bubbles: true, detail: value }));
                    }
                    this.$nextTick(() => {
                        if (this.$refs.input) this.$refs.input.value = value;
                        if (event && this.$el) {
                            this.$el.dispatchEvent(new CustomEvent(event, { bubbles: true, detail: { text: value, item } }));
                        }
                        if (submit) {
                            const form = this.$el.closest('form');
                            if (form) {
                                if (typeof form.requestSubmit === 'function') form.requestSubmit();
                                else form.submit();
                            }
                        }
                    });
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
