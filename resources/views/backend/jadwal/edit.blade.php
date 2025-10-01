@extends('layouts.backend')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="card">
                <div class="card-header bg-primary text-white fw-bold">
                    Edit Data Jadwal
                </div>
                <div class="card-body">
                    <form action="{{ route('backend.jadwal.update', $jadwal->id) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="form-floating mb-3">
                            <input type="date" name="tanggal" class="form-control @error('tanggal') is-invalid @enderror"
                                placeholder="Tanggal" value="{{ old('tanggal', $jadwal->tanggal) }}">
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
                                value="{{ old('waktu_mulai', $jadwal->waktu_mulai) }}">
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
                                placeholder="Waktu Selesai" value="{{ old('waktu_selesai', $jadwal->waktu_selesai) }}">
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
                                <option value="" disabled>Pilih Ruangan</option>
                                @foreach ($ruangan as $data)
                                    <option value="{{ $data->id }}"
                                        {{ old('ruang_id', $jadwal->ruang_id) == $data->id ? 'selected' : '' }}>
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
                        <div class="form-floating mb-3">
                            <input type="text" name="kegiatan"
                                class="form-control @error('kegiatan') is-invalid @enderror" placeholder="Kegiatan"
                                value="{{ old('kegiatan', $jadwal->kegiatan) }}">
                            <label>
                                <i class="ti ti-list me-2 fs-4"></i>Kegiatan
                            </label>
                            @error('kegiatan')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="d-flex justify-content-end mt-4">
                            <button type="submit" class="btn btn-warning hstack gap-2">
                                <i class="ti ti-edit fs-5"></i> Update
                            </button>
                            <a href="{{ route('backend.jadwal.index') }}" class="btn btn-secondary hstack gap-2 ms-2">
                                <i class="ti ti-arrow-back-up fs-5"></i> Kembali
                            </a>
                        </div>
                    </form>
                </div>  
            </div>
        </div>
    </div>
@endsection
