@component('mail::message')
# Peminjaman Ditolak

Maaf **{{ $peminjaman->user->name }}**,

Peminjaman barang **{{ $peminjaman->barang->nama }}** **ditolak**.

**Detail:**
- **Kode Peminjaman:** `{{ $peminjaman->kode }}`
- **Barang:** {{ $peminjaman->barang->nama }}
- **Jumlah:** {{ $peminjaman->jumlah }}
- **Alasan:** {{ $peminjaman->deskripsi ?? 'Tidak disebutkan' }}

Silakan ajukan ulang jika diperlukan.

@component('mail::button', ['url' => url('/riwayat')])
Lihat Riwayat
@endcomponent

@endcomponent