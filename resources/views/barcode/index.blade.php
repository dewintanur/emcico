@extends('layouts.app')

@section('title', 'Generate QR Code')

@section('content')
<div class="container py-4">
    <h1 class="h3 text-center mb-4">Generate QR Code</h1>

    <div class="card shadow-sm border-0 rounded-3 p-4">
        <form action="{{ route('barcode.send') }}" method="POST">
            @csrf
            <table class="table">
                <thead>
                    <tr>
                        <th>Pilih</th>
                        <th>Kode Booking</th>
                        <th>Nama Pemesan</th>
                        <th>Tanggal Booking</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($bookings as $booking)
                        <tr>
                            <td>
                                <input type="checkbox" name="kode_booking[]" value="{{ $booking->kode_booking }}">
                            </td>
                            <td>{{ $booking->kode_booking }}</td>
                            <td>{{ $booking->nama_pic }}</td>
                            <td>{{ $booking->tanggal }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
    <tr>
        <td>
            <input type="checkbox" id="select-all-bottom">
        </td>
        <td colspan="3"><small>Pilih Semua Booking</small></td>
    </tr>
</tfoot>
            </table>

            <button type="submit" class="btn btn-primary mt-3" 
                @if($bookings->isEmpty()) disabled @endif>
                Generate & Kirim ke WhatsApp
            </button>
        </form>
    </div>

    @if(isset($barcodes) && count($barcodes) > 0)
        <div class="row mt-4">
            @foreach ($barcodes as $barcode)
                <div class="col-md-4 mb-3">
                    <div class="card shadow-sm p-3 text-center">
                        <h6>{{ $barcode['kode'] }}</h6>
                        <img src="{{ asset('storage/qrcodes/' . $barcode['kode'] . '.png') }}" class="img-fluid" alt="QR Code">
                        <a href="{{ $barcode['whatsapp'] }}" class="btn btn-success btn-sm mt-2" target="_blank">
                            Kirim ke WhatsApp
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        @if(isset($barcodes))
            <p class="text-center text-danger">Tidak ada QR code yang dihasilkan. Pastikan Anda memilih booking yang valid.</p>
        @endif
    @endif
</div>
@endsection
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const selectTop = document.getElementById('select-all');
        const selectBottom = document.getElementById('select-all-bottom');
        const checkboxes = document.querySelectorAll('input[name="kode_booking[]"]');

        function toggleAll(checked) {
            checkboxes.forEach(cb => cb.checked = checked);
        }

        selectTop?.addEventListener('change', function () {
            toggleAll(this.checked);
            if (selectBottom) selectBottom.checked = this.checked;
        });

        selectBottom?.addEventListener('change', function () {
            toggleAll(this.checked);
            if (selectTop) selectTop.checked = this.checked;
        });
    });
</script>

