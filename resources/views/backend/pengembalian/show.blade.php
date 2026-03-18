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
                        @if($pengembalian->status == 'menunggu_pic')
                            <span class="badge bg-info px-5 py-3 fs-4">
                                <i class="fas fa-clock me-2"></i>Menunggu Verifikasi PIC
                            </span>
                        @elseif($pengembalian->status == 'dikembalikan')
                            <span class="badge bg-success px-5 py-3 fs-4">
                                <i class="fas fa-check-circle me-2"></i>Dikembalikan
                            </span>
                        @else
                            <span class="badge bg-danger px-5 py-3 fs-4">
                                <i class="fas fa-exclamation-triangle me-2"></i>Perlu Tindakan Admin
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

                    <!-- Status Awal Barang (Cek Admin) -->
                    <h5 class="fw-bold mb-3">
                        <i class="fas fa-clipboard-check me-2 text-primary"></i>Status Awal Barang (Cek Admin)
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
                                        <div class="col-md-5">
                                            <h6 class="fw-bold mb-1">{{ $detail->barang->nama ?? '-' }}</h6>
                                            <small class="text-muted">{{ $detail->jumlah }} Unit</small>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="text-muted small mb-1">Status Awal (Cek Admin)</label>
                                            <div>
                                                @if($detail->status_awal == 'baik')
                                                    <span class="badge bg-success px-4 py-2 fs-6">
                                                        <i class="fas fa-check-circle me-1"></i>Baik
                                                    </span>
                                                    <div class="text-muted small mt-1">Tidak ada masalah saat diterima</div>
                                                @else
                                                    <span class="badge bg-warning px-4 py-2 fs-6">
                                                        <i class="fas fa-exclamation-triangle me-1"></i>Bermasalah
                                                    </span>
                                                    <div class="text-warning small mt-1">Ada masalah, perlu verifikasi PIC</div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Summary Status Awal -->
                    <h6 class="fw-semibold mb-3">Summary Status Awal:</h6>
                    <div class="row g-3 mb-4">
                        @php
                            $baik = $pengembalian->detailpengembalians->where('status_awal', 'baik')->count();
                            $bermasalah = $pengembalian->detailpengembalians->where('status_awal', 'bermasalah')->count();
                            $total = $pengembalian->detailpengembalians->count();
                        @endphp
                        
                        <div class="col-md-4">
                            <div class="card border-success">
                                <div class="card-body text-center">
                                    <h3 class="text-success mb-0">{{ $baik }}</h3>
                                    <small class="text-muted">Status Awal: Baik</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border-warning">
                                <div class="card-body text-center">
                                    <h3 class="text-warning mb-0">{{ $bermasalah }}</h3>
                                    <small class="text-muted">Status Awal: Bermasalah</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border-primary">
                                <div class="card-body text-center">
                                    <h3 class="text-primary mb-0">{{ $total }}</h3>
                                    <small class="text-muted">Total Item</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Verifikasi PIC -->
                    @if($pengembalian->verifikasi)
                        <hr class="my-4">
                        <h5 class="fw-bold mb-3">
                            <i class="fas fa-user-check me-2 text-success"></i>Verifikasi PIC
                        </h5>
                        <div class="card border-success">
                            <div class="card-body">
                                <div class="row g-3 mb-3">
                                    <div class="col-md-3">
                                        <label class="text-muted small mb-1">Verifikator (PIC)</label>
                                        <div class="fw-semibold">{{ $pengembalian->verifikasi->pic->name }}</div>
                                        <small class="text-muted">{{ $pengembalian->verifikasi->pic->email }}</small>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="text-muted small mb-1">Tanggal Verifikasi</label>
                                        <div class="fw-semibold">
                                            {{ $pengembalian->verifikasi->tanggal_verifikasi_format }}
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="text-muted small mb-1">Kondisi Detail</label>
                                        <div>
                                            @if($pengembalian->verifikasi->kondisi == 'baik')
                                                <span class="badge bg-success px-3 py-2">
                                                    <i class="fas fa-check-circle me-1"></i>Baik
                                                </span>
                                            @elseif($pengembalian->verifikasi->kondisi == 'rusak_ringan')
                                                <span class="badge bg-warning px-3 py-2">
                                                    <i class="fas fa-exclamation-triangle me-1"></i>Rusak Ringan
                                                </span>
                                            @elseif($pengembalian->verifikasi->kondisi == 'rusak_berat')
                                                <span class="badge bg-danger px-3 py-2">
                                                    <i class="fas fa-times-circle me-1"></i>Rusak Berat
                                                </span>
                                            @else
                                                <span class="badge bg-dark px-3 py-2">
                                                    <i class="fas fa-question-circle me-1"></i>Hilang
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="text-muted small mb-1">Status Verifikasi</label>
                                        <div>
                                            @if($pengembalian->verifikasi->status_verifikasi == 'diterima')
                                                <span class="badge bg-success px-3 py-2">
                                                    <i class="fas fa-check-circle me-1"></i>Diterima
                                                </span>
                                            @elseif($pengembalian->verifikasi->status_verifikasi == 'pending')
                                                <span class="badge bg-warning px-3 py-2">
                                                    <i class="fas fa-clock me-1"></i>Pending
                                                </span>
                                            @else
                                                <span class="badge bg-danger px-3 py-2">
                                                    <i class="fas fa-exclamation-triangle me-1"></i>Perlu Tindakan
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                @if($pengembalian->verifikasi->catatan_pic)
                                    <div class="mb-3">
                                        <label class="text-muted small mb-1">Catatan PIC</label>
                                        <div class="p-3 bg-light rounded">
                                            {{ $pengembalian->verifikasi->catatan_pic }}
                                        </div>
                                    </div>
                                @endif

                                @if($pengembalian->verifikasi->foto_bukti && count($pengembalian->verifikasi->foto_bukti) > 0)
                                    <div class="mb-3">
                                        <label class="text-muted small mb-2">Foto Bukti ({{ count($pengembalian->verifikasi->foto_bukti) }} foto)</label>
                                        <div class="row g-2">
                                            @foreach($pengembalian->verifikasi->foto_bukti as $index => $foto)
                                                <div class="col-md-2">
                                                    <img src="{{ asset('storage/' . $foto) }}" 
                                                         class="img-thumbnail foto-bukti" 
                                                         alt="Bukti {{ $index + 1 }}" 
                                                         style="height: 120px; object-fit: cover; width: 100%; cursor: pointer;"
                                                         onclick="openModal('{{ asset('storage/' . $foto) }}', {{ $index + 1 }})">
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                @if($pengembalian->verifikasi->tindakan_admin)
                                    <div class="alert alert-info mb-0">
                                        <strong><i class="fas fa-user-shield me-2"></i>Tindakan Admin:</strong><br>
                                        {{ $pengembalian->verifikasi->tindakan_admin }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    @else
                        @if($pengembalian->status == 'menunggu_pic')
                            <hr class="my-4">
                            <div class="alert alert-info">
                                <i class="fas fa-clock me-2"></i>
                                <strong>Menunggu Verifikasi PIC</strong><br>
                                Barang dengan status "Bermasalah" sedang menunggu verifikasi detail dari PIC.
                            </div>
                        @endif
                    @endif

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
                        
                        @if(!$pengembalian->isVerified())
                            <div class="d-flex gap-2">
                                <a href="{{ route('backend.pengembalian.edit', $pengembalian->id) }}" 
                                   class="btn btn-warning">
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
                        @else
                            <div class="alert alert-warning mb-0 py-2 px-3">
                                <i class="fas fa-lock me-2"></i>Data sudah diverifikasi PIC dan tidak dapat diubah.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Lightbox -->
<div class="modal fade" id="imageModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content bg-dark">
            <div class="modal-header border-0">
                <h5 class="modal-title text-white" id="modalTitle">Foto Bukti</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0 text-center">
                <img id="modalImage" src="" class="img-fluid" alt="Foto Bukti" style="max-height: 70vh;">
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

.foto-bukti:hover {
    transform: scale(1.05);
    transition: transform 0.2s;
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}

.modal-content {
    border-radius: 12px;
    overflow: hidden;
}

.modal-body img {
    border-radius: 0 0 12px 12px;
}
</style>

<script>
function openModal(imageUrl, fotoNumber) {
    document.getElementById('modalImage').src = imageUrl;
    document.getElementById('modalTitle').textContent = 'Foto Bukti ' + fotoNumber;
    var modal = new bootstrap.Modal(document.getElementById('imageModal'));
    modal.show();
}

document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips if any
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>
@endsection