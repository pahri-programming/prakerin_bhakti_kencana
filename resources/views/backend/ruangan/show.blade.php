@extends('layouts.backend')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="card">
                    <div class="card-header bg-primary text-white fw-bold">
                        Detail Ruangan
                    </div>
                    <div class="card-body">
                        <div class="mb-3 text-center">
                            @if ($ruangan->cover)
                                <img src="{{ Storage::url($ruangan->cover) }}" alt="Cover" width="200">
                            @else
                                N/A (tidak ada gambar)
                            @endif
                        </div>
                        <table class="table table-bordered">
                            <tr>
                                <th>Kode Ruangan</th>
                                <td>{{ $ruangan->kode_ruangan }}</td>
                            </tr>
                            <tr>
                                <th>Nama Ruangan</th>
                                <td>{{ $ruangan->nama_ruangan }}</td>
                            </tr>
                            <tr>
                                <th>Kapasitas</th>
                                <td>{{ $ruangan->kapasitas }}</td>
                            </tr>
                            <tr>
                                <th>Lokasi</th>
                                <td>{{ $ruangan->lokasi }}</td>
                            </tr>
                            <tr>
                                <th>Fasilitas</th>
                                <td>{{ $ruangan->fasilitas }}</td>
                            </tr>
                        </table>
                        <div class="d-flex justify-content-end">
                            <a href="{{ route('backend.ruangan.index') }}" class="btn btn-secondary">
                                <i class="ti ti-arrow-left fs-5"></i> Kembali
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection