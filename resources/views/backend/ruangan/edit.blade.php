@extends('layouts.backend')

@section('title', 'Edit Ruangan')

@push('styles')
    <style>
        body {
            background: #f8f9fa;
        }

        /* Page Header - Simple */
        .page-header {
            background: white;
            padding: 1.5rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            border-left: 4px solid #f59e0b;
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

        /* Form Card - Clean & Simple */
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

        /* Info Alert */
        .info-alert {
            background: #f0f9ff;
            border: 1px solid #bae6fd;
            border-left: 3px solid #0ea5e9;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1.5rem;
        }

        .info-alert strong {
            color: #0369a1;
            display: block;
            margin-bottom: 0.25rem;
        }

        .info-alert small {
            color: #0c4a6e;
        }

        /* Warning Alert */
        .warning-alert {
            background: #fffbeb;
            border: 1px solid #fed7aa;
            border-left: 3px solid #f59e0b;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1.5rem;
        }

        .warning-alert strong {
            color: #92400e;
            display: block;
            margin-bottom: 0.25rem;
        }

        .warning-alert p {
            color: #78350f;
            margin: 0;
            font-size: 0.9rem;
        }

        /* Form Styling - Minimal */
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
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
            outline: none;
        }

        .form-control::placeholder {
            color: #a0aec0;
        }

        .form-text {
            color: #718096;
            font-size: 0.875rem;
            margin-top: 0.375rem;
        }

        /* Input Group */
        .input-group-text {
            background: #f7fafc;
            border: 1px solid #e2e8f0;
            color: #4a5568;
            border-right: none;
        }

        .input-group .form-control {
            border-left: none;
        }

        .input-group .form-control:focus {
            border-left: 1px solid #667eea;
        }

        /* Radio Buttons - Simple */
        .status-options {
            display: flex;
            gap: 1rem;
        }

        .status-option {
            flex: 1;
        }

        .form-check {
            margin: 0;
        }

        .form-check-input {
            width: 1.25rem;
            height: 1.25rem;
            margin-top: 0.125rem;
            cursor: pointer;
        }

        .form-check-input:checked {
            background-color: #667eea;
            border-color: #667eea;
        }

        .form-check-label {
            cursor: pointer;
            padding-left: 0.5rem;
            font-weight: 500;
            color: #2d3748;
        }

        .status-card {
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 1rem;
            transition: all 0.2s ease;
        }

        .status-card:hover {
            border-color: #cbd5e0;
            background: #f7fafc;
        }

        .status-card.active {
            border-color: #667eea;
            background: #f0f4ff;
        }

        .status-card.disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        /* Action Buttons - Simple */
        .action-buttons {
            display: flex;
            gap: 0.75rem;
            padding-top: 1.5rem;
            padding-bottom: 0.5rem;
            border-top: 1px solid #e2e8f0;
            border-bottom: 4px solid #f59e0b;
            margin-top: 1.5rem;
        }

        .btn-update {
            background: #f59e0b;
            color: white;
            border: none;
            padding: 0.75rem 1.75rem;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.95rem;
            transition: all 0.2s ease;
        }

        .btn-update:hover {
            background: #d97706;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
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

            .btn-update,
            .btn-cancel {
                width: 100%;
                justify-content: center;
            }

            .status-options {
                flex-direction: column;
            }
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <!-- Page Header - Simple -->
        <div class="page-header">
            <h2>Edit Ruangan</h2>
            <p>Update informasi ruangan</p>
        </div>

        <!-- Form Card -->
        <div class="card form-card">
            <div class="card-body">
                <!-- Current Info -->
                <div class="info-alert">
                    <strong>Informasi Ruangan Saat Ini:</strong>
                    <small>{{ $ruangan->nama_ruangan }} • Kapasitas: {{ $ruangan->kapasitas }} Orang • Status:
                        {{ ucfirst($ruangan->status) }}</small>
                </div>

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

                <!-- Warning for Active Bookings -->
                @if ($ruangan->booking()->where('status', 'Diterima')->exists())
                    <div class="warning-alert">
                        <strong>⚠️ Perhatian!</strong>
                        <p>Ruangan ini memiliki booking aktif. Status "Tersedia" tidak dapat dipilih.</p>
                    </div>
                @endif

                <form action="{{ route('backend.ruangan.update', $ruangan->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <!-- Nama Ruangan -->
                    <div class="form-group">
                        <label class="form-label">
                            Nama Ruangan <span class="required">*</span>
                        </label>
                        <input type="text" name="nama_ruangan"
                            class="form-control @error('nama_ruangan') is-invalid @enderror"
                            placeholder="Contoh: Ruang A301" value="{{ old('nama_ruangan', $ruangan->nama_ruangan) }}"
                            required>
                        @error('nama_ruangan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Kapasitas -->
                    <div class="form-group">
                        <label class="form-label">
                            Kapasitas Ruangan <span class="required">*</span>
                        </label>
                        <div class="input-group">
                            <input type="number" name="kapasitas"
                                class="form-control @error('kapasitas') is-invalid @enderror" placeholder="Jumlah orang"
                                value="{{ old('kapasitas', $ruangan->kapasitas) }}" min="1" required>
                            <span class="input-group-text">Orang</span>
                        </div>
                        @error('kapasitas')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Lokasi -->
                    <div class="form-group">
                        <label class="form-label">Lokasi Ruangan</label>
                        <input type="text" name="lokasi" class="form-control @error('lokasi') is-invalid @enderror"
                            placeholder="Contoh: Gedung A Lantai 3" value="{{ old('lokasi', $ruangan->lokasi) }}">
                        @error('lokasi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text">Opsional - untuk memudahkan pencarian</small>
                    </div>

                    <!-- Status -->
                    <div class="form-group">
                        <label class="form-label">
                            Status Ruangan <span class="required">*</span>
                        </label>
                        <div class="status-options">
                            <div class="status-option">
                                <div
                                    class="status-card {{ old('status', $ruangan->status) == 'tersedia' ? 'active' : '' }} {{ $ruangan->booking()->where('status', 'Diterima')->exists() ? 'disabled' : '' }}">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="status" id="status_tersedia"
                                            value="tersedia"
                                            {{ old('status', $ruangan->status) == 'tersedia' ? 'checked' : '' }}
                                            {{ $ruangan->booking()->where('status', 'Diterima')->exists() ? 'disabled' : '' }}>
                                        <label class="form-check-label" for="status_tersedia">
                                            Tersedia
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="status-option">
                                <div
                                    class="status-card {{ old('status', $ruangan->status) == 'dipinjam' ? 'active' : '' }}">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="status" id="status_dipinjam"
                                            value="dipinjam"
                                            {{ old('status', $ruangan->status) == 'dipinjam' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="status_dipinjam">
                                            Dipinjam
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @if ($ruangan->booking()->where('status', 'Diterima')->exists())
                            <small class="form-text text-warning">
                                Status "Tersedia" dinonaktifkan karena masih ada booking yang diterima
                            </small>
                        @endif
                    </div>

                    <!-- Action Buttons -->
                    <div class="action-buttons">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="fas fa-save me-2"></i>Update
                        </button>
                        <a href="{{ route('backend.ruangan.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times me-2"></i>Batal
                        </a>

                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Auto focus first input
        document.querySelector('input[name="nama_ruangan"]').focus();

        // Add active class to status cards on click
        document.querySelectorAll('.status-card').forEach(card => {
            card.addEventListener('click', function() {
                if (!this.classList.contains('disabled')) {
                    document.querySelectorAll('.status-card').forEach(c => c.classList.remove('active'));
                    this.classList.add('active');
                }
            });
        });
    </script>
@endpush
