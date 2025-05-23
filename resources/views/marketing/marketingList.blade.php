@extends('layouts.app')
@section('title', 'Peminjaman Barang List')
@section('content')
    @php
        $isAdminOrIT = Auth::user()->role == 'admin' || Auth::user()->role == 'it';
    @endphp

    <body class="">
        <!-- Header -->

        <div class="d-flex justify-content-between align-items-center mb-4">

        </div>
        <div class="container py-4">

            <h1 class="display-4 mb-4 text-center">Peminjaman List</h1>

            <!-- Combined Filters (Tanpa Tombol Filter & Reset) -->
            <div class="row mb-4">
                <form method="GET" action="{{ route('marketing.peminjaman') }}"
                    class="d-flex flex-wrap align-items-center gap-2">
                    <!-- Date Filter -->
                    <div>
                        <input type="date" name="date" class="form-control filter-allowed" value="{{ request('date') }}"
                            onchange="this.form.submit()">
                    </div>
                    <!-- Search Filter -->
                    <div>
                        <input type="text" name="search" class="form-control filter-allowed"
                            placeholder="Search by Event Name" value="{{ request('search') }}" oninput="this.form.submit()">
                    </div>
                </form>
            </div>



            <!-- Table -->
            <div class="container mt-4">
                <div class="card-body text-white my-2 shadow-lg" style="background-color:#091F5B; border-radius: 8px;">
                    <div class="row align-items-center">
                        <div class="d-none">Aksi</div>
                        <div class="col-md-3 text-left" style="font-weight: bold">Nama Event</div>
                        <div class="col-md-2 text-left" style="font-weight: bold">Nama Organisasi</div>
                        <div class="col-md-2 text-left" style="font-weight: bold">Tanggal</div>
                        <div class="col-md-2 text-left" style="font-weight: bold">Ruangan dan Waktu</div>
                        <div class="col-md-2 text-left" style="font-weight: bold">Nama PIC</div>
                        <div class="col-md-1 text-left" style="font-weight: bold">Aksi</div>
                    </div>
                </div>

                @foreach ($peminjamanList as $booking)
                    <div class="card-header text-dark my-2 shadow-sm" style="background-color:white; border-radius: 5px;">
                        <div class="row align-items-center">
                            <div class="d-none">
                                {{ $booking['kode_booking'] }}
                            </div>
                            <div class="col-md-3 text-left">
                                 <a href="#" data-bs-toggle="modal"
                                                    data-bs-target="#eventModal{{ $booking->id }}" class="fw-bold"
                                                    style="color: #091F5B;">
                                                    {{ $booking->nama_event }}
                                                </a>
                            </div>
                            @include('FO.detail_acara', ['booking' => $booking])

                            <div class="col-md-2 text-left" style="color:#091F5B; font-weight: 600;">
                                {{ $booking['nama_organisasi'] }}
                            </div>
                            <div class="col-md-2 text-left" style="color:#091F5B; font-weight: 600;">
                                {{ \Carbon\Carbon::parse($booking['tanggal'])->locale('id')->translatedFormat(' d F Y') }}
                            </div>
                            <div class="col-md-2 text-left" style="color:#091F5B; font-weight: 600;">

                                <p>{{ $booking->ruangan->nama_ruangan }}<br>
                                    <span>Lantai {{ $booking->ruangan->lantai }}</span><br>
                                    <span>{{ $booking->waktu_mulai }} -
                                        {{ $booking->waktu_selesai }}</span>
                                </p>
                            </div>
                            <div class="col-md-2 text-left" style="color:#091F5B; font-weight: 600;">
                                {{ $booking['pic_name'] }} <br>
                                <a href="https://wa.me/{{ $booking['no_pic'] }}" target="_blank" style="color: #25D366;">
                                    {{ $booking['no_pic'] }}</a>
                            </div>
                            <div class="col-md-1 text-left">
                                <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal"
                                    data-bs-target="#editModal{{ $booking['kode_booking'] }}" {{ $isAdminOrIT ? 'disabled' : '' }}>Edit</button>
                            </div>
                        </div>
                    </div>
                @endforeach
                
                @foreach ($peminjamanList as $booking)
                    <form id="pinjamForm" method="POST" action="">
                        @csrf
                        <div class="modal fade" id="editModal{{ $booking['kode_booking'] }}" tabindex="-1"
                            aria-labelledby="editModalLabel{{ $booking['kode_booking'] }}" aria-hidden="true">
                            <div class="modal-dialog mx-auto justify-content-center" style="max-width: 600px;">
                                <div class="modal-content p-3" style="width: 100%">
                                    <div class="main-card  mx-auto mt-2 mb-3">
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                        <h4 class="text-center mb-4 fw-bold" id="editModalLabel">Formulir Peminjaman
                                            Barang</h4>
                                        <div class="info-card border mb-2 p-3">
                                            <div class="d-flex align-items-center">
                                                <p class="mb-0">Nama Event</p>
                                                <p class="mb-0" style="color: #091F5B; margin-left: 20px;">
                                                    <strong>{{ $booking['nama_event'] }}</strong>
                                                </p>
                                            </div>
                                        </div>
                                        <div class="info-card border p-3 mb-3">
                                            <div class="row">
                                                <input type="hidden" name="kode_booking" value="{{ $booking['kode_booking'] }}">
                                                <div class="col-md-6 d-flex align-items-center">
                                                    <p class="mb-0 fw-bold flex-shrink-0" style="width: 100px;">Ruangan </p>
                                                    <p class="mb-0" style="color: #091F5B;">
                                                        {{ $booking->ruangan->nama_ruangan }}
                                                    </p>
                                                </div>
                                                <div class="col-md-6 d-flex align-items-center">
                                                    <p class="mb-0 fw-bold flex-shrink-0" style="width: 100px;">PIC</p>
                                                    <p class="mb-0" style="color: #091F5B;">
                                                        {{ $booking['nama_pic'] }}
                                                    </p>
                                                </div>
                                                <div class="col-md-6 d-flex align-items-center mt-2">
                                                    <p class="mb-0 fw-bold flex-shrink-0" style="width: 100px;">Tanggal</p>
                                                    <p class="mb-0 text-end ms-3" style="color: #091F5B;">
                                                        {{ \Carbon\Carbon::parse($booking['tanggal'])->translatedFormat('d F Y') }}
                                                    </p>
                                                </div>
                                                <div class="col-md-6 d-flex align-items-center mt-2">
                                                    <p class="mb-0 fw-bold flex-shrink-0" style="width: 100px;">Jam</p>
                                                    <p class="mb-0 text-end" style="color: #091F5B;">
                                                        {{ $booking['waktu_mulai'] ?? 'N/A' }} -
                                                        {{ $booking['waktu_selesai'] ?? 'N/A' }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        <h6 class="fw-bold">List Barang yang Tersedia</h6>
                                        <table class="table table-bordered" id="barangList{{ $booking->kode_booking }}">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>No</th>
                                                    <th>Nama Barang</th>
                                                    <th>Jumlah</th>


                                                </tr>
                                            </thead>
                                            <tbody id="availableItems{{ $booking->kode_booking }}">
                                                @foreach($listBarang as $index => $barang)
                                                    <tr>
                                                        <td>{{ $index + 1 }}</td>
                                                        <td>{{ $barang->nama_barang }}</td>
                                                        <td class="jumlah-barang">{{ $barang->jumlah }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>

                                        <h6 class="fw-bold">List Barang yang Dipinjam</h6>
                                        <table class="table table-bordered" id="borrowedList{{ $booking->id }}">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>No</th>
                                                    <th>Nama Barang</th>
                                                    <th>Jumlah</th>
                                                    <th>Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody id="borrowedItems{{ $booking->kode_booking }}">
                                                @php $no = 1; @endphp

                                                @if (!empty($booking->peminjaman) && is_iterable($booking->peminjaman))
                                                    @foreach ($booking->peminjaman as $peminjaman)
                                                        <tr>
                                                            <td>{{ $no++ }}</td>
                                                            <td>{{ optional($peminjaman->barang)->nama_barang ?? 'Tidak ditemukan' }}
                                                            </td>
                                                            <td class="text-center">
                                                                {{ $peminjaman->jumlah }}
                                                                {{ optional($peminjaman->barang)->satuan ?? '' }}
                                                            </td>
                                                            <td>
                                                                <button type="button" class="btn btn-danger btn-sm"
                                                                    onclick="removeItem(this, {{ $peminjaman->id }}, '{{ optional($peminjaman->barang)->nama_barang }}', {{ $peminjaman->jumlah }}, '{{ $booking->kode_booking }}')">Hapus</button>

                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                @else
                                                    <tr id="emptyRow{{ $booking->id }}">
                                                        <td colspan="4" class="text-center">Tidak ada barang yang dipinjam</td>
                                                    </tr>
                                                @endif
                                            </tbody>
                                        </table>

                                        <button type="button" class="btn btn-primary btn-sm"
                                            onclick="addItem('{{ $booking->kode_booking }}')">
                                            Tambah Barang
                                        </button>
                                        <!-- Form Tambah Barang (Awalnya Tersembunyi) -->
                                        <div id="addItemForm-{{ $booking->kode_booking }}" style="display: none;" class="mt-3">
                                            <table class="table table-bordered">
                                                <tr>
                                                    <td>
                                                        <select class="form-control barangSelect"
                                                            data-kode-booking="{{ $booking->kode_booking }}">
                                                            <option value="">-- Pilih Barang --</option>
                                                            @foreach($listBarang as $barang)
                                                                <option value="{{ $barang->id }}" data-stok="{{ $barang->jumlah }}">
                                                                    {{ $barang->nama_barang }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <input type="number" class="form-control jumlahBarang"
                                                            data-kode-booking="{{ $booking->kode_booking }}"
                                                            placeholder="Jumlah" min="1">
                                                    </td>
                                                    <td>
                                                        <button type="button" class="btn btn-success btn-sm"
                                                            onclick="saveItem('{{ $booking->kode_booking }}')">Simpan</button>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                @endforeach
                        </div>
                        <script>
                            $(document).on("click", ".btn-warning", function () {
                                let targetModal = $(this).attr("data-bs-target");
                                let modalElement = $(targetModal);
                                if (modalElement.length === 0) {
                                    console.error("Modal tidak ditemukan di DOM!");
                                    return;
                                }

                                modalElement.removeAttr("aria-hidden").removeAttr("tabindex");
                                $(".modal").modal("hide");

                                setTimeout(() => {
                                    modalElement.removeClass("fade");
                                    modalElement.css("display", "block");
                                    modalElement.modal("show");
                                }, 200);
                            });

                            $(document).ready(function () {
                                $(".modal").each(function () {
                                    $(this).appendTo("body");
                                });
                            });

                            function addItem(kodeBooking) {
                                document.getElementById('addItemForm-' + kodeBooking).style.display = 'block';
                            }

                            function saveItem(kodeBooking) {
                                let barangSelect = document.querySelector(`.barangSelect[data-kode-booking="${kodeBooking}"]`);
                                let jumlahInput = document.querySelector(`.jumlahBarang[data-kode-booking="${kodeBooking}"]`);
                                let formContainer = document.getElementById('addItemForm-' + kodeBooking);

                                if (!barangSelect || !jumlahInput || !formContainer) {
                                    console.error('Elemen input tidak ditemukan!');
                                    alert('Terjadi kesalahan: Elemen input tidak ditemukan.');
                                    return;
                                }

                                let barangId = barangSelect.value;
                                let jumlah = jumlahInput.value;
                                let namaBarang = barangSelect.options[barangSelect.selectedIndex].text.trim();

                                if (barangId === "") {
                                    alert('Pilih barang terlebih dahulu!');
                                    return;
                                }
                                if (jumlah < 1) {
                                    alert('Jumlah barang harus lebih dari 0!');
                                    return;
                                }

                                fetch('/peminjaman/store', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                    },
                                    body: JSON.stringify({
                                        kode_booking: kodeBooking,
                                        barang_id: barangId,
                                        jumlah: jumlah
                                    })
                                })
                                    .then(response => response.json())
                                    .then(data => {
                                        if (data.error) {
                                            alert(data.error);
                                        } else {
                                            alert('Barang berhasil ditambahkan!');

                                            let borrowedTable = document.getElementById('borrowedItems' + kodeBooking);
                                            let rows = borrowedTable.getElementsByTagName('tr');
                                            let found = false;

                                            for (let i = 0; i < rows.length; i++) {
                                                let cellNama = rows[i].cells[1];
                                                if (cellNama && cellNama.innerText.trim() === namaBarang) {
                                                    let cellJumlah = rows[i].cells[2];
                                                    cellJumlah.innerText = parseInt(cellJumlah.innerText) + parseInt(jumlah);
                                                    found = true;
                                                    break;
                                                }
                                            }

                                            if (!found) {
                                                let newRow = borrowedTable.insertRow();
                                                newRow.innerHTML = `
                                <td>${borrowedTable.rows.length}</td>
                                <td>${namaBarang}</td>
                                <td class="text-center">${jumlah}</td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-danger btn-sm" 
                                        onclick="removeItem(this, ${data.peminjaman_id}, '${namaBarang}', ${jumlah}, '${kodeBooking}')">Hapus</button>
                                </td>`;
                                            }

                                            // ✅ Tambahkan pengecekan sebelum akses querySelectorAll
                                            let availableItemsTable = document.querySelector(`#availableItems${kodeBooking}`);
                                            if (!availableItemsTable) {
                                                console.error(`[ERROR] Tabel dengan ID #availableItems${kodeBooking} tidak ditemukan.`);
                                                return;
                                            }

                                            let availableRows = availableItemsTable.querySelectorAll("tr");

                                            availableRows.forEach(row => {
                                                let cellNama = row.cells[1];
                                                let cellJumlah = row.querySelector('.jumlah-barang');
                                                if (cellNama && cellNama.innerText.trim() === namaBarang && cellJumlah) {
                                                    let jumlahLama = parseInt(cellJumlah.innerText);
                                                    let jumlahBaru = jumlahLama - parseInt(jumlah);
                                                    cellJumlah.innerText = jumlahBaru;
                                                    console.log(`[UPDATE] '${namaBarang}' dikurangi: ${jumlahLama} → ${jumlahBaru}`);
                                                } else {
                                                    console.log(`[SKIP] Tidak cocok: '${cellNama?.innerText.trim()}' ≠ '${namaBarang}'`);
                                                }
                                            });

                                            barangSelect.value = "";
                                            jumlahInput.value = "";
                                            formContainer.style.display = 'none';
                                        }
                                    })
                                    .catch(error => {
                                        console.error('Error:', error);
                                        alert('Terjadi kesalahan saat menyimpan barang.');
                                    });
                            }

                            function removeItem(button, peminjamanId, namaBarang, jumlah, kodeBooking) {
                                if (confirm("Apakah Anda yakin ingin menghapus barang ini?")) {
                                    fetch(`/peminjaman/destroy/${peminjamanId}`, {
                                        method: "DELETE",
                                        headers: {
                                            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                        }
                                    })
                                        .then(response => response.json())
                                        .then(data => {
                                            if (data.success) {
                                                button.closest('tr').remove();
                                                alert("Barang berhasil dihapus!");

                                                // ✅ Tambahkan kembali jumlah barang ke list tersedia
                                                let availableItemsTable = document.querySelector(`#availableItems${kodeBooking}`);
                                                if (!availableItemsTable) {
                                                    console.error(`[ERROR] Tabel barang tersedia dengan ID #availableItems${kodeBooking} tidak ditemukan`);
                                                    return;
                                                }

                                                let found = false;
                                                let availableRows = availableItemsTable.querySelectorAll("tr");
                                                availableRows.forEach(row => {
                                                    let cellNama = row.cells[1];
                                                    let cellJumlah = row.querySelector('.jumlah-barang');
                                                    if (cellNama && cellJumlah && cellNama.innerText.trim() === namaBarang.trim()) {
                                                        let jumlahLama = parseInt(cellJumlah.innerText);
                                                        let jumlahBaru = jumlahLama + parseInt(jumlah);
                                                        cellJumlah.innerText = jumlahBaru;
                                                        console.log(`[RETURN] '${namaBarang}' dikembalikan: ${jumlahLama} → ${jumlahBaru}`);
                                                        found = true;
                                                    }
                                                });

                                                if (!found) {
                                                    console.warn(`[SKIP] Barang '${namaBarang}' tidak ditemukan di daftar tersedia`);
                                                }

                                            } else {
                                                alert("Gagal menghapus barang!");
                                            }
                                        })
                                        .catch(error => console.error('Error:', error));
                                }
                            }

                        </script>

@endsection