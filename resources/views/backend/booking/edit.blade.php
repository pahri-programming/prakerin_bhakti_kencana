@extends('layouts.backend')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="card">
                <div class="card-header bg-primary text-white fw-bold">
                    Edit Data Booking
                </div>
                <div class="card-body">
                    <form action="{{ route('backend.booking.update', $booking->id) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        {{-- Pilih Pengguna --}}
                        <div class="form-floating mb-3">
                            <select name="user_id" class="form-select @error('user_id') is-invalid @enderror" id="user_id">
                                <option value="" disabled selected>Pilih Pengguna</option>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}"
                                        {{ old('user_id', $booking->user_id) == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                            <label for="user_id">
                                <i class="ti ti-user me-2 fs-4"></i>Pengguna
                            </label>
                            @error('user_id')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="form-floating mb-3">
                            <input type="date" name="tanggal" class="form-control @error('tanggal') is-invalid @enderror"
                                placeholder="Tanggal" value="{{ old('tanggal', $booking->tanggal) }}">
                            <label>
                                <i class="ti ti-calendar me-2 fs-4"></i>Tanggal
                            </label>
                            @error('tanggal')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="form-floating mb-3">
                            <input type="time" name="waktu_mulai"
                                class="form-control @error('waktu_mulai') is-invalid @enderror" placeholder="Waktu Mulai"
                                value="{{ old('waktu_mulai', $booking->waktu_mulai) }}">
                            <label>
                                <i class="ti ti-clock me-2 fs-4"></i>Waktu Mulai
                            </label>
                            @error('waktu_mulai')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="form-floating mb-3">
                            <input type="time" name="waktu_selesai"
                                class="form-control @error('waktu_selesai') is-invalid @enderror"
                                placeholder="Waktu Selesai" value="{{ old('waktu_selesai', $booking->waktu_selesai) }}">
                            <label>
                                <i class="ti ti-clock me-2 fs-4"></i>Waktu Selesai
                            </label>
                            @error('waktu_selesai')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="form-floating mb-3">
                            <select name="ruang_id" class="form-select @error('ruang_id') is-invalid @enderror"
                                id="ruang_id">
                                <option value="" disabled selected>Pilih Ruangan</option>
                                @foreach ($ruangan as $ruang)
                                    <option value="{{ $ruang->id }}"
                                        {{ old('ruang_id', $booking->ruang_id) == $ruang->id ? 'selected' : '' }}>
                                        {{ $ruang->nama_ruangan }}
                                    </option>
                                @endforeach
                            </select>
                            <label for="ruang_id">
                                <i class="ti ti-doorme-2 fs-4"></i>Ruangan
                            </label>
                            @error('ruang_id')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="form-floating mb-3">
                            <select name="status" class="form-select @error('status') is-invalid @enderror" id="status">
                                <option value="" disabled selected>Pilih Status</option>
                                <option value="Pending"
                                    {{ old('status', $booking->status) == 'Pending' ? 'selected' : '' }}>Pending</option>
                                <option value="Diterima"
                                    {{ old('status', $booking->status) == 'Diterima' ? 'selected' : '' }}>Diterima</option>
                                <option value="Ditolak"
                                    {{ old('status', $booking->status) == 'Ditolak' ? 'selected' : '' }}>Ditolak</option>
                                <option value="Selesai"
                                    {{ old('status', $booking->status) == 'Selesai' ? 'selected' : '' }}>Selesai</option>
                            </select>
                            <label for="status">
                                <i class="ti ti-info-circle me-2 fs-4"></i>Status
                            </label>
                            @error('status')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        @if ($booking->status == 'Ditolak' && $booking->keterangan)
                            <div class="form-floating mb-3">
                                <label class="form-label fw-semibold"><i class="bi bi-chat-dots me-2"></i>Alasan Penolakan
                                    (jika ditolak)</label>
                                <textarea name="keterangan" rows="3" class="form-control"
                                    placeholder="Contoh: Ruangan sudah dipesan pada jam tersebut">{{ old('keterangan', $booking->keterangan) }}</textarea>
                            </div>
                        @endif
                        <button type="submit" class="btn btn-success">
                            <i class="ti ti-check me-1"></i>Update Booking
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
