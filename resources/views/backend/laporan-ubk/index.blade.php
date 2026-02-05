@extends('layouts.backend')

@section('title', 'Laporan Booking & Peminjaman UBK')

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.2/css/dataTables.bootstrap5.css">
    <style>
        .page-header {
            background: white;
            padding: 1.5rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            border-left: 4px solid #ff8c00;
        }

        .page-header h2 {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 0.25rem;
            color: #2d3748;
        }

        .page-header p {
            font-size: 0.9rem;
            color: #718096;
            margin: 0;
        }

        .filter-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            margin-bottom: 1.5rem;
            overflow: hidden;
        }

        .filter-card .card-header {
            background: #ff8c00;
            color: white;
            padding: 1rem 1.5rem;
            border: none;
        }

        .filter-card .card-body {
            padding: 1.5rem;
        }

        .stats-box {
            background: #ff8c00;
            color: white;
            padding: 1.5rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            box-shadow: 0 4px 12px rgba(255, 140, 0, 0.3);
        }

        .stats-box h3 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .stats-box p {
            margin: 0;
            font-size: 1rem;
            opacity: 0.9;
        }

        .data-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        .data-card .card-header {
            background: #f8f9fa;
            padding: 1rem 1.5rem;
            border-bottom: 2px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .data-card .card-header h5 {
            margin: 0;
            font-weight: 700;
            color: #2d3748;
        }

        .btn-export-pdf {
            background: #ef4444;
            color: white;
            border: none;
            padding: 0.5rem 1.25rem;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.2s ease;
        }

        .btn-export-pdf:hover {
            background: #dc2626;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
            color: white;
        }

        .empty-state {
            text-align: center;
            padding: 3rem;
            color: #a0aec0;
        }

        .empty-state i {
            font-size: 4rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }

        .empty-state p {
            font-size: 1.1rem;
            margin: 0;
        }

        .status-badge {
            padding: 0.4rem 0.8rem;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.85rem;
            display: inline-block;
        }

        .status-pending {
            background: #fef3c7;
            color: #92400e;
        }

        .status-approved {
            background: #dbeafe;
            color: #1e40af;
        }

        .status-borrowed {
            background: #e0e7ff;
            color: #3730a3;
        }

        .status-completed {
            background: #d1fae5;
            color: #065f46;
        }

        .status-rejected {
            background: #fee2e2;
            color: #991b1b;
        }

        .table-custom {
            margin: 0;
        }

        .table-custom thead {
            background: #f8f9fa;
        }

        .table-custom thead th {
            font-weight: 700;
            color: #4a5568;
            border-bottom: 2px solid #e2e8f0;
            padding: 1rem;
        }

        .table-custom tbody td {
            padding: 1rem;
            vertical-align: middle;
        }

        .table-custom tbody tr:hover {
            background: #f7fafc;
        }

        .form-floating>label {
            color: #718096;
        }

        .form-floating>.form-control:focus~label,
        .form-floating>.form-select:focus~label {
            color: #ff8c00;
        }
    </style>
@endpush


@section('content')
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2>
                        <i class="ti ti-report me-2"></i>
                        Laporan Booking & Peminjaman
                    </h2>
                    <p>Kelola dan cetak laporan booking ruangan & peminjaman barang</p>
                </div>
            </div>
        </div>

        <!-- Filter Card -->
        <div class="filter-card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="ti ti-filter me-2"></i>
                    Filter Laporan
                </h5>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('backend.laporan-ubk.index') }}">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="form-floating">
                                <select name="jenis" class="form-select" id="jenisLaporan" required>
                                    <option value="">Pilih Jenis Laporan</option>
                                    <option value="booking" {{ request('jenis') == 'booking' ? 'selected' : '' }}>
                                        📅 Booking Ruangan
                                    </option>
                                    <option value="peminjaman" {{ request('jenis') == 'peminjaman' ? 'selected' : '' }}>
                                        📦 Peminjaman Barang
                                    </option>
                                </select>
                                <label for="jenisLaporan">
                                    <i class="ti ti-list-check me-1"></i> Jenis Laporan
                                </label>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-floating">
                                <input type="date" name="start_date" class="form-control" id="startDate"
                                    value="{{ request('start_date') }}">
                                <label for="startDate">
                                    <i class="ti ti-calendar-event me-1"></i> Tanggal Awal
                                </label>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-floating">
                                <input type="date" name="end_date" class="form-control" id="endDate"
                                    value="{{ request('end_date') }}">
                                <label for="endDate">
                                    <i class="ti ti-calendar-check me-1"></i> Tanggal Akhir
                                </label>
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-info text-white" style="height: 58px;">
                                    <i class="ti ti-search me-1"></i> Filter
                                </button>
                                <a href="{{ route('backend.laporan-ubk.index') }}" class="btn btn-secondary"
                                    style="height: 58px; display: flex; align-items: center; justify-content: center;">
                                    <i class="ti ti-refresh me-1"></i> Reset
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Booking Data -->
        @if ($isBooking)
            @if ($bookings->isNotEmpty())
                <!-- Stats Box -->
                <div class="stats-box">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-0">{{ $total_booking }}</h3>
                            <p>Total User Booking Ruangan</p>
                        </div>
                        <i class="ti ti-calendar-stats" style="font-size: 3rem; opacity: 0.3;"></i>
                    </div>
                </div>

                <!-- Data Card -->
                <div class="data-card">
                    <div class="card-header">
                        <h5>
                            <i class="ti ti-table me-2"></i>
                            Data Booking Ruangan
                        </h5>
                        <a href="{{ route('backend.laporan-ubk.pdf_booking') }}?{{ http_build_query(request()->all()) }}"
                            class="btn-export-pdf" target="_blank">
                            <i class="ti ti-file-type-pdf me-1"></i> Export PDF
                        </a>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-custom" id="tabelBooking">
                                <thead>
                                    <tr>
                                        <th width="5%">No</th>
                                        <th width="12%">Kode Booking</th>
                                        <th width="18%">Nama Customer</th>
                                        <th width="18%">Ruangan</th>
                                        <th width="15%">Tanggal</th>
                                        <th width="15%">Waktu</th>
                                        <th width="12%">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($bookings as $b)
                                        <tr>
                                            <td class="text-center">{{ $loop->iteration }}</td>
                                            <td>
                                                <span class="badge bg-primary">{{ $b->kode }}</span>
                                            </td>
                                            <td>
                                                <strong>{{ $b->nama }}</strong>
                                            </td>
                                            <td>{{ $b->item }}</td>
                                            <td>{{ $b->tanggal_indonesia ?? $b->tanggal_format }}</td>
                                            <td>
                                                @php
                                                    $range = explode(' - ', $b->waktu ?? '');
                                                    $mulai = $range[0] ?? '-';
                                                    $selesai = $range[1] ?? '-';
                                                @endphp
                                                <strong>{{ $mulai }}</strong> -
                                                <strong>{{ $selesai }}</strong>
                                            </td>
                                            <td>
                                                @php
                                                    $status = strtolower($b->status_laporan ?? $b->status);
                                                    $badgeClass = match ($status) {
                                                        'menunggu' => 'status-pending',
                                                        'disetujui' => 'status-approved',
                                                        'dipinjam' => 'status-borrowed',
                                                        'selesai' => 'status-completed',
                                                        default => 'bg-secondary',
                                                    };
                                                @endphp
                                                <span class="status-badge {{ $badgeClass }}">
                                                    {{ $b->status_laporan ?? 'Unknown' }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @else
                <div class="data-card">
                    <div class="empty-state">
                        <i class="ti ti-calendar-off"></i>
                        <p>Tidak ada data booking untuk periode yang dipilih</p>
                    </div>
                </div>
            @endif
        @endif

        <!-- Peminjaman Data -->
        @if ($isPeminjaman)
            @if ($peminjamans->isNotEmpty())
                <!-- Stats Box -->
                <div class="stats-box">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-0">{{ $total_peminjaman }}</h3>
                            <p>Total User Peminjaman Barang</p>
                        </div>
                        <i class="ti ti-package" style="font-size: 3rem; opacity: 0.3;"></i>
                    </div>
                </div>

                <!-- Data Card -->
                <div class="data-card">
                    <div class="card-header">
                        <h5>
                            <i class="ti ti-table me-2"></i>
                            Data Peminjaman Barang
                        </h5>
                        <a href="{{ route('backend.laporan-ubk.pdf_peminjaman') }}?{{ http_build_query(request()->all()) }}"
                            class="btn-export-pdf" target="_blank">
                            <i class="ti ti-file-type-pdf me-1"></i> Export PDF
                        </a>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-custom" id="tabelPeminjaman">
                                <thead>
                                    <tr>
                                        <th width="4%">No</th>
                                        <th width="10%">Kode</th>
                                        <th width="12%">Nama Peminjam</th>
                                        <th width="25%">Detail Barang</th>
                                        <th width="15%">Ruangan</th>
                                        <th width="15%">Periode Peminjaman</th>
                                        <th width="10%">Status</th>
                                        <th width="9%">Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($peminjamans as $p)
                                        <tr>
                                            <td class="text-center">{{ $loop->iteration }}</td>
                                            <td>
                                                <span class="badge bg-info">{{ $p->kode }}</span>
                                            </td>
                                            <td>
                                                <strong>{{ $p->user->name ?? 'User Dihapus' }}</strong>
                                            </td>
                                            <td>
                                                @if($p->detailbarangs && $p->detailbarangs->isNotEmpty())
                                                    <ul class="list-unstyled mb-0" style="font-size: 0.9rem;">
                                                        @foreach($p->detailbarangs as $detail)
                                                            <li class="mb-1">
                                                                <i class="ti ti-box me-1"></i>
                                                                {{ $detail->barangRuangan->barang->nama ?? '-' }}
                                                                <span class="badge bg-dark ms-1">{{ $detail->jumlah }}</span>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                @else
                                                    <em class="text-muted">Tidak ada barang</em>
                                                @endif
                                            </td>
                                            <td>
                                                @if($p->detailbarangs && $p->detailbarangs->isNotEmpty())
                                                    <ul class="list-unstyled mb-0" style="font-size: 0.85rem;">
                                                        @foreach($p->detailbarangs->unique('barang_ruangan_id') as $detail)
                                                            <li class="mb-1">
                                                                <i class="ti ti-door me-1"></i>
                                                                {{ $detail->barangRuangan->ruangan->nama_ruangan ?? '-' }}
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                @else
                                                    <em class="text-muted">-</em>
                                                @endif
                                            </td>
                                            <td>
                                                <small>
                                                    {{ \Carbon\Carbon::parse($p->tanggal_pinjam)->translatedFormat('d M Y') }}
                                                    <br>s/d<br>
                                                    {{ \Carbon\Carbon::parse($p->tanggal_kembali)->translatedFormat('d M Y') }}
                                                </small>
                                            </td>
                                            <td>
                                                @php
                                                    $status = strtolower($p->status);
                                                    $badgeClass = match ($status) {
                                                        'menunggu' => 'status-pending',
                                                        'disetujui' => 'status-approved',
                                                        'dipinjam' => 'status-borrowed',
                                                        'dikembalikan' => 'status-completed',
                                                        'ditolak' => 'status-rejected',
                                                        default => 'bg-secondary',
                                                    };
                                                    $statusLabel = match ($status) {
                                                        'menunggu' => 'Menunggu',
                                                        'disetujui' => 'Disetujui',
                                                        'dipinjam' => 'Dipinjam',
                                                        'dikembalikan' => 'Dikembalikan',
                                                        'ditolak' => 'Ditolak',
                                                        default => 'Unknown',
                                                    };
                                                @endphp
                                                <span class="status-badge {{ $badgeClass }}">
                                                    {{ $statusLabel }}
                                                </span>
                                            </td>
                                            <td>
                                                <small>{{ Str::limit($p->keterangan, 30) }}</small>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @else
                <div class="data-card">
                    <div class="empty-state">
                        <i class="ti ti-box-off"></i>
                        <p>Tidak ada data peminjaman untuk periode yang dipilih</p>
                    </div>
                </div>
            @endif
        @endif

        <!-- Initial State -->
        @if (!$isBooking && !$isPeminjaman)
            <div class="data-card">
                <div class="empty-state">
                    <i class="ti ti-file-search"></i>
                    <p>Silakan pilih jenis laporan dan tanggal untuk menampilkan data</p>
                </div>
            </div>
        @endif
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.datatables.net/2.3.2/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.3.2/js/dataTables.bootstrap5.js"></script>

    <script>
        $(document).ready(function() {
            @if ($isBooking && $bookings->isNotEmpty())
                $('#tabelBooking').DataTable({
                    language: {
                        url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/id.json'
                    },
                    order: [
                        [4, 'desc']
                    ],
                    pageLength: 25,
                    responsive: true
                });
            @endif

            @if ($isPeminjaman && $peminjamans->isNotEmpty())
                $('#tabelPeminjaman').DataTable({
                    language: {
                        url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/id.json'
                    },
                    order: [
                        [0, 'asc']
                    ],
                    pageLength: 25,
                    responsive: true
                });
            @endif
        });
    </script>
@endpush