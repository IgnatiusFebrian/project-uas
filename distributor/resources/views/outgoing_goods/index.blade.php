@extends('layouts.app')

@section('content')
<div class="container py-4">

    <h1 class="mb-4">Daftar Barang Keluar</h1>

    <form method="GET" action="{{ route('items.index') }}" class="mb-4 row g-3 align-items-center">
        <div class="col-auto">
            <label for="start_date" class="col-form-label">Tanggal Mulai:</label>
        </div>
        <div class="col-auto">
            <input type="date" id="start_date" name="start_date" value="{{ request('start_date') }}" class="form-control">
        </div>
        <div class="col-auto">
            <label for="end_date" class="col-form-label">Tanggal Akhir:</label>
        </div>
        <div class="col-auto">
            <input type="date" id="end_date" name="end_date" value="{{ request('end_date') }}" class="form-control">
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-primary">Filter</button>
        </div>
    </form>

    @if(session('success'))
        <div class="alert alert-success mb-4">
            {{ session('success') }}
        </div>
    @endif

    <table class="table table-bordered table-hover">
        <thead class="table-light">
            <tr>
                <th>Tanggal</th>
                <th>Nama</th>
                <th>Kategori</th>
                <th>Jumlah</th>
                <th>Satuan</th>
                <th>Stok Minimum</th>
                <th>Harga</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($outgoingGoods as $outgoing)
            <tr class="border-b border-gray-200 hover:bg-gray-100">
<td>{{ \Carbon\Carbon::parse($outgoing->date)->format('d/m/Y') }}</td>
                <td>{{ $outgoing->item->name }}</td>
                <td>{{ $outgoing->item->category }}</td>
                <td>{{ $outgoing->quantity }}</td>
                <td>{{ $outgoing->item->unit }}</td>
                <td>{{ $outgoing->item->minimum_stock }}</td>
                <td>{{ number_format($outgoing->price, 2) }} / {{ $outgoing->item->unit }}</td>
                <td>
                    <a href="{{ route('outgoing_goods.edit', $outgoing->id) }}" class="btn btn-warning btn-sm">Edit</a>
                    <form action="{{ route('outgoing_goods.destroy', $outgoing->id) }}" method="POST" class="d-inline ms-2" onsubmit="return confirm('Hapus barang keluar ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="text-center text-muted">Tidak ada transaksi barang keluar ditemukan.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <a href="{{ route('outgoing_goods.create') }}" class="btn btn-primary mt-4">Tambah Barang Keluar</a>
</div>
@endsection
