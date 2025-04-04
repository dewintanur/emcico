<!-- Modal Detail Acara -->
<div class="modal fade" id="eventModal{{ $booking->id }}" tabindex="-1"
    aria-labelledby="eventModalLabel{{ $booking->id }}" aria-hidden="true">
    <div class="modal-dialog" style="max-width: 600px;">
        <div class="modal-content p-0 rounded-3">
            <div class="modal-header" style="border: none; padding-bottom: 0px;">
                <h3 class="modal-title w-100 text-center" id="eventModalLabel{{ $booking->id }}"
                    style="color: #091F5B;">
                    Detail Acara
                </h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="padding-top: 0px;">
                <div class="text-center mb-2" style="border-bottom: 3px solid #091F5B;">
                    <div style="font-size: 1.5rem;">{{ $booking->nama_event }}</div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Nama PIC:</strong></p>
                        <p>{{ $booking->nama_pic }}</p>
                        <p><strong>Kategori Ekraf:</strong></p>
                        <p>{{ $booking->kategori_ekraf }}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>No Telp:</strong></p>
                        <p>{{ $booking->no_pic }}</p>
                        <p><strong>Kategori Event:</strong></p>
                        <p>{{ $booking->kategori_event }}</p>
                    </div>
                </div>

                @php
                    $peminjaman = \App\Models\PeminjamanBarang::where('kode_booking', $booking->kode_booking)->exists();
                @endphp

                @if ($peminjaman)
                    <div class="mt-3 p-2 border rounded" style="background-color: #f8f9fa;">
                        <p class="mb-1"><strong>Peminjaman Barang:</strong></p>
                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal"
                            data-bs-target="#peminjamanModal{{ $booking->id }}">
                            Lihat Detail
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
<!-- Modal Peminjaman Barang (Langsung Berbentuk Seperti Formulir) -->
<div class="modal fade" id="peminjamanModal{{ $booking->id }}" tabindex="-1"
    aria-labelledby="peminjamanModalLabel{{ $booking->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content p-0 border-0 shadow-none bg-transparent"> <!-- Hapus kotak modal -->
            <div class="modal-body p-0"> <!-- Hilangkan padding biar langsung pas -->

                <!-- Formulir Peminjaman Barang (langsung modalnya berbentuk ini) -->
                <div class="container">
                    <div class="card shadow p-3" style="border-radius: 15px; background: white;"> <!-- Ini yang jadi modalnya -->
                        
                        <!-- Header Formulir + Tombol Close -->
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="mb-0" style="color: #091F5B;">Formulir Peminjaman Barang</h4>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>

                        @php
                            use App\Models\Kehadiran;
                            use App\Models\PeminjamanBarang;

                            $peminjaman = PeminjamanBarang::where('kode_booking', $booking->kode_booking)->get();
                            $kehadiran = Kehadiran::where('kode_booking', $booking->kode_booking)->latest()->first();
                            @endphp

                        <div class="info-card border mb-2 p-2 rounded" style="font-size: 14px;">
                            <p class="mb-0"><strong>Nama Event:</strong>
                                <span style="color: var(--bs-primary); font-weight: bold; margin-left: 10px;">
                                    {{ $booking->nama_event ?? 'Tidak Ada Data' }}
                                </span>
                            </p>
                        </div>

                        <div class="info-card border p-2 mb-3 rounded" style="font-size: 14px;">
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Ruangan:</strong>
                                        <span style="color: var(--bs-primary); margin-left: 10px;">
                                            {{ $booking->nama_ruangan ?? 'Tidak Ada Data' }}
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

                        @if ($peminjaman->count() > 0)
                            <table class="table table-bordered mx-auto rounded-sm"
                                style="width: 100%; border-radius: 10px; overflow: hidden;">
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

                    </div> <!-- Penutup Card (Modalnya) -->
                </div> <!-- Penutup Container -->

            </div> <!-- Penutup Modal Body -->
        </div>
    </div>
</div>


<!-- Script untuk Mengatasi Shadow Tidak Hilang -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var modals = document.querySelectorAll('.modal');

        modals.forEach(function (modal) {
            modal.addEventListener('hidden.bs.modal', function () {
                document.body.classList.remove('modal-open');
                document.querySelector('.modal-backdrop')?.remove();
            });
        });
    });
</script>