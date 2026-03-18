{{--
    modal-detail-booking.blade.php
    ─────────────────────────────
    Partial modal detail per-record dengan support multiple photos carousel.
    Di-include di LUAR loop (setelah tabel), bukan di dalam <tbody>.

    Variabel yang diharapkan: $verifikasi  (instance VerifikasiBooking)
--}}

<div class="modal fade" id="detailModalBooking{{ $verifikasi->id }}" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">

            <!-- Header -->
            <div class="modal-header" style="background:linear-gradient(135deg,#2980b9,#8e44ad);color:#fff;">
                <h5 class="modal-title">
                    <i class="ti ti-clipboard-check"></i> Detail Verifikasi Booking
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div class="row">
                    <!-- Left Column: Info -->
                    <div class="col-lg-8">
                        <!-- Info Booking -->
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="ti ti-info-circle"></i> Informasi Booking</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <table class="table table-sm table-borderless mb-0">
                                            <tr>
                                                <td width="40%" class="fw-semibold text-muted">Kode Booking</td>
                                                <td><span class="badge bg-info">{{ $verifikasi->booking->kode ?? '–' }}</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="fw-semibold text-muted">Nama User</td>
                                                <td>{{ $verifikasi->booking->user->name ?? 'User Dihapus' }}</td>
                                            </tr>
                                            <tr>
                                                <td class="fw-semibold text-muted">Email</td>
                                                <td>{{ $verifikasi->booking->user->email ?? '–' }}</td>
                                            </tr>
                                            <tr>
                                                <td class="fw-semibold text-muted">Instansi</td>
                                                <td>{{ $verifikasi->booking->user->instansi ?? '–' }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="col-md-6">
                                        <table class="table table-sm table-borderless mb-0">
                                            <tr>
                                                <td width="40%" class="fw-semibold text-muted">Ruangan</td>
                                                <td>{{ $verifikasi->booking->ruangan->nama_ruangan ?? 'Ruangan Dihapus' }}</td>
                                            </tr>
                                            <tr>
                                                <td class="fw-semibold text-muted">Lokasi</td>
                                                <td>{{ $verifikasi->booking->ruangan->lokasi ?? '–' }}</td>
                                            </tr>
                                            <tr>
                                                <td class="fw-semibold text-muted">Kapasitas</td>
                                                <td>{{ $verifikasi->booking->ruangan->kapasitas ?? '–' }} orang</td>
                                            </tr>
                                            <tr>
                                                <td class="fw-semibold text-muted">Tanggal</td>
                                                <td>{{ \Carbon\Carbon::parse($verifikasi->booking->tanggal)->translatedFormat('d F Y') }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="fw-semibold text-muted">Waktu</td>
                                                <td>{{ substr($verifikasi->booking->waktu_mulai, 0, 5) }} –
                                                    {{ substr($verifikasi->booking->waktu_selesai, 0, 5) }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>

                                @if ($verifikasi->booking->keperluan)
                                    <div class="mt-2 p-2 bg-light rounded">
                                        <small class="text-muted"><strong>Keperluan:</strong>
                                            {{ $verifikasi->booking->keperluan }}</small>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Hasil Verifikasi PIC -->
                        <div class="card mb-3">
                            <div class="card-header" style="background:#3498db;color:#fff;">
                                <h6 class="mb-0"><i class="ti ti-clipboard-check"></i> Hasil Verifikasi dari PIC</h6>
                            </div>
                            <div class="card-body">
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <p class="mb-1 fw-semibold">PIC yang Melakukan Pengecekan</p>
                                        <p class="text-muted mb-0">
                                            <i class="ti ti-user-check"></i> {{ $verifikasi->pic->name }}<br>
                                            <small>{{ $verifikasi->pic->email }}</small>
                                        </p>
                                    </div>
                                    <div class="col-md-6">
                                        <p class="mb-1 fw-semibold">Tanggal & Waktu Verifikasi</p>
                                        <p class="text-muted mb-0">
                                            <i class="ti ti-calendar"></i> {{ $verifikasi->tanggal_verifikasi_format }}
                                        </p>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <p class="mb-1 fw-semibold">Kondisi Ruangan</p>
                                        <span
                                            class="badge bg-{{ $verifikasi->kondisi_badge }} fs-6">{{ $verifikasi->kondisi_label }}</span>
                                    </div>
                                    <div class="col-md-6">
                                        <p class="mb-1 fw-semibold">Status Verifikasi</p>
                                        <span
                                            class="badge bg-{{ $verifikasi->status_badge }} fs-6">{{ $verifikasi->status_label }}</span>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <p class="mb-1"><strong>Catatan dari PIC:</strong></p>
                                    <div class="p-3 bg-light border rounded">
                                        <i class="ti ti-message me-2 text-primary"></i>
                                        <span class="text-dark">{{ $verifikasi->catatan_pic ?? 'Tidak ada catatan' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tindakan Admin -->
                        <div class="card border-warning">
                            <div class="card-header bg-warning bg-opacity-10">
                                <h6 class="mb-0"><i class="ti ti-edit"></i> Tindakan Admin</h6>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('backend.verifikasi.booking.tindakan', $verifikasi->id) }}"
                                    method="POST">
                                    @csrf
                                    @method('PUT')

                                    <div class="mb-3">
                                        <label class="form-label form-label-sm">
                                            Status Verifikasi <span class="text-danger">*</span>
                                        </label>
                                        <select name="status_verifikasi" class="form-select form-select-sm" required>
                                            <option value="">Pilih Status</option>
                                            <option value="diterima"
                                                {{ $verifikasi->status_verifikasi == 'diterima' ? 'selected' : '' }}>✅
                                                Diterima</option>
                                            <option value="perlu_tindakan"
                                                {{ $verifikasi->status_verifikasi == 'perlu_tindakan' ? 'selected' : '' }}>⚡
                                                Perlu Tindakan Lanjut</option>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label form-label-sm">
                                            Keterangan Tindakan <span class="text-danger">*</span>
                                        </label>
                                        <textarea name="tindakan_admin" class="form-control form-control-sm" rows="3"
                                            placeholder="Misal: Tagih biaya penggantian Rp 5.000.000 ke user …" required>{{ $verifikasi->tindakan_admin }}</textarea>
                                    </div>

                                    <button type="submit" class="btn btn-primary btn-sm w-100">
                                        <i class="ti ti-device-floppy"></i> Simpan Tindakan
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: Photos -->
                    <div class="col-lg-4">
                        @if($verifikasi->foto_bukti && count($verifikasi->foto_bukti_urls) > 0)
                        <div class="card">
                            <div class="card-header bg-info text-white">
                                <h6 class="mb-0">
                                    <i class="ti ti-photo"></i> Foto Bukti 
                                    <span class="badge bg-white text-info">{{ $verifikasi->total_photos }}</span>
                                </h6>
                            </div>
                            <div class="card-body p-2">
                                @if(count($verifikasi->foto_bukti_urls) > 1)
                                    <!-- Carousel untuk multiple photos -->
                                    <div id="carouselModal{{ $verifikasi->id }}" class="carousel slide" data-bs-ride="false">
                                        <div class="carousel-indicators">
                                            @foreach($verifikasi->foto_bukti_urls as $index => $url)
                                            <button type="button" 
                                                    data-bs-target="#carouselModal{{ $verifikasi->id }}" 
                                                    data-bs-slide-to="{{ $index }}" 
                                                    class="{{ $index === 0 ? 'active' : '' }}"></button>
                                            @endforeach
                                        </div>

                                        <div class="carousel-inner" style="border-radius: 8px; overflow: hidden;">
                                            @foreach($verifikasi->foto_bukti_urls as $index => $url)
                                            <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                                                <img src="{{ $url }}" 
                                                     class="d-block w-100" 
                                                     alt="Foto {{ $index + 1 }}"
                                                     style="height: 300px; object-fit: cover;">
                                                <div class="carousel-caption d-none d-md-block" 
                                                     style="background: rgba(0,0,0,0.5); border-radius: 4px; padding: 5px 10px;">
                                                    <p class="mb-0">Foto {{ $index + 1 }} dari {{ count($verifikasi->foto_bukti_urls) }}</p>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>

                                        <button class="carousel-control-prev" type="button" 
                                                data-bs-target="#carouselModal{{ $verifikasi->id }}" 
                                                data-bs-slide="prev">
                                            <span class="carousel-control-prev-icon"></span>
                                        </button>
                                        <button class="carousel-control-next" type="button" 
                                                data-bs-target="#carouselModal{{ $verifikasi->id }}" 
                                                data-bs-slide="next">
                                            <span class="carousel-control-next-icon"></span>
                                        </button>
                                    </div>

                                    <!-- Thumbnails -->
                                    <div class="d-flex gap-1 mt-2 flex-wrap justify-content-center">
                                        @foreach($verifikasi->foto_bukti_urls as $index => $url)
                                        <img src="{{ $url }}" 
                                             class="rounded border {{ $index === 0 ? 'border-primary border-2' : '' }}"
                                             style="width: 60px; height: 60px; object-fit: cover; cursor: pointer;"
                                             onclick="goToSlideModal{{ $verifikasi->id }}({{ $index }})"
                                             alt="Thumb {{ $index + 1 }}">
                                        @endforeach
                                    </div>

                                    <script>
                                    function goToSlideModal{{ $verifikasi->id }}(index) {
                                        const carousel = new bootstrap.Carousel(document.getElementById('carouselModal{{ $verifikasi->id }}'));
                                        carousel.to(index);
                                    }
                                    </script>
                                @else
                                    <!-- Single photo -->
                                    <img src="{{ $verifikasi->foto_bukti_urls[0] }}"
                                         class="img-fluid rounded border" 
                                         alt="Foto Bukti"
                                         style="max-height: 350px; width: 100%; object-fit: cover;">
                                @endif

                                <small class="text-muted d-block mt-2 text-center">
                                    <i class="ti ti-info-circle"></i> Dokumentasi kondisi ruangan
                                </small>
                            </div>
                        </div>
                        @else
                        <div class="card">
                            <div class="card-body text-center text-muted">
                                <i class="ti ti-photo-off" style="font-size: 3rem;"></i>
                                <p class="mb-0 mt-2">Tidak ada foto bukti</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div><!-- /modal-body -->

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">
                    <i class="ti ti-x"></i> Tutup
                </button>
            </div>
        </div>
    </div>
</div>