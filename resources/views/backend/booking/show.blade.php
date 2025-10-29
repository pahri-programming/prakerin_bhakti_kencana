@extends('layouts.backend')

@section('title', 'Detail Booking')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="card">
                <div class="card-header bg-info text-white fw-bold">
                    Detail Data Booking
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <table class="table table-borderless">
                                <tbody>
                                    <tr>
                                        <th style="width:170px">Nama Customer</th>
                                        <td>
                                            {{ $booking->user->name }}
                                            @if (!empty($booking->user->email))
                                                <small class="text-muted">({{ $booking->user->email }})</small>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Ruangan</th>
                                        <td>{{ $booking->ruangan->nama_ruangan ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Tanggal</th>
                                        <td>{{ $booking->tanggal_format ?? $booking->tanggal }}</td>
                                    </tr>
                                    <tr>
                                        <th>Waktu</th>
                                        <td>{{ $booking->waktu_range ?? $booking->waktu_mulai . ' - ' . $booking->waktu_selesai }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Status</th>
                                        <td>
                                            @php
                                                switch ($booking->status) {
                                                    case 'menunggu':
                                                        $c = 'secondary';
                                                        break;
                                                    case 'disetujui':
                                                        $c = 'primary';
                                                        break;
                                                    case 'dipinjam':
                                                        $c = 'info';
                                                        break;
                                                    case 'dikembalikan':
                                                        $c = 'success';
                                                        break;
                                                    case 'selesai':
                                                        $c = 'dark';
                                                        break;
                                                    case 'ditolak':
                                                        $c = 'danger';
                                                        break;
                                                    default:
                                                        $c = 'secondary';
                                                }
                                            @endphp
                                            <span
                                                class="badge bg-{{ $c }}">{{ ucfirst($booking->status) }}</span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="col-md-4">
                            <div class="card shadow-sm">
                                <div class="card-body">
                                    <p class="mb-1"><strong>Diajukan pada</strong></p>
                                    <p class="text-muted mb-3">{{ $booking->created_at->translatedFormat('d F Y H:i') }}</p>

                                    @if ($booking->updated_at && $booking->updated_at != $booking->created_at)
                                        <p class="mb-1"><strong>Terakhir diubah</strong></p>
                                        <p class="text-muted">{{ $booking->updated_at->translatedFormat('d F Y H:i') }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-3">
                        <a href="{{ route('backend.booking.index') }}" class="btn btn-secondary">
                            <i class="ti ti-arrow-left me-1"></i> Kembali
                        </a>

                        <a href="{{ route('backend.booking.edit', $booking->id) }}" class="btn btn-primary ms-2">
                            <i class="ti ti-edit me-1"></i> Edit
                        </a>

                        <form action="{{ route('backend.booking.destroy', $booking->id) }}" method="POST"
                            class="d-inline-block ms-2"
                            onsubmit="return confirm('Apakah anda yakin ingin menghapus data ini?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i class="ti ti-trash me-1"></i> Hapus
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
