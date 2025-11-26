@component('mail::message')
# Peminjaman Disetujui!

Halo **{{ $peminjaman->user->name }}**,

Peminjaman barang **{{ $peminjaman->barang->nama }}** telah **disetujui**.

**Detail:**
- **Kode Peminjaman:** `{{ $peminjaman->kode }}`
- **Barang:** {{ $peminjaman->barang->nama }}
- **Jumlah:** {{ $peminjaman->jumlah }}
- **Tanggal Pinjam:** {{ $peminjaman->tanggal_format }}
- **Waktu:** {{ $peminjaman->waktu_mulai }} - {{ $peminjaman->waktu_selesai }}

Silakan ambil barang di lokasi yang ditentukan.

@component('mail::button', ['url' => url('/riwayat')])
Lihat Riwayat
@endcomponent

Terima kasih,  
**BKU Prakerin**
@endcomponent