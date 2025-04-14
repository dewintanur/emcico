@extends('layouts.app')

@section('content')
<div class="container text-center">
    <h2 class="my-4">Scan QR Code untuk Check-In</h2>

    <div id="reader" style="width: 300px; margin: auto;"></div>

    <div class="mt-4">
        <p><strong>Kode Booking:</strong> <span id="bookingCode">-</span></p>
        <p id="statusMessage" class="text-primary"></p>
    </div>
</div>

<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<script>
    function onScanSuccess(decodedText, decodedResult) {
        document.getElementById("bookingCode").textContent = decodedText;
        document.getElementById("statusMessage").textContent = "Memvalidasi kode booking...";

        fetch(`/checkin/validate/${decodedText}`)
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    document.getElementById("statusMessage").textContent = "✅ " + data.message;
                } else {
                    document.getElementById("statusMessage").textContent = "❌ " + data.message;
                }
            })
            .catch(err => {
                console.error(err);
                document.getElementById("statusMessage").textContent = "❌ Gagal memproses kode.";
            });

        // Hentikan kamera setelah scan
        html5QrcodeScanner.clear();
    }

    const html5QrcodeScanner = new Html5QrcodeScanner(
        "reader", { fps: 10, qrbox: 250 });
    html5QrcodeScanner.render(onScanSuccess);
</script>
@endsection
