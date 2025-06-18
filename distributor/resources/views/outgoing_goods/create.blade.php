@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4">
    <h1 class="text-2xl font-bold mb-4">Input Barang Keluar</h1>

    @if ($errors->any())
        <div class="mb-4 p-4 bg-red-100 text-red-700 rounded">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>- {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('outgoing_goods.store') }}" method="POST">
        @csrf
        <div class="mb-4">
            <label for="item_id" class="block text-gray-700 font-bold mb-2">Select Item</label>
            <select name="item_id" id="item_id" class="w-full border border-gray-300 rounded px-3 py-2">
                <option value="">-- Select an item --</option>
                @foreach ($items as $item)
                    <option value="{{ $item->id }}" {{ old('item_id') == $item->id ? 'selected' : '' }}>
                        {{ $item->name }} (Stock: {{ $item->stock }})
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-4">
            <label for="quantity" class="block text-gray-700 font-bold mb-2">Jumlah</label>
            <input type="number" name="quantity" id="quantity" min="1" value="{{ old('quantity') }}" class="w-full border border-gray-300 rounded px-3 py-2" required>
        </div>

        <div class="mb-4">
            <label for="price" class="block text-gray-700 font-bold mb-2">Harga Per Item</label>
            <input type="number" name="price" id="price" min="0" step="0.01" value="{{ old('price') }}" class="w-full border border-gray-300 rounded px-3 py-2" required>
        </div>

        <div class="mb-4">
            <label for="date" class="block text-gray-700 font-bold mb-2">Tanggal</label>
            <input type="date" name="date" id="date" value="{{ old('date', now()->format('Y-m-d')) }}" class="w-full border border-gray-300 rounded px-3 py-2" required>
        </div>

         <div class="flex items-center justify-between">
            <button type="submit" class="btn btn-primary">
                Simpan
            </button>
            <a href="{{ route('outgoing_goods.index') }}" class="btn btn-danger">
                Batal
            </a>
        </div>
    </form>
</div>
@endsection
