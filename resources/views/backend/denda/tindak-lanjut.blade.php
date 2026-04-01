@extends('layouts.backend')
@section('title', 'Tindak Lanjut & Input Denda')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">

            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold mb-1">Tindak Lanjut Verifikasi PIC</h2>
                    <p class="text-muted mb-0">Input denda untuk barang
                        {{ strtolower(str_replace('_', ' ', $pengembalian->verifikasi->kondisi)) }}</p>
                </div>
                <a href="{{ route('backend.denda.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Kembali
                </a>
            </div>

            <!-- Info Pengembalian -->
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-primary text-white py-3">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informasi Pengembalian</h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <table class="table table-sm table-borderless mb-0">
                                <tr>
                                    <td width="40%" class="text-muted">Kode</td>
                                    <td width="5%">:</td>
                                    <td><strong>{{ $pengembalian->peminjamanBarang->kode }}</strong></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Peminjam</td>
                                    <td>:</td>
                                    <td><strong>{{ $pengembalian->peminjamanBarang->nama_peminjam ?? $pengembalian->peminjamanBarang->user->name }}</strong></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Tanggal Kembali</td>
                                    <td>:</td>
                                    <td>{{ \Carbon\Carbon::parse($pengembalian->tanggal_kembali)->format('d M Y') }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-sm table-borderless mb-0">
                                <tr>
                                    <td width="40%" class="text-muted">PIC Verifikasi</td>
                                    <td width="5%">:</td>
                                    <td><strong>{{ $pengembalian->verifikasi->pic->name }}</strong></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Kondisi</td>
                                    <td>:</td>
                                    <td>
                                        @if($pengembalian->verifikasi->kondisi == 'rusak_ringan')
                                            <span class="badge bg-warning">Rusak Ringan</span>
                                        @elseif($pengembalian->verifikasi->kondisi == 'rusak_berat')
                                            <span class="badge bg-danger">Rusak Berat</span>
                                        @else
                                            <span class="badge bg-dark">Hilang</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Tanggal Verifikasi</td>
                                    <td>:</td>
                                    <td>{{ \Carbon\Carbon::parse($pengembalian->verifikasi->tanggal_verifikasi)->format('d M Y, H:i') }} WIB</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Hasil Verifikasi PIC -->
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0"><i class="fas fa-clipboard-check text-primary me-2"></i>Hasil Verifikasi PIC</h5>
                </div>
                <div class="card-body p-4">
                    @if($pengembalian->verifikasi->catatan_pic)
                        <div class="bg-light rounded-3 p-3 mb-3">
                            <h6 class="fw-bold mb-2">Catatan PIC:</h6>
                            <p class="mb-0" style="white-space: pre-line;">{{ $pengembalian->verifikasi->catatan_pic }}</p>
                        </div>
                    @endif

                    @if($pengembalian->verifikasi->foto_bukti && count($pengembalian->verifikasi->foto_bukti) > 0)
                        <h6 class="fw-bold mb-2">Foto Bukti:</h6>
                        <div class="row g-2">
                            @foreach($pengembalian->verifikasi->foto_bukti as $foto)
                                <div class="col-4 col-md-2">
                                    <img src="{{ asset('storage/' . $foto) }}" class="img-thumbnail"
                                        style="height: 100px; object-fit: cover; width: 100%;">
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            {{-- ══════════════════════════════════════════════════════
                 CASE 1: Belum ada denda → tampilkan form input denda
            ══════════════════════════════════════════════════════ --}}
            @if(!$pengembalian->denda)
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-warning text-dark py-3">
                    <h5 class="mb-0"><i class="fas fa-calculator me-2"></i>Perhitungan Denda</h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('backend.denda.store', $pengembalian->id) }}" method="POST" id="formDenda">
                        @csrf

                        <div class="alert alert-info mb-4">
                            <h6 class="fw-bold mb-2"><i class="fas fa-magic me-1"></i>Perhitungan Otomatis:</h6>
                            <div class="row g-2">
                                <div class="col-md-4">
                                    <small class="text-muted">Kondisi:</small>
                                    <div class="fw-bold">{{ ucwords(str_replace('_', ' ', $dendaSuggestion['kondisi'])) }}</div>
                                </div>
                                <div class="col-md-4">
                                    <small class="text-muted">Persentase:</small>
                                    <div class="fw-bold">{{ $dendaSuggestion['persentase'] }}%</div>
                                </div>
                                <div class="col-md-4">
                                    <small class="text-muted">Total Denda Saran:</small>
                                    <div class="fw-bold text-danger fs-5">Rp {{ number_format($dendaSuggestion['total'], 0, ',', '.') }}</div>
                                </div>
                            </div>
                        </div>

                        @if(count($dendaSuggestion['rincian']) > 0)
                        <div class="mb-4">
                            <h6 class="fw-bold mb-3">Rincian Per Barang:</h6>
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Barang</th>
                                            <th class="text-end">Harga Satuan</th>
                                            <th class="text-center">Jumlah</th>
                                            <th class="text-center">%</th>
                                            <th class="text-end">Denda</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($dendaSuggestion['rincian'] as $item)
                                        <tr>
                                            <td>{{ $item['nama_barang'] }}</td>
                                            <td class="text-end">Rp {{ number_format($item['harga_satuan'], 0, ',', '.') }}</td>
                                            <td class="text-center">{{ $item['jumlah'] }}</td>
                                            <td class="text-center">{{ $item['persentase'] }}%</td>
                                            <td class="text-end fw-bold">Rp {{ number_format($item['denda'], 0, ',', '.') }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="table-light">
                                        <tr>
                                            <th colspan="4" class="text-end">TOTAL:</th>
                                            <th class="text-end text-danger">Rp {{ number_format($dendaSuggestion['total'], 0, ',', '.') }}</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                        @endif

                        <hr class="my-4">

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Jumlah Denda <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" name="jumlah_denda" id="jumlahDenda"
                                        class="form-control @error('jumlah_denda') is-invalid @enderror"
                                        value="{{ old('jumlah_denda', $dendaSuggestion['total']) }}"
                                        min="0" step="1000" required>
                                </div>
                                @error('jumlah_denda')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Anda bisa edit manual jika perlu penyesuaian</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Tipe Perhitungan <span class="text-danger">*</span></label>
                                <select name="tipe_perhitungan" class="form-select" required>
                                    <option value="otomatis" selected>Otomatis ({{ $dendaSuggestion['persentase'] }}%)</option>
                                    <option value="manual">Manual (Disesuaikan)</option>
                                </select>
                                <input type="hidden" name="rincian_perhitungan" value="{{ json_encode($dendaSuggestion['rincian']) }}">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Status Pembayaran <span class="text-danger">*</span></label>
                            <div class="d-flex gap-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="status_pembayaran"
                                        id="belumBayar" value="belum_bayar" checked required>
                                    <label class="form-check-label" for="belumBayar">
                                        <i class="fas fa-clock text-danger me-1"></i>Belum Bayar
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="status_pembayaran"
                                        id="dibebaskan" value="dibebaskan" required>
                                    <label class="form-check-label" for="dibebaskan">
                                        <i class="fas fa-check-circle text-success me-1"></i>Dibebaskan
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Keterangan Denda</label>
                            <textarea name="keterangan_denda" rows="3" class="form-control"
                                placeholder="Alasan penetapan denda (opsional)">{{ old('keterangan_denda') }}</textarea>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Tindakan Admin <span class="text-danger">*</span></label>
                            <textarea name="tindakan_admin" rows="4"
                                class="form-control @error('tindakan_admin') is-invalid @enderror"
                                placeholder="Jelaskan tindakan yang diambil admin" required>{{ old('tindakan_admin') }}</textarea>
                            @error('tindakan_admin')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr class="my-4">
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('backend.denda.index') }}" class="btn btn-secondary px-4">
                                <i class="fas fa-times me-2"></i>Batal
                            </a>
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fas fa-save me-2"></i>Simpan Denda & Tindakan
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- ══════════════════════════════════════════════════════
                 CASE 2: Sudah ada denda → tampilkan info denda
            ══════════════════════════════════════════════════════ --}}
            @else
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0"><i class="fas fa-file-invoice-dollar text-danger me-2"></i>Info Denda</h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-4 text-center">
                            <div class="p-3 rounded-3 bg-danger bg-opacity-10">
                                <small class="text-danger fw-semibold d-block">TOTAL DENDA</small>
                                <div class="fw-bold fs-4 text-danger">{{ $pengembalian->denda->jumlah_denda_format }}</div>
                            </div>
                        </div>
                        <div class="col-md-4 text-center">
                            <div class="p-3 rounded-3 bg-light">
                                <small class="text-muted fw-semibold d-block">STATUS</small>
                                <span class="badge {{ $pengembalian->denda->status_pembayaran_badge }} fs-6 mt-1">
                                    {{ $pengembalian->denda->status_pembayaran_label }}
                                </span>
                            </div>
                        </div>
                        <div class="col-md-4 text-center">
                            <div class="p-3 rounded-3 bg-light">
                                <small class="text-muted fw-semibold d-block">DITETAPKAN</small>
                                <div class="fw-bold">{{ $pengembalian->denda->tanggal_tindakan->format('d M Y') }}</div>
                            </div>
                        </div>
                    </div>

                    @if($pengembalian->denda->keterangan_denda)
                    <div class="mt-3 p-3 bg-light rounded-3">
                        <small class="text-muted fw-semibold">Keterangan:</small>
                        <p class="mb-0 mt-1">{{ $pengembalian->denda->keterangan_denda }}</p>
                    </div>
                    @endif

                    @if($pengembalian->denda->tindakan_admin)
                    <div class="mt-2 p-3 bg-warning bg-opacity-10 rounded-3">
                        <small class="text-warning fw-semibold">Tindakan Admin:</small>
                        <p class="mb-0 mt-1">{{ $pengembalian->denda->tindakan_admin }}</p>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            {{-- ══════════════════════════════════════════════════════
                 CASE 3: Menunggu verifikasi → tampilkan bukti + approve/reject
                 (ini card TERPISAH, di luar semua form)
            ══════════════════════════════════════════════════════ --}}
            @if($pengembalian->denda && $pengembalian->denda->status_pembayaran === 'menunggu_verifikasi')
            <div class="card border-0 shadow-sm rounded-3 mb-4" style="border-left: 4px solid #f39c12 !important;">
                <div class="card-header bg-warning text-dark py-3">
                    <h5 class="mb-0">
                        <i class="fas fa-hourglass-half me-2"></i>
                        Bukti Pembayaran User — Perlu Verifikasi
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4">

                        {{-- Foto bukti --}}
                        <div class="col-md-5">
                            <h6 class="fw-bold mb-2">Foto Bukti Transfer:</h6>
                            @if($pengembalian->denda->bukti_pembayaran)
                                <a href="{{ Storage::url($pengembalian->denda->bukti_pembayaran) }}" target="_blank">
                                    <img src="{{ Storage::url($pengembalian->denda->bukti_pembayaran) }}"
                                        class="img-fluid rounded-3 border"
                                        style="max-height: 300px; object-fit: contain; width: 100%;">
                                </a>
                                <small class="text-muted d-block mt-1">Klik foto untuk perbesar</small>
                            @else
                                <div class="text-muted">Tidak ada foto</div>
                            @endif
                        </div>

                        {{-- Info + tombol --}}
                        <div class="col-md-7">
                            <h6 class="fw-bold mb-3">Detail Pembayaran:</h6>
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td class="text-muted" width="40%">Jumlah Denda</td>
                                    <td>:</td>
                                    <td class="fw-bold text-danger">{{ $pengembalian->denda->jumlah_denda_format }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Tanggal Transfer</td>
                                    <td>:</td>
                                    <td>
                                        {{ $pengembalian->denda->tanggal_bayar
                                            ? \Carbon\Carbon::parse($pengembalian->denda->tanggal_bayar)->format('d M Y')
                                            : '-' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Keterangan User</td>
                                    <td>:</td>
                                    <td>{{ $pengembalian->denda->keterangan_pembayaran ?? '-' }}</td>
                                </tr>
                            </table>

                            <hr>

                            <div class="alert alert-warning py-2 mb-3">
                                <i class="fas fa-exclamation-triangle me-1"></i>
                                Periksa foto bukti transfer sebelum approve.
                            </div>

                            {{-- Form Approve (berdiri sendiri) --}}
                            <form action="{{ route('backend.denda.approve-bukti', $pengembalian->denda->id) }}"
                                  method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-success px-4"
                                    onclick="return confirm('Konfirmasi pembayaran ini sebagai LUNAS?')">
                                    <i class="fas fa-check me-2"></i>Approve — Tandai Lunas
                                </button>
                            </form>

                            {{-- Trigger modal reject --}}
                            <button type="button" class="btn btn-danger px-4 ms-2"
                                    data-bs-toggle="modal" data-bs-target="#modalReject">
                                <i class="fas fa-times me-2"></i>Tolak Bukti
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Modal Reject (di luar semua form/card) --}}
            <div class="modal fade" id="modalReject" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content rounded-3">
                        <div class="modal-header bg-danger text-white">
                            <h5 class="modal-title">
                                <i class="fas fa-times-circle me-2"></i>Tolak Bukti Pembayaran
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <form action="{{ route('backend.denda.reject-bukti', $pengembalian->denda->id) }}" method="POST">
                            @csrf
                            <div class="modal-body p-4">
                                <p class="text-muted mb-3">
                                    Bukti akan dihapus dan status kembali ke <strong>Belum Bayar</strong>.
                                    User perlu upload ulang.
                                </p>
                                <label class="form-label fw-semibold">
                                    Alasan Penolakan <span class="text-danger">*</span>
                                </label>
                                <textarea name="alasan_tolak" rows="3" class="form-control"
                                    placeholder="Contoh: Bukti tidak jelas, nominal tidak sesuai, dll"
                                    required></textarea>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-danger px-4">
                                    <i class="fas fa-times me-2"></i>Tolak & Hapus Bukti
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            @endif

        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const formDenda = document.getElementById('formDenda');
    if (!formDenda) return; // kalau form tidak ada (denda sudah dibuat), skip

    document.getElementById('jumlahDenda').addEventListener('input', function () {
        this.value = this.value.replace(/\D/g, '');
    });

    formDenda.addEventListener('submit', function (e) {
        const jumlah = document.getElementById('jumlahDenda').value;
        const status = document.querySelector('input[name="status_pembayaran"]:checked').value;
        const msg = status === 'belum_bayar'
            ? `Denda Rp ${parseInt(jumlah).toLocaleString('id-ID')} akan dibebankan ke peminjam.\n\nLanjutkan?`
            : `Peminjam akan DIBEBASKAN dari denda.\n\nLanjutkan?`;
        if (!confirm(msg)) e.preventDefault();
    });
});
</script>
@endpush
@endsection