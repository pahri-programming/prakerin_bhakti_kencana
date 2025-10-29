@extends('layouts.backend')

@section('title', 'Data Peminjaman')

@section('content')
    <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
        <div class="card-header d-flex justify-content-between align-items-center py-3"
            style="background: linear-gradient(90deg, #007bff, #00b4d8); color: #fff;">
            <h5 class="mb-0 d-flex align-items-center">
                <i class="ti ti-clipboard fs-4 me-2"></i> <span>Data Peminjaman</span>
            </h5>
            <div class="d-flex gap-2 ms-auto">
                {{-- <a href="{{ route('backend.peminjaman.exportpdf') }}" class="btn btn-sm btn-danger">
                    <i class="fa fa-file-pdf me-1"></i> Export PDF
                </a> --}}
                <a href="{{ route('backend.peminjaman.create') }}" class="btn btn-success btn-sm">
                    <i class="ti ti-plus fs-5"></i> Tambah Peminjaman
                </a>
            </div>
        </div>

        <div class="card-body bg-light">
            <form method="GET" class="row g-2 mb-4">
                <div class="col-md-3">
                    <select name="barang_id" class="form-select">
                        <option value="">Semua Barang</option>
                        @foreach ($barangs as $barang)
                            <option value="{{ $barang->id }}" {{ request('barang_id') == $barang->id ? 'selected' : '' }}>
                                {{ $barang->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="date" name="tanggal" class="form-control" value="{{ request('tanggal') }}">
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="menunggu" {{ request('status') == 'menunggu' ? 'selected' : '' }}>Menunggu</option>
                        <option value="disetujui" {{ request('status') == 'disetujui' ? 'selected' : '' }}>Disetujui
                        </option>
                        <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                        <option value="dipinjam" {{ request('status') == 'dipinjam' ? 'selected' : '' }}>Dipinjam</option>
                        <option value="dikembalikan" {{ request('status') == 'dikembalikan' ? 'selected' : '' }}>
                            Dikembalikan</option>
                        <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-primary w-100">
                        <i class="ti ti-filter"></i> Filter
                    </button>
                </div>
                <div class="col-md-2">
                    <a href="{{ route('backend.peminjaman.index') }}" class="btn btn-outline-secondary w-100">
                        <i class="ti ti-refresh"></i> Reset
                    </a>
                </div>
            </form>

            <div class="table-responsive shadow-sm rounded-3">
                <table class="table table-hover align-middle mb-0 bg-white rounded">
                    <thead class="table-primary">
                        <tr>
                            <th>#</th>
                            {{-- <th>Nama Customer</th> --}}
                            <th>Nama Peminjam</th>
                            <th>Barang</th>
                            <th>Jumlah</th>
                            <th>Tanggal</th>
                            <th>Waktu</th>
                            <th>Status</th>
                            <th>Keterangan</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($peminjaman as $p)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $p->user->name }}</td>
                                <td>{{ $p->barang->nama }}</td>
                                <td>{{ $p->jumlah }}</td>
                                <td>{{ $p->tanggal_format }}</td>
                                <td>{{ substr($p->waktu_mulai, 0, 5) }} - {{ substr($p->waktu_selesai, 0, 5) }}</td>
                                <td>
                                    @if ($p->status == 'menunggu')
                                        <span class="badge bg-warning">Menunggu</span>
                                    @elseif($p->status == 'disetujui')
                                        <span class="badge bg-info">Disetujui</span>
                                    @elseif($p->status == 'ditolak')
                                        <span class="badge bg-danger">Ditolak</span>
                                    @elseif($p->status == 'dipinjam')
                                        <span class="badge bg-primary">Dipinjam</span>
                                    @elseif($p->status == 'dikembalikan')
                                        <span class="badge bg-success">Dikembalikan</span>
                                    @else
                                        <span class="badge bg-secondary">Selesai</span>
                                    @endif
                                </td>
                                <td>{{ Str::limit($p->keterangan, 30) }}</td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center align-items-center gap-2">
                                        <a href="{{ route('backend.peminjaman.edit', $p->id) }}"
                                            class="btn btn-sm btn-warning d-flex align-items-center px-2">
                                            <i class="ti ti-edit"></i>Edit 
                                        </a>
                                        <a href="{{ route('backend.peminjaman.show', $p->id) }}"
                                            class="btn btn-sm btn-info text-white d-flex align-items-center px-2">
                                            <i class="ti ti-eye"></i>Show
                                        </a>
                                        <form action="{{ route('backend.peminjaman.destroy', $p->id) }}" method="POST"
                                            class="d-inline" id="delete-form-{{ $p->id }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button"
                                                class="btn btn-sm btn-danger d-flex align-items-center px-2"
                                                onclick="confirmDelete({{ $p->id }})">
                                                <i class="ti ti-trash"></i>Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-4">
                                    <i class="ti ti-clipboard-x fs-1 d-block mb-2 text-secondary"></i>
                                    Belum ada data peminjaman.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        function confirmDelete(id) {
            Swal.fire({
                title: 'Yakin Hapus?',
                text: "Data peminjaman akan dihapus permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonText: 'Batal',
                confirmButtonText: 'Ya, Hapus!'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + id).submit();
                }
            });
        }
    </script>
@endsection
