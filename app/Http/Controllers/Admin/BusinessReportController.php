<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class BusinessReportController extends Controller
{
    public function form(Request $request): View
    {
        return view('admin.business-report.form', [
            'month' => (int) $request->input('month', now()->month),
            'year' => (int) $request->input('year', now()->year),
        ]);
    }

    public function download(Request $request): Response
    {
        $validated = $request->validate([
            'month' => ['required', 'integer', 'min:1', 'max:12'],
            'year' => ['required', 'integer', 'min:2020', 'max:2100'],
        ]);

        $month = $validated['month'];
        $year = $validated['year'];
        $periodStart = now()->setDate($year, $month, 1)->startOfDay();
        $periodEnd = $periodStart->copy()->endOfMonth();

        $ordersInPeriod = Order::whereBetween('created_at', [$periodStart, $periodEnd]);

        $orderCountByStatus = (clone $ordersInPeriod)
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $totalOrders = array_sum($orderCountByStatus->toArray());

        $revenue = Payment::where('status', 'ACCEPTE')
            ->whereBetween('paid_at', [$periodStart, $periodEnd])
            ->sum('amount');

        $orderTotalNonCancelled = (clone $ordersInPeriod)
            ->where('status', '!=', 'ANNULEE')
            ->sum('total');

        $averageOrderValue = $totalOrders > 0 ? $orderTotalNonCancelled / max($totalOrders, 1) : 0;

        $newClientsCount = Client::whereBetween('created_at', [$periodStart, $periodEnd])->count();

        $paymentMethodBreakdown = Payment::where('status', 'ACCEPTE')
            ->whereBetween('paid_at', [$periodStart, $periodEnd])
            ->selectRaw('payment_method, count(*) as total, sum(amount) as amount_sum')
            ->groupBy('payment_method')
            ->get();

        $topProducts = OrderItem::query()
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->whereBetween('orders.created_at', [$periodStart, $periodEnd])
            ->where('orders.status', '!=', 'ANNULEE')
            ->selectRaw('order_items.product_id, sum(order_items.quantity) as qty, sum(order_items.subtotal) as revenue')
            ->groupBy('order_items.product_id')
            ->orderByDesc('qty')
            ->limit(10)
            ->with('product')
            ->get();

        $lowStockProducts = Product::where('status', 'ACTIF')
            ->whereColumn('stock', '<=', 'alert_threshold')
            ->orderBy('stock')
            ->limit(15)
            ->get();

        $totalActiveProducts = Product::where('status', 'ACTIF')->count();

        $pdf = Pdf::loadView('admin.business-report.pdf', [
            'periodStart' => $periodStart,
            'periodEnd' => $periodEnd,
            'orderCountByStatus' => $orderCountByStatus,
            'totalOrders' => $totalOrders,
            'revenue' => $revenue,
            'averageOrderValue' => $averageOrderValue,
            'newClientsCount' => $newClientsCount,
            'paymentMethodBreakdown' => $paymentMethodBreakdown,
            'topProducts' => $topProducts,
            'lowStockProducts' => $lowStockProducts,
            'totalActiveProducts' => $totalActiveProducts,
        ])->setPaper('a4');

        $fileName = 'rapport-mensuel-'.$periodStart->format('Y-m').'.pdf';

        return $pdf->download($fileName);
    }
}
