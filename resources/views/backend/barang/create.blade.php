@extends('layouts.backend')
@section('title', 'Tambah Barang')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold mb-1">Tambah Barang Baru</h2>
                    <p class="text-muted mb-0">Isi form untuk menambah barang ke sistem</p>
                </div>
                <a href="{{ route('backend.barang.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Kembali
                </a>
            </div>

            <!-- Form Card -->
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-4">
                    <form action="{{ route('backend.barang.store') }}" method="POST">
                        @csrf

                        <!-- Nama Barang -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold">
                                Nama Barang <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="nama" 
                                   class="form-control @error('nama') is-invalid @enderror" 
                                   value="{{ old('nama') }}" 
                                   placeholder="Contoh: Laptop Asus Vivobook"
                                   required>
                            @error('nama')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Kategori -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Kategori</label>
                            <select name="kategori_id" class="form-select @error('kategori_id') is-invalid @enderror">
                                <option value="">-- Pilih Kategori (Opsional) --</option>
                                @foreach($kategoris as $kategori)
                                    <option value="{{ $kategori->id }}" {{ old('kategori_id') == $kategori->id ? 'selected' : '' }}>
                                        {{ $kategori->nama }}
                                    </option>
                                @endforeach
                            </select>
                            @error('kategori_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Harga -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold">
                                Harga Barang
                                <i class="fas fa-info-circle text-muted" 
                                   data-bs-toggle="tooltip" 
                                   title="Digunakan untuk perhitungan denda otomatis jika barang rusak/hilang"></i>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" name="harga" 
                                       class="form-control @error('harga') is-invalid @enderror" 
                                       value="{{ old('harga', 0) }}" 
                                       min="0" 
                                       step="1000"
                                       placeholder="0">
                                @error('harga')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <small class="text-muted">
                                <i class="fas fa-lightbulb me-1"></i>
                                Harga digunakan untuk auto-calculate denda:
                                <span class="badge bg-warning text-dark">Rusak Ringan 20%</span>
                                <span class="badge bg-danger">Rusak Berat 80%</span>
                                <span class="badge bg-dark">Hilang 100%</span>
                            </small>
                        </div>

                        <!-- Keterangan -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Keterangan</label>
                            <textarea name="keterangan" rows="3" 
                                      class="form-control @error('keterangan') is-invalid @enderror" 
                                      placeholder="Spesifikasi, merek, atau catatan lainnya (opsional)">{{ old('keterangan') }}</textarea>
                            @error('keterangan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Buttons -->
                        <hr class="my-4">
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('backend.barang.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>Batal
                            </a>
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fas fa-save me-2"></i>Simpan Barang
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Format input harga dengan pemisah ribuan
document.querySelector('input[name="harga"]').addEventListener('input', function(e) {
    let value = this.value.replace(/\D/g, '');
    this.value = value;
});

// Initialize tooltips
var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl);
});
</script>
@endpush
@endsection