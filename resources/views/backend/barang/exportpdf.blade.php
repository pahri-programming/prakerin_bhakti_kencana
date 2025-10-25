<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
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
    <h2>Laporan Data Barang</h2>
    <p>Tanggal: {{ $tanggal }}</p>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Kode</th>
                <th>Nama</th>
                <th>Kategori</th>
                <th>Stok</th>
                <th>Keterangan</th>
                <th>Tanggal Input</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($barangs as $data)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $data->kode }}</td>
                    <td>{{ $data->nama }}</td>
                    <td>{{ $data->kategori?->nama ?? '-' }}</td>
                    <td>{{ $data->stok }}</td>
                    <td>{{ $data->keterangan }}</td>
                    <td>{{ \Carbon\Carbon::parse($data->created_at)->format('d-m-Y') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

</body>

</html>
