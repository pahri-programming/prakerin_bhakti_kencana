@extends('layouts.frontend')
@section('title', 'Peminjaman Saya')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">

            {{-- Header --}}
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="fw-bold mb-0">Peminjaman Saya</h4>
                    <p class="text-muted small mb-0">Riwayat pengajuan peminjaman barang</p>
                </div>
                <a href="{{ route('user.peminjaman.create') }}" class="btn btn-primary px-4">
                    <i class="fas fa-plus me-2"></i>Ajukan Peminjaman
                </a>
            </div>

            {{-- Alert --}}
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show mb-4 border-0 shadow-sm" role="alert">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show mb-4 border-0 shadow-sm" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>{{ $errors->first() }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{-- List --}}
            @forelse ($peminjamans as $item)
                <div class="card border-0 shadow-sm rounded-3 mb-3">
                    <div class="card-body p-4">
                        <div class="row align-items-start">

                            <div class="col-md-8">
                                {{-- Kode & Status --}}
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <span class="badge bg-primary bg-opacity-10 text-primary fw-semibold px-3 py-2">
                                        {{ $item->kode }}
                                    </span>
                                    @if ($item->status == 'menunggu')
                                        <span class="badge bg-warning text-dark px-3 py-2">
                                            <i class="fas fa-clock me-1"></i>Menunggu
                                        </span>
                                    @elseif ($item->status == 'disetujui')
                                        <span class="badge bg-success px-3 py-2">
                                            <i class="fas fa-check-circle me-1"></i>Disetujui
                                        </span>
                                    @elseif ($item->status == 'ditolak')
                                        <span class="badge bg-danger px-3 py-2">
                                            <i class="fas fa-times-circle me-1"></i>Ditolak
                                        </span>
                                    @else
                                        <span class="badge bg-secondary px-3 py-2">
                                            <i class="fas fa-check-double me-1"></i>Dikembalikan
                                        </span>
                                    @endif
                                </div>

                                {{-- Barang --}}
                                <div class="mb-2">
                                    @foreach ($item->detailbarangs->take(2) as $detail)
                                        <span class="badge bg-light text-dark border me-1 mb-1 px-2 py-1">
                                            <i class="fas fa-box me-1 text-muted"></i>
                                            {{ $detail->barangRuangan->barang->nama ?? '-' }}
                                            <span class="text-muted">({{ $detail->jumlah }} unit)</span>
                                        </span>
                                    @endforeach
                                    @if ($item->detailbarangs->count() > 2)
                                        <span class="text-muted small">+{{ $item->detailbarangs->count() - 2 }} lainnya</span>
                                    @endif
                                </div>

                                {{-- Tanggal --}}
                                <div class="text-muted small">
                                    <i class="far fa-calendar me-1"></i>
                                    {{ \Carbon\Carbon::parse($item->tanggal_pinjam)->format('d M Y') }}
                                    <span class="mx-1">→</span>
                                    {{ \Carbon\Carbon::parse($item->tanggal_kembali)->format('d M Y') }}
                                    <span class="ms-2 badge bg-light text-muted border">
                                        {{ \Carbon\Carbon::parse($item->tanggal_pinjam)->diffInDays($item->tanggal_kembali) }} hari
                                    </span>
                                </div>

                                {{-- Alasan tolak --}}
                                @if ($item->status == 'ditolak' && $item->alasan_tolak)
                                    <div class="mt-2 p-2 bg-danger bg-opacity-10 rounded-2">
                                        <small class="text-danger">
                                            <i class="fas fa-info-circle me-1"></i>
                                            <strong>Alasan penolakan:</strong> {{ $item->alasan_tolak }}
                                        </small>
                                    </div>
                                @endif
                            </div>

                            {{-- Aksi --}}
                            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                                <div class="d-flex gap-2 justify-content-md-end">
                                    <a href="{{ route('user.peminjaman.show', $item->id) }}"
                                        class="btn btn-sm btn-outline-primary px-3">
                                        <i class="fas fa-eye me-1"></i>Detail
                                    </a>
                                    @if ($item->status == 'menunggu')
                                        <form action="{{ route('user.peminjaman.destroy', $item->id) }}"
                                            method="POST" class="d-inline"
                                            onsubmit="return confirm('Yakin ingin membatalkan peminjaman ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger px-3">
                                                <i class="fas fa-times me-1"></i>Batalkan
                                            </button>
                                        </form>
                                    @endif
                                </div>
                                <div class="text-muted mt-2" style="font-size:11px;">
                                    Diajukan {{ \Carbon\Carbon::parse($item->created_at)->diffForHumans() }}
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            @empty
                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-box-open fa-3x text-muted opacity-50 mb-3"></i>
                        <h6 class="text-muted">Belum Ada Peminjaman</h6>
                        <p class="text-muted small mb-4">Kamu belum pernah mengajukan peminjaman barang.</p>
                        <a href="{{ route('user.peminjaman.create') }}" class="btn btn-primary px-4">
                            <i class="fas fa-plus me-2"></i>Ajukan Sekarang
                        </a>
                    </div>
                </div>
            @endforelse

        </div>
    </div>
</div>
@endsection