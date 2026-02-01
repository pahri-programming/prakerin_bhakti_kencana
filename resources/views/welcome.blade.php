@extends('layouts.frontend')

@section('title', 'Beranda - Bhakti Kencana')

@push('styles')
<style>
    .hero-section {
        background: linear-gradient(135deg, #ff9800 0%, #ff5722 100%);
        padding: 3rem 0;
        margin-bottom: 3rem;
        border-radius: 20px;
        color: white;
        position: relative;
        overflow: hidden;
    }

    .hero-section::before {
        content: '';
        position: absolute;
        width: 400px;
        height: 400px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
        top: -200px;
        right: -100px;
    }

    .hero-content {
        position: relative;
        z-index: 1;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 3rem;
        padding: 0 2rem;
    }

    .hero-logo {
        flex-shrink: 0;
        background: white;
        border-radius: 20px;
        padding: 15px 20px;
        box-shadow: 0 8px 20px rgba(0,0,0,0.15);
        transition: transform 0.3s ease;
    }

    .hero-logo:hover {
        transform: scale(1.05);
    }

    .hero-logo img {
        width: 200px;
        height: 120px;
        object-fit: cover;
        border-radius: 12px;
        display: block;
    }

    .hero-text-content {
        flex: 1;
        text-align: center;
    }

    .hero-clock {
        flex-shrink: 0;
        text-align: center;
        background: rgba(255, 255, 255, 0.2);
        padding: 20px 25px;
        border-radius: 20px;
        backdrop-filter: blur(10px);
        border: 2px solid rgba(255, 255, 255, 0.3);
        min-width: 200px;
        box-shadow: 0 8px 20px rgba(0,0,0,0.1);
    }

    .clock-time {
        font-size: 2.8rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
        font-family: 'Segoe UI', 'Arial', sans-serif;
        text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
        letter-spacing: 2px;
    }

    .clock-date {
        font-size: 0.95rem;
        opacity: 0.95;
        font-weight: 600;
        letter-spacing: 0.5px;
    }

    .welcome-text {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }

    .welcome-subtitle {
        font-size: 1.1rem;
        opacity: 0.95;
    }

    .section-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 2rem;
        padding-bottom: 1rem;
        border-bottom: 3px solid #f0f0f0;
    }

    .section-title {
        font-size: 1.75rem;
        font-weight: 700;
        color: #2d3748;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin: 0;
    }

    .section-title i {
        color: #ff9800;
        font-size: 2rem;
    }

    .section-badge {
        background: #ff9800;
        color: white;
        padding: 0.5rem 1.25rem;
        border-radius: 50px;
        font-weight: 600;
        font-size: 0.9rem;
    }

    /* Ruangan Cards */
    .ruangan-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 1.5rem;
        margin-bottom: 3rem;
    }

    .ruangan-card {
        background: white;
        border-radius: 15px;
        padding: 1.5rem;
        box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        transition: all 0.3s ease;
        border: 2px solid transparent;
    }

    .ruangan-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.12);
        border-color: #ff9800;
    }

    .ruangan-header {
        display: flex;
        justify-content: space-between;
        align-items: start;
        margin-bottom: 1rem;
    }

    .ruangan-name {
        font-size: 1.25rem;
        font-weight: 700;
        color: #2d3748;
        margin: 0;
    }

    .status-badge {
        padding: 0.375rem 0.875rem;
        border-radius: 50px;
        font-size: 0.8rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
    }

    .status-badge.tersedia {
        background: #d4edda;
        color: #155724;
    }

    .status-badge.dipinjam {
        background: #f8d7da;
        color: #721c24;
    }

    .status-badge i {
        font-size: 0.9rem;
    }

    .ruangan-info {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
        padding-top: 1rem;
        border-top: 1px solid #e2e8f0;
    }

    .info-item {
        display: flex;
        align-items: center;
        gap: 0.625rem;
        font-size: 0.9rem;
        color: #4a5568;
    }

    .info-item i {
        color: #ff9800;
        font-size: 1.1rem;
        width: 20px;
    }

    /* Barang Table */
    .table-container {
        background: white;
        border-radius: 15px;
        padding: 1.5rem;
        box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        margin-bottom: 3rem;
    }

    .table-modern {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0 0.5rem;
    }

    .table-modern thead th {
        background: linear-gradient(135deg, #ff9800 0%, #ff5722 100%);
        color: white;
        padding: 1rem;
        font-weight: 600;
        text-align: left;
        font-size: 0.9rem;
    }

    .table-modern thead th:first-child {
        border-radius: 10px 0 0 10px;
    }

    .table-modern thead th:last-child {
        border-radius: 0 10px 10px 0;
    }

    .table-modern tbody tr {
        background: #f8f9fa;
        transition: all 0.2s ease;
    }

    .table-modern tbody tr:hover {
        background: #fff3e0;
        transform: scale(1.01);
    }

    .table-modern tbody td {
        padding: 1rem;
        border: none;
        font-size: 0.9rem;
    }

    .table-modern tbody tr td:first-child {
        border-radius: 10px 0 0 10px;
    }

    .table-modern tbody tr td:last-child {
        border-radius: 0 10px 10px 0;
    }

    .kategori-badge {
        background: #e3f2fd;
        color: #1565c0;
        padding: 0.375rem 0.75rem;
        border-radius: 8px;
        font-size: 0.85rem;
        font-weight: 500;
    }

    .stok-badge {
        background: #f3e5f5;
        color: #6a1b9a;
        padding: 0.375rem 0.75rem;
        border-radius: 8px;
        font-size: 0.85rem;
        font-weight: 600;
    }

    /* Kalender Section */
    .calendar-section {
        background: white;
        border-radius: 15px;
        padding: 2rem;
        box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    }

    #calendar {
        border-radius: 10px;
        overflow: hidden;
    }

    @media (max-width: 768px) {
        .welcome-text {
            font-size: 1.75rem;
        }

        .hero-content {
            flex-direction: column;
            text-align: center;
            gap: 1.5rem;
            padding: 0 1rem;
        }

        .hero-logo {
            padding: 12px 15px;
        }

        .hero-logo img {
            width: 160px;
            height: 100px;
        }

        .clock-time {
            font-size: 2.2rem;
        }

        .hero-clock {
            min-width: 180px;
            padding: 18px 22px;
        }

        .ruangan-grid {
            grid-template-columns: 1fr;
        }

        .table-container {
            overflow-x: auto;
        }

        .section-header {
            flex-direction: column;
            align-items: start;
            gap: 1rem;
        }
    }
</style>
@endpush

@section('content')
<div class="container py-4">
    <!-- Hero Section dengan Welcome Message, Logo, dan Jam -->
    <div class="hero-section">
        <div class="hero-content">
            <!-- Logo Universitas -->
            <div class="hero-logo">
                <img src="{{ asset('assets/backend/images/bku.jpeg') }}" alt="Logo Universitas Bhakti Kencana">
            </div>

            <!-- Text Content -->
            <div class="hero-text-content">
                @auth
                    <h1 class="welcome-text">
                        Selamat Datang, {{ Auth::user()->name }}! 👋
                    </h1>
                    <p class="welcome-subtitle">
                        Kelola peminjaman ruangan dan barang dengan mudah dan cepat
                    </p>
                @else
                    <h1 class="welcome-text">Bhakti Kencana</h1>
                    <p class="welcome-subtitle">
                        Sistem Penjadwalan Ruangan & Peminjaman Fasilitas
                    </p>
                @endauth
            </div>

            <!-- Clock Widget -->
            <div class="hero-clock">
                <div class="clock-time" id="clock">00:00:00</div>
                <div class="clock-date" id="date">Loading...</div>
            </div>
        </div>
    </div>

    <!-- Section: Daftar Ruangan -->
    <div class="section-header">
        <h2 class="section-title">
            <i class="ti ti-building"></i>
            Daftar Ruangan
        </h2>
        <span class="section-badge">{{ $ruangans->count() }} Ruangan</span>
    </div>

    <div class="ruangan-grid">
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
                        <span>Lokasi: <strong>{{ $ruangan->lokasi ?? 'Tidak ada info' }}</strong></span>
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

    <!-- Section: Daftar Barang -->
    <div class="section-header">
        <h2 class="section-title">
            <i class="ti ti-package"></i>
            Daftar Barang
        </h2>
        <span class="section-badge">{{ $barangs->count() }} Barang</span>
    </div>

    <div class="table-container">
        <table class="table-modern">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Barang</th>
                    <th>Kategori</th>
                    <th>Stok</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($barangs as $index => $barang)
                    <tr>
                        <td><strong>{{ $index + 1 }}</strong></td>
                        <td><strong>{{ $barang->nama }}</strong></td>
                        <td>
                            <span class="kategori-badge">
                                {{ $barang->kategori->nama ?? 'Tidak ada kategori' }}
                            </span>
                        </td>
                        <td>
                            <span class="stok-badge">{{ $barang->stok }} Unit</span>
                        </td>
                        <td>
                            <span class="status-badge {{ $barang->status }}">
                                <i class="ti ti-{{ $barang->status == 'tersedia' ? 'check' : 'clock' }}"></i>
                                {{ ucfirst($barang->status) }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center">
                            <em>Belum ada barang tersedia</em>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Section: Kalender Jadwal-->
    <div class="section-header">
        <h2 class="section-title">
            <i class="ti ti-calendar-event"></i>
            Kalender Jadwal Booking Ruangan
        </h2>
    </div>

    <div class="calendar-section">
        <div id="calendar"></div>
    </div>
</div>
@endsection

@push('scripts')
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js'></script>
<script>
    // Real-time Clock
    function updateClock() {
        const now = new Date();
        
        // Format time
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const seconds = String(now.getSeconds()).padStart(2, '0');
        document.getElementById('clock').textContent = `${hours}:${minutes}:${seconds}`;
        
        // Format date in Indonesian
        const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 
                       'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        
        const dayName = days[now.getDay()];
        const day = now.getDate();
        const month = months[now.getMonth()];
        const year = now.getFullYear();
        
        document.getElementById('date').textContent = `${dayName}, ${day} ${month} ${year}`;
    }
    
    // Update clock every second
    setInterval(updateClock, 1000);
    updateClock(); // Initial call

    // Calendar initialization
    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');
        
        // DEBUG: Log events data
        const eventsData = @json($jadwals);
        console.log('=== CALENDAR DEBUG ===');
        console.log('Total Events:', eventsData.length);
        console.log('Events Data:', eventsData);
        console.log('FullCalendar Loaded:', typeof FullCalendar !== 'undefined');
        console.log('===================');
        
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            locale: 'id',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            buttonText: {
                today: 'Hari Ini',
                month: 'Bulan',
                week: 'Minggu',
                day: 'Hari'
            },
            events: eventsData,
            eventClick: function(info) {
                alert(info.event.extendedProps.description.replace(/<br>/g, '\n').replace(/<[^>]*>/g, ''));
            },
            height: 'auto',
            eventDisplay: 'block',
            // DEBUG: Log when events are added
            eventDidMount: function(info) {
                console.log('Event mounted:', info.event.title, 'on', info.event.start);
            }
        });
        
        calendar.render();
        
        // DEBUG: Check if events rendered
        setTimeout(() => {
            console.log('Calendar events after render:', calendar.getEvents().length);
        }, 1000);
    });
</script>
@endpush