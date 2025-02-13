<?php

use Illuminate\Database\Seeder;
use App\User;
use App\Persona;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {   
        $persona = Persona::create([
            'nombre' => 'Administrador',
            'tipo_documento' => 'ci',
            'num_documento' => '0000',
            'direccion' => 'tu direccion',
            'telefono' => '11111111',
            'email' => 'admin@admin.com',
        ]);

        $user = User::create([
            'username' =>'admin',
            'password' => bcrypt('123456'),
            'persona_id' => $persona->id,
        ]);
        
        // Asignación del rol
        $user->assignRole('Administrator');
    }
}
