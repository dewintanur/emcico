@extends('layouts.app')

@section('content')
<div class="container position-relative">
   
    <h1 class="text-center mb-4">Edit Profil</h1>

    <div class="card mx-auto p-4" style="max-width: 800px; border-radius: 12px;">
         <!-- Tombol Kembali -->
    <a href="{{ route('profile') }}" class="position-absolute top-0 start-0 mt-2 ms-2" 
       style="text-decoration: none; color: #091F5B;">
        <i class="fas fa-arrow-left fa-lg"></i>
    </a>

        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row align-items-center">
                <!-- Kolom Kiri: Foto & Upload -->
                <div class="col-md-4 text-center">
                    <label class="form-label fw-bold">Foto Profil</label><br>
                    <div class="position-relative d-inline-block">
                        <img id="previewImage" 
                             src="{{ Auth::user()->gambar ? asset('storage/profile_images/' . Auth::user()->gambar) : asset('images/default.png') }}" 
                             class="rounded-circle mb-3 shadow-sm border" 
                             width="120" height="120" alt="Foto Profil">

                        <!-- Tombol Upload (Icon Kamera) -->
                        <label for="uploadImage" 
                               class="position-absolute bottom-0 end-0 bg-white p-1 rounded-circle shadow-sm" 
                               style="cursor: pointer;">
                            <i class="fas fa-camera text-primary"></i>
                        </label>
                        <input type="file" class="d-none" id="uploadImage" name="gambar" accept="image/*">
                    </div>
                    <small class="text-muted d-block">Unggah gambar baru jika ingin mengganti foto profil</small>
                </div>

                <!-- Garis Pembatas -->
                <div class="col-md-1 d-none d-md-block">
                    <div style="width: 1px; height: 100%; background-color: #ccc;"></div>
                </div>

                <!-- Kolom Kanan: Form Edit Profil -->
                <div class="col-md-7">
                    <!-- Nama -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama</label>
                        <input type="text" class="form-control" name="nama" 
                               value="{{ old('nama', Auth::user()->nama) }}" required>
                    </div>

                    <!-- Email -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Email</label>
                        <input type="email" class="form-control" name="email" 
                               value="{{ old('email', Auth::user()->email) }}" required>
                    </div>

                    <div class="mb-3 position-relative">
                        <label class="form-label fw-bold">Password Baru (Opsional)</label>
                        <input type="password" class="form-control" id="newPassword" name="password">
                        <i class="fas fa-eye position-absolute" onclick="togglePassword('newPassword', this)"
                        style="top: 38px; right: 10px; cursor: pointer; position: absolute;"></i>
                        <small class="text-muted">Isi jika ingin merubah password.</small>
                    </div>


                    <div class="mb-3 position-relative">
    <label class="form-label fw-bold">Konfirmasi Password</label>
    <input type="password" class="form-control" id="confirmPassword" name="password_confirmation">
    <i class="fas fa-eye position-absolute" onclick="togglePassword('confirmPassword', this)"
       style="top: 38px; right: 10px; cursor: pointer; position: absolute;"></i>
    <small class="text-muted">konfirmasi password baru</small>
</div>


                    <!-- Tombol Simpan -->
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary" style="background-color: #091F5B;">Simpan Perubahan</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- JavaScript untuk Preview Gambar -->
<script>
    document.getElementById("uploadImage").addEventListener("change", function(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById("previewImage").src = e.target.result;
            };
            reader.readAsDataURL(file);
        }
    });
    function togglePassword(fieldId, icon) {
        const field = document.getElementById(fieldId);
        if (field.type === "password") {
            field.type = "text";
            icon.classList.remove("fa-eye");
            icon.classList.add("fa-eye-slash");
        } else {
            field.type = "password";
            icon.classList.remove("fa-eye-slash");
            icon.classList.add("fa-eye");
        }
    }


</script>

@endsection
