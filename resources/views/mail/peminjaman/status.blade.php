@component('mail::message')
# Status Peminjaman Barang

Halo **{{ $peminjaman->user->name ?? 'User' }}**,

Peminjaman barang **{{ $peminjaman->barang->nama_barang ?? 'Tidak diketahui' }}**  
Jumlah: **{{ $peminjaman->jumlah }} unit**  
Tanggal: {{ $peminjaman->tanggal }}  
Waktu: {{ $peminjaman->waktu_mulai }} - {{ $peminjaman->waktu_selesai }}

Status terbaru: **{{ ucfirst($peminjaman->status) }}**

@component('mail::button', ['url' => route('backend.peminjaman.index')])
Lihat Detail Peminjaman
@endcomponent

Terima kasih,  
{{ config('app.name') }}
@endcomponent
