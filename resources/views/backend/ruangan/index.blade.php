@extends('layouts.backend')

@section('title', 'Manajemen Ruangan')

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/2.3.2/css/dataTables.bootstrap5.css">
<style>
    .page-header {
        background: linear-gradient(135deg, #ff9800 0%, #ff5722 100%);
        padding: 2rem;
        border-radius: 15px;
        color: white;
        margin-bottom: 2rem;
    }

    .table-card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    }

    .table-card .card-header {
        background: white;
        border-bottom: 2px solid #f0f0f0;
        padding: 1.5rem;
        border-radius: 15px 15px 0 0;
    }

    .status-badge {
        padding: 0.5rem 1rem;
        border-radius: 50px;
        font-weight: 600;
        font-size: 0.85rem;
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
    }

    .status-tersedia {
        background: #d4edda;
        color: #155724;
    }

    .status-dipinjam {
        background: #fff3cd;
        color: #856404;
    }

    .stats-badge {
        background: #e3f2fd;
        color: #1976d2;
        padding: 0.375rem 0.75rem;
        border-radius: 8px;
        font-size: 0.85rem;
        font-weight: 600;
    }

    #dataRuangan thead th {
        background: linear-gradient(135deg, #ff9800 0%, #ff5722 100%);
        color: white;
        font-weight: 600;
        border: none;
        padding: 1rem;
    }

    #dataRuangan tbody tr {
        transition: all 0.2s ease;
    }

    #dataRuangan tbody tr:hover {
        background: #fff3e0;
        transform: scale(1.01);
    }

    .btn-group-actions {
        display: flex;
        gap: 0.5rem;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="fw-bold mb-2">
                    <i class="ti ti-building"></i> Manajemen Ruangan
                </h2>
                <p class="mb-0 opacity-90">Kelola data ruangan kampus</p>
            </div>
            <div>
                <a href="{{ route('backend.ruangan.create') }}" class="btn btn-light btn-lg shadow-sm">
                    <i class="ti ti-plus"></i> Tambah Ruangan
                </a>
            </div>
        </div>
    </div>

    <!-- Table Card -->
    <div class="card table-card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold">
                    <i class="ti ti-list"></i> Daftar Ruangan
                </h5>
                <div class="d-flex gap-2">
                    <span class="stats-badge">
                        <i class="ti ti-building"></i>
                        Total: {{ $ruangan->count() }} Ruangan
                    </span>
                    <span class="status-badge status-tersedia">
                        <i class="ti ti-check"></i>
                        Tersedia: {{ $ruangan->where('status', 'tersedia')->count() }}
                    </span>
                    <span class="status-badge status-dipinjam">
                        <i class="ti ti-clock"></i>
                        Dipinjam: {{ $ruangan->where('status', 'dipinjam')->count() }}
                    </span>
                </div>
            </div>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="dataRuangan">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th width="25%">Nama Ruangan</th>
                            <th width="15%">Kapasitas</th>
                            <th width="20%">Lokasi</th>
                            <th width="15%">Status</th>
                            <th width="10%" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($ruangan as $data)
                            <tr>
                                <td class="fw-bold">{{ $loop->iteration }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm bg-light-primary text-primary rounded me-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                            <i class="ti ti-door fs-5"></i>
                                        </div>
                                        <div>
                                            <strong>{{ $data->nama_ruangan }}</strong>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <i class="ti ti-users text-primary me-1"></i>
                                    <strong>{{ $data->kapasitas }}</strong> Orang
                                </td>
                                <td>
                                    <i class="ti ti-map-pin text-warning me-1"></i>
                                    {{ $data->lokasi ?? '-' }}
                                </td>
                                <td>
                                    @if ($data->status == 'tersedia')
                                        <span class="status-badge status-tersedia">
                                            <i class="ti ti-check"></i> Tersedia
                                        </span>
                                    @else
                                        <span class="status-badge status-dipinjam">
                                            <i class="ti ti-clock"></i> Dipinjam
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group-actions justify-content-center">
                                        <a href="{{ route('backend.ruangan.edit', $data->id) }}" 
                                           class="btn btn-sm btn-warning" 
                                           data-bs-toggle="tooltip" 
                                           title="Edit">
                                            <i class="ti ti-edit"></i>
                                        </a>
                                        <form id="delete-form-{{ $data->id }}" 
                                              action="{{ route('backend.ruangan.destroy', $data->id) }}" 
                                              method="POST" 
                                              class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" 
                                                    class="btn btn-sm btn-danger" 
                                                    onclick="confirmDelete({{ $data->id }})" 
                                                    data-bs-toggle="tooltip" 
                                                    title="Hapus">
                                                <i class="ti ti-trash"></i>
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
@endsection

@push('scripts')
<script src="https://cdn.datatables.net/2.3.2/js/dataTables.js"></script>
<script src="https://cdn.datatables.net/2.3.2/js/dataTables.bootstrap5.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        // Init DataTable
        $('#dataRuangan').DataTable({
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/id.json'
            },
            order: [[1, 'asc']], // Sort by nama ruangan
            pageLength: 10
        });

        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });

    // Konfirmasi delete
    function confirmDelete(id) {
        Swal.fire({
            title: 'Yakin Hapus Ruangan?',
            text: "Data ruangan akan dihapus permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + id).submit();
            }
        });
    }
</script>
@endpush