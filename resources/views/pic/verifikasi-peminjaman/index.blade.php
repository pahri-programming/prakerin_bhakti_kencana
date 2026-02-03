@extends('layouts.backend')

@section('title', 'Verifikasi Peminjaman Barang')

@push('styles')
<style>
    .filter-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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

    .filter-card .form-control::placeholder {
        color: rgba(255,255,255,0.7);
    }

    .filter-card .form-control:focus,
    .filter-card .form-select:focus {
        background: rgba(255,255,255,0.2);
        color: white;
        border-color: white;
    }

    .filter-card .form-select option {
        color: #333;
    }

    .peminjaman-card {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        transition: all 0.3s ease;
        border-left: 4px solid #e0e0e0;
    }

    .peminjaman-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 20px rgba(0,0,0,0.12);
        border-left-color: #ff9800;
    }

    .peminjaman-card.verified {
        border-left-color: #28a745;
        background: #f8fff9;
    }

    .barang-name {
        font-size: 1.2rem;
        font-weight: 700;
        color: #2d3748;
        margin-bottom: 0.5rem;
    }

    .info-row {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 0.5rem;
        color: #6c757d;
    }

    .info-row i {
        color: #ff9800;
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
                        <i class="ti ti-package text-warning"></i> Verifikasi Peminjaman Barang
                    </h2>
                    <p class="text-muted mb-0">Kelola verifikasi pengembalian barang</p>
                </div>
                <a href="{{ route('pic.dashboard') }}" class="btn btn-outline-secondary">
                    <i class="ti ti-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="filter-card">
        <form action="{{ route('pic.verifikasi-peminjaman.index') }}" method="GET">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">
                        <i class="ti ti-search"></i> Cari Peminjaman
                    </label>
                    <input type="text" name="search" class="form-control" 
                           placeholder="Cari nama barang atau peminjam..." 
                           value="{{ request('search') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">
                        <i class="ti ti-filter"></i> Status Verifikasi
                    </label>
                    <select name="status_verifikasi" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="belum_verifikasi" {{ request('status_verifikasi') == 'belum_verifikasi' ? 'selected' : '' }}>
                            Belum Verifikasi
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
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-light w-100 fw-semibold">
                        <i class="ti ti-search"></i> Cari
                    </button>
                </div>
            </div>
            @if(request()->hasAny(['search', 'status_verifikasi']))
            <div class="text-end mt-2">
                <a href="{{ route('pic.verifikasi-peminjaman.index') }}" class="btn btn-sm btn-outline-light">
                    <i class="ti ti-x"></i> Reset Filter
                </a>
            </div>
            @endif
        </form>
    </div>

    <!-- Peminjaman List -->
    @forelse($peminjaman as $item)
    <div class="peminjaman-card {{ $item->isVerified() ? 'verified' : '' }}">
        <div class="row align-items-center">
            <div class="col-md-7">
                <h5 class="barang-name">{{ $item->barang_summary }}</h5>
                
                <div class="info-row">
                    <i class="ti ti-user"></i>
                    <span><strong>Peminjam:</strong> {{ $item->user->name }}</span>
                </div>

                <div class="info-row">
                    <i class="ti ti-calendar"></i>
                    <span>
                        <strong>Dipinjam:</strong> {{ \Carbon\Carbon::parse($item->tanggal_pinjam)->format('d/m/Y') }}
                    </span>
                </div>

                <div class="info-row">
                    <i class="ti ti-calendar-check"></i>
                    <span>
                        <strong>Dikembalikan:</strong> 
                        {{ \Carbon\Carbon::parse($item->tanggal_kembali)->format('d/m/Y') }}
                    </span>
                </div>

                <div class="info-row">
                    <i class="ti ti-package"></i>
                    <span><strong>Jumlah:</strong> {{ $item->total_jumlah }} unit</span>
                </div>

                @if($item->isVerified())
                <div class="mt-2">
                    <span class="badge bg-{{ $item->verifikasi->kondisi_badge }} me-2">
                        {!! $item->verifikasi->kondisi_label !!}
                    </span>
                    <span class="badge bg-{{ $item->verifikasi->status_badge }}">
                        {!! $item->verifikasi->status_label !!}
                    </span>
                </div>
                @else
                <div class="mt-2">
                    <span class="badge bg-warning">
                        <i class="ti ti-alert-circle"></i> Belum Diverifikasi
                    </span>
                </div>
                @endif
            </div>

            <div class="col-md-5 text-end">
                @if($item->isVerified())
                    <a href="{{ route('pic.verifikasi-peminjaman.show', $item->id) }}" 
                       class="btn btn-info btn-lg">
                        <i class="ti ti-eye"></i> Lihat Detail
                    </a>
                @else
                    <a href="{{ route('pic.verifikasi-peminjaman.create', $item->id) }}" 
                       class="btn btn-warning btn-lg">
                        <i class="ti ti-check"></i> Verifikasi Sekarang
                    </a>
                @endif
            </div>
        </div>
    </div>
    @empty
    <div class="text-center py-5">
        <div class="mb-3">
            <i class="ti ti-inbox" style="font-size: 5rem; color: #e0e0e0;"></i>
        </div>
        <h4 class="text-muted">Tidak Ada Data</h4>
        <p class="text-muted">
            @if(request()->hasAny(['search', 'status_verifikasi']))
                Tidak ada peminjaman yang sesuai dengan filter Anda.
            @else
                Belum ada peminjaman yang perlu diverifikasi.
            @endif
        </p>
        @if(request()->hasAny(['search', 'status_verifikasi']))
        <a href="{{ route('pic.verifikasi-peminjaman.index') }}" class="btn btn-primary">
            <i class="ti ti-refresh"></i> Reset Filter
        </a>
        @endif
    </div>
    @endforelse

    <!-- Pagination -->
    @if($peminjaman->hasPages())
    <div class="d-flex justify-content-between align-items-center mt-4">
        <div class="text-muted">
            Menampilkan {{ $peminjaman->firstItem() }} - {{ $peminjaman->lastItem() }} 
            dari {{ $peminjaman->total() }} peminjaman
        </div>
        <div>
            {{ $peminjaman->links() }}
        </div>
    </div>
    @endif
</div>
@endsection