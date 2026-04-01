@extends('layouts.backend')
@section('title', 'Daftar Semua Denda Booking')

@section('content')
<div class="container-fluid px-4 py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">Daftar Semua Denda Booking</h2>
            <p class="text-muted mb-0">Riwayat & tracking pembayaran denda booking ruangan</p>
        </div>
        <a href="{{ route('backend.denda-booking.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i>Kembali
        </a>
    </div>

    {{-- Stats --}}
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

    {{-- Filter --}}
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-body p-3">
            <form method="GET">
                <div class="row g-2">
                    <div class="col-md-5">
                        <input type="text" name="search" class="form-control"
                               placeholder="Cari nama ruangan/user..."
                               value="{{ request('search') }}">
                    </div>
                    <div class="col-md-4">
                        <select name="status_pembayaran" class="form-select">
                            <option value="">Semua Status</option>
                            <option value="belum_bayar"         {{ request('status_pembayaran') == 'belum_bayar'         ? 'selected' : '' }}>Belum Bayar</option>
                            <option value="menunggu_verifikasi" {{ request('status_pembayaran') == 'menunggu_verifikasi' ? 'selected' : '' }}>Menunggu Verifikasi</option>
                            <option value="sudah_bayar"         {{ request('status_pembayaran') == 'sudah_bayar'         ? 'selected' : '' }}>Sudah Bayar</option>
                            <option value="dibebaskan"          {{ request('status_pembayaran') == 'dibebaskan'          ? 'selected' : '' }}>Dibebaskan</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-filter me-1"></i>Filter
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Table --}}
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="px-4">No</th>
                            <th>Ruangan</th>
                            <th>User</th>
                            <th>Kondisi</th>
                            <th class="text-end">Jumlah Denda</th>
                            <th>Status</th>
                            <th>Admin</th>
                            <th>Tanggal</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($denda as $index => $item)
                        <tr>
                            <td class="px-4">{{ $denda->firstItem() + $index }}</td>
                            <td class="fw-semibold">{{ $item->booking->ruangan->nama_ruangan ?? '-' }}</td>
                            <td>{{ $item->booking->user->name ?? '-' }}</td>
                            <td>
                                @php $kondisi = $item->verifikasiBooking->kondisi_ruangan ?? '-' @endphp
                                @if($kondisi == 'kotor')
                                    <span class="badge bg-warning text-dark">Kotor</span>
                                @elseif($kondisi == 'rusak')
                                    <span class="badge bg-danger">Rusak</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-end fw-bold text-danger">
                                {{ $item->jumlah_denda_format }}
                            </td>
                            <td>
                                <span class="badge {{ $item->status_pembayaran_badge }}">
                                    {{ $item->status_pembayaran_label }}
                                </span>
                                @if($item->isMenungguVerifikasi())
                                    <br><small class="text-warning fw-semibold">
                                        <i class="fas fa-exclamation-circle"></i> Perlu dicek
                                    </small>
                                @endif
                            </td>
                            <td><small>{{ $item->admin->name ?? '-' }}</small></td>
                            <td>
                                <small>
                                    {{ $item->tanggal_tindakan ? $item->tanggal_tindakan->format('d M Y') : '-' }}
                                </small>
                            </td>
                            <td class="text-center">
                                <a href="{{ route('backend.denda-booking.tindak-lanjut', $item->verifikasi_booking_id) }}"
                                   class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted">
                                <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                Tidak ada data denda booking
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