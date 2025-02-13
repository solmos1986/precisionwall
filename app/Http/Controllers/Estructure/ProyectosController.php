<?php

namespace App\Http\Controllers\Estructure;

use App\Http\Controllers\Controller;
use DB;
use Illuminate\Http\Request;
use Validator;

//use stdClass;
class ProyectosController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    private function calculo_horas_estimadas($tareas)
    {
        foreach ($tareas as $key => $tarea) {
            $registro_diario_actividad = DB::table('registro_diario_actividad')
                ->select(
                    DB::raw("SUM(registro_diario_actividad.Horas_Contract) as horas_usadas"),
                )
                ->where('Task_ID', $tarea->Task_ID)
                ->first();
            /*   $update_tarea = DB::table('task')
            ->where('task.Task_ID', $tarea->Task_ID)
            ->update([
            'Total_HCode' => $registro_diario_actividad->horas_usadas,
            ]); */
            $tarea->horas_usadas = $registro_diario_actividad->horas_usadas;
        }
        return $tareas;
    }
    private function detalle_proyectos($proyectos, $status, $from_date, $to_date, $cargo, $filtro = false)
    {
        $list_proyectos = DB::table('proyectos')
            ->select(
                'proyectos.*',
                'estatus.Nombre_Estatus as Nombre_Estatus',
                DB::raw("CONCAT( project_manager.Nombre,' ', project_manager.Apellido_Paterno,' ',project_manager.Apellido_Materno) as nombre_project_manager"),
                DB::raw("CONCAT( foreman.Nombre,' ', foreman.Apellido_Paterno,' ',foreman.Apellido_Materno) as nombre_foreman"),
            )
            ->when(!is_null($proyectos), function ($query) use ($proyectos) {
                return $query->whereIn('proyectos.Pro_ID', $proyectos);
            })
            ->when($status, function ($query) use ($status) {
                return $query->where('proyectos.Estatus_ID', $status);
            })
            ->when($to_date, function ($query) use ($to_date, $from_date) {
                return $query->whereBetween('proyectos.Fecha_Inicio', [date('Y-m-d', strtotime($from_date)), date('Y-m-d', strtotime($to_date))]);
            })
            ->when($filtro, function ($query) use ($cargo, $filtro) {
                switch ($cargo) {
                    case 'foreman':
                        return $query->where('proyectos.Foreman_ID', $filtro);
                        break;
                    case 'pm':
                        return $query->where('proyectos.Manager_ID', $filtro);
                        break;
                    case 'super':
                        return $query->where('proyectos.Coordinador_Obra_ID', $filtro);
                        break;
                    case 'APM':
                        return $query->where('proyectos.Manager_ID', $filtro);
                        break;
                    default:
                        # code...
                        break;
                }
            })
            ->join('personal as project_manager', 'project_manager.Empleado_ID', 'proyectos.Manager_ID')
            ->join('personal as foreman', 'foreman.Empleado_ID', 'proyectos.Foreman_ID')
            ->join('estatus', 'estatus.Estatus_ID', 'proyectos.Estatus_ID')
            ->orderBy('proyectos.Nombre', 'ASC')
            ->get();
        foreach ($list_proyectos as $key => $proyecto) {
            $edificios = DB::table('edificios')
                ->where('edificios.Pro_ID', $proyecto->Pro_ID)
                ->orderBy('edificios.Nombre', 'ASC')
                ->get();
            foreach ($edificios as $key => $edificio) {
                $floors = DB::table('floor')
                    ->where('floor.Edificio_ID', $edificio->Edificio_ID)
                    ->orderBy('floor.Nombre', 'ASC')
                    ->get();
                foreach ($floors as $key => $floor) {
                    $area_control = DB::table('area_control')
                        ->select(
                            'area_control.*',
                            //DB::raw("SUM(task.Total_HCode) as Total_HCode")
                        )
                    //->join('task', 'task.Area_ID', 'area_control.Area_ID')
                        ->where('area_control.Floor_ID', $floor->Floor_ID)
                        ->orderBy('area_control.Nombre', 'ASC')
                        ->get();
                    //dd($area_control);
                    foreach ($area_control as $key => $area) {
                        $tasks = DB::table('task')
                            ->where('task.Area_ID', $area->Area_ID)
                            ->orderBy('task.Tas_IDT', 'ASC')
                            ->get();

                        //adicion de horas estimadas
                        $tasks = $this->calculo_horas_estimadas($tasks);
                        $area->task = $tasks;

                        ///calculo de horas estimadas
                        $horas_estimado_tarea = 0;
                        foreach ($tasks as $key => $task) {
                            $horas_estimado_tarea += $task->horas_usadas;
                        }
                        $area->horas_usadas = $horas_estimado_tarea;
                    }
                    $floor->area_control = $area_control;

                    ///calculo de horas estimadas
                    $horas_estimado_area = 0;
                    foreach ($floor->area_control as $key => $area) {
                        $horas_estimado_area += $area->horas_usadas;
                    }
                    $floor->horas_usadas = $horas_estimado_area;
                }
                $edificio->floors = $floors;

                ///calculo de horas estimadas
                $horas_estimado_floor = 0;
                foreach ($edificio->floors as $key => $floor) {
                    $horas_estimado_floor += $floor->horas_usadas;
                }
                $edificio->horas_usadas = $horas_estimado_floor;
            }
            $proyecto->edificios = $edificios;

            ///calculo de horas estimadas
            $horas_estimado_edificio = 0;
            foreach ($proyecto->edificios as $key => $edificio) {
                $horas_estimado_edificio += $edificio->horas_usadas;
            }
            $proyecto->horas_usadas = $horas_estimado_edificio;
        }
        return $list_proyectos;
    }
    private function list_proyectos($proyectos, $status, $from_date, $to_date, $cargo, $filtro = false)
    {
        $list_proyectos = DB::table('proyectos')
            ->select(
                'proyectos.*',
                'estatus.Nombre_Estatus as Nombre_Estatus',
                DB::raw("CONCAT( project_manager.Nombre,' ', project_manager.Apellido_Paterno,' ',project_manager.Apellido_Materno) as nombre_project_manager"),
                DB::raw("CONCAT( foreman.Nombre,' ', foreman.Apellido_Paterno,' ',foreman.Apellido_Materno) as nombre_foreman"),
            )
            ->when(!is_null($proyectos), function ($query) use ($proyectos) {
                return $query->whereIn('proyectos.Pro_ID', $proyectos);
            })
            ->when($status, function ($query) use ($status) {
                return $query->where('proyectos.Estatus_ID', $status);
            })
            ->when($to_date, function ($query) use ($to_date, $from_date) {
                return $query->whereBetween('proyectos.Fecha_Inicio', [date('Y-m-d', strtotime($from_date)), date('Y-m-d', strtotime($to_date))]);
            })
            ->when($filtro, function ($query) use ($cargo, $filtro) {
                switch ($cargo) {
                    case 'foreman':
                        return $query->where('proyectos.Foreman_ID', $filtro);
                        break;
                    case 'pm':
                        return $query->where('proyectos.Manager_ID', $filtro);
                        break;
                    case 'super':
                        return $query->where('proyectos.Coordinador_Obra_ID', $filtro);
                        break;
                    case 'APM':
                        return $query->where('proyectos.Manager_ID', $filtro);
                        break;
                    default:
                        # code...
                        break;
                }
            })
            ->join('personal as project_manager', 'project_manager.Empleado_ID', 'proyectos.Manager_ID')
            ->join('personal as foreman', 'foreman.Empleado_ID', 'proyectos.Foreman_ID')
            ->join('estatus', 'estatus.Estatus_ID', 'proyectos.Estatus_ID')
            ->orderBy('proyectos.Nombre', 'ASC')
            ->get();
        foreach ($list_proyectos as $key => $proyecto) {
            $edificios = DB::table('edificios')
                ->where('edificios.Pro_ID', $proyecto->Pro_ID)
                ->get();
            foreach ($edificios as $key => $edificio) {
                $floors = DB::table('floor')
                    ->where('floor.Edificio_ID', $edificio->Edificio_ID)
                    ->orderBy('floor.Nombre', 'ASC')
                    ->get();
                foreach ($floors as $key => $floor) {
                    $area_control = DB::table('area_control')
                        ->select(
                            'area_control.*',
                            //DB::raw("SUM(task.Total_HCode) as Total_HCode")
                        )
                    //->join('task', 'task.Area_ID', 'area_control.Area_ID')
                        ->where('area_control.Floor_ID', $floor->Floor_ID)
                        ->orderBy('area_control.Nombre', 'ASC')
                        ->get();
                    //dd($area_control);
                    /* foreach ($area_control as $key => $area) {
                    $task = DB::table('task')
                    ->where('task.Area_ID', $area->Area_ID)
                    ->get();

                    //adicion de horas estimadas
                    $task = $this->calculo_horas_estimadas($task);
                    $area->task = $task;
                    } */
                    $floor->area_control = $area_control;
                }
                $edificio->floors = $floors;
            }
            $proyecto->edificios = $edificios;
        }
        return $list_proyectos;
    }
    public function proyectos_list(Request $request)
    {
        $data = $this->list_proyectos($request->multiselect_project, $request->status, $request->from_date, $request->to_date, $request->cargo, $request->filtro);
        return response()->json($data, 200);
    }
    //!estructura de proyectos
    public function proyectos_detalle(Request $request)
    {
        $data = $this->detalle_proyectos($request->multiselect_project, $request->status, $request->from_date, $request->to_date, $request->cargo, $request->filtro);

        return response()->json($data, 200);
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
        return view('panel.proyectos.estructura_jobs', compact('status', 'proyectos'));
    }

    //!cambiando estrucutura a floor
    public function update_floor(Request $request)
    {
        $check = DB::table('floor')
            ->select(
                'floor.*',
            )
            ->where('floor.Floor_ID', $request->floor_id)
            ->first();
        $this->modificar_floor($request->floor_id, $request->edificio_id);
        //aux
        $edificios = DB::table('edificios')
            ->where('edificios.Pro_ID', $check->Pro_ID)
            ->orderBy('edificios.Nombre', 'ASC')
            ->get();
        //edificios
        foreach ($edificios as $key => $edificio) {
            $floors = DB::table('floor')
                ->where('floor.Edificio_ID', $edificio->Edificio_ID)
                ->orderBy('floor.Nombre', 'ASC')
                ->get();
            foreach ($floors as $key => $floor) {
                $area_control = DB::table('area_control')
                    ->where('area_control.Floor_ID', $floor->Floor_ID)
                    ->orderBy('area_control.Nombre', 'ASC')
                    ->get();
                foreach ($area_control as $key => $area) {
                    $tasks = DB::table('task')
                        ->where('task.Area_ID', $area->Area_ID)
                        ->orderBy('task.Tas_IDT', 'ASC')
                        ->get();
                    $tasks = $this->calculo_horas_estimadas($tasks);
                    $area->task = $tasks;

                    ///calculo de horas estimadas
                    $horas_estimado_tarea = 0;
                    foreach ($tasks as $key => $task) {
                        $horas_estimado_tarea += $task->horas_usadas;
                    }
                    $area->horas_usadas = $horas_estimado_tarea;
                }
                $floor->area_control = $area_control;

                ///calculo de horas estimadas
                $horas_estimado_area = 0;
                foreach ($floor->area_control as $key => $area) {
                    $horas_estimado_area += $area->horas_usadas;
                }
                $floor->horas_usadas = $horas_estimado_area;
            }
            $edificio->floors = $floors;

            ///calculo de horas estimadas
            $horas_estimado_floor = 0;
            foreach ($edificio->floors as $key => $floor) {
                $horas_estimado_floor += $floor->horas_usadas;
            }
            $edificio->horas_usadas = $horas_estimado_floor;
        }
        return response()->json([
            'status' => 'ok',
            'data' => [
                'edificios' => $edificios,
                'proyecto_id' => $check->Pro_ID,
            ],
            'message' => 'Successful change of area',
        ], 200);
    }

    private function modificar_floor($floor_id, $edificio_id)
    {
        //cambiando tareas
        $areas_control = DB::table('area_control')
            ->where('area_control.Floor_ID', $floor_id)
            ->get();
        //*modificando areas
        /* foreach ($areas_control as $key => $area) {
        $areas_control_update = DB::table('areas_control')
        ->where('areas_control.Area_ID', $area->Area_ID)
        ->update([
        'Floor_ID' => $floor_id,
        ]);
        $tareas = DB::table('areas_control')
        ->where('areas_control.Area_ID', $area->Area_ID)
        ->get();
        //*modificando areas
        foreach ($tareas as $key => $tarea) {
        $tarea = DB::table('task')
        ->where('task.Area_ID', $area_id)
        ->update([
        'Floor_ID' => $floor_id,
        ]);
        }
        } */
        $area_id = DB::table('floor')->where('Floor_ID', $floor_id)
            ->update([
                'Edificio_ID' => $edificio_id,
            ]);
    }
    //!cambiando estrucutura a area
    public function update_area(Request $request)
    {
        $check = $this->data_floor($request->floor_id);
        $this->modificar_area($request->area_id, $request->floor_id);
        return response()->json([
            'status' => 'ok',
            'data' => $this->view_estructura_floor($check),
            'message' => 'Successful change of area',
        ], 200);
    }
    private function modificar_area($area_id, $floor_id)
    {
        //cambiando tareas
        $tareas = DB::table('task')
            ->where('task.Area_ID', $area_id)
            ->get();
        //*modificando tareas
        foreach ($tareas as $key => $tarea) {
            $tarea = DB::table('task')
                ->where('task.Area_ID', $area_id)
                ->update([
                    'Floor_ID' => $floor_id,
                ]);
        }
        $area_id = DB::table('area_control')->where('Area_ID', $area_id)
            ->update([
                'Floor_ID' => $floor_id,
            ]);
    }
    //!cambiando estrucutura a task
    public function update_task(Request $request)
    {
        $check = $this->data_task($request->task_id);
        $this->modificar_task($request->task_id, $request->area_id);
        //validacion para buscar datos
        $buscando_floors = DB::table('floor')
            ->where('floor.Floor_ID', $check->Floor_ID)
            ->first();
        return response()->json([
            'status' => 'ok',
            'data' => $this->view_estructura_area($check),
            'message' => 'Successful change of area',
        ], 200);
    }
    private function modificar_task($task_id, $area_id)
    {
        //cambiando tareas
        $tareas = DB::table('task')
            ->where('task.Task_ID', $task_id)
            ->first();
        //*modificando tareas
        $area_id = DB::table('task')->where('Task_ID', $task_id)
            ->update([
                'Area_ID' => $area_id,
            ]);
    }
    private function cambios_porcentaje($tarea)
    {
        $tareas = DB::table('task')
            ->where('task.Task_ID', $tarea->Task_ID)
            ->update([
                'B_Last_Date' => $tarea->Last_Date_Per_Recorded,
                'B_Last_Percentage' => $tarea->Last_Per_Recorded,
                'BUsr' => $tarea->Usr,
            ]);
    }
    /*
    !cambios en estrucutra
     *task
     */
    public function import_task_update(Request $request)
    {
        switch ($request->input) {
            case 'completado':
                return $this->modificando_estructura_porcentaje($request->task_id, $request->valor, $request->tipo, $request->id);
                break;
            case 'Horas_Estimadas':
                return $this->modificando_estructura_horas_estimadas($request->task_id, $request->valor, $request->tipo, $request->id);
                break;
            case 'Tas_IDT':
                return $this->modificando_estructura_Tas_IDT($request->task_id, $request->valor, $request->tipo, $request->id);
                break;
            case 'precio_total':
                return $this->modificando_estructura_precio_total($request->task_id, $request->valor, $request->tipo, $request->id);
                break;
            case 'ActAre':
                return $this->modificando_estructura_ActAre($request->task_id, $request->valor, $request->tipo, $request->id);
                break;
            case 'ActTas':
                return $this->modificando_estructura_ActTas($request->task_id, $request->valor, $request->tipo, $request->id);
                break;
            case 'cc_butdget_qty':
                return $this->modificando_estructura_cc_butget_ytq($request->task_id, $request->valor, $request->tipo, $request->id);
                break;
            case 'um':
                return $this->modificando_estructura_um($request->task_id, $request->valor, $request->tipo, $request->id);
                break;
            case 'nombre_task':
                return $this->modificando_estructura_nombre_task($request->task_id, $request->valor, $request->tipo, $request->id);
                break;
            case 'Material_Estimado':
                return $this->modificando_estructura_Material_Estimado($request->task_id, $request->valor, $request->tipo, $request->id);
                break;
            default:
                # code...
                break;
        }
    }
    private function data_task($task_id)
    {
        $check = DB::table('task')
            ->select(
                'task.*',
            )
            ->where('task.Task_ID', $task_id)
            ->first();
        return $check;
    }
    private function view_estructura_task($tarea)
    {
        $task = DB::table('task')
            ->select(
                'task.*',
            )
            ->orderBy('task.Tas_IDT', 'ASC')
            ->where('task.Area_ID', $tarea->Area_ID)
            ->get();
        $task = $this->calculo_horas_estimadas($task);
        $task_ids = [];
        foreach ($task as $key => $tarea) {
            $task_ids[] = $tarea->Task_ID;
        }
        $areas = DB::table('area_control')
            ->select(
                'area_control.*',
            )
            ->where('area_control.Floor_ID', $tarea->Floor_ID)
            ->orderBy('area_control.Nombre', 'ASC')
            ->get();
        return [
            'task' => $task,
            'areas' => $areas,
            'area_id' => $tarea->Area_ID,
        ];
    }
    private function modificando_estructura_porcentaje($task_id, $valor, $tipo, $id)
    {
        $check = $this->data_task($task_id);
        $this->cambios_porcentaje($check);
        $update = DB::table('task')
            ->where('task.Task_ID', $task_id)
            ->update([
                'Last_Date_Per_Recorded' => date('Y-m-d'),
                'Last_Per_Recorded' => $valor,
                'precio_segun_avance' => ($check->precio_total * ($valor / 100)),
                'Usr' => auth()->user()->Nombre . ' ' . auth()->user()->Apellido_Paterno . '' . auth()->user()->Apellido_Materno,
            ]);

        if ($update) {
            return response()->json([
                'status' => 'ok',
                'data' => $this->view_estructura_task($check),
                'message' => 'Percentage Successfully modified',
            ], 200);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred',
            ], 200);
        }
    }
    private function modificando_estructura_horas_estimadas($task_id, $valor, $tipo, $id)
    {
        $check = $this->data_task($task_id);
        $update = DB::table('task')
            ->where('task.Task_ID', $task_id)
            ->update([
                'Horas_Estimadas' => $valor,
            ]);

        if ($update) {
            return response()->json([
                'status' => 'ok',
                'data' => $this->view_estructura_task($check),
                'message' => 'Estimated Hours Successfully modified',
            ], 200);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred',
            ], 200);
        }
    }
    private function modificando_estructura_precio_total($task_id, $valor, $tipo, $id)
    {
        $check = $this->data_task($task_id);
        $update = DB::table('task')
            ->where('task.Task_ID', $task_id)
            ->update([
                'precio_total' => $valor,
                'precio_segun_avance' => ($valor * ($check->Last_Per_Recorded / 100)),
            ]);

        if ($update) {
            return response()->json([
                'status' => 'ok',
                'data' => $this->view_estructura_task($check),
                'message' => 'Price total Successfully modified',
            ], 200);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred',
            ], 200);
        }
    }
    private function modificando_estructura_cc_butget_ytq($task_id, $valor, $tipo, $id)
    {
        $check = $this->data_task($task_id);
        $update = DB::table('task')
            ->where('task.Task_ID', $task_id)
            ->update([
                'cc_butdget_qty' => $valor,
            ]);
        if ($update) {
            return response()->json([
                'status' => 'ok',
                'data' => $this->view_estructura_task($check),
                'message' => 'cc butdget qty Successfully modified',
            ], 200);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred',
            ], 200);
        }
    }
    private function modificando_estructura_um($task_id, $valor, $tipo, $id)
    {
        $check = $this->data_task($task_id);
        $update = DB::table('task')
            ->where('task.Task_ID', $task_id)
            ->update([
                'um' => $valor,
            ]);

        if ($update) {
            return response()->json([
                'status' => 'ok',
                'data' => $this->view_estructura_task($check),
                'message' => 'Um Successfully modified',
            ], 200);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred',
            ], 200);
        }
    }
    private function modificando_estructura_Tas_IDT($task_id, $valor, $tipo, $id)
    {
        $check = $this->data_task($task_id);
        $update = DB::table('task')
            ->where('task.Task_ID', $task_id)
            ->update([
                'Tas_IDT' => $valor,
                'NumAct' => "$check->ActAre $valor",
                'ActTas' => $valor,
            ]);

        if ($update) {
            return response()->json([
                'status' => 'ok',
                'data' => $this->view_estructura_task($check),
                'message' => 'Cost code Successfully modified',
            ], 200);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred',
            ], 200);
        }
    }
    private function modificando_estructura_ActAre($task_id, $valor, $tipo, $id)
    {
        $check = $this->data_task($task_id);
        $update = DB::table('task')
            ->where('task.Task_ID', $task_id)
            ->update([
                'ActAre' => $valor,
                'NumAct' => "$valor $check->ActTas",
            ]);

        if ($update) {
            return response()->json([
                'status' => 'ok',
                'data' => $this->view_estructura_task($check),
                'message' => 'Ac Area Successfully modified',
            ], 200);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred',
            ], 200);
        }
    }
    private function modificando_estructura_ActTas($task_id, $valor, $tipo, $id)
    {
        $check = $this->data_task($task_id);
        $update = DB::table('task')
            ->where('task.Task_ID', $task_id)
            ->update([
                'ActTas' => $valor,
                'NumAct' => "$check->ActAre $valor",
                'Tas_IDT' => $valor,
            ]);

        if ($update) {
            return response()->json([
                'status' => 'ok',
                'data' => $this->view_estructura_task($check),
                'message' => 'Ac Task Successfully modified',
            ], 200);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred',
            ], 200);
        }
    }
    private function modificando_estructura_nombre_task($task_id, $valor, $tipo, $id)
    {
        $check = $this->data_task($task_id);
        $update = DB::table('task')
            ->where('task.Task_ID', $task_id)
            ->update([
                'Nombre' => $valor,
            ]);

        if ($update) {
            return response()->json([
                'status' => 'ok',
                'data' => $this->view_estructura_task($check),
                'message' => 'Name Task Successfully modified',
            ], 200);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred',
            ], 200);
        }
    }
    private function modificando_estructura_Material_Estimado($task_id, $valor, $tipo, $id)
    {
        $check = $this->data_task($task_id);
        $update = DB::table('task')
            ->where('task.Task_ID', $task_id)
            ->update([
                'Material_Estimado' => $valor,
            ]);

        if ($update) {
            return response()->json([
                'status' => 'ok',
                'data' => $this->view_estructura_task($check),
                'message' => 'Estimate Material Successfully modified',
            ], 200);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred',
            ], 200);
        }
    }
    /*
    !cambios en estrucutra
     *area
     */
    private function data_area($area_id)
    {
        $area = DB::table('area_control')
            ->select(
                'area_control.*',
                'floor.Edificio_ID'
            )
            ->join('floor', 'floor.Floor_ID', 'area_control.Floor_ID')
            ->where('area_control.Area_ID', $area_id)
            ->first();
        return $area;
    }
    private function view_estructura_area($area)
    {
        $buscando_floors = DB::table('floor')
            ->where('floor.Floor_ID', $area->Floor_ID)
            ->first();
        //floors
        $floors = DB::table('floor')
            ->where('floor.Edificio_ID', $buscando_floors->Edificio_ID)
            ->orderBy('floor.Nombre', 'ASC')
            ->get();

        $area_control = DB::table('area_control')
            ->where('area_control.Floor_ID', $buscando_floors->Floor_ID)
            ->orderBy('area_control.Nombre', 'ASC')
            ->get();
        foreach ($area_control as $key => $area) {
            $tasks = DB::table('task')
                ->where('task.Area_ID', $area->Area_ID)
                ->orderBy('task.Tas_IDT', 'ASC')
                ->get();
            $tasks = $this->calculo_horas_estimadas($tasks);
            $area->task = $tasks;

            ///calculo de horas estimadas
            $horas_estimado_tarea = 0;
            foreach ($tasks as $key => $task) {
                $horas_estimado_tarea += $task->horas_usadas;
            }
            $area->horas_usadas = $horas_estimado_tarea;
        }

        return [
            'floors' => $floors,
            'floor_id' => $area->Floor_ID,
            'areas' => $area_control,
        ];
    }
    public function import_area_update(Request $request)
    {
        switch ($request->input) {
            case 'Are_IDT':
                return $this->modificando_Are_IDT($request->area_id, $request->valor, $request->tipo, $request->id);
                break;
            case 'nombre_area':
                return $this->modificando_nombre_area($request->area_id, $request->valor, $request->tipo, $request->id);
                break;
            default:
                # code...
                break;
        }
    }
    private function modificando_Are_IDT($area_id, $valor, $tipo, $id)
    {
        $check = $this->data_area($area_id);
        $update = DB::table('area_control')
            ->where('area_control.Area_ID', $area_id)
            ->update([
                'Are_IDT' => $valor,
            ]);
        if ($update) {
            return response()->json([
                'status' => 'ok',
                'data' => $this->view_estructura_area($check),
                'message' => 'Cod Successfully modified',
            ], 200);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred',
            ], 200);
        }
    }
    private function modificando_nombre_area($area_id, $valor, $tipo, $id)
    {
        $check = $this->data_area($area_id);
        $update = DB::table('area_control')
            ->where('area_control.Area_ID', $area_id)
            ->update([
                'Nombre' => $valor,
            ]);
        if ($update) {
            return response()->json([
                'status' => 'ok',
                'data' => $this->view_estructura_area($check),
                'message' => 'Name Successfully modified',
            ], 200);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred',
            ], 200);
        }
    }
    /*
    !cambios en estrucutra
     *floor
     */
    public function import_floor_update(Request $request)
    {
        switch ($request->input) {
            case 'nombre_floor':
                return $this->modificando_nombre_floor($request->floor_id, $request->valor, $request->tipo, $request->id);
                break;
            default:
                # code...
                break;
        }
    }
    private function data_floor($floor_id)
    {
        $floor = DB::table('floor')
            ->where('floor.Floor_ID', $floor_id)
            ->first();
        return $floor;
    }
    private function view_estructura_floor($floor)
    {
        $edificios = DB::table('edificios')
            ->where('edificios.Pro_ID', $floor->Pro_ID)
            ->orderBy('edificios.Nombre', 'ASC')
            ->get();
        $floors = DB::table('floor')
            ->where('floor.Edificio_ID', $floor->Edificio_ID)
            ->orderBy('floor.Nombre', 'ASC')
            ->get();
        foreach ($floors as $key => $floor) {
            $area_control = DB::table('area_control')
                ->where('area_control.Floor_ID', $floor->Floor_ID)
                ->orderBy('area_control.Nombre', 'ASC')
                ->get();
            foreach ($area_control as $key => $area) {
                $tasks = DB::table('task')
                    ->where('task.Area_ID', $area->Area_ID)
                    ->orderBy('task.Tas_IDT', 'ASC')
                    ->get();
                $tasks = $this->calculo_horas_estimadas($tasks);
                $area->task = $tasks;

                ///calculo de horas estimadas
                $horas_estimado_tarea = 0;
                foreach ($tasks as $key => $task) {
                    $horas_estimado_tarea += $task->horas_usadas;
                }
                $area->horas_usadas = $horas_estimado_tarea;
            }
            $floor->area_control = $area_control;

            ///calculo de horas estimadas
            $horas_estimado_area = 0;
            foreach ($floor->area_control as $key => $area) {
                $horas_estimado_area += $area->horas_usadas;
            }
            $floor->horas_usadas = $horas_estimado_area;
        }

        return [
            'floors' => $floors,
            'edificios' => $edificios,
            'edificio_id' => $floor->Edificio_ID,
        ];
    }
    private function modificando_nombre_floor($floor_id, $valor, $tipo, $id)
    {
        $check = $this->data_floor($floor_id);
        $update = DB::table('floor')
            ->where('floor.Floor_ID', $floor_id)
            ->update([
                'Nombre' => $valor,
            ]);
        //devolucion de vista

        if ($update) {
            return response()->json([
                'status' => 'ok',
                'data' => $this->view_estructura_floor($check),
                'message' => 'Name Successfully modified',
            ], 200);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred',
            ], 200);
        }
    }
    /*
    !cambios en estrucutra
     *edificio
     */
    private function data_edificio($edificio_id)
    {
        $edificio = DB::table('edificios')
            ->where('edificios.Edificio_ID', $edificio_id)
            ->first();
        return $edificio;
    }
    private function view_estructura_edificio($edificio)
    {
        $edificios = DB::table('edificios')
            ->where('edificios.Pro_ID', $edificio->Pro_ID)
            ->orderBy('edificios.Nombre', 'ASC')
            ->get();
        foreach ($edificios as $key => $edificio) {
            $floors = DB::table('floor')
                ->where('floor.Edificio_ID', $edificio->Edificio_ID)
                ->orderBy('floor.Nombre', 'ASC')
                ->get();
            foreach ($floors as $key => $floor) {
                $area_control = DB::table('area_control')
                    ->where('area_control.Floor_ID', $floor->Floor_ID)
                    ->orderBy('area_control.Nombre', 'ASC')
                    ->get();
                foreach ($area_control as $key => $area) {
                    $tasks = DB::table('task')
                        ->where('task.Area_ID', $area->Area_ID)
                        ->orderBy('task.Tas_IDT', 'ASC')
                        ->get();
                    $tasks = $this->calculo_horas_estimadas($tasks);
                    $area->task = $tasks;

                    ///calculo de horas estimadas
                    $horas_estimado_tarea = 0;
                    foreach ($tasks as $key => $task) {
                        $horas_estimado_tarea += $task->horas_usadas;
                    }
                    $area->horas_usadas = $horas_estimado_tarea;
                }
                $floor->area_control = $area_control;

                ///calculo de horas estimadas
                $horas_estimado_area = 0;
                foreach ($floor->area_control as $key => $area) {
                    $horas_estimado_area += $area->horas_usadas;
                }
                $floor->horas_usadas = $horas_estimado_area;
            }
            $edificio->floors = $floors;

            ///calculo de horas estimadas
            $horas_estimado_floor = 0;
            foreach ($edificio->floors as $key => $floor) {
                $horas_estimado_floor += $floor->horas_usadas;
            }
            $edificio->horas_usadas = $horas_estimado_floor;
        }
        return [
            'edificios' => $edificios,
            'proyecto_id' => $edificio->Pro_ID,
        ];
    }
    public function import_edificio_update(Request $request)
    {
        switch ($request->input) {
            case 'nombre_edifico':
                return $this->modificando_nombre_edificio($request->edificio_id, $request->valor, $request->tipo, $request->id);
                break;
            case 'descripcion_edificio':
                return $this->modificando_edificio_descripcion($request->edificio_id, $request->valor, $request->tipo, $request->id);
                break;
            default:
                # code...
                break;
        }
    }
    private function modificando_nombre_edificio($edificio_id, $valor, $tipo, $id)
    {

        $update = DB::table('edificios')
            ->where('edificios.Edificio_ID', $edificio_id)
            ->update([
                'Nombre' => $valor,
            ]);
        $check = $this->data_edificio($edificio_id);
        if ($update) {
            return response()->json([
                'status' => 'ok',
                'data' => $this->view_estructura_edificio($check),
                'message' => 'Name Successfully modified',
            ], 200);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred',
            ], 200);
        }
    }
    private function modificando_edificio_descripcion($edificio_id, $valor, $tipo, $id)
    {

        $update = DB::table('edificios')
            ->where('edificios.Edificio_ID', $edificio_id)
            ->update([
                'Descripcion' => $valor,
            ]);
        $check = $this->data_edificio($edificio_id);
        if ($update) {
            return response()->json([
                'status' => 'ok',
                'data' => $this->view_estructura_edificio($check),
                'message' => 'Description Successfully modified',
            ], 200);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred',
            ], 200);
        }
    }
    /*
    !cambios en estrucutra
     *project
     */
    public function import_project_update(Request $request)
    {
        switch ($request->input) {
            case 'horas_estimadas':
                return $this->modificando_horas_estimadas($request->task_id, $request->valor, $request->tipo, $request->id);
                break;
            case 'porcentaje':
                return $this->modificando_porcentaje($request->task_id, $request->valor, $request->tipo, $request->id);
                break;
            case 'precio_total':
                return $this->modificando_precio_total($request->task_id, $request->valor, $request->tipo, $request->id);
                break;
            case 'sov_id':
                return $this->modificando_sov_id($request->task_id, $request->valor, $request->tipo, $request->id);
                break;
            case 'sov_descripcion':
                return $this->modificando_sov_descripcion($request->task_id, $request->valor, $request->tipo, $request->id);
                break;
            case 'cc_butdget_qty':
                return $this->modificando_cc_butget_ytq($request->task_id, $request->valor, $request->tipo, $request->id);
                break;
            case 'um':
                return $this->modificando_um($request->task_id, $request->valor, $request->tipo, $request->id);
                break;
            default:
                # code...
                break;
        }
    }
    /*
    !nuevo
     */
    public function store_task(Request $request)
    {
        $rules = array(
            'nuevo_cost_code' => 'required',
            'nuevo_ac_area' => 'required',
            'nuevo_nombre_task' => 'required',
            'nuevo_precio_total' => 'numeric',
            'nuevo_completado' => 'numeric',
        );
        $messages = [
            'nuevo_cost_code.required' => "The Cost Code field is required",
            'nuevo_ac_area.required' => "The Ac Area field is required",
            'nuevo_nombre_task.required' => "The Name field is required",
            'nuevo_precio_total.numeric' => "The Price Total field is numeric",
            'nuevo_completado.numeric' => "The % Completed field is numeric",
        ];
        $error = Validator::make($request->all(), $rules, $messages);

        if ($error->errors()->all()) {
            return response()->json([
                'status' => 'errors',
                'message' => $error->errors()->all(),
            ]);
        }
        $data_area = DB::table('area_control')
            ->where('area_control.Area_ID', $request->area_id)
            ->first();

        $nuevo = DB::table('task')
            ->insert([
                'Pro_ID' => $data_area->Pro_ID,
                'Floor_ID' => $data_area->Floor_ID,
                'Area_ID' => $request->area_id,
                'Tas_IDT' => $request->nuevo_cost_code,
                'ActAre' => $request->nuevo_ac_area,
                'ActTas' => $request->nuevo_act_Tas,
                'NumAct' => "$request->nuevo_ac_area $request->nuevo_act_Tas",
                'Nombre' => $request->nuevo_nombre_task,
                'Horas_Estimadas' => $request->nuevo_Horas_Estimadas,
                'Material_Estimado' => $request->nuevo_Material_Estimado,
                'cc_butdget_qty' => $request->nuevo_cc_butdget_qty,
                'precio_total' => $request->nuevo_precio_total,
                'precio_segun_avance' => ($request->nuevo_precio_total * ($request->nuevo_completado / 100)),
                'Last_Date_Per_Recorded' => date('Y-m-d'),
                'Last_Per_Recorded' => $request->nuevo_completado,
                'Usr' => auth()->user()->Nombre . ' ' . auth()->user()->Apellido_Paterno . '' . auth()->user()->Apellido_Materno,
            ]);
        if ($nuevo) {
            return response()->json([
                'status' => 'ok',
                'data' => $this->view_estructura_task($data_area),
                'message' => 'Task Successfully modified',
            ], 200);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred',
            ], 200);
        }
    }
    public function delete_task(Request $request)
    {
        $check = $this->data_task($request->task_id);
        $delete = DB::table('task')
            ->where('task.Task_ID', $request->task_id)
            ->delete();
        if ($delete) {
            return response()->json([
                'status' => 'ok',
                'data' => $this->view_estructura_task($check),
                'message' => 'Task Successfully delete',
            ], 200);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred',
            ], 200);
        }
    }
    /*
    !areas
     */
    public function store_area(Request $request)
    {
        $rules = array(
            'nuevo_code_area' => 'required',
            'nuevo_nombre_area' => 'nullable',
        );
        $messages = [
            'nuevo_code_area.required' => "The Code Area field is required",
        ];
        $error = Validator::make($request->all(), $rules, $messages);

        if ($error->errors()->all()) {
            return response()->json([
                'status' => 'errors',
                'message' => $error->errors()->all(),
            ]);
        }
        $data_floor = DB::table('floor')
            ->where('floor.Floor_ID', $request->floor_id)
            ->first();

        $nuevo = DB::table('area_control')
            ->insertGetId([
                'Pro_ID' => $data_floor->Pro_ID,
                'Floor_ID' => $data_floor->Floor_ID,
                'Are_IDT' => $request->nuevo_code_area,
                'Nombre' => $request->nuevo_nombre_area,
            ]);
        $check = $this->data_area($nuevo);
        if ($nuevo) {
            return response()->json([
                'status' => 'ok',
                'data' => $this->view_estructura_area($check),
                'message' => 'Area Successfully create',
            ], 200);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred',
            ], 200);
        }
    }
    public function delete_area(Request $request)
    {
        $check = $this->data_area($request->area_id);
        //verificando si hay areas
        $task = DB::table('task')
            ->where('task.Area_ID', $request->area_id)
            ->get();
        if (count($task) == 0) {
            $delete = DB::table('area_control')
                ->where('area_control.Area_ID', $request->area_id)
                ->delete();
            if ($delete) {
                return response()->json([
                    'status' => 'ok',
                    'data' => $this->view_estructura_area($check),
                    'message' => 'Area Successfully delete',
                ], 200);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'An error occurred',
                ], 200);
            }
        } else {
            return response()->json([
                'status' => 'error',
                'data' => $this->view_estructura_area($check),
                'message' => 'This area contains task',
            ], 200);
        }
    }
    /*
    !floor
     */
    public function store_floor(Request $request)
    {
        $rules = array(
            'nuevo_nombre_floor' => 'required',
        );
        $messages = [
            'nuevo_nombre_floor.required' => "The Name field is required",
        ];
        $error = Validator::make($request->all(), $rules, $messages);

        if ($error->errors()->all()) {
            return response()->json([
                'status' => 'errors',
                'message' => $error->errors()->all(),
            ]);
        }
        $data_floor = DB::table('edificios')
            ->where('edificios.Edificio_ID', $request->edificio_id)
            ->first();
        $nuevo = DB::table('floor')
            ->insertGetId([
                'Pro_ID' => $data_floor->Pro_ID,
                'Edificio_ID' => $request->edificio_id,
                'Nombre' => $request->nuevo_nombre_floor,
            ]);
        $check = $this->data_floor($nuevo);
        if ($nuevo) {
            return response()->json([
                'status' => 'ok',
                'data' => $this->view_estructura_floor($check),
                'message' => 'Floor Successfully create',
            ], 200);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred',
            ], 200);
        }
    }
    public function delete_floor(Request $request)
    {
        $check = $this->data_floor($request->floor_id);
        //verificar si tiene areas
        $areas = DB::table('area_control')
            ->where('area_control.Floor_ID', $request->floor_id)
            ->get();
        if (count($areas) == 0) {
            $delete = DB::table('floor')
                ->where('floor.Floor_ID', $request->floor_id)
                ->delete();
            if ($delete) {
                return response()->json([
                    'status' => 'ok',
                    'data' => $this->view_estructura_floor($check),
                    'message' => 'Floor Successfully delete',
                ], 200);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'An error occurred',
                ], 200);
            }
        } else {
            return response()->json([
                'status' => 'error',
                'data' => $this->view_estructura_floor($check),
                'message' => 'This floor contains Areas',
            ], 200);
        }
    }
    /*
    !floor
     */
    public function store_edificio(Request $request)
    {
        $rules = array(
            'nuevo_nombre_edificio' => 'required',
        );
        $messages = [
            'nuevo_nombre_edificio.required' => "The Name field is required",
        ];
        $error = Validator::make($request->all(), $rules, $messages);

        if ($error->errors()->all()) {
            return response()->json([
                'status' => 'errors',
                'message' => $error->errors()->all(),
            ]);
        }
        $nuevo = DB::table('edificios')
            ->insertGetId([
                'Pro_ID' => $request->proyecto_id,
                'Descripcion' => $request->nuevo_descripcion_edificio,
                'Nombre' => $request->nuevo_nombre_edificio,
            ]);
        $check = $this->data_edificio($nuevo);
        if ($nuevo) {
            return response()->json([
                'status' => 'ok',
                'data' => $this->view_estructura_edificio($check),
                'message' => 'Building created successfully',
            ], 200);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred',
            ], 200);
        }
    }
    public function delete_edificio(Request $request)
    {
        $check = $this->data_edificio($request->edificio_id);
        //verificar si tiene floor
        $foors = DB::table('floor')
            ->where('floor.Edificio_ID', $request->edificio_id)
            ->get();
        //dd($foors);
        if (count($foors) == 0) {
            $delete = DB::table('edificios')
                ->where('edificios.Edificio_ID', $request->edificio_id)
                ->delete();
            if ($delete) {
                return response()->json([
                    'status' => 'ok',
                    'data' => $this->view_estructura_edificio($check),
                    'message' => 'Floor Successfully delete',
                ], 200);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'An error occurred',
                ], 200);
            }
        } else {
            return response()->json([
                'status' => 'error',
                'data' => $this->view_estructura_floor($check),
                'message' => 'This building contains Floors',
            ], 200);
        }
    }
}
