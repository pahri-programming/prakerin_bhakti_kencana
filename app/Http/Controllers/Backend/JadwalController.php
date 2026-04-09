<?php
namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\booking;
use App\Models\jadwal;
use App\Models\ruangan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class JadwalController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = jadwal::with('ruangan');

        // Filter by search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('kegiatan', 'like', "%{$search}%")
                    ->orWhereHas('ruangan', function ($q2) use ($search) {
                        $q2->where('nama_ruangan', 'like', "%{$search}%");
                    });
            });
        }

        // Filter by ruangan
        if ($request->filled('ruangan_id')) {
            $query->where('ruang_id', $request->ruangan_id);
        }

        // Filter by tanggal
        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal', $request->tanggal);
        }

        // Filter by bulan
        if ($request->filled('bulan')) {
            $query->whereMonth('tanggal', $request->bulan);
        }

        // Filter by tahun
        if ($request->filled('tahun')) {
            $query->whereYear('tanggal', $request->tahun);
        }

        $jadwal = $query->orderBy('tanggal', 'DESC')
            ->orderBy('waktu_mulai', 'ASC')
            ->paginate(15) //  Ubah dari 10 ke 15
            ->through(function ($item) {
                $item->tanggal_format = Carbon::parse($item->tanggal)->translatedFormat('d F Y');
                $item->hari           = Carbon::parse($item->tanggal)->translatedFormat('l');
                $item->status_waktu   = $this->getStatusWaktu($item);
                return $item;
            });

        // Data untuk filter
        $ruangans = ruangan::orderBy('nama_ruangan')->get();
        $bulan    = [
            1 => 'Januari', 2    => 'Februari', 3 => 'Maret', 4     => 'April',
            5 => 'Mei', 6        => 'Juni', 7     => 'Juli', 8      => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];
        $tahun = range(date('Y') - 2, date('Y') + 2); //  +2 tahun ke depan

        // Confirm delete
        $title = 'Hapus Data Jadwal';
        $text  = "Apakah Anda yakin ingin menghapus data jadwal ini?";
        confirmDelete($title, $text);

        return view('backend.jadwal.index', compact('jadwal', 'ruangans', 'bulan', 'tahun'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $ruangans = ruangan::orderBy('nama_ruangan')->get(); //  Tampilkan semua ruangan
        return view('backend.jadwal.create', compact('ruangans'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'ruang_id'      => 'required|exists:ruangans,id',
            'tanggal'       => 'required|date|after_or_equal:today',
            'waktu_mulai'   => 'required|date_format:H:i',
            'waktu_selesai' => 'required|date_format:H:i|after:waktu_mulai',
            'kegiatan'      => 'required|string|max:255',
        ], [
            'required'       => ':attribute harus diisi',
            'exists'         => ':attribute tidak valid',
            'date'           => ':attribute harus berupa tanggal',
            'after_or_equal' => ':attribute tidak boleh sebelum hari ini',
            'date_format'    => 'Format :attribute tidak valid (HH:MM)',
            'after'          => ':attribute harus setelah waktu mulai',
            'max'            => ':attribute maksimal :max karakter',
        ]);

        try {
            //  Cek konflik dengan jadwal lain
            if ($this->checkJadwalConflict(
                $validated['ruang_id'],
                $validated['tanggal'],
                $validated['waktu_mulai'],
                $validated['waktu_selesai']
            )) {
                toast('Jadwal bentrok! Ruangan sudah terpakai pada waktu tersebut.', 'error');
                return back()->withInput();
            }

            //  Cek konflik dengan booking yang sudah diterima
            if ($this->checkBookingConflict(
                $validated['ruang_id'],
                $validated['tanggal'],
                $validated['waktu_mulai'],
                $validated['waktu_selesai']
            )) {
                toast('Jadwal bentrok! Ada booking yang sudah diterima di waktu tersebut.', 'error');
                return back()->withInput();
            }

            DB::beginTransaction();

            $jadwal = jadwal::create($validated);

            DB::commit();

            Log::info('Jadwal berhasil dibuat', [
                'id'         => $jadwal->id,
                'ruangan'    => $jadwal->ruangan->nama_ruangan ?? 'N/A',
                'tanggal'    => $jadwal->tanggal,
                'kegiatan'   => $jadwal->kegiatan,
                'created_by' => auth()->user()->name,
            ]);

            toast('Jadwal berhasil ditambahkan!', 'success');
            return redirect()->route('backend.jadwal.index');

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error saat membuat jadwal', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ]);

            toast('Gagal menambahkan jadwal: ' . $e->getMessage(), 'error');
            return back()->withInput();
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $jadwal   = jadwal::findOrFail($id);
        $ruangans = ruangan::orderBy('nama_ruangan')->get();

        return view('backend.jadwal.edit', compact('jadwal', 'ruangans'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'ruang_id'      => 'required|exists:ruangans,id',
            'tanggal'       => 'required|date',
            'waktu_mulai'   => 'required|date_format:H:i',
            'waktu_selesai' => 'required|date_format:H:i|after:waktu_mulai',
            'kegiatan'      => 'required|string|max:255',
        ], [
            'required'    => ':attribute harus diisi',
            'exists'      => ':attribute tidak valid',
            'date'        => ':attribute harus berupa tanggal',
            'date_format' => 'Format :attribute tidak valid (HH:MM)',
            'after'       => ':attribute harus setelah waktu mulai',
            'max'         => ':attribute maksimal :max karakter',
        ]);

        try {
            $jadwal = jadwal::findOrFail($id);

            //  Cek konflik dengan jadwal lain (kecuali jadwal ini sendiri)
            if ($this->checkJadwalConflict(
                $validated['ruang_id'],
                $validated['tanggal'],
                $validated['waktu_mulai'],
                $validated['waktu_selesai'],
                $id
            )) {
                toast('Jadwal bentrok! Ruangan sudah terpakai pada waktu tersebut.', 'error');
                return back()->withInput();
            }

            //  Cek konflik dengan booking yang sudah diterima
            if ($this->checkBookingConflict(
                $validated['ruang_id'],
                $validated['tanggal'],
                $validated['waktu_mulai'],
                $validated['waktu_selesai']
            )) {
                toast('Jadwal bentrok! Ada booking yang sudah diterima di waktu tersebut.', 'error');
                return back()->withInput();
            }

            DB::beginTransaction();

            $jadwal->update($validated);

            DB::commit();

            Log::info('Jadwal berhasil diupdate', [
                'id'         => $jadwal->id,
                'ruangan'    => $jadwal->ruangan->nama_ruangan ?? 'N/A',
                'tanggal'    => $jadwal->tanggal,
                'kegiatan'   => $jadwal->kegiatan,
                'updated_by' => auth()->user()->name,
            ]);

            toast('Jadwal berhasil diperbarui!', 'success');
            return redirect()->route('backend.jadwal.index');

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error saat update jadwal', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ]);

            toast('Gagal memperbarui jadwal: ' . $e->getMessage(), 'error');
            return back()->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $jadwal = jadwal::findOrFail($id);

            $kegiatan = $jadwal->kegiatan;
            $tanggal  = $jadwal->tanggal_format;

            $jadwal->delete();

            Log::info('Jadwal berhasil dihapus', [
                'id'         => $id,
                'kegiatan'   => $kegiatan,
                'tanggal'    => $tanggal,
                'deleted_by' => auth()->user()->name,
            ]);

            toast('Jadwal berhasil dihapus!', 'success');
            return redirect()->route('backend.jadwal.index');

        } catch (\Exception $e) {
            Log::error('Error saat hapus jadwal', [
                'id'      => $id,
                'message' => $e->getMessage(),
            ]);

            toast('Gagal menghapus jadwal!', 'error');
            return back();
        }
    }

    // =================== HELPER METHODS ===================

    /**
     * Get status waktu jadwal
     */
    private function getStatusWaktu($jadwal)
    {
        $now = Carbon::now();

        $tanggalString = $jadwal->tanggal instanceof Carbon
            ? $jadwal->tanggal->format('Y-m-d')
            : $jadwal->tanggal;

        $tanggalJadwal = Carbon::parse($tanggalString . ' ' . $jadwal->waktu_selesai);

        if ($tanggalJadwal->isPast()) {
            return 'selesai';
        } elseif (Carbon::parse($tanggalString)->isToday()) {
            return 'berlangsung';
        } else {
            return 'akan-datang';
        }
    }

    /**
     *  CEK KONFLIK DENGAN JADWAL LAIN
     */
    private function checkJadwalConflict($ruangId, $tanggal, $waktuMulai, $waktuSelesai, $excludeId = null)
    {
        $query = jadwal::where('ruang_id', $ruangId)
            ->where('tanggal', $tanggal)
            ->where(function ($q) use ($waktuMulai, $waktuSelesai) {
                $q->whereBetween('waktu_mulai', [$waktuMulai, $waktuSelesai])
                    ->orWhereBetween('waktu_selesai', [$waktuMulai, $waktuSelesai])
                    ->orWhere(function ($q2) use ($waktuMulai, $waktuSelesai) {
                        $q2->where('waktu_mulai', '<=', $waktuMulai)
                            ->where('waktu_selesai', '>=', $waktuSelesai);
                    });
            });

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }

    /**
     *  CEK KONFLIK DENGAN BOOKING YANG DITERIMA
     */
    private function checkBookingConflict($ruangId, $tanggal, $waktuMulai, $waktuSelesai)
    {
        return booking::where('ruang_id', $ruangId)
            ->where('tanggal', $tanggal)
            ->where('status', 'Diterima')
            ->where(function ($q) use ($waktuMulai, $waktuSelesai) {
                $q->whereBetween('waktu_mulai', [$waktuMulai, $waktuSelesai])
                    ->orWhereBetween('waktu_selesai', [$waktuMulai, $waktuSelesai])
                    ->orWhere(function ($q2) use ($waktuMulai, $waktuSelesai) {
                        $q2->where('waktu_mulai', '<=', $waktuMulai)
                            ->where('waktu_selesai', '>=', $waktuSelesai);
                    });
            })
            ->exists();
    }
}
