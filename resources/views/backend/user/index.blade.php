@extends('layouts.backend')
@section('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.2/css/dataTables.bootstrap5.css">
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-header bg-secondary text-white">
                        Data Akun
                        <a href="{{ route('backend.user.create') }}" class="btn btn-info btn-sm"
                            style="text-color:white;  float: right">
                            Tambah
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table" id="dataAkun">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama</th>
                                        <th>Email</th>
                                        <th>Role</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($user as $data)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $data->name }}</td>
                                            <td>{{ $data->email }}</td>
                                            <td>{{ $data->isAdmin == 1 ? 'Admin' : 'Member' }}</td>

                                            <!-- Kolom baru untuk menampilkan info pembayaran -->
                                            <td>
                                                <a href="{{ route('backend.user.edit', $data->id) }}"
                                                    class="btn btn-sm btn-warning">
                                                    Edit
                                                </a>
                                                @if ($data->isAdmin == 1 && $loop->first)
                                                @else
                                                    <a href="{{ route('backend.user.destroy', $data->id) }}"
                                                        class="btn btn-sm btn-danger"
                                                        onclick="event.preventDefault(); if(confirm('Yakin hapus akun ini?')) { document.getElementById('delete-form-{{ $data->id }}').submit(); }">
                                                        Delete
                                                    </a>
                                                    <form id="delete-form-{{ $data->id }}"
                                                        action="{{ route('backend.user.destroy', $data->id) }}"
                                                        method="POST" style="display: none;">
                                                        @csrf
                                                        @method('DELETE')
                                                    </form>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>
@endsection

@push('scripts')
    <script src="https://cdn.datatables.net/2.3.2/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.3.2/js/dataTables.bootstrap5.js"></script>
    <script>
        new DataTable('#dataAkun', {
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/id.json'
            }
        });
    </script>
@endpush
