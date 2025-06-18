@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <h1 class="mb-4">Dashboard</h1>

        @if($lowStockItems->count() > 0)
        <div class="alert alert-warning">
            <h5>Warning: Stok Barang Rendah</h5>
            <ul>
                @foreach($lowStockItems as $item)
                    <li>{{ $item->name }} (Stock: {{ $item->stock }}, Minimum: {{ $item->minimum_stock }})</li>
                @endforeach
            </ul>
        </div>
        @endif

        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="card text-center shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Net Sales</h5>
                        <p class="display-6">{{ number_format($netSales, 2) }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-center shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Total Profit</h5>
                        <p class="display-6">{{ number_format($totalProfit, 2) }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-center shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Total Transaksi</h5>
                        <p class="display-6">{{ $totalTransactions }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mt-4">
                <div class="card text-center shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Items Sold</h5>
                        <p class="display-6">{{ $itemsSold }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mt-4">
                <div class="card text-center shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Stock Info</h5>
                        <p class="display-6">{{ $totalStock }}</p>
                    </div>
                </div>
            </div>
        </div>

    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <h5 class="card-title mb-3">Penjualan Per Hari</h5>
            <canvas id="salesOverTimeChart" height="100"></canvas>
        </div>
    </div>

    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <h5 class="card-title mb-3">Product Sales</h5>
            <canvas id="productSalesChart" height="100"></canvas>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <h5 class="card-title mb-3">Transaksi Terbaru</h5>
            <div class="table-responsive">
                <table class="table table-bordered table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Tipe</th>
                            <th>Tanggal</th>
                            <th>Total Harga</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentTransactions as $transaction)
                        <tr>
                            <td>{{ ucfirst($transaction['type']) }}</td>
                            <td>{{ $transaction['date'] }}</td>
                            <td>
                                @if(isset($transaction['total_price']))
                                    {{ number_format($transaction['total_price'], 2) }}
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const salesOverTimeCtx = document.getElementById('salesOverTimeChart').getContext('2d');
        const salesOverTimeChart = new Chart(salesOverTimeCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($salesOverTimeLabels) !!},
                datasets: [{
                    label: 'Sales Over Time',
                    data: {!! json_encode($salesOverTimeData) !!},
                    borderColor: 'rgba(54, 162, 235, 1)',
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    fill: true,
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    x: { display: true },
                    y: { display: true, beginAtZero: true }
                }
            }
        });

        const productSalesCtx = document.getElementById('productSalesChart').getContext('2d');
        const productSalesChart = new Chart(productSalesCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($productSalesLabels) !!},
                datasets: [{
                    label: 'Product Sales',
                    data: {!! json_encode($productSalesData) !!},
                    backgroundColor: 'rgba(255, 159, 64, 0.7)'
                }]
            },
            options: {
                responsive: true,
                scales: {
                    x: { display: true },
                    y: { display: true, beginAtZero: true }
                }
            }
        });
    </script>
</div>
@endsection
