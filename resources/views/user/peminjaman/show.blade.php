@extends('layouts.frontend')
@section('title', 'Detail Peminjaman')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-9">

            {{-- Header --}}
            <div class="mb-4">
                <a href="{{ route('user.peminjaman.index') }}" class="text-muted text-decoration-none small">
                    <i class="fas fa-arrow-left me-1"></i>Kembali ke Daftar
                </a>
                <h4 class="fw-bold mt-2 mb-0">Detail Peminjaman</h4>
                <p class="text-muted small">Informasi lengkap pengajuan peminjaman barang</p>
            </div>

            {{-- Alert --}}
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show mb-4 border-0 shadow-sm">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{-- Status Card --}}
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <h5 class="fw-bold text-primary mb-0">{{ $peminjaman->kode }}</h5>
                                @if ($peminjaman->status == 'menunggu')
                                    <span class="badge bg-warning text-dark px-3 py-2">
                                        <i class="fas fa-clock me-1"></i>Menunggu Persetujuan
                                    </span>
                                @elseif ($peminjaman->status == 'disetujui')
                                    <span class="badge bg-success px-3 py-2">
                                        <i class="fas fa-check-circle me-1"></i>Disetujui
                                    </span>
                                @elseif ($peminjaman->status == 'ditolak')
                                    <span class="badge bg-danger px-3 py-2">
                                        <i class="fas fa-times-circle me-1"></i>Ditolak
                                    </span>
                                @else
                                    <span class="badge bg-secondary px-3 py-2">
                                        <i class="fas fa-check-double me-1"></i>Dikembalikan
                                    </span>
                                @endif
                            </div>
                            <div class="text-muted small">
                                <i class="fas fa-clock me-1"></i>
                                Diajukan {{ \Carbon\Carbon::parse($peminjaman->created_at)->translatedFormat('d F Y, H:i') }} WIB
                            </div>
                        </div>
                        <div class="col-md-4 text-md-end mt-3 mt-md-0">
                            @if ($peminjaman->status == 'menunggu')
                                <form action="{{ route('user.peminjaman.destroy', $peminjaman->id) }}"
                                    method="POST"
                                    onsubmit="return confirm('Yakin ingin membatalkan peminjaman ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger px-4">
                                        <i class="fas fa-times me-1"></i>Batalkan Peminjaman
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>

                    {{-- Alasan tolak --}}
                    @if ($peminjaman->status == 'ditolak' && $peminjaman->alasan_tolak)
                        <div class="mt-3 p-3 bg-danger bg-opacity-10 rounded-3 border border-danger border-opacity-25">
                            <div class="fw-semibold text-danger mb-1 small">
                                <i class="fas fa-info-circle me-1"></i>Alasan Penolakan
                            </div>
                            <div class="text-danger">{{ $peminjaman->alasan_tolak }}</div>
                        </div>
                    @endif

                    @if ($peminjaman->status == 'menunggu')
                        <div class="mt-3 p-3 bg-warning bg-opacity-10 rounded-3 border border-warning border-opacity-25">
                            <small class="text-warning-emphasis">
                                <i class="fas fa-info-circle me-1"></i>
                                Peminjaman kamu sedang menunggu persetujuan admin. Kamu akan mendapat notifikasi setelah diproses.
                            </small>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Barang yang Dipinjam --}}
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-white border-bottom py-3 px-4">
                    <h6 class="fw-bold mb-0">
                        <i class="fas fa-boxes me-2 text-primary"></i>Barang yang Dipinjam
                    </h6>
                </div>
                <div class="card-body p-0">
                    @foreach ($peminjaman->detailbarangs as $index => $detail)
                        <div class="p-4 {{ !$loop->last ? 'border-bottom' : '' }}">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center fw-bold"
                                        style="width:36px;height:36px;font-size:14px;">
                                        {{ $index + 1 }}
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="fw-semibold">
                                        {{ $detail->barangRuangan->barang->nama ?? '-' }}
                                    </div>
                                    <div class="text-muted small">
                                        <i class="fas fa-map-marker-alt me-1"></i>
                                        {{ $detail->barangRuangan->ruangan->nama_ruangan ?? '-' }}
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <span class="badge bg-primary px-3 py-2">
                                        {{ $detail->jumlah }} Unit
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Jadwal --}}
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-white border-bottom py-3 px-4">
                    <h6 class="fw-bold mb-0">
                        <i class="fas fa-calendar-alt me-2 text-primary"></i>Jadwal Peminjaman
                    </h6>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="p-3 bg-light rounded-3 text-center">
                                <div class="text-muted small mb-1">Tanggal Pinjam</div>
                                <div class="fw-bold">
                                    {{ \Carbon\Carbon::parse($peminjaman->tanggal_pinjam)->translatedFormat('d F Y') }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 bg-light rounded-3 text-center">
                                <div class="text-muted small mb-1">Tanggal Kembali</div>
                                <div class="fw-bold">
                                    {{ \Carbon\Carbon::parse($peminjaman->tanggal_kembali)->translatedFormat('d F Y') }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 bg-primary bg-opacity-10 rounded-3 text-center">
                                <div class="text-muted small mb-1">Durasi</div>
                                <div class="fw-bold text-primary">
                                    {{ \Carbon\Carbon::parse($peminjaman->tanggal_pinjam)->diffInDays($peminjaman->tanggal_kembali) }} Hari
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Keterangan --}}
            @if ($peminjaman->keterangan)
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-header bg-white border-bottom py-3 px-4">
                        <h6 class="fw-bold mb-0">
                            <i class="fas fa-sticky-note me-2 text-primary"></i>Keterangan
                        </h6>
                    </div>
                    <div class="card-body p-4">
                        <p class="mb-0 text-muted">{{ $peminjaman->keterangan }}</p>
                    </div>
                </div>
            @endif

        </div>
    </div>
</div>
@endsection