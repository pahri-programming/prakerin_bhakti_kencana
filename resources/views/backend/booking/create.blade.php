@extends('layouts.backend')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="card">
                <div class="card-header bg-primary text-white fw-bold">
                    Tambah Data Booking
                </div>
                <div class="card-body">
                    <form action="{{ route('backend.booking.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        {{-- Pilih Pengguna --}}
                        <div class="form-floating mb-3">
                            <select name="user_id" class="form-select @error('user_id') is-invalid @enderror" id="user_id">
                                <option value="" disabled selected>Pilih Pengguna</option>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
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
                                placeholder="Tanggal" value="{{ old('tanggal') }}">
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
                                value="{{ old('waktu_mulai') }}">
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
                                placeholder="Waktu Selesai" value="{{ old('waktu_selesai') }}">
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
                                id="ruang_id" aria-label="Pilih Ruangan">
                                <option value="" disabled selected>Pilih Ruangan</option>
                                @foreach ($ruangan as $data)
                                    <option value="{{ $data->id }}"
                                        {{ old('ruang_id') == $data->id ? 'selected' : '' }}>
                                        {{ $data->nama_ruangan }}</option>
                                @endforeach
                            </select>
                            <label for="ruang_id">
                                <i class="ti ti-door me-2 fs-4"></i>Ruangan
                            </label>
                            @error('ruang_id')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                          <div class="d-md-flex align-items-center">
                            <div class="mt-3 mt-md-0 ms-auto">
                                <button type="submit" class="btn btn-primary  hstack gap-6">
                                    <i class="ti ti-send fs-4"></i>
                                    Submit
                                </button>
                            </div>
                            <div class="mt-3 mt-md-0 ms-2">
                                <a href="{{ route('backend.booking.index') }}" class="btn btn-secondary hstack gap-2">
                                    <i class="ti ti-arrow-back-up fs-5"></i> Kembali
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endsection
