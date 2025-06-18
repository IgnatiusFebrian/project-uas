@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h1 class="mb-4">Tambah Barang</h1>

    @php
        $user = auth()->user();
    @endphp

    @if ($user->role === 'employee')
        <form method="POST" action="{{ route('items.store') }}">
            @csrf

            <div class="mb-3">
                <label for="existing_item" class="form-label">Pilih Barang</label>
                <select class="form-select" id="existing_item" name="existing_item" required>
                    <option value="">-- Pilih Barang --</option>
                    @foreach(\App\Models\Item::all() as $item)
                        <option value="{{ $item->id }}">{{ $item->name }} (Stok: {{ $item->stock }})</option>
                    @endforeach
                </select>
                @error('existing_item')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="stock" class="form-label">Tambah Stok</label>
                <input type="number" class="form-control" id="stock" name="stock" value="{{ old('stock') }}" min="1" required>
                @error('stock')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary">Tambah Stok</button>
        </form>
    @elseif ($user->role === 'admin')
        <form method="POST" action="{{ route('items.store') }}">
            @csrf

            <div class="mb-3">
                <label for="new_item_name" class="form-label">Nama Barang</label>
                <input type="text" class="form-control" id="new_item_name" name="new_item_name" value="{{ old('new_item_name') }}" required>
                @error('new_item_name')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="category" class="form-label">Kategori</label>
                <input type="text" class="form-control" id="category" name="category" value="{{ old('category') }}">
                @error('category')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="unit" class="form-label">Satuan</label>
                <input type="text" class="form-control" id="unit" name="unit" value="{{ old('unit') }}">
                @error('unit')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="price" class="form-label">Harga Per Item</label>
                <input type="number" step="0.01" class="form-control" id="price" name="price" value="{{ old('price') }}" min="0" required>
                @error('price')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="minimum_stock" class="form-label">Stok Minimum</label>
                <input type="number" class="form-control" id="minimum_stock" name="minimum_stock" value="{{ old('minimum_stock', 0) }}" min="0">
                @error('minimum_stock')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <!-- Removed stock input for admin as per request -->

            <button type="submit" class="btn btn-primary" style="background-color: blue; border-color: blue;">Simpan</button>
            <a href="{{ route('items.index') }}" class="btn btn-danger ms-2" style="background-color: red; border-color: red;">Batal</a>
    </form>
        </form>
    @else
        <p>Anda tidak memiliki akses untuk menambah barang.</p>
    @endif
</div>
@endsection
