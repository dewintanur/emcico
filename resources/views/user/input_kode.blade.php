@extends('layouts.app')

@section('title', 'Kode Booking')

@section('content')
    <h5 class="text-center my-4 mt-4" style="font-weight:bold;">Check-In Event</h5>
    <form action="{{ route('check') }}" method="POST">
    @csrf
        <div class="container">
            <div class="card w-50 justify-content-center mx-auto  bg-white shadow border-0 "" style="border-color: #091F5B; border-radius:20px;">
                <div class="card-body">
                    @if (session()->has('gagal'))
                        <div class="alert alert-warning alert-dismissible fade show" role="alert">
                            {{ session()->get('gagal') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    @if (session()->has('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session()->get('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    <p class="card-text text-center py-5 mt-3" style="font-size: 21px; border: none;">Silahkan Masukkan Kode Booking Anda</p>
                    <div class="mb-3 text-center px-5 ">
                        <input type="text" class="form-control py-2" name="id_booking" id="id_booking"
                            placeholder="Masukkan Kode Booking"
                            style="background-color: #E1E9FF; font-style: italic; text-align:center; border-radius:25px; border-color:#E1E9FF;">
                    </div>
                    <div class="text-center p-5">
                        <button type="submit" class="btn"
                            style="background-color: #091F5B; color:white; border-radius:30px; padding:10px 50px; font-weight:bold;">
                            Check - In
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <p class="text-center">*kode booking bisa dicek di halaman riwayat booking</p>
@endsection
