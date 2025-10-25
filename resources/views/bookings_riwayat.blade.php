@extends('layouts.frontend')

@section('content')
    <div class="container py-5">
        @if ($booking->count())
            <div class="card shadow rounded-4 border-0">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <span>
                        <i class="bi bi-clock-history me-2"></i>Riwayat Booking Anda
                    </span>
                    <a href="{{ route('bookings.export', [
                        'ruang_id' => request('ruang_id'),
                        'tanggal' => request('tanggal'),
                        'status' => request('status'),
                    ]) }}"
                        class="btn btn-danger btn-sm">
                        <i class="bi bi-file-earmark-pdf me-1"></i> Export PDF
                    </a>
                </div>

                <div class="card-body">
                    {{-- Form filter di sini --}}
                    <form action="{{ route('bookings.riwayat') }}" method="GET"
                        class="row g-3 align-items-center mb-4 bg-light p-3 rounded shadow-sm">
                        <div class="col-md-4">
                            <div class="form-floating">
                                <select name="ruang_id" id="ruang_id" class="form-select">
                                    <option value="">Pilih Ruangan</option>
                                    @foreach ($ruangan as $ruangans)
                                        <option value="{{ $ruangans->id }}"
                                            {{ request('ruang_id') == $ruangans->id ? 'selected' : '' }}>
                                            {{ $ruangans->nama_ruangan }}
                                        </option>
                                    @endforeach
                                </select>
                                <label for="ruang_id"><i class="bi bi-door-open me-1"></i> Ruangan</label>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-floating">
                                <select name="status" id="status" class="form-select">
                                    <option value="">Pilih Status</option>
                                    <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending
                                    </option>
                                    <option value="Diterima" {{ request('status') == 'Diterima' ? 'selected' : '' }}>
                                        Diterima</option>
                                    <option value="Ditolak" {{ request('status') == 'Ditolak' ? 'selected' : '' }}>Ditolak
                                    </option>
                                    <option value="Selesai" {{ request('status') == 'Selesai' ? 'selected' : '' }}>Selesai
                                    </option>
                                </select>
                                <label for="status"><i class="bi bi-info-circle me-1"></i> Status</label>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-floating">
                                <input type="date" name="tanggal" id="tanggal" class="form-control"
                                    value="{{ request('tanggal') }}">
                                <label for="tanggal"><i class="bi bi-calendar3 me-1"></i> Tanggal</label>
                            </div>
                        </div>

                        <div class="col-md-2 d-flex justify-content-between gap-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-funnel-fill me-1"></i> Filter
                            </button>
                            <a href="{{ route('bookings.riwayat') }}" class="btn btn-outline-secondary w-100">
                                <i class="bi bi-arrow-repeat me-1"></i> Reset
                            </a>
                        </div>
                    </form>

                    {{-- Tabel Riwayat Booking --}}
                    <div class="table-responsive shadow-sm rounded-4">
                        <table class="table table-hover align-middle text-center mb-0">
                            <thead class="table-light">
                                <tr class="fw-semibold text-uppercase small text-secondary">
                                    <th>#</th>
                                    <th>Ruangan</th>
                                    <th>Tanggal</th>
                                    <th>Waktu</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($booking as $data)
                                    <tr>
                                        <td class="text-muted">{{ $loop->iteration }}</td>
                                        <td class="fw-medium">{{ $data->ruangan->nama_ruangan }}</td>
                                        <td>{{ \Carbon\Carbon::parse($data->tanggal)->translatedFormat('l, d F Y') }}</td>
                                        <td>{{ $data->waktu_mulai }} - {{ $data->waktu_selesai }}</td>
                                        <td>
                                            @switch($data->status)
                                                @case('Pending')
                                                    <span class="badge bg-warning text-dark px-3 py-2">Menunggu</span>
                                                @break

                                                @case('Diterima')
                                                    <span class="badge bg-primary px-3 py-2">Diterima</span>
                                                @break

                                                @case('Ditolak')
                                                    <span class="badge bg-danger px-3 py-2">Ditolak</span>
                                                @break

                                                @case('Selesai')
                                                    <span class="badge bg-success px-3 py-2">Selesai</span>
                                                @break

                                                @default
                                                    <span class="badge bg-secondary px-3 py-2">Tidak Diketahui</span>
                                            @endswitch
                                        </td>
                                    </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-muted py-4">Tidak ada data booking ditemukan.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @else
                <div class="alert alert-info text-center mt-4">
                    <i class="bi bi-info-circle-fill me-2"></i> Belum ada riwayat booking ruangan.
                </div>
            @endif
        </div>
    @endsection
