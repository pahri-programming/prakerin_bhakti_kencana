<?php
namespace App\Notifications;

use App\Models\PeminjamanBarang;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PeminjamanStatusNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $peminjaman;

    public function __construct(PeminjamanBarang $peminjaman)
    {
        $this->peminjaman = $peminjaman;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Status Peminjaman Barang')
            ->greeting('Halo ' . ($notifiable->name ?? 'User'))
            ->line("Peminjaman barang **{$this->peminjaman->barang->nama_barang}**")
            ->line("Tanggal: {$this->peminjaman->tanggal}")
            ->line("Waktu: {$this->peminjaman->waktu_mulai} - {$this->peminjaman->waktu_selesai}")
            ->line("Status terbaru: **{$this->peminjaman->status}**")
            ->action('Lihat Peminjaman', route('peminjaman.index'))
            ->line('Terima kasih telah menggunakan sistem kami!');
    }

    public function toArray($notifiable)
    {
        return [
            'peminjaman_id' => $this->peminjaman->id,
            'barang'        => $this->peminjaman->barang->nama_barang,
            'status'        => $this->peminjaman->status,
            'tanggal'       => $this->peminjaman->tanggal,
            'waktu_mulai'   => $this->peminjaman->waktu_mulai,
            'waktu_selesai' => $this->peminjaman->waktu_selesai,
        ];
    }
}
