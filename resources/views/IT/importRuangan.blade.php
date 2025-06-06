@extends('layouts.app')

@section('content')
<div class="container mt-5 d-flex justify-content-center">
    <div class="card shadow-lg p-4" style="width: 50%; border-radius: 8px;">
        <div class="card-header text-white" style="background-color:#091F5B; border-radius: 8px;">
            <h3 class="mb-0 text-center">Import Data Ruangan</h3>
        </div>
        <div class="card-body">
            {{-- Notifikasi Sukses --}}
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show text-center" role="alert">
                    ✅ {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            {{-- Notifikasi Error --}}
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show text-center" role="alert">
                    ❌ {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            {{-- Form Import --}}
            <form action="{{ route('ruangan.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-3 text-center">
                    <label for="file" class="form-label fw-bold">📎 Pilih File CSV / SQL</label>
                    <input type="file" class="form-control" name="file" required accept=".csv,.txt,.sql">
                    </div>
                <div class="text-center">
                    <button type="submit" class="btn px-4 text-white" style="background-color:#091F5B; border-radius: 8px;">🚀 Import Sekarang</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
