@extends('layouts.frontend')
@section('title', 'Ajukan Peminjaman Barang')

@push('styles')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap"
        rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .pinjam-wrap {
            max-width: 760px;
            margin: 0 auto;
            padding: 2.5rem 1rem;
        }

        /* Breadcrumb */
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
            color: #6b7280;
            text-decoration: none;
            margin-bottom: 1.5rem;
            transition: color .15s;
        }

        .back-link:hover {
            color: #f97316;
        }

        /* Page header */
        .page-title {
            font-size: 22px;
            font-weight: 700;
            color: #111827;
            margin-bottom: 4px;
        }

        .page-sub {
            font-size: 13px;
            color: #6b7280;
        }

        /* Progress bar */
        .progress-track {
            height: 4px;
            background: #f3f4f6;
            border-radius: 4px;
            margin: 1.5rem 0;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #f97316, #fb923c);
            border-radius: 4px;
            width: 0%;
            transition: width .4s ease;
        }

        /* Alert */
        .alert-custom {
            display: none;
            align-items: center;
            gap: 10px;
            background: #fff1f0;
            border: 1px solid #fca5a5;
            border-radius: 10px;
            padding: .75rem 1rem;
            font-size: 13px;
            color: #dc2626;
            margin-bottom: 1.25rem;
        }

        .alert-custom.show {
            display: flex;
        }

        .alert-custom.alert-success-custom {
            background: #f0fdf4;
            border-color: #86efac;
            color: #16a34a;
        }

        /* Section card */
        .section-card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 14px;
            margin-bottom: 1.25rem;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0, 0, 0, .04);
        }

        .section-head {
            padding: 1rem 1.25rem;
            border-bottom: 1px solid #f3f4f6;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .section-label {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .section-icon {
            width: 36px;
            height: 36px;
            border-radius: 9px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .icon-orange {
            background: #fff3e8;
        }

        .icon-blue {
            background: #eff6ff;
        }

        .icon-green {
            background: #f0fdf4;
        }

        .section-title {
            font-size: 14px;
            font-weight: 600;
            color: #111827;
        }

        .section-desc {
            font-size: 12px;
            color: #9ca3af;
            margin-top: 1px;
        }

        .section-body {
            padding: 1.25rem;
        }

        /* Item counter badge */
        .item-counter {
            background: #f3f4f6;
            border: 1px solid #e5e7eb;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            color: #6b7280;
            padding: 2px 10px;
            margin-left: 8px;
            transition: all .2s;
        }

        .item-counter.has-items {
            background: #fff3e8;
            border-color: #fed7aa;
            color: #f97316;
        }

        /* Add button */
        .btn-add-barang {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #f97316;
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 7px 14px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            font-family: inherit;
            transition: background .15s, transform .1s;
        }

        .btn-add-barang:hover {
            background: #ea6c0c;
        }

        .btn-add-barang:active {
            transform: scale(.97);
        }

        /* Item row */
        .item-row {
            background: #fafafa;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: .75rem;
            position: relative;
            animation: rowIn .2s ease;
        }

        .item-row:last-child {
            margin-bottom: 0;
        }

        @keyframes rowIn {
            from {
                opacity: 0;
                transform: translateY(-6px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .row-number {
            position: absolute;
            top: -11px;
            left: 14px;
            background: #f97316;
            color: #fff;
            font-size: 10px;
            font-weight: 700;
            padding: 1px 9px;
            border-radius: 20px;
        }

        .row-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 90px 38px;
            gap: 10px;
            align-items: end;
        }

        /* Form fields */
        .field-group {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .field-label {
            font-size: 12px;
            font-weight: 600;
            color: #374151;
        }

        .field-req {
            color: #f97316;
        }

        .field-select,
        .field-input {
            width: 100%;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 8px 11px;
            font-size: 13px;
            color: #111827;
            background: #fff;
            font-family: inherit;
            outline: none;
            transition: border-color .15s, box-shadow .15s;
            appearance: none;
            -webkit-appearance: none;
        }

        .field-select:focus,
        .field-input:focus {
            border-color: #f97316;
            box-shadow: 0 0 0 3px rgba(249, 115, 22, .12);
        }

        .field-select:disabled {
            background: #f9fafb;
            color: #9ca3af;
            cursor: not-allowed;
        }

        .stok-hint {
            font-size: 11px;
            color: #9ca3af;
            margin-top: 3px;
            min-height: 16px;
        }

        .stok-hint.ok {
            color: #16a34a;
        }

        .stok-hint.err {
            color: #dc2626;
        }

        /* Remove button */
        .btn-remove {
            width: 38px;
            height: 38px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            background: #fff;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #9ca3af;
            transition: all .15s;
            flex-shrink: 0;
        }

        .btn-remove:hover {
            background: #fef2f2;
            border-color: #fca5a5;
            color: #dc2626;
        }

        /* Empty state */
        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
        }

        .empty-icon-wrap {
            width: 56px;
            height: 56px;
            background: #f3f4f6;
            border-radius: 14px;
            margin: 0 auto 12px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .empty-title {
            font-size: 14px;
            font-weight: 500;
            color: #374151;
        }

        .empty-sub {
            font-size: 12px;
            color: #9ca3af;
            margin-top: 4px;
        }

        /* Date grid */
        .date-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        /* Durasi chip */
        .durasi-chip {
            display: none;
            align-items: center;
            gap: 6px;
            background: #eff6ff;
            color: #1d4ed8;
            border-radius: 20px;
            padding: 5px 14px;
            font-size: 12px;
            font-weight: 600;
            margin-top: .875rem;
            width: fit-content;
        }

        .durasi-chip.show {
            display: inline-flex;
        }

        .durasi-chip.warn {
            background: #fff1f0;
            color: #dc2626;
        }

        /* Textarea */
        textarea.field-input {
            resize: vertical;
            min-height: 96px;
            line-height: 1.5;
        }

        /* Char counter */
        .char-counter {
            font-size: 11px;
            color: #9ca3af;
            text-align: right;
            margin-top: 4px;
        }

        /* Action bar */
        .action-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 1.5rem;
            padding-top: 1.25rem;
            border-top: 1px solid #f3f4f6;
        }

        .btn-batal {
            border: 1px solid #e5e7eb;
            background: #fff;
            border-radius: 8px;
            padding: 9px 22px;
            font-size: 14px;
            color: #6b7280;
            cursor: pointer;
            font-family: inherit;
            transition: all .15s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-batal:hover {
            background: #f9fafb;
            border-color: #d1d5db;
            color: #374151;
        }

        .btn-submit {
            background: #f97316;
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 10px 28px;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-family: inherit;
            transition: background .15s, transform .1s;
        }

        .btn-submit:hover {
            background: #ea6c0c;
        }

        .btn-submit:active {
            transform: scale(.98);
        }

        .btn-submit:disabled {
            background: #fed7aa;
            cursor: not-allowed;
            transform: none;
        }

        .btn-submit .spinner {
            display: none;
            width: 16px;
            height: 16px;
            border: 2px solid rgba(255, 255, 255, .3);
            border-top-color: #fff;
            border-radius: 50%;
            animation: spin .7s linear infinite;
        }

        .btn-submit.loading .spinner {
            display: inline-block;
        }

        .btn-submit.loading .btn-icon {
            display: none;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        /* Responsive */
        @media (max-width: 580px) {
            .row-grid {
                grid-template-columns: 1fr 1fr;
                grid-template-rows: auto auto;
            }

            .row-grid>*:nth-child(3) {
                grid-column: 1;
            }

            .row-grid>*:nth-child(4) {
                grid-column: 2;
                grid-row: 2;
                align-self: end;
            }

            .date-grid {
                grid-template-columns: 1fr;
            }

            .page-title {
                font-size: 18px;
            }
        }
    </style>
@endpush

@section('content')
    <div class="pinjam-wrap">

        {{-- Back link --}}
        <a href="{{ route('user.peminjaman.index') }}" class="back-link">
            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M10 12L6 8l4-4" />
            </svg>
            Kembali ke Peminjaman Saya
        </a>

        {{-- Page header --}}
        <h1 class="page-title">Ajukan Peminjaman Barang</h1>
        <p class="page-sub">Isi form di bawah untuk mengajukan peminjaman barang</p>

        {{-- Progress bar --}}
        <div class="progress-track">
            <div class="progress-fill" id="progressFill"></div>
        </div>

        {{-- Alert validasi Laravel --}}
        @if ($errors->any())
            <div class="alert-custom show">
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor"
                    stroke-width="1.5">
                    <circle cx="8" cy="8" r="7" />
                    <path d="M8 5v3.5M8 10.5v.5" />
                </svg>
                <div>
                    <strong>Terjadi kesalahan:</strong>
                    <ul style="margin: 4px 0 0; padding-left: 16px;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        {{-- Alert JS --}}
        <div class="alert-custom" id="jsAlert">
            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5">
                <circle cx="8" cy="8" r="7" />
                <path d="M8 5v3.5M8 10.5v.5" />
            </svg>
            <span id="jsAlertText"></span>
        </div>

        <form action="{{ route('user.peminjaman.store') }}" method="POST" id="formPeminjaman">
            @csrf

            {{-- ── SECTION 1: BARANG ──────────────────────────────────── --}}
            <div class="section-card">
                <div class="section-head">
                    <div class="section-label">
                        <div class="section-icon icon-orange">
                            <svg width="18" height="18" viewBox="0 0 18 18" fill="none" stroke="#f97316"
                                stroke-width="1.6">
                                <path d="M3 5h12l-1.5 9a1 1 0 0 1-1 .9H5.5a1 1 0 0 1-1-.9L3 5z" />
                                <path d="M1 5h16M6 5V3.5a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1V5" />
                            </svg>
                        </div>
                        <div>
                            <div class="section-title">
                                Barang yang Dipinjam
                                <span class="item-counter" id="itemCounter">0 item</span>
                            </div>
                            <div class="section-desc">Pilih barang dan jumlah yang ingin dipinjam</div>
                        </div>
                    </div>
                    <button type="button" class="btn-add-barang" onclick="addRow()">
                        <svg width="13" height="13" viewBox="0 0 13 13" fill="none" stroke="currentColor"
                            stroke-width="2.2">
                            <path d="M6.5 1.5v10M1.5 6.5h10" />
                        </svg>
                        Tambah Barang
                    </button>
                </div>
                <div class="section-body">
                    <div id="containerBarang"></div>
                    <div id="emptyState" class="empty-state">
                        <div class="empty-icon-wrap">
                            <svg width="26" height="26" viewBox="0 0 26 26" fill="none" stroke="#9ca3af"
                                stroke-width="1.5">
                                <rect x="3" y="6" width="20" height="16" rx="2" />
                                <path d="M8 6V4a1 1 0 0 1 1-1h8a1 1 0 0 1 1 1v2M13 11v6M10 14h6" />
                            </svg>
                        </div>
                        <p class="empty-title">Belum ada barang dipilih</p>
                        <p class="empty-sub">Klik "+ Tambah Barang" untuk mulai menambahkan</p>
                    </div>
                </div>
            </div>

            {{-- ── SECTION 2: JADWAL ──────────────────────────────────── --}}
            <div class="section-card">
                <div class="section-head">
                    <div class="section-label">
                        <div class="section-icon icon-blue">
                            <svg width="18" height="18" viewBox="0 0 18 18" fill="none" stroke="#3b82f6"
                                stroke-width="1.6">
                                <rect x="2" y="3" width="14" height="13" rx="2" />
                                <path d="M5.5 3V1.5M12.5 3V1.5M2 7.5h14" />
                            </svg>
                        </div>
                        <div>
                            <div class="section-title">Jadwal Peminjaman</div>
                            <div class="section-desc">Tentukan tanggal pinjam dan kembali</div>
                        </div>
                    </div>
                </div>
                <div class="section-body">
                    <div class="date-grid">
                        <div class="field-group">
                            <label class="field-label">Tanggal Pinjam <span class="field-req">*</span></label>
                            <input type="date" name="tanggal_pinjam" id="tanggalPinjam"
                                class="field-input @error('tanggal_pinjam') is-invalid @enderror"
                                value="{{ old('tanggal_pinjam', date('Y-m-d')) }}" min="{{ date('Y-m-d') }}" required
                                onchange="hitungDurasi()">
                            @error('tanggal_pinjam')
                                <div class="stok-hint err">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="field-group">
                            <label class="field-label">Tanggal Kembali <span class="field-req">*</span></label>
                            <input type="date" name="tanggal_kembali" id="tanggalKembali"
                                class="field-input @error('tanggal_kembali') is-invalid @enderror"
                                value="{{ old('tanggal_kembali') }}" min="{{ date('Y-m-d') }}" required
                                onchange="hitungDurasi()">
                            @error('tanggal_kembali')
                                <div class="stok-hint err">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="durasi-chip" id="durasiChip">
                        <svg width="13" height="13" viewBox="0 0 13 13" fill="none" stroke="currentColor"
                            stroke-width="1.6">
                            <circle cx="6.5" cy="6.5" r="5.5" />
                            <path d="M6.5 3.5v3l2 1.2" />
                        </svg>
                        <span id="durasiText"></span>
                    </div>
                </div>
            </div>

            {{-- ── SECTION 3: KETERANGAN ──────────────────────────────── --}}
            <div class="section-card">
                <div class="section-head">
                    <div class="section-label">
                        <div class="section-icon icon-green">
                            <svg width="18" height="18" viewBox="0 0 18 18" fill="none" stroke="#16a34a"
                                stroke-width="1.6">
                                <path
                                    d="M3 4h12v11a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V4zM6 4V3a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v1" />
                                <path d="M6 8h6M6 11h4" />
                            </svg>
                        </div>
                        <div>
                            <div class="section-title">Keterangan</div>
                            <div class="section-desc">Opsional — tuliskan keperluan peminjaman</div>
                        </div>
                    </div>
                </div>
                <div class="section-body">
                    <textarea name="keterangan" id="keteranganInput" class="field-input @error('keterangan') is-invalid @enderror"
                        placeholder="Contoh: Digunakan untuk praktikum jaringan kelas XII RPL 1..." maxlength="500"
                        oninput="updateCharCount(); updateProgress()">{{ old('keterangan') }}</textarea>
                    <div class="char-counter"><span id="charCount">0</span>/500</div>
                    @error('keterangan')
                        <div class="stok-hint err">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- ── ACTION BAR ─────────────────────────────────────────── --}}
            <div class="action-bar">
                <a href="{{ url()->previous() }}" class="btn-batal">
                    <svg width="14" height="14" viewBox="0 0 14 14" fill="none" stroke="currentColor"
                        stroke-width="1.8">
                        <path d="M10.5 3.5l-7 7M3.5 3.5l7 7" />
                    </svg>
                    Batal
                </a>
                <button type="submit" class="btn-submit" id="btnSubmit">
                    <span class="spinner"></span>
                    <svg class="btn-icon" width="16" height="16" viewBox="0 0 16 16" fill="none"
                        stroke="currentColor" stroke-width="2">
                        <path d="M2 8l4 4 8-8" />
                    </svg>
                    Ajukan Peminjaman
                </button>
            </div>

        </form>
    </div>
@endsection

@push('scripts')
    <script>
        // ─── Data dari backend ────────────────────────────────────────────────────────
        const barangData = @json($barangData);
        const uniqueRuangans = [...new Map(barangData.map(b => [b.ruangan_id, {
            id: b.ruangan_id,
            nama: b.ruangan_nama
        }])).values()];
        let rowIdx = 0;

        // ─── Helpers stok ─────────────────────────────────────────────────────────────
        function getRemainingQty(barangRuanganId, excludeRow = null) {
            const br = barangData.find(b => b.id == barangRuanganId);
            if (!br) return 0;
            let used = 0;
            document.querySelectorAll('.barang-row').forEach(row => {
                if (row === excludeRow) return;
                const sel = row.querySelector('.b-sel');
                const inp = row.querySelector('.q-inp');
                if (sel && sel.value == barangRuanganId && inp) used += parseInt(inp.value) || 0;
            });
            return br.qty - used;
        }

        // ─── Add row ──────────────────────────────────────────────────────────────────
        function addRow() {
            const container = document.getElementById('containerBarang');
            const emptyState = document.getElementById('emptyState');
            const n = rowIdx++;

            let ruanganOpts = '<option value="">Pilih ruangan</option>';
            uniqueRuangans.forEach(r => {
                ruanganOpts += `<option value="${r.id}">${r.nama}</option>`;
            });

            const div = document.createElement('div');
            div.className = 'item-row barang-row';
            div.id = `row-${n}`;
            div.innerHTML = `
        <span class="row-number" id="rowNum-${n}">1</span>
        <div class="row-grid">
            <div class="field-group">
                <label class="field-label">Ruangan <span class="field-req">*</span></label>
                <select class="field-select r-sel" onchange="onRuanganChange(this, 'row-${n}')" required>
                    ${ruanganOpts}
                </select>
            </div>
            <div class="field-group">
                <label class="field-label">Barang <span class="field-req">*</span></label>
                <select name="barang_ruangan_id[]" class="field-select b-sel"
                    onchange="onBarangChange(this, 'row-${n}')" required disabled>
                    <option value="">— Pilih ruangan dulu —</option>
                </select>
                <div class="stok-hint" id="stokHint-${n}"></div>
            </div>
            <div class="field-group">
                <label class="field-label">Jumlah <span class="field-req">*</span></label>
               <input type="number" name="jumlah[]" class="field-input q-inp"
    min="1" value="1"
    oninput="onQtyInput(this,'row-${n}')"
    onblur="onQtyBlur(this)"
    onkeydown="return event.key!=='-' && event.key!=='e' && event.key!=='+'"
    required>
            </div>
            <div class="field-group" style="align-items:flex-end">
                <button type="button" class="btn-remove" onclick="removeRow('row-${n}')" title="Hapus baris">
                    <svg width="14" height="14" viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="1.6">
                        <path d="M2 3.5h10M5.5 3.5V2.5h3v1M3 3.5l.7 7.5a1 1 0 0 0 1 .9h4.6a1 1 0 0 0 1-.9L11 3.5"/>
                        <path d="M5.5 6.5v3M8.5 6.5v3"/>
                    </svg>
                </button>
            </div>
        </div>`;

            container.appendChild(div);
            emptyState.style.display = 'none';
            updateRowNumbers();
            updateRemoveButtons();
            updateProgress();
        }

        // ─── Remove row ───────────────────────────────────────────────────────────────
        function removeRow(rowId) {
            const rows = document.querySelectorAll('.barang-row');
            if (rows.length <= 1) {
                showAlert('Minimal harus ada 1 barang yang dipinjam.');
                return;
            }
            document.getElementById(rowId).remove();
            refreshAllDropdowns();
            updateRowNumbers();
            updateRemoveButtons();
            updateProgress();

            if (!document.querySelectorAll('.barang-row').length) {
                document.getElementById('emptyState').style.display = 'block';
            }
        }

        // ─── Ruangan change ───────────────────────────────────────────────────────────
        function onRuanganChange(sel, rowId) {
            const row = document.getElementById(rowId);
            const bs = row.querySelector('.b-sel');
            const rowN = rowId.replace('row-', '');
            const hint = document.getElementById(`stokHint-${rowN}`);

            hint.textContent = '';
            hint.className = 'stok-hint';

            if (!sel.value) {
                bs.disabled = true;
                bs.innerHTML = '<option value="">— Pilih ruangan dulu —</option>';
                updateProgress();
                return;
            }

            const filtered = barangData.filter(b => b.ruangan_id == sel.value && b.qty > 0);
            bs.disabled = false;
            bs.innerHTML = '<option value="">Pilih barang</option>';

            let available = 0;
            filtered.forEach(b => {
                const rem = getRemainingQty(b.id, row);
                if (rem <= 0) return;
                const opt = document.createElement('option');
                opt.value = b.id;
                opt.textContent = `${b.barang_nama}`;
                opt.dataset.qty = rem;
                opt.dataset.nama = b.barang_nama;
                bs.appendChild(opt);
                available++;
            });

            if (available === 0) {
                bs.disabled = true;
                bs.innerHTML = '<option value="">Semua barang sudah dipilih</option>';
                hint.className = 'stok-hint err';
                hint.textContent = 'Tidak ada barang tersedia di ruangan ini';
            }

            updateProgress();
        }

        // ─── Barang change ────────────────────────────────────────────────────────────
        function onBarangChange(sel, rowId) {
            const row = document.getElementById(rowId);
            const qi = row.querySelector('.q-inp');
            const rowN = rowId.replace('row-', '');
            const hint = document.getElementById(`stokHint-${rowN}`);

            if (!sel.value) {
                hint.textContent = '';
                hint.className = 'stok-hint';
                updateProgress();
                return;
            }

            const rem = getRemainingQty(sel.value, row);
            qi.setAttribute('max', rem);
            qi.dataset.maxStok = rem;
            if (parseInt(qi.value) > rem) qi.value = rem;

            hint.className = 'stok-hint ok';
            hint.textContent = `Stok tersedia: ${rem} unit`;

            refreshAllDropdowns(row);
            updateProgress();
        }

        // ─── Qty change ───────────────────────────────────────────────────────────────
        function onQtyChange(inp, rowId) {
            const max = parseInt(inp.dataset.maxStok) || 999;
            if (parseInt(inp.value) > max) inp.value = max;
            if (parseInt(inp.value) < 1 || !inp.value) inp.value = 1;

            const row = document.getElementById(rowId);
            const sel = row.querySelector('.b-sel');
            if (sel && sel.value) refreshAllDropdowns(row);
            updateProgress();

        }

        // ─── Qty change ───────────────────────────────────────────────────────────────
        function onQtyInput(inp, rowId) {
            const max = parseInt(inp.dataset.maxStok) || 999;
            // Hanya cek max, jangan force minimum saat mengetik
            if (inp.value !== '' && parseInt(inp.value) > max) inp.value = max;

            const row = document.getElementById(rowId);
            const sel = row.querySelector('.b-sel');
            if (sel && sel.value) refreshAllDropdowns(row);
            updateProgress();
        }

        function onQtyBlur(inp) {
            // Validasi minimum baru jalan pas user keluar dari field
            if (inp.value === '' || parseInt(inp.value) < 1) inp.value = 1;
            const max = parseInt(inp.dataset.maxStok) || 999;
            if (parseInt(inp.value) > max) inp.value = max;
        }

        // ─── Refresh all dropdowns ────────────────────────────────────────────────────
        function refreshAllDropdowns(changedRow = null) {
            document.querySelectorAll('.barang-row').forEach(row => {
                if (row === changedRow) return;
                const rs = row.querySelector('.r-sel');
                const bs = row.querySelector('.b-sel');
                const qi = row.querySelector('.q-inp');
                const rowN = row.id.replace('row-', '');
                const hint = document.getElementById(`stokHint-${rowN}`);
                if (!rs || !rs.value) return;

                const currentVal = bs.value;
                const filteredBarang = barangData.filter(b => b.ruangan_id == rs.value && b.qty > 0);

                bs.innerHTML = '<option value="">Pilih barang</option>';
                filteredBarang.forEach(b => {
                    const rem = getRemainingQty(b.id, row);
                    if (rem <= 0 && b.id != currentVal) return;
                    const opt = document.createElement('option');
                    opt.value = b.id;
                    opt.textContent = b.barang_nama;
                    opt.dataset.qty = rem;
                    if (b.id == currentVal) opt.selected = true;
                    bs.appendChild(opt);
                });

                if (currentVal && hint) {
                    const rem = getRemainingQty(currentVal, row);
                    if (qi) {
                        qi.max = rem;
                        qi.dataset.maxStok = rem;
                    }
                    hint.className = 'stok-hint ok';
                    hint.textContent = `Stok tersedia: ${rem} unit`;
                }
            });
        }

        // ─── Update row numbers ───────────────────────────────────────────────────────
        function updateRowNumbers() {
            const rows = document.querySelectorAll('.barang-row');
            rows.forEach((row, i) => {
                const numEl = row.querySelector('.row-number');
                if (numEl) numEl.textContent = i + 1;
            });
            const count = rows.length;
            const counter = document.getElementById('itemCounter');
            counter.textContent = count + ' item';
            counter.className = 'item-counter' + (count > 0 ? ' has-items' : '');
        }

        // ─── Update remove button state ───────────────────────────────────────────────
        function updateRemoveButtons() {
            const rows = document.querySelectorAll('.barang-row');
            rows.forEach(row => {
                const btn = row.querySelector('.btn-remove');
                if (btn) btn.disabled = rows.length === 1;
            });
        }

        // ─── Durasi ───────────────────────────────────────────────────────────────────
        function hitungDurasi() {
            const pinjam = document.getElementById('tanggalPinjam').value;
            const kembali = document.getElementById('tanggalKembali').value;
            const chip = document.getElementById('durasiChip');
            const text = document.getElementById('durasiText');

            if (!pinjam || !kembali) {
                chip.classList.remove('show', 'warn');
                return;
            }

            const diff = Math.round((new Date(kembali) - new Date(pinjam)) / 864e5);

            if (diff < 0) {
                text.textContent = 'Tanggal kembali tidak valid';
                chip.className = 'durasi-chip show warn';
                return;
            }
            text.textContent = `Durasi peminjaman: ${diff} hari`;
            chip.className = 'durasi-chip show';

            document.getElementById('tanggalKembali').min = pinjam;
            updateProgress();
        }

        // ─── Char counter ─────────────────────────────────────────────────────────────
        function updateCharCount() {
            const val = document.getElementById('keteranganInput').value.length;
            document.getElementById('charCount').textContent = val;
        }

        // ─── Progress bar ─────────────────────────────────────────────────────────────
        function updateProgress() {
            const rows = [...document.querySelectorAll('.barang-row')];
            const hasBarang = rows.length > 0 && rows.every(r => r.querySelector('.b-sel') && r.querySelector('.b-sel')
                .value);
            const hasTgl = document.getElementById('tanggalPinjam').value && document.getElementById('tanggalKembali')
            .value;
            const hasKet = document.getElementById('keteranganInput').value.length > 0;

            let filled = 0;
            if (hasBarang) filled++;
            if (hasTgl) filled++;
            if (hasKet) filled++;

            document.getElementById('progressFill').style.width = Math.round((filled / 3) * 100) + '%';
        }

        // ─── Alert JS ─────────────────────────────────────────────────────────────────
        function showAlert(msg) {
            const el = document.getElementById('jsAlert');
            document.getElementById('jsAlertText').textContent = msg;
            el.classList.add('show');
            el.scrollIntoView({
                behavior: 'smooth',
                block: 'nearest'
            });
            setTimeout(() => el.classList.remove('show'), 4500);
        }

        // ─── Submit handler ───────────────────────────────────────────────────────────
        document.getElementById('formPeminjaman').addEventListener('submit', function(e) {
            const rows = document.querySelectorAll('.barang-row');

            if (rows.length === 0) {
                e.preventDefault();
                showAlert('Tambahkan minimal 1 barang yang ingin dipinjam.');
                return;
            }

            const noBarang = [...rows].find(r => !r.querySelector('.b-sel').value);
            if (noBarang) {
                e.preventDefault();
                showAlert('Semua baris barang harus dipilih terlebih dahulu.');
                noBarang.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
                return;
            }

            const pinjam = document.getElementById('tanggalPinjam').value;
            const kembali = document.getElementById('tanggalKembali').value;
            if (!pinjam || !kembali) {
                e.preventDefault();
                showAlert('Tanggal pinjam dan tanggal kembali wajib diisi.');
                return;
            }

            if (new Date(kembali) < new Date(pinjam)) {
                e.preventDefault();
                showAlert('Tanggal kembali tidak boleh sebelum tanggal pinjam.');
                return;
            }

            // Loading state
            const btn = document.getElementById('btnSubmit');
            btn.disabled = true;
            btn.classList.add('loading');
        });

        // ─── Init ─────────────────────────────────────────────────────────────────────
        addRow();
        updateCharCount();
        document.getElementById('tanggalPinjam').addEventListener('change', function() {
            document.getElementById('tanggalKembali').min = this.value;
            hitungDurasi();
        });
        document.getElementById('tanggalKembali').addEventListener('change', hitungDurasi);
    </script>
@endpush
