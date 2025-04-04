@extends('layouts.app')

@section('title', 'Scan QR Code')

@section('content')
<div class="container text-center">
    <h2 class="my-4">Scan QR Code untuk Check-in</h2>

    <div id="reader" style="width: 300px; margin: auto;"></div>

    <script src="https://unpkg.com/html5-qrcode"></script>
    <script>
        function onScanSuccess(decodedText) {
            window.location.href = "/checkin/" + decodedText; 
        }
        new Html5QrcodeScanner("reader", { fps: 10, qrbox: 250 }).render(onScanSuccess);
    </script>
</div>
@endsection
