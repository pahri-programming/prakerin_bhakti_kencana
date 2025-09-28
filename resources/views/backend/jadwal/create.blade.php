@extends('layouts.backend')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="card">
                <div class="card-header bg-primary text-white fw-bold">
                    Tambah Data Jadwal
                </div>
                <div class="card-body">
                    <form action="{{ route('backend.jadwal.store') }}" method="POST">
                        @csrf
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
                            <select name="ruangan_id" class="form-select @error('ruangan_id') is-invalid @enderror"
                                id="ruangan_id" aria-label="Pilih Ruangan">
                                <option value="" disabled selected>Pilih Ruangan</option>
                                @foreach ($ruangan as $data)
                                    <option value="{{ $data->id }}"
                                        {{ old('ruangan_id') == $data->id ? 'selected' : '' }}>
                                        {{ $data->nama_ruangan }}</option>
                                @endforeach
                            </select>
                            <label for="ruangan_id">
                                <i class="ti ti-door me-2 fs-4"></i>Ruangan
                            </label>
                            @error('ruangan_id')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="form-floating mb-3">
                            <input type="text" name="kegiatan"
                                class="form-control @error('kegiatan') is-invalid @enderror" placeholder="Kegiatan"
                                value="{{ old('kegiatan') }}">
                            <label>
                                <i class="ti ti-list me-2 fs-4"></i>Kegiatan
                            </label>
                            @error('kegiatan')
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
                                <a href="{{ route('backend.jadwal.index') }}" class="btn btn-secondary hstack gap-2">
                                    <i class="ti ti-arrow-back-up fs-5"></i> Kembali
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
