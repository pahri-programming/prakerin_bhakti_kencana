<!DOCTYPE html>
<html lang="en" dir="ltr" data-bs-theme="light" data-color-theme="Blue_Theme" data-layout="vertical">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <title>Bhakti kencana</title>

    <!-- Favicon -->
    <link rel="shortcut icon" type="image/jpg" href="{{ asset('assets/backend/images/logos/ubk.jpg') }}" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="{{ asset('assets/backend/css/styles.css') }}" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <!-- Owl Carousel (optional) -->
    <link rel="stylesheet" href="{{ asset('assets/backend/libs/owl.carousel/dist/assets/owl.carousel.min.css') }}" />


    <!-- Tambahin di <head> atau sebelum </body> -->
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
   
    @stack('styles')
</head>

<body>


    {{-- PRELOADER DIMATIKAN UNTUK KELANCARAN --}}
    {{-- <div class="preloader">
    <img src="{{ asset('assets/backend/images/logos/favicon.png') }}" alt="loader" class="lds-ripple img-fluid" />
  </div> --}}

    <!-- Header -->
    @include('layouts.components-frontend.navbar')

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer>
        @include('layouts.components-frontend.footer')
    </footer>

    <!-- Scroll to Top Button -->
    <a href="javascript:void(0)"
        class="top-btn btn btn-primary d-flex align-items-center justify-content-center round-54 p-0 rounded-circle">
        <i class="ti ti-arrow-up fs-7"></i>
    </a>

    <!-- Vendor JS (Modernize needs this) -->
    <script src="{{ asset('assets/backend/js/vendor.min.js') }}"></script>

    <!-- ✅ Bootstrap Bundle (WAJIB CUMA SATU) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Theme Scripts -->
    <script src="{{ asset('assets/backend/js/theme/app.init.js') }}"></script>
    <script src="{{ asset('assets/backend/js/theme/theme.js') }}"></script>
    <script src="{{ asset('assets/backend/js/theme/app.min.js') }}"></script>

    <!-- SimpleBar -->
    <script src="{{ asset('assets/backend/libs/simplebar/dist/simplebar.min.js') }}"></script>

    <!-- Owl Carousel -->
    <script src="{{ asset('assets/backend/libs/owl.carousel/dist/owl.carousel.min.js') }}"></script>
    <script src="{{ asset('assets/backend/js/frontend-landingpage/homepage.js') }}"></script>

    <!-- Iconify -->
    <script src="https://cdn.jsdelivr.net/npm/iconify-icon@1.0.8/dist/iconify-icon.min.js"></script>

    <!-- SweetAlert -->
    @include('sweetalert::alert')

    @stack('scripts')

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const notifBtn = document.getElementById('notifDropdown');
            notifBtn?.addEventListener('click', function() {
                fetch("{{ url('/notifications/read') }}", {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": "{{ csrf_token() }}",
                        "Accept": "application/json",
                        "Content-Type": "application/json"
                    }
                }).then(() => {
                    const badge = notifBtn.querySelector('.badge');
                    if (badge) badge.remove(); // hapus badge merah
                });
            });
        });
    </script>
 {{-- @vite(['resources/js/app.js']) --}}
</body>

</html>
