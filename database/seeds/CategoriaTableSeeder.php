<?php

use Illuminate\Database\Seeder;
use App\Categoria;

class CategoriaTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Categoria::create(
            [
            'nombre' => 'Equipos',
            'descripcion' => '-'
            ]
        );
        Categoria::create(
            [
                'nombre' => 'Materiales',
                'descripcion' => '-'
            ]
        );
    }
}
