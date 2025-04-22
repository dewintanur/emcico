@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="text-center mb-4">Profil Saya</h1>

    <div class="card mx-auto p-4 shadow-sm" style="max-width: 800px; border-radius: 12px;">
        <div class="row align-items-center">
            <!-- Kolom Kiri: Foto & Info Dasar -->
            <div class="col-md-4  d-flex flex-column align-items-center">
                <img src="{{ $user->gambar ? asset('storage/profile_images/' . $user->gambar) : asset('images/default.png') }}" 
                     class="rounded-circle mb-3 shadow" width="120" height="120" alt="Foto Profil">
                <h4 class="mb-1">{{ $user->nama }}</h4>
                <p class="text-muted mb-1">{{ ucfirst($user->role) }}</p>
                <p> {{ $user->email }}</p>
            
                <!-- Tombol Edit -->
                <a href="{{ route('profile.edit') }}" class="btn btn-light border shadow-sm d-flex align-items-center justify-content-center profile-btn">
                    <i class="fas fa-edit"></i>
                    <span class="ms-2 fw-semibold">Edit</span>
                </a>
            </div>

            <!-- Garis Pembatas -->
            <div class="col-md-1 d-none d-md-block">
                <div style="width: 1px; height: 100%; background-color: #ccc;"></div>
            </div>

            <!-- Kolom Kanan: Log Aktivitas -->
            <div class="col-md-7">
                <h5>Log Aktivitas Terakhir</h5>
                <ul class="list-group list-group-flush">
                    @forelse ($logAktivitas as $log)
                        <li class="list-group-item" style="border-left: 4px solid #091F5B;">
                            {{ $log->aktivitas }} <br>
                            <small class="text-muted">{{ \Carbon\Carbon::parse($log->waktu)->translatedFormat('d F Y, H:i') }}</small>
                        </li>
                    @empty
                        <li class="list-group-item text-muted">Belum ada aktivitas</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- CSS Tambahan -->
<style>
    .profile-btn {
        border-radius: 8px;
        padding: 8px 16px;
        height: 40px;
        width: max-content;
        background-color: white;
        border: 1px solid #091F5B;
        color: #091F5B;
        font-weight: 600;
        transition: all 0.3s ease-in-out;
    }

    .profile-btn i {
        color: #091F5B;
        font-size: 16px;
    }

    .profile-btn:hover {
        background-color: #091F5B;
        color: white;
    }

    .profile-btn:hover i {
        color: white;
    }
</style>
@endsection
