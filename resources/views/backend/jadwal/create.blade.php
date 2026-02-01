@extends('layouts.backend')

@section('title', 'Tambah Jadwal')

@push('styles')
<style>
    .content-wrapper {
        padding: 1.5rem;
        background: #f8f9fa;
        min-height: 100vh;
    }

    .page-header {
        background: white;
        padding: 1.5rem;
        border-radius: 12px;
        margin-bottom: 1.5rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }

    .page-header h4 {
        margin: 0;
        font-weight: 700;
        color: #2d3748;
        font-size: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .page-header h4 i {
        color: #ff9800;
        background: #fff3e0;
        width: 45px;
        height: 45px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
    }

    .page-header p {
        margin: 0.5rem 0 0;
        color: #718096;
        font-size: 0.95rem;
    }

    .form-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        overflow: hidden;
    }

    .form-card-body {
        padding: 2rem;
    }

    .alert-info {
        background: #fff3e0;
        border: none;
        border-left: 4px solid #ff9800;
        border-radius: 8px;
        padding: 1rem 1.25rem;
        margin-bottom: 2rem;
        display: flex;
        gap: 1rem;
    }

    .alert-info i {
        color: #ff9800;
        font-size: 1.5rem;
        flex-shrink: 0;
    }

    .alert-info-content strong {
        display: block;
        color: #e65100;
        margin-bottom: 0.25rem;
        font-size: 0.95rem;
    }

    .alert-info-content p {
        margin: 0;
        color: #5d4037;
        font-size: 0.875rem;
        line-height: 1.5;
    }

    .form-section {
        margin-bottom: 2rem;
    }

    .form-section-title {
        font-size: 1rem;
        font-weight: 700;
        color: #2d3748;
        margin-bottom: 1.25rem;
        padding-bottom: 0.75rem;
        border-bottom: 2px solid #e2e8f0;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .form-section-title i {
        color: #ff9800;
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-group:last-child {
        margin-bottom: 0;
    }

    .form-label {
        display: block;
        font-weight: 600;
        color: #2d3748;
        margin-bottom: 0.5rem;
        font-size: 0.9rem;
    }

    .form-label .text-danger {
        color: #e74c3c;
    }

    .form-control,
    .form-select {
        width: 100%;
        padding: 0.675rem 1rem;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        font-size: 0.9rem;
        transition: all 0.2s;
        background: white;
    }

    .form-control:focus,
    .form-select:focus {
        outline: none;
        border-color: #ff9800;
        box-shadow: 0 0 0 3px rgba(255, 152, 0, 0.1);
    }

    .form-control::placeholder {
        color: #a0aec0;
    }

    .form-text {
        display: block;
        margin-top: 0.5rem;
        font-size: 0.85rem;
        color: #718096;
    }

    .time-group {
        display: grid;
        grid-template-columns: 1fr auto 1fr;
        gap: 1rem;
        align-items: end;
    }

    .time-item label {
        display: block;
        font-size: 0.85rem;
        color: #718096;
        margin-bottom: 0.5rem;
        font-weight: 500;
    }

    .time-separator {
        padding-bottom: 0.675rem;
        text-align: center;
        color: #cbd5e0;
        font-size: 1.25rem;
    }

    .duration-info {
        margin-top: 1rem;
        padding: 0.75rem 1rem;
        background: #e8f5e9;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.9rem;
    }

    .duration-info i {
        color: #4caf50;
    }

    .duration-info strong {
        color: #2e7d32;
    }

    .preview-box {
        background: #f3e5f5;
        border-radius: 12px;
        padding: 1.5rem;
        margin-top: 2rem;
        display: none;
    }

    .preview-box.show {
        display: block;
    }

    .preview-title {
        font-weight: 700;
        color: #6a1b9a;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .preview-grid {
        display: grid;
        gap: 0.75rem;
    }

    .preview-item {
        background: rgba(255,255,255,0.7);
        padding: 0.75rem 1rem;
        border-radius: 8px;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .preview-item i {
        color: #9c27b0;
        font-size: 1.1rem;
    }

    .preview-item-content {
        flex: 1;
    }

    .preview-label {
        font-size: 0.8rem;
        color: #6a1b9a;
        font-weight: 500;
    }

    .preview-value {
        font-size: 0.9rem;
        color: #4a148c;
        font-weight: 700;
    }

    .form-footer {
        padding: 1.5rem 2rem;
        background: #f8f9fa;
        border-top: 1px solid #e2e8f0;
        display: flex;
        justify-content: space-between;
        gap: 1rem;
    }

    .btn {
        padding: 0.625rem 1.75rem;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.9rem;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        border: none;
        cursor: pointer;
    }

    .btn-secondary {
        background: white;
        color: #4a5568;
        border: 1px solid #e2e8f0;
    }

    .btn-secondary:hover {
        background: #f7fafc;
        border-color: #cbd5e0;
    }

    .btn-primary {
        background: linear-gradient(135deg, #ff9800 0%, #ff5722 100%);
        color: white;
    }

    .btn-primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(255, 152, 0, 0.3);
    }

    .invalid-feedback {
        display: block;
        color: #e74c3c;
        font-size: 0.85rem;
        margin-top: 0.375rem;
    }

    .is-invalid {
        border-color: #e74c3c !important;
    }

    @media (max-width: 768px) {
        .content-wrapper {
            padding: 1rem;
        }

        .form-card-body {
            padding: 1.25rem;
        }

        .time-group {
            grid-template-columns: 1fr;
        }

        .time-separator {
            display: none;
        }

        .form-footer {
            flex-direction: column-reverse;
            padding: 1.25rem;
        }

        .btn {
            width: 100%;
            justify-content: center;
        }
    }
</style>
@endpush

@section('content')
<div class="content-wrapper">
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <h4>
                <i class="ti ti-calendar-plus"></i>
                Tambah Jadwal Baru
            </h4>
            <p>Isi formulir di bawah untuk menambahkan jadwal kegiatan baru</p>
        </div>

        <!-- Form Card -->
        <div class="form-card">
            <div class="form-card-body">
                <!-- Alert Info -->
                <div class="alert-info">
                    <i class="ti ti-info-circle"></i>
                    <div class="alert-info-content">
                        <strong>Informasi Penting</strong>
                        <p>Pastikan waktu yang dipilih tidak bentrok dengan jadwal lain di ruangan yang sama untuk menghindari konflik pemesanan.</p>
                    </div>
                </div>

                <form action="{{ route('backend.jadwal.store') }}" method="POST" id="formJadwal">
                    @csrf

                    <!-- Section: Ruangan -->
                    <div class="form-section">
                        <div class="form-section-title">
                            <i class="ti ti-building"></i>
                            Informasi Ruangan
                        </div>

                        <div class="form-group">
                            <label class="form-label">
                                Pilih Ruangan <span class="text-danger">*</span>
                            </label>
                            <select name="ruang_id" id="ruang_id" 
                                    class="form-select @error('ruang_id') is-invalid @enderror" required>
                                <option value="">-- Pilih Ruangan --</option>
                                @foreach($ruangans as $ruangan)
                                    <option value="{{ $ruangan->id }}" 
                                            {{ old('ruang_id') == $ruangan->id ? 'selected' : '' }}
                                            data-kapasitas="{{ $ruangan->kapasitas }}"
                                            data-lokasi="{{ $ruangan->lokasi }}">
                                        {{ $ruangan->nama_ruangan }} - {{ $ruangan->lokasi }} (Kapasitas: {{ $ruangan->kapasitas }} orang)
                                    </option>
                                @endforeach
                            </select>
                            @error('ruang_id')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                            <small class="form-text" id="ruanganInfo"></small>
                        </div>
                    </div>

                    <!-- Section: Detail Kegiatan -->
                    <div class="form-section">
                        <div class="form-section-title">
                            <i class="ti ti-calendar-event"></i>
                            Detail Kegiatan
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">
                                        Tanggal Kegiatan <span class="text-danger">*</span>
                                    </label>
                                    <input type="date" name="tanggal" id="tanggal" 
                                           class="form-control @error('tanggal') is-invalid @enderror" 
                                           value="{{ old('tanggal', date('Y-m-d')) }}"
                                           min="{{ date('Y-m-d') }}" required>
                                    @error('tanggal')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">
                                        Nama Kegiatan <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="kegiatan" id="kegiatan" 
                                           class="form-control @error('kegiatan') is-invalid @enderror" 
                                           value="{{ old('kegiatan') }}"
                                           placeholder="Contoh: Rapat Koordinasi Tim" maxlength="255" required>
                                    @error('kegiatan')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section: Waktu -->
                    <div class="form-section">
                        <div class="form-section-title">
                            <i class="ti ti-clock"></i>
                            Jadwal Waktu
                        </div>

                        <div class="form-group">
                            <label class="form-label">
                                Waktu Kegiatan <span class="text-danger">*</span>
                            </label>

                            <div class="time-group">
                                <div class="time-item">
                                    <label>Waktu Mulai</label>
                                    <input type="time" name="waktu_mulai" id="waktu_mulai" 
                                           class="form-control @error('waktu_mulai') is-invalid @enderror" 
                                           value="{{ old('waktu_mulai', '08:00') }}" required>
                                    @error('waktu_mulai')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="time-separator">
                                    <i class="ti ti-arrow-right"></i>
                                </div>

                                <div class="time-item">
                                    <label>Waktu Selesai</label>
                                    <input type="time" name="waktu_selesai" id="waktu_selesai" 
                                           class="form-control @error('waktu_selesai') is-invalid @enderror" 
                                           value="{{ old('waktu_selesai', '10:00') }}" required>
                                    @error('waktu_selesai')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="duration-info">
                                <i class="ti ti-hourglass"></i>
                                <span>Durasi: <strong id="durasi">-</strong></span>
                            </div>
                        </div>
                    </div>

                    <!-- Preview Box -->
                    <div class="preview-box" id="previewBox">
                        <div class="preview-title">
                            <i class="ti ti-eye"></i>
                            Preview Jadwal
                        </div>
                        <div class="preview-grid">
                            <div class="preview-item">
                                <i class="ti ti-door"></i>
                                <div class="preview-item-content">
                                    <div class="preview-label">Ruangan</div>
                                    <div class="preview-value" id="previewRuangan">-</div>
                                </div>
                            </div>
                            <div class="preview-item">
                                <i class="ti ti-calendar"></i>
                                <div class="preview-item-content">
                                    <div class="preview-label">Tanggal</div>
                                    <div class="preview-value" id="previewTanggal">-</div>
                                </div>
                            </div>
                            <div class="preview-item">
                                <i class="ti ti-clipboard-text"></i>
                                <div class="preview-item-content">
                                    <div class="preview-label">Kegiatan</div>
                                    <div class="preview-value" id="previewKegiatan">-</div>
                                </div>
                            </div>
                            <div class="preview-item">
                                <i class="ti ti-clock"></i>
                                <div class="preview-item-content">
                                    <div class="preview-label">Waktu</div>
                                    <div class="preview-value" id="previewWaktu">-</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Form Footer -->
            <div class="form-footer">
                <a href="{{ route('backend.jadwal.index') }}" class="btn btn-secondary">
                    <i class="ti ti-arrow-left"></i>
                    Kembali
                </a>
                <button type="submit" form="formJadwal" class="btn btn-primary">
                    <i class="ti ti-check"></i>
                    Simpan Jadwal
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    function updatePreview() {
        const ruangSelect = $('#ruang_id option:selected');
        const tanggal = $('#tanggal').val();
        const kegiatan = $('#kegiatan').val();
        const waktuMulai = $('#waktu_mulai').val();
        const waktuSelesai = $('#waktu_selesai').val();

        if (ruangSelect.val() && tanggal && kegiatan && waktuMulai && waktuSelesai) {
            $('#previewBox').addClass('show');
            $('#previewRuangan').text(ruangSelect.text().split('-')[0].trim());
            $('#previewTanggal').text(formatDate(tanggal));
            $('#previewKegiatan').text(kegiatan);
            $('#previewWaktu').text(waktuMulai + ' - ' + waktuSelesai);
        } else {
            $('#previewBox').removeClass('show');
        }
    }

    function calculateDuration() {
        const waktuMulai = $('#waktu_mulai').val();
        const waktuSelesai = $('#waktu_selesai').val();

        if (waktuMulai && waktuSelesai) {
            const start = new Date('2000-01-01 ' + waktuMulai);
            const end = new Date('2000-01-01 ' + waktuSelesai);
            const diff = (end - start) / 1000 / 60;

            if (diff > 0) {
                const hours = Math.floor(diff / 60);
                const minutes = diff % 60;
                let text = '';
                if (hours > 0) text += hours + ' jam ';
                if (minutes > 0) text += minutes + ' menit';
                $('#durasi').text(text || '0 menit');
            } else {
                $('#durasi').text('Waktu tidak valid!');
            }
        }
    }

    function formatDate(dateString) {
        const date = new Date(dateString);
        const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
        return date.toLocaleDateString('id-ID', options);
    }

    $('#ruang_id').on('change', function() {
        const selected = $(this).find('option:selected');
        const kapasitas = selected.data('kapasitas');
        const lokasi = selected.data('lokasi');
        
        if (kapasitas && lokasi) {
            $('#ruanganInfo').html('<i class="ti ti-info-circle"></i> Kapasitas: <strong>' + kapasitas + ' orang</strong> | Lokasi: <strong>' + lokasi + '</strong>');
        } else {
            $('#ruanganInfo').html('');
        }
        updatePreview();
    });

    $('#tanggal, #kegiatan, #waktu_mulai, #waktu_selesai').on('change input', function() {
        updatePreview();
        calculateDuration();
    });

    $('#formJadwal').on('submit', function(e) {
        if ($('#waktu_mulai').val() >= $('#waktu_selesai').val()) {
            e.preventDefault();
            alert('Waktu selesai harus lebih besar dari waktu mulai!');
            return false;
        }
    });

    calculateDuration();
});
</script>
@endpush