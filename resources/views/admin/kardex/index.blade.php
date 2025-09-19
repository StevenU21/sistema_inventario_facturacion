@extends('layouts.app')
@section('title', 'Kardex')

@section('content')
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Breadcrumbs -->
        <nav class="mt-4 mb-2 text-sm text-gray-500 dark:text-gray-400" aria-label="Breadcrumb">
            <ol class="flex items-center gap-2">
                <li>
                    <a href="#" class="hover:text-gray-700 dark:hover:text-gray-200 transition-colors">
                        <i class="fas fa-home mr-1"></i> Modulo de Inventario
                    </a>
                </li>
                <li class="text-gray-400">/</li>
                <li>
                    <span class="text-gray-700 dark:text-gray-200">Kardex</span>
                </li>
            </ol>
        </nav>

        <style>
            .animate-gradient {
                background-image: linear-gradient(90deg, #c026d3, #7c3aed, #4f46e5, #c026d3);
                background-size: 300% 100%;
                animation: gradientShift 8s linear infinite alternate;
                filter: saturate(1.2) contrast(1.05);
                will-change: background-position;
            }

            @keyframes gradientShift {
                0% {
                    background-position: 100% 50%;
                }

                100% {
                    background-position: 0% 50%;
                }
            }

            @media (prefers-reduced-motion: reduce) {
                .animate-gradient {
                    animation: none;
                }
            }
        </style>

        <!-- Page header card -->
        <section
            class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-purple-600 to-indigo-600 shadow-lg animate-gradient">
            <div class="absolute inset-0 opacity-20 pointer-events-none"
                style="background-image: radial-gradient(ellipse at top left, rgba(255,255,255,.35), transparent 40%), radial-gradient(ellipse at bottom right, rgba(0,0,0,.25), transparent 40%);">
            </div>
            <div class="relative p-6 sm:p-8">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight flex items-center">
                            <i class="fas fa-clipboard-list text-white/90 mr-3"></i>
                            Kardex de Inventario
                        </h1>
                        <p class="mt-1 text-white/80 text-sm">Genera y exporta el informe por producto, rango y método.</p>
                    </div>
                    <div class="flex items-center gap-2">
                        @if ($kardexModel)
                            <a href="{{ route('kardex.export', request()->all()) }}" target="_blank"
                                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-white/10 hover:bg-white/15 text-white text-sm font-medium backdrop-blur transition">
                                <i class="fas fa-file-pdf"></i>
                                Exportar PDF
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </section>

        <!-- Mensajes de éxito -->
        <div class="mt-4">
            <x-session-message />
        </div>

        <!-- Info helper card -->
        <section class="mt-4 rounded-xl bg-white dark:bg-gray-800 shadow-md p-4 sm:p-5">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200">¿Qué significa cada método?</h3>
            <ul class="list-disc pl-5 mt-2 text-sm text-gray-700 dark:text-gray-300 space-y-1">
                <li><strong>Costo Promedio Ponderado (CPP):</strong> Cada salida se valora al costo promedio de todas las
                    existencias hasta ese momento.</li>
                <li><strong>PEPS (FIFO):</strong> Las salidas se valoran al costo de las primeras entradas (las más
                    antiguas).</li>
                <li><strong>UEPS (LIFO):</strong> Las salidas se valoran al costo de las últimas entradas (las más
                    recientes).</li>
            </ul>
        </section>

        <!-- Script moved BEFORE component usage to avoid 'kardexComponent is not defined' -->
        <script>
            // Definir función global antes de que Alpine procese x-data
            function kardexComponent() {
                return {
                    filters: {
                        product_id: null,
                        warehouse_id: null,
                        color_id: null,
                        size_id: null,
                        category_id: null,
                        brand_id: null,
                        metodo: 'cpp',
                        from: null,
                        to: null,
                        product_variant_id: null,
                    },
                    // Snapshot para restablecer filtros originales
                    get initialFilters() {
                        return {
                            product_id: null,
                            warehouse_id: null,
                            color_id: null,
                            size_id: null,
                            category_id: null,
                            brand_id: null,
                            metodo: 'cpp',
                            from: null,
                            to: null,
                            product_variant_id: null,
                        };
                    },
                    // result siempre será un objeto para evitar errores al acceder a .rows en el template
                    result: { rows: [], final: null },
                    loading: false,
                    errors: [],
                    hasGenerated: false, // controla visibilidad de la sección de resultados
                    clearAll() {
                        this.filters = { ...this.initialFilters };
                        this.result = { rows: [], final: null };
                        this.errors = [];
                        this.hasGenerated = false;
                        // Disparar evento global para que el variant-picker se limpie
                        window.dispatchEvent(new CustomEvent('kardex-clear'));
                    },
                    async generate() {
                        this.errors = [];
                        // Validaciones mínimas
                        if (!this.filters.product_variant_id && !this.filters.product_id) {
                            this.errors.push('Seleccione una variante de producto.');
                        }
                        if (!this.filters.from) {
                            this.errors.push('La fecha Desde es obligatoria.');
                        }
                        if (this.errors.length) return;
                        this.loading = true;
                        try {
                            const res = await fetch("{{ route('kardex.generate') }}", {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: JSON.stringify(this.filters)
                            });
                            const text = await res.text();
                            if (!res.ok) {
                                console.error('Request failed', res.status, text);
                                this.errors.push('Error al generar el kardex.');
                                return;
                            }
                            try {
                                const parsed = JSON.parse(text);
                                // Normalizar estructura para evitar result.rows undefined/null
                                this.result = {
                                    ...parsed,
                                    rows: Array.isArray(parsed?.rows) ? parsed.rows : (Array.isArray(parsed?.data) ? parsed.data : []),
                                    final: parsed?.final || null,
                                };
                                this.hasGenerated = true;
                            } catch (err) {
                                console.error('Invalid JSON response:', text);
                                this.errors.push('Respuesta inválida del servidor.');
                            }
                        } catch (e) {
                            console.error('Fetch error:', e);
                            this.errors.push('Error de red al generar.');
                        } finally {
                            this.loading = false;
                        }
                    }
                }
            }
        </script>
        <!-- Filtros -->
        <section class="mt-4 rounded-xl bg-white dark:bg-gray-800 shadow-md p-4 sm:p-5" x-data="kardexComponent()" x-init="
            window.addEventListener('kardex-variant-picked', e => { filters.product_variant_id = e.detail.product_variant_id; filters.product_id = e.detail.product_id; });
        ">
            <form @submit.prevent="generate" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3 items-end">
                <!-- Selector de variante: ocupa toda la fila -->
                <div class="sm:col-span-2 lg:col-span-6 mb-4">
                    <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-200 mb-2">Selecciona la variante</h3>
                    <x-kardex.variant-picker
                        :colors="$colors ?? []"
                        :sizes="$sizes ?? []"
                        :product-id="$productId ?? null"
                        :entities="$entities ?? []"
                        :categories="$categories ?? []"
                        :brands="$brands ?? []"
                    />
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wide text-gray-600 dark:text-gray-300 mb-1">Seleccionar Kardex</label>
                    <select name="metodo" x-model="filters.metodo" class="block w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-sm text-gray-800 dark:text-gray-100 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                        <option value="cpp">Costo Promedio</option>
                        <option value="peps">PEPS (FIFO)</option>
                        <option value="ueps">UEPS (LIFO)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wide text-gray-600 dark:text-gray-300 mb-1">Desde</label>
                    <input type="date" x-model="filters.from" required class="block w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-sm text-gray-800 dark:text-gray-100 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500" />
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wide text-gray-600 dark:text-gray-300 mb-1">Hasta</label>
                    <input type="date" x-model="filters.to" class="block w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-sm text-gray-800 dark:text-gray-100 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500" />
                </div>
                <div class="sm:col-span-2 lg:col-span-6 flex gap-2">
                    <button type="submit" class="inline-flex items-center justify-center gap-2 px-4 py-2 w-full sm:w-auto text-sm font-semibold rounded-lg transition-colors bg-purple-600 hover:bg-purple-700 text-white shadow" :disabled="loading">
                        <span x-show="!loading" class="inline-flex items-center gap-2"><i class="fas fa-cogs"></i> Generar</span>
                        <span x-show="loading" class="inline-flex items-center gap-2"><i class="fas fa-spinner fa-spin"></i> Procesando...</span>
                    </button>
                    <button type="button" @click="clearAll()" class="inline-flex items-center justify-center gap-2 px-4 py-2 w-full sm:w-auto text-sm font-medium rounded-lg bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200">
                        <i class="fas fa-undo"></i> Limpiar
                    </button>
                </div>
            </form>
            <template x-if="errors.length">
                <div class="mt-4 space-y-1">
                    <template x-for="(err,i) in errors" :key="i">
                        <div class="text-sm text-red-600 dark:text-red-400" x-text="err"></div>
                    </template>
                </div>
            </template>
            <div class="mt-4" x-show="hasGenerated" x-cloak>
                <div class="mb-4 text-gray-700 dark:text-gray-200">
                    <p><strong>Producto:</strong> <span x-text="(result && result.product) || ''"></span></p>
                    <p><strong>Almacén:</strong> <span x-text="(result && result.warehouse) ? result.warehouse : 'Todos'"></span></p>
                    <p><strong>Rango:</strong> <span x-text="(result && result.date_from) || ''"></span> a <span x-text="(result && result.date_to) || ''"></span></p>
                    <p><strong>Método:</strong> <span x-text="(result && result.method) || ''"></span></p>
                </div>
                <div class="overflow-x-auto border border-gray-200 dark:border-gray-700 rounded-lg">
                    <table class="min-w-full text-left text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-800">
                            <tr class="text-xs font-semibold tracking-wide text-gray-600 dark:text-gray-300 uppercase border-b border-gray-200 dark:border-gray-700">
                                <th class="px-4 py-3">Fecha y hora</th>
                                <th class="px-4 py-3">Concepto</th>
                                <th class="px-4 py-3">Almacén</th>
                                <th class="px-4 py-3 text-right">Entrada (Cant.)</th>
                                <th class="px-4 py-3 text-right">Salida (Cant.)</th>
                                <th class="px-4 py-3 text-right">Existencias</th>
                                <th class="px-4 py-3 text-right">Costo unitario</th>
                                <th class="px-4 py-3 text-right">Costo promedio</th>
                                <th class="px-4 py-3 text-right">Debe</th>
                                <th class="px-4 py-3 text-right">Haber</th>
                                <th class="px-4 py-3 text-right">Saldo</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700 bg-white dark:bg-gray-800">
                            <template x-if="!result || !result.rows || result.rows.length===0">
                                <tr><td colspan="11" class="px-4 py-3">Sin movimientos en el rango.</td></tr>
                            </template>
                            <template x-for="(r,i) in result.rows" :key="i">
                                <tr class="text-gray-700 dark:text-gray-300 hover:bg-gray-50/60 dark:hover:bg-gray-700/50 transition-colors">
                                    <td class="px-4 py-3 text-sm" x-text="r.date"></td>
                                    <td class="px-4 py-3 text-sm" x-text="r.concept || ''"></td>
                                    <td class="px-4 py-3 text-sm" x-text="r.warehouse"></td>
                                    <td class="px-4 py-3 text-sm text-right" x-text="r.entry_qty"></td>
                                    <td class="px-4 py-3 text-sm text-right" x-text="r.exit_qty"></td>
                                    <td class="px-4 py-3 text-sm text-right" x-text="r.balance_qty"></td>
                                    <td class="px-4 py-3 text-sm text-right" x-text="`C$ ${Number(r.unit_cost).toFixed(2)}`"></td>
                                    <td class="px-4 py-3 text-sm text-right" x-text="`C$ ${Number(r.avg_cost).toFixed(2)}`"></td>
                                    <td class="px-4 py-3 text-sm text-right" x-text="`C$ ${Number(r.debe).toFixed(2)}`"></td>
                                    <td class="px-4 py-3 text-sm text-right" x-text="`C$ ${Number(r.haber).toFixed(2)}`"></td>
                                    <td class="px-4 py-3 text-sm text-right" x-text="`C$ ${Number(r.saldo).toFixed(2)}`"></td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
                <div class="mt-4 text-gray-700 dark:text-gray-200" x-show="result && result.final">
                    <p><strong>Determinación final del inventario:</strong>
                        Unidades finales <span x-text="(result && result.final) ? result.final.qty : 0"></span> × Costo promedio
                        C$ <span x-text="(result && result.final) ? Number(result.final.unit_cost).toFixed(2) : '0.00'"></span>
                        = <strong>C$ <span x-text="(result && result.final) ? (result.final.qty * result.final.unit_cost).toFixed(2) : '0.00'"></span></strong>
                    </p>
                    <p>Saldo final reportado: <strong>C$ <span x-text="(result && result.final) ? Number(result.final.total).toFixed(2) : '0.00'"></span></strong></p>
                    <div class="mt-3">
                        <a :href="`{{ route('kardex.export') }}?product_id=${filters.product_id||''}&product_variant_id=${filters.product_variant_id||''}&from=${filters.from||''}&to=${filters.to||''}&metodo=${filters.metodo}`" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium">
                            <i class="fas fa-file-pdf"></i> Exportar PDF
                        </a>
                    </div>
                </div>
            </div>
        </section>

        @if ($kardexModel)
            <div class="mt-4 w-full overflow-hidden rounded-xl shadow-md bg-white dark:bg-gray-800">
                <div class="w-full overflow-x-auto p-4">
                    <div class="mb-4 text-gray-700 dark:text-gray-200">
                        <p><strong>Producto:</strong> {{ $kardexModel->product->name }}</p>
                        <p><strong>Almacén:</strong> {{ $kardexModel->warehouse->name ?? 'Todos' }}</p>
                        <p><strong>Rango:</strong> {{ $kardexModel->date_from }} a {{ $kardexModel->date_to }}</p>
                        <p><strong>Método:</strong>
                            @if (request('metodo', 'cpp') == 'cpp')
                                Costo Promedio Ponderado
                            @elseif(request('metodo') == 'peps')
                                PEPS (FIFO)
                            @elseif(request('metodo') == 'ueps')
                                UEPS (LIFO)
                            @endif
                        </p>
                    </div>
                    <table class="w-full text-left">
                        <thead class="bg-gray-50 dark:bg-gray-800">
                            <tr
                                class="text-xs font-semibold tracking-wide text-gray-600 dark:text-gray-300 uppercase border-b border-gray-200 dark:border-gray-700">
                                <th class="px-4 py-3">Fecha y hora</th>
                                <th class="px-4 py-3">Concepto</th>
                                <th class="px-4 py-3">Almacén</th>
                                <th class="px-4 py-3 text-right">Entrada (Cant.)</th>
                                <th class="px-4 py-3 text-right">Salida (Cant.)</th>
                                <th class="px-4 py-3 text-right">Existencias</th>
                                <th class="px-4 py-3 text-right">Costo unitario</th>
                                <th class="px-4 py-3 text-right">Costo promedio</th>
                                <th class="px-4 py-3 text-right">Debe</th>
                                <th class="px-4 py-3 text-right">Haber</th>
                                <th class="px-4 py-3 text-right">Saldo</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700 bg-white dark:bg-gray-800">
                            @forelse ($kardexModel->rows as $r)
                                <tr
                                    class="text-gray-700 dark:text-gray-300 hover:bg-gray-50/60 dark:hover:bg-gray-700/50 transition-colors">
                                    <td class="px-4 py-3 text-sm">{{ $r['date'] }}</td>
                                    <td class="px-4 py-3 text-sm">{{ $r['concept'] ?? '' }}</td>
                                    <td class="px-4 py-3 text-sm">{{ $r['warehouse'] }}</td>
                                    <td class="px-4 py-3 text-sm text-right">{{ $r['entry_qty'] }}</td>
                                    <td class="px-4 py-3 text-sm text-right">{{ $r['exit_qty'] }}</td>
                                    <td class="px-4 py-3 text-sm text-right">{{ $r['balance_qty'] }}</td>
                                    <td class="px-4 py-3 text-sm text-right">C$ {{ number_format($r['unit_cost'], 2) }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-right">C$ {{ number_format($r['avg_cost'], 2) }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-right">C$ {{ number_format($r['debe'], 2) }}</td>
                                    <td class="px-4 py-3 text-sm text-right">C$ {{ number_format($r['haber'], 2) }}</td>
                                    <td class="px-4 py-3 text-sm text-right">C$ {{ number_format($r['saldo'], 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="px-4 py-3" colspan="11">Sin movimientos en el rango.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="mt-4 text-gray-700 dark:text-gray-200">
                        <p><strong>Determinación final del inventario:</strong>
                            Unidades finales {{ $kardexModel->final['qty'] }} × Costo promedio
                            C$ {{ number_format($kardexModel->final['unit_cost'], 2) }}
                            =
                            <strong>C$
                                {{ number_format($kardexModel->final['qty'] * $kardexModel->final['unit_cost'], 2) }}</strong>
                        </p>
                        <p>Saldo final reportado: <strong>C$ {{ number_format($kardexModel->final['total'], 2) }}</strong>
                        </p>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection

@push('scripts')<!-- (Definición movida arriba) -->@endpush
