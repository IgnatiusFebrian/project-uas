@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h1 class="mb-4">Daftar Barang</h1>

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

    <a href="{{ route('items.create') }}" class="btn btn-primary mb-4">+ Tambah Barang</a>

    @if(session('success'))
        <div class="alert alert-success mb-4">
            {{ session('success') }}
        </div>
    @endif

    <table class="table table-bordered table-hover">
        <thead class="table-light">
            <tr>
                <th>Nama</th>
                <th>Stok</th>
                <th>Barang Keluar</th>
                <th>Harga</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $item)
            <tr class="border-b border-gray-200 hover:bg-gray-100">
                <td>{{ $item->name }}</td>
                <td>{{ $item->stock }}</td>
                <td>{{ $item->total_outgoing }}</td>
                <td>{{ $item->price }}</td>
                <td>
                    <a href="{{ route('items.edit', $item->id) }}" class="btn btn-warning btn-sm">Edit</a>
                    <a href="{{ route('outgoing_goods.create', ['item_id' => $item->id]) }}" class="btn btn-danger btn-sm ms-2">Barang Keluar</a>
                    <form action="{{ route('items.destroy', $item->id) }}" method="POST" class="d-inline ms-2">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Hapus barang ini?')">Hapus</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
