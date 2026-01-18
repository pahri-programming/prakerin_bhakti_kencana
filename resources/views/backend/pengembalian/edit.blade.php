@extends('layouts.backend')
@section('title', 'Edit Pengembalian Barang')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold mb-1">Edit Pengembalian Barang</h2>
                    <p class="text-muted mb-0">Perbarui data pengembalian</p>
                </div>
                <a href="{{ route('backend.pengembalian.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Kembali
                </a>
            </div>

            <!-- Form Card -->
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-4">
                    <form action="{{ route('backend.pengembalian.update', $pengembalian->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Info Peminjaman (Read Only) -->
                        <div class="alert alert-info mb-4">
                            <h6 class="fw-bold mb-2">
                                <i class="fas fa-info-circle me-2"></i>Informasi Peminjaman
                            </h6>
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <strong>Kode:</strong><br>
                                    {{ $pengembalian->peminjamanBarang->kode }}
                                </div>
                                <div class="col-md-3">
                                    <strong>Peminjam:</strong><br>
                                    {{ $pengembalian->peminjamanBarang->nama_peminjam ?? $pengembalian->peminjamanBarang->user->name }}
                                </div>
                                <div class="col-md-3">
                                    <strong>Tanggal Pinjam:</strong><br>
                                    {{ \Carbon\Carbon::parse($pengembalian->peminjamanBarang->tanggal_pinjam)->format('d M Y') }}
                                </div>
                                <div class="col-md-3">
                                    <strong>Status Peminjaman:</strong><br>
                                    <span class="badge bg-info">{{ ucfirst($pengembalian->peminjamanBarang->status) }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Barang yang Dikembalikan (Kondisi Bisa Diubah) -->
                        <div class="mb-4">
                            <h5 class="fw-bold mb-3">
                                <i class="fas fa-box me-2 text-primary"></i>Barang yang Dikembalikan
                            </h5>
                            <div class="alert alert-warning mb-3">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                Nama barang dan jumlah tidak dapat diubah. Hanya kondisi barang yang bisa diperbarui.
                            </div>
                            
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="5%">No</th>
                                            <th width="30%">Nama Barang</th>
                                            <th width="12%" class="text-center">Jumlah</th>
                                            <th width="25%">Kondisi Barang <span class="text-danger">*</span></th>
                                            <th width="28%">Keterangan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($pengembalian->detailpengembalians as $index => $detail)
                                            <tr>
                                                <td class="text-center">
                                                    <div class="badge bg-primary rounded-circle p-2" style="width: 30px; height: 30px; display: flex; align-items: center; justify-content: center;">
                                                        {{ $index + 1 }}
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <i class="fas fa-box text-success me-2 fs-5"></i>
                                                        <div>
                                                            <strong>{{ $detail->barang->nama ?? '-' }}</strong>
                                                        </div>
                                                    </div>
                                                    <!-- Hidden fields untuk detail yang tidak berubah -->
                                                    <input type="hidden" name="detail_id[]" value="{{ $detail->id }}">
                                                    <input type="hidden" name="barang_id[]" value="{{ $detail->barang_id }}">
                                                    <input type="hidden" name="jumlah_detail[]" value="{{ $detail->jumlah }}">
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge bg-primary px-3 py-2 fs-6">{{ $detail->jumlah }} Unit</span>
                                                </td>
                                                <td>
                                                    <select name="kondisi[]" class="form-select" required>
                                                        <option value="">-- Pilih Kondisi --</option>
                                                        <option value="baik" {{ old("kondisi.$index", $detail->kondisi) == 'baik' ? 'selected' : '' }}>
                                                            ✓ Baik
                                                        </option>
                                                        <option value="rusak" {{ old("kondisi.$index", $detail->kondisi) == 'rusak' ? 'selected' : '' }}>
                                                            ⚠ Rusak
                                                        </option>
                                                        <option value="hilang" {{ old("kondisi.$index", $detail->kondisi) == 'hilang' ? 'selected' : '' }}>
                                                            ✗ Hilang
                                                        </option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <small class="text-muted" id="kondisi-info-{{ $index }}">
                                                        @if($detail->kondisi == 'baik')
                                                            <i class="fas fa-check-circle text-success me-1"></i>Barang dalam kondisi baik
                                                        @elseif($detail->kondisi == 'rusak')
                                                            <i class="fas fa-exclamation-triangle text-warning me-1"></i>Perlu perbaikan
                                                        @else
                                                            <i class="fas fa-times-circle text-danger me-1"></i>Barang tidak ditemukan
                                                        @endif
                                                    </small>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Barang Ruangan ID (Hidden) -->
                        <input type="hidden" name="barang_ruangan_id" value="{{ $pengembalian->barang_ruangan_id }}">

                        <!-- Tanggal Kembali -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Tanggal Kembali <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal_kembali" 
                                   class="form-control @error('tanggal_kembali') is-invalid @enderror" 
                                   value="{{ old('tanggal_kembali', \Carbon\Carbon::parse($pengembalian->tanggal_kembali)->format('Y-m-d')) }}" 
                                   required>
                            @error('tanggal_kembali')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Status -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Status Pengembalian <span class="text-danger">*</span></label>
                            <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                                <option value="">-- Pilih Status --</option>
                                <option value="dikembalikan" {{ old('status', $pengembalian->status) == 'dikembalikan' ? 'selected' : '' }}>
                                    Dikembalikan
                                </option>
                                <option value="belum dikembalikan" {{ old('status', $pengembalian->status) == 'belum dikembalikan' ? 'selected' : '' }}>
                                    Belum Dikembalikan
                                </option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">
                                <i class="fas fa-exclamation-triangle me-1"></i>
                                Perubahan status akan mempengaruhi stok barang dan status peminjaman
                            </small>
                        </div>

                        <!-- Keterangan -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Keterangan</label>
                            <textarea name="keterangan" rows="3" 
                                      class="form-control @error('keterangan') is-invalid @enderror" 
                                      placeholder="Catatan tambahan (opsional)">{{ old('keterangan', $pengembalian->keterangan) }}</textarea>
                            @error('keterangan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Info Perubahan Status -->
                        <div class="alert alert-info">
                            <strong><i class="fas fa-info-circle me-2"></i>Catatan:</strong>
                            <ul class="mb-0 mt-2">
                                <li>Mengubah status ke <strong>"Dikembalikan"</strong> akan mengembalikan stok barang dan mengubah status peminjaman.</li>
                                <li>Mengubah status ke <strong>"Belum Dikembalikan"</strong> akan mengurangi stok barang kembali.</li>
                                <li>Detail barang yang dikembalikan tidak dapat diubah di form ini.</li>
                            </ul>
                        </div>

                        <!-- Buttons -->
                        <hr class="my-4">
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('backend.pengembalian.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>Batal
                            </a>
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fas fa-save me-2"></i>Update Pengembalian
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.table-hover tbody tr:hover {
    background-color: #f8f9fa;
    transition: all 0.2s;
}

.form-select:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Update keterangan otomatis saat kondisi berubah
    const kondisiSelects = document.querySelectorAll('select[name="kondisi[]"]');
    
    kondisiSelects.forEach((select, index) => {
        select.addEventListener('change', function() {
            const infoElement = document.getElementById(`kondisi-info-${index}`);
            const value = this.value;
            
            let icon = '';
            let text = '';
            let colorClass = '';
            
            switch(value) {
                case 'baik':
                    icon = '<i class="fas fa-check-circle text-success me-1"></i>';
                    text = 'Barang dalam kondisi baik';
                    colorClass = 'text-success';
                    break;
                case 'rusak':
                    icon = '<i class="fas fa-exclamation-triangle text-warning me-1"></i>';
                    text = 'Perlu perbaikan';
                    colorClass = 'text-warning';
                    break;
                case 'hilang':
                    icon = '<i class="fas fa-times-circle text-danger me-1"></i>';
                    text = 'Barang tidak ditemukan';
                    colorClass = 'text-danger';
                    break;
                default:
                    icon = '<i class="fas fa-question-circle text-muted me-1"></i>';
                    text = 'Pilih kondisi barang';
                    colorClass = 'text-muted';
            }
            
            if (infoElement) {
                infoElement.innerHTML = icon + text;
                infoElement.className = 'small ' + colorClass;
            }
        });
    });
});
</script>
@endsection