@extends('layouts.frontend')

@section('content')
<div class="main-wrapper bg-light">
  <!-- Hero Section -->
  <section class="py-5 bg-white position-relative">
    <div class="container">
      <div class="row align-items-center">
        <div class="col-lg-6 mb-4 mb-lg-0">
          <h1 class="fw-bold display-3 text-primary mb-3">Bhakti Kencana</h1>
          <p class="fs-5 text-muted mb-3">
            Sistem Penjadwalan Ruangan Labkom & Peminjaman Barang.
          </p>
          <p class="fw-semibold text-dark mb-4">
            <i class="bi bi-lightning-charge text-warning me-1"></i> Cepat. 
            <i class="bi bi-check-circle text-success me-1 ms-2"></i> Rapi. 
            <i class="bi bi-shield-check text-info me-1 ms-2"></i> Bebas Bentrok.
          </p>
          <a href="{{ route('bookings.create') }}" class="btn btn-primary btn-lg rounded-pill shadow-sm px-4 py-3 btn-hover">
            <i class="bi bi-calendar-plus me-2"></i> Booking Sekarang
          </a>
        </div>
        <div class="col-lg-6 text-center">
          <div class="image-container position-relative">
            <img src="{{ asset('assets/backend/images/bku.jpeg') }}" alt="Ilustrasi Ruangan" class="img-fluid rounded-4 shadow-lg main-image">
            <div class="image-overlay"></div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Kalender Section -->
  <section class="py-5 bg-light">
    <div class="container">
      <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-header bg-white border-0 p-4">
          <h2 class="text-center mb-0 fw-semibold text-dark">
            <i class="bi bi-calendar3 me-2 text-primary"></i>
            Kalender Jadwal & Booking
          </h2>
        </div>
        <div class="card-body p-4">
          <div id="calendar" class="bg-white rounded-3 p-3"></div>
          <div class="mt-4">
            <div class="row align-items-center">
              <div class="col-md-3">
                <h6 class="fw-semibold text-muted mb-2">
                  <i class="bi bi-info-circle me-1"></i> Keterangan:
                </h6>
              </div>
              <div class="col-md-9">
                <div class="d-flex flex-wrap gap-4">
                  <div class="legend-item d-flex align-items-center">
                    <span class="legend-box bg-warning me-2"></span>
                    <span class="small fw-medium">Booking Diterima / Selesai</span>
                  </div>
                  <div class="legend-item d-flex align-items-center">
                    <span class="legend-box bg-info me-2"></span>
                    <span class="small fw-medium">Jadwal Tetap</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>

<!-- FullCalendar CSS & JS -->
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>

<!-- FullCalendar Init Script -->
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const calendarEl = document.getElementById('calendar');
    const calendar = new FullCalendar.Calendar(calendarEl, {
      initialView: 'dayGridMonth',
      locale: 'id',
      height: 'auto',
      aspectRatio: 1.6,
      headerToolbar: {
        left: 'prev,next today',
        center: 'title',
        right: 'dayGridMonth,listMonth'
      },
      buttonText: {
        today: 'Hari Ini',
        month: 'Bulan',
        listMonth: 'List'
      },
      events: @json($jadwals),
      eventDisplay: 'block',
      eventTextColor: '#fff',
      eventDidMount: function (info) {
        if (info.event.extendedProps.description) {
          new bootstrap.Tooltip(info.el, {
            title: info.event.extendedProps.description,
            placement: 'top',
            trigger: 'hover',
            container: 'body',
            html: true,
          });
        }
      }
    });
    calendar.render();
  });
</script>

<style>
  :root {
    --primary-color: #0d6efd;
    --primary-rgb: 13, 110, 253;
    --bs-gray-100: #f8f9fa;
    --bs-gray-500: #6c757d;
    --bs-gray-700: #495057;
    --bs-gray-900: #212529;
  }

  .main-wrapper {
    min-height: 100vh;
    background-color: var(--bs-gray-100);
  }

  /* Hero Section Enhancements */
  .text-primary {
    color: var(--primary-color) !important;
  }

  .display-3 {
    font-weight: 800;
    letter-spacing: -0.02em;
  }

  .btn-hover {
    transition: all 0.3s ease;
    background: linear-gradient(135deg, var(--primary-color) 0%, #0056b3 100%);
    border: none;
    font-weight: 600;
  }

  .btn-hover:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(var(--primary-rgb), 0.3) !important;
    background: linear-gradient(135deg, #0056b3 0%, var(--primary-color) 100%);
  }

  /* Image Container */
  .image-container {
    overflow: hidden;
    border-radius: 1rem;
  }

  .main-image {
    max-height: 400px;
    object-fit: cover;
    width: 100%;
    transition: transform 0.5s ease;
  }

  .image-container:hover .main-image {
    transform: scale(1.02);
  }

  .image-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(45deg, rgba(var(--primary-rgb), 0.1) 0%, rgba(var(--primary-rgb), 0.05) 100%);
    border-radius: 1rem;
    pointer-events: none;
  }

  /* Calendar Section */
  .card {
    border: 1px solid rgba(0, 0, 0, 0.06) !important;
    backdrop-filter: blur(10px);
  }

  .card-header {
    background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%) !important;
  }

  /* Legend Styling */
  .legend-box {
    display: inline-block;
    width: 16px;
    height: 16px;
    border-radius: 50%;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  }

  .legend-item {
    transition: all 0.2s ease;
    padding: 0.25rem 0.5rem;
    border-radius: 0.5rem;
  }

  .legend-item:hover {
    background-color: rgba(var(--primary-rgb), 0.05);
    transform: translateY(-1px);
  }

  /* Calendar Enhancements */
  #calendar {
    box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.05);
    border: 1px solid rgba(0, 0, 0, 0.06);
  }

  .fc-toolbar-title {
    font-size: 1.5rem !important;
    font-weight: 700 !important;
    color: var(--bs-gray-900) !important;
  }

  .fc-button-primary {
    background-color: var(--primary-color) !important;
    border-color: var(--primary-color) !important;
    font-weight: 600 !important;
    border-radius: 0.5rem !important;
    padding: 0.5rem 1rem !important;
    transition: all 0.2s ease !important;
  }

  .fc-button-primary:hover {
    background-color: #0056b3 !important;
    border-color: #0056b3 !important;
    transform: translateY(-1px) !important;
    box-shadow: 0 4px 12px rgba(var(--primary-rgb), 0.3) !important;
  }

  .fc-button-primary:not(:disabled):active,
  .fc-button-primary:not(:disabled).fc-button-active {
    background-color: #0056b3 !important;
    border-color: #0056b3 !important;
  }

  .fc-daygrid-day-number {
    color: var(--bs-gray-700) !important;
    font-weight: 600 !important;
    padding: 0.25rem !important;
  }

  .fc-col-header-cell {
    background-color: rgba(var(--primary-rgb), 0.05) !important;
    font-weight: 600 !important;
    color: var(--primary-color) !important;
    border-color: rgba(var(--primary-rgb), 0.1) !important;
  }

  .fc-daygrid-day {
    transition: background-color 0.2s ease !important;
  }

  .fc-daygrid-day:hover {
    background-color: rgba(var(--primary-rgb), 0.02) !important;
  }

  .fc-event {
    border: none !important;
    border-radius: 0.375rem !important;
    font-weight: 500 !important;
    font-size: 0.85rem !important;
    margin: 1px 2px !important;
    transition: all 0.2s ease !important;
  }

  .fc-event:hover {
    transform: translateY(-1px) !important;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15) !important;
  }

  /* Enhanced Shadows */
  .shadow-sm {
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08) !important;
  }

  .shadow-lg {
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.12) !important;
  }

  /* Tooltip Enhancements */
  .tooltip-inner {
    background-color: var(--bs-gray-900) !important;
    color: white !important;
    border-radius: 0.5rem !important;
    padding: 0.75rem 1rem !important;
    font-size: 0.875rem !important;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2) !important;
  }

  .tooltip-arrow::before {
    border-top-color: var(--bs-gray-900) !important;
  }

  /* Responsive Improvements */
  @media (max-width: 768px) {
    .display-3 {
      font-size: 2.5rem;
    }
    
    .legend-item {
      margin-bottom: 0.5rem;
    }
    
    .d-flex.flex-wrap.gap-4 {
      flex-direction: column;
      gap: 0.5rem !important;
    }
    
    #calendar {
      padding: 1rem !important;
    }
    
    .fc-toolbar {
      flex-direction: column !important;
      gap: 1rem !important;
    }
    
    .fc-toolbar-chunk {
      display: flex !important;
      justify-content: center !important;
    }
  }

  @media (max-width: 576px) {
    .container {
      padding-left: 1rem;
      padding-right: 1rem;
    }
    
    .btn-lg {
      padding: 0.75rem 1.5rem !important;
      font-size: 1rem !important;
    }
    
    .card-body {
      padding: 1.5rem !important;
    }
    
    .fc-button {
      padding: 0.375rem 0.75rem !important;
      font-size: 0.875rem !important;
    }
  }

  /* Smooth Transitions */
  * {
    transition: background-color 0.2s ease, border-color 0.2s ease, box-shadow 0.2s ease;
  }

  /* Focus States */
  .btn:focus,
  .fc-button:focus {
    outline: 2px solid rgba(var(--primary-rgb), 0.5);
    outline-offset: 2px;
  }

  /* Loading Animation */
  .fc-daygrid {
    animation: fadeIn 0.5s ease-in-out;
  }

  @keyframes fadeIn {
    from {
      opacity: 0;
      transform: translateY(10px);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }
</style>

@endsection