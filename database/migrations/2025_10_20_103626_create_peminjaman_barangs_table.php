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
        Schema::create('peminjaman_barangs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('barang_id')->constrained('barangs')->onDelete('cascade');
            $table->integer('jumlah')->default(1);
            $table->date('tanggal')->nullable(false);
            $table->time('waktu_mulai')->nullable(false);
            $table->time('waktu_selesai')->nullable(false);
            $table->enum('status', ['menunggu', 'disetujui', 'ditolak', 'dipinjam', 'dikembalikan', 'selesai'])
                ->default('menunggu');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('peminjamanbarangs');
    }
};
