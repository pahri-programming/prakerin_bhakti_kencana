@extends('layouts.backend')

@section('title', 'Form Verifikasi Booking – ' . $booking->ruangan->nama_ruangan)

@push('styles')
<style>
    .detail-card, .form-card {
        background: #fff;
        border-radius: 14px;
        padding: 1.75rem;
        box-shadow: 0 3px 14px rgba(0,0,0,0.08);
    }

    /*  sidebar info  */
    .info-row {
        display: flex;
        justify-content: space-between;
        padding: 0.7rem 0;
        border-bottom: 1px solid #f0f0f0;
    }
    .info-row:last-child { border-bottom: none; }
    .info-label { color: #6c757d; font-weight: 600; font-size: 0.9rem; }
    .info-value { color: #2d3748; font-weight: 700; font-size: 0.9rem; text-align: right; }

    /*  kondisi radio  */
    .kondisi-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1rem;
    }

    .kondisi-option {
        border: 2px solid #dee2e6;
        border-radius: 12px;
        padding: 1.25rem 0.75rem;
        text-align: center;
        cursor: pointer;
        transition: all 0.2s ease;
        position: relative;
    }

    .kondisi-option:hover {
        border-color: #2980b9;
        background: #eef5fc;
    }

    .kondisi-option.selected {
        border-color: #2980b9;
        background: #eef5fc;
        border-width: 3px;
        box-shadow: 0 0 0 3px rgba(41,128,185,0.2);
    }

    .kondisi-option input[type="radio"] { display: none; }

    .kondisi-option .ikon { font-size: 2.2rem; margin-bottom: 0.4rem; }
    .kondisi-option .label { font-weight: 700; font-size: 0.95rem; color: #2d3748; }
    .kondisi-option .sub   { font-size: 0.78rem; color: #6c757d; margin-top: 0.15rem; }

    /*  upload area  */
    .upload-zone {
        border: 2px dashed #dee2e6;
        border-radius: 12px;
        padding: 2rem;
        text-align: center;
        cursor: pointer;
        transition: all 0.25s ease;
    }

    .upload-zone:hover,
    .upload-zone.dragover {
        border-color: #2980b9;
        background: #eef5fc;
    }

    .upload-zone .upload-icon { font-size: 2.8rem; color: #2980b9; }

    /*  preview gallery  */
    .preview-gallery {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
        gap: 1rem;
        margin-top: 1rem;
    }

    .preview-item {
        position: relative;
        border-radius: 8px;
        overflow: hidden;
        border: 2px solid #dee2e6;
        aspect-ratio: 1;
    }

    .preview-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .preview-item .remove-btn {
        position: absolute;
        top: 5px;
        right: 5px;
        background: rgba(220, 53, 69, 0.9);
        color: white;
        border: none;
        border-radius: 50%;
        width: 28px;
        height: 28px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s;
        font-size: 16px;
    }

    .preview-item .remove-btn:hover {
        background: rgba(220, 53, 69, 1);
        transform: scale(1.1);
    }

    .preview-item .photo-number {
        position: absolute;
        bottom: 5px;
        left: 5px;
        background: rgba(41, 128, 185, 0.9);
        color: white;
        padding: 2px 8px;
        border-radius: 4px;
        font-size: 0.75rem;
        font-weight: 600;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1"><i class="ti ti-clipboard-check text-primary"></i> Form Verifikasi Booking</h2>
            <p class="text-muted mb-0">Isi kondisi ruangan setelah penggunaan</p>
        </div>
        <a href="{{ route('pic.verifikasi-booking.index') }}" class="btn btn-outline-secondary">
            <i class="ti ti-arrow-left"></i> Kembali
        </a>
    </div>

    <!-- Alerts -->
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row">

        <!-- ============ SIDEBAR — detail booking ============ -->
        <div class="col-lg-4 mb-4">
            <div class="detail-card">
                <h5 class="fw-bold mb-3"><i class="ti ti-info-circle text-primary"></i> Detail Booking</h5>

                <div class="info-row">
                    <span class="info-label">Ruangan</span>
                    <span class="info-value">{{ $booking->ruangan->nama_ruangan }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Lokasi</span>
                    <span class="info-value">{{ $booking->ruangan->lokasi ?? '–' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Kapasitas</span>
                    <span class="info-value">{{ $booking->ruangan->kapasitas }} orang</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Peminjam</span>
                    <span class="info-value">{{ $booking->user->name }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Instansi</span>
                    <span class="info-value">{{ $booking->user->instansi ?? '–' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Tanggal</span>
                    <span class="info-value">{{ \Carbon\Carbon::parse($booking->tanggal)->translatedFormat('d F Y') }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Waktu</span>
                    <span class="info-value">{{ substr($booking->waktu_mulai, 0, 5) }} – {{ substr($booking->waktu_selesai, 0, 5) }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Status</span>
                    <span class="badge bg-success">{{ $booking->status }}</span>
                </div>

                @if($booking->keperluan)
                <div class="mt-3 p-3 bg-light rounded">
                    <strong class="text-muted" style="font-size:.88rem">Keperluan</strong>
                    <p class="mb-0 mt-1" style="font-size:.9rem">{{ $booking->keperluan }}</p>
                </div>
                @endif
            </div>
        </div>

        <!-- ============ MAIN — form verifikasi ============ -->
        <div class="col-lg-8">
            <div class="form-card">
                <h5 class="fw-bold mb-4"><i class="ti ti-check text-success"></i> Isi Verifikasi</h5>

                <form id="formVerifikasi"
                      action="{{ route('pic.verifikasi-booking.store', $booking->id) }}"
                      method="POST"
                      enctype="multipart/form-data">
                    @csrf

                    <!-- Kondisi Ruangan -->
                    <label class="form-label fw-bold">Kondisi Ruangan <span class="text-danger">*</span></label>

                    <div class="kondisi-grid mb-2">
                        <!-- Baik -->
                        <label class="kondisi-option" data-val="baik">
                            <input type="radio" name="kondisi_ruangan" value="baik" class="kondisi-radio" required>
                            <div class="ikon">✅</div>
                            <div class="label">Baik</div>
                            <div class="sub">Bersih & terawat</div>
                        </label>

                        <!-- Kotor -->
                        <label class="kondisi-option" data-val="kotor">
                            <input type="radio" name="kondisi_ruangan" value="kotor" class="kondisi-radio" required>
                            <div class="ikon">🧹</div>
                            <div class="label">Kotor</div>
                            <div class="sub">Perlu dibersihkan</div>
                        </label>

                        <!-- Rusak -->
                        <label class="kondisi-option" data-val="rusak">
                            <input type="radio" name="kondisi_ruangan" value="rusak" class="kondisi-radio" required>
                            <div class="ikon">🔴</div>
                            <div class="label">Rusak</div>
                            <div class="sub">Butuh perbaikan</div>
                        </label>
                    </div>

                    @error('kondisi_ruangan')
                    <div class="text-danger mb-3" style="font-size:.85rem">{{ $message }}</div>
                    @enderror

                    <!-- Catatan -->
                    <div class="mb-4">
                        <label class="form-label fw-bold">Catatan PIC</label>
                        <textarea name="catatan_pic" class="form-control" rows="3"
                                  placeholder="Deskripsikan kondisi ruangan secara detail …">{{ old('catatan_pic') }}</textarea>
                        <small class="text-muted">Opsional – detail tentang kebersihan / kerusakan</small>
                        @error('catatan_pic')
                        <div class="text-danger mt-1" style="font-size:.85rem">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Foto Bukti (Multiple) -->
                    <div class="mb-4">
                        <label class="form-label fw-bold">Foto Bukti <span class="badge bg-info">Maksimal 6 foto</span></label>

                        <div class="upload-zone" id="uploadZone">
                            <input type="file" name="foto_bukti[]" id="fotoInput"
                                   class="d-none" accept="image/jpeg,image/jpg,image/png" multiple>

                            <!-- placeholder -->
                            <div id="uploadPlaceholder">
                                <div class="upload-icon"><i class="ti ti-cloud-upload"></i></div>
                                <p class="mb-0 fw-semibold">Klik atau tarik foto ke sini</p>
                                <small class="text-muted">JPG / PNG · maks 2 MB per foto · maksimal 6 foto</small>
                            </div>
                        </div>

                        <!-- Preview Gallery -->
                        <div id="previewGallery" class="preview-gallery d-none"></div>

                        <small class="text-muted d-block mt-2">
                            <i class="ti ti-info-circle"></i> Opsional – foto kondisi ruangan sebagai bukti. 
                            Upload beberapa foto untuk dokumentasi lebih lengkap.
                        </small>
                        @error('foto_bukti')
                        <div class="text-danger mt-1" style="font-size:.85rem">{{ $message }}</div>
                        @enderror
                        @error('foto_bukti.*')
                        <div class="text-danger mt-1" style="font-size:.85rem">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Buttons -->
                    <div class="d-flex justify-content-between align-items-center">
                        <a href="{{ route('pic.verifikasi-booking.index') }}" class="btn btn-outline-secondary">
                            <i class="ti ti-x"></i> Batal
                        </a>
                        <button type="submit" class="btn btn-primary btn-lg" id="btnSubmit">
                            <i class="ti ti-check"></i> Simpan Verifikasi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    'use strict';

    const MAX_FILES = 6;
    const MAX_SIZE = 2 * 1024 * 1024; // 2MB
    let selectedFiles = [];

    /*  kondisi radio  */
    document.querySelectorAll('.kondisi-option').forEach(function (el) {
        el.addEventListener('click', function () {
            document.querySelectorAll('.kondisi-option').forEach(function (o) { o.classList.remove('selected'); });
            el.classList.add('selected');
            el.querySelector('.kondisi-radio').checked = true;
        });
    });

    /*  file upload / drag-drop (MULTIPLE)  */
    var zone        = document.getElementById('uploadZone');
    var input       = document.getElementById('fotoInput');
    var placeholder = document.getElementById('uploadPlaceholder');
    var gallery     = document.getElementById('previewGallery');

    zone.addEventListener('click', function (e) {
        if (!e.target.closest('.remove-btn')) {
            input.click();
        }
    });

    zone.addEventListener('dragover', function (e) { 
        e.preventDefault(); 
        zone.classList.add('dragover'); 
    });

    zone.addEventListener('dragleave', function (e) { 
        e.preventDefault(); 
        zone.classList.remove('dragover'); 
    });

    zone.addEventListener('drop', function (e) {
        e.preventDefault();
        zone.classList.remove('dragover');
        if (e.dataTransfer.files.length) {
            handleFiles(e.dataTransfer.files);
        }
    });

    input.addEventListener('change', function () {
        if (this.files.length) {
            handleFiles(this.files);
        }
    });

    function handleFiles(files) {
        const filesArray = Array.from(files);
        
        // Check total files limit
        if (selectedFiles.length + filesArray.length > MAX_FILES) {
            alert(`Maksimal ${MAX_FILES} foto. Anda sudah memilih ${selectedFiles.length} foto.`);
            return;
        }

        filesArray.forEach(file => {
            // Validate file type
            if (!['image/jpeg', 'image/jpg', 'image/png'].includes(file.type)) {
                alert(`File ${file.name} bukan format JPG/PNG yang valid.`);
                return;
            }

            // Validate file size
            if (file.size > MAX_SIZE) {
                alert(`File ${file.name} melebihi 2 MB.`);
                return;
            }

            selectedFiles.push(file);
        });

        updatePreview();
        updateFileInput();
    }

    function updatePreview() {
        if (selectedFiles.length === 0) {
            gallery.classList.add('d-none');
            placeholder.classList.remove('d-none');
            return;
        }

        placeholder.classList.add('d-none');
        gallery.classList.remove('d-none');
        gallery.innerHTML = '';

        selectedFiles.forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = function (e) {
                const previewItem = document.createElement('div');
                previewItem.className = 'preview-item';
                previewItem.innerHTML = `
                    <img src="${e.target.result}" alt="Preview ${index + 1}">
                    <button type="button" class="remove-btn" data-index="${index}">
                        <i class="ti ti-x"></i>
                    </button>
                    <span class="photo-number">${index + 1}/${MAX_FILES}</span>
                `;
                
                gallery.appendChild(previewItem);

                // Add remove handler
                previewItem.querySelector('.remove-btn').addEventListener('click', function(e) {
                    e.stopPropagation();
                    removeFile(index);
                });
            };
            reader.readAsDataURL(file);
        });
    }

    function removeFile(index) {
        selectedFiles.splice(index, 1);
        updatePreview();
        updateFileInput();
    }

    function updateFileInput() {
        // Create new DataTransfer to update input files
        const dataTransfer = new DataTransfer();
        selectedFiles.forEach(file => {
            dataTransfer.items.add(file);
        });
        input.files = dataTransfer.files;
    }

    /*  form submit  */
    document.getElementById('formVerifikasi').addEventListener('submit', function (e) {
        var checked = document.querySelector('input[name="kondisi_ruangan"]:checked');
        if (!checked) {
            e.preventDefault();
            alert('Silakan pilih kondisi ruangan terlebih dahulu.');
            return;
        }

        const confirmMsg = `Pastikan data sudah benar.\nKondisi: ${checked.value.toUpperCase()}\nJumlah foto: ${selectedFiles.length}`;
        
        if (!confirm(confirmMsg)) {
            e.preventDefault();
        } else {
            document.getElementById('btnSubmit').disabled = true;
            document.getElementById('btnSubmit').innerHTML = '<i class="ti ti-loader"></i> Menyimpan …';
        }
    });
})();
</script>
@endpush