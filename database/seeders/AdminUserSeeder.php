<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $password = config('clinic.admin_password');

        if (! is_string($password) || $password === '') {
            throw new RuntimeException('Define ADMIN_PASSWORD antes de sembrar el usuario administrador.');
        }

        User::updateOrCreate(
            ['email' => config('clinic.admin_email')],
            [
                'name' => 'Patricia Cuesta',
                'password' => Hash::make($password),
            ],
        );
    }
}
