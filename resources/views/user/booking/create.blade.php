@extends('layouts.frontend')
@section('title', 'Ajukan Booking Ruangan')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">

                {{-- Header --}}
                <div class="mb-4">
                    <a href="{{ Route('user.booking.index') }}" class="text-muted text-decoration-none small">
                        <i class="fas fa-arrow-left me-1"></i>Kembali
                    </a>
                    <h4 class="fw-bold mt-2 mb-0">Ajukan Booking Ruangan</h4>
                    <p class="text-muted small">Isi form di bawah untuk mengajukan booking ruangan</p>
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

                <form action="{{ route('user.booking.store') }}" method="POST" id="formBooking">
                    @csrf

                    {{-- CARD: Ruangan --}}
                    <div class="card border-0 shadow-sm rounded-3 mb-4">
                        <div class="card-header bg-white border-bottom py-3 px-4">
                            <h6 class="fw-bold mb-0">
                                <i class="fas fa-door-open me-2 text-primary"></i>Pilih Ruangan
                            </h6>
                        </div>
                        <div class="card-body p-4">
                            <label class="form-label fw-semibold small">Ruangan <span class="text-danger">*</span></label>
                            <select name="ruang_id" id="ruang_id"
                                class="form-select @error('ruang_id') is-invalid @enderror" required>
                                <option value="">-- Pilih Ruangan --</option>
                                @foreach ($ruangan as $r)
                                    <option value="{{ $r->id }}" {{ old('ruang_id') == $r->id ? 'selected' : '' }}>
                                        {{ $r->nama_ruangan }}
                                        @if ($r->kapasitas)
                                            (Kapasitas: {{ $r->kapasitas }})
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('ruang_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- CARD: Jadwal --}}
                    <div class="card border-0 shadow-sm rounded-3 mb-4">
                        <div class="card-header bg-white border-bottom py-3 px-4">
                            <h6 class="fw-bold mb-0">
                                <i class="fas fa-calendar-alt me-2 text-primary"></i>Jadwal Booking
                            </h6>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-3">

                                {{-- Tanggal --}}
                                <div class="col-12">
                                    <label class="form-label fw-semibold small">
                                        Tanggal <span class="text-danger">*</span>
                                    </label>
                                    <input type="date" name="tanggal" id="tanggal"
                                        class="form-control @error('tanggal') is-invalid @enderror"
                                        value="{{ old('tanggal', date('Y-m-d')) }}" min="{{ date('Y-m-d') }}" required>
                                    @error('tanggal')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Waktu Mulai --}}
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small">
                                        Waktu Mulai <span class="text-danger">*</span>
                                    </label>
                                    <input type="time" name="waktu_mulai" id="waktu_mulai"
                                        class="form-control @error('waktu_mulai') is-invalid @enderror"
                                        value="{{ old('waktu_mulai') }}" required>
                                    @error('waktu_mulai')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Waktu Selesai --}}
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small">
                                        Waktu Selesai <span class="text-danger">*</span>
                                    </label>
                                    <input type="time" name="waktu_selesai" id="waktu_selesai"
                                        class="form-control @error('waktu_selesai') is-invalid @enderror"
                                        value="{{ old('waktu_selesai') }}" required>
                                    @error('waktu_selesai')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Durasi Info --}}
                                <div class="col-12">
                                    <div id="durasi-info" class="text-muted small d-none">
                                        <i class="fas fa-clock me-1 text-primary"></i>
                                        Durasi: <strong id="durasi-text"></strong>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                    {{-- Action --}}
                    <div class="d-flex justify-content-between align-items-center">
                        <a href="{{ url()->previous() }}" class="btn btn-outline-secondary px-4">
                            <i class="fas fa-times me-1"></i>Batal
                        </a>
                        <button type="submit" class="btn btn-primary px-4" id="btnSubmit">
                            <i class="fas fa-paper-plane me-2"></i>Ajukan Booking
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // Hitung durasi otomatis
            function hitungDurasi() {
                const mulai = document.getElementById('waktu_mulai').value;
                const selesai = document.getElementById('waktu_selesai').value;
                const info = document.getElementById('durasi-info');
                const text = document.getElementById('durasi-text');

                if (!mulai || !selesai) {
                    info.classList.add('d-none');
                    return;
                }

                const [mH, mM] = mulai.split(':').map(Number);
                const [sH, sM] = selesai.split(':').map(Number);
                const diffMin = (sH * 60 + sM) - (mH * 60 + mM);

                if (diffMin <= 0) {
                    text.textContent = 'Waktu selesai harus lebih dari waktu mulai';
                    info.classList.remove('d-none');
                    return;
                }

                const jam = Math.floor(diffMin / 60);
                const menit = diffMin % 60;
                text.textContent = jam > 0 ?
                    `${jam} jam${menit > 0 ? ' ' + menit + ' menit' : ''}` :
                    `${menit} menit`;
                info.classList.remove('d-none');
            }

            document.getElementById('waktu_mulai').addEventListener('change', hitungDurasi);
            document.getElementById('waktu_selesai').addEventListener('change', hitungDurasi);

            // Submit guard
            document.getElementById('formBooking').addEventListener('submit', function() {
                const btn = document.getElementById('btnSubmit');
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Memproses...';
            });
        </script>
    @endpush
@endsection
