@extends('layouts.app')

@section('content')
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-6 offset-md-3">
                <h2 class="mb-4 text-center">Edit User</h2>
                <div class="card p-4 shadow-sm rounded">
                <form action="{{ route('users.update', $user->id) }}" method="POST">
                @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label for="nama" class="form-label">Nama</label>
                            <input type="text" class="form-control" id="nama" name="nama" value="{{ $user->nama }}"
                                required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="{{ $user->email }}"
                                required>
                        </div>
                        <div class="mb-3">
                            <label for="role" class="form-label">Role</label>
                            <select class="form-select" id="role" name="role" required>
                                 <option value="" >Silahkan Pilih Role</option>

                                <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Admin</option>
                                <option value="marketing" {{ $user->role == 'marketing' ? 'selected' : '' }}>Marketing
                                </option>
                                <option value="produksi" {{ $user->role == 'produksi' ? 'selected' : '' }}>Produksi</option>
                                <option value="front_office" {{ $user->role == 'front_office' ? 'selected' : '' }}>Front
                                    Office</option>
                                    <option value="duty_officer" {{ $user->role == 'duty_officer' ? 'selected' : '' }}>Duty Officer</option>
                            </select>

                        </div>
                        <button type="submit" class="btn btn-primary w-100">Update</button>
                        <a href="{{ route('users.index') }}" class="btn btn-secondary w-100 mt-2">Batal</a>
                        </form>
                </div>
            </div>
        </div>
    </div>
@endsection