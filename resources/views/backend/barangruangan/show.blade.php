@extends('layouts.backend')
@section('title', 'Detail Barang Ruangan')

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">Detail Barang Ruangan</h2>
            <p class="text-muted mb-0">Informasi lengkap barang ruangan</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('backend.barangruangan.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Kembali
            </a>
        </div>
    </div>

    <div class="row g-4">
        <!-- Main Info Card -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-primary text-white py-3">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informasi Umum</h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4">
                        <!-- Ruangan -->
                        <div class="col-md-6">
                            <div class="info-group">
                                <label class="text-muted small mb-2 d-block">
                                    <i class="fas fa-door-open me-1"></i>Ruangan
                                </label>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-lg bg-primary bg-opacity-10 rounded-3 d-flex align-items-center justify-content-center me-3">
                                        <i class="fas fa-door-open fa-2x text-primary"></i>
                                    </div>
                                    <div>
                                        <h5 class="mb-1">{{ $barangRuangan->ruangan->nama_ruangan ?? '-' }}</h5>
                                        <p class="text-muted mb-0 small">
                                            <i class="fas fa-map-marker-alt me-1"></i>
                                            {{ $barangRuangan->ruangan->lokasi ?? 'Lokasi tidak tersedia' }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Barang -->
                        <div class="col-md-6">
                            <div class="info-group">
                                <label class="text-muted small mb-2 d-block">
                                    <i class="fas fa-box me-1"></i>Barang
                                </label>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-lg bg-success bg-opacity-10 rounded-3 d-flex align-items-center justify-content-center me-3">
                                        <i class="fas fa-box fa-2x text-success"></i>
                                    </div>
                                    <div>
                                        <h5 class="mb-1">{{ $barangRuangan->barang->nama ?? '-' }}</h5>
                                        <p class="text-muted mb-0 small">
                                            <i class="fas fa-barcode me-1"></i>
                                            Kode: {{ $barangRuangan->barang->kode ?? '-' }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Jumlah -->
                        <div class="col-md-6">
                            <div class="info-group">
                                <label class="text-muted small mb-2 d-block">
                                    <i class="fas fa-layer-group me-1"></i>Jumlah/Qty
                                </label>
                                <div class="qty-display">
                                    <h2 class="mb-0 text-primary">{{ $barangRuangan->qty }}</h2>
                                    <small class="text-muted">Unit</small>
                                </div>
                            </div>
                        </div>

                        <!-- Status -->
                        <div class="col-md-6">
                            <div class="info-group">
                                <label class="text-muted small mb-2 d-block">
                                    <i class="fas fa-flag me-1"></i>Status
                                </label>
                                <div>
                                    @if($barangRuangan->status == 'tersedia')
                                        <span class="badge bg-success px-4 py-3 fs-6">
                                            <i class="fas fa-check-circle me-2"></i>Tersedia
                                        </span>
                                    @else
                                        <span class="badge bg-warning px-4 py-3 fs-6">
                                            <i class="fas fa-clock me-2"></i>Sedang Dipinjam
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- <!-- Additional Info Card -->
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-light py-3">
                    <h5 class="mb-0"><i class="fas fa-clipboard-list me-2"></i>Detail Barang</h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="detail-item">
                                <label class="text-muted small mb-1">Kategori Barang</label>
                                <p class="mb-0 fw-semibold">{{ $barangRuangan->barang->kategori ?? '-' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="detail-item">
                                <label class="text-muted small mb-1">Kondisi</label>
                                <p class="mb-0 fw-semibold">{{ $barangRuangan->barang->kondisi ?? '-' }}</p>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="detail-item">
                                <label class="text-muted small mb-1">Deskripsi</label>
                                <p class="mb-0">{{ $barangRuangan->barang->deskripsi ?? 'Tidak ada deskripsi' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div> --}}
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Timeline Card -->
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-light py-3">
                    <h5 class="mb-0"><i class="fas fa-clock me-2"></i>Timeline</h5>
                </div>
                <div class="card-body p-4">
                    <div class="timeline-item mb-4">
                        <div class="d-flex">
                            <div class="timeline-icon bg-success text-white rounded-circle me-3">
                                <i class="fas fa-plus"></i>
                            </div>
                            <div>
                                <p class="mb-1 fw-semibold">Dibuat</p>
                                <p class="text-muted small mb-0">
                                    <i class="far fa-calendar me-1"></i>
                                    {{ $barangRuangan->created_at->format('d F Y') }}
                                </p>
                                <p class="text-muted small mb-0">
                                    <i class="far fa-clock me-1"></i>
                                    {{ $barangRuangan->created_at->format('H:i') }} WIB
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="timeline-item">
                        <div class="d-flex">
                            <div class="timeline-icon bg-warning text-white rounded-circle me-3">
                                <i class="fas fa-sync"></i>
                            </div>
                            <div>
                                <p class="mb-1 fw-semibold">Update Terakhir</p>
                                <p class="text-muted small mb-0">
                                    <i class="far fa-calendar me-1"></i>
                                    {{ $barangRuangan->updated_at->format('d F Y') }}
                                </p>
                                <p class="text-muted small mb-0">
                                    <i class="far fa-clock me-1"></i>
                                    {{ $barangRuangan->updated_at->format('H:i') }} WIB
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Card -->
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-light py-3">
                    <h5 class="mb-0"><i class="fas fa-cogs me-2"></i>Aksi Cepat</h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('backend.barangruangan.update-status', $barangRuangan->id) }}" 
                          method="POST" class="mb-3">
                        @csrf
                        @method('PUT')
                        <label class="form-label small text-muted">Ubah Status</label>
                        <div class="input-group">
                            <select name="status" class="form-select" required>
                                <option value="tersedia" {{ $barangRuangan->status == 'tersedia' ? 'selected' : '' }}>
                                    Tersedia
                                </option>
                                <option value="sedang dipinjam" {{ $barangRuangan->status == 'sedang dipinjam' ? 'selected' : '' }}>
                                    Sedang Dipinjam
                                </option>
                            </select>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i>
                            </button>
                        </div>
                    </form>

                    <hr class="my-3">

                    <div class="d-grid gap-2">
                        <a href="{{ route('backend.barangruangan.edit', $barangRuangan->id) }}" 
                           class="btn btn-warning">
                            <i class="fas fa-edit me-2"></i>Edit Data
                        </a>
                        <form action="{{ route('backend.barangruangan.destroy', $barangRuangan->id) }}" 
                              method="POST" 
                              onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger w-100">
                                <i class="fas fa-trash me-2"></i>Hapus Data
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.avatar-lg {
    width: 70px;
    height: 70px;
    font-size: 24px;
}

.info-group {
    padding: 20px;
    background: #f8f9fa;
    border-radius: 10px;
    height: 100%;
}

.qty-display {
    display: flex;
    align-items: baseline;
    gap: 10px;
}

.detail-item {
    padding: 15px;
    background: #f8f9fa;
    border-radius: 8px;
}

.timeline-icon {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.card {
    transition: all 0.3s;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 16px rgba(0,0,0,0.1) !important;
}
</style>
@endsection