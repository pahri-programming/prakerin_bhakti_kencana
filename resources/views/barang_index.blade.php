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

        .bg-primary-soft { background-color: rgba(var(--primary-rgb), 0.1) !important; }
        .item-card {
            background: white;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid rgba(0, 0, 0, 0.06) !important;
            position: relative;
            overflow: hidden;
        }
        .item-card::before {
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
        .item-card:hover::before { left: 100%; }
        .item-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1) !important;
        }
        .item-image {
            transition: transform 0.5s ease;
        }
        .item-card:hover .item-image { transform: scale(1.05); }
        .image-overlay {
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: linear-gradient(45deg, rgba(var(--primary-rgb), 0.1), transparent 50%);
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        .item-card:hover .image-overlay { opacity: 1; }
        .filter-btn {
            transition: all 0.3s ease;
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
        .badge-stock {
            font-size: 0.85rem;
            font-weight: 600;
        }
        .item-category {
            font-size: 0.8rem;
            color: #6c757d;
        }
        .quick-borrow-btn {
            transition: all 0.2s ease;
        }
        .quick-borrow-btn:hover {
            background-color: var(--primary-color);
            color: white;
            transform: scale(1.02);
        }
        .item-item {
            animation: fadeInUp 0.6s ease forwards;
            opacity: 0;
            transform: translateY(30px);
        }
        .item-item:nth-child(1) { animation-delay: 0.1s; }
        .item-item:nth-child(2) { animation-delay: 0.2s; }
        .item-item:nth-child(3) { animation-delay: 0.3s; }
        .item-item:nth-child(4) { animation-delay: 0.4s; }
        .item-item:nth-child(5) { animation-delay: 0.5s; }
        .item-item:nth-child(6) { animation-delay: 0.6s; }

        @keyframes fadeInUp {
            to { opacity: 1; transform: translateY(0); }
        }

        @media (max-width: 768px) {
            .display-4 { font-size: 2rem; }
            .item-card:hover { transform: translateY(-4px); }
        }
    </style>
@endsection

@section('content')
<div class="container py-5">
    <!-- Header -->
    <div class="text-center mb-5">
        <div class="mb-3">
            <span class="badge bg-primary-soft text-primary rounded-pill px-3 py-2 fw-medium">
                <i class="bi bi-box-seam me-1"></i> Koleksi Lengkap
            </span>
        </div>
        <h1 class="display-4 fw-bold text-primary mb-3">Daftar Barang</h1>
        <p class="lead text-muted mb-4">Pinjam peralatan terbaik untuk mendukung kegiatanmu.</p>

        <!-- Filter Kategori -->
        <div class="row justify-content-center mb-4">
            <div class="col-lg-8">
                <div class="d-flex flex-wrap justify-content-center gap-2">
                    <button class="btn btn-outline-primary btn-sm rounded-pill px-3 filter-btn active" data-filter="all">
                        <i class="bi bi-grid-3x3-gap me-1"></i> Semua
                    </button>
                    @foreach($kategoris as $kategori)
                        <button class="btn btn-outline-primary btn-sm rounded-pill px-3 filter-btn"
                                data-filter="{{ $kategori->id }}">
                            <i class="bi bi-tag me-1"></i> {{ $kategori->nama }}
                        </button>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Grid Barang -->
    <div class="row g-4" id="items-container">
        @foreach($barangs as $barang)
            <div class="col-lg-4 col-md-6 item-item"
                 data-category="{{ $barang->kategori_id ?? 'other' }}">
                <div class="card item-card h-100 border-0 rounded-4 overflow-hidden shadow-sm">
                    <div class="px-4 pt-3">
                        <span class="badge bg-secondary text-white rounded-pill px-3 py-2 small">
                            <i class="bi bi-hash me-1"></i> {{ $barang->kode }}
                        </span>
                    </div>

                    <!-- Image -->
                    <div class="position-relative">
                        @if($barang->foto)
                            <div class="ratio ratio-16x9 image-container">
                                <img src="{{ Storage::url($barang->foto) }}"
                                     class="object-fit-cover w-100 h-100 item-image"
                                     alt="{{ $barang->nama }}">
                                <div class="image-overlay"></div>
                            </div>
                        @else
                            <div class="ratio ratio-16x9 bg-light d-flex align-items-center justify-content-center">
                                <div class="text-center text-muted">
                                    <i class="bi bi-box fs-1 mb-2"></i>
                                    <p class="mb-0">No Image</p>
                                </div>
                            </div>
                        @endif

                        <!-- Status Stok -->
                        <div class="position-absolute top-0 end-0 m-3">
                            @if($barang->stok > 0)
                                <span class="badge bg-success text-white rounded-pill px-3 py-2 shadow-sm badge-stock">
                                    <i class="bi bi-check-circle-fill me-1"></i> Tersedia ({{ $barang->stok }})
                                </span>
                            @else
                                <span class="badge bg-danger text-white rounded-pill px-3 py-2 shadow-sm">
                                    <i class="bi bi-x-circle-fill me-1"></i> Habis
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Content -->
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h5 class="fw-bold text-dark mb-1">{{ $barang->nama }}</h5>
                        </div>
                        <p class="text-muted small item-category">
                            <i class="bi bi-tag-fill me-1 text-primary"></i>
                            {{ $barang->kategori?->nama ?? 'Uncategorized' }}
                        </p>
                        <p class="text-muted small mb-3">{!! Str::limit($barang->keterangan, 80) !!}</p>

                        <!-- Quick Stats -->
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <div class="stat-card bg-light rounded-3 p-2 text-center">
                                    <div class="fw-bold text-primary">{{ $barang->stok }}</div>
                                    <small class="text-muted">Stok</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="stat-card bg-light rounded-3 p-2 text-center">
                                    <div class="fw-bold text-success">Pinjam</div>
                                    <small class="text-muted">Sekarang</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="card-footer bg-transparent border-0 p-4 pt-0">
                        <div class="d-grid gap-2">
                            <a href="{{ route('barang.show', $barang->id) }}"
                               class="btn btn-primary rounded-pill py-2 fw-semibold btn-hover">
                                <i class="bi bi-eye-fill me-2"></i> Lihat Detail
                            </a>
                            @if($barang->stok > 0)
                                <button class="btn btn-outline-primary btn-sm rounded-pill quick-borrow-btn"
                                        data-item="{{ $barang->nama }}" data-id="{{ $barang->id }}">
                                    <i class="bi bi-lightning-charge me-1"></i> Quick Pinjam
                                </button>
                            @else
                                <button class="btn btn-outline-secondary btn-sm rounded-pill" disabled>
                                    <i class="bi bi-slash-circle me-1"></i> Tidak Tersedia
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Load More -->
    <div class="text-center mt-5">
        <button class="btn btn-outline-primary btn-lg rounded-pill px-5">
            <i class="bi bi-arrow-down-circle me-2"></i> Muat Lebih Banyak
        </button>
    </div>
</div>

<!-- Quick Pinjam Modal -->
<div class="modal fade" id="quickBorrowModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-lightning-charge text-warning me-2"></i> Quick Pinjam
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted">Anda akan meminjam: <span class="fw-bold" id="selectedItem"></span></p>
                <div class="alert alert-info rounded-3">
                    <i class="bi bi-info-circle me-2"></i>
                    Anda akan diarahkan ke form peminjaman dengan barang sudah terpilih.
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">Batal</button>
                <a href="#" id="proceedBorrow" class="btn btn-primary rounded-pill">
                    <i class="bi bi-arrow-right me-2"></i> Lanjut
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const filterButtons = document.querySelectorAll('.filter-btn');
    const itemItems = document.querySelectorAll('.item-item');
    const modal = new bootstrap.Modal(document.getElementById('quickBorrowModal'));
    const selectedItemEl = document.getElementById('selectedItem');
    const proceedBtn = document.getElementById('proceedBorrow');

    // Filter
    filterButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            filterButtons.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            const filter = this.dataset.filter;

            itemItems.forEach(item => {
                const cat = item.dataset.category;
                if (filter === 'all' || cat == filter) {
                    item.classList.remove('d-none');
                    item.style.opacity = '0';
                    setTimeout(() => {
                        item.style.opacity = '1';
                        item.style.transform = 'scale(1)';
                    }, 50);
                } else {
                    item.style.opacity = '0';
                    setTimeout(() => item.classList.add('d-none'), 300);
                }
            });
        });
    });

    // Quick Borrow
    document.querySelectorAll('.quick-borrow-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const name = this.dataset.item;
            const id = this.dataset.id;
            selectedItemEl.textContent = name;
            proceedBtn.href = `{{ route('peminjaman.create') }}?barang_id=${id}`;
            modal.show();
        });
    });
});
</script>
@endpush