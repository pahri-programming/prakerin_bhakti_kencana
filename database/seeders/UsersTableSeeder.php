<?php
namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name'     => 'AdminBKU',
                'password' => Hash::make('rahasia'),
                'isAdmin'  => 1,
            ]
        );

        User::firstOrCreate(
            ['email' => 'member@gmail.com'],
            [
                'name'     => 'Member',
                'password' => Hash::make('member123'),
                'isAdmin'  => 0,
            ]
        );

        
    }
}
