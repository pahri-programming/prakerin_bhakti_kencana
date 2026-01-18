<header class="shadow-sm">
    <nav class="navbar navbar-expand-lg bg-white py-3">
        <style>
            .dropdown-menu {
                animation: dropdownFade .2s ease-in-out;
            }

            @keyframes dropdownFade {
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
        <div class="container">
            <!-- Logo -->
            <a class="navbar-brand d-flex align-items-center gap-2" href="{{ url('/') }}">
                <img src="{{ asset('assets/backend/images/logos/ubk2.png') }}" alt="image/png" height="100">
            </a>

            <!-- Toggle Button -->
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarContent">
                <i class="ti ti-menu-2 fs-4 text-dark"></i>
            </button>

            <!-- Navbar Content -->
            <div class="collapse navbar-collapse" id="navbarContent">
                <!-- Menu Tengah -->
                <ul class="navbar-nav mx-auto gap-lg-4">
                    <li class="nav-item">
                        <a class="nav-link text-dark fw-semibold" href="{{ url('/') }}">
                            Beranda
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-dark fw-semibold" href="{{ route('bookings.create') }}">
                            Booking
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-dark fw-semibold" href="{{ route('ruangan') }}">
                            Ruangan
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-dark fw-semibold" href="{{ route('peminjaman.create') }}">
                            Pinjam
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-dark fw-semibold" href="{{ route('barang') }}">
                            Barang
                        </a>
                    </li>
                    @auth
                        <li class="nav-item">
                            <a class="nav-link text-dark fw-semibold" href="{{ route('riwayat.index') }}">
                                Riwayat
                            </a>
                        </li>
                    @endauth
                </ul>

                <!-- Kanan -->
                <ul class="navbar-nav ms-auto">
                    @guest
                        <li class="nav-item">
                            <a href="{{ route('login') }}"
                                class="btn btn-outline-primary rounded-pill px-4 fw-semibold">
                                Login
                            </a>
                        </li>
                    @else
                        <!-- 👤 User Dropdown -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" 
                               href="#"
                               id="userDropdown" 
                               role="button" 
                               data-bs-toggle="dropdown" 
                               aria-expanded="false">
                                <!-- Avatar -->
                                <div class="rounded-circle bg-light border d-flex justify-content-center align-items-center"
                                    style="width:45px;height:45px;">
                                    <i class="bi bi-person-fill text-secondary fs-4"></i>
                                </div>
                            </a>

                            <div class="dropdown-menu dropdown-menu-end p-3 shadow-lg border-0 rounded-4"
                                style="width:260px;" aria-labelledby="userDropdown">

                                <!-- Header Profile -->
                                <div class="text-center mb-3">
                                    <div class="rounded-circle bg-light border mx-auto d-flex justify-content-center align-items-center"
                                        style="width:70px;height:70px;">
                                        <i class="bi bi-person-fill text-secondary fs-1"></i>
                                    </div>

                                    <h6 class="mt-2 mb-0 fw-bold">{{ Auth::user()->name }}</h6>
                                    <small class="text-muted">{{ Auth::user()->email }}</small>
                                </div>

                                <hr class="my-2">

                                <!-- Menu -->
                                <a href="{{ route('profile') }}"
                                    class="dropdown-item d-flex align-items-center gap-2 py-2 rounded-3">
                                    <i class="bi bi-person text-primary"></i> Profile Saya
                                </a>

                                <a href="{{ route('riwayat.index') }}"
                                    class="dropdown-item d-flex align-items-center gap-2 py-2 rounded-3">
                                    <i class="bi bi-clock-history text-info"></i> Riwayat
                                </a>

                                <hr class="my-2">

                                <form action="{{ route('logout') }}" method="POST" class="mt-2">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-danger w-100 rounded-pill">
                                        <i class="bi bi-box-arrow-right me-2"></i>Logout
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