@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4">
    <h1 class="text-2xl font-bold mb-4">Edit Barang Masuk</h1>

    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>- {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('incoming_goods.update', $incomingGood->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label for="item_id" class="block text-gray-700 font-bold mb-2">Pilih Barang</label>
            <select name="item_id" id="item_id" class="w-full border border-gray-300 rounded px-3 py-2">
                <option value="">-- Pilih Barang --</option>
                @foreach ($items as $item)
                    <option value="{{ $item->id }}" {{ $incomingGood->item_id == $item->id ? 'selected' : '' }}>
                        {{ $item->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-4">
            <label for="new_item_name" class="block text-gray-700 font-bold mb-2">Nama Barang Baru (jika ada)</label>
            <input type="text" name="new_item_name" id="new_item_name" value="{{ old('new_item_name') }}" class="w-full border border-gray-300 rounded px-3 py-2" placeholder="Masukkan nama barang baru">
        </div>

        <div class="mb-4">
            <label for="quantity" class="block text-gray-700 font-bold mb-2">Jumlah</label>
            <input type="number" name="quantity" id="quantity" value="{{ old('quantity', $incomingGood->quantity) }}" class="w-full border border-gray-300 rounded px-3 py-2" min="1" required>
        </div>

        <div class="mb-4">
            <label for="price" class="block text-gray-700 font-bold mb-2">Harga</label>
            <input type="number" step="0.01" name="price" id="price" value="{{ old('price', $incomingGood->price) }}" class="w-full border border-gray-300 rounded px-3 py-2" min="0" required>
        </div>

        <div class="mb-4">
            <label for="date" class="block text-gray-700 font-bold mb-2">Tanggal</label>
            <input type="date" name="date" id="date" value="{{ old('date', $incomingGood->date->format('Y-m-d')) }}" class="w-full border border-gray-300 rounded px-3 py-2" required>
        </div>

        <div class="mb-4">
            <label for="notes" class="block text-gray-700 font-bold mb-2">Catatan</label>
            <textarea name="notes" id="notes" class="w-full border border-gray-300 rounded px-3 py-2">{{ old('notes', $incomingGood->notes) }}</textarea>
        </div>

        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Update</button>
        <a href="{{ route('incoming_goods.index') }}" class="ml-4 text-gray-600 hover:underline">Batal</a>
    </form>
</div>
@endsection
