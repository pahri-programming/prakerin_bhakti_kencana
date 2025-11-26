@component('mail::message')
# Booking Anda Diterima!

Halo **{{ $booking->user->name }}**,

Booking ruangan **{{ $booking->ruangan->nama_ruangan }}** telah **diterima**.

**Detail:**
- **Kode Booking:** `{{ $booking->kode }}`
- **Tanggal:** {{ $booking->tanggal_format }}
- **Waktu:** {{ $booking->waktu_mulai }} - {{ $booking->waktu_selesai }}

Silakan datang tepat waktu.

@component('mail::button', ['url' => url('/riwayat')])
Lihat Riwayat
@endcomponent

Terima kasih,  
**BKU Prakerin**
@endcomponent