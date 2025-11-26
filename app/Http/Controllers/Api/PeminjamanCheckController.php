<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PeminjamanBarang;
use App\Services\StockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PeminjamanCheckController extends Controller
{
    public function check(Request $request)
    {
        $now = now()->toDateTimeString();

        Log::info('CHECK EXPIRED START', ['now' => $now]);

        // Ambil peminjaman yang sudah lewat end datetime dan belum diselesaikan
        $expired = PeminjamanBarang::whereNotIn('status', [
            'selesai', 'dikembalikan', 'ditolak',
        ])
            ->whereRaw("TIMESTAMP(tanggal_kembali, waktu_selesai) < ?", [$now])
            ->get();

        Log::info('FOUND EXPIRED', ['count' => $expired->count(), 'ids' => $expired->pluck('id')]);

        $updated  = 0;
        $stockSvc = new StockService();

        foreach ($expired as $p) {
            DB::transaction(function () use ($p, $stockSvc, &$updated) {
                // refetch with lock to avoid race
                $fresh = PeminjamanBarang::lockForUpdate()->find($p->id);
                if (! $fresh) {
                    return;
                }

                // kalau sudah berubah (double-check) skip
                if (in_array($fresh->status, ['selesai', 'dikembalikan', 'ditolak'])) {
                    return;
                }

                $old = $fresh->status;

                // HANYA kembalikan stok kalau sebelumnya mengurangi stok
                if (in_array($old, ['disetujui', 'dipinjam'])) {
                    $stockSvc->increase($fresh->barang_id, $fresh->jumlah);
                    Log::info("Stock returned for peminjaman {$fresh->id}: +{$fresh->jumlah} to barang {$fresh->barang_id}");
                }

                $fresh->status = 'selesai';
                $fresh->save();

                // broadcast setelah commit (ShouldBroadcast akan handlenya)
                event(new \App\Events\PeminjamanExpired($fresh));
                event(new \App\Events\PeminjamanStatusChanged($fresh, $old));

                $updated++;
            }, 5);
        }

        return response()->json(['updated' => $updated]);
    }
}
