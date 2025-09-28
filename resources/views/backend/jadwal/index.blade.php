@extends('layouts.backend')

@section('styles')
   <link rel="stylesheet" href="https://cdn.datatables.net/2.3.2/css/dataTables.bootstrap5.css">
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-header bg-secondary text-white">
                        Data Jadwal
                        <a href="{{ route('backend.jadwal.create') }}" class="btn btn-success btn-sm float-end">
                            <i class="ti ti-plus fs-5"></i> Tambah Jadwal
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table" id="dataJadwal">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Tanggal</th>
                                        <th>Waktu Mulai</th>
                                        <th>Waktu Selesai</th>
                                        <th>Ruangan</th>
                                        <th>Kegiatan</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($jadwal as $data)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $data->tanggal }}</td>
                                            <td>{{ $data->waktu_mulai }}</td>
                                            <td>{{ $data->waktu_selesai }}</td>
                                            <td>{{ $data->ruangan->nama_ruangan }}</td>
                                            <td>{{ $data->kegiatan }}</td>
                                            <td class="text-end">
                                                <div class="d-inline-flex gap-1">
                                                    <a href="{{ route('backend.jadwal.edit', $data->id) }}"
                                                        class="btn btn-sm btn-warning">
                                                        <i class="ti ti-pencil me-1"></i>Edit
                                                    </a>
                                                    <a href="{{ route('backend.jadwal.show', $data->id) }}"
                                                        class="btn btn-sm btn-info">
                                                        <i class="ti ti-eye me-1"></i>Detail
                                                    </a>
                                                    <form action="{{ route('backend.jadwal.destroy', $data->id) }}"
                                                        method="POST"
                                                        onsubmit="return confirm('Yakin ingin menghapus data ini?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger">
                                                            <i class="ti ti-trash me-1"></i>Hapus
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.datatables.net/2.3.2/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.3.2/js/dataTables.bootstrap5.js"></script>
    <script>
        new DataTable('#dataJadwal', {
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/id.json'
            }
        });
    </script>
@endpush