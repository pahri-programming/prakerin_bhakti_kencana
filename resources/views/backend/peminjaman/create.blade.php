@extends('layouts.backend')

{{-- create --}}
@section('title', 'Tambah Peminjaman Barang')
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="card">
                <div class="card-header bg-primary text-white fw-bold">
                    Tambah Data Peminjaman Barang
                </div>
                <div class="card-body">
                    <form action="{{ route('backend.peminjaman.store') }}" method="POST" enctype="multipart/form-data">
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
                            <select name="barang_id" class="form-select @error('barang_id') is-invalid @enderror"
                                id="barang_id">
                                <option value="" disabled selected>Pilih Barang</option>
                                @foreach ($barangs as $barang)
                                    <option value="{{ $barang->id }}"
                                        {{ old('barang_id') == $barang->id ? 'selected' : '' }}>
                                        {{ $barang->nama }} (Stok: {{ $barang->stok }})
                                    </option>
                                @endforeach
                            </select>
                            <label for="barang_id">
                                <i class="ti ti-package me-2 fs-4"></i>Barang
                            </label>
                            @error('barang_id')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="form-floating mb-3">
                            <input type="number" name="jumlah" class="form-control @error('jumlah') is-invalid @enderror"
                                placeholder="Jumlah Peminjaman" value="{{ old('jumlah') }}">
                            <label>
                                <i class="ti ti-numbers me-2 fs-4"></i>Jumlah Peminjaman
                            </label>
                            @error('jumlah')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        {{-- tanggal_pinjam --}}
                        <div class="form-floating mb-3">
                            <input type="date" name="tanggal_pinjam"
                                class="form-control @error('tanggal_pinjam') is-invalid @enderror"
                                placeholder="Tanggal Pinjam" value="{{ old('tanggal_pinjam') }}">
                            <label>
                                <i class="ti ti-calendar-event me-2 fs-4"></i>Tanggal Pinjam
                            </label>
                            @error('tanggal_pinjam')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        {{-- tanggal_kembali --}}
                        <div class="form-floating mb-3">
                            <input type="date" name="tanggal_kembali"
                                class="form-control @error('tanggal_kembali') is-invalid @enderror"
                                placeholder="Tanggal Kembali" value="{{ old('tanggal_kembali') }}">
                            <label>
                                <i class="ti ti-calendar-event-measurement me-2 fs-4"></i>Tanggal Kembali
                            </label>
                            @error('tanggal_kembali')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        {{-- waktu_mulai --}}
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
                            <textarea name="keterangan" class="form-control @error('keterangan') is-invalid @enderror" placeholder="Keterangan">{{ old('keterangan') }}</textarea>
                            <label>
                                <i class="ti ti-notes me-2 fs-4"></i>Keterangan
                            </label>
                            @error('keterangan')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="ti ti-check me-1"></i> Simpan Peminjaman
                        </button>
                        <a href="{{ route('backend.peminjaman.index') }}" class="btn btn-secondary ms-2">
                            <i class="ti ti-arrow-left me-1"></i> Kembali
                        </a>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
