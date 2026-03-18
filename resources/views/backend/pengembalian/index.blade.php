@extends('layouts.backend')
@section('title', 'Data Pengembalian Barang')

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">Pengembalian Barang</h2>
            <p class="text-muted mb-0">Kelola data pengembalian barang peminjaman</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('backend.pengembalian.export') }}" class="btn btn-danger">
                <i class="fas fa-file-pdf me-2"></i>Export PDF
            </a>
            <a href="{{ route('backend.pengembalian.create') }}" class="btn btn-primary px-4">
                <i class="fas fa-plus me-2"></i>Tambah Pengembalian
            </a>
        </div>
    </div>

    <!-- Alert Success -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('warning'))
        <div class="alert alert-warning alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>{{ session('warning') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Main Card -->
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body p-0">
            <!-- Search & Filter Section -->
            <div class="p-4 border-bottom bg-light">
                <form action="{{ route('backend.pengembalian.index') }}" method="GET">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0">
                                    <i class="fas fa-search text-muted"></i>
                                </span>
                                <input type="text" 
                                       name="search" 
                                       class="form-control border-start-0 ps-0" 
                                       placeholder="Cari kode peminjaman atau nama..."
                                       value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <input type="date" 
                                   name="tanggal_kembali" 
                                   class="form-control" 
                                   placeholder="Tanggal Kembali"
                                   value="{{ request('tanggal_kembali') }}">
                        </div>
                        <div class="col-md-2">
                            <select name="status" class="form-select">
                                <option value="">Semua Status</option>
                                <option value="menunggu_pic" {{ request('status') == 'menunggu_pic' ? 'selected' : '' }}>
                                    Menunggu PIC
                                </option>
                                <option value="dikembalikan" {{ request('status') == 'dikembalikan' ? 'selected' : '' }}>
                                    Dikembalikan
                                </option>
                                <option value="perlu_tindakan" {{ request('status') == 'perlu_tindakan' ? 'selected' : '' }}>
                                    Perlu Tindakan
                                </option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary flex-grow-1">
                                    <i class="fas fa-filter me-1"></i>Filter
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
                
                @if(request()->hasAny(['search', 'tanggal_kembali', 'status']))
                    <div class="mt-3">
                        <a href="{{ route('backend.pengembalian.index') }}" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-times me-1"></i>Reset Filter
                        </a>
                    </div>
                @endif
            </div>

            <!-- Table Section -->
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="px-4 py-3 text-muted fw-semibold" width="5%">No</th>
                            <th class="py-3 text-muted fw-semibold" width="12%">Kode Peminjaman</th>
                            <th class="py-3 text-muted fw-semibold" width="13%">Peminjam</th>
                            <th class="py-3 text-muted fw-semibold" width="10%">Tanggal</th>
                            <th class="py-3 text-muted fw-semibold" width="12%">Status Awal</th>
                            <th class="py-3 text-muted fw-semibold" width="11%">Status</th>
                            <th class="py-3 text-muted fw-semibold" width="12%">Verifikasi PIC</th>
                            <th class="py-3 text-muted fw-semibold text-center" width="12%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pengembalian as $index => $item)
                            <tr class="border-bottom">
                                <td class="px-4">
                                    <span class="text-muted">{{ $pengembalian->firstItem() + $index }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2">
                                        {{ $item->peminjamanBarang->kode ?? '-' }}
                                    </span>
                                </td>
                                <td>
                                    <div>
                                        <div class="fw-semibold">
                                            {{ $item->peminjamanBarang->nama_peminjam ?? $item->peminjamanBarang->user->name }}
                                        </div>
                                        @if($item->peminjamanBarang->instansi)
                                            <small class="text-muted">
                                                <i class="fas fa-building me-1"></i>{{ Str::limit($item->peminjamanBarang->instansi, 15) }}
                                            </small>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="small">
                                        <i class="far fa-calendar me-1 text-primary"></i>
                                        {{ \Carbon\Carbon::parse($item->tanggal_kembali)->format('d M Y') }}
                                    </div>
                                </td>
                                <td>
                                    @if($item->detailpengembalians && $item->detailpengembalians->count() > 0)
                                        @php
                                            $baik = $item->detailpengembalians->where('status_awal', 'baik')->count();
                                            $bermasalah = $item->detailpengembalians->where('status_awal', 'bermasalah')->count();
                                        @endphp
                                        <div class="small">
                                            @if($baik > 0)
                                                <span class="badge bg-success bg-opacity-10 text-success mb-1">
                                                    ✓ {{ $baik }} Baik
                                                </span>
                                            @endif
                                            @if($bermasalah > 0)
                                                <span class="badge bg-warning bg-opacity-10 text-warning mb-1">
                                                    ⚠ {{ $bermasalah }} Bermasalah
                                                </span>
                                            @endif
                                        </div>
                                    @else
                                        <small class="text-muted">-</small>
                                    @endif
                                </td>
                                <td>
                                    @if($item->status == 'menunggu_pic')
                                        <span class="badge bg-info px-3 py-2">
                                            <i class="fas fa-clock me-1"></i>Menunggu PIC
                                        </span>
                                    @elseif($item->status == 'dikembalikan')
                                        <span class="badge bg-success px-3 py-2">
                                            <i class="fas fa-check-circle me-1"></i>Dikembalikan
                                        </span>
                                    @else
                                        <span class="badge bg-danger px-3 py-2">
                                            <i class="fas fa-exclamation-triangle me-1"></i>Perlu Tindakan
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if($item->verifikasi)
                                        <div class="small">
                                            <span class="badge {{ $item->verifikasi->status_badge }} mb-1">
                                                {{ $item->verifikasi->kondisi_label }}
                                            </span>
                                            <div class="text-muted">
                                                <small>oleh {{ $item->verifikasi->pic->name }}</small>
                                            </div>
                                        </div>
                                    @else
                                        @if($item->status == 'menunggu_pic')
                                            <span class="badge bg-secondary">Belum Diverifikasi</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex justify-content-center gap-1">
                                        <a href="{{ route('backend.pengembalian.show', $item->id) }}" 
                                           class="btn btn-sm btn-light border" 
                                           data-bs-toggle="tooltip" 
                                           title="Detail">
                                            <i class="fas fa-eye text-info"></i>
                                        </a>
                                        
                                        @if(!$item->isVerified())
                                            <a href="{{ route('backend.pengembalian.edit', $item->id) }}" 
                                               class="btn btn-sm btn-light border" 
                                               data-bs-toggle="tooltip" 
                                               title="Edit">
                                                <i class="fas fa-edit text-warning"></i>
                                            </a>
                                        @else
                                            <button class="btn btn-sm btn-light border" 
                                                    data-bs-toggle="tooltip" 
                                                    title="Tidak bisa edit (sudah diverifikasi)"
                                                    disabled>
                                                <i class="fas fa-lock text-muted"></i>
                                            </button>
                                        @endif

                                        @if(!$item->isVerified())
                                            <form action="{{ route('backend.pengembalian.destroy', $item->id) }}" 
                                                  method="POST" 
                                                  class="d-inline"
                                                  onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="btn btn-sm btn-light border" 
                                                        data-bs-toggle="tooltip" 
                                                        title="Hapus">
                                                    <i class="fas fa-trash text-danger"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <div class="empty-state">
                                        <div class="mb-3">
                                            <i class="fas fa-undo-alt fa-4x text-muted opacity-50"></i>
                                        </div>
                                        <h5 class="text-muted">Belum Ada Data</h5>
                                        <p class="text-muted mb-4">
                                            @if(request()->hasAny(['search', 'tanggal_kembali', 'status']))
                                                Data tidak ditemukan dengan filter yang dipilih
                                            @else
                                                Data pengembalian belum tersedia
                                            @endif
                                        </p>
                                        @if(!request()->hasAny(['search', 'tanggal_kembali', 'status']))
                                            <a href="{{ route('backend.pengembalian.create') }}" class="btn btn-primary">
                                                <i class="fas fa-plus me-2"></i>Tambah Pengembalian Pertama
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination Section -->
            @if($pengembalian->hasPages())
                <div class="d-flex justify-content-between align-items-center p-4 border-top bg-light">
                    <div class="text-muted small">
                        Menampilkan <strong>{{ $pengembalian->firstItem() ?? 0 }}</strong> - 
                        <strong>{{ $pengembalian->lastItem() ?? 0 }}</strong> dari 
                        <strong>{{ $pengembalian->total() }}</strong> data
                    </div>
                    <div>
                        {{ $pengembalian->appends(request()->query())->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
.btn-light.border:hover {
    background-color: #f8f9fa;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    transition: all 0.2s;
}

.table tbody tr:hover {
    background-color: #f8f9fa;
}

.empty-state {
    padding: 40px 20px;
}

.badge {
    font-weight: 500;
    font-size: 0.813rem;
}

/* Modern scrollbar */
.table-responsive::-webkit-scrollbar {
    height: 8px;
}

.table-responsive::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

.table-responsive::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 10px;
}

.table-responsive::-webkit-scrollbar-thumb:hover {
    background: #555;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>
@endsection