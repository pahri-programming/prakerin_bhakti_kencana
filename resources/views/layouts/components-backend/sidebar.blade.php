<aside class="left-sidebar with-vertical">
    <div><!-- ---------------------------------- -->
        <!-- Start Vertical Layout Sidebar -->
        <!-- ---------------------------------- -->
        <div class="brand-logo d-flex align-items-center justify-content-between">
            <a href="{{ asset('assets/main/index.html') }}" class="text-nowrap logo-img">
                <img src="{{ asset('assets/backend/images/logos/dark-logo.svg') }}" class="dark-logo" alt="Logo-Dark" />
                <img src="{{ asset('assets/backend/images/logos/light-logo.svg') }}" class="light-logo" alt="Logo-light" />
            </a>
            <a href="javascript:void(0)" class="sidebartoggler ms-auto text-decoration-none fs-5 d-block d-xl-none">
                <i class="ti ti-x"></i>
            </a>
        </div>

        <nav class="sidebar-nav scroll-sidebar" data-simplebar>
            <ul id="sidebarnav">
                <!-- ---------------------------------- -->
                <!-- Home -->
                <!-- ---------------------------------- -->
                <li class="nav-small-cap">
                    <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
                    <span class="hide-menu">Home</span>
                </li>
                <!-- ---------------------------------- -->
                <!-- Dashboard -->
                <!-- ---------------------------------- -->
                <li class="sidebar-item">
                    <a class="sidebar-link" href="{{ url('admin') }}" aria-expanded="false">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                            <path fill="currentColor"
                                d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z" />
                        </svg>
                        <span class="hide-menu">Dashboard</span>
                    </a>
                </li>
                <!-- ---------------------------------- -->
                <!-- Frontend page -->
                <!-- ---------------------------------- -->

                <!-- ---------------------------------- -->
                <!-- Apps -->
                <!-- ---------------------------------- -->

                <!-- ---------------------------------- -->
                <!-- PAGES -->
                <!-- ---------------------------------- -->
                <li class="nav-small-cap">
                    <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
                    <span class="hide-menu">Menu</span>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link" href="./main/page-pricing.html" aria-expanded="false">
                        <span>
                            <i class="ti ti-currency-dollar"></i>
                        </span>
                        <span class="hide-menu">Pricing</span>
                    </a>
                </li>
        </nav>

        {{-- profile akun --}}
        <div class="fixed-profile p-3 mx-4 mb-2 bg-secondary-subtle rounded mt-3">
            <div class="hstack gap-3">
                <div class="john-img">
                    <img src="{{ asset('assets/backend/images/profile/user-1.jpg') }}" class="rounded-circle"
                        width="40" height="40" alt="modernize-img" />
                </div>
                <div class="john-title">
                    <h6 class="mb-0 fs-4 fw-semibold">{{ Auth::user()->name }}</h6>
                    <span class="fs-2">
                        {{ Auth::user()->isAdmin == 1 ? 'admin' : 'member' }}
                    </span>

                </div>
                <a href="{{ route('logout') }}"
                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                    class="border-0 bg-transparent text-primary ms-auto" tabindex="0" type="button"
                    aria-label="logout" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="logout">
                    <i class="ti ti-power fs-6"></i>
                </a>

                <form action="{{ route('logout') }}" method="POST" id="logout-form">
                    @csrf
                </form>
            </div>
        </div>

        <!-- ---------------------------------- -->
        <!-- Start Vertical Layout Sidebar -->
        <!-- ---------------------------------- -->
    </div>
</aside>
