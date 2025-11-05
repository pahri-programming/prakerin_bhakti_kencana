<?php
namespace App\Console\Commands;

use App\Models\booking;
use Illuminate\Console\Command;

class UpdateBookingStatus extends Command
{
    protected $signature   = 'booking:update-status';
    protected $description = 'Update status booking yang sudah lewat';

    public function handle()
    {
        $nowDate = now()->toDateString();
        $nowTime = now()->format('H:i:s'); // pakai detik!

        $updatedCount = booking::where(function ($query) use ($nowDate, $nowTime) {
            $query->where('tanggal', '<', $nowDate)
                ->orWhere(function ($q) use ($nowDate, $nowTime) {
                    $q->where('tanggal', $nowDate)
                        ->where('waktu_selesai', '<', $nowTime);
                });
        })
            ->where('status', '!=', 'Selesai')
            ->update(['status' => 'Selesai']);

        $this->info("Jumlah booking yang diperbarui: " . $updatedCount);
    }

} 
