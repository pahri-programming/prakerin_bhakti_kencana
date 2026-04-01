@extends('layouts.backend')
@section('title', 'Tindak Lanjut Verifikasi PIC')

@section('content')
    <div class="container-fluid px-4 py-4">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold mb-1">Tindak Lanjut Verifikasi PIC</h2>
                <p class="text-muted mb-0">Kelola denda untuk barang rusak/hilang hasil verifikasi PIC</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('backend.denda.list') }}" class="btn btn-outline-primary">
                    <i class="fas fa-list me-1"></i>Daftar Semua Denda
                </a>
                <a href="{{ route('backend.pengembalian.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i>Kembali
                </a>
            </div>
        </div>

        <!-- Alert Info -->
        <div class="alert alert-info mb-4">
            <div class="d-flex align-items-start">
                <i class="fas fa-info-circle me-3 mt-1 fs-4"></i>
                <div>
                    <h6 class="fw-bold mb-2">Cara Kerja Tindak Lanjut:</h6>
                    <ol class="mb-0 ps-3">
                        <li>PIC sudah verifikasi barang: <strong>Rusak Ringan / Rusak Berat / Hilang</strong></li>
                        <li>Admin cek hasil verifikasi → Klik <strong>"Tindak Lanjut"</strong></li>
                        <li>Sistem auto-calculate denda (bisa di-edit manual)</li>
                        <li>Pilih status: <span class="badge bg-danger">Belum Bayar</span> atau <span
                                class="badge bg-info">Dibebaskan</span></li>
                        <li>Jika belum bayar → Peminjam harus upload bukti pembayaran</li>
                    </ol>
                </div>
            </div>
        </div>

        <!-- Filter & Search -->
        <div class="card border-0 shadow-sm rounded-3 mb-4">
            <div class="card-body p-3">
                <form method="GET" action="{{ route('backend.denda.index') }}">
                    <div class="row g-2 align-items-center">
                        <!-- Search -->
                        <div class="col-md-4">
                            <div class="input-group">
                                <span class="input-group-text bg-white">
                                    <i class="fas fa-search text-muted"></i>
                                </span>
                                <input type="text" name="search" class="form-control"
                                    placeholder="Cari kode/nama peminjam..." value="{{ request('search') }}">
                            </div>
                        </div>

                        <!-- Filter Kondisi -->
                        <div class="col-md-3">
                            <select name="kondisi" class="form-select">
                                <option value="">Semua Kondisi</option>
                                <option value="rusak_ringan" {{ request('kondisi') == 'rusak_ringan' ? 'selected' : '' }}>
                                    Rusak Ringan
                                </option>
                                <option value="rusak_berat" {{ request('kondisi') == 'rusak_berat' ? 'selected' : '' }}>
                                    Rusak Berat
                                </option>
                                <option value="hilang" {{ request('kondisi') == 'hilang' ? 'selected' : '' }}>
                                    Hilang
                                </option>
                            </select>
                        </div>

                        <!-- Filter Status Denda -->
                        <div class="col-md-3">
                            <select name="status_denda" class="form-select">
                                <option value="">Semua Status Denda</option>
                                <option value="belum_ada" {{ request('status_denda') == 'belum_ada' ? 'selected' : '' }}>
                                    Belum Ada Denda
                                </option>
                                <option value="sudah_ada" {{ request('status_denda') == 'sudah_ada' ? 'selected' : '' }}>
                                    Sudah Ada Denda
                                </option>
                            </select>
                        </div>

                        <!-- Buttons -->
                        <div class="col-md-2">
                            <div class="d-flex gap-1">
                                <button type="submit" class="btn btn-primary flex-grow-1">
                                    <i class="fas fa-filter me-1"></i>Filter
                                </button>
                                <a href="{{ route('backend.denda.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-redo"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Table -->
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="px-4 py-3">No</th>
                                <th>Kode Peminjaman</th>
                                <th>Peminjam</th>
                                <th>Tanggal Kembali</th>
                                <th>Kondisi Verifikasi</th>
                                <th>PIC</th>
                                <th>Status Denda</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pengembalian as $index => $item)
                                <tr>
                                    <td class="px-4">{{ $pengembalian->firstItem() + $index }}</td>
                                    <td>
                                        <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2">
                                            {{ $item->peminjamanBarang->kode }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="fw-semibold">
                                            {{ $item->peminjamanBarang->nama_peminjam ?? $item->peminjamanBarang->user->name }}
                                        </div>
                                        @if ($item->peminjamanBarang->instansi)
                                            <small class="text-muted">{{ $item->peminjamanBarang->instansi }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <i class="fas fa-calendar me-1 text-muted"></i>
                                        {{ \Carbon\Carbon::parse($item->tanggal_kembali)->format('d M Y') }}
                                    </td>
                                    <td>
                                        @if ($item->verifikasi)
                                            @if ($item->verifikasi->kondisi == 'rusak_ringan')
                                                <span class="badge bg-warning px-3 py-2">
                                                    <i class="fas fa-exclamation-triangle me-1"></i>Rusak Ringan
                                                </span>
                                            @elseif($item->verifikasi->kondisi == 'rusak_berat')
                                                <span class="badge bg-danger px-3 py-2">
                                                    <i class="fas fa-times-circle me-1"></i>Rusak Berat
                                                </span>
                                            @else
                                                <span class="badge bg-dark px-3 py-2">
                                                    <i class="fas fa-ban me-1"></i>Hilang
                                                </span>
                                            @endif
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($item->verifikasi)
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-user-check text-success me-2"></i>
                                                <small>{{ $item->verifikasi->pic->name }}</small>
                                            </div>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($item->hasDenda())
                                            <div class="d-flex flex-column gap-1">
                                                <span class="badge {{ $item->denda->status_pembayaran_badge }} px-3 py-2">
                                                    {{ $item->denda->status_pembayaran_label }}
                                                </span>
                                                <small class="text-muted">
                                                    {{ $item->denda->jumlah_denda_format }}
                                                </small>
                                            </div>
                                        @else
                                            <span class="badge bg-secondary px-3 py-2">
                                                <i class="fas fa-clock me-1"></i>Belum Ada Denda
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if (!$item->hasDenda())
                                            <a href="{{ route('backend.denda.tindak-lanjut', $item->id) }}"
                                                class="btn btn-sm btn-primary">
                                                <i class="fas fa-tasks me-1"></i>Tindak Lanjut
                                            </a>
                                        @elseif($item->denda->status_pembayaran === 'menunggu_verifikasi')
                                            {{-- Ada bukti masuk, perlu diverifikasi --}}
                                            <a href="{{ route('backend.denda.tindak-lanjut', $item->id) }}"
                                                class="btn btn-sm btn-warning text-dark">
                                                <i class="fas fa-hourglass-half me-1"></i>Cek Bukti
                                            </a>
                                        @else
                                            <a href="{{ route('backend.denda.tindak-lanjut', $item->id) }}"
                                                class="btn btn-sm btn-info">
                                                <i class="fas fa-eye me-1"></i>Detail
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-5 text-muted">
                                        <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                        <p class="mb-0">Tidak ada data yang perlu ditindaklanjuti</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if ($pengembalian->hasPages())
                <div class="card-footer bg-white border-top">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted small">
                            Menampilkan {{ $pengembalian->firstItem() }} - {{ $pengembalian->lastItem() }}
                            dari {{ $pengembalian->total() }} data
                        </div>
                        {{ $pengembalian->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>

    <style>
        .table tbody tr:hover {
            background-color: #f8f9fa;
            transition: all 0.2s;
        }
    </style>
@endsection
