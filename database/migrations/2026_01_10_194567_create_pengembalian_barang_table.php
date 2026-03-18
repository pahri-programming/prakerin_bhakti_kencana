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
        Schema::create('pengembalian_barangs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('peminjaman_barang_id')->constrained('peminjaman_barangs')->onDelete('cascade');
            $table->foreignId('barang_ruangan_id')->constrained('barang_ruangans')->onDelete('cascade');
            $table->date('tanggal_kembali')->nullable(false);
            $table->enum('status', ['menunggu_pic', 'dikembalikan', 'perlu_tindakan'])
                ->default('menunggu_pic');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengembalian_barangs');
    }
};
