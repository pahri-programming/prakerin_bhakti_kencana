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
                        Data Pembayaran
                        <a href="{{ route('backend.ruangan.create') }}" class="btn btn-success btn-sm float-end">
                            <i class="ti ti-plus fs-5"></i> Tambah Ruangan
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table" id="dataRuangan">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Cover</th>
                                        <th>Kode Ruangan</th>
                                        <th>Nama Ruangan</th>
                                        <th>Kapasitas</th>
                                        <th>Lokasi</th>
                                        <th>Fasilitas</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($ruangan as $data)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                @if ($data->cover)
                                                    <img src="{{ Storage::url($data->cover) }}" alt="Cover"
                                                        width="100">
                                                @else
                                                    N/A
                                                @endif

                                            </td>
                                            <td>{{ $data->kode_ruangan }}</td>
                                            <td>{{ $data->nama_ruangan }}</td>
                                            <td>{{ $data->kapasitas }}</td>
                                            <td>{{ $data->lokasi }}</td>
                                            <td>{{ $data->fasilitas }}</td>
                                            <td>
                                                <a href="{{ route('backend.ruangan.edit', $data->id) }}"
                                                    class="btn btn-sm btn-warning">
                                                    <i class="ti ti-pencil me-1"></i>Edit
                                                </a>
                                                <a href="{{ route('backend.ruangan.show', $data->id) }}"
                                                    class="btn btn-sm btn-info">
                                                    <i class="ti ti-eye me-1"></i>Detail
                                                </a>
                                                <a href="{{ route('backend.ruangan.destroy', $data->id) }}"
                                                    class="btn btn-sm btn-danger"
                                                    onclick="event.preventDefault(); if(confirm('Yakin hapus ruangan ini?')) { document.getElementById('delete-form-{{ $data->id }}').submit(); }">
                                                    <i class="ti ti-trash me-1"></i> Delete
                                                </a>
                                                <form id="delete-form-{{ $data->id }}"
                                                    action="{{ route('backend.ruangan.destroy', $data->id) }}"
                                                    method="POST" style="display: none;">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>
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
        new DataTable('#dataRuangan', {
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/id.json'
            }
        });
    </script>
@endpush
