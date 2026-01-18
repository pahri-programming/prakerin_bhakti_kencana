@extends('layouts.backend')

@section('title', 'Edit Jadwal')

@push('styles')
<style>
    .form-card {
        border: none;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        border-radius: 15px;
        overflow: hidden;
    }

    .form-header {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
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
        border-color: #f5576c;
        box-shadow: 0 0 0 0.2rem rgba(245, 87, 108, 0.15);
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
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        color: white;
        border: none;
        border-radius: 10px 0 0 10px;
    }

    .btn-submit {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        border: none;
        padding: 12px 40px;
        border-radius: 10px;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(245, 87, 108, 0.4);
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
        border-color: #f5576c;
    }

    .warning-box {
        background: linear-gradient(135deg, #fff3cd 0%, #ffe8a1 100%);
        border-left: 4px solid #ffc107;
        padding: 15px 20px;
        border-radius: 10px;
        margin-bottom: 20px;
    }

    .warning-box i {
        font-size: 1.5rem;
        color: #ffc107;
        margin-right: 10px;
    }

    .old-data-box {
        background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
        border-left: 4px solid #2196F3;
        padding: 20px;
        border-radius: 10px;
        margin-bottom: 20px;
    }

    .old-data-item {
        display: flex;
        align-items: center;
        margin-bottom: 10px;
        padding: 10px;
        background: rgba(255,255,255,0.5);
        border-radius: 8px;
    }

    .old-data-item i {
        font-size: 1.2rem;
        color: #2196F3;
        margin-right: 10px;
        width: 25px;
    }

    .old-data-value {
        font-weight: 600;
        color: #333;
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

    .badge-changed {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        color: white;
        padding: 3px 10px;
        border-radius: 10px;
        font-size: 0.75rem;
        margin-left: 10px;
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
                        <i class="ti ti-edit"></i> Edit Jadwal
                    </h2>
                    <p class="mb-0 mt-2 opacity-75">Perbarui informasi jadwal kegiatan</p>
                </div>

                <!-- Form Body -->
                <div class="form-body">
                    <!-- Warning Box -->
                    <div class="warning-box">
                        <div class="d-flex align-items-start">
                            <i class="ti ti-alert-triangle"></i>
                            <div>
                                <strong>Perhatian!</strong>
                                <p class="mb-0 mt-1">Perubahan jadwal akan mempengaruhi ketersediaan ruangan. Pastikan tidak ada bentrokan dengan jadwal lain.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Old Data Box -->
                    <div class="old-data-box">
                        <h6 class="fw-bold mb-3">
                            <i class="ti ti-history"></i> Data Jadwal Saat Ini
                        </h6>
                        <div class="old-data-item">
                            <i class="ti ti-door"></i>
                            <span>Ruangan: <span class="old-data-value">{{ $jadwal->ruangan->nama_ruangan ?? 'N/A' }}</span></span>
                        </div>
                        <div class="old-data-item">
                            <i class="ti ti-calendar"></i>
                            <span>Tanggal: <span class="old-data-value">{{ Carbon\Carbon::parse($jadwal->tanggal)->translatedFormat('d F Y') }}</span></span>
                        </div>
                        <div class="old-data-item">
                            <i class="ti ti-clipboard-text"></i>
                            <span>Kegiatan: <span class="old-data-value">{{ $jadwal->kegiatan }}</span></span>
                        </div>
                        <div class="old-data-item">
                            <i class="ti ti-clock"></i>
                            <span>Waktu: <span class="old-data-value">{{ substr($jadwal->waktu_mulai, 0, 5) }} - {{ substr($jadwal->waktu_selesai, 0, 5) }}</span></span>
                        </div>
                    </div>

                    <form action="{{ route('backend.jadwal.update', $jadwal->id) }}" method="POST" id="formJadwal">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <!-- Ruangan -->
                            <div class="col-md-12 mb-4">
                                <label for="ruang_id" class="form-label">
                                    <i class="ti ti-door"></i> Ruangan <span class="required">*</span>
                                    @if(old('ruang_id', $jadwal->ruang_id) != $jadwal->ruang_id)
                                        <span class="badge-changed">Diubah</span>
                                    @endif
                                </label>
                                <select name="ruang_id" id="ruang_id" 
                                        class="form-select @error('ruang_id') is-invalid @enderror" 
                                        required>
                                    <option value="">-- Pilih Ruangan --</option>
                                    @foreach($ruangans as $ruangan)
                                        <option value="{{ $ruangan->id }}" 
                                                {{ old('ruang_id', $jadwal->ruang_id) == $ruangan->id ? 'selected' : '' }}
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
                                    @if(old('tanggal', $jadwal->tanggal->format('Y-m-d')) != $jadwal->tanggal->format('Y-m-d'))
                                        <span class="badge-changed">Diubah</span>
                                    @endif
                                </label>
                                <input type="date" name="tanggal" id="tanggal" 
                                       class="form-control @error('tanggal') is-invalid @enderror" 
                                       value="{{ old('tanggal', $jadwal->tanggal->format('Y-m-d')) }}"
                                       required>
                                @error('tanggal')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Kegiatan -->
                            <div class="col-md-6 mb-4">
                                <label for="kegiatan" class="form-label">
                                    <i class="ti ti-clipboard-text"></i> Nama Kegiatan <span class="required">*</span>
                                    @if(old('kegiatan', $jadwal->kegiatan) != $jadwal->kegiatan)
                                        <span class="badge-changed">Diubah</span>
                                    @endif
                                </label>
                                <input type="text" name="kegiatan" id="kegiatan" 
                                       class="form-control @error('kegiatan') is-invalid @enderror" 
                                       value="{{ old('kegiatan', $jadwal->kegiatan) }}"
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
                                    @if(old('waktu_mulai', substr($jadwal->waktu_mulai, 0, 5)) != substr($jadwal->waktu_mulai, 0, 5) || 
                                        old('waktu_selesai', substr($jadwal->waktu_selesai, 0, 5)) != substr($jadwal->waktu_selesai, 0, 5))
                                        <span class="badge-changed">Diubah</span>
                                    @endif
                                </label>
                                <div class="time-picker-group">
                                    <div class="time-picker-wrapper">
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="ti ti-clock"></i>
                                            </span>
                                            <input type="time" name="waktu_mulai" id="waktu_mulai" 
                                                   class="form-control @error('waktu_mulai') is-invalid @enderror" 
                                                   value="{{ old('waktu_mulai', substr($jadwal->waktu_mulai, 0, 5)) }}"
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
                                                   value="{{ old('waktu_selesai', substr($jadwal->waktu_selesai, 0, 5)) }}"
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
                        <div class="preview-box" id="previewBox">
                            <h6 class="fw-bold mb-3">
                                <i class="ti ti-eye"></i> Preview Perubahan
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
                                <i class="ti ti-check"></i> Update Jadwal
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
    // Original data for comparison
    const originalData = {
        ruang_id: '{{ $jadwal->ruang_id }}',
        tanggal: '{{ $jadwal->tanggal->format('Y-m-d') }}',
        kegiatan: '{{ $jadwal->kegiatan }}',
        waktu_mulai: '{{ substr($jadwal->waktu_mulai, 0, 5) }}',
        waktu_selesai: '{{ substr($jadwal->waktu_selesai, 0, 5) }}'
    };

    // Update preview when form changes
    function updatePreview() {
        const ruangSelect = $('#ruang_id option:selected');
        const tanggal = $('#tanggal').val();
        const kegiatan = $('#kegiatan').val();
        const waktuMulai = $('#waktu_mulai').val();
        const waktuSelesai = $('#waktu_selesai').val();

        $('#previewRuangan').text(ruangSelect.text().trim());
        $('#previewTanggal').text(formatDate(tanggal));
        $('#previewKegiatan').text(kegiatan);
        $('#previewWaktu').text(waktuMulai + ' - ' + waktuSelesai);

        // Highlight changed fields
        highlightChanges();
    }

    // Highlight changed fields
    function highlightChanges() {
        const currentData = {
            ruang_id: $('#ruang_id').val(),
            tanggal: $('#tanggal').val(),
            kegiatan: $('#kegiatan').val(),
            waktu_mulai: $('#waktu_mulai').val(),
            waktu_selesai: $('#waktu_selesai').val()
        };

        // Check each field
        for (let key in currentData) {
            if (currentData[key] !== originalData[key]) {
                $(`#${key}`).css('border-color', '#f5576c');
            } else {
                $(`#${key}`).css('border-color', '#e0e0e0');
            }
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

    // Initial setup
    updatePreview();
    calculateDuration();
    $('#ruang_id').trigger('change');
});
</script>
@endpush