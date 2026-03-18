@extends('layouts.backend')
@section('title', 'Edit Pengembalian Barang')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold mb-1">Edit Pengembalian Barang</h2>
                    <p class="text-muted mb-0">Perbarui data pengembalian</p>
                </div>
                <a href="{{ route('backend.pengembalian.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Kembali
                </a>
            </div>

            {{-- Alert Proteksi jika sudah diverifikasi --}}
            @if($pengembalian->isVerified())
                <div class="alert alert-danger mb-4">
                    <div class="d-flex align-items-start">
                        <i class="fas fa-lock me-3 fs-4"></i>
                        <div>
                            <h6 class="fw-bold mb-2">Pengembalian Sudah Diverifikasi</h6>
                            <p class="mb-2">Data ini sudah diverifikasi oleh PIC dan tidak dapat diedit.</p>
                            <a href="{{ route('backend.pengembalian.show', $pengembalian->id) }}" 
                               class="btn btn-sm btn-primary">
                                <i class="fas fa-eye me-1"></i>Lihat Detail
                            </a>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Form Card -->
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-4">
                    <form action="{{ route('backend.pengembalian.update', $pengembalian->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Info Peminjaman (Read Only) -->
                        <div class="alert alert-info mb-4">
                            <h6 class="fw-bold mb-2">
                                <i class="fas fa-info-circle me-2"></i>Informasi Peminjaman
                            </h6>
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <strong>Kode:</strong><br>
                                    {{ $pengembalian->peminjamanBarang->kode }}
                                </div>
                                <div class="col-md-3">
                                    <strong>Peminjam:</strong><br>
                                    {{ $pengembalian->peminjamanBarang->nama_peminjam ?? $pengembalian->peminjamanBarang->user->name }}
                                </div>
                                <div class="col-md-3">
                                    <strong>Tanggal Pinjam:</strong><br>
                                    {{ \Carbon\Carbon::parse($pengembalian->peminjamanBarang->tanggal_pinjam)->format('d M Y') }}
                                </div>
                                <div class="col-md-3">
                                    <strong>Status Peminjaman:</strong><br>
                                    <span class="badge bg-info">{{ ucfirst($pengembalian->peminjamanBarang->status) }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Status Awal Barang (Radio Button) -->
                        <div class="mb-4">
                            <h5 class="fw-bold mb-3">
                                <i class="fas fa-clipboard-check me-2 text-primary"></i>Status Awal Barang
                            </h5>
                            <div class="alert alert-warning mb-3">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                Ubah status awal hanya jika terjadi kesalahan pencatatan awal.
                            </div>
                            
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="5%">No</th>
                                            <th width="30%">Nama Barang</th>
                                            <th width="12%" class="text-center">Jumlah</th>
                                            <th width="28%">Status Awal <span class="text-danger">*</span></th>
                                            <th width="25%">Keterangan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($pengembalian->detailpengembalians as $index => $detail)
                                            <tr>
                                                <td class="text-center">
                                                    <div class="badge bg-primary rounded-circle p-2" style="width: 35px; height: 35px; display: flex; align-items: center; justify-content: center;">
                                                        {{ $index + 1 }}
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <i class="fas fa-box text-success me-2 fs-5"></i>
                                                        <div>
                                                            <strong>{{ $detail->barang->nama ?? '-' }}</strong>
                                                        </div>
                                                    </div>
                                                    <input type="hidden" name="detail_id[]" value="{{ $detail->id }}">
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge bg-primary px-3 py-2 fs-6">{{ $detail->jumlah }} Unit</span>
                                                </td>
                                                <td>
                                                    <div class="btn-group w-100" role="group">
                                                        <input type="radio" 
                                                               class="btn-check status-radio" 
                                                               name="status_awal_radio_{{ $index }}" 
                                                               id="baik_{{ $index }}" 
                                                               value="baik" 
                                                               {{ old("status_awal.$index", $detail->status_awal) == 'baik' ? 'checked' : '' }}
                                                               onchange="document.getElementById('hidden_status_{{ $index }}').value = 'baik'; updateStatusInfo({{ $index }}, 'baik')">
                                                        <label class="btn btn-outline-success" for="baik_{{ $index }}">
                                                            <i class="fas fa-check-circle me-1"></i>Baik
                                                        </label>

                                                        <input type="radio" 
                                                               class="btn-check status-radio" 
                                                               name="status_awal_radio_{{ $index }}" 
                                                               id="bermasalah_{{ $index }}" 
                                                               value="bermasalah"
                                                               {{ old("status_awal.$index", $detail->status_awal) == 'bermasalah' ? 'checked' : '' }}
                                                               onchange="document.getElementById('hidden_status_{{ $index }}').value = 'bermasalah'; updateStatusInfo({{ $index }}, 'bermasalah')">
                                                        <label class="btn btn-outline-warning" for="bermasalah_{{ $index }}">
                                                            <i class="fas fa-exclamation-triangle me-1"></i>Bermasalah
                                                        </label>
                                                    </div>
                                                    <input type="hidden" name="status_awal[]" id="hidden_status_{{ $index }}" 
                                                           value="{{ old("status_awal.$index", $detail->status_awal) }}">
                                                </td>
                                                <td>
                                                    <small class="text-muted" id="status-info-{{ $index }}">
                                                        @if($detail->status_awal == 'baik')
                                                            <i class="fas fa-check-circle text-success me-1"></i>Tidak ada masalah
                                                        @else
                                                            <i class="fas fa-exclamation-triangle text-warning me-1"></i>Ada masalah, perlu verifikasi PIC
                                                        @endif
                                                    </small>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Barang Ruangan ID (Hidden) -->
                        <input type="hidden" name="barang_ruangan_id" value="{{ $pengembalian->barang_ruangan_id }}">

                        <!-- Tanggal Kembali -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Tanggal Kembali <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal_kembali" 
                                   class="form-control @error('tanggal_kembali') is-invalid @enderror" 
                                   value="{{ old('tanggal_kembali', \Carbon\Carbon::parse($pengembalian->tanggal_kembali)->format('Y-m-d')) }}" 
                                   required>
                            @error('tanggal_kembali')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Keterangan -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Keterangan</label>
                            <textarea name="keterangan" rows="3" 
                                      class="form-control @error('keterangan') is-invalid @enderror" 
                                      placeholder="Catatan tambahan (opsional)">{{ old('keterangan', $pengembalian->keterangan) }}</textarea>
                            @error('keterangan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Info Alert -->
                        <div class="alert alert-info">
                            <strong><i class="fas fa-info-circle me-2"></i>Catatan:</strong>
                            <ul class="mb-0 mt-2">
                                <li>Ubah status awal hanya jika terjadi kesalahan pencatatan awal</li>
                                <li>Jika ada barang <span class="badge bg-warning">Bermasalah</span>, PIC akan melakukan verifikasi detail</li>
                                <li>Status pengembalian akan otomatis diupdate berdasarkan status awal</li>
                                <li>Data yang sudah diverifikasi PIC tidak dapat diedit</li>
                            </ul>
                        </div>

                        <!-- Buttons -->
                        <hr class="my-4">
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('backend.pengembalian.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>Batal
                            </a>
                            <button type="submit" class="btn btn-primary px-4" {{ $pengembalian->isVerified() ? 'disabled' : '' }}>
                                <i class="fas fa-save me-2"></i>Update Pengembalian
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.table-hover tbody tr:hover {
    background-color: #f8f9fa;
    transition: all 0.2s;
}

.btn-check:checked + .btn-outline-success {
    background-color: #198754;
    color: white;
}

.btn-check:checked + .btn-outline-warning {
    background-color: #ffc107;
    color: #000;
}

.btn-group label {
    cursor: pointer;
}
</style>

<script>
function updateStatusInfo(index, status) {
    const infoElement = document.getElementById(`status-info-${index}`);
    
    let icon = '';
    let text = '';
    let colorClass = '';
    
    if (status === 'baik') {
        icon = '<i class="fas fa-check-circle text-success me-1"></i>';
        text = 'Tidak ada masalah';
        colorClass = 'text-success';
    } else {
        icon = '<i class="fas fa-exclamation-triangle text-warning me-1"></i>';
        text = 'Ada masalah, perlu verifikasi PIC';
        colorClass = 'text-warning';
    }
    
    if (infoElement) {
        infoElement.innerHTML = icon + text;
        infoElement.className = 'small ' + colorClass;
    }
}
</script>
@endsection