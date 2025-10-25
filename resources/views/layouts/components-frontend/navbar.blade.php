<header class="shadow-sm">
    <nav class="navbar navbar-expand-lg bg-white py-3">
        <div class="container">
            <!-- Logo -->
            <a class="navbar-brand d-flex align-items-center gap-2" href="{{ url('/') }}">
                <img src="{{ asset('assets/backend/images/logos/ubk2.png') }}" alt="image/png" height="100">
            </a>

            <!-- Toggle Button -->
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
                <i class="ti ti-menu-2 fs-4 text-dark"></i>
            </button>

            <!-- Navbar Content -->
            <div class="collapse navbar-collapse" id="navbarContent">
                <!-- Menu Tengah -->
                <ul class="navbar-nav mx-auto gap-lg-4">
                    <li class="nav-item"><a class="nav-link text-dark fw-semibold" href="{{ url('/') }}">Beranda</a></li>
                    <li class="nav-item"><a class="nav-link text-dark fw-semibold" href="{{ route('bookings.create') }}">Booking</a></li>
                    <li class="nav-item"><a class="nav-link text-dark fw-semibold" href="{{ route('ruangan') }}">Ruangan</a></li>
                    @auth
                        <li class="nav-item"><a class="nav-link text-dark fw-semibold" href="{{ route('bookings.riwayat') }}">Riwayat</a></li>
                    @endauth
                </ul>

                <!-- Kanan -->
                <ul class="navbar-nav ms-auto">
                    @guest
                        <li class="nav-item">
                            <a href="{{ route('login') }}" class="btn btn-outline-primary rounded-pill px-4 fw-semibold">Login</a>
                        </li>
                    @else
                        <!-- 🔔 Notifikasi -->
                        <li class="nav-item dropdown me-3">
                            <a class="nav-link position-relative" href="#" id="notifDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-bell-fill fs-5 text-dark"></i>

                                @php
                                    $totalNotifikasi = ($userNotifications->count() ?? 0) + ($userBorrowNotifications->count() ?? 0);
                                @endphp

                                @if ($totalNotifikasi > 0)
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                        {{ $totalNotifikasi }}
                                    </span>
                                @endif
                            </a>

                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 mt-2 rounded-4" aria-labelledby="notifDropdown" style="min-width: 300px;">
                                <!-- Booking Section -->
                                <li class="dropdown-header fw-semibold px-3 pt-2">📅 Notifikasi Booking</li>
                                @forelse ($userNotifications as $notif)
                                    <li>
                                        <a class="dropdown-item small py-2 px-3" href="{{ route('bookings.riwayat') }}">
                                            @if ($notif->status == 'Diterima')
                                                ✅ Booking <strong>{{ $notif->ruangan->nama_ruangan }}</strong> <span class="text-success">DITERIMA</span>.
                                            @elseif ($notif->status == 'Ditolak')
                                                ❌ Booking <strong>{{ $notif->ruangan->nama_ruangan }}</strong> <span class="text-danger">DITOLAK</span>.
                                            @endif
                                            <div class="text-muted small">{{ $notif->created_at->diffForHumans() }}</div>
                                        </a>
                                    </li>
                                @empty
                                    <li><span class="dropdown-item text-muted small">Tidak ada notifikasi booking.</span></li>
                                @endforelse

                                <li><hr class="dropdown-divider"></li>

                                <!-- Peminjaman Section -->
                                <li class="dropdown-header fw-semibold px-3 pt-2">📦 Notifikasi Peminjaman</li>
                                @forelse ($userBorrowNotifications as $notif)
                                    <li>
                                        <a class="dropdown-item small py-2 px-3" href="{{ route('peminjaman.index') }}">
                                            @if ($notif->status == 'disetujui')
                                                ✅ Peminjaman <strong>{{ $notif->barang->nama_barang }}</strong> <span class="text-success">DISETUJUI</span>.
                                            @elseif ($notif->status == 'ditolak')
                                                ❌ Peminjaman <strong>{{ $notif->barang->nama_barang }}</strong> <span class="text-danger">DITOLAK</span>.
                                            @elseif ($notif->status == 'selesai')
                                                📦 Peminjaman <strong>{{ $notif->barang->nama_barang }}</strong> telah <span class="text-primary">SELESAI</span>.
                                            @endif
                                            <div class="text-muted small">{{ $notif->created_at->diffForHumans() }}</div>
                                        </a>
                                    </li>
                                @empty
                                    <li><span class="dropdown-item text-muted small">Tidak ada notifikasi peminjaman.</span></li>
                                @endforelse

                                <li><hr class="dropdown-divider"></li>

                                <!-- Link ke Semua Riwayat -->
                                <li>
                                    <a class="dropdown-item text-center text-primary small fw-semibold" href="{{ route('bookings.riwayat') }}">
                                        <i class="bi bi-eye me-1"></i> Lihat Semua Aktivitas
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <!-- 👤 User Dropdown -->
                        <li class="nav-item dropdown">
                            @php
                                $name = Auth::user()->name;
                                $initials = collect(explode(' ', $name))->map(fn($w) => strtoupper(substr($w, 0, 1)))->join('');
                            @endphp
                            <a class="nav-link dropdown-toggle d-flex flex-column align-items-center" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <div class="rounded-circle bg-primary text-white d-flex justify-content-center align-items-center mb-1" style="width: 45px; height: 45px; font-weight: bold; font-size: 16px;">
                                    {{ $initials }}
                                </div>
                                <span class="fw-semibold small">{{ $name }}</span>
                            </a>

                            <ul class="dropdown-menu dropdown-menu-end shadow rounded-3 border-0 mt-2" aria-labelledby="userDropdown">
                                <li><a class="dropdown-item" href="{{ route('bookings.riwayat') }}"><i class="ti ti-clock-history me-2"></i>Riwayat</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item text-danger" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        <i class="ti ti-logout me-2"></i>Logout
                                    </a>
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
                                </li>
                            </ul>
                        </li>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>
</header>

<!-- ✅ AJAX: tandai semua notifikasi (booking & peminjaman) sebagai sudah dibaca -->
<script>
   document.addEventListener('DOMContentLoaded', function() {
    const notifBtn = document.getElementById('notifDropdown');
    notifBtn?.addEventListener('click', function() {
        fetch("{{ url('/notifications/read') }}", {
            method: "POST",
            credentials: "same-origin",   // <= PENTING supaya cookie session dikirim
            headers: {
                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                "Accept": "application/json",
                "Content-Type": "application/json"
            }
        }).then(res => {
            if (!res.ok) console.log('Notif read failed', res.status, res.statusText);
            return res.json().catch(() => ({}));
        }).then(() => {
            const badge = notifBtn.querySelector('.badge');
            if (badge) badge.remove();
        }).catch(err => console.error('Error mark read:', err));
    });
});
</script>
