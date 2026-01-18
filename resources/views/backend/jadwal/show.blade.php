@extends('layouts.backend')

@section('title', 'Detail Jadwal')

@push('styles')
<style>
    body {
        background: #f5f7fa;
    }

    .page-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 20px;
        padding: 30px;
        margin-bottom: 30px;
        color: white;
        box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
        position: relative;
        overflow: hidden;
    }

    .page-header::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -10%;
        width: 400px;
        height: 400px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
    }

    .page-header .content {
        position: relative;
        z-index: 1;
    }

    .page-header h2 {
        font-weight: 700;
        margin: 0 0 10px 0;
    }

    .page-header p {
        margin: 0;
        opacity: 0.9;
    }

    .status-badge-large {
        position: absolute;
        top: 30px;
        right: 30px;
        padding: 12px 24px;
        border-radius: 30px;
        font-weight: 700;
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    }

    .status-badge-large.selesai {
        background: linear-gradient(135deg, #78909c 0%, #546e7a 100%);
        color: white;
    }

    .status-badge-large.berlangsung {
        background: linear-gradient(135deg, #66bb6a 0%, #43a047 100%);
        color: white;
        animation: pulse-status 2s infinite;
    }

    .status-badge-large.akan-datang {
        background: linear-gradient(135deg, #42a5f5 0%, #1e88e5 100%);
        color: white;
    }

    @keyframes pulse-status {
        0%, 100% {
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }
        50% {
            box-shadow: 0 4px 25px rgba(102, 187, 106, 0.5);
        }
    }

    .info-card {
        background: white;
        border-radius: 20px;
        border: none;
        box-shadow: 0 2px 20px rgba(0,0,0,0.06);
        margin-bottom: 20px;
        overflow: hidden;
        transition: all 0.3s ease;
    }

    .info-card:hover {
        box-shadow: 0 8px 30px rgba(0,0,0,0.1);
        transform: translateY(-2px);
    }

    .card-header-custom {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        padding: 20px 30px;
        border-bottom: 2px solid #dee2e6;
    }

    .card-header-custom h5 {
        margin: 0;
        font-weight: 600;
        color: #495057;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .card-header-custom .icon-badge {
        width: 40px;
        height: 40px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.2rem;
    }

    .card-body-custom {
        padding: 30px;
    }

    .detail-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
    }

    .detail-item {
        background: linear-gradient(135deg, #f8f9fa 0%, #f1f3f5 100%);
        padding: 20px;
        border-radius: 15px;
        border-left: 4px solid #667eea;
        transition: all 0.3s ease;
    }

    .detail-item:hover {
        transform: translateX(5px);
        border-left-color: #764ba2;
    }

    .detail-label {
        font-size: 0.85rem;
        font-weight: 600;
        color: #6c757d;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .detail-label i {
        color: #667eea;
        font-size: 1.1rem;
    }

    .detail-value {
        font-size: 1.1rem;
        font-weight: 700;
        color: #212529;
        word-break: break-word;
    }

    .timeline-card {
        background: linear-gradient(135deg, #e8eaf6 0%, #c5cae9 100%);
        border-radius: 15px;
        padding: 25px;
        border: none;
        position: relative;
        overflow: hidden;
    }

    .timeline-card::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 4px;
        background: linear-gradient(180deg, #5e35b1 0%, #512da8 100%);
    }

    .timeline-card h6 {
        font-weight: 700;
        color: #4527a0;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .timeline-item {
        display: flex;
        align-items: center;
        gap: 15px;
        padding: 15px;
        background: rgba(255, 255, 255, 0.7);
        border-radius: 12px;
        margin-bottom: 12px;
    }

    .timeline-item:last-child {
        margin-bottom: 0;
    }

    .timeline-icon {
        width: 50px;
        height: 50px;
        background: linear-gradient(135deg, #5e35b1 0%, #512da8 100%);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.5rem;
        flex-shrink: 0;
    }

    .timeline-content {
        flex: 1;
    }

    .timeline-time {
        font-size: 1.2rem;
        font-weight: 700;
        color: #4527a0;
        margin-bottom: 4px;
    }

    .timeline-label {
        font-size: 0.85rem;
        color: #6c757d;
        font-weight: 600;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
        margin-top: 20px;
    }

    .stat-box {
        background: white;
        padding: 20px;
        border-radius: 12px;
        text-align: center;
        border: 2px solid #e9ecef;
        transition: all 0.3s ease;
    }

    .stat-box:hover {
        border-color: #667eea;
        transform: translateY(-3px);
    }

    .stat-icon {
        width: 50px;
        height: 50px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.3rem;
        margin: 0 auto 12px;
    }

    .stat-value {
        font-size: 1.4rem;
        font-weight: 700;
        color: #212529;
        margin-bottom: 4px;
    }

    .stat-label {
        font-size: 0.85rem;
        color: #6c757d;
        font-weight: 600;
    }

    .action-card {
        background: white;
        border-radius: 20px;
        padding: 25px 30px;
        box-shadow: 0 2px 20px rgba(0,0,0,0.06);
        margin-top: 20px;
    }

    .btn-action {
        padding: 12px 28px;
        border-radius: 12px;
        font-weight: 600;
        font-size: 0.95rem;
        transition: all 0.3s ease;
        border: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-primary-custom {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
    }

    .btn-primary-custom:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 25px rgba(102, 126, 234, 0.4);
        color: white;
    }

    .btn-warning-custom {
        background: linear-gradient(135deg, #ffa726 0%, #fb8c00 100%);
        color: white;
        box-shadow: 0 4px 15px rgba(255, 167, 38, 0.3);
    }

    .btn-warning-custom:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 25px rgba(255, 167, 38, 0.4);
        color: white;
    }

    .btn-danger-custom {
        background: linear-gradient(135deg, #ef5350 0%, #e53935 100%);
        color: white;
        box-shadow: 0 4px 15px rgba(239, 83, 80, 0.3);
    }

    .btn-danger-custom:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 25px rgba(239, 83, 80, 0.4);
        color: white;
    }

    .btn-secondary-custom {
        background: #e9ecef;
        color: #495057;
    }

    .btn-secondary-custom:hover {
        background: #dee2e6;
        color: #212529;
    }

    .empty-state {
        text-align: center;
        padding: 40px 20px;
        color: #adb5bd;
    }

    .empty-state i {
        font-size: 4rem;
        margin-bottom: 15px;
        opacity: 0.5;
    }

    @media (max-width: 768px) {
        .status-badge-large {
            position: static;
            display: inline-block;
            margin-top: 15px;
        }

        .detail-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="content">
            <h2><i class="ti ti-calendar-event"></i> Detail Jadwal</h2>
            <p>Informasi lengkap jadwal kegiatan</p>
        </div>
        
        @php
            $statusClass = match($jadwal->status_waktu) {
                'selesai' => 'selesai',
                'berlangsung' => 'berlangsung',
                'akan-datang' => 'akan-datang',
                default => 'akan-datang'
            };
            
            $statusLabel = match($jadwal->status_waktu) {
                'selesai' => 'Selesai',
                'berlangsung' => 'Berlangsung',
                'akan-datang' => 'Akan Datang',
                default => 'Unknown'
            };
        @endphp
        
        <span class="status-badge-large {{ $statusClass }}">
            @if($jadwal->status_waktu === 'berlangsung')
                <i class="ti ti-clock me-1"></i>
            @elseif($jadwal->status_waktu === 'selesai')
                <i class="ti ti-check me-1"></i>
            @else
                <i class="ti ti-hourglass me-1"></i>
            @endif
            {{ $statusLabel }}
        </span>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Informasi Utama -->
            <div class="info-card">
                <div class="card-header-custom">
                    <h5>
                        <span class="icon-badge">
                            <i class="ti ti-info-circle"></i>
                        </span>
                        Informasi Jadwal
                    </h5>
                </div>
                <div class="card-body-custom">
                    <div class="detail-grid">
                        <div class="detail-item">
                            <div class="detail-label">
                                <i class="ti ti-door"></i> Ruangan
                            </div>
                            <div class="detail-value">{{ $jadwal->ruangan->nama_ruangan ?? 'N/A' }}</div>
                        </div>

                        <div class="detail-item">
                            <div class="detail-label">
                                <i class="ti ti-map-pin"></i> Lokasi
                            </div>
                            <div class="detail-value">{{ $jadwal->ruangan->lokasi ?? 'N/A' }}</div>
                        </div>

                        <div class="detail-item">
                            <div class="detail-label">
                                <i class="ti ti-users"></i> Kapasitas
                            </div>
                            <div class="detail-value">{{ $jadwal->ruangan->kapasitas ?? 0 }} Orang</div>
                        </div>

                        <div class="detail-item">
                            <div class="detail-label">
                                <i class="ti ti-calendar"></i> Tanggal
                            </div>
                            <div class="detail-value">{{ $jadwal->tanggal_format }}</div>
                        </div>

                        <div class="detail-item">
                            <div class="detail-label">
                                <i class="ti ti-sun"></i> Hari
                            </div>
                            <div class="detail-value">{{ $jadwal->hari }}</div>
                        </div>

                        <div class="detail-item">
                            <div class="detail-label">
                                <i class="ti ti-file-text"></i> Kegiatan
                            </div>
                            <div class="detail-value">{{ $jadwal->kegiatan }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Timeline Waktu -->
            <div class="timeline-card">
                <h6><i class="ti ti-clock"></i> Jadwal Waktu</h6>
                
                <div class="timeline-item">
                    <div class="timeline-icon">
                        <i class="ti ti-player-play"></i>
                    </div>
                    <div class="timeline-content">
                        <div class="timeline-time">{{ substr($jadwal->waktu_mulai, 0, 5) }}</div>
                        <div class="timeline-label">Waktu Mulai</div>
                    </div>
                </div>

                <div class="timeline-item">
                    <div class="timeline-icon">
                        <i class="ti ti-player-stop"></i>
                    </div>
                    <div class="timeline-content">
                        <div class="timeline-time">{{ substr($jadwal->waktu_selesai, 0, 5) }}</div>
                        <div class="timeline-label">Waktu Selesai</div>
                    </div>
                </div>

                @php
                    $start = \Carbon\Carbon::parse('2000-01-01 ' . $jadwal->waktu_mulai);
                    $end = \Carbon\Carbon::parse('2000-01-01 ' . $jadwal->waktu_selesai);
                    $diffMinutes = $end->diffInMinutes($start);
                    $hours = floor($diffMinutes / 60);
                    $minutes = $diffMinutes % 60;
                    $duration = '';
                    if ($hours > 0) $duration .= $hours . ' jam ';
                    if ($minutes > 0) $duration .= $minutes . ' menit';
                    if (empty($duration)) $duration = '0 menit';
                @endphp

                <div class="stats-grid">
                    <div class="stat-box">
                        <div class="stat-icon">
                            <i class="ti ti-hourglass"></i>
                        </div>
                        <div class="stat-value">{{ $duration }}</div>
                        <div class="stat-label">Total Durasi</div>
                    </div>

                    {{-- <div class="stat-box">
                        <div class="stat-icon">
                            <i class="ti ti-calendar-stats"></i>
                        </div>
                        <div class="stat-value">
                            @php
                                $now = \Carbon\Carbon::now();
                                $jadwalDate = \Carbon\Carbon::parse($jadwal->tanggal);
                                $diff = $now->diffInDays($jadwalDate, false);
                            @endphp
                            @if($diff > 0)
                                {{ $diff }} Hari
                            @elseif($diff < 0)
                                {{ abs($diff) }} Hari Lalu
                            @else
                                Hari Ini
                            @endif
                        </div>
                        <div class="stat-label">
                            @if($diff > 0)
                                Akan Datang
                            @elseif($diff < 0)
                                Sudah Lewat
                            @else
                                Sedang Berlangsung
                            @endif
                        </div>
                    </div> --}}
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Informasi Tambahan -->
            <div class="info-card">
                <div class="card-header-custom">
                    <h5>
                        <span class="icon-badge">
                            <i class="ti ti-file-info"></i>
                        </span>
                        Informasi Tambahan
                    </h5>
                </div>
                <div class="card-body-custom">
                    <div class="detail-item mb-3">
                        <div class="detail-label">
                            <i class="ti ti-id"></i> ID Jadwal
                        </div>
                        <div class="detail-value">#{{ str_pad($jadwal->id, 5, '0', STR_PAD_LEFT) }}</div>
                    </div>

                    <div class="detail-item mb-3">
                        <div class="detail-label">
                            <i class="ti ti-calendar-plus"></i> Dibuat Pada
                        </div>
                        <div class="detail-value" style="font-size: 0.95rem;">
                            {{ $jadwal->created_at->translatedFormat('d F Y') }}<br>
                            <small class="text-muted">{{ $jadwal->created_at->format('H:i') }} WIB</small>
                        </div>
                    </div>

                    <div class="detail-item">
                        <div class="detail-label">
                            <i class="ti ti-refresh"></i> Terakhir Diubah
                        </div>
                        <div class="detail-value" style="font-size: 0.95rem;">
                            {{ $jadwal->updated_at->translatedFormat('d F Y') }}<br>
                            <small class="text-muted">{{ $jadwal->updated_at->format('H:i') }} WIB</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Status Info -->
            <div class="info-card">
                <div class="card-body-custom">
                    <div class="text-center">
                        <div class="stat-icon mx-auto mb-3" style="width: 70px; height: 70px; font-size: 2rem;">
                            @if($jadwal->status_waktu === 'berlangsung')
                                <i class="ti ti-clock"></i>
                            @elseif($jadwal->status_waktu === 'selesai')
                                <i class="ti ti-check"></i>
                            @else
                                <i class="ti ti-hourglass"></i>
                            @endif
                        </div>
                        <h4 class="fw-bold mb-2">{{ $statusLabel }}</h4>
                        <p class="text-muted mb-0">
                            @if($jadwal->status_waktu === 'berlangsung')
                                Kegiatan sedang berlangsung saat ini
                            @elseif($jadwal->status_waktu === 'selesai')
                                Kegiatan telah selesai dilaksanakan
                            @else
                                Kegiatan akan dilaksanakan
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Card -->
    <div class="action-card">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
            <a href="{{ route('backend.jadwal.index') }}" class="btn btn-secondary-custom btn-action">
                <i class="ti ti-arrow-left"></i> Kembali ke Daftar
            </a>
            
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('backend.jadwal.edit', $jadwal->id) }}" class="btn btn-warning-custom btn-action">
                    <i class="ti ti-edit"></i> Edit Jadwal
                </a>
                
                <form action="{{ route('backend.jadwal.destroy', $jadwal->id) }}" 
                      method="POST" 
                      class="d-inline"
                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus jadwal ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger-custom btn-action">
                        <i class="ti ti-trash"></i> Hapus Jadwal
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Animate on scroll
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    });

    document.querySelectorAll('.info-card, .timeline-card').forEach(el => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(20px)';
        el.style.transition = 'all 0.5s ease';
        observer.observe(el);
    });
});
</script>
@endpush