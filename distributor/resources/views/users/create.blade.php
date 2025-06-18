@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4">
    <h1 class="text-2xl font-bold mb-4">Buat Akun</h1>

    @if ($errors->any())
        <div class="mb-4 p-4 bg-red-100 text-red-700 rounded">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>- {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('users.store') }}" method="POST" class="space-y-6 max-w-md mx-auto">
        @csrf

        <div class="mb-4">
            <label for="name" class="block font-medium text-gray-700 mb-1">Nama</label>
            <input type="text" name="name" id="name" value="{{ old('name') }}" required autofocus
                class="w-full px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 rounded">
        </div>

        <div class="mb-4">
            <label for="email" class="block font-medium text-gray-700 mb-1">Email</label>
            <input type="email" name="email" id="email" value="{{ old('email') }}" required
                class="w-full px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 rounded">
        </div>

        <div class="mb-4">
            <label for="role" class="block font-medium text-gray-700 mb-1">Role</label>
            <select name="role" id="role" required
                class="w-full px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 rounded">
                <option value="">-- Select Role --</option>
                <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                <option value="employee" {{ old('role') == 'employee' ? 'selected' : '' }}>Employee</option>
            </select>
        </div>

        <div class="mb-4">
            <label for="password" class="block font-medium text-gray-700 mb-1">Password</label>
            <input type="password" name="password" id="password" required
                class="w-full px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 rounded">
        </div>

        <div class="mb-6">
            <label for="password_confirmation" class="block font-medium text-gray-700 mb-1">Confirm Password</label>
            <input type="password" name="password_confirmation" id="password_confirmation" required
                class="w-full px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 rounded">
        </div>

        <div class="flex items-center justify-between">
            <br>
            <button type="submit" class="btn btn-primary px-6 py-2">
                Simpan
            </button>
            <a href="{{ route('users.index') }}" class="btn btn-danger px-6 py-2">
                Batal
            </a>
        </div>

    </form>
</div>
@endsection
