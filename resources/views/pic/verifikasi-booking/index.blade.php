@extends('layouts.backend')

@section('title', 'Verifikasi Booking Ruangan')

@push('styles')
<style>
    .filter-card {
        background: linear-gradient(135deg, #2980b9 0%, #8e44ad 100%);
        border: none;
        color: white;
        border-radius: 15px;
        padding: 1.5rem;
        margin-bottom: 2rem;
    }

    .filter-card .form-control,
    .filter-card .form-select {
        border: 2px solid rgba(255,255,255,0.3);
        background: rgba(255,255,255,0.1);
        color: white;
    }

    .filter-card .form-control::placeholder { color: rgba(255,255,255,0.7); }

    .filter-card .form-control:focus,
    .filter-card .form-select:focus {
        background: rgba(255,255,255,0.2);
        color: white;
        border-color: white;
        box-shadow: none;
    }

    .filter-card .form-select option { color: #333; }

    .booking-card {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 1rem;
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        transition: all 0.25s ease;
        border-left: 4px solid #dee2e6;
    }

    .booking-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 18px rgba(0,0,0,0.12);
        border-left-color: #2980b9;
    }

    .booking-card.verified {
        border-left-color: #28a745;
        background: #f0fff4;
    }

    .info-row {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 0.35rem;
        color: #6c757d;
        font-size: 0.92rem;
    }

    .info-row i { color: #2980b9; width: 18px; text-align: center; }
    .info-row strong { color: #495057; }

    .empty-state { padding: 4rem 1rem; }
    .empty-state i { font-size: 4.5rem; color: #dee2e6; }
</style>
@endpush

@section('content')
<div class="container-fluid">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1"><i class="ti ti-door-open text-primary"></i> Verifikasi Booking Ruangan</h2>
            <p class="text-muted mb-0">Kelola verifikasi kondisi ruangan setelah penggunaan</p>
        </div>
        <a href="{{ route('pic.dashboard') }}" class="btn btn-outline-secondary">
            <i class="ti ti-arrow-left"></i> Kembali
        </a>
    </div>

    <!-- Filter -->
    <div class="filter-card">
        <form method="GET" action="{{ route('pic.verifikasi-booking.index') }}">
            <div class="row g-3 align-items-end">
                <div class="col-md-5">
                    <label class="form-label fw-semibold"><i class="ti ti-search"></i> Cari</label>
                    <input type="text" name="search" class="form-control"
                           placeholder="Nama ruangan atau peminjam …"
                           value="{{ request('search') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold"><i class="ti ti-filter"></i> Status Verifikasi</label>
                    <select name="status_verifikasi" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="belum_verifikasi"  {{ request('status_verifikasi') == 'belum_verifikasi'  ? 'selected' : '' }}>Belum Diverifikasi</option>
                        <option value="pending"           {{ request('status_verifikasi') == 'pending'           ? 'selected' : '' }}>Pending</option>
                        <option value="diterima"           {{ request('status_verifikasi') == 'diterima'           ? 'selected' : '' }}>Diterima</option>
                        <option value="perlu_tindakan"    {{ request('status_verifikasi') == 'perlu_tindakan'    ? 'selected' : '' }}>Perlu Tindakan</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-light fw-semibold flex-grow-1">
                        <i class="ti ti-search"></i> Cari
                    </button>
                    @if(request()->hasAny(['search','status_verifikasi']))
                    <a href="{{ route('pic.verifikasi-booking.index') }}" class="btn btn-outline-light">
                        <i class="ti ti-x"></i>
                    </a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <!-- List -->
    @forelse($bookings as $booking)
    <div class="booking-card {{ $booking->isVerified() ? 'verified' : '' }}">
        <div class="row align-items-center">

            <!-- Info kiri -->
            <div class="col-lg-8">
                <h5 class="fw-bold mb-2">{{ $booking->ruangan->nama_ruangan }}</h5>

                <div class="info-row"><i class="ti ti-user"></i>      <strong>Peminjam :</strong> {{ $booking->user->name }}</div>
                <div class="info-row"><i class="ti ti-calendar"></i>  <strong>Tanggal  :</strong> {{ \Carbon\Carbon::parse($booking->tanggal)->format('d / m / Y') }}</div>
                <div class="info-row"><i class="ti ti-clock"></i>     <strong>Waktu    :</strong> {{ substr($booking->waktu_mulai, 0, 5) }} – {{ substr($booking->waktu_selesai, 0, 5) }}</div>

                @if($booking->keperluan)
                <div class="info-row"><i class="ti ti-note"></i>      <strong>Keperluan:</strong> {{ $booking->keperluan }}</div>
                @endif

                <!-- Badge status verifikasi -->
                <div class="mt-2">
                    @if($booking->isVerified())
                        <span class="badge bg-{{ $booking->verifikasi->kondisi_badge }} me-1">
                            {!! $booking->verifikasi->kondisi_label !!}
                        </span>
                        <span class="badge bg-{{ $booking->verifikasi->status_badge }}">
                            {!! $booking->verifikasi->status_label !!}
                        </span>
                    @else
                        <span class="badge bg-warning text-dark">
                            <i class="ti ti-alert-circle"></i> Belum Diverifikasi
                        </span>
                    @endif
                </div>
            </div>

            <!-- Tombol aksi kanan -->
            <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
                @if($booking->isVerified())
                    <a href="{{ route('pic.verifikasi-booking.show', $booking->id) }}"
                       class="btn btn-info">
                        <i class="ti ti-eye"></i> Lihat Detail
                    </a>
                @else
                    <a href="{{ route('pic.verifikasi-booking.create', $booking->id) }}"
                       class="btn btn-primary">
                        <i class="ti ti-clipboard-check"></i> Verifikasi
                    </a>
                @endif
            </div>
        </div>
    </div>
    @empty
    <div class="empty-state text-center">
        <i class="ti ti-inbox"></i>
        <h5 class="text-muted mt-3">Tidak ada data</h5>
        <p class="text-muted">
            @if(request()->hasAny(['search','status_verifikasi']))
                Tidak ada booking yang cocok dengan filter Anda.
                <br>
                <a href="{{ route('pic.verifikasi-booking.index') }}" class="btn btn-sm btn-outline-primary mt-2">
                    <i class="ti ti-refresh"></i> Reset Filter
                </a>
            @else
                Belum ada booking dengan status Selesai yang perlu diverifikasi.
            @endif
        </p>
    </div>
    @endforelse

    <!-- Pagination -->
    @if($bookings->hasPages())
    <div class="d-flex justify-content-between align-items-center mt-3">
        <small class="text-muted">
            Tampil {{ $bookings->firstItem() }}–{{ $bookings->lastItem() }} dari {{ $bookings->total() }}
        </small>
        {{ $bookings->links() }}
    </div>
    @endif

</div>
@endsection