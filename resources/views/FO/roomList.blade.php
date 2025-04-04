@extends('layouts.app')
@push('styles')
    <style>
        .room-card {
            height: 150px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            border-radius: 15px;
            background: white;
            padding: 15px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 15px;
        }

        .room-status {
            padding: 5px 10px;
            border-radius: 10px;
            font-weight: bold;
            font-size: 14px;
        }

        .room-status.dipesan {
            background-color: #2b2b2b;
            color: #c1c1c1;
        }

        .room-status.sedang-digunakan {
            background-color: #A3F1BA60;
            color: #07CF43;
        }

        .room-status.kosong {
            background-color: #F1A3A450;
            color: #E53235;
        }

        /* Animasi highlight */
        @keyframes bg-flash {
            0% {
                background-color: rgba(255, 0, 0, 0.2);
            }

            50% {
                background-color: rgba(255, 0, 0, 0.5);
            }

            100% {
                background-color: rgba(255, 0, 0, 0.2);
            }
        }

        .room-card.highlight {
            animation: bg-flash 0.8s infinite alternate;
            border: 4px solid red;
        }
    </style>
@endpush

@section('content')
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            setTimeout(() => {
                const urlParams = new URLSearchParams(window.location.search);
                const highlight = urlParams.get("highlight");

                if (highlight) {
                    let roomCard = document.querySelector(`#room-${highlight}`);

                    if (roomCard) {
                        const statusKonfirmasi = roomCard.dataset.statusKonfirmasi; // Ambil status dari data attribute

                        if (statusKonfirmasi !== "siap_checkout") {
                            roomCard.classList.add("highlight");
                            roomCard.scrollIntoView({ behavior: "smooth", block: "center" });
                        }
                    }
                }
            }, 500);
        });


        // setInterval(function () {
        //     location.reload();
        // }, 36000); // Refresh setiap 5 detik

    </script>
    <h1 class="text-center">Room List</h1>
    <div class="container">
        <div class="d-flex justify-content-start">
            <div class="container mb-5">
                <div class="row align-items-center">
                    <div class="col-md-8 d-flex">
                        <form method="GET" action="" class="me-3">
                            <select name="lantai" class="form-select shadow-sm" aria-label="Filter Lantai"
                                onchange="this.form.submit()">
                                <option value="">Semua Lantai</option>
                                @foreach ($lantaiOptions as $lantai)
                                    <option value="{{ $lantai }}" {{ request('lantai') == $lantai ? 'selected' : '' }}>
                                        {{ ucfirst($lantai) }}
                                    </option>
                                @endforeach
                            </select>
                            <input type="hidden" name="status" value="{{ request('status') }}">
                            <input type="hidden" name="search" value="{{ request('search') }}">
                        </form>
                        <form method="GET" action="">
                            <select name="status" class="form-select shadow-sm" aria-label="Filter Status"
                                onchange="this.form.submit()">
                                <option value="">Semua Status</option>
                                <option value="Kosong" {{ request('status') == 'Kosong' ? 'selected' : '' }}>Kosong</option>
                                <option value="Dipesan" {{ request('status') == 'Dipesan' ? 'selected' : '' }}>Dipesan
                                </option>
                                <option value="Sedang Digunakan" {{ request('status') == 'Sedang Digunakan' ? 'selected' : '' }}>Sedang Digunakan</option>
                            </select>
                            <input type="hidden" name="lantai" value="{{ request('lantai') }}">
                            <input type="hidden" name="search" value="{{ request('search') }}">
                        </form>
                    </div>
                    <div class="col-md-4">
                        <form method="GET" action="" class="position-relative">
                            <input type="text" class="form-control py-2 shadow-sm" placeholder="Telusuri" name="search"
                                value="{{ request('search') }}">
                            <input type="hidden" name="lantai" value="{{ request('lantai') }}">
                            <input type="hidden" name="status" value="{{ request('status') }}">
                            <button type="submit" class="btn position-absolute end-0 top-50 translate-middle-y pe-3">
                                <span class="fa fa-search" style="font-size: 18px;"></span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div id="pagination-container">
            <div class="row d-flex align-items-stretch">
                @php
                    $highlightedRoom = request()->query('highlight'); // Ambil highlight dari URL
                @endphp

                @foreach ($ruangan as $room)
                            <div class="col-md-4">
                                <div class="room-card shadow p-3 mb-4 bg-white transition-all duration-500 d-flex flex-column position-relative"
                                    data-room-id="{{ $room->id }}"
                                    data-status-konfirmasi="{{ optional($room->kehadiran->first())->status_konfirmasi }}"
                                    id="room-{{ $room->id }}">

                                    <script>console.log("Room rendered:", {{ $room->id }});</script>

                                    <!-- Header Nama Ruangan & Status -->
                                    <div class="d-flex justify-content-between">
                                        <span class="fw-bold mt-1">{{ $room->nama_ruangan }}</span>
                                        <span
                                            class="room-status {{ strtolower(str_replace(' ', '-', $room->status)) }}">{{ $room->status }}</span>
                                    </div>

                                    <!-- Detail Lantai -->
                                    <p>Lantai: {{ $room->lantai }}</p>

                                    <!-- Jadwal Booking Berikutnya -->
                                    <p>
                                        @if ($room->next_booking)
                                            <span>
                                                {{ \Carbon\Carbon::parse($room->next_booking->waktu_mulai)->format('H:i') }} -
                                                {{ \Carbon\Carbon::parse($room->next_booking->waktu_selesai)->format('H:i') }}
                                            </span><br>
                                        @else
                                            Tidak ada jadwal booking hari ini
                                        @endif
                                    </p>

                                    <!-- Tombol Konfirmasi di Pojok Kanan Bawah -->
                                    @if(
                                        auth()->check()
                                        && auth()->user()->role == 'duty_officer'
                                        && auth()->user()->id == optional($room->kehadiran->first())->duty_officer
                                    )
                                                        <div class="position-absolute bottom-0 end-0 p-2">
                                                            @if(optional($room->kehadiran->first())->status_konfirmasi !== 'siap_checkout')
                                                                <form action="{{ route('ruangan.konfirmasi', $room->id) }}" method="POST" class="d-inline">
                                                                    @csrf
                                                                    <button type="submit" class="btn btn-success btn-sm">Siap CO</button>
                                                                </form>
                                                            @endif

                                                            @if(
                                                                optional($room->kehadiran->first())->status_konfirmasi !== 'siap_checkout' &&
                                                                optional($room->kehadiran->first())->status_konfirmasi !== 'belum_siap_checkout'
                                                            )
                                                                                <form action="{{ route('ruangan.belum_siap', $room->id) }}" method="POST" class="d-inline">
                                                                                    @csrf
                                                                                    <button type="submit" class="btn btn-warning btn-sm">Belum Siap CO</button>
                                                                                </form>
                                                            @endif
                                                        </div>
                                    @endif
                                </div>
                            </div>
                @endforeach

            </div>
        </div>

    </div>
@endsection