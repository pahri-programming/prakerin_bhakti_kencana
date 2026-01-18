@extends('layouts.backend')
@section('title', 'Edit Barang')

@section('content')
    <div class="container-fluid px-4 py-4">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <!-- Header -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h2 class="fw-bold mb-1">Edit Barang</h2>
                        <p class="text-muted mb-0">Perbarui informasi barang</p>
                    </div>
                    <a href="{{ route('backend.barang.index') }}" class="btn btn-secondary">
                        <i class="ti ti-arrow-left me-2"></i>Kembali
                    </a>
                </div>

                <!-- Form Card -->
                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-body p-4">
                        <form action="{{ route('backend.barang.update', $barang->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <!-- Nama Barang -->
                            <div class="mb-4">
                                <label class="form-label fw-semibold">
                                    Nama Barang <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="nama"
                                    class="form-control @error('nama') is-invalid @enderror"
                                    value="{{ old('nama', $barang->nama) }}" placeholder="Masukkan nama barang" required>
                                @error('nama')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Kategori -->
                            <div class="mb-4">
                                <label class="form-label fw-semibold">Kategori</label>
                                <select name="kategori_id" class="form-select @error('kategori_id') is-invalid @enderror">
                                    <option value="">-- Pilih Kategori --</option>
                                    @foreach ($kategoris as $kategori)
                                        <option value="{{ $kategori->id }}"
                                            {{ old('kategori_id', $barang->kategori_id) == $kategori->id ? 'selected' : '' }}>
                                            {{ $kategori->nama }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('kategori_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Keterangan -->
                            <div class="mb-4">
                                <label class="form-label fw-semibold">Keterangan</label>
                                <textarea name="keterangan" rows="4" class="form-control @error('keterangan') is-invalid @enderror"
                                    placeholder="Deskripsi barang (opsional)">{{ old('keterangan', $barang->keterangan) }}</textarea>
                                @error('keterangan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Info Update -->
                            <div class="alert alert-info">
                                <small>
                                    <i class="ti ti-info-circle me-1"></i>
                                    <strong>Terakhir diupdate:</strong>
                                    {{ $barang->updated_at->format('d F Y, H:i') }}
                                </small>
                            </div>

                            <!-- Buttons -->
                            <hr class="my-4">
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('backend.barang.index') }}" class="btn btn-secondary">
                                    <i class="ti ti-x me-2"></i>Batal
                                </a>
                                <button type="submit" class="btn btn-warning px-4">
                                    <i class="ti ti-device-floppy me-2"></i>Update Barang
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
