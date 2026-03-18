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
        
        Schema::table('verifikasi_peminjaman', function (Blueprint $table) {
            $table->text('foto_bukti')->nullable()->change();
        });

      
        Schema::table('verifikasi_booking', function (Blueprint $table) {
            $table->text('foto_bukti')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('verifikasi_peminjaman', function (Blueprint $table) {
            $table->string('foto_bukti')->nullable()->change();
        });

        Schema::table('verifikasi_booking', function (Blueprint $table) {
            $table->string('foto_bukti')->nullable()->change();
        });
    }
};
