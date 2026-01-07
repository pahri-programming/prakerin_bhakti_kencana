@extends('layouts.backend')
@section('title', 'Data Barang Ruangan')

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">Barang Ruangan</h2>
            <p class="text-muted mb-0">Kelola data barang di setiap ruangan</p>
        </div>
        <a href="{{ route('backend.barangruangan.create') }}" class="btn btn-primary px-4">
            <i class="fas fa-plus me-2"></i>Tambah Data
        </a>
    </div>

    <!-- Alert Success -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Main Card -->
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body p-0">
            <!-- Search & Filter Section -->
            <div class="p-4 border-bottom bg-light">
                <form action="{{ route('backend.barangruangan.index') }}" method="GET">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0">
                                    <i class="fas fa-search text-muted"></i>
                                </span>
                                <input type="text" 
                                       name="search" 
                                       class="form-control border-start-0 ps-0" 
                                       placeholder="Cari barang atau ruangan..."
                                       value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <select name="status" class="form-select">
                                <option value="">Semua Status</option>
                                <option value="tersedia" {{ request('status') == 'tersedia' ? 'selected' : '' }}>Tersedia</option>
                                <option value="sedang dipinjam" {{ request('status') == 'sedang dipinjam' ? 'selected' : '' }}>Sedang Dipinjam</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select name="ruangan_id" class="form-select">
                                <option value="">Semua Ruangan</option>
                                @foreach($ruangans as $ruangan)
                                    <option value="{{ $ruangan->id }}" {{ request('ruangan_id') == $ruangan->id ? 'selected' : '' }}>
                                        {{ $ruangan->nama_ruangan }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-1">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-filter"></i>
                            </button>
                        </div>
                    </div>
                </form>
                
                @if(request()->hasAny(['search', 'status', 'ruangan_id']))
                    <div class="mt-3">
                        <a href="{{ route('backend.barangruangan.index') }}" class="btn btn-sm btn-outline-secondary">
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
                            <th class="py-3 text-muted fw-semibold" width="20%">Ruangan</th>
                            <th class="py-3 text-muted fw-semibold" width="25%">Barang</th>
                            <th class="py-3 text-muted fw-semibold" width="10%">Jumlah</th>
                            <th class="py-3 text-muted fw-semibold" width="13%">Status</th>
                            <th class="py-3 text-muted fw-semibold" width="15%">Update Terakhir</th>
                            <th class="py-3 text-muted fw-semibold text-center" width="12%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($barangRuangans as $index => $item)
                            <tr class="border-bottom">
                                <td class="px-4">
                                    <span class="text-muted">{{ $barangRuangans->firstItem() + $index }}</span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm bg-primary bg-opacity-10 rounded-2 d-flex align-items-center justify-content-center me-3">
                                            <i class="fas fa-door-open text-primary"></i>
                                        </div>
                                        <div>
                                            <div class="fw-semibold">{{ $item->ruangan->nama_ruangan ?? '-' }}</div>
                                            <small class="text-muted">{{ $item->ruangan->lokasi ?? 'Lokasi tidak tersedia' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm bg-success bg-opacity-10 rounded-2 d-flex align-items-center justify-content-center me-3">
                                            <i class="fas fa-box text-success"></i>
                                        </div>
                                        <div>
                                            <div class="fw-semibold">{{ $item->barang->nama ?? '-' }}</div>
                                            <small class="text-muted">{{ $item->barang->kode ?? '-' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-secondary bg-opacity-10 text-dark px-3 py-2">
                                        <i class="fas fa-layer-group me-1"></i>{{ $item->qty }}
                                    </span>
                                </td>
                                <td>
                                    @if($item->status == 'tersedia')
                                        <span class="badge bg-success-subtle text-success px-3 py-2 rounded-pill">
                                            <i class="fas fa-check-circle me-1"></i>Tersedia
                                        </span>
                                    @else
                                        <span class="badge bg-warning-subtle text-warning px-3 py-2 rounded-pill">
                                            <i class="fas fa-clock me-1"></i>Dipinjam
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <div class="text-muted small">
                                        <i class="far fa-calendar-alt me-1"></i>{{ $item->updated_at->format('d M Y') }}
                                        <br>
                                        <i class="far fa-clock me-1"></i>{{ $item->updated_at->format('H:i') }}
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex justify-content-center gap-1">
                                        <a href="{{ route('backend.barangruangan.show', $item->id) }}" 
                                           class="btn btn-sm btn-light border" 
                                           data-bs-toggle="tooltip" 
                                           title="Detail">
                                            <i class="fas fa-eye text-info"></i>
                                        </a>
                                        <a href="{{ route('backend.barangruangan.edit', $item->id) }}" 
                                           class="btn btn-sm btn-light border" 
                                           data-bs-toggle="tooltip" 
                                           title="Edit">
                                            <i class="fas fa-edit text-warning"></i>
                                        </a>
                                        <form action="{{ route('backend.barangruangan.destroy', $item->id) }}" 
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
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <div class="empty-state">
                                        <div class="mb-3">
                                            <i class="fas fa-inbox fa-4x text-muted opacity-50"></i>
                                        </div>
                                        <h5 class="text-muted">Belum Ada Data</h5>
                                        <p class="text-muted mb-4">
                                            @if(request()->hasAny(['search', 'status', 'ruangan_id']))
                                                Data tidak ditemukan dengan filter yang dipilih
                                            @else
                                                Data barang ruangan belum tersedia
                                            @endif
                                        </p>
                                        @if(!request()->hasAny(['search', 'status', 'ruangan_id']))
                                            <a href="{{ route('backend.barangruangan.create') }}" class="btn btn-primary">
                                                <i class="fas fa-plus me-2"></i>Tambah Data Pertama
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
            @if($barangRuangans->hasPages())
                <div class="d-flex justify-content-between align-items-center p-4 border-top bg-light">
                    <div class="text-muted small">
                        Menampilkan <strong>{{ $barangRuangans->firstItem() ?? 0 }}</strong> - 
                        <strong>{{ $barangRuangans->lastItem() ?? 0 }}</strong> dari 
                        <strong>{{ $barangRuangans->total() }}</strong> data
                    </div>
                    <div>
                        {{ $barangRuangans->appends(request()->query())->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
.avatar-sm {
    width: 45px;
    height: 45px;
    font-size: 18px;
}

.table > :not(caption) > * > * {
    padding: 1rem 0.75rem;
}

.btn-light.border:hover {
    background-color: #f8f9fa;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    transition: all 0.2s;
}

.card {
    transition: all 0.3s;
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

/* Bootstrap 5.3 Subtle Colors Support */
.bg-success-subtle {
    background-color: #d1e7dd !important;
}

.text-success {
    color: #198754 !important;
}

.bg-warning-subtle {
    background-color: #fff3cd !important;
}

.text-warning {
    color: #ffc107 !important;
}
</style>

<script>
// Initialize Bootstrap tooltips
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>
@endsection