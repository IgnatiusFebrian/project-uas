@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h1 class="mb-4">Tambah Barang</h1>

    <form action="{{ route('items.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="existing_item" class="form-label">Pilih Barang yang Ada</label>
            <select class="form-select" id="existing_item" name="existing_item">
                <option value="">-- Pilih Barang --</option>
                @foreach($items as $item)
                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="new_item_name" class="form-label">Atau Nama Barang Baru</label>
            <input type="text" class="form-control" id="new_item_name" name="new_item_name" placeholder="Masukkan nama barang baru">
        </div>

        <div class="mb-3">
            <label for="stock" class="form-label">Jumlah Stok</label>
            <input type="number" class="form-control" id="stock" name="stock" required>
        </div>

        <div class="mb-3">
            <label for="price" class="form-label">Harga</label>
            <input type="number" step="0.01" class="form-control" id="price" name="price" required>
        </div>

        <button type="submit" class="btn btn-primary">Simpan</button>
        <a href="{{ route('items.index') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>
@endsection
