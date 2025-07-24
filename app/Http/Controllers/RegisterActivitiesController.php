<?php
namespace App\Http\Controllers;

use App\Modules\UserModules\UserService;
use App\Personal;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RegisterActivitiesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->userService = new UserService;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('panel.register_actividad.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function dataTable()
    {
        return Datatables::of($evaluacion)
            ->addIndexColumn()
            ->addColumn('acciones', function ($evaluacion) {
                $button = "<a href='" . route('list.personal.evaluar', ['id' => $evaluacion->evaluacion_id]) . "'><i class='fas fa-eye ms-text-primary'></i></a>";

                return $button;
            })
            ->addColumn('ver_usuarios', function ($evaluacion) {
                $html = "<span class='badge badge-success'> $evaluacion->personal_evaluar assigned users </span>";
                return $html;
            })
            ->addColumn('status', function ($evaluacion) {
                if (date('Y-m-d', strtotime($evaluacion->fecha_asignacion)) == date('Y-m-d')) {
                    $html = "<span class='badge badge-success'> pending </span>";
                } else {
                    $html = "<span class='badge badge-success'> time out </span>";
                }
                return $html;
            })
            ->rawColumns(['ver_usuarios', 'acciones', 'status'])
            ->make(true);
    }

    public function empleado(Request $request)
    {
        if (! isset($request->searchTerm)) {
            $personal = Personal::select(
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
            $personal = Personal::select(
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
            $data[] = [
                "id"   => $row->Empleado_ID,
                "text" => $row->Nick_Name,
            ];
        }
        return response()->json($data);
    }

    public function proyectos()
    {
        if (! isset($request->searchTerm)) {
            $proyectos = $proyectos = DB::table('proyectos')
                ->select('proyectos.*')
                ->where('proyectos.Pro_ID', $id)
                ->get();
        } else {
            $proyectos = DB::table('proyectos')
                ->select('proyectos.*')
                ->where('proyectos.Nombre', 'like', '%' . $request->searchTerm . '%')
                ->where('proyectos.Pro_ID', $id)
                ->get();
        }
        $data = [];
        foreach ($proyectos as $row) {
            $data[] = [
                "id"   => $row->Pro_ID,
                "text" => $row->Nombre,
            ];
        }
        return response()->json($data);
    }

    public function edificio(Request $request, $id)
    {
        if (! isset($request->searchTerm)) {
            $edificios = DB::table('edificios')
                ->select('edificios.*')
                ->where('edificios.Pro_ID', $id)
                ->get();
        } else {
            $edificios = DB::table('edificios')
                ->select('edificios.*')
                ->where('edificios.Nombre', 'like', '%' . $request->searchTerm . '%')
                ->where('edificios.Pro_ID', $id)
                ->get();
        }
        $data = [];
        foreach ($edificios as $row) {
            $data[] = [
                "id"   => $row->Edificio_ID,
                "text" => $row->Nombre,
            ];
        }
        return response()->json($data);
    }

    public function piso(Request $request, $id)
    {
        if (! isset($request->searchTerm)) {
            $pisos = DB::table('floor')
                ->select('floor.*')
                ->where('floor.Edificio_ID', $id)
                ->get();
        } else {
            $pisos = DB::table('floor')
                ->select('floor.*')
                ->where('floor.Nombre', 'like', '%' . $request->searchTerm . '%')
                ->where('floor.Edificio_ID', $id)
                ->get();
        }
        $data = [];
        foreach ($pisos as $row) {
            $data[] = [
                "id"   => $row->Floor_ID,
                "text" => $row->Nombre,
            ];
        }
        return response()->json($data);
    }

    public function area(Request $request, $id)
    {
        if (! isset($request->searchTerm)) {
            $edificios = DB::table('area_control')
                ->select('area_control.*')
                ->where('area_control.Floor_ID', $id)
                ->get();
        } else {
            $edificios = DB::table('area_control')
                ->select('area_control.*')
                ->where('area_control.Nombre', 'like', '%' . $request->searchTerm . '%')
                ->where('area_control.Floor_ID', $id)
                ->get();
        }
        $data = [];
        foreach ($edificios as $row) {
            $data[] = [
                "id"   => $row->Area_ID,
                "text" => $row->Nombre,
            ];
        }
        return response()->json($data);
    }

    public function tarea(Request $request, $id)
    {
        if (! isset($request->searchTerm)) {
            $edificios = DB::table('task')
                ->select(
                    'task.*',
                    DB::raw("CONCAT(task.Tas_IDT,' ',task.Nombre) as Nombre"),
                )
                ->where('task.Area_ID', $id)
                ->get();
        } else {
            $edificios = DB::table('task')
                ->select(
                    'task.*',
                    DB::raw("CONCAT(task.Tas_IDT,' ',task.Nombre) as Nombre"),
                )
                ->select('task.*')
                ->where('task.Nombre', 'like', '%' . $request->searchTerm . '%')
                ->where('task.Area_ID', $id)
                ->get();
        }
        $data = [];
        foreach ($edificios as $row) {
            $data[] = [
                "id"   => $row->Task_ID,
                "text" => $row->Nombre,
            ];
        }
        return response()->json($data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function create_proceso_actividad_personal($actividad, $Actividad_ID)
    {
        Log::info("create_proceso_actividad_personal " . json_encode($actividad, JSON_PRETTY_PRINT) . " " . $Actividad_ID);
        $insert_actividad_personal = DB::table('actividad_personal')
            ->insertGetId([
                'Actividad_ID' => $Actividad_ID,
                'Empleado_ID'  => $actividad['Empleado_ID'],
                'HContract'    => 0,
                'HTM'          => 0,
                'Note'         => ' ',
            ]);
        //dump('actividad personal creada', $insert_actividad_personal);
        $insert_registro_dario = DB::table('registro_diario')->insertGetId([
            'Actividad_Id'       => $Actividad_ID,
            'Pro_ID'             => $actividad['Pro_ID'],
            'Empleado_ID'        => $actividad['Empleado_ID'],
            'Hora_Ingreso'       => $actividad['Hora_Ingreso'],
            'Fecha_Hingreso'     => date('Y-m-d H:i:s', strtotime($actividad['Fecha'])),
            'Hora_Salida'        => $actividad['Hora_Salida'],
            'Fecha_Hsalida'      => date('Y-m-d H:i:s', strtotime($actividad['Fecha'])),
            'Fecha'              => date('Y-m-d H:i:s', strtotime($actividad['Fecha'])),
            'Latitud_Ingreso'    => null,
            'Longitud_Ingreso'   => null,
            'Latitud_Salida'     => null,
            'Longitud_Salida'    => null,
            'Foto_Ingreso'       => null,
            'Foto_Salida'        => null,
            'Clave_Digitada_In'  => null,
            'Clave_Digitada_Out' => null,
            'Pregunta_IN'        => null,
            'Pregunta_OUT'       => null,
            'Aux1'               => null,
            'Aux2'               => null,
            'Direc_Estado'       => null,
            'Cargo'              => null,
            'Usuario_Pass'       => null,
        ]);
        //dump('insert_registro_dario ', $insert_registro_dario);
        $insert_registro_dario_actividad = DB::table('registro_diario_actividad')->insertGetId([
            'Reg_ID'             => $insert_registro_dario,
            'Task_ID'            => $actividad['Task_ID'],
            'Horas_Contract'     => $actividad['Hora'],
            'Horas_TM'           => 0,
            'Verificado_Foreman' => $actividad['Verificado_Foreman'],
            'Detalle_Foreman'    => '',
            'Verificado_Oficina' => 0,
            'Detalle_Oficina'    => '',
            'Detalles'           => $actividad['Detalles'],
            'Actividad_ID'       => $Actividad_ID,
            'Empleado_ID'        => $actividad['Empleado_ID'],
        ]);
        $this->actualizacion_actividad_personal($Actividad_ID);
    }

    public function store_visit_report(Request $request)
    {
        Log::info('store_visit_report user ' . json_encode(Auth::user(), JSON_PRETTY_PRINT));
        Log::info('store_visit_report ' . json_encode($request->all(), JSON_PRETTY_PRINT));
        if ($request->actividades) {
            foreach ($request->actividades as $key => $actividad) {
                //dump('estado',$actividad['estado'] );
                $verificar_actividad = DB::table('registro_diario')
                    ->join('registro_diario_actividad', 'registro_diario_actividad.Reg_ID', 'registro_diario.Reg_ID')
                    ->join('actividades', 'actividades.Actividad_ID', 'registro_diario.Actividad_ID')
                    ->join('task', 'task.Task_ID', 'registro_diario_actividad.Task_ID')
                    ->where('actividades.Pro_ID', $actividad['Pro_ID'])
                    ->where('task.Nombre', 'like', '%super%')
                    ->first();
                Log::info('store_visit_report verificar_actividad ' . json_encode($verificar_actividad, JSON_PRETTY_PRINT));
                if ($actividad['estado'] == 'nuevo') {

                    if (! $verificar_actividad) {
                        //crea todo para una actividad nueva
                        //dump('crea todo para una actividad nueva');
                        $insert_actividad = DB::table('actividades')
                            ->insertGetId([
                                'Pro_ID'            => $actividad['Pro_ID'],
                                'Tipo_Actividad_ID' => 1,
                                'Descripcion'       => 'by system',
                                'Fecha'             => date('Y-m-d H:i:s', strtotime($actividad['Fecha'])),
                                'Hora'              => '06:00:00', //defauld
                                'Aux1'              => '',
                                'Aux2'              => '',
                                'Aux3'              => '',
                                'Estatus'           => null,
                                'Color'             => '#FFFFFF',
                                'Aux4'              => null,
                            ]);
                        $this->create_proceso_actividad_personal($actividad, $insert_actividad);
                    } else {
                        //dump('verifica si esta esa persona en la actividad');
                        //verifica si esta esa persona en la actividad
                        $actividad_personal = DB::table('actividad_personal')
                            ->select('actividades.*')
                            ->join('actividades', 'actividades.Actividad_ID', 'actividad_personal.Actividad_ID')
                            ->where('actividades.Fecha', date('Y-m-d H:i:s', strtotime($actividad['Fecha'])))
                            ->where('actividades.Pro_ID', $actividad['Pro_ID'])
                            ->where('actividad_personal.Empleado_ID', $actividad['Empleado_ID'])
                            ->first();

                        if (! $actividad_personal) {
                            //dump('añade esa persona a la actividad');
                            //añade esa persona a la actividad
                            $this->create_proceso_actividad_personal($actividad, $verificar_actividad->Actividad_ID);
                        } else {
                            $obtener_registro_diario = DB::table('registro_diario')
                                ->join('actividades', 'actividades.Actividad_ID', 'registro_diario.Actividad_ID')
                                ->where('actividades.Actividad_ID', $actividad_personal->Actividad_ID)
                                ->where('registro_diario.Empleado_ID', $actividad['Empleado_ID'])
                                ->first();

                            $insert_registro_dario_actividad = DB::table('registro_diario_actividad')->insertGetId([
                                'Reg_ID'             => $obtener_registro_diario->Reg_ID,
                                'Task_ID'            => $actividad['Task_ID'],
                                'Horas_Contract'     => $actividad['Hora'],
                                'Horas_TM'           => 0,
                                'Verificado_Foreman' => $actividad['Verificado_Foreman'],
                                'Detalle_Foreman'    => '',
                                'Verificado_Oficina' => 0,
                                'Detalle_Oficina'    => '',
                                'Detalles'           => $actividad['Detalles'],
                                'Actividad_ID'       => $verificar_actividad->Actividad_ID,
                                'Empleado_ID'        => $actividad['Empleado_ID'],
                            ]);
                            $this->actualizacion_actividad_personal($verificar_actividad->Actividad_ID);
                        }
                    }
                } else {
                    //modificar  registro diario
                    $update_registro_dario = DB::table('registro_diario')
                        ->where('registro_diario.Reg_ID', $actividad['Reg_ID'])
                        ->update([
                            //'Actividad_ID' => $actividad['Actividad_ID'],
                            'Pro_ID'      => $actividad['Pro_ID'],
                            'Empleado_ID' => $actividad['Empleado_ID'],
                        ]);
                    $registro_diario_actividad = DB::table('registro_diario_actividad')
                        ->where('registro_diario_actividad.RDA_ID', $actividad['RDA_ID'])
                        ->update([
                            'Reg_ID'         => $actividad['Reg_ID'],
                            'Task_ID'        => $actividad['Task_ID'],
                            'Horas_Contract' => $actividad['Hora'],
                        ]);
                    $this->actualizacion_actividad_personal($verificar_actividad->Actividad_ID);
                }
            }
            return response()->json([
                'status'  => 'ok',
                'message' => 'Registered Successfully',
            ]);
        } else {
            return response()->json([
                'status'  => 'ok',
                'message' => 'Unrecorded hours',
            ]);
        }
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $actividades = DB::table('registro_diario')
            ->select(
                DB::raw("'update' as 'estado'"),
                'actividades.Actividad_ID',
                'actividades.Fecha',
                'personal.Empleado_ID',
                'personal.Nick_Name',
                'actividades.Hora',
                'registro_diario.Hora_Ingreso',
                'registro_diario.Hora_Salida',
                'registro_diario_actividad.Horas_Contract',
                'registro_diario_actividad.Horas_TM',
                'registro_diario_actividad.Detalles',
                'proyectos.Pro_ID',
                'proyectos.Nombre as nombre_proyecto',
                'edificios.Nombre as nombre_edificio',
                'edificios.Edificio_ID',
                'floor.Nombre as nombre_floor',
                'floor.Floor_ID',
                'area_control.Nombre as nombre_area',
                'area_control.Area_ID',
                'task.Task_ID',
                DB::raw("CONCAT(task.Tas_IDT,' ',task.Nombre) as nombre_tarea"),
                'registro_diario_actividad.Verificado_Foreman',
                'registro_diario_actividad.RDA_ID',
                'registro_diario.Reg_ID'
            )
            ->join('registro_diario_actividad', 'registro_diario.Reg_ID', 'registro_diario_actividad.Reg_ID')
            ->leftJoin('task', 'registro_diario_actividad.Task_ID', 'task.Task_ID')
            ->leftJoin('area_control', 'area_control.Area_ID', 'task.Area_ID')
            ->leftJoin('floor', 'floor.Floor_ID', 'area_control.Floor_ID')
            ->leftJoin('edificios', 'edificios.Edificio_ID', 'floor.Edificio_ID')
            ->leftJoin('personal', 'personal.Empleado_ID', 'registro_diario.Empleado_ID')
            ->leftJoin('actividades', 'registro_diario.Actividad_ID', 'actividades.Actividad_ID')
            ->leftJoin('proyectos', 'proyectos.Pro_ID', 'actividades.Pro_ID')
            ->where(DB::raw('substring(proyectos.Codigo, 1, 3)'), '<', 900)
        //filtro rango de fecha
            ->when(request()->from_date, function ($query) {
                //dd(date('Y-m-d H:i:s', strtotime(request()->from_date)), date('Y-m-d H:i:s', strtotime(request()->to_date)));
                return $query->whereBetween('actividades.Fecha', [date('Y-m-d H:i:s', strtotime(request()->from_date)), date('Y-m-d H:i:s', strtotime(request()->to_date))]);
            })
        //filtro nickname
            ->when(request()->nick_name, function ($query) {
                //dd(date('Y-m-d H:i:s', strtotime(request()->from_date)), date('Y-m-d H:i:s', strtotime(request()->to_date)));
                return $query->where('personal.Nick_Name', 'like', '%' . request()->nick_name . '%');
            })
        //filtro proyecto
            ->when(request()->job, function ($query) {
                return $query->where(function ($q) {
                    $q->where('proyectos.Nombre', 'like', '%' . request()->job . '%')
                        ->orWhere('proyectos.Codigo', 'like', '%' . request()->job . '%');
                });
            })
        //filtro no show
            ->when(request()->no_cost_code == 2, function ($query) {
                //dd('filtro aplicado');
                return $query->whereRaw("(registro_diario_actividad.Task_ID=0 OR registro_diario_actividad.Task_ID is null OR (registro_diario_actividad.Horas_Contract=0 and task.Tas_IDT<>'VACNOSHOW' ))");
            })
        //filtro no show
            ->when(request()->horas_trabajo, function ($query) {
                return $query->where('registro_diario_actividad.Horas_Contract', request()->horas_trabajo);
            })
        //filtro no tarea
            ->when(request()->cost_code, function ($query) {
                return $query->where('task.Tas_IDT', request()->cost_code);
            })
        //filtro verificar si es admin
            ->when(! auth()->user()->verificarRol([1]), function ($query) {
                return $query->where('registro_diario.Empleado_ID', auth()->user()->Empleado_ID);
            })
        //->groupBy('registro_diario.Fecha', 'registro_diario.Pro_ID', 'registro_diario.Actividad_ID', 'personal.Nick_Name')
            ->get();

        return response()->json([
            'status'  => 'ok',
            'message' => 'lista de actividades',
            'data'    => $actividades,
        ], 200);
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
    public function auto_complementar_proyecto($id)
    {
        $data = DB::table('edificios')
            ->select(
                'edificios.Edificio_ID',
                'edificios.Nombre as nombre_edificio',
                'floor.Floor_ID',
                'floor.Nombre as nombre_floor',
            )
            ->join('floor', 'floor.Edificio_ID', 'edificios.Edificio_ID')
            ->where('floor.Pro_ID', $id)
            ->first();
        return response()->json([
            'status'  => 'ok',
            'message' => 'verificar si existe edificio y floor',
            'data'    => $data,
        ], 200);
    }
    public function delete_registro_diario($id)
    {
        Log::info("delete_registro_diario user " . json_encode(Auth::user(), JSON_PRETTY_PRINT));
        Log::info("delete_registro_diario " . json_encode($id, JSON_PRETTY_PRINT));
        $registro_diario_actividad        = DB::table('registro_diario_actividad')->where('RDA_ID', $id)->first();
        $delete_registro_diario_actividad = DB::table('registro_diario_actividad')
            ->where('registro_diario_actividad.RDA_ID', $id)
            ->delete();
        //dump($registro_diario_actividad);
        $this->actualizacion_actividad_personal($registro_diario_actividad->Actividad_ID);
        return response()->json([
            'status'  => 'ok',
            'message' => 'Correctly removed',
            'data'    => null,
        ], 200);
    }

    public function actualizacion_actividad_personal($Actividad_ID)
    {
        Log::info('RegisterActivitiesController/actualizacion_actividad_personal ' . strval(json_encode($Actividad_ID, JSON_PRETTY_PRINT)));
        $Empleado_ID    = null;
        $Horas_Contract = 0;
        $Horas_TM       = 0;
        $Nota           = "";

        $obtener_registros = DB::table('registro_diario_actividad')
            ->select(
                DB::raw('SUM(registro_diario_actividad.Horas_Contract) AS Horas_Contract'),
                DB::raw('SUM(registro_diario_actividad.Horas_TM) AS Horas_TM'),
                'registro_diario.Actividad_ID',
                'registro_diario.Empleado_ID'
            )->join('registro_diario', 'registro_diario.Reg_ID', 'registro_diario_actividad.Reg_ID')
            ->where('registro_diario_actividad.Actividad_ID', $Actividad_ID)
            ->groupBy('registro_diario.Empleado_ID')
            ->get();
        //dump($obtener_registros, $Actividad_ID);
        $delete = DB::table('actividad_personal')
            ->where('Actividad_ID', $Actividad_ID)
            ->delete();
        Log::info("RegisterActivitiesController/actualizacion_actividad_personal Actividad_ID => $Actividad_ID delete => " . json_encode($delete, JSON_PRETTY_PRINT));
        Log::info('RegisterActivitiesController/actualizacion_actividad_personal obtener_registros ' . json_encode($obtener_registros, JSON_PRETTY_PRINT));
        foreach ($obtener_registros as $key => $obtener_registro) {
            $data = [
                'Actividad_ID' => $obtener_registro->Actividad_ID,
                'Empleado_ID'  => $obtener_registro->Empleado_ID,
                'HTM'          => $obtener_registro->Horas_TM,
                'HContract'    => $obtener_registro->Horas_Contract,
                'Note'         => $obtener_registro->Horas_Contract == 0 ? "No show up to the field/" : "STP-TimeCard: vs",
            ];
            $insert = DB::table('actividad_personal')
                ->insertGetId($data);
            Log::info("RegisterActivitiesController/actualizacion_actividad_personal insert => $insert => data " . json_encode($data, JSON_PRETTY_PRINT));
        }
    }
}
