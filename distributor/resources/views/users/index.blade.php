@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h1 class="mb-4">Akun</h1>

    @if(session('success'))
        <div class="alert alert-success" role="alert">
            {{ session('success') }}
        </div>
    @endif

    <a href="{{ route('users.create') }}" class="btn btn-primary mb-4">
        Tambah Akun Baru
    </a>

    @if($users->isEmpty())
        <p>No users found.</p>
    @else
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                    <tr>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ ucfirst($user->role) }}</td>
                        <td>
                            <a href="{{ route('users.show', $user) }}" class="btn btn-sm btn-info me-1">View</a>
                            <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-warning me-1">Edit</a>
                            <form action="{{ route('users.destroy', $user) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
