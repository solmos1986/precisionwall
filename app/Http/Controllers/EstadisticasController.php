<?php

namespace App\Http\Controllers;

use App\Personal;
use DB;
use Illuminate\Http\Request;
use \stdClass;

class EstadisticasController extends Controller
{
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
        $status = DB::table('estatus')->select('estatus.*')->get();
        $proyectos = DB::table('proyectos')
            ->select('proyectos.*')
        /* ->where('proyectos.Estatus_ID',1) */
            ->get();
        return view('panel.estadisticas.list', compact('status', 'proyectos'));
    }
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
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function select2(Request $request)
    {
        /* cargos */
        $verificar = $this->validador_cargo($request->cargo);
        if (!isset($request->searchTerm)) { //,' ',personal.Apellido_Materno
            $personal = Personal::
                select(
                'personal.Empleado_ID',
                DB::raw("CONCAT(COALESCE(personal.Nick_Name,''),' / ',COALESCE(personal.Nombre,''),' ',COALESCE(personal.Apellido_Paterno,''),' ',COALESCE(personal.Apellido_Materno,'')) as nombre_completo"),
            )
            /* filtro si es proyecto manager */
                ->when($verificar->pm, function ($query) {
                    return $query->join('empresas', 'empresas.Emp_ID', 'personal.Emp_ID')
                    //->where('empresas.Codigo', 'PWT')
                        ->where(function ($query) {
                            return $query->orwhere('personal.Aux5', 'F')
                                ->orWhere('personal.Aux5', 'FS')
                                ->orWhere('personal.Aux5', 'FX')
                                ->orWhere('personal.Aux5', 'FY');
                        });
                })
            /* filtro si es foreman*/
                ->when($verificar->foreman, function ($query) {

                    return $query->join('empresas', 'empresas.Emp_ID', 'personal.Emp_ID')
                        ->where(function ($query) {
                            return $query->orWhere('personal.Aux5', 'F')
                                ->orWhere('personal.Aux5', 'FB')
                                ->orWhere('personal.Aux5', 'FT')
                                ->orWhere('personal.Aux5', 'FS')
                                ->orWhere('personal.Aux5', 'FX')
                                ->orWhere('personal.Aux5', 'FY')
                                ->orWhere('personal.Cargo', 'like', '%Sub%');
                        })
                        ->where('personal.Emp_ID', '6');
                })
            /* filtro si es super*/
                ->when($verificar->super, function ($query) {

                    return $query->join('empresas', 'empresas.Emp_ID', 'personal.Emp_ID')
                        ->where('empresas.Codigo', 'PWT')
                        ->where(function ($query) {
                            return $query->where('personal.Aux5', 'F')
                                ->orWhere('personal.Aux5', 'FB')
                                ->orWhere('personal.Aux5', 'FX')
                                ->orWhere('personal.Aux5', 'FY');
                        });
                })
            /* filtro si es APM*/
                ->when($verificar->APM, function ($query) {

                    return $query->join('empresas', 'empresas.Emp_ID', 'personal.Emp_ID')
                        ->where(function ($query) {
                            return $query->orWhere('personal.Aux5', 'F')
                                ->orWhere('personal.Aux5', 'FB')
                                ->orWhere('personal.Aux5', 'FT')
                                ->orWhere('personal.Aux5', 'FS')
                                ->orWhere('personal.Aux5', 'FX')
                                ->orWhere('personal.Aux5', 'FY')
                                ->orWhere('personal.Cargo', 'like', '%Sub%');
                        });
                })
                ->orderBy('personal.Aux5', 'ASC')
                ->orderBy('personal.Nick_Name', 'ASC')
                ->get();
        } else {
            $personal = Personal::select(
                'personal.Empleado_ID',
                DB::raw("CONCAT(COALESCE(personal.Nick_Name,''),' / ',COALESCE(personal.Nombre,''),' ',COALESCE(personal.Apellido_Paterno,''),' ',COALESCE(personal.Apellido_Materno,'')) as nombre_completo"),
            )
            /* filtro si es proyecto manager */
                ->when($verificar->pm, function ($query) {
                    return $query->join('empresas', 'empresas.Emp_ID', 'personal.Emp_ID')
                    //->where('empresas.Codigo', 'PWT')
                        ->where(function ($query) {
                            return $query->orwhere('personal.Aux5', 'F')
                                ->orWhere('personal.Aux5', 'FS')
                                ->orWhere('personal.Aux5', 'FX')
                                ->orWhere('personal.Aux5', 'FY');
                        });
                })
            /* filtro si es foreman*/
                ->when($verificar->foreman, function ($query) {
                    return $query->join('empresas', 'empresas.Emp_ID', 'personal.Emp_ID')
                        ->where(function ($query) {
                            return $query->orWhere('personal.Aux5', 'F')
                                ->orWhere('personal.Aux5', 'FB')
                                ->orWhere('personal.Aux5', 'FT')
                                ->orWhere('personal.Aux5', 'FS')
                                ->orWhere('personal.Aux5', 'FX')
                                ->orWhere('personal.Aux5', 'FY')
                                ->orWhere('personal.Cargo', 'like', '%Sub%');
                        })
                        ->where('personal.Emp_ID', '6');
                })
            /* filtro si es super*/
                ->when($verificar->super, function ($query) {
                    return $query->join('empresas', 'empresas.Emp_ID', 'personal.Emp_ID')
                        ->where('empresas.Codigo', 'PWT')
                        ->where(function ($query) {
                            return $query->where('personal.Aux5', 'F')
                                ->orWhere('personal.Aux5', 'FB')
                                ->orWhere('personal.Aux5', 'FX')
                                ->orWhere('personal.Aux5', 'FY');
                        });
                })
            /* filtro si es APM*/
                ->when($verificar->APM, function ($query) {
                    return $query->join('empresas', 'empresas.Emp_ID', 'personal.Emp_ID')
                        ->where(function ($query) {
                            return $query->orWhere('personal.Aux5', 'F')
                                ->orWhere('personal.Aux5', 'FB')
                                ->orWhere('personal.Aux5', 'FT')
                                ->orWhere('personal.Aux5', 'FS')
                                ->orWhere('personal.Aux5', 'FX')
                                ->orWhere('personal.Aux5', 'FY')
                                ->orWhere('personal.Cargo', 'like', '%Sub%');
                        });
                })

                ->Where(DB::raw("CONCAT(COALESCE(personal.Nick_Name,''),' / ',COALESCE(personal.Nombre,''),' ',COALESCE(personal.Apellido_Paterno,''),' ',COALESCE(personal.Apellido_Materno,''))"), 'like', '%' . $request->searchTerm . '%')
                ->orderBy('personal.Aux5', 'ASC')
                ->orderBy('personal.Nick_Name', 'ASC')
                ->get();
        }
        //dd($personal);
        $data = [];
        foreach ($personal as $row) {
            $data[] = array(
                "id" => $row->Empleado_ID,
                "text" => "$row->nombre_completo",
            );
        }
        return response()->json($data);
    }
    public function select_proyectos(Request $request)
    {
        //dd(request()->filtro);
        $resultado = new stdClass();
        $proyectos = DB::table('proyectos')
            ->select(
                'proyectos.Pro_ID',
                'proyectos.Nombre',
                'empresas.Nombre as NombreEmpresa',
                'estatus.Nombre_Estatus as nombre_estatus',
                DB::raw("CONCAT( project_manager.Nombre,' ', project_manager.Apellido_Paterno,' ',project_manager.Apellido_Materno) as nombre_project_manager"),
                DB::raw("CONCAT( foreman.Nombre,' ', foreman.Apellido_Paterno,' ',foreman.Apellido_Materno) as nombre_foreman"),
                DB::raw("CONCAT( super.Nombre,' ', super.Apellido_Paterno,' ',super.Apellido_Materno) as nombre_super"),
                DB::raw("CONCAT( asistente.Nombre,' ', asistente.Apellido_Paterno,' ',asistente.Apellido_Materno) as nombre_asistente"),
                'proyectos.Codigo'
            )
        /* filtro si es status*/
            ->when(request()->status, function ($query) {
                return $query->whereIn('proyectos.Estatus_ID', request()->status);
            })
            ->join('personal as project_manager', 'project_manager.Empleado_ID', 'proyectos.Manager_ID')
            ->join('personal as foreman', 'foreman.Empleado_ID', 'proyectos.Foreman_ID')
            ->join('personal as super', 'super.Empleado_ID', 'proyectos.Coordinador_ID')
            ->leftJoin('personal as asistente', 'asistente.Empleado_ID', 'proyectos.Asistant_Proyect_ID')
            ->Join('estatus', 'estatus.Estatus_ID', 'proyectos.Estatus_ID')
        /* filtro si es proyecto manager */
            ->when(request()->cargo == 'pm' && request()->filtro, function ($query) {
                //dd('PM');
                return $query->where('proyectos.Manager_ID', request()->filtro);
            })
        /* filtro si es foreman*/
            ->when(request()->cargo == 'foreman' && request()->filtro, function ($query) {
                //dd('FOREMAN');
                return $query->where('proyectos.Foreman_ID', request()->filtro);
            })
        /* filtro si es super*/
            ->when(request()->cargo == 'super' && request()->filtro, function ($query) {
                //dd('super');
                return $query->where('proyectos.Coordinador_ID', request()->filtro);
            })
        /* filtro si es APM*/
            ->when(request()->cargo == 'APM' && request()->filtro, function ($query) {
                //dd('APM');
                return $query->where('proyectos.Asistant_Proyect_ID', request()->filtro);
            })
        /* filtro si es proyecto*/
            ->when(request()->multiselect_project, function ($query) {
                //dd('busqueda por compañia');
                return $query->whereIn('proyectos.Pro_ID', request()->multiselect_project);
            })
        /* filtro si es compañia*/
            ->when(request()->select2_company, function ($query) {
                //dd('busqueda por compañia');
                return $query->where('proyectos.Emp_ID', request()->select2_company);
            })
            ->join('empresas', 'empresas.Emp_ID', 'proyectos.Emp_ID')
            ->orderBy('proyectos.Nombre', 'ASC')
            ->get();
        $resultado->proyectos = $proyectos;
        $resultado->tipo = 'proyectos';
        $resultado->view_floor = true;
        $resultado->view_areas = true;
        $resultado->view_task = true;
        $this->all_floor($proyectos, true, true);
        return response()->json($resultado, 200);
        /*  switch (!$request->multiselect_project) {
    case true:
    if ($request->filtro) {
    $proyectos = $this->all_proyecto_persona(
    $request->cargo,
    $request->filtro,
    $request->status,
    $request->view_floor,
    $request->view_area,
    $request->view_task,
    $request->from_date,
    $request->to_date
    );
    $proyectos->tipo = "proyectos";
    return response()->json($proyectos, 200);
    } else {
    if ($request->select2_company) {
    $proyectos = $this->all_proyectos(
    $request->multiselect_project,
    $request->select2_company,
    $request->status,
    $request->view_floor,
    $request->view_area,
    $request->view_task
    );
    $proyectos->tipo = "proyectos";
    return response()->json($proyectos, 200);
    } else {
    if ($request->proyectos == 'all') {
    $proyectos = $this->inicio_proyectos(
    false,
    false,
    $request->status,
    $request->view_floor,
    $request->view_area,
    $request->view_task
    );
    $proyectos->tipo = "proyectos";
    return response()->json($proyectos, 200);
    } else {
    $empresas = $this->all_empresas($request->status);
    $empresas->tipo = "empresas";
    return response()->json($empresas, 200);
    }
    }
    }
    break;
    case false:
    $proyectos = $this->all_proyectos(
    $request->multiselect_project,
    false,
    $request->status,
    $request->view_floor,
    $request->view_area,
    $request->view_task
    );
    $proyectos->tipo = "proyectos";
    return response()->json($proyectos, 200);
    break;

    default:
    # code...
    break;
    } */
    }
    private function all_task($areas)
    {
        foreach ($areas as $i => $area) {
            $task = DB::table('task')
                ->select(
                    'task.Task_ID',
                    'task.Nombre'
                )
                ->where('task.Area_ID', $area->Area_ID)
                ->where('task.Horas_Estimadas', '>', 0)
                ->orderBy('task.Nombre', 'ASC')
                ->get();
            $areas[$i]->tareas = $task;
        }
    }
    private function all_areas($floors, $view_task)
    {
        foreach ($floors as $i => $floor) {
            $area_control = DB::table('area_control')
                ->select(
                    'area_control.Area_ID',
                    'area_control.Nombre'
                )
                ->where('area_control.Floor_ID', $floor->Floor_ID)
                ->where('area_control.Horas_Estimadas', '>', 0)
                ->orderBy('area_control.Nombre', 'ASC')
                ->get();
            $floors[$i]->area_control = $area_control;
            if ($view_task) {
                $this->all_task($area_control);
            }
        }
    }
    private function all_floor($proyectos, $view_areas, $view_task)
    {
        foreach ($proyectos as $i => $value) {
            $floors = DB::table('floor')
                ->select(
                    'floor.Floor_ID',
                    'floor.Pro_ID',
                    'floor.Nombre'
                )
                ->where('floor.Pro_ID', $value->Pro_ID)
                ->where('floor.Horas_Estimadas', '>', 0)
                ->orderBy('floor.Nombre', 'ASC')
                ->get();
            $proyectos[$i]->floors = $floors;

            if ($view_areas) {
                $this->all_areas($floors, $view_task);
            }
        }
    }
    private function validador_cargo($cargo)
    {
        $validar = new stdClass();
        /* cargos */
        $validar->pm = false;
        $validar->super = false;
        $validar->APM = false;
        $validar->foreman = false;
        switch ($cargo) {
            case 'pm':
                $validar->pm = true;
                break;
            case 'super':
                $validar->super = true;
                break;

            case 'APM':
                $validar->APM = true;
                break;

            case 'foreman':
                $validar->foreman = true;
                break;

            default:
                # code...
                break;
        }
        return $validar;
    }

    private function inicio_proyectos($array_proyecto_id, $empresa_id = false, $status, $floor, $areas, $task)
    {
        $verificar = is_null($array_proyecto_id);
        $resultado = new stdClass();
        $fecha_actual = date('Y-m-d');
        $fecha_antigua = date('Y-m-d', strtotime(date('Y-m-d') . "- 4 month"));

        $proyectos = DB::table('proyectos')
            ->select(
                'proyectos.Pro_ID',
                'proyectos.Nombre',
                'estatus.Nombre_Estatus as nombre_estatus',
                DB::raw("CONCAT( project_manager.Nombre,' ', project_manager.Apellido_Paterno,' ',project_manager.Apellido_Materno) as nombre_project_manager"),
                DB::raw("CONCAT( foreman.Nombre,' ', foreman.Apellido_Paterno,' ',foreman.Apellido_Materno) as nombre_foreman"),
                'proyectos.Codigo'
            )
        /* filtro si hay empresa */
            ->when($empresa_id, function ($query, $empresa_id) {
                return $query->where('proyectos.Emp_ID', $empresa_id);
            })
        /* filtro si hay proyecto  */
            ->when($array_proyecto_id, function ($query, $array_proyecto_id) {
                return $query->whereIn('proyectos.Pro_ID', $array_proyecto_id);
            })
        /* filtro si es status*/
            ->when($status, function ($query) use ($status) {
                return $query->where('proyectos.Estatus_ID', $status);
            })
            ->join('personal as project_manager', 'project_manager.Empleado_ID', 'proyectos.Manager_ID')
            ->join('personal as foreman', 'foreman.Empleado_ID', 'proyectos.Foreman_ID')
            ->join('estatus', 'estatus.Estatus_ID', 'proyectos.Estatus_ID')
            ->whereBetween('proyectos.Fecha_Inicio', [$fecha_antigua, $fecha_actual])
            ->orderBy('proyectos.Nombre', 'ASC')
            ->get();
        /* filtro ver pisos */
        if (!is_null($floor)) {
            /* filtro ver areas */
            if (!is_null($areas)) {
                $this->all_floor($proyectos, true, true);
                if (!is_null($task)) {
                    $this->all_floor($proyectos, true, true);
                    $resultado->view_floor = true;
                    $resultado->view_areas = true;
                    $resultado->view_task = true;
                } else {
                    $this->all_floor($proyectos, true, false);
                    $resultado->view_floor = true;
                    $resultado->view_areas = true;
                    $resultado->view_task = false;
                }
            } else {
                $this->all_floor($proyectos, false, false);
                $resultado->view_floor = true;
                $resultado->view_areas = false;
                $resultado->view_task = false;
            }
        } else {
            $resultado->view_floor = false;
            $resultado->view_areas = false;
            $resultado->view_task = false;
        }
        $resultado->proyectos = $proyectos;
        return $resultado;
    }
    private function all_proyectos($array_proyecto_id, $empresa_id = false, $status, $floor, $areas, $task)
    {
        $verificar = is_null($array_proyecto_id);
        $resultado = new stdClass();
        $proyectos = DB::table('proyectos')
            ->select(
                'proyectos.Pro_ID',
                'proyectos.Nombre',
                'estatus.Nombre_Estatus as nombre_estatus',
                DB::raw("CONCAT( project_manager.Nombre,' ', project_manager.Apellido_Paterno,' ',project_manager.Apellido_Materno) as nombre_project_manager"),
                DB::raw("CONCAT( foreman.Nombre,' ', foreman.Apellido_Paterno,' ',foreman.Apellido_Materno) as nombre_foreman"),
                'proyectos.Codigo'
            )
        /* filtro si hay empresa */
            ->when($empresa_id, function ($query, $empresa_id) {
                return $query->where('proyectos.Emp_ID', $empresa_id);
            })
        /* filtro si hay proyecto  */
            ->when($array_proyecto_id, function ($query, $array_proyecto_id) {
                return $query->whereIn('proyectos.Pro_ID', $array_proyecto_id);
            })
        /* filtro si es status*/
            ->when($status, function ($query) use ($status) {
                return $query->where('proyectos.Estatus_ID', $status);
            })
            ->join('personal as project_manager', 'project_manager.Empleado_ID', 'proyectos.Manager_ID')
            ->join('personal as foreman', 'foreman.Empleado_ID', 'proyectos.Foreman_ID')
            ->join('estatus', 'estatus.Estatus_ID', 'proyectos.Estatus_ID')
            ->orderBy('proyectos.Nombre', 'ASC')
            ->get();
        /* filtro ver pisos */
        if (!is_null($floor)) {
            /* filtro ver areas */
            if (!is_null($areas)) {
                $this->all_floor($proyectos, true, true);
                if (!is_null($task)) {
                    $this->all_floor($proyectos, true, true);
                    $resultado->view_floor = true;
                    $resultado->view_areas = true;
                    $resultado->view_task = true;
                } else {
                    $this->all_floor($proyectos, true, false);
                    $resultado->view_floor = true;
                    $resultado->view_areas = true;
                    $resultado->view_task = false;
                }
            } else {
                $this->all_floor($proyectos, false, false);
                $resultado->view_floor = true;
                $resultado->view_areas = false;
                $resultado->view_task = false;
            }
        } else {
            $resultado->view_floor = false;
            $resultado->view_areas = false;
            $resultado->view_task = false;
        }
        $resultado->proyectos = $proyectos;
        return $resultado;
    }
    private function all_proyecto_persona($cargo, $personal_id, $status, $floor, $areas, $task, $date_from = false, $date_to = false)
    {
        $verificar_cargo = $this->validador_cargo($cargo);
        $resultado = new stdClass();
        $proyectos = DB::table('proyectos')
            ->select(
                'proyectos.Pro_ID',
                'proyectos.Nombre',
                'estatus.Nombre_Estatus as nombre_estatus',
                DB::raw("CONCAT( project_manager.Nombre,' ', project_manager.Apellido_Paterno,' ',project_manager.Apellido_Materno) as nombre_project_manager"),
                DB::raw("CONCAT( foreman.Nombre,' ', foreman.Apellido_Paterno,' ',foreman.Apellido_Materno) as nombre_foreman"),
                'proyectos.Codigo'
            )
        /* filtro si es status*/
            ->when($status, function ($query) use ($status) {
                return $query->where('proyectos.Estatus_ID', $status);
            })
            ->join('personal as project_manager', 'project_manager.Empleado_ID', 'proyectos.Manager_ID')
            ->join('personal as foreman', 'foreman.Empleado_ID', 'proyectos.Foreman_ID')
            ->join('estatus', 'estatus.Estatus_ID', 'proyectos.Estatus_ID')
        /* filtro si es proyecto manager */
            ->when($verificar_cargo->pm, function ($query) use ($personal_id) {
                return $query->where('proyectos.Manager_ID', $personal_id);
            })
        /* filtro si es foreman*/
            ->when($verificar_cargo->foreman, function ($query) use ($personal_id) {
                return $query->where('proyectos.Foreman_ID', $personal_id);
            })
        /* filtro si es super*/
            ->when($verificar_cargo->super, function ($query) use ($personal_id) {
                return $query->where('proyectos.Coordinador_Obra_ID', $personal_id);
            })
        /* filtro si es APM*/
            ->when($verificar_cargo->APM, function ($query) use ($personal_id) {
                return $query->where('proyectos.Manager_ID', $personal_id);
            })
        /* filtro si es Fecha*/
            ->when($date_from, function ($query) use ($date_from, $date_to) {
                //dd($date_from, $date_to);
                return $query->whereBetween('proyectos.Fecha_Inicio', [date('Y-m-d H:i:s', strtotime($date_from)), date('Y-m-d H:i:s', strtotime($date_to))]);
            })
            ->orderBy('proyectos.Nombre', 'ASC')
            ->get();

        /* filtro ver pisos */
        if (!is_null($floor)) {
            /* filtro ver areas */
            if (!is_null($areas)) {
                $this->all_floor($proyectos, true, true);
                if (!is_null($task)) {
                    $this->all_floor($proyectos, true, true);
                    $resultado->view_floor = true;
                    $resultado->view_areas = true;
                    $resultado->view_task = true;
                } else {
                    $this->all_floor($proyectos, true, false);
                    $resultado->view_floor = true;
                    $resultado->view_areas = true;
                    $resultado->view_task = false;
                }
            } else {
                $this->all_floor($proyectos, false, false);
                $resultado->view_floor = true;
                $resultado->view_areas = false;
                $resultado->view_task = false;
            }
        } else {
            $resultado->view_floor = false;
            $resultado->view_areas = false;
            $resultado->view_task = false;
        }

        $resultado->proyectos = $proyectos;
        return $resultado;
    }
    private function filtros($cargo, $personal_id, $status, $floor, $areas, $task, $date_from = false, $date_to = false)
    {
        $verificar_cargo = $this->validador_cargo($cargo);
        $resultado = new stdClass();
        $proyectos = DB::table('proyectos')
            ->select(
                'proyectos.Pro_ID',
                'proyectos.Nombre',
                'estatus.Nombre_Estatus as nombre_estatus',
                DB::raw("CONCAT( project_manager.Nombre,' ', project_manager.Apellido_Paterno,' ',project_manager.Apellido_Materno) as nombre_project_manager"),
                DB::raw("CONCAT( foreman.Nombre,' ', foreman.Apellido_Paterno,' ',foreman.Apellido_Materno) as nombre_foreman"),
                'proyectos.Codigo'
            )
        /* filtro si es status*/
            ->when($status, function ($query) use ($status) {
                return $query->where('proyectos.Estatus_ID', $status);
            })
            ->join('personal as project_manager', 'project_manager.Empleado_ID', 'proyectos.Manager_ID')
            ->join('personal as foreman', 'foreman.Empleado_ID', 'proyectos.Foreman_ID')
            ->join('estatus', 'estatus.Estatus_ID', 'proyectos.Estatus_ID')
        /* filtro si es proyecto manager */
            ->when($verificar_cargo->pm, function ($query) use ($personal_id) {
                return $query->where('proyectos.Manager_ID', $personal_id);
            })
        /* filtro si es foreman*/
            ->when($verificar_cargo->foreman, function ($query) use ($personal_id) {
                return $query->where('proyectos.Foreman_ID', $personal_id);
            })
        /* filtro si es super*/
            ->when($verificar_cargo->super, function ($query) use ($personal_id) {
                return $query->where('proyectos.Coordinador_Obra_ID', $personal_id);
            })
        /* filtro si es APM*/
            ->when($verificar_cargo->APM, function ($query) use ($personal_id) {
                return $query->where('proyectos.Manager_ID', $personal_id);
            })
        /* filtro si es Fecha*/
            ->when($date_from, function ($query) use ($date_from, $date_to) {
                //dd($date_from, $date_to);
                return $query->whereBetween('proyectos.Fecha_Inicio', [date('Y-m-d H:i:s', strtotime($date_from)), date('Y-m-d H:i:s', strtotime($date_to))]);
            })
            ->orderBy('proyectos.Nombre', 'ASC')
            ->get();

        /* filtro ver pisos */
        if (!is_null($floor)) {
            /* filtro ver areas */
            if (!is_null($areas)) {
                $this->all_floor($proyectos, true, true);
                if (!is_null($task)) {
                    $this->all_floor($proyectos, true, true);
                    $resultado->view_floor = true;
                    $resultado->view_areas = true;
                    $resultado->view_task = true;
                } else {
                    $this->all_floor($proyectos, true, false);
                    $resultado->view_floor = true;
                    $resultado->view_areas = true;
                    $resultado->view_task = false;
                }
            } else {
                $this->all_floor($proyectos, false, false);
                $resultado->view_floor = true;
                $resultado->view_areas = false;
                $resultado->view_task = false;
            }
        } else {
            $resultado->view_floor = false;
            $resultado->view_areas = false;
            $resultado->view_task = false;
        }

        $resultado->proyectos = $proyectos;
        return $resultado;
    }
    private function all_empresas($status)
    {
        //dd($status);
        $resultado = new stdClass();
        $empresas = DB::table('empresas')
            ->select(
                'empresas.Emp_ID',
                'empresas.Nombre',
                'empresas.Codigo'
            )
            ->orderBy('empresas.Nombre', 'ASC')
        /* filtro si es status*/
            ->when($status, function ($query) use ($status) {
                return $query->join('proyectos', 'proyectos.Emp_ID', 'proyectos.Emp_ID')
                    ->join('estatus', 'estatus.Estatus_ID', 'proyectos.Estatus_ID')
                    ->addSelect(DB::raw('estatus.Nombre_Estatus'))
                    ->addSelect(DB::raw('estatus.Estatus_ID'))
                    ->distinct('empresas.Emp_ID')
                    ->where('proyectos.Estatus_ID', $status);
            })
            ->get();
        $resultado->empresas = $empresas;
        return $resultado;
    }
    public function project_manager(Request $request)
    {
        switch ($request->tipo) {
            case 'task':
                $tarea = $this->tareas($request->task_id);
                $tarea->tipo = $request->tipo;
                return response()->json($tarea, 200);
                break;
            case 'areas':
                $area = $this->areas($request->pro_id, $request->floor_id, $request->area_id);
                $area->tipo = $request->tipo;
                return response()->json($area, 200);
                break;
            case 'floor':
                $pisos = $this->pisos($request->pro_id, $request->floor_id);
                $pisos->tipo = $request->tipo;
                return response()->json($pisos, 200);
                break;
            case 'proyecto':
                $proyecto = $this->proyectos($request->pro_id);
                $proyecto->tipo = $request->tipo;
                return response()->json($proyecto, 200);
                break;
            case 'empresa':
                $empresa = $this->empresas($request->emp_id, $request->status);
                $empresa->tipo = $request->tipo;
                return response()->json($empresa, 200);
                break;
            case 'resumen':
                //dd($request->proyectos, $request->cargo, $request->personal_id, $request->status);
                $empresa = $this->resumen_personal($request->proyectos, $request->cargo, $request->personal_id, $request->status, $request->from_date, $request->to_date);
                $empresa->tipo = $request->tipo;
                return response()->json($empresa, 200);
                break;
            case 'inicio':
                $empresa = $this->inicio_proyecto_graficos();
                $empresa->tipo = $request->tipo;
                return response()->json($empresa, 200);
                break;

            default:
                break;
        }
    }
    private function resumen_proyectos()
    {
        $proyectos = new stdClass();
        $proyectos->horas_estimadas = 0;
        $proyectos->horas_trabajadas = 0;
        $proyectos->porcentaje_horas_completadas = 0;
        $proyectos->horas_completadas = 0;
        $proyectos->porcentaje_horas_trabajadas = 0;
        $proyectos->horas_trabajadas = 0;
        $proyectos->horas_restantes = 0;
        $fecha_actual = date('Y-m-d');
        $fecha_antigua = date('Y-m-d', strtotime(date('Y-m-d') . "- 4 month"));
        $db_proyectos = DB::table('proyectos')
            ->select(
                'proyectos.Pro_ID',
                'proyectos.Emp_ID',
                'proyectos.Nombre',
                'proyectos.Horas_Estimadas'
            )
            ->where('proyectos.Horas_Estimadas', '>', 0)
            ->where('proyectos.Estatus_ID', 1)
            ->whereBetween('proyectos.Fecha_Inicio', [$fecha_antigua, $fecha_actual])
            ->get();
        foreach ($db_proyectos as $key => $proyecto) {
            $db_proyectos[$key] = $this->proyectos($proyecto->Pro_ID);
            $db_proyectos[$key]->Nombre = $proyecto->Nombre;
        }
        /* sumando datos de horas */
        foreach ($db_proyectos as $key => $proyecto) {
            $proyectos->horas_estimadas += $proyecto->horas_estimadas;
            $proyectos->horas_trabajadas += $proyecto->horas_trabajadas;
            $proyectos->porcentaje_horas_completadas += $proyecto->porcentaje_horas_completadas;
            $proyectos->horas_completadas += $proyecto->horas_completadas;
        }
        /* redondeo */
        $proyectos->horas_completadas = round($proyectos->horas_completadas, 1);
        $proyectos->horas_estimadas = round($proyectos->horas_estimadas, 1);
        $proyectos->horas_trabajadas = round($proyectos->horas_trabajadas, 1);
        $proyectos->horas_restantes = round($proyectos->horas_estimadas - $proyectos->horas_trabajadas, 1);
        $proyectos->porcentaje_horas_trabajadas = $this->porcentaje_horas_trabajadas($proyectos->horas_trabajadas, $proyectos->horas_estimadas);
        $proyectos->porcentaje_horas_completadas = $this->porcentaje_horas_trabajadas($proyectos->horas_completadas, $proyectos->horas_estimadas);
        $proyectos->proyectos = $db_proyectos;
        $proyectos->Nombre = 'Summary Project ' . date("m/d/Y", strtotime($fecha_antigua)) . ' - ' . date("m/d/Y", strtotime($fecha_actual));
        return $proyectos;
    }
    private function inicio_proyecto_graficos()
    {
        $proyectos = new stdClass();
        $proyectos->horas_estimadas = 0;
        $proyectos->horas_trabajadas = 0;
        $proyectos->porcentaje_horas_completadas = 0;
        $proyectos->horas_completadas = 0;
        $proyectos->porcentaje_horas_trabajadas = 0;
        $proyectos->horas_trabajadas = 0;
        $proyectos->horas_restantes = 0;
        $fecha_actual = date('Y-m-d');
        $fecha_antigua = date('Y-m-d', strtotime(date('Y-m-d') . "- 4 month"));
        $db_proyectos = DB::table('proyectos')
            ->select(
                'proyectos.Pro_ID',
                'proyectos.Emp_ID',
                'proyectos.Nombre',
                'proyectos.Horas_Estimadas'
            )
            ->where('proyectos.Horas_Estimadas', '>', 0)
            ->where('proyectos.Estatus_ID', 1)
            ->whereBetween('proyectos.Fecha_Inicio', [$fecha_antigua, $fecha_actual])
            ->get();
        foreach ($db_proyectos as $key => $proyecto) {
            $db_proyectos[$key] = $this->proyectos($proyecto->Pro_ID);
            $db_proyectos[$key]->Nombre = $proyecto->Nombre;
        }
        /* sumando datos de horas */
        foreach ($db_proyectos as $key => $proyecto) {
            $proyectos->horas_estimadas += $proyecto->horas_estimadas;
            $proyectos->horas_trabajadas += $proyecto->horas_trabajadas;
            $proyectos->porcentaje_horas_completadas += $proyecto->porcentaje_horas_completadas;
            $proyectos->horas_completadas += $proyecto->horas_completadas;
        }
        /* redondeo */
        $proyectos->horas_completadas = round($proyectos->horas_completadas, 1);
        $proyectos->horas_estimadas = round($proyectos->horas_estimadas, 1);
        $proyectos->horas_trabajadas = round($proyectos->horas_trabajadas, 1);
        $proyectos->horas_restantes = round($proyectos->horas_estimadas - $proyectos->horas_trabajadas, 1);
        $proyectos->porcentaje_horas_trabajadas = $this->porcentaje_horas_trabajadas($proyectos->horas_trabajadas, $proyectos->horas_estimadas);
        $proyectos->porcentaje_horas_completadas = $this->porcentaje_horas_trabajadas($proyectos->horas_completadas, $proyectos->horas_estimadas);
        $proyectos->proyectos = $db_proyectos;
        $proyectos->Nombre = 'Summary Project ' . date("m/d/Y", strtotime($fecha_antigua)) . ' - ' . date("m/d/Y", strtotime($fecha_actual));
        return $proyectos;
    }
    private function multiplesEmpresas($proyectos_id, $status)
    {
        $db_proyectos = DB::table('proyectos')
            ->select(
                'proyectos.Pro_ID',
                'proyectos.Emp_ID',
                'proyectos.Nombre',
                'proyectos.Horas_Estimadas',
                'empresas.Nombre as nombre_empresa'
            )
            ->where('proyectos.Horas_Estimadas', '>', 0)
            ->join('empresas', 'empresas.Emp_ID', 'proyectos.Emp_ID')
        /* filtro si es status*/
            ->when($status, function ($query) use ($status) {
                return $query->whereIn('proyectos.Estatus_ID', $status);
            })
            ->whereIn('proyectos.Pro_ID', $proyectos_id)
            ->orderBy('proyectos.Nombre', 'ASC')
            ->get();

        return $db_proyectos;
    }
    private function unaEmpresas($empresa_id, $status)
    {
        $db_proyectos = DB::table('proyectos')
            ->select(
                'proyectos.Pro_ID',
                'proyectos.Emp_ID',
                'proyectos.Nombre',
                'proyectos.Horas_Estimadas',
                'empresas.Nombre as nombre_empresa'
            )
            ->where('proyectos.Horas_Estimadas', '>', 0)
            ->join('empresas', 'empresas.Emp_ID', 'proyectos.Emp_ID')
        /* filtro si es status*/
            ->when($status, function ($query) use ($status) {
                return $query->whereIn('proyectos.Estatus_ID', $status);
            })
            ->where('proyectos.Emp_ID', $empresa_id)
            ->get();
        return $db_proyectos;
    }
    private function empresas($empresa_id, $status = false)
    {
        $proyectos = $area = $this->variables_calculo();
        if (is_array($empresa_id)) {
            $db_proyectos = $this->multiplesEmpresas($empresa_id, $status);
        } else {
            $db_proyectos = $this->unaEmpresas($empresa_id, $status);
        }
        $evaluar_array = [];
        foreach ($db_proyectos as $key => $proyecto) {
            $db_proyectos[$key] = $this->proyectos($proyecto->Pro_ID);
            $db_proyectos[$key]->Nombre = $proyecto->Nombre;

            $evaluar_array[] = $proyecto->nombre_empresa;
        }
        $proyectos->Nombre = implode(',', array_unique($evaluar_array));
        /* sumando datos de horas */
        foreach ($db_proyectos as $key => $proyecto) {
            $proyectos->horas_estimadas += $proyecto->horas_estimadas;
            $proyectos->horas_trabajadas += $proyecto->horas_trabajadas;
            $proyectos->porcentaje_horas_completadas += $proyecto->porcentaje_horas_completadas;
            $proyectos->horas_completadas += $proyecto->horas_completadas;
        }
        /* redondeo */
        $proyectos->horas_completadas = round($proyectos->horas_completadas, 1);
        $proyectos->horas_estimadas = round($proyectos->horas_estimadas, 1);
        $proyectos->horas_trabajadas = round($proyectos->horas_trabajadas, 1);
        $proyectos->horas_restantes = round($proyectos->horas_estimadas - $proyectos->horas_trabajadas, 1);
        $proyectos->porcentaje_horas_trabajadas = $this->porcentaje_horas_trabajadas($proyectos->horas_trabajadas, $proyectos->horas_estimadas);
        $proyectos->porcentaje_horas_completadas = $this->porcentaje_horas_trabajadas($proyectos->horas_completadas, $proyectos->horas_estimadas);
        $proyectos->proyectos = $db_proyectos;
        return $proyectos;
    }
    private function resumen_personal($proyectos, $cargo, $personal_id, $status, $date_from = false, $date_to = false)
    {
        $verificar_cargo = $this->validador_cargo($cargo);
        $proyectos = $this->variables_calculo();
        $db_proyectos = DB::table('proyectos')
            ->select(
                'proyectos.Pro_ID',
                'proyectos.Emp_ID',
                'proyectos.Nombre',
                'proyectos.Horas_Estimadas'
            )
            ->where('proyectos.Horas_Estimadas', '>', 0)
        /* filtro si es status*/
            ->when($status, function ($query) use ($status) {
                return $query->where('proyectos.Estatus_ID', $status);
            })
        /* filtro si es proyecto manager */
            ->when($verificar_cargo->pm, function ($query) use ($personal_id) {
                return $query->where('proyectos.Manager_ID', $personal_id);
            })
        /* filtro si es foreman*/
            ->when($verificar_cargo->foreman, function ($query) use ($personal_id) {
                return $query->where('proyectos.Foreman_ID', $personal_id);
            })
        /* filtro si es super*/
            ->when($verificar_cargo->super, function ($query) use ($personal_id) {
                return $query->where('proyectos.Coordinador_Obra_ID', $personal_id);
            })
        /* filtro si es APM*/
            ->when($verificar_cargo->APM, function ($query) use ($personal_id) {
                return $query->where('proyectos.Manager_ID', $personal_id);
            })
        /* filtro si es Fecha*/
            ->when($date_from, function ($query) use ($date_from, $date_to) {
                //dd($date_from, $date_to);
                return $query->whereBetween('proyectos.Fecha_Inicio', [date('Y-m-d H:i:s', strtotime($date_from)), date('Y-m-d H:i:s', strtotime($date_to))]);
            })
            ->join('personal as project_manager', 'project_manager.Empleado_ID', 'proyectos.Manager_ID')
            ->join('personal as foreman', 'foreman.Empleado_ID', 'proyectos.Foreman_ID')
            ->join('estatus', 'estatus.Estatus_ID', 'proyectos.Estatus_ID')
            ->orderBy('proyectos.Nombre', 'ASC')
            ->get();
        foreach ($db_proyectos as $key => $proyecto) {
            $db_proyectos[$key] = $this->proyectos($proyecto->Pro_ID);
            $db_proyectos[$key]->Nombre = $proyecto->Nombre;
        }
        /* sumando datos de horas */
        foreach ($db_proyectos as $key => $proyecto) {
            $proyectos->horas_estimadas += $proyecto->horas_estimadas;
            $proyectos->horas_trabajadas += $proyecto->horas_trabajadas;
            $proyectos->porcentaje_horas_completadas += $proyecto->porcentaje_horas_completadas;
            $proyectos->horas_completadas += $proyecto->horas_completadas;
        }
        /* redondeo */
        $proyectos->horas_completadas = round($proyectos->horas_completadas, 1);
        $proyectos->horas_estimadas = round($proyectos->horas_estimadas, 1);
        $proyectos->horas_trabajadas = round($proyectos->horas_trabajadas, 1);
        $proyectos->horas_restantes = round($proyectos->horas_estimadas - $proyectos->horas_trabajadas, 1);
        $proyectos->porcentaje_horas_trabajadas = $this->porcentaje_horas_trabajadas($proyectos->horas_trabajadas, $proyectos->horas_estimadas);
        $proyectos->porcentaje_horas_completadas = $this->porcentaje_horas_trabajadas($proyectos->horas_completadas, $proyectos->horas_estimadas);
        $proyectos->proyectos = $db_proyectos;
        $proyectos->Nombre = 'Summary Projects';
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
        //dd($pisos);
        return $pisos;
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
        //dd($areas_control, $floor_id, $pro_id);
        //dd($areas_control);
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
        //dd('DATA TAREAS', $area);
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
            ->where(function ($query) {
                $query->where('registro_diario_actividad.Horas_Contract', '>',0.001)
                    ->orWhere('registro_diario_actividad.Horas_Contract', '<', -1);
            })
            ->join('task', 'registro_diario_actividad.Task_ID', 'task.Task_ID')
            ->where('registro_diario_actividad.Task_ID', $task_id)
            ->orderBy('registro_diario_actividad.Reg_ID', 'ASC')
            ->first();
        //dd($registro_diario_actividad);
        $registro_diario_actividad->horas_restantes = round($registro_diario_actividad->horas_estimadas - $registro_diario_actividad->horas_trabajadas, 1);
        $registro_diario_actividad->porcentaje_horas_trabajadas = $this->porcentaje($registro_diario_actividad->horas_estimadas, $registro_diario_actividad->horas_trabajadas);
        /* porcentaje real */
        $registro_diario_actividad->horas_completadas = $this->horas_completadas($registro_diario_actividad->horas_estimadas, $registro_diario_actividad->porcentaje_horas_completadas);
        $registro_diario_actividad->Nombre = "" . $registro_diario_actividad->Nombre;
        return $registro_diario_actividad;
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
    private function horas_completadas($horas_estimadas, $completado)
    {
        $completado = $completado / 100;
        return $horas_estimadas * $completado;
    }
    private function porcentaje_aux($horas_estimadas, $total_horas_estimadas)
    {
        $cantidad = $horas_estimadas / $total_horas_estimadas;
        $porcentaje = ((float) $cantidad * 100);
        $porcentaje = round($porcentaje, 1); // Quitar los decimales

        return $porcentaje;
    }
    private function porcentaje_aux_completado($porcentaje_horas_trabajadas, $porcentaje_aux)
    {

        $cantidad = $porcentaje_horas_trabajadas / 100; //decimal
        $cantidad = $cantidad * $porcentaje_aux; //multiplicacion por porcentaje_aux
        $porcentaje = round($cantidad, 1); // Quitar los decimales
        return $porcentaje;
    }
    private function porcentaje($total, $cantidad)
    {
        $porcentaje = ((100 * $cantidad) / $total);
        return round($porcentaje, 0);
    }

    public function select2_company(Request $request)
    {
        if (!isset($request->searchTerm)) {
            $empresas = DB::table('empresas')
                ->orderBy('empresas.Nombre', 'ASC')
                ->get();
        } else {
            $empresas = DB::table('empresas')
                ->where('empresas.Nombre', 'like', '%' . $request->searchTerm . '%')
                ->orderBy('empresas.Nombre', 'ASC')
                ->get();
        }
        $data = [];
        foreach ($empresas as $row) {
            $data[] = array(
                "id" => $row->Emp_ID,
                "text" => "$row->Nombre",
            );
        }
        return response()->json($data);
    }

    public function view_detail_proyecto($proyecto_id)
    {
        $detalle = $this->proyectos($proyecto_id);
        return response()->json($detalle, 200);
    }
    public function list_proyectos($id)
    {
        if ($id == 'all') {
            $id = false;
        }
        $proyectos = DB::table('proyectos')
            ->when($id, function ($query) use ($id) {
                return $query->where('proyectos.Estatus_ID', $id);
            })
            ->orderBy('proyectos.Nombre', 'ASC')
            ->get();
        foreach ($proyectos as $key => $proyecto) {
            $logitud = strlen($proyecto->Nombre);
            if ($logitud > 30) {
                $proyecto->Nombre = substr($proyecto->Nombre, 0, 35) . '...';
            }
        }
        return response()->json($proyectos, 200);
    }
}
