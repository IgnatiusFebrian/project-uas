@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4">
    <h1 class="text-2xl font-bold mb-4">Daftar Barang Keluar</h1>

    @if(session('success'))
        <div class="mb-4 p-4 bg-green-100 text-green-700 rounded">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white shadow-md rounded my-6">
        <table class="table table-striped table-hover table-bordered">
            <thead class="table-light">
                <tr>
                    <th>Tanggal</th>
                    <th>Nama Barang</th>
                    <th>Jumlah</th>
                    <th>Harga per Unit</th>
                    <th>Total Harga</th>
                    <th>User</th>
                    <th>Catatan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($outgoingGoods as $outgoing)
                <tr>
                    <td>{{ $outgoing->date }}</td>
                    <td>{{ $outgoing->item->name }}</td>
                    <td>{{ $outgoing->quantity }}</td>
                    <td>{{ number_format($outgoing->price, 2) }}</td>
                    <td>{{ number_format($outgoing->quantity * $outgoing->price, 2) }}</td>
                    <td>{{ $outgoing->user->name }}</td>
                    <td>{{ $outgoing->notes }}</td>
                    <td>
                        <a href="{{ route('outgoing_goods.edit', $outgoing->id) }}" class="text-primary">Edit</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center text-muted">Tidak ada transaksi barang keluar ditemukan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        <a href="{{ route('outgoing_goods.create') }}" class="btn btn-primary">
            Tambah Barang Keluar
        </a>
    </div>
</div>
@endsection
