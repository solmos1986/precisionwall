<?php

use Illuminate\Database\Seeder;
use App\Impuesto;

class ImpuestoTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
            Impuesto::create(
                [
                    'nombre' => 'IVA+IT',
                    'valor' => 19.00
                ]);
    }
}
