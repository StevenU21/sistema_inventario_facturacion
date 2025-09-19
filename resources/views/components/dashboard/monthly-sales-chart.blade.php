<div class="xl:col-span-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-5 shadow">
    <div class="flex items-start justify-between mb-4">
        <div>
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200 tracking-wide">Ventas por Mes</h3>
            <p class="text-xs text-gray-500 dark:text-gray-400">Últimos 12 meses</p>
        </div>
    </div>
    <div class="chart-box"><canvas id="chartMonthly"></canvas></div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const canvas = document.getElementById('chartMonthly');
    if(!canvas || !window.Chart) return;
    const ctx = canvas.getContext('2d');
    // Etiquetas de meses ya en español gracias a Carbon labels
    const labels = @json($labels);
    const totals = @json($totals);
    const baseGrid = 'rgba(148,163,184,0.15)';
    const baseTicks = '#94a3b8';
    const fontFamily = 'Inter, system-ui, sans-serif';
    const moneyFmt = v => 'C$ ' + new Intl.NumberFormat('es-NI',{minimumFractionDigits:2,maximumFractionDigits:2}).format(v);
    const createBarGradient = (ctx) => { const g = ctx.createLinearGradient(0,0,0,ctx.canvas.height); g.addColorStop(0,'rgba(16,185,129,0.9)'); g.addColorStop(1,'rgba(16,185,129,0.25)'); return g; };
    const gradient = createBarGradient(ctx);
    new Chart(ctx, {
        type: 'bar',
        data: { labels, datasets: [{ label: 'Ventas', data: totals, borderRadius: 6, backgroundColor: gradient, borderWidth:0 }]},
        options: {
            responsive:true, maintainAspectRatio:false,
            plugins:{ legend:{display:false}, tooltip:{backgroundColor:'rgba(30,41,59,0.9)',titleColor:'#f1f5f9',bodyColor:'#e2e8f0',callbacks:{label: c=> moneyFmt(c.parsed.y)}}},
            interaction:{mode:'index',intersect:false},
            scales:{
                x:{grid:{color:baseGrid},ticks:{color:baseTicks,font:{family:fontFamily}}},
                y:{grid:{color:baseGrid},ticks:{color:baseTicks,font:{family:fontFamily},callback:v=>'C$'+v},beginAtZero:true}
            }
        }
    });
});
</script>
@endpush
