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

    /* ---------- sidebar info ---------- */
    .info-row {
        display: flex;
        justify-content: space-between;
        padding: 0.7rem 0;
        border-bottom: 1px solid #f0f0f0;
    }
    .info-row:last-child { border-bottom: none; }
    .info-label { color: #6c757d; font-weight: 600; font-size: 0.9rem; }
    .info-value { color: #2d3748; font-weight: 700; font-size: 0.9rem; text-align: right; }

    /* ---------- kondisi radio ---------- */
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

    /* ---------- upload area ---------- */
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

    .preview-wrap img {
        max-width: 100%;
        max-height: 260px;
        border-radius: 8px;
        margin-top: 0.75rem;
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

                    <!-- Foto Bukti -->
                    <div class="mb-4">
                        <label class="form-label fw-bold">Foto Bukti</label>

                        <div class="upload-zone" id="uploadZone">
                            <input type="file" name="foto_bukti" id="fotoInput"
                                   class="d-none" accept="image/jpeg,image/jpg,image/png">

                            <!-- placeholder -->
                            <div id="uploadPlaceholder">
                                <div class="upload-icon"><i class="ti ti-cloud-upload"></i></div>
                                <p class="mb-0 fw-semibold">Klik atau tarik foto ke sini</p>
                                <small class="text-muted">JPG / PNG · maks 2 MB</small>
                            </div>

                            <!-- preview -->
                            <div id="previewWrap" class="preview-wrap d-none">
                                <img id="previewImg" src="" alt="preview">
                                <div class="mt-2">
                                    <button type="button" class="btn btn-sm btn-outline-danger" id="btnHapusFoto">
                                        <i class="ti ti-trash"></i> Hapus
                                    </button>
                                </div>
                            </div>
                        </div>

                        <small class="text-muted">Opsional – foto kondisi ruangan sebagai bukti</small>
                        @error('foto_bukti')
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

    /* ---------- kondisi radio ---------- */
    document.querySelectorAll('.kondisi-option').forEach(function (el) {
        el.addEventListener('click', function () {
            document.querySelectorAll('.kondisi-option').forEach(function (o) { o.classList.remove('selected'); });
            el.classList.add('selected');
            el.querySelector('.kondisi-radio').checked = true;
        });
    });

    /* ---------- file upload / drag-drop ---------- */
    var zone        = document.getElementById('uploadZone');
    var input       = document.getElementById('fotoInput');
    var placeholder = document.getElementById('uploadPlaceholder');
    var previewWrap = document.getElementById('previewWrap');
    var previewImg  = document.getElementById('previewImg');
    var btnHapus    = document.getElementById('btnHapusFoto');

    zone.addEventListener('click', function (e) {
        if (e.target === btnHapus || btnHapus.contains(e.target)) return;
        input.click();
    });

    zone.addEventListener('dragover', function (e) { e.preventDefault(); zone.classList.add('dragover'); });
    zone.addEventListener('dragleave', function (e) { e.preventDefault(); zone.classList.remove('dragover'); });
    zone.addEventListener('drop', function (e) {
        e.preventDefault();
        zone.classList.remove('dragover');
        if (e.dataTransfer.files.length) loadFile(e.dataTransfer.files[0]);
    });

    input.addEventListener('change', function () {
        if (this.files[0]) loadFile(this.files[0]);
    });

    function loadFile(file) {
        if (!['image/jpeg','image/jpg','image/png'].includes(file.type)) {
            return alert('Format tidak valid. Gunakan JPG atau PNG.');
        }
        if (file.size > 2 * 1024 * 1024) {
            return alert('Ukuran file melebihi 2 MB.');
        }
        var reader = new FileReader();
        reader.onload = function (e) {
            previewImg.src = e.target.result;
            placeholder.classList.add('d-none');
            previewWrap.classList.remove('d-none');
        };
        reader.readAsDataURL(file);
    }

    btnHapus.addEventListener('click', function (e) {
        e.stopPropagation();
        input.value = '';
        previewImg.src = '';
        previewWrap.classList.add('d-none');
        placeholder.classList.remove('d-none');
    });

    /* ---------- form submit ---------- */
    document.getElementById('formVerifikasi').addEventListener('submit', function (e) {
        var checked = document.querySelector('input[name="kondisi_ruangan"]:checked');
        if (!checked) {
            e.preventDefault();
            alert('Silakan pilih kondisi ruangan terlebih dahulu.');
            return;
        }
        if (!confirm('Pastikan data sudah benar.\nKondisi: ' + checked.value.toUpperCase())) {
            e.preventDefault();
        } else {
            document.getElementById('btnSubmit').disabled = true;
            document.getElementById('btnSubmit').innerHTML = '<i class="ti ti-loader"></i> Menyimpan …';
        }
    });
})();
</script>
@endpush