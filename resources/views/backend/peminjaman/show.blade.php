@extends('layouts.backend')

@section('title', 'Detail Peminjaman Barang')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="card">
            <div class="card-header bg-primary text-white fw-bold">
                Detail Peminjaman
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <table class="table table-borderless">
                            <tbody>
                                <tr>
                                    <th style="width:170px">Nama Peminjaman</th>
                                    <td>
                                        {{ $peminjaman->user->name }}
                                        @if(!empty($peminjaman->user->email))
                                            <small class="text-muted">({{ $peminjaman->user->email }})</small>
                                        @endif
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
                                    <th>Tanggal</th>
                                    <td>{{ $peminjaman->tanggal_format ?? $peminjaman->tanggal }}</td>
                                </tr>
                                <tr>
                                    <th>Waktu</th>
                                    <td>{{ $peminjaman->waktu_range ?? ($peminjaman->waktu_mulai . ' - ' . $peminjaman->waktu_selesai) }}</td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <td>
                                        @php
                                            switch($peminjaman->status) {
                                                case 'menunggu': $c='secondary'; break;
                                                case 'disetujui': $c='primary'; break;
                                                case 'dipinjam': $c='info'; break;
                                                case 'dikembalikan': $c='success'; break;
                                                case 'selesai': $c='dark'; break;
                                                case 'ditolak': $c='danger'; break;
                                                default: $c='secondary';
                                            }
                                        @endphp
                                        <span class="badge bg-{{ $c }}">{{ ucfirst($peminjaman->status) }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Keterangan</th>
                                    <td>{{ $peminjaman->keterangan ?? '-' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="col-md-4">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <p class="mb-1"><strong>Diajukan pada</strong></p>
                                <p class="text-muted mb-3">{{ $peminjaman->created_at->translatedFormat('d F Y H:i') }}</p>

                                @if($peminjaman->updated_at && $peminjaman->updated_at != $peminjaman->created_at)
                                    <p class="mb-1"><strong>Terakhir diubah</strong></p>
                                    <p class="text-muted">{{ $peminjaman->updated_at->translatedFormat('d F Y H:i') }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-3">
                    <a href="{{ route('backend.peminjaman.index') }}" class="btn btn-secondary">
                        <i class="ti ti-arrow-left me-1"></i> Kembali
                    </a>

                    <a href="{{ route('backend.peminjaman.edit', $peminjaman->id) }}" class="btn btn-primary ms-2">
                        <i class="ti ti-edit me-1"></i> Edit
                    </a>

                    <form action="{{ route('backend.peminjaman.destroy', $peminjaman->id) }}" method="POST" class="d-inline-block ms-2" onsubmit="return confirm('Apakah anda yakin ingin menghapus data ini?');">
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
</div>
@endsection