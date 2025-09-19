<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Entity;
use App\Models\Inventory;
use App\Models\InventoryMovement;
use App\Models\Product;
use App\Models\User;
use App\Models\Sale;
use App\Models\SaleDetail;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(\Illuminate\Http\Request $request)
    {
        $now = now();
        // Usar locale español para formatos de fecha
        Carbon::setLocale('es');
        $driver = DB::getDriverName();
        $coalesceDate = 'COALESCE(sale_date, created_at)';
        // Expresión de total neto SIN impuestos (impuestos no representan ganancia)
    $netTotalExpr = '(total - COALESCE(tax_amount,0))'; // usar en SUM() para métricas sin impuestos

        // Resumen básico existente
        $products = Product::count();
        $entities = Entity::where('is_client', 1)->count();
        $inventoryTotal = Inventory::sum('stock');
        $movementsToday = InventoryMovement::whereDate('created_at', $now->toDateString())->count();


        // Ventas por mes (últimos 12 meses) - compatibilidad multi driver (sqlite / mysql / pgsql)
        switch ($driver) {
            case 'sqlite':
                $groupExpr = "strftime('%Y-%m', $coalesceDate)"; // SQLite
                break;
            case 'pgsql':
                $groupExpr = "to_char($coalesceDate, 'YYYY-MM')"; // PostgreSQL
                break;
            default: // mysql, mariadb
                $groupExpr = "DATE_FORMAT($coalesceDate, '%Y-%m')";
        }

        $monthStart = $now->copy()->subMonths(11)->startOfMonth()->toDateTimeString();
        $salesByMonthRaw = Sale::select(
            DB::raw($groupExpr . ' as ym'),
            DB::raw('SUM(' . $netTotalExpr . ') as total')
        )
            ->whereRaw("$coalesceDate >= ?", [$monthStart])
            ->groupBy('ym')
            ->orderBy('ym')
            ->get();

        $monthsLabels = [];
        $monthsTotals = [];
        $cursor = $now->copy()->subMonths(11)->startOfMonth();
        while ($cursor <= $now->copy()->startOfMonth()) {
            $key = $cursor->format('Y-m');
            $label = ucfirst($cursor->locale('es')->translatedFormat('M Y'));
            $found = $salesByMonthRaw->firstWhere('ym', $key);
            $monthsLabels[] = $label;
            $monthsTotals[] = $found ? round($found->total, 2) : 0;
            $cursor->addMonth();
        }

        $totalSales = array_sum($monthsTotals);
        $bestMonthIndex = $monthsTotals ? array_search(max($monthsTotals), $monthsTotals) : null;
        $bestMonthLabel = $bestMonthIndex !== null ? $monthsLabels[$bestMonthIndex] : null;
        $bestMonthAmount = $bestMonthIndex !== null ? $monthsTotals[$bestMonthIndex] : 0;

        // Ventas por hora (hoy) - compatibilidad multi driver
        switch ($driver) {
            case 'sqlite':
                $hourExpr = "CAST(strftime('%H', created_at) AS integer)";
                break;
            case 'pgsql':
                $hourExpr = 'EXTRACT(hour from created_at)';
                break;
            default:
                $hourExpr = 'HOUR(created_at)';
        }
        $salesByHourRaw = Sale::select(
            DB::raw($hourExpr . ' as h'),
            DB::raw('SUM(' . $netTotalExpr . ') as total')
        )
            ->whereDate('created_at', $now->toDateString())
            ->groupBy('h')
            ->orderBy('h')
            ->get();
        $hoursLabels = [];
        $hoursTotals = [];
        for ($h = 0; $h < 24; $h++) {
            $row = $salesByHourRaw->firstWhere('h', $h);
            $hoursLabels[] = str_pad($h, 2, '0', STR_PAD_LEFT) . ':00';
            $hoursTotals[] = $row ? round($row->total, 2) : 0;
        }

        // Ventas por día (últimos 14 días)
    $salesByDayRaw = Sale::select(DB::raw('DATE(created_at) as d'), DB::raw('SUM(' . $netTotalExpr . ') as total'))
            ->where('created_at', '>=', $now->copy()->subDays(13)->startOfDay())
            ->groupBy('d')
            ->orderBy('d')
            ->get();
        $daysLabels = [];
        $daysTotals = [];
        $cursorDay = $now->copy()->subDays(13)->startOfDay();
        while ($cursorDay <= $now->copy()->startOfDay()) {
            $k = $cursorDay->toDateString();
            // Etiqueta en español (día mes abreviado)
            $label = ucfirst($cursorDay->locale('es')->translatedFormat('d M'));
            $row = $salesByDayRaw->firstWhere('d', $k);
            $daysLabels[] = $label;
            $daysTotals[] = $row ? round($row->total, 2) : 0;
            $cursorDay->addDay();
        }

        // Top productos (por ganancia = suma sub_total - discount_amount) últimos 30 días
        $topProducts = SaleDetail::select(
            'product_variants.id as variant_id',
            'products.name as product_name',
            'colors.name as color_name',
            'sizes.name as size_name',
            DB::raw('SUM(sale_details.quantity) as qty_total'),
            DB::raw('SUM(sale_details.sub_total - sale_details.discount_amount) as revenue')
        )
            ->join('product_variants', 'sale_details.product_variant_id', '=', 'product_variants.id')
            ->join('products', 'product_variants.product_id', '=', 'products.id')
            ->leftJoin('colors', 'product_variants.color_id', '=', 'colors.id')
            ->leftJoin('sizes', 'product_variants.size_id', '=', 'sizes.id')
            ->join('sales', 'sale_details.sale_id', '=', 'sales.id')
            ->whereRaw("COALESCE(sales.sale_date, sales.created_at) >= ?", [$now->copy()->subDays(30)->startOfDay()->toDateTimeString()])
            ->groupBy('variant_id', 'products.name', 'colors.name', 'sizes.name')
            ->orderByDesc('revenue')
            ->limit(5)
            ->get();

        // Top clientes (ventas últimos 30 días)
        // Expresión para concatenar nombre completo según driver
        if ($driver === 'sqlite') {
            $clientNameExpr = "entities.first_name || ' ' || entities.last_name";
        } else {
            $clientNameExpr = "CONCAT(entities.first_name, ' ', entities.last_name)";
        }
        $topClients = Sale::select(
            'entities.id',
            DB::raw($clientNameExpr . ' as client_name'),
            DB::raw('COUNT(sales.id) as sales_count'),
            DB::raw('SUM(' . $netTotalExpr . ') as total_amount')
        )
            ->join('entities', 'sales.entity_id', '=', 'entities.id')
            ->whereRaw("COALESCE(sales.sale_date, sales.created_at) >= ?", [$now->copy()->subDays(30)->startOfDay()->toDateTimeString()])
            ->groupBy('entities.id', 'client_name')
            ->orderByDesc('total_amount')
            ->limit(5)
            ->get();

        // Top vendedores (ventas últimos 30 días)
        $topSellersRaw = Sale::select(
            'user_id',
            DB::raw('COUNT(id) as sales_count'),
            DB::raw('SUM(' . $netTotalExpr . ') as total_amount')
        )
            ->whereRaw("COALESCE(sale_date, created_at) >= ?", [$now->copy()->subDays(30)->startOfDay()->toDateTimeString()])
            ->groupBy('user_id')
            ->orderByDesc('sales_count')
            ->limit(5)
            ->get();
        $topSellers = collect();
        if ($topSellersRaw->isNotEmpty()) {
            $userIds = $topSellersRaw->pluck('user_id');
            $usersMap = User::whereIn('id', $userIds)->get()->keyBy('id');
            $topSellers = $topSellersRaw->map(function ($row) use ($usersMap) {
                $user = $usersMap->get($row->user_id);
                $name = $user ? $user->short_name : ('ID #' . $row->user_id);
                return [
                    'user_id' => $row->user_id,
                    'name' => $name,
                    'sales_count' => $row->sales_count,
                    'total_amount' => $row->total_amount,
                ];
            });
        }

        // Total profit for top sellers
        $totalSellersProfit = $topSellers->sum('total_amount');

        // Métricas rápidas periódicas
        // Métricas rápidas utilizando COALESCE para contemplar registros sin sale_date
        $todaySales = Sale::whereRaw("date($coalesceDate) = ?", [$now->toDateString()])
            ->selectRaw('SUM(' . $netTotalExpr . ') as net_total')
            ->value('net_total') ?? 0;
        $monthSales = Sale::whereRaw("$coalesceDate BETWEEN ? AND ?", [
            $now->copy()->startOfMonth()->toDateTimeString(),
            $now->copy()->endOfMonth()->toDateTimeString(),
        ])->selectRaw('SUM(' . $netTotalExpr . ') as net_total')->value('net_total') ?? 0;
        $yearSales = Sale::whereRaw("$coalesceDate BETWEEN ? AND ?", [
            $now->copy()->startOfYear()->toDateTimeString(),
            $now->copy()->endOfYear()->toDateTimeString(),
        ])->selectRaw('SUM(' . $netTotalExpr . ') as net_total')->value('net_total') ?? 0;

        // Tasa de crecimiento (ventas mes actual vs mes anterior)
        $prevMonthSales = Sale::whereRaw("$coalesceDate BETWEEN ? AND ?", [
            $now->copy()->subMonth()->startOfMonth()->toDateTimeString(),
            $now->copy()->subMonth()->endOfMonth()->toDateTimeString(),
        ])->selectRaw('SUM(' . $netTotalExpr . ') as net_total')->value('net_total') ?? 0;
        $growthRate = ($prevMonthSales > 0)
            ? round((($monthSales - $prevMonthSales) / $prevMonthSales) * 100, 2)
            : null; // null si no hay base de comparación

        // Ventas cobradas vs pendientes (basado en cuentas por cobrar)
        // Consideramos ventas de crédito (is_credit = 1) vinculadas a account_receivables
        $totalCreditDue = DB::table('account_receivables')->sum('amount_due');
        $totalCreditPaid = DB::table('account_receivables')->sum('amount_paid');
        $totalCreditPending = $totalCreditDue - $totalCreditPaid; // puede ser 0 o más

        // Deuda total clientes y top deudores (TOP 5)
        $clientsDebtRaw = DB::table('account_receivables')
            ->select('entity_id', DB::raw('SUM(amount_due - amount_paid) as debt'))
            ->groupBy('entity_id')
            ->havingRaw('debt > 0')
            ->orderByDesc('debt')
            ->limit(5)
            ->get();
        $totalClientsDebt = $clientsDebtRaw->sum('debt');

        // Obtener nombres clientes para top deudores
        $topDebtors = collect();
        if ($clientsDebtRaw->isNotEmpty()) {
            $entityIds = $clientsDebtRaw->pluck('entity_id');
            $entitiesMap = Entity::whereIn('id', $entityIds)->get()->keyBy('id');
            $topDebtors = $clientsDebtRaw->map(function ($row) use ($entitiesMap) {
                $entity = $entitiesMap->get($row->entity_id);
                $fullName = $entity ? trim(($entity->first_name ?? '') . ' ' . ($entity->last_name ?? '')) : ('ID #' . $row->entity_id);
                return [
                    'entity_id' => $row->entity_id,
                    'name' => $fullName,
                    'debt' => round($row->debt, 2),
                ];
            });
        }

        // Nuevos KPIs requeridos (colocados aquí para depender de métricas ya calculadas)
        // 1. Porcentaje ventas a crédito vs contado (mes actual)
        $monthStartDate = $now->copy()->startOfMonth()->toDateTimeString();
        $monthEndDate = $now->copy()->endOfMonth()->toDateTimeString();
        $creditVsContadoBaseQuery = Sale::whereRaw("$coalesceDate BETWEEN ? AND ?", [$monthStartDate, $monthEndDate]);
        $creditSalesAmount = (clone $creditVsContadoBaseQuery)->where('is_credit', 1)
            ->selectRaw('SUM(' . $netTotalExpr . ') as net_total')->value('net_total') ?? 0;
        $cashSalesAmount = (clone $creditVsContadoBaseQuery)->where('is_credit', 0)
            ->selectRaw('SUM(' . $netTotalExpr . ') as net_total')->value('net_total') ?? 0;
        $totalCreditCash = $creditSalesAmount + $cashSalesAmount;
        $percentCredit = $totalCreditCash > 0 ? round(($creditSalesAmount / $totalCreditCash) * 100, 2) : 0;
        $percentCash = $totalCreditCash > 0 ? round(($cashSalesAmount / $totalCreditCash) * 100, 2) : 0;

        // 2. Valor inventario a costo y a precio de venta (stock actual * purchase_price / sale_price)
        $inventoryValueCost = Inventory::select(DB::raw('SUM(stock * purchase_price) as total'))->value('total') ?? 0;
        $inventoryValueSale = Inventory::select(DB::raw('SUM(stock * sale_price) as total'))->value('total') ?? 0;

        // 3. Margen bruto estimado del mes actual
        $salesIdsMonth = Sale::whereRaw("$coalesceDate BETWEEN ? AND ?", [$monthStartDate, $monthEndDate])->pluck('id');
        $monthSaleDetails = collect();
        if ($salesIdsMonth->isNotEmpty()) {
            $monthSaleDetails = \App\Models\SaleDetail::whereIn('sale_id', $salesIdsMonth)->get();
        }
        $netSalesRevenue = $monthSaleDetails->sum(function ($d) {
            return ($d->sub_total - $d->discount_amount);
        });
        $variantIdsSold = $monthSaleDetails->pluck('product_variant_id')->unique();
        $inventoryByVariant = collect();
        if ($variantIdsSold->isNotEmpty()) {
            $inventoryByVariant = Inventory::whereIn('product_variant_id', $variantIdsSold)->get()->groupBy('product_variant_id');
        }
        $estimatedCost = 0;
        foreach ($monthSaleDetails as $detail) {
            $invGroup = $inventoryByVariant->get($detail->product_variant_id);
            if ($invGroup && $invGroup->count() > 0) {
                $totalStockForVariant = max(1, $invGroup->sum('stock'));
                $weightedCost = $invGroup->sum(function ($inv) {
                    return $inv->stock * $inv->purchase_price;
                }) / $totalStockForVariant;
                $estimatedCost += $weightedCost * $detail->quantity;
            }
        }
        $grossMarginAmount = $netSalesRevenue - $estimatedCost;
        $grossMarginPercent = $netSalesRevenue > 0 ? round(($grossMarginAmount / $netSalesRevenue) * 100, 2) : 0;


        return view('dashboard', [
            'products' => $products,
            'entities' => $entities,
            'inventoryTotal' => $inventoryTotal,
            'movementsToday' => $movementsToday,
            // Mensual
            'monthsLabels' => $monthsLabels,
            'monthsTotals' => $monthsTotals,
            'totalSales' => $totalSales,
            'bestMonthLabel' => $bestMonthLabel,
            'bestMonthAmount' => $bestMonthAmount,
            // Horario
            'hoursLabels' => $hoursLabels,
            'hoursTotals' => $hoursTotals,
            // Diario
            'daysLabels' => $daysLabels,
            'daysTotals' => $daysTotals,
            // Top lists
            'topProducts' => $topProducts,
            'topClients' => $topClients,
            // Periodic quick metrics
            'todaySales' => $todaySales,
            'monthSales' => $monthSales,
            'yearSales' => $yearSales,
            // Nuevas métricas
            'growthRate' => $growthRate, // porcentaje o null
            'prevMonthSales' => $prevMonthSales,
            'totalCreditDue' => $totalCreditDue,
            'totalCreditPaid' => $totalCreditPaid,
            'totalCreditPending' => $totalCreditPending,
            'totalClientsDebt' => round($totalClientsDebt, 2),
            'topDebtors' => $topDebtors,
            'topSellers' => $topSellers,
            'totalSellersCount' => $topSellers->sum('sales_count'),
            'totalSellersProfit' => $totalSellersProfit,
            // Nuevos KPIs
            'percentCredit' => $percentCredit,
            'percentCash' => $percentCash,
            'inventoryValueCost' => $inventoryValueCost,
            'inventoryValueSale' => $inventoryValueSale,
            'grossMarginAmount' => $grossMarginAmount,
            'grossMarginPercent' => $grossMarginPercent,
            'netSalesRevenue' => $netSalesRevenue,
            'estimatedCost' => $estimatedCost,
        ]);
    }
}
