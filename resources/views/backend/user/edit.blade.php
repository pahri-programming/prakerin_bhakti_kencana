@extends('layouts.backend')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="card">
                    <div class="card-header bg-primary text-white fw-bold">
                        Edit Data Akun
                    </div>
                    <div class="card-body">
                        <form action="{{ route('backend.user.update', $user->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="form-floating mb-3">
                                <input type="text" name="name" value="{{ old('name', $user->name) }}"
                                    class="form-control @error('name') is-invalid @enderror" placeholder="Nama">
                                <label>
                                    <i class="ti ti-user me-2 fs-4"></i>Nama
                                </label>
                                @error('name')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>

                            <div class="form-floating mb-3">
                                <input type="email" name="email" value="{{ old('email', $user->email) }}"
                                    class="form-control @error('email') is-invalid @enderror" placeholder="Email">
                                <label>
                                    <i class="ti ti-mail me-2 fs-4"></i>Email
                                </label>
                                @error('email')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>

                            <div class="form-floating mb-3">
                                <input type="password" name="password"
                                    class="form-control @error('password') is-invalid @enderror" placeholder="Password">
                                <label>
                                    <i class="ti ti-lock me-2 fs-4"></i>Password Baru
                                </label>
                                @error('password')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>

                            <div class="form-floating mb-3">
                                <input type="password" name="password_confirmation" class="form-control"
                                    placeholder="Konfirmasi Password">
                                <label>
                                    <i class="ti ti-lock me-2 fs-4"></i>Konfirmasi Password Baru
                                </label>
                            </div>


                            <div class="d-flex justify-content-end mt-4">
                                <button type="submit" class="btn btn-warning hstack gap-2">
                                    <i class="ti ti-edit fs-5"></i> Update
                                </button>
                                <a href="{{ route('backend.user.index') }}" class="btn btn-secondary hstack gap-2 ms-2">
                                    <i class="ti ti-arrow-back-up fs-5"></i> Kembali
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
