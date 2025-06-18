@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4">
    <h1 class="text-2xl font-bold mb-4">Daftar Retur Barang</h1>

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

    <table class="table-auto w-full border-collapse border border-gray-300">
        <thead>
            <tr>
                <th class="border border-gray-300 px-4 py-2">Tanggal</th>
                <th class="border border-gray-300 px-4 py-2">Nama Barang</th>
                <th class="border border-gray-300 px-4 py-2">Jumlah</th>
                <th class="border border-gray-300 px-4 py-2">Alasan</th>
                <th class="border border-gray-300 px-4 py-2">User</th>
                <th class="border border-gray-300 px-4 py-2">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($returns as $return)
            <tr>
                <td class="border border-gray-300 px-4 py-2">{{ \Carbon\Carbon::parse($return->date)->format('d/m/Y') }}</td>
                <td class="border border-gray-300 px-4 py-2">{{ $return->item->name }}</td>
                <td class="border border-gray-300 px-4 py-2">{{ $return->quantity }}</td>
                <td class="border border-gray-300 px-4 py-2">{{ $return->reason }}</td>
                <td class="border border-gray-300 px-4 py-2">{{ $return->user->name }}</td>
                <td class="border border-gray-300 px-4 py-2">
                    <a href="{{ route('returns.edit', $return->id) }}" class="btn btn-warning mr-2">Edit</a>
                    <form action="{{ route('returns.destroy', $return->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin ingin menghapus retur ini?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Hapus</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="border border-gray-300 px-4 py-2 text-center">Tidak ada data retur.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <br>
    <a href="{{ route('returns.create') }}" class="btn btn-primary mb-4">Tambah Retur Barang</a>

</div>
@endsection
