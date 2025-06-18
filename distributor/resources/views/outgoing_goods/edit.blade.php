@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h1 class="mb-4">Edit Barang Keluar</h1>

    @if ($errors->any())
        <div class="alert alert-danger mb-4">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('outgoing_goods.update', $outgoingGoods->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="item_id" class="form-label">Nama Barang</label>
            <select name="item_id" id="item_id" class="form-select" required>
                @foreach ($items as $item)
                    <option value="{{ $item->id }}" {{ $item->id == $outgoingGoods->item_id ? 'selected' : '' }}>
                        {{ $item->name }} ({{ $item->category }})
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="quantity" class="form-label">Jumlah</label>
            <input type="number" name="quantity" id="quantity" class="form-control" value="{{ old('quantity', $outgoingGoods->quantity) }}" min="1" required>
        </div>

        <div class="mb-3">
            <label for="price" class="form-label">Harga</label>
            <input type="number" step="0.01" name="price" id="price" class="form-control" value="{{ old('price', $outgoingGoods->price) }}" min="0" required>
        </div>

        <div class="mb-3">
            <label for="date" class="form-label">Tanggal</label>
            <input type="date" name="date" id="date" class="form-control" value="{{ old('date', now()->format('Y-m-d')) }}" class="w-full border border-gray-300 rounded px-3 py-2" required>
        </div>

        <button type="submit" class="btn btn-primary" style="background-color: blue; border-color: blue;">Simpan Perubahan</button>
        <a href="{{ route('outgoing_goods.index') }}" class="btn btn-danger ms-2" style="background-color: red; border-color: red;">Batal</a>
    </form>
</div>
@endsection
