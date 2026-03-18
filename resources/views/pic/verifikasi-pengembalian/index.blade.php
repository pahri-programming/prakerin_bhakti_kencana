@extends('layouts.backend')
@section('title', 'Verifikasi Pengembalian Barang')

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">Verifikasi Pengembalian Barang</h2>
            <p class="text-muted mb-0">Verifikasi kondisi detail barang yang bermasalah</p>
        </div>
    </div>

    <!-- Info Alert -->
    <div class="alert alert-info mb-4">
        <div class="d-flex align-items-start">
            <i class="fas fa-info-circle me-3 mt-1 fs-4"></i>
            <div>
                <h6 class="fw-bold mb-2">Tugas PIC:</h6>
                <ul class="mb-0">
                    <li>Admin sudah mencatat barang dengan status <span class="badge bg-warning">Bermasalah</span></li>
                    <li>PIC verifikasi kondisi detail: <strong>Rusak Ringan / Rusak Berat / Hilang</strong></li>
                    <li>Upload foto bukti (max 6 foto) dan beri catatan</li>
                    <li>Laporan otomatis dikirim ke admin untuk tindak lanjut</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-body p-4">
            <form action="{{ route('pic.verifikasi-pengembalian.index') }}" method="GET">
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-text bg-white">
                                <i class="fas fa-search text-muted"></i>
                            </span>
                            <input type="text" name="search" class="form-control" 
                                   placeholder="Cari kode peminjaman atau peminjam..."
                                   value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select name="status_verifikasi" class="form-select">
                            <option value="">Semua Status</option>
                            <option value="belum_verifikasi" {{ request('status_verifikasi') == 'belum_verifikasi' ? 'selected' : '' }}>
                                Belum Diverifikasi
                            </option>
                            <option value="pending" {{ request('status_verifikasi') == 'pending' ? 'selected' : '' }}>
                                Pending
                            </option>
                            <option value="diterima" {{ request('status_verifikasi') == 'diterima' ? 'selected' : '' }}>
                                Diterima
                            </option>
                            <option value="perlu_tindakan" {{ request('status_verifikasi') == 'perlu_tindakan' ? 'selected' : '' }}>
                                Perlu Tindakan
                            </option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-filter me-1"></i>Filter
                        </button>
                    </div>
                    <div class="col-md-3">
                        @if(request()->hasAny(['search', 'status_verifikasi']))
                            <a href="{{ route('pic.verifikasi-pengembalian.index') }}" class="btn btn-outline-secondary w-100">
                                <i class="fas fa-times me-1"></i>Reset
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Table Card -->
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="px-4 py-3" width="5%">No</th>
                            <th class="py-3" width="12%">Kode Peminjaman</th>
                            <th class="py-3" width="15%">Peminjam</th>
                            <th class="py-3" width="12%">Tanggal Kembali</th>
                            <th class="py-3" width="15%">Barang Bermasalah</th>
                            <th class="py-3" width="13%">Status Verifikasi</th>
                            <th class="py-3 text-center" width="15%">Aksi</th>
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
                                        {{ $item->peminjamanBarang->kode }}
                                    </span>
                                </td>
                                <td>
                                    <div class="fw-semibold">
                                        {{ $item->peminjamanBarang->nama_peminjam ?? $item->peminjamanBarang->user->name }}
                                    </div>
                                    @if($item->peminjamanBarang->instansi)
                                        <small class="text-muted">
                                            <i class="fas fa-building me-1"></i>{{ Str::limit($item->peminjamanBarang->instansi, 20) }}
                                        </small>
                                    @endif
                                </td>
                                <td>
                                    <i class="far fa-calendar me-1 text-primary"></i>
                                    {{ \Carbon\Carbon::parse($item->tanggal_kembali)->format('d M Y') }}
                                </td>
                                <td>
                                    @php
                                        $bermasalah = $item->detailpengembalians->where('status_awal', 'bermasalah');
                                    @endphp
                                    @if($bermasalah->count() > 0)
                                        @foreach($bermasalah->take(2) as $detail)
                                            <div class="mb-1">
                                                <span class="badge bg-warning bg-opacity-10 text-warning">
                                                    <i class="fas fa-exclamation-triangle me-1"></i>{{ $detail->barang->nama }}
                                                </span>
                                                <small class="text-muted">({{ $detail->jumlah }})</small>
                                            </div>
                                        @endforeach
                                        @if($bermasalah->count() > 2)
                                            <small class="text-muted">+{{ $bermasalah->count() - 2 }} lainnya</small>
                                        @endif
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($item->verifikasi)
                                        <span class="badge {{ $item->verifikasi->status_badge }} px-3 py-2">
                                            {{ $item->verifikasi->status_label }}
                                        </span>
                                        <div class="text-muted small mt-1">
                                            {{ $item->verifikasi->kondisi_label }}
                                        </div>
                                    @else
                                        <span class="badge bg-secondary px-3 py-2">
                                            <i class="fas fa-clock me-1"></i>Belum Diverifikasi
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if(!$item->isVerified())
                                        <a href="{{ route('pic.verifikasi-pengembalian.create', $item->id) }}" 
                                           class="btn btn-sm btn-primary">
                                            <i class="fas fa-check-circle me-1"></i>Verifikasi
                                        </a>
                                    @else
                                        <a href="{{ route('pic.verifikasi-pengembalian.show', $item->id) }}" 
                                           class="btn btn-sm btn-info">
                                            <i class="fas fa-eye me-1"></i>Detail
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <div class="empty-state">
                                        <i class="fas fa-clipboard-check fa-4x text-muted opacity-50 mb-3"></i>
                                        <h5 class="text-muted">Tidak Ada Pengembalian yang Perlu Diverifikasi</h5>
                                        <p class="text-muted">
                                            @if(request()->hasAny(['search', 'status_verifikasi']))
                                                Tidak ditemukan dengan filter yang dipilih
                                            @else
                                                Semua pengembalian sudah diverifikasi atau tidak ada yang bermasalah
                                            @endif
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
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
.table tbody tr:hover {
    background-color: #f8f9fa;
}

.empty-state {
    padding: 60px 20px;
}

.badge {
    font-weight: 500;
    font-size: 0.813rem;
}
</style>
@endsection