@extends('layouts.backend')

@section('title', 'Tambah Booking Baru')

@push('styles')
    <style>
        body {
            background: #f8f9fa;
        }

        /* Page Header */
        .page-header {
            background: white;
            padding: 1.5rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            border-left: 4px solid #10b981;
        }

        .page-header h2 {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 0.25rem;
            color: #2d3748;
        }

        .page-header p {
            font-size: 0.9rem;
            color: #718096;
            margin: 0;
        }

        /* Form Card */
        .form-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            border: none;
            overflow: hidden;
        }

        .form-card .card-body {
            padding: 2rem;
        }

        /* Form Group */
        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 0.5rem;
            font-size: 0.95rem;
        }

        .form-label .required {
            color: #e53e3e;
        }

        .form-control,
        .form-select {
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 0.75rem 1rem;
            font-size: 0.95rem;
            transition: all 0.2s ease;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #10b981;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
            outline: none;
        }

        .form-text {
            color: #718096;
            font-size: 0.875rem;
            margin-top: 0.375rem;
        }

        /* Action Buttons */
        .action-buttons {
            display: flex;
            gap: 0.75rem;
            padding-top: 1.5rem;
            padding-bottom: 0.5rem;
            border-top: 1px solid #e2e8f0;
            border-bottom: 4px solid #10b981;
            margin-top: 1.5rem;
        }

        .btn-submit {
            background: #10b981;
            color: white;
            border: none;
            padding: 0.75rem 1.75rem;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.95rem;
            transition: all 0.2s ease;
        }

        .btn-submit:hover {
            background: #059669;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
            color: white;
        }

        .btn-cancel {
            background: #6c757d;
            color: white;
            border: none;
            padding: 0.75rem 1.75rem;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.95rem;
            transition: all 0.2s ease;
        }

        .btn-cancel:hover {
            background: #5a6268;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(108, 117, 125, 0.3);
            color: white;
        }

        /* Alert */
        .alert {
            border-radius: 8px;
            border: none;
            padding: 1rem 1.25rem;
            margin-bottom: 1.5rem;
        }

        .alert-danger {
            background: #fff5f5;
            color: #c53030;
            border-left: 3px solid #e53e3e;
        }

        .invalid-feedback {
            font-size: 0.875rem;
            margin-top: 0.375rem;
        }

        .is-invalid {
            border-color: #e53e3e !important;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .form-card .card-body {
                padding: 1.25rem;
            }

            .action-buttons {
                flex-direction: column;
            }

            .btn-submit,
            .btn-cancel {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <h2>Tambah Booking Baru</h2>
            <p>Buat peminjaman ruangan untuk user</p>
        </div>

        <!-- Form Card -->
        <div class="card form-card">
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <strong>Terdapat kesalahan!</strong>
                        <ul class="mb-0 mt-2 ps-3">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('backend.booking.store') }}" method="POST" id="bookingForm">
                    @csrf

                    <div class="row">
                        <!-- User -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">
                                    Peminjam <span class="required">*</span>
                                </label>
                                <select name="user_id" class="form-select @error('user_id') is-invalid @enderror" required>
                                    <option value="">-- Pilih User --</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }} - {{ $user->email }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('user_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Ruangan -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">
                                    Ruangan <span class="required">*</span>
                                </label>
                                <select name="ruang_id" class="form-select @error('ruang_id') is-invalid @enderror" required>
                                    <option value="">-- Pilih Ruangan --</option>
                                    @foreach($ruangan as $r)
                                        <option value="{{ $r->id }}" {{ old('ruang_id') == $r->id ? 'selected' : '' }}>
                                            {{ $r->nama_ruangan }} ({{ $r->kapasitas }} orang)
                                        </option>
                                    @endforeach
                                </select>
                                @error('ruang_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Tanggal -->
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">
                                    Tanggal Booking <span class="required">*</span>
                                </label>
                                <input type="date" 
                                    name="tanggal" 
                                    class="form-control @error('tanggal') is-invalid @enderror" 
                                    value="{{ old('tanggal') }}"
                                    min="{{ date('Y-m-d') }}"
                                    required>
                                @error('tanggal')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text">Minimal hari ini</small>
                            </div>
                        </div>

                        <!-- Waktu Mulai -->
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">
                                    Waktu Mulai <span class="required">*</span>
                                </label>
                                <input type="time" 
                                    name="waktu_mulai" 
                                    class="form-control @error('waktu_mulai') is-invalid @enderror" 
                                    value="{{ old('waktu_mulai') }}"
                                    required>
                                @error('waktu_mulai')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Waktu Selesai -->
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">
                                    Waktu Selesai <span class="required">*</span>
                                </label>
                                <input type="time" 
                                    name="waktu_selesai" 
                                    class="form-control @error('waktu_selesai') is-invalid @enderror" 
                                    value="{{ old('waktu_selesai') }}"
                                    required>
                                @error('waktu_selesai')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text">Harus lebih besar dari waktu mulai</small>
                            </div>
                        </div>
                    </div>

                    <!-- Hidden Status - Auto Pending -->
                    <input type="hidden" name="status" value="Pending">

                    <!-- Action Buttons -->
                    <div class="action-buttons">
                        <button type="submit" class="btn btn-submit">
                            Simpan Booking
                        </button>
                        <a href="{{ route('backend.booking.index') }}" class="btn btn-cancel">
                            Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Auto focus first select
        document.querySelector('select[name="user_id"]').focus();

        // Validation waktu selesai > waktu mulai
        const waktuMulai = document.querySelector('input[name="waktu_mulai"]');
        const waktuSelesai = document.querySelector('input[name="waktu_selesai"]');

        waktuSelesai.addEventListener('change', function() {
            if (waktuMulai.value && waktuSelesai.value) {
                if (waktuSelesai.value <= waktuMulai.value) {
                    alert('Waktu selesai harus lebih besar dari waktu mulai!');
                    waktuSelesai.value = '';
                }
            }
        });
    </script>
@endpush