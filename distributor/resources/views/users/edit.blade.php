@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4">
    <h1 class="text-2xl font-bold mb-4">Edit Akun</h1>

    @if ($errors->any())
        <div class="mb-4 p-4 bg-red-100 text-red-700 rounded">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>- {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('users.update', $user->id) }}" method="POST" class="space-y-4">
        @csrf
        @method('PATCH')

        <div>
            <label for="name" class="block font-medium text-gray-700">Nama</label>
            <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required autofocus
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
        </div>

        <div>
            <label for="email" class="block font-medium text-gray-700">Email</label>
            <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
        </div>

        <div>
            <label for="password" class="block font-medium text-gray-700">Password</label>
            <input type="password" name="password" id="password"
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
        </div>

        <div>
            <label for="password_confirmation" class="block font-medium text-gray-700">Confirm Password</label>
            <input type="password" name="password_confirmation" id="password_confirmation"
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                <h1>
        </div>

        <div class="flex items-center justify-between">
            <button type="submit" class="btn btn-primary">
                Simpan
            </button>
            <a href="{{ route('users.index') }}" class="btn btn-danger">
                Batal
            </a>
        </div>
    </form>

</div>
@endsection
