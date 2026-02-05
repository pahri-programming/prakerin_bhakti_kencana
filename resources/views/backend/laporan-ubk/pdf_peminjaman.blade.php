<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $judul ?? 'Laporan Peminjaman Barang' }}</title>
    <style>
        @page {
            margin: 20px;
        }
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 9px;
            color: #000;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 3px solid #ff8c00;
        }
        .logo {
            width: 100px;
            margin-bottom: 8px;
        }
        .title-main {
            font-size: 18px;
            font-weight: bold;
            color: #000;
            margin-bottom: 3px;
        }
        .title-sub {
            font-size: 14px;
            font-weight: bold;
            color: #ff8c00;
            margin-top: 3px;
            margin-bottom: 8px;
        }
        .info {
            font-size: 10px;
            margin-top: 4px;
            color: #666;
        }
        .info strong {
            color: #000;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        thead {
            background-color: #ff8c00;
            color: white;
        }
        th {
            padding: 8px 5px;
            text-align: left;
            font-weight: bold;
            font-size: 9px;
            border: 1px solid #ddd;
        }
        td {
            padding: 6px 5px;
            border: 1px solid #ddd;
            font-size: 8px;
            vertical-align: top;
        }
        tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        .text-center {
            text-align: center;
        }
        .status-badge {
            padding: 3px 8px;
            border-radius: 10px;
            font-weight: bold;
            font-size: 7px;
            display: inline-block;
            text-align: center;
            white-space: nowrap;
        }
        .status-menunggu { background: #fff3cd; color: #856404; border: 1px solid #ffc107; }
        .status-disetujui { background: #dbeafe; color: #1e40af; border: 1px solid #3b82f6; }
        .status-dipinjam { background: #e0e7ff; color: #3730a3; border: 1px solid #6366f1; }
        .status-dikembalikan { background: #d1fae5; color: #065f46; border: 1px solid #10b981; }
        .status-ditolak { background: #fee2e2; color: #991b1b; border: 1px solid #ef4444; }
        .kode-badge {
            background-color: #ff8c00;
            color: white;
            padding: 2px 6px;
            border-radius: 3px;
            font-weight: bold;
            font-size: 8px;
            font-family: 'Courier New', monospace;
        }
        .jumlah-badge {
            background-color: #374151;
            color: white;
            padding: 2px 6px;
            border-radius: 3px;
            font-weight: bold;
            font-size: 7px;
        }
        .footer {
            margin-top: 25px;
            font-size: 9px;
            text-align: right;
            color: #666;
        }
        ul {
            margin: 0;
            padding-left: 12px;
            list-style: none;
        }
        ul li {
            margin-bottom: 3px;
            line-height: 1.4;
        }
        .item-bullet {
            color: #ff8c00;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <img src="{{ public_path('assets/backend/images/logos/BKU2.png') }}" class="logo" alt="Logo BKU">
        <div class="title-main">UNIVERSITAS BHAKTI KENCANA</div>
        <div class="title-sub">{{ $judul ?? 'Laporan Peminjaman Barang' }}</div>
        <div class="info"><strong>Periode:</strong> {{ $periode ?? 'Semua Periode' }}</div>
        <div class="info"><strong>Total User Peminjaman:</strong> {{ $total ?? 0 }} Orang</div>
    </div>

    <!-- Data Table -->
    <table>
        <thead>
            <tr>
                <th width="3%" class="text-center">No</th>
                <th width="10%">Kode</th>
                <th width="12%">Nama Peminjam</th>
                <th width="25%">Detail Barang</th>
                <th width="15%">Ruangan</th>
                <th width="15%">Periode Peminjaman</th>
                <th width="10%" class="text-center">Status</th>
                <th width="10%">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($peminjamans as $i => $p)
                <tr>
                    <td class="text-center">{{ $i + 1 }}</td>
                    <td>
                        <span class="kode-badge">{{ $p->kode }}</span>
                    </td>
                    <td>
                        <strong>{{ $p->user->name ?? 'User Dihapus' }}</strong>
                    </td>
                    <td>
                        @if($p->detailbarangs && $p->detailbarangs->isNotEmpty())
                            <ul>
                                @foreach($p->detailbarangs as $detail)
                                    <li>
                                        <span class="item-bullet">•</span> {{ $detail->barangRuangan->barang->nama ?? '-' }}
                                        <span class="jumlah-badge">{{ $detail->jumlah }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            -
                        @endif
                    </td>
                    <td>
                        @if($p->detailbarangs && $p->detailbarangs->isNotEmpty())
                            <ul>
                                @foreach($p->detailbarangs->unique('barang_ruangan_id') as $detail)
                                    <li>
                                        <span class="item-bullet">•</span> {{ $detail->barangRuangan->ruangan->nama_ruangan ?? '-' }}
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            -
                        @endif
                    </td>
                    <td>
                        {{ \Carbon\Carbon::parse($p->tanggal_pinjam)->translatedFormat('d M Y') }}
                        s/d
                        {{ \Carbon\Carbon::parse($p->tanggal_kembali)->translatedFormat('d M Y') }}
                    </td>
                    <td class="text-center">
                        @php
                            $status = strtolower($p->status);
                            $statusClass = 'status-' . $status;
                            $statusLabel = match ($status) {
                                'menunggu' => 'Menunggu',
                                'disetujui' => 'Disetujui',
                                'dipinjam' => 'Dipinjam',
                                'dikembalikan' => 'Dikembalikan',
                                'ditolak' => 'Ditolak',
                                default => 'Unknown',
                            };
                        @endphp
                        <span class="status-badge {{ $statusClass }}">{{ $statusLabel }}</span>
                    </td>
                    <td>
                        {{ Str::limit($p->keterangan, 40) }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center" style="padding: 15px;">
                        <em>Tidak ada data peminjaman</em>
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