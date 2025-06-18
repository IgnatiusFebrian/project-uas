<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\Item;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Net sales: sum of total_price from transaction_details + outgoing_goods
        $transactionNetSales = TransactionDetail::sum('total_price');
        $outgoingNetSales = \App\Models\OutgoingGoods::sum(DB::raw('quantity * price'));
        $netSales = $transactionNetSales + $outgoingNetSales;

        // Total transactions count + outgoing goods count
        $totalTransactions = Transaction::count() + \App\Models\OutgoingGoods::count();

        // Items sold: sum of quantity in transaction_details + outgoing_goods
        $itemsSold = TransactionDetail::sum('quantity') + \App\Models\OutgoingGoods::sum('quantity');

        // Total stock: sum of stock in items only
        $totalStock = Item::sum('stock');

        // Sales over time: combine transactions and outgoing goods by date
        $transactionSales = Transaction::select(
            DB::raw('DATE(date) as date'),
            DB::raw('SUM(total_price) as total_sales')
        )
        ->groupBy('date');

        $outgoingSales = \App\Models\OutgoingGoods::select(
            DB::raw('DATE(date) as date'),
            DB::raw('SUM(quantity * price) as total_sales')
        )
        ->groupBy('date');

        $salesOverTimeData = $transactionSales->unionAll($outgoingSales)
            ->orderBy('date')
            ->get()
            ->groupBy('date')
            ->map(function ($group) {
                return $group->sum('total_sales');
            });

        $salesOverTimeLabels = $salesOverTimeData->keys();
        $salesOverTimeValues = $salesOverTimeData->values();

        // Product sales: sum quantity grouped by item from transaction_details and outgoing_goods
        $transactionProductSales = TransactionDetail::select(
            'item_id',
            DB::raw('SUM(quantity) as total_quantity')
        )
        ->groupBy('item_id')
        ->with('item')
        ->get();

        $outgoingProductSales = \App\Models\OutgoingGoods::select(
            'item_id',
            DB::raw('SUM(quantity) as total_quantity')
        )
        ->groupBy('item_id')
        ->with('item')
        ->get();

        // Merge product sales data
        $productSalesMap = [];

        foreach ($transactionProductSales as $ps) {
            $productSalesMap[$ps->item_id] = $ps->total_quantity;
        }

        foreach ($outgoingProductSales as $ops) {
            if (isset($productSalesMap[$ops->item_id])) {
                $productSalesMap[$ops->item_id] += $ops->total_quantity;
            } else {
                $productSalesMap[$ops->item_id] = $ops->total_quantity;
            }
        }

        $productSalesLabels = [];
        $productSalesValues = [];

        foreach ($productSalesMap as $itemId => $quantity) {
            $item = \App\Models\Item::find($itemId);
            $productSalesLabels[] = $item ? $item->name : 'Unknown';
            $productSalesValues[] = $quantity;
        }

        // Recent transactions: latest 10 from transactions and outgoing goods combined
        $recentTransactions = Transaction::with('user')->orderBy('date', 'desc')->limit(10)->get();

        $recentOutgoingGoods = \App\Models\OutgoingGoods::with('item', 'user')
            ->orderBy('date', 'desc')
            ->limit(10)
            ->get();

        // Merge recent transactions and outgoing goods collections first
        $recentCombinedCollection = $recentTransactions->merge($recentOutgoingGoods);

        // Map the merged collection to arrays and sort by date descending
        $recentCombined = $recentCombinedCollection->map(function ($item) {
            if ($item instanceof Transaction) {
                return [
                    'id' => $item->id,
                    'type' => 'Transaction',
                    'date' => $item->date->format('d/m/Y'),
                    'item_name' => null,
                    'quantity' => null,
                    'user_name' => $item->user->name,
                    'notes' => null,
                ];
            } elseif ($item instanceof \App\Models\OutgoingGoods) {
                return [
                    'id' => $item->id,
                    'type' => 'Outgoing Goods',
                    'date' => $item->date->format('d/m/Y'),
                    'item_name' => $item->item->name,
                    'quantity' => $item->quantity,
                    'user_name' => $item->user->name,
                    'notes' => $item->notes,
                    'total_price' => $item->quantity * $item->price,
                ];
            }
        })->sortByDesc('date')->values();

        // Profit calculation is skipped due to missing cost data
        // Implementing a basic profit calculation: total sales revenue - total cost of goods sold

        $totalCost = \App\Models\IncomingGoods::selectRaw('SUM(quantity * price) as total_cost')->value('total_cost') ?? 0;
        $totalProfit = $netSales - $totalCost;

        // Fetch items with stock below minimum_stock for notification
        $lowStockItems = Item::whereColumn('stock', '<', 'minimum_stock')->get();

        return view('dashboard', [
            'netSales' => $netSales,
            'totalTransactions' => $totalTransactions,
            'itemsSold' => $itemsSold,
            'totalStock' => $totalStock,
            'salesOverTimeLabels' => $salesOverTimeLabels,
            'salesOverTimeData' => $salesOverTimeValues,
            'productSalesLabels' => $productSalesLabels,
            'productSalesData' => $productSalesValues,
            'recentTransactions' => $recentCombined,
            'totalProfit' => $totalProfit,
            'lowStockItems' => $lowStockItems,
        ]);
    }
}
