<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('denda_bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings')->onDelete('cascade');
            $table->foreignId('verifikasi_booking_id')->constrained('verifikasi_booking')->onDelete('cascade');
            $table->decimal('jumlah_denda', 15, 2)->default(0);
            $table->text('keterangan_denda')->nullable();
            $table->text('tindakan_admin')->nullable();
            $table->enum('status_pembayaran', ['belum_bayar', 'menunggu_verifikasi', 'sudah_bayar', 'dibebaskan'])
                ->default('belum_bayar');
            $table->string('bukti_pembayaran')->nullable();
            $table->timestamp('tanggal_bayar')->nullable();
            $table->text('keterangan_pembayaran')->nullable();
            $table->timestamp('tanggal_tindakan')->nullable();
            $table->foreignId('admin_id')->constrained('users');
            $table->foreignId('verifikator_bayar_id')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();

            $table->index('status_pembayaran');
            $table->index('booking_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('denda_bookings');
    }
};
