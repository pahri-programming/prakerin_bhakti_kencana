@extends('layouts.backend')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="card">
                <div class="card-header bg-primary text-white fw-bold">
                    Edit Data Barang
                </div>
                <div class="card-body">
                    <form action="{{ route('backend.barang.update', $barang->id) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="form-floating mb-3">
                            <input type="text" name="kode" class="form-control @error('kode') is-invalid @enderror"
                                placeholder="Kode Barang" value="{{ old('kode', $barang->kode) }}">
                            <label>
                                <i class="ti ti-barcode me-2 fs-4"></i>Kode Barang
                            </label>
                            @error('kode')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="form-floating mb-3">
                            <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror"
                                placeholder="Nama Barang" value="{{ old('nama', $barang->nama) }}">
                            <label>
                                <i class="ti ti-box me-2 fs-4"></i>Nama Barang
                            </label>
                            @error('nama')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="form-floating mb-3">
                            <select name="kategori_id" class="form-select @error('kategori_id') is-invalid @enderror"
                                id="kategori_id">
                                <option value="" disabled>Pilih Kategori</option>
                                @foreach ($kategoris as $kategori)
                                    <option value="{{ $kategori->id }}"
                                        {{ old('kategori_id', $barang->kategori_id) == $kategori->id ? 'selected' : '' }}>
                                        {{ $kategori->nama }}
                                    </option>
                                @endforeach
                            </select>
                            <label for="kategori_id">
                                <i class="ti ti-tags me-2 fs-4"></i>Kategori
                            </label>
                            @error('kategori_id')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="form-floating mb-3">
                            <input type="number" name="stok" class="form-control @error('stok') is-invalid @enderror"
                                placeholder="Stok Barang" value="{{ old('stok', $barang->stok) }}">
                            <label>
                                <i class="ti ti-stack me-2 fs-4"></i>Stok Barang
                            </label>
                            @error('stok')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="form-floating mb-3">
                            <textarea name="deskripsi" class="form-control @error('deskripsi') is-invalid @enderror" placeholder="Deskripsi Barang"
                                style="height: 100px">{{ old('deskripsi', $barang->deskripsi) }}</textarea>
                            <label>
                                <i class="ti ti-file-description me-2 fs-4"></i>Deskripsi Barang
                            </label>
                            @error('deskripsi')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="ti ti-checks me-2"></i>Simpan Perubahan
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
