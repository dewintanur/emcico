<body class="bg-light">
    <!-- Header -->
    @include('layouts.app')

    <div class="d-flex justify-content-between align-items-center mb-4">

    </div>
    <div class="container py-4">

        <h1 class="display-4 mb-4 text-center">Peminjaman List</h1>

        <!-- Combined Filters -->
        <div class="row mb-4">
            <form method="GET" action="#" class="d-flex align-items-center justify-content-between">
                <!-- Date Filter -->
                <div class="me-3">
                <input type="date" name="date" class="form-control" value="{{ request('date') }}">
                </div>
                <!-- Search Filter -->
                <div>
                <input type="text" name="search" class="form-control" placeholder="Search by Event Name" value="{{ request('search') }}">
                </div>
            </form>
        </div>

        <!-- Table -->
        <div class="container mt-4">
            <div class="card-body text-white my-2 shadow-lg" style="background-color:#091F5B; border-radius: 8px;">
                <div class="row align-items-center">
                    <div class="d-none">Aksi</div>
                    <div class="col-md-3 text-left" style="font-weight: bold">Nama Event</div>
                    <div class="col-md-2 text-left" style="font-weight: bold">Nama Organisasi</div>
                    <div class="col-md-2 text-left" style="font-weight: bold">Tanggal</div>
                    <div class="col-md-2 text-left" style="font-weight: bold">Ruangan dan Waktu</div>
                    <div class="col-md-2 text-left" style="font-weight: bold">Nama PIC</div>
                    <div class="col-md-1 text-left" style="font-weight: bold">Aksi</div>
                </div>
            </div>

            @foreach ($bookings as $booking)
                <div class="card-header text-dark my-2 shadow-sm" style="background-color:white; border-radius: 5px;">
                    <div class="row align-items-center">
                        <div class="d-none">
                            {{ $booking->kode_booking }}
                        </div>
                        <div class="col-md-3 text-left">
                            <a href="#" data-bs-toggle="modal" data-bs-target="#eventModal{{ $booking->id }}"
                                class="fw-bold" style="color: #091F5B;">
                                {{ $booking->nama_event }}
                            </a>
                        </div>
                        <div class="col-md-2 text-left" style="color:#091F5B; font-weight: 600;">
                            {{ $booking->nama_organisasi }}
                        </div>
                        <div class="col-md-2 text-left" style="color:#091F5B; font-weight: 600;">
                            {{ $booking->tanggal }}
                        </div>
                        <div class="col-md-2 text-left" style="color:#091F5B; font-weight: 600;">
                            @if ($booking->ruangan)
                                <p>{{ $booking->ruangan->nama_ruangan }}<br>
                                    <span>{{ $booking->ruangan->lantai }}</span><br>
                                    <span>{{ $booking->waktu_mulai }} - {{ $booking->waktu_selesai }}</span>
                                </p>
                            @else
                                <p>-</p> {{-- Jika ruangan tidak ditemukan, tampilkan "-" --}}
                            @endif

                        </div>
                        <div class="col-md-2 text-left" style="color:#091F5B; font-weight: 600;">
                            {{ $booking->nama_pic }} <br>
                            <a href="https://wa.me/{{ $booking->no_pic }}" target="_blank" style="color: #25D366;">
                                {{ $booking->no_pic }}</a>
                        </div>
                        <div class="col-md-1 text-left">
                            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                data-bs-target="#editModal{{ $booking->id }}">Lihat</button>
                        </div>
                    </div>
                </div>
            @endforeach
            @php
    $peminjaman = \App\Models\PeminjamanBarang::where('kode_booking', $booking->kode_booking)->get();
    $kehadiran = \App\Models\Kehadiran::where('kode_booking', $booking->kode_booking)->latest()->first();
@endphp

            @foreach ($bookings as $booking)
    <div class="modal fade" id="editModal{{ $booking->id }}" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content p-3" style="border-radius: 15px; background: white;">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="mb-0" style="color: #091F5B;">Formulir Peminjaman Barang</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="info-card border mb-2 p-2 rounded">
                    <p><strong>Nama Event:</strong>
                        <span style="color: var(--bs-primary); font-weight: bold; margin-left: 10px;">
                            {{ $booking->nama_event ?? 'Tidak Ada Data' }}
                        </span>
                    </p>
                </div>

                <div class="info-card border p-2 mb-3 rounded">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Ruangan:</strong>
                                <span style="color: var(--bs-primary); margin-left: 10px;">
                                    {{ $booking->ruangan->nama_ruangan ?? 'Tidak Ada Data' }}
                                </span>
                            </p>
                            <p><strong>Tanggal:</strong>
                                <span style="color: var(--bs-primary); margin-left: 10px;">
                                    {{ \Carbon\Carbon::parse($booking->tanggal ?? now())->translatedFormat('d F Y') }}
                                </span>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>PIC:</strong>
                                <span style="color: var(--bs-primary); margin-left: 10px;">
                                    {{ $booking->nama_pic ?? 'Tidak Ada Data' }}
                                </span>
                            </p>
                            <p><strong>Jam:</strong>
                                <span style="color: var(--bs-primary); margin-left: 10px;">
                                    {{ date('H:i', strtotime($booking->waktu_mulai ?? '00:00')) }} - 
                                    {{ date('H:i', strtotime($booking->waktu_selesai ?? '00:00')) }}
                                </span>
                            </p>
                        </div>
                    </div>
                </div>

                <h5 class="section-title mt-3 mx-2" style="color: #091F5B">List Barang yang Dipinjam</h5>


                @if ($booking->peminjaman->count() > 0)
                    <table class="table table-bordered mx-auto rounded-sm">
                        <thead>
                            <tr class="text-white text-center" style="background-color: #091F5B;">
                                <th>No</th>
                                <th>Nama Item</th>
                                <th>Jumlah</th>
                                <th>Status Pengembalian</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($booking->peminjaman as $index => $item)
                                <tr class="text-center">
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $item->barang->nama_barang }}</td>
                                    <td>{{ $item->jumlah }}</td>
                                    <td class="color: {{ $item->status_pengembalian === 'Sudah Dikembalikan' ? 'green' : 'red' }}">{{ $item->status_pengembalian ?? 'Belum Dikembalikan' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p class="text-center text-muted">Tidak ada barang yang dipinjam.</p>
                @endif
                 <!-- Tanda Tangan -->
                 <div class="container-fluid mt-4">
                            <div class="signature-wrapper d-flex justify-content-between mx-auto" style="width: 100%;">
                                <div class="signature-group text-center">
                                    <p class="signature-title">Mengetahui,<br> Marketing</p>
                                    <p>
                                        <img src="{{ asset('images/marketing_ttd.png') }}" alt="Tanda Tangan"
                                            style="width: 120px; height: auto;">
                                    </p>
                                </div>
                                <div class="signature-group text-center">
                                    <p class="signature-title">Mengetahui,<br> Peminjam</p>
                                    @if ($kehadiran && $kehadiran->ttd)
                                        <p>
                                            <img src="{{ $kehadiran->ttd }}" alt="Tanda Tangan"
                                                style="width: 120px; height: auto;">
                                        </p>
                                    @else
                                        <p class="text-muted">Tanda tangan tidak tersedia.</p>
                                    @endif
                                    <p style="color: var(--bs-primary);">{{ $kehadiran->nama_ci ?? 'Tidak Ada Data' }}</spa></p>


                                </div>
                            </div>
                        </div>
            </div>
        </div>
    </div>
@endforeach


        </div>
    </div>
</body>