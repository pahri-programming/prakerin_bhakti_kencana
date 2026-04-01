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
        Schema::create('denda_pengembalians', function (Blueprint $table) {
            $table->id();

            // Relasi
            $table->foreignId('pengembalian_barang_id')
                ->constrained('pengembalian_barangs')
                ->onDelete('cascade');

            $table->foreignId('verifikasi_pengembalian_id')
                ->constrained('verifikasi_pengembalian')
                ->onDelete('cascade');

            // Data Denda
            $table->decimal('jumlah_denda', 15, 2)->default(0);
            $table->enum('tipe_perhitungan', ['manual', 'otomatis'])->default('otomatis');
            $table->text('rincian_perhitungan')->nullable(); // JSON: detail per barang

            // Pembayaran
            $table->enum('status_pembayaran', ['belum_bayar', 'sudah_bayar', 'dibebaskan'])
                ->default('belum_bayar');
            $table->timestamp('tanggal_bayar')->nullable();
            $table->string('bukti_pembayaran')->nullable(); // Path foto bukti
            $table->text('keterangan_pembayaran')->nullable();

            // Tindakan Admin
            $table->text('keterangan_denda')->nullable();
            $table->text('tindakan_admin')->nullable();
            $table->timestamp('tanggal_tindakan')->nullable();

            // User tracking
            $table->foreignId('admin_id')->constrained('users');                         // Admin yang input denda
            $table->foreignId('verifikator_bayar_id')->nullable()->constrained('users'); // Admin yang verifikasi pembayaran

            $table->timestamps();
            $table->softDeletes(); // Soft delete untuk history

            // Indexes
            $table->index('status_pembayaran');
            $table->index('tanggal_tindakan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('denda_pengembalians');
    }
};
