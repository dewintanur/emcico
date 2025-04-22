@extends('layouts.app')

@php
    use Carbon\Carbon;
    Carbon::setLocale('id');
@endphp

@section('content')
    <div class="container py-4 d-flex justify-content-center">
        <div class="card p-4 shadow-sm" style="width: 100%; max-width: 450px; border-radius: 15px;">
            <h4 class="text-center mb-4 fw-bold" style="font-size: 20px; color: var(--bs-primary);">Isi Data Check-In</h4>

            <form id="checkinForm" action="{{ route('proses_checkin') }}" method="POST">
                @csrf
                <input type="hidden" name="kode_booking" value="{{ $booking->kode_booking }}">
                <input type="hidden" id="signatureData" name="signatureData">

                <div class="mb-3 d-flex align-items-center">
                    <label for="name" class="form-label me-3" style="width: 150px;">Nama</label>
                    <input type="text" class="form-control" id="name" name="name"
                        style="border-radius: 15px; border-color:var(--bs-secondary);background-color: var(--bs-secondary);"
                        required>
                </div>
                <div class="mb-3">
    <div class="d-flex align-items-center">
        <label for="phone" class="form-label me-3" style="width: 150px;">No Telepon</label>
        <input type="tel" class="form-control" id="phone" name="phone"
            required minlength="12" maxlength="12"
            pattern="\d{12}"
            title="Nomor harus terdiri dari 12 angka"
            style="border-radius: 15px; border-color:var(--bs-secondary); background-color: var(--bs-secondary);"
            oninput="this.value=this.value.replace(/[^0-9]/g,'')">
    </div>

    <!-- Pesan error berada di bawah form input, sejajar dengan input -->
    <small id="phoneError" class="text-warning d-none" style="font-size: 12px; padding-left: 130px;">
    Nomor HP harus 12 digit
    </small>
</div>



                <h5 class="mb-3 mt-4 text-center fw-bold" style="font-size: 18px; color: var(--bs-primary);">Detail Booking</h5>
                <div class="card p-3 mb-4 text-center mx-auto" style="border-radius: 15px; width:300px; font-size: 16px;">
                    <p class="text-center mb-1" style="color: var(--bs-primary); font-weight: 500;">
                        {{ $booking->nama_event }}</p>
                        <p class="text-center mb-1">
                        {{ $booking->ruangan->nama_ruangan }} - Lantai {{ $booking->lantai }}
                    </p>                   
                    <p class="text-center mb-1">{{ Carbon::parse($booking->tanggal)->translatedFormat('l, d F Y') }}</p>
                    <p class="text-center mb-1">
                        {{ Carbon::parse($booking->waktu_mulai)->format('H:i') }} -
                        {{ Carbon::parse($booking->waktu_selesai)->format('H:i') }}
                    </p>
                    <p class="text-center fw-bold mb-0">{{ $booking->nama_pic }}</p>
                </div>

                <button type="button" class="btn w-auto px-4 fw-bold mx-auto d-block" data-bs-toggle="modal"
                    data-bs-target="#termsModal"
                    style="background-color: var(--bs-primary); color: white; border-radius: 20px;">
                    Check-In
                </button>
            </form>
        </div>
    </div>

    <!-- Modal Syarat dan Ketentuan -->
    <div class="modal fade" id="termsModal" tabindex="-1" aria-labelledby="termsModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header justify-content-center">
                    <h5 class="modal-title" style="color: #091F5B; font-weight:800;">Syarat dan Ketentuan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <ul style="font-size: 14px;">
                      
                        <li>Penggunaan ruangan maksimal hingga pukul 21.00 WIB.</li>
                        <li>Menjaga kebersihan dan kerapihan ruangan selama dan setelah penggunaan.</li>
                        <li>Dilarang merusak, memindahkan, atau menghilangkan fasilitas ruangan tanpa izin.</li>
                        <li>Jika terjadi kerusakan, peminjam wajib melaporkan kepada petugas yang berwenang.</li>
                        <li>Penggunaan ruangan hanya untuk keperluan yang telah disetujui dalam proses booking.</li>
                        <li>Dilarang membawa makanan/minuman berlebih yang dapat mengotori ruangan, kecuali telah mendapat izin.</li>
                        <li>Barang-barang pribadi dan sisa acara harus dibersihkan sebelum waktu peminjaman berakhir.</li>
                        <li>Peminjam bertanggung jawab penuh atas kegiatan selama penggunaan ruangan.</li>
                    </ul>

                    <!-- Signature -->
                    <div class="d-flex flex-column align-items-start">
                        <canvas id="signature" class="mb-3" width="300" height="150"
                            style="border:1px solid #ccc;"></canvas>
                        <button type="button" class="btn btn-secondary" id="clearSignature">Clear</button>
                        <p style="font-style: italic; font-size: 12px;">Silahkan menandatangani untuk menyetujui syarat dan
                            ketentuan.</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary btn-center" form="checkinForm">Setuju</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function () {
        var canvas = document.getElementById('signature');
        var ctx = canvas.getContext('2d');
        var isDrawing = false;

        function resizeCanvas() {
            canvas.width = canvas.clientWidth;
            canvas.height = 150;
            ctx.clearRect(0, 0, canvas.width, canvas.height);
        }

        var termsModal = document.getElementById('termsModal');
        termsModal.addEventListener('shown.bs.modal', function () {
            resizeCanvas();
        });

        canvas.addEventListener('mousedown', function (e) {
            isDrawing = true;
            ctx.beginPath();
            ctx.moveTo(e.offsetX, e.offsetY);
        });

        canvas.addEventListener('mousemove', function (e) {
            if (isDrawing) {
                ctx.lineTo(e.offsetX, e.offsetY);
                ctx.strokeStyle = "#000";
                ctx.lineWidth = 2;
                ctx.stroke();
            }
        });

        canvas.addEventListener('mouseup', function () {
            isDrawing = false;
        });

        document.getElementById('clearSignature').addEventListener('click', function () {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
        });

        document.getElementById('checkinForm').addEventListener('submit', function (e) {
            var signatureData = canvas.toDataURL('image/png');
            if (signatureData.length <= 100) {
                e.preventDefault();
                alert("Harap tambahkan tanda tangan untuk melanjutkan.");
            } else {
                document.getElementById('signatureData').value = signatureData;
            }
        });
    });
    document.addEventListener("DOMContentLoaded", function () {
    const phoneInput = document.getElementById('phone');
    const phoneError = document.getElementById('phoneError');

    if (phoneInput && phoneError) {
        phoneInput.addEventListener('input', function () {
            const value = this.value.replace(/[^0-9]/g, '');
            this.value = value.slice(0, 12);

            if (this.value.length < 12) {
                phoneError.classList.remove('d-none');
            } else {
                phoneError.classList.add('d-none');
            }
        });
    }
});

</script>
@endpush

