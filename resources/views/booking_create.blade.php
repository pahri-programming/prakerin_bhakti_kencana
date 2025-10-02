@extends('layouts.frontend')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card border-0 rounded-5 shadow-lg">
                    <div class="card-body p-5">
                        <h3 class="text-center fw-bold mb-4 text-gradient">
                            <i class="bi bi-calendar-check-fill me-2"></i> Booking Ruangan
                        </h3>

                        @if (session('error'))
                            <div class="alert alert-danger shadow-sm">{{ session('error') }}</div>
                        @endif
                        @if (session('success'))
                            <div class="alert alert-success shadow-sm">{{ session('success') }}</div>
                        @endif

                        <form action="{{ route('bookings.store') }}" method="POST" novalidate>
                            @csrf
                            <input type="hidden" name="user_id" value="{{ auth()->id() }}">

                            <div class="mb-4">
                                <label class="form-label fw-semibold">Pilih Ruangan</label>
                                <select name="ruang_id"
                                    class="form-select form-select-lg @error('ruang_id') is-invalid @enderror" required>
                                    <option value="">-- Pilih Ruangan --</option>
                                    @foreach ($ruangans as $ruangan)
                                        <option value="{{ $ruangan->id }}"
                                            {{ request('ruangan_id') == $ruangan->id || old('ruang_id') == $ruangan->id ? 'selected' : '' }}>
                                            {{ $ruangan->nama_ruangan}}
                                        </option>
                                    @endforeach
                                </select>
                                @error('ruang_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-semibold">Tanggal</label>
                                <input type="date" name="tanggal"
                                    class="form-control form-control-lg @error('tanggal') is-invalid @enderror"
                                    value="{{ old('tanggal') }}" required>
                                @error('tanggal')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row g-4">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Jam Mulai</label>
                                    <input type="time" name="waktu_mulai"
                                        class="form-control form-control-lg @error('waktu_mulai') is-invalid @enderror"
                                        value="{{ old('waktu_mulai') }}" required>
                                    @error('waktu_mulai')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Jam Selesai</label>
                                    <input type="time" name="waktu_selesai"
                                        class="form-control form-control-lg @error('waktu_selesai') is-invalid @enderror"
                                        value="{{ old('waktu_selesai') }}" required>
                                    @error('waktu_selesai')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="d-grid mt-5">
                                <button type="submit" class="btn btn-primary btn-lg rounded-pill shadow">
                                    <i class="bi bi-send-check me-2"></i> Ajukan Booking
                                </button>
                            </div>
                        </form>

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
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Custom Style -->
    <style>
        .text-gradient {
            background: linear-gradient(to right, #1e3a8a, #3b82f6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .card {
            background: #ffffff;
        }

        .form-control,
        .form-select {
            background-color: #f8fafc;
            border: 1px solid #cbd5e1;
            transition: all 0.2s ease-in-out;
            color: #1e293b;
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
            transition: 0.3s ease;
        }

        .btn-primary:hover {
            background: linear-gradient(to right, #1e40af, #2563eb);
            transform: translateY(-2px);
        }

        .alert {
            border: none;
            border-left: 4px solid #3b82f6;
            background-color: #f0f9ff;
            color: #1e3a8a;
        }
    </style>
@endsection
