<?php

namespace App\Http\Controllers\InformacionProyecto;

use App\Http\Controllers\Controller;
use DataTables;
use DB;
use Illuminate\Http\Request;

class NotificacionAccionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
    public function show()
    {
        $this->actualizacion_automato();
        $notificacion = DB::table('notificacion_acciones_persona')
            ->select(
                'notificacion_acciones_persona.*',
                'proyecto_detail.*',
                'notificacion_estado.notificacion_estado_nombre',
                'notificacion_estado.color_estado'
            )
            ->join('notificacion_acciones', 'notificacion_acciones.notificacion_acciones_id', 'notificacion_acciones_persona.notificacion_acciones_id')
            ->join('proyecto_detail', 'proyecto_detail.id', 'notificacion_acciones.proyecto_detail_id')
            ->join('notificacion_estado', 'notificacion_estado.notificacion_acciones_estado_id', 'notificacion_acciones.notificacion_acciones_id')
            ->where('notificacion_acciones_persona.Empleado_ID', auth()->user()->Empleado_ID)
            ->where('notificacion_acciones_persona.notificacion_estado', 0)
            ->orderBy('proyecto_detail.fecha_proyecto_movimiento', 'DESC')
            ->get();
        return response()->json([
            'status' => 'ok',
            'message' => "mensajes",
            'data' => $notificacion,
        ]);
    }

    public function marcado(Request $request, $id)
    {
        $notificacion = DB::table('notificacion_acciones_persona')
            ->where('notificacion_acciones_persona_id', $id)
            ->where('Empleado_ID', auth()->user()->Empleado_ID)
            ->update([
                'notificacion_estado' => $request->completado,
                'fecha_registro' => $request->completado == 1 ? date('Y-m-d H:m:s') : null,
            ]);

        return response()->json([
            'status' => 'ok',
            'message' => "Registered Successfully",
            'data' => $notificacion,
        ]);
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $proyecto_detail = DB::table('proyecto_detail')
            ->where('proyecto_id', $id)
            ->orderBy('proyecto_detail.fecha_proyecto_movimiento', 'DESC')
            ->get();
        foreach ($proyecto_detail as $key => $detail) {
            //verificar si existe
            $this->verficar_notificacion($detail->id);
            $detail->notificacion = DB::table('notificacion_acciones')
                ->select(
                    'notificacion_acciones.*',
                    'notificacion_estado.notificacion_estado_nombre',
                    'notificacion_estado.color_estado'
                )
                ->join('notificacion_estado', 'notificacion_estado.notificacion_acciones_estado_id', 'notificacion_acciones.notificacion_estado_id')
                ->where('proyecto_detail_id', $detail->id)
                ->first();
            $detail->notificacion_personas = DB::table('notificacion_acciones_persona')
                ->select(
                    'notificacion_acciones_persona.*',
                    DB::raw("CONCAT(COALESCE(personal.Nombre,''),' ',COALESCE(personal.Apellido_Paterno,''),' ',COALESCE(personal.Apellido_Materno,'')) as Nombre"),
                )
                ->join('personal', 'personal.Empleado_ID', 'notificacion_acciones_persona.Empleado_ID')
                ->where('notificacion_acciones_persona.notificacion_acciones_id', $detail->notificacion->notificacion_acciones_id)
                ->where(function ($query) {
                    $query->where('notificacion_acciones_persona.notificacion_estado', 0)
                        ->orWhere('notificacion_acciones_persona.verificar_estado', 0);
                })
                ->get();
            $detail->notificacion_estado = DB::table('notificacion_estado')->get();
        }
        return response()->json([
            'status' => 'ok',
            'message' => "datos extraidos",
            'data' => $proyecto_detail,
        ]);
    }
    public function empleados(Request $request)
    {
        if (!isset($request->searchTerm)) {
            $personal = DB::table('personal')->select(
                'personal.Empleado_ID',
                'personal.Numero',
                DB::raw("CONCAT(COALESCE(personal.Nombre,''),' ',COALESCE(personal.Apellido_Paterno,''),' ',COALESCE(personal.Apellido_Materno,'')) as Nombre"),
                'personal.Nick_Name',
                'tipo_personal.nombre as nombre_tipo',
                'personal.Cargo',
                'personal.email',
                'empresas.Nombre as nombre_empresa'
            )
                ->join('empresas', 'empresas.Emp_ID', 'personal.Emp_ID')
                ->leftjoin('tipo_personal', 'tipo_personal.id', 'personal.tipo_personal_id')
                ->leftjoin('cargo_personal', 'cargo_personal.id', 'personal.cargo_personal_id')
                ->where('personal.status', '1')
                ->groupBy('personal.Empleado_ID')
                ->orderBy('personal.Nombre', 'ASC')
                ->get();
        } else {
            $personal = DB::table('personal')->select(
                'personal.Empleado_ID',
                'personal.Numero',
                DB::raw("CONCAT(COALESCE(personal.Nombre,''),' ',COALESCE(personal.Apellido_Paterno,''),' ',COALESCE(personal.Apellido_Materno,'')) as Nombre"),
                'personal.Nick_Name',
                'tipo_personal.nombre as nombre_tipo',
                'personal.Cargo',
                'personal.email',
                'empresas.Nombre as nombre_empresa'
            )
                ->join('empresas', 'empresas.Emp_ID', 'personal.Emp_ID')
                ->leftjoin('tipo_personal', 'tipo_personal.id', 'personal.tipo_personal_id')
                ->leftjoin('cargo_personal', 'cargo_personal.id', 'personal.cargo_personal_id')
                ->where('personal.Nick_Name', 'like', '%' . $request->searchTerm . '%')
                ->where('personal.status', '1')
                ->groupBy('personal.Empleado_ID')
                ->orderBy('personal.Nombre', 'ASC')
                ->get();
        }
        $data = [];
        foreach ($personal as $row) {
            $data[] = array(
                "id" => $row->Empleado_ID,
                "text" => $row->Nombre,
            );
        }
        return response()->json($data);
    }

    public function verficar_notificacion($proyecto_detail_id)
    {
        //crear notificaion si no exite
        $notificacion = DB::table('notificacion_acciones')
            ->select(
                'notificacion_acciones.*'
            )
            ->where('proyecto_detail_id', $proyecto_detail_id)
            ->first();
        //dd($notificacion);
        if (!$notificacion) {
            $insert = DB::table('notificacion_acciones')->insertGetId([
                "proyecto_detail_id" => $proyecto_detail_id,
                "notificacion_estado_id" => 1,
            ]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $modificando = DB::table('proyecto_detail')
            ->where('id', $request->proyecto_detail_id)
            ->update([
                'action_for_week' => $request->mensaje,
            ]);

        foreach ($request->personas as $key => $persona) {
            if ($persona['estado'] == 'nuevo') {
                $insert_notificacion_persona = DB::table('notificacion_acciones_persona')->insertGetId([
                    'notificacion_acciones_id' => $request->notificacion_acciones_id,
                    'Empleado_ID' => $persona['Empleado_ID'],
                    'notificacion_estado' => $persona['notificacion_estado'],
                    'verificar_estado' => $persona['verificar_estado'],
                ]);
                //dump('nuevo', $request->notificacion_acciones_id, $persona);
            } else {
                $update_notificacion_persona = DB::table('notificacion_acciones_persona')
                    ->where('notificacion_acciones_persona.notificacion_acciones_persona_id', $persona['notificacion_acciones_persona_id'])
                    ->update([
                        'Empleado_ID' => $persona['Empleado_ID'],
                        'notificacion_estado' => $persona['notificacion_estado'],
                        'verificar_estado' => $persona['verificar_estado'],
                        'fecha_registro' => $persona['notificacion_estado'] == 1 ? date('Y-m-d H:m:s') : null,
                    ]);
            }
        }
        return response()->json([
            'status' => 'ok',
            'message' => "Registered Successfully",
            'data' => null,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $detele = DB::table('notificacion_acciones_persona')
            ->where('notificacion_acciones_persona_id', $id)
            ->delete();

        return response()->json([
            'status' => 'ok',
            'message' => "Employee removed correctly",
            'data' => null,
        ]);
    }

    public function historial($id)
    {
        $historial = DB::table('notificacion_acciones')
            ->select(
                'proyecto_detail.action_for_week',
                'proyecto_detail.fecha_proyecto_movimiento',
                'notificacion_acciones_persona.*',
                'notificacion_estado.notificacion_estado_nombre',
                'notificacion_estado.color_estado',
                'personal.Empleado_ID',
                DB::raw("CONCAT(COALESCE(personal.Nombre,''),' ',COALESCE(personal.Apellido_Paterno,''),' ',COALESCE(personal.Apellido_Materno,'')) as Nombre")
            )
            ->join('proyecto_detail', 'proyecto_detail.id', 'notificacion_acciones.proyecto_detail_id')
            ->join('notificacion_estado', 'notificacion_estado.notificacion_acciones_estado_id', 'notificacion_acciones.notificacion_estado_id')
            ->join('notificacion_acciones_persona', 'notificacion_acciones_persona.notificacion_acciones_id', 'notificacion_acciones.notificacion_acciones_id')
            ->join('personal', 'personal.Empleado_ID', 'notificacion_acciones_persona.Empleado_ID')
            ->where('proyecto_detail.proyecto_id', $id)
            ->orderBy('proyecto_detail.fecha_proyecto_movimiento', 'ASC')
            ->get();

        return Datatables::of($historial)
            ->addIndexColumn()
            ->make(true);
    }
    public function cambio_estado(Request $request, $id)
    {
        $cambio_estado = DB::table('notificacion_acciones')
            ->where('notificacion_acciones_id', $id)
            ->update([
                'notificacion_estado_id' => $request->estado_id,
            ]);
        return response()->json([
            'status' => 'ok',
            'message' => "Modified correctly",
            'data' => null,
        ]);
    }
    public function actualizacion_automato()
    {
        $fecha_actual = date('Y-m-d H:m:s');
        $fecha_actual_reduccion = date("Y-m-d H:m:s", strtotime($fecha_actual . "- 7 days"));

        $action_week = DB::table('proyecto_detail')
            ->select('notificacion_acciones.notificacion_acciones_id')
            ->join('notificacion_acciones', 'notificacion_acciones.proyecto_detail_id', 'proyecto_detail.id')
            ->join('notificacion_acciones_persona', 'notificacion_acciones_persona.notificacion_acciones_id', 'notificacion_acciones.notificacion_acciones_id')
            ->where('proyecto_detail.fecha_proyecto_movimiento', '<=', date("Y-m-d H:m:s", strtotime($fecha_actual_reduccion)))
            ->where('notificacion_acciones_persona.notificacion_estado', 0)
            ->where('notificacion_acciones.notificacion_estado_id', 2)
            ->pluck('notificacion_acciones.notificacion_acciones_id');
        foreach ($action_week as $key => $valor) {
            $modificando = DB::table('notificacion_acciones')
                ->whereIn('notificacion_acciones_id', $action_week)
                ->update([
                    'notificacion_estado_id' => 4
                ]);
        }
    }
}
