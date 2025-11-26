<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Riwayat Peminjaman - {{ auth()->user()->name }}</title>
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
            border-bottom: 3px solid #198754;
            padding-bottom: 10px;
        }

        header h2 {
            margin: 0;
            color: #198754;
            font-size: 18px;
            text-transform: uppercase;
        }

        header p {
            margin: 3px 0;
            font-size: 12px;
            color: #555;
        }

        .logo {
            width: 120px;
            margin-bottom: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        table th {
            background-color: #198754;
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

        /* STATUS COLORS */
        .status-menunggu {
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
        <img src="{{ public_path('assets/backend/images/logos/BKU2.png') }}" class="logo" alt="Logo">
        <h2>Riwayat Peminjaman Barang</h2>
        <p>Nama Pengguna: <strong>{{ auth()->user()->name }}</strong></p>
        <p>Tanggal Cetak: {{ \Carbon\Carbon::now()->translatedFormat('d F Y, H:i') }}</p>
    </header>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Kode Pinjam</th>
                <th>Barang</th>
                <th>Jumlah</th>
                <th>Tanggal Pinjam</th>
                <th>Tanggal Kembali</th>
                <th>Waktu</th>
                <th>Status</th>
            </tr>
        </thead>

        <tbody>
            @forelse ($peminjaman as $index => $data)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $data->kode }}</td>
                    <td>{{ $data->barang->nama }}</td>
                    <td>{{ $data->jumlah }}</td>
                    <td>{{ \Carbon\Carbon::parse($data->tanggal_pinjam)->translatedFormat('d/m/Y') }}</td>
                    <td>
                        @if ($data->tanggal_kembali)
                            {{ \Carbon\Carbon::parse($data->tanggal_kembali)->translatedFormat('d/m/Y') }}
                        @else
                            -
                        @endif

                    <td>
                        {{ $data->waktu_mulai ?? '-' }}
                        @if ($data->waktu_selesai)
                            - {{ $data->waktu_selesai }}
                        @endif
                    </td>
                    <td
                        class="
                        @switch($data->status)
                            @case('menunggu') status-menunggu @break
                            @case('disetujui') status-disetujui @break
                            @case('ditolak') status-ditolak @break
                            @case('selesai') status-selesai @break
                        @endswitch
                    ">
                        {{ ucfirst($data->status) }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align:center; color:#777; padding:15px;">
                        Tidak ada data peminjaman.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <footer>
        Dicetak otomatis oleh sistem Peminjaman Barang &copy; {{ date('Y') }}
    </footer>
</body>

</html>
