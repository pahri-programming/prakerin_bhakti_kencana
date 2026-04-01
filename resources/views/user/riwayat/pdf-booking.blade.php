<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Riwayat Booking</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; color: #1e293b; }

        .header {
            background: #1a6bb5; color: #fff;
            padding: 16px 20px; margin-bottom: 16px;
            border-radius: 6px;
        }
        .header h1 { font-size: 16px; font-weight: 700; margin-bottom: 2px; }
        .header p  { font-size: 10px; opacity: .85; }

        .meta {
            display: flex; gap: 24px;
            background: #f8fafc; border: 1px solid #e2e8f0;
            border-radius: 6px; padding: 10px 14px;
            margin-bottom: 14px; font-size: 10px; color: #475569;
        }
        .meta span { font-weight: 700; color: #1e293b; }

        table { width: 100%; border-collapse: collapse; }
        thead th {
            background: #1a6bb5; color: #fff;
            padding: 8px 10px; text-align: left;
            font-size: 10px; font-weight: 700;
        }
        tbody tr:nth-child(even) td { background: #f8fafc; }
        tbody td { padding: 8px 10px; border-bottom: 1px solid #e2e8f0; font-size: 10px; }

        .badge {
            display: inline-block; padding: 2px 8px;
            border-radius: 10px; font-size: 9px; font-weight: 700;
        }
        .badge-pending  { background: #fef9e7; color: #d35400; }
        .badge-diterima { background: #eef4fd; color: #1a5276; }
        .badge-ditolak  { background: #fdecea; color: #c0392b; }
        .badge-selesai  { background: #eafaf1; color: #1e8449; }

        .footer {
            margin-top: 20px; text-align: right;
            font-size: 9px; color: #94a3b8;
            border-top: 1px solid #e2e8f0; padding-top: 8px;
        }
    </style>
</head>
<body>

<div class="header">
    <h1>Riwayat Booking Ruangan</h1>
    <p>Universitas Bhakti Kencana &bull; Dicetak: {{ now()->translatedFormat('d F Y, H:i') }} WIB</p>
</div>

<div class="meta">
    <div>Nama Peminjam: <span>{{ $user->name }}</span></div>
    <div>Email: <span>{{ $user->email }}</span></div>
    <div>Total Data: <span>{{ $booking->count() }} booking</span></div>
</div>

<table>
    <thead>
        <tr>
            <th style="width:30px;">#</th>
            <th>Kode Booking</th>
            <th>Ruangan</th>
            <th>Tanggal</th>
            <th>Waktu Mulai</th>
            <th>Waktu Selesai</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($booking as $b)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $b->kode ?? '-' }}</td>
                <td>{{ $b->ruangan->nama_ruangan ?? '-' }}</td>
                <td>{{ \Carbon\Carbon::parse($b->tanggal)->translatedFormat('d F Y') }}</td>
                <td>{{ substr($b->waktu_mulai, 0, 5) }}</td>
                <td>{{ substr($b->waktu_selesai, 0, 5) }}</td>
                <td>
                    @if($b->status === 'Pending')
                        <span class="badge badge-pending">Menunggu</span>
                    @elseif($b->status === 'Diterima')
                        <span class="badge badge-diterima">Diterima</span>
                    @elseif($b->status === 'Ditolak')
                        <span class="badge badge-ditolak">Ditolak</span>
                    @elseif($b->status === 'Selesai')
                        <span class="badge badge-selesai">Selesai</span>
                    @else
                        <span class="badge">{{ $b->status }}</span>
                    @endif
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7" style="text-align:center; padding:20px; color:#94a3b8;">
                    Tidak ada data booking.
                </td>
            </tr>
        @endforelse
    </tbody>
</table>

<div class="footer">
    Dicetak oleh sistem &bull; {{ now()->format('d/m/Y H:i') }}
</div>

</body>
</html>