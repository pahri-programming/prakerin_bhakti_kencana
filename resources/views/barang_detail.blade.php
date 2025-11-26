@extends('layouts.frontend')

@section('styles')
    <style>
        .object-fit-cover {
            object-fit: cover;
            height: 100%;
        }

        .text-gradient {
            background: linear-gradient(to right, #1b263b, #778da9);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .btn-gradient {
            background: linear-gradient(to right, #1b263b, #778da9);
            color: white;
            border: none;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(27, 38, 59, 0.2);
        }

        .btn-gradient:hover {
            background: linear-gradient(to right, #0d1b2a, #5c748f);
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(27, 38, 59, 0.3);
        }

        .btn-quick {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            transition: all 0.3s ease;
        }

        .btn-quick:hover {
            transform: scale(1.02);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
        }

        .badge-stock {
            font-size: 1rem;
            font-weight: 600;
            padding: 0.5rem 1rem;
            border-radius: 50px;
        }

        .stock-high {
            background: #d4edda;
            color: #155724;
        }

        .stock-medium {
            background: #fff3cd;
            color: #856404;
        }

        .stock-low {
            background: #f8d7da;
            color: #721c24;
        }

        .facility-item {
            transition: all 0.2s ease;
        }

        .facility-item:hover {
            transform: translateX(5px);
        }

        .card-detail {
            border: none;
            border-radius: 1.5rem;
            overflow: hidden;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.08);
        }

        .image-container {
            position: relative;
            overflow: hidden;
        }

        .image-container::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(27, 38, 59, 0.1), transparent 50%);
            opacity: 0;
            transition: opacity 0.4s ease;
        }

        .image-container:hover::after {
            opacity: 1;
        }

        .image-container img {
            transition: transform 0.6s ease;
        }

        .image-container:hover img {
            transform: scale(1.05);
        }
    </style>
@endsection

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-11 col-xl-10">
                <div class="card card-detail">
                    <div class="row g-0">

                        <!-- Image Section -->
                        <div class="col-md-6">
                            <div class="image-container h-100">
                                @if ($barang->foto)
                                    <img src="{{ Storage::url($barang->foto) }}" alt="{{ $barang->nama }}"
                                        class="w-100 h-100 object-fit-cover">
                                @else
                                    <div class="w-100 h-100 bg-light d-flex align-items-center justify-content-center">
                                        <div class="text-center text-muted">
                                            <i class="bi bi-box fs-1 mb-3"></i>
                                            <p class="h5 fw-medium">Tidak ada gambar</p>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Detail Section -->
                        <div class="col-md-6 d-flex align-items-center bg-white">
                            <div class="card-body p-5 p-lg-6">

                                <!-- Header -->
                                <div class="mb-4">
                                    <h2 class="display-5 fw-bold text-gradient mb-2">{{ $barang->nama }}</h2>
                                    <p class="text-muted mb-1">
                                        <i class="bi bi-tag-fill me-2 text-primary"></i>
                                        Kategori: <strong>{{ $barang->kategori?->nama ?? 'Tidak Dikategorikan' }}</strong>
                                    </p>
                                    <p class="text-muted">
                                        <i class="bi bi-hash me-2"></i> Kode: <code
                                            class="bg-light px-2 py-1 rounded">{{ $barang->kode }}</code>
                                    </p>
                                </div>

                                <!-- Stok Status -->
                                <div class="mb-4">
                                    @php
                                        $stok = $barang->stok;
                                        $statusClass =
                                            $stok > 10 ? 'stock-high' : ($stok > 0 ? 'stock-medium' : 'stock-low');
                                        $statusText =
                                            $stok > 10 ? 'Banyak Tersedia' : ($stok > 0 ? 'Terbatas' : 'Habis');
                                    @endphp
                                    <div class="d-inline-block">
                                        <span class="badge {{ $statusClass }} badge-stock">
                                            <i
                                                class="bi {{ $stok > 0 ? 'bi-check-circle-fill' : 'bi-x-circle-fill' }} me-2"></i>
                                            {{ $stok }} Unit • {{ $statusText }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Deskripsi -->
                                <div class="mb-5">
                                    <h5 class="text-secondary mb-3">
                                        <i class="bi bi-info-circle-fill me-2 text-primary"></i> Deskripsi
                                    </h5>
                                    <p class="text-dark lh-lg">{!! nl2br(e($barang->keterangan ?: 'Tidak ada keterangan.')) !!}</p>
                                </div>

                                <!-- Spesifikasi & Keunggulan (Dari keterangan) -->
                                <div class="mb-5">
                                    <h5 class="text-secondary mb-3">
                                        <i class="bi bi-gear-fill me-2 text-primary"></i> Spesifikasi & Keunggulan
                                    </h5>

                                    @php
                                        $lines = preg_split('/\r\n|\r|\n/', $barang->keterangan ?? '');
                                        $specs = [];
                                        foreach ($lines as $line) {
                                            $line = trim($line);
                                            if (empty($line)) {
                                                continue;
                                            }

                                            // Cek apakah format key:value atau key|value
                                            if (preg_match('/[:|]/', $line)) {
                                                $parts = preg_split('/[:|]/', $line, 2);
                                                if (count($parts) === 2) {
                                                    $key = trim($parts[0]);
                                                    $val = trim($parts[1]);
                                                    if ($key && $val) {
                                                        $specs[] = ['label' => $key, 'value' => $val];
                                                        continue;
                                                    }
                                                }
                                            }
                                            // Jika bukan format key:value → jadi deskripsi
                                            $specs[] = ['desc' => $line];
                                        }
                                    @endphp

                                    <ul class="list-unstyled">
                                        @forelse($specs as $item)
                                            @if (isset($item['label']))
                                                <li class="d-flex align-items-start mb-2 facility-item">
                                                    <i class="bi bi-check-circle-fill text-success me-2 mt-1"></i>
                                                    <div>
                                                        <strong class="text-primary">{{ $item['label'] }}</strong>
                                                        <span class="text-dark ms-1">{{ $item['value'] }}</span>
                                                    </div>
                                                </li>
                                            @else
                                                <li class="d-flex align-items-center mb-2 facility-item">
                                                    <i class="bi bi-arrow-right-circle-fill text-info me-2"></i>
                                                    <span class="text-dark">{{ $item['desc'] }}</span>
                                                </li>
                                            @endif
                                        @empty
                                            <li class="text-muted small">
                                                <i class="bi bi-info-circle me-1"></i> Tidak ada spesifikasi tambahan.
                                            </li>
                                        @endforelse
                                    </ul>
                                </div>

                                <!-- Action Buttons -->
                                <div class="d-grid gap-3">
                                    @if ($barang->stok > 0)
                                        <a href="{{ route('peminjaman.create', ['barang_id' => $barang->id]) }}"
                                            class="btn btn-gradient btn-lg rounded-pill fw-semibold shadow-sm">
                                            <i class="bi bi-cart-plus-fill me-2"></i> Pinjam Sekarang
                                        </a>

                                        <button type="button" class="btn btn-quick btn-lg rounded-pill fw-medium"
                                            data-bs-toggle="modal" data-bs-target="#quickBorrowModal">
                                            <i class="bi bi-lightning-charge-fill me-2"></i> Quick Pinjam
                                        </button>
                                    @else
                                        <button class="btn btn-secondary btn-lg rounded-pill" disabled>
                                            <i class="bi bi-slash-circle me-2"></i> Stok Habis
                                        </button>
                                    @endif

                                    <a href="{{ route('barang') }}"
                                        class="btn btn-outline-secondary rounded-pill">
                                        <i class="bi bi-arrow-left me-2"></i> Kembali ke Daftar
                                    </a>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Pinjam Modal -->
    <div class="modal fade" id="quickBorrowModal" tabindex="-1" aria-labelledby="quickBorrowModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="modal-header bg-gradient text-white border-0"
                    style="background: linear-gradient(to right, #1b263b, #778da9);">
                    <h5 class="modal-title fw-bold" id="quickBorrowModalLabel">
                        <i class="bi bi-lightning-charge-fill me-2"></i> Quick Pinjam
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="text-center mb-4">
                        <i class="h1 text-primary mb-3 d-block"></i>
                        <h4 class="fw-bold">{{ $barang->nama }}</h4>
                        <p class="text-muted">Stok tersedia: <strong>{{ $barang->stok }}</strong></p>
                    </div>
                    <div class="alert alert-info rounded-3">
                        <i class="bi bi-info-circle-fill me-2"></i>
                        Anda akan diarahkan ke form peminjaman dengan barang ini sudah terpilih.
                    </div>
                </div>
                <div class="modal-footer border-0 justify-content-center pb-4">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">
                        Batal
                    </button>
                    <a href="{{ route('peminjaman.create', ['barang_id' => $barang->id]) }}"
                        class="btn btn-gradient rounded-pill px-5 fw-semibold">
                        <i class="bi bi-arrow-right me-2"></i> Lanjut
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
