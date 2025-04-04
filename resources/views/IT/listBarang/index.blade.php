@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h2 class="text-center mb-4">Daftar Barang</h2>

    @if(session('success'))
        <div class="alert alert-success text-center">
            ✅ {{ session('success') }}
        </div>
    @endif

    <div class="text-end mb-3">
        <a href="{{ route('list_barang.create') }}" class="btn text-white" style="background-color:#091F5B; border-radius: 8px;">Tambah Barang</a>
    </div>

    <div class="card">
        <div class="card-header text-white fw-bold" style="background-color:#091F5B; border-radius: 8px 8px 0 0;">
            <div class="row text-center">
                <div class="col-1">#</div>
                <div class="col-3">Nama Barang</div>
                <div class="col-2">Jumlah</div>
                <div class="col-2">Satuan</div>
                <div class="col-4">Aksi</div>
            </div>
        </div>
        <div class="card-body p-0">
            @foreach($barang as $item)
                <div class="row align-items-center text-center py-3 mx-1 my-2 rounded shadow-sm" 
                    style="background: none; border-bottom: 1px solid #ddd;">
                    <div class="col-1">{{ $loop->iteration }}</div>
                    <div class="col-3">{{ $item->nama_barang }}</div>
                    <div class="col-2">{{ $item->jumlah }}</div>
                    <div class="col-2">{{ $item->satuan }}</div>
                    <div class="col-4 d-flex justify-content-center gap-2">
                        <a href="{{ route('list_barang.edit', $item->id) }}" class="btn btn-warning btn-sm">Edit</a>
                        <form action="{{ route('list_barang.destroy', $item->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus?')">
                                Hapus
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
