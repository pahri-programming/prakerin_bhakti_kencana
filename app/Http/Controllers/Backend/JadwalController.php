<?php
namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Jadwal;
use App\Models\Ruangan;
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
        $query = Jadwal::with('ruangan');

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
            ->paginate(10)
            ->through(function ($item) {
                $item->tanggal_format = Carbon::parse($item->tanggal)->translatedFormat('d F Y');
                $item->hari           = Carbon::parse($item->tanggal)->translatedFormat('l');
                $item->status_waktu   = $this->getStatusWaktu($item);
                return $item;
            });

        // Data untuk filter
        $ruangans = Ruangan::orderBy('nama_ruangan')->get();
        $bulan    = [
            1 => 'Januari', 2    => 'Februari', 3 => 'Maret', 4     => 'April',
            5 => 'Mei', 6        => 'Juni', 7     => 'Juli', 8      => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];
        $tahun = range(date('Y') - 2, date('Y') + 1);

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
        $ruangans = Ruangan::where('status', 'tersedia')->orderBy('nama_ruangan')->get();
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
            // Check konflik jadwal
            $konflik = Jadwal::where('ruang_id', $validated['ruang_id'])
                ->where('tanggal', $validated['tanggal'])
                ->where(function ($query) use ($validated) {
                    $query->whereBetween('waktu_mulai', [$validated['waktu_mulai'], $validated['waktu_selesai']])
                        ->orWhereBetween('waktu_selesai', [$validated['waktu_mulai'], $validated['waktu_selesai']])
                        ->orWhere(function ($q) use ($validated) {
                            $q->where('waktu_mulai', '<=', $validated['waktu_mulai'])
                                ->where('waktu_selesai', '>=', $validated['waktu_selesai']);
                        });
                })
                ->exists();

            if ($konflik) {
                toast('Jadwal bentrok! Ruangan sudah terpakai pada waktu tersebut.', 'error');
                return back()->withInput();
            }

            DB::beginTransaction();

            $jadwal = Jadwal::create($validated);

            DB::commit();

            Log::info('Jadwal berhasil dibuat', [
                'id'       => $jadwal->id,
                'ruangan'  => $jadwal->ruangan->nama_ruangan ?? 'N/A',
                'tanggal'  => $jadwal->tanggal,
                'kegiatan' => $jadwal->kegiatan,
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
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $jadwal                 = Jadwal::with('ruangan')->findOrFail($id);
        $jadwal->tanggal_format = Carbon::parse($jadwal->tanggal)->translatedFormat('d F Y');
        $jadwal->hari           = Carbon::parse($jadwal->tanggal)->translatedFormat('l');
        $jadwal->status_waktu   = $this->getStatusWaktu($jadwal);

        return view('backend.jadwal.show', compact('jadwal'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $jadwal   = Jadwal::findOrFail($id);
        $ruangans = Ruangan::orderBy('nama_ruangan')->get();

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
            $jadwal = Jadwal::findOrFail($id);

            // Check konflik jadwal (kecuali jadwal ini sendiri)
            $konflik = Jadwal::where('ruang_id', $validated['ruang_id'])
                ->where('tanggal', $validated['tanggal'])
                ->where('id', '!=', $id)
                ->where(function ($query) use ($validated) {
                    $query->whereBetween('waktu_mulai', [$validated['waktu_mulai'], $validated['waktu_selesai']])
                        ->orWhereBetween('waktu_selesai', [$validated['waktu_mulai'], $validated['waktu_selesai']])
                        ->orWhere(function ($q) use ($validated) {
                            $q->where('waktu_mulai', '<=', $validated['waktu_mulai'])
                                ->where('waktu_selesai', '>=', $validated['waktu_selesai']);
                        });
                })
                ->exists();

            if ($konflik) {
                toast('Jadwal bentrok! Ruangan sudah terpakai pada waktu tersebut.', 'error');
                return back()->withInput();
            }

            DB::beginTransaction();

            $jadwal->update($validated);

            DB::commit();

            Log::info('Jadwal berhasil diupdate', [
                'id'       => $jadwal->id,
                'ruangan'  => $jadwal->ruangan->nama_ruangan ?? 'N/A',
                'tanggal'  => $jadwal->tanggal,
                'kegiatan' => $jadwal->kegiatan,
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
            $jadwal = Jadwal::findOrFail($id);

            // Cek apakah jadwal sudah lewat (opsional: tidak bisa dihapus jika sudah lewat)
            // if (Carbon::parse($jadwal->tanggal)->isPast()) {
            //     toast('Jadwal yang sudah lewat tidak dapat dihapus!', 'error');
            //     return back();
            // }

            $jadwal->delete();

            Log::info('Jadwal berhasil dihapus', ['id' => $id]);

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

    /**
     * Get status waktu jadwal
     */
    private function getStatusWaktu($jadwal)
    {
        $now = Carbon::now();

        // Convert tanggal to string format if it's Carbon object
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
}
