@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h1 class="mb-4">Edit Transaction</h1>

    <form action="{{ route('transactions.update', $transaction->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="type" class="form-label">Transaction Type</label>
            <input type="text" class="form-control" id="type" name="type" value="{{ old('type', $transaction->type) }}" required>
        </div>

        <div class="mb-3">
            <label for="date" class="form-label">Date</label>
            <input type="date" class="form-control" id="date" name="date" value="{{ old('date', $transaction->date) }}" required>
        </div>

        <div class="mb-3">
            <label for="total_price" class="form-label">Total Price</label>
            <input type="number" step="0.01" class="form-control" id="total_price" name="total_price" value="{{ old('total_price', $transaction->total_price) }}" required>
        </div>

        <!-- Add more fields as necessary -->

        <button type="submit" class="btn btn-primary">Update Transaction</button>
        <a href="{{ route('transactions.show', $transaction->id) }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
