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
                    <p class="text-muted mb-0">Catat kondisi awal barang yang dikembalikan</p>
                </div>
                <a href="{{ route('backend.pengembalian.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Kembali
                </a>
            </div>

            {{-- ✅ OPSI 1: Info Box --}}
            <div class="alert alert-info mb-4">
                <div class="d-flex align-items-start">
                    <i class="fas fa-info-circle me-3 mt-1 fs-4"></i>
                    <div>
                        <h6 class="fw-bold mb-2">Cara Kerja OPSI 1:</h6>
                        <ol class="mb-0 ps-3">
                            <li>Admin cek kondisi <strong>awal</strong> setiap barang: <span class="badge bg-success">Baik</span> atau <span class="badge bg-warning">Bermasalah</span></li>
                            <li>Jika <strong>SEMUA BAIK</strong> → Stok langsung dikembalikan ✅</li>
                            <li>Jika <strong>ADA BERMASALAH</strong> → Tunggu PIC verifikasi detail 🔍</li>
                            <li>PIC akan cek detail kondisi + upload foto bukti</li>
                        </ol>
                    </div>
                </div>
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

                        <!-- Tanggal Kembali -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Tanggal Kembali <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal_kembali" 
                                   class="form-control @error('tanggal_kembali') is-invalid @enderror" 
                                   value="{{ old('tanggal_kembali', date('Y-m-d')) }}" required>
                            @error('tanggal_kembali')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
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

                        <!-- ✅ OPSI 1: Cek Status Awal Barang -->
                        <div class="mb-4">
                            <h5 class="fw-bold mb-3">
                                <i class="fas fa-clipboard-check me-2 text-primary"></i>Cek Status Awal Barang
                            </h5>
                            <div class="alert alert-warning mb-3">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <strong>Penting:</strong> Cek kondisi awal setiap barang. Jika ada yang bermasalah, PIC akan melakukan verifikasi detail.
                            </div>
                            <div id="barangContainer">
                                <div class="alert alert-secondary">
                                    <i class="fas fa-info-circle me-2"></i>Pilih peminjaman terlebih dahulu untuk menampilkan barang yang dipinjam.
                                </div>
                            </div>
                        </div>

                        <!-- Barang Ruangan ID (Hidden) -->
                        <input type="hidden" name="barang_ruangan_id" id="barang_ruangan_id" value="">

                        <!-- Buttons -->
                        <hr class="my-4">
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('backend.pengembalian.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>Batal
                            </a>
                            <button type="submit" class="btn btn-primary px-4" id="btnSubmit">
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
    const btnSubmit = document.getElementById('btnSubmit');

    peminjamanSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        
        if (!selectedOption.value) {
            infoPeminjaman.style.display = 'none';
            barangRuanganInput.value = '';
            barangContainer.innerHTML = `
                <div class="alert alert-secondary">
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

                // ✅ OPSI 1: Generate form dengan status_awal
                let html = '<div class="table-responsive">';
                html += '<table class="table table-bordered table-hover align-middle">';
                html += `
                    <thead class="table-light">
                        <tr>
                            <th width="5%">No</th>
                            <th width="30%">Nama Barang</th>
                            <th width="20%">Ruangan</th>
                            <th width="12%" class="text-center">Qty</th>
                            <th width="33%">
                                <i class="fas fa-clipboard-check me-2 text-primary"></i>Status Awal
                                <span class="text-danger">*</span>
                            </th>
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
                                <div class="badge bg-primary rounded-circle p-2" style="width: 35px; height: 35px; display: flex; align-items: center; justify-content: center;">
                                    ${index + 1}
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-box text-success me-2 fs-5"></i>
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
                                <input type="hidden" name="jumlah[]" value="${detail.jumlah}">
                            </td>
                            <td>
                                <div class="btn-group w-100" role="group">
                                    <input type="radio" 
                                           class="btn-check status-radio" 
                                           name="status_awal[${index}]" 
                                           id="baik_${index}" 
                                           value="baik" 
                                           checked>
                                    <label class="btn btn-outline-success" for="baik_${index}">
                                        <i class="fas fa-check-circle me-1"></i>Baik
                                    </label>

                                    <input type="radio" 
                                           class="btn-check status-radio" 
                                           name="status_awal[${index}]" 
                                           id="bermasalah_${index}" 
                                           value="bermasalah">
                                    <label class="btn btn-outline-warning" for="bermasalah_${index}">
                                        <i class="fas fa-exclamation-triangle me-1"></i>Bermasalah
                                    </label>
                                </div>
                                <input type="hidden" name="status_awal[]" value="baik" id="hidden_status_${index}">
                            </td>
                        </tr>
                    `;
                });

                html += '</tbody></table></div>';
                
                // ✅ Summary & Info
                html += `
                    <div class="alert alert-success mt-3">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <strong><i class="fas fa-info-circle me-2"></i>Total:</strong> 
                                ${details.length} jenis barang, 
                                ${details.reduce((sum, d) => sum + parseInt(d.jumlah), 0)} unit
                            </div>
                            <div class="col-md-4 text-end">
                                <span id="summaryBadge" class="badge bg-success px-3 py-2">
                                    <i class="fas fa-check-circle me-1"></i>Semua Baik
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="alert alert-info">
                        <strong>Catatan:</strong>
                        <ul class="mb-0 mt-2">
                            <li>Pilih <span class="badge bg-success">Baik</span> jika barang tidak ada masalah → Stok langsung dikembalikan</li>
                            <li>Pilih <span class="badge bg-warning">Bermasalah</span> jika ada kerusakan/kehilangan → PIC akan verifikasi detail</li>
                        </ul>
                    </div>
                `;
                
                barangContainer.innerHTML = html;

                // ✅ Update hidden inputs & summary saat radio berubah
                document.querySelectorAll('.status-radio').forEach(radio => {
                    radio.addEventListener('change', function() {
                        const match = this.name.match(/\[(\d+)\]/);
                        if (match) {
                            const index = match[1];
                            document.getElementById(`hidden_status_${index}`).value = this.value;
                        }
                        updateSummary();
                    });
                });

                function updateSummary() {
                    const bermasalahCount = document.querySelectorAll('input[value="bermasalah"]:checked').length;
                    const summaryBadge = document.getElementById('summaryBadge');
                    
                    if (bermasalahCount > 0) {
                        summaryBadge.className = 'badge bg-warning px-3 py-2';
                        summaryBadge.innerHTML = `<i class="fas fa-exclamation-triangle me-1"></i>${bermasalahCount} Bermasalah → Tunggu PIC`;
                    } else {
                        summaryBadge.className = 'badge bg-success px-3 py-2';
                        summaryBadge.innerHTML = '<i class="fas fa-check-circle me-1"></i>Semua Baik';
                    }
                }

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

    // Konfirmasi submit
    document.getElementById('formPengembalian').addEventListener('submit', function(e) {
        const bermasalahCount = document.querySelectorAll('input[value="bermasalah"]:checked').length;
        
        if (bermasalahCount > 0) {
            const confirmed = confirm(
                `Ada ${bermasalahCount} barang bermasalah.\n\n` +
                `Status akan menjadi "Menunggu PIC" dan PIC akan melakukan verifikasi detail.\n\n` +
                `Lanjutkan?`
            );
            
            if (!confirmed) {
                e.preventDefault();
            }
        }
    });
});
</script>

<style>
.table-hover tbody tr:hover {
    background-color: #f8f9fa;
    transition: all 0.2s;
}

.btn-check:checked + .btn-outline-success {
    background-color: #198754;
    color: white;
}

.btn-check:checked + .btn-outline-warning {
    background-color: #ffc107;
    color: #000;
}

.btn-group label {
    cursor: pointer;
}
</style>
@endsection