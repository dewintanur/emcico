@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="text-center mb-4" style="font-weight: normal; border-bottom: none;">Riwayat Check-in</h1>

    <!-- Filter & Export -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <!-- Filter Tanggal -->
        <input type="date" name="tanggal" id="filter_tanggal"
            class="form-control"
            value="{{ request('tanggal') }}"
            style="width: 180px; border: 2px solid #091F5B; border-radius: 8px;">

        <!-- Tombol Export -->
        <div class="dropdown">
            <button class="btn btn-outline-dark btn-sm dropdown-toggle" type="button"
                id="exportDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fas fa-download"></i> Export
            </button>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="exportDropdown">
                <li>
                    <a class="dropdown-item" href="{{ route('riwayat.checkin.export.excel', ['tanggal' => request('tanggal')]) }}">
                        <i class="fas fa-file-excel text-success"></i> Export Excel
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" href="{{ route('riwayat.checkin.export.pdf', ['tanggal' => request('tanggal')]) }}">
                        <i class="fas fa-file-pdf text-danger"></i> Export PDF
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <!-- Header Tabel -->
    <div class="rounded-3 text-white text-center p-2"
        style="background-color: #091F5B; font-weight: bold;">
        <div class="row">
            <div class="col-1">No</div>
            <div class="col-2">Kode Booking</div>
            <div class="col-2">Nama Event</div>
            <div class="col-2">Nama Organisasi</div>
            <div class="col-2">Nama User</div>
            <div class="col-2">Tanggal & Waktu</div>
            <div class="col-1">Aksi</div>
        </div>
    </div>

    <!-- List Check-in dengan Card per Baris -->
    <div class="mt-2">
        @foreach ($kehadiran as $index => $data)
        <div class="card mb-2 shadow-sm border-0"
            style="border-radius: 12px; padding: 10px;">
            <div class="row text-center align-items-center">
                <div class="col-1">{{ $loop->iteration }}</div>
                <div class="col-2">{{ $data->kode_booking }}</div>
                <div class="col-2 text-truncate">{{ $data->booking->nama_event ?? '-' }}</div>
                <div class="col-2 text-truncate">{{ $data->booking->nama_organisasi ?? '-' }}</div>
                <div class="col-2">{{ $data->nama_ci }}</div>
                <div class="col-2">
    {{ optional($data->booking)->tanggal ? \Carbon\Carbon::parse($data->booking->tanggal)->translatedFormat('d F Y') : '-' }} <br>
    {{ optional($data->booking)->waktu_mulai ? \Carbon\Carbon::parse($data->booking->waktu_mulai)->format('H:i') : '-' }} -
    {{ optional($data->booking)->waktu_selesai ? \Carbon\Carbon::parse($data->booking->waktu_selesai)->format('H:i') : '-' }}
</div>

                <div class="col-1">
                    <a href="{{ route('riwayat.checkin.detail', $data->kode_booking) }}"
                        class="btn" style="background-color: #4C74E1; font-weight: 600; color: black;"> Lihat
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

<!-- Script untuk Auto Filter Tanpa Enter -->
<script>
    document.getElementById('filter_tanggal').addEventListener('change', function () {
        window.location.href = "{{ route('riwayat.checkin') }}?tanggal=" + this.value;
    });
</script>

@endsection
