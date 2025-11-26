@component('mail::message')
# Booking Anda Ditolak

Maaf **{{ $booking->user->name }}**,

Booking ruangan **{{ $booking->ruangan->nama_ruangan }}** **ditolak**.

**Detail:**
- **Kode Booking:** `{{ $booking->kode }}`
- **Tanggal:** {{ $booking->tanggal_format }}
- **Waktu:** {{ $booking->waktu_mulai }} - {{ $booking->waktu_selesai }}
- **Alasan:** {{ $booking->keterangan ?? 'Tidak disebutkan' }}

Silakan ajukan ulang jika diperlukan.

@component('mail::button', ['url' => url('/riwayat')])
Lihat Riwayat
@endcomponent

@endcomponent