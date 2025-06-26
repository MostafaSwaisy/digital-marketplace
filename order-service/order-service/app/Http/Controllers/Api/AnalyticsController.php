<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    // Get Sales Analytics (GET /api/analytics/sales)
    public function sales(Request $request)
    {
        $startDate = $request->get('start_date', now()->subDays(30));
        $endDate = $request->get('end_date', now());

        // Total sales
        $totalSales = Order::where('status', 'completed')
            ->whereBetween('completed_at', [$startDate, $endDate])
            ->sum('total_amount');

        // Total orders
        $totalOrders = Order::where('status', 'completed')
            ->whereBetween('completed_at', [$startDate, $endDate])
            ->count();

        // Sales by day
        $salesByDay = Order::select(
                DB::raw('DATE(completed_at) as date'),
                DB::raw('SUM(total_amount) as total'),
                DB::raw('COUNT(*) as orders')
            )
            ->where('status', 'completed')
            ->whereBetween('completed_at', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Top selling products
        $topProducts = OrderItem::select('product_id', 'product_name')
            ->selectRaw('COUNT(*) as sales_count, SUM(price) as revenue')
            ->whereHas('order', function ($query) use ($startDate, $endDate) {
                $query->where('status', 'completed')
                      ->whereBetween('completed_at', [$startDate, $endDate]);
            })
            ->groupBy('product_id', 'product_name')
            ->orderByDesc('sales_count')
            ->limit(10)
            ->get();

        return response()->json([
            'total_sales' => $totalSales,
            'total_orders' => $totalOrders,
            'average_order_value' => $totalOrders > 0 ? $totalSales / $totalOrders : 0,
            'sales_by_day' => $salesByDay,
            'top_products' => $topProducts,
        ]);
    }

    // Get Seller Analytics (GET /api/analytics/seller/{sellerId})
    public function seller($sellerId, Request $request)
    {
        $startDate = $request->get('start_date', now()->subDays(30));
        $endDate = $request->get('end_date', now());

        // Seller's total earnings
        $totalEarnings = OrderItem::whereHas('order', function ($query) use ($startDate, $endDate) {
                $query->where('status', 'completed')
                      ->whereBetween('completed_at', [$startDate, $endDate]);
            })
            ->where('seller_id', $sellerId)
            ->sum('seller_amount');

        // Total products sold
        $productsSold = OrderItem::whereHas('order', function ($query) use ($startDate, $endDate) {
                $query->where('status', 'completed')
                      ->whereBetween('completed_at', [$startDate, $endDate]);
            })
            ->where('seller_id', $sellerId)
            ->count();

        // Seller's product performance
        $productPerformance = OrderItem::select('product_id', 'product_name')
            ->selectRaw('COUNT(*) as sales_count, SUM(seller_amount) as earnings')
            ->whereHas('order', function ($query) use ($startDate, $endDate) {
                $query->where('status', 'completed')
                      ->whereBetween('completed_at', [$startDate, $endDate]);
            })
            ->where('seller_id', $sellerId)
            ->groupBy('product_id', 'product_name')
            ->orderByDesc('sales_count')
            ->get();

        return response()->json([
            'seller_id' => $sellerId,
            'total_earnings' => $totalEarnings,
            'products_sold' => $productsSold,
            'product_performance' => $productPerformance,
        ]);
    }
}