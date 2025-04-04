@extends('layouts.app')

@section('content')
<div class="container" style="max-width: 95%;">
    <h2 class="text-center mb-4" style="color: #091F5B;">Riwayat Check-in: {{ $kode_booking }}</h2>

    <a href="{{ route('riwayat.checkin') }}" class="btn btn-secondary mb-3">Kembali</a>

    <!-- HEADER TABEL -->
    <div class="rounded-3 text-white text-center p-2"
        style="background-color: #091F5B; font-weight: bold;">
        <div class="row">
            <div class="col-1">No</div>
            <div class="col-2">Nama Event</div>
            <div class="col-2">Nama User</div>
            <div class="col-2">Ruangan & Lantai</div>
            <div class="col-1">Duty Officer</div>
            <div class="col-1">Status</div>
            <div class="col-2">Tanggal Check-in</div>
            <div class="col-1">Pinjam Barang</div>
        </div>
    </div>

    <!-- LIST DATA CHECK-IN (PER BARIS SEBAGAI CARD) -->
    <div class="mt-2">
        @foreach ($kehadiran as $index => $data)
        <div class="card mb-2 shadow-sm border-0 p-2"
            style="border-radius: 12px;">
            <div class="row text-center align-items-center">
                <div class="col-1">{{ $loop->iteration }}</div>
                <div class="col-2 text-truncate">{{ $data->booking->nama_event ?? '-' }}</div>
                <div class="col-2 text-truncate">{{ $data->nama_ci }}</div>
                <div class="col-2">
                    <p class="mb-0">{{ $data->booking->ruangan->nama_ruangan ?? 'Tidak Diketahui' }}</p>
                    <small>Lantai {{ $data->booking->lantai ?? '-' }}</small>
                </div>
                <div class="col-1">{{ $data->duty_officer }}</div>
                <div class="col-1">
                    <span class="badge bg-{{ $data->status == 'Selesai' ? 'success' : 'warning' }}">
                        {{ $data->status }}
                    </span>
                </div>
                <div class="col-2">
                    {{ \Carbon\Carbon::parse($data->tanggal_ci)->translatedFormat('d F Y') }}
                </div>
                <div class="col-1">
                    @php
                        $peminjaman = \App\Models\PeminjamanBarang::where('kode_booking', $data->kode_booking)->get();
                    @endphp
                    @if ($peminjaman->count() > 0)
                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal"
                            data-bs-target="#peminjamanModal{{ $data->id }}">
                            Lihat
                        </button>
                    @else
                        <span class="text-muted">Tidak Ada</span>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- MODAL PEMINJAMAN BARANG -->
    @foreach ($kehadiran as $data)
    <div class="modal fade" id="peminjamanModal{{ $data->id }}" tabindex="-1"
        aria-labelledby="peminjamanModalLabel{{ $data->id }}" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content p-0 border-0 shadow-none bg-transparent">
                <div class="modal-body p-0">
                    <div class="container">
                        <div class="card shadow p-4 rounded" style="border-radius: 15px; background: white;">
                            
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h4 class="mb-0" style="color: #091F5B;">Detail Peminjaman Barang</h4>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>

                            @php
                                $peminjaman = \App\Models\PeminjamanBarang::where('kode_booking', $data->kode_booking)->get();
                                $kehadiranTerbaru = \App\Models\Kehadiran::where('kode_booking', $data->kode_booking)->latest()->first();
                            @endphp

                            <div class="info-card border mb-3 p-3 rounded bg-light">
                                <p class="mb-0"><strong>Nama Event:</strong>
                                    <span class="text-primary ms-2">{{ $data->booking->nama_event ?? 'Tidak Ada Data' }}</span>
                                </p>
                            </div>

                            <h5 class="section-title mt-3 mx-2" style="color: #091F5B">List Barang yang Dipinjam</h5>

                            @if ($peminjaman->count() > 0)
                                <table class="table table-bordered">
                                    <thead>
                                        <tr class="text-white text-center" style="background-color: #091F5B;">
                                            <th>No</th>
                                            <th>Nama Item</th>
                                            <th>Jumlah</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($peminjaman as $index => $item)
                                            <tr class="text-center">
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $item->barang->nama_barang }}</td>
                                                <td>{{ $item->jumlah }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @else
                                <p class="text-center text-muted">Tidak ada barang yang dipinjam.</p>
                            @endif

                            <!-- Tanda Tangan -->
                            <div class="container-fluid mt-4">
                                <div class="signature-wrapper d-flex justify-content-between">
                                    <div class="signature-group text-center">
                                        <p class="signature-title">Mengetahui,<br> Marketing</p>
                                        <p>
                                            <img src="{{ asset('images/marketing_ttd.png') }}" alt="Tanda Tangan"
                                                class="img-fluid" style="max-width: 120px;">
                                        </p>
                                    </div>
                                    <div class="signature-group text-center">
                                        <p class="signature-title">Mengetahui,<br> Peminjam</p>
                                        @if ($kehadiranTerbaru && $kehadiranTerbaru->ttd)
                                            <p>
                                                <img src="{{ $kehadiranTerbaru->ttd }}" alt="Tanda Tangan"
                                                    class="img-fluid" style="max-width: 120px;">
                                            </p>
                                        @else
                                            <p class="text-muted">Tanda tangan tidak tersedia.</p>
                                        @endif
                                        <p class="fw-bold text-primary">{{ $kehadiranTerbaru->nama_ci ?? 'Tidak Ada Data' }}</p>
                                    </div>
                                </div>
                            </div>

                        </div> 
                    </div> 
                </div> 
            </div>
        </div>
    </div>
    @endforeach

</div>
@endsection
