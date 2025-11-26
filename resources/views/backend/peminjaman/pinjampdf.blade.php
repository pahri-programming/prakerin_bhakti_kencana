<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Laporan Peminjaman Barang</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 6px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        h2 {
            text-align: center;
        }
    </style>
</head>

<body>
    <h2>Laporan Peminjaman Barang</h2>
    <p>Tanggal Cetak: {{ date('d-m-Y') }}</p>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Peminjam</th>
                <th>Barang</th>
                <th>Jumlah</th>
                <th>Tanggal</th>
                <th>Waktu</th>
                <th>Status</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $p)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $p->user->name }}</td>
                    <td>{{ $p->barang->nama }}</td>
                    <td>{{ $p->jumlah }}</td>
                    <td>{{ $p->tanggal_pinjam }}</td>
                    <td>{{ $p->tanggal_kembali}}</td>
                    <td>{{ substr($p->waktu_mulai, 0, 5) }} - {{ substr($p->waktu_selesai, 0, 5) }}</td>
                    <td>{{ ucfirst($p->status) }}</td>
                    <td>{{ $p->keterangan }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
