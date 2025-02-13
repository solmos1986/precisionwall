<?php

namespace App\Http\Controllers\Api;

use App\Actividad;
use App\Http\Controllers\Controller;
use App\Personal;
use DB;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $data = Actividad::selectRaw("
        actividades.Actividad_ID,
        actividades.Fecha,
        proyectos.Pro_ID,
        proyectos.Codigo,
        proyectos.Nombre,
        CONCAT(proyectos.Calle, ', ', proyectos.Ciudad, ', ',  proyectos.Estado,' ', proyectos.Zip_Code) as dirrecion,
        tipo_actividad.Actividad_Nombre,
        empresas.Codigo as empresa,
        CONCAT(f.Nombre, ' ', f.Apellido_Paterno, ' ',  f.Apellido_Materno) as Foreman,
        proyectos.Foreman_ID as foreman_id,
        CONCAT(l.Nombre, ' ', l.Apellido_Paterno, ' ',  l.Apellido_Materno) as Lead,
        CONCAT(c_o.Nombre, ' ',  c_o.Apellido_Paterno, ' ',  c_o.Apellido_Materno) as Coordinador_Obra,
        CONCAT(c.Nick_Name) as Pwtsuper")
            ->where('Fecha', $request->from_date)
            ->join('tipo_actividad', 'actividades.Tipo_Actividad_ID', 'tipo_actividad.Tipo_Actividad_ID')
            ->join('proyectos', 'proyectos.Pro_ID', 'actividades.Pro_ID')
            ->join('empresas', 'empresas.Emp_ID', 'proyectos.Emp_ID')
            ->join('actividad_personal', 'actividad_personal.Actividad_ID', 'actividades.Actividad_ID')
            ->leftJoin('personal as f', 'f.Empleado_ID', 'proyectos.Foreman_ID')
            ->leftJoin('personal as l', 'l.Empleado_ID', 'proyectos.Lead_ID')
            ->leftJoin('personal as c', 'c.Empleado_ID', 'proyectos.Coordinador_ID')
            ->leftJoin('personal as c_o', 'c_o.Empleado_ID', 'proyectos.Coordinador_Obra_ID')
            ->distinct('actividades.Actividad_ID')
            ->get();
        ///filtrar personas
        return response()->json($data, 200);
    }
    private function filtrado($arrayObject, $newArrayObject)
    {
        foreach ($arrayObject as $value) {
            $personas = Personal::select('personal.Nick_Name')
                ->join('actividad_personal', 'actividad_personal.Empleado_ID', 'personal.Empleado_ID')
                ->where('actividad_personal.Actividad_ID', $data->Actividad_ID)
                ->get();
            //...$value->personas=$personas;
        }
    }
    public function actividadesAsistencia(Request $request)
    {
        $listActividades = DB::table('proyectos')
            ->select(
                'actividades.*',
                'proyectos.Pro_ID',
                'empresas.Codigo as empresa',
                'proyectos.Codigo',
                'proyectos.Nombre',
                'proyectos.Foreman_ID',
                'proyectos.Lead_ID',
                'em1.Celular as celular_foreman',
                DB::raw("CONCAT(proyectos.Numero, ', ', proyectos.Calle, ', ', proyectos.Ciudad, ', ',  proyectos.Estado,' ', proyectos.Zip_Code) as dirrecion"),
                DB::raw("CONCAT(em1.Nombre, ' ', em1.Apellido_Paterno, ' ',  em1.Apellido_Materno) as Foreman"),
                DB::raw("CONCAT(em2.Nombre, ' ',  em2.Apellido_Paterno, ' ',  em2.Apellido_Materno) as Cordinador"),
                DB::raw("CONCAT(em3.Nombre, ' ',  em3.Apellido_Paterno, ' ',  em3.Apellido_Materno) as Manager"),
                DB::raw("CONCAT(em4.Nombre, ' ',  em4.Apellido_Paterno, ' ',  em4.Apellido_Materno) as Project_Manager"),
                DB::raw("CONCAT(em5.Nombre, ' ',  em5.Apellido_Paterno, ' ',  em5.Apellido_Materno) as Coordinador_Obra"),
                DB::raw("CONCAT(em6.Nombre, ' ',  em6.Apellido_Paterno, ' ',  em6.Apellido_Materno) as asistente_proyecto"),
            )
            ->join('actividades', 'actividades.Pro_ID', 'proyectos.Pro_ID')
            ->join('empresas', 'empresas.Emp_ID', 'proyectos.Emp_ID')
            ->leftJoin('personal as em1', 'em1.Empleado_ID', 'proyectos.Foreman_ID')
            ->leftJoin('personal as em2', 'em2.Empleado_ID', 'proyectos.Coordinador_ID')
            ->leftJoin('personal as em3', 'em3.Empleado_ID', 'proyectos.Manager_ID')
            ->leftJoin('personal as em4', 'em4.Empleado_ID', 'proyectos.Project_Manager_ID')
            ->leftJoin('personal as em5', 'em5.Empleado_ID', 'proyectos.Coordinador_Obra_ID')
            ->leftJoin('personal as em6', 'em6.Empleado_ID', 'proyectos.Asistant_Proyect_ID')
            ->where(DB::raw('substring(proyectos.Codigo, 1, 3)'), '<', 900)
            ->where(function ($query) use ($request) {
                return $query->where('proyectos.Foreman_ID', $request->foreman_id)->orWhere('proyectos.Lead_ID', $request->foreman_id);
            })
            ->orderBy('proyectos.Pro_ID')
            ->get();
        foreach ($listActividades as $key => $actividades) {
            $actividades_personal = DB::table('actividad_personal')
                ->select(
                    'personal.Nick_Name'
                )
                ->join('personal', 'personal.Empleado_ID', 'actividad_personal.Empleado_ID')
                ->where('actividad_personal.Actividad_ID', $actividades->Actividad_ID)
                ->pluck('personal.Nick_Name')->toArray();
            //dd($actividades_personal);
            $personal = implode(",", $actividades_personal);
            $listActividades[$key]->personal = $personal;
        }

        //dd($actividad);
        /* foreach ($actividad as $key => $value) {
        $actividad[$key] = $value;
        $actividad[$key] = DB::table('proyectos')
        ->select(
        'proyectos.Pro_ID',
        'empresas.Codigo as empresa',
        'proyectos.Codigo',
        'proyectos.Nombre',
        'proyectos.Foreman_ID',
        'proyectos.Lead_ID',
        'em1.Celular as celular_foreman',
        DB::raw("CONCAT(proyectos.Numero, ', ', proyectos.Calle, ', ', proyectos.Ciudad, ', ',  proyectos.Estado,' ', proyectos.Zip_Code) as dirrecion"),
        DB::raw("CONCAT(em1.Nombre, ' ', em1.Apellido_Paterno, ' ',  em1.Apellido_Materno) as Foreman"),
        DB::raw("CONCAT(em2.Nombre, ' ',  em2.Apellido_Paterno, ' ',  em2.Apellido_Materno) as Cordinador"),
        DB::raw("CONCAT(em3.Nombre, ' ',  em3.Apellido_Paterno, ' ',  em3.Apellido_Materno) as Manager"),
        DB::raw("CONCAT(em4.Nombre, ' ',  em4.Apellido_Paterno, ' ',  em4.Apellido_Materno) as Project_Manager"),
        DB::raw("CONCAT(em5.Nombre, ' ',  em5.Apellido_Paterno, ' ',  em5.Apellido_Materno) as Coordinador_Obra"),
        DB::raw("CONCAT(em6.Nombre, ' ',  em6.Apellido_Paterno, ' ',  em6.Apellido_Materno) as asistente_proyecto"),
        )
        ->join('empresas', 'empresas.Emp_ID', 'proyectos.Emp_ID')
        ->leftJoin('personal as em1', 'em1.Empleado_ID', 'proyectos.Foreman_ID')
        ->leftJoin('personal as em2', 'em2.Empleado_ID', 'proyectos.Coordinador_ID')
        ->leftJoin('personal as em3', 'em3.Empleado_ID', 'proyectos.Manager_ID')
        ->leftJoin('personal as em4', 'em4.Empleado_ID', 'proyectos.Project_Manager_ID')
        ->leftJoin('personal as em5', 'em5.Empleado_ID', 'proyectos.Coordinador_Obra_ID')
        ->leftJoin('personal as em6', 'em6.Empleado_ID', 'proyectos.Asistant_Proyect_ID')
        ->where('proyectos.Pro_ID', $value->Pro_ID)
        ->get();
        } */

        /*   $proyectos = DB::table('proyectos')
        ->whereIn('actividades.Actividad_ID', $actividad_id)
        ->where('proyectos.Foreman_ID', $request->foreman_id)
        ->orWhere('proyectos.Lead_ID', $request->foreman_id)
        ->get(); */

        /* $actividades = Actividad::select(
        'actividades.*',
        'empresas.Codigo as empresa',
        'proyectos.Codigo',
        'proyectos.Nombre',
        'proyectos.Foreman_ID',
        'proyectos.Lead_ID',
        'em1.Celular as celular_foreman',
        DB::raw("CONCAT(proyectos.Numero, ', ', proyectos.Calle, ', ', proyectos.Ciudad, ', ',  proyectos.Estado,' ', proyectos.Zip_Code) as dirrecion"),
        DB::raw("CONCAT(em1.Nombre, ' ', em1.Apellido_Paterno, ' ',  em1.Apellido_Materno) as Foreman"),
        DB::raw("CONCAT(em2.Nombre, ' ',  em2.Apellido_Paterno, ' ',  em2.Apellido_Materno) as Cordinador"),
        DB::raw("CONCAT(em3.Nombre, ' ',  em3.Apellido_Paterno, ' ',  em3.Apellido_Materno) as Manager"),
        DB::raw("CONCAT(em4.Nombre, ' ',  em4.Apellido_Paterno, ' ',  em4.Apellido_Materno) as Project_Manager"),
        DB::raw("CONCAT(em5.Nombre, ' ',  em5.Apellido_Paterno, ' ',  em5.Apellido_Materno) as Coordinador_Obra"),
        DB::raw("CONCAT(em6.Nombre, ' ',  em6.Apellido_Paterno, ' ',  em6.Apellido_Materno) as asistente_proyecto"),
        'tipo_actividad.Actividad_Nombre'
        )
        ->join('tipo_actividad', 'tipo_actividad.Tipo_Actividad_ID', 'actividades.Tipo_Actividad_ID')
        ->join('proyectos', 'proyectos.Pro_ID', 'actividades.Pro_ID')
        ->join('empresas', 'empresas.Emp_ID', 'proyectos.Emp_ID')
        ->leftJoin('personal as em1', 'em1.Empleado_ID', 'proyectos.Foreman_ID')
        ->leftJoin('personal as em2', 'em2.Empleado_ID', 'proyectos.Coordinador_ID')
        ->leftJoin('personal as em3', 'em3.Empleado_ID', 'proyectos.Manager_ID')
        ->leftJoin('personal as em4', 'em4.Empleado_ID', 'proyectos.Project_Manager_ID')
        ->leftJoin('personal as em5', 'em5.Empleado_ID', 'proyectos.Coordinador_Obra_ID')
        ->leftJoin('personal as em6', 'em6.Empleado_ID', 'proyectos.Asistant_Proyect_ID')
        ->where('proyectos.Foreman_ID', $request->foreman_id)
        ->orWhere('proyectos.Lead_ID', $request->foreman_id)
        ->where('actividades.Fecha', $request->from_date)
        ->get(); */
        //se requere realizar array de personas q estan en la lista de trabajo
        /*  foreach ($actividades as $key => $actividad) {
        $actividades[$key]->actividades_personal = DB::table('actividad_personal')
        ->select(
        'personal.Nick_Name',
        'actividad_personal.*'
        )
        ->join('personal', 'personal.Empleado_ID', 'actividad_personal.Empleado_ID')
        ->where('actividad_personal.Actividad_ID', $actividad->Actividad_ID)
        ->get();
        }
         */
        /* $actividades = Actividad::selectRaw("
        actividades.Actividad_ID,
        actividades.Fecha,
        proyectos.Pro_ID,
        proyectos.Codigo,
        proyectos.Nombre,
        CONCAT(proyectos.Numero, ', ', proyectos.Calle, ', ', proyectos.Ciudad, ', ',  proyectos.Estado,' ', proyectos.Zip_Code) as dirrecion,
        tipo_actividad.Actividad_Nombre,
        empresas.Codigo as empresa,
        CONCAT(f.Nombre, ' ', f.Apellido_Paterno, ' ',  f.Apellido_Materno, ':',  f.Celular) as Foreman,
        proyectos.Foreman_ID as foreman_id,
        CONCAT(l.Nombre, ' ', l.Apellido_Paterno, ' ',  l.Apellido_Materno) as Lead,
        CONCAT(c_o.Nombre, ' ',  c_o.Apellido_Paterno, ' ',  c_o.Apellido_Materno) as Coordinador_Obra,
        CONCAT(c.Nick_Name) as Pwtsuper")
        ->where('actividades.Fecha', $request->from_date)
        ->where('proyectos.Foreman_ID', $request->foreman_id)
        ->join('tipo_actividad', 'actividades.Tipo_Actividad_ID', 'tipo_actividad.Tipo_Actividad_ID')
        ->join('proyectos', 'proyectos.Pro_ID', 'actividades.Pro_ID')
        ->join('empresas', 'empresas.Emp_ID', 'proyectos.Emp_ID')
        ->join('actividad_personal', 'actividad_personal.Actividad_ID', 'actividades.Actividad_ID')
        ->leftJoin('personal as f', 'f.Empleado_ID', 'proyectos.Foreman_ID')
        ->leftJoin('personal as l', 'l.Empleado_ID', 'proyectos.Lead_ID')
        ->leftJoin('personal as c', 'c.Empleado_ID', 'proyectos.Coordinador_ID')
        ->leftJoin('personal as c_o', 'c_o.Empleado_ID', 'proyectos.Coordinador_Obra_ID')
        ->distinct('actividades.Actividad_ID')
        ->orderBy('proyectos.Nombre', 'ASC')
        ->orderBy('proyectos.Pro_ID', 'ASC')
        ->orderBy('actividades.Tipo_Actividad_ID', 'ASC')
        ->orderBy('actividades.Fecha', 'ASC')
        ->orderBy('actividades.Hora', 'ASC')
        ->get(); */

        return response()->json($listActividades, 200);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
