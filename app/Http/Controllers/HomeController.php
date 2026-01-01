<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sales;
use App\Models\SalesItem;
use App\Models\Product;
use App\Models\Config;
use App\Models\Rule;
use App\Models\Cart;
use App\Models\StockHistory;
use Carbon\Carbon;
use DB;

class HomeController extends Controller
{
 public function index(Request $request)
    {
        $assets = ['chart', 'animation'];

        // Tanggal untuk filter
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();

        // ================= TOTAL STATISTICS =================
        $totalSales = Sales::sum('total_amount');
        $totalTransactions = Sales::count();
        $totalProducts = Product::count();
        $activeProducts = Product::where('is_active', true)->count();
        $orderServed = SalesItem::sum('quantity');
        $activeCarts = Cart::count();

        // ================= TODAY'S STATISTICS =================
        $salesToday = Sales::whereDate('created_at', $today)->sum('total_amount');
        $salesYesterday = Sales::whereDate('created_at', $yesterday)->sum('total_amount');
        $transactionsToday = Sales::whereDate('created_at', $today)->count();
        $ordersToday = SalesItem::whereHas('sale', function($query) use ($today) {
            $query->whereDate('created_at', $today);
        })->sum('quantity');

        // ================= MONTHLY STATISTICS =================
        $salesThisMonth = Sales::whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->sum('total_amount');

        // ================= SALES GROWTH =================
        $salesGrowth = 0;
        if ($salesYesterday > 0) {
            $salesGrowth = (($salesToday - $salesYesterday) / $salesYesterday) * 100;
        } elseif ($salesToday > 0) {
            $salesGrowth = 100;
        }

        // ================= WEEKLY SALES (untuk chart) =================
        $weeklySales = Sales::whereBetween('created_at', [$startOfWeek, $endOfWeek])
            ->selectRaw('DAYNAME(created_at) as day, DATE(created_at) as date, SUM(total_amount) as total')
            ->groupBy('day', 'date')
            ->orderBy('date')
            ->get();

        // Data untuk chart
        $chartLabels = [];
        $chartData = [];
        $daysOrder = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        
        // Inisialisasi array dengan 0 untuk semua hari
        foreach ($daysOrder as $day) {
            $chartLabels[] = substr($day, 0, 3); // Mon, Tue, etc
            $chartData[] = 0;
        }
        
        // Isi data yang ada
        foreach ($weeklySales as $sale) {
            $dayIndex = array_search($sale->day, $daysOrder);
            if ($dayIndex !== false) {
                $chartData[$dayIndex] = (float) $sale->total;
            }
        }

        // ================= MONTHLY SALES BY DAY =================
        $monthlySales = Sales::whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->selectRaw('DAY(created_at) as day, SUM(total_amount) as total')
            ->groupBy('day')
            ->orderBy('day')
            ->get()
            ->pluck('total', 'day');

        // ================= TOP PRODUCTS =================
        $topProducts = SalesItem::select('product_id', DB::raw('SUM(quantity) as total_quantity'))
            ->with('product')
            ->whereHas('sale', function($query) use ($currentMonth, $currentYear) {
                $query->whereMonth('created_at', $currentMonth)
                    ->whereYear('created_at', $currentYear);
            })
            ->groupBy('product_id')
            ->orderByDesc('total_quantity')
            ->limit(5)
            ->get();

        // ================= LOW STOCK PRODUCTS =================
        $lowStockProducts = Product::where('stock', '<', 10)->count();

        // ================= RECENT TRANSACTIONS =================
        $recentSales = Sales::with('items')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // ================= TODAY'S ACTIVITIES =================
        $todayActivities = [
            'sales' => $salesToday,
            'transactions' => $transactionsToday,
            'orders' => $ordersToday,
            'new_carts' => Cart::whereDate('created_at', $today)->count()
        ];

        return view('dashboards.dashboard', compact(
            'assets',
            'totalSales',
            'salesToday',
            'salesYesterday',
            'salesThisMonth',
            'salesGrowth',
            'totalTransactions',
            'transactionsToday',
            'totalProducts',
            'activeProducts',
            'orderServed',
            'ordersToday',
            'activeCarts',
            'lowStockProducts',
            'chartLabels',
            'chartData',
            'monthlySales',
            'topProducts',
            'recentSales',
            'todayActivities'
        ));
    }

    // API untuk data real-time
    public function getDashboardData(Request $request)
    {
        $today = Carbon::today();
        
        $data = [
            'sales_today' => Sales::whereDate('created_at', $today)->sum('total_amount'),
            'transactions_today' => Sales::whereDate('created_at', $today)->count(),
            'active_carts' => Cart::count(),
            'orders_today' => SalesItem::whereHas('sale', function($query) use ($today) {
                $query->whereDate('created_at', $today);
            })->sum('quantity'),
        ];

        return response()->json($data);
    }

    /*
     * Menu Style Routs
     */
    public function horizontal(Request $request)
    {
        $assets = ['chart', 'animation'];
        return view('menu-style.horizontal',compact('assets'));
    }
    public function dualhorizontal(Request $request)
    {
        $assets = ['chart', 'animation'];
        return view('menu-style.dual-horizontal',compact('assets'));
    }
    public function dualcompact(Request $request)
    {
        $assets = ['chart', 'animation'];
        return view('menu-style.dual-compact',compact('assets'));
    }
    public function boxed(Request $request)
    {
        $assets = ['chart', 'animation'];
        return view('menu-style.boxed',compact('assets'));
    }
    public function boxedfancy(Request $request)
    {
        $assets = ['chart', 'animation'];
        return view('menu-style.boxed-fancy',compact('assets'));
    }

    /*
     * Pages Routs
     */
    public function billing(Request $request)
    {
        return view('special-pages.billing');
    }

    public function calender(Request $request)
    {
        $assets = ['calender'];
        return view('special-pages.calender',compact('assets'));
    }

    public function kanban(Request $request)
    {
        return view('special-pages.kanban');
    }

    public function pricing(Request $request)
    {
        return view('special-pages.pricing');
    }

    public function rtlsupport(Request $request)
    {
        return view('special-pages.rtl-support');
    }

    public function timeline(Request $request)
    {
        return view('special-pages.timeline');
    }


    /*
     * Widget Routs
     */
    public function widgetbasic(Request $request)
    {
        return view('widget.widget-basic');
    }
    public function widgetchart(Request $request)
    {
        $assets = ['chart'];
        return view('widget.widget-chart', compact('assets'));
    }
    public function widgetcard(Request $request)
    {
        return view('widget.widget-card');
    }

    /*
     * Maps Routs
     */
    public function google(Request $request)
    {
        return view('maps.google');
    }
    public function vector(Request $request)
    {
        return view('maps.vector');
    }

    /*
     * Auth Routs
     */
    public function signin(Request $request)
    {
        return view('auth.login');
    }
    public function signup(Request $request)
    {
        return view('auth.register');
    }
    public function confirmmail(Request $request)
    {
        return view('auth.confirm-mail');
    }
    public function lockscreen(Request $request)
    {
        return view('auth.lockscreen');
    }
    public function recoverpw(Request $request)
    {
        return view('auth.recoverpw');
    }
    public function userprivacysetting(Request $request)
    {
        return view('auth.user-privacy-setting');
    }

    /*
     * Error Page Routs
     */

    public function error404(Request $request)
    {
        return view('errors.error404');
    }

    public function error500(Request $request)
    {
        return view('errors.error500');
    }
    public function maintenance(Request $request)
    {
        return view('errors.maintenance');
    }

    /*
     * uisheet Page Routs
     */
    public function uisheet(Request $request)
    {
        return view('uisheet');
    }

    /*
     * Form Page Routs
     */
    public function element(Request $request)
    {
        return view('forms.element');
    }

    public function wizard(Request $request)
    {
        return view('forms.wizard');
    }

    public function validation(Request $request)
    {
        return view('forms.validation');
    }

     /*
     * Table Page Routs
     */
    public function bootstraptable(Request $request)
    {
        return view('table.bootstraptable');
    }

    public function datatable(Request $request)
    {
        return view('table.datatable');
    }

    /*
     * Icons Page Routs
     */

    public function solid(Request $request)
    {
        return view('icons.solid');
    }

    public function outline(Request $request)
    {
        return view('icons.outline');
    }

    public function dualtone(Request $request)
    {
        return view('icons.dualtone');
    }

    public function colored(Request $request)
    {
        return view('icons.colored');
    }

    /*
     * Extra Page Routs
     */
    public function privacypolicy(Request $request)
    {
        return view('privacy-policy');
    }
    public function termsofuse(Request $request)
    {
        return view('terms-of-use');
    }
}
