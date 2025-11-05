<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>{{ $judul }}</title>
    <style>
        @page {
            margin: 25px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            color: #000;
            font-size: 12px;
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
        }

        td {
            padding: 7px;
            border: 1px solid #000;
            font-size: 11px;
        }

        tr:nth-child(even) {
            background: #f9fafb;
        }

        .status {
            padding: 4px 8px;
            border-radius: 4px;
            font-weight: bold;
            display: inline-block;
            color: #fff;
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

        .status-selesai {
            background: #15c6d6;
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

        .signature-wrap {
            margin-top: 60px;
            text-align: right;
        }

        .signature-line {
            border-top: 1px solid #000;
            width: 220px;
            margin-left: auto;
        }

        .signature-name {
            font-weight: bold;
            margin-top: 5px;
        }

        .signature-note {
            font-size: 10px;
        }

        td.status-col,
        th.status-col {
            text-align: center;
        }
    </style>
</head>

<body>

    <div class="header">
        <img src="{{ public_path('assets/backend/images/logos/BKU2.png') }}" class="logo" alt="Logo">
        <div class="title-main">UNIVERSITAS BHAKTI KENCANA</div>
        <div class="title-sub">{{ $judul }}</div>
        <div class="info"><strong>Periode:</strong> {{ $periode }}</div>
        <div class="info"><strong>Total Pengguna:</strong> {{ $total }} Orang</div>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama</th>

                @php
                    $first = $data->first();
                    $isRuangan = $first && str_contains(strtolower($first->item), 'ruangan');
                    $isPeminjaman = session('laporan_jenis') === 'peminjaman';
                    $isPeminjaman = session('laporan_jenis') === 'peminjaman';
                    $isBarang = !$isRuangan;
                    $showJumlah = $isPeminjaman && $isBarang;

                @endphp
                @if ($showJumlah)
                    <th>Jumlah</th>
                @endif
                <th>{{ $isRuangan ? 'Ruangan' : 'Barang' }}</th>
                <th>Tanggal</th>
                <th>Waktu</th>
                <th class="status-col">Status</th>

                @if ($isPeminjaman)
                    <th>Keterangan</th>
                @endif
            </tr>
        </thead>

        <tbody>
            @forelse($data as $i => $d)
                @php
                    // Format waktu 18:00 → 18.00
                    [$mulai, $selesai] = explode(' - ', $d->waktu);
                    $mulai = str_replace(':', '.', $mulai);
                    $selesai = str_replace(':', '.', $selesai);

                    $statusCss =
                        [
                            'Menunggu' => 'status-menunggu',
                            'Disetujui' => 'status-disetujui',
                            'Dipinjam' => 'status-dipinjam',
                            'Selesai' => 'status-selesai',
                            'Ditolak' => 'status-ditolak',
                        ][$d->status_laporan] ?? 'status-unknown';
                @endphp

                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $d->nama }}</td>
                    @if ($showJumlah)
                        <td>{{ $d->jumlah ?? '-' }}</td>
                    @endif
                    <td>{{ $d->item }}</td>
                    <td>{{ $d->tanggal_indonesia }}</td>
                    <td>{{ $mulai }} - {{ $selesai }}</td>
                    <td class="status-col">
                        <span class="status {{ $statusCss }}">{{ $d->status_laporan }}</span>
                    </td>
                    @if ($isPeminjaman)
                        <td>{{ $d->keterangan ?? '-' }}</td>
                    @endif
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="font-style: italic;">Tidak ada data.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Dicetak pada: {{ now()->translatedFormat('d F Y H:i') }} WIB
    </div>
    <div class="signature-wrap" style="text-align: right;">
        <div style="display: inline-block; text-align: center;">
            <div class="signature-line"></div>
            <div class="signature-name">Admin UBK</div>
            <div class="signature-note">(Tanda Tangan Digital)</div>
        </div>
    </div>

</body>

</html>
