<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RevenueController extends Controller
{
    /**
     * Get store revenue statistics
     *
     * @return \Illuminate\Http\Response
     */
    public function getStoreRevenue(Request $request)
    {
        $storeId = Auth::user()->stores->first()->id ?? null;
        
        if (!$storeId) {
            return response()->json([
                'success' => false,
                'message' => 'No store found for this user'
            ], 404);
        }
        
        $period = $request->input('period', 'month');
        $customStartDate = $request->input('start_date');
        $customEndDate = $request->input('end_date');
        
        if ($customStartDate && $customEndDate) {
            $startDate = Carbon::parse($customStartDate);
            $endDate = Carbon::parse($customEndDate);
        } else {
            switch ($period) {
                case 'week':
                    $startDate = Carbon::now()->subWeek();
                    break;
                case 'year':
                    $startDate = Carbon::now()->subYear();
                    break;
                default:
                    $startDate = Carbon::now()->subMonth();
            }
            $endDate = Carbon::now();
        }
        
        $statistics = [
            'period' => [
                'start' => $startDate->format('Y-m-d'),
                'end' => $endDate->format('Y-m-d'),
                'label' => $this->getPeriodLabel($startDate, $endDate, $period)
            ],
            'summary' => $this->getRevenueSummary($storeId, $startDate, $endDate),
            'top_products' => $this->getTopSellingProducts($storeId, $startDate, $endDate),
            'revenue_chart' => $this->getRevenueChartData($storeId, $startDate, $endDate, $period),
            'customer_stats' => $this->getCustomerStatistics($storeId, $startDate, $endDate),
            'product_stats' => $this->getProductStatistics($storeId, $startDate, $endDate),
            'average_order_value' => $this->getAverageOrderValue($storeId, $startDate, $endDate),
            'revenue_by_category' => $this->getRevenueByCategory($storeId, $startDate, $endDate),
        ];
        
        return response()->json([
            'success' => true,
            'data' => $statistics
        ]);
    }
    
    private function getRevenueSummary($storeId, $startDate, $endDate)
    {
        $totalRevenue = Order::where('store_id', $storeId)
            ->where('status', 'completed')
            ->whereBetween('order_date', [$startDate, $endDate])
            ->sum('total_price');
           
        $orderCount = Order::where('store_id', $storeId)
            ->whereBetween('order_date', [$startDate, $endDate])
            ->count();
            
        $pendingOrders = Order::where('store_id', $storeId)
            ->where('status', 'pending')
            ->whereBetween('order_date', [$startDate, $endDate])
            ->count();
          
        $productsSold = OrderItem::whereHas('order', function ($query) use ($storeId, $startDate, $endDate) {
                $query->where('store_id', $storeId)
                    ->whereBetween('order_date', [$startDate, $endDate]);
            })
            ->sum('quantity');
            
        $totalProducts = Product::where('store_id', $storeId)
            ->whereNull('deleted_at')
            ->count();
            
        return [
            'total_revenue' => number_format($totalRevenue, 2),
            'order_count' => $orderCount,
            'pending_orders' => $pendingOrders,
            'products_sold' => $productsSold,
            'total_products' => $totalProducts,
        ];
    }
    
    private function getTopSellingProducts($storeId, $startDate, $endDate, $limit = 5)
    {
        $topProducts = OrderItem::select(
                'products.id',
                'products.name',
                'products.original_price',
                'products.discount_percent',
                'products.product_type',
                'products.discounted_price',
                'products.rating',
                DB::raw('SUM(order_items.quantity) as total_sold'),
                DB::raw('SUM(order_items.price * order_items.quantity) as total_revenue')
            )
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.store_id', $storeId)
            ->whereBetween('orders.order_date', [$startDate, $endDate])
            ->groupBy(
                'products.id',
                'products.name',
                'products.original_price',
                'products.discount_percent',
                'products.product_type',
                'products.discounted_price',
                'products.rating'
            )
            ->orderBy('total_sold', 'desc')
            ->limit($limit)
            ->get();
        
        foreach ($topProducts as $product) {
            $image = DB::table('images')
                ->where('product_id', $product->id)
                ->orderBy('image_order')
                ->first();
                
            $product->image_url = $image ? $image->image_url : null;
        }
        
        return $topProducts;
    }
    
    private function getRevenueChartData($storeId, $startDate, $endDate, $period)
    {
        switch ($period) {
            case 'week':
                $groupFormat = 'Y-m-d';
                $selectFormat = 'DATE(order_date) as date';
                $displayFormat = 'd M';
                break;
            case 'year':
                $groupFormat = 'Y-m';
                $selectFormat = "DATE_FORMAT(order_date, '%Y-%m') as date";
                $displayFormat = 'M Y';
                break;
            default:
                $groupFormat = 'Y-m-d';
                $selectFormat = 'DATE(order_date) as date';
                $displayFormat = 'd M';
        }
        
        $revenueData = DB::table('orders')
            ->select(
                DB::raw($selectFormat),
                DB::raw('SUM(total_price) as revenue'),
                DB::raw('COUNT(*) as order_count')
            )
            ->where('store_id', $storeId)
            ->where('status', 'completed')
            ->whereBetween('order_date', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date')
            ->get();
            
        $chartData = [
            'labels' => [],
            'revenue' => [],
            'orders' => []
        ];
        
        foreach ($revenueData as $data) {
            $date = Carbon::parse($data->date);
            $chartData['labels'][] = $date->format($displayFormat);
            $chartData['revenue'][] = round($data->revenue, 2);
            $chartData['orders'][] = $data->order_count;
        }
        
        return $chartData;
    }
    
    private function getCustomerStatistics($storeId, $startDate, $endDate)
    {
        $totalCustomers = Order::where('store_id', $storeId)
            ->whereBetween('order_date', [$startDate, $endDate])
            ->distinct('user_id')
            ->count('user_id');
            
        $newCustomers = DB::table('orders as o1')
            ->join(DB::raw('(
                SELECT user_id, MIN(order_date) as first_order_date
                FROM orders
                GROUP BY user_id
            ) as o2'), function($join) {
                $join->on('o1.user_id', '=', 'o2.user_id')
                    ->on('o1.order_date', '=', 'o2.first_order_date');
            })
            ->where('o1.store_id', $storeId)
            ->whereBetween('o1.order_date', [$startDate, $endDate])
            ->count();
            
        $returningCustomers = $totalCustomers - $newCustomers;
        
        return [
            'total_customers' => $totalCustomers,
            'new_customers' => $newCustomers,
            'returning_customers' => $returningCustomers,
            'returning_rate' => $totalCustomers > 0 ? 
                round(($returningCustomers / $totalCustomers) * 100, 1) : 0
        ];
    }
    
    private function getProductStatistics($storeId, $startDate, $endDate)
    {
        $topCategories = DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->select(
                'categories.id',
                'categories.name',
                DB::raw('SUM(order_items.quantity) as total_sold'),
                DB::raw('SUM(order_items.price * order_items.quantity) as total_revenue')
            )
            ->where('orders.store_id', $storeId)
            ->whereBetween('orders.order_date', [$startDate, $endDate])
            ->groupBy('categories.id', 'categories.name')
            ->orderBy('total_revenue', 'desc')
            ->limit(3)
            ->get();
            
        $discountProducts = DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->select(
                'products.id',
                'products.name',
                'products.discount_percent',
                DB::raw('SUM(order_items.quantity) as total_sold'),
                DB::raw('SUM(order_items.price * order_items.quantity) as total_revenue')
            )
            ->where('orders.store_id', $storeId)
            ->where('products.discount_percent', '>', 0)
            ->whereBetween('orders.order_date', [$startDate, $endDate])
            ->groupBy('products.id', 'products.name', 'products.discount_percent')
            ->orderBy('total_revenue', 'desc')
            ->limit(3)
            ->get();
            
        $lowStockProducts = Product::where('store_id', $storeId)
            ->where('stock_quantity', '<', 10)
            ->whereHas('orderItems', function ($query) use ($startDate, $endDate) {
                $query->whereHas('order', function ($q) use ($startDate, $endDate) {
                    $q->whereBetween('order_date', [$startDate, $endDate]);
                });
            })
            ->withCount(['orderItems as total_sold' => function ($query) use ($startDate, $endDate) {
                $query->whereHas('order', function ($q) use ($startDate, $endDate) {
                    $q->whereBetween('order_date', [$startDate, $endDate]);
                });
                $query->select(DB::raw('SUM(quantity)'));
            }])
            ->orderBy('total_sold', 'desc')
            ->limit(5)
            ->get(['id', 'name', 'stock_quantity']);
            
        return [
            'top_categories' => $topCategories,
            'discount_performers' => $discountProducts,
            'low_stock_alert' => $lowStockProducts
        ];
    }
    
    private function getAverageOrderValue($storeId, $startDate, $endDate)
    {
        $averageOrderValue = Order::where('store_id', $storeId)
            ->where('status', 'completed')
            ->whereBetween('order_date', [$startDate, $endDate])
            ->avg('total_price');
          
        $previousStartDate = (clone $startDate)->subDays($endDate->diffInDays($startDate));
        $previousEndDate = (clone $endDate)->subDays($endDate->diffInDays($startDate));
        
        $previousAverageOrderValue = Order::where('store_id', $storeId)
            ->where('status', 'completed')
            ->whereBetween('order_date', [$previousStartDate, $previousEndDate])
            ->avg('total_price') ?? 0;
            
        $percentChange = $previousAverageOrderValue > 0 ? 
            (($averageOrderValue - $previousAverageOrderValue) / $previousAverageOrderValue) * 100 : 0;
            
        return [
            'current' => round($averageOrderValue, 2),
            'previous' => round($previousAverageOrderValue, 2),
            'percent_change' => round($percentChange, 1),
            'trending' => $percentChange > 0 ? 'up' : ($percentChange < 0 ? 'down' : 'stable')
        ];
    }
    
    private function getRevenueByCategory($storeId, $startDate, $endDate)
    {
        return DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->select(
                'categories.id',
                'categories.name',
                DB::raw('SUM(order_items.quantity) as total_sold'),
                DB::raw('SUM(order_items.price * order_items.quantity) as total_revenue'),
                DB::raw('COUNT(DISTINCT orders.id) as order_count')
            )
            ->where('orders.store_id', $storeId)
            ->where('orders.status', 'completed')
            ->whereBetween('orders.order_date', [$startDate, $endDate])
            ->groupBy('categories.id', 'categories.name')
            ->orderBy('total_revenue', 'desc')
            ->get();
    }
    
    
    private function getPeriodLabel($startDate, $endDate, $period)
    {
        switch ($period) {
            case 'week':
                return 'Tuần';
            case 'year':
                return 'Năm';
            case 'custom':
                return 'Tùy chỉnh';
            default:
                return 'Tháng';
        }
    }
    
    public function exportRevenueReport(Request $request)
    {
        $storeId = Auth::user()->stores->first()->id ?? null;
        
        if (!$storeId) {
            return response()->json([
                'success' => false,
                'message' => 'No store found for this user'
            ], 404);
        }
        
        $startDate = Carbon::parse($request->input('start_date', Carbon::now()->subMonth()));
        $endDate = Carbon::parse($request->input('end_date', Carbon::now()));
        
        $orders = Order::with(['orderItems.product', 'user'])
            ->where('store_id', $storeId)
            ->whereBetween('order_date', [$startDate, $endDate])
            ->get();
           
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="revenue_report.csv"',
        ];
        
        $callback = function() use ($orders) {
            $file = fopen('php://output', 'w');
            
            fputcsv($file, [
                'Order ID', 
                'Order Date', 
                'Customer', 
                'Status', 
                'Total Amount',
                'Products'
            ]);
            
            foreach ($orders as $order) {
                $products = [];
                foreach ($order->orderItems as $item) {
                    $products[] = $item->product->name . ' (x' . $item->quantity . ')';
                }
                
                fputcsv($file, [
                    $order->order_code,
                    $order->order_date,
                    $order->user->username,
                    $order->status,
                    $order->total_price,
                    implode(', ', $products)
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
}