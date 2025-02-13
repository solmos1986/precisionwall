<?php

namespace App\Http\Controllers;

use App\Proyecto;
use DataTables;
use DB;
use Illuminate\Http\Request;
use Image;
use PDF;
use Validator;
use \stdClass;

class InformacionProjectController extends Controller
{
    private $diasferiados = [
        '2017-01-01', '2017-12-25', '2017-02-13', '2017-04-13', '2017-05-26', '2017-11-02',
        '2018-01-01', '2018-12-25', '2018-02-13', '2018-04-13', '2018-05-26', '2018-11-02',
        '2019-01-01', '2019-12-25', '2019-02-13', '2019-04-13', '2019-05-26', '2019-11-02',
        '2020-01-01', '2020-12-25', '2020-02-13', '2020-04-13', '2020-05-26', '2020-11-02',
        '2021-01-01', '2021-12-25', '2021-02-13', '2021-04-13', '2021-05-26', '2021-11-02',
        '2022-01-01', '2022-12-25', '2022-02-13', '2022-04-13', '2022-05-26', '2022-11-02',
        '2023-01-01', '2023-12-25', '2023-02-13', '2023-04-13', '2023-05-26', '2023-11-02',
        '2024-01-01', '2024-12-25', '2024-02-13', '2024-04-13', '2024-05-26', '2024-11-02',
        '2025-01-01', '2025-12-25', '2025-02-13', '2025-04-13', '2025-05-26', '2025-11-02',
        '2026-01-01', '2026-12-25', '2026-02-13', '2026-04-13', '2026-05-26', '2026-11-02',
        '2027-01-01', '2027-12-25', '2027-02-13', '2027-04-13', '2027-05-26', '2027-11-02',
        '2028-01-01', '2028-12-25', '2028-02-13', '2028-04-13', '2028-05-26', '2028-11-02',
        '2029-01-01', '2029-12-25', '2029-02-13', '2029-04-13', '2029-05-26', '2029-11-02',
        '2030-01-01', '2030-12-25', '2030-02-13', '2030-04-13', '2030-05-26', '2030-11-02',
    ];
    //"1-1", "13-2", "13-4", "26-5", "02-11", "25-12"
    public function __construct()
    {
        $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $status_proyecto = DB::table('estatus')->select('estatus.*')->get();
        $status_info = DB::table('proyecto_info_status')
            ->get();
        $tipo_proyecto = DB::table('tipo_proyecto')->get();
        $proyectos = DB::table('proyectos')
            ->select('proyectos.*')
            /* ->where('proyectos.Estatus_ID',1) */
            ->get();
        foreach ($proyectos as $key => $proyecto) {
            $logitud = strlen($proyecto->Nombre);
            if ($logitud > 30) {
                $proyecto->Nombre = substr($proyecto->Nombre, 0, 35) . '...';
            }
        }
        return view('panel.informacion_proyecto.index', compact('status_proyecto', 'proyectos', 'status_info', 'tipo_proyecto'));
    }
    public function datatable_proyectos(Request $request)
    {
        $data = Proyecto::select(
            'proyectos.Pro_ID',
            'proyectos.Codigo',
            'proyectos.color',
            'proyectos.Nombre',
            DB::raw('DATE_FORMAT(proyectos.Fecha_Inicio , "%m/%d/%Y") as Fecha_Inicio'),
            DB::raw('DATE_FORMAT(proyectos.Fecha_Fin , "%m/%d/%Y") as Fecha_Fin'),
            'proyectos.Horas',
            DB::raw('CONCAT(proyectos.Calle, " ", proyectos.Ciudad, " ",  proyectos.Estado, " ",  proyectos.Zip_Code) as direccion'),
            'empresas.Nombre as empresa',
            'estatus.Estatus_ID as Estatus_ID',
            'estatus.Estatus_ID',
            'tipo_proyecto.Nombre_Tipo as tipo',
            DB::raw("CONCAT(em1.Nombre, ' ',  em1.Apellido_Paterno, ' ',  em1.Apellido_Materno) as Foreman"),
            DB::raw("CONCAT(em2.Nombre, ' ',  em2.Apellido_Paterno, ' ',  em2.Apellido_Materno) as Cordinador"),
            DB::raw("CONCAT(em3.Nombre, ' ',  em3.Apellido_Paterno, ' ',  em3.Apellido_Materno) as Manager"),
            DB::raw("CONCAT(em4.Nombre, ' ',  em4.Apellido_Paterno, ' ',  em4.Apellido_Materno) as Project_Manager"),
            DB::raw("CONCAT(em5.Nombre, ' ',  em5.Apellido_Paterno, ' ',  em5.Apellido_Materno) as Coordinador_Obra"),
            DB::raw("CONCAT(em6.Nombre, ' ',  em6.Apellido_Paterno, ' ',  em6.Apellido_Materno) as asistente_proyecto"),
            DB::raw("CONCAT(em7.Nombre, ' ',  em7.Apellido_Paterno, ' ',  em7.Apellido_Materno) as lead")
        )
            ->when((request()->from_date && request()->to_date), function ($query) {
                return $query->join('actividades', 'actividades.Pro_ID', 'proyectos.Pro_ID')
                    ->addSelect('actividades.Actividad_ID')
                    ->whereBetween('actividades.Fecha', [date('Y-m-d', strtotime(request()->from_date)), date('Y-m-d', strtotime(request()->to_date))])
                    ->groupBy('proyectos.Pro_ID');
            })
            ->when(request()->status, function ($query) {

                $status = explode(',', request()->status);
                return $query->whereIn('proyectos.Estatus_ID', $status)
                    ->orderBy('proyectos.Estatus_ID');
            })
            ->when(request()->gc, function ($query) {
                return $query->where('empresas.Nombre', request()->gc);
            })
            ->when($request->query('proyectos'), function ($query) use ($request) {
                $proyectos = explode(',', $request->query('proyectos'));
                return $query->whereIn('proyectos.Pro_ID', $proyectos);
            })
            ->when(($request->query('filtro') != 'null'), function ($query) use ($request) {

                switch ($request->query('cargo')) {
                    case 'pm':
                        return $query->where('proyectos.Manager_ID', $request->query('filtro'));
                        break;
                    case 'super':
                        return $query->where('proyectos.Coordinador_ID', $request->query('filtro'));
                        break;
                    case 'foreman':
                        return $query->where('proyectos.Foreman_ID', $request->query('filtro'));
                        break;
                    case 'APM':
                        return $query->where('proyectos.Asistant_Proyect_ID', $request->query('filtro'));
                        break;
                    default:
                        # code...
                        break;
                }
            })
            ->join('empresas', 'proyectos.Emp_ID', 'empresas.Emp_ID')
            ->leftjoin('estatus', 'proyectos.Estatus_ID', 'estatus.Estatus_ID')
            ->leftjoin('tipo_proyecto', 'proyectos.Tipo_ID', 'tipo_proyecto.Tipo_ID')
            ->leftJoin('personal as em1', 'em1.Empleado_ID', 'proyectos.Foreman_ID')
            ->leftJoin('personal as em2', 'em2.Empleado_ID', 'proyectos.Coordinador_ID')
            ->leftJoin('personal as em3', 'em3.Empleado_ID', 'proyectos.Manager_ID')
            ->leftJoin('personal as em4', 'em4.Empleado_ID', 'proyectos.Project_Manager_ID')
            ->leftJoin('personal as em5', 'em5.Empleado_ID', 'proyectos.Coordinador_Obra_ID')
            ->leftJoin('personal as em6', 'em6.Empleado_ID', 'proyectos.Asistant_Proyect_ID')
            ->leftJoin('personal as em7', 'em7.Empleado_ID', 'proyectos.Lead_ID')
            ->get();
        return Datatables::of($data)
            ->addIndexColumn()
            ->addColumn('campo', function ($data) {
                $campo = "
                        <select class='change_reg'>
                            <option value=''>Select an option</option>
                            <option value='Add' data-id='$data->Pro_ID' data-start='$data->Fecha_Inicio' data-end='$data->Fecha_Fin'>Add</option>
                            <option value='No SDate' data-id='$data->Pro_ID' data-start='$data->Fecha_Inicio' data-end='$data->Fecha_Fin'>No SDate</option>
                            <option value='No EDate' data-id='$data->Pro_ID' data-start='$data->Fecha_Inicio' data-end='$data->Fecha_Fin'>No EDate</option>
                            <option value='Ini' data-id='$data->Pro_ID' data-start='$data->Fecha_Inicio' data-end='$data->Fecha_Fin'>Ini</option>
                        </select>
                        ";
                return $campo;
            })
            ->addColumn('check_pdf', function ($data) {
                $button = "
                    <label class='ms-checkbox-wrap ms-checkbox-info'>
                        <input type='checkbox' value='$data->Pro_ID' class='proyectos' style='opacity: 1;' data-proyecto='$data->Pro_ID'>
                        <i class='ms-checkbox-check'></i>
                    </label>";
                return $button;
            })
            ->addColumn('actions', function ($data) {
                if ($data->Actividad_ID != null) {
                    //logica si existe actividad
                    $verificar = DB::table('report_daily_detalle')
                        ->where('report_daily_detalle.actividad_id', $data->Actividad_ID)
                        ->join('actividades', 'actividades.Actividad_ID', 'report_daily_detalle.actividad_id')
                        ->whereBetween('actividades.Fecha', [date('Y-m-d', strtotime(request()->from_date)), date('Y-m-d', strtotime(request()->to_date))])
                        ->where('report_daily_detalle.estado', 'pending')
                        ->get();
                    if (count($verificar) > 0) {
                        $libro = "<a href='" . route('daily_report_detail.index', ['id' => $data->Pro_ID]) . "' ><i class='fa fa-book ms-text-danger cursor-pointer mr-1' title='View Activities (daily report pending)'></i></a>";
                    } else {
                        $libro = "<a href='" . route('daily_report_detail.index', ['id' => $data->Pro_ID]) . "' ><i class='fa fa-book ms-text-primary cursor-pointer mr-1' title='View Activities'></i></a>";
                    }
                } else {
                    $libro = "<a href='" . route('daily_report_detail.index', ['id' => $data->Pro_ID]) . "' ><i class='fa fa-book ms-text-primary cursor-pointer mr-1' title='View Activities'></i></a>";
                }

                $button = "
                <i class='fas fa-pencil-alt ms-text-warning view_proyecto cursor-pointer mr-1' data-id='$data->Pro_ID' title='Edit Project'></i>"
                    . $libro .
                    "<a href='" . route('config_daily_report_detail.edit', ['id' => $data->Pro_ID]) . "' ><i class='fa fa-cog ms-text-primary cursor-pointer mr-1' title='Configure Report Daily'></i></a>
                    <a class='view_historial_notificacion' href='#' data-id='$data->Pro_ID' ><i class='fa fa-comment ms-text-primary cursor-pointer mr-1' title='View action for the week history'></i></a>
                    ";
                return $button;
            })

            ->rawColumns(['campo', 'check_pdf', 'actions'])
            ->make(true);
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
    public function show(Request $request, $id)
    {
        $movimiento = $this->obtener_movimiento($request->proyecto_movimiento_id);
        return $movimiento;
    }
    private function obtener_info($proyecto_id)
    {
        $proyecto = DB::table('proyecto_info')
            ->where('proyecto_info.proyecto_id', $proyecto_id)
            ->orderBy('proyecto_info.fecha_proyecto_movimiento', 'DESC')
            ->first();
        return $proyecto;
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        $data = new stdClass();
        $data->proyecto = $this->primer_movimiento($id);
        $data->info = $this->obtener_info($id);
        $data->actions = $this->action($id);
        $data->status = $this->status();

        return response()->json($data, 200);
    }
    private function status()
    {
        $status = DB::table('proyecto_info_status')
            ->get();
        return $status;
    }
    private function action($proyecto_id)
    {
        $action = DB::table('proyecto_detail')
            ->where('proyecto_detail.proyecto_id', $proyecto_id)
            ->orderBy('proyecto_detail.fecha_proyecto_movimiento', 'DESC')
            ->limit(3)
            ->get();
        return $action;
    }
    private function obtener_movimiento($id)
    {
        $movimiento = DB::table('proyecto_movimiento')
            ->select(
                'proyecto_movimiento.*',
                'estatus.Nombre_Estatus as nombre_status',
                'proyectos.*',
                DB::raw('DATE_FORMAT(proyecto_movimiento.Fecha_Fin , "%m/%d/%Y") as Fecha_Fin'),
                DB::raw('DATE_FORMAT(proyecto_movimiento.Fecha_Inicio , "%m/%d/%Y") as Fecha_Inicio')
            )
            ->join('proyectos', 'proyectos.Pro_ID', 'proyecto_movimiento.proyecto_id')
            ->join('estatus', 'proyectos.Estatus_ID', 'estatus.Estatus_ID')
            ->where('proyecto_movimiento.id', $id)
            ->first();
        return $movimiento;
    }
    /* informacio del proyecto */
    private function primer_movimiento($id)
    {
        $movimiento = DB::table('proyectos')
            ->select(
                'empresas.Nombre as nombre_empresa',
                'proyectos.*',
                DB::raw('DATE_FORMAT(proyectos.Fecha_Fin , "%m/%d/%Y") as Fecha_Fin'),
                DB::raw('DATE_FORMAT(proyectos.Fecha_Inicio , "%m/%d/%Y") as Fecha_Inicio'),
                'estatus.Nombre_Estatus as nombre_status',
                'tipo_proyecto.Nombre_Tipo as nombre_tipo',
                DB::raw("CONCAT(COALESCE(em1.Nombre,''),' ',COALESCE(em1.Apellido_Paterno,''),' ',COALESCE(em1.Apellido_Materno,'')) as Foreman"),
                'em1.Celular as Foreman_celular',
                'em1.email as Foreman_email',
                DB::raw("CONCAT(COALESCE(em2.Nombre,''),' ',COALESCE(em2.Apellido_Paterno,''),' ',COALESCE(em2.Apellido_Materno,'')) as field_superintendent"),
                'em2.Celular as field_superintendent_celular',
                'em2.email as field_superintendent_email',
                DB::raw("CONCAT(COALESCE(em3.Nombre,''),' ',COALESCE(em3.Apellido_Paterno,''),' ',COALESCE(em3.Apellido_Materno,'')) as Manager"),
                'em3.Celular as Manager_celular',
                'em3.email as Manager_email',
                DB::raw("CONCAT(COALESCE(em4.Nombre,''), ' ',COALESCE(em4.Apellido_Paterno,''), ' ',COALESCE(em4.Apellido_Materno,'')) as Project_Manager"),
                'em4.Celular as Project_Manager_celular',
                'em4.email as Project_Manager_email',
                DB::raw("CONCAT(COALESCE(em5.Nombre,''), ' ',COALESCE(em5.Apellido_Paterno,''), ' ',COALESCE(em5.Apellido_Materno,'')) as Coordinador_Obra"),
                'em5.Celular as Coordinador_Obra_celular',
                'em5.email as Coordinador_Obra_email',
                DB::raw("CONCAT(COALESCE(em6.Nombre,''), ' ',COALESCE(em6.Apellido_Paterno,''), ' ',COALESCE(em6.Apellido_Materno,'')) as asistente_proyecto"),
                'em6.Celular as asistente_proyecto_celular',
                'em6.email as asistente_proyecto_email',
                DB::raw("CONCAT(COALESCE(em7.Nombre,''), ' ',COALESCE(em7.Apellido_Paterno,''), ' ',COALESCE(em7.Apellido_Materno,'')) as lead_proyecto"),
                'em7.Celular as lead_proyecto_celular',
                'em7.email as lead_proyecto_email',
                'proyecto_date_proyecto.nota'
            )
            ->leftjoin('tipo_proyecto', 'tipo_proyecto.Tipo_ID', 'proyectos.Tipo_ID')
            ->join('empresas', 'proyectos.Emp_ID', 'empresas.Emp_ID')
            ->join('estatus', 'proyectos.Estatus_ID', 'estatus.Estatus_ID')
            ->leftJoin('personal as em1', 'em1.Empleado_ID', 'proyectos.Foreman_ID')
            ->leftJoin('personal as em2', 'em2.Empleado_ID', 'proyectos.Coordinador_ID')
            ->leftJoin('personal as em3', 'em3.Empleado_ID', 'proyectos.Manager_ID')
            ->leftJoin('personal as em4', 'em4.Empleado_ID', 'proyectos.Project_Manager_ID')
            ->leftJoin('personal as em5', 'em5.Empleado_ID', 'proyectos.Coordinador_Obra_ID')
            ->leftJoin('personal as em6', 'em6.Empleado_ID', 'proyectos.Asistant_Proyect_ID')
            ->leftJoin('personal as em7', 'em7.Empleado_ID', 'proyectos.Lead_ID')
            //left consultado si hay nota en registro anteriores
            ->leftJoin('proyecto_date_proyecto', 'proyecto_date_proyecto.proyecto_id', 'proyectos.Pro_ID')
            ->where('proyectos.Pro_ID', $id)
            ->orderBy('proyecto_date_proyecto.id', 'DESC')
            ->first();
        return $movimiento;
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function get_action($id)
    {
        $action = DB::table('proyecto_detail')
            ->select(
                'proyecto_detail.*'
            )
            ->where('proyecto_detail.proyecto_id', $id)
            ->orderBy('proyecto_detail.fecha_proyecto_movimiento', 'DESC')
            ->get();
        return response()->json([
            'action' => $action,
        ], 200);
    }
    public function get_info($id)
    {
        $info = DB::table('proyecto_info')
            ->select(
                'proyecto_info.*'
            )
            ->where('proyecto_info.proyecto_id', $id)
            ->orderBy('proyecto_info.fecha_proyecto_movimiento', 'DESC')
            ->get();
        $status = $this->status();
        return response()->json([
            'info' => $info,
            'status' => $status,
        ], 200);
    }
    /* informacion de de fecha de proyectos */
    private function variables_calculo()
    {
        $proyectos = new stdClass();
        $proyectos->horas_estimadas = 0;
        $proyectos->horas_trabajadas = 0;
        $proyectos->porcentaje_horas_completadas = 0;
        $proyectos->horas_completadas = 0;
        $proyectos->porcentaje_horas_trabajadas = 0;
        $proyectos->horas_trabajadas = 0;
        $proyectos->horas_restantes = 0;
        $proyectos->Nombre = '';

        return $proyectos;
    }
    private function proyectos($pro_id)
    {
        $pisos = $this->variables_calculo();
        $floors = DB::table('floor')
            ->select(
                'floor.Pro_ID',
                'floor.Floor_ID',
                'floor.Nombre',
                'floor.Horas_Estimadas',
                'proyectos.Nombre as nombre_proyecto'
            )
            //->where('floor.Horas_Estimadas', '>', 0)
            ->where(function ($query) {
                $query->where('floor.Horas_Estimadas', '>', 0.001)
                    ->orWhere('floor.Horas_Estimadas', '<', -1);
            })
            ->join('proyectos', 'proyectos.Pro_ID', 'floor.Pro_ID')
            ->where('floor.Pro_ID', $pro_id)
            ->get();
        foreach ($floors as $key => $floor) {
            $floors[$key] = $this->pisos($floor->Pro_ID, $floor->Floor_ID);
            $pisos->Nombre = $floor->nombre_proyecto;
        }
        /* sumando datos de horas */
        foreach ($floors as $key => $floor) {
            $pisos->horas_estimadas += $floor->horas_estimadas;
            $pisos->horas_trabajadas += $floor->horas_trabajadas;
            $pisos->porcentaje_horas_completadas += $floor->porcentaje_horas_completadas;
            $pisos->horas_completadas += $floor->horas_completadas;
        }
        /* redondeo */
        $pisos->horas_completadas = round($pisos->horas_completadas, 1);
        $pisos->horas_estimadas = round($pisos->horas_estimadas, 1);
        $pisos->horas_trabajadas = round($pisos->horas_trabajadas, 1);
        $pisos->horas_restantes = round($pisos->horas_estimadas - $pisos->horas_trabajadas, 1);
        $pisos->porcentaje_horas_trabajadas = $this->porcentaje_horas_trabajadas($pisos->horas_trabajadas, $pisos->horas_estimadas);
        $pisos->porcentaje_horas_completadas = $this->porcentaje_horas_trabajadas($pisos->horas_completadas, $pisos->horas_estimadas);
        $pisos->floors = $floors;

        return $pisos;
    }
    private function horas_completadas($horas_estimadas, $completado)
    {
        $completado = $completado / 100;
        return $horas_estimadas * $completado;
    }
    private function porcentaje($total, $cantidad)
    {
        $porcentaje = ((100 * $cantidad) / $total);
        return round($porcentaje, 0);
    }
    private function porcentaje_horas_trabajadas($horas_trabajadas, $horas_estimadas)
    {
        /*controlador de error */
        try {
            $cantidad = $horas_trabajadas / $horas_estimadas;
        } catch (\Throwable $th) {
            $cantidad = 0;
        }
        $cantidad = $cantidad * 100;
        $cantidad = round($cantidad, 0);
        return $cantidad;
    }
    public function pisos($pro_id, $floor_id)
    {
        $area = $this->variables_calculo();
        $areas_control = DB::table('area_control')
            ->select(
                'area_control.Area_ID',
                'area_control.Pro_ID',
                'area_control.Floor_ID',
                'area_control.Nombre',
                'area_control.Horas_Estimadas',
                'floor.Nombre as nombre_floor'
            )
            //->where('area_control.Horas_Estimadas', '>', 0)
            ->where(function ($query) {
                $query->where('area_control.Horas_Estimadas', '>', 0.001)
                    ->orWhere('area_control.Horas_Estimadas', '<', -1);
            })
            ->join('floor', 'floor.Floor_ID', 'area_control.Floor_ID')
            ->where('area_control.Floor_ID', $floor_id)
            ->where('area_control.Pro_ID', $pro_id)
            ->orderBy('area_control.Nombre', 'ASC')
            ->get();
        foreach ($areas_control as $key => $area_control) {
            $areas_control[$key] = $this->areas($area_control->Pro_ID, $area_control->Floor_ID, $area_control->Area_ID);
            $area->Nombre = $area_control->nombre_floor;
        }
        /* sumando datos de horas */
        foreach ($areas_control as $key => $area_control) {
            $area->horas_estimadas += $area_control->horas_estimadas;
            $area->horas_trabajadas += $area_control->horas_trabajadas;
            $area->porcentaje_horas_completadas += $area_control->porcentaje_horas_completadas;
            $area->horas_completadas += $area_control->horas_completadas;
        }
        /* redondeo */
        $area->horas_completadas = round($area->horas_completadas, 1);
        $area->horas_estimadas = round($area->horas_estimadas, 1);
        $area->horas_trabajadas = round($area->horas_trabajadas, 1);
        $area->horas_restantes = round($area->horas_estimadas - $area->horas_trabajadas, 1);
        $area->porcentaje_horas_trabajadas = $this->porcentaje_horas_trabajadas($area->horas_trabajadas, $area->horas_estimadas);
        $area->porcentaje_horas_completadas = $this->porcentaje_horas_trabajadas($area->horas_completadas, $area->horas_estimadas);
        $area->areas_control = $areas_control;
        return $area;
    }
    public function areas($pro_id, $floor_id, $area_id)
    {
        $area = $this->variables_calculo();
        $tareas = DB::table('task')
            ->select(
                'task.Horas_Estimadas',
                'task.Task_ID',
                'task.Nombre',
                'task.Last_Per_Recorded',
                'area_control.Nombre as nombre_area'
            )
            //->where('task.Horas_Estimadas', '>', 0)
            ->where(function ($query) {
                $query->where('task.Horas_Estimadas', '>', 0.001)
                    ->orWhere('task.Horas_Estimadas', '<', -1);
            })
            ->where('task.Pro_ID', $pro_id)
            ->where('task.Floor_ID', $floor_id)
            ->where('task.Area_ID', $area_id)
            ->join('area_control', 'area_control.Area_ID', 'task.Area_ID')
            ->orderBy('Tas_IDT', 'ASC')
            ->get();
        foreach ($tareas as $key => $tarea) {
            $tareas[$key] = $this->tareas($tarea->Task_ID);

            $area->Nombre = $tarea->nombre_area;
        }
        /* sumando datos de horas */
        foreach ($tareas as $key => $tarea) {
            $area->horas_estimadas += $tarea->horas_estimadas;
            $area->horas_trabajadas += $tarea->horas_trabajadas;
            $area->porcentaje_horas_completadas += $tarea->porcentaje_horas_completadas;
            $area->horas_completadas += $tarea->horas_completadas;
        }
        /* redondeos */
        $area->horas_completadas = round($area->horas_completadas, 1);
        $area->horas_estimadas = round($area->horas_estimadas, 1);
        $area->horas_trabajadas = round($area->horas_trabajadas, 1);
        $area->horas_restantes = round($area->horas_estimadas - $area->horas_trabajadas, 1);
        /* */
        $area->porcentaje_horas_trabajadas = $this->porcentaje_horas_trabajadas($area->horas_trabajadas, $area->horas_estimadas);
        $area->porcentaje_horas_completadas = $this->porcentaje_horas_trabajadas($area->horas_completadas, $area->horas_estimadas);
        $area->tareas = $tareas;
        return $area;
    }
    public function tareas($task_id)
    {
        $registro_diario_actividad = DB::table(DB::raw('actividades, registro_diario,registro_diario_actividad'))
            ->select(
                DB::raw("sum(registro_diario_actividad.Horas_Contract) as horas_trabajadas"),
                'registro_diario.Reg_ID',
                'registro_diario_actividad.Task_ID',
                'task.Nombre',
                'task.Horas_Estimadas as horas_estimadas',
                'task.Last_Per_Recorded as porcentaje_horas_completadas'
            )
            ->whereRaw('registro_diario.Actividad_ID=actividades.Actividad_ID')
            ->whereRaw('registro_diario.Reg_ID=registro_diario_actividad.Reg_ID')
            ->join('task', 'registro_diario_actividad.Task_ID', 'task.Task_ID')
            ->where('registro_diario_actividad.Task_ID', $task_id)
            ->where(function ($query) {
                $query->where('registro_diario_actividad.Horas_Contract', '>', 0.001)
                    ->orWhere('registro_diario_actividad.Horas_Contract', '<', -1);
            })
            ->orderBy('registro_diario_actividad.Reg_ID', 'ASC')->get()
            ->first();

        $registro_diario_actividad->horas_restantes = round($registro_diario_actividad->horas_estimadas - $registro_diario_actividad->horas_trabajadas, 1);
        $registro_diario_actividad->porcentaje_horas_trabajadas = $this->porcentaje($registro_diario_actividad->horas_estimadas, $registro_diario_actividad->horas_trabajadas);
        /* porcentaje real */
        $registro_diario_actividad->horas_completadas = $this->horas_completadas($registro_diario_actividad->horas_estimadas, $registro_diario_actividad->porcentaje_horas_completadas);
        $registro_diario_actividad->Nombre = "" . $registro_diario_actividad->Nombre;
        return $registro_diario_actividad;
    }
    public function getDiasHabiles($fechainicio, $fechafin, $diasferiados)
    {
        // Convirtiendo en timestamp las fechas
        $fechainicio = strtotime($fechainicio);
        $fechafin = strtotime($fechafin);

        // Incremento en 1 dia
        $diainc = 24 * 60 * 60;

        // Arreglo de dias habiles, inicianlizacion
        $diashabiles = array();

        // Se recorre desde la fecha de inicio a la fecha fin, incrementando en 1 dia
        for ($midia = $fechainicio; $midia <= $fechafin; $midia += $diainc) {
            // Si el dia indicado, no es sabado o domingo es habil
            if (!in_array(date('N', $midia), array(6, 7))) { // DOC: http://www.php.net/manual/es/function.date.php
                // Si no es un dia feriado entonces es habil
                if (!in_array(date('Y-m-d', $midia), $diasferiados)) {
                    array_push($diashabiles, date('Y-m-d', $midia));
                }
            }
        }
        return $diashabiles;
    }
    private function obtener_task($proyecto_id)
    {
        $data = $this->proyectos($proyecto_id);
        return $data;
    }

    public function get_date_proyecto($id)
    {
        $fecha_proyecto = DB::table('proyecto_date_proyecto')
            ->select(
                'proyecto_date_proyecto.*',
                DB::raw('DATE_FORMAT(proyecto_date_proyecto.Fecha_Inicio , "%m/%d/%Y ") as Fecha_Inicio'),
                DB::raw('DATE_FORMAT(proyecto_date_proyecto.Fecha_Fin , "%m/%d/%Y ") as Fecha_Fin')
            )
            ->where('proyecto_date_proyecto.proyecto_id', $id)
            ->orderBy('proyecto_date_proyecto.fecha_proyecto_movimiento', 'DESC')
            ->get();
        return $fecha_proyecto;
    }
    public function update_date_proyecto(Request $request, $id)
    {
        $rules = array(
            'fecha_inicio' => 'required|date_format:m/d/Y',
            'fecha_fin' => 'required|date_format:m/d/Y',
        );
        $messages = [
            'fecha_inicio.required' => "The date start field is required",
            'fecha_fin.required' => "The end start field is required",
        ];
        $error = Validator::make($request->all(), $rules, $messages);
        if ($error->errors()->all()) {
            return response()->json([
                'status' => 'errors',
                'message' => $error->errors()->all(),
            ]);
        }
        $validar = $this->verficar_fecha_update_movimiento($request, $id);
        if (!$validar) {
            $insert = $this->create_movimiento_fecha_proyecto($request, $id);
            $update_proyecto = $this->update_proyecto($request, $id);
        } else {
            $update = $this->update_movimiento($request, $id, $validar->id);
            $update_proyecto = $this->update_proyecto($request, $id);
        }
        if ($validar) {
            return response()->json([
                'status' => 'ok',
                'message' => ['Successfully modified'],
            ], 200);
        } else {
            return response()->json([
                'status' => 'ok',
                'message' => ['Registered Successfully'],
            ], 200);
        }
    }
    public function update_proyecto(Request $request, $proyecto_id)
    {
        $proyecto = DB::table('proyectos')
            ->where('proyectos.Pro_ID', $proyecto_id)
            ->update([
                'Fecha_Inicio' => date('Y-m-d', strtotime($request->fecha_inicio)),
                'Fecha_Fin' => date('Y-m-d', strtotime($request->fecha_fin)),
                'Horas' => $request->horas_con,
            ]);
        return $proyecto;
    }
    public function update_info(Request $request, $id)
    {
        $rules = array(
            'contact' => 'required',
            'submittals' => 'required',
            'plans' => 'required',
            'vendor' => 'required',
            'const_schedule' => 'required',
            'field_folder' => 'required',
            'brake_down' => 'required',
            'badges' => 'required',
            'special_material' => 'required',
        );
        $messages = [
            'contact.required' => "The contact field is required",
            'submittals.required' => "The submittals field is required",
            'plans.required' => "The plans field is required",
            'vendor.required' => "The vendor field is required",
            'const_schedule.required' => "The constschedule field is required",
            'field_folder.required' => "The folder field is required",
            'brake_down.required' => "The brake down field is required",
            'badges.required' => "The badges field is required",
            'special_material.required' => "The special material field is required",
        ];
        $error = Validator::make($request->all(), $rules, $messages);
        if ($error->errors()->all()) {
            return response()->json([
                'status' => 'errors',
                'message' => $error->errors()->all(),
            ]);
        }
        $validar = $this->verficar_info($request, $id);
        if (!$validar) {
            $insert = $this->create_info($request, $id);
        } else {
            $update = $this->update_proyecto_info($request, $id, $validar->id);
        }
        if ($validar) {
            return response()->json([
                'status' => 'ok',
                'message' => ['Successfully modified'],
            ], 200);
        } else {
            return response()->json([
                'status' => 'ok',
                'message' => ['Registered Successfully'],
            ], 200);
        }
    }
    public function update_action(Request $request, $id)
    {
        $rules = array(
            'report_weekly' => 'nullable',
            'action_for_week' => 'nullable',
        );
        $error = Validator::make($request->all(), $rules);
        if ($error->errors()->all()) {
            return response()->json([
                'status' => 'errors',
                'message' => $error->errors()->all(),
            ]);
        }
        $validar = $this->verficar_action($request, $id);

        if (!$validar) {
            $insert = $this->create_action($request, $id);
        } else {
            $update = $this->update_proyecto_action($request, $id, $validar->id);
        }
        if ($validar) {
            return response()->json([
                'status' => 'ok',
                'message' => ['Successfully modified'],
            ], 200);
        } else {
            return response()->json([
                'status' => 'ok',
                'message' => ['Registered Successfully'],
            ], 200);
        }
    }
    private function base64_to_imagen(Request $request)
    {
        $png_url = "grafic-" . time() . ".png";
        $path = public_path() . '/uploads/' . $png_url;

        Image::make(file_get_contents($request->imagen))->save($path);
        return $png_url;
    }
    public function update_action_history(Request $request, $id)
    {
        $rules = array(
            'report_week' => 'nullable',
            'action_for_week' => 'nullable',
        );
        $error = Validator::make($request->all(), $rules);
        if ($error->errors()->all()) {
            return response()->json([
                'status' => 'errors',
                'message' => $error->errors()->all(),
            ]);
        }
        $proyecto_detail = DB::table('proyecto_detail')
            ->where('id', $id)
            ->update([
                "report_weekly" => $request->report_week,
                "action_for_week" => $request->action_for_week,
            ]);

        return response()->json([
            'status' => 'ok',
            'message' => 'Successfully modified',
        ], 200);
    }
    public function delete_action_history(Request $request, $id)
    {
        $rules = array(
            'report_week' => 'nullable',
            'action_for_week' => 'nullable',
        );
        $error = Validator::make($request->all(), $rules);
        if ($error->errors()->all()) {
            return response()->json([
                'status' => 'errors',
                'message' => $error->errors()->all(),
            ]);
        }
        $proyecto_detail = DB::table('proyecto_detail')
            ->where('id', $id)
            ->delete();
        return response()->json([
            'status' => 'ok',
            'message' => 'Successfully removed',
        ], 200);
    }
    private function verficar_info(Request $request, $proyecto_id)
    {
        $movimientos = DB::table('proyecto_info')
            ->select(
                'proyecto_info.*',
                DB::raw('DATE_FORMAT(proyecto_info.fecha_proyecto_movimiento , "%Y/%m/%d %H:%i:%s") as fecha_proyecto_movimiento')
            )
            ->where('proyecto_info.proyecto_id', $proyecto_id)
            ->get();
        $data = false;
        foreach ($movimientos as $key => $movimiento) {
            if (date('Y-m-d', strtotime($movimiento->fecha_proyecto_movimiento)) == date('Y-m-d', strtotime($request->fecha_registro))) {
                $data = $movimiento;
                break;
            }
        }
        return $data;
    }
    private function verficar_action(Request $request, $proyecto_id)
    {
        $movimientos = DB::table('proyecto_detail')
            ->select(
                'proyecto_detail.*',
                DB::raw('DATE_FORMAT(proyecto_detail.fecha_proyecto_movimiento , "%Y/%m/%d %H:%i:%s") as fecha_proyecto_movimiento')
            )
            ->where('proyecto_detail.proyecto_id', $proyecto_id)
            ->get();
        $data = false;
        foreach ($movimientos as $key => $movimiento) {
            if (date('Y-m-d', strtotime($movimiento->fecha_proyecto_movimiento)) == date('Y-m-d', strtotime($request->fecha_registro))) {
                $data = $movimiento;
                break;
            }
        }
        return $data;
    }
    private function verficar_fecha_update_movimiento(Request $request, $proyecto_id)
    {
        $movimientos = DB::table('proyecto_date_proyecto')
            ->select(
                'proyecto_date_proyecto.*',
                DB::raw('DATE_FORMAT(proyecto_date_proyecto.fecha_proyecto_movimiento , "%Y/%m/%d %H:%i:%s") as fecha_proyecto_movimiento')
            )
            ->where('proyecto_date_proyecto.proyecto_id', $proyecto_id)
            ->get();
        $data = false;
        foreach ($movimientos as $key => $movimiento) {
            if (date('Y-m-d', strtotime($movimiento->fecha_proyecto_movimiento)) == date('Y-m-d', strtotime($request->fecha_registro))) {
                //dd(date('Y-m-d', strtotime($movimiento->fecha_proyecto_movimiento)), date('Y-m-d', strtotime($request->fecha_registro)));
                $data = $movimiento;
                break;
            }
        }
        return $data;
    }
    private function update_movimiento(Request $request, $proyecto_id, $fecha_proyecto_id)
    {
        $update_movimiento = DB::table('proyecto_date_proyecto')
            ->where('proyecto_date_proyecto.proyecto_id', $proyecto_id)
            ->where('proyecto_date_proyecto.id', $fecha_proyecto_id)
            ->update([
                'fecha_proyecto_movimiento' => $request->fecha_registro,
                'proyecto_id' => $proyecto_id,
                'Fecha_Inicio' => date('Y-m-d', strtotime($request->fecha_inicio)),
                'Fecha_Fin' => date('Y-m-d', strtotime($request->fecha_fin)),
                'nota' => $request->nota == null ? '' : $request->nota,
            ]);
        return $update_movimiento;
    }

    private function create_movimiento_fecha_proyecto(Request $request, $proyecto_id)
    {
        $create_movimiento = DB::table('proyecto_date_proyecto')
            ->where('proyecto_date_proyecto.proyecto_id', $proyecto_id)
            ->insertGetId([
                'fecha_proyecto_movimiento' => date('Y-m-d H:i:s', strtotime($request->fecha_registro)),
                'proyecto_id' => $proyecto_id,
                'Fecha_Inicio' => date('Y-m-d', strtotime($request->fecha_inicio)),
                'Fecha_Fin' => date('Y-m-d', strtotime($request->fecha_fin)),
                'nota' => $request->nota == null ? '' : $request->nota,
            ]);
        return $create_movimiento;
    }
    private function create_info(Request $request, $proyecto_id)
    {
        $create_info = DB::table('proyecto_info')
            ->where('proyecto_info.proyecto_id', $proyecto_id)
            ->insertGetId([
                'fecha_proyecto_movimiento' => date('Y-m-d H:i:s', strtotime($request->fecha_registro)),
                'proyecto_id' => $proyecto_id,
                'contact_id' => $request->contact,
                'submittals_id' => $request->submittals,
                'plans_id' => $request->plans,
                'const_schedule_id' => $request->const_schedule,
                'field_folder_id' => $request->field_folder,
                'brake_down_id' => $request->brake_down,
                'badges_id' => $request->badges,
                'vendor_id' => $request->vendor,
                'special_material_id' => $request->special_material,
            ]);
        return $create_info;
    }
    private function create_action(Request $request, $proyecto_id)
    {
        try {
            $imagen = $this->base64_to_imagen($request);
        } catch (\Throwable $th) {
            $imagen = "";
        }
        $create_info = DB::table('proyecto_detail')
            ->where('proyecto_detail.proyecto_id', $proyecto_id)
            ->insertGetId([
                'fecha_proyecto_movimiento' => date('Y-m-d H:i:s', strtotime($request->fecha_registro)),
                'proyecto_id' => $proyecto_id,
                'report_weekly' => $request->report_weekly == $request->report_weekly ? $request->report_weekly : '',
                'action_for_week' => $request->action_for_week == $request->action_for_week ? $request->action_for_week : '',
                'imagen' => $imagen,
            ]);
        $insert_notificaciont = DB::table('notificacion_acciones')->insertGetId([
            'proyecto_detail_id' => $create_info,
            'notificacion_estado_id' => 1
        ]);
        return $create_info;
    }
    private function update_proyecto_action(Request $request, $proyecto_id, $proyecto_action_id)
    {
        $imagen = $this->base64_to_imagen($request);

        $update_info = DB::table('proyecto_detail')
            ->where('proyecto_detail.proyecto_id', $proyecto_id)
            ->where('proyecto_detail.id', $proyecto_action_id)
            ->update([
                'fecha_proyecto_movimiento' => date('Y-m-d H:i:s', strtotime($request->fecha_registro)),
                'proyecto_id' => $proyecto_id,
                'report_weekly' => $request->report_weekly,
                'action_for_week' => $request->action_for_week,
                'imagen' => $imagen,
            ]);
        return $update_info;
    }
    private function update_proyecto_info(Request $request, $proyecto_id, $proyecto_info_id)
    {
        $update_info = DB::table('proyecto_info')
            ->where('proyecto_info.proyecto_id', $proyecto_id)
            ->where('proyecto_info.id', $proyecto_info_id)
            ->update([
                'fecha_proyecto_movimiento' => date('Y-m-d H:i:s', strtotime($request->fecha_registro)),
                'proyecto_id' => $proyecto_id,
                'contact_id' => $request->contact,
                'submittals_id' => $request->submittals,
                'plans_id' => $request->plans,
                'const_schedule_id' => $request->const_schedule,
                'field_folder_id' => $request->field_folder,
                'brake_down_id' => $request->brake_down,
                'badges_id' => $request->badges,
                'vendor_id' => $request->vendor,
                'special_material_id' => $request->special_material,
            ]);
        return $update_info;
    }
    public function get_graficos(Request $request, $id)
    {
        $proyectos = $this->obtener_task($id);
        $dias = count($this->getDiasHabiles(date('Y-m-d', strtotime($request->fecha_inicio)), date('Y-m-d', strtotime($request->fecha_fin)), $this->diasferiados));
        try {
            $personal = round(($proyectos->horas_estimadas / $dias) / 8);
        } catch (\Throwable $th) {
            $personal = 0;
        }
        return response()->json([
            'status' => 'ok',
            'data' => [
                'dias' => $dias,
                'proyecto' => $proyectos,
                'personas' => $personal,
            ],
        ], 200);
    }
    public function view_pdf(Request $request)
    {
        $proyectos = explode(',', $request->query('proyectos'));
        $resultado = [];
        foreach ($proyectos as $key => $proyecto) {
            $data = new stdClass();
            $data->proyectos = $this->primer_movimiento($proyecto);
            $data->info = $this->obtener_info_pdf($proyecto);
            $data->actions = $this->action_pdf($proyecto);
            $data->graficos = $this->obtener_graficos_pdf($data->proyectos->Fecha_Inicio, $data->proyectos->Fecha_Fin, $proyecto);
            $data->view_graficos = $this->generate_grafico_to_base64($data->graficos);
            $resultado[] = $data;
        }
        $pdf = PDF::loadView('panel.informacion_proyecto.report.proyecto-pdf', compact('resultado'))->setPaper('landscape')->setWarnings(false);
        return $pdf->stream("View information Job.pdf");
    }
    private function generate_grafico_to_base64($graficos)
    {
        $y = $this->valor_alto([$graficos->proyectos->horas_estimadas, $graficos->proyectos->horas_trabajadas, $graficos->proyectos->horas_restantes, $graficos->proyectos->horas_completadas, $graficos->proyectos->horas_trabajadas, $graficos->proyectos->porcentaje_horas_completadas, $graficos->proyectos->porcentaje_horas_trabajadas]);
        $validar_negativo = $this->validar_negativo($graficos->proyectos->horas_restantes);
        $validar_completadas = $this->validar_completado($graficos->proyectos->porcentaje_horas_completadas, $graficos->proyectos->porcentaje_horas_trabajadas);
        $chartData = "{
            type: 'bar',
            data: {
              datasets: [
                {
                  label:'" . $graficos->proyectos->Nombre . "',
                  data: [" . $graficos->proyectos->horas_estimadas . ", " . $graficos->proyectos->horas_trabajadas . ", " . $graficos->proyectos->horas_restantes . ", " . $graficos->proyectos->horas_completadas . ", " . $graficos->proyectos->horas_trabajadas . "," . $graficos->proyectos->porcentaje_horas_completadas . "," . $graficos->proyectos->porcentaje_horas_trabajadas . "],
                  backgroundColor : [
                    'rgba(248, 251, 85, 0.4)',
                    'rgba(246, 255, 147, 0.2)',
                    '" . $validar_negativo->fondo . "',
                    '" . $validar_completadas->borde . "',
                    'rgba(246, 255, 147, 0.2)',
                ],
                borderColor : [
                    'rgba(255, 206, 86, 1)',
                    'rgba(255, 206, 86, 1)',
                    '" . $validar_negativo->borde . "',
                    '" . $validar_completadas->borde . "',
                    'rgba(255, 206, 86, 1)',
                ],
                borderWidth : 1,
                },
              ],
              labels: ['Hrs. Est.', 'Hrs. Used', 'Hrs Left', '% Completed', '% Used'],
            },
            options: {
                scales: {
                    yAxes: [{
                        display: true,
                        stacked: true,
                        ticks: {
                            max: " . $y . "
                        }
                    }]
                },
                plugins: {
                    datalabels: {
                        color: 'black',
                        anchor: 'end',
                        align: 'top',
                        borderWidth: 1,
                        font: {
                            weinht: 'bold'
                        },
                        formatter: function (value, context) {
                            switch (context.dataIndex) {
                                case 3:
                                    return context.chart.data.datasets[0].data[5]+'%';
                                    break;
                                case 4:
                                    return context.chart.data.datasets[0].data[6]+'%';
                                    break;
                                default:
                                    return context.chart.data.datasets[0].data[context.dataIndex];
                                    break;

                            }
                        }
                    }
                },
                legend: {
                    display: false,
                },
            }
        }";
        $chartURL = "https://quickchart.io/chart?width=300&height=200&c=" . urlencode($chartData);
        $arrContextOptions = array(
            "ssl" => array(
                "verify_peer" => false,
                "verify_peer_name" => false,
            ),
        );
        $chartData = file_get_contents($chartURL, false, stream_context_create($arrContextOptions));

        return 'data:image/png;base64, ' . base64_encode($chartData);
    }
    private function valor_alto($array)
    {
        $valor = max($array);
        try {
            $porcentaje = $valor / 100;
        } catch (\Throwable $th) {
            $porcentaje = 0;
        }
        $valor += $porcentaje * 20;
        return $valor;
    }
    private function validar_negativo($valor)
    {
        $color = new stdClass();
        if ($valor > 0) {
            $color->fondo = 'rgba(138, 140, 149, 0.2)';
            $color->borde = 'rgba(128, 130, 138, 0.5)';
        } else {
            $color->fondo = 'rgba(255, 99, 132, 0.2)';
            $color->borde = 'rgba(255, 99, 132, 0.5)';
        }
        return $color;
    }
    private function validar_completado($completadas, $usadas)
    {
        $color = new stdClass();
        if ($completadas >= $usadas) {
            $color->fondo = 'rgba(128, 128, 128, 0.4)';
            $color->borde = 'rgba(128, 130, 138, 0.6)';
        } else {
            $color->fondo = 'rgba(255, 45, 49, 0.3)';
            $color->borde = 'rgba(255, 45, 49, 0.5)';
        }
        return $color;
    }
    private function obtener_info_pdf($proyecto_id)
    {
        $proyecto = DB::table('proyecto_info')
            ->select(
                'contacto.nombre_status as contacto_status',
                'plans.nombre_status as plans_status',
                'const_schedule.nombre_status as const_schedule_status',
                'field_folder.nombre_status as field_folder_status',
                'brake_down.nombre_status as brake_down_status',
                'proyecto_info.*'
            )
            ->where('proyecto_info.proyecto_id', $proyecto_id)
            ->leftJoin('proyecto_info_status as contacto', 'contacto.id', 'proyecto_info.contact_id')
            ->leftJoin('proyecto_info_status as plans', 'plans.id', 'proyecto_info.plans_id')
            ->leftJoin('proyecto_info_status as const_schedule', 'const_schedule.id', 'proyecto_info.const_schedule_id')
            ->leftJoin('proyecto_info_status as field_folder', 'field_folder.id', 'proyecto_info.field_folder_id')
            ->leftJoin('proyecto_info_status as brake_down', 'brake_down.id', 'proyecto_info.brake_down_id')
            ->orderBy('proyecto_info.fecha_proyecto_movimiento', 'DESC')
            ->first();
        return $proyecto;
    }
    public function obtener_graficos_pdf($fecha_inicio, $fecha_fin, $id)
    {
        $data = new stdClass();
        $data->proyectos = $this->obtener_task($id);
        $data->dias = count($this->getDiasHabiles($fecha_inicio, $fecha_fin, $this->diasferiados));
        try {
            $data->personal = round(($data->proyectos->horas_estimadas / $data->dias) / 8);
        } catch (\Throwable $th) {
            $data->personal = 0;
        }
        return $data;
    }
    private function action_pdf($proyecto_id)
    {
        $action = DB::table('proyecto_detail')
            ->where('proyecto_detail.proyecto_id', $proyecto_id)
            ->orderBy('proyecto_detail.fecha_proyecto_movimiento', 'DESC')
            ->first();
        return $action;
    }
    public function update_hrs_cont(Request $request)
    {
        $proyecto = DB::table('proyectos')
            ->where('proyectos.Pro_ID', $request->id)
            ->update([
                'Horas' => $request->horas_con,
            ]);
        if ($proyecto) {
            return response()->json(['status' => true], 200);
        } else {
            return response()->json(['status' => false], 200);
        }
    }
    public function update_color(Request $request)
    {
        $proyecto = DB::table('proyectos')
            ->whereIn('proyectos.Pro_ID', $request->proyecto_id)
            ->update([
                'color' => $request->color,
            ]);
        if ($proyecto) {
            return response()->json([
                'status' => true,
                'color' => $request->color,
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'color' => $request->color,
            ], 200);
        }
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update_general(Request $request)
    {
        switch ($request->tipo) {
            case 'tipo':
                $proyectos = DB::table('proyectos')
                    ->where('proyectos.Pro_ID', $request->proyecto_id)
                    ->update([
                        "Tipo_ID" => $request->data,
                    ]);

                return response()->json([
                    'status' => "ok",
                    'message' => "Type modified successfully",
                ], 200);
            case 'status':
                $proyectos = DB::table('proyectos')
                    ->where('proyectos.Pro_ID', $request->proyecto_id)
                    ->update([
                        "Estatus_ID" => $request->data,
                    ]);

                return response()->json([
                    'status' => "ok",
                    'message' => "Status modified successfully",
                ], 200);
            default:
                # code...
                break;
        }
    }
}
