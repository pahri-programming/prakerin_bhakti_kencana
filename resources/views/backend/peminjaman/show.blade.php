@extends('layouts.backend')

@section('title', 'Detail Peminjaman Barang')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="card">
                <div class="card-header bg-primary text-white fw-bold">
                    Detail Peminjaman
                </div>
                <div class="row g-3 p-4">
                    <div class="col-lg-8">
                        <div class="card shadow-sm">
                            <div class="card-body">

                                @if ($peminjaman->status === 'ditolak' && $peminjaman->deskripsi)
                                    <div class="alert alert-danger mb-4">
                                        <h6><i class="ti ti-alert-circle"></i> Alasan Penolakan</h6>
                                        <hr>
                                        <p class="mb-0">{{ $peminjaman->deskripsi }}</p>
                                    </div>
                                @endif

                                <table class="table table-borderless">
                                    <tbody>

                                        <tr>
                                            <th style="width:180px">Nama Peminjaman</th>
                                            <td>
                                                {{ $peminjaman->user->name }}
                                                <small class="text-muted">({{ $peminjaman->user->email }})</small>
                                            </td>
                                        </tr>

                                        <tr>
                                            <th>Barang</th>
                                            <td>{{ $peminjaman->barang->nama ?? '-' }}</td>
                                        </tr>

                                        <tr>
                                            <th>Jumlah</th>
                                            <td>{{ $peminjaman->jumlah }}</td>
                                        </tr>

                                        <tr>
                                            <th>Tanggal Pinjam</th>
                                            <td>{{ $peminjaman->tanggal_pinjam_format }}</td>
                                        </tr>

                                        <tr>
                                            <th>Tanggal Kembali</th>
                                            <td>{{ $peminjaman->tanggal_kembali_format ?? '-' }}</td>
                                        </tr>

                                        <tr>
                                            <th>Waktu</th>
                                            <td>{{ $peminjaman->waktu_range }}</td>
                                        </tr>

                                        <tr>
                                            <th>Status</th>
                                            <td>
                                                @php
                                                    $c = match ($peminjaman->status) {
                                                        'menunggu' => 'secondary',
                                                        'disetujui' => 'primary',
                                                        'dipinjam' => 'info',
                                                        'dikembalikan' => 'success',
                                                        'selesai' => 'dark',
                                                        'ditolak' => 'danger',
                                                        default => 'secondary',
                                                    };
                                                @endphp
                                                <span class="badge bg-{{ $c }}">
                                                    {{ ucfirst($peminjaman->status) }}
                                                </span>
                                            </td>
                                        </tr>

                                        <tr>
                                            <th>Keterangan</th>
                                            <td>{{ $peminjaman->keterangan ?? '-' }}</td>
                                        </tr>

                                    </tbody>
                                </table>

                                <div class="mt-3">
                                    <a href="{{ route('backend.peminjaman.index') }}" class="btn btn-secondary">
                                        <i class="ti ti-arrow-left me-1"></i> Kembali
                                    </a>

                                    <a href="{{ route('backend.peminjaman.edit', $peminjaman->id) }}"
                                        class="btn btn-primary ms-2">
                                        <i class="ti ti-edit me-1"></i> Edit
                                    </a>

                                    <form action="{{ route('backend.peminjaman.destroy', $peminjaman->id) }}"
                                        method="POST" class="d-inline-block ms-2"
                                        onsubmit="return confirm('Apakah anda yakin ingin menghapus data ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger">
                                            <i class="ti ti-trash me-1"></i> Hapus
                                        </button>
                                    </form>
                                </div>

                            </div>
                        </div>
                    </div>

                    {{-- Kolom Kanan — Info Meta --}}
                    <div class="col-lg-4">
                        <div class="card shadow-sm">
                            <div class="card-body py-3">

                                <p class="mb-1"><strong>Diajukan pada</strong></p>
                                <p class="text-muted mb-3">
                                    {{ $peminjaman->created_at->translatedFormat('d F Y H:i') }}
                                </p>

                                @if ($peminjaman->updated_at != $peminjaman->created_at)
                                    <p class="mb-1"><strong>Terakhir diubah</strong></p>
                                    <p class="text-muted mb-0">
                                        {{ $peminjaman->updated_at->translatedFormat('d F Y H:i') }}
                                    </p>
                                @endif

                            </div>
                        </div>
                    </div>

                </div>
            </div>
        @endsection
