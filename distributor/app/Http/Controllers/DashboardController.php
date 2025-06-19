<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\Item;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Menampilkan data dashboard utama.
     *
     * Menghitung berbagai metrik seperti penjualan bersih, total transaksi,
     * jumlah barang terjual, stok total, data penjualan dari waktu ke waktu,
     * penjualan produk, transaksi terbaru, keuntungan total, dan barang dengan stok rendah.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Menghitung total penjualan dari transaksi dengan total harga > 0
        $transactionSales = TransactionDetail::join('transactions', 'transaction_details.transaction_id', '=', 'transactions.id')
            ->where('transactions.total_price', '>', 0)
            ->sum('transaction_details.total_price');

        // Menghitung total pengembalian dari transaksi dengan total harga < 0
        $transactionReturns = TransactionDetail::join('transactions', 'transaction_details.transaction_id', '=', 'transactions.id')
            ->where('transactions.total_price', '<', 0)
            ->sum('transaction_details.total_price');

        // Menghitung total penjualan barang keluar (outgoing goods)
        $outgoingNetSales = \App\Models\OutgoingGoods::sum(DB::raw('quantity * price'));

        // Menghitung penjualan bersih total
        $netSales = $transactionSales + $outgoingNetSales + $transactionReturns;

        // Menghitung total transaksi (transaksi + barang keluar)
        $totalTransactions = Transaction::count() + \App\Models\OutgoingGoods::count();

        // Menghitung jumlah barang yang terjual (dari transaksi dan barang keluar)
        $itemsSold = TransactionDetail::sum('quantity') + \App\Models\OutgoingGoods::sum('quantity');

        // Menghitung total stok barang
        $totalStock = Item::sum('stock');

        // Mengambil data penjualan transaksi per tanggal
        $transactionSales = Transaction::select(
            DB::raw('DATE(date) as date'),
            DB::raw('SUM(total_price) as total_sales')
        )
        ->groupBy('date');

        // Mengambil data penjualan barang keluar per tanggal
        $outgoingSales = \App\Models\OutgoingGoods::select(
            DB::raw('DATE(date) as date'),
            DB::raw('SUM(quantity * price) as total_sales')
        )
        ->groupBy('date');

        // Menggabungkan data penjualan transaksi dan barang keluar, lalu mengelompokkan berdasarkan tanggal
        $salesOverTimeData = $transactionSales->unionAll($outgoingSales)
            ->orderBy('date')
            ->get()
            ->groupBy('date')
            ->map(function ($group) {
                return $group->sum('total_sales');
            });

        // Mendapatkan label tanggal untuk grafik penjualan
        $salesOverTimeLabels = $salesOverTimeData->keys();
        // Mendapatkan nilai penjualan untuk grafik penjualan
        $salesOverTimeValues = $salesOverTimeData->values();

        // Mengambil data penjualan produk dari transaksi
        $transactionProductSales = TransactionDetail::select(
            'item_id',
            DB::raw('SUM(quantity) as total_quantity')
        )
        ->groupBy('item_id')
        ->with('item')
        ->get();

        // Mengambil data penjualan produk dari barang keluar
        $outgoingProductSales = \App\Models\OutgoingGoods::select(
            'item_id',
            DB::raw('SUM(quantity) as total_quantity')
        )
        ->groupBy('item_id')
        ->with('item')
        ->get();

        // Menggabungkan data penjualan produk dari transaksi dan barang keluar
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

        // Menyiapkan label dan nilai penjualan produk untuk grafik
        $productSalesLabels = [];
        $productSalesValues = [];

        foreach ($productSalesMap as $itemId => $quantity) {
            $item = \App\Models\Item::find($itemId);
            $productSalesLabels[] = $item ? $item->name : 'Unknown';
            $productSalesValues[] = $quantity;
        }

        // Mengambil 10 transaksi terbaru beserta data user
        $recentTransactions = Transaction::with('user')->orderBy('date', 'desc')->limit(10)->get();

        // Mengambil 10 barang keluar terbaru beserta data item dan user
        $recentOutgoingGoods = \App\Models\OutgoingGoods::with('item', 'user')
            ->orderBy('date', 'desc')
            ->limit(10)
            ->get();

        // Menggabungkan koleksi transaksi dan barang keluar terbaru
        $recentCombinedCollection = $recentTransactions->merge($recentOutgoingGoods);

        // Memetakan data gabungan untuk tampilan di dashboard
        $recentCombined = $recentCombinedCollection->map(function ($item) {
            if ($item instanceof Transaction) {
                return [
                    'id' => $item->id,
                    'type' => 'Transaction',
                    'date' => is_string($item->date) ? date('d/m/Y', strtotime($item->date)) : $item->date->format('d/m/Y'),
                    'item_name' => null,
                    'quantity' => null,
                    'user_name' => $item->user->name,
                    'notes' => null,
                ];
            } elseif ($item instanceof \App\Models\OutgoingGoods) {
                return [
                    'id' => $item->id,
                    'type' => 'Outgoing Goods',
                    'date' => is_string($item->date) ? date('d/m/Y', strtotime($item->date)) : $item->date->format('d/m/Y'),
                    'item_name' => $item->item->name,
                    'quantity' => $item->quantity,
                    'user_name' => $item->user->name,
                    'notes' => $item->notes,
                    'total_price' => $item->quantity * $item->price,
                ];
            }
        })->sortByDesc('date')->values();

        // Menghitung total biaya dari barang masuk
        $totalCost = \App\Models\IncomingGoods::selectRaw('SUM(quantity * price) as total_cost')->value('total_cost') ?? 0;

        // Menghitung total keuntungan (penjualan bersih - total biaya)
        $totalProfit = $netSales - $totalCost;

        // Mengambil daftar barang dengan stok rendah
        $lowStockItems = Item::whereColumn('stock', '<', 'minimum_stock')->get();

        // Mengembalikan view dashboard dengan data yang sudah disiapkan
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
