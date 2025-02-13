<?php

namespace App\Http\Controllers\PayRoll;

use App\Http\Controllers\Controller;
use DataTables;
use DateInterval;
use DatePeriod;
use DateTime;
use DB;
use File;
use Illuminate\Http\Request;
use Storage;
use Validator;
use \stdClass;

class PayRollController extends Controller
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
        return view('panel.Payroll.index');
    }

    public function uploadFileTimerLine(Request $request)
    {
        if (!$request->hasFile('doc_timerLine')) {
            return response()->json(['error' => 'no hay archivo'], 200);
        } else {
            $texto = "";
            $file = request()->file('doc_timerLine');
            $archivo = fopen($file, "r");
            while (!feof($archivo)) {
                $texto .= fgets($archivo);
                // Imprimiendo una linea
            }

            // Cerrando el archivo
            fclose($archivo);
            //dd($texto);
            $texto = $this->limpiarCaracterEspecial($texto);
            $textoToArray = $this->separandoCadena($texto);
            $datos = $this->Analizando($textoToArray);
            $timberLineId = $this->insertData($datos);
            return response()->json([
                'status' => 'ok',
                'message' => 'recibido correctamente',
                'data' => [
                    'timberLineId' => $timberLineId,
                ],
            ], 200);
        }
    }
    public function insertData($datos)
    {
        $insertTimerLine = DB::table('temp_timerline')->insertGetId([
            'descripcion' => 'demo',
            'fechaRegistro' => date('Y-m-d H:i:s'),
        ]);
        foreach ($datos as $key => $dato) {
            $insertData = DB::table('temp_timerline_data')->insertGetId([
                'codigoProyecto' => $dato->codigoProyecto,
                'nombreProyecto' => $dato->nombreProyecto,
                'codigoEdificio' => $dato->codigoEdificio,
                'nombreEdificio' => $dato->nombreEdificio,
                'codigoFloor' => $dato->codigoFloor,
                'nombreFloor' => $dato->nombreFloor,
                'codigoArea' => $dato->codigoArea,
                'nombreArea' => $dato->nombreArea,
                'costCode' => $dato->costCode,
                'nombreTrabajo' => $dato->nombreTrabajo,
                'hours' => $dato->hours,
                'temp_timerline_id' => $insertTimerLine,
            ]);
        }
        return $insertTimerLine;
    }
    private function limpiarCaracterEspecial($cadena)
    {
        //eliminar caracteres especiales
        $resultado = preg_replace("/[\f]+/", "", $cadena);
        $resultado = preg_replace("/[\r\n|\n|\r]+/", "|", $resultado);
        //separado por | return array
        return $resultado;
    }
    private function limpiarEspacios($cadena)
    {
        $resultado = $caden->trim();
        return $resultado;
    }
    private function separandoCadena($cadena)
    {
        //separando por array
        $resultado = explode('|', $cadena);
        return $resultado;
    }
    private function separarComa($cadena)
    {
        $resultado = explode(',', $cadena);
        return $resultado;
    }
    /*analisis */
    private function Analizando($array)
    {
        //campos que se esperan
        $campos = 11;
        $data = [];
        foreach ($array as $key => $text) {
            $inserts = new stdClass;
            $valores = $this->separarComa($text);
            //validar campos necesarios
            if (count($valores) == 11) {

                $inserts->codigoProyecto = trim(str_replace('NL:', '', $valores[0]));
                $inserts->nombreProyecto = trim($valores[1]);
                $inserts->codigoEdificio = trim($valores[2]);
                $inserts->nombreEdificio = trim($valores[3]);
                $inserts->codigoFloor = trim($valores[4]);
                $inserts->nombreFloor = trim($valores[5]);
                $inserts->codigoArea = trim($valores[6]);
                $inserts->nombreArea = trim($valores[7]);
                $inserts->costCode = trim($valores[8]);
                $inserts->nombreTrabajo = trim($valores[9]);
                $inserts->hours = trim($valores[10]);

                $data[] = $inserts;
            }
        }
        return $data;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function datatable_timberline()
    {
        $listaTimerline = DB::table('temp_timerline')->where('estado', 'creado')->get();
        return Datatables::of($listaTimerline)
            ->addIndexColumn()
            ->addColumn('acciones', function ($data) {
                $button = '
                <i class="fa fa-edit ms-text-primary export_timberLine cursor-pointer" data-id="' . $data->id . '" data-descripcion="' . $data->descripcion . '"  title="Export TimerLine"></i>
                <i class="far fa-trash-alt ms-text-danger delete_timberLine cursor-pointer" data-id="' . $data->id . '" data-descripcion="' . $data->descripcion . '"  title="Delete TimberLine"></i>
                ';
                return $button;
            })
            ->editColumn('fechaRegistro', function ($data) {
                return $data->fechaRegistro ? date('m/d/Y H:i:s', strtotime($data->fechaRegistro)) : null;
            })
            ->rawColumns(['acciones'])
            ->make(true);
    }
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
    public function store_timber_line(Request $request, $id)
    {
        $rules = array(
            'timerlineId' => 'required|string',
            'descripcion' => 'required|string',
            'fechaRegistro' => 'required',
        );
        $messages = [
            'timerlineId.required' => "The Description field is required",
            'descripcion.required' => "The Description field is required",
            'fechaRegistro.required' => "The Registration date field is required",
        ];

        $error = Validator::make($request->all(), $rules, $messages);
        if ($error->errors()->all()) {
            return response()->json([
                'status' => 'errors',
                'message' => $error->errors()->all(),
            ]);
        } else {
            $update = DB::table('temp_timerline')
                ->where('id', $id)
                ->update([
                    'descripcion' => $request->descripcion,
                    'fechaRegistro' => $request->fechaRegistro,
                    'estado' => 'creado',
                ]);
            return response()->json([
                'status' => 'ok',
                'message' => 'Saved successfully',
                'data' => null,
            ], 200);
        }
    }
    /*
    List employee
     */
    public function uploadFileListEmployee(Request $request)
    {
        if (!$request->hasFile('doc_list_employee')) {
            return response()->json(['error' => 'no hay archivo'], 200);
        } else {
            $data = array();
            $file = request()->file('doc_list_employee');
            $archivo = fopen($file, "r");
            while (!feof($archivo)) {
                $data[] = fgetcsv($archivo, null, ';');
                // Leyendo una linea
            }

            // Cerrando el archivo
            fclose($archivo);

            $valores = $this->EliminarArrayInicialFinal($data);
            $valores = $this->separar_Array($valores);
            $listEmployeeId = $this->insertarListaEmployee($valores);
            return response()->json([
                'status' => 'ok',
                'message' => 'recibido correctamente',
                'data' => [
                    'listEmployeeId' => $listEmployeeId,
                ],
            ], 200);
        }
    }
    private function separar_Array($datos)
    {
        $resultado = [];
        foreach ($datos as $i => $dato) {
            foreach ($dato as $key => $value) {
                //dd($value);
                $valores = explode(',', $value);
                //create object
                $listaEmpleado = new stdClass;
                $listaEmpleado->numero = $valores[0];
                $listaEmpleado->nickName = $valores[1];
                $listaEmpleado->cargo = $valores[2];
                $listaEmpleado->tipoPersona = $valores[3];
                $listaEmpleado->telefono = $valores[4];
                $listaEmpleado->email = $valores[5];
                $resultado[] = $listaEmpleado;
            }
        }
        return $resultado;
    }
    private function EliminarArrayInicialFinal($datos)
    {
        $resultado = [];

        $valores = count($datos);
        if (count($datos) > 0) {
            unset($datos[0]);
            unset($datos[count($datos)]);
        }
        return $datos;
    }
    private function insertarListaEmployee($valores)
    {
        $insertListEmployeeId = DB::table('temp_list_employee')->insertGetId([
            'descripcion' => "",
            'fechaRegistro' => date('Y-m-d'),
        ]);
        foreach ($valores as $key => $value) {
            $insertData = DB::table('temp_list_employee_data')->insertGetId([
                'numero' => $value->numero,
                'NickName' => $value->nickName,
                'cargo' => $value->cargo,
                'tipoPersona' => $value->tipoPersona,
                'telefono' => $value->telefono,
                'email' => $value->email,
                'temp_list_employee_id' => $insertListEmployeeId,
            ]);
        }
        return $insertListEmployeeId;
    }

    public function store_list_employee(Request $request, $id)
    {
        $rules = array(
            'listEmployeeId' => 'required|string',
            'descripcion' => 'required|string',
            'fechaRegistro' => 'required',
        );
        $messages = [
            'timerlineId.required' => "The Description field is required",
            'descripcion.required' => "The Description field is required",
            'fechaRegistro.required' => "The Registration date field is required",
        ];

        $error = Validator::make($request->all(), $rules, $messages);
        if ($error->errors()->all()) {
            return response()->json([
                'status' => 'errors',
                'message' => $error->errors()->all(),
            ]);
        } else {
            $update = DB::table('temp_list_employee')
                ->where('id', $id)
                ->update([
                    'descripcion' => $request->descripcion,
                    'fechaRegistro' => $request->fechaRegistro,
                    'estado' => 'creado',
                ]);
            return response()->json([
                'status' => 'ok',
                'message' => 'Saved successfully',
                'data' => null,
            ], 200);
        }
    }
    public function datatable_list_employee()
    {
        $listaEmployee = DB::table('temp_list_employee')->where('estado', 'creado')->get();
        return Datatables::of($listaEmployee)
            ->addIndexColumn()
            ->addColumn('acciones', function ($data) {
                $button = '
                <i class="fa fa-edit ms-text-primary export_list_employee cursor-pointer" data-id="' . $data->id . '" data-descripcion="' . $data->descripcion . '"  title="Export list employee"></i>
                <i class="far fa-trash-alt ms-text-danger delete_list_employee cursor-pointer" data-id="' . $data->id . '" data-descripcion="' . $data->descripcion . '"  title="Delete list employee"></i>
                ';
                return $button;
            })
            ->editColumn('fechaRegistro', function ($data) {
                return $data->fechaRegistro ? date('m/d/Y H:i:s', strtotime($data->fechaRegistro)) : null;
            })
            ->rawColumns(['acciones'])
            ->make(true);
    }
    public function compare_info(Request $request)
    {
        $rules = array(
            'listEmployeeId' => 'required|string',
            'timberlineId' => 'required|string',
            'from_date' => 'required|date_format:m/d/Y',
            'to_date' => 'required|date_format:m/d/Y',
        );
        $messages = [
            'listEmployeeId.required' => "The List employee field is required",
            'timberlineId.required' => "The TimberLine field is required",
            'from_date.required' => 'required|date_format:m/d/Y',
            'to_date.required' => 'required|date_format:m/d/Y',
        ];

        $error = Validator::make($request->all(), $rules, $messages);
        if ($error->errors()->all()) {
            return response()->json([
                'status' => 'errors',
                'message' => $error->errors()->all(),
            ]);
        }

        $verificar = $this->verificarFechaInicio($request->from_date);

        $data = $this->GuardarPayRoll(
            $request->timberlineId,
            $request->listEmployeeId,
            $request->from_date,
            $request->to_date,
            "",
            ""
        );

        if (count($verificar) > 0) {
            return response()->json([
                'status' => 'ok',
                'message' => 'Saved successfully',
                'data' => [
                    'estado' => "conflicto",
                    'temp_payroll_job' => $data,
                    'errors' => $verificar,
                    'from_date' => $request->from_date,
                    'to_date' => $request->to_date,
                ],
            ]);
        } else {
            return response()->json([
                'status' => 'ok',
                'message' => 'Saved successfully',
                'data' => [
                    'estado' => "correcto",
                    'temp_payroll_job' => $data,
                    'errors' => null,
                    'from_date' => $request->from_date,
                    'to_date' => $request->to_date,
                ],
            ]);
        }
    }
    private function GuardarPayRoll($timberLineId, $listEmployeeId, $from_date, $to_date, $nombre, $descripcion)
    {
        $listEmployee_data = true;
        $timberline_data = true;
        $timberline = DB::table('temp_timerline_data')->where('temp_timerline_id', $timberLineId)->get()->toArray();
        $proyectos_dias = DB::table('actividades as a')
            ->select(
                'pe.Numero',
                'pe.Nick_Name',
                'rd.Empleado_ID',
                'rd.Fecha',
                'rda.Horas_Contract as horas_actividad',
                't.Tas_IDT as code_cost',
                't.Nombre as nombre_tarea',
                't.Task_ID',
                'arc.Nombre as nombre_area',
                'arc.Area_ID',
                'arc.Are_IDT',
                'f.Nombre as nombre_floor',
                'f.Floor_ID',
                'f.Flo_IDT',
                'e.Nombre as nombre_edificio',
                'e.Edificio_ID',
                'e.Edi_IDT',
                'p.Nombre as nombre_proyecto',
                'p.Pro_ID',
                'p.Codigo'
            )
            ->join('registro_diario as rd', 'rd.Actividad_ID', 'a.Actividad_ID')
            ->leftjoin('personal as pe', 'pe.Empleado_ID', 'rd.Empleado_ID')
            ->join('registro_diario_actividad as rda', 'rda.Reg_ID', 'rd.Reg_ID')
            ->leftJoin('task as t', 't.Task_ID', 'rda.Task_ID')
            ->leftjoin('area_control as arc', 't.Area_ID', 'arc.Area_ID')
            ->leftjoin('floor as f', 'f.Floor_ID', 'arc.Floor_ID')
            ->leftjoin('edificios as e', 'e.Edificio_ID', 'f.Edificio_ID')
            ->leftjoin('proyectos as p', 'p.Pro_ID', 'e.Pro_ID')
            ->whereBetween('a.Fecha', [date('Y-m-d', strtotime($from_date)), date('Y-m-d', strtotime($to_date))])
            ->where('pe.Aux5', 'F')
            ->orderBy('rd.Empleado_ID', 'ASC')
            ->get()
            ->toArray();
        $timberLine = DB::table('temp_timerline_data')
            ->where('temp_timerline_data.temp_timerline_id', $timberLineId)
            ->get()
            ->toArray();
        $listEmpleados = DB::table('temp_list_employee_data')
            ->where('temp_list_employee_data.temp_list_employee_id', $listEmployeeId)
            ->get()
            ->toArray();
        //dd($proyectos_dias);
        $resultados = [];
        foreach ($proyectos_dias as $i => $proyecto) {
            $data = new stdClass;

            if ($proyecto->Empleado_ID != null) {
                if ($listEmployee_data == true) {
                    $resultado_empleado = $this->analizar_empleado($proyecto, $listEmpleados);
                    $data->empleadoId = $resultado_empleado->empleadoId;
                    $data->Nick_Name = $resultado_empleado->Nick_Name;
                    $data->Numero = $resultado_empleado->Numero;
                } else {
                    $data->empleadoId = $proyecto->Empleado_ID;
                    $data->Nick_Name = $proyecto->Nick_Name;
                    $data->Numero = 'Error';
                }
            } else {
                $data->empleadoId = 0;
                $data->Nick_Name = 'Error';
                $data->Numero = 'Error';
            }

            if ($proyecto->Pro_ID != null) {
                if ($timberline_data == true) {
                    $resultado_proyecto = $this->analizar_proyecto($proyecto, $timberLine);
                    $data->codigoProyecto = $resultado_proyecto->codigoProyecto;
                    $data->nombreProyecto = $resultado_proyecto->nombreProyecto;
                    $data->Pro_ID = $resultado_proyecto->Pro_ID;
                } else {
                    $data->codigoProyecto = $proyecto->Codigo;
                    $data->nombreProyecto = $proyecto->nombre_proyecto;
                    $data->Pro_ID = $proyecto->Pro_ID;
                }
            } else {
                $data->Pro_ID = 0;
                $data->nombreProyecto = 'Error';
                $data->codigoProyecto = 'Error';
            }

            if ($proyecto->Edificio_ID != null) {
                if ($timberline_data == true) {
                    $resultado_edificio = $this->analizar_edificio($proyecto, $timberLine);
                    $data->codigoEdificio = $resultado_edificio->codigoEdificio;
                    $data->nombreEdificio = $resultado_edificio->nombreEdificio;
                    $data->Edificio_ID = $resultado_edificio->Edificio_ID;
                } else {
                    $data->codigoEdificio = $proyecto->Edi_IDT;
                    $data->nombreEdificio = $proyecto->nombre_edificio;
                    $data->Edificio_ID = $proyecto->Edificio_ID;
                }
            } else {
                $data->Edificio_ID = 0;
                $data->nombreEdificio = 'Error';
                $data->codigoEdificio = 'Error';
            }

            if ($proyecto->Floor_ID != null) {
                if ($timberline_data == true) {
                    $resultado_floor = $this->analizar_floor($proyecto, $timberLine);
                    $data->codigoFloor = $resultado_floor->codigoFloor;
                    $data->nombreFloor = $resultado_floor->nombreFloor;
                    $data->Floor_ID = $resultado_floor->Floor_ID;
                } else {
                    $data->codigoFloor = $proyecto->Flo_IDT;
                    $data->nombreFloor = $proyecto->nombre_floor;
                    $data->Floor_ID = $proyecto->Floor_ID;
                }
            } else {
                $data->Floor_ID = 0;
                $data->nombreFloor = 'Error';
                $data->codigoFloor = 'Error';
            }

            if ($proyecto->Area_ID != null) {
                if ($timberline_data == true) {
                    $resultado_area = $this->analizar_area($proyecto, $timberLine);
                    $data->codigoArea = $resultado_area->codigoArea;
                    $data->nombreArea = $resultado_area->nombreArea;
                    $data->Area_ID = $resultado_area->Area_ID;
                } else {
                    $data->codigoArea = $proyecto->Are_IDT;
                    $data->nombreArea = $proyecto->nombre_area;
                    $data->Area_ID = $proyecto->Area_ID;
                }
            } else {
                $data->Area_ID = 0;
                $data->nombreArea = 'Error';
                $data->codigoArea = 'Error';
            }

            $data->Task_ID = 0;
            $data->nombreTrabajo = 'Error';
            $data->costCode = 'Error';
            if ($proyecto->Task_ID != null) {
                if ($timberline_data == true) {
                    $resultado_task = $this->analizar_task($proyecto, $timberLine);
                    $data->costCode = $resultado_task->costCode;
                    $data->nombreTrabajo = $resultado_task->nombreTrabajo;
                    $data->Task_ID = $resultado_task->Task_ID;
                } else {
                    $data->costCode = $proyecto->code_cost;
                    $data->nombreTrabajo = $proyecto->nombre_tarea;
                    $data->Task_ID = $proyecto->Task_ID;
                }
            } else {
                $data->Task_ID = 0;
                $data->nombreTrabajo = 'Error';
                $data->costCode = 'Error';
            }

            $data->horas_actividad = $proyecto->horas_actividad;
            $data->Fecha = $proyecto->Fecha;
            $resultados[] = $data;
        }

        $payRollId = DB::table('temp_payroll_job')->insertGetId([
            'nombre' => $nombre,
            'descripcion' => $descripcion,
            'fecha_inicio' => date('Y-m-d', strtotime($from_date)),
            'fecha_fin' => date('Y-m-d', strtotime($to_date)),
            'estado' => 'pendiente',
        ]);
        foreach ($resultados as $key => $resultado) {
            $insert = DB::table('temp_payroll_job_data')->insertGetId([
                'empleadoId' => 0,
                'NickName' => $resultado->Nick_Name,
                'codigoProyecto' => $resultado->codigoProyecto,
                'nombreProyecto' => $resultado->nombreProyecto,
                'Pro_ID' => $resultado->Pro_ID,
                'nombreEdificio' => $resultado->nombreEdificio,
                'codigoEdificio' => $resultado->codigoEdificio,
                'Edificio_ID' => $resultado->Edificio_ID,
                'nombreFloor' => $resultado->nombreFloor,
                'codigoFloor' => $resultado->codigoFloor,
                'Floor_ID' => $resultado->Floor_ID,
                'nombreArea' => $resultado->nombreArea,
                'codigoArea' => $resultado->codigoArea,
                'Area_ID' => $resultado->Area_ID,
                'nombreTrabajo' => $resultado->nombreTrabajo,
                'costCode' => $resultado->costCode,
                'Task_ID' => $resultado->Task_ID,
                'cat' => 0,
                'horas' => $resultado->horas_actividad,
                'hr_type' => 0,
                'PayId' => 0,
                'work_date' => date('Y-m-d', strtotime($resultado->Fecha)),
                'cert_class' => 0,
                'reimbId' => 0,
                'unit' => 0,
                'um' => 0,
                'rate' => 0,
                'amount' => 0,
                'temp_payroll_job_id' => $payRollId,
            ]);
        }
        return $payRollId;
    }
    /*analizar por proyecto */
    private function analizar_empleado($proyecto, $listEmpleado)
    {
        $resultado = new stdClass;
        $resultado->empleadoId = 0;
        $resultado->Nick_Name = 'Error';
        $resultado->Numero = 'Error';
        //timberline
        foreach ($listEmpleado as $key => $empleados) {
            if ($proyecto->Nick_Name == $empleados->NickName) {
                $resultado->empleadoId = $proyecto->Empleado_ID;
                $resultado->Nick_Name = $proyecto->Nick_Name;
                $resultado->Numero = $proyecto->Numero;
                return $resultado;
                break;
            }
        }
        return $resultado;
    }
    private function analizar_proyecto($proyecto, $timberLine)
    {

        $resultado = new stdClass;
        $resultado->Pro_ID = 0;
        $resultado->nombreProyecto = 'Error';
        $resultado->codigoProyecto = 'Error';
        //timberline
        foreach ($timberLine as $key => $timber) {
            if ($proyecto->Codigo == $timber->codigoProyecto) {

                $resultado->Pro_ID = $proyecto->Pro_ID;
                $resultado->nombreProyecto = $proyecto->nombre_proyecto;
                $resultado->codigoProyecto = $proyecto->Codigo;
                return $resultado;
                break;
            }
        }
        return $resultado;
    }
    private function analizar_edificio($proyecto, $timberLine)
    {
        $resultado = new stdClass;
        $resultado->Edificio_ID = 0;
        $resultado->nombreEdificio = 'Error';
        $resultado->codigoEdificio = 'Error';
        //timberline
        foreach ($timberLine as $key => $timber) {
            if ($proyecto->Codigo == $timber->codigoProyecto) {
                if ($proyecto->Edi_IDT == $timber->codigoEdificio) {
                    $resultado->Edificio_ID = $proyecto->Edificio_ID;
                    $resultado->nombreEdificio = $proyecto->nombre_edificio;
                    $resultado->codigoEdificio = $proyecto->Edi_IDT;
                    return $resultado;
                    break;
                }
            }
        }
        return $resultado;
    }
    private function analizar_floor($proyecto, $timberLine)
    {
        //dd($floor_id, $edificio_id, $proyecto_id, $proyectos);
        $resultado = new stdClass;
        $resultado->Floor_ID = 0;
        $resultado->nombreFloor = 'Error';
        $resultado->codigoFloor = 'Error';

        //timberline
        foreach ($timberLine as $key => $timber) {
            if ($proyecto->Codigo == $timber->codigoProyecto) {
                if ($proyecto->Edi_IDT == $timber->codigoEdificio) {
                    if ($proyecto->Flo_IDT == $timber->codigoFloor) {
                        $resultado->Floor_ID = $proyecto->Floor_ID;
                        $resultado->nombreFloor = $proyecto->nombre_floor;
                        $resultado->codigoFloor = $proyecto->Flo_IDT;
                        return $resultado;
                        break;
                    }
                }
            }
        }
        return $resultado;
    }

    private function analizar_area($proyecto, $timberLine)
    {
        $resultado = new stdClass;
        $resultado->Area_ID = 0;
        $resultado->nombreArea = 'Error';
        $resultado->codigoArea = 'Error';
        if ($proyecto->Pro_ID != null) {
            //timberline
            foreach ($timberLine as $key => $timber) {
                if ($proyecto->Codigo == $timber->codigoProyecto) {
                    if ($proyecto->Edi_IDT == $timber->codigoEdificio) {
                        if ($proyecto->Flo_IDT == $timber->codigoFloor) {
                            if ($proyecto->Are_IDT == $timber->codigoArea) {
                                $resultado->Area_ID = $proyecto->Area_ID;
                                $resultado->nombreArea = $proyecto->nombre_area;
                                $resultado->codigoArea = $proyecto->Are_IDT;
                                return $resultado;
                                break;
                            }
                        }
                    }
                }
            }
        }
        return $resultado;
    }
    private function analizar_task($proyecto, $timberLine)
    {
        $resultado = new stdClass;
        $resultado->Task_ID = 0;
        $resultado->nombreTrabajo = 'Error';
        $resultado->costCode = 'Error';

        //timberline
        foreach ($timberLine as $key => $timber) {
            if ($proyecto->Codigo == $timber->codigoProyecto) {
                if ($proyecto->Edi_IDT == $timber->codigoEdificio) {
                    if ($proyecto->Flo_IDT == $timber->codigoFloor) {
                        if ($proyecto->Are_IDT == $timber->codigoArea) {
                            if ($proyecto->code_cost == $timber->costCode) {
                                $resultado->Task_ID = $proyecto->Task_ID;
                                $resultado->nombreTrabajo = $proyecto->nombre_tarea;
                                $resultado->costCode = $proyecto->code_cost;
                                return $resultado;
                                break;
                            }
                        }
                    }
                }
            }
        }
        return $resultado;
    }
    private function verificarFechaInicio($fechaInicio)
    {

        $guardados = DB::table('temp_payroll_job')
            ->where(DB::raw("'" . date('Y-m-d', strtotime($fechaInicio)) . "'"), '>=', DB::raw('DATE(temp_payroll_job.fecha_inicio)'))
            ->where(DB::raw('DATE(temp_payroll_job.fecha_fin)'), '>=', DB::raw("'" . date('Y-m-d', strtotime($fechaInicio)) . "'"))
            ->where('temp_payroll_job.estado', 'creado')
            ->get();
        //dd($guardados);
        //revisar antes de guardar
        return $guardados;
    }
    private function generarRangoFecha($fechaInicio, $fechaFinal)
    {
        $comienzo = new DateTime(date('d-m-Y', strtotime($fechaInicio)));
        $final = new DateTime(date('d-m-Y', strtotime($fechaFinal)));
        // Necesitamos modificar la fecha final en 1 día para que aparezca en el bucle
        $final = $final->modify('+1 day');

        $intervalo = DateInterval::createFromDateString('1 day');
        $periodo = new DatePeriod($comienzo, $intervalo, $final);
        $dias = [];
        foreach ($periodo as $dt) {
            $dias[] = $dt->format("Y-m-d");
        }
        return $dias;
    }
    public function datatable_data($id)
    {
        //filtro mostrar error
        $data_payroll = DB::table('temp_payroll_job_data')
            ->where('temp_payroll_job_data.temp_payroll_job_id', $id)
            ->when(!empty(request()->fecha), function ($query) {
                return $query->where('temp_payroll_job_data.work_date',request()->fecha);
            })
            ->orderBy('temp_payroll_job_data.work_date', 'ASC')
        /* ->when(!empty(request()->error), function ($q) {
        return $q->whereIn('temp_payroll_job_data.Emp_ID', explode(',', request()->companies));
        }) */
            ->get();
        return Datatables::of($data_payroll)
            ->addIndexColumn()
            ->addColumn('acciones', function ($data) {
                $button = '
                <i class="far fa-trash-alt ms-text-danger delete_payroll cursor-pointer" data-id="' . $data->id . '"  title="Export delete"></i>
                ';
                return $button;
            })
            ->editColumn('work_date', function ($data) {
                return $data->work_date ? date('m/d/Y', strtotime($data->work_date)) : null;
            })
            ->editColumn('costCode', function ($data) {
                return "$data->costCode $data->nombreTrabajo";
            })
            ->rawColumns(['acciones'])
            ->make(true);
    }

    public function datatable_data_payroll()
    {
        $listaPayroll = DB::table('temp_payroll_job')
            ->select(
                'temp_payroll_job.*'
            )
            ->where('temp_payroll_job.estado', 'creado')
            ->get();
        return Datatables::of($listaPayroll)
            ->addIndexColumn()
            ->addColumn('acciones', function ($data) {
                $button = '
                <i class="fa fa-edit ms-text-primary export_payroll_data cursor-pointer" data-id="' . $data->id . '"  title="Export Payroll"></i>
                <i class="far fa-trash-alt ms-text-danger delete_payroll_data cursor-pointer" data-id="' . $data->id . '"  title="Delete Payroll"></i>
                <i class="fa fa-download ms-text-success download_payroll_data cursor-pointer" data-id="' . $data->id . '"  title="Download Payroll"></i>
                ';
                return $button;
            })
            ->rawColumns(['acciones'])
            ->make(true);
    }
    public function select_empleado(Request $request)
    {
        $data = [];
        if (!isset($request->searchTerm)) {
            $personal = DB::table('personal')
                ->select(
                    'personal.*'
                )
                ->orderBy('personal.Nick_Name')
                ->where('personal.Aux5', 'F')
                ->get();
        } else {
            $personal = DB::table('personal')
                ->select(
                    'personal.*'
                )
                ->where('personal.Nick_Name', 'like', '%' . $request->searchTerm . '%')
                ->orderBy('personal.Nick_Name')
                ->where('personal.Aux5', 'F')
                ->get();
        }
        foreach ($personal as $persona) {
            $data[] = array(
                "id" => $persona->Empleado_ID,
                "text" => $persona->Nick_Name,
                "payroll_id" => $request->payroll_id,
            );
        }
        return response()->json($data);
    }
    public function select_proyectos(Request $request)
    {
        $data = [];
        if (!isset($request->searchTerm)) {
            $proyectos = DB::table('proyectos')
                ->select(
                    'proyectos.*',
                    'empresas.Nombre as nombre_empresa',
                    'proyect_manager.Empleado_ID as proyecto_manager_id',
                    DB::raw("CONCAT(COALESCE(proyect_manager.Nombre,''),' ',COALESCE(proyect_manager.Apellido_Paterno,''),' ',COALESCE(proyect_manager.Apellido_Materno,'')) as proyecto_manager"),
                    'asistente_proyecto_manager.Empleado_ID as asistente_proyecto_manager_id',
                    DB::raw("CONCAT(COALESCE(asistente_proyecto_manager.Nombre,''),' ',COALESCE(asistente_proyecto_manager.Apellido_Paterno,''),' ',COALESCE(asistente_proyecto_manager.Apellido_Materno,'')) as asistente_proyecto_manager"),
                    'lead.Empleado_ID as lead_id',
                    DB::raw("CONCAT(COALESCE(lead.Nombre,''),' ',COALESCE(lead.Apellido_Paterno,''),' ',COALESCE(lead.Apellido_Materno,'')) as lead"),
                    'foreman.Empleado_ID as foreman_id',
                    DB::raw("CONCAT(COALESCE(foreman.Nombre,''),' ',COALESCE(foreman.Apellido_Paterno,''),' ',COALESCE(foreman.Apellido_Materno,'')) as foreman"),
                )
                ->join('empresas', 'empresas.Emp_ID', 'proyectos.Emp_ID')
                ->leftJoin('personal as proyect_manager', 'proyect_manager.Empleado_ID', 'proyectos.Manager_ID')
                ->leftJoin('personal as lead', 'lead.Empleado_ID', 'proyectos.Lead_ID')
                ->leftJoin('personal as foreman', 'foreman.Empleado_ID', 'proyectos.Foreman_ID')
                ->leftJoin('personal as asistente_proyecto_manager', 'asistente_proyecto_manager.Empleado_ID', 'proyectos.Asistant_Proyect_ID')
                ->get();
        } else {
            $proyectos = DB::table('proyectos')
                ->select(
                    'proyectos.*',
                    'empresas.Nombre as nombre_empresa',
                    'proyect_manager.Empleado_ID as proyecto_manager_id',
                    DB::raw("CONCAT(COALESCE(proyect_manager.Nombre,''),' ',COALESCE(proyect_manager.Apellido_Paterno,''),' ',COALESCE(proyect_manager.Apellido_Materno,'')) as proyecto_manager"),
                    'asistente_proyecto_manager.Empleado_ID as asistente_proyecto_manager_id',
                    DB::raw("CONCAT(COALESCE(asistente_proyecto_manager.Nombre,''),' ',COALESCE(asistente_proyecto_manager.Apellido_Paterno,''),' ',COALESCE(asistente_proyecto_manager.Apellido_Materno,'')) as asistente_proyecto_manager"),
                    'lead.Empleado_ID as lead_id',
                    DB::raw("CONCAT(COALESCE(lead.Nombre,''),' ',COALESCE(lead.Apellido_Paterno,''),' ',COALESCE(lead.Apellido_Materno,'')) as lead"),
                    'foreman.Empleado_ID as foreman_id',
                    DB::raw("CONCAT(COALESCE(foreman.Nombre,''),' ',COALESCE(foreman.Apellido_Paterno,''),' ',COALESCE(foreman.Apellido_Materno,'')) as foreman"),
                )
                ->join('empresas', 'empresas.Emp_ID', 'proyectos.Emp_ID')
                ->leftJoin('personal as proyect_manager', 'proyect_manager.Empleado_ID', 'proyectos.Manager_ID')
                ->leftJoin('personal as lead', 'lead.Empleado_ID', 'proyectos.Lead_ID')
                ->leftJoin('personal as foreman', 'foreman.Empleado_ID', 'proyectos.Foreman_ID')
                ->leftJoin('personal as asistente_proyecto_manager', 'asistente_proyecto_manager.Empleado_ID', 'proyectos.Asistant_Proyect_ID')
                ->where('proyectos.Nombre', 'like', '%' . $request->searchTerm . '%')->get();
        }
        foreach ($proyectos as $proyecto) {
            $data[] = array(
                "id" => $proyecto->Pro_ID,
                "text" => $proyecto->Nombre,
                "codigo" => $proyecto->Codigo,
                "empresa" => $proyecto->nombre_empresa,
                "proyecto_manager_id" => $proyecto->proyecto_manager_id,
                "proyecto_manager" => $proyecto->proyecto_manager,
                "asistente_proyecto_manager" => $proyecto->asistente_proyecto_manager,
                "asistente_proyecto_manager_id" => $proyecto->asistente_proyecto_manager_id,
                "foreman_id" => $proyecto->foreman_id,
                "foreman" => $proyecto->foreman,
                "lead_id" => $proyecto->lead_id,
                "lead" => $proyecto->lead,
                "payroll_id" => $request->payroll_id,
            );
        }
        return response()->json($data);
    }

    public function select_edificio(Request $request)
    {
        $data = [];
        if (!isset($request->searchTerm)) {
            $edificios = DB::table('edificios')
                ->select(
                    'edificios.*'
                )
                ->where('edificios.Pro_ID', $request->pro_id)
                ->get();
        } else {
            $edificios = DB::table('proyectos')
                ->select(
                    'edificios.*'
                )
                ->where('proyectos.Nombre', 'like', '%' . $request->searchTerm . '%')
                ->where('edificios.Pro_ID', $request->Pro_ID)
                ->get();
        }
        foreach ($edificios as $edificio) {
            $data[] = array(
                "id" => $edificio->Edificio_ID,
                "text" => $edificio->Nombre,
                "payroll_id" => $request->payroll_id,
            );
        }
        return response()->json($data);
    }
    public function select_floor(Request $request)
    {
        $data = [];
        if (!isset($request->searchTerm)) {
            $floors = DB::table('floor')
                ->select(
                    'floor.*'
                )
                ->where('floor.Edificio_ID', $request->edificio_id)
                ->get();
        } else {
            $floors = DB::table('floor')
                ->select(
                    'floor.*'
                )
                ->where('floor.Nombre', 'like', '%' . $request->searchTerm . '%')
                ->where('floor.Edificio_ID', $request->edificio_id)
                ->get();
        }
        foreach ($floors as $floor) {
            $data[] = array(
                "id" => $floor->Floor_ID,
                "text" => $floor->Nombre,
                "payroll_id" => $request->payroll_id,
            );
        }
        return response()->json($data);
    }
    public function select_area(Request $request)
    {
        $data = [];
        if (!isset($request->searchTerm)) {
            $areas = DB::table('area_control')
                ->select(
                    'area_control.*'
                )
                ->where('area_control.Floor_ID', $request->floor_id)
                ->get();
        } else {
            $areas = DB::table('floor')
                ->select(
                    'area_control.*'
                )
                ->where('area_control.Nombre', 'like', '%' . $request->searchTerm . '%')
                ->where('area_control.Floor_ID', $request->floor_id)
                ->get();
        }
        foreach ($areas as $area) {
            $data[] = array(
                "id" => $area->Area_ID,
                "text" => $area->Nombre,
                "payroll_id" => $request->payroll_id,
            );
        }
        return response()->json($data);
    }
    public function select_task(Request $request)
    {
        $data = [];
        if (!isset($request->searchTerm)) {
            $tasks = DB::table('task')
                ->select(
                    'task.*'
                )
                ->where('task.Area_ID', $request->area_id)
                ->get();
        } else {
            $tasks = DB::table('task')
                ->select(
                    'task.*'
                )
                ->where('task.Nombre', 'like', '%' . $request->searchTerm . '%')
                ->where('task.Area_ID', $request->area_id)
                ->get();
        }
        foreach ($tasks as $task) {
            $data[] = array(
                "id" => $task->Task_ID,
                "text" => "$task->Tas_IDT $task->Nombre",
                "payroll_id" => $request->payroll_id,
            );
        }
        return response()->json($data);
    }
    public function store_payroll(Request $request)
    {
        $rules = array(
            'payrollId' => 'required|string',
            'descripcion' => 'nullable|string',
            'nombre' => 'required',
        );
        $messages = [
            'payrollId.required' => "The payrollId field is required",
            'descripcion.required' => "The Description field is required",
            'nombre.required' => "The Name field is required",
        ];
        $error = Validator::make($request->all(), $rules, $messages);

        if ($error->errors()->all()) {
            return response()->json([
                'status' => 'errors',
                'message' => $error->errors()->all(),
            ]);
        } else {
            $data_payroll = DB::table('temp_payroll_job')->where('id', $request->payrollId)->first();
            if ($request->select_payroll) {
                //no funcional
                $data = $this->resolverConflictos($request->payrollId, $request->select_payroll);
                $update = DB::table('temp_payroll_job')
                    ->where('id', $request->payrollId)
                    ->update([
                        'descripcion' => $request->descripcion ? $request->descripcion : '',
                        'nombre' => $request->nombre,
                        'estado' => 'creado',
                    ]);
                return response()->json([
                    'status' => 'ok',
                    'message' => 'Payroll Saved successfully',
                    'data' => [
                        'payrollId' => $request->payrollId,
                        'nuevos' => $data,
                        'fechas' => $this->generarRangoFecha($data_payroll->fecha_inicio, $data_payroll->fecha_fin),
                    ],
                ], 200);
            } else {
                $update = DB::table('temp_payroll_job')
                    ->where('id', $request->payrollId)
                    ->update([
                        'descripcion' => $request->descripcion ? $request->descripcion : '',
                        'nombre' => $request->nombre,
                        'estado' => 'creado',
                    ]);
                return response()->json([
                    'status' => 'ok',
                    'message' => 'Payroll Saved successfully',
                    'data' => [
                        'payrollId' => $request->payrollId,
                        'nuevos' => null,
                        'fechas' => $this->generarRangoFecha($data_payroll->fecha_inicio, $data_payroll->fecha_fin),
                    ],
                ], 200);
            }

        }
    }
    /*Resolver Conflictos */
    private function resolverConflictos($payrollId, $select_payroll)
    {
        $payrollNuevo = DB::table('temp_payroll_job')
            ->where('temp_payroll_job.id', $payrollId)
            ->first();
        $payrollAnterior = DB::table('temp_payroll_job')
            ->where('temp_payroll_job.id', $select_payroll)
            ->first();
        $nuevos_ids = [];
        $GenerateFechasPayrollNuevo = $this->generarRangoFecha($payrollNuevo->fecha_inicio, $payrollNuevo->fecha_fin);
        $GenerateFechasPayrollAnterior = $this->generarRangoFecha($payrollAnterior->fecha_inicio, $payrollAnterior->fecha_fin);
        foreach ($GenerateFechasPayrollAnterior as $key => $anterior) {
            foreach ($GenerateFechasPayrollNuevo as $key => $nuevo) {
                if ($nuevo == $anterior) {
                    $data_nuevo = DB::table('temp_payroll_job_data')
                        ->where('temp_payroll_job_data.work_date', $nuevo)
                        ->where('temp_payroll_job_data.temp_payroll_job_id', $payrollId)
                        ->delete();
                    $data_anterior = DB::table('temp_payroll_job_data')
                        ->where('temp_payroll_job_data.work_date', $nuevo)
                        ->where('temp_payroll_job_data.temp_payroll_job_id', $select_payroll)
                        ->get()
                        ->toArray();

                    //dd($data_anterior);
                    foreach ($data_anterior as $key => $value) {
                        $insertReemplazar = DB::table('temp_payroll_job_data')
                            ->where('temp_payroll_job_data.work_date', $nuevo)
                            ->where('temp_payroll_job_data.temp_payroll_job_id', $payrollId)
                            ->insertGetId([
                                'empleadoId' => $value->empleadoId,
                                'NickName' => $value->NickName,
                                'codigoProyecto' => $value->codigoProyecto,
                                'nombreProyecto' => $value->nombreProyecto,
                                'Pro_ID' => $value->Pro_ID,
                                'nombreEdificio' => $value->nombreEdificio,
                                'codigoEdificio' => $value->codigoEdificio,
                                'Edificio_ID' => $value->Edificio_ID,
                                'nombreFloor' => $value->nombreFloor,
                                'codigoFloor' => $value->codigoFloor,
                                'Floor_ID' => $value->Floor_ID,
                                'nombreArea' => $value->nombreArea,
                                'codigoArea' => $value->codigoArea,
                                'Area_ID' => $value->Area_ID,
                                'nombreTrabajo' => $value->nombreTrabajo,
                                'costCode' => $value->costCode,
                                'Task_ID' => $value->Task_ID,
                                'cat' => $value->cat,
                                'horas' => $value->horas,
                                'hr_type' => $value->hr_type,
                                'PayId' => $value->PayId,
                                'work_date' => $value->work_date,
                                'cert_class' => $value->cert_class,
                                'reimbId' => $value->reimbId,
                                'unit' => $value->unit,
                                'um' => $value->um,
                                'rate' => $value->rate,
                                'amount' => $value->amount,
                                'temp_payroll_job_id' => $payrollNuevo->id,
                            ]);
                        $nuevos_ids[] = $insertReemplazar;
                    }
                }
            }
        }
        return $nuevos_ids;
    }
    /*update campos */
    public function update_empleado(Request $request)
    {
        $empleado = DB::table('personal')
            ->where('personal.Empleado_ID', $request->empleado_id)
            ->first();
        $update_payroll = DB::table('temp_payroll_job_data')
            ->where('temp_payroll_job_data.id', $request->payroll_id)
            ->update([
                'empleadoId' => $empleado->Empleado_ID,
                'NickName' => $empleado->Nick_Name,
            ]);
        return response()->json([
            'status' => 'ok',
            'message' => 'Payroll modified successfully',
            'data' => null,
        ], 200);
    }
    public function update_proyectos(Request $request)
    {
        $proyecto = DB::table('proyectos')
            ->where('proyectos.Pro_ID', $request->proyecto_id)
            ->first();
        $update_payroll = DB::table('temp_payroll_job_data')
            ->where('temp_payroll_job_data.id', $request->payroll_id)
            ->update([
                'Pro_ID' => $proyecto->Pro_ID,
                'nombreProyecto' => $proyecto->Nombre,
                'codigoProyecto' => $proyecto->Codigo,
            ]);
        return response()->json([
            'status' => 'ok',
            'message' => 'Payroll modified successfully',
            'data' => null,
        ], 200);
    }
    public function update_edificio(Request $request)
    {
        $edificio = DB::table('edificios')
            ->where('edificios.Edificio_ID', $request->edificio_id)
            ->first();
        $update_payroll = DB::table('temp_payroll_job_data')
            ->where('temp_payroll_job_data.id', $request->payroll_id)
            ->update([
                'Edificio_ID' => $edificio->Edificio_ID,
                'nombreEdificio' => $edificio->Nombre,
                'codigoEdificio' => $edificio->Edi_IDT,
            ]);
        return response()->json([
            'status' => 'ok',
            'message' => 'Payroll modified successfully',
            'data' => null,
        ], 200);
    }
    public function update_floor(Request $request)
    {
        $floor = DB::table('floor')
            ->where('floor.Floor_ID', $request->floor_id)
            ->first();
        $update_payroll = DB::table('temp_payroll_job_data')
            ->where('temp_payroll_job_data.id', $request->payroll_id)
            ->update([
                'Floor_ID' => $floor->Floor_ID,
                'nombreFloor' => $floor->Nombre,
                'codigoFloor' => $floor->Flo_IDT,
            ]);
        return response()->json([
            'status' => 'ok',
            'message' => 'Payroll modified successfully',
            'data' => null,
        ], 200);
    }
    public function update_area(Request $request)
    {
        $area = DB::table('area_control')
            ->where('area_control.Area_ID', $request->area_id)
            ->first();
        $update_payroll = DB::table('temp_payroll_job_data')
            ->where('temp_payroll_job_data.id', $request->payroll_id)
            ->update([
                'Area_ID' => $area->Area_ID,
                'nombreArea' => $area->Nombre,
                'codigoArea' => $area->Are_IDT,
            ]);
        return response()->json([
            'status' => 'ok',
            'message' => 'Payroll modified successfully',
            'data' => null,
        ], 200);
    }
    public function update_task(Request $request)
    {
        $task = DB::table('task')
            ->where('task.Task_ID', $request->task_id)
            ->first();
        $update_payroll = DB::table('temp_payroll_job_data')
            ->where('temp_payroll_job_data.id', $request->payroll_id)
            ->update([
                'Task_ID' => $task->Task_ID,
                'nombreTrabajo' => $task->Nombre,
                'costCode' => $task->Tas_IDT,
            ]);
        return response()->json([
            'status' => 'ok',
            'message' => 'Payroll modified successfully',
            'data' => null,
        ], 200);
    }
    public function load_descarga_txt(Request $request)
    {
        $constructor = 'texto de salida ' . $request->query('id');
        Storage::disk('public')->put('payroll.txt', $constructor);
        $file = public_path() . "/docs/payroll.txt";
        $headers = array(
            'Content-Type: application/txt',
        );
        /* $proyecto->Nombre = substr(str_replace(' ', ' ', strtoupper($proyecto->Nombre)), 0, 15);
        return response()->download($file, "$proyecto->Nombre For Timberline txt " . date('m-d-Y') . ".txt", $headers); */
        //$proyecto->Nombre = substr(str_replace(' ', ' ', strtoupper($proyecto->Nombre)), 0, 15);
        return response()->download($file, "export payroll txt " . date('m-d-Y') . ".txt", $headers);
    }
}
