@extends('layouts.app')

@section('title', 'History Peminjaman Barang')

@section('content')
<div class="container py-4">
    <!-- Header -->
    <div class="text-center mb-4">
        <h1 class="h3 fw-bold">History Peminjaman Barang</h1>
    </div>

    <!-- Alert Section -->
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm text-center" role="alert">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Table Header (Lebih Kecil & Rounded) -->
    <div class="row fw-bold text-center my-3 mx-1 px-3 py-2 rounded-3 shadow-sm" 
        style="background-color: #091F5B; color: white; max-width: 98%; margin: auto;">
        <div class="col-1">#</div>
        <div class="col-2">Tanggal</div>
        <div class="col-2">Kode Booking</div>
        <div class="col-2">Nama Item</div>
        <div class="col-1">Jumlah</div>
        <div class="col-2">Ditambah Oleh</div>
        <div class="col-2">Dihapus Oleh</div>
    </div>

    <!-- Table Content (Setiap Baris Jadi Card) -->
    @forelse ($riwayat as $index => $item)
        <div class="card shadow-sm border-0 rounded-4 p-3 my-2 mx-1" style="max-width: 98%; margin: auto;">
            <div class="row align-items-center text-center">
                <div class="col-1 fw-bold">{{ $index + 1 }}</div>
                <div class="col-2">{{ \Carbon\Carbon::parse($item->created_at)->format('d-m-Y H:i') }}</div>
                <div class="col-2 fw-semibold">{{ $item->kode_booking }}</div>
                <div class="col-2">{{ $item->nama_barang }}</div>
                <div class="col-1 fw-bold text-primary">{{ $item->jumlah }}</div>
                <div class="col-2 text-success">{{ $item->created_by_user ?? '-' }}</div>
                <div class="col-2 text-danger">{{ $item->deleted_by_user ?? '-' }}</div>
            </div>
        </div>
    @empty
        <div class="text-center text-muted py-4">
            <i class="fas fa-box-open fa-2x mb-2"></i><br>
            Tidak ada data history
        </div>
    @endforelse

    <!-- Pagination -->
    @if ($riwayat->hasPages())
        <nav aria-label="Page navigation" class="mt-4">
            <ul class="pagination justify-content-center">
                <li class="page-item {{ $riwayat->onFirstPage() ? 'disabled' : '' }}">
                    <a class="page-link rounded-pill shadow-sm" href="{{ $riwayat->previousPageUrl() }}">
                        <i class="fas fa-chevron-left"></i>
                    </a>
                </li>

                @for ($i = 1; $i <= $riwayat->lastPage(); $i++)
                    <li class="page-item {{ $i == $riwayat->currentPage() ? 'active' : '' }}">
                        <a class="page-link rounded-pill shadow-sm" href="{{ $riwayat->url($i) }}">{{ $i }}</a>
                    </li>
                @endfor

                <li class="page-item {{ $riwayat->hasMorePages() ? '' : 'disabled' }}">
                    <a class="page-link rounded-pill shadow-sm" href="{{ $riwayat->nextPageUrl() }}">
                        <i class="fas fa-chevron-right"></i>
                    </a>
                </li>
            </ul>
        </nav>
    @endif
</div>
@endsection
