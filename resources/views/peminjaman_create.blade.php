@extends('layouts.frontend')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card border-0 rounded-5 shadow-lg">
                    <div class="card-body p-5">
                        <h3 class="text-center fw-bold mb-4 text-gradient">
                            <i class="bi bi-box-seam-fill me-2"></i> Peminjaman Barang
                        </h3>

                        @if (session('error'))
                            <div class="alert alert-danger shadow-sm">{{ session('error') }}</div>
                        @endif
                        @if (session('success'))
                            <div class="alert alert-success shadow-sm">{{ session('success') }}</div>
                        @endif

                        <form action="{{ route('peminjaman.store') }}" method="POST" novalidate>
                            @csrf

                            <div class="row g-4">
                                <!-- Tanggal Pinjam -->
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">
                                        <i class="bi bi-calendar-date me-2"></i>Tanggal Pinjam
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="bi bi-calendar"></i></span>
                                        <input type="date" name="tanggal_pinjam"
                                            class="form-control @error('tanggal_pinjam') is-invalid @enderror"
                                            value="{{ old('tanggal_pinjam') }}" min="{{ date('Y-m-d') }}" required>
                                        @error('tanggal_pinjam')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Tanggal Kembali -->
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">
                                        <i class="bi bi-calendar-date-fill me-2"></i>Tanggal Kembali
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="bi bi-calendar2-check"></i></span>
                                        <input type="date" name="tanggal_kembali"
                                            class="form-control @error('tanggal_kembali') is-invalid @enderror"
                                            value="{{ old('tanggal_kembali') }}" min="{{ date('Y-m-d') }}" required>
                                        @error('tanggal_kembali')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Barang -->
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold"><i class="bi bi-box me-2"></i>Barang</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="bi bi-tag"></i></span>
                                        <select name="barang_id"
                                            class="form-select @error('barang_id') is-invalid @enderror" required>
                                            <option value="">Pilih Barang</option>
                                            @foreach ($barang as $barangs)
                                                <option value="{{ $barangs->id }}"
                                                    {{ old('barang_id') == $barangs->id ? 'selected' : '' }}
                                                    data-stok="{{ $barangs->stok }}">
                                                    {{ $barangs->nama }} (Stok: {{ $barangs->stok }})
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('barang_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Jumlah -->
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold"><i class="bi bi-hash me-2"></i>Jumlah</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="bi bi-calculator"></i></span>
                                        <input type="number" name="jumlah" min="1"
                                            class="form-control @error('jumlah') is-invalid @enderror"
                                            value="{{ old('jumlah') }}" required>
                                        @error('jumlah')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Waktu Mulai -->
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold"><i class="bi bi-clock me-2"></i>Waktu
                                        Mulai</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="bi bi-clock-history"></i></span>
                                        <input type="time" name="waktu_mulai"
                                            class="form-control @error('waktu_mulai') is-invalid @enderror"
                                            value="{{ old('waktu_mulai') }}" required>
                                        @error('waktu_mulai')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Waktu Selesai -->
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold"><i class="bi bi-clock-fill me-2"></i>Waktu
                                        Selesai</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="bi bi-clock"></i></span>
                                        <input type="time" name="waktu_selesai"
                                            class="form-control @error('waktu_selesai') is-invalid @enderror"
                                            value="{{ old('waktu_selesai') }}" required>
                                        @error('waktu_selesai')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Keterangan -->
                                <div class="col-12">
                                    <label class="form-label fw-semibold"><i class="bi bi-chat-dots me-2"></i>Keterangan
                                        (Opsional)</label>
                                    <textarea name="keterangan" rows="3" class="form-control @error('keterangan') is-invalid @enderror"
                                        placeholder="Contoh: Untuk acara seminar kampus">{{ old('keterangan') }}</textarea>
                                    @error('keterangan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="d-grid mt-4">
                                <button type="submit" class="btn btn-primary btn-lg rounded-pill shadow-sm">
                                    <i class="bi bi-send-check me-2"></i> Ajukan Peminjaman
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast Error -->
    @if ($errors->any())
        <script>
            @foreach ($errors->all() as $error)
                Toastify({
                    text: "{{ $error }}",
                    duration: 4000,
                    close: true,
                    gravity: "top",
                    position: "right",
                    backgroundColor: "#ef4444"
                }).showToast();
            @endforeach
        </script>
    @endif

    <!-- Script: Update max jumlah berdasarkan stok -->
    <script>
        document.querySelector('[name="barang_id"]').addEventListener('change', function() {
            const selected = this.options[this.selectedIndex];
            const stok = selected.dataset.stok || 0;
            const jumlahInput = document.querySelector('[name="jumlah"]');
            jumlahInput.max = stok;
            if (parseInt(jumlahInput.value) > stok) {
                jumlahInput.value = stok;
            }
        });
        document.querySelector('[name="tanggal_pinjam"]').addEventListener('change', function() {
            const start = this.value;
            const endInput = document.querySelector('[name="tanggal_kembali"]');

            endInput.min = start;

            // kalau end lebih kecil dari start, set ulang
            if (endInput.value < start) {
                endInput.value = start;
            }
        });
    </script>

    <style>
        .text-gradient {
            background: linear-gradient(to right, #1e3a8a, #3b82f6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .form-control,
        .form-select {
            background-color: #f8fafc;
            border: 1px solid #cbd5e1;
            transition: all 0.2s ease-in-out;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 0.2rem rgba(59, 130, 246, 0.1);
            background-color: #fff;
        }

        .btn-primary {
            background: linear-gradient(to right, #1e3a8a, #3b82f6);
            border: none;
        }

        .btn-primary:hover {
            background: linear-gradient(to right, #1e40af, #2563eb);
            transform: translateY(-2px);
        }

        label i {
            color: #2563eb;
        }
    </style>
@endsection
