<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProdutosSeeder extends Seeder
{
    public function run()
    {
        DB::table('produtos')->insert([
            [
                'nome' => 'Baly Abacaxi com Hortelã',
                'tipo' => 'energetico',
                'preco_base' => 9.00,
                'estoque' => 50,
                'img_url' => 'p/baly-abacaxi-hortelã.png',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nome' => 'Baly Melancia',
                'tipo' => 'energetico',
                'preco_base' => 9.00,
                'estoque' => 50,
                'img_url' => 'p/baly-melancia.png',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nome' => 'Ballena',
                'tipo' => 'bebida',
                'preco_base' => 10.00,
                'estoque' => 15,
                'img_url' => 'p/ballena.png',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nome' => 'Licor 43',
                'tipo' => 'bebida',
                'preco_base' => 15.00,
                'estoque' => 14,
                'img_url' => 'p/licor43.png',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nome' => 'Absolut Vodka',
                'tipo' => 'bebida',
                'preco_base' => 8.00,
                'estoque' => 20,
                'img_url' => 'p/absolut.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nome' => 'J. Walker Black Label',
                'tipo' => 'bebida',
                'preco_base' => 10.00,
                'estoque' => 20,
                'img_url' => 'p/jw-blacklabel.png',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nome' => 'Jack Daniels',
                'tipo' => 'bebida',
                'preco_base' => 8.00,
                'estoque' => 20,
                'img_url' => 'p/jack.png',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nome' => 'J. Walker Red Label',
                'tipo' => 'bebida',
                'preco_base' => 9.00,
                'estoque' => 15,
                'img_url' => 'p/jw-redlabel.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nome' => 'Gelo Maracujá',
                'tipo' => 'gelo',
                'preco_base' => 5.00,
                'estoque' => 100,
                'img_url' => 'p/gelo.png',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nome' => 'Copo 770ml',
                'tipo' => 'copao',
                'preco_base' => 5.00,
                'estoque' => 100,
                'img_url' => 'p/copo-770ml.png',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
