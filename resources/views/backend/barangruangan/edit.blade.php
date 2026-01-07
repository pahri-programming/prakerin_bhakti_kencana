@extends('layouts.backend')

@section('content')
<div class="container-fluid px-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">
                        <i class="fas fa-edit"></i> Edit Barang Ruangan
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('backend.barangruangan.update', $barangRuangan->id) }}" method="POST">
                        @csrf
                        @method('PUT')

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
                                            {{ (old('ruangan_id', $barangRuangan->ruangan_id) == $ruangan->id) ? 'selected' : '' }}>
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
                                            {{ (old('barang_id', $barangRuangan->barang_id) == $barang->id) ? 'selected' : '' }}>
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
                                   value="{{ old('qty', $barangRuangan->qty) }}"
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
                                <option value="tersedia" 
                                        {{ old('status', $barangRuangan->status) == 'tersedia' ? 'selected' : '' }}>
                                    Tersedia
                                </option>
                                <option value="sedang dipinjam" 
                                        {{ old('status', $barangRuangan->status) == 'sedang dipinjam' ? 'selected' : '' }}>
                                    Sedang Dipinjam
                                </option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Info Terakhir Update -->
                        <div class="alert alert-info">
                            <small>
                                <i class="fas fa-info-circle"></i>
                                <strong>Terakhir diupdate:</strong> 
                                {{ $barangRuangan->updated_at->format('d/m/Y H:i:s') }}
                            </small>
                        </div>

                        <hr class="my-4">

                        <!-- Buttons -->
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('backend.barangruangan.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </a>
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-save"></i> Update Data
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection