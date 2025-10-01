@extends('layouts.backend')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-header bg-info text-white fw-bold">
                        Detail Data Jadwal
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <tr>
                                <th>Tanggal</th>
                                <td>{{ $jadwal->tanggal_format }}</td>
                            </tr>
                            <tr>
                                <th>Waktu Mulai</th>
                                <td>{{ $jadwal->waktu_mulai }}</td>
                            </tr>
                            <tr>
                                <th>Waktu Selesai</th>
                                <td>{{ $jadwal->waktu_selesai }}</td>
                            </tr>
                            <tr>
                                <th>Ruangan</th>
                                <td>{{ $jadwal->ruangan->nama_ruangan }}</td>
                            </tr>
                            <tr>
                                <th>Kegiatan</th>
                                <td>{{ $jadwal->kegiatan }}</td>
                            </tr>
                        </table>
                        <div class="d-flex justify-content-end mt-4">
                            <a href="{{ route('backend.jadwal.index') }}" class="btn btn-secondary hstack gap-2">
                                <i class="ti ti-arrow-back-up fs-5"></i> Kembali
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
