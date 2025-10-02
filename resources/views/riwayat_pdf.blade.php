<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Riwayat Booking - {{ auth()->user()->name }}</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table th,
        table td {
            border: 1px solid #333;
            padding: 8px;
            text-align: center;
        }

        table th {
            background-color: #f2f2f2;
        }

        .status-pending {
            background-color: #f7e673;
        }

        .status-disetujui {
            background-color: #7dbef7;
        }

        .status-ditolak {
            background-color: #f77171;
        }

        .status-selesai {
            background-color: #8df78d;
        }

        .status-tidakdiketahui {
            background-color: #d9d9d9;
        }
    </style>
</head>

<body>
    <h2>Riwayat Booking Ruangan</h2>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Ruangan</th>
                <th>Tanggal</th>
                <th>Waktu</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($bookings as $index => $data)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $data->ruangan->nama_ruangan }}</td>
                    <td>{{ \Carbon\Carbon::parse($data->tanggal)->translatedFormat('d/m/Y') }}</td>
                    <td>{{ $data->waktu_mulai }} - {{ $data->waktu_selesai }}</td>
                    <td
                        class="
                    @switch($data->status)
                        @case('Pending') status-pending @break
                        @case('Disetujui') status-disetujui @break
                        @case('Ditolak') status-ditolak @break
                        @case('Selesai') status-selesai @break
                        @default status-tidakdiketahui
                    @endswitch
                    ">
                        {{ $data->status }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
