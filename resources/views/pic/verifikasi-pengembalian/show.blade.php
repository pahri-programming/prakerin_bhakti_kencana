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
                    Status: {{ $pengembalian->verifikasi->status_label }}
                </h5>
                <p class="mb-0">
                    Kondisi: <strong>{{ $pengembalian->verifikasi->kondisi_label }}</strong>
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
                            <td>{{ $pengembalian->verifikasi->tanggal_verifikasi_format }}</td>
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
                <span class="badge {{ $pengembalian->verifikasi->kondisi_badge }} px-4 py-3 fs-6">
                    {{ $pengembalian->verifikasi->kondisi_label }}
                </span>
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
                <h6 class="text-muted mb-3">Foto Bukti ({{ count($pengembalian->verifikasi->foto_bukti) }} foto)</h6>
                <div class="row g-3">
                    @foreach($pengembalian->verifikasi->foto_bukti as $index => $foto)
                    <div class="col-6 col-md-4 col-lg-2">
                        <a href="{{ asset('storage/' . $foto) }}" 
                           data-lightbox="verifikasi-{{ $pengembalian->id }}" 
                           data-title="Foto {{ $index + 1 }}">
                            <img src="{{ asset('storage/' . $foto) }}" 
                                 class="img-thumbnail rounded-3" 
                                 alt="Foto {{ $index + 1 }}"
                                 style="width: 100%; height: 150px; object-fit: cover; cursor: pointer;">
                        </a>
                    </div>
                    @endforeach
                </div>
                <p class="text-muted small mt-2 mb-0">
                    <i class="fas fa-info-circle me-1"></i>Klik foto untuk memperbesar
                </p>
            </div>
            @endif

            <!-- Status Verifikasi -->
            <div class="mb-4">
                <h6 class="text-muted mb-2">Status Verifikasi</h6>
                <span class="badge {{ $pengembalian->verifikasi->status_badge }} px-4 py-3 fs-6">
                    {{ $pengembalian->verifikasi->status_label }}
                </span>
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

<!-- Lightbox CSS -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/css/lightbox.min.css" rel="stylesheet">

<style>
.table tbody tr:hover {
    background-color: #f8f9fa;
}
</style>

<!-- Lightbox JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js"></script>
<script>
    lightbox.option({
        'resizeDuration': 200,
        'wrapAround': true,
        'albumLabel': 'Foto %1 dari %2'
    });
</script>
@endsection