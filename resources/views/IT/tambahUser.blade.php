@extends('layouts.app')

@section('content')
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-6 offset-md-3">
                <h2 class="mb-4 text-center">Tambah User</h2>
                <div class="card p-4 shadow-sm rounded">
                    <form action="{{ route('users.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="nama" class="form-label">Nama</label>
                            <input type="text" class="form-control" id="nama" name="nama" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="role" class="form-label">Role</label>
                            <select class="form-select" id="role" name="role" required>
                                <option value="">Pilih Role</option>
                                <option value="marketing">Marketing</option>
                                <option value="front_office">Front Office</option>
                                <option value="produksi">Produksi</option>
                                <option value="it">IT</option>
                                <option value="duty_officer">Duty Office</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Kata Sandi</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Simpan</button>
                        <a href="{{ route('users.index') }}" class="btn btn-secondary w-100 mt-2">Batal</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection