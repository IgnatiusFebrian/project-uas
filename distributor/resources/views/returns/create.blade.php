@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4">
    <h1 class="text-2xl font-bold mb-4">Tambah Retur Barang</h1>

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4" role="alert">
            <strong>Perhatian!</strong> {{ session('error') }}
        </div>
    @endif

    @if($errors->has('quantity') && str_contains($errors->first('quantity'), 'exceeds total outgoing quantity'))
        <div class="bg-red-200 border border-red-600 text-red-800 px-4 py-3 rounded mb-4" role="alert">
            <strong>Barang Retur Melebihi Barang Keluar</strong>
        </div>
    @endif

    <form action="{{ route('returns.store') }}" method="POST">
        @csrf

        <div class="mb-4">
            <label for="item_id" class="block font-medium text-gray-700">Nama Barang</label>
            <select name="item_id" id="item_id" class="form-select w-full @error('item_id') border-red-500 @enderror">
                <option value="">Pilih Barang</option>
                @foreach($items as $item)
                    <option value="{{ $item->id }}" {{ old('item_id') == $item->id ? 'selected' : '' }}>
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
            <input type="number" name="quantity" id="quantity" min="1" value="{{ old('quantity') }}" class="form-input w-full @error('quantity') border-red-500 @enderror">
            @error('quantity')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
            @if($errors->has('quantity') && str_contains($errors->first('quantity'), 'exceeds total outgoing quantity'))
                <p class="text-red-600 font-semibold mt-1">Barang Retur Melebihi Barang Keluar</p>
            @endif
        </div>

        <div class="mb-4">
            <label for="date" class="block font-medium text-gray-700">Tanggal</label>
            <input type="date" name="date" id="date" value="{{ old('date', date('Y-m-d')) }}" class="form-input w-full @error('date') border-red-500 @enderror">
            @error('date')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="reason" class="block font-medium text-gray-700">Alasan Retur</label>
            <textarea name="reason" id="reason" rows="3" class="form-textarea w-full @error('reason') border-red-500 @enderror">{{ old('reason') }}</textarea>
            @error('reason')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary">Simpan</button>
        <a href="{{ route('returns.index') }}" class="btn btn-secondary ml-2">Batal</a>
    </form>
</div>
@endsection
