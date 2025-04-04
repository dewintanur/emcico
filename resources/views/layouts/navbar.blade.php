@push('scripts')
    <script>
        function markNotificationAsRead(event, notificationId) {
            event.preventDefault(); // Mencegah reload halaman langsung

            let csrfTokenMeta = document.querySelector('meta[name="csrf-token"]');
            if (!csrfTokenMeta) {
                console.error("CSRF token tidak ditemukan di halaman!");
                return;
            }

            fetch(`/notifikasi/${notificationId}/read`, {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": csrfTokenMeta.content,
                    "Content-Type": "application/json",
                },
                body: JSON.stringify({}),
            })
                .then(response => {
                    if (response.ok) {
                        console.log("Notifikasi ditandai sebagai telah dibaca.");
                        event.target.closest("li").remove(); // Hapus dari dropdown
                        window.location.href = event.target.href; // Redirect setelah notifikasi dihapus
                    }
                })
                .catch(error => console.error("Gagal menandai notifikasi:", error));
        }
    </script>
@endpush

<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
        <a class="navbar-brand" href="{{ url('/') }}">
            <img src="https://event.mcc.or.id/assets/images/logo.png" width="250" alt="Event Malang Creative Center">
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
            <div class="d-flex align-items-center">
                <!-- Tanggal dalam Bahasa Indonesia -->
                <div class="me-3">
                    <span>{{ now()->locale('id')->translatedFormat('l, d F Y') }}</span>
                </div>
                @php
                    // Pastikan user sudah login sebelum mengambil notifikasi
                    $notifications = auth()->check() && auth()->user()->role === 'duty_officer'
                        ? auth()->user()->unreadNotifications
                        : collect();
                @endphp

                @auth
                    @if(auth()->user()->role === 'duty_officer')
                        <!-- Notifikasi untuk Duty Officer -->
                        <li class="nav-item dropdown me-3">
                            <a class="nav-link dropdown-toggle position-relative" href="#" id="notifikasiDropdown" role="button"
                                data-bs-toggle="dropdown">
                                <i class="fas fa-bell"></i>
                                @if($notifications->count() > 0)
                                    <span class="badge bg-danger position-absolute top-0 start-100 translate-middle">
                                        {{ $notifications->count() }}
                                    </span>
                                @endif
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="notifikasiDropdown">
                                @forelse($notifications as $notification)
                                    <li>
                                        <a class="dropdown-item"
                                            href="{{ url('/ruangan?highlight=' . $notification->data['ruangan_id']) }}"
                                            onclick="markNotificationAsRead(event, '{{ $notification->id }}')">
                                            {{ $notification->data['message'] }}
                                        </a>
                                    </li>

                                @empty
                                    <li><a class="dropdown-item" href="#">Tidak ada notifikasi</a></li>
                                @endforelse
                            </ul>

                            <script>
                                document.addEventListener("DOMContentLoaded", function () {
                                    const urlParams = new URLSearchParams(window.location.search);
                                    const highlight = urlParams.get("highlight");

                                    if (highlight) {
                                        console.log("Highlighting ruangan ID:", highlight); // Debugging

                                        let roomCard = document.querySelector(`[data-room-id="${highlight}"]`);
                                        if (roomCard) {
                                            roomCard.classList.add("border-4", "border-red-500", "shadow-lg", "animate-pulse");

                                            // Scroll ke card yang ter-highlight
                                            roomCard.scrollIntoView({ behavior: "smooth", block: "center" });
                                        }
                                    }
                                });
                            </script>

                        </li>
                    @endif

                    <div class="d-flex align-items-center rounded-pill border px-3 py-1"
                        style="border: 2px solid #091F5B; font-family: 'Montserrat', sans-serif; color: #091F5B; background-color: transparent;">
                        <!-- Profile Icon -->
                        <!-- Profile Icon (Ganti dengan Foto Profil) -->
                        @if(Auth::user()->gambar)
                            <img src="{{ asset('storage/profile_images/' . Auth::user()->gambar) }}" alt="Foto Profil"
                                class="rounded-circle me-2" width="32" height="32" style="object-fit: cover;">
                        @else
                            <i class="fas fa-user-circle me-2" style="font-size: 26px; color: #091F5B;"></i>
                        @endif

                        <!-- Role and Name -->
                        <div class="text-start">
                            <div class="fw-bold" style="color: #091F5B; font-size: 12px;">{{ auth()->user()->role }}</div>
                            <div style="font-size: 14px; color: #091F5B;">{{ auth()->user()->nama }}</div>
                        </div>

                        <!-- Dropdown Menu -->
                        <div class="dropdown">
                            <a href="#" class="d-flex align-items-center text-decoration-none" id="dropdownUser"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-chevron-down ms-2" style="color: #091F5B;"></i>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="dropdownUser"
                                style="font-family: 'Montserrat', sans-serif; font-size: 14px; min-width: 200px;">
                                @if (auth()->user()->role === 'it' || auth()->user()->role === 'front_office')
                                    <li><a class="dropdown-item py-2" href="/ruangan"><i class="fas fa-door-open me-2"></i> Room
                                            List</a></li>
                                    <li><a class="dropdown-item py-2" href="/booking-list"><i
                                                class="fas fa-calendar-alt me-2"></i> Booking List</a></li>

                                    <hr class="dropdown-divider my-1">
                                @endif
                                @if (auth()->user()->role === 'it' || auth()->user()->role === 'marketing')
                                    <li>
                                        <a class="dropdown-item py-2" href="{{ route('marketing.peminjaman') }}">
                                            <i class="fas fa-boxes me-2"></i> Peminjaman Barang
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item py-2" href="{{ route('marketing.riwayat') }}">
                                            <i class="fas fa-history me-2"></i> History
                                        </a>
                                    </li>


                                    <hr class="dropdown-divider my-1">
                                @endif

                                @if (auth()->user()->role === 'it')
                                    <li>
                                        <a class="dropdown-item py-2" href="/it">
                                            <i class="fas fa-user-cog me-2"></i> Daftar Pengguna
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item py-2" href="/generate-barcode">
                                            <i class="fas fa-qrcode me-2"></i> Generate Barcode
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item py-2" href="/booking/import">
                                            <i class="fas fa-file-import me-2"></i> Import Booking
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item py-2" href="{{ route('list_barang.index') }}">
                                            <i class="fas fa-boxes me-2"></i> List Barang
                                        </a>
                                    </li>
                                    <hr class="dropdown-divider my-1">
                                @endif
                                @if (auth()->user()->role === 'it' || auth()->user()->role === 'marketing' || auth()->user()->role === 'front_office')
                                    <li>
                                        <a class="dropdown-item py-2" href="{{ route('riwayat.checkin') }}">
                                            <i class="fas fa-history me-2"></i> Riwayat Check-in
                                        </a>
                                    </li>
                                    <hr class="dropdown-divider my-1">
                                @endif

                                <li>
                                    <a class="dropdown-item py-2" href="{{ route('profile') }}">
                                        <i class="fas fa-user me-2"></i> Profil Saya
                                    </a>
                                </li>

                                <form action="/logout" method="POST">
                                    @csrf
                                    <button type="submit" class="dropdown-item py-2">
                                        <i class="fas fa-sign-out-alt me-2"></i> Logout
                                    </button>
                                </form>
                                </li>
                            </ul>
                        </div>
                    </div>

                @endauth
            </div>
        </div>
    </div>
</nav>