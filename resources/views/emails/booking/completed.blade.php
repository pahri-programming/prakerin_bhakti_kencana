@component('mail::message')
# Booking Selesai

Halo **{{ $booking->user->name }}**,

Booking ruangan **{{ $booking->ruangan->nama_ruangan }}** telah **selesai**.

Terima kasih telah menggunakan layanan kami.

@component('mail::button', ['url' => url('/riwayat')])
Lihat Riwayat
@endcomponent

@endcomponent