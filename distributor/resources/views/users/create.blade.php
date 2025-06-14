@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4">
    <h1 class="text-2xl font-bold mb-4">Create User</h1>

    @if ($errors->any())
        <div class="mb-4 p-4 bg-red-100 text-red-700 rounded">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>- {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('users.store') }}" method="POST" class="space-y-4">
        @csrf

        <div>
            <label for="name" class="block font-medium text-gray-700">Name</label>
            <input type="text" name="name" id="name" value="{{ old('name') }}" required autofocus
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
        </div>

        <div>
            <label for="email" class="block font-medium text-gray-700">Email</label>
            <input type="email" name="email" id="email" value="{{ old('email') }}" required
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
        </div>

        <div>
            <label for="password" class="block font-medium text-gray-700">Password</label>
            <input type="password" name="password" id="password" required
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
        </div>

        <div>
            <label for="password_confirmation" class="block font-medium text-gray-700">Confirm Password</label>
            <input type="password" name="password_confirmation" id="password_confirmation" required
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
        </div>

        <div>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                Create User
            </button>
        </div>
    </form>
</div>
@endsection
