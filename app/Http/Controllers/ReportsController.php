<?php

namespace App\Http\Controllers;

use App\Ticket;
use App\Actividad;
use App\Empresas;
use App\Personal;
use App\RegistroDiario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PDF;
use \stdClass;
use App\Exports\asistenciaExport;
use Maatwebsite\Excel\Excel;

class ReportsController extends Controller
{
    private $excel;
    public function __construct(Excel $excel)
    {
        ///inject libreria
        $this->excel=$excel;
        $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index()
    {
        $empresas=Empresas::select('Emp_ID', 'Nombre', 'Codigo')
        ->orderBy('Nombre', 'ASC')
        ->get();
        $tipo_empleado=Personal::select('Aux5')
        ->groupByRaw('Aux5')
        ->get();
        //para todos el personal
        $personal=DB::table('personal')
        ->select('Empleado_ID', 'Nick_Name', 'Nombre', 'Apellido_Paterno', 'Apellido_Materno')
        ->orderBy('Nombre', 'ASC')
        ->distinct()
        ->get();
        return view('panel.report.report', compact('empresas', 'personal', 'tipo_empleado'));
    }
    /*
    reporte de trabajo
    */
    public function get_empresas($id)
    {
        $personal=DB::table('empresas')->select('Codigo', 'Nombre')->where('Emp_ID', $id)->first();
        return $personal;
    }
    public function get_empleados(Request $request, $id)
    {
        $request->tipo_personal = explode(',', $request->tipo_personal);
        
        $personal=Personal::select('Empleado_ID', 'Nick_Name')
        ->where('Emp_ID', $id)
        ->whereIn('aux5', $request->tipo_personal)
        ->orderBy('Nick_Name', 'ASC')->get();
 
        return response()->json($personal);
    }
    ///funcion maestra
    public function reporte_asistencia(Request $request)
    {
        //dd($request->all());
        //fecha de filtro
        $fecha_inicio=date('Y-m-d', strtotime($request->fecha_inicio));
        $fecha_fin=date('Y-m-d', strtotime($request->fecha_fin));
        //extraer personal
        $personal=$this->personal_all($fecha_inicio, $fecha_fin, $request->nick_name, $request->empresa);
        //empresa
        $empresa=$this->get_empresas($request->empresa);
        //resultado final
        $resultado_final=[];
        $detalles_final=[];
        foreach ($personal as $persona) {
            //validar dia de inicio de trabajo
            //dias laborales
            $dias_laborables=$this->verificar_fecha_ingreso($fecha_inicio, $fecha_fin, $persona->Fecha_Expiracion_Trabajo);
            $fin_semanas=$this->validar_sabados_domingos($fecha_inicio, $fecha_fin, $persona->Fecha_Expiracion_Trabajo, $persona->Empleado_ID);
            //dias no laborables

            /*Otros*/
            $dias_no_trabajados=$this->contar_dias_no_trabajados($fecha_inicio, $fecha_fin, $persona->Empleado_ID);
            $dias_trabajados=(count($this->contar_dias_trabajados($fecha_inicio, $fecha_fin, $persona->Empleado_ID)));
            $castigo=$this->total_castigo($fecha_inicio, $fecha_fin, $persona->Empleado_ID);
            $permiso=$this->total_permisos($fecha_inicio, $fecha_fin, $persona->Empleado_ID);

            $sin_trabajo=$this->total_sin_trabajo($fecha_inicio, $fecha_fin, $persona->Empleado_ID);
            $suspendido=$this->total_suspendido($fecha_inicio, $fecha_fin, $persona->Empleado_ID);
            //hora de entrada
            $horas_registradas=$this->dias_retraso($fecha_inicio, $fecha_fin, $persona->Empleado_ID);
            $dias_retraso=$this->contar_dias_retraso($horas_registradas);
            //datos generales
            $registros=$this->registros_asistencia($fecha_inicio, $fecha_fin, $persona->Empleado_ID);

            $resultado_final[] = array(
                "fecha_ingreso" => $persona->Fecha_Expiracion_Trabajo,
                "Empleado_ID" => $persona->Empleado_ID,
                "Nombre" => $persona->Nick_Name,
                "dias_laborables"=>count($dias_laborables),//contando dias
                "dias_no_trabajados"=>count($dias_no_trabajados),
                "dias_trabajados"=>$dias_trabajados,
                "castigo"=>$castigo,
                "permiso"=>$permiso,
                "fin_semanas"=>count($fin_semanas),
                "dias_retraso"=>count($dias_retraso),
                "sin_trabajo"=>$sin_trabajo,
                "suspendido"=>$suspendido,
            );

            /*Evaluacion total */
            $eva_trabajados=$this->contar_dias_trabajados($fecha_inicio, $fecha_fin, $persona->Empleado_ID);
            //dd($registros);
            $detalle=[];
            if ($request->detalle==="true") {
                foreach ($registros as $registro) {
                    $status_fecha="        ";
                    $status_entrada=" ";
                    /*foreach ($eva_trabajados as  $eva_fecha) {
                        if ($registro->Fecha===$eva_fecha) {
                            $status_fecha="presence";
                            $detalle[]=array(
                                "Reg_ID"=>$registro->Reg_ID,
                                "Fecha"=>$registro->Fecha,
                                "status_fecha"=>$status_fecha,
                                "Hora_Ingreso"=>$registro->Hora_Ingreso,
                                "Hora"=>$registro->Hora,
                                "status_entrada"=>$status_entrada,
                                "Descripcion"=>$registro->Descripcion,
                                "Actividad_ID"=>$registro->Actividad_ID,
                                "Actividad_Nombre"=>$registro->Actividad_Nombre,
                                "Empleado_ID"=>$registro->Empleado_ID,
                                "empresa"=>$registro->empresa,
                                "Nombre"=>$registro->Nombre,
                                "Calle"=>$registro->Calle,
                                "Codigo"=>$registro->Codigo,
                            );
                            break;
                        } else {
                            $status_fecha="       ";
                        }
                    }*/
                    foreach ($dias_no_trabajados as  $no_trabajado) {
                        if ($registro->Reg_ID===$no_trabajado->Reg_ID) {
                            $status_fecha="absence";
                            $detalle[]=array(
                                "Reg_ID"=>$registro->Reg_ID,
                                "Fecha"=>$registro->Fecha,
                                "status_fecha"=>$status_fecha,
                                "Hora_Ingreso"=>$registro->Hora_Ingreso,
                                "Hora"=>$registro->Hora,
                                "status_entrada"=>$status_entrada,
                                "Descripcion"=>$registro->Descripcion,
                                "Actividad_ID"=>$registro->Actividad_ID,
                                "Actividad_Nombre"=>$registro->Actividad_Nombre,
                                "Empleado_ID"=>$registro->Empleado_ID,
                                "empresa"=>$registro->empresa,
                                "Nombre"=>$registro->Nombre,
                                "Calle"=>$registro->Calle,
                                "Codigo"=>$registro->Tas_IDT,
                            );
                            break;
                        }
                    }
                    foreach ($dias_retraso as  $retrasos) {
                        if ($registro->Reg_ID===$retrasos['Reg_ID']) {
                            $status_entrada="late";
                            $detalle[]=array(
                                "Reg_ID"=>$registro->Reg_ID,
                                "Fecha"=>$registro->Fecha,
                                "status_fecha"=>$status_fecha,
                                "Hora_Ingreso"=>$registro->Hora_Ingreso,
                                "Hora"=>$registro->Hora,
                                "status_entrada"=>$status_entrada,
                                "Descripcion"=>$registro->Descripcion,
                                "Actividad_ID"=>$registro->Actividad_ID,
                                "Actividad_Nombre"=>$registro->Actividad_Nombre,
                                "Empleado_ID"=>$registro->Empleado_ID,
                                "empresa"=>$registro->empresa,
                                "Nombre"=>$registro->Nombre,
                                "Calle"=>$registro->Calle,
                                "Codigo"=>$registro->Tas_IDT,
                            );
                            break;
                        }
                    }
                }
            }
            /** */
            $detalles_final[]=array(
                "Empleado_ID" => $persona->Empleado_ID,
                "Nombre" => $persona->Nick_Name,
                "dias_trabajados"=>$dias_trabajados,
                "dias_no_trabajados"=>count($dias_no_trabajados),
                "dias_retraso"=>count($dias_retraso),
                "registros"=> $detalle,
            );
        }
        $pdf = PDF::loadView('reports/asistencia/pdf', [
            'empresa'=>$empresa,
            'total_registros'=>count($personal),
            'fecha_fin'=>$fecha_fin,
            'fecha_inicio'=>$fecha_inicio,
            'lista_personal' => $resultado_final,
            'detalle_personal'=>$detalles_final,
            "detalle"=>$request->detalle,
        ]);
        //// validar  si es pdf o view
        return $pdf->setPaper('a4', 'landscape')->download("Report of Attendance.pdf");
    }
    public function view_reporte_asistencia(Request $request)
    {
        //dd($request->all());
        $request->nick_name = explode(',', $request->nick_name);
        //fecha de filtro
        $fecha_inicio=date('Y-m-d', strtotime($request->fecha_inicio));
        $fecha_fin=date('Y-m-d', strtotime($request->fecha_fin));
        //extraer personal
        $personal=$this->personal_all($fecha_inicio, $fecha_fin, $request->nick_name, $request->empresa);
        //dd($personal);
        //empresa
        $empresa=$this->get_empresas($request->empresa);
        //resultado final
        $resultado_final=[];
        $detalles_final=[];
        foreach ($personal as $persona) {
            //validar dia de inicio de trabajo
            //dias laborales
            $dias_laborables=$this->verificar_fecha_ingreso($fecha_inicio, $fecha_fin, $persona->Fecha_Expiracion_Trabajo);
            $fin_semanas=$this->validar_sabados_domingos($fecha_inicio, $fecha_fin, $persona->Fecha_Expiracion_Trabajo, $persona->Empleado_ID);
            //dias no laborables

            /*Otros*/
            $dias_no_trabajados=$this->contar_dias_no_trabajados($fecha_inicio, $fecha_fin, $persona->Empleado_ID);
            $dias_trabajados=(count($this->contar_dias_trabajados($fecha_inicio, $fecha_fin, $persona->Empleado_ID)));
            $castigo=$this->total_castigo($fecha_inicio, $fecha_fin, $persona->Empleado_ID);
            $permiso=$this->total_permisos($fecha_inicio, $fecha_fin, $persona->Empleado_ID);

            $sin_trabajo=$this->total_sin_trabajo($fecha_inicio, $fecha_fin, $persona->Empleado_ID);
            $suspendido=$this->total_suspendido($fecha_inicio, $fecha_fin, $persona->Empleado_ID);
            //hora de entrada
            $horas_registradas=$this->dias_retraso($fecha_inicio, $fecha_fin, $persona->Empleado_ID);
            $dias_retraso=$this->contar_dias_retraso($horas_registradas);
            //datos generales
            $registros=$this->registros_asistencia($fecha_inicio, $fecha_fin, $persona->Empleado_ID);

            $resultado_final[] = array(
                "fecha_ingreso" => $persona->Fecha_Expiracion_Trabajo,
                "Empleado_ID" => $persona->Empleado_ID,
                "Nombre" => $persona->Nick_Name,
                "dias_laborables"=>count($dias_laborables),//contando dias
                "dias_no_trabajados"=>count($dias_no_trabajados),
                "dias_trabajados"=>$dias_trabajados,
                "castigo"=>$castigo,
                "permiso"=>$permiso,
                "fin_semanas"=>count($fin_semanas),
                "dias_retraso"=>count($dias_retraso),
                "sin_trabajo"=>$sin_trabajo,
                "suspendido"=>$suspendido,
            );

            /*Evaluacion total */
            $eva_trabajados=$this->contar_dias_trabajados($fecha_inicio, $fecha_fin, $persona->Empleado_ID);
            //dd($registros);
            $detalle=[];
            if ($request->detalle==="true") {
                foreach ($registros as $registro) {
                    $status_fecha="        ";
                    $status_entrada=" ";
                    /*foreach ($eva_trabajados as  $eva_fecha) {
                        if ($registro->Fecha===$eva_fecha) {
                            $status_fecha="presence";
                            $detalle[]=array(
                                "Reg_ID"=>$registro->Reg_ID,
                                "Fecha"=>$registro->Fecha,
                                "status_fecha"=>$status_fecha,
                                "Hora_Ingreso"=>$registro->Hora_Ingreso,
                                "Hora"=>$registro->Hora,
                                "status_entrada"=>$status_entrada,
                                "Descripcion"=>$registro->Descripcion,
                                "Actividad_ID"=>$registro->Actividad_ID,
                                "Actividad_Nombre"=>$registro->Actividad_Nombre,
                                "Empleado_ID"=>$registro->Empleado_ID,
                                "empresa"=>$registro->empresa,
                                "Nombre"=>$registro->Nombre,
                                "Calle"=>$registro->Calle,
                                "Codigo"=>$registro->Codigo,
                            );
                            break;
                        } else {
                            $status_fecha="       ";
                        }
                    }*/
                    foreach ($dias_no_trabajados as  $no_trabajado) {
                        if ($registro->Reg_ID===$no_trabajado->Reg_ID) {
                            $status_fecha="absence";
                            $detalle[]=array(
                                "Reg_ID"=>$registro->Reg_ID,
                                "Fecha"=>$registro->Fecha,
                                "status_fecha"=>$status_fecha,
                                "Hora_Ingreso"=>$registro->Hora_Ingreso,
                                "Hora"=>$registro->Hora,
                                "status_entrada"=>$status_entrada,
                                "Descripcion"=>$registro->Descripcion,
                                "Actividad_ID"=>$registro->Actividad_ID,
                                "Actividad_Nombre"=>$registro->Actividad_Nombre,
                                "Empleado_ID"=>$registro->Empleado_ID,
                                "empresa"=>$registro->empresa,
                                "Nombre"=>$registro->Nombre,
                                "Calle"=>$registro->Calle,
                                "Codigo"=>$registro->Tas_IDT,
                            );
                            break;
                        }
                    }
                    foreach ($dias_retraso as  $retrasos) {
                        if ($registro->Reg_ID===$retrasos['Reg_ID']) {
                            $status_entrada="late";
                            $detalle[]=array(
                                "Reg_ID"=>$registro->Reg_ID,
                                "Fecha"=>$registro->Fecha,
                                "status_fecha"=>$status_fecha,
                                "Hora_Ingreso"=>$registro->Hora_Ingreso,
                                "Hora"=>$registro->Hora,
                                "status_entrada"=>$status_entrada,
                                "Descripcion"=>$registro->Descripcion,
                                "Actividad_ID"=>$registro->Actividad_ID,
                                "Actividad_Nombre"=>$registro->Actividad_Nombre,
                                "Empleado_ID"=>$registro->Empleado_ID,
                                "empresa"=>$registro->empresa,
                                "Nombre"=>$registro->Nombre,
                                "Calle"=>$registro->Calle,
                                "Codigo"=>$registro->Tas_IDT,
                            );
                            break;
                        }
                    }
                }
            }
            /** */

            $detalles_final[]=array(
                "Empleado_ID" => $persona->Empleado_ID,
                "Nombre" => $persona->Nick_Name,
                "dias_trabajados"=>$dias_trabajados,
                "dias_no_trabajados"=>count($dias_no_trabajados),
                "dias_retraso"=>count($dias_retraso),
                "registros"=> $detalle,
            );
        };
        //dd($detalles_final);
        $pdf = PDF::loadView('reports/asistencia/pdf', [
                'empresa'=>$empresa,
                'total_registros'=>count($personal),
                'fecha_fin'=>$fecha_fin,
                'fecha_inicio'=>$fecha_inicio,
                'lista_personal' => $resultado_final,
                'detalle_personal'=>$detalles_final,
                "detalle"=>$request->detalle,
            ]);

        return $pdf->setPaper('a4', 'landscape')->stream("Report of Attendance");
    }

    //cargar varios personal
    public function personal_all($fecha_inicio, $fecha_fin, $array, $empresa_id)
    {
        $personal=DB::table('personal')->select(
            "personal.Fecha_Expiracion_Trabajo",
            "personal.Empleado_ID",
            "personal.Nombre",
            "personal.Nick_Name",
            "empresas.Nombre as nombre_empresa"
        )
        ->whereIn('personal.Empleado_ID', $array)
        ->where('personal.Emp_ID', $empresa_id)
        ->whereBetween('registro_diario.Fecha', [$fecha_inicio,$fecha_fin])
        ->join('registro_diario', 'personal.Empleado_ID', 'registro_diario.Empleado_ID')
        ->join('empresas', 'empresas.Emp_ID', 'personal.Emp_ID')
        ->orderBy('Nombre', 'ASC')
        ->distinct()
        ->get();
       
        //dd($personal);
        return $personal;
    }
    //verificar fecha de ingreso del personal
    public function verificar_fecha_ingreso($fecha_inicio, $fecha_fin, $fecha_ingreso)
    {
        if ($fecha_inicio<$fecha_ingreso) {
            $dia=$this->getDiasHabiles($fecha_ingreso, $fecha_fin);

            return $dia;
        } else {
            $dia=$this->getDiasHabiles($fecha_inicio, $fecha_fin);
  
            return $dia;
        }
    }

    //funcion para verificar dias habiles
    public function getDiasHabiles($fechainicio, $fechafin)
    {
        $diasferiados=[
            '2017-01-01', '2017-12-25','2017-07-04','2017-11-25','2017-05-31',
            '2018-01-01', '2018-12-25','2018-07-04','2018-11-25','2018-05-31',
            '2019-01-01', '2019-12-25','2019-07-04','2019-11-25','2019-05-31',
            '2020-01-01', '2020-12-25','2020-07-04','2020-11-25','2020-05-31',
            '2021-01-01', '2021-12-25','2021-07-04','2021-11-25','2021-05-31',
            '2022-01-01', '2022-12-25','2022-07-04','2022-11-25','2022-05-31',
            '2023-01-01', '2023-12-25','2023-07-04','2023-11-25','2023-05-31',
            '2024-01-01', '2024-12-25','2024-07-04','2024-11-25','2024-05-31',
            '2025-01-01', '2025-12-25','2025-07-04','2025-11-25','2025-05-31',
        ];
        // Convirtiendo en timestamp las fechas
        $fechainicio = strtotime($fechainicio);
        $fechafin = strtotime($fechafin);
       
        // Incremento en 1 dia
        $diainc = 24*60*60;
       
        // Arreglo de dias habiles, inicianlizacion
        $diashabiles = array();
       
        // Se recorre desde la fecha de inicio a la fecha fin, incrementando en 1 dia
        for ($midia = $fechainicio; $midia <= $fechafin; $midia += $diainc) {
            // Si el dia indicado, no es sabado o domingo es habil
                if (!in_array(date('N', $midia), array(6,7))) { // DOC: http://www.php.net/manual/es/function.date.php
                        // Si no es un dia feriado entonces es habil
                        if (!in_array(date('Y-m-d', $midia), $diasferiados)) {
                            array_push($diashabiles, date('Y-m-d', $midia));
                        }
                }
        }
        return $diashabiles;
    }
    public function getSabadosDomingos($fechainicio, $fechafin)
    {
        $diasferiados=[
            '2017-01-01', '2017-12-25','2017-07-04','2017-11-25','2017-05-31',
            '2018-01-01', '2018-12-25','2018-07-04','2018-11-25','2018-05-31',
            '2019-01-01', '2019-12-25','2019-07-04','2019-11-25','2019-05-31',
            '2020-01-01', '2020-12-25','2020-07-04','2020-11-25','2020-05-31',
            '2021-01-01', '2021-12-25','2021-07-04','2021-11-25','2021-05-31',
            '2022-01-01', '2022-12-25','2022-07-04','2022-11-25','2022-05-31',
            '2023-01-01', '2023-12-25','2023-07-04','2023-11-25','2023-05-31',
            '2024-01-01', '2024-12-25','2024-07-04','2024-11-25','2024-05-31',
            '2025-01-01', '2025-12-25','2025-07-04','2025-11-25','2025-05-31',
        ];
        // Convirtiendo en timestamp las fechas
        $fechainicio = strtotime($fechainicio);
        $fechafin = strtotime($fechafin);
       
        // Incremento en 1 dia
        $diainc = 24*60*60;
       
        // Arreglo de dias habiles, inicianlizacion
        $diashabiles = array();
       
        // Se recorre desde la fecha de inicio a la fecha fin, incrementando en 1 dia
        for ($midia = $fechainicio; $midia <= $fechafin; $midia += $diainc) {
            // Si el dia indicado, no es sabado o domingo es habil
                if (!in_array(date('N', $midia), array(1,2,3,4,5))) { // DOC: http://www.php.net/manual/es/function.date.php
                        // Si no es un dia feriado entonces es habil
                        if (!in_array(date('Y-m-d', $midia), $diasferiados)) {
                            array_push($diashabiles, date('Y-m-d', $midia));
                        }
                }
        }
        return $diashabiles;
    }
    ////nuevo modulo
    public function validar_sabados_domingos($fecha_inicio, $fecha_fin, $fecha_ingreso, $personal_id)
    {
        $resultado=[];
        if ($fecha_inicio<$fecha_ingreso) {
            $dia_trabajados=$this->contar_dias_trabajados($fecha_ingreso, $fecha_fin, $personal_id);
            $dias_no_laborables=$this->getSabadosDomingos($fecha_ingreso, $fecha_fin);
            //buscando dias sabados y domingos usados
            $coincidencias = array_intersect($dia_trabajados, $dias_no_laborables);
            foreach ($coincidencias as $value) {
                $resultado[]=$value;
            }

            return $resultado;
        } else {
            $dia_trabajados=$this->contar_dias_trabajados($fecha_inicio, $fecha_fin, $personal_id);
            $dias_no_laborables=$this->getSabadosDomingos($fecha_inicio, $fecha_fin);
            //buscando dias sabados y domingos usados
            $coincidencias = array_intersect($dia_trabajados, $dias_no_laborables);
            foreach ($coincidencias as $value) {
                $resultado[]=$value;
            }
            return $resultado;
        }
    }
    ///dias que llego tarde
    public function dias_retraso($fecha_inicio, $fecha_fin, $personal_id)
    {
        $registros=DB::table('registro_diario')->select(
            'registro_diario.Reg_ID',
            'registro_diario.Hora_Ingreso',
            'actividades.Hora',
            'actividades.Descripcion',
            'actividades.Actividad_ID',
            'tipo_actividad.Actividad_Nombre',
            'registro_diario.Empleado_ID'
        )
        ->whereBetween('Fecha_Hingreso', [$fecha_inicio, $fecha_fin])
        ->join('actividades', 'registro_diario.Actividad_Id', 'actividades.Actividad_Id')
        ->join('tipo_actividad', 'actividades.Tipo_Actividad_ID', 'tipo_actividad.Tipo_Actividad_ID')
        ->where('registro_diario.Empleado_ID', $personal_id)
        ->distinct()
        ->orderBy('registro_diario.Reg_ID', 'ASC')
        ->get();
        return $registros;
    }
    public function contar_dias_retraso($array)
    {
        $num_veses=[];
        //dd($array);
        foreach ($array as $value) {
            //dd($value->Hora_Ingreso, $value->Hora);
            if ($value->Hora_Ingreso>$value->Hora) {
                $num_veses[]= array(
                    "Reg_ID"=>$value->Reg_ID,
                    "hora_ingreso"=>$value->Hora_Ingreso,
                    "hora_actividad"=>$value->Hora
                );
            }
        }
        return $num_veses;
    }
    public function registros_asistencia($fecha_inicio, $fecha_fin, $personal_id)
    {
        $num_veses=DB::table('registro_diario')->select(
            'empresas.Codigo as empresa',
            'proyectos.Nombre',
            'proyectos.Calle',
            'proyectos.Codigo',
            'registro_diario.Reg_ID',
            'registro_diario.Fecha',
            'registro_diario.Hora_Ingreso',
            'actividades.Hora',
            'actividades.Descripcion',
            'actividades.Actividad_ID',
            'tipo_actividad.Actividad_Nombre',
            'registro_diario.Empleado_ID',
            'registro_diario_actividad.Horas_Contract',
            'task.Tas_IDT'
        )
        ->join('registro_diario_actividad', 'registro_diario.Reg_ID', 'registro_diario_actividad.Reg_ID')
        ->join('actividades', 'registro_diario.Actividad_Id', 'actividades.Actividad_Id')
        ->join('tipo_actividad', 'actividades.Tipo_Actividad_ID', 'tipo_actividad.Tipo_Actividad_ID')
        ->join('proyectos', 'registro_diario.Pro_ID', 'proyectos.Pro_ID')
        ->join('empresas', 'empresas.Emp_ID', 'proyectos.Emp_ID')
        ->join('task', 'registro_diario_actividad.Task_ID', 'task.Task_ID')
        ->whereBetween('registro_diario.Fecha', [$fecha_inicio, $fecha_fin])
        ->where('registro_diario.Empleado_ID', $personal_id)
        ->distinct()->get();
        //dd($num_veses);
        return $num_veses;
    }
    public function contar_dias_trabajados($fecha_inicio, $fecha_fin, $personal_id)
    {
        $num_veses=RegistroDiario::select('registro_diario.Fecha_Hsalida', 'registro_diario.Fecha', 'registro_diario_actividad.Horas_Contract')
        ->join('registro_diario_actividad', 'registro_diario.Reg_ID', 'registro_diario_actividad.Reg_ID')
        ->whereBetween('registro_diario.Fecha', [$fecha_inicio, $fecha_fin])
        ->where('registro_diario.Empleado_ID', $personal_id)
        ->where('registro_diario_actividad.Horas_Contract', '>', '0')
        ->distinct()->get()
        ->pluck('Fecha')->toarray();
        //dd($num_veses, $personal_id, $fecha_inicio, $fecha_fin);
        return $num_veses;
    }
    public function contar_dias_no_trabajados($fecha_inicio, $fecha_fin, $personal_id)
    {
        $num_veses=DB::table('actividad_personal')
        ->select(
            'proyectos.Nombre',
            'proyectos.Codigo',
            'registro_diario.Reg_ID',
            'registro_diario.Hora_Ingreso',
            'actividades.Hora',
            'actividades.Descripcion',
            'actividades.Actividad_ID',
            'tipo_actividad.Actividad_Nombre',
            'registro_diario.Empleado_ID',
            'task.Tas_IDT'
        )
        ->join('personal', 'personal.Empleado_ID', 'actividad_personal.Empleado_ID')
        ->join('registro_diario', function ($res) {
            $res->on('registro_diario.Empleado_ID', 'actividad_personal.Empleado_ID')
            ->on('registro_diario.Actividad_Id', 'actividad_personal.Actividad_Id');
        })
        ->join('registro_diario_actividad', 'registro_diario.Reg_ID', 'registro_diario_actividad.Reg_ID')
        ->join('task', 'registro_diario_actividad.Task_ID', 'task.Task_ID')
        ->join('actividades', 'registro_diario.Actividad_Id', 'actividades.Actividad_Id')
        ->join('tipo_actividad', 'actividades.Tipo_Actividad_ID', 'tipo_actividad.Tipo_Actividad_ID')
        ->join('proyectos', 'registro_diario.Pro_ID', 'proyectos.Pro_ID')
        ->where('registro_diario.Empleado_ID', $personal_id)
        ->where('task.Tas_IDT', 'VACNOSHOW')
        ->whereBetween('registro_diario.Fecha', [$fecha_inicio, $fecha_fin])
        ->distinct('registro_diario.Reg_ID')
        ->orderBy('registro_diario.Reg_ID', 'ASC')
        ->get();
        //dd($num_veses);
        return $num_veses;
    }

    public function total_castigo($fecha_inicio, $fecha_fin, $personal_id)
    {
        $num_veses=DB::table('registro_diario')
        ->select('registro_diario.Reg_ID')
        ->join('proyectos', 'proyectos.Pro_ID', 'registro_diario.Pro_ID')
        ->whereBetween('registro_diario.Fecha', [$fecha_inicio, $fecha_fin])
        ->where('registro_diario.Empleado_ID', $personal_id)
        ->where('proyectos.Codigo', '992.00.9')
        ->get()->count();
   
        return $num_veses;
    }
    public function total_permisos($fecha_inicio, $fecha_fin, $personal_id)
    {
        $num_veses=DB::table('registro_diario')
        ->select('registro_diario.Reg_ID')
        ->join('proyectos', 'proyectos.Pro_ID', 'registro_diario.Pro_ID')
        ->whereBetween('registro_diario.Fecha', [$fecha_inicio, $fecha_fin])
        ->where('registro_diario.Empleado_ID', $personal_id)
        ->where(function ($query) {
            $query->where('proyectos.Codigo', '996.00.9')
                  ->orWhere('proyectos.Codigo', '994.21.9');
        })
        ->get()->count();

        return $num_veses;
    }
    public function total_sin_trabajo($fecha_inicio, $fecha_fin, $personal_id)
    {
        $num_veses=DB::table('registro_diario')
        ->select('registro_diario.Reg_ID')
        ->join('proyectos', 'proyectos.Pro_ID', 'registro_diario.Pro_ID')
        ->whereBetween('registro_diario.Fecha', [$fecha_inicio, $fecha_fin])
        ->where('registro_diario.Empleado_ID', $personal_id)
        ->where('proyectos.Codigo', '997.00.9')
        ->get()->count();
        
        return $num_veses;
    }
    public function total_suspendido($fecha_inicio, $fecha_fin, $personal_id)
    {
        $num_veses=DB::table('registro_diario')
        ->select('registro_diario.Reg_ID')
        ->join('proyectos', 'proyectos.Pro_ID', 'registro_diario.Pro_ID')
        ->whereBetween('registro_diario.Fecha', [$fecha_inicio, $fecha_fin])
        ->where('registro_diario.Empleado_ID', $personal_id)
        ->where('proyectos.Codigo', '995.00.9')
        ->get()->count();
        
        return $num_veses;
    }
    public function test_excel(Request $request)
    {
        //fecha de filtro
        $fecha_inicio=date('Y-m-d', strtotime($request->fecha_inicio));
        $fecha_fin=date('Y-m-d', strtotime($request->fecha_fin));
        //extraer personal
        $personal=$this->personal_all($fecha_inicio, $fecha_fin, $request->nick_name, $request->empresa);
        //empresa
        $empresa=$this->get_empresas($request->empresa);
        //resultado final
        $resultado_final=[];
        foreach ($personal as $persona) {
            //validar dia de inicio de trabajo
            $dias_habiles=$this->verificar_fecha_ingreso($fecha_inicio, $fecha_fin, $persona->Fecha_Expiracion_Trabajo);
            //dias laborales
            $dias_habiles=count($dias_habiles);
            //dias trabajados
            $dias_trabajados=(count($this->contar_dias_trabajados($fecha_inicio, $fecha_fin, $persona->Empleado_ID)));
            //dias no trabajados
            $dias_no_trabajados=count($this->contar_dias_no_trabajados($fecha_inicio, $fecha_fin, $persona->Empleado_ID));
            //dias castigo
            $total_castigo=$this->total_castigo($fecha_inicio, $fecha_fin, $persona->Empleado_ID);
            //dias permiso
            $dias_permiso=$this->total_permisos($fecha_inicio, $fecha_fin, $persona->Empleado_ID);
            //sin trabajo
            $dias_sin_trabajo=$this->total_sin_trabajo($fecha_inicio, $fecha_fin, $persona->Empleado_ID);
            //suspendido
            $dias_suspendidos=$this->total_suspendido($fecha_inicio, $fecha_fin, $persona->Empleado_ID);
            $fin_semanas=$this->validar_sabados_domingos($fecha_inicio, $fecha_fin, $persona->Fecha_Expiracion_Trabajo, $persona->Empleado_ID);
            //contando
            $dias_retraso=$this->dias_retraso($fecha_inicio, $fecha_fin, $persona->Empleado_ID);
            $dias_retraso=$this->contar_dias_retraso($dias_retraso);
            //dd($dias_retraso);
            $resultado_final[] = array(
                 "fecha_ingreso" => date("m/d/Y", strtotime($persona->Fecha_Expiracion_Trabajo)),
                 "Empleado_ID" => $persona->Empleado_ID,
                 "Nombre" => $persona->Nick_Name,
                 "dias_laborables"=>strval($dias_habiles),
                 "dias_laborables%"=>'1',// para q la config de excel lo lea 100%
                 "dias_trabajados"=>strval($dias_trabajados),
                 "dias_trabajados%"=>strval(round(((float)($dias_trabajados)* 100)/$dias_habiles).'%'),
                 "castigo"=>strval($total_castigo),
                 "castigo%"=>strval(round(((float)($total_castigo)* 100)/$dias_habiles).'%'),
                 "dias_no_trabajados"=>strval($dias_no_trabajados),
                 "dias_no_trabajados%"=>strval(round(((float)($dias_no_trabajados)* 100)/$dias_habiles).'%'),
                 "permiso"=>strval($dias_permiso),
                 "permiso%"=>strval(round(((float)($dias_permiso)* 100)/$dias_habiles).'%'),
                 "sin_trabajo"=>strval($dias_sin_trabajo),
                 "sin_trabajo%"=>strval(round(((float)($dias_sin_trabajo)* 100)/$dias_habiles).'%'),
                 "suspendido"=>strval($dias_suspendidos),
                 "suspendido%"=>strval(round(((float)($dias_suspendidos)* 100)/$dias_habiles).'%'),
                 "fin_semanas"=>strval(count($fin_semanas)),
                 "fin_semanas%"=>strval(round(((float)(count($fin_semanas))* 100)/$dias_habiles).'%'),
                 "dias_retraso"=>strval(count($dias_retraso)),
                 "dias_retraso%"=>strval(round(((float)(count($dias_retraso))* 100)/$dias_habiles).'%'),
             );
        }
        $extras=new stdClass();
        $extras->empresa=$empresa;
        $extras->total_registros=count($personal);
        $extras->fecha_fin=date("m-d-Y", strtotime($fecha_fin));
        $extras->fecha_inicio=date("m-d-Y", strtotime($fecha_inicio));

        return $this->excel->download(new asistenciaExport($resultado_final, $extras), 'attendance.xlsx');
    }

    /* reporte detalle por persona */
    public function reporte_asistencia_detail()
    {
    }
    public function view_reporte_asistencia_detail()
    {
    }
    /**
     *
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
