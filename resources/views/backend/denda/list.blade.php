@extends('layouts.backend')
@section('title', 'Daftar Semua Denda')

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">Daftar Semua Denda Pengembalian</h2>
            <p class="text-muted mb-0">Riwayat & tracking pembayaran denda</p>
        </div>
        <a href="{{ route('backend.denda.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i>Kembali
        </a>
    </div>

    <!-- Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-3 bg-primary text-white">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="opacity-75">Total Denda</small>
                            <h4 class="mb-0 fw-bold">Rp {{ number_format($stats['total_denda'], 0, ',', '.') }}</h4>
                        </div>
                        <i class="fas fa-money-bill-wave fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-3 bg-danger text-white">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="opacity-75">Belum Bayar</small>
                            <h4 class="mb-0 fw-bold">Rp {{ number_format($stats['belum_bayar'], 0, ',', '.') }}</h4>
                        </div>
                        <i class="fas fa-clock fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- ← BARU: Menunggu Verifikasi --}}
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-3 bg-warning text-dark">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="opacity-75">Menunggu Verifikasi</small>
                            <h4 class="mb-0 fw-bold">{{ $stats['menunggu_verifikasi'] }} tagihan</h4>
                        </div>
                        <i class="fas fa-hourglass-half fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-3 bg-success text-white">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="opacity-75">Sudah Bayar</small>
                            <h4 class="mb-0 fw-bold">Rp {{ number_format($stats['sudah_bayar'], 0, ',', '.') }}</h4>
                        </div>
                        <i class="fas fa-check-circle fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter -->
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-body p-3">
            <form method="GET">
                <div class="row g-2">
                    <div class="col-md-4">
                        <input type="text" name="search" class="form-control"
                               placeholder="Cari kode/peminjam..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-3">
                        <select name="status_pembayaran" class="form-select">
                            <option value="">Semua Status</option>
                            <option value="belum_bayar"         {{ request('status_pembayaran') == 'belum_bayar'         ? 'selected' : '' }}>Belum Bayar</option>
                            <option value="menunggu_verifikasi" {{ request('status_pembayaran') == 'menunggu_verifikasi' ? 'selected' : '' }}>Menunggu Verifikasi</option> {{-- ← BARU --}}
                            <option value="sudah_bayar"         {{ request('status_pembayaran') == 'sudah_bayar'         ? 'selected' : '' }}>Sudah Bayar</option>
                            <option value="dibebaskan"          {{ request('status_pembayaran') == 'dibebaskan'          ? 'selected' : '' }}>Dibebaskan</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <input type="date" name="tanggal_tindakan" class="form-control" value="{{ request('tanggal_tindakan') }}">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-filter me-1"></i>Filter
                        </button>
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
                            <th class="px-4">No</th>
                            <th>Kode Peminjaman</th>
                            <th>Peminjam</th>
                            <th>Kondisi</th>
                            <th class="text-end">Jumlah Denda</th>
                            <th>Status Bayar</th>
                            <th>Admin</th>
                            <th>Tanggal</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($denda as $index => $item)
                        <tr>
                            <td class="px-4">{{ $denda->firstItem() + $index }}</td>
                            <td>
                                <span class="badge bg-primary bg-opacity-10 text-primary">
                                    {{ $item->pengembalianBarang->peminjamanBarang->kode }}
                                </span>
                            </td>
                            <td>
                                <div class="fw-semibold">
                                    {{ $item->pengembalianBarang->peminjamanBarang->nama_peminjam ?? $item->pengembalianBarang->peminjamanBarang->user->name }}
                                </div>
                            </td>
                            <td>
                                @php $kondisi = $item->verifikasiPengembalian->kondisi @endphp
                                @if($kondisi == 'rusak_ringan')
                                    <span class="badge bg-warning text-dark">Rusak Ringan</span>
                                @elseif($kondisi == 'rusak_berat')
                                    <span class="badge bg-danger">Rusak Berat</span>
                                @else
                                    <span class="badge bg-dark">Hilang</span>
                                @endif
                            </td>
                            <td class="text-end fw-bold text-danger">
                                {{ $item->jumlah_denda_format }}
                            </td>
                            <td>
                                {{-- ← badge sekarang cover semua status termasuk menunggu_verifikasi --}}
                                <span class="badge {{ $item->status_pembayaran_badge }}">
                                    {{ $item->status_pembayaran_label }}
                                </span>

                                {{-- ← notif kecil kalau butuh aksi admin --}}
                                @if($item->status_pembayaran === 'menunggu_verifikasi')
                                    <br><small class="text-warning fw-semibold">
                                        <i class="fas fa-exclamation-circle"></i> Perlu dicek
                                    </small>
                                @endif
                            </td>
                            <td>
                                <small>{{ $item->admin->name }}</small>
                            </td>
                            <td>
                                <small>{{ $item->tanggal_tindakan->format('d M Y') }}</small>
                            </td>
                            <td class="text-center">
                                <a href="{{ route('backend.pengembalian.show', $item->pengembalian_barang_id) }}"
                                   class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted">
                                <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                Tidak ada data denda
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($denda->hasPages())
        <div class="card-footer bg-white">
            {{ $denda->links() }}
        </div>
        @endif
    </div>
</div>
@endsection