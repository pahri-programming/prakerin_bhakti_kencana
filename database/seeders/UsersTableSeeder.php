<?php
namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        User::firstOrCreate(
            ['email' => 'admin@bku.ac.id'],
            [
                'name'     => 'Admin BKU',
                'password' => Hash::make('bhaktikencana202612'),
                'isAdmin'  => 1, // Admin
                'role'     => 'admin',
                'instansi' => 'Universitas Bhakti Kencana',
            ]
        );

        // PIC (Person In Charge) - Petugas Pengecekan
        User::firstOrCreate(
            ['email' => 'pic@bku.ac.id'],
            [
                'name'     => 'PIC Pengecekan BKU',
                'password' => Hash::make('picbku2026'),
                'isAdmin'  => 0,
                'role'     => 'pic',
                'instansi' => 'Petugas Pengecekan - Universitas Bhakti Kencana',
            ]
        );

        // User biasaz
        User::firstOrCreate(
            ['email' => 'user@bku.ac.id'],
            [
                'name'     => 'User Demo',
                'password' => Hash::make('password123'),
                'isAdmin'  => 0, // User biasa
                'role'     => 'user',
                'instansi' => 'Karyawan Universitas Bhakti Kencana',
            ]
        );

    }
}
