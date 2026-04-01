@extends('layouts.frontend')
@section('title', 'Tagihan Denda Saya')

@push('styles')
<style>
    body { background: #f0f4f8; }

    .page-wrapper { min-height: 100vh; padding: 2.5rem 0 4rem; }

    .page-hero {
        background: linear-gradient(135deg, #c0392b 0%, #922b21 100%);
        border-radius: 16px;
        padding: 2rem 2.5rem;
        color: #fff;
        margin-bottom: 2rem;
        position: relative;
        overflow: hidden;
    }
    .page-hero::after {
        content: ''; position: absolute;
        top: -40px; right: -40px;
        width: 180px; height: 180px;
        border-radius: 50%; background: rgba(255,255,255,0.07);
    }
    .page-hero h4 { font-size: 1.5rem; font-weight: 700; margin: 0 0 .25rem; }
    .page-hero p  { margin: 0; opacity: .75; font-size: .9rem; }
    .page-hero .back-link {
        color: rgba(255,255,255,.75); text-decoration: none;
        font-size: .875rem; display: inline-flex; align-items: center;
        gap: 6px; margin-bottom: .75rem; transition: color .2s;
    }
    .page-hero .back-link:hover { color: #fff; }

    /* Stat cards */
    .stat-grid { display: grid; grid-template-columns: repeat(3,1fr); gap: 1rem; margin-bottom: 1.75rem; }
    .stat-card {
        background: #fff; border-radius: 14px;
        padding: 1.25rem 1.5rem;
        box-shadow: 0 2px 10px rgba(0,0,0,.06);
    }
    .stat-card .lbl { font-size: .78rem; font-weight: 600; color: #94a3b8; text-transform: uppercase; letter-spacing: .05em; }
    .stat-card .val { font-size: 1.4rem; font-weight: 700; margin-top: 4px; }
    .stat-card.danger .val { color: #e74c3c; }
    .stat-card.success .val { color: #27ae60; }
    .stat-card.info .val    { color: #2980b9; }

    /* Denda card */
    .denda-card {
        background: #fff; border-radius: 14px;
        padding: 1.5rem;
        box-shadow: 0 2px 10px rgba(0,0,0,.06);
        margin-bottom: 1rem;
        border-left: 5px solid #dee2e6;
        transition: transform .2s, box-shadow .2s;
    }
    .denda-card:hover { transform: translateY(-2px); box-shadow: 0 6px 18px rgba(0,0,0,.1); }
    .denda-card.belum_bayar          { border-left-color: #e74c3c; }
    .denda-card.menunggu_verifikasi  { border-left-color: #f39c12; }
    .denda-card.sudah_bayar          { border-left-color: #27ae60; }
    .denda-card.dibebaskan           { border-left-color: #95a5a6; }

    .badge-status {
        display: inline-flex; align-items: center; gap: 5px;
        padding: 5px 12px; border-radius: 20px;
        font-size: .78rem; font-weight: 700;
    }
    .badge-status.belum_bayar         { background: #fdecea; color: #c0392b; }
    .badge-status.menunggu_verifikasi { background: #fef9e7; color: #d35400; }
    .badge-status.sudah_bayar         { background: #eafaf1; color: #1e8449; }
    .badge-status.dibebaskan          { background: #f2f3f4; color: #7f8c8d; }

    .jumlah-denda { font-size: 1.3rem; font-weight: 700; color: #e74c3c; }
    .jumlah-denda.lunas { color: #27ae60; }

    .meta-row { display: flex; flex-wrap: wrap; gap: 1rem; margin-top: .6rem; }
    .meta-item { font-size: .83rem; color: #64748b; display: flex; align-items: center; gap: 5px; }

    /* Empty state */
    .empty-state {
        background: #fff; border-radius: 14px; padding: 4rem 2rem;
        text-align: center; box-shadow: 0 2px 10px rgba(0,0,0,.06);
    }
    .empty-state i { font-size: 3rem; color: #cbd5e1; }

    @media (max-width: 576px) {
        .stat-grid { grid-template-columns: 1fr; }
    }
</style>
@endpush

@section('content')
<div class="page-wrapper">
<div class="container">
<div class="row justify-content-center">
<div class="col-xl-9 col-lg-10">

    {{-- Hero --}}
    <div class="page-hero">
        <a href="{{ url()->previous() }}" class="back-link">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
        <h4><i class="fas fa-file-invoice-dollar me-2"></i>Tagihan Denda Saya</h4>
        <p>Daftar denda peminjaman barang yang perlu diselesaikan</p>
    </div>

    {{-- Alert --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4 rounded-3" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Stat Cards --}}
    <div class="stat-grid">
        <div class="stat-card danger">
            <div class="lbl">Total Tagihan Aktif</div>
            <div class="val">Rp {{ number_format($stats['total_tagihan'], 0, ',', '.') }}</div>
        </div>
        <div class="stat-card success">
            <div class="lbl">Total Sudah Lunas</div>
            <div class="val">Rp {{ number_format($stats['total_lunas'], 0, ',', '.') }}</div>
        </div>
        <div class="stat-card info">
            <div class="lbl">Denda Belum Bayar</div>
            <div class="val">{{ $stats['jumlah_aktif'] }} tagihan</div>
        </div>
    </div>

    {{-- List --}}
    @forelse ($dendas as $denda)
        @php
            $status = $denda->status_pembayaran;
            $pmj    = $denda->pengembalianBarang->peminjamanBarang ?? null;
        @endphp
        <div class="denda-card {{ $status }}">
            <div class="row align-items-center g-3">
                <div class="col-md-8">
                    {{-- Status badge --}}
                    <div class="mb-2">
                        <span class="badge-status {{ $status }}">
                            @if($status === 'belum_bayar')          <i class="fas fa-exclamation-circle"></i> Belum Bayar
                            @elseif($status === 'menunggu_verifikasi') <i class="fas fa-clock"></i> Menunggu Verifikasi
                            @elseif($status === 'sudah_bayar')      <i class="fas fa-check-circle"></i> Lunas
                            @elseif($status === 'dibebaskan')       <i class="fas fa-times-circle"></i> Dibebaskan
                            @endif
                        </span>
                    </div>

                    {{-- Kode peminjaman --}}
                    <div class="fw-bold mb-1" style="font-size:.95rem; color:#1e293b;">
                        {{ $pmj->kode ?? '-' }}
                    </div>

                    {{-- Jumlah --}}
                    <div class="jumlah-denda {{ in_array($status, ['sudah_bayar','dibebaskan']) ? 'lunas' : '' }}">
                        Rp {{ number_format($denda->jumlah_denda, 0, ',', '.') }}
                    </div>

                    {{-- Meta --}}
                    <div class="meta-row">
                        <span class="meta-item">
                            <i class="fas fa-exclamation-triangle text-warning"></i>
                            Kondisi: <strong>{{ ucfirst(str_replace('_',' ', $denda->verifikasiPengembalian->kondisi ?? '-')) }}</strong>
                        </span>
                        <span class="meta-item">
                            <i class="fas fa-calendar"></i>
                            {{ $denda->tanggal_tindakan ? \Carbon\Carbon::parse($denda->tanggal_tindakan)->translatedFormat('d F Y') : '-' }}
                        </span>
                    </div>
                </div>

                <div class="col-md-4 d-flex justify-content-md-end">
                    <a href="{{ route('user.denda.show', $denda->id) }}"
                        class="btn btn-outline-primary btn-sm px-4">
                        <i class="fas fa-eye me-1"></i>
                        {{ $status === 'belum_bayar' ? 'Bayar Sekarang' : 'Lihat Detail' }}
                    </a>
                </div>
            </div>
        </div>
    @empty
        <div class="empty-state">
            <i class="fas fa-check-circle text-success d-block mb-3"></i>
            <h5 class="fw-semibold text-muted">Tidak ada tagihan denda</h5>
            <p class="text-muted small">Semua peminjaman Anda berjalan baik tanpa denda.</p>
        </div>
    @endforelse

</div>
</div>
</div>
</div>
@endsection