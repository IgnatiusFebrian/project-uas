@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4">
    <h1 class="text-2xl font-bold mb-4">Daftar Barang Masuk</h1>

    <div class="bg-white shadow-md rounded my-6">
        <table class="table table-striped table-hover table-bordered">
            <thead class="table-light">
                <tr>
                    <th>Tanggal</th>
                    <th>Nama Barang</th>
                    <th>Jumlah</th>
                    <th>User</th>
                    <th>Catatan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($incomingGoods as $incoming)
                <tr>
                    <td>
                        {{ $incoming->date->format('d/m/Y') }}
                    </td>
                    <td>
                        {{ $incoming->item->name }}
                    </td>
                    <td>
                        {{ $incoming->quantity }}
                    </td>
                    <td>
                        {{ $incoming->user->name }}
                    </td>
                    <td>
                        {{ $incoming->notes }}
                    </td>
                    <td>
                        <a href="{{ route('incoming_goods.edit', $incoming->id) }}" class="text-primary">Edit</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        <a href="{{ route('incoming_goods.create') }}" class="btn btn-primary">
            Tambah Barang Masuk
        </a>
    </div>
</div>
@endsection
