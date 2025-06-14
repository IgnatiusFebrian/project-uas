@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h1 class="mb-4">Transaction Details</h1>

    <div class="card shadow-sm">
        <div class="card-body">
            <h5 class="card-title">Transaction #{{ $transaction->id ?? 'N/A' }}</h5>
            <p><strong>Type:</strong> {{ ucfirst($transaction->type ?? 'N/A') }}</p>
            <p><strong>Date:</strong> {{ $transaction->date ?? 'N/A' }}</p>
            <p><strong>Total Price:</strong> {{ number_format($transaction->total_price ?? 0, 2) }}</p>
            <!-- Add more transaction details as needed -->
        </div>
    </div>

    <a href="{{ route('transactions.index') }}" class="btn btn-primary mt-4">Back to Transactions</a>
</div>
@endsection
