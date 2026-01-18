@extends('layouts.backend')
@section('title', 'Tambah Pengembalian Barang')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold mb-1">Tambah Pengembalian Barang</h2>
                    <p class="text-muted mb-0">Isi form untuk mencatat pengembalian barang</p>
                </div>
                <a href="{{ route('backend.pengembalian.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Kembali
                </a>
            </div>

            <!-- Form Card -->
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-4">
                    <form action="{{ route('backend.pengembalian.store') }}" method="POST" id="formPengembalian">
                        @csrf

                        <!-- Pilih Peminjaman -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Pilih Peminjaman <span class="text-danger">*</span></label>
                            <select name="peminjaman_barang_id" id="peminjaman_barang_id" 
                                    class="form-select @error('peminjaman_barang_id') is-invalid @enderror" required>
                                <option value="">-- Pilih Peminjaman --</option>
                                @foreach($peminjamans as $peminjaman)
                                    <option value="{{ $peminjaman->id }}" 
                                            data-peminjaman='@json($peminjaman)'
                                            {{ old('peminjaman_barang_id') == $peminjaman->id ? 'selected' : '' }}>
                                        {{ $peminjaman->kode }} - {{ $peminjaman->nama_peminjam ?? $peminjaman->user->name }}
                                        ({{ \Carbon\Carbon::parse($peminjaman->tanggal_pinjam)->format('d/m/Y') }})
                                    </option>
                                @endforeach
                            </select>
                            @error('peminjaman_barang_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Pilih peminjaman yang akan dikembalikan</small>
                        </div>

                        <!-- Info Peminjaman -->
                        <div id="infoPeminjaman" class="alert alert-info mb-4" style="display: none;">
                            <h6 class="fw-bold mb-2"><i class="fas fa-info-circle me-2"></i>Detail Peminjaman</h6>
                            <div id="detailPeminjamanInfo"></div>
                        </div>

                        <!-- Tanggal & Status -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Tanggal Kembali <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal_kembali" 
                                       class="form-control @error('tanggal_kembali') is-invalid @enderror" 
                                       value="{{ old('tanggal_kembali', date('Y-m-d')) }}" required>
                                @error('tanggal_kembali')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Status Pengembalian <span class="text-danger">*</span></label>
                                <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                                    <option value="">-- Pilih Status --</option>
                                    <option value="dikembalikan" {{ old('status', 'dikembalikan') == 'dikembalikan' ? 'selected' : '' }}>
                                        Dikembalikan
                                    </option>
                                    <option value="belum dikembalikan" {{ old('status') == 'belum dikembalikan' ? 'selected' : '' }}>
                                        Belum Dikembalikan
                                    </option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Keterangan -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Keterangan</label>
                            <textarea name="keterangan" rows="3" 
                                      class="form-control @error('keterangan') is-invalid @enderror" 
                                      placeholder="Catatan tambahan (opsional)">{{ old('keterangan') }}</textarea>
                            @error('keterangan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Detail Barang yang Dikembalikan -->
                        <div class="mb-4">
                            <h5 class="fw-bold mb-3">
                                <i class="fas fa-box me-2 text-primary"></i>Detail Barang yang Dikembalikan
                            </h5>
                            <div id="barangContainer">
                                <div class="alert alert-warning">
                                    <i class="fas fa-info-circle me-2"></i>Pilih peminjaman terlebih dahulu untuk menampilkan barang yang dipinjam.
                                </div>
                            </div>
                        </div>

                        <!-- Barang Ruangan ID (Hidden, will be set by JS) -->
                        <input type="hidden" name="barang_ruangan_id" id="barang_ruangan_id" value="">

                        <!-- Buttons -->
                        <hr class="my-4">
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('backend.pengembalian.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>Batal
                            </a>
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fas fa-save me-2"></i>Simpan Pengembalian
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const peminjamanSelect = document.getElementById('peminjaman_barang_id');
    const barangContainer = document.getElementById('barangContainer');
    const infoPeminjaman = document.getElementById('infoPeminjaman');
    const detailPeminjamanInfo = document.getElementById('detailPeminjamanInfo');
    const barangRuanganInput = document.getElementById('barang_ruangan_id');

    peminjamanSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        
        if (!selectedOption.value) {
            infoPeminjaman.style.display = 'none';
            barangRuanganInput.value = '';
            barangContainer.innerHTML = `
                <div class="alert alert-warning">
                    <i class="fas fa-info-circle me-2"></i>Pilih peminjaman terlebih dahulu untuk menampilkan barang yang dipinjam.
                </div>
            `;
            return;
        }

        try {
            const peminjaman = JSON.parse(selectedOption.dataset.peminjaman);
            const details = peminjaman.detailbarangs;

            console.log('Peminjaman Data:', peminjaman);
            console.log('Detail Barangs:', details);

            if (details && details.length > 0) {
                // Set barang_ruangan_id dari detail pertama
                barangRuanganInput.value = details[0].barang_ruangan_id;

                // Tampilkan info peminjaman
                infoPeminjaman.style.display = 'block';
                let infoHTML = `
                    <div class="row g-3">
                        <div class="col-md-3">
                            <strong>Peminjam:</strong><br>
                            ${peminjaman.nama_peminjam ?? peminjaman.user.name}
                        </div>
                        <div class="col-md-3">
                            <strong>Instansi:</strong><br>
                            ${peminjaman.instansi ?? '-'}
                        </div>
                        <div class="col-md-3">
                            <strong>Tanggal Pinjam:</strong><br>
                            ${new Date(peminjaman.tanggal_pinjam).toLocaleDateString('id-ID')}
                        </div>
                        <div class="col-md-3">
                            <strong>Harus Kembali:</strong><br>
                            ${new Date(peminjaman.tanggal_kembali).toLocaleDateString('id-ID')}
                        </div>
                    </div>
                `;
                detailPeminjamanInfo.innerHTML = infoHTML;

                // Generate form barang dengan grouping by ruangan
                let html = '<div class="table-responsive">';
                html += '<table class="table table-bordered table-hover align-middle">';
                html += `
                    <thead class="table-light">
                        <tr>
                            <th width="5%">No</th>
                            <th width="25%">Nama Barang</th>
                            <th width="20%">Ruangan</th>
                            <th width="12%" class="text-center">Qty Dipinjam</th>
                            <th width="15%">Qty Dikembalikan</th>
                            <th width="18%">Kondisi</th>
                        </tr>
                    </thead>
                    <tbody>
                `;

                details.forEach((detail, index) => {
                    const barang = detail.barang_ruangan?.barang;
                    const ruangan = detail.barang_ruangan?.ruangan;
                    
                    html += `
                        <tr>
                            <td class="text-center">
                                <div class="badge bg-primary rounded-circle p-2" style="width: 30px; height: 30px; display: flex; align-items: center; justify-content: center;">
                                    ${index + 1}
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-box text-success me-2"></i>
                                    <div>
                                        <strong>${barang?.nama ?? 'Barang tidak ditemukan'}</strong>
                                    </div>
                                </div>
                                <input type="hidden" name="barang_id[]" value="${barang?.id ?? ''}">
                            </td>
                            <td>
                                <i class="fas fa-map-marker-alt me-1 text-danger"></i>
                                <strong>${ruangan?.nama_ruangan ?? '-'}</strong>
                                ${ruangan?.lokasi ? `<br><small class="text-muted">${ruangan.lokasi}</small>` : ''}
                            </td>
                            <td class="text-center">
                                <span class="badge bg-primary px-3 py-2 fs-6">${detail.jumlah}</span>
                            </td>
                            <td>
                                <input type="number" name="jumlah[]" class="form-control form-control-lg" 
                                       value="${detail.jumlah}" min="1" max="${detail.jumlah}" required
                                       style="font-size: 1.1rem; font-weight: bold;">
                                <small class="text-muted">Max: ${detail.jumlah} unit</small>
                            </td>
                            <td>
                                <select name="kondisi[]" class="form-select form-select-lg" required>
                                    <option value="">-- Pilih Kondisi --</option>
                                    <option value="baik" selected>✓ Baik</option>
                                    <option value="rusak">⚠ Rusak</option>
                                    <option value="hilang">✗ Hilang</option>
                                </select>
                            </td>
                        </tr>
                    `;
                });

                html += '</tbody></table></div>';
                
                // Tambahkan summary
                html += `
                    <div class="alert alert-success mt-3">
                        <strong><i class="fas fa-info-circle me-2"></i>Total:</strong> 
                        ${details.length} jenis barang, 
                        ${details.reduce((sum, d) => sum + parseInt(d.jumlah), 0)} unit
                    </div>
                `;
                
                barangContainer.innerHTML = html;
            } else {
                infoPeminjaman.style.display = 'none';
                barangRuanganInput.value = '';
                barangContainer.innerHTML = `
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>Peminjaman ini tidak memiliki detail barang.
                    </div>
                `;
            }
        } catch (error) {
            console.error('Error parsing data:', error);
            barangContainer.innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle me-2"></i>Terjadi kesalahan saat memuat data. Silakan refresh halaman.
                </div>
            `;
        }
    });

    // Trigger change jika ada old value
    if (peminjamanSelect.value) {
        peminjamanSelect.dispatchEvent(new Event('change'));
    }
});
</script>

<style>
.table-hover tbody tr:hover {
    background-color: #f8f9fa;
    transition: all 0.2s;
}

.form-control-lg {
    padding: 0.5rem 0.75rem;
}

.form-select-lg {
    padding: 0.5rem 0.75rem;
}
</style>
@endsection