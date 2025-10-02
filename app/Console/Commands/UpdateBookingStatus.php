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
            $updatedCount = booking::where(function ($query) {
                $query->where('tanggal', '<', now()->toDateString())
                    ->orWhere(function ($q) {
                        $q->where('tanggal', now()->toDateString())
                            ->where('waktu_selesai', '<', now()->format('H:i'));
                    });
            })
                ->where('status', '!=', 'Selesai')
                ->update(['status' => 'Selesai']);

            $this->info("Jumlah booking yang diperbarui: " . $updatedCount);
        }
    }
