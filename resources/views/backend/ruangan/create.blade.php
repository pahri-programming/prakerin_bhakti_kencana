@extends('layouts.backend')

@section('content')
    <div class="container">
        <div class="row">
            <div class="card">
                <div class="card-header bg-primary text-white fw-bold">
                    Tambah Data Ruangan
                </div>
                <div class="card-body">
                    <form action="{{ route('backend.ruangan.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-floating mb-3">
                            <input type="text" name="nama_ruangan"
                                class="form-control @error('nama_ruangan') is-invalid @enderror" placeholder="Nama Ruangan"
                                value="{{ old('nama_ruangan') }}">
                            <label>
                                <i class="ti ti-door me-2 fs-4"></i>Nama Ruangan
                            </label>
                            @error('nama_ruangan')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="form-floating mb-3">
                            <input type="number" name="kapasitas"
                                class="form-control @error('kapasitas') is-invalid @enderror" placeholder="Kapasitas"
                                value="{{ old('kapasitas') }}">
                            <label>
                                <i class="ti ti-users me-2 fs-4"></i>Kapasitas
                            </label>
                            @error('kapasitas')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="form-floating mb-3">
                            <input type="text" name="lokasi" class="form-control @error('lokasi') is-invalid @enderror"
                                placeholder="Lokasi" value="{{ old('lokasi') }}">
                            <label>
                                <i class="ti ti-map-pin me-2 fs-4"></i>Lokasi
                            </label>
                            @error('lokasi')
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
                                <a href="{{ route('backend.ruangan.index') }}" class="btn btn-secondary hstack gap-2">
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