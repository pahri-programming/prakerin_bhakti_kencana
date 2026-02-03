@extends('layouts.backend')

@section('title', 'Form Verifikasi Peminjaman')

@push('styles')
<style>
    .detail-card {
        background: white;
        border-radius: 15px;
        padding: 2rem;
        box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        margin-bottom: 2rem;
    }

    .form-card {
        background: white;
        border-radius: 15px;
        padding: 2rem;
        box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    }

    .kondisi-option {
        border: 2px solid #e0e0e0;
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 1rem;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .kondisi-option:hover {
        border-color: #ff9800;
        background: #fff3e0;
    }

    .kondisi-option input[type="radio"]:checked ~ * {
        color: #ff9800;
        font-weight: 600;
    }

    .kondisi-option input[type="radio"]:checked ~ label {
        color: #ff9800 !important;
    }

    .kondisi-option.checked {
        border-color: #ff9800;
        background: #fff3e0;
        border-width: 3px;
    }

    .kondisi-icon {
        font-size: 2.5rem;
        margin-bottom: 0.5rem;
    }

    .info-detail {
        display: flex;
        justify-content: space-between;
        padding: 1rem;
        border-bottom: 1px solid #f0f0f0;
    }

    .info-detail:last-child {
        border-bottom: none;
    }

    .info-label {
        font-weight: 600;
        color: #6c757d;
    }

    .info-value {
        font-weight: 700;
        color: #2d3748;
    }

    .upload-area {
        border: 2px dashed #e0e0e0;
        border-radius: 12px;
        padding: 2rem;
        text-align: center;
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .upload-area:hover {
        border-color: #ff9800;
        background: #fff3e0;
    }

    .upload-area.dragover {
        border-color: #ff9800;
        background: #fff3e0;
    }

    .preview-image {
        max-width: 100%;
        max-height: 300px;
        border-radius: 8px;
        margin-top: 1rem;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="fw-bold mb-2">
                        <i class="ti ti-check-circle text-warning"></i> Form Verifikasi Peminjaman
                    </h2>
                    <p class="text-muted mb-0">Verifikasi kondisi barang yang telah dikembalikan</p>
                </div>
                <a href="{{ route('pic.verifikasi-peminjaman.index') }}" class="btn btn-outline-secondary">
                    <i class="ti ti-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Detail Peminjaman -->
        <div class="col-lg-5 mb-4">
            <div class="detail-card">
                <h5 class="fw-bold mb-4">
                    <i class="ti ti-info-circle text-primary"></i> Detail Peminjaman
                </h5>

                {{-- 🔥 FIX: Pakai barang_summary accessor --}}
                <div class="info-detail">
                    <span class="info-label">Barang:</span>
                    <span class="info-value">{{ $peminjaman->barang_summary }}</span>
                </div>

                <div class="info-detail">
                    <span class="info-label">Peminjam:</span>
                    <span class="info-value">{{ $peminjaman->user->name }}</span>
                </div>

                <div class="info-detail">
                    <span class="info-label">Instansi:</span>
                    <span class="info-value">{{ $peminjaman->user->instansi ?? $peminjaman->instansi ?? '-' }}</span>
                </div>

                {{-- 🔥 FIX: Pakai total_jumlah accessor --}}
                <div class="info-detail">
                    <span class="info-label">Total Item:</span>
                    <span class="info-value">{{ $peminjaman->total_jumlah }} unit</span>
                </div>

                <div class="info-detail">
                    <span class="info-label">Tanggal Pinjam:</span>
                    <span class="info-value">
                        {{ \Carbon\Carbon::parse($peminjaman->tanggal_pinjam)->translatedFormat('d F Y') }}
                    </span>
                </div>

                <div class="info-detail">
                    <span class="info-label">Tanggal Kembali:</span>
                    <span class="info-value">
                        {{ \Carbon\Carbon::parse($peminjaman->tanggal_kembali)->translatedFormat('d F Y') }}
                    </span>
                </div>

                <div class="info-detail">
                    <span class="info-label">Status:</span>
                    <span class="badge bg-success">{{ ucfirst($peminjaman->status) }}</span>
                </div>

                @if($peminjaman->keterangan)
                <div class="mt-3">
                    <strong class="info-label">Keterangan:</strong>
                    <p class="text-muted mt-2">{{ $peminjaman->keterangan }}</p>
                </div>
                @endif

                {{-- Detail Barang List --}}
                <div class="mt-3">
                    <strong class="info-label">Detail Barang:</strong>
                    <div class="mt-2">
                        @foreach($peminjaman->detailbarangs as $detail)
                        <div class="p-2 bg-light rounded mb-2">
                            <small>
                                <i class="ti ti-package"></i>
                                <strong>{{ $detail->barangRuangan->barang->nama }}</strong>
                                <br>
                                <i class="ti ti-door"></i> {{ $detail->barangRuangan->ruangan->nama_ruangan }}
                                <br>
                                <i class="ti ti-hash"></i> Jumlah: {{ $detail->jumlah }} unit
                            </small>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Verifikasi -->
        <div class="col-lg-7">
            <div class="form-card">
                <h5 class="fw-bold mb-4">
                    <i class="ti ti-clipboard-check text-warning"></i> Form Verifikasi
                </h5>

                <form action="{{ route('pic.verifikasi-peminjaman.store', $peminjaman->id) }}" 
                      method="POST" 
                      enctype="multipart/form-data"
                      id="verifikasiForm">
                    @csrf

                    <!-- Kondisi Barang -->
                    <div class="mb-4">
                        <label class="form-label fw-bold">
                            <i class="ti ti-check"></i> Kondisi Barang <span class="text-danger">*</span>
                        </label>

                        <div class="row">
                            <div class="col-md-6">
                                <label class="kondisi-option" data-value="baik">
                                    <input type="radio" name="kondisi" value="baik" 
                                           class="form-check-input d-none kondisi-radio" required>
                                    <div class="text-center">
                                        <div class="kondisi-icon">✅</div>
                                        <h6 class="mb-0">Baik</h6>
                                        <small class="text-muted">Barang dalam kondisi sempurna</small>
                                    </div>
                                </label>
                            </div>

                            <div class="col-md-6">
                                <label class="kondisi-option" data-value="rusak_ringan">
                                    <input type="radio" name="kondisi" value="rusak_ringan" 
                                           class="form-check-input d-none kondisi-radio" required>
                                    <div class="text-center">
                                        <div class="kondisi-icon">⚠️</div>
                                        <h6 class="mb-0">Rusak Ringan</h6>
                                        <small class="text-muted">Ada kerusakan kecil</small>
                                    </div>
                                </label>
                            </div>

                            <div class="col-md-6">
                                <label class="kondisi-option" data-value="rusak_berat">
                                    <input type="radio" name="kondisi" value="rusak_berat" 
                                           class="form-check-input d-none kondisi-radio" required>
                                    <div class="text-center">
                                        <div class="kondisi-icon">🔴</div>
                                        <h6 class="mb-0">Rusak Berat</h6>
                                        <small class="text-muted">Kerusakan signifikan</small>
                                    </div>
                                </label>
                            </div>

                            <div class="col-md-6">
                                <label class="kondisi-option" data-value="hilang">
                                    <input type="radio" name="kondisi" value="hilang" 
                                           class="form-check-input d-none kondisi-radio" required>
                                    <div class="text-center">
                                        <div class="kondisi-icon">❌</div>
                                        <h6 class="mb-0">Hilang</h6>
                                        <small class="text-muted">Barang tidak dikembalikan</small>
                                    </div>
                                </label>
                            </div>
                        </div>

                        @error('kondisi')
                        <div class="text-danger mt-2">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Catatan PIC -->
                    <div class="mb-4">
                        <label class="form-label fw-bold">
                            <i class="ti ti-note"></i> Catatan PIC
                        </label>
                        <textarea name="catatan_pic" class="form-control" rows="4" 
                                  placeholder="Jelaskan kondisi barang secara detail...">{{ old('catatan_pic') }}</textarea>
                        <small class="text-muted">Opsional - Jelaskan detail kondisi atau kerusakan jika ada</small>
                        @error('catatan_pic')
                        <div class="text-danger mt-2">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Upload Foto -->
                    <div class="mb-4">
                        <label class="form-label fw-bold">
                            <i class="ti ti-camera"></i> Foto Bukti
                        </label>
                        <div class="upload-area" id="uploadArea">
                            <input type="file" name="foto_bukti" id="foto_bukti" 
                                   class="d-none" accept="image/jpeg,image/jpg,image/png">
                            <div id="uploadContent">
                                <i class="ti ti-cloud-upload" style="font-size: 3rem; color: #ff9800;"></i>
                                <h6 class="mt-2">Klik atau Drag & Drop Foto</h6>
                                <small class="text-muted">Format: JPG, JPEG, PNG (Max: 2MB)</small>
                            </div>
                            <div id="previewContent" class="d-none">
                                <img id="preview" class="preview-image" src="" alt="Preview">
                                <div class="mt-3">
                                    <button type="button" class="btn btn-sm btn-danger" id="removeImage">
                                        <i class="ti ti-trash"></i> Hapus Foto
                                    </button>
                                </div>
                            </div>
                        </div>
                        <small class="text-muted">Opsional - Upload foto kondisi barang sebagai bukti</small>
                        @error('foto_bukti')
                        <div class="text-danger mt-2">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Submit Buttons -->
                    <div class="d-flex justify-content-between align-items-center">
                        <a href="{{ route('pic.verifikasi-peminjaman.index') }}" class="btn btn-outline-secondary">
                            <i class="ti ti-x"></i> Batal
                        </a>
                        <button type="submit" class="btn btn-warning btn-lg" id="submitBtn">
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
$(document).ready(function() {
    // Handle kondisi radio selection
    $('.kondisi-option').click(function() {
        $('.kondisi-option').removeClass('checked');
        $(this).addClass('checked');
        $(this).find('.kondisi-radio').prop('checked', true);
    });

    // File upload handling
    const uploadArea = $('#uploadArea');
    const fileInput = $('#foto_bukti');
    const uploadContent = $('#uploadContent');
    const previewContent = $('#previewContent');
    const preview = $('#preview');
    const removeBtn = $('#removeImage');

    // 🔥 FIX: Click to upload - hanya di uploadContent
    uploadContent.click(function(e) {
        e.stopPropagation();
        fileInput.click();
    });

    // 🔥 FIX: Tambahkan click pada ikon upload juga
    uploadContent.find('i, h6, small').click(function(e) {
        e.stopPropagation();
        fileInput.click();
    });

    // Drag & drop
    uploadArea.on('dragover', function(e) {
        e.preventDefault();
        e.stopPropagation();
        $(this).addClass('dragover');
    });

    uploadArea.on('dragleave', function(e) {
        e.preventDefault();
        e.stopPropagation();
        $(this).removeClass('dragover');
    });

    uploadArea.on('drop', function(e) {
        e.preventDefault();
        e.stopPropagation();
        $(this).removeClass('dragover');
        
        const files = e.originalEvent.dataTransfer.files;
        if (files.length > 0) {
            fileInput[0].files = files;
            handleFileSelect(files[0]);
        }
    });

    // File input change
    fileInput.change(function() {
        if (this.files && this.files[0]) {
            handleFileSelect(this.files[0]);
        }
    });

    // Handle file selection
    function handleFileSelect(file) {
        // Validate file type
        const validTypes = ['image/jpeg', 'image/jpg', 'image/png'];
        if (!validTypes.includes(file.type)) {
            alert('Format file tidak valid! Gunakan JPG, JPEG, atau PNG');
            fileInput.val(''); // Reset input
            return;
        }

        // Validate file size (2MB)
        if (file.size > 2 * 1024 * 1024) {
            alert('Ukuran file terlalu besar! Maksimal 2MB');
            fileInput.val(''); // Reset input
            return;
        }

        // Show preview
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.attr('src', e.target.result);
            uploadContent.addClass('d-none');
            previewContent.removeClass('d-none');
        };
        reader.readAsDataURL(file);
    }

    // Remove image
    removeBtn.click(function(e) {
        e.stopPropagation();
        e.preventDefault();
        fileInput.val('');
        preview.attr('src', '');
        previewContent.addClass('d-none');
        uploadContent.removeClass('d-none');
    });

    // Form validation
    $('#verifikasiForm').submit(function(e) {
        const kondisi = $('input[name="kondisi"]:checked').val();
        if (!kondisi) {
            e.preventDefault();
            alert('Silakan pilih kondisi barang!');
            return false;
        }

        // Confirm submission
        const kondisiText = kondisi.replace(/_/g, ' ').toUpperCase();
        const confirmMsg = `Apakah Anda yakin data verifikasi sudah benar?\n\nKondisi: ${kondisiText}`;
        if (!confirm(confirmMsg)) {
            e.preventDefault();
            return false;
        }

        $('#submitBtn').prop('disabled', true).html('<i class="ti ti-loader ti-spin"></i> Menyimpan...');
    });
});
</script>
@endpush