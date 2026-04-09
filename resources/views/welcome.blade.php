@extends('layouts.frontend')

@section('title', 'Beranda - Bhakti Kencana')

@push('styles')
<style>
    /* ===== HERO ===== */
    .hero-section {
        background: linear-gradient(135deg, #ff9800 0%, #f4511e 100%);
        border-radius: 20px;
        padding: 2.5rem 2rem;
        color: white;
        position: relative;
        overflow: hidden;
        margin-bottom: 2.5rem;
    }
    .hero-section::before {
        content: '';
        position: absolute;
        width: 350px; height: 350px;
        background: rgba(255,255,255,0.08);
        border-radius: 50%;
        top: -150px; right: -80px;
        pointer-events: none;
    }
    .hero-inner {
        position: relative;
        z-index: 1;
        display: flex;
        align-items: center;
        gap: 2rem;
    }
    .hero-logo {
        flex-shrink: 0;
        background: white;
        border-radius: 16px;
        padding: 12px;
        box-shadow: 0 6px 20px rgba(0,0,0,0.15);
    }
    .hero-logo img {
        width: 180px; height: 120px;
        object-fit: cover;
        border-radius: 10px;
        display: block;
    }
    .hero-text { flex: 1; }
    .hero-text h1 {
        font-size: clamp(1.4rem, 4vw, 2.2rem);
        font-weight: 700;
        margin-bottom: 0.4rem;
        line-height: 1.2;
    }
    .hero-text p { font-size: 1rem; opacity: 0.9; margin: 0; }
    .hero-cta {
        margin-top: 1.25rem;
        display: flex;
        gap: 0.75rem;
        flex-wrap: wrap;
    }
    .btn-hero-primary {
        background: white;
        color: #e65100;
        font-weight: 600;
        padding: 0.6rem 1.5rem;
        border-radius: 50px;
        border: none;
        font-size: 0.9rem;
        text-decoration: none;
        transition: all 0.2s;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    .btn-hero-primary:hover {
        background: #fff3e0;
        color: #bf360c;
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(0,0,0,0.2);
    }
    .btn-hero-secondary {
        background: rgba(255,255,255,0.2);
        color: white;
        font-weight: 600;
        padding: 0.6rem 1.5rem;
        border-radius: 50px;
        border: 1.5px solid rgba(255,255,255,0.5);
        font-size: 0.9rem;
        text-decoration: none;
        transition: all 0.2s;
        backdrop-filter: blur(4px);
    }
    .btn-hero-secondary:hover {
        background: rgba(255,255,255,0.3);
        color: white;
        transform: translateY(-1px);
    }
    .hero-clock {
        flex-shrink: 0;
        text-align: center;
        background: rgba(255,255,255,0.15);
        padding: 1.25rem 1.5rem;
        border-radius: 16px;
        backdrop-filter: blur(8px);
        border: 1.5px solid rgba(255,255,255,0.25);
        min-width: 170px;
    }
    .clock-time {
        font-size: 2.2rem;
        font-weight: 700;
        letter-spacing: 2px;
        line-height: 1;
        margin-bottom: 0.4rem;
    }
    .clock-date { font-size: 0.82rem; opacity: 0.9; font-weight: 500; }

    /* ===== SECTION HEADER ===== */
    .section-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1.5rem;
        padding-bottom: 0.75rem;
        border-bottom: 2px solid #f0f0f0;
    }
    .section-title {
        font-size: 1.4rem;
        font-weight: 700;
        color: #1a202c;
        display: flex;
        align-items: center;
        gap: 0.6rem;
        margin: 0;
    }
    .section-title i { color: #ff9800; }
    .section-badge {
        background: #ff9800;
        color: white;
        padding: 0.35rem 1rem;
        border-radius: 50px;
        font-weight: 600;
        font-size: 0.85rem;
        white-space: nowrap;
    }

    /* ===== RUANGAN CARDS ===== */
    .ruangan-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
        gap: 1.25rem;
        margin-bottom: 2.5rem;
    }
    .ruangan-card {
        background: white;
        border-radius: 14px;
        padding: 1.25rem;
        box-shadow: 0 2px 12px rgba(0,0,0,0.07);
        border: 1.5px solid transparent;
        transition: all 0.25s ease;
        cursor: pointer;
    }
    .ruangan-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 24px rgba(255,152,0,0.18);
        border-color: #ff9800;
    }
    .ruangan-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 1rem;
        gap: 8px;
    }
    .ruangan-name {
        font-size: 1.1rem;
        font-weight: 700;
        color: #1a202c;
        margin: 0;
        line-height: 1.3;
    }
    .status-badge {
        padding: 0.3rem 0.75rem;
        border-radius: 50px;
        font-size: 0.78rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        white-space: nowrap;
        flex-shrink: 0;
    }
    .status-badge.tersedia { background: #d4edda; color: #155724; }
    .status-badge.dipinjam { background: #f8d7da; color: #721c24; }
    .ruangan-info {
        display: flex;
        flex-direction: column;
        gap: 0.6rem;
        padding-top: 0.75rem;
        border-top: 1px solid #f0f0f0;
    }
    .info-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.875rem;
        color: #4a5568;
    }
    .info-item i { color: #ff9800; width: 18px; flex-shrink: 0; }

    /* ===== TABLE ===== */
    .table-wrapper {
        background: white;
        border-radius: 14px;
        padding: 1.25rem;
        box-shadow: 0 2px 12px rgba(0,0,0,0.07);
        margin-bottom: 2.5rem;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
    .table-modern {
        width: 100%;
        min-width: 480px;
        border-collapse: separate;
        border-spacing: 0 6px;
    }
    .table-modern thead th {
        background: linear-gradient(135deg, #ff9800, #f4511e);
        color: white;
        padding: 0.875rem 1rem;
        font-weight: 600;
        font-size: 0.875rem;
        text-align: left;
        white-space: nowrap;
    }
    .table-modern thead th:first-child { border-radius: 10px 0 0 10px; }
    .table-modern thead th:last-child { border-radius: 0 10px 10px 0; }
    .table-modern tbody tr { background: #f8f9fa; transition: background 0.15s; }
    .table-modern tbody tr:hover { background: #fff3e0; }
    .table-modern tbody td {
        padding: 0.875rem 1rem;
        font-size: 0.875rem;
        color: #374151;
        border: none;
    }
    .table-modern tbody tr td:first-child { border-radius: 10px 0 0 10px; }
    .table-modern tbody tr td:last-child { border-radius: 0 10px 10px 0; }
    .kategori-badge {
        background: #e3f2fd;
        color: #0d47a1;
        padding: 0.3rem 0.7rem;
        border-radius: 6px;
        font-size: 0.8rem;
        font-weight: 500;
        white-space: nowrap;
    }
    .stok-badge {
        background: #f3e5f5;
        color: #6a1b9a;
        padding: 0.3rem 0.7rem;
        border-radius: 6px;
        font-size: 0.8rem;
        font-weight: 600;
    }

    /* ===== CALENDAR ===== */
    .calendar-wrapper {
        background: white;
        border-radius: 14px;
        padding: 1.5rem;
        box-shadow: 0 2px 12px rgba(0,0,0,0.07);
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
    #calendar { min-width: 300px; }

    /* ===== MOBILE ===== */
    @media (max-width: 767px) {
        .hero-inner { flex-direction: column; text-align: center; }
        .hero-logo { display: none; }
        .hero-cta { justify-content: center; }
        .hero-clock { min-width: 0; width: 100%; }
        .clock-time { font-size: 2.8rem; }
        .clock-date { font-size: 0.9rem; }
        .hero-text h1 { font-size: 1.6rem; }
        .ruangan-grid { grid-template-columns: 1fr; }
        .section-header {
            flex-direction: row;
            align-items: center;
            flex-wrap: wrap;
            gap: 0.5rem;
        }
        .section-title { font-size: 1.1rem; }
        .section-badge { font-size: 0.78rem; }
        .table-wrapper { padding: 0.75rem; }
    }
</style>
@endpush

@section('content')
<div class="container py-4">

    {{-- HERO --}}
    <div class="hero-section">
        <div class="hero-inner">
            <div class="hero-logo">
                <img src="{{ asset('assets/backend/images/bku.jpeg') }}" alt="Universitas Bhakti Kencana">
            </div>
            <div class="hero-text">
                @auth
                    <h1>Selamat Datang, {{ Auth::user()->name }}! 👋</h1>
                    <p>Kelola peminjaman ruangan dan barang dengan mudah dan cepat</p>
                    <div class="hero-cta">
                        <a href="{{ route('user.booking.create') }}" class="btn-hero-primary">
                            <i class="ti ti-calendar-plus me-1"></i> Booking Ruangan
                        </a>
                        <a href="{{ route('user.peminjaman.create') }}" class="btn-hero-secondary">
                            <i class="ti ti-package me-1"></i> Pinjam Barang
                        </a>
                    </div>
                @else
                    <h1>Bhakti Kencana</h1>
                    <p>Sistem Penjadwalan Ruangan & Peminjaman Fasilitas</p>
                    <div class="hero-cta">
                        <a href="{{ route('login') }}" class="btn-hero-primary">
                            <i class="ti ti-login me-1"></i> Masuk Sekarang
                        </a>
                    </div>
                @endauth
            </div>
            <div class="hero-clock">
                <div class="clock-time" id="clock">00:00:00</div>
                <div class="clock-date" id="clockDate">Loading...</div>
            </div>
        </div>
    </div>

    {{-- DAFTAR RUANGAN --}}
    <div class="section-header">
        <h2 class="section-title">
            <i class="ti ti-building"></i> Daftar Ruangan
        </h2>
        <span class="section-badge">{{ $ruangans->count() }} Ruangan</span>
    </div>
    <div class="ruangan-grid mb-4">
        @forelse($ruangans as $ruangan)
            <div class="ruangan-card">
                <div class="ruangan-header">
                    <h3 class="ruangan-name">{{ $ruangan->nama_ruangan }}</h3>
                    <span class="status-badge {{ $ruangan->status }}">
                        <i class="ti ti-{{ $ruangan->status == 'tersedia' ? 'check' : 'clock' }}"></i>
                        {{ ucfirst($ruangan->status) }}
                    </span>
                </div>
                <div class="ruangan-info">
                    <div class="info-item">
                        <i class="ti ti-users"></i>
                        <span>Kapasitas: <strong>{{ $ruangan->kapasitas }} Orang</strong></span>
                    </div>
                    <div class="info-item">
                        <i class="ti ti-map-pin"></i>
                        <span>Lokasi: <strong>{{ $ruangan->lokasi ?? '-' }}</strong></span>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info">
                    <i class="ti ti-info-circle"></i> Belum ada ruangan tersedia.
                </div>
            </div>
        @endforelse
    </div>

    {{-- DAFTAR BARANG --}}
    <div class="section-header">
        <h2 class="section-title">
            <i class="ti ti-package"></i> Daftar Barang per Ruangan
        </h2>
        <span class="section-badge">{{ $barangRuangans->count() }} Barang</span>
    </div>
    <div class="table-wrapper">
        <table class="table-modern">
            <thead>
                <tr>
                    <th style="width:50px">No</th>
                    <th>Nama Barang</th>
                    <th>Kategori</th>
                    <th>Stok</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($barangRuangans as $index => $item)
                    <tr>
                        <td><strong>{{ $index + 1 }}</strong></td>
                        <td>
                            <strong>{{ $item->barang->nama }}</strong><br>
                            <small class="text-muted">{{ $item->ruangan->nama_ruangan }}</small>
                        </td>
                        <td><span class="kategori-badge">{{ $item->barang->kategori->nama ?? '-' }}</span></td>
                        <td><span class="stok-badge">{{ $item->qty }} Unit</span></td>
                        <td>
                            <span class="status-badge {{ $item->status }}">
                                <i class="ti ti-{{ $item->status == 'tersedia' ? 'check' : 'clock' }}"></i>
                                {{ ucfirst($item->status) }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-3">
                            <em class="text-muted">Belum ada barang tersedia</em>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- KALENDER --}}
    <div class="section-header">
        <h2 class="section-title">
            <i class="ti ti-calendar-event"></i> Kalender Jadwal Booking
        </h2>
    </div>
    <div class="calendar-wrapper">
        <div id="calendar"></div>
    </div>

</div>
@endsection

@push('scripts')
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js'></script>
<script>
    function updateClock() {
        const now = new Date();
        const pad = n => String(n).padStart(2,'0');
        document.getElementById('clock').textContent =
            `${pad(now.getHours())}:${pad(now.getMinutes())}:${pad(now.getSeconds())}`;
        const days = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
        const months = ['Januari','Februari','Maret','April','Mei','Juni',
                        'Juli','Agustus','September','Oktober','November','Desember'];
        document.getElementById('clockDate').textContent =
            `${days[now.getDay()]}, ${now.getDate()} ${months[now.getMonth()]} ${now.getFullYear()}`;
    }
    setInterval(updateClock, 1000);
    updateClock();

    document.addEventListener('DOMContentLoaded', function() {
        var calendar = new FullCalendar.Calendar(document.getElementById('calendar'), {
            initialView: 'dayGridMonth',
            locale: 'id',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            buttonText: { today: 'Hari Ini', month: 'Bulan', week: 'Minggu', day: 'Hari' },
            events: @json($jadwals),
            eventClick: function(info) {
                alert(info.event.extendedProps.description
                    .replace(/<br>/g, '\n').replace(/<[^>]*>/g, ''));
            },
            height: 'auto',
            eventDisplay: 'block'
        });
        calendar.render();
    });
</script>
@endpush