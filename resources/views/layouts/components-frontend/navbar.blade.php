<header>
    <nav class="navbar navbar-expand-lg py-3" id="mainNav">
        <style>
            #mainNav {
                background: rgba(255, 255, 255, 0.95);
                backdrop-filter: blur(12px);
                -webkit-backdrop-filter: blur(12px);
                border-bottom: 1px solid rgba(0, 0, 0, .07);
                position: sticky;
                top: 0;
                z-index: 1030;
            }

            .nav-link-custom {
                position: relative;
                color: #444 !important;
                font-weight: 500;
                font-size: 0.9rem;
                padding: 6px 4px !important;
                transition: color .2s;
            }

            .nav-link-custom::after {
                content: '';
                position: absolute;
                bottom: -2px;
                left: 0;
                width: 0;
                height: 2px;
                background: #2563eb;
                border-radius: 2px;
                transition: width .25s ease;
            }

            .nav-link-custom:hover {
                color: #2563eb !important;
            }

            .nav-link-custom:hover::after {
                width: 100%;
            }

            .nav-link-custom.active {
                color: #2563eb !important;
            }

            .nav-link-custom.active::after {
                width: 100%;
            }

            /* Dropdown denda */
            .denda-dropdown .dropdown-menu {
                min-width: 200px;
                border: 0;
                border-radius: 12px;
                box-shadow: 0 8px 24px rgba(0, 0, 0, .12);
                padding: .5rem;
                animation: dropFade .18s ease;
            }

            .denda-dropdown .dropdown-item {
                border-radius: 8px;
                font-size: .875rem;
                padding: 8px 12px;
                color: #374151;
                display: flex;
                align-items: center;
                gap: 8px;
                transition: background .15s;
            }

            .denda-dropdown .dropdown-item:hover {
                background: #f3f4f6;
            }

            .denda-dropdown .dropdown-item.active-item {
                background: #eff6ff;
                color: #2563eb;
                font-weight: 600;
            }

            @keyframes dropFade {
                from {
                    opacity: 0;
                    transform: translateY(8px);
                }

                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            .dropdown-menu-custom {
                width: 260px;
                border: 0;
                border-radius: 16px;
                box-shadow: 0 8px 32px rgba(0, 0, 0, .12);
                padding: 1rem;
                animation: dropFade .18s ease;
            }

            .avatar-circle {
                width: 40px;
                height: 40px;
                border-radius: 50%;
                background: #eff6ff;
                border: 1.5px solid #bfdbfe;
                display: flex;
                align-items: center;
                justify-content: center;
                transition: border-color .2s;
            }

            .avatar-circle:hover {
                border-color: #2563eb;
            }

            .avatar-circle-lg {
                width: 68px;
                height: 68px;
                border-radius: 50%;
                background: #eff6ff;
                border: 2px solid #bfdbfe;
                display: flex;
                align-items: center;
                justify-content: center;
                margin: 0 auto;
            }

            .dropdown-item-custom {
                display: flex;
                align-items: center;
                gap: 10px;
                padding: 9px 12px;
                border-radius: 10px;
                font-size: 0.875rem;
                color: #374151;
                text-decoration: none;
                transition: background .15s;
            }

            .dropdown-item-custom:hover {
                background: #f3f4f6;
                color: #111;
            }
        </style>

        <div class="container">
            <!-- Logo -->
            <a class="navbar-brand" href="{{ url('/') }}">
                <img src="{{ asset('assets/backend/images/logos/ubk2.png') }}" alt="Logo" height="90">
            </a>

            <!-- Toggler -->
            <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarContent">
                <i class="ti ti-menu-2 fs-4 text-dark"></i>
            </button>

            <div class="collapse navbar-collapse" id="navbarContent">
                <!-- Menu Tengah -->
                <ul class="navbar-nav mx-auto gap-lg-4">
                    <li class="nav-item">
                        <a class="nav-link nav-link-custom {{ request()->is('/') ? 'active' : '' }}"
                            href="{{ url('/') }}">Beranda</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-link-custom {{ request()->routeIs('user.booking.*') ? 'active' : '' }}"
                            href="{{ route('user.booking.create') }}">Booking</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-link-custom {{ request()->routeIs('user.peminjaman.*') ? 'active' : '' }}"
                            href="{{ route('user.peminjaman.create') }}">Pinjam</a>
                    </li>
                    @auth
                        <li class="nav-item">
                            @php
                                $totalDendaAktif =
                                    \App\Models\DendaPengembalian::whereIn('status_pembayaran', [
                                        'belum_bayar',
                                        'menunggu_verifikasi',
                                    ])
                                        ->whereHas(
                                            'pengembalianBarang.peminjamanBarang',
                                            fn($q) => $q->where('user_id', Auth::id()),
                                        )
                                        ->count() +
                                    \App\Models\DendaBooking::whereIn('status_pembayaran', [
                                        'belum_bayar',
                                        'menunggu_verifikasi',
                                    ])
                                        ->whereHas('booking', fn($q) => $q->where('user_id', Auth::id()))
                                        ->count();
                            @endphp
                            <a class="nav-link nav-link-custom {{ request()->routeIs('riwayat.*') ? 'active' : '' }}"
                                href="{{ route('riwayat.index') }}" style="display:inline-flex;align-items:center;gap:5px;">
                                Riwayat
                                @if ($totalDendaAktif > 0)
                                    <span
                                        style="background:#ef4444;color:#fff;font-size:10px;font-weight:700;
                    min-width:18px;height:18px;border-radius:50%;display:inline-flex;
                    align-items:center;justify-content:center;padding:0 4px;">
                                        {{ $totalDendaAktif }}
                                    </span>
                                @endif
                            </a>
                        </li>
                    @endauth
                </ul>

                <!-- Kanan -->
                <ul class="navbar-nav ms-auto">
                    @guest
                        <li class="nav-item">
                            <a href="{{ route('login') }}" class="btn btn-primary rounded-pill px-4 fw-semibold"
                                style="font-size:.875rem;">
                                Login
                            </a>
                        </li>
                    @else
                        <li class="nav-item dropdown">
                            <a href="#" class="nav-link p-0 d-flex align-items-center gap-2" id="userDropdown"
                                role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <div class="avatar-circle">
                                    <i class="bi bi-person-fill text-primary"></i>
                                </div>
                                <span class="d-none d-lg-inline fw-semibold text-dark" style="font-size:.875rem;">
                                    {{ Str::limit(Auth::user()->name, 14) }}
                                </span>
                                <i class="bi bi-chevron-down text-muted" style="font-size:.7rem;"></i>
                            </a>

                            <div class="dropdown-menu dropdown-menu-end dropdown-menu-custom"
                                aria-labelledby="userDropdown">
                                <div class="text-center mb-3">
                                    <div class="avatar-circle-lg">
                                        <i class="bi bi-person-fill text-primary fs-1"></i>
                                    </div>
                                    <h6 class="mt-2 mb-0 fw-bold" style="font-size:.95rem;">
                                        {{ Auth::user()->name }}
                                    </h6>
                                    <small class="text-muted">{{ Auth::user()->email }}</small>
                                </div>

                                <hr class="my-2">

                                <a href="{{ route('profile') }}" class="dropdown-item-custom">
                                    <i class="bi bi-person text-primary"></i> Profile Saya
                                </a>
                                {{-- <a href="{{ route('riwayat.index') }}" class="dropdown-item-custom">
                                    <i class="bi bi-clock-history text-info"></i> Riwayat
                                </a> --}}
                                <a href="{{ route('user.denda.index') }}" class="dropdown-item-custom">
                                    <i class="bi bi-cash-coin text-warning"></i> Denda Peminjaman
                                </a>
                                <a href="{{ route('user.denda-booking.index') }}" class="dropdown-item-custom">
                                    <i class="bi bi-building text-danger"></i> Denda Booking
                                </a>

                                <hr class="my-2">

                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-danger w-100 rounded-pill btn-sm mt-1">
                                        <i class="bi bi-box-arrow-right me-1"></i> Logout
                                    </button>
                                </form>
                            </div>
                        </li>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>
</header>
