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
                    <li class="nav-item"><a class="nav-link text-dark fw-semibold"
                            href="{{ url('/') }}">Beranda</a></li>
                    <li class="nav-item"><a class="nav-link text-dark fw-semibold"
                            href="{{ route('bookings.create') }}">Booking</a></li>
                    <li class="nav-item"><a class="nav-link text-dark fw-semibold"
                            href="{{ route('ruangan') }}">Ruangan</a></li>
                    <li class="nav-item"><a class="nav-link text-dark fw-semibold"
                            href="{{ route('peminjaman.create') }}">Pinjam</a></li>
                    <li class="nav-item"><a class="nav-link text-dark fw-semibold"
                            href="{{ route('barang') }}">Barang</a></li>
                    @auth
                        <li class="nav-item"><a class="nav-link text-dark fw-semibold"
                                href="{{ route('riwayat') }}">Riwayat</a></li>
                    @endauth
                </ul>

                <!-- Kanan -->
                <ul class="navbar-nav ms-auto">
                    @guest
                        <li class="nav-item">
                            <a href="{{ route('login') }}"
                                class="btn btn-outline-primary rounded-pill px-4 fw-semibold">Login</a>
                        </li>
                    @else
                        @auth
                            @php
                                // === NOTIFIKASI BOOKING ===
                                $bookingNotifications = \App\Models\Booking::where('user_id', Auth::id())
                                    ->where('is_read', false)
                                    ->whereIn('status', ['Diterima', 'Ditolak'])
                                    ->latest()
                                    ->get();

                                // === NOTIFIKASI PEMINJAMAN BARANG ===
                                $peminjamanNotifications = \App\Models\PeminjamanBarang::where('user_id', Auth::id())
                                    ->where('is_read', false)
                                    ->whereIn('status', ['disetujui', 'ditolak'])
                                    ->with('barang')
                                    ->latest()
                                    ->get();

                                $totalNotifications =
                                    $bookingNotifications->count() + $peminjamanNotifications->count();
                            @endphp
                        @endauth

                        <!-- Notifikasi -->
                        <li class="nav-item dropdown me-3">
                            <a class="nav-link position-relative" href="#" id="notifDropdown" role="button"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-bell-fill fs-5 text-dark"></i>
                                @if ($totalNotifications > 0)
                                    <span
                                        class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                        {{ $totalNotifications }}
                                    </span>
                                @endif
                            </a>

                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 mt-2 rounded-4"
                                aria-labelledby="notifDropdown" style="min-width: 320px;">

                                <li class="dropdown-header fw-semibold px-3 pt-2">Notifikasi</li>

                                <!-- Booking Notifications -->
                                @forelse ($bookingNotifications as $notif)
                                    <li>
                                        <a class="dropdown-item small py-2 px-3 d-flex align-items-start"
                                            href="{{ route('riwayat') }}">
                                            <div class="me-2">
                                                @if ($notif->status === 'Diterima')
                                                    Success
                                                @else
                                                    Cross
                                                @endif
                                            </div>
                                            <div>
                                                Booking <strong>{{ $notif->ruangan->nama_ruangan }}</strong>
                                                <span
                                                    class="{{ $notif->status === 'Diterima' ? 'text-success' : 'text-danger' }}">
                                                    {{ $notif->status === 'Diterima' ? 'DITERIMA' : 'DITOLAK' }}
                                                </span>
                                                <div class="text-muted small">{{ $notif->created_at->diffForHumans() }}
                                                </div>
                                            </div>
                                        </a>
                                    </li>
                                @empty
                                    <!-- Hanya tampilkan jika tidak ada booking notif -->
                                @endforelse

                                <!-- Peminjaman Barang Notifications -->
                                @forelse ($peminjamanNotifications as $notif)
                                    <li>
                                        <a class="dropdown-item small py-2 px-3 d-flex align-items-start"
                                            href="{{ route('riwayat') }}">
                                            <div class="me-2">
                                                @if ($notif->status === 'disetujui')
                                                    Success
                                                @else
                                                    Cross
                                                @endif
                                            </div>
                                            <div>
                                                Peminjaman <strong>{{ $notif->barang->nama }}</strong>
                                                <span
                                                    class="{{ $notif->status === 'disetujui' ? 'text-success' : 'text-danger' }}">
                                                    {{ $notif->status === 'disetujui' ? 'DISETUJUI' : 'DITOLAK' }}
                                                </span>
                                                <div class="text-muted small">{{ $notif->created_at->diffForHumans() }}
                                                </div>
                                            </div>
                                        </a>
                                    </li>
                                @empty
                                    @if ($bookingNotifications->isEmpty())
                                        <li><span class="dropdown-item text-muted small">Tidak ada notifikasi.</span></li>
                                    @endif
                                @endforelse

                                @if ($totalNotifications > 0)
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li>
                                        <a class="dropdown-item text-center text-primary small fw-semibold"
                                            href="{{ route('riwayat') }}">
                                            <i class="bi bi-eye me-1"></i> Lihat Semua Notifikasi
                                        </a>
                                    </li>
                                @endif
                            </ul>
                        </li>

                        <!-- 👤 User Dropdown -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#"
                                id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">

                                <!-- Avatar abu2 -->
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

                                <form action="{{ route('logout') }}" method="POST" class="mt-2">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-primary w-100 rounded-pill">
                                        Logout
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

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const notifBtn = document.getElementById('notifDropdown');
        if (!notifBtn) return;

        notifBtn.addEventListener('click', function() {
            // Tandai Booking
            fetch("{{ route('booking.notifications.read') }}", {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    "Accept": "application/json",
                }
            });

            // Tandai Peminjaman
            fetch("{{ route('peminjaman.notifications.read') }}", {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    "Accept": "application/json",
                }
            }).then(() => {
                const badge = notifBtn.querySelector('.badge');
                if (badge) badge.remove();
            });
        });
    });
</script>
