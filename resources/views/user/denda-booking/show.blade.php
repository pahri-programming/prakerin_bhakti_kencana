@extends('layouts.frontend')
@section('title', 'Detail Denda Booking')

@push('styles')
<style>
    body { background: #f0f4f8; }
    .page-wrapper { min-height: 100vh; padding: 2.5rem 0 4rem; }
    .page-hero {
        background: linear-gradient(135deg, #1a3a8f 0%, #0d2561 100%);
        border-radius: 16px; padding: 2rem 2.5rem;
        color: #fff; margin-bottom: 2rem;
        position: relative; overflow: hidden;
    }
    .page-hero h4 { font-size: 1.4rem; font-weight: 700; margin: 0 0 .25rem; }
    .page-hero p  { margin: 0; opacity: .75; font-size: .875rem; }
    .page-hero .back-link {
        color: rgba(255,255,255,.75); text-decoration: none;
        font-size: .875rem; display: inline-flex; align-items: center;
        gap: 6px; margin-bottom: .75rem;
    }
    .info-card { background: #fff; border-radius: 14px; box-shadow: 0 2px 12px rgba(0,0,0,.07); margin-bottom: 1.5rem; overflow: hidden; }
    .info-card .card-head { padding: 1rem 1.5rem; border-bottom: 1px solid #f0f0f0; display: flex; align-items: center; gap: 10px; }
    .info-card .card-head-icon { width: 34px; height: 34px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: .95rem; }
    .info-card .card-head-icon.red    { background: #fdecea; color: #c0392b; }
    .info-card .card-head-icon.blue   { background: #eef4fd; color: #2980b9; }
    .info-card .card-head-icon.green  { background: #eafaf1; color: #1e8449; }
    .info-card .card-head-icon.orange { background: #fef9e7; color: #d35400; }
    .info-card .card-head h6 { font-weight: 700; margin: 0; font-size: .95rem; color: #1e293b; }
    .info-card .card-body-inner { padding: 1.5rem; }
    .info-row { display: flex; justify-content: space-between; padding: .65rem 0; border-bottom: 1px solid #f8fafc; font-size: .875rem; }
    .info-row:last-child { border-bottom: none; }
    .info-row .lbl { color: #64748b; font-weight: 500; }
    .info-row .val { color: #1e293b; font-weight: 700; text-align: right; }
    .jumlah-besar { text-align: center; padding: 1.25rem; background: #fdecea; border-radius: 12px; margin-bottom: 1rem; }
    .jumlah-besar .label  { font-size: .8rem; color: #c0392b; font-weight: 600; text-transform: uppercase; }
    .jumlah-besar .amount { font-size: 2rem; font-weight: 800; color: #c0392b; }
    .jumlah-besar.lunas { background: #eafaf1; }
    .jumlah-besar.lunas .label, .jumlah-besar.lunas .amount { color: #1e8449; }
    .badge-status { display: inline-flex; align-items: center; gap: 5px; padding: 6px 14px; border-radius: 20px; font-size: .8rem; font-weight: 700; }
    .badge-status.belum_bayar         { background: #fdecea; color: #c0392b; }
    .badge-status.menunggu_verifikasi { background: #fef9e7; color: #d35400; }
    .badge-status.sudah_bayar         { background: #eafaf1; color: #1e8449; }
    .badge-status.dibebaskan          { background: #f2f3f4; color: #7f8c8d; }
    .upload-zone { border: 2px dashed #e2e8f0; border-radius: 12px; padding: 2rem; text-align: center; cursor: pointer; transition: all .25s; background: #f8fafc; }
    .upload-zone:hover, .upload-zone.dragover { border-color: #3b82f6; background: #eef4fd; }
    .upload-zone .icon { font-size: 2.5rem; color: #94a3b8; margin-bottom: .5rem; }
    .upload-zone p { margin: 0; color: #64748b; font-size: .875rem; }
    .bukti-preview { border: 1px solid #e2e8f0; border-radius: 10px; overflow: hidden; margin-top: 1rem; }
    .bukti-preview img { width: 100%; max-height: 280px; object-fit: contain; background: #f8fafc; }
    .timeline { position: relative; padding-left: 1.75rem; }
    .timeline::before { content: ''; position: absolute; left: 7px; top: 0; bottom: 0; width: 2px; background: #e2e8f0; }
    .tl-item { position: relative; margin-bottom: 1.25rem; }
    .tl-item::before { content: ''; position: absolute; left: -1.45rem; top: .3rem; width: 11px; height: 11px; border-radius: 50%; background: #3b82f6; border: 2px solid #fff; box-shadow: 0 0 0 2px #3b82f6; }
    .tl-item .tl-title { font-weight: 700; font-size: .875rem; color: #1e293b; }
    .tl-item .tl-sub   { font-size: .8rem; color: #64748b; margin-top: 2px; }
</style>
@endpush

@section('content')
<div class="page-wrapper">
<div class="container">
<div class="row justify-content-center">
<div class="col-xl-9 col-lg-10">

    <div class="page-hero">
        <a href="{{ route('user.denda-booking.index') }}" class="back-link">
            <i class="fas fa-arrow-left"></i> Kembali ke Tagihan
        </a>
        <h4><i class="fas fa-building me-2"></i>Detail Denda Booking</h4>
        <p>{{ $denda->booking->ruangan->nama_ruangan ?? '-' }} —
           {{ $denda->booking->tanggal ? \Carbon\Carbon::parse($denda->booking->tanggal)->format('d M Y') : '-' }}
        </p>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4 rounded-3">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show mb-4 rounded-3">
            <i class="fas fa-exclamation-circle me-2"></i>{{ $errors->first() }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-4">

        {{-- Kolom Kiri --}}
        <div class="col-lg-7">

            {{-- Jumlah Denda --}}
            <div class="jumlah-besar {{ in_array($denda->status_pembayaran, ['sudah_bayar','dibebaskan']) ? 'lunas' : '' }}">
                <div class="label">Total Denda</div>
                <div class="amount">Rp {{ number_format($denda->jumlah_denda, 0, ',', '.') }}</div>
                <div class="mt-2">
                    <span class="badge-status {{ $denda->status_pembayaran }}">
                        @if($denda->status_pembayaran === 'belum_bayar')          <i class="fas fa-exclamation-circle"></i> Belum Bayar
                        @elseif($denda->status_pembayaran === 'menunggu_verifikasi') <i class="fas fa-clock"></i> Menunggu Verifikasi Admin
                        @elseif($denda->status_pembayaran === 'sudah_bayar')      <i class="fas fa-check-circle"></i> Sudah Lunas
                        @elseif($denda->status_pembayaran === 'dibebaskan')       <i class="fas fa-times-circle"></i> Dibebaskan
                        @endif
                    </span>
                </div>
            </div>

            {{-- Info Denda --}}
            <div class="info-card">
                <div class="card-head">
                    <div class="card-head-icon red"><i class="fas fa-info-circle"></i></div>
                    <h6>Informasi Denda</h6>
                </div>
                <div class="card-body-inner">
                    <div class="info-row">
                        <span class="lbl">Ruangan</span>
                        <span class="val">{{ $denda->booking->ruangan->nama_ruangan ?? '-' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="lbl">Tanggal Booking</span>
                        <span class="val">
                            {{ $denda->booking->tanggal
                                ? \Carbon\Carbon::parse($denda->booking->tanggal)->format('d M Y')
                                : '-' }}
                        </span>
                    </div>
                    <div class="info-row">
                        <span class="lbl">Kondisi Ruangan</span>
                        <span class="val">{{ ucfirst($denda->verifikasiBooking->kondisi_ruangan ?? '-') }}</span>
                    </div>
                    <div class="info-row">
                        <span class="lbl">Tanggal Ditetapkan</span>
                        <span class="val">
                            {{ $denda->tanggal_tindakan
                                ? $denda->tanggal_tindakan->format('d M Y')
                                : '-' }}
                        </span>
                    </div>
                    <div class="info-row">
                        <span class="lbl">Ditetapkan Oleh</span>
                        <span class="val">{{ $denda->admin->name ?? 'Admin' }}</span>
                    </div>
                    @if($denda->keterangan_denda)
                    <div class="mt-3 p-3 rounded" style="background:#f8fafc; font-size:.875rem;">
                        <strong style="color:#64748b;">Keterangan:</strong>
                        <p class="mb-0 mt-1">{{ $denda->keterangan_denda }}</p>
                    </div>
                    @endif
                    @if($denda->tindakan_admin)
                    <div class="mt-2 p-3 rounded" style="background:#fff8e1; font-size:.875rem;">
                        <strong style="color:#7d5c00;">Tindakan Admin:</strong>
                        <p class="mb-0 mt-1">{{ $denda->tindakan_admin }}</p>
                    </div>
                    @endif
                </div>
            </div>

        </div>

        {{-- Kolom Kanan --}}
        <div class="col-lg-5">

            {{-- Upload Bukti --}}
            @if($denda->status_pembayaran === 'belum_bayar')
            <div class="info-card">
                <div class="card-head">
                    <div class="card-head-icon green"><i class="fas fa-upload"></i></div>
                    <h6>Upload Bukti Pembayaran</h6>
                </div>
                <div class="card-body-inner">
                    <div style="background:#fff8e1; border:1px solid #ffe082; border-radius:12px; padding:1rem 1.25rem; margin-bottom:1.25rem; font-size:.875rem; color:#7d5c00;">
                        <i class="fas fa-info-circle me-2"></i>
                        Transfer ke rekening admin, lalu upload bukti transfer di bawah ini.
                    </div>

                    <form action="{{ route('user.denda-booking.upload-bukti', $denda->id) }}"
                          method="POST" enctype="multipart/form-data" id="formBukti">
                        @csrf

                        <div class="upload-zone mb-3" id="uploadZone">
                            <input type="file" name="bukti_pembayaran" id="buktiInput"
                                   class="d-none" accept="image/jpg,image/jpeg,image/png">
                            <div id="uploadPlaceholder">
                                <div class="icon"><i class="fas fa-cloud-upload-alt"></i></div>
                                <p><strong>Klik atau tarik foto</strong> ke sini</p>
                                <p class="mt-1" style="font-size:.78rem;">JPG / PNG · maks 2 MB</p>
                            </div>
                        </div>

                        <div id="previewWrap" class="d-none bukti-preview mb-3">
                            <img id="previewImg" src="" alt="Preview">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold" style="font-size:.83rem;">
                                Tanggal Transfer <span class="text-danger">*</span>
                            </label>
                            <input type="date" name="tanggal_bayar"
                                   class="form-control @error('tanggal_bayar') is-invalid @enderror"
                                   value="{{ old('tanggal_bayar', date('Y-m-d')) }}"
                                   max="{{ date('Y-m-d') }}" required>
                            @error('tanggal_bayar')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold" style="font-size:.83rem;">Keterangan (opsional)</label>
                            <textarea name="keterangan_pembayaran" rows="2" class="form-control"
                                      style="font-size:.875rem;"
                                      placeholder="Contoh: transfer via BCA...">{{ old('keterangan_pembayaran') }}</textarea>
                        </div>

                        <button type="submit" class="btn btn-success w-100" id="btnUpload" disabled>
                            <i class="fas fa-paper-plane me-2"></i>Kirim Bukti Pembayaran
                        </button>
                    </form>
                </div>
            </div>

            {{-- Menunggu verifikasi --}}
            @elseif($denda->status_pembayaran === 'menunggu_verifikasi')
            <div class="info-card">
                <div class="card-head">
                    <div class="card-head-icon orange"><i class="fas fa-clock"></i></div>
                    <h6>Bukti Sudah Dikirim</h6>
                </div>
                <div class="card-body-inner">
                    <div class="alert alert-warning rounded-3 mb-3">
                        <i class="fas fa-hourglass-half me-2"></i>
                        Bukti sedang menunggu verifikasi admin.
                    </div>
                    @if($denda->bukti_pembayaran)
                    <div class="bukti-preview">
                        <img src="{{ Storage::url($denda->bukti_pembayaran) }}" alt="Bukti Bayar">
                    </div>
                    @endif
                    <div class="mt-3" style="font-size:.83rem; color:#64748b;">
                        <i class="fas fa-calendar me-1"></i>
                        Tanggal Transfer: <strong>
                            {{ $denda->tanggal_bayar ? $denda->tanggal_bayar->format('d M Y') : '-' }}
                        </strong>
                    </div>
                </div>
            </div>

            {{-- Sudah lunas --}}
            @elseif($denda->status_pembayaran === 'sudah_bayar')
            <div class="info-card">
                <div class="card-head">
                    <div class="card-head-icon green"><i class="fas fa-check-circle"></i></div>
                    <h6>Pembayaran Lunas</h6>
                </div>
                <div class="card-body-inner">
                    <div class="alert alert-success rounded-3 mb-3">
                        <i class="fas fa-check-circle me-2"></i>
                        Pembayaran telah terverifikasi dan dinyatakan lunas.
                    </div>
                    @if($denda->bukti_pembayaran)
                    <div class="bukti-preview">
                        <img src="{{ Storage::url($denda->bukti_pembayaran) }}" alt="Bukti Bayar">
                    </div>
                    @endif
                </div>
            </div>

            {{-- Dibebaskan --}}
            @elseif($denda->status_pembayaran === 'dibebaskan')
            <div class="info-card">
                <div class="card-head">
                    <div class="card-head-icon blue"><i class="fas fa-times-circle"></i></div>
                    <h6>Denda Dibebaskan</h6>
                </div>
                <div class="card-body-inner">
                    <div class="alert alert-secondary rounded-3">
                        <i class="fas fa-info-circle me-2"></i>
                        Denda ini telah dibebaskan oleh admin.
                    </div>
                </div>
            </div>
            @endif

            {{-- Timeline --}}
            <div class="info-card">
                <div class="card-head">
                    <div class="card-head-icon blue"><i class="fas fa-history"></i></div>
                    <h6>Riwayat Status</h6>
                </div>
                <div class="card-body-inner">
                    <div class="timeline">
                        <div class="tl-item">
                            <div class="tl-title">Denda Ditetapkan</div>
                            <div class="tl-sub">
                                {{ $denda->tanggal_tindakan ? $denda->tanggal_tindakan->format('d M Y') : '-' }}
                                — oleh {{ $denda->admin->name ?? 'Admin' }}
                            </div>
                        </div>
                        @if($denda->bukti_pembayaran)
                        <div class="tl-item">
                            <div class="tl-title">Bukti Pembayaran Dikirim</div>
                            <div class="tl-sub">
                                {{ $denda->tanggal_bayar ? $denda->tanggal_bayar->format('d M Y') : '-' }}
                            </div>
                        </div>
                        @endif
                        @if($denda->status_pembayaran === 'sudah_bayar')
                        <div class="tl-item">
                            <div class="tl-title">Pembayaran Terverifikasi</div>
                            <div class="tl-sub">Denda dinyatakan lunas</div>
                        </div>
                        @endif
                        @if($denda->status_pembayaran === 'dibebaskan')
                        <div class="tl-item">
                            <div class="tl-title">Denda Dibebaskan</div>
                            <div class="tl-sub">Tidak ada kewajiban pembayaran</div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

        </div>
    </div>

</div>
</div>
</div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs@4.2.0/dist/tf.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@tensorflow-models/blazeface@0.0.7/dist/blazeface.min.js"></script>
<script>
(function () {
    const zone    = document.getElementById('uploadZone');
    const input   = document.getElementById('buktiInput');
    const preview = document.getElementById('previewImg');
    const wrap    = document.getElementById('previewWrap');
    const holder  = document.getElementById('uploadPlaceholder');
    const btn     = document.getElementById('btnUpload');

    if (!zone) return;

    let faceModel = null;
    let fileValid = false;

    blazeface.load().then(m => { faceModel = m; }).catch(() => {});

    function showToast(msg, type = 'danger') {
        const old = document.getElementById('vToast');
        if (old) old.remove();
        const t = document.createElement('div');
        t.id = 'vToast';
        t.className = `alert alert-${type} alert-dismissible fade show`;
        t.style.cssText = 'position:fixed;top:20px;right:20px;z-index:9999;min-width:320px;box-shadow:0 4px 12px rgba(0,0,0,.15)';
        t.innerHTML = `<i class="fas fa-${type==='danger'?'times':'check'}-circle me-2"></i>${msg}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>`;
        document.body.appendChild(t);
        setTimeout(() => { if (t.parentNode) t.remove(); }, 5000);
    }

    async function handleFile(file) {
        if (!['image/jpeg','image/jpg','image/png'].includes(file.type)) {
            showToast('File harus JPG atau PNG.'); return;
        }
        if (file.size > 2 * 1024 * 1024) {
            showToast('Ukuran file maksimal 2MB.'); return;
        }

        const reader = new FileReader();
        reader.onload = async function (e) {
            preview.src = e.target.result;
            wrap.classList.remove('d-none');
            holder.classList.add('d-none');

            if (faceModel) {
                try {
                    const img = new Image();
                    img.src = e.target.result;
                    await new Promise(r => { img.onload = r; });
                    const preds = await faceModel.estimateFaces(img, false);
                    if (preds.length > 0) {
                        showToast('❌ Foto ditolak! Terdeteksi foto wajah/selfie. Upload screenshot bukti transfer.');
                        wrap.classList.add('d-none');
                        holder.classList.remove('d-none');
                        preview.src = '';
                        input.value = '';
                        fileValid = false;
                        if (btn) btn.disabled = true;
                        return;
                    }
                } catch (err) {}
            }

            fileValid = true;
            if (btn) btn.disabled = false;
            showToast('✅ Gambar valid, silakan kirim.', 'success');
        };
        reader.readAsDataURL(file);
    }

    zone.addEventListener('click', e => { if (!e.target.closest('#previewWrap')) input.click(); });
    zone.addEventListener('dragover',  e => { e.preventDefault(); zone.classList.add('dragover'); });
    zone.addEventListener('dragleave', () => zone.classList.remove('dragover'));
    zone.addEventListener('drop', e => {
        e.preventDefault(); zone.classList.remove('dragover');
        if (e.dataTransfer.files[0]) handleFile(e.dataTransfer.files[0]);
    });
    input.addEventListener('change', function () {
        if (this.files[0]) handleFile(this.files[0]);
    });

    const form = document.getElementById('formBukti');
    if (form) {
        form.addEventListener('submit', function (e) {
            if (!fileValid) { e.preventDefault(); showToast('Pilih bukti pembayaran terlebih dahulu.'); return; }
            if (btn) { btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Mengirim...'; }
        });
    }
})();
</script>
@endpush