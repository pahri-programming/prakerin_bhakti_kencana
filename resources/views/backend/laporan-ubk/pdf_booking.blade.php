<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>{{ $judul ?? 'Laporan Booking Ruangan' }}</title>

    <style>
        @page {
            margin: 25px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #000;
        }

        .header {
            text-align: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 3px solid #ff8c00;
        }

        .logo {
            width: 120px;
            margin-bottom: 10px;
        }

        .title-main {
            font-size: 20px;
            font-weight: bold;
            color: #000;
            margin-bottom: 3px;
        }

        .title-sub {
            font-size: 15px;
            font-weight: bold;
            color: #ff8c00;
            margin-top: 3px;
            margin-bottom: 10px;
        }

        .info {
            font-size: 12px;
            margin-top: 5px;
            color: #666;
        }

        .info strong {
            color: #000;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        thead {
            background-color: #ff8c00;
            color: white;
        }

        th {
            padding: 10px 8px;
            text-align: left;
            font-weight: bold;
            font-size: 11px;
            border: 1px solid #ddd;
        }

        td {
            padding: 8px;
            border: 1px solid #ddd;
            font-size: 11px;
            vertical-align: top;
        }

        tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        .text-center {
            text-align: center;
        }

        .status-badge {
            padding: 4px 10px;
            border-radius: 12px;
            font-weight: bold;
            font-size: 10px;
            display: inline-block;
            text-align: center;
            white-space: nowrap;
        }

        .status-menunggu {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffc107;
        }

        .status-disetujui {
            background: #dbeafe;
            color: #1e40af;
            border: 1px solid #3b82f6;
        }

        .status-selesai {
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #10b981;
        }

        .status-ditolak {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #ef4444;
        }

        .kode-badge {
            background-color: #ff8c00;
            color: white;
            padding: 3px 8px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 10px;
            font-family: 'Courier New', monospace;
        }

        .footer {
            margin-top: 35px;
            font-size: 11px;
            text-align: right;
            color: #666;
        }

        .text-muted {
            color: #666;
        }

        .fw-bold {
            font-weight: bold;
        }

        .small {
            font-size: 10px;
        }
    </style>
</head>

<body>
    <!-- Header -->
    <div class="header">
        <img src="{{ public_path('assets/backend/images/logos/BKU2.png') }}" class="logo" alt="Logo BKU">
        <div class="title-main">UNIVERSITAS BHAKTI KENCANA</div>
        <div class="title-sub">{{ $judul ?? 'Laporan Booking Ruangan' }}</div>
        <div class="info"><strong>Periode:</strong> {{ $periode ?? 'Semua Periode' }}</div>
        <div class="info"><strong>Total User Booking:</strong> {{ $total ?? 0 }} Orang</div>
    </div>

    <!-- Data Table -->
    <table>
        <thead>
            <tr>
                <th width="5%" class="text-center">No</th>
                <th width="12%">Kode Booking</th>
                <th width="20%">Nama Customer</th>
                <th width="20%">Ruangan</th>
                <th width="15%">Tanggal</th>
                <th width="15%">Waktu</th>
                <th width="13%" class="text-center">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($data as $i => $d)
                <tr>
                    <td class="text-center">{{ $i + 1 }}</td>
                    <td>
                        <span class="kode-badge">{{ $d->kode ?? '-' }}</span>
                    </td>
                    <td>
                        <strong>{{ $d->nama ?? 'User Dihapus' }}</strong>
                    </td>
                    <td>{{ $d->item ?? 'Ruangan Dihapus' }}</td>
                    <td>
                        {{ $d->tanggal_indonesia ?? '-' }}<br>
                        @if(isset($d->hari))
                            <span class="text-muted small">{{ $d->hari }}</span>
                        @endif
                    </td>
                    <td>
                        @php
                            $waktu = $d->waktu ?? '-';
                            if ($waktu !== '-') {
                                $range = explode(' - ', $waktu);
                                $mulai = $range[0] ?? '-';
                                $selesai = $range[1] ?? '-';
                            } else {
                                $mulai = '-';
                                $selesai = '-';
                            }
                        @endphp
                        <strong>{{ $mulai }}</strong> - <strong>{{ $selesai }}</strong>
                    </td>
                    <td class="text-center">
                        @php
                            $statusLaporan = $d->status_laporan ?? 'unknown';
                            $statusClass = 'status-' . strtolower($statusLaporan);
                        @endphp
                        <span class="status-badge {{ $statusClass }}">{{ $statusLaporan }}</span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center" style="padding: 20px;">
                        <em>Tidak ada data booking</em>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Footer -->
    <div class="footer">
        Dicetak pada: {{ now()->translatedFormat('d F Y H:i') }} WIB
    </div>
</body>

</html>