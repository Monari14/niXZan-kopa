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
        // Primeiro usuário será admin
        User::create([
            'name' => 'Administrador',
            'email' => 'admin@admin.com',
            'username' => 'admin',
            'telefone' => '(54)99999-9999',
            'role' => 'admin',
            'password' => Hash::make('admin123'),
        ]);
    }
}
