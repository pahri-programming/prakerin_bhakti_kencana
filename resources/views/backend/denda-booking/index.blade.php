@extends('layouts.backend')
@section('title', 'Tindak Lanjut Denda Booking')

@section('content')
<div class="container-fluid px-4 py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">Tindak Lanjut Denda Booking</h2>
            <p class="text-muted mb-0">Kelola denda untuk ruangan yang rusak/kotor</p>
        </div>
        <a href="{{ route('backend.denda-booking.list') }}" class="btn btn-outline-primary">
            <i class="fas fa-list me-1"></i>Daftar Semua Denda
        </a>
    </div>

    {{-- Info --}}
    <div class="alert alert-info mb-4">
        <div class="d-flex align-items-start">
            <i class="fas fa-info-circle me-3 mt-1 fs-4"></i>
            <div>
                <h6 class="fw-bold mb-2">Cara Kerja:</h6>
                <ol class="mb-0 ps-3">
                    <li>PIC sudah verifikasi kondisi ruangan: <strong>Kotor</strong> atau <strong>Rusak</strong></li>
                    <li>Admin cek laporan → Klik <strong>"Tindak Lanjut"</strong></li>
                    <li>Tentukan nominal denda atau bebaskan user</li>
                    <li>User upload bukti transfer → Admin approve</li>
                </ol>
            </div>
        </div>
    </div>

    {{-- Filter --}}
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-body p-3">
            <form method="GET">
                <div class="row g-2">
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span>
                            <input type="text" name="search" class="form-control"
                                   placeholder="Cari nama ruangan/user..."
                                   value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select name="kondisi" class="form-select">
                            <option value="">Semua Kondisi</option>
                            <option value="kotor"  {{ request('kondisi') == 'kotor'  ? 'selected' : '' }}>Kotor</option>
                            <option value="rusak"  {{ request('kondisi') == 'rusak'  ? 'selected' : '' }}>Rusak</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="status_denda" class="form-select">
                            <option value="">Semua Status Denda</option>
                            <option value="belum_ada" {{ request('status_denda') == 'belum_ada' ? 'selected' : '' }}>Belum Ada Denda</option>
                            <option value="sudah_ada" {{ request('status_denda') == 'sudah_ada' ? 'selected' : '' }}>Sudah Ada Denda</option>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex gap-1">
                        <button type="submit" class="btn btn-primary flex-grow-1">
                            <i class="fas fa-filter me-1"></i>Filter
                        </button>
                        <a href="{{ route('backend.denda-booking.index') }}" class="btn btn-secondary">
                            <i class="fas fa-redo"></i>
                        </a>
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
                            <th>Tanggal Booking</th>
                            <th>Kondisi</th>
                            <th>PIC</th>
                            <th>Status Denda</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($verifikasi as $index => $item)
                        <tr>
                            <td class="px-4">{{ $verifikasi->firstItem() + $index }}</td>
                            <td>
                                <div class="fw-semibold">{{ $item->booking->ruangan->nama_ruangan ?? '-' }}</div>
                            </td>
                            <td>
                                <div class="fw-semibold">{{ $item->booking->user->name ?? '-' }}</div>
                                <small class="text-muted">{{ $item->booking->user->email ?? '' }}</small>
                            </td>
                            <td>
                                <i class="fas fa-calendar me-1 text-muted"></i>
                                {{ \Carbon\Carbon::parse($item->booking->tanggal)->format('d M Y') }}
                            </td>
                            <td>
                                @if($item->kondisi_ruangan == 'kotor')
                                    <span class="badge bg-warning text-dark">
                                        <i class="fas fa-broom me-1"></i>Kotor
                                    </span>
                                @else
                                    <span class="badge bg-danger">
                                        <i class="fas fa-tools me-1"></i>Rusak
                                    </span>
                                @endif
                            </td>
                            <td>
                                <small>{{ $item->pic->name ?? '-' }}</small>
                            </td>
                            <td>
                                @if($item->denda)
                                    <span class="badge {{ $item->denda->status_pembayaran_badge }}">
                                        {{ $item->denda->status_pembayaran_label }}
                                    </span>
                                    <br>
                                    <small class="text-muted">{{ $item->denda->jumlah_denda_format }}</small>
                                    @if($item->denda->isMenungguVerifikasi())
                                        <br><small class="text-warning fw-semibold">
                                            <i class="fas fa-exclamation-circle"></i> Perlu dicek
                                        </small>
                                    @endif
                                @else
                                    <span class="badge bg-secondary">Belum Ada Denda</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if(!$item->denda)
                                    <a href="{{ route('backend.denda-booking.tindak-lanjut', $item->id) }}"
                                       class="btn btn-sm btn-primary">
                                        <i class="fas fa-tasks me-1"></i>Tindak Lanjut
                                    </a>
                                @elseif($item->denda->isMenungguVerifikasi())
                                    <a href="{{ route('backend.denda-booking.tindak-lanjut', $item->id) }}"
                                       class="btn btn-sm btn-warning text-dark">
                                        <i class="fas fa-hourglass-half me-1"></i>Cek Bukti
                                    </a>
                                @else
                                    <a href="{{ route('backend.denda-booking.tindak-lanjut', $item->id) }}"
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
                                Tidak ada data yang perlu ditindaklanjuti
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($verifikasi->hasPages())
        <div class="card-footer bg-white">
            {{ $verifikasi->links() }}
        </div>
        @endif
    </div>
</div>
@endsection