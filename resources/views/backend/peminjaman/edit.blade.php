@extends('layouts.backend')


@section('title', 'Edit Peminjaman Barang')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="card">
                <div class="card-header bg-primary text-white fw-bold">
                    Edit Data Peminjaman Barang
                </div>
                <div class="card-body">
                    <form action="{{ route('backend.peminjaman.update', $peminjaman->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        {{-- kirim user_id --}}
                        <input type="hidden" name="user_id" value="{{ old('user_id', $peminjaman->user_id) }}">

                        <div class="form-floating mb-3">
                            <select name="barang_id" class="form-select @error('barang_id') is-invalid @enderror" id="barang_id">
                                <option value="" disabled {{ old('barang_id', $peminjaman->barang_id) ? '' : 'selected' }}>Pilih Barang</option>
                                @foreach ($barangs as $barang)
                                    <option value="{{ $barang->id }}"
                                        {{ (string) old('barang_id', $peminjaman->barang_id) === (string) $barang->id ? 'selected' : '' }}>
                                        {{ $barang->nama }} (Stok: {{ $barang->stok }})
                                    </option>
                                @endforeach
                            </select>
                            <label for="barang_id">
                                <i class="ti ti-package me-2 fs-4"></i>Barang
                            </label>
                            @error('barang_id')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>

                        <div class="form-floating mb-3">
                            <input type="number" name="jumlah" class="form-control @error('jumlah') is-invalid @enderror"
                                placeholder="Jumlah Peminjaman" value="{{ old('jumlah', $peminjaman->jumlah) }}">
                            <label>
                                <i class="ti ti-numbers me-2 fs-4"></i>Jumlah Peminjaman
                            </label>
                            @error('jumlah')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>

                        <div class="form-floating mb-3">
                            <input type="date" name="tanggal" class="form-control @error('tanggal') is-invalid @enderror"
                                placeholder="Tanggal Peminjaman" value="{{ old('tanggal', $peminjaman->tanggal) }}">
                            <label>
                                <i class="ti ti-calendar-measurement me-2 fs-4"></i>Tanggal Peminjaman
                            </label>
                            @error('tanggal')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>

                        <div class="form-floating mb-3">
                            <input type="time" name="waktu_mulai"
                                class="form-control @error('waktu_mulai') is-invalid @enderror" placeholder="Waktu Mulai"
                                value="{{ old('waktu_mulai', $peminjaman->waktu_mulai) }}">
                            <label>
                                <i class="ti ti-clock me-2 fs-4"></i>Waktu Mulai
                            </label>
                            @error('waktu_mulai')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>

                        <div class="form-floating mb-3">
                            <input type="time" name="waktu_selesai"
                                class="form-control @error('waktu_selesai') is-invalid @enderror"
                                placeholder="Waktu Selesai" value="{{ old('waktu_selesai', $peminjaman->waktu_selesai) }}">
                            <label>
                                <i class="ti ti-clock me-2 fs-4"></i>Waktu Selesai
                            </label>
                            @error('waktu_selesai')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>

                        <div class="form-floating mb-3">
                            <select name="status" class="form-select @error('status') is-invalid @enderror">
                                @php
                                    $statuses = ['menunggu','disetujui','ditolak','dipinjam','dikembalikan','selesai'];
                                    $selectedStatus = old('status', $peminjaman->status);
                                @endphp
                                @foreach ($statuses as $status)
                                    <option value="{{ $status }}" {{ $selectedStatus === $status ? 'selected' : '' }}>
                                        {{ ucfirst($status) }}
                                    </option>
                                @endforeach
                            </select>
                            <label>
                                <i class="ti ti-info-circle me-2 fs-4"></i>Status
                            </label>
                            @error('status')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>

                        <div class="form-floating mb-3">
                            <textarea name="keterangan" class="form-control @error('keterangan') is-invalid @enderror" placeholder="Keterangan">{{ old('keterangan', $peminjaman->keterangan === '-' ? '' : $peminjaman->keterangan) }}</textarea>
                            <label>
                                <i class="ti ti-notes me-2 fs-4"></i>Keterangan
                            </label>
                            @error('keterangan')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="ti ti-check me-1"></i> Simpan Perubahan
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
