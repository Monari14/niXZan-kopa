<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PrecosSeeder extends Seeder
{
    public function run()
    {
        DB::table('precos')->insert([
            [
                'id_produto' => 1,
                'valor' => 9.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_produto' => 2,
                'valor' => 9.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_produto' => 3,
                'valor' => 10.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_produto' => 4,
                'valor' => 15.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_produto' => 5,
                'valor' => 8.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_produto' => 6,
                'valor' => 10.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_produto' => 7,
                'valor' => 8.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_produto' => 8,
                'valor' => 9.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_produto' => 9,
                'valor' => 5.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_produto' => 10,
                'valor' => 5.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
