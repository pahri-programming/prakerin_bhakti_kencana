@extends('layouts.backend')

@section('title', 'Data Kategori')
@section('content')
    <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
        <div class="card-header d-flex justify-content-between align-items-center py-3"
            style="background: linear-gradient(90deg, #007bff, #00b4d8); color: #fff;">
            <h5 class="mb-0 d-flex align-items-center">
                <i class="ti ti-tags fs-4 me-2"></i> <span>Data Kategori</span>
            </h5>
            <a href="{{ route('backend.kategori.create') }}" class="btn btn-success btn-sm">
                <i class="ti ti-plus fs-5"></i> Tambah Kategori
            </a>
        </div>

        <div class="card-body bg-light">
            <div class="table-responsive shadow-sm rounded-3">
                <table class="table table-hover align-middle mb-0 bg-white rounded">
                    <thead class="table-primary">
                        <tr>
                            <th>#</th>
                            <th>Nama Kategori</th>
                            <th>Deskripsi</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($kategoris as $kategori)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $kategori->nama }}</td>
                                <td>{{ $kategori->deskripsi }}</td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-2">
                                        <a href="{{ route('backend.kategori.edit', $kategori->id) }}"
                                            class="btn btn-sm btn-primary d-flex align-items-center">
                                            <i class="ti ti-edit me-1"></i> Edit
                                        </a>

                                        <form action="{{ route('backend.kategori.destroy', $kategori->id) }}" method="POST"
                                            id="delete-form-{{ $kategori->id }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn btn-sm btn-danger d-flex align-items-center"
                                                onclick="confirmDelete({{ $kategori->id }})">
                                                <i class="ti ti-trash me-1"></i> Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- SCRIPT SWEETALERT YANG AMAN & TIDAK BENTROK --}}
    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            // Fungsi global, aman dari bentrokan
            window.confirmDelete = function(id) {
                Swal.fire({
                    title: 'Yakin ingin menghapus kategori ini?',
                    text: "Data kategori akan dihapus secara permanen!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonText: 'Batal',
                    confirmButtonText: 'Ya, Hapus!',
                    reverseButtons: true,
                    width: '400px'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Cari form berdasarkan ID
                        const form = document.getElementById('delete-form-' + id);
                        if (form) {
                            form.submit();
                        }
                    }
                });
            };
        </script>
    @endpush

@endsection
