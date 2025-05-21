<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Kehadiran</title>
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
            padding: 5px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }
    </style>
</head>

<body>

    <h3>Daftar Kehadiran</h3>
    <table>
        <thead>
            <tr>
                <th>Kode Booking</th>
                <th>Nama Event</th>
                <th>Nama Organisasi</th>
                <th>Nama PIC</th>
                <th>Nama Check-in</th>
                <th>No Check-in</th>
                <th>Tanggal Check-in</th>
                <th>Tanggal Check-out</th>
                <th>FO</th>
                <th>Status Kehadiran</th>
            </tr>
        </thead>
        <tbody>
            @foreach($kehadiran as $data)
                <tr>
                    <td>{{ $data->booking->kode_booking ?? '-' }}</td>
                    <td>{{ $data->booking->nama_event ?? '-' }}</td>
                    <td>{{ $data->booking->nama_organisasi ?? '-' }}</td>
                    <td>{{ $data->booking->nama_pic ?? '-' }}</td>
                    <td>{{ $data->nama_ci ?? '-' }}</td>
                    <td>{{ $data->no_ci ?? '-' }}</td>
                    <td>{{ $data->created_at->format('Y-m-d H:i') }}</td>
                    <td>{{ $data->tanggal_co_display ?? 'Belum Checkout' }}</td>
                    <td>{{ $data->fo->nama ?? 'Belum Checkout' }}</td>
                    <td>{{ $data->status ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>

    </table>

</body>

</html>