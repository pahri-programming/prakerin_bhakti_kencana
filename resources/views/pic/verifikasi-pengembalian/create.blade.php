@extends('layouts.backend')
@section('title', 'Verifikasi Pengembalian Barang')

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">Verifikasi Pengembalian Barang</h2>
            <p class="text-muted mb-0">Verifikasi kondisi detail barang yang bermasalah</p>
        </div>
        <a href="{{ route('pic.verifikasi-pengembalian.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i>Kembali
        </a>
    </div>

    <!-- Info Pengembalian -->
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-body p-4">
            <div class="row g-4">
                <div class="col-md-6">
                    <h6 class="text-muted mb-3">Informasi Peminjaman</h6>
                    <table class="table table-sm table-borderless">
                        <tr>
                            <td width="40%" class="text-muted">Kode Peminjaman</td>
                            <td width="5%">:</td>
                            <td>
                                <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2">
                                    {{ $pengembalian->peminjamanBarang->kode }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Nama Peminjam</td>
                            <td>:</td>
                            <td class="fw-semibold">
                                {{ $pengembalian->peminjamanBarang->nama_peminjam ?? $pengembalian->peminjamanBarang->user->name }}
                            </td>
                        </tr>
                        @if($pengembalian->peminjamanBarang->instansi)
                        <tr>
                            <td class="text-muted">Instansi</td>
                            <td>:</td>
                            <td>{{ $pengembalian->peminjamanBarang->instansi }}</td>
                        </tr>
                        @endif
                        <tr>
                            <td class="text-muted">Tanggal Pinjam</td>
                            <td>:</td>
                            <td>{{ \Carbon\Carbon::parse($pengembalian->peminjamanBarang->tanggal_pinjam)->format('d M Y') }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <h6 class="text-muted mb-3">Informasi Pengembalian</h6>
                    <table class="table table-sm table-borderless">
                        <tr>
                            <td width="40%" class="text-muted">Tanggal Kembali</td>
                            <td width="5%">:</td>
                            <td>
                                <i class="far fa-calendar text-primary me-1"></i>
                                {{ \Carbon\Carbon::parse($pengembalian->tanggal_kembali)->format('d M Y') }}
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Status</td>
                            <td>:</td>
                            <td>
                                <span class="badge bg-info px-3 py-2">
                                    <i class="fas fa-clock me-1"></i>Menunggu PIC
                                </span>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Daftar Barang yang Bermasalah -->
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-header bg-white border-bottom py-3">
            <h5 class="mb-0 fw-bold">
                <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                Barang yang Bermasalah
            </h5>
        </div>
        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table table-bordered align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th width="5%">No</th>
                            <th width="40%">Nama Barang</th>
                            <th width="15%">Jumlah</th>
                            <th width="20%">Status Awal (Admin)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $no = 1; @endphp
                        @foreach($pengembalian->detailpengembalians as $detail)
                            @if($detail->status_awal === 'bermasalah')
                            <tr>
                                <td class="text-center">{{ $no++ }}</td>
                                <td class="fw-semibold">{{ $detail->barang->nama }}</td>
                                <td class="text-center">
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary px-3 py-2">
                                        {{ $detail->jumlah }} unit
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-warning px-3 py-2">
                                        <i class="fas fa-exclamation-triangle me-1"></i>Bermasalah
                                    </span>
                                </td>
                            </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="alert alert-warning mt-4 mb-0">
                <i class="fas fa-info-circle me-2"></i>
                <strong>Catatan:</strong> Barang di atas telah ditandai bermasalah oleh admin. 
                Silakan verifikasi kondisi detail dan upload foto bukti.
            </div>
        </div>
    </div>

    <!-- Form Verifikasi -->
    <form action="{{ route('pic.verifikasi-pengembalian.store', $pengembalian->id) }}" 
          method="POST" 
          enctype="multipart/form-data"
          id="verifikasiForm">
        @csrf

        <div class="card border-0 shadow-sm rounded-3 mb-4">
            <div class="card-header bg-primary text-white py-3">
                <h5 class="mb-0 fw-bold">
                    <i class="fas fa-clipboard-check me-2"></i>
                    Form Verifikasi PIC
                </h5>
            </div>
            <div class="card-body p-4">
                <!-- Kondisi Detail -->
                <div class="mb-4">
                    <label class="form-label fw-semibold">
                        Kondisi Detail <span class="text-danger">*</span>
                    </label>
                    <p class="text-muted small mb-3">Pilih kondisi yang paling sesuai dengan hasil pengecekan:</p>
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-check form-check-card">
                                <input class="form-check-input" type="radio" name="kondisi" 
                                       id="kondisi_baik" value="baik" 
                                       {{ old('kondisi') == 'baik' ? 'checked' : '' }}>
                                <label class="form-check-label w-100" for="kondisi_baik">
                                    <div class="card border h-100 kondisi-card">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center">
                                                <div class="icon-box bg-success bg-opacity-10 text-success me-3">
                                                    <i class="fas fa-check-circle fa-2x"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-1 fw-bold">Baik</h6>
                                                    <small class="text-muted">Tidak ada kerusakan, berfungsi normal</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-check form-check-card">
                                <input class="form-check-input" type="radio" name="kondisi" 
                                       id="kondisi_rusak_ringan" value="rusak_ringan"
                                       {{ old('kondisi') == 'rusak_ringan' ? 'checked' : '' }}>
                                <label class="form-check-label w-100" for="kondisi_rusak_ringan">
                                    <div class="card border h-100 kondisi-card">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center">
                                                <div class="icon-box bg-warning bg-opacity-10 text-warning me-3">
                                                    <i class="fas fa-exclamation-triangle fa-2x"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-1 fw-bold">Rusak Ringan</h6>
                                                    <small class="text-muted">Kerusakan minor, masih bisa digunakan</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-check form-check-card">
                                <input class="form-check-input" type="radio" name="kondisi" 
                                       id="kondisi_rusak_berat" value="rusak_berat"
                                       {{ old('kondisi') == 'rusak_berat' ? 'checked' : '' }}>
                                <label class="form-check-label w-100" for="kondisi_rusak_berat">
                                    <div class="card border h-100 kondisi-card">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center">
                                                <div class="icon-box bg-danger bg-opacity-10 text-danger me-3">
                                                    <i class="fas fa-times-circle fa-2x"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-1 fw-bold">Rusak Berat</h6>
                                                    <small class="text-muted">Kerusakan parah, tidak bisa digunakan</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-check form-check-card">
                                <input class="form-check-input" type="radio" name="kondisi" 
                                       id="kondisi_hilang" value="hilang"
                                       {{ old('kondisi') == 'hilang' ? 'checked' : '' }}>
                                <label class="form-check-label w-100" for="kondisi_hilang">
                                    <div class="card border h-100 kondisi-card">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center">
                                                <div class="icon-box bg-dark bg-opacity-10 text-dark me-3">
                                                    <i class="fas fa-question-circle fa-2x"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-1 fw-bold">Hilang</h6>
                                                    <small class="text-muted">Barang tidak dikembalikan/tidak ditemukan</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>

                    @error('kondisi')
                        <div class="text-danger small mt-2">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Foto Bukti -->
                <div class="mb-4">
                    <label class="form-label fw-semibold">
                        Foto Bukti (Maksimal 6 Foto) <span class="text-danger">*</span>
                    </label>
                    <p class="text-muted small mb-3">Upload foto kondisi barang dari berbagai sudut pandang</p>

                    <div class="upload-area border rounded-3 p-4 text-center" id="uploadArea">
                        <input type="file" 
                               class="d-none" 
                               id="foto_bukti" 
                               name="foto_bukti[]" 
                               accept="image/jpeg,image/jpg,image/png"
                               multiple>
                        
                        <div class="upload-placeholder" id="uploadPlaceholder">
                            <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
                            <h6 class="mb-2">Klik atau Drag & Drop Foto</h6>
                            <p class="text-muted small mb-3">Format: JPG, JPEG, PNG (Max 2MB per file)</p>
                            <button type="button" class="btn btn-outline-primary" onclick="document.getElementById('foto_bukti').click()">
                                <i class="fas fa-folder-open me-1"></i>Pilih Foto
                            </button>
                        </div>

                        <div id="previewContainer" class="row g-3 mt-3" style="display: none;"></div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-2">
                        <small class="text-muted">
                            <i class="fas fa-info-circle me-1"></i>
                            <span id="photoCount">0</span> dari 6 foto dipilih
                        </small>
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="clearPhotos" style="display: none;">
                            <i class="fas fa-times me-1"></i>Hapus Semua
                        </button>
                    </div>

                    @error('foto_bukti')
                        <div class="text-danger small mt-2">{{ $message }}</div>
                    @enderror
                    @error('foto_bukti.*')
                        <div class="text-danger small mt-2">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Catatan PIC -->
                <div class="mb-4">
                    <label for="catatan_pic" class="form-label fw-semibold">
                        Catatan PIC
                    </label>
                    <textarea class="form-control" 
                              id="catatan_pic" 
                              name="catatan_pic" 
                              rows="4" 
                              placeholder="Jelaskan kondisi barang secara detail, kerusakan yang ditemukan, atau catatan penting lainnya...">{{ old('catatan_pic') }}</textarea>
                    <small class="text-muted">
                        <span id="charCount">0</span>/1000 karakter
                    </small>
                    @error('catatan_pic')
                        <div class="text-danger small mt-2">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Info Alert -->
                <div class="alert alert-info mb-0">
                    <div class="d-flex align-items-start">
                        <i class="fas fa-lightbulb me-3 mt-1"></i>
                        <div>
                            <h6 class="fw-bold mb-2">Tips Verifikasi:</h6>
                            <ul class="mb-0 small">
                                <li>Pastikan foto jelas dan menunjukkan kondisi barang dengan detail</li>
                                <li>Ambil foto dari berbagai sudut untuk dokumentasi lengkap</li>
                                <li>Tulis catatan yang spesifik tentang kerusakan yang ditemukan</li>
                                <li>Laporan akan otomatis dikirim ke admin untuk tindak lanjut</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-footer bg-light border-top py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <a href="{{ route('pic.verifikasi-pengembalian.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-1"></i>Batal
                    </a>
                    <button type="submit" class="btn btn-primary px-4" id="submitBtn">
                        <i class="fas fa-check-circle me-1"></i>Simpan Verifikasi
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<style>
.form-check-card {
    margin-bottom: 0;
}

.kondisi-card {
    cursor: pointer;
    transition: all 0.3s ease;
}

.kondisi-card:hover {
    border-color: #0d6efd !important;
    box-shadow: 0 0.25rem 0.5rem rgba(0,0,0,0.1);
}

.form-check-input:checked + label .kondisi-card {
    border-color: #0d6efd !important;
    background-color: #f8f9fa;
    box-shadow: 0 0.25rem 0.5rem rgba(13,110,253,0.2);
}

.form-check-input {
    display: none;
}

.icon-box {
    width: 60px;
    height: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 12px;
}

.upload-area {
    background-color: #f8f9fa;
    border: 2px dashed #dee2e6 !important;
    transition: all 0.3s ease;
}

.upload-area:hover {
    border-color: #0d6efd !important;
    background-color: #e7f1ff;
}

.upload-area.drag-over {
    border-color: #0d6efd !important;
    background-color: #e7f1ff;
}

.preview-item {
    position: relative;
}

.preview-img {
    width: 100%;
    height: 150px;
    object-fit: cover;
    border-radius: 8px;
}

.remove-photo {
    position: absolute;
    top: -8px;
    right: -8px;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    background: #dc3545;
    color: white;
    border: 2px solid white;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    transition: all 0.3s ease;
}

.remove-photo:hover {
    background: #bb2d3b;
    transform: scale(1.1);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('foto_bukti');
    const uploadArea = document.getElementById('uploadArea');
    const uploadPlaceholder = document.getElementById('uploadPlaceholder');
    const previewContainer = document.getElementById('previewContainer');
    const photoCount = document.getElementById('photoCount');
    const clearPhotos = document.getElementById('clearPhotos');
    const catatanPic = document.getElementById('catatan_pic');
    const charCount = document.getElementById('charCount');
    const submitBtn = document.getElementById('submitBtn');
    
    let selectedFiles = [];

    // Character count
    catatanPic.addEventListener('input', function() {
        const count = this.value.length;
        charCount.textContent = count;
        if (count > 1000) {
            charCount.classList.add('text-danger');
        } else {
            charCount.classList.remove('text-danger');
        }
    });

    // File input change
    fileInput.addEventListener('change', function(e) {
        handleFiles(e.target.files);
    });

    // Drag & drop
    uploadArea.addEventListener('dragover', function(e) {
        e.preventDefault();
        uploadArea.classList.add('drag-over');
    });

    uploadArea.addEventListener('dragleave', function() {
        uploadArea.classList.remove('drag-over');
    });

    uploadArea.addEventListener('drop', function(e) {
        e.preventDefault();
        uploadArea.classList.remove('drag-over');
        handleFiles(e.dataTransfer.files);
    });

    // Clear all photos
    clearPhotos.addEventListener('click', function() {
        if (confirm('Hapus semua foto yang dipilih?')) {
            selectedFiles = [];
            updatePreview();
            fileInput.value = '';
        }
    });

    function handleFiles(files) {
        const filesArray = Array.from(files);
        
        // Validate file count
        if (selectedFiles.length + filesArray.length > 6) {
            alert('Maksimal 6 foto!');
            return;
        }

        // Validate each file
        for (let file of filesArray) {
            // Check type
            if (!file.type.match('image/jpeg|image/jpg|image/png')) {
                alert(`File ${file.name} harus berformat JPG, JPEG, atau PNG`);
                continue;
            }

            // Check size (2MB)
            if (file.size > 2 * 1024 * 1024) {
                alert(`File ${file.name} terlalu besar (max 2MB)`);
                continue;
            }

            selectedFiles.push(file);
        }

        updatePreview();
        updateFileInput();
    }

    function updatePreview() {
        if (selectedFiles.length === 0) {
            uploadPlaceholder.style.display = 'block';
            previewContainer.style.display = 'none';
            clearPhotos.style.display = 'none';
        } else {
            uploadPlaceholder.style.display = 'none';
            previewContainer.style.display = 'flex';
            clearPhotos.style.display = 'inline-block';
        }

        previewContainer.innerHTML = '';
        selectedFiles.forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = function(e) {
                const col = document.createElement('div');
                col.className = 'col-4 col-md-2';
                col.innerHTML = `
                    <div class="preview-item">
                        <img src="${e.target.result}" class="preview-img" alt="Preview">
                        <button type="button" class="remove-photo" onclick="removePhoto(${index})">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                `;
                previewContainer.appendChild(col);
            };
            reader.readAsDataURL(file);
        });

        photoCount.textContent = selectedFiles.length;
    }

    function updateFileInput() {
        const dataTransfer = new DataTransfer();
        selectedFiles.forEach(file => dataTransfer.items.add(file));
        fileInput.files = dataTransfer.files;
    }

    // Make removePhoto global
    window.removePhoto = function(index) {
        selectedFiles.splice(index, 1);
        updatePreview();
        updateFileInput();
    };

    // Form validation
    document.getElementById('verifikasiForm').addEventListener('submit', function(e) {
        const kondisi = document.querySelector('input[name="kondisi"]:checked');
        
        if (!kondisi) {
            e.preventDefault();
            alert('Pilih kondisi barang terlebih dahulu!');
            return false;
        }

        if (selectedFiles.length === 0) {
            e.preventDefault();
            alert('Upload minimal 1 foto bukti!');
            return false;
        }

        // Disable submit button
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Menyimpan...';
    });
});
</script>
@endsection