@extends('layouts.backend')

@section('content')
<div class="container-fluid px-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-plus-circle"></i> Tambah Barang Ruangan
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('backend.barangruangan.store') }}" method="POST">
                        @csrf

                        <!-- Ruangan -->
                        <div class="mb-3">
                            <label for="ruangan_id" class="form-label">
                                Ruangan <span class="text-danger">*</span>
                            </label>
                            <select name="ruangan_id" id="ruangan_id" 
                                    class="form-select @error('ruangan_id') is-invalid @enderror" required>
                                <option value="">-- Pilih Ruangan --</option>
                                @foreach($ruangans as $ruangan)
                                    <option value="{{ $ruangan->id }}" 
                                            {{ old('ruangan_id') == $ruangan->id ? 'selected' : '' }}>
                                        {{ $ruangan->nama_ruangan }}
                                    </option>
                                @endforeach
                            </select>
                            @error('ruangan_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Barang -->
                        <div class="mb-3">
                            <label for="barang_id" class="form-label">
                                Barang <span class="text-danger">*</span>
                            </label>
                            <select name="barang_id" id="barang_id" 
                                    class="form-select @error('barang_id') is-invalid @enderror" required>
                                <option value="">-- Pilih Barang --</option>
                                @foreach($barangs as $barang)
                                    <option value="{{ $barang->id }}" 
                                            {{ old('barang_id') == $barang->id ? 'selected' : '' }}>
                                        {{ $barang->nama }}
                                    </option>
                                @endforeach
                            </select>
                            @error('barang_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Jumlah -->
                        <div class="mb-3">
                            <label for="qty" class="form-label">
                                Jumlah <span class="text-danger">*</span>
                            </label>
                            <input type="number" 
                                   name="qty" 
                                   id="qty" 
                                   class="form-control @error('qty') is-invalid @enderror" 
                                   value="{{ old('qty') }}"
                                   min="1"
                                   placeholder="Masukkan jumlah barang"
                                   required>
                            @error('qty')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Status -->
                        <div class="mb-3">
                            <label for="status" class="form-label">
                                Status <span class="text-danger">*</span>
                            </label>
                            <select name="status" id="status" 
                                    class="form-select @error('status') is-invalid @enderror" required>
                                <option value="">-- Pilih Status --</option>
                                <option value="tersedia" {{ old('status') == 'tersedia' ? 'selected' : '' }}>
                                    Tersedia
                                </option>
                                <option value="sedang dipinjam" {{ old('status') == 'sedang dipinjam' ? 'selected' : '' }}>
                                    Sedang Dipinjam
                                </option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr class="my-4">

                        <!-- Buttons -->
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('backend.barangruangan.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Simpan Data
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection