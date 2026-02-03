@extends('layouts.backend')

@section('title', 'Detail Verifikasi – ' . $booking->ruangan->nama_ruangan)

@push('styles')
<style>
    .header-banner {
        background: linear-gradient(135deg, #2980b9 0%, #8e44ad 100%);
        border-radius: 14px;
        padding: 1.75rem 2rem;
        color: #fff;
        margin-bottom: 1.75rem;
    }

    .card-section {
        background: #fff;
        border-radius: 14px;
        padding: 1.75rem;
        box-shadow: 0 3px 14px rgba(0,0,0,0.07);
        margin-bottom: 1.5rem;
    }

    .card-section h5 { border-bottom: 3px solid #2980b9; padding-bottom: .45rem; display: inline-block; }

    /* info grid */
    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 1rem;
    }

    .info-box {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 1rem;
        border-left: 4px solid #2980b9;
    }

    .info-box .lbl { font-size: .82rem; color: #6c757d; font-weight: 600; }
    .info-box .val { font-size: 1rem;   color: #2d3748; font-weight: 700; margin-top: .2rem; }

    /* timeline */
    .timeline { position: relative; padding-left: 2rem; }
    .timeline::before {
        content:''; position:absolute; left:8px; top:0; bottom:0;
        width:2px; background:#dee2e6;
    }

    .tl-item { position:relative; margin-bottom:1.5rem; }
    .tl-item::before {
        content:''; position:absolute; left:-1.6rem; top:.35rem;
        width:12px; height:12px; border-radius:50%;
        background:#2980b9; border:3px solid #fff;
        box-shadow: 0 0 0 2px #2980b9;
    }

    .tl-item .tl-title  { font-weight:700; color:#2d3748; }
    .tl-item .tl-date   { font-size:.85rem; color:#6c757d; }

    /* foto */
    .foto-thumb {
        max-width:100%; border-radius:10px;
        box-shadow: 0 4px 12px rgba(0,0,0,.1);
        cursor:pointer; transition:transform .2s;
    }
    .foto-thumb:hover { transform:scale(1.02); }

    /* print */
    @media print {
        .no-print, .navbar, .sidebar, footer, .btn { display:none !important; }
        .container-fluid { width:100% !important; max-width:100% !important; }
        .card-section { box-shadow:none; border:1px solid #dee2e6; }
    }
</style>
@endpush

@section('content')
<div class="container-fluid">

    <!-- Header buttons -->
    <div class="d-flex justify-content-between align-items-center mb-3 no-print">
        <a href="{{ route('pic.verifikasi-booking.index') }}" class="btn btn-outline-secondary">
            <i class="ti ti-arrow-left"></i> Kembali
        </a>
        <button class="btn btn-outline-primary" onclick="window.print()">
            <i class="ti ti-printer"></i> Cetak
        </button>
    </div>

    <!-- Banner -->
    <div class="header-banner">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h4 class="mb-1"><i class="ti ti-door-open"></i> {{ $booking->ruangan->nama_ruangan }}</h4>
                <small class="opacity-75">
                    Diverifikasi pada {{ $booking->verifikasi->tanggal_verifikasi_format }}
                    &bull; oleh {{ $booking->verifikasi->pic->name }}
                </small>
            </div>
            <div class="col-md-4 text-md-end mt-2 mt-md-0">
                <span class="badge bg-{{ $booking->verifikasi->kondisi_badge }} fs-6 me-1">
                    {!! $booking->verifikasi->kondisi_label !!}
                </span>
                <span class="badge bg-{{ $booking->verifikasi->status_badge }} fs-6">
                    {!! $booking->verifikasi->status_label !!}
                </span>
            </div>
        </div>
    </div>

    <div class="row">

        <!-- ====== Kolom Kiri — booking + verifikasi ====== -->
        <div class="col-lg-7">

            <!-- Detail Booking -->
            <div class="card-section">
                <h5 class="fw-bold mb-3"><i class="ti ti-info-circle text-primary"></i> Detail Booking</h5>
                <div class="info-grid">
                    <div class="info-box">
                        <div class="lbl">Ruangan</div>
                        <div class="val">{{ $booking->ruangan->nama_ruangan }}</div>
                    </div>
                    <div class="info-box">
                        <div class="lbl">Lokasi</div>
                        <div class="val">{{ $booking->ruangan->lokasi ?? '–' }}</div>
                    </div>
                    <div class="info-box">
                        <div class="lbl">Kapasitas</div>
                        <div class="val">{{ $booking->ruangan->kapasitas }} orang</div>
                    </div>
                    <div class="info-box">
                        <div class="lbl">Peminjam</div>
                        <div class="val">{{ $booking->user->name }}</div>
                    </div>
                    <div class="info-box">
                        <div class="lbl">Instansi</div>
                        <div class="val">{{ $booking->user->instansi ?? '–' }}</div>
                    </div>
                    <div class="info-box">
                        <div class="lbl">Tanggal</div>
                        <div class="val">{{ \Carbon\Carbon::parse($booking->tanggal)->translatedFormat('d F Y') }}</div>
                    </div>
                    <div class="info-box">
                        <div class="lbl">Waktu</div>
                        <div class="val">{{ substr($booking->waktu_mulai,0,5) }} – {{ substr($booking->waktu_selesai,0,5) }}</div>
                    </div>
                    <div class="info-box">
                        <div class="lbl">Status Booking</div>
                        <div class="val"><span class="badge bg-success">{{ $booking->status }}</span></div>
                    </div>
                </div>

                @if($booking->keperluan)
                <div class="mt-3 p-3 bg-light rounded">
                    <strong class="text-muted" style="font-size:.88rem">Keperluan</strong>
                    <p class="mb-0 mt-1" style="font-size:.9rem">{{ $booking->keperluan }}</p>
                </div>
                @endif
            </div>

            <!-- Detail Verifikasi -->
            <div class="card-section">
                <h5 class="fw-bold mb-3"><i class="ti ti-clipboard-check text-success"></i> Hasil Verifikasi</h5>
                <div class="info-grid">
                    <div class="info-box">
                        <div class="lbl">Diverifikasi Oleh</div>
                        <div class="val">{{ $booking->verifikasi->pic->name }}</div>
                    </div>
                    <div class="info-box">
                        <div class="lbl">Tanggal Verifikasi</div>
                        <div class="val">{{ $booking->verifikasi->tanggal_verifikasi_format }}</div>
                    </div>
                    <div class="info-box">
                        <div class="lbl">Kondisi Ruangan</div>
                        <div class="val">
                            <span class="badge bg-{{ $booking->verifikasi->kondisi_badge }} fs-6">
                                {!! $booking->verifikasi->kondisi_label !!}
                            </span>
                        </div>
                    </div>
                    <div class="info-box">
                        <div class="lbl">Status Verifikasi</div>
                        <div class="val">
                            <span class="badge bg-{{ $booking->verifikasi->status_badge }} fs-6">
                                {!! $booking->verifikasi->status_label !!}
                            </span>
                        </div>
                    </div>
                </div>

                @if($booking->verifikasi->catatan_pic)
                <div class="mt-3 p-3 bg-light rounded">
                    <strong class="text-muted" style="font-size:.88rem">Catatan PIC</strong>
                    <p class="mb-0 mt-1" style="font-size:.9rem">{{ $booking->verifikasi->catatan_pic }}</p>
                </div>
                @endif

                @if($booking->verifikasi->tindakan_admin)
                <div class="mt-3 p-3 rounded" style="background:#fff3cd; border:1px solid #ffc107;">
                    <strong style="font-size:.88rem; color:#856404;">
                        <i class="ti ti-alert-triangle"></i> Tindakan Admin
                    </strong>
                    <p class="mb-0 mt-1" style="font-size:.9rem; color:#856404;">
                        {{ $booking->verifikasi->tindakan_admin }}
                    </p>
                </div>
                @endif
            </div>
        </div>

        <!-- ====== Kolom Kanan — foto + timeline ====== -->
        <div class="col-lg-5">

            <!-- Foto Bukti -->
            @if($booking->verifikasi->foto_bukti)
            <div class="card-section">
                <h5 class="fw-bold mb-3"><i class="ti ti-photo text-info"></i> Foto Bukti</h5>
                <div class="text-center">
                    <img src="{{ $booking->verifikasi->foto_bukti_url }}"
                         alt="Foto Bukti"
                         class="foto-thumb"
                         data-bs-toggle="modal"
                         data-bs-target="#modalFoto">
                    <small class="text-muted d-block mt-2">Klik untuk memperbesar</small>
                </div>
            </div>
            @endif

            <!-- Timeline -->
            <div class="card-section">
                <h5 class="fw-bold mb-3"><i class="ti ti-timeline text-primary"></i> Timeline</h5>

                <div class="timeline">
                    <div class="tl-item">
                        <div class="tl-title">Booking Dibuat</div>
                        <div class="tl-date">{{ $booking->created_at->translatedFormat('d F Y, H:i') }} WIB</div>
                    </div>

                    <div class="tl-item">
                        <div class="tl-title">Tanggal Penggunaan</div>
                        <div class="tl-date">
                            {{ \Carbon\Carbon::parse($booking->tanggal)->translatedFormat('d F Y') }}
                            &nbsp; {{ substr($booking->waktu_mulai,0,5) }} – {{ substr($booking->waktu_selesai,0,5) }}
                        </div>
                    </div>

                    <div class="tl-item">
                        <div class="tl-title">Status Selesai</div>
                        <div class="tl-date">
                            {{ $booking->updated_at->translatedFormat('d F Y, H:i') }} WIB
                        </div>
                    </div>

                    <div class="tl-item">
                        <div class="tl-title">Diverifikasi PIC</div>
                        <div class="tl-date">
                            {{ $booking->verifikasi->tanggal_verifikasi_format }}
                            <br>
                            <span class="badge bg-{{ $booking->verifikasi->kondisi_badge }} mt-1">
                                {!! $booking->verifikasi->kondisi_label !!}
                            </span>
                        </div>
                    </div>

                    @if($booking->verifikasi->tindakan_admin)
                    <div class="tl-item">
                        <div class="tl-title">Tindakan Admin</div>
                        <div class="tl-date">
                            {{ $booking->verifikasi->updated_at->translatedFormat('d F Y, H:i') }} WIB
                            <br>
                            <span class="badge bg-{{ $booking->verifikasi->status_badge }} mt-1">
                                {!! $booking->verifikasi->status_label !!}
                            </span>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Foto -->
@if($booking->verifikasi->foto_bukti)
<div class="modal fade" id="modalFoto" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Foto Bukti – {{ $booking->ruangan->nama_ruangan }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0 text-center">
                <img src="{{ $booking->verifikasi->foto_bukti_url }}" alt="Foto Bukti" class="w-100">
            </div>
        </div>
    </div>
</div>
@endif

@endsection