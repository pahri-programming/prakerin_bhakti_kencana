<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\User::create([
            'name' => 'Pahri',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('rahasia'),
            'isAdmin' => 1,
        ]);
       
        \App\Models\User::create([
            'name' => 'Member',
            'email' => 'member@gmail.com',
            'password' => bcrypt('member123'),
            'isAdmin' => 0,
        ]);

    }
}
