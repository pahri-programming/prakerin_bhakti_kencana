@extends('layouts.frontend')

@section('content')
<div class="container py-5">
    <h2 class="mb-4 fw-bold text-center text-primary">
        <i class="bi bi-clock-history me-2"></i>Riwayat Booking Anda
    </h2>

    @if ($booking->count())
        <form action="{{ route('bookings.riwayat') }}" method="GET" class="row g-2 mb-4">
            <div class="col-md-4">
                <select name="ruang_id" class="form-control">
                    <option value="">-- Pilih Ruangan --</option>
                    @foreach ($ruangan as $ruangans)
                        <option value="{{ $ruangans->id }}" {{ request('ruang_id') == $ruangans->id ? 'selected' : '' }}>
                            {{ $ruangans->nama_ruangan }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select name="status" class="form-control">
                    <option value="">-- Pilih Status --</option>
                    <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                    <option value="Diterima" {{ request('status') == 'Diterima' ? 'selected' : '' }}>Diterima</option>
                    <option value="Ditolak" {{ request('status') == 'Ditolak' ? 'selected' : '' }}>Ditolak</option>
                    <option value="Selesai" {{ request('status') == 'Selesai' ? 'selected' : '' }}>Selesai</option>
                </select>
            </div>
            <div class="col-md-3">
                <input type="date" name="tanggal" class="form-control" value="{{ request('tanggal') }}">
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary w-100">Filter</button>
                <a href="{{ route('bookings.riwayat') }}" class="btn btn-secondary">Reset</a>
            </div>
        </form>
        <div class="text-end mb-3">
            <a href="{{ route('bookings.export', [
                'ruang_id' => request('ruang_id'),
                'tanggal'  => request('tanggal'),
                'status'   => request('status'),
            ]) }}" class="btn btn-danger">
                <i class="fa fa-file-pdf"></i> Export PDF
            </a>
        </div>
        <div class="table-responsive shadow rounded-4">
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
                    @foreach ($booking as $index => $data)
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
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="alert alert-info text-center mt-4">
            <i class="bi bi-info-circle-fill me-2"></i> Belum ada riwayat booking ruangan.
        </div>
    @endif
</div>
@endsection