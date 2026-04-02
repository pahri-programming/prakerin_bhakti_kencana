@extends('layouts.frontend')
@section('title', 'Riwayat Saya')

@push('styles')
    <style>
        body {
            background: #f0f4f8;
        }

        .page-wrapper {
            padding: 2.5rem 0 4rem;
        }

        .page-hero {
            background: linear-gradient(135deg, #1a6bb5 0%, #0f4c81 100%);
            border-radius: 16px;
            padding: 2rem 2.5rem;
            color: #fff;
            margin-bottom: 2rem;
            position: relative;
            overflow: hidden;
        }

        .page-hero::after {
            content: '';
            position: absolute;
            top: -40px;
            right: -40px;
            width: 180px;
            height: 180px;
            border-radius: 50%;
            background: rgba(255, 255, 255, .07);
        }

        .page-hero h4 {
            font-size: 1.5rem;
            font-weight: 700;
            margin: 0 0 .25rem;
        }

        .page-hero p {
            margin: 0;
            opacity: .75;
            font-size: .9rem;
        }

        /* Tab nav */
        .tab-nav {
            display: flex;
            gap: 8px;
            background: #fff;
            border-radius: 14px;
            padding: 6px;
            margin-bottom: 1.5rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, .06);
        }

        .tab-btn {
            flex: 1;
            padding: 10px 16px;
            border-radius: 10px;
            border: none;
            background: transparent;
            font-size: .875rem;
            font-weight: 600;
            color: #64748b;
            cursor: pointer;
            transition: all .2s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
        }

        .tab-btn.active {
            background: #1a6bb5;
            color: #fff;
        }

        .tab-btn:hover:not(.active) {
            background: #f1f5f9;
            color: #1e293b;
        }

        .tab-panel {
            display: none;
        }

        .tab-panel.active {
            display: block;
        }

        /* Section card */
        .section-card {
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, .06);
            margin-bottom: 1.5rem;
            overflow: hidden;
        }

        .section-head {
            padding: 1rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid #f0f0f0;
        }

        .section-head h6 {
            margin: 0;
            font-weight: 700;
            font-size: .95rem;
        }

        /* Filter bar */
        .filter-bar {
            background: #f8fafc;
            border-bottom: 1px solid #f0f0f0;
            padding: 1rem 1.5rem;
        }

        /* Table */
        .riwayat-table {
            width: 100%;
            border-collapse: collapse;
            font-size: .875rem;
        }

        .riwayat-table th {
            background: #f8fafc;
            color: #64748b;
            font-weight: 600;
            padding: 10px 14px;
            border-bottom: 2px solid #e2e8f0;
            text-align: left;
            font-size: .78rem;
            text-transform: uppercase;
            letter-spacing: .04em;
        }

        .riwayat-table td {
            padding: 12px 14px;
            border-bottom: 1px solid #f0f0f0;
            color: #1e293b;
            vertical-align: middle;
        }

        .riwayat-table tr:last-child td {
            border-bottom: none;
        }

        .riwayat-table tr:hover td {
            background: #f8fafc;
        }

        .badge-pill {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: .75rem;
            font-weight: 700;
        }

        .badge-pending {
            background: #fef9e7;
            color: #d35400;
        }

        .badge-diterima {
            background: #eef4fd;
            color: #1a5276;
        }

        .badge-ditolak {
            background: #fdecea;
            color: #c0392b;
        }

        .badge-selesai {
            background: #eafaf1;
            color: #1e8449;
        }

        .badge-menunggu {
            background: #fef9e7;
            color: #d35400;
        }

        .badge-disetujui {
            background: #eef4fd;
            color: #1a5276;
        }

        /* Barang list */
        .barang-list {
            display: flex;
            flex-wrap: wrap;
            gap: 4px;
        }

        .barang-tag {
            background: #f1f5f9;
            color: #475569;
            border-radius: 6px;
            padding: 2px 8px;
            font-size: .78rem;
        }

        /* Empty state */
        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
            color: #94a3b8;
        }

        .empty-state i {
            font-size: 2.5rem;
            display: block;
            margin-bottom: .75rem;
        }

        .btn-export {
            background: #fdecea;
            color: #c0392b;
            border: none;
            border-radius: 8px;
            padding: 6px 14px;
            font-size: .8rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 6px;
            text-decoration: none;
            transition: background .2s;
        }

        .btn-export:hover {
            background: #f5b7b1;
            color: #922b21;
        }
    </style>
@endpush

@section('content')
    <div class="page-wrapper">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xl-11 col-lg-12">

                    {{-- Hero --}}
                    <div class="page-hero">
                        <h4><i class="fas fa-history me-2"></i>Riwayat Saya</h4>
                        <p>Lihat semua riwayat booking, peminjaman, dan denda Anda</p>
                    </div>

                    {{-- Alert --}}
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show mb-4 rounded-3">
                            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    {{-- Tab Nav --}}
                    <div class="tab-nav">
                        <button class="tab-btn active" onclick="switchTab('booking', this)">
                            <i class="fas fa-door-open"></i> Booking Ruangan
                            <span class="badge bg-primary bg-opacity-20 text-primary ms-1"
                                style="font-size:.7rem;">{{ $booking->count() }}</span>
                        </button>
                        <button class="tab-btn" onclick="switchTab('peminjaman', this)">
                            <i class="fas fa-boxes"></i> Peminjaman Barang
                            <span class="badge bg-success bg-opacity-20 text-success ms-1"
                                style="font-size:.7rem;">{{ $peminjaman->count() }}</span>
                        </button>
                        <button class="tab-btn" onclick="switchTab('denda', this)">
                            <i class="fas fa-file-invoice-dollar"></i> Denda
                            <span class="badge bg-danger bg-opacity-20 text-danger ms-1"
                                style="font-size:.7rem;">{{ $denda->count() }}</span>
                        </button>
                    </div>

                    {{-- ═══════════════════════════════════════════ --}}
                    {{-- TAB 1: BOOKING --}}
                    {{-- ═══════════════════════════════════════════ --}}
                    <div id="tab-booking" class="tab-panel active">
                        <div class="section-card">
                            <div class="section-head">
                                <h6><i class="fas fa-door-open me-2 text-primary"></i>Riwayat Booking Ruangan</h6>
                                <a href="{{ route('riwayat.booking.export', request()->query()) }}" class="btn-export">
                                    <i class="fas fa-file-pdf"></i> Export PDF
                                </a>
                            </div>

                            {{-- Filter --}}
                            <div class="filter-bar">
                                <form method="GET" action="{{ route('riwayat.index') }}" id="form-booking">
                                    <input type="hidden" name="tab" value="booking">
                                    <div class="row g-2 align-items-end">
                                        <div class="col-md-3">
                                            <label class="form-label"
                                                style="font-size:.78rem; color:#64748b; font-weight:600;">Ruangan</label>
                                            <select name="ruang_id" class="form-select form-select-sm">
                                                <option value="">Semua Ruangan</option>
                                                @foreach ($ruangan as $r)
                                                    <option value="{{ $r->id }}"
                                                        {{ request('ruang_id') == $r->id ? 'selected' : '' }}>
                                                        {{ $r->nama_ruangan }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label"
                                                style="font-size:.78rem; color:#64748b; font-weight:600;">Status</label>
                                            <select name="status_booking" class="form-select form-select-sm">
                                                <option value="">Semua Status</option>
                                                <option value="Pending"
                                                    {{ request('status_booking') == 'Pending' ? 'selected' : '' }}>Pending
                                                </option>
                                                <option value="Diterima"
                                                    {{ request('status_booking') == 'Diterima' ? 'selected' : '' }}>
                                                    Diterima</option>
                                                <option value="Ditolak"
                                                    {{ request('status_booking') == 'Ditolak' ? 'selected' : '' }}>
                                                    Ditolak</option>
                                                <option value="Selesai"
                                                    {{ request('status_booking') == 'Selesai' ? 'selected' : '' }}>
                                                    Selesai</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label"
                                                style="font-size:.78rem; color:#64748b; font-weight:600;">Tanggal</label>
                                            <input type="date" name="tanggal" class="form-control form-control-sm"
                                                value="{{ request('tanggal') }}">
                                        </div>
                                        <div class="col-md-3 d-flex gap-2">
                                            <button type="submit" class="btn btn-primary btn-sm flex-grow-1">
                                                <i class="fas fa-search me-1"></i> Filter
                                            </button>
                                            <a href="{{ route('riwayat.index') }}"
                                                class="btn btn-outline-secondary btn-sm">
                                                <i class="fas fa-times"></i>
                                            </a>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <div class="p-0">
                                @if ($booking->isEmpty())
                                    <div class="empty-state">
                                        <i class="fas fa-calendar-times"></i>
                                        Belum ada riwayat booking.
                                    </div>
                                @else
                                    <div class="table-responsive">
                                        <table class="riwayat-table">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Kode Booking</th>
                                                    <th>Ruangan</th>
                                                    <th>Tanggal</th>
                                                    <th>Waktu</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($booking as $b)
                                                    <tr>
                                                        <td class="text-muted">{{ $loop->iteration }}</td>
                                                        <td><code style="font-size:.8rem;">{{ $b->kode ?? '-' }}</code>
                                                        </td>
                                                        <td class="fw-semibold">{{ $b->ruangan->nama_ruangan ?? '-' }}</td>
                                                        <td>{{ \Carbon\Carbon::parse($b->tanggal)->translatedFormat('d F Y') }}
                                                        </td>
                                                        <td>{{ substr($b->waktu_mulai, 0, 5) }} –
                                                            {{ substr($b->waktu_selesai, 0, 5) }}</td>
                                                        <td>
                                                            @if ($b->status === 'Pending')
                                                                <span class="badge-pill badge-pending">Menunggu</span>
                                                            @elseif($b->status === 'Diterima')
                                                                <span class="badge-pill badge-diterima">Diterima</span>
                                                            @elseif($b->status === 'Ditolak')
                                                                <span class="badge-pill badge-ditolak">Ditolak</span>
                                                            @elseif($b->status === 'Selesai')
                                                                <span class="badge-pill badge-selesai">Selesai</span>
                                                            @else
                                                                <span class="badge-pill"
                                                                    style="background:#f1f5f9;color:#64748b;">{{ $b->status }}</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- ═══════════════════════════════════════════ --}}
                    {{-- TAB 2: PEMINJAMAN --}}
                    {{-- ═══════════════════════════════════════════ --}}
                    <div id="tab-peminjaman" class="tab-panel">
                        <div class="section-card">
                            <div class="section-head">
                                <h6><i class="fas fa-boxes me-2 text-success"></i>Riwayat Peminjaman Barang</h6>
                                <a href="{{ route('riwayat.peminjaman.export', request()->query()) }}" class="btn-export">
                                    <i class="fas fa-file-pdf"></i> Export PDF
                                </a>
                            </div>

                            {{-- Filter --}}
                            <div class="filter-bar">
                                <form method="GET" action="{{ route('riwayat.index') }}">
                                    <input type="hidden" name="tab" value="peminjaman">
                                    <div class="row g-2 align-items-end">
                                        <div class="col-md-3">
                                            <label class="form-label"
                                                style="font-size:.78rem; color:#64748b; font-weight:600;">Ruangan</label>
                                            <select name="ruangan_pinjam_id" class="form-select form-select-sm">
                                                <option value="">Semua Ruangan</option>
                                                @foreach ($ruangan as $r)
                                                    <option value="{{ $r->id }}"
                                                        {{ request('ruangan_pinjam_id') == $r->id ? 'selected' : '' }}>
                                                        {{ $r->nama_ruangan }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label"
                                                style="font-size:.78rem; color:#64748b; font-weight:600;">Barang</label>
                                            <select name="barang_id" class="form-select form-select-sm">
                                                <option value="">Semua Barang</option>
                                                @foreach ($barang as $item)
                                                    <option value="{{ $item->id }}"
                                                        {{ request('barang_id') == $item->id ? 'selected' : '' }}>
                                                        {{ $item->nama }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label"
                                                style="font-size:.78rem; color:#64748b; font-weight:600;">Status</label>
                                            <select name="status_peminjaman" class="form-select form-select-sm">
                                                <option value="">Semua Status</option>
                                                <option value="menunggu"
                                                    {{ request('status_peminjaman') == 'menunggu' ? 'selected' : '' }}>
                                                    Menunggu</option>
                                                <option value="disetujui"
                                                    {{ request('status_peminjaman') == 'disetujui' ? 'selected' : '' }}>
                                                    Disetujui</option>
                                                <option value="ditolak"
                                                    {{ request('status_peminjaman') == 'ditolak' ? 'selected' : '' }}>
                                                    Ditolak</option>
                                                <option value="selesai"
                                                    {{ request('status_peminjaman') == 'selesai' ? 'selected' : '' }}>
                                                    Selesai</option>
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label"
                                                style="font-size:.78rem; color:#64748b; font-weight:600;">Tgl
                                                Pinjam</label>
                                            <input type="date" name="tanggal_pinjam"
                                                class="form-control form-control-sm"
                                                value="{{ request('tanggal_pinjam') }}">
                                        </div>
                                        <div class="col-md-1">
                                            <label class="form-label"
                                                style="font-size:.78rem; color:#64748b; font-weight:600;">Tgl
                                                Kembali</label>
                                            <input type="date" name="tanggal_kembali"
                                                class="form-control form-control-sm"
                                                value="{{ request('tanggal_kembali') }}">
                                        </div>
                                        <div class="col-md-2 d-flex gap-2">
                                            <button type="submit" class="btn btn-success btn-sm flex-grow-1">
                                                <i class="fas fa-search me-1"></i> Filter
                                            </button>
                                            <a href="{{ route('riwayat.index') }}"
                                                class="btn btn-outline-secondary btn-sm">
                                                <i class="fas fa-times"></i>
                                            </a>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <div class="p-0">
                                @if ($peminjaman->isEmpty())
                                    <div class="empty-state">
                                        <i class="fas fa-box-open"></i>
                                        Belum ada riwayat peminjaman.
                                    </div>
                                @else
                                    <div class="table-responsive">
                                        <table class="riwayat-table">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Kode</th>
                                                    <th>Barang & Ruangan</th>
                                                    <th>Tgl Pinjam</th>
                                                    <th>Tgl Kembali</th>
                                                    <th>Keterangan</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($peminjaman as $p)
                                                    <tr>
                                                        <td class="text-muted">{{ $loop->iteration }}</td>
                                                        <td><code style="font-size:.8rem;">{{ $p->kode ?? '-' }}</code>
                                                        </td>
                                                        <td>
                                                            <div class="barang-list">
                                                                @foreach ($p->detailbarangs as $d)
                                                                    <span class="barang-tag">
                                                                        {{ $d->barangRuangan->barang->nama ?? '-' }}
                                                                        ({{ $d->barangRuangan->ruangan->nama_ruangan ?? '-' }})
                                                                        ×{{ $d->jumlah }}
                                                                    </span>
                                                                @endforeach
                                                            </div>
                                                        </td>
                                                        <td>{{ \Carbon\Carbon::parse($p->tanggal_pinjam)->translatedFormat('d F Y') }}
                                                        </td>
                                                        <td>
                                                            {{ $p->tanggal_kembali ? \Carbon\Carbon::parse($p->tanggal_kembali)->translatedFormat('d F Y') : '-' }}
                                                        </td>
                                                        <td style="max-width:140px; font-size:.8rem; color:#64748b;">
                                                            {{ $p->keterangan ?? '-' }}
                                                        </td>
                                                        <td>
                                                            @php $st = strtolower($p->status) @endphp
                                                            @if ($st === 'menunggu')
                                                                <span class="badge-pill badge-menunggu">Menunggu</span>
                                                            @elseif($st === 'disetujui')
                                                                <span class="badge-pill badge-disetujui">Disetujui</span>
                                                            @elseif($st === 'ditolak')
                                                                <span class="badge-pill badge-ditolak">Ditolak</span>
                                                            @elseif($st === 'selesai')
                                                                <span class="badge-pill badge-selesai">Selesai</span>
                                                            @else
                                                                <span class="badge-pill"
                                                                    style="background:#f1f5f9;color:#64748b;">{{ $p->status }}</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- ═══════════════════════════════════════════ --}}
                    {{-- TAB 3: DENDA --}}
                    {{-- ═══════════════════════════════════════════ --}}
                    <div id="tab-denda" class="tab-panel">

                        {{-- Sub tab denda --}}
                        <div style="display:flex; gap:8px; margin-bottom:1rem;">
                            <button class="btn btn-sm btn-danger" id="btn-denda-barang"
                                onclick="switchSubTab('barang', this)">
                                <i class="fas fa-boxes me-1"></i>Denda Barang
                                <span class="badge bg-white text-danger ms-1">{{ $denda->count() }}</span>
                            </button>
                            <button class="btn btn-sm btn-outline-danger" id="btn-denda-booking"
                                onclick="switchSubTab('booking', this)">
                                <i class="fas fa-door-open me-1"></i>Denda Booking
                                <span
                                    class="badge bg-danger bg-opacity-10 text-danger ms-1">{{ $dendaBooking->count() }}</span>
                            </button>
                        </div>

                        {{-- Sub panel: Denda Barang --}}
                        <div id="sub-denda-barang">
                            <div class="section-card">
                                <div class="section-head">
                                    <h6><i class="fas fa-boxes me-2 text-danger"></i>Denda Peminjaman Barang</h6>
                                </div>
                                <div class="p-0">
                                    @if ($denda->isEmpty())
                                        <div class="empty-state">
                                            <i class="fas fa-check-circle text-success"></i>
                                            Tidak ada denda peminjaman barang.
                                        </div>
                                    @else
                                        <div class="table-responsive">
                                            <table class="riwayat-table">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Kode Peminjaman</th>
                                                        <th>Kondisi</th>
                                                        <th>Jumlah Denda</th>
                                                        <th>Tanggal</th>
                                                        <th>Status</th>
                                                        <th>Aksi</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($denda as $d)
                                                        <tr>
                                                            <td class="text-muted">{{ $loop->iteration }}</td>
                                                            <td><code
                                                                    style="font-size:.8rem;">{{ $d->pengembalianBarang->peminjamanBarang->kode ?? '-' }}</code>
                                                            </td>
                                                            <td>
                                                                @php $kondisi = $d->verifikasiPengembalian->kondisi ?? '-' @endphp
                                                                @if ($kondisi === 'rusak_ringan')
                                                                    <span class="badge-pill badge-menunggu">Rusak
                                                                        Ringan</span>
                                                                @elseif($kondisi === 'rusak_berat')
                                                                    <span class="badge-pill badge-ditolak">Rusak
                                                                        Berat</span>
                                                                @elseif($kondisi === 'hilang')
                                                                    <span class="badge-pill"
                                                                        style="background:#1e293b;color:#fff;">Hilang</span>
                                                                @else
                                                                    <span class="text-muted">-</span>
                                                                @endif
                                                            </td>
                                                            <td class="fw-bold" style="color:#c0392b;">
                                                                Rp {{ number_format($d->jumlah_denda, 0, ',', '.') }}
                                                            </td>
                                                            <td>{{ $d->tanggal_tindakan ? \Carbon\Carbon::parse($d->tanggal_tindakan)->translatedFormat('d F Y') : '-' }}
                                                            </td>
                                                            <td>
                                                                @if ($d->status_pembayaran === 'belum_bayar')
                                                                    <span class="badge-pill badge-ditolak">Belum
                                                                        Bayar</span>
                                                                @elseif($d->status_pembayaran === 'menunggu_verifikasi')
                                                                    <span class="badge-pill badge-menunggu">Menunggu
                                                                        Verifikasi</span>
                                                                @elseif($d->status_pembayaran === 'sudah_bayar')
                                                                    <span class="badge-pill badge-selesai">Lunas</span>
                                                                @elseif($d->status_pembayaran === 'dibebaskan')
                                                                    <span class="badge-pill"
                                                                        style="background:#f1f5f9;color:#64748b;">Dibebaskan</span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                <a href="{{ route('user.denda.show', $d->id) }}"
                                                                    class="btn btn-sm btn-outline-danger"
                                                                    style="font-size:.78rem;">
                                                                    <i class="fas fa-eye me-1"></i>
                                                                    {{ $d->status_pembayaran === 'belum_bayar' ? 'Bayar' : 'Detail' }}
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Sub panel: Denda Booking --}}
                        <div id="sub-denda-booking" style="display:none;">
                            <div class="section-card">
                                <div class="section-head">
                                    <h6><i class="fas fa-door-open me-2 text-danger"></i>Denda Booking Ruangan</h6>
                                </div>
                                <div class="p-0">
                                    @if ($dendaBooking->isEmpty())
                                        <div class="empty-state">
                                            <i class="fas fa-check-circle text-success"></i>
                                            Tidak ada denda booking ruangan.
                                        </div>
                                    @else
                                        <div class="table-responsive">
                                            <table class="riwayat-table">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Ruangan</th>
                                                        <th>Kondisi</th>
                                                        <th>Jumlah Denda</th>
                                                        <th>Tanggal</th>
                                                        <th>Status</th>
                                                        <th>Aksi</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($dendaBooking as $db)
                                                        <tr>
                                                            <td class="text-muted">{{ $loop->iteration }}</td>
                                                            <td class="fw-semibold">
                                                                {{ $db->booking->ruangan->nama_ruangan ?? '-' }}</td>
                                                            <td>
                                                                @php $kondisiR = $db->verifikasiBooking->kondisi_ruangan ?? '-' @endphp
                                                                @if ($kondisiR === 'kotor')
                                                                    <span class="badge-pill badge-menunggu">Kotor</span>
                                                                @elseif($kondisiR === 'rusak')
                                                                    <span class="badge-pill badge-ditolak">Rusak</span>
                                                                @else
                                                                    <span class="text-muted">-</span>
                                                                @endif
                                                            </td>
                                                            <td class="fw-bold" style="color:#c0392b;">
                                                                Rp {{ number_format($db->jumlah_denda, 0, ',', '.') }}
                                                            </td>
                                                            <td>{{ $db->tanggal_tindakan ? $db->tanggal_tindakan->format('d M Y') : '-' }}
                                                            </td>
                                                            <td>
                                                                @if ($db->status_pembayaran === 'belum_bayar')
                                                                    <span class="badge-pill badge-ditolak">Belum
                                                                        Bayar</span>
                                                                @elseif($db->status_pembayaran === 'menunggu_verifikasi')
                                                                    <span class="badge-pill badge-menunggu">Menunggu
                                                                        Verifikasi</span>
                                                                @elseif($db->status_pembayaran === 'sudah_bayar')
                                                                    <span class="badge-pill badge-selesai">Lunas</span>
                                                                @elseif($db->status_pembayaran === 'dibebaskan')
                                                                    <span class="badge-pill"
                                                                        style="background:#f1f5f9;color:#64748b;">Dibebaskan</span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                <a href="{{ route('user.denda-booking.show', $db->id) }}"
                                                                    class="btn btn-sm btn-outline-danger"
                                                                    style="font-size:.78rem;">
                                                                    <i class="fas fa-eye me-1"></i>
                                                                    {{ $db->status_pembayaran === 'belum_bayar' ? 'Bayar' : 'Detail' }}
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function switchTab(name, btn) {
            document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            document.getElementById('tab-' + name).classList.add('active');
            btn.classList.add('active');
            localStorage.setItem('riwayat_tab', name);
        }

        // Restore tab dari localStorage atau dari URL param
        document.addEventListener('DOMContentLoaded', function() {
            const urlTab = new URLSearchParams(location.search).get('tab');
            const savedTab = urlTab || localStorage.getItem('riwayat_tab') || 'booking';
            const btn = document.querySelector(`.tab-btn[onclick*="${savedTab}"]`);
            if (btn) switchTab(savedTab, btn);
        });

        function switchSubTab(name, btn) {
            document.getElementById('sub-denda-barang').style.display = name === 'barang' ? 'block' : 'none';
            document.getElementById('sub-denda-booking').style.display = name === 'booking' ? 'block' : 'none';

            document.getElementById('btn-denda-barang').className = name === 'barang' ?
                'btn btn-sm btn-danger' :
                'btn btn-sm btn-outline-danger';
            document.getElementById('btn-denda-booking').className = name === 'booking' ?
                'btn btn-sm btn-danger' :
                'btn btn-sm btn-outline-danger';

            // Update icon badge teks
            document.getElementById('btn-denda-barang').innerHTML =
                `<i class="fas fa-boxes me-1"></i>Denda Barang <span class="badge bg-white text-danger ms-1">{{ $denda->count() }}</span>`;
            document.getElementById('btn-denda-booking').innerHTML =
                `<i class="fas fa-door-open me-1"></i>Denda Booking <span class="badge bg-white text-danger ms-1">{{ $dendaBooking->count() }}</span>`;
        }
    </script>
@endpush
