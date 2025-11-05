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

                @if (request('jenis') && ($bookings->isNotEmpty() || $peminjamans->isNotEmpty()))
                    <a href="{{ route('backend.laporan-ubk.pdf') }}?{{ http_build_query(request()->all()) }}"
                        class="btn btn-danger btn-sm" target="_blank">
                        <i class="fa fa-file-pdf"></i> Export PDF
                    </a>
                @endif
            </div>

            {{-- FILTER FORM --}}
            <div class="p-4 bg-light border-bottom">
                <form method="GET" class="row g-3 align-items-end">

                    {{-- Jenis Laporan --}}
                    <div class="col-md-4">
                        <div class="form-floating">
                            <select name="jenis" class="form-select" id="jenis" required>
                                <option value=""> Pilih Jenis Laporan </option>
                                <option value="booking" {{ request('jenis') == 'booking' ? 'selected' : '' }}>Booking
                                    Ruangan</option>
                                <option value="peminjaman" {{ request('jenis') == 'peminjaman' ? 'selected' : '' }}>
                                    Peminjaman Barang</option>
                            </select>
                            <label for="jenis"><i class="ti ti-folder me-2 fs-4"></i>Jenis Laporan</label>
                        </div>
                    </div>

                    {{-- Start Date --}}
                    <div class="col-md-3">
                        <div class="form-floating">
                            <input type="date" name="start_date" class="form-control" id="start_date"
                                value="{{ request('start_date') }}">
                            <label for="start_date"><i class="ti ti-calendar me-2 fs-4"></i>Tanggal Awal</label>
                        </div>
                    </div>

                    {{-- End Date --}}
                    <div class="col-md-3">
                        <div class="form-floating">
                            <input type="date" name="end_date" class="form-control" id="end_date"
                                value="{{ request('end_date') }}">
                            <label for="end_date"><i class="ti ti-calendar-event me-2 fs-4"></i>Tanggal Akhir</label>
                        </div>
                    </div>

                    {{-- Tombol Filter & Reset --}}
                    <div class="col-md-2 d-flex gap-2">
                        <button class="btn btn-info text-white w-100">
                            <i class="ti ti-filter me-1"></i> Filter
                        </button>
                        <a href="{{ route('backend.laporan-ubk.index') }}" class="btn btn-secondary w-100">
                            <i class="ti ti-refresh me-1"></i> Reset
                        </a>
                    </div>

                </form>
            </div>

            {{-- BOOKING TABLE --}}
            @if (request('jenis') === 'booking' && $bookings->isNotEmpty())
                <div class="p-4">
                    <div class="total-box">
                        <h4><strong>{{ $total_booking }}</strong> Orang</h4>
                        <p class="mb-0">Sedang booking ruangan</p>
                    </div>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" id="tabelBooking">
                            <thead class="table-info">
                                <tr>
                                    <th>No</th>
                                    <th>Nama Customer</th>
                                    <th>Ruangan</th>
                                    <th>Tanggal</th>
                                    <th>Waktu</th>
                                    <th>Status</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($bookings as $data)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $data->nama }}</td>
                                        <td>{{ $data->item }}</td>
                                        <td>{{ $data->tanggal_indonesia ?? $data->tanggal_format }}</td>
                                        <td>
                                            @php
                                                [$mulai, $selesai] = explode(' - ', $data->waktu);
                                            @endphp

                                        {{ $mulai }} - {{ $selesai }}
                                        </td>
                                        <td>
                                            @switch($data->status_laporan ?? $data->status)
                                                @case('Menunggu')
                                                @case('Pending')
                                                    <span class="badge bg-warning text-dark">Pending</span>
                                                @break

                                                @case('Disetujui')
                                                @case('Diterima')
                                                    <span class="badge bg-primary">Diterima</span>
                                                @break

                                                @case('Dipinjam')
                                                    <span class="badge bg-info text-dark">Dipinjam</span>
                                                @break

                                                @case('Selesai')
                                                    <span class="badge bg-success">Selesai</span>
                                                @break

                                                @default
                                                    <span class="badge bg-secondary">Tidak Diketahui</span>
                                            @endswitch
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>


                {{-- PEMINJAMAN TABLE --}}
            @elseif (request('jenis') === 'peminjaman' && $peminjamans->isNotEmpty())
                <div class="p-4">
                    <div class="total-box">
                        <h4><strong>{{ $total_peminjaman }}</strong> Orang</h4>
                        <p class="mb-0">Sedang meminjam barang</p>
                    </div>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" id="tabelPeminjaman">
                            <thead class="table-info">
                                <tr>
                                    <th>No</th>
                                    <th>Nama Peminjam</th>
                                    <th>Barang</th>
                                    <th>Jumlah</th>
                                    <th>Tanggal</th>
                                    <th>Waktu</th>
                                    <th>Status</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($peminjamans as $p)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $p->nama }}</td>
                                        <td>{{ $p->item }}</td>
                                        <td>{{ $p->jumlah }}</td>
                                        <td>{{ $p->tanggal_indonesia ?? $p->tanggal_format }}</td>
                                        <td>
                                            @php
                                                [$mulai, $selesai] = explode(' - ', $p->waktu);
                                            @endphp

                                        {{ $mulai }} - {{ $selesai }}
                                        </td>

                                        <td>
                                            @if ($p->status_laporan == 'Menunggu' || $p->status == 'menunggu')
                                                <span class="badge bg-warning">Menunggu</span>
                                            @elseif ($p->status_laporan == 'Disetujui' || $p->status == 'disetujui')
                                                <span class="badge bg-info">Disetujui</span>
                                            @elseif ($p->status_laporan == 'Dipinjam' || $p->status == 'dipinjam')
                                                <span class="badge bg-primary">Dipinjam</span>
                                            @elseif ($p->status_laporan == 'Selesai' || $p->status == 'dikembalikan')
                                                <span class="badge bg-success">Selesai</span>
                                            @elseif ($p->status == 'ditolak')
                                                <span class="badge bg-danger">Ditolak</span>
                                            @else
                                                <span class="badge bg-secondary">Tidak Diketahui</span>
                                            @endif
                                        </td>
                                        <td>{{ Str::limit($p->keterangan, 30) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>


                {{-- TIDAK ADA DATA --}}
            @elseif(request('jenis'))
                <div class="p-4 text-center text-muted">
                    <i class="ti ti-clipboard-x fs-1 d-block mb-2"></i>
                    Tidak ada data untuk periode ini.
                </div>
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
                pageLength: 10,
                order: [
                    [0, 'asc']
                ],
            });
        @elseif (request('jenis') === 'peminjaman')
            new DataTable('#tabelPeminjaman', {
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/id.json'
                },
                pageLength: 10,
                order: [
                    [0, 'asc']
                ],
            });
        @endif
    </script>
@endpush
