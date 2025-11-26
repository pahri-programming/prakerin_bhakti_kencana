@component('mail::message')
# Peminjaman Selesai

Halo **{{ $peminjaman->user->name }}**,

Peminjaman barang **{{ $peminjaman->barang->nama }}** telah **selesai**.

**Detail:**
- **Kode Peminjaman:** `{{ $peminjaman->kode }}`
- **Barang:** {{ $peminjaman->barang->nama }}
- **Jumlah:** {{ $peminjaman->jumlah }}
- **Tanggal Pinjam:** {{ $peminjaman->tanggal_format }}
- **Waktu:** {{ $peminjaman->waktu_mulai }} - {{ $peminjaman->waktu_selesai }}

Terima kasih telah mengembalikan barang tepat waktu.

@component('mail::button', ['url' => url('/riwayat')])
Lihat Riwayat
@endcomponent

@endcomponent