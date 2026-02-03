{{-- Modal Detail Verifikasi Peminjaman --}}
<div class="modal fade" id="detailModal{{ $verifikasi->id }}" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="ti ti-clipboard-check"></i> 
                    Detail Laporan Verifikasi PIC
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                
                {{-- Info Peminjaman --}}
                <div class="card mb-3">
                    <div class="card-header bg-light">
                        <h6 class="mb-0"><i class="ti ti-info-circle"></i> Informasi Peminjaman</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <td width="40%"><strong>Kode Peminjaman:</strong></td>
                                        <td><span class="badge bg-primary">{{ $verifikasi->peminjaman->kode }}</span></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Nama User:</strong></td>
                                        <td>{{ $verifikasi->peminjaman->user->name ?? 'User Dihapus' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Email:</strong></td>
                                        <td>{{ $verifikasi->peminjaman->user->email ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Instansi:</strong></td>
                                        <td>{{ $verifikasi->peminjaman->user->instansi ?? '-' }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <td width="40%"><strong>Tanggal Pinjam:</strong></td>
                                        <td>{{ $verifikasi->peminjaman->tanggal_pinjam_format }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Tanggal Kembali:</strong></td>
                                        <td>{{ $verifikasi->peminjaman->tanggal_kembali_format }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Barang:</strong></td>
                                        <td>{{ $verifikasi->peminjaman->barang_summary }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Total Jumlah:</strong></td>
                                        <td><span class="badge bg-dark">{{ $verifikasi->peminjaman->total_jumlah }} Item</span></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Hasil Verifikasi PIC --}}
                <div class="card mb-3">
                    <div class="card-header bg-info text-white">
                        <h6 class="mb-0"><i class="ti ti-clipboard-check"></i> Hasil Verifikasi dari PIC</h6>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <p class="mb-1"><strong>PIC yang Melakukan Pengecekan:</strong></p>
                                <p class="text-muted">
                                    <i class="ti ti-user-check"></i> {{ $verifikasi->pic->name }}<br>
                                    <small>{{ $verifikasi->pic->email }}</small>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Tanggal & Waktu Verifikasi:</strong></p>
                                <p class="text-muted">
                                    <i class="ti ti-calendar"></i> {{ $verifikasi->tanggal_verifikasi_format }}
                                </p>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Kondisi Barang:</strong></p>
                                @php
                                    $badge = match($verifikasi->kondisi) {
                                        'baik' => 'success',
                                        'rusak_ringan' => 'warning',
                                        'rusak_berat' => 'danger',
                                        'hilang' => 'dark',
                                        default => 'secondary',
                                    };
                                @endphp
                                <h5><span class="badge bg-{{ $badge }}">{{ $verifikasi->kondisi_label }}</span></h5>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Status Verifikasi:</strong></p>
                                @php
                                    $statusBadge = match($verifikasi->status_verifikasi) {
                                        'pending' => 'warning',
                                        'diterima' => 'success',
                                        'perlu_tindakan' => 'danger',
                                        default => 'secondary',
                                    };
                                @endphp
                                <h5><span class="badge bg-{{ $statusBadge }}">{{ $verifikasi->status_label }}</span></h5>
                            </div>
                        </div>

                        <div class="mb-3">
                            <p class="mb-1"><strong>Catatan dari PIC:</strong></p>
                            <div class="alert alert-light">
                                <i class="ti ti-message"></i> {{ $verifikasi->catatan_pic ?? '-' }}
                            </div>
                        </div>

                        @if($verifikasi->foto_bukti)
                        <div class="mb-3">
                            <p class="mb-1"><strong>Foto Bukti:</strong></p>
                            <img src="{{ asset('storage/' . $verifikasi->foto_bukti) }}" 
                                 class="img-fluid rounded border" 
                                 alt="Foto Bukti"
                                 style="max-height: 300px;">
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Form Tindakan Admin --}}
                <div class="card border-warning">
                    <div class="card-header bg-warning bg-opacity-10">
                        <h6 class="mb-0"><i class="ti ti-edit"></i> Tindakan Admin</h6>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('backend.verifikasi.laporan.peminjaman.tindakan', $verifikasi->id) }}" method="POST">
                            @csrf
                            
                            <div class="mb-3">
                                <label class="form-label">Status Verifikasi <span class="text-danger">*</span></label>
                                <select name="status_verifikasi" class="form-select" required>
                                    <option value="">Pilih Status</option>
                                    <option value="diterima" {{ $verifikasi->status_verifikasi == 'diterima' ? 'selected' : '' }}>
                                        ✅ Diterima (Tidak ada masalah)
                                    </option>
                                    <option value="perlu_tindakan" {{ $verifikasi->status_verifikasi == 'perlu_tindakan' ? 'selected' : '' }}>
                                        ⚡ Perlu Tindakan Lanjut
                                    </option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Tindakan Lanjut <span class="text-danger">*</span></label>
                                <textarea name="tindakan_admin" 
                                          class="form-control" 
                                          rows="4" 
                                          placeholder="Contoh: Tagih biaya penggantian Rp 5.000.000 ke user. Ganti dengan unit baru."
                                          required>{{ $verifikasi->tindakan_admin }}</textarea>
                                <small class="text-muted">
                                    💡 Jelaskan tindakan yang akan diambil untuk menindaklanjuti hasil verifikasi dari PIC.
                                </small>
                            </div>

                            @if($verifikasi->tindakan_admin)
                            <div class="alert alert-info">
                                <strong><i class="ti ti-info-circle"></i> Tindakan Sebelumnya:</strong><br>
                                {{ $verifikasi->tindakan_admin }}
                            </div>
                            @endif

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">
                                    <i class="ti ti-device-floppy"></i> Simpan Tindakan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="ti ti-x"></i> Tutup
                </button>
            </div>
        </div>
    </div>
</div>