@extends('layouts.backend')

@section('title', 'Laporan Verifikasi Peminjaman dari PIC')

@push('styles')
    <style>
        .stats-card {
            border-radius: 10px;
            transition: transform 0.2s;
        }

        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .stats-card h3 {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }

        .page-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem;
            border-radius: 10px;
            margin-bottom: 2rem;
        }

        .filter-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            margin-bottom: 2rem;
        }

        .table-hover tbody tr:hover {
            background-color: #f8f9fa;
        }

        .btn-detail {
            cursor: pointer;
        }

        /* Carousel Styling */
        .carousel-control-prev-icon,
        .carousel-control-next-icon {
            background-color: rgba(0, 0, 0, 0.5);
            border-radius: 50%;
            padding: 10px;
        }

        .carousel-indicators button {
            width: 10px;
            height: 10px;
            border-radius: 50%;
        }

        .carousel-indicators button.active {
            background-color: #ff9800;
        }

        .carousel-photo {
            max-height: 300px;
            cursor: pointer;
            transition: transform 0.2s;
        }

        .carousel-photo:hover {
            transform: scale(1.02);
        }

        /* Lightbox Modal */
        #photoLightbox .modal-content {
            background: rgba(0, 0, 0, 0.95);
            border: none;
        }

        #photoLightbox .carousel-control-prev-icon,
        #photoLightbox .carousel-control-next-icon {
            padding: 20px;
        }

        #photoLightbox .carousel-photo-fullscreen {
            max-height: 85vh;
            max-width: 100%;
            width: auto;
            height: auto;
            object-fit: contain;
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
                        <i class="ti ti-clipboard-check"></i>
                        Laporan Verifikasi Peminjaman Barang
                    </h2>
                    <p class="mb-0 opacity-75">Laporan hasil pengecekan kondisi barang yang dikembalikan oleh PIC</p>
                </div>
                <div>
                    <a href="{{ route('backend.verifikasi.laporan.peminjaman.export') }}" class="btn btn-light">
                        <i class="ti ti-file-pdf"></i> Export PDF
                    </a>
                </div>
            </div>
        </div>

        {{-- Statistics Cards --}}
        <div class="row mb-4">
            <div class="col-md-2">
                <div class="card stats-card text-center border-0 bg-light">
                    <div class="card-body">
                        <h3 class="text-primary">{{ $stats['total'] }}</h3>
                        <small class="text-muted">Total Verifikasi</small>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card stats-card text-center border-success">
                    <div class="card-body">
                        <h3 class="text-success">{{ $stats['baik'] }}</h3>
                        <small class="text-muted">✅ Baik</small>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card stats-card text-center border-warning">
                    <div class="card-body">
                        <h3 class="text-warning">{{ $stats['rusak_ringan'] }}</h3>
                        <small class="text-muted">⚠️ Rusak Ringan</small>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card stats-card text-center border-danger">
                    <div class="card-body">
                        <h3 class="text-danger">{{ $stats['rusak_berat'] }}</h3>
                        <small class="text-muted">🔴 Rusak Berat</small>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card stats-card text-center border-dark">
                    <div class="card-body">
                        <h3 class="text-dark">{{ $stats['hilang'] }}</h3>
                        <small class="text-muted">❌ Hilang</small>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card stats-card text-center border-warning bg-warning bg-opacity-10">
                    <div class="card-body">
                        <h3 class="text-warning">{{ $stats['perlu_tindakan'] }}</h3>
                        <small class="text-muted">⚡ Perlu Tindakan</small>
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
                <form action="{{ route('backend.verifikasi.laporan.peminjaman') }}" method="GET">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Kondisi Barang</label>
                            <select name="kondisi" class="form-select">
                                <option value="">Semua Kondisi</option>
                                <option value="baik" {{ request('kondisi') == 'baik' ? 'selected' : '' }}>✅ Baik</option>
                                <option value="rusak_ringan" {{ request('kondisi') == 'rusak_ringan' ? 'selected' : '' }}>⚠️ Rusak Ringan</option>
                                <option value="rusak_berat" {{ request('kondisi') == 'rusak_berat' ? 'selected' : '' }}>🔴 Rusak Berat</option>
                                <option value="hilang" {{ request('kondisi') == 'hilang' ? 'selected' : '' }}>❌ Hilang</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Status Verifikasi</label>
                            <select name="status" class="form-select">
                                <option value="">Semua Status</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>⏳ Pending</option>
                                <option value="diterima" {{ request('status') == 'diterima' ? 'selected' : '' }}>✅ Diterima</option>
                                <option value="perlu_tindakan" {{ request('status') == 'perlu_tindakan' ? 'selected' : '' }}>⚡ Perlu Tindakan</option>
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
                                <a href="{{ route('backend.verifikasi.laporan.peminjaman') }}" class="btn btn-outline-secondary">
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
                <h5 class="mb-0">Daftar Laporan Verifikasi</h5>
                <span class="badge bg-secondary">{{ $laporan->total() }} Laporan</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="5%">No</th>
                                <th width="12%">Kode Pinjam</th>
                                <th width="15%">User</th>
                                <th width="15%">Barang</th>
                                <th width="12%">PIC</th>
                                <th width="10%">Kondisi</th>
                                <th width="10%">Status</th>
                                <th width="12%">Tgl Verifikasi</th>
                                <th width="9%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($laporan as $i => $v)
                                <tr>
                                    <td class="text-center">{{ $laporan->firstItem() + $i }}</td>
                                    <td>
                                        <span class="badge bg-primary">{{ $v->peminjaman->kode }}</span>
                                    </td>
                                    <td>
                                        <div>
                                            <strong>{{ $v->peminjaman->user->name ?? 'User Dihapus' }}</strong><br>
                                            <small class="text-muted">{{ $v->peminjaman->user->email ?? '-' }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <small>{{ $v->peminjaman->barang_summary }}</small>
                                    </td>
                                    <td>
                                        <div>
                                            <i class="ti ti-user-check"></i> {{ $v->pic->name }}<br>
                                            <small class="text-muted">{{ $v->pic->email }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        @php
                                            $badge = match ($v->kondisi) {
                                                'baik' => 'success',
                                                'rusak_ringan' => 'warning',
                                                'rusak_berat' => 'danger',
                                                'hilang' => 'dark',
                                                default => 'secondary',
                                            };
                                        @endphp
                                        <span class="badge bg-{{ $badge }}">{{ $v->kondisi_label }}</span>
                                    </td>
                                    <td>
                                        @php
                                            $statusBadge = match ($v->status_verifikasi) {
                                                'pending' => 'warning',
                                                'diterima' => 'success',
                                                'perlu_tindakan' => 'danger',
                                                default => 'secondary',
                                            };
                                        @endphp
                                        <span class="badge bg-{{ $statusBadge }}">{{ $v->status_label }}</span>
                                    </td>
                                    <td>
                                        <small>{{ $v->tanggal_verifikasi_format }}</small>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-info btn-detail"
                                            data-bs-toggle="modal" data-bs-target="#detailModal"
                                            data-verifikasi-id="{{ $v->id }}"
                                            data-kode="{{ $v->peminjaman->kode }}"
                                            data-user="{{ $v->peminjaman->user->name ?? 'User Dihapus' }}"
                                            data-email="{{ $v->peminjaman->user->email ?? '-' }}"
                                            data-instansi="{{ $v->peminjaman->user->instansi ?? '-' }}"
                                            data-tanggal-pinjam="{{ $v->peminjaman->tanggal_pinjam_format }}"
                                            data-tanggal-kembali="{{ $v->peminjaman->tanggal_kembali_format }}"
                                            data-total-jumlah="{{ $v->peminjaman->total_jumlah }}"
                                            data-pic-name="{{ $v->pic->name }}"
                                            data-pic-email="{{ $v->pic->email }}"
                                            data-tanggal-verifikasi="{{ $v->tanggal_verifikasi_format }}"
                                            data-kondisi="{{ $v->kondisi }}"
                                            data-kondisi-label="{{ $v->kondisi_label }}"
                                            data-kondisi-badge="{{ $badge }}"
                                            data-status="{{ $v->status_verifikasi }}"
                                            data-status-label="{{ $v->status_label }}"
                                            data-status-badge="{{ $statusBadge }}"
                                            data-catatan="{{ $v->catatan_pic ?? '' }}"
                                            data-foto="{{ json_encode($v->foto_bukti_urls) }}"
                                            data-total-foto="{{ $v->total_photos }}"
                                            data-tindakan="{{ $v->tindakan_admin ?? '' }}"
                                            data-action-url="{{ route('backend.verifikasi.laporan.peminjaman.tindakan', $v->id) }}">
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
            @if ($laporan->hasPages())
                <div class="card-footer bg-transparent">
                    {{ $laporan->links() }}
                </div>
            @endif
        </div>

    </div>

    {{-- MODAL DETAIL --}}
    <div class="modal fade" id="detailModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        <i class="ti ti-clipboard-check"></i>
                        Detail Laporan Verifikasi PIC
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">

                    {{-- Info Peminjaman --}}
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="ti ti-info-circle"></i> Informasi Peminjaman</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-sm table-borderless">
                                        <tr>
                                            <td width="40%"><strong>Kode Peminjaman:</strong></td>
                                            <td><span class="badge bg-primary" id="modal-kode"></span></td>
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
                                            <td width="40%"><strong>Tanggal Pinjam:</strong></td>
                                            <td id="modal-tanggal-pinjam"></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Tanggal Kembali:</strong></td>
                                            <td id="modal-tanggal-kembali"></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Total Item:</strong></td>
                                            <td><span class="badge bg-dark" id="modal-total-jumlah"></span></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            {{-- Detail Barang --}}
                            <div class="mt-3">
                                <p class="mb-2"><strong>Detail Barang yang Dipinjam:</strong></p>
                                <div id="modal-detail-barang-container"></div>
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
                                    <p class="mb-1"><strong>Kondisi Barang:</strong></p>
                                    <h5><span class="badge" id="modal-kondisi-badge"></span></h5>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-1"><strong>Status Verifikasi:</strong></p>
                                    <h5><span class="badge" id="modal-status-badge"></span></h5>
                                </div>
                            </div>

                            <div class="mb-3">
                                <p class="mb-1"><strong>Catatan dari PIC:</strong></p>
                                <div class="p-3 bg-light border rounded">
                                    <i class="ti ti-message me-2 text-primary"></i>
                                    <span class="text-dark" id="modal-catatan"></span>
                                </div>
                            </div>

                            {{-- Foto Bukti dengan Carousel --}}
                            <div class="mb-3" id="modal-foto-container" style="display: none;">
                                <p class="mb-2"><strong>Foto Bukti:</strong> <span class="badge bg-primary" id="modal-foto-count"></span></p>
                                <div id="photoCarousel" class="carousel slide" data-bs-ride="false">
                                    <div class="carousel-indicators" id="carousel-indicators"></div>
                                    <div class="carousel-inner" id="carousel-inner"></div>
                                    <button class="carousel-control-prev" type="button" data-bs-target="#photoCarousel" data-bs-slide="prev" id="carousel-prev">
                                        <span class="carousel-control-prev-icon"></span>
                                    </button>
                                    <button class="carousel-control-next" type="button" data-bs-target="#photoCarousel" data-bs-slide="next" id="carousel-next">
                                        <span class="carousel-control-next-icon"></span>
                                    </button>
                                </div>
                                <p class="text-center text-muted mt-2 mb-0">
                                    <small><i class="ti ti-click"></i> Klik foto untuk memperbesar</small>
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Form Tindakan Admin --}}
                    <div class="card border-warning">
                        <div class="card-header bg-warning bg-opacity-10">
                            <h6 class="mb-0"><i class="ti ti-edit"></i> Tindakan Admin</h6>
                        </div>
                        <div class="card-body">
                            <form id="formTindakan" method="POST">
                                @csrf

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
                                    <textarea name="tindakan_admin" id="modal-tindakan-input" class="form-control" rows="4" placeholder="Contoh: Tagih biaya penggantian Rp 5.000.000 ke user. Ganti dengan unit baru." required></textarea>
                                    <small class="text-muted">
                                        💡 Jelaskan tindakan yang akan diambil untuk menindaklanjuti hasil verifikasi dari PIC.
                                    </small>
                                </div>

                                <div class="alert alert-info" id="modal-tindakan-sebelumnya" style="display: none;">
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

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="ti ti-x"></i> Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL LIGHTBOX UNTUK FOTO FULL SIZE --}}
    <div class="modal fade" id="photoLightbox" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title text-white">
                        <i class="ti ti-photo"></i> Foto Bukti Verifikasi
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center p-0">
                    <div id="photoLightboxCarousel" class="carousel slide" data-bs-ride="false">
                        <div class="carousel-inner" id="lightbox-carousel-inner"></div>
                        <button class="carousel-control-prev" type="button" data-bs-target="#photoLightboxCarousel" data-bs-slide="prev" id="lightbox-prev">
                            <span class="carousel-control-prev-icon"></span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#photoLightboxCarousel" data-bs-slide="next" id="lightbox-next">
                            <span class="carousel-control-next-icon"></span>
                        </button>
                    </div>
                </div>
                <div class="modal-footer border-0 justify-content-center">
                    <div class="btn-group" id="lightbox-thumbnails"></div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Global variable untuk menyimpan foto URLs
        let currentPhotoUrls = [];

        $(document).ready(function() {
            console.log('Script loaded');

            // Event listener untuk button detail
            $(document).on('click', '.btn-detail', function(e) {
                e.preventDefault();
                console.log('Button clicked');

                const btn = $(this);
                const verifikasiId = btn.data('verifikasi-id');

                console.log('Verifikasi ID:', verifikasiId);

                // Update modal content
                $('#modal-kode').text(btn.data('kode'));
                $('#modal-user').text(btn.data('user'));
                $('#modal-email').text(btn.data('email'));
                $('#modal-instansi').text(btn.data('instansi'));
                $('#modal-tanggal-pinjam').text(btn.data('tanggal-pinjam'));
                $('#modal-tanggal-kembali').text(btn.data('tanggal-kembali'));
                $('#modal-total-jumlah').text(btn.data('total-jumlah') + ' Item');

                // Load detail barang via AJAX
                $.ajax({
                    url: '{{ route('backend.verifikasi.laporan.peminjaman.detail-barang', ':id') }}'.replace(':id', verifikasiId),
                    type: 'GET',
                    beforeSend: function() {
                        $('#modal-detail-barang-container').html(
                            '<div class="text-center py-3"><div class="spinner-border spinner-border-sm text-primary" role="status"></div> Loading...</div>'
                        );
                    },
                    success: function(response) {
                        let html = '<div class="table-responsive"><table class="table table-sm table-bordered">';
                        html += '<thead class="table-light"><tr>';
                        html += '<th width="5%" class="text-center">No</th>';
                        html += '<th width="45%">Nama Barang</th>';
                        html += '<th width="15%" class="text-center">Jumlah</th>';
                        html += '<th width="35%">Ruangan</th>';
                        html += '</tr></thead>';
                        html += '<tbody>';

                        if (response.details && response.details.length > 0) {
                            response.details.forEach((item, index) => {
                                html += `<tr>
                                    <td class="text-center">${index + 1}</td>
                                    <td><i class="ti ti-package me-1 text-primary"></i>${item.nama}</td>
                                    <td class="text-center"><span class="badge bg-dark">${item.jumlah} unit</span></td>
                                    <td><i class="ti ti-door me-1 text-info"></i>${item.ruangan}</td>
                                </tr>`;
                            });
                        } else {
                            html += '<tr><td colspan="4" class="text-center text-muted py-3"><i class="ti ti-inbox me-2"></i>Tidak ada detail barang</td></tr>';
                        }

                        html += '</tbody></table></div>';
                        $('#modal-detail-barang-container').html(html);
                    },
                    error: function(xhr) {
                        console.error('AJAX Error:', xhr.responseText);
                        $('#modal-detail-barang-container').html(
                            '<div class="alert alert-danger">' +
                            '<i class="ti ti-alert-circle me-2"></i>' +
                            'Gagal memuat detail barang. ' +
                            (xhr.status === 404 ? 'Route tidak ditemukan.' : 'Silakan coba lagi.') +
                            '</div>'
                        );
                    }
                });

                // Update PIC info
                $('#modal-pic-name').text(btn.data('pic-name'));
                $('#modal-pic-email').text(btn.data('pic-email'));
                $('#modal-tanggal-verifikasi').text(btn.data('tanggal-verifikasi'));

                // Update kondisi badge
                const kondisiBadge = $('#modal-kondisi-badge');
                kondisiBadge.removeClass().addClass('badge bg-' + btn.data('kondisi-badge'));
                kondisiBadge.text(btn.data('kondisi-label'));

                // Update status badge
                const statusBadge = $('#modal-status-badge');
                statusBadge.removeClass().addClass('badge bg-' + btn.data('status-badge'));
                statusBadge.text(btn.data('status-label'));

                // Tampilkan catatan
                const catatan = btn.data('catatan');
                if (catatan && catatan.trim() !== '') {
                    $('#modal-catatan').text(catatan);
                } else {
                    $('#modal-catatan').html('<span class="text-muted fst-italic">Tidak ada catatan</span>');
                }

                //  UPDATE FOTO - Handle multiple photos dengan carousel yang bisa diklik
                const fotoData = btn.data('foto');
                const totalFoto = btn.data('total-foto') || 0;

                if (fotoData && totalFoto > 0) {
                    let fotoUrls = [];
                    try {
                        fotoUrls = typeof fotoData === 'string' ? JSON.parse(fotoData) : fotoData;
                    } catch (e) {
                        console.error('Error parsing foto data:', e);
                        fotoUrls = [];
                    }

                    if (fotoUrls.length > 0) {
                        // Simpan URLs ke global variable
                        currentPhotoUrls = fotoUrls;

                        // Update badge count
                        $('#modal-foto-count').text(fotoUrls.length + ' Foto');

                        // Build carousel indicators
                        let indicatorsHtml = '';
                        if (fotoUrls.length > 1) {
                            fotoUrls.forEach((url, i) => {
                                indicatorsHtml += `<button type="button" data-bs-target="#photoCarousel" data-bs-slide-to="${i}" class="${i === 0 ? 'active' : ''}" aria-label="Foto ${i + 1}"></button>`;
                            });
                        }
                        $('#carousel-indicators').html(indicatorsHtml);

                        // Build carousel slides
                        let slidesHtml = '';
                        fotoUrls.forEach((url, i) => {
                            slidesHtml += `
                                <div class="carousel-item ${i === 0 ? 'active' : ''}">
                                    <div class="text-center" style="min-height: 300px; background: #f8f9fa; display: flex; align-items: center; justify-content: center;">
                                        <img src="${url}" 
                                             class="carousel-photo" 
                                             alt="Foto ${i + 1}" 
                                             data-photo-index="${i}"
                                             style="max-height: 300px; max-width: 100%; object-fit: contain;">
                                    </div>
                                </div>
                            `;
                        });
                        $('#carousel-inner').html(slidesHtml);

                        // Show/hide navigation buttons
                        if (fotoUrls.length > 1) {
                            $('#carousel-prev, #carousel-next').show();
                        } else {
                            $('#carousel-prev, #carousel-next').hide();
                        }

                        $('#modal-foto-container').show();
                    } else {
                        $('#modal-foto-container').hide();
                    }
                } else {
                    $('#modal-foto-container').hide();
                }

                // Update form action
                $('#formTindakan').attr('action', btn.data('action-url'));

                // Update status select
                $('#modal-status-select').val(btn.data('status'));

                // Update tindakan
                const tindakan = btn.data('tindakan');
                if (tindakan && tindakan.trim() !== '') {
                    $('#modal-tindakan-input').val(tindakan);
                    $('#modal-tindakan-text').text(tindakan);
                    $('#modal-tindakan-sebelumnya').show();
                } else {
                    $('#modal-tindakan-input').val('');
                    $('#modal-tindakan-sebelumnya').hide();
                }
            });

            //  Event listener untuk klik foto (membuka lightbox)
            $(document).on('click', '.carousel-photo', function() {
                const photoIndex = $(this).data('photo-index');
                openPhotoLightbox(photoIndex);
            });
        });

        //  Function untuk membuka lightbox dengan foto tertentu
        function openPhotoLightbox(startIndex = 0) {
            if (currentPhotoUrls.length === 0) return;

            // Build lightbox carousel
            let lightboxHtml = '';
            currentPhotoUrls.forEach((url, i) => {
                lightboxHtml += `
                    <div class="carousel-item ${i === startIndex ? 'active' : ''}">
                        <div class="d-flex justify-content-center align-items-center" style="min-height: 85vh; background: rgba(0,0,0,0.95);">
                            <img src="${url}" 
                                 class="carousel-photo-fullscreen" 
                                 alt="Foto ${i + 1}">
                        </div>
                    </div>
                `;
            });
            $('#lightbox-carousel-inner').html(lightboxHtml);

            // Build thumbnails
            let thumbnailsHtml = '';
            currentPhotoUrls.forEach((url, i) => {
                thumbnailsHtml += `
                    <button type="button" 
                            class="btn btn-sm btn-outline-light" 
                            onclick="goToLightboxSlide(${i})">
                        ${i + 1}
                    </button>
                `;
            });
            $('#lightbox-thumbnails').html(thumbnailsHtml);

            // Show/hide navigation
            if (currentPhotoUrls.length > 1) {
                $('#lightbox-prev, #lightbox-next').show();
            } else {
                $('#lightbox-prev, #lightbox-next').hide();
            }

            // Open modal
            const lightboxModal = new bootstrap.Modal(document.getElementById('photoLightbox'));
            lightboxModal.show();
        }

        // Function untuk navigasi ke slide tertentu di lightbox
        function goToLightboxSlide(index) {
            const carousel = document.getElementById('photoLightboxCarousel');
            const bsCarousel = bootstrap.Carousel.getInstance(carousel) || new bootstrap.Carousel(carousel);
            bsCarousel.to(index);
        }
    </script>
@endpush