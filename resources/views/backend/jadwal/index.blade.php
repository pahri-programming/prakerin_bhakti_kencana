@extends('layouts.backend')

@section('title', 'Manajemen Jadwal')

@push('styles')
<style>
    .card-jadwal {
        transition: all 0.3s ease;
        border: none;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }
    
    .card-jadwal:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.15);
    }

    .status-badge {
        padding: 0.4rem 0.8rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .status-selesai {
        background: linear-gradient(135deg, #6c757d 0%, #5a6268 100%);
        color: white;
    }

    .status-berlangsung {
        background: linear-gradient(135deg, #28a745 0%, #218838 100%);
        color: white;
        animation: pulse 2s infinite;
    }

    .status-akan-datang {
        background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
        color: white;
    }

    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.7; }
    }

    .time-badge {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 0.3rem 0.6rem;
        border-radius: 8px;
        font-size: 0.85rem;
        font-weight: 600;
    }

    .filter-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        color: white;
    }

    .filter-card .form-control,
    .filter-card .form-select {
        border: 2px solid rgba(255,255,255,0.3);
        background: rgba(255,255,255,0.1);
        color: white;
    }

    .filter-card .form-control::placeholder {
        color: rgba(255,255,255,0.7);
    }

    .filter-card .form-control:focus,
    .filter-card .form-select:focus {
        background: rgba(255,255,255,0.2);
        color: white;
        border-color: white;
    }

    .filter-card .form-select option {
        color: #333;
    }

    .stats-card {
        border-left: 4px solid;
        transition: all 0.3s ease;
    }

    .stats-card:hover {
        transform: translateX(5px);
    }

    .ruangan-badge {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        color: white;
        padding: 0.4rem 0.8rem;
        border-radius: 15px;
        font-weight: 600;
    }

    .calendar-icon {
        font-size: 2rem;
        opacity: 0.8;
    }

    .action-buttons .btn {
        margin: 0 2px;
        transition: all 0.2s ease;
    }

    .action-buttons .btn:hover {
        transform: scale(1.1);
    }

    .empty-state {
        padding: 60px 20px;
        text-align: center;
    }

    .empty-state i {
        font-size: 5rem;
        color: #e0e0e0;
        margin-bottom: 20px;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="fw-bold mb-2">
                        <i class="ti ti-calendar-time text-primary"></i> Manajemen Jadwal
                    </h2>
                    <p class="text-muted mb-0">Kelola jadwal kegiatan ruangan</p>
                </div>
                <div>
                    <a href="{{ route('backend.jadwal.create') }}" class="btn btn-primary btn-lg shadow-sm">
                        <i class="ti ti-plus"></i> Tambah Jadwal
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card stats-card border-start-primary">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Total Jadwal</p>
                            <h3 class="fw-bold mb-0">{{ $jadwal->total() }}</h3>
                        </div>
                        <div class="calendar-icon text-primary">
                            <i class="ti ti-calendar"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stats-card border-start-success">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Berlangsung</p>
                            <h3 class="fw-bold mb-0 text-success">
                                {{ $jadwal->filter(fn($j) => $j->status_waktu == 'berlangsung')->count() }}
                            </h3>
                        </div>
                        <div class="calendar-icon text-success">
                            <i class="ti ti-clock"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stats-card border-start-info">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Akan Datang</p>
                            <h3 class="fw-bold mb-0 text-info">
                                {{ $jadwal->filter(fn($j) => $j->status_waktu == 'akan-datang')->count() }}
                            </h3>
                        </div>
                        <div class="calendar-icon text-info">
                            <i class="ti ti-hourglass"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stats-card border-start-secondary">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Selesai</p>
                            <h3 class="fw-bold mb-0 text-secondary">
                                {{ $jadwal->filter(fn($j) => $j->status_waktu == 'selesai')->count() }}
                            </h3>
                        </div>
                        <div class="calendar-icon text-secondary">
                            <i class="ti ti-check"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card filter-card mb-4 shadow-sm">
        <div class="card-body">
            <form action="{{ route('backend.jadwal.index') }}" method="GET">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">
                            <i class="ti ti-search"></i> Cari Kegiatan
                        </label>
                        <input type="text" name="search" class="form-control" 
                               placeholder="Cari kegiatan..." 
                               value="{{ request('search') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">
                            <i class="ti ti-door"></i> Ruangan
                        </label>
                        <select name="ruangan_id" class="form-select">
                            <option value="">Semua Ruangan</option>
                            @foreach($ruangans as $ruangan)
                                <option value="{{ $ruangan->id }}" 
                                        {{ request('ruangan_id') == $ruangan->id ? 'selected' : '' }}>
                                    {{ $ruangan->nama_ruangan }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold">
                            <i class="ti ti-calendar"></i> Bulan
                        </label>
                        <select name="bulan" class="form-select">
                            <option value="">Semua Bulan</option>
                            @foreach($bulan as $key => $value)
                                <option value="{{ $key }}" {{ request('bulan') == $key ? 'selected' : '' }}>
                                    {{ $value }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold">
                            <i class="ti ti-calendar-event"></i> Tahun
                        </label>
                        <select name="tahun" class="form-select">
                            <option value="">Semua Tahun</option>
                            @foreach($tahun as $t)
                                <option value="{{ $t }}" {{ request('tahun') == $t ? 'selected' : '' }}>
                                    {{ $t }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-light w-100 fw-semibold">
                            <i class="ti ti-filter"></i> Filter
                        </button>
                    </div>
                </div>
                @if(request()->hasAny(['search', 'ruangan_id', 'bulan', 'tahun']))
                    <div class="text-end mt-2">
                        <a href="{{ route('backend.jadwal.index') }}" class="btn btn-sm btn-outline-light">
                            <i class="ti ti-x"></i> Reset Filter
                        </a>
                    </div>
                @endif
            </form>
        </div>
    </div>

    <!-- Jadwal List -->
    @if($jadwal->count() > 0)
        <div class="row">
            @foreach($jadwal as $item)
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card card-jadwal h-100">
                        <div class="card-body">
                            <!-- Header -->
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <span class="ruangan-badge">
                                        <i class="ti ti-door"></i> {{ $item->ruangan->nama_ruangan ?? 'N/A' }}
                                    </span>
                                </div>
                                <span class="status-badge status-{{ $item->status_waktu }}">
                                    @if($item->status_waktu == 'selesai')
                                        <i class="ti ti-check"></i> Selesai
                                    @elseif($item->status_waktu == 'berlangsung')
                                        <i class="ti ti-clock"></i> Berlangsung
                                    @else
                                        <i class="ti ti-hourglass"></i> Akan Datang
                                    @endif
                                </span>
                            </div>

                            <!-- Kegiatan -->
                            <h5 class="fw-bold mb-3">{{ $item->kegiatan }}</h5>

                            <!-- Tanggal & Waktu -->
                            <div class="mb-3">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="ti ti-calendar text-primary me-2"></i>
                                    <span class="text-muted">{{ $item->tanggal_format }}</span>
                                </div>
                                <div class="d-flex align-items-center mb-2">
                                    <i class="ti ti-sun text-warning me-2"></i>
                                    <span class="text-muted">{{ $item->hari }}</span>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="time-badge">
                                        <i class="ti ti-clock"></i> {{ substr($item->waktu_mulai, 0, 5) }}
                                    </span>
                                    <i class="ti ti-arrow-right"></i>
                                    <span class="time-badge">
                                        <i class="ti ti-clock"></i> {{ substr($item->waktu_selesai, 0, 5) }}
                                    </span>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="action-buttons d-flex justify-content-end gap-1 pt-3 border-top">
                                <a href="{{ route('backend.jadwal.show', $item->id) }}" 
                                   class="btn btn-sm btn-info" 
                                   data-bs-toggle="tooltip" 
                                   title="Detail">
                                    <i class="ti ti-eye"></i>
                                </a>
                                <a href="{{ route('backend.jadwal.edit', $item->id) }}" 
                                   class="btn btn-sm btn-warning" 
                                   data-bs-toggle="tooltip" 
                                   title="Edit">
                                    <i class="ti ti-edit"></i>
                                </a>
                                <form action="{{ route('backend.jadwal.destroy', $item->id) }}" 
                                      method="POST" 
                                      class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="btn btn-sm btn-danger" 
                                            data-bs-toggle="tooltip" 
                                            title="Hapus"
                                            data-confirm-delete="true">
                                        <i class="ti ti-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-between align-items-center mt-4">
            <div class="text-muted">
                Menampilkan {{ $jadwal->firstItem() }} - {{ $jadwal->lastItem() }} dari {{ $jadwal->total() }} jadwal
            </div>
            <div>
                {{ $jadwal->links() }}
            </div>
        </div>
    @else
        <!-- Empty State -->
        <div class="card">
            <div class="card-body">
                <div class="empty-state">
                    <i class="ti ti-calendar-off"></i>
                    <h4 class="text-muted">Belum Ada Jadwal</h4>
                    <p class="text-muted mb-4">
                        @if(request()->hasAny(['search', 'ruangan_id', 'bulan', 'tahun']))
                            Tidak ada jadwal yang sesuai dengan filter Anda.
                        @else
                            Mulai tambahkan jadwal kegiatan ruangan.
                        @endif
                    </p>
                    @if(request()->hasAny(['search', 'ruangan_id', 'bulan', 'tahun']))
                        <a href="{{ route('backend.jadwal.index') }}" class="btn btn-outline-primary">
                            <i class="ti ti-refresh"></i> Reset Filter
                        </a>
                    @else
                        <a href="{{ route('backend.jadwal.create') }}" class="btn btn-primary">
                            <i class="ti ti-plus"></i> Tambah Jadwal
                        </a>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        // Auto refresh status setiap 1 menit (opsional)
        // setInterval(function() {
        //     location.reload();
        // }, 60000);
    });
</script>
@endpush