<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE denda_pengembalians
        MODIFY COLUMN status_pembayaran
        ENUM('belum_bayar', 'menunggu_verifikasi', 'sudah_bayar', 'dibebaskan')
        DEFAULT 'belum_bayar'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE denda_pengembalians
        MODIFY COLUMN status_pembayaran
        ENUM('belum_bayar', 'sudah_bayar', 'dibebaskan')
        DEFAULT 'belum_bayar'");
    }
};
