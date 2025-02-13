<?php

use Illuminate\Database\Seeder;
use App\Configuracion;

class ConfigTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Configuracion::create([
            'nombre' =>'WSC SA',
            'direccion' =>'Urb. La Madre C/Guapilo #77 Santa Cruz, Bolivia',
            'actividad' =>'Seguridad Integral',
            'tipo_documento' =>'nit',
            'num_documento' =>'000000',
            'email' =>'info@wsc-sa.com',
            'web' =>'http://wsc-sa.com/',
            'iue' =>'25',
            'fv' =>'10',
            'iva' =>'13',
            'it' =>'3',
            'importacion' => '40',
            'telefono' =>'33535757',
            'moneda' =>'BOB',
        ]);
    }
}
