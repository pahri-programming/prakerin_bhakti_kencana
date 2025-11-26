<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Riwayat Peminjaman</title>

    <style>
        @page {
            margin: 25px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #000;
        }

        .header {
            text-align: center;
            margin-bottom: 25px;
        }

        .logo {
            width: 120px;
            margin-bottom: 10px;
        }

        .title-main {
            font-size: 20px;
            font-weight: bold;
        }

        .title-sub {
            font-size: 15px;
            margin-top: 3px;
            font-weight: bold;
        }

        .info {
            font-size: 12px;
            margin-top: 1px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th {
            background: #e5e7eb;
            padding: 8px;
            border: 1px solid #000;
            text-transform: uppercase;
            font-size: 11px;
        }

        td {
            padding: 7px;
            border: 1px solid #000;
            font-size: 11px;
            text-align: center;
        }

        tr:nth-child(even) {
            background: #f9fafb;
        }

        .status {
            padding: 3px 7px;
            border-radius: 4px;
            color: #fff;
            font-weight: bold;
        }

        .status-menunggu {
            background: #f59e0b;
        }

        .status-disetujui {
            background: #3b82f6;
        }

        .status-dipinjam {
            background: #2563eb;
        }

        .status-dikembalikan {
            background: #10b981;
        }

        .status-selesai {
            background: #14b8a6;
        }

        .status-ditolak {
            background: #dc2626;
        }

        .status-unknown {
            background: #6b7280;
        }

        .footer {
            margin-top: 35px;
            font-size: 11px;
            text-align: right;
        }
    </style>
</head>

<body>

    <div class="header">
        <img src="{{ public_path('assets/backend/images/logos/BKU2.png') }}" class="logo">
        <div class="title-main">UNIVERSITAS BHAKTI KENCANA</div>
        <div class="title-sub">Riwayat Peminjaman Barang</div>
        <div class="info"><strong>Periode:</strong> {{ $periode }}</div>
        <div class="info"><strong>Total Peminjaman:</strong> {{ $total }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Kode</th>
                <th>Nama Peminjam</th>
                <th>Barang</th>
                <th>Jumlah</th>
                <th>Tanggal Peminjaman</th>
                <th>Waktu</th>
                <th>Status</th>
                <th>Keterangan</th>
            </tr>
        </thead>

        <tbody>
            @forelse ($data as $i => $d)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $d->kode }}</td>
                    <td>{{ $d->nama }}</td>
                    <td>{{ $d->item }}</td>
                    <td>{{ $d->jumlah }}</td>
                    <td>{{ $d->tanggal_indonesia ?? $d->tanggal_format }}</td>
                    <td>
                        @php
                            [$mulai, $selesai] = explode(' - ', $d->waktu);
                        @endphp
                        {{ $mulai }} - {{ $selesai }}
                    </td>

                    @php
                        $statusClass = 'status-' . ($d->status ?? 'unknown');
                    @endphp

                    <td><span class="status {{ $statusClass }}">{{ $d->status_laporan }}</span></td>
                    <td>{{ $d->keterangan }}</td>

                </tr>
            @empty
                <tr>
                    <td colspan="8">Tidak ada data.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Dicetak pada: {{ now()->translatedFormat('d F Y H:i') }} WIB
    </div>
</body>

</html>
