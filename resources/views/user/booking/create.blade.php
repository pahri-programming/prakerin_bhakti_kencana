@extends('layouts.frontend')
@section('title', 'Ajukan Booking Ruangan')

@push('styles')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
    body { font-family: 'Plus Jakarta Sans', sans-serif; }

    .booking-wrap { max-width: 680px; margin: 0 auto; padding: 2.5rem 1rem; }

    /* Breadcrumb */
    .back-link {
        display: inline-flex; align-items: center; gap: 6px;
        font-size: 13px; color: #6b7280; text-decoration: none;
        margin-bottom: 1.5rem; transition: color .15s;
    }
    .back-link:hover { color: #f97316; }

    /* Page header */
    .page-title { font-size: 22px; font-weight: 700; color: #111827; margin-bottom: 4px; }
    .page-sub   { font-size: 13px; color: #6b7280; }

    /* Progress bar */
    .progress-track {
        height: 4px; background: #f3f4f6; border-radius: 4px;
        margin: 1.5rem 0; overflow: hidden;
    }
    .progress-fill {
        height: 100%; background: linear-gradient(90deg, #f97316, #fb923c);
        border-radius: 4px; width: 0%; transition: width .4s ease;
    }

    /* Alert */
    .alert-custom {
        display: none; align-items: flex-start; gap: 10px;
        background: #fff1f0; border: 1px solid #fca5a5;
        border-radius: 10px; padding: .75rem 1rem;
        font-size: 13px; color: #dc2626; margin-bottom: 1.25rem;
    }
    .alert-custom.show { display: flex; }

    /* Section card */
    .section-card {
        background: #fff; border: 1px solid #e5e7eb;
        border-radius: 14px; margin-bottom: 1.25rem; overflow: hidden;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
    }
    .section-head {
        padding: 1rem 1.25rem;
        border-bottom: 1px solid #f3f4f6;
        display: flex; align-items: center; gap: 10px;
    }
    .section-icon {
        width: 36px; height: 36px; border-radius: 9px;
        display: flex; align-items: center; justify-content: center; flex-shrink: 0;
    }
    .icon-purple { background: #f5f3ff; }
    .icon-blue   { background: #eff6ff; }
    .icon-green  { background: #f0fdf4; }
    .section-title { font-size: 14px; font-weight: 600; color: #111827; }
    .section-desc  { font-size: 12px; color: #9ca3af; margin-top: 1px; }
    .section-body  { padding: 1.25rem; }

    /* Form fields */
    .field-group  { display: flex; flex-direction: column; gap: 5px; }
    .field-label  { font-size: 12px; font-weight: 600; color: #374151; }
    .field-req    { color: #f97316; }
    .field-select, .field-input {
        width: 100%; border: 1px solid #e5e7eb; border-radius: 8px;
        padding: 9px 11px; font-size: 13px; color: #111827;
        background: #fff; font-family: inherit; outline: none;
        transition: border-color .15s, box-shadow .15s;
        appearance: none; -webkit-appearance: none;
    }
    .field-select:focus, .field-input:focus {
        border-color: #f97316;
        box-shadow: 0 0 0 3px rgba(249, 115, 22, .12);
    }
    .field-hint { font-size: 11px; color: #9ca3af; margin-top: 3px; min-height: 16px; }
    .field-hint.ok  { color: #16a34a; }
    .field-hint.err { color: #dc2626; }

    /* Ruangan card grid */
    .ruangan-grid {
        display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
        gap: 10px; margin-top: 4px;
    }
    .ruangan-card {
        border: 1.5px solid #e5e7eb; border-radius: 10px;
        padding: .875rem 1rem; cursor: pointer;
        transition: all .15s; position: relative;
        background: #fff;
    }
    .ruangan-card:hover { border-color: #f97316; background: #fffbf7; }
    .ruangan-card.selected {
        border-color: #f97316; background: #fff7ed;
        box-shadow: 0 0 0 3px rgba(249,115,22,.1);
    }
    .ruangan-card input[type="radio"] {
        position: absolute; opacity: 0; pointer-events: none;
    }
    .ruangan-name { font-size: 13px; font-weight: 600; color: #111827; margin-bottom: 4px; }
    .ruangan-meta { font-size: 11px; color: #9ca3af; display: flex; align-items: center; gap: 4px; }
    .ruangan-check {
        position: absolute; top: 8px; right: 8px;
        width: 18px; height: 18px; border-radius: 50%;
        background: #e5e7eb; border: 1.5px solid #e5e7eb;
        display: flex; align-items: center; justify-content: center;
        transition: all .15s;
    }
    .ruangan-card.selected .ruangan-check {
        background: #f97316; border-color: #f97316;
    }
    .ruangan-card.selected .ruangan-check svg { display: block; }
    .ruangan-check svg { display: none; }

    /* Time grid */
    .time-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }

    /* Durasi chip */
    .durasi-chip {
        display: none; align-items: center; gap: 6px;
        border-radius: 20px; padding: 5px 14px;
        font-size: 12px; font-weight: 600; margin-top: .875rem;
        width: fit-content;
    }
    .durasi-chip.show { display: inline-flex; }
    .durasi-chip.ok   { background: #eff6ff; color: #1d4ed8; }
    .durasi-chip.warn { background: #fff1f0; color: #dc2626; }

    /* Timeline visual */
    .time-visual {
        display: none; margin-top: 1rem;
        background: #f9fafb; border: 1px solid #f3f4f6;
        border-radius: 10px; padding: .875rem 1rem;
    }
    .time-visual.show { display: block; }
    .timeline-bar {
        height: 6px; background: #e5e7eb; border-radius: 6px;
        margin: 8px 0; position: relative; overflow: hidden;
    }
    .timeline-fill {
        height: 100%; background: linear-gradient(90deg, #f97316, #fb923c);
        border-radius: 6px; transition: all .3s ease;
        position: absolute; left: 0;
    }
    .timeline-labels {
        display: flex; justify-content: space-between;
        font-size: 11px; color: #9ca3af;
    }
    .timeline-labels span { font-weight: 600; color: #374151; }

    /* Textarea */
    textarea.field-input { resize: vertical; min-height: 88px; line-height: 1.5; }
    .char-counter { font-size: 11px; color: #9ca3af; text-align: right; margin-top: 4px; }

    /* Action bar */
    .action-bar {
        display: flex; justify-content: space-between; align-items: center;
        margin-top: 1.5rem; padding-top: 1.25rem;
        border-top: 1px solid #f3f4f6;
    }
    .btn-batal {
        border: 1px solid #e5e7eb; background: #fff;
        border-radius: 8px; padding: 9px 22px;
        font-size: 14px; color: #6b7280; cursor: pointer;
        font-family: inherit; transition: all .15s;
        text-decoration: none; display: inline-flex; align-items: center; gap: 6px;
    }
    .btn-batal:hover { background: #f9fafb; border-color: #d1d5db; color: #374151; }
    .btn-submit {
        background: #f97316; color: #fff; border: none;
        border-radius: 8px; padding: 10px 28px;
        font-size: 14px; font-weight: 700; cursor: pointer;
        display: inline-flex; align-items: center; gap: 8px;
        font-family: inherit; transition: background .15s, transform .1s;
    }
    .btn-submit:hover  { background: #ea6c0c; }
    .btn-submit:active { transform: scale(.98); }
    .btn-submit:disabled { background: #fed7aa; cursor: not-allowed; transform: none; }
    .btn-submit .spinner {
        display: none; width: 16px; height: 16px;
        border: 2px solid rgba(255,255,255,.3); border-top-color: #fff;
        border-radius: 50%; animation: spin .7s linear infinite;
    }
    .btn-submit.loading .spinner  { display: inline-block; }
    .btn-submit.loading .btn-icon { display: none; }
    @keyframes spin { to { transform: rotate(360deg); } }

    @media (max-width: 520px) {
        .time-grid  { grid-template-columns: 1fr; }
        .ruangan-grid { grid-template-columns: 1fr 1fr; }
        .page-title { font-size: 18px; }
    }
</style>
@endpush

@section('content')
<div class="booking-wrap">

    {{-- Back link --}}
    <a href="{{ route('user.booking.index') }}" class="back-link">
        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M10 12L6 8l4-4"/>
        </svg>
        Kembali ke Booking Saya
    </a>

    {{-- Page header --}}
    <h1 class="page-title">Ajukan Booking Ruangan</h1>
    <p class="page-sub">Isi form di bawah untuk mengajukan booking ruangan</p>

    {{-- Progress bar --}}
    <div class="progress-track">
        <div class="progress-fill" id="progressFill"></div>
    </div>

    {{-- Alert Laravel --}}
    @if ($errors->any())
        <div class="alert-custom show">
            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" style="flex-shrink:0;margin-top:1px">
                <circle cx="8" cy="8" r="7"/><path d="M8 5v3.5M8 10.5v.5"/>
            </svg>
            <div>
                <strong>Terjadi kesalahan:</strong>
                <ul style="margin:4px 0 0;padding-left:16px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    {{-- Alert JS --}}
    <div class="alert-custom" id="jsAlert">
        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" style="flex-shrink:0;margin-top:1px">
            <circle cx="8" cy="8" r="7"/><path d="M8 5v3.5M8 10.5v.5"/>
        </svg>
        <span id="jsAlertText"></span>
    </div>

    <form action="{{ route('user.booking.store') }}" method="POST" id="formBooking">
        @csrf

        {{-- ── SECTION 1: RUANGAN ─────────────────────────────────── --}}
        <div class="section-card">
            <div class="section-head">
                <div class="section-icon icon-purple">
                    <svg width="18" height="18" viewBox="0 0 18 18" fill="none" stroke="#7c3aed" stroke-width="1.6">
                        <rect x="2" y="3" width="14" height="13" rx="2"/>
                        <path d="M7 16V10h4v6M2 8h14"/>
                    </svg>
                </div>
                <div>
                    <div class="section-title">Pilih Ruangan</div>
                    <div class="section-desc">Pilih ruangan yang ingin di-booking</div>
                </div>
            </div>
            <div class="section-body">
                {{-- Hidden input untuk form submit --}}
                <input type="hidden" name="ruang_id" id="ruang_id" value="{{ old('ruang_id') }}" required>

                <div class="ruangan-grid" id="ruanganGrid">
                    @foreach ($ruangan as $r)
                        <label class="ruangan-card {{ old('ruang_id') == $r->id ? 'selected' : '' }}"
                            onclick="selectRuangan({{ $r->id }}, this)">
                            <input type="radio" name="_ruang_radio" value="{{ $r->id }}"
                                {{ old('ruang_id') == $r->id ? 'checked' : '' }}>
                            <div class="ruangan-check">
                                <svg width="10" height="10" viewBox="0 0 10 10" fill="none" stroke="#fff" stroke-width="2">
                                    <path d="M1.5 5l2.5 2.5 4.5-4.5"/>
                                </svg>
                            </div>
                            <div class="ruangan-name">{{ $r->nama_ruangan }}</div>
                            <div class="ruangan-meta">
                                <svg width="11" height="11" viewBox="0 0 11 11" fill="none" stroke="currentColor" stroke-width="1.4">
                                    <path d="M5.5 1C3.6 1 2 2.6 2 4.5c0 2.6 3.5 5.5 3.5 5.5s3.5-2.9 3.5-5.5C9 2.6 7.4 1 5.5 1z"/>
                                    <circle cx="5.5" cy="4.5" r="1.2"/>
                                </svg>
                                {{ $r->lokasi ?? 'Lokasi tidak tersedia' }}
                                @if ($r->kapasitas)
                                    &nbsp;·&nbsp;{{ $r->kapasitas }} orang
                                @endif
                            </div>
                        </label>
                    @endforeach
                </div>

                @error('ruang_id')
                    <div class="field-hint err" style="margin-top:8px">{{ $message }}</div>
                @enderror
            </div>
        </div>

        {{-- ── SECTION 2: JADWAL ──────────────────────────────────── --}}
        <div class="section-card">
            <div class="section-head">
                <div class="section-icon icon-blue">
                    <svg width="18" height="18" viewBox="0 0 18 18" fill="none" stroke="#3b82f6" stroke-width="1.6">
                        <rect x="2" y="3" width="14" height="13" rx="2"/>
                        <path d="M5.5 3V1.5M12.5 3V1.5M2 7.5h14"/>
                    </svg>
                </div>
                <div>
                    <div class="section-title">Jadwal Booking</div>
                    <div class="section-desc">Tentukan tanggal dan jam booking</div>
                </div>
            </div>
            <div class="section-body">

                {{-- Tanggal --}}
                <div class="field-group" style="margin-bottom:1rem">
                    <label class="field-label">Tanggal <span class="field-req">*</span></label>
                    <input type="date" name="tanggal" id="tanggal"
                        class="field-input @error('tanggal') is-invalid @enderror"
                        value="{{ old('tanggal', date('Y-m-d')) }}"
                        min="{{ date('Y-m-d') }}" required
                        onchange="updateProgress()">
                    @error('tanggal')
                        <div class="field-hint err">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Waktu --}}
                <div class="time-grid">
                    <div class="field-group">
                        <label class="field-label">Waktu Mulai <span class="field-req">*</span></label>
                        <input type="time" name="waktu_mulai" id="waktuMulai"
                            class="field-input @error('waktu_mulai') is-invalid @enderror"
                            value="{{ old('waktu_mulai') }}" required
                            onchange="hitungDurasi()">
                        @error('waktu_mulai')
                            <div class="field-hint err">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="field-group">
                        <label class="field-label">Waktu Selesai <span class="field-req">*</span></label>
                        <input type="time" name="waktu_selesai" id="waktuSelesai"
                            class="field-input @error('waktu_selesai') is-invalid @enderror"
                            value="{{ old('waktu_selesai') }}" required
                            onchange="hitungDurasi()">
                        @error('waktu_selesai')
                            <div class="field-hint err">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- Durasi chip --}}
                <div class="durasi-chip" id="durasiChip">
                    <svg width="13" height="13" viewBox="0 0 13 13" fill="none" stroke="currentColor" stroke-width="1.6">
                        <circle cx="6.5" cy="6.5" r="5.5"/><path d="M6.5 3.5v3l2 1.2"/>
                    </svg>
                    <span id="durasiText"></span>
                </div>

                {{-- Timeline visual --}}
                <div class="time-visual" id="timeVisual">
                    <div class="timeline-labels">
                        <span id="tlMulai">--:--</span>
                        <span id="tlSelesai">--:--</span>
                    </div>
                    <div class="timeline-bar">
                        <div class="timeline-fill" id="tlFill" style="width:0%"></div>
                    </div>
                    <div class="timeline-labels" style="justify-content:center;margin-top:2px">
                        <span style="font-weight:400;color:#9ca3af" id="tlDurasi"></span>
                    </div>
                </div>

            </div>
        </div>

        {{-- ── SECTION 3: KETERANGAN ──────────────────────────────── --}}
        <div class="section-card">
            <div class="section-head">
                <div class="section-icon icon-green">
                    <svg width="18" height="18" viewBox="0 0 18 18" fill="none" stroke="#16a34a" stroke-width="1.6">
                        <path d="M3 4h12v11a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V4zM6 4V3a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v1"/>
                        <path d="M6 8h6M6 11h4"/>
                    </svg>
                </div>
                <div>
                    <div class="section-title">Keterangan</div>
                    <div class="section-desc">Opsional — tuliskan keperluan booking</div>
                </div>
            </div>
            <div class="section-body">
                <textarea name="keterangan" id="keteranganInput"
                    class="field-input @error('keterangan') is-invalid @enderror"
                    placeholder="Contoh: Rapat jurusan, Ujian praktik, Seminar..."
                    maxlength="500"
                    oninput="updateCharCount(); updateProgress()">{{ old('keterangan') }}</textarea>
                <div class="char-counter"><span id="charCount">0</span>/500</div>
                @error('keterangan')
                    <div class="field-hint err">{{ $message }}</div>
                @enderror
            </div>
        </div>

        {{-- ── ACTION BAR ─────────────────────────────────────────── --}}
        <div class="action-bar">
            <a href="{{ url()->previous() }}" class="btn-batal">
                <svg width="14" height="14" viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M10.5 3.5l-7 7M3.5 3.5l7 7"/>
                </svg>
                Batal
            </a>
            <button type="submit" class="btn-submit" id="btnSubmit">
                <span class="spinner"></span>
                <svg class="btn-icon" width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M2 8l4 4 8-8"/>
                </svg>
                Ajukan Booking
            </button>
        </div>

    </form>
</div>
@endsection

@push('scripts')
<script>
// ─── Select ruangan (card style) ──────────────────────────────────────────────
function selectRuangan(id, el) {
    document.querySelectorAll('.ruangan-card').forEach(c => c.classList.remove('selected'));
    el.classList.add('selected');
    document.getElementById('ruang_id').value = id;
    updateProgress();
}

// ─── Hitung durasi ────────────────────────────────────────────────────────────
function hitungDurasi() {
    const mulai   = document.getElementById('waktuMulai').value;
    const selesai = document.getElementById('waktuSelesai').value;
    const chip    = document.getElementById('durasiChip');
    const visual  = document.getElementById('timeVisual');

    if (!mulai || !selesai) {
        chip.className  = 'durasi-chip';
        visual.className = 'time-visual';
        updateProgress();
        return;
    }

    const [mH, mM] = mulai.split(':').map(Number);
    const [sH, sM] = selesai.split(':').map(Number);
    const diffMin  = (sH * 60 + sM) - (mH * 60 + mM);

    if (diffMin <= 0) {
        document.getElementById('durasiText').textContent = 'Waktu selesai harus lebih dari waktu mulai';
        chip.className  = 'durasi-chip show warn';
        visual.className = 'time-visual';
        updateProgress();
        return;
    }

    const jam   = Math.floor(diffMin / 60);
    const menit = diffMin % 60;
    const label = jam > 0
        ? `Durasi: ${jam} jam${menit > 0 ? ' ' + menit + ' menit' : ''}`
        : `Durasi: ${menit} menit`;

    document.getElementById('durasiText').textContent = label;
    chip.className = 'durasi-chip show ok';

    // Timeline visual
    const startMinutes = mH * 60 + mM;
    const endMinutes   = sH * 60 + sM;
    const totalDay     = 24 * 60;
    const leftPct      = (startMinutes / totalDay) * 100;
    const widthPct     = (diffMin / totalDay) * 100;

    document.getElementById('tlMulai').textContent   = mulai;
    document.getElementById('tlSelesai').textContent = selesai;
    document.getElementById('tlFill').style.left     = leftPct + '%';
    document.getElementById('tlFill').style.width    = widthPct + '%';
    document.getElementById('tlDurasi').textContent  = jam > 0
        ? `${jam} jam${menit > 0 ? ' ' + menit + ' menit' : ''}`
        : `${menit} menit`;
    visual.className = 'time-visual show';

    updateProgress();
}

// ─── Progress bar ─────────────────────────────────────────────────────────────
function updateProgress() {
    const hasRuangan = !!document.getElementById('ruang_id').value;
    const hasTanggal = !!document.getElementById('tanggal').value;
    const hasWaktu   = !!(document.getElementById('waktuMulai').value && document.getElementById('waktuSelesai').value);
    const hasKet     = document.getElementById('keteranganInput').value.length > 0;

    let filled = 0;
    if (hasRuangan) filled++;
    if (hasTanggal) filled++;
    if (hasWaktu)   filled++;
    if (hasKet)     filled++;

    document.getElementById('progressFill').style.width = Math.round((filled / 4) * 100) + '%';
}

// ─── Char counter ─────────────────────────────────────────────────────────────
function updateCharCount() {
    document.getElementById('charCount').textContent =
        document.getElementById('keteranganInput').value.length;
}

// ─── Alert JS ─────────────────────────────────────────────────────────────────
function showAlert(msg) {
    const el = document.getElementById('jsAlert');
    document.getElementById('jsAlertText').textContent = msg;
    el.classList.add('show');
    el.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    setTimeout(() => el.classList.remove('show'), 4500);
}

// ─── Submit handler ───────────────────────────────────────────────────────────
document.getElementById('formBooking').addEventListener('submit', function (e) {
    if (!document.getElementById('ruang_id').value) {
        e.preventDefault();
        showAlert('Pilih ruangan terlebih dahulu.');
        return;
    }
    if (!document.getElementById('tanggal').value) {
        e.preventDefault();
        showAlert('Tanggal booking wajib diisi.');
        return;
    }
    const mulai   = document.getElementById('waktuMulai').value;
    const selesai = document.getElementById('waktuSelesai').value;
    if (!mulai || !selesai) {
        e.preventDefault();
        showAlert('Waktu mulai dan waktu selesai wajib diisi.');
        return;
    }
    const [mH, mM] = mulai.split(':').map(Number);
    const [sH, sM] = selesai.split(':').map(Number);
    if ((sH * 60 + sM) <= (mH * 60 + mM)) {
        e.preventDefault();
        showAlert('Waktu selesai harus lebih dari waktu mulai.');
        return;
    }

    const btn = document.getElementById('btnSubmit');
    btn.disabled = true;
    btn.classList.add('loading');
});

// ─── Init ─────────────────────────────────────────────────────────────────────
updateCharCount();
updateProgress();
</script>
@endpush