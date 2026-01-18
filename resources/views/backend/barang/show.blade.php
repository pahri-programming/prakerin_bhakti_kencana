@extends('layouts.backend')
@section('title', 'Detail Barang')

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">Detail Barang</h2>
            <p class="text-muted mb-0">Informasi lengkap barang</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('backend.barang.index') }}" class="btn btn-secondary">
                <i class="ti ti-arrow-left me-2"></i>Kembali
            </a>
        </div>
    </div>

    <div class="row g-4">
        <!-- Main Info -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-primary text-white py-3">
                    <h5 class="mb-0"><i class="ti ti-info-circle me-2"></i>Informasi Barang</h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4">
                        <div class="col-md-12">
                            <label class="text-muted small mb-1">Nama Barang</label>
                            <h4 class="mb-0">{{ $barang->nama }}</h4>
                        </div>

                        <div class="col-md-6">
                            <label class="text-muted small mb-1">Kategori</label>
                            <div>
                                @if($barang->kategori)
                                    <span class="badge bg-primary px-3 py-2 fs-6">
                                        {{ $barang->kategori->nama }}
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="text-muted small mb-1">Tanggal Input</label>
                            <div class="fw-semibold">
                                <i class="ti ti-calendar me-2 text-primary"></i>
                                {{ $barang->created_at_format }}
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="text-muted small mb-1">Keterangan</label>
                            <p class="mb-0">{{ $barang->keterangan ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Timeline -->
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-light py-3">
                    <h5 class="mb-0"><i class="ti ti-clock me-2"></i>Timeline</h5>
                </div>
                <div class="card-body p-4">
                    <div class="timeline-item mb-3">
                        <div class="d-flex">
                            <div class="timeline-icon bg-success text-white rounded-circle me-3">
                                <i class="ti ti-plus"></i>
                            </div>
                            <div>
                                <p class="mb-1 fw-semibold">Dibuat</p>
                                <small class="text-muted">{{ $barang->created_at->format('d F Y, H:i') }}</small>
                            </div>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="d-flex">
                            <div class="timeline-icon bg-warning text-white rounded-circle me-3">
                                <i class="ti ti-refresh"></i>
                            </div>
                            <div>
                                <p class="mb-1 fw-semibold">Update Terakhir</p>
                                <small class="text-muted">{{ $barang->updated_at->format('d F Y, H:i') }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Card -->
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-light py-3">
                    <h5 class="mb-0"><i class="ti ti-settings me-2"></i>Aksi</h5>
                </div>
                <div class="card-body p-4">
                    <div class="d-grid gap-2">
                        <a href="{{ route('backend.barang.edit', $barang->id) }}" 
                           class="btn btn-warning">
                            <i class="ti ti-edit me-2"></i>Edit Barang
                        </a>
                        <form action="{{ route('backend.barang.destroy', $barang->id) }}" 
                              method="POST"
                              id="delete-form-{{ $barang->id }}">
                            @csrf
                            @method('DELETE')
                            <button type="button" 
                                    class="btn btn-danger w-100"
                                    onclick="confirmDelete({{ $barang->id }})">
                                <i class="ti ti-trash me-2"></i>Hapus Barang
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.timeline-icon {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    font-size: 18px;
}
</style>

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        window.confirmDelete = function(id) {
            Swal.fire({
                title: 'Yakin ingin menghapus barang ini?',
                text: "Data barang akan dihapus permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonText: 'Batal',
                confirmButtonText: 'Ya, Hapus!',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + id).submit();
                }
            });
        };
    </script>
@endpush
@endsection