@extends('layouts.app')

@section('content')
<div class="container mt-5 d-flex justify-content-center">
    <div class="card shadow-lg p-4" style="width: 50%;">
        <div class="card-header text-white text-center" style="background-color:#091F5B; border-radius: 8px;">
            <h3 class="mb-0" > Tambah Barang</h3>
        </div>
        <div class="card-body">
            {{-- Notifikasi Sukses --}}
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show text-center" role="alert">
                    ✅ {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            {{-- Form Tambah Barang --}}
            <form action="{{ route('list_barang.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="nama_barang" class="form-label fw-bold">Nama Barang</label>
                    <input type="text" class="form-control" name="nama_barang" required>
                </div>

                <div class="mb-3">
                    <label for="jumlah" class="form-label fw-bold">Jumlah</label>
                    <input type="number" class="form-control" name="jumlah" required min="1">
                </div>

                <div class="mb-3">
                    <label for="satuan" class="form-label fw-bold">Satuan</label>
                    <input type="text" class="form-control" name="satuan" required>
                </div>

                <div class="text-center">
                    <button type="submit" class="btn text-white fw-bold px-4" style="background-color:#091F5B; border-radius: 8px;">Simpan Barang</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
