@extends('layouts.backend')

@section('title', 'Dashboard PIC')

@push('styles')
<style>
    .stats-card {
        border-radius: 15px;
        padding: 1.5rem;
        transition: all 0.3s ease;
        border-left: 4px solid;
    }

    .stats-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.12);
    }

    .stats-icon {
        font-size: 3rem;
        opacity: 0.3;
    }

    .recent-card {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        margin-bottom: 1.5rem;
    }

    .recent-item {
        padding: 1rem;
        border-left: 3px solid #e0e0e0;
        margin-bottom: 1rem;
        background: #f8f9fa;
        border-radius: 8px;
        transition: all 0.2s ease;
    }

    .recent-item:hover {
        border-left-color: #ff9800;
        background: #fff3e0;
    }

    .badge-custom {
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 600;
    }

    .pending-badge {
        background: #fff3cd;
        color: #856404;
        border: 1px solid #ffeaa7;
    }

    .section-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: #2d3748;
        margin-bottom: 1.5rem;
        padding-bottom: 0.5rem;
        border-bottom: 3px solid #ff9800;
    }

    .barang-list {
        font-size: 0.9rem;
        color: #6c757d;
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
                        <i class="ti ti-dashboard text-primary"></i> Dashboard PIC
                    </h2>
                    <p class="text-muted mb-0">
                        Selamat datang, <strong>{{ Auth::user()->name }}</strong>
                    </p>
                </div>
                <div>
                    <span class="badge bg-info fs-6">
                        <i class="ti ti-clock"></i> {{ now()->translatedFormat('l, d F Y') }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <!-- Peminjaman Stats -->
        <div class="col-md-6 col-lg-3 mb-3">
            <div class="stats-card bg-white border-start-warning">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Peminjaman Perlu Verifikasi</p>
                        <h2 class="fw-bold mb-0 text-warning">{{ $stats['peminjaman_perlu_verifikasi'] }}</h2>
                    </div>
                    <div class="stats-icon text-warning">
                        <i class="ti ti-package"></i>
                    </div>
                </div>
                <a href="{{ route('pic.verifikasi-peminjaman.index') }}" class="btn btn-sm btn-warning mt-3 w-100">
                    <i class="ti ti-arrow-right"></i> Lihat Detail
                </a>
            </div>
        </div>

        <div class="col-md-6 col-lg-3 mb-3">
            <div class="stats-card bg-white border-start-success">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Peminjaman Sudah Diverifikasi</p>
                        <h2 class="fw-bold mb-0 text-success">{{ $stats['peminjaman_sudah_verifikasi'] }}</h2>
                    </div>
                    <div class="stats-icon text-success">
                        <i class="ti ti-check"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Booking Stats -->
        <div class="col-md-6 col-lg-3 mb-3">
            <div class="stats-card bg-white border-start-primary">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Booking Perlu Verifikasi</p>
                        <h2 class="fw-bold mb-0 text-primary">{{ $stats['booking_perlu_verifikasi'] }}</h2>
                    </div>
                    <div class="stats-icon text-primary">
                        <i class="ti ti-door"></i>
                    </div>
                </div>
                <a href="{{ route('pic.verifikasi-booking.index') }}" class="btn btn-sm btn-primary mt-3 w-100">
                    <i class="ti ti-arrow-right"></i> Lihat Detail
                </a>
            </div>
        </div>

        <div class="col-md-6 col-lg-3 mb-3">
            <div class="stats-card bg-white border-start-info">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Booking Sudah Diverifikasi</p>
                        <h2 class="fw-bold mb-0 text-info">{{ $stats['booking_sudah_verifikasi'] }}</h2>
                    </div>
                    <div class="stats-icon text-info">
                        <i class="ti ti-check"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert untuk Item Bermasalah -->
    @if($stats['peminjaman_bermasalah'] > 0 || $stats['booking_bermasalah'] > 0)
    <div class="row mb-4">
        <div class="col-12">
            <div class="alert alert-danger d-flex align-items-center">
                <i class="ti ti-alert-triangle fs-1 me-3"></i>
                <div>
                    <h5 class="mb-1">⚠️ Perhatian: Ada Item Bermasalah!</h5>
                    <p class="mb-0">
                        <strong>{{ $stats['peminjaman_bermasalah'] }}</strong> peminjaman barang bermasalah &bull; 
                        <strong>{{ $stats['booking_bermasalah'] }}</strong> booking ruangan bermasalah
                    </p>
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="row">
        <!-- Pending Verifikasi Peminjaman -->
        <div class="col-lg-6 mb-4">
            <div class="recent-card">
                <h5 class="section-title">
                    <i class="ti ti-package"></i> Peminjaman Perlu Verifikasi
                </h5>

                @forelse($pendingPeminjaman as $item)
                <div class="recent-item">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div class="flex-grow-1">
                            {{-- 🔥 FIX: Pakai accessor barang_summary --}}
                            <h6 class="mb-1 fw-bold">{{ $item->barang_summary }}</h6>
                            <small class="text-muted">
                                <i class="ti ti-user"></i> {{ $item->user->name }}
                            </small>
                            <br>
                            <small class="text-muted">
                                <i class="ti ti-building"></i> {{ $item->instansi ?? $item->user->instansi }}
                            </small>
                        </div>
                        <span class="badge-custom pending-badge">
                            <i class="ti ti-clock"></i> Pending
                        </span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <small class="text-muted">
                            <i class="ti ti-calendar"></i> 
                            {{ \Carbon\Carbon::parse($item->tanggal_kembali)->format('d/m/Y') }}
                        </small>
                        <a href="{{ route('pic.verifikasi-peminjaman.create', $item->id) }}" 
                           class="btn btn-sm btn-warning">
                            <i class="ti ti-check"></i> Verifikasi
                        </a>
                    </div>
                </div>
                @empty
                <div class="text-center text-muted py-4">
                    <i class="ti ti-check-circle fs-1"></i>
                    <p class="mb-0 mt-2">Tidak ada peminjaman yang perlu diverifikasi</p>
                </div>
                @endforelse

                @if($pendingPeminjaman->count() > 0)
                <div class="text-center mt-3">
                    <a href="{{ route('pic.verifikasi-peminjaman.index') }}" class="btn btn-outline-warning">
                        Lihat Semua <i class="ti ti-arrow-right"></i>
                    </a>
                </div>
                @endif
            </div>
        </div>

        <!-- Pending Verifikasi Booking -->
        <div class="col-lg-6 mb-4">
            <div class="recent-card">
                <h5 class="section-title">
                    <i class="ti ti-door"></i> Booking Perlu Verifikasi
                </h5>

                @forelse($pendingBooking as $item)
                <div class="recent-item">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div class="flex-grow-1">
                            <h6 class="mb-1 fw-bold">{{ $item->ruangan->nama_ruangan }}</h6>
                            <small class="text-muted">
                                <i class="ti ti-user"></i> {{ $item->user->name }}
                            </small>
                            <br>
                            <small class="text-muted">
                                <i class="ti ti-clock"></i> {{ $item->waktu_mulai }} - {{ $item->waktu_selesai }}
                            </small>
                        </div>
                        <span class="badge-custom pending-badge">
                            <i class="ti ti-clock"></i> Pending
                        </span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <small class="text-muted">
                            <i class="ti ti-calendar"></i> 
                            {{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}
                        </small>
                        <a href="{{ route('pic.verifikasi-booking.create', $item->id) }}" 
                           class="btn btn-sm btn-primary">
                            <i class="ti ti-check"></i> Verifikasi
                        </a>
                    </div>
                </div>
                @empty
                <div class="text-center text-muted py-4">
                    <i class="ti ti-check-circle fs-1"></i>
                    <p class="mb-0 mt-2">Tidak ada booking yang perlu diverifikasi</p>
                </div>
                @endforelse

                @if($pendingBooking->count() > 0)
                <div class="text-center mt-3">
                    <a href="{{ route('pic.verifikasi-booking.index') }}" class="btn btn-outline-primary">
                        Lihat Semua <i class="ti ti-arrow-right"></i>
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Recent Verifikasi -->
    <div class="row">
        <!-- Recent Verifikasi Peminjaman -->
        <div class="col-lg-6 mb-4">
            <div class="recent-card">
                <h5 class="section-title">
                    <i class="ti ti-history"></i> Verifikasi Peminjaman Terbaru
                </h5>

                @forelse($recentVerifikasiPeminjaman as $verif)
                <div class="recent-item border-start-{{ $verif->kondisi_badge }}">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            {{-- 🔥 FIX: Pakai accessor barang_summary --}}
                            <h6 class="mb-1 fw-bold">{{ $verif->peminjaman->barang_summary }}</h6>
                            <small class="text-muted">
                                {{ $verif->peminjaman->user->name }}
                            </small>
                        </div>
                        <span class="badge bg-{{ $verif->kondisi_badge }}">
                            {!! $verif->kondisi_label !!}
                        </span>
                    </div>
                    <small class="text-muted">
                        <i class="ti ti-clock"></i> {{ $verif->tanggal_verifikasi_format }}
                    </small>
                </div>
                @empty
                <div class="text-center text-muted py-4">
                    <i class="ti ti-inbox fs-1"></i>
                    <p class="mb-0 mt-2">Belum ada riwayat verifikasi</p>
                </div>
                @endforelse
            </div>
        </div>

        <!-- Recent Verifikasi Booking -->
        <div class="col-lg-6 mb-4">
            <div class="recent-card">
                <h5 class="section-title">
                    <i class="ti ti-history"></i> Verifikasi Booking Terbaru
                </h5>

                @forelse($recentVerifikasiBooking as $verif)
                <div class="recent-item border-start-{{ $verif->kondisi_badge }}">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <h6 class="mb-1 fw-bold">{{ $verif->booking->ruangan->nama_ruangan }}</h6>
                            <small class="text-muted">
                                {{ $verif->booking->user->name }}
                            </small>
                        </div>
                        <span class="badge bg-{{ $verif->kondisi_badge }}">
                            {!! $verif->kondisi_label !!}
                        </span>
                    </div>
                    <small class="text-muted">
                        <i class="ti ti-clock"></i> {{ $verif->tanggal_verifikasi_format }}
                    </small>
                </div>
                @empty
                <div class="text-center text-muted py-4">
                    <i class="ti ti-inbox fs-1"></i>
                    <p class="mb-0 mt-2">Belum ada riwayat verifikasi</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection