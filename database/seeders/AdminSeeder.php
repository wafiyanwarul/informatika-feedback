<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buat role admin jika belum ada
        $adminRole = Role::firstOrCreate(
            ['nama_role' => 'admin'],
            ['created_at' => now(), 'updated_at' => now()]
        );

        // Buat user admin
        User::updateOrCreate(
            ['email' => 'wafiyanwarulhikam12@gmail.com'],
            [
                'name' => 'admin',
                'password' => Hash::make(value: env('ADMIN_PASSWORD', 'default_secure_password')),
                'role_id' => $adminRole->id,
            ]
        );
    }
}
