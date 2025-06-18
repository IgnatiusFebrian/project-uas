 @extends('layouts.app')

@section('content')
<div class="container mx-auto px-4">
    <h1 class="text-2xl font-bold mb-4">Input Barang Masuk</h1>

    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('incoming_goods.store') }}" method="POST" class="max-w-lg">
        @csrf

        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="item_id">
                Pilih Barang
            </label>
            <select name="item_id" id="item_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                <option value="">Pilih Barang</option>
                @foreach($items as $item)
                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="quantity">
                Jumlah
            </label>
            <input type="number" name="quantity" id="quantity" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required min="1">
        </div>

        @php
            $user = auth()->user();
        @endphp

        @if ($user->role === 'admin')
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="price">
                    Harga Satuan
                </label>
                <input type="number" name="price" id="price" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required min="0" step="0.01">
            </div>
        @elseif ($user->role === 'employee')
            {{-- For employees, do not show price input --}}
        @endif

        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="date">
                Tanggal
            </label>
            <input type="date" name="date" id="date" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required value="{{ date('Y-m-d') }}">
        </div>

        <div class="flex items-center justify-between">
            <button type="submit" class="btn btn-primary">
                Simpan
            </button>
            <a href="{{ route('incoming_goods.index') }}" class="btn btn-danger">
                Batal
            </a>
        </div>
    </form>
</div>
@endsection
