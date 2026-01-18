@extends('layouts.backend')
@section('title', 'Detail Peminjaman')

@section('content')
    <div class="container-fluid px-4 py-4">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <!-- Header -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h2 class="fw-bold mb-1">Detail Peminjaman</h2>
                        <p class="text-muted mb-0">Informasi lengkap peminjaman barang</p>
                    </div>
                </div>

                <!-- Main Card -->
                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-body p-4">
                        <!-- Kode & Status -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="text-muted small mb-1">Kode Peminjaman</label>
                                    <h4 class="fw-bold text-primary mb-0">{{ $peminjaman->kode }}</h4>
                                </div>
                            </div>
                            <div class="col-md-6 text-md-end">
                                <label class="text-muted small mb-1 d-block">Status</label>
                                @if ($peminjaman->status == 'menunggu')
                                    <span class="badge bg-warning px-4 py-2 fs-6">
                                        <i class="fas fa-clock me-1"></i>Menunggu Persetujuan
                                    </span>
                                @elseif($peminjaman->status == 'disetujui')
                                    <span class="badge bg-info px-4 py-2 fs-6">
                                        <i class="fas fa-check-circle me-1"></i>Disetujui
                                    </span>
                                @elseif($peminjaman->status == 'ditolak')
                                    <span class="badge bg-danger px-4 py-2 fs-6">
                                        <i class="fas fa-times-circle me-1"></i>Ditolak
                                    </span>
                                @else
                                    <span class="badge bg-success px-4 py-2 fs-6">
                                        <i class="fas fa-check-double me-1"></i>Dikembalikan
                                    </span>
                                @endif
                            </div>
                        </div>

                        <hr class="my-4">

                        <!-- Informasi Peminjam -->
                        <h5 class="fw-bold mb-3">
                            <i class="fas fa-user-circle me-2 text-primary"></i>Informasi Peminjam
                        </h5>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <div class="p-3 bg-light rounded">
                                    <label class="text-muted small mb-1">User/Akun</label>
                                    <div class="fw-semibold">{{ $peminjaman->user->name }}</div>
                                    <small class="text-muted">{{ $peminjaman->user->email }}</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-3 bg-light rounded">
                                    <label class="text-muted small mb-1">Nama Peminjam</label>
                                    <div class="fw-semibold">
                                        {{ $peminjaman->nama_peminjam ?? $peminjaman->user->name }}
                                    </div>
                                    @if ($peminjaman->nama_peminjam && $peminjaman->nama_peminjam != $peminjaman->user->name)
                                        <small class="text-info">
                                            <i class="fas fa-info-circle me-1"></i>Berbeda dari user
                                        </small>
                                    @endif
                                </div>
                            </div>
                            @if ($peminjaman->instansi)
                                <div class="col-md-12">
                                    <div class="p-3 bg-light rounded">
                                        <label class="text-muted small mb-1">Instansi/Organisasi</label>
                                        <div class="fw-semibold">{{ $peminjaman->instansi }}</div>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <hr class="my-4">

                        <!-- Barang yang Dipinjam -->
                        <h5 class="fw-bold mb-3">
                            <i class="fas fa-box me-2 text-primary"></i>Barang yang Dipinjam
                        </h5>
                        <div class="mb-4">
                            @foreach ($peminjaman->detailbarangs as $index => $detail)
                                <div class="card mb-3 border">
                                    <div class="card-body">
                                        <div class="row align-items-center">
                                            <div class="col-md-1 text-center">
                                                <div class="badge bg-primary rounded-circle p-3">
                                                    <h5 class="mb-0">{{ $index + 1 }}</h5>
                                                </div>
                                            </div>
                                            <div class="col-md-5">
                                                <h6 class="fw-bold mb-1">{{ $detail->barangRuangan->barang->nama ?? '-' }}
                                                </h6>
                                                <div class="text-muted small mb-1">
                                                    <i class="fas fa-tag me-1"></i>Kode:
                                                    {{ $detail->barangRuangan->barang->kode ?? '-' }}
                                                </div>
                                                <div class="text-muted small mb-1">
                                                    <i class="fas fa-layer-group me-1"></i>Kategori:
                                                    {{ $detail->barangRuangan->barang->kategori ?? '-' }}
                                                </div>
                                                <div class="text-muted small">
                                                    <i class="fas fa-info-circle me-1"></i>Deskripsi:
                                                    {{ $detail->barangRuangan->barang->keterangan ?? '-' }}
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="text-muted small mb-1">Ruangan</label>
                                                <div class="fw-semibold mb-2">
                                                    <i class="fas fa-map-marker-alt me-1 text-danger"></i>
                                                    {{ $detail->barangRuangan->ruangan->nama_ruangan ?? '-' }}
                                                </div>
                                                <label class="text-muted small mb-1">Lokasi Detail</label>
                                                <div class="text-muted small">
                                                    <i class="fas fa-location-arrow me-1"></i>
                                                    {{ $detail->barangRuangan->ruangan->lokasi ?? '-' }}
                                                </div>
                                            </div>
                                            <div class="col-md-3 text-end">
                                                <label class="text-muted small mb-1">Jumlah</label>
                                                <h4 class="mb-0 text-primary">
                                                    <span class="badge bg-primary px-3 py-2">{{ $detail->jumlah }}
                                                        Unit</span>
                                                </h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <hr class="my-4">

                        <!-- Informasi Tanggal -->
                        <h5 class="fw-bold mb-3">
                            <i class="fas fa-calendar-alt me-2 text-primary"></i>Jadwal Peminjaman
                        </h5>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <div class="p-3 bg-light rounded">
                                    <label class="text-muted small mb-1">Tanggal Pinjam</label>
                                    <div class="fw-bold text-dark fs-5">
                                        <i class="far fa-calendar-check me-2 text-success"></i>
                                        {{ $peminjaman->tanggal_pinjam_format }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-3 bg-light rounded">
                                    <label class="text-muted small mb-1">Tanggal Kembali</label>
                                    <div class="fw-bold text-dark fs-5">
                                        <i class="far fa-calendar-times me-2 text-danger"></i>
                                        {{ $peminjaman->tanggal_kembali_format }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="p-3 bg-info bg-opacity-10 border border-info rounded">
                                    @php
                                        $tanggalPinjam = \Carbon\Carbon::parse($peminjaman->tanggal_pinjam);
                                        $tanggalKembali = \Carbon\Carbon::parse($peminjaman->tanggal_kembali);
                                        $durasi = $tanggalPinjam->diffInDays($tanggalKembali);
                                    @endphp
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-hourglass-half me-2 text-info fs-4"></i>
                                        <div>
                                            <label class="text-muted small mb-0">Durasi Peminjaman</label>
                                            <div class="fw-bold text-dark">{{ $durasi }} Hari</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Tambahkan di bagian action card --}}
                        @if ($peminjaman->status == 'disetujui' && !$peminjaman->hasReturn())
                            <a href="{{ route('backend.pengembalian.create', ['peminjaman_id' => $peminjaman->id]) }}"
                                class="btn btn-success w-100 mb-2">
                                <i class="fas fa-undo me-2"></i>Buat Pengembalian
                            </a>
                        @endif

                        @if ($peminjaman->hasReturn())
                            <a href="{{ route('backend.pengembalian.show', $peminjaman->pengembalianBarang->id) }}"
                                class="btn btn-info w-100 mb-2">
                                <i class="fas fa-eye me-2"></i>Lihat Pengembalian
                            </a>
                        @endif

                        <!-- Keterangan -->
                        @if ($peminjaman->keterangan)
                            <hr class="my-4">
                            <h5 class="fw-bold mb-3">
                                <i class="fas fa-sticky-note me-2 text-primary"></i>Keterangan
                            </h5>
                            <div class="p-3 bg-light rounded">
                                <p class="mb-0">{{ $peminjaman->keterangan }}</p>
                            </div>
                        @endif

                        <!-- Timestamp -->
                        <hr class="my-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="small text-muted">
                                    <i class="fas fa-clock me-1"></i>Dibuat:
                                    {{ $peminjaman->created_at->translatedFormat('d F Y H:i') }}
                                </div>
                            </div>
                            <div class="col-md-6 text-md-end">
                                <div class="small text-muted">
                                    <i class="fas fa-edit me-1"></i>Terakhir diupdate:
                                    {{ $peminjaman->updated_at->translatedFormat('d F Y H:i') }}
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <hr class="my-4">
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('backend.peminjaman.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Kembali ke List
                            </a>
                            <div class="d-flex gap-2">
                                <a href="{{ route('backend.peminjaman.edit', $peminjaman->id) }}"
                                    class="btn btn-warning">
                                    <i class="fas fa-edit me-2"></i>Edit Data
                                </a>
                                <form action="{{ route('backend.peminjaman.destroy', $peminjaman->id) }}" method="POST"
                                    class="d-inline" onsubmit="return confirm('Yakin ingin menghapus peminjaman ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">
                                        <i class="fas fa-trash me-2"></i>Hapus
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .badge.rounded-circle {
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .card {
            transition: all 0.3s ease;
        }

        .card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
    </style>
@endsection
