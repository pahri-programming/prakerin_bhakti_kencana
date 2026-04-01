<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\BarangRuangan;

class BarangApiController extends Controller
{
    /**
     * GET /api/barang
     * Daftar semua barang beserta ruangan & stok yang tersedia
     * Dipakai Flutter untuk tampilkan daftar barang yang bisa dipinjam
     */
    public function index()
    {
        $barangs = Barang::with([
            'kategori',
            'barangruangan' => function ($q) {
                // Hanya tampilkan barang yang tersedia dan ada stok
                $q->where('status', 'tersedia')
                    ->where('qty', '>', 0)
                    ->with('ruangan');
            },
        ])
            ->whereHas('barangruangan', function ($q) {
                $q->where('status', 'tersedia')->where('qty', '>', 0);
            })
            ->get();

        $data = $barangs->map(function ($barang) {
            return [
                'id'           => $barang->id,
                'nama'         => $barang->nama,
                'kategori'     => $barang->kategori->nama ?? '-',
                'harga'        => $barang->harga,
                'harga_format' => $barang->harga_format,
                'keterangan'   => $barang->keterangan,
                // Daftar ruangan tempat barang ini tersedia
                'tersedia_di'  => $barang->barangruangan->map(function ($br) {
                    return [
                        'barang_ruangan_id' => $br->id,
                        'ruangan_id'        => $br->ruangan_id,
                        'nama_ruangan'      => $br->ruangan->nama_ruangan ?? '-',
                        'qty'               => $br->qty,
                        'status'            => $br->status,
                    ];
                }),
            ];
        });

        return response()->json([
            'success' => true,
            'data'    => $data,
        ], 200);
    }

    /**
     * GET /api/barang/{id}
     * Detail 1 barang beserta semua ruangan & stoknya
     */
    public function show($id)
    {
        $barang = Barang::with([
            'kategori',
            'barangruangan.ruangan',
        ])->find($id);

        if (! $barang) {
            return response()->json([
                'success' => false,
                'message' => 'Barang tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => [
                'id'           => $barang->id,
                'nama'         => $barang->nama,
                'kategori'     => $barang->kategori->nama ?? '-',
                'harga'        => $barang->harga,
                'harga_format' => $barang->harga_format,
                'keterangan'   => $barang->keterangan,
                'tersedia_di'  => $barang->barangruangan->map(function ($br) {
                    return [
                        'barang_ruangan_id' => $br->id,
                        'ruangan_id'        => $br->ruangan_id,
                        'nama_ruangan'      => $br->ruangan->nama_ruangan ?? '-',
                        'qty'               => $br->qty,
                        'status'            => $br->status,
                    ];
                }),
            ],
        ], 200);
    }
}
