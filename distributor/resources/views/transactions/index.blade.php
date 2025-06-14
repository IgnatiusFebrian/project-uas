@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4">
    <h1 class="text-2xl font-bold mb-4">Transactions (Incoming & Outgoing Goods)</h1>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <a href="{{ route('transactions.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mb-4 inline-block">
        Add New Transaction
    </a>

    @if($combined->isEmpty())
        <p>No transactions found.</p>
    @else
        <table class="min-w-full bg-white border border-gray-200">
            <thead>
                <tr>
                    <th class="py-2 px-4 border-b">ID</th>
                    <th class="py-2 px-4 border-b">Type</th>
                    <th class="py-2 px-4 border-b">Date</th>
                    <th class="py-2 px-4 border-b">Item Name</th>
                    <th class="py-2 px-4 border-b">Quantity</th>
                    <th class="py-2 px-4 border-b">User</th>
                    <th class="py-2 px-4 border-b">Notes</th>
                </tr>
            </thead>
            <tbody>
                @foreach($combined as $transaction)
                <tr>
                    <td class="py-2 px-4 border-b">{{ $transaction['id'] }}</td>
                    <td class="py-2 px-4 border-b">{{ $transaction['type'] }}</td>
                    <td class="py-2 px-4 border-b">{{ \Carbon\Carbon::parse($transaction['date'])->format('Y-m-d') }}</td>
                    <td class="py-2 px-4 border-b">{{ $transaction['item_name'] }}</td>
                    <td class="py-2 px-4 border-b">{{ $transaction['quantity'] }}</td>
                    <td class="py-2 px-4 border-b">{{ $transaction['user_name'] }}</td>
                    <td class="py-2 px-4 border-b">{{ $transaction['notes'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection
