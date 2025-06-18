@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h1 class="mb-4">Edit Barang</h1>

    @php
        $user = auth()->user();
    @endphp

    <form method="POST" action="{{ route('items.update', $item->id) }}">
        @csrf
        @method('PUT')

        @if ($user->role === 'admin')
            <div class="mb-3">
                <label for="name" class="form-label">Nama Barang</label>
                <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $item->name) }}" required>
                @error('name')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="category" class="form-label">Kategori</label>
                <input type="text" class="form-control" id="category" name="category" value="{{ old('category', $item->category) }}">
                @error('category')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="stock" class="form-label">Stok</label>
                <input type="number" class="form-control" id="stock" name="stock" value="{{ old('stock', $item->stock) }}" min="0" required>
                @error('stock')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="unit" class="form-label">Satuan</label>
                <input type="text" class="form-control" id="unit" name="unit" value="{{ old('unit', $item->unit) }}">
                @error('unit')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="price" class="form-label">Harga Per Item</label>
                <input type="number" step="0.01" class="form-control" id="price" name="price" value="{{ old('price', $item->price) }}" min="0" required>
                @error('price')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="minimum_stock" class="form-label">Stok Minimum</label>
                <input type="number" class="form-control" id="minimum_stock" name="minimum_stock" value="{{ old('minimum_stock', $item->minimum_stock) }}" min="0">
                @error('minimum_stock')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
        @elseif ($user->role === 'employee')
            <div class="mb-3">
                <label for="stock" class="form-label">Stok</label>
                <input type="number" class="form-control" id="stock" name="stock" value="{{ old('stock', $item->stock) }}" min="0" required>
                @error('stock')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
        @else
            <p>Anda tidak memiliki akses untuk mengedit barang.</p>
        @endif

        <button type="submit" class="btn btn-primary" style="background-color: blue; border-color: blue;">Simpan</button>
        <a href="{{ route('items.index') }}" class="btn btn-danger ms-2" style="background-color: red; border-color: red;">Batal</a>
    </form>
    </form>
</div>
@endsection
