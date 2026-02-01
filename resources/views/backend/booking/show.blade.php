@extends('layouts.backend')

@section('title', 'Detail Booking')

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

        /* Detail Card */
        .detail-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            border: none;
            overflow: hidden;
            margin-bottom: 1.5rem;
        }

        .detail-card .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1.5rem;
            border: none;
        }

        .detail-card .card-header h5 {
            margin: 0;
            font-weight: 700;
        }

        .detail-card .card-body {
            padding: 2rem;
        }

        /* Info Row */
        .info-row {
            display: flex;
            padding: 1rem 0;
            border-bottom: 1px solid #e2e8f0;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            width: 200px;
            font-weight: 600;
            color: #4a5568;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .info-value {
            flex: 1;
            color: #2d3748;
        }

        /* Status Badge */
        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.875rem;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
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

        /* Kode Badge */
        .kode-badge {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 700;
            font-size: 1.25rem;
            font-family: 'Courier New', monospace;
            display: inline-block;
            letter-spacing: 1px;
        }

        /* Time Badge */
        .time-badge {
            background: #f0f4ff;
            color: #667eea;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        /* Alert Box */
        .alert-keterangan {
            background: #fff5f5;
            border: 1px solid #feb2b2;
            border-left: 4px solid #ef4444;
            padding: 1rem;
            border-radius: 8px;
            margin-top: 1rem;
        }

        .alert-keterangan strong {
            color: #991b1b;
            display: block;
            margin-bottom: 0.5rem;
        }

        .alert-keterangan p {
            color: #7f1d1d;
            margin: 0;
        }

        /* Action Buttons */
        .action-buttons {
            display: flex;
            gap: 0.75rem;
            padding-top: 1.5rem;
            border-top: 1px solid #e2e8f0;
            margin-top: 1.5rem;
        }

        .btn-back {
            background: #6c757d;
            color: white;
            border: none;
            padding: 0.75rem 1.75rem;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.95rem;
            transition: all 0.2s ease;
        }

        .btn-back:hover {
            background: #5a6268;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(108, 117, 125, 0.3);
            color: white;
        }

        .btn-edit {
            background: #f59e0b;
            color: white;
            border: none;
            padding: 0.75rem 1.75rem;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.95rem;
            transition: all 0.2s ease;
        }

        .btn-edit:hover {
            background: #d97706;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
            color: white;
        }

        .btn-delete {
            background: #ef4444;
            color: white;
            border: none;
            padding: 0.75rem 1.75rem;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.95rem;
            transition: all 0.2s ease;
        }

        .btn-delete:hover {
            background: #dc2626;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
            color: white;
        }

        /* User Info */
        .user-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .user-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            font-weight: 700;
        }

        .user-details strong {
            display: block;
            color: #2d3748;
        }

        .user-details small {
            color: #718096;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .info-row {
                flex-direction: column;
                gap: 0.5rem;
            }

            .info-label {
                width: 100%;
            }

            .action-buttons {
                flex-direction: column;
            }

            .btn-back,
            .btn-edit,
            .btn-delete {
                width: 100%;
                text-align: center;
            }
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <h2>Detail Booking</h2>
            <p>Informasi lengkap booking ruangan</p>
        </div>

        <!-- Detail Card -->
        <div class="card detail-card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5>
                        <i class="ti ti-calendar-event me-2"></i>
                        Informasi Booking
                    </h5>
                    <span class="kode-badge">{{ $booking->kode }}</span>
                </div>
            </div>

            <div class="card-body">
                <!-- Status -->
                <div class="info-row">
                    <div class="info-label">
                        <i class="ti ti-info-circle"></i>
                        Status
                    </div>
                    <div class="info-value">
                        @switch($booking->status)
                            @case('Pending')
                                <span class="status-badge status-pending">
                                    <i class="ti ti-clock"></i> Pending
                                </span>
                                @break
                            @case('Diterima')
                                <span class="status-badge status-diterima">
                                    <i class="ti ti-check"></i> Diterima
                                </span>
                                @break
                            @case('Ditolak')
                                <span class="status-badge status-ditolak">
                                    <i class="ti ti-x"></i> Ditolak
                                </span>
                                @break
                            @case('Selesai')
                                <span class="status-badge status-selesai">
                                    <i class="ti ti-circle-check"></i> Selesai
                                </span>
                                @break
                        @endswitch
                    </div>
                </div>

                <!-- Peminjam -->
                <div class="info-row">
                    <div class="info-label">
                        <i class="ti ti-user"></i>
                        Peminjam
                    </div>
                    <div class="info-value">
                        <div class="user-info">
                            <div class="user-avatar">
                                {{ strtoupper(substr($booking->user->name, 0, 1)) }}
                            </div>
                            <div class="user-details">
                                <strong>{{ $booking->user->name }}</strong>
                                <small>{{ $booking->user->email }}</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Ruangan -->
                <div class="info-row">
                    <div class="info-label">
                        <i class="ti ti-door"></i>
                        Ruangan
                    </div>
                    <div class="info-value">
                        <strong>{{ $booking->ruangan->nama_ruangan }}</strong><br>
                        <small class="text-muted">
                            <i class="ti ti-users"></i> Kapasitas: {{ $booking->ruangan->kapasitas }} orang
                            @if($booking->ruangan->lokasi)
                                | <i class="ti ti-map-pin"></i> {{ $booking->ruangan->lokasi }}
                            @endif
                        </small>
                    </div>
                </div>

                <!-- Tanggal -->
                <div class="info-row">
                    <div class="info-label">
                        <i class="ti ti-calendar"></i>
                        Tanggal
                    </div>
                    <div class="info-value">
                        <strong>{{ $booking->tanggal_format }}</strong><br>
                        <small class="text-muted">{{ $booking->hari }}</small>
                    </div>
                </div>

                <!-- Waktu -->
                <div class="info-row">
                    <div class="info-label">
                        <i class="ti ti-clock"></i>
                        Waktu
                    </div>
                    <div class="info-value">
                        <div class="d-flex gap-2">
                            <span class="time-badge">
                                <i class="ti ti-clock"></i>
                                Mulai: {{ \Carbon\Carbon::parse($booking->waktu_mulai)->format('H:i') }}
                            </span>
                            <span class="time-badge">
                                <i class="ti ti-clock"></i>
                                Selesai: {{ \Carbon\Carbon::parse($booking->waktu_selesai)->format('H:i') }}
                            </span>
                        </div>
                        <small class="text-muted mt-1 d-block">
                            Durasi: {{ \Carbon\Carbon::parse($booking->waktu_mulai)->diffInMinutes(\Carbon\Carbon::parse($booking->waktu_selesai)) }} menit
                        </small>
                    </div>
                </div>

                <!-- Tanggal Dibuat -->
                <div class="info-row">
                    <div class="info-label">
                        <i class="ti ti-calendar-plus"></i>
                        Dibuat Pada
                    </div>
                    <div class="info-value">
                        {{ \Carbon\Carbon::parse($booking->created_at)->translatedFormat('d F Y, H:i') }} WIB
                    </div>
                </div>

                <!-- Terakhir Diupdate -->
                <div class="info-row">
                    <div class="info-label">
                        <i class="ti ti-calendar-edit"></i>
                        Terakhir Diupdate
                    </div>
                    <div class="info-value">
                        {{ \Carbon\Carbon::parse($booking->updated_at)->translatedFormat('d F Y, H:i') }} WIB
                    </div>
                </div>

                <!-- Keterangan (jika ditolak) -->
                @if($booking->status === 'Ditolak' && $booking->keterangan)
                    <div class="alert-keterangan">
                        <strong><i class="ti ti-alert-circle"></i> Alasan Penolakan:</strong>
                        <p>{{ $booking->keterangan }}</p>
                    </div>
                @endif

                <!-- Action Buttons -->
                <div class="action-buttons">
                    <a href="{{ route('backend.booking.index') }}" class="btn btn-back">
                        <i class="ti ti-arrow-left"></i> Kembali
                    </a>
                    <a href="{{ route('backend.booking.edit', $booking->id) }}" class="btn btn-edit">
                        <i class="ti ti-edit"></i> Edit Booking
                    </a>
                    <form id="delete-form" action="{{ route('backend.booking.destroy', $booking->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="btn btn-delete" onclick="confirmDelete()">
                            <i class="ti ti-trash"></i> Hapus Booking
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function confirmDelete() {
            Swal.fire({
                title: 'Yakin Hapus Booking?',
                text: "Data booking akan dihapus permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form').submit();
                }
            });
        }
    </script>
@endpush