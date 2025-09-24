@extends('layouts.backend')

@section('content')

 <div class="container-fluid">
          <!--  count -->
          <div class="row">
            <!-- user -->
            <div class="col-md-3"> <!-- atur ukuran card -->
              <div class="card border-0 zoom-in bg-primary shadow">
                <a href="{{route('backend.user.index')}}">
                  <div class="card-body">
                    <div class="text-center">
                      <i class="ti ti-user" style="font-size: 70px; color: white;"></i>
                      <p class="fw-semibold fs-3 text-light mb-1">User</p>
                      <h5 class="fw-semibold text-light mb-0">{{ \App\Models\User::count() }}</h5>
                    </div>
                  </div>
                </a>
              </div>
            </div>
            <!-- end user -->

            <!-- ruangan -->
            <div class="col-md-3"> <!-- atur ukuran card -->
              <div class="card border-0 zoom-in bg-primary shadow">
                <a href="{{route ('backend.ruangan.index')}}">
                  <div class="card-body">
                    <div class="text-center">
                      <i class="ti ti-door" style="font-size: 70px; color: white;"></i>
                      <p class="fw-semibold fs-3 text-light mb-1">Ruangan</p>
                      <h5 class="fw-semibold text-light mb-0">{{ \App\Models\ruangan::count() }}</h5>
                    </div>
                  </div>
                </a>
              </div>
            </div>
            <!-- end ruangan -->

            <!-- jadwal -->
            <div class="col-md-3"> <!-- atur ukuran card -->
              <div class="card border-0 zoom-in bg-primary shadow">
                <a href="{{route ('backend.jadwal.index')}}">
                  <div class="card-body">
                    <div class="text-center">
                      <i class="ti ti-calendar" style="font-size: 70px; color: white;"></i>
                      <p class="fw-semibold fs-3 text-light mb-1">jadwal</p>
                      <h5 class="fw-semibold text-light mb-0">{{ \App\Models\jadwal::count() }}</h5>
                    </div>
                  </div>
                </a>
              </div>
            </div>
            <!-- end jadwal -->

            <!-- booking -->
            <div class="col-md-3"> <!-- atur ukuran card -->
              <div class="card border-0 zoom-in bg-primary shadow">
                {{-- <a href="{{route ('backend.bookings.index')}}"> --}}
                  <div class="card-body">
                    <div class="text-center">
                      <i class="ti ti-bookmark" style="font-size: 70px; color: white;"></i>
                      <p class="fw-semibold fs-3 text-light mb-1">booking</p>
                      <h5 class="fw-semibold text-light mb-0">{{ \App\Models\booking::count() }}</h5>
                    </div>
                  </div>
                </a>
              </div>
            </div>
            <!-- end booking -->
          </div>
          <!-- end count -->

          <!--  Row 1 -->
          <div class="row">
            <div class="col-lg-12 d-flex align-items-stretch">
              <div class="card w-100 shadow">
                <div class="card-body">
                  <div class="d-sm-flex d-block align-items-center justify-content-between mb-7">
                    <div class="mb-3 mb-sm-0">
                      <h4 class="card-title fw-semibold">Booking</h4>
                      <p class="card-subtitle">Status Booking</p>
                    </div>
                  </div>
                  <div class="table-responsive">
                    <table class="table align-middle text-nowrap mb-0">
                      <thead>
                          <tr class="text-muted fw-semibold">
                              <th scope="col">Nama</th>
                              <th scope="col">Ruangan</th>
                              <th scope="col">Tanggal</th>
                              <th scope="col">Jam Mulai</th>
                              <th scope="col">Jam Selesai</th>
                              <th scope="col">Status</th>
                          </tr>
                      </thead>
                      <tbody class="border-top">
                        <!-- isi table -->

                        <!-- end isi table -->
                      </tbody>
                    </table>
                  </div> 
                </div>
              </div>
            </div>
          </div>
        </div>

@endsection