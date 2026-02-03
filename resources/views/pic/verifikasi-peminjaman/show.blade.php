@extends('layouts.backend')

@section('title', 'Detail Verifikasi Peminjaman')

@push('styles')
<style>
    .detail-card {
        background: white;
        border-radius: 15px;
        padding: 2rem;
        box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        margin-bottom: 2rem;
    }

    .status-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 15px;
        padding: 2rem;
        color: white;
        margin-bottom: 2rem;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
    }

    .info-item {
        padding: 1rem;
        background: #f8f9fa;
        border-radius: 10px;
        border-left: 4px solid #ff9800;
    }

    .info-label {
        font-size: 0.9rem;
        color: #6c757d;
        font-weight: 600;
        margin-bottom: 0.5rem;
    }

    .info-value {
        font-size: 1.1rem;
        color: #2d3748;
        font-weight: 700;
    }

    .foto-bukti {
        max-width: 100%;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        cursor: pointer;
        transition: transform 0.3s ease;
    }

    .foto-bukti:hover {
        transform: scale(1.02);
    }

    .timeline {
        position: relative;
        padding-left: 2rem;
    }

    .timeline::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 2px;
        background: #e0e0e0;
    }

    .timeline-item {
        position: relative;
        margin-bottom: 2rem;
    }

    .timeline-item::before {
        content: '';
        position: absolute;
        left: -2.5rem;
        top: 0;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: #ff9800;
        border: 3px solid white;
        box-shadow: 0 0 0 2px #ff9800;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="fw-bold mb-2">
                        <i class="ti ti-eye text-info"></i> Detail Verifikasi Peminjaman
                    </h2>
                    <p class="text-muted mb-0">Informasi lengkap verifikasi peminjaman barang</p>
                </div>
                <div>
                    <a href="{{ route('pic.verifikasi-peminjaman.index') }}" class="btn btn-outline-secondary">
                        <i class="ti ti-arrow-left"></i> Kembali
                    </a>
                    <button onclick="window.print()" class="btn btn-primary">
                        <i class="ti ti-printer"></i> Cetak
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Header -->
    <div class="status-header">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h4 class="mb-2">
                    <i class="ti ti-package"></i> {{ $peminjaman->barang_summary }}
                </h4>
                <p class="mb-0 opacity-75">
                    Diverifikasi pada {{ $peminjaman->verifikasi->tanggal_verifikasi_format }}
                </p>
            </div>
            <div class="col-md-4 text-md-end">
                <span class="badge bg-{{ $peminjaman->verifikasi->kondisi_badge }} fs-5 mb-2">
                    {!! $peminjaman->verifikasi->kondisi_label !!}
                </span>
                <br>
                <span class="badge bg-{{ $peminjaman->verifikasi->status_badge }} fs-6">
                    {!! $peminjaman->verifikasi->status_label !!}
                </span>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Detail Peminjaman -->
        <div class="col-lg-6 mb-4">
            <div class="detail-card">
                <h5 class="fw-bold mb-4">
                    <i class="ti ti-info-circle text-primary"></i> Detail Peminjaman
                </h5>

                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">Nama Barang</div>
                        <div class="info-value">{{ $peminjaman->barang_summary }}</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">Peminjam</div>
                        <div class="info-value">{{ $peminjaman->user->name }}</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">Instansi</div>
                        <div class="info-value">{{ $peminjaman->user->instansi ?? '-' }}</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">Jumlah Dipinjam</div>
                        <div class="info-value">{{ $peminjaman->total_jumlah }} unit</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">Tanggal Pinjam</div>
                        <div class="info-value">
                            {{ \Carbon\Carbon::parse($peminjaman->tanggal_pinjam)->translatedFormat('d F Y') }}
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">Tanggal Kembali</div>
                        <div class="info-value">
                            {{ \Carbon\Carbon::parse($peminjaman->tanggal_kembali)->translatedFormat('d F Y') }}
                        </div>
                    </div>
                </div>

                @if($peminjaman->keperluan)
                <div class="mt-4 p-3 bg-light rounded">
                    <strong class="text-muted">Keperluan Peminjaman:</strong>
                    <p class="mb-0 mt-2">{{ $peminjaman->keperluan }}</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Detail Verifikasi -->
        <div class="col-lg-6 mb-4">
            <div class="detail-card">
                <h5 class="fw-bold mb-4">
                    <i class="ti ti-clipboard-check text-success"></i> Detail Verifikasi
                </h5>

                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">Diverifikasi Oleh</div>
                        <div class="info-value">{{ $peminjaman->verifikasi->pic->name }}</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">Tanggal Verifikasi</div>
                        <div class="info-value">
                            {{ \Carbon\Carbon::parse($peminjaman->verifikasi->tanggal_verifikasi)->translatedFormat('d F Y, H:i') }} WIB
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">Kondisi Barang</div>
                        <div>
                            <span class="badge bg-{{ $peminjaman->verifikasi->kondisi_badge }} fs-6">
                                {!! $peminjaman->verifikasi->kondisi_label !!}
                            </span>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">Status Verifikasi</div>
                        <div>
                            <span class="badge bg-{{ $peminjaman->verifikasi->status_badge }} fs-6">
                                {!! $peminjaman->verifikasi->status_label !!}
                            </span>
                        </div>
                    </div>
                </div>

                @if($peminjaman->verifikasi->catatan_pic)
                <div class="mt-4 p-3 bg-light rounded">
                    <strong class="text-muted">Catatan PIC:</strong>
                    <p class="mb-0 mt-2">{{ $peminjaman->verifikasi->catatan_pic }}</p>
                </div>
                @endif

                @if($peminjaman->verifikasi->tindakan_admin)
                <div class="mt-3 p-3 bg-warning bg-opacity-10 rounded border border-warning">
                    <strong class="text-warning">
                        <i class="ti ti-alert-triangle"></i> Tindakan Admin:
                    </strong>
                    <p class="mb-0 mt-2">{{ $peminjaman->verifikasi->tindakan_admin }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Foto Bukti -->
    @if($peminjaman->verifikasi->foto_bukti)
    <div class="row">
        <div class="col-12">
            <div class="detail-card">
                <h5 class="fw-bold mb-4">
                    <i class="ti ti-photo text-info"></i> Foto Bukti Kondisi Barang
                </h5>
                <div class="text-center">
                    <img src="{{ $peminjaman->verifikasi->foto_bukti_url }}" 
                         alt="Foto Bukti" 
                         class="foto-bukti"
                         data-bs-toggle="modal" 
                         data-bs-target="#fotoModal">
                    <p class="text-muted mt-3">Klik foto untuk memperbesar</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Foto -->
    <div class="modal fade" id="fotoModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Foto Bukti Verifikasi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center p-0">
                    <img src="{{ $peminjaman->verifikasi->foto_bukti_url }}" 
                         alt="Foto Bukti" 
                         class="w-100">
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Timeline -->
    <div class="row">
        <div class="col-12">
            <div class="detail-card">
                <h5 class="fw-bold mb-4">
                    <i class="ti ti-timeline text-primary"></i> Timeline Peminjaman
                </h5>

                <div class="timeline">
                    <div class="timeline-item">
                        <strong>Peminjaman Dibuat</strong>
                        <p class="text-muted mb-0">
                            {{ \Carbon\Carbon::parse($peminjaman->created_at)->translatedFormat('d F Y, H:i') }} WIB
                        </p>
                    </div>

                    <div class="timeline-item">
                        <strong>Tanggal Pinjam</strong>
                        <p class="text-muted mb-0">
                            {{ \Carbon\Carbon::parse($peminjaman->tanggal_pinjam)->translatedFormat('d F Y') }}
                        </p>
                    </div>

                    <div class="timeline-item">
                        <strong>Barang Dikembalikan</strong>
                        <p class="text-muted mb-0">
                            {{ \Carbon\Carbon::parse($peminjaman->tanggal_kembali)->translatedFormat('d F Y') }}
                        </p>
                    </div>

                    <div class="timeline-item">
                        <strong>Diverifikasi oleh PIC</strong>
                        <p class="text-muted mb-0">
                            {{ \Carbon\Carbon::parse($peminjaman->verifikasi->tanggal_verifikasi)->translatedFormat('d F Y, H:i') }} WIB
                            <br>
                            <span class="badge bg-{{ $peminjaman->verifikasi->kondisi_badge }} mt-1">
                                {!! $peminjaman->verifikasi->kondisi_label !!}
                            </span>
                        </p>
                    </div>

                    @if($peminjaman->verifikasi->tindakan_admin)
                    <div class="timeline-item">
                        <strong>Tindakan Admin</strong>
                        <p class="text-muted mb-0">
                            {{ \Carbon\Carbon::parse($peminjaman->verifikasi->updated_at)->translatedFormat('d F Y, H:i') }} WIB
                            <br>
                            <span class="badge bg-{{ $peminjaman->verifikasi->status_badge }} mt-1">
                                {!! $peminjaman->verifikasi->status_label !!}
                            </span>
                        </p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<style>
@media print {
    .btn, .navbar, .sidebar, footer {
        display: none !important;
    }
    .container-fluid {
        width: 100% !important;
        max-width: 100% !important;
    }
}
</style>
@endpush