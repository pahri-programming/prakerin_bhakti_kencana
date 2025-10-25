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

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold"><i
                                            class="bi bi-calendar-date me-2"></i>Tanggal</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="bi bi-calendar"></i></span>
                                        <input type="date" name="tanggal"
                                            class="form-control @error('tanggal') is-invalid @enderror"
                                            value="{{ old('tanggal') }}" required>
                                        @error('tanggal')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold"><i
                                            class="bi bi-door-closed me-2"></i>Ruangan</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="bi bi-building"></i></span>
                                        <select name="ruang_id" class="form-select @error('ruang_id') is-invalid @enderror"
                                            required>
                                            <option value="">Pilih Ruangan</option>
                                            @foreach ($ruangans as $ruangan)
                                                <option value="{{ $ruangan->id }}"
                                                    {{ old('ruang_id') == $ruangan->id ? 'selected' : '' }}>
                                                    {{ $ruangan->nama_ruangan }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('ruang_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

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
                            </div>

                            <div class="d-grid mt-4">
                                <button type="submit" class="btn btn-primary btn-lg rounded-pill shadow-sm">
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

        .input-group-text {
            border: 1px solid #dbe3ec;
            color: #4b5563;
        }

        .form-control,
        .form-select {
            border: 1px solid #dbe3ec;
            padding: 0.75rem 1rem;
            transition: 0.2s ease;
            background-color: #f9fafb;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.2);
            background-color: #fff;
        }

        .btn-primary {
            background: linear-gradient(90deg, #2563eb, #1d4ed8);
            border: none;
        }

        .btn-primary:hover {
            background: linear-gradient(90deg, #1d4ed8, #1e3a8a);
            transform: translateY(-2px);
        }

        label i {
            color: #2563eb;
        }
    </style>
@endsection
