<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('peminjaman_barangs', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 30)->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('nama_peminjam')->nullable();
            $table->string('instansi')->nullable();
            $table->date('tanggal_pinjam')->nullable(false);
            $table->date('tanggal_kembali')->nullable(false);
            $table->enum('status', ['menunggu', 'disetujui', 'ditolak', 'dikembalikan'])
                ->default('menunggu');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('peminjaman_barangs'); 
    }
};
