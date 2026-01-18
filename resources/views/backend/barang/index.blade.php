@extends('layouts.backend')

@section('title', 'Data Barang')

@section('content')
    <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
        <div class="card-header d-flex justify-content-between align-items-center py-3"
            style="background: linear-gradient(90deg, #007bff, #00b4d8); color: #fff;">
            <h5 class="mb-0 d-flex align-items-center">
                <i class="ti ti-package fs-4 me-2"></i> <span>Data Barang</span>
            </h5>
            <div class="d-flex gap-2 ms-auto">
                <a href="{{ route('backend.barang.exportpdf') }}" class="btn btn-sm btn-danger">
                    <i class="fa fa-file-pdf me-1"></i> Export PDF
                </a>
                <a href="{{ route('backend.barang.create') }}" class="btn btn-success btn-sm">
                    <i class="ti ti-plus fs-5"></i> Tambah Barang
                </a>
            </div>
        </div>

        <div class="card-body bg-light">
            <form method="GET" class="row g-2 mb-4">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="ti ti-search text-primary"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="Cari nama barang..."
                            value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <select name="kategori" class="form-select">
                        <option value="">Semua Kategori</option>
                        @foreach ($kategoris as $kategori)
                            <option value="{{ $kategori->id }}"
                                {{ request('kategori') == $kategori->id ? 'selected' : '' }}>
                                {{ $kategori->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-primary w-100">
                        <i class="ti ti-filter"></i> Filter
                    </button>
                </div>
                <div class="col-md-2">
                    <a href="{{ route('backend.barang.index') }}" class="btn btn-outline-secondary w-100">
                        <i class="ti ti-refresh"></i> Reset
                    </a>
                </div>
            </form>

            <div class="table-responsive shadow-sm rounded-3">
                <table class="table table-hover align-middle mb-0 bg-white rounded">
                    <thead class="table-primary">
                        <tr>
                            <th>#</th>
                            <th>Nama</th>
                            <th>Kategori</th>
                            <th>Keterangan</th>
                            <th>Tanggal Input</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($barangs as $barang)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $barang->nama }}</td>
                                <td>{{ $barang->kategori?->nama ?? '-' }}</td>
                                <td>{{ Str::limit($barang->keterangan, 40) }}</td>
                                <td>{{ $barang->created_at_format }}</td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center align-items-center gap-2">
                                        <a href="{{ route('backend.barang.show', $barang->id) }}"
                                            class="btn btn-sm btn-info text-white d-flex align-items-center px-2">
                                            <i class="ti ti-eye"></i>
                                            <span class="ms-1 d-none d-md-inline">Show</span>
                                        </a>

                                        <a href="{{ route('backend.barang.edit', $barang->id) }}"
                                            class="btn btn-sm btn-warning d-flex align-items-center px-2">
                                            <i class="ti ti-edit"></i>
                                            <span class="ms-1 d-none d-md-inline">Edit</span>
                                        </a>

                                        {{-- ✅ PERBAIKAN: Tambah ID pada form --}}
                                        <form action="{{ route('backend.barang.destroy', $barang->id) }}" method="POST"
                                            id="delete-form-{{ $barang->id }}" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button"
                                                class="btn btn-sm btn-danger d-flex align-items-center px-2"
                                                onclick="confirmDelete({{ $barang->id }})">
                                                <i class="ti ti-trash"></i>
                                                <span class="ms-1 d-none d-md-inline">Delete</span>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    <i class="ti ti-box fs-1 d-block mb-2 text-secondary"></i>
                                    Belum ada data barang.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- SCRIPT SWEETALERT --}}
    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            window.confirmDelete = function(id) {
                Swal.fire({
                    title: 'Yakin ingin menghapus barang ini?',
                    text: "Data barang akan dihapus permanen dan tidak bisa dikembalikan!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonText: 'Batal',
                    confirmButtonText: 'Ya, Hapus!',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('delete-form-' + id).submit();
                    }
                });
            };
        </script>
    @endpush
@endsection
