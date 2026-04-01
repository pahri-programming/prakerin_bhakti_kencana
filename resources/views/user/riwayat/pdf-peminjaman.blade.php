<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Riwayat Peminjaman</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; color: #1e293b; }

        .header {
            background: #16a34a; color: #fff;
            padding: 16px 20px; margin-bottom: 16px;
            border-radius: 6px;
        }
        .header h1 { font-size: 16px; font-weight: 700; margin-bottom: 2px; }
        .header p  { font-size: 10px; opacity: .85; }

        .meta {
            background: #f8fafc; border: 1px solid #e2e8f0;
            border-radius: 6px; padding: 10px 14px;
            margin-bottom: 14px; font-size: 10px; color: #475569;
        }
        .meta span { font-weight: 700; color: #1e293b; }

        table { width: 100%; border-collapse: collapse; }
        thead th {
            background: #16a34a; color: #fff;
            padding: 8px 10px; text-align: left;
            font-size: 10px; font-weight: 700;
        }
        tbody tr:nth-child(even) td { background: #f8fafc; }
        tbody td { padding: 8px 10px; border-bottom: 1px solid #e2e8f0; font-size: 10px; vertical-align: top; }

        .badge {
            display: inline-block; padding: 2px 8px;
            border-radius: 10px; font-size: 9px; font-weight: 700;
        }
        .badge-menunggu  { background: #fef9e7; color: #d35400; }
        .badge-disetujui { background: #eef4fd; color: #1a5276; }
        .badge-ditolak   { background: #fdecea; color: #c0392b; }
        .badge-selesai   { background: #eafaf1; color: #1e8449; }

        .barang-item { margin-bottom: 2px; }

        .footer {
            margin-top: 20px; text-align: right;
            font-size: 9px; color: #94a3b8;
            border-top: 1px solid #e2e8f0; padding-top: 8px;
        }
    </style>
</head>
<body>

<div class="header">
    <h1>Riwayat Peminjaman Barang</h1>
    <p>Universitas Bhakti Kencana &bull; Dicetak: {{ now()->translatedFormat('d F Y, H:i') }} WIB</p>
</div>

<div class="meta">
    Nama Peminjam: <span>{{ $user->name }}</span> &nbsp;&bull;&nbsp;
    Email: <span>{{ $user->email }}</span> &nbsp;&bull;&nbsp;
    Total Data: <span>{{ $peminjaman->count() }} peminjaman</span>
</div>

<table>
    <thead>
        <tr>
            <th style="width:25px;">#</th>
            <th>Kode</th>
            <th>Barang (Ruangan × Jumlah)</th>
            <th>Tgl Pinjam</th>
            <th>Tgl Kembali</th>
            <th>Keterangan</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($peminjaman as $p)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $p->kode ?? '-' }}</td>
                <td>
                    @foreach ($p->detailbarangs as $d)
                        <div class="barang-item">
                            &bull; {{ $d->barangRuangan->barang->nama ?? '-' }}
                            ({{ $d->barangRuangan->ruangan->nama_ruangan ?? '-' }})
                            &times;{{ $d->jumlah }}
                        </div>
                    @endforeach
                </td>
                <td>{{ \Carbon\Carbon::parse($p->tanggal_pinjam)->format('d/m/Y') }}</td>
                <td>
                    {{ $p->tanggal_kembali
                        ? \Carbon\Carbon::parse($p->tanggal_kembali)->format('d/m/Y')
                        : '-' }}
                </td>
                <td style="max-width:100px;">{{ $p->keterangan ?? '-' }}</td>
                <td>
                    @php $st = strtolower($p->status) @endphp
                    @if($st === 'menunggu')
                        <span class="badge badge-menunggu">Menunggu</span>
                    @elseif($st === 'disetujui')
                        <span class="badge badge-disetujui">Disetujui</span>
                    @elseif($st === 'ditolak')
                        <span class="badge badge-ditolak">Ditolak</span>
                    @elseif($st === 'selesai')
                        <span class="badge badge-selesai">Selesai</span>
                    @else
                        <span class="badge">{{ $p->status }}</span>
                    @endif
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7" style="text-align:center; padding:20px; color:#94a3b8;">
                    Tidak ada data peminjaman.
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