@extends('layouts.backend')
@section('title', 'Tambah Peminjaman')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold mb-1">Tambah Peminjaman Barang</h2>
                    <p class="text-muted mb-0">Isi form untuk membuat peminjaman baru</p>
                </div>
                <a href="{{ route('backend.peminjaman.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Kembali
                </a>
            </div>

            <!-- Form Card -->
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-4">
                    <form action="{{ route('backend.peminjaman.store') }}" method="POST" id="formPeminjaman">
                        @csrf

                        <!-- User & Info -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">User <span class="text-danger">*</span></label>
                                <select name="user_id" class="form-select @error('user_id') is-invalid @enderror" required>
                                    <option value="">-- Pilih User --</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('user_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Nama Peminjam</label>
                                <input type="text" name="nama_peminjam"
                                       class="form-control @error('nama_peminjam') is-invalid @enderror"
                                       value="{{ old('nama_peminjam') }}"
                                       placeholder="Opsional - jika berbeda dari user">
                                @error('nama_peminjam')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Instansi</label>
                            <input type="text" name="instansi"
                                   class="form-control @error('instansi') is-invalid @enderror"
                                   value="{{ old('instansi') }}"
                                   placeholder="Nama instansi/organisasi">
                            @error('instansi')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Barang Section -->
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <label class="form-label fw-semibold mb-0">
                                    Barang yang Dipinjam <span class="text-danger">*</span>
                                </label>
                                <button type="button" class="btn btn-sm btn-primary" onclick="addBarangRow()">
                                    <i class="fas fa-plus me-1"></i>Tambah Barang
                                </button>
                            </div>

                            <div id="barang-container"></div>
                        </div>

                        <!-- Tanggal -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Tanggal Pinjam <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal_pinjam"
                                       class="form-control @error('tanggal_pinjam') is-invalid @enderror"
                                       value="{{ old('tanggal_pinjam', date('Y-m-d')) }}"
                                       min="{{ date('Y-m-d') }}"
                                       required>
                                @error('tanggal_pinjam')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Tanggal Kembali <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal_kembali"
                                       class="form-control @error('tanggal_kembali') is-invalid @enderror"
                                       value="{{ old('tanggal_kembali') }}"
                                       min="{{ date('Y-m-d') }}"
                                       required>
                                @error('tanggal_kembali')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Keterangan -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Keterangan</label>
                            <textarea name="keterangan" rows="3"
                                      class="form-control @error('keterangan') is-invalid @enderror"
                                      placeholder="Keperluan peminjaman... (opsional)">{{ old('keterangan') }}</textarea>
                            @error('keterangan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Buttons -->
                        <hr class="my-4">
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('backend.peminjaman.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>Batal
                            </a>
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fas fa-save me-2"></i>Simpan Peminjaman
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Data barang ruangan dari backend
const barangRuanganData = {!! json_encode($barangRuangans->map(function($br) {
    return [
        'id'           => $br->id,
        'ruangan_id'   => $br->ruangan_id,
        'ruangan_nama' => $br->ruangan->nama_ruangan ?? '-',
        'barang_nama'  => $br->barang->nama ?? '-',
        'qty'          => $br->qty,
    ];
})) !!};

// Unique ruangan
const uniqueRuangan = [...new Map(barangRuanganData.map(item =>
    [item.ruangan_id, {id: item.ruangan_id, nama: item.ruangan_nama}]
)).values()];

let barangRowCount = 0;

/**
 * Ambil semua barang_ruangan_id yang sudah dipilih di row lain
 * Opsional: exclude row tertentu
 */
function getSelectedBarangIds(excludeRow = null) {
    const selected = [];
    document.querySelectorAll('.barang-row').forEach(row => {
        if (row === excludeRow) return;
        const barangSelect = row.querySelector('.barang-select');
        if (barangSelect && barangSelect.value) {
            selected.push(parseInt(barangSelect.value));
        }
    });
    return selected;
}

/**
 * Hitung sisa stok suatu barang_ruangan_id
 * dengan memperhitungkan jumlah yang sudah diinput di row lain
 */
function getRemainingQty(barangRuanganId, excludeRow = null) {
    const br = barangRuanganData.find(b => b.id == barangRuanganId);
    if (!br) return 0;

    let usedQty = 0;
    document.querySelectorAll('.barang-row').forEach(row => {
        if (row === excludeRow) return;
        const barangSelect = row.querySelector('.barang-select');
        const jumlahInput  = row.querySelector('.jumlah-input');
        if (barangSelect && barangSelect.value == barangRuanganId && jumlahInput) {
            usedQty += parseInt(jumlahInput.value) || 0;
        }
    });

    return br.qty - usedQty;
}

/**
 * Refresh semua dropdown barang di semua row
 * supaya opsi yang stoknya habis (karena row lain) ikut diupdate
 */
function refreshAllBarangDropdowns(changedRow = null) {
    document.querySelectorAll('.barang-row').forEach(row => {
        if (row === changedRow) return; // skip row yang baru diubah

        const ruanganSelect = row.querySelector('.ruangan-select');
        const barangSelect  = row.querySelector('.barang-select');
        const stokInfo      = row.querySelector('.stok-info');
        const jumlahInput   = row.querySelector('.jumlah-input');

        if (!ruanganSelect || !ruanganSelect.value) return;

        const ruanganId      = ruanganSelect.value;
        const currentBarang  = barangSelect.value;

        // Filter barang by ruangan
        const filteredBarang = barangRuanganData.filter(br => br.ruangan_id == ruanganId && br.qty > 0);

        // Rebuild options — exclude barang yang stoknya 0 setelah dikurangi row lain
        barangSelect.innerHTML = '<option value="">-- Pilih Barang --</option>';

        filteredBarang.forEach(br => {
            const remaining = getRemainingQty(br.id, row);
            if (remaining <= 0 && br.id != currentBarang) return; // sembunyikan jika stok habis

            const option        = document.createElement('option');
            option.value        = br.id;
            option.textContent  = `${br.barang_nama} (Stok: ${remaining})`;
            option.dataset.qty  = remaining;
            option.dataset.nama = br.barang_nama;
            if (br.id == currentBarang) option.selected = true;
            barangSelect.appendChild(option);
        });

        // Update stok info & max jumlah jika ada barang dipilih
        if (currentBarang) {
            const remaining = getRemainingQty(currentBarang, row);
            jumlahInput.setAttribute('max', remaining);
            jumlahInput.dataset.maxStok = remaining;
            stokInfo.innerHTML = `<i class="fas fa-info-circle me-1 text-primary"></i>Stok tersedia: <strong class="text-success">${remaining}</strong> unit`;
        }
    });
}

/**
 * Filter barang berdasarkan ruangan yang dipilih
 */
function filterBarangByRuangan(selectElement) {
    const ruanganId    = selectElement.value;
    const row          = selectElement.closest('.barang-row');
    const barangSelect = row.querySelector('.barang-select');
    const stokInfo     = row.querySelector('.stok-info');
    const jumlahInput  = row.querySelector('.jumlah-input');

    // Reset
    barangSelect.innerHTML = '<option value="">-- Pilih Barang --</option>';
    stokInfo.innerHTML     = '';
    jumlahInput.value      = 1;
    jumlahInput.removeAttribute('max');
    jumlahInput.removeAttribute('data-max-stok');
    jumlahInput.removeAttribute('data-barang-nama');

    if (!ruanganId) {
        barangSelect.disabled  = true;
        barangSelect.innerHTML = '<option value="">-- Pilih Ruangan Dulu --</option>';
        return;
    }

    // Filter barang by ruangan & qty > 0
    const filteredBarang = barangRuanganData.filter(br => br.ruangan_id == ruanganId && br.qty > 0);

    if (filteredBarang.length === 0) {
        barangSelect.disabled  = true;
        barangSelect.innerHTML = '<option value="">-- Tidak Ada Barang Tersedia --</option>';
        Swal.fire({ icon: 'info', title: 'Stok Habis!', text: 'Semua barang di ruangan ini stoknya habis.', timer: 2000, showConfirmButton: false });
        return;
    }

    barangSelect.disabled = false;

    // Exclude barang yang stok sisanya 0 (sudah dipilih row lain dengan jumlah = max stok)
    let availableCount = 0;
    filteredBarang.forEach(br => {
        const remaining = getRemainingQty(br.id, row);
        if (remaining <= 0) return; // skip — stok habis terpakai row lain

        const option        = document.createElement('option');
        option.value        = br.id;
        option.textContent  = `${br.barang_nama} (Stok: ${remaining})`;
        option.dataset.qty  = remaining;
        option.dataset.nama = br.barang_nama;
        barangSelect.appendChild(option);
        availableCount++;
    });

    if (availableCount === 0) {
        barangSelect.disabled  = true;
        barangSelect.innerHTML = '<option value="">-- Semua Barang Sudah Dipilih --</option>';
        Swal.fire({ icon: 'info', title: 'Semua Barang Sudah Dipilih!', text: 'Semua barang di ruangan ini sudah ada di form.', timer: 2500, showConfirmButton: false });
    }
}

/**
 * Handle barang selection
 */
function handleBarangSelect(selectElement) {
    const selectedOption = selectElement.options[selectElement.selectedIndex];
    const row            = selectElement.closest('.barang-row');
    const stokInfo       = row.querySelector('.stok-info');
    const jumlahInput    = row.querySelector('.jumlah-input');

    if (!selectElement.value) {
        stokInfo.innerHTML = '';
        jumlahInput.removeAttribute('max');
        jumlahInput.removeAttribute('data-max-stok');
        jumlahInput.removeAttribute('data-barang-nama');
        refreshAllBarangDropdowns(row);
        return;
    }

    const remaining  = getRemainingQty(selectElement.value, row);
    const namaBarang = selectedOption.dataset.nama || '';

    jumlahInput.setAttribute('max', remaining);
    jumlahInput.dataset.maxStok    = remaining;
    jumlahInput.dataset.namaBarang = namaBarang;
    jumlahInput.value              = Math.min(parseInt(jumlahInput.value) || 1, remaining);

    stokInfo.innerHTML = `<i class="fas fa-info-circle me-1 text-primary"></i>Stok tersedia: <strong class="text-success">${remaining}</strong> unit`;

    // Refresh row lain supaya barang yang stoknya habis menghilang
    refreshAllBarangDropdowns(row);
}

/**
 * Validate jumlah input realtime
 */
function validateJumlahInput(inputElement) {
    const row        = inputElement.closest('.barang-row');
    const barangSelect = row.querySelector('.barang-select');
    const maxStok    = parseInt(inputElement.dataset.maxStok) || 0;
    const namaBarang = inputElement.dataset.namaBarang || 'Barang';
    const jumlah     = parseInt(inputElement.value) || 0;
    const errorMsg   = row.querySelector('.error-msg');

    errorMsg.style.display = 'none';
    inputElement.classList.remove('is-invalid');

    if (jumlah > maxStok) {
        errorMsg.textContent   = `⚠️ Stok tidak cukup! Tersedia: ${maxStok} unit`;
        errorMsg.style.display = 'block';
        inputElement.classList.add('is-invalid');

        Swal.fire({
            icon: 'warning',
            title: 'Stok Tidak Cukup!',
            html: `<div class="text-start">
                <p><strong>Barang:</strong> ${namaBarang}</p>
                <p><strong>Stok Tersedia:</strong> <span class="badge bg-success">${maxStok}</span> unit</p>
                <p><strong>Anda Input:</strong> <span class="badge bg-danger">${jumlah}</span> unit</p>
            </div>`,
            confirmButtonText: 'OK, Saya Paham',
            confirmButtonColor: '#3085d6',
        }).then(() => {
            inputElement.value             = maxStok;
            errorMsg.style.display         = 'none';
            inputElement.classList.remove('is-invalid');
        });

        return false;
    }

    // Setelah jumlah berubah, refresh row lain agar stok sisa terupdate
    if (barangSelect && barangSelect.value) {
        refreshAllBarangDropdowns(row);
    }

    return true;
}

/**
 * Add barang row
 */
function addBarangRow() {
    if (barangRuanganData.length === 0) {
        Swal.fire({ icon: 'error', title: 'Tidak Ada Barang!', text: 'Semua barang stoknya habis.', confirmButtonColor: '#d33' });
        return;
    }

    const container = document.getElementById('barang-container');

    let ruanganOptions = '<option value="">-- Pilih Ruangan --</option>';
    uniqueRuangan.forEach(ruangan => {
        ruanganOptions += `<option value="${ruangan.id}">${ruangan.nama}</option>`;
    });

    const template = `
        <div class="barang-row card mb-3 border-0 shadow-sm">
            <div class="card-body p-3">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">Ruangan <span class="text-danger">*</span></label>
                        <select class="form-select ruangan-select" onchange="filterBarangByRuangan(this)" required>
                            ${ruanganOptions}
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">Barang <span class="text-danger">*</span></label>
                        <select name="barang_ruangan_id[]" class="form-select barang-select" onchange="handleBarangSelect(this)" required disabled>
                            <option value="">-- Pilih Ruangan Dulu --</option>
                        </select>
                        <small class="stok-info text-muted"></small>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-semibold">Jumlah <span class="text-danger">*</span></label>
                        <input type="number" name="jumlah[]" class="form-control jumlah-input"
                               min="1" value="1" oninput="validateJumlahInput(this)" required>
                        <small class="error-msg text-danger" style="display:none;"></small>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="button" class="btn btn-danger w-100 remove-btn" onclick="removeBarangRow(this)">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;

    container.insertAdjacentHTML('beforeend', template);
    updateRemoveButtons();
}

/**
 * Remove barang row
 */
function removeBarangRow(button) {
    const rows = document.querySelectorAll('.barang-row');

    if (rows.length === 1) {
        Swal.fire({ icon: 'warning', title: 'Tidak Bisa Dihapus!', text: 'Minimal harus ada 1 barang.', confirmButtonColor: '#f39c12' });
        return;
    }

    const row = button.closest('.barang-row');
    row.remove();
    updateRemoveButtons();

    // Refresh semua row setelah row dihapus supaya stok kembali muncul
    refreshAllBarangDropdowns();
}

/**
 * Update remove button states
 */
function updateRemoveButtons() {
    const rows = document.querySelectorAll('.barang-row');
    rows.forEach(row => {
        const btn = row.querySelector('.remove-btn');
        if (rows.length === 1) {
            btn.disabled = true;
            btn.classList.add('opacity-50');
        } else {
            btn.disabled = false;
            btn.classList.remove('opacity-50');
        }
    });
}

// Sync min tanggal kembali
document.querySelector('input[name="tanggal_pinjam"]').addEventListener('change', function () {
    document.querySelector('input[name="tanggal_kembali"]').min = this.value;
});

// Submit validation
document.getElementById('formPeminjaman').addEventListener('submit', function (e) {
    const hasError = [...document.querySelectorAll('.jumlah-input')].some(i => i.classList.contains('is-invalid'));
    if (hasError) {
        e.preventDefault();
        Swal.fire({ icon: 'error', title: 'Validasi Gagal!', text: 'Masih ada jumlah barang yang melebihi stok.', confirmButtonColor: '#d33' });
    }
});

// Init
document.addEventListener('DOMContentLoaded', () => addBarangRow());
</script>
@endpush

<style>
.barang-row {
    transition: all 0.3s ease;
}
.barang-row:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1) !important;
}
.stok-info {
    display: block;
    margin-top: 0.25rem;
    font-size: 0.875rem;
}
.error-msg {
    display: block;
    margin-top: 0.25rem;
    font-size: 0.875rem;
}
.remove-btn.opacity-50 {
    opacity: 0.5;
    cursor: not-allowed;
}
</style>
@endsection