<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;

class CheckPeminjaman extends Command
{
    protected $signature   = 'peminjaman:check-expired';
    protected $description = 'Check peminjaman yang expired dan mark as selesai (kembalikan stok).';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    // protected $signature = 'app:check-peminjaman';

    /**
     * The console command description.
     *
     * @var string
     */
    // protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        app(\App\Services\PeminjamanService::class)->checkExpired();
        $this->info('Check expired executed.');

    }

}
