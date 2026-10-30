<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class ContactSeeder extends Seeder
{
    public function run(): void
    {
        $usuario = User::where('email', 'ana@example.com')->firstOrFail();

        $contactos = [
            [
                'name' => 'Andrés Martínez',
                'phone_number' => '3001234567',
                'address' => 'Bogotá',
                'email' => 'andres.contacto@example.com',
                'avatar' => null,
            ],
            [
                'name' => 'Laura Rodríguez',
                'phone_number' => '3107654321',
                'address' => 'Medellín',
                'email' => 'laura.contacto@example.com',
                'avatar' => null,
            ],
            [
                'name' => 'Juan Torres',
                'phone_number' => '3204567890',
                'address' => 'Cali',
                'email' => 'juan.contacto@example.com',
                'avatar' => null,
            ],
        ];

        foreach ($contactos as $datos) {
            $usuario->contacts()->updateOrCreate(
                ['email' => $datos['email']],
                $datos
            );
        }
    }
}
