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
        // Table untuk verifikasi pengembalian barang oleh PIC
        Schema::create('verifikasi_peminjaman', function (Blueprint $table) {
            $table->id();
            $table->foreignId('peminjaman_id')->constrained('peminjaman_barangs')->onDelete('cascade');
            $table->foreignId('pic_id')->constrained('users')->comment('PIC yang melakukan pengecekan');
            $table->enum('kondisi', ['baik', 'rusak_ringan', 'rusak_berat', 'hilang'])->default('baik');
            $table->text('catatan_pic')->nullable()->comment('Catatan dari PIC tentang kondisi barang');
            $table->string('foto_bukti')->nullable()->comment('Foto kondisi barang saat pengecekan');
            $table->enum('status_verifikasi', ['pending', 'diterima', 'perlu_tindakan'])->default('pending');
            $table->text('tindakan_admin')->nullable()->comment('Tindakan yang diambil admin setelah laporan PIC');
            $table->timestamp('tanggal_verifikasi')->useCurrent();
            $table->boolean('is_reported_to_admin')->default(true)->comment('Otomatis kirim laporan ke admin');
            $table->timestamps();

            $table->index('peminjaman_id');
            $table->index('pic_id');
            $table->index('kondisi');
        });

        // Table untuk verifikasi pengembalian ruangan oleh PIC
        Schema::create('verifikasi_booking', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings')->onDelete('cascade');
            $table->foreignId('pic_id')->constrained('users')->comment('PIC yang melakukan pengecekan');
            $table->enum('kondisi_ruangan', ['baik', 'kotor', 'rusak'])->default('baik');
            $table->text('catatan_pic')->nullable()->comment('Catatan dari PIC tentang kondisi ruangan');
            $table->string('foto_bukti')->nullable()->comment('Foto kondisi ruangan saat pengecekan');
            $table->enum('status_verifikasi', ['pending', 'diterima', 'perlu_tindakan'])->default('pending');
            $table->text('tindakan_admin')->nullable()->comment('Tindakan yang diambil admin');
            $table->timestamp('tanggal_verifikasi')->useCurrent();
            $table->boolean('is_reported_to_admin')->default(true);
            $table->timestamps();

            $table->index('booking_id');
            $table->index('pic_id');
            $table->index('kondisi_ruangan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('verifikasi_booking');
        Schema::dropIfExists('verifikasi_peminjaman');
    }
};
