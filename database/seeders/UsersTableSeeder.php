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

        // User biasa
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
