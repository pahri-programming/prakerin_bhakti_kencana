@extends('layouts.frontend')
@section('title', 'Ajukan Peminjaman Barang')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-9">

            {{-- Header --}}
            <div class="mb-4">
                <a href="{{ route('user.peminjaman.index') }}" class="text-muted text-decoration-none small">
                    <i class="fas fa-arrow-left me-1"></i>Kembali
                </a>
                <h4 class="fw-bold mt-2 mb-0">Ajukan Peminjaman Barang</h4>
                <p class="text-muted small">Isi form di bawah untuk mengajukan peminjaman barang</p>
            </div>

            {{-- Alert Error --}}
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <strong>Terjadi kesalahan:</strong>
                    <ul class="mb-0 mt-1 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <form action="{{ route('user.peminjaman.store') }}" method="POST" id="formPeminjaman">
                @csrf

                {{-- CARD: Barang --}}
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-header bg-white border-bottom py-3 px-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="fw-bold mb-0"><i class="fas fa-boxes me-2 text-primary"></i>Barang yang Dipinjam</h6>
                                <small class="text-muted">Pilih barang dan jumlah yang ingin dipinjam</small>
                            </div>
                            <button type="button" class="btn btn-primary btn-sm px-3" onclick="addRow()">
                                <i class="fas fa-plus me-1"></i>Tambah Barang
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <div id="container-barang"></div>
                        <div id="empty-state" class="text-center py-4 text-muted d-none">
                            <i class="fas fa-box-open fa-2x mb-2 opacity-50"></i>
                            <p class="mb-0 small">Belum ada barang dipilih. Klik "+ Tambah Barang"</p>
                        </div>
                    </div>
                </div>

                {{-- CARD: Jadwal --}}
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-header bg-white border-bottom py-3 px-4">
                        <h6 class="fw-bold mb-0"><i class="fas fa-calendar-alt me-2 text-primary"></i>Jadwal Peminjaman</h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Tanggal Pinjam <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal_pinjam" id="tanggal_pinjam"
                                    class="form-control @error('tanggal_pinjam') is-invalid @enderror"
                                    value="{{ old('tanggal_pinjam', date('Y-m-d')) }}"
                                    min="{{ date('Y-m-d') }}" required>
                                @error('tanggal_pinjam')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Tanggal Kembali <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal_kembali" id="tanggal_kembali"
                                    class="form-control @error('tanggal_kembali') is-invalid @enderror"
                                    value="{{ old('tanggal_kembali') }}"
                                    min="{{ date('Y-m-d') }}" required>
                                @error('tanggal_kembali')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12">
                                <div id="durasi-info" class="text-muted small d-none">
                                    <i class="fas fa-clock me-1 text-primary"></i>
                                    Durasi: <strong id="durasi-text"></strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- CARD: Keterangan --}}
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-header bg-white border-bottom py-3 px-4">
                        <h6 class="fw-bold mb-0"><i class="fas fa-sticky-note me-2 text-primary"></i>Keterangan</h6>
                    </div>
                    <div class="card-body p-4">
                        <textarea name="keterangan" rows="3"
                            class="form-control @error('keterangan') is-invalid @enderror"
                            placeholder="Tuliskan keperluan peminjaman (opsional)...">{{ old('keterangan') }}</textarea>
                        @error('keterangan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- Action --}}
                <div class="d-flex justify-content-between align-items-center">
                    <a href="{{ url()->previous() }}" class="btn btn-outline-secondary px-4">
                        <i class="fas fa-times me-1"></i>Batal
                    </a>
                    <button type="submit" class="btn btn-success px-4" id="btnSubmit">
                        <i class="fas fa-paper-plane me-2"></i>Ajukan Peminjaman
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Data barang dari backend
const barangData = @json($barangData);
// Unique ruangan
const uniqueRuangan = [...new Map(barangData.map(b => [b.ruangan_id, {id: b.ruangan_id, nama: b.ruangan_nama}])).values()];

let rowCount = 0;

/**
 * Hitung sisa stok setelah dikurangi row lain
 */
function getRemainingQty(barangRuanganId, excludeRow = null) {
    const br = barangData.find(b => b.id == barangRuanganId);
    if (!br) return 0;
    let used = 0;
    document.querySelectorAll('.barang-row').forEach(row => {
        if (row === excludeRow) return;
        const sel = row.querySelector('.barang-select');
        const inp = row.querySelector('.jumlah-input');
        if (sel && sel.value == barangRuanganId && inp) {
            used += parseInt(inp.value) || 0;
        }
    });
    return br.qty - used;
}

/**
 * Refresh dropdown barang di semua row (kecuali row yang baru diubah)
 */
function refreshAllDropdowns(changedRow = null) {
    document.querySelectorAll('.barang-row').forEach(row => {
        if (row === changedRow) return;
        const ruanganSelect = row.querySelector('.ruangan-select');
        const barangSelect  = row.querySelector('.barang-select');
        const stokInfo      = row.querySelector('.stok-info');
        const jumlahInput   = row.querySelector('.jumlah-input');
        if (!ruanganSelect || !ruanganSelect.value) return;

        const currentBarang  = barangSelect.value;
        const filteredBarang = barangData.filter(b => b.ruangan_id == ruanganSelect.value && b.qty > 0);

        barangSelect.innerHTML = '<option value="">-- Pilih Barang --</option>';
        filteredBarang.forEach(b => {
            const remaining = getRemainingQty(b.id, row);
            if (remaining <= 0 && b.id != currentBarang) return;
            const opt = document.createElement('option');
            opt.value = b.id;
            opt.textContent = `${b.barang_nama} (Stok: ${remaining})`;
            opt.dataset.qty  = remaining;
            if (b.id == currentBarang) opt.selected = true;
            barangSelect.appendChild(opt);
        });

        if (currentBarang) {
            const rem = getRemainingQty(currentBarang, row);
            if (jumlahInput) {
                jumlahInput.setAttribute('max', rem);
                jumlahInput.dataset.maxStok = rem;
            }
            if (stokInfo) stokInfo.textContent = `Stok tersedia: ${rem} unit`;
        }
    });
}

/**
 * Filter barang berdasarkan ruangan
 */
function filterBarang(selectElement) {
    const ruanganId    = selectElement.value;
    const row          = selectElement.closest('.barang-row');
    const barangSelect = row.querySelector('.barang-select');
    const stokInfo     = row.querySelector('.stok-info');
    const jumlahInput  = row.querySelector('.jumlah-input');

    barangSelect.innerHTML = '<option value="">-- Pilih Barang --</option>';
    stokInfo.textContent   = '';
    jumlahInput.value      = 1;
    jumlahInput.removeAttribute('max');
    jumlahInput.removeAttribute('data-max-stok');

    if (!ruanganId) {
        barangSelect.disabled  = true;
        barangSelect.innerHTML = '<option value="">-- Pilih Ruangan Dulu --</option>';
        return;
    }

    const filtered = barangData.filter(b => b.ruangan_id == ruanganId && b.qty > 0);
    barangSelect.disabled = false;

    let available = 0;
    filtered.forEach(b => {
        const remaining = getRemainingQty(b.id, row);
        if (remaining <= 0) return;
        const opt = document.createElement('option');
        opt.value = b.id;
        opt.textContent = `${b.barang_nama} (Stok: ${remaining})`;
        opt.dataset.qty  = remaining;
        barangSelect.appendChild(opt);
        available++;
    });

    if (available === 0) {
        barangSelect.disabled  = true;
        barangSelect.innerHTML = '<option value="">-- Semua Barang Sudah Dipilih --</option>';
    }
}

/**
 * Handle pemilihan barang
 */
function handleBarangSelect(selectElement) {
    const row         = selectElement.closest('.barang-row');
    const stokInfo    = row.querySelector('.stok-info');
    const jumlahInput = row.querySelector('.jumlah-input');

    if (!selectElement.value) {
        stokInfo.textContent = '';
        jumlahInput.removeAttribute('max');
        refreshAllDropdowns(row);
        return;
    }

    const remaining = getRemainingQty(selectElement.value, row);
    jumlahInput.setAttribute('max', remaining);
    jumlahInput.dataset.maxStok = remaining;
    jumlahInput.value = Math.min(parseInt(jumlahInput.value) || 1, remaining);
    stokInfo.textContent = `Stok tersedia: ${remaining} unit`;

    refreshAllDropdowns(row);
}

/**
 * Validasi jumlah
 */
function validateJumlah(input) {
    const max    = parseInt(input.dataset.maxStok) || 0;
    const jumlah = parseInt(input.value) || 0;
    if (jumlah > max && max > 0) {
        input.value = max;
    }
    const row = input.closest('.barang-row');
    const sel = row.querySelector('.barang-select');
    if (sel && sel.value) refreshAllDropdowns(row);
}

/**
 * Tambah row barang
 */
function addRow() {
    const container = document.getElementById('container-barang');
    const emptyState = document.getElementById('empty-state');

    let ruanganOptions = '<option value="">-- Pilih Ruangan --</option>';
    uniqueRuangan.forEach(r => {
        ruanganOptions += `<option value="${r.id}">${r.nama}</option>`;
    });

    const html = `
    <div class="barang-row border rounded-3 p-3 mb-3 bg-light">
        <div class="row g-2 align-items-end">
            <div class="col-md-4">
                <label class="form-label small fw-semibold mb-1">Ruangan <span class="text-danger">*</span></label>
                <select class="form-select form-select-sm ruangan-select" onchange="filterBarang(this)" required>
                    ${ruanganOptions}
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-semibold mb-1">Barang <span class="text-danger">*</span></label>
                <select name="barang_ruangan_id[]" class="form-select form-select-sm barang-select"
                    onchange="handleBarangSelect(this)" required disabled>
                    <option value="">-- Pilih Ruangan Dulu --</option>
                </select>
                <div class="stok-info text-muted mt-1" style="font-size:11px;"></div>
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-semibold mb-1">Jumlah <span class="text-danger">*</span></label>
                <input type="number" name="jumlah[]" class="form-control form-control-sm jumlah-input"
                    min="1" value="1" oninput="validateJumlah(this)" required>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="button" class="btn btn-outline-danger btn-sm w-100" onclick="removeRow(this)">
                    <i class="fas fa-trash-alt me-1"></i>Hapus
                </button>
            </div>
        </div>
    </div>`;

    container.insertAdjacentHTML('beforeend', html);
    emptyState.classList.add('d-none');
    updateRemoveButtons();
    rowCount++;
}

/**
 * Hapus row
 */
function removeRow(btn) {
    const row  = btn.closest('.barang-row');
    const rows = document.querySelectorAll('.barang-row');

    if (rows.length === 1) {
        alert('Minimal harus ada 1 barang yang dipinjam.');
        return;
    }

    row.remove();
    refreshAllDropdowns();
    updateRemoveButtons();
}

/**
 * Update state tombol hapus
 */
function updateRemoveButtons() {
    const rows = document.querySelectorAll('.barang-row');
    rows.forEach(row => {
        const btn = row.querySelector('button[onclick*="removeRow"]');
        if (btn) btn.disabled = rows.length === 1;
    });
}

// ─── Durasi otomatis ────────────────────────────────────────────────────────
function hitungDurasi() {
    const pinjam  = document.getElementById('tanggal_pinjam').value;
    const kembali = document.getElementById('tanggal_kembali').value;
    const info    = document.getElementById('durasi-info');
    const text    = document.getElementById('durasi-text');

    if (!pinjam || !kembali) { info.classList.add('d-none'); return; }

    const diff = Math.round((new Date(kembali) - new Date(pinjam)) / (1000 * 60 * 60 * 24));
    if (diff < 0) {
        text.textContent = 'Tanggal kembali tidak valid';
        info.classList.remove('d-none');
        return;
    }
    text.textContent = `${diff} hari`;
    info.classList.remove('d-none');
}

document.getElementById('tanggal_pinjam').addEventListener('change', function () {
    document.getElementById('tanggal_kembali').min = this.value;
    hitungDurasi();
});
document.getElementById('tanggal_kembali').addEventListener('change', hitungDurasi);

// ─── Submit validation ───────────────────────────────────────────────────────
document.getElementById('formPeminjaman').addEventListener('submit', function (e) {
    const rows = document.querySelectorAll('.barang-row');
    if (rows.length === 0) {
        e.preventDefault();
        alert('Tambahkan minimal 1 barang yang ingin dipinjam.');
        return;
    }
    document.getElementById('btnSubmit').disabled = true;
    document.getElementById('btnSubmit').innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Memproses...';
});

// Init
addRow();
</script>
@endpush
@endsection