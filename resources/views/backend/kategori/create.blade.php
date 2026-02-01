@extends('layouts.backend')

@section('title', 'Data Kategori')
{{-- create --}}
@section('content')
    <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
        <div class="card-header d-flex justify-content-between align-items-center py-3"
            style="background: linear-gradient(90deg, #007bff, #00b4d8); color: #fff;">
            <h5 class="mb-0 d-flex align-items-center">
                <i class="ti ti-tags fs-4 me-2"></i> <span>Tambah Kategori</span>
            </h5>
            <a href="{{ route('backend.kategori.index') }}" class="btn btn-secondary btn-sm">
                <i class="ti ti-arrow-left fs-5"></i> Kembali
            </a>
        </div>
        <div class="card-body bg-light">
            <form action="{{ route('backend.kategori.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="nama" class="form-label">Nama Kategori</label>
                    <input type="text" class="form-control" id="nama" name="nama" required
                        value="{{ old('nama') }}">
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="ti ti-check"></i> Simpan
                </button>
            </form>
        </div>
    </div>
@endsection