<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Seed the application's default users.
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@dauthau.gov.vn'],
            [
                'name'     => 'Admin',
                'password' => Hash::make('admin@123'),
                'role'     => User::ROLE_ADMIN,
            ]
        );

        User::firstOrCreate(
            ['email' => 'operator@dauthau.gov.vn'],
            [
                'name'     => 'System Operator',
                'password' => Hash::make('Operator@2024'),
                'role'     => User::ROLE_OPERATOR,
            ]
        );

        User::firstOrCreate(
            ['email' => 'viewer@dauthau.gov.vn'],
            [
                'name'     => 'Report Viewer',
                'password' => Hash::make('Viewer@2024'),
                'role'     => User::ROLE_VIEWER,
            ]
        );
    }
}
