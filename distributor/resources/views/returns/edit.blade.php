@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4">
    <h1 class="text-2xl font-bold mb-4">Edit Retur Barang</h1>

    <form action="{{ route('returns.update', $returnGoods->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label for="item_id" class="block font-medium text-gray-700">Nama Barang</label>
            <select name="item_id" id="item_id" class="form-select w-full @error('item_id') border-red-500 @enderror">
                <option value="">Pilih Barang</option>
                @foreach($items as $item)
                    <option value="{{ $item->id }}" {{ (old('item_id', $returnGoods->item_id) == $item->id) ? 'selected' : '' }}>
                        {{ $item->name }}
                    </option>
                @endforeach
            </select>
            @error('item_id')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="quantity" class="block font-medium text-gray-700">Jumlah</label>
            <input type="number" name="quantity" id="quantity" min="1" value="{{ old('quantity', $returnGoods->quantity) }}" class="form-input w-full @error('quantity') border-red-500 @enderror">
            @error('quantity')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="date" class="block font-medium text-gray-700">Tanggal</label>
            <input type="date" name="date" id="date" value="{{ old('date', \Carbon\Carbon::parse($returnGoods->date)->format('Y-m-d')) }}" class="form-input w-full @error('date') border-red-500 @enderror">
            @error('date')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="reason" class="block font-medium text-gray-700">Alasan Retur</label>
            <textarea name="reason" id="reason" rows="3" class="form-textarea w-full @error('reason') border-red-500 @enderror">{{ old('reason', $returnGoods->reason) }}</textarea>
            @error('reason')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary">Simpan</button>
        <a href="{{ route('returns.index') }}" class="btn btn-secondary ml-2">Batal</a>
    </form>
</div>
@endsection
