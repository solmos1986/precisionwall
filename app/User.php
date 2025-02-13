<?php

namespace App;

use DB;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $table = 'personal';
    protected $primaryKey = 'Empleado_ID';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'Emp_ID', 'Nombre', 'Apellido_Paterno', 'Apellido_Materno', 'Nick_Name', 'Estado', 'Ciudad', 'Zip_Code',
        'Calle', 'Numero', 'Cargo', 'Numero_Seguro_Social', 'Fecha_Nacimiento', 'Numero_Licencia_Conducir', 'Numero_Permiso_Trabajo',
        'Fecha_Expiracion_Trabajo', 'Numero_Residente', 'email', 'Telefono', 'Usuario', 'Indice_produccion', 'Nro_Bono', 'Spec_Bon1',
        'Not_Bon', 'Extra_Mon1', 'Benefit1', 'Benefit2', 'empresa', 'Rol_ID',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'Password', 'remember_token',
    ];
    public function checkRol($roles)
    {
        $ban = 0;
        try {
            $role = DB::table('roles_app')->whereIn('id', json_decode(auth()->user()->Rol_ID))->get();
            foreach ($role as $val) {
                if (is_array($roles)) {
                    if (in_array($val->nombre, $roles)) {
                        $ban = 1;
                    }
                } else {
                    if ($val->nombre == $roles) {
                        $ban = 1;
                    }
                }
            }
        } catch (\Throwable $th) {
            return $ban;
        }
        return $ban;
    }
    public function checkEvaluaciones($cargo)
    {
        $personal = DB::table('personal')->select('personal.Empleado_ID')
            ->join('evaluaciones', 'evaluaciones.foreman_id', 'personal.Empleado_ID')
            ->where('evaluaciones.foreman_id', json_decode(auth()->user()->Empleado_ID))
            ->first();
        if ($personal) {
            return true;
        } else {
            return false;
        }
    }
    public function checkViewHumanResource()
    {
        $personal = DB::table('personal')->select('personal.Empleado_ID')
            ->join('personal_eventos', 'personal_eventos.Empleado_ID', 'personal.Empleado_ID')
            ->where('personal.Empleado_ID', json_decode(auth()->user()->Empleado_ID))
            ->first();
        if ($personal) {
            return true;
        } else {
            return false;
        }
    }
    public function checkForeman()
    {
        $foreman = DB::table('proyectos')->select('personal.Empleado_ID')
            ->join('personal', 'personal.Empleado_ID', 'proyectos.Foreman_ID')
            ->orWhere('proyectos.Foreman_ID', json_decode(auth()->user()->Empleado_ID))
            ->orWhere('proyectos.Asistant_Proyect_ID', json_decode(auth()->user()->Empleado_ID))
            ->orWhere('proyectos.Project_Manager_ID', json_decode(auth()->user()->Empleado_ID))
            ->orWhere('proyectos.Lead_ID ', json_decode(auth()->user()->Empleado_ID))
            ->where('proyectos.Estatus_ID', '!=', 5)
            ->first();
        if ($foreman) {
            return true;
        } else {
            return false;
        }
    }
    public function obtenerAccesoModulo()
    {
        $modulos_permitidos = DB::table('rol_personal')
            ->select(
                'modulo.class_icon',
                'rol_modulo.rol_modulo_id',
                'modulo.modulo_id',
                'modulo.nombre_modulo',
                'modulo.url'
            )
            ->join('rol_modulo', 'rol_modulo.roles_app_id', 'rol_personal.roles_app_id')
            ->join('modulo', 'modulo.modulo_id', 'rol_modulo.modulo_id')
            ->where('rol_personal.Empleado_ID', json_decode(auth()->user()->Empleado_ID))
            ->groupBy('rol_modulo.modulo_id')
            ->orderBy('modulo.modulo_id','ASC')
            ->get();
        foreach ($modulos_permitidos as $key => $modulo) {
            $modulo->sub_modulos = DB::table('rol_sub_modulo')
                ->select('sub_modulo.*')
                ->join('sub_modulo', 'sub_modulo.sub_modulo_id', 'rol_sub_modulo.sub_modulo_id')
                ->where('rol_sub_modulo.rol_modulo_id', $modulo->rol_modulo_id)
                ->get();
        }
        return $modulos_permitidos;
    }
    public function obtenerAccesoSubModulo()
    {
        $sub_modulos_permitidos = DB::table('rol_personal')
            ->select('rol_sub_modulo.sub_modulo_id')
            ->join('rol_modulo', 'rol_modulo.roles_app_id', 'rol_personal.roles_app_id')
            ->join('rol_sub_modulo', 'rol_sub_modulo.rol_modulo_id', 'rol_modulo.rol_modulo_id')
            ->where('rol_personal.Empleado_ID', json_decode(auth()->user()->Empleado_ID))
            ->groupBy('rol_sub_modulo.sub_modulo_id')
            ->pluck('rol_sub_modulo.sub_modulo_id')
            ->toArray();

        return $sub_modulos_permitidos;
    }
    public function verificarRol($roles_id)
    {
        $verificarRol = DB::table('rol_personal')
            ->where('rol_personal.Empleado_ID', json_decode(auth()->user()->Empleado_ID))
            ->whereIn('rol_personal.roles_app_id', $roles_id)
            ->first();
        if ($verificarRol) {
            return true;
        } else {
            return false;
        }
    }
}
