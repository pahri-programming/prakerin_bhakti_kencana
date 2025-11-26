@extends('layouts.backend')

@section('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.2/css/dataTables.bootstrap5.css">
    <style>
        .total-box {
            background: #e3f2fd;
            border-left: 5px solid #2196F3;
            padding: 15px;
            border-radius: 8px;
        }

        .card-header {
            background: linear-gradient(135deg, #17a2b8, #0dcaf0);
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid py-4">
        <div class="card shadow-lg border-0 rounded-4 overflow-hidden">

            {{-- HEADER --}}
            <div class="card-header text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <img src="{{ asset('assets/backend/images/logos/ubk2.png') }}" class="logo-img me-2" />
                    Laporan Booking & Peminjaman UBK
                </h5>

                @if ($isBooking && $bookings->isNotEmpty())
                    <a href="{{ route('backend.laporan-ubk.pdf_booking') }}?{{ http_build_query(request()->all()) }}"
                        class="btn btn-danger btn-sm" target="_blank">
                        <i class="fa fa-file-pdf"></i> Export PDF Booking
                    </a>
                @endif

                @if ($isPeminjaman && $peminjamans->isNotEmpty())
                    <a href="{{ route('backend.laporan-ubk.pdf_peminjaman') }}?{{ http_build_query(request()->all()) }}"
                        class="btn btn-danger btn-sm" target="_blank">
                        <i class="fa fa-file-pdf"></i> Export PDF Peminjaman
                    </a>
                @endif
            </div>

            {{-- FILTER --}}
            <div class="p-4 bg-light border-bottom">
                <form method="GET" class="row g-3 align-items-end">

                    <div class="col-md-4">
                        <div class="form-floating">
                            <select name="jenis" class="form-select" required>
                                <option value="">Pilih Jenis Laporan</option>
                                <option value="booking" {{ request('jenis') == 'booking' ? 'selected' : '' }}>
                                    Booking Ruangan
                                </option>
                                <option value="peminjaman" {{ request('jenis') == 'peminjaman' ? 'selected' : '' }}>
                                    Peminjaman Barang
                                </option>
                            </select>
                            <label>
                                <i class="bi bi-list-check me-1"></i> Jenis Laporan
                            </label>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-floating">
                            <input type="date" name="start_date" class="form-control"
                                value="{{ request('start_date') }}">
                            <label>
                                <i class="bi bi-calendar-event me-1"></i> Tanggal Awal
                            </label>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-floating">
                            <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                            <label>
                                <i class="bi bi-calendar-check me-1"></i> Tanggal Akhir
                            </label>
                        </div>
                    </div>

                    <div class="col-md-2 d-flex gap-2">
                        <button class="btn btn-info text-white w-100">
                            <i class="bi bi-funnel-fill me-1"></i> Filter
                        </button>
                        <a href="{{ route('backend.laporan-ubk.index') }}" class="btn btn-secondary w-100">
                            <i class="bi bi-arrow-clockwise me-1"></i> Reset
                        </a>
                    </div>

                </form>
            </div>

            {{-- booking --}}
            @if ($isBooking)

                @if ($bookings->isNotEmpty())
                    <div class="p-4">
                        <div class="total-box">
                            <h4><strong>{{ $total_booking }}</strong> Orang</h4>
                            <p class="mb-0">Sedang booking ruangan</p>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle" id="tabelBooking">
                                <thead class="table-info">
                                    <tr>
                                        <th>No</th>
                                        <th>Kode Booking</th>
                                        <th>Nama</th>
                                        <th>Ruangan</th>
                                        <th>Tanggal</th>
                                        <th>Waktu</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($bookings as $b)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $b->kode }}</td>
                                            <td>{{ $b->nama }}</td>
                                            <td>{{ $b->item }}</td>
                                            <td>{{ $b->tanggal_indonesia ?? $b->tanggal_format }}</td>
                                            <td>
                                                @php
                                                    $range = explode(' - ', $b->waktu ?? '');
                                                    $mulai = $range[0] ?? '-';
                                                    $selesai = $range[1] ?? '-';
                                                @endphp
                                                {{ $mulai }} - {{ $selesai }}
                                            </td>
                                            <td>
                                                @php
                                                    $status = strtolower($b->status_laporan ?? $b->status);
                                                    $map = [
                                                        'menunggu' => [
                                                            'text' => 'Pending',
                                                            'class' => 'bg-warning text-dark',
                                                        ],
                                                        'disetujui' => ['text' => 'Diterima', 'class' => 'bg-primary'],
                                                        'dipinjam' => [
                                                            'text' => 'Dipinjam',
                                                            'class' => 'bg-info text-dark',
                                                        ],
                                                        'selesai' => ['text' => 'Selesai', 'class' => 'bg-success'],
                                                    ];
                                                    $badge = $map[$status] ?? [
                                                        'text' => 'Unknown',
                                                        'class' => 'bg-secondary',
                                                    ];
                                                @endphp
                                                <span class="badge {{ $badge['class'] }}">{{ $badge['text'] }}</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @else
                    <div class="p-4 text-center text-muted">
                        <i class="ti ti-calendar-off fs-1 d-block mb-2"></i>
                        Tidak ada data booking
                    </div>
                @endif

                {{-- peminjaman --}}
            @elseif ($isPeminjaman)
                @if ($peminjamans->isNotEmpty())
                    <div class="p-4">
                        <div class="total-box">
                            <h4><strong>{{ $total_peminjaman }}</strong> Orang</h4>
                            <p class="mb-0">Sedang meminjam barang</p>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle" id="tabelPeminjaman">
                                <thead class="table-info">
                                    <tr>
                                        <th>No</th>
                                        <th>Kode</th>
                                        <th>Nama</th>
                                        <th>Barang</th>
                                        <th>Jumlah</th>
                                        <th>Tanggal Peminjaman</th>
                                        <th>Waktu</th>
                                        <th>Status</th>
                                        <th>Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($peminjamans as $p)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $p->kode }}</td>
                                            <td>{{ $p->nama }}</td>
                                            <td>{{ $p->item }}</td>
                                            <td>{{ $p->jumlah }}</td>
                                            <td>{{ $p->tanggal_indonesia ?? $p->tanggal_format }}</td>
                                            <td>
                                                @php
                                                    $range = explode(' - ', $p->waktu ?? '');
                                                    $mulai = $range[0] ?? '-';
                                                    $selesai = $range[1] ?? '-';
                                                @endphp
                                                {{ $mulai }} - {{ $selesai }}
                                            </td>
                                            <td>
                                                @php
                                                    $status = strtolower($p->status_laporan ?? $p->status);
                                                    $map = [
                                                        'menunggu' => ['text' => 'Menunggu', 'class' => 'bg-warning'],
                                                        'disetujui' => ['text' => 'Disetujui', 'class' => 'bg-info'],
                                                        'dipinjam' => ['text' => 'Dipinjam', 'class' => 'bg-primary'],
                                                        'dikembalikan' => [
                                                            'text' => 'Selesai',
                                                            'class' => 'bg-success',
                                                        ],
                                                        'selesai' => ['text' => 'Selesai', 'class' => 'bg-success'],
                                                        'ditolak' => ['text' => 'Ditolak', 'class' => 'bg-danger'],
                                                    ];
                                                    $badge = $map[$status] ?? [
                                                        'text' => 'Unknown',
                                                        'class' => 'bg-secondary',
                                                    ];
                                                @endphp
                                                <span class="badge {{ $badge['class'] }}">{{ $badge['text'] }}</span>
                                            </td>
                                            <td>{{ Str::limit($p->keterangan, 30) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @else
                    <div class="p-4 text-center text-muted">
                        <i class="ti ti-box-off fs-1 d-block mb-2"></i>
                        Tidak ada data peminjaman
                    </div>
                @endif

            @endif
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.datatables.net/2.3.2/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.3.2/js/dataTables.bootstrap5.js"></script>

    <script>
        @if (request('jenis') === 'booking')
            new DataTable('#tabelBooking', {
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/id.json'
                },
                order: [
                    [4, 'desc']
                ]
            });
        @endif

        @if (request('jenis') === 'peminjaman')
            new DataTable('#tabelPeminjaman', {
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/id.json'
                },
                order: [
                    [0, 'asc']
                ]
            });
        @endif
    </script>
@endpush
