@extends('layouts.backend')

@section('title', 'Edit Booking')

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

        /* Info Alert */
        .info-alert {
            background: #f0f9ff;
            border: 1px solid #bae6fd;
            border-left: 3px solid #0ea5e9;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1.5rem;
        }

        .info-alert .badge {
            font-size: 0.85rem;
            padding: 0.4rem 0.8rem;
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
            border-color: #f59e0b;
            box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.1);
            outline: none;
        }

        .form-text {
            color: #718096;
            font-size: 0.875rem;
            margin-top: 0.375rem;
        }

        /* Status Badges */
        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 6px;
            font-weight: 600;
            font-size: 0.875rem;
        }

        .status-pending {
            background: #fef3c7;
            color: #92400e;
        }

        .status-diterima {
            background: #d1fae5;
            color: #065f46;
        }

        .status-ditolak {
            background: #fee2e2;
            color: #991b1b;
        }

        .status-selesai {
            background: #e0e7ff;
            color: #3730a3;
        }

        /* Quick Action Buttons */
        .quick-actions {
            display: flex;
            gap: 0.75rem;
            margin-bottom: 1.5rem;
            padding: 1rem;
            background: #f7fafc;
            border-radius: 8px;
        }

        .btn-quick {
            flex: 1;
            padding: 0.75rem;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.9rem;
            transition: all 0.2s ease;
            border: none;
        }

        .btn-approve {
            background: #10b981;
            color: white;
        }

        .btn-approve:hover {
            background: #059669;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        }

        .btn-reject-quick {
            background: #ef4444;
            color: white;
        }

        .btn-reject-quick:hover {
            background: #dc2626;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
        }

        .btn-complete {
            background: #6366f1;
            color: white;
        }

        .btn-complete:hover {
            background: #4f46e5;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
        }

        /* Keterangan Textarea */
        .keterangan-group {
            display: none;
        }

        .keterangan-group.show {
            display: block;
        }

        /* Action Buttons */
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

            .action-buttons,
            .quick-actions {
                flex-direction: column;
            }

            .btn-update,
            .btn-cancel,
            .btn-quick {
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
            <h2>Edit Booking</h2>
            <p>Update status dan informasi booking</p>
        </div>

        <!-- Form Card -->
        <div class="card form-card">
            <div class="card-body">
                <!-- Current Info -->
                <div class="info-alert">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <strong>Kode Booking: {{ $booking->kode }}</strong><br>
                            <small>{{ $booking->user->name }} • {{ $booking->ruangan->nama_ruangan }}</small>
                        </div>
                        <span class="status-badge status-{{ strtolower($booking->status) }}">
                            {{ $booking->status }}
                        </span>
                    </div>
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

                <!-- Quick Actions -->
                @if($booking->status == 'Pending')
                    <div class="quick-actions">
                        <form action="{{ route('backend.booking.approve', $booking->id) }}" method="POST" class="flex-fill">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-approve btn-quick" onclick="return confirm('Setujui booking ini?')">
                                ✓ Terima Booking
                            </button>
                        </form>
                        <button type="button" class="btn btn-reject-quick btn-quick" data-bs-toggle="modal" data-bs-target="#rejectModal">
                            ✕ Tolak Booking
                        </button>
                    </div>
                @endif

                @if($booking->status == 'Diterima')
                    <div class="quick-actions">
                        <form action="{{ route('backend.booking.complete', $booking->id) }}" method="POST" class="flex-fill">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-complete btn-quick" onclick="return confirm('Tandai booking ini selesai?')">
                                ✓ Selesaikan Booking
                            </button>
                        </form>
                    </div>
                @endif

                <form action="{{ route('backend.booking.update', $booking->id) }}" method="POST" id="bookingForm">
                    @csrf
                    @method('PUT')

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
                                        <option value="{{ $user->id }}" {{ old('user_id', $booking->user_id) == $user->id ? 'selected' : '' }}>
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
                                        <option value="{{ $r->id }}" {{ old('ruang_id', $booking->ruang_id) == $r->id ? 'selected' : '' }}>
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
                                    value="{{ old('tanggal', \Carbon\Carbon::parse($booking->tanggal)->format('Y-m-d')) }}"
                                    required>
                                @error('tanggal')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
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
                                    value="{{ old('waktu_mulai', \Carbon\Carbon::parse($booking->waktu_mulai)->format('H:i')) }}"
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
                                    value="{{ old('waktu_selesai', \Carbon\Carbon::parse($booking->waktu_selesai)->format('H:i')) }}"
                                    required>
                                @error('waktu_selesai')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Status -->
                    <div class="form-group">
                        <label class="form-label">
                            Status Booking <span class="required">*</span>
                        </label>
                        <select name="status" id="statusSelect" class="form-select @error('status') is-invalid @enderror" required>
                            <option value="Pending" {{ old('status', $booking->status) == 'Pending' ? 'selected' : '' }}>Pending</option>
                            <option value="Diterima" {{ old('status', $booking->status) == 'Diterima' ? 'selected' : '' }}>Diterima</option>
                            <option value="Ditolak" {{ old('status', $booking->status) == 'Ditolak' ? 'selected' : '' }}>Ditolak</option>
                            <option value="Selesai" {{ old('status', $booking->status) == 'Selesai' ? 'selected' : '' }}>Selesai</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Keterangan (Show when Ditolak) -->
                    <div class="form-group keterangan-group {{ old('status', $booking->status) == 'Ditolak' ? 'show' : '' }}" id="keteranganGroup">
                        <label class="form-label">
                            Alasan Penolakan <span class="required">*</span>
                        </label>
                        <textarea name="keterangan" 
                            class="form-control @error('keterangan') is-invalid @enderror" 
                            rows="3" 
                            placeholder="Tuliskan alasan penolakan...">{{ old('keterangan', $booking->keterangan) }}</textarea>
                        @error('keterangan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Action Buttons -->
                    <div class="action-buttons">
                        <button type="submit" class="btn btn-update">
                            Update Booking
                        </button>
                        <a href="{{ route('backend.booking.index') }}" class="btn btn-cancel">
                            Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Reject Modal -->
    <div class="modal fade" id="rejectModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('backend.booking.reject', $booking->id) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="modal-header">
                        <h5 class="modal-title">Tolak Booking</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="form-label">Alasan Penolakan <span class="required">*</span></label>
                            <textarea name="keterangan" class="form-control" rows="4" placeholder="Tuliskan alasan penolakan..." required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger">Tolak Booking</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Show/hide keterangan based on status
        const statusSelect = document.getElementById('statusSelect');
        const keteranganGroup = document.getElementById('keteranganGroup');

        statusSelect.addEventListener('change', function() {
            if (this.value === 'Ditolak') {
                keteranganGroup.classList.add('show');
                keteranganGroup.querySelector('textarea').required = true;
            } else {
                keteranganGroup.classList.remove('show');
                keteranganGroup.querySelector('textarea').required = false;
            }
        });

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