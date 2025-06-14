@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4">
    <h1 class="text-2xl font-bold mb-4">User Details</h1>

    <div class="mb-4">
        <strong>Name:</strong> {{ $user->name }}
    </div>

    <div class="mb-4">
        <strong>Email:</strong> {{ $user->email }}
    </div>

    <a href="{{ route('users.index') }}" class="text-blue-600 hover:underline">Back to Users List</a>
</div>
@endsection
