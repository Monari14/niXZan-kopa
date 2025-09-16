<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Administrador',
            'email' => 'admin@admin.com',
            'username' => 'admin',
            'telefone' => '(54)99999-9999',
            'role' => 'admin',
            'password' => Hash::make('admin123'),
        ]);

        //User::create([
        //    'name' => 'Felipe Eduardo Monari',
        //    'email' => 'felipeemonari@gmail.com',
        //    'username' => 'monari',
        //    'telefone' => '54996472916',
        //    'role' => 'cliente',
        //    'password' => Hash::make('felipe'),
        //]);
    }
}
