@extends('layouts.app')

@section('content')
@php
    use Carbon\Carbon;
    setlocale(LC_TIME, 'id_ID.UTF-8');
    Carbon::setLocale('id');
    $user = auth()->user();
@endphp

<div class="container mx-auto px-4">
    <h1 class="text-2xl font-bold mb-4">Daftar Barang Masuk</h1>

    <h2 class="text-xl font-semibold mb-2">Total Stok per Barang</h2>

    <div class="bg-white shadow-md rounded my-6">
        {{-- Summary Table for Total Stock per Item --}}
        <table class="table table-striped table-hover table-bordered mb-6">
            <thead class="table-light">
                <tr>
                    <th>Nama</th>
                    <th>Kategori</th>
                    <th>Total Stok</th>
                    <th>Satuan</th>
                    <th>Stok Minimum</th>
                    <th>Harga</th>
                </tr>
            </thead>
            <tbody>
@php
    $stockSummary = [];
    foreach ($incomingGoods as $incoming) {
        $itemId = $incoming->item->id;
        if (!isset($stockSummary[$itemId])) {
            $stockSummary[$itemId] = [
                'name' => $incoming->item->name,
                'category' => $incoming->item->category,
                'total_stock' => $incoming->item->stock,
                'unit' => $incoming->item->unit,
                'minimum_stock' => $incoming->item->minimum_stock,
                'price' => $incoming->price,
            ];
        }
    }
@endphp
                @foreach ($stockSummary as $summary)
                <tr>
                    <td>{{ $summary['name'] }}</td>
                    <td>{{ $summary['category'] }}</td>
                    <td>{{ $summary['total_stock'] }}</td>
                    <td>{{ $summary['unit'] }}</td>
                    <td>{{ $summary['minimum_stock'] }}</td>
                    <td>{{ number_format($summary['price'], 2) }} / {{ $summary['unit'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="container mx-auto px-4">
    <h2 class="text-xl font-semibold mb-2">Detail Barang Masuk</h2>

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

        {{-- Detailed Incoming Goods Records Table --}}
        <table class="table table-striped table-hover table-bordered">
            <thead class="table-light">
                <tr>
                    <th>Tanggal Ditambahkan</th>
                    <th>Nama</th>
                    <th>Kategori</th>
                    <th>Jumlah</th>
                    <th>Satuan</th>
                    <th>Stok Minimum</th>
                    <th>Harga</th>
                    @if($user->role !== 'employee')
                    <th>User</th>
                    <th>Catatan</th>
                    @endif
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($incomingGoods as $incoming)
                <tr>
                    <td>{{ $incoming->date->format('d/m/Y') }}</td>
                    <td>{{ $incoming->item->name }}</td>
                    <td>{{ $incoming->item->category }}</td>
                    <td>{{ $incoming->quantity }}</td>
                    <td>{{ $incoming->item->unit }}</td>
                    <td>{{ $incoming->item->minimum_stock }}</td>
                    <td>{{ number_format($incoming->price, 2) }} / {{ $incoming->item->unit }}</td>
                    @if($user->role !== 'employee')
                    <td>{{ $incoming->user->name }}</td>
                    <td>{{ $incoming->notes }}</td>
                    @endif
                    <td>
                        <a href="{{ route('incoming_goods.edit', $incoming->id) }}" class="btn btn-primary btn-sm">Edit</a>
                        <form action="{{ route('incoming_goods.destroy', $incoming->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus data ini?')">Hapus</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        <a href="{{ route('incoming_goods.create') }}" class="btn btn-primary">
            Tambah Barang Masuk
        </a>
    </div>
</div>
@endsection
