@extends('layouts.app')

@section('content')
    <style>
        body {
            font-family: 'Montserrat', sans-serif;
            margin: 0;
            overflow-x: hidden;
            
        }

        .toggle-button {
            
            position: absolute;
            top: 80px;
            right: 10px;
            display: flex;
            width: 180px;
            border-radius: 25px;
            overflow: hidden;
            background-color: #e0e8ff;
            z-index: 1000;
        }

        .toggle-button div {
            flex: 1;
            text-align: center;
            padding: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .toggle-button .active {
            background-color: #002855;
            color: #fff;
            font-weight: bold;
        }

        .toggle-button .inactive {
            background-color: #e0e8ff;
            color: #002855;
            font-weight: bold;
        }

        video {
            width: 100%;
            max-height: 50vh;
            border-radius: 10px;
        }

        h4,
        h5 {
            margin: 0;
        }

        @media (max-width: 768px) {
            .toggle-button {
                width: 50%;
                top: 85px;
                right: 25%;
            }

            h4 {
                font-size: 24px;
            }

            h5 {
                font-size: 12px;
            }
        }
    </style>


    <!-- TOGGLE BARCODE / INPUT -->
    <div class="toggle-button">
    <div id="barcodeButton"
     class="{{ request()->routeIs('barcode.scan') ? 'active' : 'inactive' }}"
     onclick="navigateTo('barcode')">
     BARCODE
</div>

<div id="inputButton"
     class="{{ request()->routeIs('inputkode.show') ? 'active' : 'inactive' }}"
     onclick="navigateTo('input')">
     INPUT
</div>

    </div>

    <script>
        function navigateTo(view) {
            if (view === 'barcode') {
                window.location.href = "{{ route('barcode.scan') }}";
            } else if (view === 'input') {
                window.location.href = "{{ route('inputkode.show') }}";
            }
        }
        
    </script>


<div class="container col-lg-5 py-5">
    <div class="text-center mt-4">
        <h4 style="font-size: 32px; color: #091F5B;">Silahkan tunjukkan barcode Anda ke Kamera</h4>
    </div>

    <div class="card bg-white shadow rounded-3 p-3 border-0 mt-4">
        @if (session('error_message'))
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                {{ session('error_message') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <video id="preview"></video>

        <form action="{{ route('scan.barcode') }}" method="POST" id="form">
            @csrf
            <input type="hidden" name="kode_booking" id="kode_booking">
        </form>
    </div>

    <h5 class="text-center mt-4" style="font-size: 14px; color: #091F5B;">*Barcode dikirimkan ke wa Anda apabila sudah di-approve</h5>
</div>


    <!-- SCRIPT SCANNER -->
    <script>
        let scanner = new Instascan.Scanner({
            video: document.getElementById('preview')
        });

        scanner.addListener('scan', function (content) {
            document.getElementById('kode_booking').value = content;
            document.getElementById('form').submit();
        });

        Instascan.Camera.getCameras().then(function (cameras) {
            if (cameras.length > 0) {
                scanner.start(cameras[0]);
            } else {
                alert('Kamera tidak ditemukan.');
            }
        }).catch(function (e) {
            console.error(e);
        });
    </script>
      <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
    </script>
@endsection
