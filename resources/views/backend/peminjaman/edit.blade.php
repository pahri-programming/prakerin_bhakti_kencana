@extends('layouts.backend')
@section('title', 'Edit Peminjaman')

@section('content')
    <div class="container-fluid px-4 py-4">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <!-- Header -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h2 class="fw-bold mb-1">Edit Peminjaman Barang</h2>
                        <p class="text-muted mb-0">Perbarui data peminjaman {{ $peminjaman->kode }}</p>
                    </div>
                    <a href="{{ route('backend.peminjaman.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Kembali
                    </a>
                </div>

                <!-- Form Card -->
                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-body p-4">
                        <form action="{{ route('backend.peminjaman.update', $peminjaman->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <!-- Kode Peminjaman -->
                            <div class="mb-4">
                                <label class="form-label fw-semibold">Kode Peminjaman</label>
                                <input type="text" class="form-control bg-light" value="{{ $peminjaman->kode }}"
                                    readonly>
                            </div>

                            <!-- User & Info -->
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">User <span class="text-danger">*</span></label>
                                    <select name="user_id" class="form-select @error('user_id') is-invalid @enderror"
                                        required>
                                        <option value="">-- Pilih User --</option>
                                        @foreach ($users as $user)
                                            <option value="{{ $user->id }}"
                                                {{ old('user_id', $peminjaman->user_id) == $user->id ? 'selected' : '' }}>
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
                                        value="{{ old('nama_peminjam', $peminjaman->nama_peminjam) }}"
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
                                    value="{{ old('instansi', $peminjaman->instansi) }}"
                                    placeholder="Nama instansi/organisasi">
                                @error('instansi')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Barang Section (Editable) -->
                            <div class="mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <label class="form-label fw-semibold mb-0">
                                        Barang yang Dipinjam <span class="text-danger">*</span>
                                    </label>
                                    <button type="button" class="btn btn-sm btn-primary" onclick="addBarangRow()">
                                        <i class="fas fa-plus me-1"></i>Tambah Barang
                                    </button>
                                </div>

                                <div id="barang-container">
                                    @foreach ($peminjaman->detailbarangs as $index => $detail)
                                        <div class="barang-row card mb-2 p-3">
                                            <div class="row g-2 align-items-end">
                                                <div class="col-md-4">
                                                    <label class="form-label small">Ruangan <span
                                                            class="text-danger">*</span></label>
                                                    <select class="form-select ruangan-select"
                                                        onchange="filterBarangByRuangan(this)" required>
                                                        <option value="">-- Pilih Ruangan --</option>
                                                        @foreach ($barangRuangans->unique('ruangan_id') as $br)
                                                            <option value="{{ $br->ruangan->id }}"
                                                                {{ $detail->barangRuangan->ruangan_id == $br->ruangan->id ? 'selected' : '' }}>
                                                                {{ $br->ruangan->nama_ruangan }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label small">Barang <span
                                                            class="text-danger">*</span></label>
                                                    <select name="barang_ruangan_id[]" class="form-select barang-select"
                                                        required>
                                                        <option value="">-- Pilih Barang --</option>
                                                        @foreach ($barangRuangans->where('ruangan_id', $detail->barangRuangan->ruangan_id) as $br)
                                                            <option value="{{ $br->id }}"
                                                                {{ $detail->barang_ruangan_id == $br->id ? 'selected' : '' }}>
                                                                {{ $br->barang->nama }} (Stok: {{ $br->qty }})
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-2">
                                                    <label class="form-label small">Jumlah</label>
                                                    <input type="number" name="jumlah[]" class="form-control"
                                                        min="1" value="{{ $detail->jumlah }}" required>
                                                </div>
                                                <div class="col-md-2">
                                                    <button type="button" class="btn btn-danger w-100"
                                                        onclick="removeBarangRow(this)">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Tanggal -->
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Tanggal Pinjam <span
                                            class="text-danger">*</span></label>
                                    <input type="date" name="tanggal_pinjam"
                                        class="form-control @error('tanggal_pinjam') is-invalid @enderror"
                                        value="{{ old('tanggal_pinjam', \Carbon\Carbon::parse($peminjaman->tanggal_pinjam)->format('Y-m-d')) }}"
                                        required>
                                    @error('tanggal_pinjam')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Tanggal Kembali <span
                                            class="text-danger">*</span></label>
                                    <input type="date" name="tanggal_kembali"
                                        class="form-control @error('tanggal_kembali') is-invalid @enderror"
                                        value="{{ old('tanggal_kembali', \Carbon\Carbon::parse($peminjaman->tanggal_kembali)->format('Y-m-d')) }}"
                                        required>
                                    @error('tanggal_kembali')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>


                            <!-- Status -->
                            <div class="mb-4">
                                <label class="form-label fw-semibold">Status Peminjaman <span
                                        class="text-danger">*</span></label>
                                <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                                    <option value="menunggu"
                                        {{ old('status', $peminjaman->status) == 'menunggu' ? 'selected' : '' }}>
                                        Menunggu Persetujuan
                                    </option>
                                    <option value="disetujui"
                                        {{ old('status', $peminjaman->status) == 'disetujui' ? 'selected' : '' }}>
                                        Disetujui
                                    </option>
                                    <option value="ditolak"
                                        {{ old('status', $peminjaman->status) == 'ditolak' ? 'selected' : '' }}>
                                        Ditolak
                                    </option>
                                    <option value="dikembalikan"
                                        {{ old('status', $peminjaman->status) == 'dikembalikan' ? 'selected' : '' }}>
                                        Dikembalikan
                                    </option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">
                                    <i class="fas fa-exclamation-triangle me-1"></i>
                                    Perubahan status akan mempengaruhi stok barang
                                </small>
                            </div>

                            <!-- Keterangan -->
                            <div class="mb-4">
                                <label class="form-label fw-semibold">Keterangan</label>
                                <textarea name="keterangan" rows="3" class="form-control @error('keterangan') is-invalid @enderror"
                                    placeholder="Keperluan peminjaman...(opsional)">{{ old('keterangan', $peminjaman->keterangan) }}</textarea>
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
                                    <i class="fas fa-save me-2"></i>Update Peminjaman
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Data barang ruangan dari backend
        const barangRuanganData = {!! json_encode(
            $barangRuangans->map(function ($br) {
                return [
                    'id' => $br->id,
                    'ruangan_id' => $br->ruangan_id,
                    'barang_nama' => $br->barang->nama,
                    'qty' => $br->qty,
                ];
            }),
        ) !!};

        let barangRowCount = {{ count($peminjaman->detailbarangs) }};

        function filterBarangByRuangan(selectElement) {
            const ruanganId = selectElement.value;
            const row = selectElement.closest('.barang-row');
            const barangSelect = row.querySelector('.barang-select');

            // Reset barang select
            barangSelect.innerHTML = '<option value="">-- Pilih Barang --</option>';

            if (!ruanganId) {
                barangSelect.disabled = true;
                barangSelect.innerHTML = '<option value="">-- Pilih Ruangan Dulu --</option>';
                return;
            }

            // Filter barang berdasarkan ruangan
            const filteredBarang = barangRuanganData.filter(br => br.ruangan_id == ruanganId);

            if (filteredBarang.length === 0) {
                barangSelect.disabled = true;
                barangSelect.innerHTML = '<option value="">-- Tidak Ada Barang Tersedia --</option>';
                return;
            }

            // Populate barang options
            barangSelect.disabled = false;
            filteredBarang.forEach(br => {
                const option = document.createElement('option');
                option.value = br.id;
                option.textContent = `${br.barang_nama} (Stok: ${br.qty})`;
                barangSelect.appendChild(option);
            });
        }

        function addBarangRow() {
            const container = document.getElementById('barang-container');
            const ruanganOptions = document.querySelector('.ruangan-select').innerHTML;

            const template = `
        <div class="barang-row card mb-2 p-3">
            <div class="row g-2 align-items-end">
                <div class="col-md-4">
                    <label class="form-label small">Ruangan <span class="text-danger">*</span></label>
                    <select class="form-select ruangan-select" onchange="filterBarangByRuangan(this)" required>
                        ${ruanganOptions}
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label small">Barang <span class="text-danger">*</span></label>
                    <select name="barang_ruangan_id[]" class="form-select barang-select" required disabled>
                        <option value="">-- Pilih Ruangan Dulu --</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small">Jumlah</label>
                    <input type="number" name="jumlah[]" class="form-control" min="1" value="1" required>
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-danger w-100" onclick="removeBarangRow(this)">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>
    `;
            container.insertAdjacentHTML('beforeend', template);
            barangRowCount++;
            updateRemoveButtons();
        }

        function removeBarangRow(button) {
            button.closest('.barang-row').remove();
            barangRowCount--;
            updateRemoveButtons();
        }

        function updateRemoveButtons() {
            const rows = document.querySelectorAll('.barang-row');
            rows.forEach((row, index) => {
                const removeBtn = row.querySelector('button[onclick*="removeBarangRow"]');
                if (rows.length === 1) {
                    removeBtn.disabled = true;
                } else {
                    removeBtn.disabled = false;
                }
            });
        }

        // Set min date for tanggal_kembali based on tanggal_pinjam
        document.querySelector('input[name="tanggal_pinjam"]').addEventListener('change', function() {
            document.querySelector('input[name="tanggal_kembali"]').min = this.value;
        });

        // Initialize min date on page load
        window.addEventListener('DOMContentLoaded', function() {
            const tanggalPinjam = document.querySelector('input[name="tanggal_pinjam"]').value;
            if (tanggalPinjam) {
                document.querySelector('input[name="tanggal_kembali"]').min = tanggalPinjam;
            }

            // Update remove buttons on load
            updateRemoveButtons();
        });
    </script>
@endsection
