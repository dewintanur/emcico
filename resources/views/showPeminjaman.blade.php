@extends('layouts.app') <!-- Include navbar -->

@section('content')
    <div class="container mt-3 d-flex justify-content-center">
        <!-- Card utama untuk form peminjaman -->
        <div class="card shadow" style="width: 600px; border-radius: 20px;">
            <div class="card-body">
                <h4 class="text-center mb-4" style="color: #091F5B;">Formulir Peminjaman Barang</h4>

                <!-- Info Card untuk Nama Event -->
                <div class="info-card border mb-2 p-2" style="border-radius: 20px; font-size: 14px;">
                    <div class="d-flex align-items-center">
                        <p class="mb-0">Nama Event
                            <span style="color: var(--bs-primary); font-weight: bold; margin-left: 10px;">
                                {{ $booking->nama_event ?? 'Tidak Ada Data' }}
                            </span>
                        </p>
                    </div>
                </div>

                <!-- Card untuk Ruangan, PIC, Tanggal, dan Jam -->
                <div class="info-card border p-2 mb-3" style="border-radius:20px; font-size: 14px;">
                    <div class="row">
                        <div class="col-md-6 d-flex align-items-center">
                            <p class="mb-0">Ruangan <span style="color: var(--bs-primary); margin-left: 35px;">
                                    {{ $booking->ruangan->nama_ruangan ?? 'Tidak Ada Data' }}
                                </span></p>
                        </div>
                        <div class="col-md-6 d-flex align-items-center">
                            <p class="mb-0">PIC <span style="color: var(--bs-primary); margin-left: 35px;">
                                    {{ $booking->nama_pic ?? 'Tidak Ada Data' }}
                                </span></p>
                        </div>
                        <div class="col-md-6 d-flex align-items-center mt-2">
                            <p class="mb-0">Tanggal <span style="color: var(--bs-primary); margin-left: 45px;">
                                    {{ \Carbon\Carbon::parse($booking->tanggal ?? now())->translatedFormat('d F Y') }}
                                </span></p>
                        </div>
                        <div class="col-md-6 d-flex align-items-center mt-2">
                            <p class="mb-0">Jam <span style="color: var(--bs-primary); margin-left: 30px;">
                                    {{ date('H:i', strtotime($booking->waktu_mulai ?? '00:00')) }} -
                                    {{ date('H:i', strtotime($booking->waktu_selesai ?? '00:00')) }}
                                </span></p>
                        </div>
                    </div>
                </div>

                <!-- List Barang yang Dipinjam -->
                <h5 class="section-title mt-4 mx-2" style="color: #091F5B">List Barang yang Dipinjam</h5>
                <table class="table table-striped mx-auto rounded-sm"
                    style="width: 550px; border-radius: 15px; overflow: hidden; border: 1px solid #ccc;">
                    <thead>
                        <tr style="background-color: #D2E7FF; color: black;">
                            <th>No</th>
                            <th>Nama Item</th>
                            <th>Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($peminjaman as $index => $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $item->barang->nama_barang }}</td>
                                <td>{{ $item->jumlah }}</td>
                            </tr>
                        @endforeach

                </table>

                <!-- Checkbox Syarat dan Ketentuan -->
                <div class="form-check mb-3 ml-4">
                    <input type="checkbox" class="form-check-input" id="agree" name="agree" required>
                    <label class="form-check-label" for="agree" style="color: #091F5B; font-weight:500;">
                        Syarat dan Ketentuan
                    </label>
                </div>

                <!-- Bagian Tanda Tangan -->
                <div class="signature-wrapper d-flex justify-content-between mt-2 mx-auto px-4">
                    <div class="signature-group mt-4 text-center">
                        <p class="signature-title">Mengetahui,<br> Marketing</p>
                        <p><img src="{{ asset('images/marketing_ttd.png') }}" alt="Tanda Tangan"
                                style="width: 80px; height: 80px;"></p>
                    </div>
                    <div class="signature-group mt-4 text-center">
                        <p class="signature-title">Mengetahui,<br> Peminjam</p>
                        @if(session()->has('checkin_data.signatureData'))
                            <p><img src="{{ session('checkin_data.signatureData') }}" alt="Tanda Tangan"
                                    style="width: 80px; height: 80px;"></p>
                            <p>{{ session('checkin_data.name') }}</p>
                        @else
                            <p class="text-danger">Tanda tangan belum tersedia.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Tombol Setuju -->
    <div class="text-center mt-4 mb-4">
        <form action="{{ route('simpan.setuju.peminjaman') }}" method="POST">
            @csrf
            <button type="submit" class="btn" id="setujuButton"
                style="background-color: var(--bs-primary); width:150px; color:white; border-radius: 20px; font-weight: bold;">
                Setuju
            </button>
        </form>
    </div>
    <!-- Modal Syarat dan Ketentuan -->
    <div class="modal fade" id="termsModal" tabindex="-1" aria-labelledby="termsModalLabel" aria-hidden="true">
        <div class="modal-dialog" style="max-width: 500px;">
            <div class="modal-content">
                <div class="modal-header text-center">
                    <h5 class="modal-title" id="termsModalLabel" style="color: #091F5B;">Syarat dan Ketentuan</h5>
                </div>
                <div class="modal-body">
                    <ul class=" mb-1">
                        <li>Peminjam setuju untuk mengembalikan semua pinjaman pada tanggal pengembalian di atas dalam
                            keadaan baik.</li>
                        <li>Peminjam menyanggupi penggantian bila terjadi kehilangan dan kerusakan.</li>
                    </ul>
                </div>
                <div class="modal-footer text-center">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Setuju</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS untuk Modal -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js"></script>

    <script>
        // Ambil elemen checkbox, tombol, dan modal
        const agreeCheckbox = document.getElementById('agree');
        const agreeButton = document.getElementById('agreeButton');
        const termsModal = new bootstrap.Modal(document.getElementById('termsModal'));

        agreeCheckbox.addEventListener('change', function () {
            if (this.checked) {
                termsModal.show();
            } else {
                agreeButton.setAttribute('disabled', 'true');
            }
        });

        document.querySelector('#termsModal .btn-primary').addEventListener('click', function () {
            termsModal.hide();
            if (agreeCheckbox.checked) {
                agreeButton.removeAttribute('disabled');
            }
        });

        document.getElementById('termsModal').addEventListener('hidden.bs.modal', function () {
            if (!agreeCheckbox.checked) {
                agreeButton.setAttribute('disabled', 'true');
            }
        });
        document.addEventListener('DOMContentLoaded', function () {
            const agreeCheckbox = document.getElementById('agree');
            const setujuButton = document.getElementById('setujuButton');

            // Disable tombol saat halaman dimuat
            setujuButton.disabled = true;

            // Event listener untuk checkbox
            agreeCheckbox.addEventListener('change', function () {
                setujuButton.disabled = !this.checked;
            });
        });
    </script>
@endsection