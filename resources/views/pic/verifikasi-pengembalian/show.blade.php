@extends('layouts.backend')
@section('title', 'Detail Verifikasi Pengembalian')

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">Detail Verifikasi Pengembalian</h2>
            <p class="text-muted mb-0">Hasil verifikasi kondisi barang</p>
        </div>
        <a href="{{ route('pic.verifikasi-pengembalian.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i>Kembali
        </a>
    </div>

    <!-- Status Badge -->
    @if($pengembalian->verifikasi)
    <div class="alert alert-{{ $pengembalian->verifikasi->status_verifikasi == 'diterima' ? 'success' : ($pengembalian->verifikasi->status_verifikasi == 'pending' ? 'warning' : 'danger') }} mb-4">
        <div class="d-flex align-items-center">
            <i class="fas fa-{{ $pengembalian->verifikasi->status_verifikasi == 'diterima' ? 'check-circle' : ($pengembalian->verifikasi->status_verifikasi == 'pending' ? 'clock' : 'exclamation-triangle') }} fa-2x me-3"></i>
            <div>
                <h5 class="mb-1">
                    Status: 
                    @if($pengembalian->verifikasi->status_verifikasi == 'diterima')
                        Diterima
                    @elseif($pengembalian->verifikasi->status_verifikasi == 'pending')
                        Menunggu Tindakan Admin
                    @else
                        Perlu Tindakan Lanjut
                    @endif
                </h5>
                <p class="mb-0">
                    Kondisi: <strong>
                        @if($pengembalian->verifikasi->kondisi == 'baik')
                            ✅ Baik
                        @elseif($pengembalian->verifikasi->kondisi == 'rusak_ringan')
                            ⚠️ Rusak Ringan
                        @elseif($pengembalian->verifikasi->kondisi == 'rusak_berat')
                            🔴 Rusak Berat
                        @else
                            ❌ Hilang
                        @endif
                    </strong>
                </p>
            </div>
        </div>
    </div>
    @endif

    <!-- Info Pengembalian -->
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-header bg-white border-bottom py-3">
            <h5 class="mb-0 fw-bold">
                <i class="fas fa-info-circle text-primary me-2"></i>
                Informasi Pengembalian
            </h5>
        </div>
        <div class="card-body p-4">
            <div class="row g-4">
                <div class="col-md-6">
                    <h6 class="text-muted mb-3">Data Peminjaman</h6>
                    <table class="table table-sm table-borderless">
                        <tr>
                            <td width="40%" class="text-muted">Kode Peminjaman</td>
                            <td width="5%">:</td>
                            <td>
                                <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2">
                                    {{ $pengembalian->peminjamanBarang->kode }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Nama Peminjam</td>
                            <td>:</td>
                            <td class="fw-semibold">
                                {{ $pengembalian->peminjamanBarang->nama_peminjam ?? $pengembalian->peminjamanBarang->user->name }}
                            </td>
                        </tr>
                        @if($pengembalian->peminjamanBarang->instansi)
                        <tr>
                            <td class="text-muted">Instansi</td>
                            <td>:</td>
                            <td>{{ $pengembalian->peminjamanBarang->instansi }}</td>
                        </tr>
                        @endif
                        <tr>
                            <td class="text-muted">Tanggal Pinjam</td>
                            <td>:</td>
                            <td>{{ \Carbon\Carbon::parse($pengembalian->peminjamanBarang->tanggal_pinjam)->format('d M Y') }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Tanggal Kembali</td>
                            <td>:</td>
                            <td>{{ \Carbon\Carbon::parse($pengembalian->tanggal_kembali)->format('d M Y') }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <h6 class="text-muted mb-3">Status Pengembalian</h6>
                    <table class="table table-sm table-borderless">
                        <tr>
                            <td width="40%" class="text-muted">Status</td>
                            <td width="5%">:</td>
                            <td>
                                @if($pengembalian->status == 'menunggu_pic')
                                    <span class="badge bg-info px-3 py-2">
                                        <i class="fas fa-clock me-1"></i>Menunggu PIC
                                    </span>
                                @elseif($pengembalian->status == 'dikembalikan')
                                    <span class="badge bg-success px-3 py-2">
                                        <i class="fas fa-check-circle me-1"></i>Dikembalikan
                                    </span>
                                @else
                                    <span class="badge bg-danger px-3 py-2">
                                        <i class="fas fa-exclamation-triangle me-1"></i>Perlu Tindakan
                                    </span>
                                @endif
                            </td>
                        </tr>
                        @if($pengembalian->verifikasi)
                        <tr>
                            <td class="text-muted">Diverifikasi Oleh</td>
                            <td>:</td>
                            <td class="fw-semibold">{{ $pengembalian->verifikasi->pic->name }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Tanggal Verifikasi</td>
                            <td>:</td>
                            <td>{{ \Carbon\Carbon::parse($pengembalian->verifikasi->tanggal_verifikasi)->translatedFormat('d F Y, H:i') }} WIB</td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Barang yang Dikembalikan -->
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-header bg-white border-bottom py-3">
            <h5 class="mb-0 fw-bold">
                <i class="fas fa-box text-primary me-2"></i>
                Daftar Barang yang Dikembalikan
            </h5>
        </div>
        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table table-bordered align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th width="5%">No</th>
                            <th width="45%">Nama Barang</th>
                            <th width="15%" class="text-center">Jumlah</th>
                            <th width="20%" class="text-center">Status Awal (Admin)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pengembalian->detailpengembalians as $index => $detail)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td class="fw-semibold">{{ $detail->barang->nama }}</td>
                            <td class="text-center">
                                <span class="badge bg-secondary bg-opacity-10 text-secondary px-3 py-2">
                                    {{ $detail->jumlah }} unit
                                </span>
                            </td>
                            <td class="text-center">
                                @if($detail->status_awal === 'baik')
                                    <span class="badge bg-success px-3 py-2">
                                        <i class="fas fa-check-circle me-1"></i>Baik
                                    </span>
                                @else
                                    <span class="badge bg-warning px-3 py-2">
                                        <i class="fas fa-exclamation-triangle me-1"></i>Bermasalah
                                    </span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Hasil Verifikasi PIC -->
    @if($pengembalian->verifikasi)
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-header bg-primary text-white py-3">
            <h5 class="mb-0 fw-bold">
                <i class="fas fa-clipboard-check me-2"></i>
                Hasil Verifikasi PIC
            </h5>
        </div>
        <div class="card-body p-4">
            <!-- Kondisi -->
            <div class="mb-4">
                <h6 class="text-muted mb-2">Kondisi Detail</h6>
                @if($pengembalian->verifikasi->kondisi == 'baik')
                    <span class="badge bg-success px-4 py-3 fs-6">
                        <i class="fas fa-check-circle me-1"></i>Baik
                    </span>
                @elseif($pengembalian->verifikasi->kondisi == 'rusak_ringan')
                    <span class="badge bg-warning px-4 py-3 fs-6">
                        <i class="fas fa-exclamation-triangle me-1"></i>Rusak Ringan
                    </span>
                @elseif($pengembalian->verifikasi->kondisi == 'rusak_berat')
                    <span class="badge bg-danger px-4 py-3 fs-6">
                        <i class="fas fa-times-circle me-1"></i>Rusak Berat
                    </span>
                @else
                    <span class="badge bg-dark px-4 py-3 fs-6">
                        <i class="fas fa-ban me-1"></i>Hilang
                    </span>
                @endif
            </div>

            <!-- Catatan PIC -->
            @if($pengembalian->verifikasi->catatan_pic)
            <div class="mb-4">
                <h6 class="text-muted mb-2">Catatan PIC</h6>
                <div class="bg-light rounded-3 p-3">
                    <p class="mb-0" style="white-space: pre-line;">{{ $pengembalian->verifikasi->catatan_pic }}</p>
                </div>
            </div>
            @endif

            <!-- Foto Bukti -->
            @if($pengembalian->verifikasi->foto_bukti && count($pengembalian->verifikasi->foto_bukti) > 0)
            <div class="mb-4">
                <h6 class="text-muted mb-3">
                    <i class="fas fa-images me-1"></i>Foto Bukti 
                    <span class="badge bg-primary">{{ count($pengembalian->verifikasi->foto_bukti) }} foto</span>
                </h6>
                <div class="row g-3">
                    @foreach($pengembalian->verifikasi->foto_bukti as $index => $foto)
                    <div class="col-6 col-md-4 col-lg-2">
                        <div class="position-relative">
                            <img src="{{ asset('storage/' . $foto) }}" 
                                 class="img-thumbnail rounded-3 foto-bukti" 
                                 alt="Foto {{ $index + 1 }}"
                                 data-index="{{ $index }}"
                                 style="width: 100%; height: 150px; object-fit: cover; cursor: pointer;">
                            <div class="position-absolute top-0 start-0 m-2">
                                <span class="badge bg-dark bg-opacity-75">Foto {{ $index + 1 }}</span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                <p class="text-muted small mt-3 mb-0">
                    <i class="fas fa-info-circle me-1"></i>Klik foto untuk memperbesar
                </p>
            </div>
            @endif

            <!-- Status Verifikasi -->
            <div class="mb-4">
                <h6 class="text-muted mb-2">Status Verifikasi</h6>
                @if($pengembalian->verifikasi->status_verifikasi == 'diterima')
                    <span class="badge bg-success px-4 py-3 fs-6">
                        <i class="fas fa-check-circle me-1"></i>Diterima
                    </span>
                @elseif($pengembalian->verifikasi->status_verifikasi == 'pending')
                    <span class="badge bg-warning px-4 py-3 fs-6">
                        <i class="fas fa-clock me-1"></i>Menunggu Tindakan Admin
                    </span>
                @else
                    <span class="badge bg-danger px-4 py-3 fs-6">
                        <i class="fas fa-exclamation-triangle me-1"></i>Perlu Tindakan Lanjut
                    </span>
                @endif
            </div>

            <!-- Tindakan Admin -->
            @if($pengembalian->verifikasi->tindakan_admin)
            <div class="alert alert-warning mb-0">
                <h6 class="fw-bold mb-2">
                    <i class="fas fa-user-shield me-1"></i>Tindakan Admin
                </h6>
                <p class="mb-0" style="white-space: pre-line;">{{ $pengembalian->verifikasi->tindakan_admin }}</p>
            </div>
            @else
            <div class="alert alert-info mb-0">
                <i class="fas fa-info-circle me-2"></i>
                Laporan telah dikirim ke admin dan menunggu tindak lanjut.
            </div>
            @endif
        </div>
    </div>
    @endif

    <!-- Action Buttons -->
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body p-4 text-center">
            <a href="{{ route('pic.verifikasi-pengembalian.index') }}" class="btn btn-primary px-4">
                <i class="fas fa-arrow-left me-1"></i>Kembali ke Daftar Verifikasi
            </a>
        </div>
    </div>
</div>

<!-- Modal Lightbox -->
<div class="modal fade" id="fotoModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content bg-dark">
            <div class="modal-header border-0">
                <h5 class="modal-title text-white">
                    <span id="fotoTitle">Foto Bukti</span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center p-0">
                <img id="fotoImage" src="" class="img-fluid" alt="Foto Bukti" style="max-height: 70vh;">
            </div>
            <div class="modal-footer border-0 justify-content-between">
                <button type="button" class="btn btn-secondary" id="btnPrev">
                    <i class="fas fa-chevron-left"></i> Sebelumnya
                </button>
                <span class="text-white" id="fotoCounter"></span>
                <button type="button" class="btn btn-secondary" id="btnNext">
                    Selanjutnya <i class="fas fa-chevron-right"></i>
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Get all foto bukti
    const allFotos = @json(collect($pengembalian->verifikasi->foto_bukti ?? [])->map(function($foto) {
        return asset('storage/' . $foto);
    })->values());
    
    let currentIndex = 0;
    const modal = new bootstrap.Modal(document.getElementById('fotoModal'));
    
    // Click event on images
    document.querySelectorAll('.foto-bukti').forEach(img => {
        img.addEventListener('click', function() {
            currentIndex = parseInt(this.dataset.index);
            showFoto(currentIndex);
            modal.show();
        });
    });
    
    // Previous button
    document.getElementById('btnPrev').addEventListener('click', function() {
        currentIndex = (currentIndex - 1 + allFotos.length) % allFotos.length;
        showFoto(currentIndex);
    });
    
    // Next button
    document.getElementById('btnNext').addEventListener('click', function() {
        currentIndex = (currentIndex + 1) % allFotos.length;
        showFoto(currentIndex);
    });
    
    // Keyboard navigation
    document.addEventListener('keydown', function(e) {
        if (document.getElementById('fotoModal').classList.contains('show')) {
            if (e.key === 'ArrowLeft') {
                document.getElementById('btnPrev').click();
            } else if (e.key === 'ArrowRight') {
                document.getElementById('btnNext').click();
            } else if (e.key === 'Escape') {
                modal.hide();
            }
        }
    });
    
    function showFoto(index) {
        document.getElementById('fotoImage').src = allFotos[index];
        document.getElementById('fotoTitle').textContent = `Foto Bukti ${index + 1}`;
        document.getElementById('fotoCounter').textContent = `${index + 1} dari ${allFotos.length}`;
        
        // Update button states
        document.getElementById('btnPrev').disabled = (allFotos.length === 1);
        document.getElementById('btnNext').disabled = (allFotos.length === 1);
    }
});
</script>
@endpush

<style>
.table tbody tr:hover {
    background-color: #f8f9fa;
}

.foto-bukti {
    transition: all 0.3s ease;
}

.foto-bukti:hover {
    transform: scale(1.05);
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
}

.modal-content {
    border-radius: 15px;
    overflow: hidden;
}

.modal-body img {
    border-radius: 10px;
}
</style>
@endsection