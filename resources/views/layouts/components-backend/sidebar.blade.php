<style>
    .logo-img {
        height: 60px;
        width: auto;
        object-fit: contain;
    }

    .brand-text {
        letter-spacing: 0.5px;
        color: #2c3e50;
    }

    .brand-logo {
        border-bottom: 1px solid #eee;
    }

    /* Active menu styling */
    .sidebar-item.active .sidebar-link {
        background-color: #5d87ff;
        color: #fff;
    }

    .sidebar-item.active .sidebar-link svg,
    .sidebar-item.active .sidebar-link i {
        color: #fff;
    }
</style>

<aside class="left-sidebar with-vertical">
    <div>
        <!-- ================================== -->
        <!-- Start Vertical Layout Sidebar -->
        <!-- ================================== -->
        
        <!-- Brand Logo -->
        <div class="brand-logo d-flex align-items-center justify-content-between px-3 py-2">
            <a href="{{ route('backend.index') }}" class="d-flex align-items-center text-decoration-none">
                <img src="{{ asset('assets/backend/images/logos/ubk2.png') }}" alt="Logo UBK" class="logo-img me-2" />
                <span class="brand-text fw-semibold fs-5 text-dark">Bhakti Kencana</span>
            </a>
            <a href="javascript:void(0)" class="sidebartoggler ms-auto text-decoration-none fs-5 d-block d-xl-none">
                <i class="ti ti-x"></i>
            </a>
        </div>

        <!-- Sidebar Navigation -->
        <nav class="sidebar-nav scroll-sidebar" data-simplebar>
            <ul id="sidebarnav">
                
                <!-- ================================== -->
                <!-- HOME SECTION -->
                <!-- ================================== -->
                <li class="nav-small-cap">
                    <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
                    <span class="hide-menu">Home</span>
                </li>

                <!-- Dashboard -->
                <li class="sidebar-item {{ request()->is('admin') ? 'active' : '' }}">
                    <a class="sidebar-link" href="{{ route('backend.index') }}" aria-expanded="false">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                            <path fill="currentColor" d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z" />
                        </svg>
                        <span class="hide-menu">Dashboard</span>
                    </a>
                </li>

                <!-- ================================== -->
                <!-- MASTER DATA SECTION -->
                <!-- ================================== -->
                <li class="nav-small-cap">
                    <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
                    <span class="hide-menu">Master Data</span>
                </li>

                <!-- User Management -->
                <li class="sidebar-item {{ request()->is('admin/user*') ? 'active' : '' }}">
                    <a class="sidebar-link" href="{{ route('backend.user.index') }}" aria-expanded="false">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                            <path fill="currentColor" d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5s-3 1.34-3 3s1.34 3 3 3m-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5S5 6.34 5 8s1.34 3 3 3m0 2c-2.33 0-7 1.17-7 3.5V20h14v-3.5c0-2.33-4.67-3.5-7-3.5m8 0c-.29 0-.62.02-.97.05c1.16.84 1.97 1.97 1.97 3.45V20h6v-3.5c0-2.33-4.67-3.5-7-3.5" />
                        </svg>
                        <span class="hide-menu">Manajemen Akun</span>
                    </a>
                </li>

                <!-- Kategori -->
                <li class="sidebar-item {{ request()->is('admin/kategori*') ? 'active' : '' }}">
                    <a class="sidebar-link" href="{{ route('backend.kategori.index') }}" aria-expanded="false">
                        <span><i class="ti ti-tags"></i></span>
                        <span class="hide-menu">Kategori</span>
                    </a>
                </li>

                <!-- Ruangan -->
                <li class="sidebar-item {{ request()->is('admin/ruangan*') ? 'active' : '' }}">
                    <a class="sidebar-link" href="{{ route('backend.ruangan.index') }}" aria-expanded="false">
                        <span><i class="ti ti-door"></i></span>
                        <span class="hide-menu">Ruangan</span>
                    </a>
                </li>

                <!-- Jadwal -->
                <li class="sidebar-item {{ request()->is('admin/jadwal*') ? 'active' : '' }}">
                    <a class="sidebar-link" href="{{ route('backend.jadwal.index') }}" aria-expanded="false">
                        <span><i class="ti ti-calendar-time"></i></span>
                        <span class="hide-menu">Jadwal</span>
                    </a>
                </li>

                <!-- Barang -->
                <li class="sidebar-item {{ request()->is('admin/barang') || request()->is('admin/barang/create') || request()->is('admin/barang/*/edit') ? 'active' : '' }}">
                    <a class="sidebar-link" href="{{ route('backend.barang.index') }}" aria-expanded="false">
                        <span><i class="ti ti-box"></i></span>
                        <span class="hide-menu">Barang</span>
                    </a>
                </li>

                <!-- Barang Ruangan -->
                <li class="sidebar-item {{ request()->is('admin/barangruangan*') ? 'active' : '' }}">
                    <a class="sidebar-link" href="{{ route('backend.barangruangan.index') }}" aria-expanded="false">
                        <span><i class="ti ti-layout-grid"></i></span>
                        <span class="hide-menu">Barang Ruangan</span>
                    </a>
                </li>

                <!-- ================================== -->
                <!-- TRANSAKSI SECTION -->
                <!-- ================================== -->
                <li class="nav-small-cap">
                    <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
                    <span class="hide-menu">Transaksi</span>
                </li>

                <!-- Booking -->
                <li class="sidebar-item {{ request()->is('admin/booking*') ? 'active' : '' }}">
                    <a class="sidebar-link" href="{{ route('backend.booking.index') }}" aria-expanded="false">
                        <span><i class="ti ti-bookmark"></i></span>
                        <span class="hide-menu">Booking Ruangan</span>
                    </a>
                </li>

                <!-- Peminjaman Barang -->
                <li class="sidebar-item {{ request()->is('admin/peminjaman*') ? 'active' : '' }}">
                    <a class="sidebar-link" href="{{ route('backend.peminjaman.index') }}" aria-expanded="false">
                        <span><i class="ti ti-package"></i></span>
                        <span class="hide-menu">Peminjaman Barang</span>
                    </a>
                </li>

                <!-- Pengembalian Barang (NEW) -->
                <li class="sidebar-item {{ request()->is('admin/pengembalian*') ? 'active' : '' }}">
                    <a class="sidebar-link" href="{{ route('backend.pengembalian.index') }}" aria-expanded="false">
                        <span><i class="ti ti-package-import"></i></span>
                        <span class="hide-menu">Pengembalian Barang</span>
                    </a>
                </li>

                <!-- ================================== -->
                <!-- LAPORAN SECTION -->
                <!-- ================================== -->
                <li class="nav-small-cap">
                    <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
                    <span class="hide-menu">Laporan</span>
                </li>

                <!-- Laporan UBK -->
                <li class="sidebar-item {{ request()->is('admin/laporan-ubk*') ? 'active' : '' }}">
                    <a class="sidebar-link" href="{{ route('backend.laporan-ubk.index') }}" aria-expanded="false">
                        <span><i class="ti ti-report-money"></i></span>
                        <span class="hide-menu">Laporan UBK</span>
                    </a>
                </li>

            </ul>
        </nav>

        <!-- User Profile Section -->
        <div class="fixed-profile p-3 mx-4 mb-2 bg-secondary-subtle rounded mt-3">
            <div class="hstack gap-3">
                <div class="john-img">
                    <img src="{{ asset('assets/backend/images/profile/user-1.jpg') }}" 
                         class="rounded-circle" 
                         width="40" 
                         height="40" 
                         alt="{{ Auth::user()->name }}" />
                </div>
                <div class="john-title">
                    <h6 class="mb-0 fs-4 fw-semibold">{{ Auth::user()->name }}</h6>
                    <span class="fs-2 text-muted">
                        {{ Auth::user()->isAdmin == 1 ? 'Administrator' : 'Member' }}
                    </span>
                </div>
                <a href="{{ route('logout') }}"
                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                   class="border-0 bg-transparent text-primary ms-auto" 
                   tabindex="0" 
                   type="button"
                   aria-label="Logout" 
                   data-bs-toggle="tooltip" 
                   data-bs-placement="top" 
                   data-bs-title="Logout">
                    <i class="ti ti-power fs-6"></i>
                </a>

                <form action="{{ route('logout') }}" method="POST" id="logout-form" class="d-none">
                    @csrf
                </form>
            </div>
        </div>

        <!-- ================================== -->
        <!-- End Vertical Layout Sidebar -->
        <!-- ================================== -->
    </div>
</aside>

@push('scripts')
<script>
    // Auto active menu based on current URL
    $(document).ready(function() {
        // Get current URL path
        var currentPath = window.location.pathname;
        
        // Find matching sidebar link
        $('#sidebarnav .sidebar-link').each(function() {
            var linkHref = $(this).attr('href');
            if (linkHref && currentPath.includes(linkHref.split('/').pop())) {
                $(this).closest('.sidebar-item').addClass('active');
            }
        });

        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>
@endpush