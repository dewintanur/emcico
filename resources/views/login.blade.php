@extends('layouts.app')

@section('content')
<div class="d-flex flex-column justify-content-center align-items-center pt-5">
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif
    <h1 style="color: #091F5B;">Login</h1>
    <form action="{{ route('login') }}" method="POST" class="card p-5 w-75 shadow-lg" style="border-radius: 10px">
        @csrf
        <div class="form-group my-1">
            <label class="py-1 fw-bold fs-5" for="email">Email</label>
            <input type="email" name="email" id="email" class="form-control py-3" style="border-radius: 10px" required>
        </div>
        <div class="form-group my-1">
            <label class="py-1 fw-bold fs-5" for="password">Password</label>
            <input type="password" name="password" id="password" class="form-control py-3" style="border-radius: 10px" required>
        </div>
        <div class="form-check py-1">
            <input type="checkbox" class="form-check-input" id="show-password" onclick="togglePassword()">
            <label class="form-check-label" for="show-password">Show Password</label>
        </div>
        <script>
            function togglePassword() {
                var x = document.getElementById("password");
                x.type = x.type === "password" ? "text" : "password";
            }
        </script>
        <div class="d-flex flex-column py-3">
            <button type="submit" class="btn" style="background-color: #091F5B; color: white; padding: 16px; font-size: 20px; border-radius: 10px;">
                <strong>Masuk</strong>
            </button>
        </div>
    </form>
</div>
@endsection
