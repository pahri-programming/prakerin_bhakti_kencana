<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Riwayat Booking - {{ auth()->user()->name }}</title>
    <style>
        @page {
            margin: 40px 30px;
        }

        body {
            font-family: "DejaVu Sans", sans-serif;
            font-size: 12px;
            color: #333;
        }

        header {
            text-align: center;
            margin-bottom: 25px;
            border-bottom: 3px solid #0d6efd;
            padding-bottom: 10px;
        }

        header h2 {
            margin: 0;
            color: #0d6efd;
            font-size: 18px;
            text-transform: uppercase;
        }

        header p {
            margin: 3px 0;
            font-size: 12px;
            color: #555;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        table th {
            background-color: #0d6efd;
            color: white;
            padding: 8px 6px;
            font-weight: bold;
            text-align: center;
            border: 1px solid #ddd;
            text-transform: uppercase;
            font-size: 11px;
        }

        table td {
            border: 1px solid #ddd;
            padding: 8px 6px;
            text-align: center;
        }

        tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        .status-pending {
            background-color: #fff3cd;
            color: #856404;
            font-weight: 600;
        }

        .status-disetujui {
            background-color: #cfe2ff;
            color: #084298;
            font-weight: 600;
        }

        .status-ditolak {
            background-color: #f8d7da;
            color: #842029;
            font-weight: 600;
        }

        .status-selesai {
            background-color: #d1e7dd;
            color: #0f5132;
            font-weight: 600;
        }

        .status-tidakdiketahui {
            background-color: #e2e3e5;
            color: #41464b;
            font-weight: 600;
        }

        footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: right;
            font-size: 10px;
            color: #888;
        }
    </style>
</head>

<body>
    <header>
        <h2>Riwayat Booking Ruangan</h2>
        <p>Nama Pengguna: <strong>{{ auth()->user()->name }}</strong></p>
        <p>Tanggal Cetak: {{ \Carbon\Carbon::now()->translatedFormat('d F Y, H:i') }}</p>
    </header>

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
            @forelse ($bookings as $index => $data)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $data->ruangan->nama_ruangan }}</td>
                    <td>{{ \Carbon\Carbon::parse($data->tanggal)->translatedFormat('d/m/Y') }}</td>
                    <td>{{ $data->waktu_mulai }} - {{ $data->waktu_selesai }}</td>
                    <td
                        class="
                        @switch($data->status)
                            @case('Pending') status-pending @break
                            @case('Diterima') status-disetujui @break
                            @case('Ditolak') status-ditolak @break
                            @case('Selesai') status-selesai @break
                            @default status-tidakdiketahui
                        @endswitch
                    ">
                        {{ ucfirst($data->status) }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align:center; color:#777; padding:15px;">Tidak ada data booking.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <footer>
        Dicetak otomatis oleh sistem Booking Ruangan &copy; {{ date('Y') }}
    </footer>
</body>

</html>
