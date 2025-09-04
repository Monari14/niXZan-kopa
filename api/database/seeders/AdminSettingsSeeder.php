<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AdminSettings;

class AdminSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Primeiro usuário será admin
        AdminSettings::create([
            'valor_adicional_pedido' => 30,
            'status_aberto' => true,
        ]);

    }
}
