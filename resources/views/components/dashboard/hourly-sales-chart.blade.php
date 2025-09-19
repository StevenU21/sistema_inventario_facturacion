<div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-5 shadow">
    <div class="flex items-start justify-between mb-4">
        <div>
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200 tracking-wide">Ventas por Hora</h3>
            <p class="text-xs text-gray-500 dark:text-gray-400">Hoy</p>
        </div>
    </div>
    <div class="chart-box"><canvas id="chartHourly"></canvas></div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const canvas = document.getElementById('chartHourly');
    if(!canvas || !window.Chart) return;
    const ctx = canvas.getContext('2d');
    const labels = @json($labels);
    const totals = @json($totals);
    const baseTicks = '#94a3b8';
    const baseGrid = 'rgba(148,163,184,0.15)';
    const fontFamily = 'Inter, system-ui, sans-serif';
    const moneyFmt = v => 'C$ ' + new Intl.NumberFormat('es-NI',{minimumFractionDigits:2,maximumFractionDigits:2}).format(v);
    new Chart(ctx, {
        type: 'line',
        data: { labels, datasets: [{ label:'Ventas', data: totals, fill:true, tension:.35, borderColor:'rgba(34,197,94,0.9)', backgroundColor:'rgba(34,197,94,0.18)', pointRadius:3, pointBackgroundColor:'rgba(34,197,94,1)', pointBorderWidth:0 }]},
        options:{
            responsive:true, maintainAspectRatio:false, interaction:{mode:'index',intersect:false}, plugins:{legend:{display:false}, tooltip:{backgroundColor:'rgba(30,41,59,0.9)',titleColor:'#f1f5f9',bodyColor:'#e2e8f0',callbacks:{label:c=>moneyFmt(c.parsed.y)}}},
            scales:{x:{grid:{color:baseGrid},ticks:{color:baseTicks,font:{family:fontFamily}}}, y:{grid:{color:baseGrid},ticks:{color:baseTicks,font:{family:fontFamily},callback:v=>'C$'+v}}}
        }
    });
});
</script>
@endpush
