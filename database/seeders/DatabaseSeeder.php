<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Abdukadir Direy',
            'email' => 'direy@email.com',
            'password' => bcrypt('password'),
            'phone' => '252618',
            'type' => 'super_admin',
        ]);
    }
}
