@extends('layouts.backend')

@section('title', 'Manajemen Booking')

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.2/css/dataTables.bootstrap5.css">
    <style>
        :root {
            --orange-primary: #F97316;
            --orange-dark:    #EA580C;
            --orange-light:   #FFF7ED;
            --orange-border:  #FED7AA;
        }

        .page-hero {
            background: linear-gradient(135deg, var(--orange-primary) 0%, var(--orange-dark) 100%);
            border-radius: 20px;
            padding: 2rem 2.5rem;
            margin-bottom: 2rem;
            position: relative;
            overflow: hidden;
        }

        .page-hero::before {
            content: '';
            position: absolute;
            top: -40px; right: -40px;
            width: 200px; height: 200px;
            background: rgba(255,255,255,0.08);
            border-radius: 50%;
        }

        .page-hero::after {
            content: '';
            position: absolute;
            bottom: -60px; right: 80px;
            width: 150px; height: 150px;
            background: rgba(255,255,255,0.05);
            border-radius: 50%;
        }

        .page-hero h2 {
            font-size: 1.75rem;
            font-weight: 800;
            color: white;
            margin: 0 0 0.25rem;
        }

        .page-hero p {
            color: rgba(255,255,255,0.8);
            margin: 0;
            font-size: 0.95rem;
        }

        /* Stats Cards */
        .stat-card {
            background: white;
            border-radius: 16px;
            padding: 1.25rem 1.5rem;
            box-shadow: 0 2px 12px rgba(0,0,0,0.06);
            border: 1px solid #F3F4F6;
            display: flex;
            align-items: center;
            gap: 1rem;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(0,0,0,0.1);
        }

        .stat-icon {
            width: 52px; height: 52px;
            border-radius: 14px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.4rem;
            flex-shrink: 0;
        }

        .stat-icon.pending  { background: #FEF3C7; color: #D97706; }
        .stat-icon.diterima { background: #D1FAE5; color: #059669; }
        .stat-icon.ditolak  { background: #FEE2E2; color: #DC2626; }
        .stat-icon.selesai  { background: #EDE9FE; color: #7C3AED; }

        .stat-value {
            font-size: 1.75rem;
            font-weight: 800;
            line-height: 1;
            color: #111827;
        }

        .stat-label {
            font-size: 0.8rem;
            color: #6B7280;
            font-weight: 500;
            margin-top: 2px;
        }

        /* Filter Card */
        .filter-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.06);
            border: 1px solid #F3F4F6;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .filter-card .form-control,
        .filter-card .form-select {
            border: 1.5px solid #E5E7EB;
            border-radius: 10px;
            padding: 0.6rem 0.9rem;
            font-size: 0.9rem;
            transition: border-color 0.2s;
        }

        .filter-card .form-control:focus,
        .filter-card .form-select:focus {
            border-color: var(--orange-primary);
            box-shadow: 0 0 0 3px rgba(249,115,22,0.1);
        }

        /* Table Card */
        .table-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.06);
            border: 1px solid #F3F4F6;
            overflow: hidden;
        }

        .table-card .card-header {
            background: white;
            border-bottom: 1.5px solid #F3F4F6;
            padding: 1.25rem 1.5rem;
        }

        /* Table */
        #dataBooking thead tr th {
            background: linear-gradient(135deg, var(--orange-primary), var(--orange-dark));
            color: white;
            font-weight: 600;
            font-size: 0.85rem;
            padding: 1rem 1.25rem;
            border: none;
            white-space: nowrap;
        }

        #dataBooking tbody tr {
            transition: background 0.15s;
        }

        #dataBooking tbody tr:hover {
            background: var(--orange-light);
        }

        #dataBooking tbody td {
            padding: 1rem 1.25rem;
            vertical-align: middle;
            border-color: #F9FAFB;
            font-size: 0.9rem;
        }

        /* Kode Badge */
        .kode-badge {
            background: #F3F4F6;
            color: #374151;
            padding: 0.3rem 0.75rem;
            border-radius: 8px;
            font-weight: 700;
            font-size: 0.82rem;
            font-family: 'Courier New', monospace;
            letter-spacing: 0.3px;
        }

        /* Status Badge */
        .status-pill {
            padding: 0.35rem 0.85rem;
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.78rem;
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            white-space: nowrap;
        }

        .status-pill.pending  { background: #FEF3C7; color: #92400E; }
        .status-pill.diterima { background: #D1FAE5; color: #065F46; }
        .status-pill.ditolak  { background: #FEE2E2; color: #991B1B; }
        .status-pill.selesai  { background: #EDE9FE; color: #4C1D95; }

        /* User Avatar */
        .user-avatar {
            width: 36px; height: 36px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--orange-primary), var(--orange-dark));
            color: white;
            display: flex; align-items: center; justify-content: center;
            font-weight: 700;
            font-size: 0.9rem;
            flex-shrink: 0;
        }

        /* Time Badge */
        .time-range {
            background: var(--orange-light);
            color: var(--orange-dark);
            padding: 0.25rem 0.6rem;
            border-radius: 6px;
            font-weight: 600;
            font-size: 0.82rem;
            white-space: nowrap;
        }

        /* Action Buttons */
        .btn-action {
            width: 30px; height: 30px;
            border-radius: 8px;
            border: none;
            display: inline-flex; align-items: center; justify-content: center;
            font-size: 0.85rem;
            transition: all 0.15s;
            text-decoration: none;
        }

        .btn-action:hover { transform: translateY(-1px); }
        .btn-action.approve  { background: #D1FAE5; color: #059669; }
        .btn-action.reject   { background: #FEE2E2; color: #DC2626; }
        .btn-action.complete { background: #EDE9FE; color: #7C3AED; }
        .btn-action.view     { background: #DBEAFE; color: #2563EB; }
        .btn-action.edit     { background: #FEF3C7; color: #D97706; }
        .btn-action.delete   { background: #FEE2E2; color: #DC2626; }

        /* Keterangan chip */
        .note-chip {
            background: #F3F4F6;
            color: #6B7280;
            font-size: 0.75rem;
            padding: 0.15rem 0.5rem;
            border-radius: 20px;
            display: inline-flex; align-items: center; gap: 0.25rem;
            margin-top: 4px;
            cursor: pointer;
        }

        /* Buttons */
        .btn-orange {
            background: var(--orange-primary);
            color: white;
            border: none;
            border-radius: 10px;
            padding: 0.6rem 1.25rem;
            font-weight: 600;
            font-size: 0.9rem;
            transition: all 0.2s;
        }

        .btn-orange:hover {
            background: var(--orange-dark);
            color: white;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(249,115,22,0.3);
        }

        .btn-outline-orange {
            background: white;
            color: var(--orange-primary);
            border: 1.5px solid var(--orange-border);
            border-radius: 10px;
            padding: 0.6rem 1.25rem;
            font-weight: 600;
            font-size: 0.9rem;
            transition: all 0.2s;
        }

        .btn-outline-orange:hover {
            background: var(--orange-light);
            color: var(--orange-dark);
        }

        /* DataTables override */
        .dataTables_wrapper .dataTables_filter input {
            border: 1.5px solid #E5E7EB;
            border-radius: 10px;
            padding: 0.4rem 0.8rem;
            font-size: 0.9rem;
        }

        .dataTables_wrapper .dataTables_filter input:focus {
            border-color: var(--orange-primary);
            outline: none;
        }

        .dataTables_wrapper .dataTables_length select {
            border: 1.5px solid #E5E7EB;
            border-radius: 8px;
            padding: 0.3rem 0.5rem;
        }

        .page-item.active .page-link {
            background: var(--orange-primary);
            border-color: var(--orange-primary);
        }

        .page-link { color: var(--orange-primary); }
    </style>
@endpush

@section('content')
<div class="container-fluid px-4">

    {{-- ── HERO HEADER ─────────────────────────────────── --}}
    <div class="page-hero mb-4">
        <div class="d-flex justify-content-between align-items-center position-relative">
            <div>
                <h2><i class="ti ti-calendar-event me-2"></i>Manajemen Booking</h2>
                <p>Kelola dan pantau semua pengajuan booking ruangan</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('backend.booking.export', request()->all()) }}"
                   class="btn btn-light btn-sm px-3 fw-600">
                    <i class="ti ti-file-pdf me-1 text-danger"></i> Export PDF
                </a>
                <a href="{{ route('backend.booking.create') }}"
                   class="btn btn-white btn-sm px-3 fw-600"
                   style="background:white; color: var(--orange-primary);">
                    <i class="ti ti-plus me-1"></i> Tambah Booking
                </a>
            </div>
        </div>
    </div>

    {{-- ── STAT CARDS ───────────────────────────────────── --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="stat-card">
                <div class="stat-icon pending">
                    <i class="ti ti-clock"></i>
                </div>
                <div>
                    <div class="stat-value">{{ $booking->where('status','Pending')->count() }}</div>
                    <div class="stat-label">Pending</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card">
                <div class="stat-icon diterima">
                    <i class="ti ti-check"></i>
                </div>
                <div>
                    <div class="stat-value">{{ $booking->where('status','Diterima')->count() }}</div>
                    <div class="stat-label">Diterima</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card">
                <div class="stat-icon ditolak">
                    <i class="ti ti-x"></i>
                </div>
                <div>
                    <div class="stat-value">{{ $booking->where('status','Ditolak')->count() }}</div>
                    <div class="stat-label">Ditolak</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card">
                <div class="stat-icon selesai">
                    <i class="ti ti-circle-check"></i>
                </div>
                <div>
                    <div class="stat-value">{{ $booking->where('status','Selesai')->count() }}</div>
                    <div class="stat-label">Selesai</div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── FILTER ───────────────────────────────────────── --}}
    <div class="filter-card">
        <form action="{{ route('backend.booking.index') }}" method="GET">
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label fw-600 small mb-1">
                        <i class="ti ti-door me-1 text-muted"></i>Ruangan
                    </label>
                    <select name="ruang_id" class="form-select">
                        <option value="">Semua Ruangan</option>
                        @foreach ($ruangan as $r)
                            <option value="{{ $r->id }}" {{ request('ruang_id') == $r->id ? 'selected' : '' }}>
                                {{ $r->nama_ruangan }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-600 small mb-1">
                        <i class="ti ti-calendar me-1 text-muted"></i>Tanggal
                    </label>
                    <input type="date" name="tanggal" class="form-control" value="{{ request('tanggal') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-600 small mb-1">
                        <i class="ti ti-adjustments me-1 text-muted"></i>Status
                    </label>
                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        @foreach(['Pending','Diterima','Ditolak','Selesai'] as $s)
                            <option value="{{ $s }}" {{ request('status') == $s ? 'selected' : '' }}>{{ $s }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn-orange flex-fill">
                        <i class="ti ti-search me-1"></i> Filter
                    </button>
                    @if(request()->hasAny(['ruang_id','tanggal','status']))
                        <a href="{{ route('backend.booking.index') }}" class="btn-outline-orange">
                            <i class="ti ti-x"></i>
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    {{-- ── TABLE ────────────────────────────────────────── --}}
    <div class="table-card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <h6 class="fw-bold mb-0 text-gray-800">
                    <i class="ti ti-list me-2 text-orange"></i>Daftar Booking
                </h6>
                <small class="text-muted">Total {{ $booking->count() }} data</small>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="dataBooking">
                    <thead>
                        <tr>
                            <th width="3%">#</th>
                            <th width="12%">Kode</th>
                            <th width="17%">Peminjam</th>
                            <th width="15%">Ruangan</th>
                            <th width="11%">Tanggal</th>
                            <th width="11%">Waktu</th>
                            <th width="12%">Status</th>
                            <th width="19%" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($booking as $data)
                        <tr>
                            <td class="fw-600 text-muted">{{ $loop->iteration }}</td>

                            <td>
                                <span class="kode-badge">{{ $data->kode }}</span>
                            </td>

                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="user-avatar">
                                        {{ strtoupper(substr($data->user->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="fw-600" style="font-size:0.88rem">{{ $data->user->name }}</div>
                                        <div class="text-muted" style="font-size:0.78rem">{{ $data->user->email }}</div>
                                    </div>
                                </div>
                            </td>

                            <td>
                                <div class="fw-600" style="font-size:0.88rem">
                                    <i class="ti ti-door me-1" style="color:var(--orange-primary)"></i>
                                    {{ $data->ruangan->nama_ruangan }}
                                </div>
                                @if($data->ruangan->lokasi)
                                    <div class="text-muted" style="font-size:0.78rem">
                                        <i class="ti ti-map-pin me-1"></i>{{ $data->ruangan->lokasi }}
                                    </div>
                                @endif
                            </td>

                            <td>
                                <div class="fw-600" style="font-size:0.88rem">{{ $data->tanggal_format }}</div>
                                <div class="text-muted" style="font-size:0.78rem">{{ $data->hari }}</div>
                            </td>

                            <td>
                                <span class="time-range">
                                    {{ substr($data->waktu_mulai,0,5) }} – {{ substr($data->waktu_selesai,0,5) }}
                                </span>
                            </td>

                            <td>
                                @php
                                    $pill = match($data->status) {
                                        'Pending'  => ['pending',  'ti-clock',        'Pending'],
                                        'Diterima' => ['diterima', 'ti-check',        'Diterima'],
                                        'Ditolak'  => ['ditolak',  'ti-x',            'Ditolak'],
                                        'Selesai'  => ['selesai',  'ti-circle-check', 'Selesai'],
                                        default    => ['pending',  'ti-help',         $data->status],
                                    };
                                @endphp
                                <span class="status-pill {{ $pill[0] }}">
                                    <i class="ti {{ $pill[1] }}"></i> {{ $pill[2] }}
                                </span>

                                @if($data->keterangan && $data->status !== 'Ditolak')
                                    <br>
                                    <span class="note-chip" data-bs-toggle="tooltip" title="{{ $data->keterangan }}">
                                        <i class="ti ti-notes"></i> Keterangan
                                    </span>
                                @endif

                                @if($data->status === 'Ditolak' && $data->keterangan)
                                    <br>
                                    <span class="note-chip text-danger" style="background:#FEE2E2"
                                          data-bs-toggle="tooltip" title="{{ $data->keterangan }}">
                                        <i class="ti ti-alert-circle"></i> Alasan
                                    </span>
                                @endif
                            </td>

                            <td>
                                <div class="d-flex gap-1 justify-content-center flex-wrap">
                                    {{-- Approve --}}
                                    @if($data->status === 'Pending')
                                        <form action="{{ route('backend.booking.approve', $data->id) }}"
                                              method="POST" class="d-inline">
                                            @csrf @method('PATCH')
                                            <button type="submit" class="btn-action approve"
                                                    data-bs-toggle="tooltip" title="Terima"
                                                    onclick="return confirm('Setujui booking ini?')">
                                                <i class="ti ti-check"></i>
                                            </button>
                                        </form>
                                        <button type="button" class="btn-action reject"
                                                data-bs-toggle="tooltip" title="Tolak"
                                                onclick="rejectBooking({{ $data->id }})">
                                            <i class="ti ti-x"></i>
                                        </button>
                                    @endif

                                    {{-- Complete --}}
                                    @if($data->status === 'Diterima')
                                        <form action="{{ route('backend.booking.complete', $data->id) }}"
                                              method="POST" class="d-inline">
                                            @csrf @method('PATCH')
                                            <button type="submit" class="btn-action complete"
                                                    data-bs-toggle="tooltip" title="Selesaikan"
                                                    onclick="return confirm('Selesaikan booking ini?')">
                                                <i class="ti ti-circle-check"></i>
                                            </button>
                                        </form>
                                    @endif

                                    <a href="{{ route('backend.booking.show', $data->id) }}"
                                       class="btn-action view" data-bs-toggle="tooltip" title="Detail">
                                        <i class="ti ti-eye"></i>
                                    </a>
                                    <a href="{{ route('backend.booking.edit', $data->id) }}"
                                       class="btn-action edit" data-bs-toggle="tooltip" title="Edit">
                                        <i class="ti ti-edit"></i>
                                    </a>
                                    <form id="del-{{ $data->id }}"
                                          action="{{ route('backend.booking.destroy', $data->id) }}"
                                          method="POST" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button type="button" class="btn-action delete"
                                                data-bs-toggle="tooltip" title="Hapus"
                                                onclick="confirmDelete({{ $data->id }})">
                                            <i class="ti ti-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.datatables.net/2.3.2/js/dataTables.js"></script>
<script src="https://cdn.datatables.net/2.3.2/js/dataTables.bootstrap5.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function () {
    $('#dataBooking').DataTable({
        language: { url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/id.json' },
        order: [[4, 'desc']],
        pageLength: 15,
    });

    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
        new bootstrap.Tooltip(el);
    });
});

function confirmDelete(id) {
    Swal.fire({
        title: 'Hapus Booking?',
        text: 'Data akan dihapus permanen.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#EF4444',
        cancelButtonColor: '#6B7280',
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal',
        borderRadius: '16px',
    }).then(r => { if (r.isConfirmed) document.getElementById('del-' + id).submit(); });
}

function rejectBooking(id) {
    Swal.fire({
        title: 'Tolak Booking',
        input: 'textarea',
        inputLabel: 'Alasan Penolakan',
        inputPlaceholder: 'Tuliskan alasan penolakan...',
        showCancelButton: true,
        confirmButtonColor: '#EF4444',
        cancelButtonColor: '#6B7280',
        confirmButtonText: 'Tolak',
        cancelButtonText: 'Batal',
        preConfirm: v => { if (!v) Swal.showValidationMessage('Alasan wajib diisi'); return v; }
    }).then(r => {
        if (!r.isConfirmed) return;
        const f = document.createElement('form');
        f.method = 'POST';
        f.action = `/admin/booking/${id}/reject`;
        f.innerHTML = `
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" name="_method" value="PATCH">
            <input type="hidden" name="keterangan" value="${r.value}">
        `;
        document.body.appendChild(f);
        f.submit();
    });
}
</script>
@endpush