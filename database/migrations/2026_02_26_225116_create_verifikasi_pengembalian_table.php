<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('verifikasi_pengembalian', function (Blueprint $table) {
            $table->id();

            // Relasi ke pengembalian barang
            $table->foreignId('pengembalian_barang_id')
                ->constrained('pengembalian_barangs')
                ->onDelete('cascade')
                ->comment('ID pengembalian yang diverifikasi');

            // PIC yang melakukan verifikasi
            $table->foreignId('pic_id')
                ->constrained('users')
                ->onDelete('cascade')
                ->comment('PIC yang melakukan verifikasi kondisi detail');

            // Kondisi umum (untuk backward compatibility dengan VerifikasiPeminjaman)
            $table->enum('kondisi', ['baik', 'rusak_ringan', 'rusak_berat', 'hilang'])
                ->default('baik')
                ->comment('Kondisi overall: baik/rusak_ringan/rusak_berat/hilang');

            // Catatan PIC
            $table->text('catatan_pic')
                ->nullable()
                ->comment('Catatan detail dari PIC tentang kondisi barang');

            // Foto bukti (bisa lebih dari 1, disimpan sebagai JSON array path)
            $table->json('foto_bukti')
                ->nullable()
                ->comment('Array path foto bukti kondisi barang (max 6)');

            // Status verifikasi oleh admin
            $table->enum('status_verifikasi', ['pending', 'diterima', 'perlu_tindakan'])
                ->default('pending')
                ->comment('Status: pending=menunggu admin, diterima=ok, perlu_tindakan=perlu tindak lanjut');

            // Tindakan yang diambil admin
            $table->text('tindakan_admin')
                ->nullable()
                ->comment('Keputusan/tindakan yang diambil admin setelah review');

            // Tanggal verifikasi
            $table->timestamp('tanggal_verifikasi')
                ->useCurrent()
                ->comment('Kapan PIC melakukan verifikasi');

            // Flag apakah sudah dilaporkan ke admin
            $table->boolean('is_reported_to_admin')
                ->default(true)
                ->comment('Apakah sudah masuk ke laporan admin');

            $table->timestamps();

            // Indexes untuk performa query
            $table->index('pengembalian_barang_id');
            $table->index('pic_id');
            $table->index('kondisi');
            $table->index('status_verifikasi');
            $table->index('tanggal_verifikasi');

            // Unique: 1 verifikasi per pengembalian
            $table->unique('pengembalian_barang_id');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('verifikasi_pengembalian');
    }
};
