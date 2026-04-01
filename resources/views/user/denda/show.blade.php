@extends('layouts.frontend')
@section('title', 'Detail Denda')

@push('styles')
<style>
    body { background: #f0f4f8; }
    .page-wrapper { min-height: 100vh; padding: 2.5rem 0 4rem; }

    /* Hero */
    .page-hero {
        background: linear-gradient(135deg, #c0392b 0%, #922b21 100%);
        border-radius: 16px; padding: 2rem 2.5rem;
        color: #fff; margin-bottom: 2rem;
        position: relative; overflow: hidden;
    }
    .page-hero::after {
        content: ''; position: absolute;
        top: -40px; right: -40px; width: 180px; height: 180px;
        border-radius: 50%; background: rgba(255,255,255,.07);
    }
    .page-hero .back-link {
        color: rgba(255,255,255,.75); text-decoration: none;
        font-size: .875rem; display: inline-flex;
        align-items: center; gap: 6px; margin-bottom: .75rem;
    }
    .page-hero .back-link:hover { color: #fff; }
    .page-hero h4 { font-size: 1.4rem; font-weight: 700; margin: 0 0 .25rem; }
    .page-hero p  { margin: 0; opacity: .75; font-size: .875rem; }

    /* Cards */
    .info-card {
        background: #fff; border-radius: 14px;
        box-shadow: 0 2px 12px rgba(0,0,0,.07);
        margin-bottom: 1.5rem; overflow: hidden;
    }
    .info-card .card-head {
        padding: 1rem 1.5rem; border-bottom: 1px solid #f0f0f0;
        display: flex; align-items: center; gap: 10px;
    }
    .info-card .card-head-icon {
        width: 34px; height: 34px; border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        font-size: .95rem;
    }
    .info-card .card-head-icon.red    { background: #fdecea; color: #c0392b; }
    .info-card .card-head-icon.blue   { background: #eef4fd; color: #2980b9; }
    .info-card .card-head-icon.green  { background: #eafaf1; color: #1e8449; }
    .info-card .card-head-icon.orange { background: #fef9e7; color: #d35400; }
    .info-card .card-head h6 { font-weight: 700; margin: 0; font-size: .95rem; color: #1e293b; }
    .info-card .card-body-inner { padding: 1.5rem; }

    /* Info rows */
    .info-row {
        display: flex; justify-content: space-between;
        padding: .65rem 0; border-bottom: 1px solid #f8fafc;
        font-size: .875rem;
    }
    .info-row:last-child { border-bottom: none; }
    .info-row .lbl { color: #64748b; font-weight: 500; }
    .info-row .val { color: #1e293b; font-weight: 700; text-align: right; }

    /* Jumlah besar */
    .jumlah-besar {
        text-align: center; padding: 1.25rem;
        background: #fdecea; border-radius: 12px; margin-bottom: 1rem;
    }
    .jumlah-besar .label { font-size: .8rem; color: #c0392b; font-weight: 600; text-transform: uppercase; }
    .jumlah-besar .amount { font-size: 2rem; font-weight: 800; color: #c0392b; }
    .jumlah-besar.lunas { background: #eafaf1; }
    .jumlah-besar.lunas .label,
    .jumlah-besar.lunas .amount { color: #1e8449; }

    /* Badge status */
    .badge-status {
        display: inline-flex; align-items: center; gap: 5px;
        padding: 6px 14px; border-radius: 20px; font-size: .8rem; font-weight: 700;
    }
    .badge-status.belum_bayar         { background: #fdecea; color: #c0392b; }
    .badge-status.menunggu_verifikasi { background: #fef9e7; color: #d35400; }
    .badge-status.sudah_bayar         { background: #eafaf1; color: #1e8449; }
    .badge-status.dibebaskan          { background: #f2f3f4; color: #7f8c8d; }

    /* Rincian tabel */
    .rincian-table { width: 100%; border-collapse: collapse; font-size: .875rem; }
    .rincian-table th {
        background: #f8fafc; color: #64748b;
        font-weight: 600; padding: 10px 12px;
        border-bottom: 2px solid #e2e8f0; text-align: left;
    }
    .rincian-table td {
        padding: 10px 12px; border-bottom: 1px solid #f0f0f0; color: #1e293b;
    }
    .rincian-table tr:last-child td { border-bottom: none; }
    .rincian-table .total-row td { font-weight: 700; background: #fef9e7; }

    /* Upload zone */
    .upload-zone {
        border: 2px dashed #e2e8f0; border-radius: 12px;
        padding: 2rem; text-align: center; cursor: pointer;
        transition: all .25s; background: #f8fafc;
    }
    .upload-zone:hover, .upload-zone.dragover {
        border-color: #3b82f6; background: #eef4fd;
    }
    .upload-zone .icon { font-size: 2.5rem; color: #94a3b8; margin-bottom: .5rem; }
    .upload-zone p { margin: 0; color: #64748b; font-size: .875rem; }
    .upload-zone strong { color: #3b82f6; }

    /* Preview bukti */
    .bukti-preview {
        border: 1px solid #e2e8f0; border-radius: 10px;
        overflow: hidden; margin-top: 1rem;
    }
    .bukti-preview img { width: 100%; max-height: 280px; object-fit: contain; background: #f8fafc; }

    /* Timeline */
    .timeline { position: relative; padding-left: 1.75rem; }
    .timeline::before {
        content: ''; position: absolute; left: 7px; top: 0; bottom: 0;
        width: 2px; background: #e2e8f0;
    }
    .tl-item { position: relative; margin-bottom: 1.25rem; }
    .tl-item::before {
        content: ''; position: absolute; left: -1.45rem; top: .3rem;
        width: 11px; height: 11px; border-radius: 50%;
        background: #3b82f6; border: 2px solid #fff;
        box-shadow: 0 0 0 2px #3b82f6;
    }
    .tl-item .tl-title { font-weight: 700; font-size: .875rem; color: #1e293b; }
    .tl-item .tl-sub   { font-size: .8rem; color: #64748b; margin-top: 2px; }

    /* Alert bayar */
    .alert-bayar {
        background: #fff8e1; border: 1px solid #ffe082;
        border-radius: 12px; padding: 1rem 1.25rem;
        margin-bottom: 1.25rem; font-size: .875rem; color: #7d5c00;
    }
</style>
@endpush

@section('content')
<div class="page-wrapper">
<div class="container">
<div class="row justify-content-center">
<div class="col-xl-9 col-lg-10">

    {{-- Hero --}}
    <div class="page-hero">
        <a href="{{ route('user.denda.index') }}" class="back-link">
            <i class="fas fa-arrow-left"></i> Kembali ke Tagihan
        </a>
        <h4><i class="fas fa-file-invoice-dollar me-2"></i>Detail Denda</h4>
        <p>Kode Peminjaman: {{ $denda->pengembalianBarang->peminjamanBarang->kode ?? '-' }}</p>
    </div>

    {{-- Alert success --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4 rounded-3">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show mb-4 rounded-3">
            <i class="fas fa-exclamation-circle me-2"></i>{{ $errors->first() }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-4">

        {{-- ── Kolom Kiri ── --}}
        <div class="col-lg-7">

            {{-- Jumlah Denda --}}
            <div class="jumlah-besar {{ in_array($denda->status_pembayaran, ['sudah_bayar','dibebaskan']) ? 'lunas' : '' }}">
                <div class="label">Total Denda</div>
                <div class="amount">Rp {{ number_format($denda->jumlah_denda, 0, ',', '.') }}</div>
                <div class="mt-2">
                    <span class="badge-status {{ $denda->status_pembayaran }}">
                        @if($denda->status_pembayaran === 'belum_bayar')         <i class="fas fa-exclamation-circle"></i> Belum Bayar
                        @elseif($denda->status_pembayaran === 'menunggu_verifikasi') <i class="fas fa-clock"></i> Menunggu Verifikasi Admin
                        @elseif($denda->status_pembayaran === 'sudah_bayar')     <i class="fas fa-check-circle"></i> Sudah Lunas
                        @elseif($denda->status_pembayaran === 'dibebaskan')      <i class="fas fa-times-circle"></i> Dibebaskan
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
                        <span class="lbl">Kode Peminjaman</span>
                        <span class="val">{{ $denda->pengembalianBarang->peminjamanBarang->kode ?? '-' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="lbl">Kondisi Barang</span>
                        <span class="val">{{ ucfirst(str_replace('_',' ', $denda->verifikasiPengembalian->kondisi ?? '-')) }}</span>
                    </div>
                    <div class="info-row">
                        <span class="lbl">Tipe Perhitungan</span>
                        <span class="val">{{ ucfirst($denda->tipe_perhitungan ?? '-') }}</span>
                    </div>
                    <div class="info-row">
                        <span class="lbl">Tanggal Ditetapkan</span>
                        <span class="val">
                            {{ $denda->tanggal_tindakan ? \Carbon\Carbon::parse($denda->tanggal_tindakan)->translatedFormat('d F Y') : '-' }}
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

            {{-- Rincian Perhitungan --}}
            @if(is_array($rincian) && count($rincian) > 0)
            <div class="info-card">
                <div class="card-head">
                    <div class="card-head-icon orange"><i class="fas fa-calculator"></i></div>
                    <h6>Rincian Perhitungan</h6>
                </div>
                <div class="card-body-inner p-0">
                    <table class="rincian-table">
                        <thead>
                            <tr>
                                <th>Barang</th>
                                <th class="text-center">Jml</th>
                                <th class="text-end">Harga Satuan</th>
                                <th class="text-end">Denda ({{ $rincian[0]['persentase'] ?? 0 }}%)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($rincian as $item)
                            <tr>
                                <td>{{ $item['nama_barang'] ?? '-' }}</td>
                                <td class="text-center">{{ $item['jumlah'] ?? 1 }}</td>
                                <td class="text-end">Rp {{ number_format($item['harga_satuan'] ?? 0, 0, ',', '.') }}</td>
                                <td class="text-end">Rp {{ number_format($item['denda'] ?? 0, 0, ',', '.') }}</td>
                            </tr>
                            @endforeach
                            <tr class="total-row">
                                <td colspan="3"><strong>Total Denda</strong></td>
                                <td class="text-end"><strong>Rp {{ number_format($denda->jumlah_denda, 0, ',', '.') }}</strong></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

        </div>

        {{-- ── Kolom Kanan ── --}}
        <div class="col-lg-5">

            {{-- Upload Bukti Bayar --}}
            @if($denda->status_pembayaran === 'belum_bayar')
            <div class="info-card">
                <div class="card-head">
                    <div class="card-head-icon green"><i class="fas fa-upload"></i></div>
                    <h6>Upload Bukti Pembayaran</h6>
                </div>
                <div class="card-body-inner">
                    <div class="alert-bayar">
                        <i class="fas fa-info-circle me-2"></i>
                        Transfer ke rekening admin, lalu upload bukti transfer di bawah ini.
                    </div>

                    <form action="{{ route('user.denda.upload-bukti', $denda->id) }}"
                        method="POST" enctype="multipart/form-data" id="formBukti">
                        @csrf

                        {{-- Upload zone --}}
                        <div class="upload-zone mb-3" id="uploadZone">
                            <input type="file" name="bukti_pembayaran" id="buktiInput"
                                class="d-none" accept="image/jpg,image/jpeg,image/png">
                            <div id="uploadPlaceholder">
                                <div class="icon"><i class="fas fa-cloud-upload-alt"></i></div>
                                <p><strong>Klik atau tarik foto</strong> ke sini</p>
                                <p class="mt-1" style="font-size:.78rem;">JPG / PNG · maks 2 MB</p>
                            </div>
                        </div>

                        {{-- Preview --}}
                        <div id="previewWrap" class="d-none bukti-preview mb-3">
                            <img id="previewImg" src="" alt="Preview">
                        </div>

                        {{-- Tanggal bayar --}}
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

                        {{-- Keterangan --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold" style="font-size:.83rem;">Keterangan (opsional)</label>
                            <textarea name="keterangan_pembayaran" rows="2"
                                class="form-control" style="font-size:.875rem;"
                                placeholder="Contoh: transfer via BCA a/n Rizqi...">{{ old('keterangan_pembayaran') }}</textarea>
                        </div>

                        <button type="submit" class="btn btn-success w-100" id="btnUpload">
                            <i class="fas fa-paper-plane me-2"></i>Kirim Bukti Pembayaran
                        </button>
                    </form>
                </div>
            </div>

            {{-- Sudah upload, menunggu verifikasi --}}
            @elseif($denda->status_pembayaran === 'menunggu_verifikasi')
            <div class="info-card">
                <div class="card-head">
                    <div class="card-head-icon orange"><i class="fas fa-clock"></i></div>
                    <h6>Bukti Sudah Dikirim</h6>
                </div>
                <div class="card-body-inner">
                    <div class="alert alert-warning rounded-3 mb-3">
                        <i class="fas fa-hourglass-half me-2"></i>
                        Bukti pembayaran sedang menunggu verifikasi admin. Harap tunggu konfirmasi.
                    </div>
                    @if($denda->bukti_pembayaran)
                    <div class="bukti-preview">
                        <img src="{{ Storage::url($denda->bukti_pembayaran) }}" alt="Bukti Bayar">
                    </div>
                    @endif
                    <div class="mt-3" style="font-size:.83rem; color:#64748b;">
                        <i class="fas fa-calendar me-1"></i>
                        Tanggal Transfer: <strong>{{ $denda->tanggal_bayar ? \Carbon\Carbon::parse($denda->tanggal_bayar)->translatedFormat('d F Y') : '-' }}</strong>
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
                        Pembayaran denda telah terverifikasi dan dinyatakan lunas.
                    </div>
                    @if($denda->bukti_pembayaran)
                    <div class="bukti-preview">
                        <img src="{{ Storage::url($denda->bukti_pembayaran) }}" alt="Bukti Bayar">
                    </div>
                    @endif
                    <div class="mt-3" style="font-size:.83rem; color:#64748b;">
                        <i class="fas fa-calendar me-1"></i>
                        Tanggal Bayar: <strong>{{ $denda->tanggal_bayar ? \Carbon\Carbon::parse($denda->tanggal_bayar)->translatedFormat('d F Y') : '-' }}</strong>
                    </div>
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
                        Denda ini telah dibebaskan oleh admin. Tidak ada kewajiban pembayaran.
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
                                {{ $denda->tanggal_tindakan ? \Carbon\Carbon::parse($denda->tanggal_tindakan)->translatedFormat('d F Y') : '-' }}
                                — oleh {{ $denda->admin->name ?? 'Admin' }}
                            </div>
                        </div>

                        @if($denda->bukti_pembayaran)
                        <div class="tl-item">
                            <div class="tl-title">Bukti Pembayaran Dikirim</div>
                            <div class="tl-sub">
                                Tanggal Transfer: {{ $denda->tanggal_bayar ? \Carbon\Carbon::parse($denda->tanggal_bayar)->translatedFormat('d F Y') : '-' }}
                            </div>
                        </div>
                        @endif

                        @if($denda->status_pembayaran === 'sudah_bayar')
                        <div class="tl-item">
                            <div class="tl-title">Pembayaran Terverifikasi</div>
                            <div class="tl-sub">Denda dinyatakan lunas oleh admin</div>
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
<script>
(function () {
    const zone      = document.getElementById('uploadZone');
    const input     = document.getElementById('buktiInput');
    const preview   = document.getElementById('previewImg');
    const wrap      = document.getElementById('previewWrap');
    const holder    = document.getElementById('uploadPlaceholder');
    const btnUpload = document.getElementById('btnUpload');

    if (!zone) return;

    let faceModel = null;
    let fileValid = false;

    // Load model TensorFlow di background
    blazeface.load().then(model => {
        faceModel = model;
        console.log('Face detection model loaded');
    }).catch(e => {
        console.warn('Model gagal load, validasi dilewati:', e);
    });

    function showToast(message, type = 'danger') {
        const existing = document.getElementById('validasiToast');
        if (existing) existing.remove();

        const toast = document.createElement('div');
        toast.id = 'validasiToast';
        toast.className = `alert alert-${type} alert-dismissible fade show`;
        toast.style.cssText = 'position:fixed; top:20px; right:20px; z-index:9999; min-width:320px; max-width:420px; box-shadow: 0 4px 12px rgba(0,0,0,.15);';
        toast.innerHTML = `
            <i class="fas fa-${type === 'danger' ? 'times-circle' : 'check-circle'} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        document.body.appendChild(toast);

        setTimeout(() => { if (toast.parentNode) toast.remove(); }, 5000);
    }

    function setLoading(msg) {
        if (btnUpload) {
            btnUpload.disabled = true;
            btnUpload.innerHTML = `<i class="fas fa-spinner fa-spin me-2"></i>${msg}`;
        }
    }

    function resetBtn() {
        if (btnUpload) {
            btnUpload.disabled = true;
            btnUpload.innerHTML = '<i class="fas fa-paper-plane me-2"></i>Kirim Bukti Pembayaran';
        }
    }

    async function validasiGambar(file) {
        setLoading('Memvalidasi gambar...');
        fileValid = false;

        // 1. Cek ukuran & tipe
        if (!['image/jpeg', 'image/jpg', 'image/png'].includes(file.type)) {
            showToast('File harus berformat JPG atau PNG.');
            resetBtn();
            return;
        }
        if (file.size > 2 * 1024 * 1024) {
            showToast('Ukuran file maksimal 2MB.');
            resetBtn();
            return;
        }

        // 2. Tampilkan preview dulu
        const reader = new FileReader();
        reader.onload = async function (e) {
            preview.src = e.target.result;
            wrap.classList.remove('d-none');
            holder.classList.add('d-none');

            // 3. Deteksi wajah
            if (faceModel) {
                try {
                    setLoading('Menganalisis gambar...');

                    const img = new Image();
                    img.src   = e.target.result;

                    await new Promise(resolve => { img.onload = resolve; });

                    const predictions = await faceModel.estimateFaces(img, false);

                    if (predictions.length > 0) {
                        // Ada wajah terdeteksi → TOLAK
                        showToast('❌ Foto ditolak! Terdeteksi foto wajah/selfie. Upload screenshot bukti transfer bank atau e-wallet.');
                        wrap.classList.add('d-none');
                        holder.classList.remove('d-none');
                        preview.src = '';
                        input.value = '';
                        fileValid = false;
                        resetBtn();
                        return;
                    }

                    // Tidak ada wajah → LOLOS
                    fileValid = true;
                    if (btnUpload) {
                        btnUpload.disabled = false;
                        btnUpload.innerHTML = '<i class="fas fa-paper-plane me-2"></i>Kirim Bukti Pembayaran';
                    }
                    showToast('✅ Gambar valid, silakan kirim bukti pembayaran.', 'success');

                } catch (err) {
                    console.warn('Deteksi wajah error:', err);
                    // Kalau model error, loloskan saja
                    fileValid = true;
                    if (btnUpload) {
                        btnUpload.disabled = false;
                        btnUpload.innerHTML = '<i class="fas fa-paper-plane me-2"></i>Kirim Bukti Pembayaran';
                    }
                }
            } else {
                // Model belum load, loloskan
                fileValid = true;
                if (btnUpload) {
                    btnUpload.disabled = false;
                    btnUpload.innerHTML = '<i class="fas fa-paper-plane me-2"></i>Kirim Bukti Pembayaran';
                }
            }
        };
        reader.readAsDataURL(file);
    }

    // Event listeners
    zone.addEventListener('click', function (e) {
        if (!e.target.closest('#previewWrap')) input.click();
    });

    zone.addEventListener('dragover',  function (e) { e.preventDefault(); zone.classList.add('dragover'); });
    zone.addEventListener('dragleave', function ()  { zone.classList.remove('dragover'); });
    zone.addEventListener('drop', function (e) {
        e.preventDefault();
        zone.classList.remove('dragover');
        if (e.dataTransfer.files[0]) validasiGambar(e.dataTransfer.files[0]);
    });

    input.addEventListener('change', function () {
        if (this.files[0]) validasiGambar(this.files[0]);
    });

    // Submit guard
    const form = document.getElementById('formBukti');
    if (form) {
        form.addEventListener('submit', function (e) {
            if (!fileValid) {
                e.preventDefault();
                showToast('Pilih bukti pembayaran yang valid terlebih dahulu.');
                return;
            }
            setLoading('Mengirim...');
        });
    }

    if (btnUpload) btnUpload.disabled = true;
})();
</script>
@endpush