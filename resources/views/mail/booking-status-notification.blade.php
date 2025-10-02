@component('mail::message')
# Status Booking Ruangan

Halo **{{ $booking->user->name ?? 'User' }}**,

Booking ruangan **{{ $booking->ruangan->nama_ruangan ?? 'Tidak diketahui' }}**  
Tanggal: {{ $booking->tanggal }}  
Jam: {{ $booking->waktu_mulai }} - {{ $booking->waktu_selesai }}  

Status terbaru: **{{ $booking->status }}**

@component('mail::button', ['url' => route('bookings.create')])
Lihat Booking
@endcomponent

Terima kasih,<br>
{{ config('app.name') }}
@endcomponent
