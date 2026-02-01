@extends('layouts.backend')

@section('title', 'Dashboard')

@push('styles')
    <style>
        /* card sizing tweak */
        .dash-card {
            border-radius: 14px;
            padding: 18px;
            min-height: 120px;
        }

        .dash-card .icon {
            font-size: 44px;
            opacity: .95;
        }

        .dash-card .label {
            font-size: 18px;
            letter-spacing: .4px;
        }

        .dash-card .count {
            font-size: 26px;
            font-weight: 700;
            margin-top: 6px;
        }

        /* tables */
        .card-section {
            border-radius: 12px;
            overflow: hidden;
        }

        .table-card-header {
            background: linear-gradient(90deg, #007bff, #00b4d8);
            color: #fff;
        }

        .small-note {
            color: #6c757d;
            font-size: 13px;
        }

        /* make action buttons compact */
        .table-action .btn {
            padding: .35rem .5rem;
            font-size: .85rem;
        }
    </style>
@endpush


@section('content')
    <div class="container-fluid">
        <!-- cards -->
        <div class="row g-3 mb-4">
            <div class="col-md-3 col-sm-6">
                <a href="{{ route('backend.user.index') }}" class="text-decoration-none">
                    <div class="card dash-card shadow-sm border-0 bg-primary text-white d-flex align-items-center">
                        <div class="w-100 d-flex justify-content-between align-items-center">
                            <div>
                                <div class="label">User</div>
                                <div class="count">{{ $counts['users'] ?? 0 }}</div>
                                <div class="small-note">Total akun terdaftar</div>
                            </div>
                            <div class="icon"><i class="ti ti-user"></i></div>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-md-3 col-sm-6">
                <a href="{{ route('backend.barang.index') }}" class="text-decoration-none">
                    <div class="card dash-card shadow-sm border-0 bg-primary text-white d-flex align-items-center">
                        <div class="w-100 d-flex justify-content-between align-items-center">
                            <div>
                                <div class="label">Barang</div>
                                <div class="count">{{ $counts['barangs'] ?? 0 }}</div>
                                <div class="small-note">Total item tersedia</div>
                            </div>
                            <div class="icon"><i class="ti ti-box"></i></div>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="card dash-card shadow-sm border-0 bg-info text-white d-flex align-items-center">
                    <div class="w-100 d-flex justify-content-between align-items-center">
                        <div>
                            <div class="label">Peminjam Hari Ini</div>
                            <div class="count">{{ $counts['peminjamanHariIni'] ?? 0 }}</div>
                            <div class="small-note">Total Peminjaman hari ini</div>
                        </div>
                        <div class="icon"><i class="ti ti-calendar-event"></i></div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="card dash-card shadow-sm border-0 bg-warning text-white d-flex align-items-center">
                    <div class="w-100 d-flex justify-content-between align-items-center">
                        <div>
                            <div class="label">Pending</div>
                            <div class="count">{{ $counts['PeminjamanPending'] ?? 0 }}</div>
                            <div class="small-note">Peminjam menunggu approval</div>
                        </div>
                        <div class="icon"><i class="ti ti-clock"></i></div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <a href="{{ route('backend.booking.index') }}" class="text-decoration-none">
                    <div class="card dash-card shadow-sm border-0 bg-primary text-white d-flex align-items-center">
                        <div class="w-100 d-flex justify-content-between align-items-center">
                            <div>
                                <div class="label">Jadwal</div>
                                <div class="count">{{ $counts['jadwals'] ?? 0 }}</div>
                                <div class="small-note">Total jadwal booking</div>
                            </div>
                            <div class="icon"><i class="ti ti-calendar"></i></div>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-md-3 col-sm-6">
                <a href="{{ route('backend.ruangan.index') }}" class="text-decoration-none">
                    <div class="card dash-card shadow-sm border-0 bg-primary text-white d-flex align-items-center">
                        <div class="w-100 d-flex justify-content-between align-items-center">
                            <div>
                                <div class="label">Ruangan</div>
                                <div class="count">{{ $counts['ruangans'] ?? 0 }}</div>
                                <div class="small-note">Total ruangan tersedia</div>
                            </div>
                            <div class="icon"><i class="ti ti-building"></i></div>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="card dash-card shadow-sm border-0 bg-info text-white d-flex align-items-center">
                    <div class="w-100 d-flex justify-content-between align-items-center">
                        <div>
                            <div class="label">Booking Hari Ini</div>
                            <div class="count">{{ $counts['bookingHariIni'] ?? 0 }}</div>
                            <div class="small-note">Total booking hari ini</div>
                        </div>
                        <div class="icon"><i class="ti ti-calendar-event"></i></div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="card dash-card shadow-sm border-0 bg-warning text-white d-flex align-items-center">
                    <div class="w-100 d-flex justify-content-between align-items-center">
                        <div>
                            <div class="label">Pending</div>
                            <div class="count">{{ $counts['bookingPending'] ?? 0 }}</div>
                            <div class="small-note">Booking menunggu approval</div>
                        </div>
                        <div class="icon"><i class="ti ti-clock"></i></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- main tables section -->
        <div class="row">
            <div class="col-12">
                <!-- Booking Table -->
                <div class="card card-section shadow-sm mb-4">
                    <div class="card-header table-card-header d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0">Booking</h5>
                            <small class="small-note">Booking terbaru</small>
                        </div>
                        <div>
                            <a href="{{ route('backend.booking.index') }}" class="btn btn-light btn-sm">Lihat Semua</a>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-borderless align-middle mb-0">
                                <thead>
                                    <tr class="text-muted small">
                                        <th>Kode Booking</th>
                                        <th>Nama</th>
                                        <th>Kode Ruangan</th>
                                        <th>Nama Ruangan</th>
                                        <th>Lokasi</th>
                                        <th>Tanggal</th>
                                        <th>Jam Mulai</th>
                                        <th>Jam Selesai</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($booking as $b)
                                        <tr>
                                            <td><strong>{{ $b->kode }}</strong></td>
                                            <td>
                                                <div class="d-flex flex-column">
                                                    <strong>{{ $b->user->name }}</strong>
                                                    <small class="text-muted">{{ $b->user->email }}</small>
                                                </div>
                                            </td>
                                            <td>{{ $b->ruangan->kode_ruangan ?? '-' }}</td>
                                            <td>{{ $b->ruangan->nama_ruangan ?? '-' }}</td>
                                            <td>{{ Str::limit($b->ruangan->lokasi ?? '-', 45) }}</td>
                                            <td>{{ $b->tanggal_format }}</td>
                                            <td>{{ \Carbon\Carbon::parse($b->waktu_mulai)->format('H:i') }}</td>
                                            <td>{{ \Carbon\Carbon::parse($b->waktu_selesai)->format('H:i') }}</td>
                                            <td>
                                                @php
                                                    $map = [
                                                        'Pending' => 'warning',
                                                        'Diterima' => 'primary',
                                                        'Ditolak' => 'danger',
                                                        'Selesai' => 'success',
                                                    ];
                                                    $cls = $map[$b->status] ?? 'primary';
                                                @endphp
                                                <span class="badge bg-{{ $cls }}">{{ $b->status }}</span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="text-center text-muted py-3">
                                                Belum ada booking terbaru
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Peminjaman Barang Table -->
                <div class="card card-section shadow-sm">
                    <div class="card-header table-card-header d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0">Peminjaman Barang</h5>
                            <small class="small-note">Peminjaman terbaru</small>
                        </div>
                        <div>
                            <a href="{{ route('backend.peminjaman.index') }}" class="btn btn-light btn-sm">
                                Lihat Semua
                            </a>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-borderless align-middle mb-0">
                                <thead>
                                    <tr class="text-muted small">
                                        <th>Kode</th>
                                        <th>Peminjam</th>
                                        <th>Barang</th>
                                        <th>Total Qty</th>
                                        <th>Tanggal Pinjam</th>
                                        <th>Tanggal Kembali</th>
                                        <th>Status</th>
                                        <th>Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($peminjamanBarang as $p)
                                        <tr>
                                            <td><strong>{{ $p->kode }}</strong></td>
                                            <td>
                                                <div class="d-flex flex-column">
                                                    <strong>{{ $p->user->name }}</strong>
                                                    <small class="text-muted">{{ $p->user->email }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                {{-- ✅ Pakai accessor helper --}}
                                                {{ $p->barang_summary }}
                                            </td>
                                            <td>
                                                {{-- ✅ Total jumlah semua barang --}}
                                                <span class="badge bg-info text-dark">
                                                    {{ $p->total_jumlah }} unit
                                                </span>
                                            </td>
                                            <td>{{ $p->tanggal_pinjam_format }}</td>
                                            <td>{{ $p->tanggal_kembali_format }}</td>
                                            <td>
                                                @php
                                                    $map2 = [
                                                        'menunggu' => 'warning',
                                                        'disetujui' => 'primary',
                                                        'ditolak' => 'danger',
                                                        'dikembalikan' => 'success',
                                                    ];
                                                    $cls2 = $map2[$p->status] ?? 'secondary';
                                                @endphp
                                                <span class="badge bg-{{ $cls2 }}">
                                                    {{ ucfirst($p->status) }}
                                                </span>
                                            </td>
                                            <td>{{ Str::limit($p->keterangan ?? '-', 30) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center text-muted py-3">
                                                Belum ada peminjaman terbaru
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
