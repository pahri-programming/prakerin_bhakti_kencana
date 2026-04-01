@extends('layouts.backend')
@section('title', 'Data Peminjaman Barang')

@section('content')
    <div class="container-fluid px-4 py-4">
        <!-- Header Section -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold mb-1">Peminjaman Barang</h2>
                <p class="text-muted mb-0">Kelola data peminjaman barang</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('backend.peminjaman.pinjampdf') }}" class="btn btn-danger">
                    <i class="fas fa-file-pdf me-2"></i>Export PDF
                </a>
                <a href="{{ route('backend.peminjaman.create') }}" class="btn btn-primary px-4">
                    <i class="fas fa-plus me-2"></i>Tambah Peminjaman
                </a>
            </div>
        </div>

        <!-- Main Card -->
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-body p-0">
                <!-- Search & Filter Section -->
                <div class="p-4 border-bottom bg-light">
                    <form action="{{ route('backend.peminjaman.index') }}" method="GET">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0">
                                        <i class="fas fa-search text-muted"></i>
                                    </span>
                                    <input type="text" name="search" class="form-control border-start-0 ps-0"
                                        placeholder="Cari peminjam, instansi, atau kode..." value="{{ request('search') }}">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <input type="date" name="tanggal_pinjam" class="form-control"
                                    value="{{ request('tanggal_pinjam') }}">
                            </div>
                            <div class="col-md-2">
                                <input type="date" name="tanggal_kembali" class="form-control"
                                    value="{{ request('tanggal_kembali') }}">
                            </div>
                            <div class="col-md-2">
                                <select name="status" class="form-select">
                                    <option value="">Semua Status</option>
                                    <option value="menunggu" {{ request('status') == 'menunggu' ? 'selected' : '' }}>
                                        Menunggu</option>
                                    <option value="disetujui" {{ request('status') == 'disetujui' ? 'selected' : '' }}>
                                        Disetujui</option>
                                    <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>Ditolak
                                    </option>
                                    <option value="dikembalikan"
                                        {{ request('status') == 'dikembalikan' ? 'selected' : '' }}>Dikembalikan</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-filter me-1"></i>Filter
                                </button>
                            </div>
                        </div>
                    </form>

                    @if (request()->hasAny(['search', 'tanggal_pinjam', 'tanggal_kembali', 'status']))
                        <div class="mt-3">
                            <a href="{{ route('backend.peminjaman.index') }}" class="btn btn-sm btn-outline-secondary">
                                <i class="fas fa-times me-1"></i>Reset Filter
                            </a>
                        </div>
                    @endif
                </div>

                <!-- Table Section -->
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="px-4 py-3 text-muted fw-semibold" width="4%">No</th>
                                <th class="py-3 text-muted fw-semibold" width="10%">Kode</th>
                                <th class="py-3 text-muted fw-semibold" width="12%">Peminjam</th>
                                <th class="py-3 text-muted fw-semibold" width="18%">Barang</th>
                                <th class="py-3 text-muted fw-semibold" width="11%">Tanggal</th>
                                <th class="py-3 text-muted fw-semibold" width="10%">Keterangan</th>
                                <th class="py-3 text-muted fw-semibold" width="9%">Status</th>
                                <th class="py-3 text-muted fw-semibold text-center" width="26%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($peminjaman as $index => $item)
                                <tr class="border-bottom">
                                    <td class="px-4">
                                        <span class="text-muted">{{ $peminjaman->firstItem() + $index }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary bg-opacity-10 text-primary px-2 py-2 small">
                                            {{ $item->kode }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="fw-semibold">{{ $item->nama_peminjam ?? $item->user->name }}</div>
                                        @if ($item->instansi)
                                            <small class="text-muted">{{ $item->instansi }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        @foreach ($item->detailbarangs as $detail)
                                            <div class="mb-1">
                                                <span class="badge bg-success bg-opacity-10 text-success">
                                                    {{ $detail->barangRuangan->barang->nama ?? '-' }}
                                                </span>
                                                <small class="text-muted">({{ $detail->jumlah }} unit)</small>
                                                <br>
                                                <small class="text-muted">
                                                    <i class="fas fa-map-marker-alt me-1"></i>
                                                    {{ $detail->barangRuangan->ruangan->nama_ruangan ?? '-' }}
                                                </small>
                                            </div>
                                        @endforeach
                                    </td>
                                    <td>
                                        <div class="small">
                                            <div><i
                                                    class="far fa-calendar me-1"></i>{{ \Carbon\Carbon::parse($item->tanggal_pinjam)->format('d M Y') }}
                                            </div>
                                            <div class="text-muted">s/d
                                                {{ \Carbon\Carbon::parse($item->tanggal_kembali)->format('d M Y') }}</div>
                                        </div>
                                    </td>
                                    <td>
                                        @if ($item->keterangan)
                                            <div class="small text-muted" data-bs-toggle="tooltip" data-bs-placement="top"
                                                title="{{ $item->keterangan }}"
                                                style="max-width:130px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;cursor:help;">
                                                <i
                                                    class="fas fa-info-circle me-1"></i>{{ Str::limit($item->keterangan, 20) }}
                                            </div>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($item->status == 'menunggu')
                                            <span class="badge bg-warning px-2 py-2">
                                                <i class="fas fa-clock me-1"></i>Menunggu
                                            </span>
                                        @elseif($item->status == 'disetujui')
                                            <span class="badge bg-info px-2 py-2">
                                                <i class="fas fa-check-circle me-1"></i>Disetujui
                                            </span>
                                        @elseif($item->status == 'ditolak')
                                            <span class="badge bg-danger px-2 py-2" data-bs-toggle="tooltip"
                                                title="{{ $item->alasan_tolak ?? '' }}">
                                                <i class="fas fa-times-circle me-1"></i>Ditolak
                                            </span>
                                        @else
                                            <span class="badge bg-success px-2 py-2">
                                                <i class="fas fa-check-double me-1"></i>Dikembalikan
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column gap-1 align-items-start">

                                            {{-- Tombol Setujui & Tolak — hanya muncul jika menunggu --}}
                                            @if ($item->status == 'menunggu')
                                                <div class="d-flex gap-1">
                                                    <form
                                                        action="{{ route('backend.peminjaman.updateStatus', $item->id) }}"
                                                        method="POST" class="d-inline">
                                                        @csrf
                                                        @method('PATCH')
                                                        <input type="hidden" name="status" value="disetujui">
                                                        <button type="submit" class="btn btn-success btn-sm px-3"
                                                            onclick="return confirm('Setujui peminjaman {{ $item->kode }}?')">
                                                            <i class="fas fa-check me-1"></i>Setujui
                                                        </button>
                                                    </form>
                                                    <button type="button" class="btn btn-danger btn-sm px-3"
                                                        onclick="openTolakModal({{ $item->id }}, '{{ $item->kode }}')">
                                                        <i class="fas fa-times me-1"></i>Tolak
                                                    </button>
                                                </div>
                                            @endif

                                            {{-- Tombol ikon — selalu tampil --}}
                                            <div class="d-flex gap-1 {{ $item->status == 'menunggu' ? 'pt-1 border-top' : '' }}"
                                                style="border-color: #dee2e6 !important;">
                                                <a href="{{ route('backend.peminjaman.show', $item->id) }}"
                                                    class="btn btn-sm btn-light border" data-bs-toggle="tooltip"
                                                    title="Detail">
                                                    <i class="fas fa-eye text-info" style="font-size:13px;"></i>
                                                </a>
                                                <a href="{{ route('backend.peminjaman.edit', $item->id) }}"
                                                    class="btn btn-sm btn-light border" data-bs-toggle="tooltip"
                                                    title="Edit">
                                                    <i class="fas fa-edit text-warning" style="font-size:13px;"></i>
                                                </a>
                                                <form action="{{ route('backend.peminjaman.destroy', $item->id) }}"
                                                    method="POST" class="d-inline"
                                                    onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-light border"
                                                        data-bs-toggle="tooltip" title="Hapus">
                                                        <i class="fas fa-trash text-danger" style="font-size:13px;"></i>
                                                    </button>
                                                </form>
                                            </div>

                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-5">
                                        <div class="empty-state">
                                            <div class="mb-3">
                                                <i class="fas fa-clipboard-list fa-4x text-muted opacity-50"></i>
                                            </div>
                                            <h5 class="text-muted">Belum Ada Data</h5>
                                            <p class="text-muted mb-4">
                                                @if (request()->hasAny(['search', 'tanggal_pinjam', 'tanggal_kembali', 'status']))
                                                    Data tidak ditemukan dengan filter yang dipilih
                                                @else
                                                    Data peminjaman belum tersedia
                                                @endif
                                            </p>
                                            @if (!request()->hasAny(['search', 'tanggal_pinjam', 'tanggal_kembali', 'status']))
                                                <a href="{{ route('backend.peminjaman.create') }}"
                                                    class="btn btn-primary">
                                                    <i class="fas fa-plus me-2"></i>Tambah Peminjaman Pertama
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if ($peminjaman->hasPages())
                    <div class="d-flex justify-content-between align-items-center p-4 border-top bg-light">
                        <div class="text-muted small">
                            Menampilkan <strong>{{ $peminjaman->firstItem() ?? 0 }}</strong> -
                            <strong>{{ $peminjaman->lastItem() ?? 0 }}</strong> dari
                            <strong>{{ $peminjaman->total() }}</strong> data
                        </div>
                        <div>
                            {{ $peminjaman->appends(request()->query())->links() }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- ============================== --}}
    {{-- MODAL TOLAK --}}
    {{-- ============================== --}}
    <div class="modal fade" id="modalTolak" tabindex="-1" aria-labelledby="modalTolakLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="modalTolakLabel">
                        <i class="fas fa-times-circle me-2"></i>Tolak Peminjaman
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="formTolak" method="POST">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="ditolak">

                    <div class="modal-body p-4">
                        <div class="alert alert-warning d-flex align-items-center mb-4">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <div>
                                Anda akan menolak peminjaman <strong id="kodeModalTolak"></strong>.
                                Tindakan ini tidak dapat dibatalkan.
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                Alasan Penolakan <span class="text-danger">*</span>
                            </label>
                            <textarea name="alasan_tolak" id="alasanTolak" rows="4" class="form-control"
                                placeholder="Tulis alasan penolakan peminjaman ini..." required></textarea>
                            <div class="form-text text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                Alasan akan ditampilkan kepada peminjam.
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i>Batal
                        </button>
                        <button type="submit" class="btn btn-danger px-4">
                            <i class="fas fa-times-circle me-1"></i>Tolak Peminjaman
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>
        .btn-light.border:hover {
            background-color: #f8f9fa;
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: all 0.2s;
        }

        .table tbody tr:hover {
            background-color: #f8f9fa;
        }

        .empty-state {
            padding: 40px 20px;
        }

        .badge {
            font-weight: 500;
            font-size: 0.813rem;
        }
    </style>

    <script>
        // Buka modal tolak dengan data peminjaman
        function openTolakModal(id, kode) {
            document.getElementById('kodeModalTolak').textContent = kode;
            document.getElementById('alasanTolak').value = '';
            document.getElementById('formTolak').action = `/admin/peminjaman/${id}/update-status`;
            new bootstrap.Modal(document.getElementById('modalTolak')).show();
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Init tooltips
            document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
                new bootstrap.Tooltip(el);
            });
        });
    </script>
@endsection
