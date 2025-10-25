@extends('layouts.backend')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="card">
                <div class="card-header bg-primary text-white fw-bold">
                    Detail Data Barang
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th>Kode Barang</th>
                            <td>{{ $barang->kode }}</td>
                        </tr>
                        <tr>
                            <th>Nama Barang</th>
                            <td>{{ $barang->nama }}</td>
                        </tr>
                        <tr>
                            <th>Kategori</th>
                            <td>{{ $barang->kategori?->nama ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Stok</th>
                            <td>{{ $barang->stok }}</td>
                        </tr>
                        <tr>
                            <th>Keterangan</th>
                            <td>{{ $barang->keterangan }}</td>
                        </tr>
                        <tr>
                            <th>Tanggal Input</th>
                            <td>{{ $barang->created_at_format }}</td>
                        </tr>
                    </table>
                    <a href="{{ route('backend.barang.index') }}" class="btn btn-secondary mt-3">
                        <i class="ti ti-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
