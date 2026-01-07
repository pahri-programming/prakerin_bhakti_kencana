@extends('layouts.backend')

@section('title', 'Tambah Peminjaman Barang')

@section('content')
    <div class="container-fluid">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        <div class="row">
            <div class="card">
                <div class="card-header bg-primary text-white fw-bold">
                    Tambah Data Peminjaman Barang
                </div>
                <div class="card-body">
                    <form action="{{ route('backend.peminjaman.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        {{-- nama peminjam --}}
                        <div class="form-floating mb-3">
                            <label for="">
                                <i class="ti ti-user me-2 fs-4 "></i>Nama Peminjam<span class="text-danger"></span>
                            </label>
                            <input type="text" class="form-control @error('nama_peminjam') is-invalid @enderror"
                                name="nama_peminjam" placeholder="Nama Peminjam" value="{{ old('nama_peminjam') }}"
                                required>
                        </div>
                        {{-- instansi --}}
                        <div class="form-floating mb-3">
                            <label for="">
                                <i class="ti ti-building-factory me-2 fs-4 "></i>Instansi<span class="text-danger"></span>
                            </label>
                            <input type="text" class="form-control @error('instansi') is-invalid @enderror"
                                name="instansi" placeholder="Instansi" value="{{ old('instansi') }}" required>
                        </div>

                        {{-- Barang Section (Multiple) --}}
                        <div class="mb-3">
                            <label class="form-label fw-bold">
                                <i class="ti ti-package me-2 fs-4"></i>Barang yang Dipinjam <span
                                    class="text-danger">*</span>
                            </label>
                            <div id="barang-container">
                                <div class="barang-item mb-3 p-3 border rounded">
                                    <div class="row g-2">
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <select name="barang_id[]"
                                                    class="form-select barang-select @error('barang_id.0') is-invalid @enderror"
                                                    required>
                                                    <option value="">-- Pilih Barang --</option>
                                                    @foreach ($barangs as $barang)
                                                        <option value="{{ $barang->id }}"
                                                            data-stok="{{ $barang->jumlah }}">
                                                            {{ $barang->nama }} (Stok: {{ $barang->jumlah }})
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <label>Pilih Barang</label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-floating">
                                                <input type="number" name="jumlah[]"
                                                    class="form-control @error('jumlah.0') is-invalid @enderror"
                                                    placeholder="Jumlah" min="1" required>
                                                <label>Jumlah</label>
                                            </div>
                                        </div>
                                        <div class="col-md-2 d-flex align-items-center">
                                            <button type="button" class="btn btn-danger btn-remove-barang w-100"
                                                style="display:none;">
                                                <i class="ti ti-trash"></i> Hapus
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <button type="button" class="btn btn-sm btn-primary mt-2" id="btn-add-barang">
                                <i class="ti ti-plus me-1"></i> Tambah Barang
                            </button>
                        </div>

                        {{-- Tanggal Pinjam --}}
                        <div class="form-floating mb-3">
                            <input type="date" name="tanggal_pinjam"
                                class="form-control @error('tanggal_pinjam') is-invalid @enderror"
                                placeholder="Tanggal Pinjam" value="{{ old('tanggal_pinjam', date('Y-m-d')) }}" required>
                            <label>
                                <i class="ti ti-calendar-event me-2 fs-4"></i>Tanggal Pinjam
                            </label>
                            @error('tanggal_pinjam')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        {{-- Tanggal Kembali --}}
                        <div class="form-floating mb-3">
                            <input type="date" name="tanggal_kembali"
                                class="form-control @error('tanggal_kembali') is-invalid @enderror"
                                placeholder="Tanggal Kembali" value="{{ old('tanggal_kembali') }}" required>
                            <label>
                                <i class="ti ti-calendar-event me-2 fs-4"></i>Tanggal Kembali
                            </label>
                            @error('tanggal_kembali')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        {{-- Waktu Mulai --}}
                        <div class="form-floating mb-3">
                            <input type="time" name="waktu_mulai"
                                class="form-control @error('waktu_mulai') is-invalid @enderror" placeholder="Waktu Mulai"
                                value="{{ old('waktu_mulai', '08:00') }}" required>
                            <label>
                                <i class="ti ti-clock me-2 fs-4"></i>Waktu Mulai
                            </label>
                            @error('waktu_mulai')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        {{-- Waktu Selesai --}}
                        <div class="form-floating mb-3">
                            <input type="time" name="waktu_selesai"
                                class="form-control @error('waktu_selesai') is-invalid @enderror"
                                placeholder="Waktu Selesai" value="{{ old('waktu_selesai', '17:00') }}" required>
                            <label>
                                <i class="ti ti-clock me-2 fs-4"></i>Waktu Selesai
                            </label>
                            @error('waktu_selesai')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        {{-- Keterangan --}}
                        <div class="form-floating mb-3">
                            <textarea name="keterangan" class="form-control @error('keterangan') is-invalid @enderror" placeholder="Keterangan"
                                style="height: 100px">{{ old('keterangan') }}</textarea>
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

    @push('scripts')
        <script>
            $(document).ready(function() {
                let barangCount = 1;

                // Add barang
                $('#btn-add-barang').click(function() {
                    const barangHtml = `
            <div class="barang-item mb-3 p-3 border rounded">
                <div class="row g-2">
                    <div class="col-md-6">
                        <div class="form-floating">
                            <select name="barang_id[]" class="form-select barang-select" required>
                                <option value="">-- Pilih Barang --</option>
                                @foreach ($barangs as $barang)
                                    <option value="{{ $barang->id }}" data-stok="{{ $barang->jumlah }}">
                                        {{ $barang->nama }} (Stok: {{ $barang->jumlah }})
                                    </option>
                                @endforeach
                            </select>
                            <label>Pilih Barang</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating">
                            <input type="number" name="jumlah[]" class="form-control" 
                                   placeholder="Jumlah" min="1" required>
                            <label>Jumlah</label>
                        </div>
                    </div>
                    <div class="col-md-2 d-flex align-items-center">
                        <button type="button" class="btn btn-danger btn-remove-barang w-100">
                            <i class="ti ti-trash"></i> Hapus
                        </button>
                    </div>
                </div>
            </div>
        `;
                    $('#barang-container').append(barangHtml);
                    barangCount++;
                    updateRemoveButtons();
                });

                // Remove barang
                $(document).on('click', '.btn-remove-barang', function() {
                    $(this).closest('.barang-item').remove();
                    barangCount--;
                    updateRemoveButtons();
                });

                function updateRemoveButtons() {
                    if ($('.barang-item').length > 1) {
                        $('.btn-remove-barang').show();
                    } else {
                        $('.btn-remove-barang').hide();
                    }
                }

                // Validate jumlah berdasarkan stok
                $(document).on('change', '.barang-select', function() {
                    const stok = $(this).find(':selected').data('stok');
                    const jumlahInput = $(this).closest('.row').find('input[name="jumlah[]"]');
                    jumlahInput.attr('max', stok);

                    // Reset nilai jika melebihi stok
                    if (parseInt(jumlahInput.val()) > stok) {
                        jumlahInput.val('');
                    }
                });

                $(document).on('input', 'input[name="jumlah[]"]', function() {
                    const max = $(this).attr('max');
                    if (max && parseInt($(this).val()) > parseInt(max)) {
                        alert('Jumlah melebihi stok yang tersedia! Maksimal: ' + max);
                        $(this).val(max);
                    }
                });

                // Prevent duplicate barang selection
                $(document).on('change', '.barang-select', function() {
                    const selectedBarangId = $(this).val();
                    const currentSelect = $(this);

                    // Check if barang already selected in other dropdowns
                    let isDuplicate = false;
                    $('.barang-select').not(currentSelect).each(function() {
                        if ($(this).val() == selectedBarangId && selectedBarangId !== '') {
                            isDuplicate = true;
                            return false;
                        }
                    });

                    if (isDuplicate) {
                        alert('Barang ini sudah dipilih! Silakan pilih barang lain.');
                        $(this).val('');
                        $(this).closest('.row').find('input[name="jumlah[]"]').val('');
                    }
                });
            });
        </script>
    @endpush
@endsection
