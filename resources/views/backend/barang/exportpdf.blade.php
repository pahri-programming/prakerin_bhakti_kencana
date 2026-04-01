<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Data Barang</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; }
        h2 { text-align: center; margin-bottom: 4px; }
        .sub { text-align: center; color: #555; font-size: 11px; margin-bottom: 16px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ccc; padding: 6px 8px; text-align: left; vertical-align: middle; }
        th { background-color: #dbeafe; color: #1e3a5f; font-weight: bold; }
        tr:nth-child(even) { background-color: #f8fafc; }
        .harga { color: #16a34a; font-weight: bold; }
        .denda-ringan { color: #b45309; }
        .denda-berat  { color: #dc2626; }
        .denda-hilang { color: #111; font-weight: bold; }
        .text-muted { color: #888; }
        img { border-radius: 4px; }
    </style>
</head>
<body>
    <h2>Laporan Data Barang</h2>
    <p class="sub">Sistem Peminjaman Barang — Bhakti Kencana University</p>
    <p style="font-size:11px;">Tanggal Cetak: {{ $tanggal }}</p>

    <table>
        <thead>
            <tr>
                <th width="3%">No</th>
                <th width="7%">Foto</th>
                <th width="8%">Kode</th>
                <th width="18%">Nama Barang</th>
                <th width="10%">Kategori</th>
                <th width="10%">Harga</th>
                <th width="22%">Simulasi Denda</th>
                <th width="12%">Keterangan</th>
                <th width="10%">Tgl Input</th>
            </tr>
        </thead>
        <tbody>
            @foreach($barangs as $data)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>
                    @if(!empty($data->foto_base64))
                        <img src="{{ $data->foto_base64 }}" width="55" height="55" style="object-fit:cover;">
                    @else
                        <span class="text-muted">-</span>
                    @endif
                </td>
                <td>{{ $data->kode ?? '-' }}</td>
                <td>{{ $data->nama }}</td>
                <td>{{ $data->kategori?->nama ?? '-' }}</td>
                <td class="harga">
                    @if($data->harga > 0)
                        Rp {{ number_format($data->harga, 0, ',', '.') }}
                    @else
                        <span class="text-muted">-</span>
                    @endif
                </td>
                <td>
                    @if($data->harga > 0)
                        <span class="denda-ringan">Ringan: Rp {{ number_format($data->harga * 0.2, 0, ',', '.') }}</span><br>
                        <span class="denda-berat">Berat: Rp {{ number_format($data->harga * 0.8, 0, ',', '.') }}</span><br>
                        <span class="denda-hilang">Hilang: Rp {{ number_format($data->harga, 0, ',', '.') }}</span>
                    @else
                        <span class="text-muted">Harga belum diset</span>
                    @endif
                </td>
                <td>{{ Str::limit($data->keterangan, 30) }}</td>
                <td>{{ \Carbon\Carbon::parse($data->created_at)->format('d-m-Y') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>