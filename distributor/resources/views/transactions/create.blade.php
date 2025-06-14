@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4">
    <h1 class="text-2xl font-bold mb-4">Create Transaction</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- Incoming Goods Card -->
        <a href="{{ route('incoming_goods.create') }}" class="block p-6 bg-white rounded-lg border border-gray-200 shadow-md hover:bg-gray-100">
            <h5 class="mb-2 text-xl font-bold text-gray-900">Barang Masuk</h5>
            <p class="text-gray-700">
                Input transaksi barang masuk ke gudang
            </p>
        </a>

        <!-- Outgoing Goods Card -->
        <a href="{{ route('outgoing_goods.create') }}" class="block p-6 bg-white rounded-lg border border-gray-200 shadow-md hover:bg-gray-100">
            <h5 class="mb-2 text-xl font-bold text-gray-900">Barang Keluar</h5>
            <p class="text-gray-700">
                Input transaksi barang keluar dari gudang
            </p>
        </a>
    </div>

    <div class="mt-4">
        <a href="{{ route('transactions.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
            Back to Transactions
        </a>
    </div>
</div>
@endsection