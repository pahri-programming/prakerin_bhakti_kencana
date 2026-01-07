@extends('layouts.backend')

@section('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.2/css/dataTables.bootstrap5.css">
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col">
                <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
                    <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="ti ti-building me-2"></i> Data Ruangan
                        </h5>
                        <a href="{{ route('backend.ruangan.create') }}" class="btn btn-success btn-sm">
                            <i class="ti ti-plus fs-5"></i> Tambah Ruangan
                        </a>
                    </div>

                    <div class="card-body bg-light">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0" id="dataRuangan">
                                <thead class="table-primary">
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Ruangan</th>
                                        <th>Kapasitas</th>
                                        <th>Lokasi</th>
                                        <th>Status</th>
=                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($ruangan as $data)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $data->nama_ruangan }}</td>
                                            <td>{{ $data->kapasitas }}</td>
                                            <td>{{ $data->lokasi }}</td>
                                        @if ($data->status == 'tersedia')
                                                <td><span class="badge bg-success">Tersedia</span></td>
                                            @else
                                                <td><span class="badge bg-warning">Tidak Tersedia</span></td>
                                            @endif
                                            <td class="text-center">
                                                <div class="d-flex justify-content-center gap-2">
                                                    <a href="{{ route('backend.ruangan.edit', $data->id) }}"
                                                        class="btn btn-sm btn-warning d-flex align-items-center">
                                                        <i class="ti ti-pencil me-1"></i>Edit
                                                    </a>
                                                    <a href="{{ route('backend.ruangan.show', $data->id) }}"
                                                        class="btn btn-sm btn-info d-flex align-items-center">
                                                        <i class="ti ti-eye me-1"></i>Detail
                                                    </a>
                                                    <form id="delete-form-{{ $data->id }}"
                                                        action="{{ route('backend.ruangan.destroy', $data->id) }}"
                                                        method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="button"
                                                            class="btn btn-sm btn-danger d-flex align-items-center"
                                                            onclick="confirmDelete({{ $data->id }})">
                                                            <i class="ti ti-trash me-1"></i>Delete
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Init DataTable
        new DataTable('#dataRuangan', {
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/id.json'
            }
        });

        // Konfirmasi delete pakai SweetAlert
        function confirmDelete(id) {
            Swal.fire({
                title: 'Yakin Hapus?',
                text: "Data ruangan akan dihapus permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonText: 'Batal',
                confirmButtonText: 'Ya, Hapus!'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + id).submit();
                }
            });
        }
    </script>
@endpush
