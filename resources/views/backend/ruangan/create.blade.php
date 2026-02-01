@extends('layouts.backend')

@section('title', 'Tambah Ruangan Baru')

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
            border-left: 4px solid #667eea;
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

        /* Action Buttons - Simple */
        .action-buttons {
            display: flex;
            gap: 0.75rem;
            padding-top: 1.5rem;
            padding-bottom: 0.5rem;
            border-top: 1px solid #e2e8f0;
            border-bottom: 4px solid #667eea;
            margin-top: 1.5rem;
        }

        .btn-submit {
            background: #667eea;
            color: white;
            border: none;
            padding: 0.75rem 1.75rem;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.95rem;
            transition: all 0.2s ease;
        }

        .btn-submit:hover {
            background: #5568d3;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
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
        <!-- Page Header - Simple -->
        <div class="page-header">
            <h2>Tambah Ruangan Baru</h2>
            <p>Isi form untuk membuat ruangan baru</p>
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

                <form action="{{ route('backend.ruangan.store') }}" method="POST">
                    @csrf

                    <!-- Nama Ruangan -->
                    <div class="form-group">
                        <label class="form-label">
                            Nama Ruangan <span class="required">*</span>
                        </label>
                        <input type="text" 
                            name="nama_ruangan" 
                            class="form-control @error('nama_ruangan') is-invalid @enderror" 
                            placeholder="Contoh: Ruang A301"
                            value="{{ old('nama_ruangan') }}"
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
                            <input type="number" 
                                name="kapasitas" 
                                class="form-control @error('kapasitas') is-invalid @enderror" 
                                placeholder="Jumlah orang"
                                value="{{ old('kapasitas') }}"
                                min="1"
                                required>
                            <span class="input-group-text">Orang</span>
                        </div>
                        @error('kapasitas')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Lokasi -->
                    <div class="form-group">
                        <label class="form-label">Lokasi Ruangan</label>
                        <input type="text" 
                            name="lokasi" 
                            class="form-control @error('lokasi') is-invalid @enderror" 
                            placeholder="Contoh: Gedung A Lantai 3"
                            value="{{ old('lokasi') }}">
                        @error('lokasi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text">Opsional - untuk memudahkan pencarian</small>
                    </div>

                    <!-- Hidden Status - Auto Tersedia -->
                    <input type="hidden" name="status" value="tersedia">

                    <!-- Action Buttons -->
                    <div class="action-buttons">
                        <button type="submit" class="btn btn-submit">
                            Simpan Ruangan
                        </button>
                        <a href="{{ route('backend.ruangan.index') }}" class="btn btn-cancel">
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
        // Auto focus first input
        document.querySelector('input[name="nama_ruangan"]').focus();
    </script>
@endpush