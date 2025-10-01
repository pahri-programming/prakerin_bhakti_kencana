@extends('layouts.backend')

@section('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.2/css/dataTables.bootstrap5.css">
@endsection

@section('content')
    <div class="container-fluid">
        <div class="card shadow-sm">
            {{-- Header --}}
            <div class="card-header bg-secondary text-white d-flex align-items-center">
                Data Booking
                <div class="d-flex gap-2 ms-auto">
                    <a href="{{ route('backend.booking.export', [
                        'ruang_id' => request('ruang_id'),
                        'tanggal' => request('tanggal'),
                        'status' => request('status'),
                    ]) }}" class="btn btn-sm btn-danger">
                        <i class="fa fa-file-pdf me-1"></i> Export Pdf
                    </a>
                    <a href="{{ route('backend.booking.create') }}" class="btn btn-success btn-sm">
                        <i class="ti ti-plus fs-5"></i> Tambah Jadwal
                    </a>
                </div>
            </div>

            {{-- Filter --}}
            <div class="px-3 py-2">
                <form action="{{ route('backend.booking.index') }}" method="GET">
                    <div class="row g-3 align-items-end">
                        {{-- Filter Ruangan --}}
                        <div class="col-md-3">
                            <div class="form-floating">
                                <select name="ruang_id" class="form-select" id="ruang_id">
                                    <option value="" disabled selected>Pilih Ruangan</option>
                                    @foreach ($ruangan as $data)
                                        <option value="{{ $data->id }}" {{ request('ruang_id') == $data->id ? 'selected' : '' }}>
                                            {{ $data->nama_ruangan }}
                                        </option>
                                    @endforeach
                                </select>
                                <label for="ruang_id">
                                    <i class="ti ti-door me-2 fs-4"></i>Ruangan
                                </label>
                            </div>
                        </div>

                        {{-- Filter Tanggal --}}
                        <div class="col-md-3">
                            <div class="form-floating">
                                <input type="date" name="tanggal" class="form-control"
                                    value="{{ request('tanggal') }}" id="tanggal">
                                <label for="tanggal">
                                    <i class="ti ti-calendar me-2 fs-4"></i>Tanggal
                                </label>
                            </div>
                        </div>

                        {{-- Filter Status --}}
                        <div class="col-md-3">
                            <div class="form-floating">
                                <select name="status" class="form-select" id="status">
                                    <option value="" disabled selected>Pilih Status</option>
                                    <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="Diterima" {{ request('status') == 'Diterima' ? 'selected' : '' }}>Diterima</option>
                                    <option value="Ditolak" {{ request('status') == 'Ditolak' ? 'selected' : '' }}>Ditolak</option>
                                    <option value="Selesai" {{ request('status') == 'Selesai' ? 'selected' : '' }}>Selesai</option>
                                </select>
                                <label for="status">
                                    <i class="ti ti-info-circle me-2 fs-4"></i>Status
                                </label>
                            </div>
                        </div>

                        {{-- Tombol Filter & Reset --}}
                        <div class="col-md-3 d-flex gap-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="ti ti-filter me-1"></i>Filter
                            </button>
                            <a href="{{ route('backend.booking.index') }}" class="btn btn-secondary w-100">
                                <i class="ti ti-refresh me-1"></i>Reset
                            </a>
                        </div>
                    </div>
                </form>
            </div>

            {{-- Tabel --}}
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table" id="dataBooking">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Customer</th>
                                <th>Ruangan</th>
                                <th>Tanggal</th>
                                <th>Waktu Mulai</th>
                                <th>Waktu Selesai</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($booking as $data)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $data->user->name }}</td>
                                    <td>{{ $data->ruangan->nama_ruangan }}</td>
                                    <td>{{ $data->tanggal_format }}</td>
                                    <td>{{ $data->waktu_mulai }}</td>
                                    <td>{{ $data->waktu_selesai }}</td>
                                    <td>
                                        @switch($data->status)
                                            @case('Pending')
                                                <span class="badge bg-warning text-dark">Pending</span>
                                            @break

                                            @case('Diterima')
                                                <span class="badge bg-primary">Diterima</span>
                                            @break

                                            @case('Ditolak')
                                                <span class="badge bg-danger">Ditolak</span>
                                            @break

                                            @case('Selesai')
                                                <span class="badge bg-success">Selesai</span>
                                            @break

                                            @default
                                                <span class="badge bg-secondary">Tidak Diketahui</span>
                                            @break
                                        @endswitch
                                    </td>
                                    <td class="text-end">
                                        <div class="d-inline-flex gap-1">
                                            <a href="{{ route('backend.booking.edit', $data->id) }}"
                                                class="btn btn-sm btn-warning">
                                                <i class="ti ti-pencil me-1"></i>Edit
                                            </a>
                                            <a href="{{ route('backend.booking.show', $data->id) }}"
                                                class="btn btn-sm btn-info">
                                                <i class="ti ti-eye me-1"></i>Detail
                                            </a>
                                            <form action="{{ route('backend.booking.destroy', $data->id) }}" method="POST"
                                                onsubmit="return confirm('Yakin ingin menghapus data ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    <i class="ti ti-trash me-1"></i>Hapus
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
    <script>
        new DataTable('#dataBooking', {
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/id.json'
            }
        });
    </script>
@endpush