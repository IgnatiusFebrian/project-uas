@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4">
    <h1 class="text-2xl font-bold mb-4">Laporan Barang Masuk dan Transaksi</h1>

    <h2 class="text-xl font-semibold mb-2">Barang Masuk</h2>
    <div class="bg-white shadow-md rounded my-6">
        <table class="min-w-full table-auto mb-8">
            <thead>
                <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                    <th class="py-3 px-6 text-left">Tanggal</th>
                    <th class="py-3 px-6 text-left">Nama Barang</th>
                    <th class="py-3 px-6 text-center">Jumlah</th>
                    <th class="py-3 px-6 text-right">User</th>
                    <th class="py-3 px-6 text-left">Catatan</th>
                </tr>
            </thead>
            <tbody class="text-gray-600 text-sm font-light">
                @foreach($incomingTransactions as $transaction)
                <tr class="border-b border-gray-200 hover:bg-gray-100">
                    <td class="py-3 px-6 text-left">
                        {{ $transaction->date->format('d/m/Y') }}
                    </td>
                    <td class="py-3 px-6 text-left">
                        {{ $transaction->item->name }}
                    </td>
                    <td class="py-3 px-6 text-center">
                        {{ $transaction->quantity }}
                    </td>
                    <td class="py-3 px-6 text-right">
                        {{ $transaction->user->name }}
                    </td>
                    <td class="py-3 px-6 text-left">
                        {{ $transaction->notes }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <h2 class="text-xl font-semibold mb-2">Transaksi</h2>
    <div class="bg-white shadow-md rounded my-6">
        <table class="min-w-full table-auto">
            <thead>
                <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                    <th class="py-3 px-6 text-left">ID</th>
                    <th class="py-3 px-6 text-left">Tipe</th>
                    <th class="py-3 px-6 text-left">Tanggal</th>
                    <th class="py-3 px-6 text-right">Total Harga</th>
                    <th class="py-3 px-6 text-left">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-gray-600 text-sm font-light">
                @foreach($transactions as $transaction)
                <tr class="border-b border-gray-200 hover:bg-gray-100">
                    <td class="py-3 px-6 text-left">{{ $transaction->id }}</td>
                    <td class="py-3 px-6 text-left">{{ $transaction->type }}</td>
                    <td class="py-3 px-6 text-left">{{ $transaction->date->format('Y-m-d') }}</td>
                    <td class="py-3 px-6 text-right">{{ number_format($transaction->total_price, 2) }}</td>
                    <td class="py-3 px-6 text-left">
                        <a href="{{ route('transactions.show', $transaction->id) }}" class="text-blue-600 hover:text-blue-900 mr-2">View</a>
                        <a href="{{ route('transactions.edit', $transaction->id) }}" class="text-green-600 hover:text-green-900 mr-2">Edit</a>
                        <form action="{{ route('transactions.destroy', $transaction->id) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        <a href="{{ route('incoming_goods.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
            Back to List
        </a>
    </div>
</div>
@endsection
