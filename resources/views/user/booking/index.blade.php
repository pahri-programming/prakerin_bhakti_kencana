@extends('layouts.frontend')
@section('title', 'Riwayat Booking Saya')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">

            {{-- Header --}}
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="fw-bold mb-0">Riwayat Booking Saya</h4>
                    <p class="text-muted small mb-0">Daftar pengajuan booking ruangan Anda</p>
                </div>
                <a href="{{ route('user.booking.create') }}" class="btn btn-primary px-4">
                    <i class="fas fa-plus me-2"></i>Booking Baru
                </a>
            </div>

            {{-- Alert --}}
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>{{ $errors->first() }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{-- Empty State --}}
            @if ($bookings->isEmpty())
                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-calendar-times fa-3x text-muted opacity-50 mb-3"></i>
                        <h6 class="fw-semibold text-muted">Belum ada booking</h6>
                        <p class="text-muted small mb-3">Anda belum pernah mengajukan booking ruangan.</p>
                        <a href="{{ route('user.booking.create') }}" class="btn btn-primary px-4">
                            <i class="fas fa-plus me-2"></i>Buat Booking Pertama
                        </a>
                    </div>
                </div>

            @else
                <div class="d-flex flex-column gap-3">
                    @foreach ($bookings as $b)
                        <div class="card border-0 shadow-sm rounded-3">
                            <div class="card-body p-4">
                                <div class="row align-items-center g-3">

                                    {{-- Info Utama --}}
                                    <div class="col-md-7">
                                        <div class="d-flex align-items-center gap-2 mb-2">
                                            <span class="badge
                                                @if($b->status === 'Pending')   bg-warning text-dark
                                                @elseif($b->status === 'Diterima') bg-success
                                                @elseif($b->status === 'Ditolak')  bg-danger
                                                @elseif($b->status === 'Selesai')  bg-secondary
                                                @endif
                                                px-3 py-1 rounded-pill">
                                                {{ $b->status }}
                                            </span>
                                            <small class="text-muted">{{ $b->kode ?? '-' }}</small>
                                        </div>

                                        <h6 class="fw-bold mb-1">
                                            <i class="fas fa-door-open me-2 text-primary"></i>
                                            {{ $b->ruangan->nama_ruangan ?? '-' }}
                                        </h6>

                                        <div class="text-muted small">
                                            <i class="fas fa-calendar me-1"></i>
                                            {{ $b->hari }}, {{ $b->tanggal_format }}
                                            &nbsp;|&nbsp;
                                            <i class="fas fa-clock me-1"></i>
                                            {{ \Carbon\Carbon::parse($b->waktu_mulai)->format('H:i') }}
                                            –
                                            {{ \Carbon\Carbon::parse($b->waktu_selesai)->format('H:i') }}
                                        </div>

                                        @if ($b->keterangan && $b->status === 'Ditolak')
                                            <div class="mt-2 text-danger small">
                                                <i class="fas fa-info-circle me-1"></i>
                                                Alasan: {{ $b->keterangan }}
                                            </div>
                                        @endif
                                    </div>

                                    {{-- Action --}}
                                    <div class="col-md-5 d-flex justify-content-md-end gap-2 flex-wrap">
                                        <a href="{{ route('user.booking.show', $b->id) }}"
                                            class="btn btn-outline-primary btn-sm px-3">
                                            <i class="fas fa-eye me-1"></i>Detail
                                        </a>

                                        @if ($b->status === 'Pending')
                                            <form action="{{ route('user.booking.destroy', $b->id) }}"
                                                method="POST"
                                                onsubmit="return confirm('Batalkan booking ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger btn-sm px-3">
                                                    <i class="fas fa-times me-1"></i>Batalkan
                                                </button>
                                            </form>
                                        @endif
                                    </div>

                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

        </div>
    </div>
</div>
@endsection