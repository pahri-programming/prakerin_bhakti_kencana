@extends('layouts.frontend')

@section('styles')
    <style>
        .form-floating>.form-control {
            height: calc(3.5rem + 2px);
            padding: 1rem 0.75rem;
        }

        .form-floating>label {
            padding: 1rem 0.75rem;
        }
    </style>
@endsection

@section('content')
    <div class="container py-5">
        @if ($booking->count())
            <div class="card shadow rounded-4 border-0 mb-5">

                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <span>
                        <i class="bi bi-clock-history me-2"></i> Riwayat Booking Anda
                    </span>

                    <a href="{{ route('riwayat.booking.export', [
                        'ruang_id' => request('ruang_id'),
                        'tanggal' => request('tanggal'),
                        'status_booking' => request('status_booking'),
                    ]) }}"
                        class="btn btn-danger btn-sm">
                        <i class="bi bi-file-earmark-pdf me-1"></i> Export PDF
                    </a>
                </div>

                <div class="card-body">
                    <form action="{{ route('riwayat.index') }}" method="GET" class="row g-3 mb-4 bg-light p-3 rounded shadow-sm">

                        <div class="col-md-4">
                            <div class="form-floating">
                                <select name="ruang_id" id="ruang_id" class="form-select">
                                    <option value="">Pilih Ruangan</option>
                                    @foreach ($ruangan as $ru)
                                        <option value="{{ $ru->id }}"
                                            {{ request('ruang_id') == $ru->id ? 'selected' : '' }}>
                                            {{ $ru->nama_ruangan }}
                                        </option>
                                    @endforeach
                                </select>
                                <label for="ruang_id"><i class="bi bi-door-open me-1"></i> Ruangan</label>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-floating">
                                <select name="status_booking" id="status_booking" class="form-select">
                                    <option value="">Pilih Status</option>
                                    <option value="Pending"
                                        {{ request('status_peminjaman') == 'Pending' ? 'selected' : '' }}>Pending
                                    </option>
                                    <option value="Diterima"
                                        {{ request('status_peminjaman') == 'Diterima' ? 'selected' : '' }}>
                                        Diterima</option>
                                    <option value="Ditolak"
                                        {{ request('status_peminjaman') == 'Ditolak' ? 'selected' : '' }}>Ditolak
                                    </option>
                                    <option value="Selesai"
                                        {{ request('status_peminjaman') == 'Selesai' ? 'selected' : '' }}>Selesai
                                    </option>
                                </select>
                                <label for="status_peminjaman"><i class="bi bi-info-circle me-1"></i> Status</label>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-floating">
                                <input type="date" name="tanggal" id="tanggal" class="form-control"
                                    value="{{ request('tanggal') }}">
                                <label for="tanggal"><i class="bi bi-calendar3 me-1"></i> Tanggal</label>
                            </div>
                        </div>

                        <div class="col-md-2 d-flex gap-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-funnel-fill me-1"></i> Filter
                            </button>
                            <a href="{{ route('riwayat.index') }}"
                                class="btn btn-outline-secondary w-100 d-flex justify-content-center align-items-center">
                                <i class="bi bi-arrow-repeat me-1"></i> Reset
                            </a>

                        </div>

                    </form>

                    <div class="table-responsive shadow-sm rounded-4">
                        <table class="table table-hover align-middle text-center mb-0">
                            <thead class="table-light">
                                <tr class="fw-semibold text-uppercase small text-secondary">
                                    <th>#</th>
                                    <th>Kode Booking</th>
                                    <th>Ruangan</th>
                                    <th>Tanggal</th>
                                    <th>Waktu</th>
                                    <th>Status</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse ($booking as $data)
                                    <tr data-booking-id="{{ $data->id }}">
                                        <td class="text-muted">{{ $loop->iteration }}</td>
                                        <td>{{ $data->kode }}</td>
                                        <td class="fw-medium">{{ $data->ruangan->nama_ruangan }}</td>
                                        <td>{{ \Carbon\Carbon::parse($data->tanggal)->translatedFormat('l, d F Y') }}</td>
                                        <td>{{ $data->waktu_mulai }} - {{ $data->waktu_selesai }}</td>

                                        <td>
                                            @switch($data->status)
                                                @case('Pending')
                                                    <span class="badge bg-warning text-dark px-3 py-2">Menunggu</span>
                                                @break

                                                @case('Diterima')
                                                    <span class="badge bg-primary px-3 py-2">Diterima</span>
                                                @break

                                                @case('Ditolak')
                                                    <span class="badge bg-danger px-3 py-2">Ditolak</span>
                                                @break

                                                @case('Selesai')
                                                    <span class="badge bg-success px-3 py-2">Selesai</span>
                                                @break

                                                @default
                                                    <span class="badge bg-secondary px-3 py-2">Tidak Diketahui</span>
                                            @endswitch
                                        </td>
                                    </tr>

                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-muted py-4">Tidak ada data booking ditemukan.</td>
                                        </tr>
                                    @endforelse
                                </tbody>

                            </table>
                        </div>

                    </div>
                </div>
            @else
                <div class="alert alert-info text-center mt-4">
                    <i class="bi bi-info-circle-fill me-2"></i> Belum ada riwayat booking ruangan.
                </div>
            @endif

            <!-- Riwayat Peminjaman Barang -->
            @if ($peminjaman->count())
                <div class="card shadow rounded-4 border-0 mb-5">
                    <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                        <span>
                            <i class="bi bi-box-seam me-2"></i> Riwayat Peminjaman Barang
                        </span>
                        <a href="{{ route('riwayat.peminjaman.export', [
                            'barang_id'         => request('barang_id'),
                            'status_peminjaman' => request('status_peminjaman'),
                            'tanggal_pinjam'    => request('tanggal_pinjam'),
                            'tanggal_kembali'   => request('tanggal_kembali'),
                        ]) }}"
                            class="btn btn-danger btn-sm">
                            <i class="bi bi-file-earmark-pdf me-1"></i> Export PDF
                        </a>
                    </div>

                    <div class="card-body">
                        <form action="{{ route('riwayat.index') }}" method="GET" class="mb-4 bg-light p-3 rounded shadow-sm">

                            <!-- BARIS PERTAMA: Input -->
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <div class="form-floating">
                                        <select name="barang_id" id="barang_id" class="form-select">
                                            <option value="">Pilih Barang</option>
                                            @foreach ($barang as $item)
                                                <option value="{{ $item->id }}"
                                                    {{ request('barang_id') == $item->id ? 'selected' : '' }}>
                                                    {{ $item->nama }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <label for="barang_id"><i class="bi bi-archive me-1"></i> Barang</label>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-floating">
                                        <select name="status_peminjaman" id="status_peminjaman" class="form-select">
                                            <option value="">Pilih Status</option>
                                            <option value="menunggu"
                                                {{ request('status_peminjaman') == 'menunggu' ? 'selected' : '' }}>
                                                Menunggu</option>
                                            <option value="disetujui"
                                                {{ request('status_peminjaman') == 'disetujui' ? 'selected' : '' }}>
                                                Disetujui</option>
                                            <option value="ditolak"
                                                {{ request('status_peminjaman') == 'ditolak' ? 'selected' : '' }}>
                                                Ditolak</option>
                                            <option value="selesai"
                                                {{ request('status_peminjaman') == 'selesai' ? 'selected' : '' }}>
                                                Selesai</option>
                                        </select>
                                        <label for="status_peminjaman"><i class="bi bi-info-circle me-1"></i> Status</label>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-floating">
                                        <input type="date" name="tanggal_pinjam" id="tanggal_pinjam" class="form-control"
                                            value="{{ request('tanggal_pinjam') }}">
                                        <label for="tanggal_pinjam"><i class="bi bi-calendar3 me-1"></i> Tanggal
                                            Pinjam</label>
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="form-floating">
                                        <input type="date" name="tanggal_kembali" id="tanggal_kembali"
                                            class="form-control" value="{{ request('tanggal_kembali') }}">
                                        <label for="tanggal_kembali"><i class="bi bi-calendar3 me-1"></i> Tanggal
                                            Kembali</label>
                                    </div>
                                </div>
                            </div>

                            <!-- BARIS KEDUA: Tombol Filter & Reset -->
                            <div class="row g-3 mt-2">
                                <div class="col-md-10"></div> <!-- Spacer -->
                                <div class="col-md-2 d-flex gap-2">
                                    <button type="submit" class="btn btn-success w-100">
                                        <i class="bi bi-funnel-fill me-1"></i> Filter
                                    </button>
                                    <a href="{{ route('riwayat.index') }}"
                                        class="btn btn-outline-secondary w-100 d-flex justify-content-center align-items-center">
                                        <i class="bi bi-arrow-repeat me-1"></i> Reset
                                    </a>
                                </div>
                            </div>

                        </form>

                        <div class="table-responsive shadow-sm rounded-4">
                            <table class="table table-hover align-middle text-center mb-0">
                                <thead class="table-light">
                                    <tr class="fw-semibold text-uppercase small text-secondary">
                                        <th>#</th>
                                        <th>KODE</th>
                                        <th>BARANG</th>
                                        <th>JUMLAH</th>
                                        <th>TANGGAL PINJAM</th>
                                        <th>TANGGAL KEMBALI</th>
                                        <th>WAKTU</th>
                                        <th>STATUS</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($peminjaman as $p)
                                        <tr data-peminjaman-id="{{ $p->id }}">
                                            <td class="text-muted">{{ $loop->iteration }}</td>
                                            <td>{{ $p->kode }}</td>
                                            <td class="fw-medium">{{ $p->barang->nama }}</td>
                                            <td>{{ $p->jumlah }}</td>
                                            <td>{{ \Carbon\Carbon::parse($p->tanggal_pinjam)->translatedFormat('l, d F Y') }}
                                            </td>
                                            <td>
                                                @if ($p->tanggal_kembali)
                                                    {{ \Carbon\Carbon::parse($p->tanggal_kembali)->translatedFormat('l, d F Y') }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>{{ \Carbon\Carbon::createFromFormat('H:i:s', $p->waktu_mulai)->format('H:i') }}
                                                -
                                                {{ \Carbon\Carbon::createFromFormat('H:i:s', $p->waktu_selesai)->format('H:i') }}
                                            </td>
                                            <td>
                                                @switch($p->status)
                                                    @case('menunggu')
                                                        <span class="badge bg-warning text-dark px-3 py-2">Menunggu</span>
                                                    @break

                                                    @case('disetujui')
                                                        <span class="badge bg-info text-white px-3 py-2">Disetujui</span>
                                                    @break

                                                    @case('ditolak')
                                                        <span class="badge bg-danger px-3 py-2">Ditolak</span>
                                                    @break

                                                    @case('selesai')
                                                        <span class="badge bg-success px-3 py-2">Selesai</span>
                                                    @break

                                                    @default
                                                        <span
                                                            class="badge bg-secondary px-3 py-2">{{ ucfirst($p->status_peminjaman) }}</span>
                                                @endswitch
                                            </td>
                                        </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="text-muted py-4">Tidak ada data peminjaman ditemukan.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="alert alert-info text-center mt-4">
                        <i class="bi bi-info-circle-fill me-2"></i> Belum ada riwayat peminjaman barang.
                    </div>
                @endif


                @push('scripts')
                    <script>
                        // Cek otomatis
                        setInterval(async () => {
                            try {
                                await fetch("{{ route('api.peminjaman.check') }}", {
                                    headers: {
                                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                                    }
                                });
                            } catch (e) {}
                        }, 30000);

                        // 2. REAL-TIME UPDATE
                        window.Echo.channel('peminjaman')
                            .listen('PeminjamanExpired', (e) => {
                                console.log('REAL-TIME:', e);

                                const row = document.querySelector(`[data-peminjaman-id="${e.peminjaman.id}"]`);
                                if (row) {
                                    const badge = row.querySelector('.badge');
                                    badge.className = 'badge bg-secondary';
                                    badge.textContent = 'Selesai';
                                }

                                Toastify({
                                    text: `Peminjaman ${e.peminjaman.barang.nama} selesai! Stok +${e.peminjaman.jumlah}`,
                                    backgroundColor: "linear-gradient(to right, #00b09b, #96c93d)",
                                    duration: 5000
                                }).showToast();
                            });

                        // POLLING BACKUP
                        setInterval(async () => {
                            try {
                                await fetch("{{ route('api.booking.check') }}", {
                                    headers: {
                                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                                    }
                                });
                            } catch (e) {}
                        }, 30000);

                        // REAL-TIME BOOKING
                        window.Echo.channel('booking')
                            .listen('BookingExpired', (e) => {
                                console.log('BOOKING SELESAI:', e);

                                const row = document.querySelector(`[data-booking-id="${e.booking.id}"]`);
                                if (row) {
                                    const badge = row.querySelector('.badge');
                                    badge.className = 'badge bg-success px-3 py-2';
                                    badge.textContent = 'Selesai';
                                }

                                Toastify({
                                    text: `Booking ${e.booking.ruang_nama} selesai!`,
                                    backgroundColor: "linear-gradient(to right, #00b09b, #96c93d)",
                                    duration: 5000
                                }).showToast();
                            });
                    </script>
                @endpush


            @endsection
