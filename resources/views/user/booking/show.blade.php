@extends('layouts.frontend')
@section('title', 'Detail Booking')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-7">

                {{-- Header --}}
                <div class="mb-4">
                    <a href="{{ route('user.booking.index') }}" class="text-muted text-decoration-none small">
                        <i class="fas fa-arrow-left me-1"></i>Kembali ke Riwayat
                    </a>
                    <h4 class="fw-bold mt-2 mb-0">Detail Booking</h4>
                    <p class="text-muted small">Informasi lengkap pengajuan booking Anda</p>
                </div>

                {{-- Alert --}}
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-body p-4">

                        {{-- Status Badge --}}
                        <div class="d-flex justify-content-between align-items-start mb-4">
                            <div>
                                <p class="text-muted small mb-1">Kode Booking</p>
                                <h5 class="fw-bold mb-0">{{ $booking->kode ?? '-' }}</h5>
                            </div>
                            <span
                                class="badge fs-6 px-3 py-2 rounded-pill
                            @if ($booking->status === 'Pending') bg-warning text-dark
                            @elseif($booking->status === 'Diterima') bg-success
                            @elseif($booking->status === 'Ditolak')  bg-danger
                            @elseif($booking->status === 'Selesai')  bg-secondary @endif">
                                {{ $booking->status }}
                            </span>
                        </div>

                        <hr class="my-3">

                        {{-- Detail Info --}}
                        <div class="row g-3">
                            <div class="col-12">
                                <p class="text-muted small mb-1">Ruangan</p>
                                <p class="fw-semibold mb-0">
                                    <i class="fas fa-door-open me-2 text-primary"></i>
                                    {{ $booking->ruangan->nama_ruangan ?? '-' }}
                                </p>
                            </div>

                            <div class="col-md-6">
                                <p class="text-muted small mb-1">Hari & Tanggal</p>
                                <p class="fw-semibold mb-0">
                                    <i class="fas fa-calendar me-2 text-primary"></i>
                                    {{ $booking->hari }}, {{ $booking->tanggal_format }}
                                </p>
                            </div>

                            <div class="col-md-6">
                                <p class="text-muted small mb-1">Waktu</p>
                                <p class="fw-semibold mb-0">
                                    <i class="fas fa-clock me-2 text-primary"></i>
                                    {{ \Carbon\Carbon::parse($booking->waktu_mulai)->format('H:i') }}
                                    –
                                    {{ \Carbon\Carbon::parse($booking->waktu_selesai)->format('H:i') }}
                                </p>
                            </div>

                            <div class="col-md-6">
                                <p class="text-muted small mb-1">Tanggal Pengajuan</p>
                                <p class="fw-semibold mb-0">
                                    <i class="fas fa-history me-2 text-muted"></i>
                                    {{ $booking->created_at->translatedFormat('d F Y, H:i') }}
                                </p>
                            </div>

                            {{-- Keterangan User (bukan alasan tolak) --}}
                            @if ($booking->keterangan && $booking->status !== 'Ditolak')
                                <div class="col-12">
                                    <p class="text-muted small mb-1">Keperluan Booking</p>
                                    <div class="alert alert-light border py-2 mb-0">
                                        <i class="fas fa-sticky-note me-2 text-muted"></i>{{ $booking->keterangan }}
                                    </div>
                                </div>
                            @endif

                            {{-- Alasan Tolak tetap seperti semula --}}
                            @if ($booking->keterangan && $booking->status === 'Ditolak')
                                <div class="col-12">
                                    <p class="text-muted small mb-1">Alasan Penolakan</p>
                                    <div class="alert alert-danger py-2 mb-0">
                                        <i class="fas fa-info-circle me-2"></i>{{ $booking->keterangan }}
                                    </div>
                                </div>
                            @endif

                            @if ($booking->keterangan)
                                <div class="col-12">
                                    <p class="text-muted small mb-1">
                                        @if ($booking->status === 'Ditolak')
                                            Alasan Penolakan
                                        @else
                                            Keterangan
                                        @endif
                                    </p>
                                    <div
                                        class="alert {{ $booking->status === 'Ditolak' ? 'alert-danger' : 'alert-info' }} py-2 mb-0">
                                        <i class="fas fa-info-circle me-2"></i>{{ $booking->keterangan }}
                                    </div>
                                </div>
                            @endif
                        </div>

                        <hr class="my-4">

                        {{-- Action --}}
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('user.booking.index') }}" class="btn btn-outline-secondary px-4">
                                <i class="fas fa-arrow-left me-1"></i>Kembali
                            </a>

                            @if ($booking->status === 'Pending')
                                <form action="{{ route('user.booking.destroy', $booking->id) }}" method="POST"
                                    onsubmit="return confirm('Yakin ingin membatalkan booking ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger px-4">
                                        <i class="fas fa-times me-2"></i>Batalkan Booking
                                    </button>
                                </form>
                            @endif
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
