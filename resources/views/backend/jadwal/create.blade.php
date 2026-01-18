@extends('layouts.backend')

@section('title', 'Tambah Jadwal')

@push('styles')
<style>
    .form-card {
        border: none;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        border-radius: 15px;
        overflow: hidden;
    }

    .form-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 30px;
        position: relative;
    }

    .form-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.1'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        opacity: 0.1;
    }

    .form-header h2 {
        position: relative;
        z-index: 1;
        font-weight: bold;
        margin: 0;
    }

    .form-body {
        padding: 40px;
    }

    .form-label {
        font-weight: 600;
        color: #333;
        margin-bottom: 8px;
    }

    .form-label .required {
        color: #e74c3c;
        margin-left: 3px;
    }

    .form-control, .form-select {
        border: 2px solid #e0e0e0;
        border-radius: 10px;
        padding: 12px 15px;
        transition: all 0.3s ease;
    }

    .form-control:focus, .form-select:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.15);
    }

    .form-control.is-invalid, .form-select.is-invalid {
        border-color: #e74c3c;
    }

    .invalid-feedback {
        display: block;
        color: #e74c3c;
        font-size: 0.875rem;
        margin-top: 5px;
    }

    .input-group-text {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        border-radius: 10px 0 0 10px;
    }

    .btn-submit {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        padding: 12px 40px;
        border-radius: 10px;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
    }

    .btn-cancel {
        border: 2px solid #e0e0e0;
        padding: 12px 40px;
        border-radius: 10px;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-cancel:hover {
        background: #f8f9fa;
        border-color: #667eea;
    }

    .info-box {
        background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
        border-left: 4px solid #2196F3;
        padding: 15px 20px;
        border-radius: 10px;
        margin-bottom: 20px;
    }

    .info-box i {
        font-size: 1.5rem;
        color: #2196F3;
        margin-right: 10px;
    }

    .preview-box {
        background: linear-gradient(135deg, #f3e5f5 0%, #e1bee7 100%);
        border-left: 4px solid #9c27b0;
        padding: 20px;
        border-radius: 10px;
        margin-top: 20px;
    }

    .preview-item {
        display: flex;
        align-items: center;
        margin-bottom: 10px;
    }

    .preview-item i {
        font-size: 1.2rem;
        color: #9c27b0;
        margin-right: 10px;
        width: 25px;
    }

    .preview-value {
        font-weight: 600;
        color: #333;
    }

    .time-picker-group {
        display: flex;
        gap: 15px;
        align-items: center;
    }

    .time-picker-wrapper {
        flex: 1;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Form Card -->
            <div class="card form-card">
                <!-- Header -->
                <div class="form-header">
                    <h2>
                        <i class="ti ti-calendar-plus"></i> Tambah Jadwal Baru
                    </h2>
                    <p class="mb-0 mt-2 opacity-75">Isi form di bawah untuk menambahkan jadwal kegiatan</p>
                </div>

                <!-- Form Body -->
                <div class="form-body">
                    <!-- Info Box -->
                    <div class="info-box">
                        <div class="d-flex align-items-start">
                            <i class="ti ti-info-circle"></i>
                            <div>
                                <strong>Informasi:</strong>
                                <p class="mb-0 mt-1">Pastikan waktu yang Anda pilih tidak bentrok dengan jadwal lain di ruangan yang sama.</p>
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('backend.jadwal.store') }}" method="POST" id="formJadwal">
                        @csrf

                        <div class="row">
                            <!-- Ruangan -->
                            <div class="col-md-12 mb-4">
                                <label for="ruang_id" class="form-label">
                                    <i class="ti ti-door"></i> Ruangan <span class="required">*</span>
                                </label>
                                <select name="ruang_id" id="ruang_id" 
                                        class="form-select @error('ruang_id') is-invalid @enderror" 
                                        required>
                                    <option value="">-- Pilih Ruangan --</option>
                                    @foreach($ruangans as $ruangan)
                                        <option value="{{ $ruangan->id }}" 
                                                {{ old('ruang_id') == $ruangan->id ? 'selected' : '' }}
                                                data-kapasitas="{{ $ruangan->kapasitas }}"
                                                data-lokasi="{{ $ruangan->lokasi }}">
                                            {{ $ruangan->nama_ruangan }} 
                                            (Kapasitas: {{ $ruangan->kapasitas }} orang - {{ $ruangan->lokasi }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('ruang_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted d-block mt-2" id="ruanganInfo"></small>
                            </div>

                            <!-- Tanggal -->
                            <div class="col-md-6 mb-4">
                                <label for="tanggal" class="form-label">
                                    <i class="ti ti-calendar"></i> Tanggal <span class="required">*</span>
                                </label>
                                <input type="date" name="tanggal" id="tanggal" 
                                       class="form-control @error('tanggal') is-invalid @enderror" 
                                       value="{{ old('tanggal', date('Y-m-d')) }}"
                                       min="{{ date('Y-m-d') }}"
                                       required>
                                @error('tanggal')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Kegiatan -->
                            <div class="col-md-6 mb-4">
                                <label for="kegiatan" class="form-label">
                                    <i class="ti ti-clipboard-text"></i> Nama Kegiatan <span class="required">*</span>
                                </label>
                                <input type="text" name="kegiatan" id="kegiatan" 
                                       class="form-control @error('kegiatan') is-invalid @enderror" 
                                       value="{{ old('kegiatan') }}"
                                       placeholder="Contoh: Rapat Koordinasi"
                                       maxlength="255"
                                       required>
                                @error('kegiatan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Waktu Mulai & Selesai -->
                            <div class="col-md-12 mb-4">
                                <label class="form-label">
                                    <i class="ti ti-clock"></i> Waktu Kegiatan <span class="required">*</span>
                                </label>
                                <div class="time-picker-group">
                                    <div class="time-picker-wrapper">
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="ti ti-clock"></i>
                                            </span>
                                            <input type="time" name="waktu_mulai" id="waktu_mulai" 
                                                   class="form-control @error('waktu_mulai') is-invalid @enderror" 
                                                   value="{{ old('waktu_mulai', '08:00') }}"
                                                   required>
                                        </div>
                                        <small class="text-muted">Waktu Mulai</small>
                                        @error('waktu_mulai')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="text-center pt-3">
                                        <i class="ti ti-arrow-right fs-4 text-muted"></i>
                                    </div>

                                    <div class="time-picker-wrapper">
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="ti ti-clock"></i>
                                            </span>
                                            <input type="time" name="waktu_selesai" id="waktu_selesai" 
                                                   class="form-control @error('waktu_selesai') is-invalid @enderror" 
                                                   value="{{ old('waktu_selesai', '10:00') }}"
                                                   required>
                                        </div>
                                        <small class="text-muted">Waktu Selesai</small>
                                        @error('waktu_selesai')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <small class="text-muted d-block mt-2">
                                    <i class="ti ti-alert-circle"></i> Durasi kegiatan: <span id="durasi" class="fw-bold">-</span>
                                </small>
                            </div>
                        </div>

                        <!-- Preview Box -->
                        <div class="preview-box" id="previewBox" style="display: none;">
                            <h6 class="fw-bold mb-3">
                                <i class="ti ti-eye"></i> Preview Jadwal
                            </h6>
                            <div class="preview-item">
                                <i class="ti ti-door"></i>
                                <span>Ruangan: <span class="preview-value" id="previewRuangan">-</span></span>
                            </div>
                            <div class="preview-item">
                                <i class="ti ti-calendar"></i>
                                <span>Tanggal: <span class="preview-value" id="previewTanggal">-</span></span>
                            </div>
                            <div class="preview-item">
                                <i class="ti ti-clipboard-text"></i>
                                <span>Kegiatan: <span class="preview-value" id="previewKegiatan">-</span></span>
                            </div>
                            <div class="preview-item">
                                <i class="ti ti-clock"></i>
                                <span>Waktu: <span class="preview-value" id="previewWaktu">-</span></span>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex justify-content-between align-items-center mt-4 pt-4 border-top">
                            <a href="{{ route('backend.jadwal.index') }}" class="btn btn-cancel">
                                <i class="ti ti-arrow-left"></i> Kembali
                            </a>
                            <button type="submit" class="btn btn-primary btn-submit">
                                <i class="ti ti-check"></i> Simpan Jadwal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Update preview when form changes
    function updatePreview() {
        const ruangSelect = $('#ruang_id option:selected');
        const tanggal = $('#tanggal').val();
        const kegiatan = $('#kegiatan').val();
        const waktuMulai = $('#waktu_mulai').val();
        const waktuSelesai = $('#waktu_selesai').val();

        if (ruangSelect.val() && tanggal && kegiatan && waktuMulai && waktuSelesai) {
            $('#previewBox').slideDown();
            $('#previewRuangan').text(ruangSelect.text().trim());
            $('#previewTanggal').text(formatDate(tanggal));
            $('#previewKegiatan').text(kegiatan);
            $('#previewWaktu').text(waktuMulai + ' - ' + waktuSelesai);
        } else {
            $('#previewBox').slideUp();
        }
    }

    // Calculate duration
    function calculateDuration() {
        const waktuMulai = $('#waktu_mulai').val();
        const waktuSelesai = $('#waktu_selesai').val();

        if (waktuMulai && waktuSelesai) {
            const start = new Date('2000-01-01 ' + waktuMulai);
            const end = new Date('2000-01-01 ' + waktuSelesai);
            const diff = (end - start) / 1000 / 60; // in minutes

            if (diff > 0) {
                const hours = Math.floor(diff / 60);
                const minutes = diff % 60;
                let durationText = '';
                
                if (hours > 0) durationText += hours + ' jam ';
                if (minutes > 0) durationText += minutes + ' menit';
                
                $('#durasi').text(durationText || '0 menit').removeClass('text-danger');
            } else {
                $('#durasi').text('Waktu tidak valid!').addClass('text-danger');
            }
        }
    }

    // Format date to Indonesian
    function formatDate(dateString) {
        const date = new Date(dateString);
        const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
        return date.toLocaleDateString('id-ID', options);
    }

    // Show ruangan info
    $('#ruang_id').on('change', function() {
        const selected = $(this).find('option:selected');
        const kapasitas = selected.data('kapasitas');
        const lokasi = selected.data('lokasi');
        
        if (kapasitas && lokasi) {
            $('#ruanganInfo').html(
                '<i class="ti ti-info-circle"></i> Kapasitas: <strong>' + kapasitas + 
                ' orang</strong> | Lokasi: <strong>' + lokasi + '</strong>'
            );
        } else {
            $('#ruanganInfo').html('');
        }
        updatePreview();
    });

    // Event listeners
    $('#tanggal, #kegiatan, #waktu_mulai, #waktu_selesai').on('change input', function() {
        updatePreview();
        calculateDuration();
    });

    // Form validation
    $('#formJadwal').on('submit', function(e) {
        const waktuMulai = $('#waktu_mulai').val();
        const waktuSelesai = $('#waktu_selesai').val();

        if (waktuMulai >= waktuSelesai) {
            e.preventDefault();
            alert('Waktu selesai harus lebih besar dari waktu mulai!');
            return false;
        }
    });

    // Initial calculation
    calculateDuration();
});
</script>
@endpush