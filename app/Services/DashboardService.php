<?php

namespace App\Services;

use App\Models\Entity;
use App\Models\Inventory;
use App\Models\InventoryMovement;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

/**
 * DashboardService
 *
 * Encapsula la lógica de agregación y KPIs del dashboard en métodos de única responsabilidad.
 * Facilita testeo, reutilización y mantenimiento.
 */
class DashboardService
{
    protected string $driver;
    protected string $coalesceDate;
    protected string $netTotalExpr; // Expresión neta sin impuestos

    public function __construct()
    {
        $this->driver = DB::getDriverName();
        $this->coalesceDate = 'COALESCE(sale_date, created_at)';
        $this->netTotalExpr = '(total - COALESCE(tax_amount,0))';
        Carbon::setLocale('es');
    }

    /* ===================== Helpers base ===================== */
    protected function monthGroupExpression(): string
    {
        switch ($this->driver) {
            case 'sqlite':
                return "strftime('%Y-%m', {$this->coalesceDate})";
            case 'pgsql':
                return "to_char({$this->coalesceDate}, 'YYYY-MM')";
            default:
                return "DATE_FORMAT({$this->coalesceDate}, '%Y-%m')"; // mysql / mariadb
        }
    }

    protected function hourExpression(): string
    {
        switch ($this->driver) {
            case 'sqlite':
                return "CAST(strftime('%H', created_at) AS integer)";
            case 'pgsql':
                return 'EXTRACT(hour from created_at)';
            default:
                return 'HOUR(created_at)';
        }
    }

    protected function clientNameExpression(): string
    {
        return $this->driver === 'sqlite'
            ? "entities.first_name || ' ' || entities.last_name"
            : "CONCAT(entities.first_name, ' ', entities.last_name)";
    }

    protected function now(): Carbon
    {
        return now();
    }

    /* ===================== Bloques de única responsabilidad ===================== */

    public function getBasicSummary(): array
    {
        return [
            'products' => Product::count(),
            'entities' => Entity::where('is_client', 1)->count(),
            'inventoryTotal' => Inventory::sum('stock'),
            'movementsToday' => InventoryMovement::whereDate('created_at', $this->now()->toDateString())->count(),
        ];
    }

    public function getMonthlySales(int $months = 12): array
    {
        $now = $this->now();
        $months = max(1, $months);
        $start = $now->copy()->subMonths($months - 1)->startOfMonth();
        $groupExpr = $this->monthGroupExpression();

        $raw = Sale::select(
            DB::raw($groupExpr . ' as ym'),
            DB::raw('SUM(' . $this->netTotalExpr . ') as total')
        )
            ->whereRaw("{$this->coalesceDate} >= ?", [$start->toDateTimeString()])
            ->groupBy('ym')
            ->orderBy('ym')
            ->get();

        $labels = [];
        $totals = [];
        $cursor = $start->copy();
        while ($cursor <= $now->copy()->startOfMonth()) {
            $key = $cursor->format('Y-m');
            $label = ucfirst($cursor->locale('es')->translatedFormat('M Y'));
            $found = $raw->firstWhere('ym', $key);
            $labels[] = $label;
            $totals[] = $found ? round($found->total, 2) : 0;
            $cursor->addMonth();
        }

        $bestIdx = $totals ? array_search(max($totals), $totals) : null;
        return [
            'monthsLabels' => $labels,
            'monthsTotals' => $totals,
            'totalSales' => array_sum($totals),
            'bestMonthLabel' => $bestIdx !== null ? $labels[$bestIdx] : null,
            'bestMonthAmount' => $bestIdx !== null ? $totals[$bestIdx] : 0,
        ];
    }

    public function getHourlySales(): array
    {
        $now = $this->now();
        $hourExpr = $this->hourExpression();
        $raw = Sale::select(DB::raw($hourExpr . ' as h'), DB::raw('SUM(' . $this->netTotalExpr . ') as total'))
            ->whereDate('created_at', $now->toDateString())
            ->groupBy('h')
            ->orderBy('h')
            ->get();
        $labels = [];
        $totals = [];
        for ($h = 0; $h < 24; $h++) {
            $row = $raw->firstWhere('h', $h);
            $labels[] = str_pad($h, 2, '0', STR_PAD_LEFT) . ':00';
            $totals[] = $row ? round($row->total, 2) : 0;
        }
        return ['hoursLabels' => $labels, 'hoursTotals' => $totals];
    }

    public function getDailySales(int $days = 14): array
    {
        $now = $this->now();
        $days = max(1, $days);
        $start = $now->copy()->subDays($days - 1)->startOfDay();
        $raw = Sale::select(DB::raw('DATE(created_at) as d'), DB::raw('SUM(' . $this->netTotalExpr . ') as total'))
            ->where('created_at', '>=', $start)
            ->groupBy('d')
            ->orderBy('d')
            ->get();
        $labels = [];
        $totals = [];
        $cursor = $start->copy();
        while ($cursor <= $now->copy()->startOfDay()) {
            $k = $cursor->toDateString();
            $label = ucfirst($cursor->locale('es')->translatedFormat('d M'));
            $row = $raw->firstWhere('d', $k);
            $labels[] = $label;
            $totals[] = $row ? round($row->total, 2) : 0;
            $cursor->addDay();
        }
        return ['daysLabels' => $labels, 'daysTotals' => $totals];
    }

    public function getTopProducts(int $limit = 5, int $days = 30): Collection
    {
        $since = $this->now()->copy()->subDays($days)->startOfDay()->toDateTimeString();
        return SaleDetail::select(
            'product_variants.id as variant_id',
            'products.name as product_name',
            'colors.name as color_name',
            'sizes.name as size_name',
            DB::raw('SUM(sale_details.quantity) as qty_total'),
            DB::raw('SUM( (sale_details.unit_price * sale_details.quantity) - sale_details.discount_amount ) as revenue')
        )
            ->join('product_variants', 'sale_details.product_variant_id', '=', 'product_variants.id')
            ->join('products', 'product_variants.product_id', '=', 'products.id')
            ->leftJoin('colors', 'product_variants.color_id', '=', 'colors.id')
            ->leftJoin('sizes', 'product_variants.size_id', '=', 'sizes.id')
            ->join('sales', 'sale_details.sale_id', '=', 'sales.id')
            ->whereRaw("COALESCE(sales.sale_date, sales.created_at) >= ?", [$since])
            ->groupBy('variant_id', 'products.name', 'colors.name', 'sizes.name')
            ->orderByDesc('revenue')
            ->limit($limit)
            ->get();
    }

    public function getTopClients(int $limit = 5, int $days = 30): Collection
    {
        $since = $this->now()->copy()->subDays($days)->startOfDay()->toDateTimeString();
        $clientNameExpr = $this->clientNameExpression();
        return Sale::select(
            'entities.id',
            DB::raw($clientNameExpr . ' as client_name'),
            DB::raw('COUNT(sales.id) as sales_count'),
            DB::raw('SUM(' . $this->netTotalExpr . ') as total_amount')
        )
            ->join('entities', 'sales.entity_id', '=', 'entities.id')
            ->whereRaw("COALESCE(sales.sale_date, sales.created_at) >= ?", [$since])
            ->groupBy('entities.id', 'client_name')
            ->orderByDesc('total_amount')
            ->limit($limit)
            ->get();
    }

    public function getTopSellers(int $limit = 5, int $days = 30): Collection
    {
        $since = $this->now()->copy()->subDays($days)->startOfDay()->toDateTimeString();
        $raw = Sale::select('user_id', DB::raw('COUNT(id) as sales_count'), DB::raw('SUM(' . $this->netTotalExpr . ') as total_amount'))
            ->whereRaw("COALESCE(sale_date, created_at) >= ?", [$since])
            ->groupBy('user_id')
            ->orderByDesc('sales_count')
            ->limit($limit)
            ->get();
        if ($raw->isEmpty()) {
            return collect();
        }
        $users = User::whereIn('id', $raw->pluck('user_id'))->get()->keyBy('id');
        return $raw->map(function ($row) use ($users) {
            $user = $users->get($row->user_id);
            return [
                'user_id' => $row->user_id,
                'name' => $user ? $user->short_name : ('ID #' . $row->user_id),
                'sales_count' => $row->sales_count,
                'total_amount' => $row->total_amount,
            ];
        });
    }

    public function getCreditStats(): array
    {
        $totalCreditDue = DB::table('account_receivables')->sum('amount_due');
        $totalCreditPaid = DB::table('account_receivables')->sum('amount_paid');
        $totalCreditPending = $totalCreditDue - $totalCreditPaid;
        return compact('totalCreditDue', 'totalCreditPaid', 'totalCreditPending');
    }

    public function getClientDebtTop(int $limit = 5): array
    {
        $raw = DB::table('account_receivables')
            ->select('entity_id', DB::raw('SUM(amount_due - amount_paid) as debt'))
            ->groupBy('entity_id')
            ->havingRaw('debt > 0')
            ->orderByDesc('debt')
            ->limit($limit)
            ->get();
        $entities = $raw->isNotEmpty() ? Entity::whereIn('id', $raw->pluck('entity_id'))->get()->keyBy('id') : collect();
        $topDebtors = $raw->map(function ($row) use ($entities) {
            $entity = $entities->get($row->entity_id);
            $fullName = $entity ? trim(($entity->first_name ?? '') . ' ' . ($entity->last_name ?? '')) : ('ID #' . $row->entity_id);
            return [
                'entity_id' => $row->entity_id,
                'name' => $fullName,
                'debt' => round($row->debt, 2),
            ];
        });
        return [
            'topDebtors' => $topDebtors,
            'totalClientsDebt' => round($raw->sum('debt'), 2),
        ];
    }

    public function getQuickMetrics(): array
    {
        $now = $this->now();
        $todaySales = Sale::whereRaw("date({$this->coalesceDate}) = ?", [$now->toDateString()])
            ->selectRaw('SUM(' . $this->netTotalExpr . ') as net_total')
            ->value('net_total') ?? 0;
        $monthSales = Sale::whereRaw("{$this->coalesceDate} BETWEEN ? AND ?", [
            $now->copy()->startOfMonth()->toDateTimeString(),
            $now->copy()->endOfMonth()->toDateTimeString(),
        ])->selectRaw('SUM(' . $this->netTotalExpr . ') as net_total')->value('net_total') ?? 0;
        $yearSales = Sale::whereRaw("{$this->coalesceDate} BETWEEN ? AND ?", [
            $now->copy()->startOfYear()->toDateTimeString(),
            $now->copy()->endOfYear()->toDateTimeString(),
        ])->selectRaw('SUM(' . $this->netTotalExpr . ') as net_total')->value('net_total') ?? 0;
        return compact('todaySales', 'monthSales', 'yearSales');
    }

    public function getGrowthRate(): array
    {
        $now = $this->now();
        $monthSales = Sale::whereRaw("{$this->coalesceDate} BETWEEN ? AND ?", [
            $now->copy()->startOfMonth()->toDateTimeString(),
            $now->copy()->endOfMonth()->toDateTimeString(),
        ])->selectRaw('SUM(' . $this->netTotalExpr . ') as net_total')->value('net_total') ?? 0;
        $prevMonthSales = Sale::whereRaw("{$this->coalesceDate} BETWEEN ? AND ?", [
            $now->copy()->subMonth()->startOfMonth()->toDateTimeString(),
            $now->copy()->subMonth()->endOfMonth()->toDateTimeString(),
        ])->selectRaw('SUM(' . $this->netTotalExpr . ') as net_total')->value('net_total') ?? 0;
        $growthRate = ($prevMonthSales > 0)
            ? round((($monthSales - $prevMonthSales) / $prevMonthSales) * 100, 2)
            : null;
        return compact('growthRate', 'prevMonthSales', 'monthSales');
    }

    public function getCreditVsCashPercentages(): array
    {
        $now = $this->now();
        $start = $now->copy()->startOfMonth()->toDateTimeString();
        $end = $now->copy()->endOfMonth()->toDateTimeString();
        $base = Sale::whereRaw("{$this->coalesceDate} BETWEEN ? AND ?", [$start, $end]);
        $credit = (clone $base)->where('is_credit', 1)->selectRaw('SUM(' . $this->netTotalExpr . ') as net_total')->value('net_total') ?? 0;
        $cash = (clone $base)->where('is_credit', 0)->selectRaw('SUM(' . $this->netTotalExpr . ') as net_total')->value('net_total') ?? 0;
        $total = $credit + $cash;
        $percentCredit = $total > 0 ? round(($credit / $total) * 100, 2) : 0;
        $percentCash = $total > 0 ? round(($cash / $total) * 100, 2) : 0;
        return compact('percentCredit', 'percentCash', 'credit', 'cash');
    }

    public function getInventoryValues(): array
    {
        $inventoryValueCost = Inventory::select(DB::raw('SUM(stock * purchase_price) as total'))->value('total') ?? 0;
        $inventoryValueSale = Inventory::select(DB::raw('SUM(stock * sale_price) as total'))->value('total') ?? 0;
        return compact('inventoryValueCost', 'inventoryValueSale');
    }

    public function getMonthlyGrossMargin(): array
    {
        $now = $this->now();
        $start = $now->copy()->startOfMonth()->toDateTimeString();
        $end = $now->copy()->endOfMonth()->toDateTimeString();
        $saleIds = Sale::whereRaw("{$this->coalesceDate} BETWEEN ? AND ?", [$start, $end])->pluck('id');
        $details = $saleIds->isNotEmpty() ? SaleDetail::whereIn('sale_id', $saleIds)->get() : collect();

        $netSalesRevenue = $details->sum(function ($d) {
            return max(0, ($d->unit_price * $d->quantity) - $d->discount_amount); // neto sin impuesto
        });
        $variantIds = $details->pluck('product_variant_id')->unique();
        $inventoryGrouped = $variantIds->isNotEmpty() ? Inventory::whereIn('product_variant_id', $variantIds)->get()->groupBy('product_variant_id') : collect();
        $estimatedCost = 0;
        foreach ($details as $detail) {
            $group = $inventoryGrouped->get($detail->product_variant_id);
            if ($group && $group->count() > 0) {
                $totalStockForVariant = max(1, $group->sum('stock'));
                $weightedCost = $group->sum(function ($inv) {
                    return $inv->stock * $inv->purchase_price; }) / $totalStockForVariant;
                $estimatedCost += $weightedCost * $detail->quantity;
            }
        }
        $grossMarginAmount = $netSalesRevenue - $estimatedCost;
        $grossMarginPercent = $netSalesRevenue > 0 ? round(($grossMarginAmount / $netSalesRevenue) * 100, 2) : 0;
        return compact('grossMarginAmount', 'grossMarginPercent', 'netSalesRevenue', 'estimatedCost');
    }

    /* ===================== Agregador principal ===================== */
    public function getDashboardData(): array
    {
        $basic = $this->getBasicSummary();
        $monthly = $this->getMonthlySales();
        $hourly = $this->getHourlySales();
        $daily = $this->getDailySales();
        $topProducts = $this->getTopProducts();
        $topClients = $this->getTopClients();
        $topSellers = $this->getTopSellers();
        $creditStats = $this->getCreditStats();
        $debts = $this->getClientDebtTop();
        $quick = $this->getQuickMetrics();
        $growth = $this->getGrowthRate();
        $creditVsCash = $this->getCreditVsCashPercentages();
        $inventoryValues = $this->getInventoryValues();
        $grossMargin = $this->getMonthlyGrossMargin();

        $totalSellersProfit = collect($topSellers)->sum('total_amount');
        $totalSellersCount = collect($topSellers)->sum('sales_count');

        return array_merge(
            $basic,
            $monthly,
            $hourly,
            $daily,
            [
                'topProducts' => $topProducts,
                'topClients' => $topClients,
                'topSellers' => $topSellers,
                'totalSellersProfit' => $totalSellersProfit,
                'totalSellersCount' => $totalSellersCount,
            ],
            $quick,
            $growth,
            $creditStats,
            $debts,
            $creditVsCash,
            $inventoryValues,
            $grossMargin,
        );
    }
}
