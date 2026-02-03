@extends('layouts.backend')

@section('title', 'Laporan Verifikasi Booking Ruangan dari PIC')

@push('styles')
<style>
    .stats-card {
        border-radius: 10px;
        transition: transform 0.2s;
    }
    .stats-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    .stats-card h3 {
        font-size: 2rem;
        font-weight: bold;
        margin-bottom: 0.5rem;
    }
    .page-header {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        color: white;
        padding: 2rem;
        border-radius: 10px;
        margin-bottom: 2rem;
    }
    .filter-card {
        background: white;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        margin-bottom: 2rem;
    }
    .table-hover tbody tr:hover {
        background-color: #f8f9fa;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">

    {{-- Page Header --}}
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="mb-2">
                    <i class="ti ti-building-community"></i>
                    Laporan Verifikasi Booking Ruangan
                </h2>
                <p class="mb-0 opacity-75">Laporan hasil pengecekan kondisi ruangan setelah booking selesai oleh PIC</p>
            </div>
        </div>
    </div>

    {{-- Statistics Cards --}}
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card stats-card text-center border-0 bg-light">
                <div class="card-body">
                    <h3 class="text-primary">{{ $stats['total'] }}</h3>
                    <small class="text-muted">Total Verifikasi</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stats-card text-center border-success">
                <div class="card-body">
                    <h3 class="text-success">{{ $stats['baik'] }}</h3>
                    <small class="text-muted">✅ Baik & Bersih</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stats-card text-center border-warning">
                <div class="card-body">
                    <h3 class="text-warning">{{ $stats['ruangan_kotor'] }}</h3>
                    <small class="text-muted">🧹 Kotor</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stats-card text-center border-danger">
                <div class="card-body">
                    <h3 class="text-danger">{{ $stats['ruangan_rusak'] }}</h3>
                    <small class="text-muted">🔴 Rusak</small>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter Card --}}
    <div class="card filter-card mb-4">
        <div class="card-header bg-transparent">
            <h5 class="mb-0"><i class="ti ti-filter"></i> Filter Laporan</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('backend.verifikasi.booking.index') }}" method="GET">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Kondisi Ruangan</label>
                        <select name="kondisi" class="form-select">
                            <option value="">Semua Kondisi</option>
                            <option value="baik"  {{ request('kondisi') == 'baik'  ? 'selected' : '' }}>✅ Baik</option>
                            <option value="kotor" {{ request('kondisi') == 'kotor' ? 'selected' : '' }}>🧹 Kotor</option>
                            <option value="rusak" {{ request('kondisi') == 'rusak' ? 'selected' : '' }}>🔴 Rusak</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Status Verifikasi</label>
                        <select name="status" class="form-select">
                            <option value="">Semua Status</option>
                            <option value="pending"         {{ request('status') == 'pending'         ? 'selected' : '' }}>⏳ Pending</option>
                            <option value="diterima"        {{ request('status') == 'diterima'        ? 'selected' : '' }}>✅ Diterima</option>
                            <option value="perlu_tindakan"  {{ request('status') == 'perlu_tindakan'  ? 'selected' : '' }}>⚡ Perlu Tindakan</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Dari Tanggal</label>
                        <input type="date" name="tanggal_dari" class="form-control" value="{{ request('tanggal_dari') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Sampai Tanggal</label>
                        <input type="date" name="tanggal_sampai" class="form-control" value="{{ request('tanggal_sampai') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="ti ti-search"></i> Filter
                            </button>
                            <a href="{{ route('backend.verifikasi.booking.index') }}" class="btn btn-outline-secondary">
                                <i class="ti ti-x"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Data Table --}}
    <div class="card">
        <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Daftar Laporan Verifikasi Booking</h5>
            <span class="badge bg-secondary">{{ $verifikasi->total() }} Laporan</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th width="5%">No</th>
                            <th width="12%">Kode Booking</th>
                            <th width="15%">User</th>
                            <th width="15%">Ruangan</th>
                            <th width="12%">PIC</th>
                            <th width="10%">Kondisi</th>
                            <th width="10%">Status</th>
                            <th width="12%">Tgl Verifikasi</th>
                            <th width="9%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($verifikasi as $i => $v)
                        @php
                            $kondisiBadge = match($v->kondisi_ruangan) {
                                'baik'  => 'success',
                                'kotor' => 'warning',
                                'rusak' => 'danger',
                                default => 'secondary',
                            };
                            $statusBadge = match($v->status_verifikasi) {
                                'pending'        => 'warning',
                                'diterima'       => 'success',
                                'perlu_tindakan' => 'danger',
                                default          => 'secondary',
                            };
                        @endphp
                        <tr>
                            <td class="text-center">{{ $verifikasi->firstItem() + $i }}</td>
                            <td>
                                <span class="badge bg-info">{{ $v->booking->kode ?? '–' }}</span>
                            </td>
                            <td>
                                <strong>{{ $v->booking->user->name ?? 'User Dihapus' }}</strong><br>
                                <small class="text-muted">{{ $v->booking->user->email ?? '–' }}</small>
                            </td>
                            <td>
                                <strong>{{ $v->booking->ruangan->nama_ruangan ?? 'Ruangan Dihapus' }}</strong><br>
                                <small class="text-muted">
                                    {{ \Carbon\Carbon::parse($v->booking->tanggal)->format('d/m/Y') }}
                                    &nbsp; {{ substr($v->booking->waktu_mulai, 0, 5) }}
                                </small>
                            </td>
                            <td>
                                <i class="ti ti-user-check"></i> {{ $v->pic->name }}<br>
                                <small class="text-muted">{{ $v->pic->email }}</small>
                            </td>
                            <td>
                                <span class="badge bg-{{ $kondisiBadge }}">{{ $v->kondisi_label }}</span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $statusBadge }}">{{ $v->status_label }}</span>
                            </td>
                            <td>
                                <small>{{ $v->tanggal_verifikasi_format }}</small>
                            </td>
                            <td>
                                <button type="button"
                                        class="btn btn-sm btn-info btn-detail"
                                        data-id="{{ $v->id }}"
                                        data-kode="{{ $v->booking->kode ?? '–' }}"
                                        data-user="{{ $v->booking->user->name ?? 'User Dihapus' }}"
                                        data-email="{{ $v->booking->user->email ?? '–' }}"
                                        data-instansi="{{ $v->booking->user->instansi ?? '–' }}"
                                        data-ruangan="{{ $v->booking->ruangan->nama_ruangan ?? 'Ruangan Dihapus' }}"
                                        data-lokasi="{{ $v->booking->ruangan->lokasi ?? '–' }}"
                                        data-kapasitas="{{ $v->booking->ruangan->kapasitas ?? '–' }}"
                                        data-tanggal="{{ \Carbon\Carbon::parse($v->booking->tanggal)->translatedFormat('d F Y') }}"
                                        data-waktu-mulai="{{ substr($v->booking->waktu_mulai, 0, 5) }}"
                                        data-waktu-selesai="{{ substr($v->booking->waktu_selesai, 0, 5) }}"
                                        data-keperluan="{{ $v->booking->keperluan ?? '' }}"
                                        data-pic-name="{{ $v->pic->name }}"
                                        data-pic-email="{{ $v->pic->email }}"
                                        data-tanggal-verifikasi="{{ $v->tanggal_verifikasi_format }}"
                                        data-kondisi="{{ $v->kondisi_ruangan }}"
                                        data-kondisi-label="{{ $v->kondisi_label }}"
                                        data-kondisi-badge="{{ $kondisiBadge }}"
                                        data-status="{{ $v->status_verifikasi }}"
                                        data-status-label="{{ $v->status_label }}"
                                        data-status-badge="{{ $statusBadge }}"
                                        data-catatan="{{ $v->catatan_pic ?? '' }}"
                                        data-foto="{{ $v->foto_bukti ? asset('storage/' . $v->foto_bukti) : '' }}"
                                        data-tindakan="{{ $v->tindakan_admin ?? '' }}"
                                        data-action-url="{{ route('backend.verifikasi.booking.tindakan', $v->id) }}">
                                    <i class="ti ti-eye"></i> Detail
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-5">
                                <i class="ti ti-inbox" style="font-size: 3rem; opacity: 0.3;"></i>
                                <p class="text-muted mt-2">Tidak ada laporan verifikasi</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($verifikasi->hasPages())
        <div class="card-footer bg-transparent">
            {{ $verifikasi->links() }}
        </div>
        @endif
    </div>

</div>

{{-- ============================================================
     SINGLE MODAL — di luar loop, content di-swap via jQuery
     Exact same pattern sebagai peminjaman yang sudah kerja
     ============================================================ --}}
<div class="modal fade" id="detailModalBooking" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="ti ti-clipboard-check"></i>
                    Detail Laporan Verifikasi Booking
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">

                {{-- Info Booking --}}
                <div class="card mb-3">
                    <div class="card-header bg-light">
                        <h6 class="mb-0"><i class="ti ti-info-circle"></i> Informasi Booking</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <td width="40%"><strong>Kode Booking:</strong></td>
                                        <td><span class="badge bg-info" id="modal-kode"></span></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Nama User:</strong></td>
                                        <td id="modal-user"></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Email:</strong></td>
                                        <td id="modal-email"></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Instansi:</strong></td>
                                        <td id="modal-instansi"></td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <td width="40%"><strong>Ruangan:</strong></td>
                                        <td id="modal-ruangan"></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Lokasi:</strong></td>
                                        <td id="modal-lokasi"></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Kapasitas:</strong></td>
                                        <td id="modal-kapasitas"></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Tanggal:</strong></td>
                                        <td id="modal-tanggal"></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Waktu:</strong></td>
                                        <td id="modal-waktu"></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <div id="modal-keperluan-wrap" class="mt-2" style="display:none;">
                            <small class="text-muted"><strong>Keperluan:</strong> <span id="modal-keperluan"></span></small>
                        </div>
                    </div>
                </div>

                {{-- Hasil Verifikasi PIC --}}
                <div class="card mb-3">
                    <div class="card-header bg-info text-white">
                        <h6 class="mb-0"><i class="ti ti-clipboard-check"></i> Hasil Verifikasi dari PIC</h6>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <p class="mb-1"><strong>PIC yang Melakukan Pengecekan:</strong></p>
                                <p class="text-muted">
                                    <i class="ti ti-user-check"></i> <span id="modal-pic-name"></span><br>
                                    <small id="modal-pic-email"></small>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Tanggal & Waktu Verifikasi:</strong></p>
                                <p class="text-muted">
                                    <i class="ti ti-calendar"></i> <span id="modal-tanggal-verifikasi"></span>
                                </p>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Kondisi Ruangan:</strong></p>
                                <h5><span class="badge" id="modal-kondisi-badge"></span></h5>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Status Verifikasi:</strong></p>
                                <h5><span class="badge" id="modal-status-badge"></span></h5>
                            </div>
                        </div>

                        <div class="mb-3">
                            <p class="mb-1"><strong>Catatan dari PIC:</strong></p>
                            <div class="alert alert-light">
                                <i class="ti ti-message"></i> <span id="modal-catatan"></span>
                            </div>
                        </div>

                        <div class="mb-3" id="modal-foto-container" style="display:none;">
                            <p class="mb-1"><strong>Foto Bukti:</strong></p>
                            <img id="modal-foto" class="img-fluid rounded border" alt="Foto Bukti" style="max-height:300px;">
                        </div>
                    </div>
                </div>

                {{-- Form Tindakan Admin --}}
                <div class="card border-warning">
                    <div class="card-header bg-warning bg-opacity-10">
                        <h6 class="mb-0"><i class="ti ti-edit"></i> Tindakan Admin</h6>
                    </div>
                    <div class="card-body">
                        <form id="formTindakanBooking" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label class="form-label">Status Verifikasi <span class="text-danger">*</span></label>
                                <select name="status_verifikasi" id="modal-status-select" class="form-select" required>
                                    <option value="">Pilih Status</option>
                                    <option value="diterima">✅ Diterima (Tidak ada masalah)</option>
                                    <option value="perlu_tindakan">⚡ Perlu Tindakan Lanjut</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Tindakan Lanjut <span class="text-danger">*</span></label>
                                <textarea name="tindakan_admin"
                                          id="modal-tindakan-input"
                                          class="form-control"
                                          rows="4"
                                          placeholder="Contoh: Tagih biaya pembersihan Rp 500.000 ke user …"
                                          required></textarea>
                                <small class="text-muted">
                                    💡 Jelaskan tindakan yang akan diambil untuk menindaklanjuti hasil verifikasi dari PIC.
                                </small>
                            </div>

                            <div class="alert alert-info" id="modal-tindakan-sebelumnya" style="display:none;">
                                <strong><i class="ti ti-info-circle"></i> Tindakan Sebelumnya:</strong><br>
                                <span id="modal-tindakan-text"></span>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">
                                    <i class="ti ti-device-floppy"></i> Simpan Tindakan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

            </div><!-- /modal-body -->
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="ti ti-x"></i> Tutup
                </button>
            </div>
        </div>
    </div>
</div>

@endsection


@push('scripts')
<script>
$(document).ready(function () {

    $('.btn-detail').on('click', function () {
        var btn = $(this);

        // ── Info Booking ──
        $('#modal-kode').text(btn.data('kode'));
        $('#modal-user').text(btn.data('user'));
        $('#modal-email').text(btn.data('email'));
        $('#modal-instansi').text(btn.data('instansi'));
        $('#modal-ruangan').text(btn.data('ruangan'));
        $('#modal-lokasi').text(btn.data('lokasi'));
        $('#modal-kapasitas').text(btn.data('kapasitas') + ' orang');
        $('#modal-tanggal').text(btn.data('tanggal'));
        $('#modal-waktu').text(btn.data('waktu-mulai') + ' – ' + btn.data('waktu-selesai'));

        // Keperluan (optional)
        if (btn.data('keperluan')) {
            $('#modal-keperluan').text(btn.data('keperluan'));
            $('#modal-keperluan-wrap').show();
        } else {
            $('#modal-keperluan-wrap').hide();
        }

        // ── Verifikasi PIC ──
        $('#modal-pic-name').text(btn.data('pic-name'));
        $('#modal-pic-email').text(btn.data('pic-email'));
        $('#modal-tanggal-verifikasi').text(btn.data('tanggal-verifikasi'));

        // Kondisi badge
        $('#modal-kondisi-badge')
            .removeClass()
            .addClass('badge bg-' + btn.data('kondisi-badge'))
            .text(btn.data('kondisi-label'));

        // Status badge
        $('#modal-status-badge')
            .removeClass()
            .addClass('badge bg-' + btn.data('status-badge'))
            .text(btn.data('status-label'));

        // Catatan
        $('#modal-catatan').text(btn.data('catatan') || '–');

        // Foto
        if (btn.data('foto')) {
            $('#modal-foto').attr('src', btn.data('foto'));
            $('#modal-foto-container').show();
        } else {
            $('#modal-foto-container').hide();
        }

        // ── Form Tindakan ──
        $('#formTindakanBooking').attr('action', btn.data('action-url'));
        $('#modal-status-select').val(btn.data('status'));

        var tindakan = btn.data('tindakan');
        $('#modal-tindakan-input').val(tindakan || '');
        if (tindakan) {
            $('#modal-tindakan-text').text(tindakan);
            $('#modal-tindakan-sebelumnya').show();
        } else {
            $('#modal-tindakan-sebelumnya').hide();
        }

        // Show
        $('#detailModalBooking').modal('show');
    });
});
</script>
@endpush