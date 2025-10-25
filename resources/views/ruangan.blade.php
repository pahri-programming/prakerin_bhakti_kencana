@extends('layouts.frontend')

@section('styles')
    <style>
        :root {
            --primary-color: #0d6efd;
            --primary-rgb: 13, 110, 253;
            --success-color: #198754;
            --warning-color: #ffc107;
            --light-bg: #f8f9fa;
            --border-light: #e9ecef;
        }

        /* Primary Soft Background */
        .bg-primary-soft {
            background-color: rgba(var(--primary-rgb), 0.1) !important;
        }

        /* Enhanced Card Styling */
        .room-card {
            background: white;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid rgba(0, 0, 0, 0.06) !important;
            position: relative;
            overflow: hidden;
        }

        .room-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
            transition: left 0.6s;
            z-index: 1;
        }

        .room-card:hover::before {
            left: 100%;
        }

        .room-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1) !important;
        }

        /* Image Enhancements */
        .image-container {
            overflow: hidden;
            position: relative;
        }

        .room-image {
            transition: transform 0.5s ease;
        }

        .room-card:hover .room-image {
            transform: scale(1.05);
        }

        .image-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, rgba(var(--primary-rgb), 0.1) 0%, transparent 50%);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .room-card:hover .image-overlay {
            opacity: 1;
        }

        /* Filter Buttons */
        .filter-btn {
            transition: all 0.3s ease;
            border-width: 1px;
            font-weight: 500;
        }

        .filter-btn.active {
            background-color: var(--primary-color) !important;
            color: white !important;
            border-color: var(--primary-color) !important;
        }

        .filter-btn:hover:not(.active) {
            background-color: rgba(var(--primary-rgb), 0.1);
            border-color: var(--primary-color);
            transform: translateY(-1px);
        }

        /* Facility Badges */
        .facility-badge {
            background-color: #f8f9fa !important;
            border-color: #e9ecef !important;
            transition: all 0.2s ease;
        }

        .facility-badge:hover {
            background-color: #e9ecef !important;
            transform: scale(1.05);
        }

        /* Stat Cards */
        .stat-card {
            transition: all 0.2s ease;
            border: 1px solid var(--border-light);
        }

        .stat-card:hover {
            background-color: #e3f2fd !important;
            transform: translateY(-1px);
        }

        /* Button Enhancements */
        .btn-hover {
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .btn-hover::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.6s;
        }

        .btn-hover:hover::before {
            left: 100%;
        }

        .btn-hover:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(var(--primary-rgb), 0.3);
        }

        .quick-book-btn {
            transition: all 0.2s ease;
        }

        .quick-book-btn:hover {
            background-color: var(--primary-color);
            color: white;
            transform: scale(1.02);
        }

        /* Rating Stars */
        .rating-stars {
            font-size: 0.85rem;
        }

        /* Modal Enhancements */
        .modal-content {
            backdrop-filter: blur(10px);
        }

        /* Animations */
        .room-item {
            animation: fadeInUp 0.6s ease forwards;
            opacity: 0;
            transform: translateY(30px);
        }

        .room-item:nth-child(1) {
            animation-delay: 0.1s;
        }

        .room-item:nth-child(2) {
            animation-delay: 0.2s;
        }

        .room-item:nth-child(3) {
            animation-delay: 0.3s;
        }

        .room-item:nth-child(4) {
            animation-delay: 0.4s;
        }

        .room-item:nth-child(5) {
            animation-delay: 0.5s;
        }

        .room-item:nth-child(6) {
            animation-delay: 0.6s;
        }

        @keyframes fadeInUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .display-4 {
                font-size: 2rem;
            }

            .room-card:hover {
                transform: translateY(-4px);
            }

            .filter-btn {
                font-size: 0.875rem;
                padding: 0.375rem 0.75rem;
            }

            .stat-card {
                padding: 0.75rem !important;
            }
        }

        @media (max-width: 576px) {
            .container {
                padding-left: 1rem;
                padding-right: 1rem;
            }

            .card-body {
                padding: 1rem !important;
            }

            .card-footer {
                padding: 1rem !important;
                padding-top: 0 !important;
            }
        }

        /* Loading States */
        .room-card.loading {
            pointer-events: none;
            opacity: 0.7;
        }

        .room-card.loading::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.8);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10;
        }

        /* Hover Effects for Interactive Elements */
        .badge:hover {
            transform: scale(1.05);
        }

        .room-card .position-absolute .badge {
            transition: all 0.3s ease;
        }

        .room-card:hover .position-absolute .badge {
            transform: scale(1.1);
        }

        .room-item {
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            filter: blur(0px);
        }

        .room-item.d-none {
            opacity: 0 !important;
            transform: scale(0.9);
            filter: blur(3px);
        }


        .filter-btn.active {
            background-color: #0d6efd;
            color: white !important;
            border-color: #0d6efd;
        }
    </style>
@endsection

@php
    use Illuminate\Support\Str;
@endphp

@section('content')
    <div class="container py-5">
        <!-- Header Section -->
        <div class="text-center mb-5">
            <div class="mb-3">
                <span class="badge bg-primary-soft text-primary rounded-pill px-3 py-2 fw-medium">
                    <i class="bi bi-building me-1"></i> Fasilitas Terbaik
                </span>
            </div>
            <h1 class="display-4 fw-bold text-primary mb-3">Daftar Ruangan</h1>
            <p class="lead text-muted mb-4">Pilih ruangan sesuai kebutuhanmu. Booking cepat, tanpa bentrok jadwal.</p>

            <!-- Filter Bar -->
            <div class="row justify-content-center mb-4">
                <div class="col-lg-8">
                    <div class="d-flex flex-wrap justify-content-center gap-2">
                        <button class="btn btn-outline-primary btn-sm rounded-pill px-3 filter-btn active"
                            data-filter="all">
                            <i class="bi bi-grid-3x3-gap me-1"></i> Semua
                        </button>
                        <button class="btn btn-outline-primary btn-sm rounded-pill px-3 filter-btn" data-filter="lab">
                            <i class="bi bi-pc-display me-1"></i> Lab Komputer
                        </button>
                        <button class="btn btn-outline-primary btn-sm rounded-pill px-3 filter-btn" data-filter="meeting">
                            <i class="bi bi-people me-1"></i> Ruang Meeting
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Rooms Grid -->
        <div class="row g-4" id="rooms-container">
            @foreach ($ruangans as $ruangan)
                <div class="col-lg-4 col-md-6 room-item"
                    data-category="{{ Str::contains(strtolower($ruangan->nama_ruangan), 'lab') ? 'lab' : (Str::contains(strtolower($ruangan->nama_ruangan), 'meeting') ? 'meeting' : 'other') }}">
                    <div class="card room-card h-100 border-0 rounded-4 overflow-hidden shadow-sm">

                        <div class="px-4 pt-3">
                            <span class="badge bg-primary-soft text-primary rounded-pill px-3 py-2 fw-medium">
                                <i class="bi bi-hash me-1"></i> {{ $ruangan->kode_ruangan ?? 'Kode tidak ada' }}
                            </span>
                        </div>

                        <!-- Image Section -->
                        <div class="position-relative">
                            @if ($ruangan->cover)
                                <div class="ratio ratio-16x9 image-container">
                                    <img src="{{ asset('storage/' . $ruangan->cover) }}"
                                        class="object-fit-cover w-100 h-100 room-image" alt="{{ $ruangan->nama }}">
                                    <div class="image-overlay"></div>
                                </div>
                            @else
                                <div class="ratio ratio-16x9 bg-light d-flex align-items-center justify-content-center">
                                    <div class="text-center text-muted">
                                        <i class="bi bi-image fs-1 mb-2"></i>
                                        <p class="mb-0">No Image</p>
                                    </div>
                                </div>
                            @endif

                            <!-- Status Badge -->
                            <div class="position-absolute top-0 end-0 m-3">
                                <span class="badge bg-success text-white rounded-pill px-3 py-2 shadow-sm">
                                    <i class="bi bi-check-circle-fill me-1"></i> Tersedia
                                </span>
                            </div>
                        </div>

                        <!-- Card Content -->
                        <div class="card-body p-4">
                            <!-- Room Name & Capacity -->
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <h5 class="fw-bold text-dark mb-1">{{ $ruangan->nama }}</h5>
                                    <div class="d-flex align-items-center text-muted small">
                                        <i class="bi bi-people-fill me-1 text-primary"></i>
                                        <span class="fw-medium">{{ $ruangan->kapasitas }} Orang</span>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <div class="rating-stars">
                                        <i class="bi bi-star-fill text-warning"></i>
                                        <i class="bi bi-star-fill text-warning"></i>
                                        <i class="bi bi-star-fill text-warning"></i>
                                        <i class="bi bi-star-fill text-warning"></i>
                                        <i class="bi bi-star-half text-warning"></i>
                                        <small class="text-muted ms-1">4.5</small>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <h6 class="fw-semibold text-dark mb-2 small">
                                    <i class="bi bi-geo-alt-fill me-1 text-danger"></i> Lokasi:
                                </h6>
                                <p class="text-muted mb-0">{{ $ruangan->lokasi ?? 'Lokasi belum diisi' }}</p>
                            </div>

                            <!-- Facilities -->
                            <div class="mb-4">
                                <h6 class="fw-semibold text-dark mb-2 small">
                                    <i class="bi bi-gear-fill me-1 text-primary"></i> Fasilitas:
                                </h6>
                                <div class="d-flex flex-wrap gap-2">
                                    @foreach (explode(',', $ruangan->fasilitas) as $index => $fasilitas)
                                        @if ($index < 3)
                                            <span
                                                class="badge bg-light text-dark border rounded-pill px-3 py-1 small facility-badge">
                                                <i class="bi bi-check-circle-fill text-success me-1"></i>
                                                {{ trim($fasilitas) }}
                                            </span>
                                        @endif
                                    @endforeach
                                    @if (count(explode(',', $ruangan->fasilitas)) > 3)
                                        <span class="badge bg-primary-soft text-primary rounded-pill px-3 py-1 small">
                                            +{{ count(explode(',', $ruangan->fasilitas)) - 3 }} lainnya
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <!-- Quick Stats -->
                            <div class="row g-2 mb-3">
                                <div class="col-6">
                                    <div class="stat-card bg-light rounded-3 p-2 text-center">
                                        <div class="fw-bold text-primary">24/7</div>
                                        <small class="text-muted">Akses</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="stat-card bg-light rounded-3 p-2 text-center">
                                        <div class="fw-bold text-success">AC</div>
                                        <small class="text-muted">Ruangan</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Card Footer -->
                        <div class="card-footer bg-transparent border-0 p-4 pt-0">
                            <div class="d-grid gap-2">
                                <a href="{{ route('ruangan.detail', $ruangan->id) }}"
                                    class="btn btn-primary rounded-pill py-2 fw-semibold btn-hover">
                                    <i class="bi bi-eye-fill me-2"></i> Lihat Detail
                                </a>
                                <button class="btn btn-outline-primary btn-sm rounded-pill quick-book-btn"
                                    data-room="{{ $ruangan->nama_ruangan }}">
                                    <i class="bi bi-lightning-charge me-1"></i> Quick Book
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Load More Button -->
        <div class="text-center mt-5">
            <button class="btn btn-outline-primary btn-lg rounded-pill px-5">
                <i class="bi bi-arrow-down-circle me-2"></i> Muat Lebih Banyak
            </button>
        </div>
    </div>

    <!-- Quick Book Modal -->
    <div class="modal fade" id="quickBookModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-lightning-charge text-warning me-2"></i>
                        Quick Book
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted">Booking cepat untuk ruangan: <span class="fw-bold" id="selectedRoom"></span></p>
                    <div class="alert alert-info rounded-3">
                        <i class="bi bi-info-circle me-2"></i>
                        Fitur ini akan mengarahkan Anda ke halaman booking dengan ruangan yang sudah terpilih.
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary rounded-pill">
                        <i class="bi bi-arrow-right me-2"></i>Lanjut ke Booking
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const filterButtons = document.querySelectorAll('.filter-btn');
            const roomItems = document.querySelectorAll('.room-item');

            // --- FILTERING DENGAN ANIMASI HALUS ---
            filterButtons.forEach(button => {
                button.addEventListener('click', function() {
                    // ubah tombol aktif
                    filterButtons.forEach(btn => btn.classList.remove('active'));
                    this.classList.add('active');

                    const filter = this.dataset.filter;

                    roomItems.forEach(item => {
                        const category = item.dataset.category;

                        if (filter === 'all' || category === filter) {
                            // tampilkan dengan efek smooth
                            item.classList.remove('d-none');
                            item.style.opacity = '0';
                            item.style.transform = 'scale(0.95)';
                            item.style.transition = 'all 0.3s ease';
                            setTimeout(() => {
                                item.style.opacity = '1';
                                item.style.transform = 'scale(1)';
                            }, 50);
                        } else {
                            // sembunyikan dengan efek smooth
                            item.style.opacity = '0';
                            item.style.transform = 'scale(0.95)';
                            item.style.transition = 'all 0.3s ease';
                            setTimeout(() => {
                                item.classList.add('d-none');
                            }, 300);
                        }
                    });
                });
            });

            // --- QUICK BOOK MODAL ---
            const quickBookBtns = document.querySelectorAll('.quick-book-btn');
            const quickBookModalEl = document.getElementById('quickBookModal');
            const quickBookModal = new bootstrap.Modal(quickBookModalEl);

            quickBookBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    const roomName = this.dataset.room;
                    document.getElementById('selectedRoom').textContent = roomName;
                    quickBookModal.show();
                });
            });

            // --- ANIMASI SAAT ELEMENT MUNCUL DI VIEWPORT ---
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };

            const observer = new IntersectionObserver(entries => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('animate-in');
                        observer.unobserve(entry.target);
                    }
                });
            }, observerOptions);

            roomItems.forEach(item => {
                observer.observe(item);
            });
        });
    </script>
@endpush
