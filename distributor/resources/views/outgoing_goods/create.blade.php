@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4">
    <h1 class="text-2xl font-bold mb-4">Create Outgoing Goods Transaction</h1>

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
            <label for="quantity" class="block text-gray-700 font-bold mb-2">Quantity</label>
            <input type="number" name="quantity" id="quantity" min="1" value="{{ old('quantity') }}" class="w-full border border-gray-300 rounded px-3 py-2" required>
        </div>

        <div class="mb-4">
            <label for="price" class="block text-gray-700 font-bold mb-2">Price per Unit</label>
            <input type="number" name="price" id="price" min="0" step="0.01" value="{{ old('price') }}" class="w-full border border-gray-300 rounded px-3 py-2" required>
        </div>

        <div class="mb-4">
            <label for="date" class="block text-gray-700 font-bold mb-2">Date</label>
            <input type="date" name="date" id="date" value="{{ old('date', date('Y-m-d')) }}" class="w-full border border-gray-300 rounded px-3 py-2" required>
        </div>

        <div class="mb-4">
            <label for="notes" class="block text-gray-700 font-bold mb-2">Notes</label>
            <textarea name="notes" id="notes" rows="3" class="w-full border border-gray-300 rounded px-3 py-2">{{ old('notes') }}</textarea>
        </div>

        <div>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Submit
            </button>
            <a href="{{ route('outgoing_goods.index') }}" class="ml-4 bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection
