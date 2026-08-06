<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => config('clinic.admin_email')],
            [
                'name' => 'Patricia Cuesta',
                'password' => Hash::make(config('clinic.admin_password')),
            ],
        );
    }
}
