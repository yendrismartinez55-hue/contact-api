<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $usuarios = [
            [
                'name' => 'Ana Pérez',
                'email' => 'ana@example.com',
            ],
            [
                'name' => 'Carlos Gómez',
                'email' => 'carlos@example.com',
            ],
            [
                'name' => 'María López',
                'email' => 'maria@example.com',
            ],
        ];

        foreach ($usuarios as $datos) {
            User::updateOrCreate(
                ['email' => $datos['email']],
                [
                    'name' => $datos['name'],
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                ]
            );
        }
    }
}
