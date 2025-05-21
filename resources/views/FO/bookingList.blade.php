@extends('layouts.app')

@push('styles')
    <style>
        .alert-highlight {
            background-color: #F1FEA9 !important;
        }

        .alert-siap {
            background-color: #A3F1BA !important;
        }

        .alert-normal {
            background-color: white !important;
        }

        /* Hanya highlight baris booking, bukan elemen lain seperti body */
        tr.highlight {
            animation: highlightEffect 2s ease-in-out;
        }

        @keyframes highlightEffect {
            0% {
                background-color: #ff0;
            }

            100% {
                background-color: inherit;
            }
        }

        .custom-table {
            border-collapse: collapse;
            border-spacing: 0;
            table-layout: fixed;
            width: 100%;
        }

        /* Notifikasi toast */
        #notification-container {
            position: fixed;
            top: 70px;
            right: 20px;
            z-index: 1050;
        }

        .notification-toast {
            width: 320px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            background: #ffffff;
            padding: 15px;
            margin-bottom: 10px;
            animation: slideIn 0.5s ease forwards;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(100%);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
    </style>
@endpush
@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const container = document.getElementById("notification-container");

            // Tampilkan toast yang ada
            document.querySelectorAll(".notification-toast").forEach(toast => {
                toast.classList.remove("d-none");
                container.appendChild(toast);
            });

            // Tutup manual
            document.querySelectorAll(".btn-close").forEach(button => {
                button.addEventListener("click", function () {
                    const notifId = this.getAttribute("data-id");
                    document.getElementById(`notif-${notifId}`).remove();
                });
            });

            // Tandai dibaca
            document.querySelectorAll(".mark-as-read").forEach(button => {
                button.addEventListener("click", function () {
                    let notificationId = this.getAttribute("data-id");
                    let kodeBooking = this.getAttribute("data-kode");

                    fetch(`/notifications/${notificationId}/read`, {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
                        },
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                document.getElementById(`notif-${notificationId}`).remove();
                                window.location.hash = `#booking-${kodeBooking}`;

                                setTimeout(() => {
                                    let targetRow = document.getElementById(`booking-${kodeBooking}`);
                                    if (targetRow && targetRow.tagName.toLowerCase() === 'tr') {
                                        targetRow.classList.add("highlight");
                                        setTimeout(() => targetRow.classList.remove("highlight"), 2000);
                                    }
                                }, 500);
                            }
                        });
                });
            });

            // Jika reload dengan hash
            let hash = window.location.hash;
            if (hash && hash.startsWith("#booking-")) {
                let targetRow = document.querySelector(hash);
                if (targetRow && targetRow.tagName.toLowerCase() === 'tr') {
                    setTimeout(() => {
                        targetRow.classList.add("highlight");
                        setTimeout(() => {
                            targetRow.classList.remove("highlight");
                        }, 2000);
                    }, 500);
                }
            }

            // Update status table
            setInterval(updateTable, 5000);
            setInterval(checkNotification, 5000);
        });

        function updateTable() {
            $.ajax({
                url: "{{ route('booking.status') }}",
                type: "GET",
                success: function (response) {
                    response.data.forEach(function (item) {
                        let row = $("#booking-" + item.kode_booking);
                        let statusCell = $(".status-" + item.kode_booking);
                        statusCell.text(item.status_konfirmasi);
                        row.removeClass("alert-highlight alert-siap alert-normal");

                        if (item.status.toLowerCase() === "checked-in") {
                            if (item.status_konfirmasi === "belum_siap_checkout") {
                                row.addClass("alert-highlight");
                            } else if (item.status_konfirmasi === "siap_checkout") {
                                row.addClass("alert-siap");
                            }
                        } else if (item.status.toLowerCase() === "checked-out") {
                            row.addClass("alert-normal");
                        }
                    });
                },
                error: function (xhr, status, error) {
                    console.log("AJAX Error:", error);
                }
            });
        }

        function checkNotification() {
            fetch('/notifications/unread')
                .then(response => response.json())
                .then(data => {
                    if (data.ada_notifikasi) {
                        console.log('Ada notifikasi baru');
                        // Tambahkan tindakan tambahan jika perlu
                    }
                })
                .catch(error => console.error('Error:', error));
        }
    </script>
@endpush



@section('content')
    @php
        $isAdminOrIT = Auth::user()->role == 'admin' || Auth::user()->role == 'it';
    @endphp

    <!-- Toast Container -->
    <div id="notification-container"></div>

   @foreach (auth()->user()->unreadNotifications as $notification)

    <div class="notification-toast d-none" id="notif-{{ $notification->id }}">
        <div class="d-flex justify-content-between align-items-start">
            <div style="flex: 1;">
                <p class="mb-1">{{ $notification->data['message'] }}</p>

                @if (!empty($notification->data['note']))
                    <p class="mb-1"><em>Catatan: {{ $notification->data['note'] }}</em></p>
                @endif

                <small class="text-muted">Kode Booking: {{ $notification->data['kode_booking'] ?? '-' }}</small>
            </div>
            <button class="btn-close ms-2" data-id="{{ $notification->id }}"></button>
        </div>
        <div class="mt-2 d-flex justify-content-end">
            @if ($notification->data['kode_booking'])
                <a href="#booking-{{ $notification->data['kode_booking'] }}" class="btn btn-sm btn-primary me-2">
                    Lihat Booking
                </a>
            @endif
            <button class="btn btn-sm btn-secondary mark-as-read" data-id="{{ $notification->id }}"
                data-kode="{{ $notification->data['kode_booking'] }}">
                Tandai Dibaca
            </button>
        </div>
    </div>
@endforeach


    {{-- Session Alert --}}
    @if(session('gagal'))
        <div class="alert alert-danger mt-3">
            {{ session('gagal') }}
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success mt-3">
            {{ session('success') }}
        </div>
    @endif
    <div class="container my-4" style="max-width: 1200px;">
        <div class="display-4 flex-column flex-md-row text-center mb-4">
            <h1 class="display-4 mb-4 text-center">Booking List</h1>
        </div>
        <div class="row align-items-center mb-3" style="margin-top: -10px; ">
            <div class="col-md-3">
                <form method="GET" action="{{ route('fo.bookingList') }}">
                    <select name="status" class="form-select" style="width: 100%; border-radius:10px;"
                        aria-label="Status Filter" onchange="this.form.submit()">
                        <option value="" {{ request('status') == '' ? 'selected' : '' }}>Semua Status</option>
                        <option value="Approved" {{ request('status') == 'Approved' ? 'selected' : '' }}>Booked</option>
                        <option value="Checked-in" {{ request('status') == 'Checked-in' ? 'selected' : '' }}>Checked-in
                        </option>
                        <option value="Checked-out" {{ request('status') == 'Checked-out' ? 'selected' : '' }}>Checked-out
                        </option>
                    </select>
                </form>
            </div>

            <div class="col-md-6 text-center">
                <form method="GET" action="{{ route('fo.bookingList') }}" class="d-inline-block" style="width: 100%; ">
                    <input type="text" name="search" class="form-control" placeholder="Cari Nama Event atau Kode Booking"
                        style="font-style: italic; border-radius:10px;" value="{{ request('search') }}">
                </form>
            </div>

            <script>
                document.addEventListener("DOMContentLoaded", function () {
                    const searchInput = document.querySelector('input[name="search"]');
                    searchInput.addEventListener("keypress", function (event) {
                        if (event.key === "Enter") {
                            event.preventDefault();
                            this.form.submit();
                        }
                    });
                });
            </script>


            <div class="col-md-3 text-end">
                <div class="dropdown">
                    <button class="btn btn-success dropdown-toggle" type="button" id="exportDropdown"
                        data-bs-toggle="dropdown" aria-expanded="false" {{ $isAdminOrIT ? 'disabled' : '' }}>
                        <i class="fas fa-download"></i> Export
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="exportDropdown">
                        <li><a class="dropdown-item" href="{{ route('booking.export.pdf') }}">
                                <i class="fas fa-file-pdf"></i> Export as PDF
                            </a></li>
                        <li><a class="dropdown-item" href="{{ route('booking.export.csv') }}">
                                <i class="fas fa-file-csv"></i> Export as CSV
                            </a></li>
                    </ul>
                </div>
            </div>

        </div>

        <!-- Booking Table -->
        <div class="responsive-container">
            <table class="table custom-table ">
                <thead class="table-header">
                    <tr style="color:white; background-color: var(--bs-primary); width: 100% !important;">
                        <th
                            style="width: 14%; font-weight:600; border-top-left-radius: 10px; border-bottom-left-radius: 10px;">
                            Kode Booking
                        </th>
                        <th style="width: 14%; font-weight:600;">Nama Event</th>
                        <th style="width: 16%; font-weight:600;">Nama Organisasi</th>
                        <th style="width: 19%; font-weight:600;">Ruangan dan Waktu</th>
                        <th style="width: 10%; font-weight:600;">Nama PIC</th>
                        <th style="width: 12%; font-weight:600; text-align: center;">Duty Officer</th>
                        <th style="width: 13%; font-weight:600;">User Check-in</th>
                        <th
                            style="width: 10%; font-weight:600; border-top-right-radius: 10px; border-bottom-right-radius: 10px;">
                            Status
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($bookings as $booking)

                        @php
                            $today = \Carbon\Carbon::now()->toDateString();


                            $kehadiran = \App\Models\Kehadiran::where('kode_booking', $booking->kode_booking)
                                ->whereDate('tanggal_ci', '=', $today) // Ambil hanya yang tanggalnya sama persis dengan hari ini
                                ->latest()
                                ->first();

                            $rowClass = 'alert-normal'; // Default: putih (belum ada check-in)

                            if ($kehadiran) {
                                if ($kehadiran->status === 'checked-in') {
                                    $rowClass = $kehadiran->status_konfirmasi === 'belum_siap_checkout' ? 'alert-highlight' : 'alert-siap';
                                } elseif ($kehadiran->status === 'checked-out') {
                                    $rowClass = 'alert-normal';
                                }
                            }
                        @endphp

                        <tr style="width:100%;">
                            <td colspan="8" style="border: none !important; width: 100% !important; padding: 4px; ">
                                <div id="booking-{{ $booking->kode_booking }}" class="{{ $rowClass }} shadow-sm p-2"
                                    style="border-radius: 10px; box-shadow: 0 4px 4px rgba(0, 0, 0, 0.15); background: white; width: 100%;">
                                    <table class="table table-borderless m-0 w-100" style="table-layout: fixed;">
                                        <tr>
                                            <td style="width: 12%;">{{ $booking->kode_booking }}</td>
                                            <td style="width: 15%;">
                                                <a href="#" data-bs-toggle="modal"
                                                    data-bs-target="#eventModal{{ $booking->id }}" class="fw-bold"
                                                    style="color: #091F5B;">
                                                    {{ $booking->nama_event }}
                                                </a>
                                            </td>
                                            <td style="width: 16%;" class="fw-semibold">{{ $booking->nama_organisasi }}</td>
                                            <td style="width: 18%;">
                                                <p>{{ $booking->nama_ruangan ?? 'Tidak Diketahui' }}<br>
                                                    <span>Lantai {{ $booking->lantai ?? '-' }}</span><br>
                                                    <span>{{ date('H:i', strtotime($booking->waktu_mulai)) }} -
                                                        {{ date('H:i', strtotime($booking->waktu_selesai)) }}</span>
                                                </p>
                                            </td>
                                            <td style="width: 10%; text-align: center;">{{ $booking->nama_pic }}</td>
                                            <td style="width: 12%;">
                                                @if ($booking->checkin_status === 'Checked-out')
                                                    <span>{{ \App\Models\User::find($booking->duty_officer)->nama ?? 'Duty Officer Tidak Ditemukan' }}</span>
                                                @elseif ($booking->checkin_status === 'Checked-in')
                                                    @if ($booking->duty_officer)
                                                        <span>{{ \App\Models\User::find($booking->duty_officer)->nama ?? 'Duty Officer Tidak Ditemukan' }}</span>
                                                    @else

                                                        <button type="button" class="btn btn-sm"
                                                            style="background-color: rgba(91, 76, 225, 0.5); color:#3207CF; font-weight: 600;"
                                                            data-bs-toggle="modal" data-bs-target="#dutyOfficerModal{{ $booking->id }}">
                                                            Pilih Duty Officer
                                                        </button>
                                                    @endif

                                                @else

                                                    <button type="button" class="btn btn-sm"
                                                        style="background-color: rgba(91, 76, 225, 0.5); color:#3207CF; font-weight: 600;"
                                                        onclick="showCheckinAlert()" {{ $isAdminOrIT ? 'disabled' : '' }}>
                                                        Pilih Duty Officer
                                                    </button>

                                                    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
                                                    <script>
                                                        function showCheckinAlert() {
                                                            Swal.fire({
                                                                icon: 'warning',
                                                                title: 'Oops!',
                                                                text: 'Silakan check-in terlebih dahulu sebelum memilih Duty Officer.',
                                                                confirmButtonColor: '#3207CF',
                                                                confirmButtonText: 'Mengerti'
                                                            });
                                                        }
                                                    </script>
                                                @endif

                                            </td>

                                            <td style="width: 13%;">
                                                @if (!empty($booking->nama_ci) && !empty($booking->no_ci))
                                                    {{ $booking->nama_ci }}<br>
                                                    @php
                                                        // Ambil nomor dan bersihkan karakter non-angka
                                                        $cleaned = preg_replace('/\D/', '', $booking->no_ci);

                                                        // Ubah 08xxxx menjadi 62xxxx
                                                        if (Str::startsWith($cleaned, '0')) {
                                                            $no_ci = '62' . substr($cleaned, 1);
                                                        } elseif (Str::startsWith($cleaned, '62')) {
                                                            $no_ci = $cleaned;
                                                        } else {
                                                            $no_ci = '62' . $cleaned; // fallback jika nomor tidak dimulai dari 0 atau 62
                                                        }
                                                    @endphp

                                                    <a href="https://wa.me/{{ $no_ci }}" target="_blank" style="color: #25D366;">
                                                        {{ $booking->no_ci }}
                                                    </a>

                                                @else

                                                    <em class="text-secondary">Belum Check-in</em>
                                                @endif

                                            </td>
                                            <td style="width: 10%;">
                                                @if ($booking->checkin_status === 'Checked-in')
                                                    {{-- Jika statusnya sudah Checked-in, tombolnya berfungsi untuk Checkout --}}
                                                    <button
                                                        class="btn btn-sm w-100 d-flex align-items-center justify-content-center custom-shadow fw-bold"
                                                        style="background-color: rgba(76, 116, 225, 0.5); color:#3207CF;"
                                                        data-bs-toggle="modal" @if (!empty($booking->status_konfirmasi) && $booking->status_konfirmasi === 'siap_checkout')
                                                            data-bs-target="#checkoutModal{{ $booking->id }}" {{-- Kalau siap checkout,
                                                        klik buka modal checkout --}} @else
                                                        data-bs-target="#dutyOfficerWarningModal" @endif>
                                                        Check-In {{-- Ini sebenarnya tombol untuk Checkout --}}
                                                    </button>
                                                @elseif ($booking->checkin_status === 'Checked-out')
                                                    {{-- Jika sudah Checkout, tombol menjadi Check-Out dan tidak bisa diklik --}}
                                                    <span class="btn btn-sm w-100 custom-shadow fw-bold"
                                                        style="background-color: rgba(243, 85, 88, 0.5); color:#F40928; pointer-events: none; border: 2px solid white;">
                                                        Check-Out
                                                    </span>

                                                @else

                                                    {{-- Status awal masih Booked, tombol akan mengarah ke proses Check-in --}}
                                                    <a href="{{ $isAdminOrIT ? '#' : route('checkin', ['kode_booking' => $booking->kode_booking]) }}"
                                                        class="btn w-100 custom-shadow"
                                                        style="background-color: rgba(150, 150, 150, 0.5); font-weight: 600; color: #696969;">
                                                        Booked
                                                    </a>
                                                @endif


                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </td>


                            <div class="modal fade" id="checkoutModal{{ $booking->id }}" tabindex="-1"
                                aria-labelledby="checkoutModalLabel{{ $booking->id }}" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="checkoutModalLabel{{ $booking->id }}">Konfirmasi
                                                Checkout</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p>Apakah Anda yakin ingin melakukan checkout untuk:</p>
                                            <ul>
                                                <li><strong>Kode Booking:</strong> {{ $booking->kode_booking }}</li>
                                                <li><strong>Nama:</strong> {{ $booking->nama_pic }}</li>
                                                <li><strong>Ruangan:</strong>
                                                    {{ $booking->nama_ruangan ?? 'Tidak ada data' }}</li>
                                            </ul>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Batal</button>
                                            <form action="{{ route('checkout') }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="kode_booking" value="{{ $booking->kode_booking }}">
                                                <button type="submit" class="btn btn-danger">Konfirmasi Checkout</button>
                                            </form>

                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal fade" id="dutyOfficerWarningModal" tabindex="-1"
                                aria-labelledby="dutyOfficerWarningLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="dutyOfficerWarningLabel">Peringatan</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p>Ruangan belum siap untuk checkout. Harap menunggu konfirmasi dari Duty
                                                Officer.</p>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Tutup</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </tr>
                        <!-- Modal Pilih Duty Officer -->
                        <div class="modal fade" id="dutyOfficerModal{{ $booking->id }}" tabindex="-1"
                            aria-labelledby="modalLabel{{ $booking->id }}" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="modalLabel{{ $booking->id }}" {{ $isAdminOrIT ? 'disabled' : '' }}>Pilih Duty Officer
                                        </h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form action="{{ route('assign.dutyofficer', $booking->id) }}" method="POST">
                                            @csrf
                                            <label for="duty_officer">Duty Officer:</label>
                                            <select name="duty_officer" class="form-control">
                                                @foreach ($dutyOfficers as $officer)
                                                    <option value="{{ $officer->id }}">{{ $officer->nama }}</option>
                                                @endforeach
                                            </select>
                                            <div class="modal-footer mt-3">
                                                <button type="submit" class="btn btn-primary">Simpan</button>
                                                <button type="button" class="btn btn-secondary"
                                                    data-bs-dismiss="modal">Batal</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </tbody>
            </table>
        </div>
        @foreach ($bookings as $booking)
            @include('FO.detail_acara', ['booking' => $booking])
        @endforeach

        <nav aria-label="Page navigation">
            <ul class="pagination justify-content-center">
                @for ($page = 1; $page <= $bookings->lastPage(); $page++)
                    <li class="page-item {{ $bookings->currentPage() == $page ? 'active' : '' }}">
                        <a class="page-link" href="{{ $bookings->url($page) }}">
                            {{ $page }}
                        </a>
                    </li>
                @endfor
            </ul>
        </nav>
    </div>
    </body>
@endsection