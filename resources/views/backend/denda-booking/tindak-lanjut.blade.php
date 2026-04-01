@extends('layouts.backend')
@section('title', 'Tindak Lanjut Denda Booking')

@section('content')
<div class="container-fluid px-4 py-4">
<div class="row justify-content-center">
<div class="col-lg-10">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">Tindak Lanjut Denda Booking</h2>
            <p class="text-muted mb-0">
                Ruangan: <strong>{{ $verifikasi->booking->ruangan->nama_ruangan ?? '-' }}</strong>
            </p>
        </div>
        <a href="{{ route('backend.denda-booking.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Kembali
        </a>
    </div>

    {{-- Info Booking --}}
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-header bg-primary text-white py-3">
            <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informasi Booking</h5>
        </div>
        <div class="card-body p-4">
            <div class="row g-3">
                <div class="col-md-6">
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <td width="40%" class="text-muted">Kode Booking</td>
                            <td width="5%">:</td>
                            <td><strong>{{ $verifikasi->booking->kode ?? '-' }}</strong></td>
                        </tr>
                        <tr>
                            <td class="text-muted">User</td>
                            <td>:</td>
                            <td><strong>{{ $verifikasi->booking->user->name ?? '-' }}</strong></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Ruangan</td>
                            <td>:</td>
                            <td><strong>{{ $verifikasi->booking->ruangan->nama_ruangan ?? '-' }}</strong></td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <td width="40%" class="text-muted">Tanggal</td>
                            <td width="5%">:</td>
                            <td>{{ \Carbon\Carbon::parse($verifikasi->booking->tanggal)->format('d M Y') }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Waktu</td>
                            <td>:</td>
                            <td>{{ $verifikasi->booking->waktu_mulai }} - {{ $verifikasi->booking->waktu_selesai }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">PIC Verifikasi</td>
                            <td>:</td>
                            <td><strong>{{ $verifikasi->pic->name ?? '-' }}</strong></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Hasil Verifikasi PIC --}}
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-header bg-white border-bottom py-3">
            <h5 class="mb-0"><i class="fas fa-clipboard-check text-primary me-2"></i>Hasil Verifikasi PIC</h5>
        </div>
        <div class="card-body p-4">
            <div class="row g-3 mb-3">
                <div class="col-md-4">
                    <small class="text-muted d-block">Kondisi Ruangan</small>
                    @if($verifikasi->kondisi_ruangan == 'kotor')
                        <span class="badge bg-warning text-dark px-3 py-2 mt-1">
                            <i class="fas fa-broom me-1"></i>Kotor
                        </span>
                    @else
                        <span class="badge bg-danger px-3 py-2 mt-1">
                            <i class="fas fa-tools me-1"></i>Rusak
                        </span>
                    @endif
                </div>
                <div class="col-md-4">
                    <small class="text-muted d-block">Tanggal Verifikasi</small>
                    <strong>{{ $verifikasi->tanggal_verifikasi_format }}</strong>
                </div>
                <div class="col-md-4">
                    <small class="text-muted d-block">Status</small>
                    <span class="badge bg-{{ $verifikasi->status_badge }} px-3 py-2 mt-1">
                        {{ $verifikasi->status_label }}
                    </span>
                </div>
            </div>

            @if($verifikasi->catatan_pic)
            <div class="bg-light rounded-3 p-3 mb-3">
                <h6 class="fw-bold mb-2">Catatan PIC:</h6>
                <p class="mb-0">{{ $verifikasi->catatan_pic }}</p>
            </div>
            @endif

            @if($verifikasi->foto_bukti && count($verifikasi->foto_bukti) > 0)
            <h6 class="fw-bold mb-2">Foto Bukti ({{ count($verifikasi->foto_bukti) }} foto):</h6>
            <div class="row g-2">
                @foreach($verifikasi->foto_bukti as $foto)
                <div class="col-4 col-md-2">
                    <a href="{{ asset('storage/' . $foto) }}" target="_blank">
                        <img src="{{ asset('storage/' . $foto) }}" class="img-thumbnail"
                             style="height: 100px; object-fit: cover; width: 100%;">
                    </a>
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>

    {{-- CASE 1: Belum ada denda → Form input denda --}}
    @if(!$verifikasi->denda)
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-header bg-warning text-dark py-3">
            <h5 class="mb-0"><i class="fas fa-calculator me-2"></i>Input Denda</h5>
        </div>
        <div class="card-body p-4">
            <div class="alert alert-info mb-4">
                <i class="fas fa-info-circle me-2"></i>
                Denda booking bersifat <strong>manual</strong> — admin tentukan nominal berdasarkan kerusakan/kebersihan ruangan.
            </div>

            <form action="{{ route('backend.denda-booking.store', $verifikasi->id) }}"
                  method="POST" id="formDenda">
                @csrf

                <div class="row mb-4">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            Jumlah Denda <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" name="jumlah_denda" id="jumlahDenda"
                                   class="form-control @error('jumlah_denda') is-invalid @enderror"
                                   value="{{ old('jumlah_denda', 0) }}"
                                   min="0" step="1000" required>
                        </div>
                        @error('jumlah_denda')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Tentukan nominal denda sesuai kerusakan</small>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            Status Pembayaran <span class="text-danger">*</span>
                        </label>
                        <div class="d-flex flex-column gap-2 mt-2">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="status_pembayaran"
                                       id="belumBayar" value="belum_bayar" checked>
                                <label class="form-check-label" for="belumBayar">
                                    <i class="fas fa-clock text-danger me-1"></i>Belum Bayar (User harus bayar)
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="status_pembayaran"
                                       id="dibebaskan" value="dibebaskan">
                                <label class="form-check-label" for="dibebaskan">
                                    <i class="fas fa-check-circle text-success me-1"></i>Dibebaskan (Tidak perlu bayar)
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold">Keterangan Denda</label>
                    <textarea name="keterangan_denda" rows="3" class="form-control"
                              placeholder="Detail kerusakan/kebersihan yang menjadi dasar denda (opsional)">{{ old('keterangan_denda') }}</textarea>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold">
                        Tindakan Admin <span class="text-danger">*</span>
                    </label>
                    <textarea name="tindakan_admin" rows="4"
                              class="form-control @error('tindakan_admin') is-invalid @enderror"
                              placeholder="Jelaskan tindakan yang diambil admin" required>{{ old('tindakan_admin') }}</textarea>
                    @error('tindakan_admin')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <hr class="my-4">
                <div class="d-flex justify-content-between">
                    <a href="{{ route('backend.denda-booking.index') }}" class="btn btn-secondary px-4">
                        <i class="fas fa-times me-2"></i>Batal
                    </a>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fas fa-save me-2"></i>Simpan Denda
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- CASE 2: Sudah ada denda → Info denda --}}
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
                        <div class="fw-bold fs-4 text-danger">{{ $verifikasi->denda->jumlah_denda_format }}</div>
                    </div>
                </div>
                <div class="col-md-4 text-center">
                    <div class="p-3 rounded-3 bg-light">
                        <small class="text-muted fw-semibold d-block">STATUS</small>
                        <span class="badge {{ $verifikasi->denda->status_pembayaran_badge }} fs-6 mt-1">
                            {{ $verifikasi->denda->status_pembayaran_label }}
                        </span>
                    </div>
                </div>
                <div class="col-md-4 text-center">
                    <div class="p-3 rounded-3 bg-light">
                        <small class="text-muted fw-semibold d-block">DITETAPKAN</small>
                        <div class="fw-bold">
                            {{ $verifikasi->denda->tanggal_tindakan
                                ? $verifikasi->denda->tanggal_tindakan->format('d M Y')
                                : '-' }}
                        </div>
                    </div>
                </div>
            </div>

            @if($verifikasi->denda->keterangan_denda)
            <div class="mt-3 p-3 bg-light rounded-3">
                <small class="text-muted fw-semibold">Keterangan:</small>
                <p class="mb-0 mt-1">{{ $verifikasi->denda->keterangan_denda }}</p>
            </div>
            @endif

            @if($verifikasi->denda->tindakan_admin)
            <div class="mt-2 p-3 bg-warning bg-opacity-10 rounded-3">
                <small class="text-warning fw-semibold">Tindakan Admin:</small>
                <p class="mb-0 mt-1">{{ $verifikasi->denda->tindakan_admin }}</p>
            </div>
            @endif
        </div>
    </div>
    @endif

    {{-- CASE 3: Menunggu verifikasi bukti → Approve/Reject --}}
    @if($verifikasi->denda && $verifikasi->denda->isMenungguVerifikasi())
    <div class="card border-0 shadow-sm rounded-3 mb-4"
         style="border-left: 4px solid #f39c12 !important;">
        <div class="card-header bg-warning text-dark py-3">
            <h5 class="mb-0">
                <i class="fas fa-hourglass-half me-2"></i>
                Bukti Pembayaran User — Perlu Verifikasi
            </h5>
        </div>
        <div class="card-body p-4">
            <div class="row g-4">
                <div class="col-md-5">
                    <h6 class="fw-bold mb-2">Foto Bukti Transfer:</h6>
                    @if($verifikasi->denda->bukti_pembayaran)
                        <a href="{{ Storage::url($verifikasi->denda->bukti_pembayaran) }}" target="_blank">
                            <img src="{{ Storage::url($verifikasi->denda->bukti_pembayaran) }}"
                                 class="img-fluid rounded-3 border"
                                 style="max-height: 300px; object-fit: contain; width: 100%;">
                        </a>
                        <small class="text-muted d-block mt-1">Klik foto untuk perbesar</small>
                    @endif
                </div>
                <div class="col-md-7">
                    <h6 class="fw-bold mb-3">Detail Pembayaran:</h6>
                    <table class="table table-sm table-borderless">
                        <tr>
                            <td class="text-muted" width="40%">Jumlah Denda</td>
                            <td>:</td>
                            <td class="fw-bold text-danger">{{ $verifikasi->denda->jumlah_denda_format }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Tanggal Transfer</td>
                            <td>:</td>
                            <td>
                                {{ $verifikasi->denda->tanggal_bayar
                                    ? $verifikasi->denda->tanggal_bayar->format('d M Y')
                                    : '-' }}
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Keterangan User</td>
                            <td>:</td>
                            <td>{{ $verifikasi->denda->keterangan_pembayaran ?? '-' }}</td>
                        </tr>
                    </table>

                    <hr>

                    <div class="alert alert-warning py-2 mb-3">
                        <i class="fas fa-exclamation-triangle me-1"></i>
                        Periksa foto bukti transfer sebelum approve.
                    </div>

                    <form action="{{ route('backend.denda-booking.approve-bukti', $verifikasi->denda->id) }}"
                          method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-success px-4"
                                onclick="return confirm('Konfirmasi pembayaran ini sebagai LUNAS?')">
                            <i class="fas fa-check me-2"></i>Approve — Tandai Lunas
                        </button>
                    </form>

                    <button type="button" class="btn btn-danger px-4 ms-2"
                            data-bs-toggle="modal" data-bs-target="#modalReject">
                        <i class="fas fa-times me-2"></i>Tolak Bukti
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Reject --}}
    <div class="modal fade" id="modalReject" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content rounded-3">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-times-circle me-2"></i>Tolak Bukti Pembayaran
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('backend.denda-booking.reject-bukti', $verifikasi->denda->id) }}"
                      method="POST">
                    @csrf
                    <div class="modal-body p-4">
                        <p class="text-muted mb-3">
                            Bukti akan dihapus dan status kembali ke <strong>Belum Bayar</strong>.
                        </p>
                        <label class="form-label fw-semibold">
                            Alasan Penolakan <span class="text-danger">*</span>
                        </label>
                        <textarea name="alasan_tolak" rows="3" class="form-control"
                                  placeholder="Contoh: Bukti tidak jelas, nominal tidak sesuai"
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
    const form = document.getElementById('formDenda');
    if (!form) return;

    document.getElementById('jumlahDenda').addEventListener('input', function () {
        this.value = this.value.replace(/\D/g, '');
    });

    form.addEventListener('submit', function (e) {
        const jumlah = document.getElementById('jumlahDenda').value;
        const status = document.querySelector('input[name="status_pembayaran"]:checked').value;
        const msg = status === 'belum_bayar'
            ? `Denda Rp ${parseInt(jumlah).toLocaleString('id-ID')} akan dibebankan ke user.\n\nLanjutkan?`
            : `User akan DIBEBASKAN dari denda.\n\nLanjutkan?`;
        if (!confirm(msg)) e.preventDefault();
    });
});
</script>
@endpush
@endsection