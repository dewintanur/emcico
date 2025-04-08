@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="card shadow rounded">
        <div class="card-header text-white" style="background-color:#091F5B;">
            <h4 class="mb-0">Edit Barang</h4>
        </div>

        <div class="card-body">
            <form action="{{ route('list_barang.update', $barang->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="nama_barang" class="form-label">Nama Barang</label>
                    <input type="text" name="nama_barang" class="form-control" value="{{ old('nama_barang', $barang->nama_barang) }}" required>
                </div>

                <div class="mb-3">
                    <label for="jumlah" class="form-label">Jumlah</label>
                    <input type="number" name="jumlah" class="form-control" value="{{ old('jumlah', $barang->jumlah) }}" required>
                </div>

                <div class="mb-3">
                    <label for="satuan" class="form-label">Satuan</label>
                    <input type="text" name="satuan" class="form-control" value="{{ old('satuan', $barang->satuan) }}" required>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('list_barang.index') }}" class="btn btn-secondary">Kembali</a>
                    <button type="submit" class="btn text-white" style="background-color:#091F5B;">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
