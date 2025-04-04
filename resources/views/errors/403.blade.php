@extends('layouts.app')

@section('content')
<div class="container text-center">
    <h2 class="text-danger">Akses Ditolak</h2>
    <p>Maaf, Anda tidak memiliki izin untuk mengakses halaman ini.</p>
    <a href="{{ url()->previous() }}" class="btn btn-primary">Kembali</a>
</div>
@endsection
