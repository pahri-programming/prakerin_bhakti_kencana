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
        }

        .btn-gradient {
            background: linear-gradient(to right, #1b263b, #778da9);
            color: white;
            border: none;
            transition: 0.3s ease-in-out;
        }

        .btn-gradient:hover {
            background: linear-gradient(to right, #0d1b2a, #5c748f);
            transform: translateY(-2px);
        }
    </style>
@endsection

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card border-0 shadow-lg rounded-5 overflow-hidden">
                    <div class="row g-0">
                        <!-- Image Section -->
                        <div class="col-md-6 bg-dark-subtle">
                            @if ($ruangan->cover)
                                <img src="{{ asset('storage/' . $ruangan->cover) }}" alt="Foto {{ $ruangan->nama }}"
                                    class="w-100 h-100 object-fit-cover" style="min-height: 100%; max-height: 100%;">
                            @else
                                <div class="w-100 h-100 bg-light d-flex align-items-center justify-content-center"
                                    style="min-height: 100%;">
                                    <div class="text-center text-muted">
                                        <i class="bi bi-image" style="font-size: 3rem;"></i>
                                        <p class="mt-2">Tidak ada gambar</p>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Detail Section -->
                        <div class="col-md-6 d-flex align-items-center">
                            <div class="card-body p-5">
                                <h2 class="fw-bold text-gradient mb-3">{{ $ruangan->nama }}</h2>
                                <p class="text-muted mb-4"><i class="bi bi-people-fill me-2"></i> Kapasitas:
                                    <strong>{{ $ruangan->kapasitas }}</strong> </p>

                                <h5 class="text-secondary mb-3">Fasilitas:</h5>
                                <ul class="list-unstyled mb-4">
                                    @foreach (explode(',', $ruangan->fasilitas) as $item)
                                        <li class="d-flex align-items-center mb-2">
                                            <i class="bi bi-check-circle-fill text-success me-2"></i>
                                            <span class="text-dark">{{ trim($item) }}</span>
                                        </li>
                                    @endforeach
                                </ul>

                                <a href="{{ route('bookings.create', ['ruangan_id' => $ruangan->id]) }}"
                                    class="btn btn-gradient btn-lg w-100 rounded-pill shadow-sm">
                                    <i class="bi bi-calendar-plus me-2"></i> Booking Sekarang
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
