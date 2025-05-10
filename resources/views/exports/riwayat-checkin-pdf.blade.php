<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Check-in</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th,
        td {
            border: 1px solid black;
            padding: 6px;
            text-align: center;
            font-size: 10px;
        }

        th {
            background-color: #f2f2f2;
        }
    </style>
</head>

<body>
    <h2 style="text-align: center;">Riwayat Check-in</h2>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Kode Booking</th>
                <th>Nama Organisasi</th>
                <th>Nama Event</th>
                <th>Nama User</th>
                <th>Tanggal Check-in</th>
                <th>Waktu Mulai</th>
                <th>Waktu Selesai</th>
                <th>Ruangan</th>
                <th>Lantai</th>
                <th>Peminjaman Barang</th> <!-- ✅ Tambahkan -->
                <th>Marketing</th> <!-- ✅ Tambahkan -->
                <th>Tanda Tangan User</th>
                <th>Petugas Duty Officer</th>
                <th>Petugas Front Office</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($kehadiran as $index => $data)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $data->kode_booking }}</td>
                    <td>{{ $data->booking->nama_organisasi ?? '-' }}</td>
                    <td>{{ $data->booking->nama_event ?? '-' }}</td>
                    <td>{{ $data->nama_ci }}</td>
                    <td>{{ \Carbon\Carbon::parse($data->created_at)->format('d F Y, H:i') }}</td>
                    <td>{{ $data->booking && $data->booking->waktu_mulai ? \Carbon\Carbon::parse($data->booking->waktu_mulai)->format('H:i') : '-' }}
                    </td>
                    <td>{{ $data->booking && $data->booking->waktu_selesai ? \Carbon\Carbon::parse($data->booking->waktu_selesai)->format('H:i') : '-' }}
                    </td>
                    <td>{{ $data->booking->ruangan->nama_ruangan ?? '-' }}</td>
                    <td>{{ $data->booking->lantai ?? '-' }}</td>
                    <td>
                        {{ optional($data->booking)->peminjaman ? 'Ada' : 'Tidak Ada' }}
                    </td>
                    <td>
                        @if ($data->booking && $data->booking->peminjaman && $data->booking->peminjaman->isNotEmpty())
                            {{ implode(', ', $data->booking->peminjaman->pluck('marketing')->toArray()) }}
                        @else
                            Tidak Ada
                        @endif
                    </td>


                    <td>
                        @if ($data->ttd)
                            <img src="{{ $data->ttd }}" alt="Tanda Tangan" style="width: 100px; height: auto;">
                        @else
                            Tidak Ada
                        @endif
                    </td>
                    <td>{{ $data->dutyOfficer->nama ?? 'Tidak Ada' }}</td>
                    <td>{{ $data->fo->nama ?? 'Belum Checkout' }}</td>

                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>