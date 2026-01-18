@extends('layouts.backend')
@section('title', 'Detail Pengembalian Barang')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold mb-1">Detail Pengembalian Barang</h2>
                    <p class="text-muted mb-0">Informasi lengkap pengembalian barang</p>
                </div>
    
            </div>

            <!-- Main Card -->
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-4">
                    <!-- Status Badge -->
                    <div class="text-center mb-4 pb-4 border-bottom">
                        <h5 class="text-muted mb-2">Status Pengembalian</h5>
                        @if($pengembalian->status == 'dikembalikan')
                            <span class="badge bg-success px-5 py-3 fs-4">
                                <i class="fas fa-check-circle me-2"></i>Dikembalikan
                            </span>
                        @else
                            <span class="badge bg-warning px-5 py-3 fs-4">
                                <i class="fas fa-clock me-2"></i>Belum Dikembalikan
                            </span>
                        @endif
                    </div>

                    <!-- Informasi Peminjaman -->
                    <h5 class="fw-bold mb-3">
                        <i class="fas fa-clipboard-list me-2 text-primary"></i>Informasi Peminjaman
                    </h5>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded">
                                <label class="text-muted small mb-1">Kode Peminjaman</label>
                                <div class="fw-bold text-primary fs-5">{{ $pengembalian->peminjamanBarang->kode }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded">
                                <label class="text-muted small mb-1">Status Peminjaman</label>
                                <div class="fw-bold">
                                    @if($pengembalian->peminjamanBarang->status == 'disetujui')
                                        <span class="badge bg-info px-3 py-2">Disetujui</span>
                                    @elseif($pengembalian->peminjamanBarang->status == 'dikembalikan')
                                        <span class="badge bg-success px-3 py-2">Dikembalikan</span>
                                    @else
                                        <span class="badge bg-warning px-3 py-2">{{ ucfirst($pengembalian->peminjamanBarang->status) }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <!-- Informasi Peminjam -->
                    <h5 class="fw-bold mb-3">
                        <i class="fas fa-user-circle me-2 text-primary"></i>Informasi Peminjam
                    </h5>
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <div class="p-3 bg-light rounded">
                                <label class="text-muted small mb-1">Nama User</label>
                                <div class="fw-semibold">{{ $pengembalian->peminjamanBarang->user->name }}</div>
                                <small class="text-muted">{{ $pengembalian->peminjamanBarang->user->email }}</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 bg-light rounded">
                                <label class="text-muted small mb-1">Nama Peminjam</label>
                                <div class="fw-semibold">
                                    {{ $pengembalian->peminjamanBarang->nama_peminjam ?? $pengembalian->peminjamanBarang->user->name }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 bg-light rounded">
                                <label class="text-muted small mb-1">Instansi</label>
                                <div class="fw-semibold">
                                    {{ $pengembalian->peminjamanBarang->instansi ?? '-' }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <!-- Informasi Tanggal -->
                    <h5 class="fw-bold mb-3">
                        <i class="fas fa-calendar-alt me-2 text-primary"></i>Informasi Tanggal
                    </h5>
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <div class="p-3 bg-light rounded">
                                <label class="text-muted small mb-1">Tanggal Pinjam</label>
                                <div class="fw-bold text-dark">
                                    <i class="far fa-calendar-check me-2 text-success"></i>
                                    {{ \Carbon\Carbon::parse($pengembalian->peminjamanBarang->tanggal_pinjam)->format('d F Y') }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 bg-light rounded">
                                <label class="text-muted small mb-1">Tanggal Harus Kembali</label>
                                <div class="fw-bold text-dark">
                                    <i class="far fa-calendar-times me-2 text-warning"></i>
                                    {{ \Carbon\Carbon::parse($pengembalian->peminjamanBarang->tanggal_kembali)->format('d F Y') }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 bg-light rounded">
                                <label class="text-muted small mb-1">Tanggal Dikembalikan</label>
                                <div class="fw-bold text-dark">
                                    <i class="far fa-calendar-check me-2 text-primary"></i>
                                    {{ $pengembalian->tanggal_kembali_format }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Status Keterlambatan -->
                    @php
                        $tanggalHarusKembali = \Carbon\Carbon::parse($pengembalian->peminjamanBarang->tanggal_kembali);
                        $tanggalDikembalikan = \Carbon\Carbon::parse($pengembalian->tanggal_kembali);
                        $terlambat = $tanggalDikembalikan->gt($tanggalHarusKembali);
                        $hariTerlambat = $terlambat ? $tanggalHarusKembali->diffInDays($tanggalDikembalikan) : 0;
                    @endphp
                    
                    @if($terlambat)
                        <div class="alert alert-danger mb-4">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Terlambat {{ $hariTerlambat }} hari!</strong> 
                            Barang dikembalikan melewati tanggal yang ditentukan.
                        </div>
                    @else
                        <div class="alert alert-success mb-4">
                            <i class="fas fa-check-circle me-2"></i>
                            <strong>Tepat Waktu!</strong> Barang dikembalikan sesuai jadwal.
                        </div>
                    @endif

                    <hr class="my-4">

                    <!-- Detail Barang yang Dikembalikan -->
                    <h5 class="fw-bold mb-3">
                        <i class="fas fa-box me-2 text-primary"></i>Detail Barang yang Dikembalikan
                    </h5>
                    <div class="mb-4">
                        @foreach($pengembalian->detailpengembalians as $index => $detail)
                            <div class="card mb-3 border">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-md-1 text-center">
                                            <div class="badge bg-primary rounded-circle p-3">
                                                <h5 class="mb-0">{{ $index + 1 }}</h5>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <h6 class="fw-bold mb-1">{{ $detail->barang->nama ?? '-' }}</h6>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="text-muted small mb-1">Jumlah Dikembalikan</label>
                                            <h4 class="mb-0">
                                                <span class="badge bg-primary px-3 py-2">{{ $detail->jumlah }} Unit</span>
                                            </h4>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="text-muted small mb-1">Kondisi Barang</label>
                                            <div>
                                                @if($detail->kondisi == 'baik')
                                                    <span class="badge bg-success px-4 py-2 fs-6">
                                                        <i class="fas fa-check-circle me-1"></i>Baik
                                                    </span>
                                                    <div class="text-muted small mt-1">Barang dalam kondisi baik</div>
                                                @elseif($detail->kondisi == 'rusak')
                                                    <span class="badge bg-warning px-4 py-2 fs-6">
                                                        <i class="fas fa-exclamation-triangle me-1"></i>Rusak
                                                    </span>
                                                    <div class="text-warning small mt-1">Barang memerlukan perbaikan</div>
                                                @else
                                                    <span class="badge bg-danger px-4 py-2 fs-6">
                                                        <i class="fas fa-times-circle me-1"></i>Hilang
                                                    </span>
                                                    <div class="text-danger small mt-1">Barang tidak ditemukan</div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Summary Kondisi -->
                    <div class="row g-3 mb-4">
                        @php
                            $baik = $pengembalian->detailpengembalians->where('kondisi', 'baik')->count();
                            $rusak = $pengembalian->detailpengembalians->where('kondisi', 'rusak')->count();
                            $hilang = $pengembalian->detailpengembalians->where('kondisi', 'hilang')->count();
                            $total = $pengembalian->detailpengembalians->count();
                        @endphp
                        
                        <div class="col-md-3">
                            <div class="card border-success">
                                <div class="card-body text-center">
                                    <h3 class="text-success mb-0">{{ $baik }}</h3>
                                    <small class="text-muted">Kondisi Baik</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card border-warning">
                                <div class="card-body text-center">
                                    <h3 class="text-warning mb-0">{{ $rusak }}</h3>
                                    <small class="text-muted">Kondisi Rusak</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card border-danger">
                                <div class="card-body text-center">
                                    <h3 class="text-danger mb-0">{{ $hilang }}</h3>
                                    <small class="text-muted">Barang Hilang</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card border-primary">
                                <div class="card-body text-center">
                                    <h3 class="text-primary mb-0">{{ $total }}</h3>
                                    <small class="text-muted">Total Item</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Keterangan -->
                    @if($pengembalian->keterangan)
                        <hr class="my-4">
                        <h5 class="fw-bold mb-3">
                            <i class="fas fa-sticky-note me-2 text-primary"></i>Keterangan
                        </h5>
                        <div class="p-3 bg-light rounded">
                            <p class="mb-0">{{ $pengembalian->keterangan }}</p>
                        </div>
                    @endif

                    <!-- Timestamp -->
                    <hr class="my-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="small text-muted">
                                <i class="fas fa-clock me-1"></i>Dibuat: {{ $pengembalian->created_at->translatedFormat('d F Y H:i') }}
                            </div>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <div class="small text-muted">
                                <i class="fas fa-edit me-1"></i>Terakhir diupdate: {{ $pengembalian->updated_at->translatedFormat('d F Y H:i') }}
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <hr class="my-4">
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('backend.pengembalian.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Kembali ke List
                        </a>
                        <div class="d-flex gap-2">
                            <a href="{{ route('backend.pengembalian.edit', $pengembalian->id) }}" class="btn btn-warning">
                                <i class="fas fa-edit me-2"></i>Edit Data
                            </a>
                            <form action="{{ route('backend.pengembalian.destroy', $pengembalian->id) }}" 
                                  method="POST" 
                                  class="d-inline"
                                  onsubmit="return confirm('Yakin ingin menghapus pengembalian ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">
                                    <i class="fas fa-trash me-2"></i>Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.badge.rounded-circle {
    width: 50px;
    height: 50px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.card {
    transition: all 0.3s ease;
}

.card:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}
</style>
@endsection