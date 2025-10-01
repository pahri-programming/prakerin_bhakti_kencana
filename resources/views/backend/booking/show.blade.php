@extends('layouts.backend')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-header bg-info text-white fw-bold">
                        Detail Data Booking
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <tr>
                                <th>Nama Customer</th>
                                <td>{{ $booking->user->name }}</td>
                            </tr>
                            <tr>
                                <th>Ruangan</th>
                                <td>{{ $booking->ruangan->nama_ruangan }}</td>
                            </tr>
                            <tr>
                                <th>Tanggal</th>
                                <td>{{ $booking->tanggal_format }}</td>
                            </tr>
                            <tr>
                                <th>Waktu Mulai</th>
                                <td>{{ $booking->waktu_mulai }}</td>
                            </tr>
                            <tr>
                                <th>Waktu Selesai</th>
                                <td>{{ $booking->waktu_selesai }}</td>
                            </tr>
                            <tr>
                                <th>Status</th>
                                <td>{{ $booking->status }}</td>
                            </tr>
                        </table>
                        <div class="d-flex justify-content-end mt-4">
                            <a href="{{ route('backend.booking.index') }}" class="btn btn-secondary hstack gap-2">
                                <i class="ti ti-arrow-back-up fs-5"></i> Kembali
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection