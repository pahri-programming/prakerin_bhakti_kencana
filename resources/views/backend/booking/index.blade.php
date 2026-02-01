@extends('layouts.backend')

@section('title', 'Manajemen Booking')

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.2/css/dataTables.bootstrap5.css">
    <style>
        .page-header {
            background: linear-gradient(135deg, #ff9800 0%, #ff5722 100%);
            padding: 2rem;
            border-radius: 15px;
            color: white;
            margin-bottom: 2rem;
        }

        .filter-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            padding: 1.5rem;
            margin-bottom: 2rem;
        }

        .table-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        }

        .table-card .card-header {
            background: white;
            border-bottom: 2px solid #f0f0f0;
            padding: 1.5rem;
            border-radius: 15px 15px 0 0;
        }

        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.85rem;
            display: inline-flex;
            align-items: center;
            gap: 0.375rem;
        }

        .status-pending {
            background: #fff3cd;
            color: #856404;
        }

        .status-diterima {
            background: #d1ecf1;
            color: #0c5460;
        }

        .status-ditolak {
            background: #f8d7da;
            color: #721c24;
        }

        .status-selesai {
            background: #d4edda;
            color: #155724;
        }

        #dataBooking thead th {
            background: linear-gradient(135deg, #ff9800 0%, #ff5722 100%);
            color: white;
            font-weight: 600;
            border: none;
            padding: 1rem;
        }

        #dataBooking tbody tr {
            transition: all 0.2s ease;
        }

        #dataBooking tbody tr:hover {
            background: #f3e5f5;
            transform: scale(1.01);
        }

        .btn-group-actions {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .kode-badge {
            color: rgb(1, 1, 1);
            padding: 0.375rem 0.75rem;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.85rem;
            font-family: 'jetbrains mono', monospace;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="fw-bold mb-2">
                        <i class="ti ti-calendar-event"></i> Manajemen Booking
                    </h2>
                    <p class="mb-0 opacity-90">Kelola semua booking ruangan</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('backend.booking.export', request()->all()) }}"
                        class="btn btn-danger btn-lg shadow-sm">
                        <i class="ti ti-file-pdf"></i> Export PDF
                    </a>
                    <a href="{{ route('backend.booking.create') }}" class="btn btn-light btn-lg shadow-sm">
                        <i class="ti ti-plus"></i> Tambah Booking
                    </a>
                </div>
            </div>
        </div>

        <!-- Filter Card -->
        <div class="filter-card">
            <form action="{{ route('backend.booking.index') }}" method="GET">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label fw-600">
                            <i class="ti ti-door"></i> Ruangan
                        </label>
                        <select name="ruang_id" class="form-select">
                            <option value="">Semua Ruangan</option>
                            @foreach ($ruangan as $data)
                                <option value="{{ $data->id }}"
                                    {{ request('ruang_id') == $data->id ? 'selected' : '' }}>
                                    {{ $data->nama_ruangan }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-600">
                            <i class="ti ti-calendar"></i> Tanggal
                        </label>
                        <input type="date" name="tanggal" class="form-control" value="{{ request('tanggal') }}">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-600">
                            <i class="ti ti-info-circle"></i> Status
                        </label>
                        <select name="status" class="form-select">
                            <option value="">Semua Status</option>
                            <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>
                                Pending
                            </option>
                            <option value="Diterima" {{ request('status') == 'Diterima' ? 'selected' : '' }}>
                                Diterima
                            </option>
                            <option value="Ditolak" {{ request('status') == 'Ditolak' ? 'selected' : '' }}>
                                Ditolak
                            </option>
                            <option value="Selesai" {{ request('status') == 'Selesai' ? 'selected' : '' }}>
                                Selesai
                            </option>
                        </select>
                    </div>

                    <div class="col-md-3 d-flex align-items-end gap-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="ti ti-filter"></i> Filter
                        </button>
                        @if (request()->hasAny(['ruang_id', 'tanggal', 'status']))
                            <a href="{{ route('backend.booking.index') }}" class="btn btn-outline-secondary">
                                <i class="ti ti-x"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>

        <!-- Table Card -->
        <div class="card table-card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold">
                        <i class="ti ti-list"></i> Daftar Booking
                    </h5>
                    <div class="d-flex gap-2">
                        <span class="status-badge status-pending">
                            <i class="ti ti-clock"></i>
                            Pending: {{ $booking->where('status', 'Pending')->count() }}
                        </span>
                        <span class="status-badge status-diterima">
                            <i class="ti ti-check"></i>
                            Diterima: {{ $booking->where('status', 'Diterima')->count() }}
                        </span>
                        <span class="status-badge status-selesai">
                            <i class="ti ti-circle-check"></i>
                            Selesai: {{ $booking->where('status', 'Selesai')->count() }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="dataBooking">
                        <thead>
                            <tr>
                                <th width="3%">No</th>
                                <th width="12%">Kode Booking</th>
                                <th width="15%">User</th>
                                <th width="15%">Ruangan</th>
                                <th width="12%">Tanggal</th>
                                <th width="10%">Waktu</th>
                                <th width="10%">Status</th>
                                <th width="13%" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($booking as $data)
                                <tr data-booking-id="{{ $data->id }}">
                                    <td class="fw-bold">{{ $loop->iteration }}</td>
                                    <td>
                                        <span class="kode-badge">{{ $data->kode }}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-light-info text-info rounded-circle me-2 d-flex align-items-center justify-content-center"
                                                style="width: 35px; height: 35px;">
                                                <i class="ti ti-user fs-6"></i>
                                            </div>
                                            <div>
                                                <strong>{{ $data->user->name }}</strong>
                                                <br>
                                                <small class="text-muted">{{ $data->user->email }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <i class="ti ti-door text-primary me-1"></i>
                                        <strong>{{ $data->ruangan->nama_ruangan }}</strong>
                                    </td>
                                    <td>
                                        <i class="ti ti-calendar text-warning me-1"></i>
                                        {{ $data->tanggal_format }}
                                    </td>
                                    <td>
                                        <small class="d-block">
                                            <i class="ti ti-clock"></i>
                                            {{ substr($data->waktu_mulai, 0, 5) }}
                                        </small>
                                        <small class="d-block">
                                            <i class="ti ti-clock"></i>
                                            {{ substr($data->waktu_selesai, 0, 5) }}
                                        </small>
                                    </td>
                                    <td>
                                        @switch($data->status)
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
                                        @if ($data->status === 'Ditolak' && $data->keterangan)
                                            <br>
                                            <small class="text-danger" data-bs-toggle="tooltip"
                                                title="{{ $data->keterangan }}">
                                                <i class="ti ti-alert-circle"></i> Ada alasan
                                            </small>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group-actions justify-content-center">
                                            {{-- ✅ FIXED: Approve Button - Tambah @method('PATCH') --}}
                                            @if ($data->status === 'Pending')
                                                <form action="{{ route('backend.booking.approve', $data->id) }}"
                                                    method="POST" class="d-inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-sm btn-success"
                                                        data-bs-toggle="tooltip" title="Terima"
                                                        onclick="return confirm('Setujui booking ini?')">
                                                        <i class="ti ti-check"></i>
                                                    </button>
                                                </form>
                                                <button type="button" class="btn btn-sm btn-danger"
                                                    onclick="rejectBooking({{ $data->id }})" data-bs-toggle="tooltip"
                                                    title="Tolak">
                                                    <i class="ti ti-x"></i>
                                                </button>
                                            @endif

                                            {{-- ✅ FIXED: Complete Button - Tambah @method('PATCH') --}}
                                            @if ($data->status === 'Diterima')
                                                <form action="{{ route('backend.booking.complete', $data->id) }}"
                                                    method="POST" class="d-inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-sm btn-info"
                                                        data-bs-toggle="tooltip" title="Selesaikan"
                                                        onclick="return confirm('Selesaikan booking ini?')">
                                                        <i class="ti ti-circle-check"></i>
                                                    </button>
                                                </form>
                                            @endif

                                            <a href="{{ route('backend.booking.show', $data->id) }}"
                                                class="btn btn-sm btn-info" data-bs-toggle="tooltip" title="Detail">
                                                <i class="ti ti-eye"></i>
                                            </a>
                                            <a href="{{ route('backend.booking.edit', $data->id) }}"
                                                class="btn btn-sm btn-warning" data-bs-toggle="tooltip" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </a>
                                            <form id="delete-form-{{ $data->id }}"
                                                action="{{ route('backend.booking.destroy', $data->id) }}" method="POST"
                                                class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-sm btn-danger"
                                                    onclick="confirmDelete({{ $data->id }})" data-bs-toggle="tooltip"
                                                    title="Hapus">
                                                    <i class="ti ti-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.datatables.net/2.3.2/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.3.2/js/dataTables.bootstrap5.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            // Init DataTable
            $('#dataBooking').DataTable({
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/id.json'
                },
                order: [
                    [4, 'desc']
                ], // Sort by tanggal descending
                pageLength: 15
            });

            // Initialize tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // Auto-refresh setiap 30 detik untuk update status
            setInterval(async () => {
                try {
                    await fetch("{{ route('backend.booking.index') }}", {
                        headers: {
                            "X-CSRF-TOKEN": "{{ csrf_token() }}"
                        }
                    });
                } catch (e) {
                    console.log('Auto-refresh failed');
                }
            }, 30000);
        });

        // Konfirmasi delete
        function confirmDelete(id) {
            Swal.fire({
                title: 'Yakin Hapus Booking?',
                text: "Data booking akan dihapus permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + id).submit();
                }
            });
        }

        // ✅ FIXED: Reject booking dengan alasan - Tambah method PATCH
        function rejectBooking(id) {
            Swal.fire({
                title: 'Tolak Booking?',
                input: 'textarea',
                inputLabel: 'Alasan Penolakan',
                inputPlaceholder: 'Masukkan alasan penolakan...',
                inputAttributes: {
                    'aria-label': 'Masukkan alasan penolakan',
                    'required': 'required'
                },
                showCancelButton: true,
                confirmButtonText: 'Tolak',
                confirmButtonColor: '#d33',
                cancelButtonText: 'Batal',
                preConfirm: (alasan) => {
                    if (!alasan) {
                        Swal.showValidationMessage('Alasan penolakan harus diisi!');
                    }
                    return alasan;
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Submit form reject
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `/admin/booking/${id}/reject`;

                    const csrf = document.createElement('input');
                    csrf.type = 'hidden';
                    csrf.name = '_token';
                    csrf.value = '{{ csrf_token() }}';

                    // ✅ FIXED: Tambah method PATCH
                    const method = document.createElement('input');
                    method.type = 'hidden';
                    method.name = '_method';
                    method.value = 'PATCH';

                    const keterangan = document.createElement('input');
                    keterangan.type = 'hidden';
                    keterangan.name = 'keterangan';
                    keterangan.value = result.value;

                    form.appendChild(csrf);
                    form.appendChild(method); // ✅ Tambahkan ini
                    form.appendChild(keterangan);
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }

        // Real-time update (jika menggunakan Pusher/Echo)
        @if (config('broadcasting.default') === 'pusher')
            window.Echo.channel('booking')
                .listen('BookingExpired', (e) => {
                    const row = document.querySelector(`[data-booking-id="${e.booking.id}"]`);
                    if (row) {
                        const badge = row.querySelector('.status-badge');
                        badge.className = 'status-badge status-selesai';
                        badge.innerHTML = '<i class="ti ti-circle-check"></i> Selesai';
                    }
                });
        @endif
    </script>
@endpush