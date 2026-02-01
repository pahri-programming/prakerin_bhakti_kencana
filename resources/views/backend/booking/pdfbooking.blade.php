<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Booking Ruangan</title>
    <style>
        @page {
            margin: 25px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 3px solid #667eea;
        }

        .logo {
            width: 120px;
            margin-bottom: 10px;
        }

        .header h1 {
            font-size: 20px;
            color: #000;
            margin-bottom: 3px;
            font-weight: bold;
        }

        .header h2 {
            font-size: 15px;
            color: #667eea;
            font-weight: bold;
            margin-top: 3px;
            margin-bottom: 10px;
        }

        .header-info {
            font-size: 12px;
            color: #666;
            margin-top: 5px;
        }

        .info-section {
            margin-bottom: 15px;
            background: #f8f9fa;
            padding: 10px;
            border-radius: 5px;
        }

        .info-section table {
            width: 100%;
        }

        .info-section td {
            padding: 3px 0;
        }

        .info-section td:first-child {
            width: 150px;
            font-weight: bold;
            color: #555;
        }

        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table.data-table thead {
            background-color: #667eea;
            color: white;
        }

        table.data-table th {
            padding: 10px 8px;
            text-align: left;
            font-weight: bold;
            font-size: 11px;
            border: 1px solid #ddd;
        }

        table.data-table td {
            padding: 8px;
            border: 1px solid #ddd;
            vertical-align: top;
        }

        table.data-table tbody tr:nth-child(even) {
            background-color: #f8f9fa;
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

        .status-pending {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffc107;
        }

        .status-diterima {
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #10b981;
        }

        .status-ditolak {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #ef4444;
        }

        .status-selesai {
            background: #e0e7ff;
            color: #3730a3;
            border: 1px solid #6366f1;
        }

        .kode-badge {
            background-color: #667eea;
            color: white;
            padding: 3px 8px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 10px;
            font-family: 'Courier New', monospace;
        }

        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 2px solid #ddd;
            text-align: center;
            font-size: 10px;
            color: #666;
        }

        .summary {
            margin-top: 15px;
            margin-bottom: 15px;
            width: 100%;
            border-collapse: collapse;
        }

        .summary td {
            width: 25%;
            text-align: center;
            padding: 10px;
            background: #f8f9fa;
            border: 1px solid #ddd;
        }

        .summary strong {
            display: block;
            font-size: 18px;
            color: #667eea;
            margin-bottom: 5px;
        }

        .summary span {
            font-size: 11px;
            color: #666;
        }

        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .fw-bold { font-weight: bold; }
        .text-muted { color: #666; }
        .small { font-size: 10px; }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <img src="{{ public_path('assets/backend/images/logos/BKU2.png') }}" class="logo" alt="Logo BKU">
        <h1>UNIVERSITAS BHAKTI KENCANA</h1>
        <h2>Laporan Booking Ruangan</h2>
        <div class="header-info"><strong>Periode:</strong> 
            @if(request()->filled('tanggal'))
                {{ \Carbon\Carbon::parse(request('tanggal'))->translatedFormat('d F Y') }}
            @else
                Semua Periode
            @endif
        </div>
        <div class="header-info"><strong>Total Booking:</strong> {{ $booking->count() }}</div>
    </div>

    <!-- Info Section -->
    <div class="info-section">
        <table>
            @if(request()->filled('ruang_id'))
                <tr>
                    <td>Filter Ruangan</td>
                    <td>: {{ \App\Models\Ruangan::find(request('ruang_id'))->nama_ruangan ?? '-' }}</td>
                </tr>
            @endif
            @if(request()->filled('status'))
                <tr>
                    <td>Filter Status</td>
                    <td>: {{ request('status') }}</td>
                </tr>
            @endif
            <tr>
                <td>Tanggal Cetak</td>
                <td>: {{ \Carbon\Carbon::now()->translatedFormat('d F Y, H:i') }} WIB</td>
            </tr>
        </table>
    </div>

    <!-- Summary Statistics -->
    <table class="summary">
        <tr>
            <td>
                <strong>{{ $booking->where('status', 'Pending')->count() }}</strong>
                <span>Pending</span>
            </td>
            <td>
                <strong>{{ $booking->where('status', 'Diterima')->count() }}</strong>
                <span>Diterima</span>
            </td>
            <td>
                <strong>{{ $booking->where('status', 'Ditolak')->count() }}</strong>
                <span>Ditolak</span>
            </td>
            <td>
                <strong>{{ $booking->where('status', 'Selesai')->count() }}</strong>
                <span>Selesai</span>
            </td>
        </tr>
    </table>

    <!-- Data Table -->
    <table class="data-table">
        <thead>
            <tr>
                <th width="3%" class="text-center">No</th>
                <th width="10%">Kode</th>
                <th width="15%">Peminjam</th>
                <th width="15%">Ruangan</th>
                <th width="12%">Tanggal</th>
                <th width="10%">Waktu</th>
                <th width="8%" class="text-center">Status</th>
                <th width="27%">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($booking as $data)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td>
                        <span class="kode-badge">{{ $data->kode }}</span>
                    </td>
                    <td>
                        <strong>{{ $data->user->name }}</strong><br>
                        <span class="small text-muted">{{ $data->user->email }}</span>
                    </td>
                    <td>
                        <strong>{{ $data->ruangan->nama_ruangan }}</strong><br>
                        <span class="small text-muted">
                            Kapasitas: {{ $data->ruangan->kapasitas }} orang
                        </span>
                    </td>
                    <td>
                        {{ \Carbon\Carbon::parse($data->tanggal)->translatedFormat('d M Y') }}<br>
                        <span class="small text-muted">{{ \Carbon\Carbon::parse($data->tanggal)->translatedFormat('l') }}</span>
                    </td>
                    <td>
                        <strong>{{ \Carbon\Carbon::parse($data->waktu_mulai)->format('H:i') }}</strong> - 
                        <strong>{{ \Carbon\Carbon::parse($data->waktu_selesai)->format('H:i') }}</strong><br>
                        <span class="small text-muted">
                            ({{ \Carbon\Carbon::parse($data->waktu_mulai)->diffInMinutes(\Carbon\Carbon::parse($data->waktu_selesai)) }} menit)
                        </span>
                    </td>
                    <td class="text-center">
                        @switch($data->status)
                            @case('Pending')
                                <span class="status-badge status-pending">Pending</span>
                                @break
                            @case('Diterima')
                                <span class="status-badge status-diterima">Diterima</span>
                                @break
                            @case('Ditolak')
                                <span class="status-badge status-ditolak">Ditolak</span>
                                @break
                            @case('Selesai')
                                <span class="status-badge status-selesai">Selesai</span>
                                @break
                        @endswitch
                    </td>
                    <td class="small">
                        @if($data->status === 'Ditolak' && $data->keterangan)
                            <strong class="text-muted">Alasan Penolakan:</strong><br>
                            {{ $data->keterangan }}
                        @else
                            -
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center" style="padding: 20px;">
                        <em>Tidak ada data booking</em>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Footer -->
    <div class="footer">
        <p>Dicetak pada: {{ \Carbon\Carbon::now()->translatedFormat('d F Y H:i') }} WIB</p>
    </div>
</body>
</html>