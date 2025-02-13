<?php

namespace App\Http\Controllers;

use App\Empresas;
use App\Proyecto;
use DataTables;
use DateTime;
use DB;
use Illuminate\Http\Request;

class ProyectoController extends Controller
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
        $empresas = Empresas::all();
        $status = DB::table('estatus')->get();
        if (request()->ajax()) {

        } else {
            return view('panel.proyectos.list', compact('empresas', 'status'));
        }

    }
    public function datatable()
    {
        $status = DB::table('estatus')->get();
        $personal = DB::table('personal')
            ->selectRaw("
                    personal.Empleado_ID,
                    CONCAT(personal.Nombre, ' ',  personal.Apellido_Paterno, ' ',  personal.Apellido_Materno) as asistente_proyecto
                    ")
            ->join('empresas', 'empresas.Emp_ID', 'personal.Emp_ID')
            ->where(function ($query) {
                return $query->orWhere('personal.Aux5', 'F')
                    ->orWhere('personal.Aux5', 'FB')
                    ->orWhere('personal.Aux5', 'FT')
                    ->orWhere('personal.Aux5', 'FS')
                    ->orWhere('personal.Aux5', 'FX')
                    ->orWhere('personal.Aux5', 'FY')
                    ->orWhere('personal.Cargo', 'like', '%Sub%');
            })
            ->where('personal.Emp_ID', '6')
            ->get();

        $data = Proyecto::selectRaw("
                proyectos.Pro_ID,
                proyectos.Codigo,
                proyectos.Nombre,
                proyectos.Fecha_Inicio,
                proyectos.Fecha_Fin,
                proyectos.Horas,
                CONCAT(proyectos.Calle, ' ', proyectos.Ciudad, ' ',  proyectos.Estado, ' ',  proyectos.Zip_Code) as direccion,
                empresas.Nombre as empresa,
                estatus.Estatus_ID as Estatus_ID,
                estatus.Estatus_ID,
                tipo_proyecto.Nombre_Tipo as tipo,
                CONCAT(em1.Nombre, ' ', em1.Apellido_Paterno, ' ',  em1.Apellido_Materno) as Foreman,
                CONCAT(em2.Nombre, ' ',  em2.Apellido_Paterno, ' ',  em2.Apellido_Materno) as Cordinador,
                CONCAT(em3.Nombre, ' ',  em3.Apellido_Paterno, ' ',  em3.Apellido_Materno) as Manager,
                CONCAT(em4.Nombre, ' ',  em4.Apellido_Paterno, ' ',  em4.Apellido_Materno) as Project_Manager,
                CONCAT(em5.Nombre, ' ',  em5.Apellido_Paterno, ' ',  em5.Apellido_Materno) as Coordinador_Obra,
                CONCAT(em6.Nombre, ' ',  em6.Apellido_Paterno, ' ',  em6.Apellido_Materno) as asistente_proyecto
                ")
            ->when(request()->status, function ($query) {
                return $query->where('estatus.Nombre_Estatus', request()->status);
            })
            ->when(request()->gc, function ($query) {
                return $query->where('empresas.Nombre', request()->gc);
            })
            ->leftjoin('estatus', 'proyectos.Estatus_ID', 'estatus.Estatus_ID')
            ->leftjoin('tipo_proyecto', 'proyectos.Tipo_ID', 'tipo_proyecto.Tipo_ID')
            ->join('empresas', 'proyectos.Emp_ID', 'empresas.Emp_ID')
            ->leftJoin('personal as em1', 'em1.Empleado_ID', 'proyectos.Foreman_ID')
            ->leftJoin('personal as em2', 'em2.Empleado_ID', 'proyectos.Coordinador_ID')
            ->leftJoin('personal as em3', 'em3.Empleado_ID', 'proyectos.Manager_ID')
            ->leftJoin('personal as em4', 'em4.Empleado_ID', 'proyectos.Project_Manager_ID')
            ->leftJoin('personal as em5', 'em5.Empleado_ID', 'proyectos.Coordinador_Obra_ID')
            ->leftJoin('personal as em6', 'em6.Empleado_ID', 'proyectos.Asistant_Proyect_ID')
            ->get();
        return Datatables::of($data)
            ->addIndexColumn()
            ->addColumn('Fecha_Inicio', function ($data) {
                return date('m/d/Y', strtotime($data->Fecha_Inicio));
            })
            ->addColumn('Fecha_Fin', function ($data) {
                return date('m/d/Y', strtotime($data->Fecha_Fin));
            })
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
            ->addColumn('estatus', function ($data) use ($status) {
                $optionStatus = "";
                foreach ($status as $value) {
                    if ($value->Estatus_ID == $data->Estatus_ID) {
                        $optionStatus .= "<option value='$value->Estatus_ID' data-id='$data->Pro_ID' data-start='$data->Fecha_Inicio' data-end='$data->Fecha_Fin' selected >$value->Nombre_Estatus</option>";
                    } else {
                        $optionStatus .= "<option value='$value->Estatus_ID' data-id='$data->Pro_ID' data-start='$data->Fecha_Inicio' data-end='$data->Fecha_Fin' >$value->Nombre_Estatus</option>";
                    }
                }
                $campo = "
                        <select class='change_status'>
                        $optionStatus
                        </select>
                        ";
                return $campo;
            })
            ->addColumn('Asistant_Proyect_ID', function ($data) use ($personal) {

                $optionPersonal = "<option value=' '>Select an option</option>";
                foreach ($personal as $value) {
                    $optionPersonal .= "<option value='$value->Empleado_ID' " . ($value->Empleado_ID == $data->Asistant_Proyect_ID ? 'selected' : '') . " data-id='$data->Pro_ID' >$value->asistente_proyecto</option>";
                }
                $campo = "
                        <select class='change_asistente_proyecto'>
                        $optionPersonal
                        </select>
                        ";
                return $campo;
            })
            ->rawColumns(['campo', 'estatus', 'Asistant_Proyect_ID'])
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
    public function update_status(Request $request)
    {
        try {
            $update = Proyecto::where('Pro_ID', $request->id)->update([
                "Estatus_ID" => $request->dato,
            ]);
            return response()->json(['alert' => 'successfully modified status'], 200);
        } catch (\Throwable $th) {
            return response()->json(['alert' => 'an error has occurred'], 200);
        }
    }
    public function update_asistente_proyecto(Request $request)
    {
        try {
            $update = Proyecto::where('Pro_ID', $request->id)->update([
                "Asistant_Proyect_ID" => $request->dato,
            ]);
            return response()->json(['alert' => 'successfully modified '], 200);
        } catch (\Throwable $th) {
            return response()->json(['alert' => 'an error has occurred'], 200);
        }
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $proyecto = Proyecto::where('Pro_ID', $request->id)->first();
        $Horas = $proyecto->Horas + $proyecto->Adi1 + $proyecto->Adi2 + $proyecto->Adi3 + $proyecto->Adi4 + $proyecto->Adi5;
        $Fecha_Inicio_Etapa = $proyecto->Fecha_Inicio;
        $Fecha_Fin_Etapa = $proyecto->Fecha_Fin;
        $Note = "SDate $Fecha_Inicio_Etapa End $Fecha_Fin_Etapa";
        $Note = "$Note T.Hrs.: $Horas";
        $Nombre = $request->dato;
        $consulta = DB::table('etapas')
            ->where('Pro_ID', $proyecto->Pro_ID)->where(function ($q) use ($Fecha_Inicio_Etapa, $Fecha_Fin_Etapa) {
            $q->whereBetween('Fecha_Inicio', [$Fecha_Inicio_Etapa, $Fecha_Fin_Etapa]);
        })->Orwhere(function ($q) use ($Fecha_Inicio_Etapa, $Fecha_Fin_Etapa) {
            $q->whereBetween('Fecha_Fin', [$Fecha_Inicio_Etapa, $Fecha_Fin_Etapa]);
        })->get()->toArray();

        if ($Nombre == "Add" || $Nombre == "No SDate" || $Nombre == "No EDate") {
            if (count($consulta) > 0) {
                return response()->json([
                    'success' => true,
                    'data' => DB::table('etapas')->where('Pro_ID', $proyecto->Pro_ID)->get(),
                    'alert' => 'ERROR Fechas incluidas en otras etapas',
                ], 200);
            } else {
                $Horas_Etapa = $Horas;
                $total_dias_habiles = $this->Dias_Habiles($Fecha_Inicio_Etapa, $Fecha_Fin_Etapa);
                $Horas_Dia = round($Horas_Etapa / $total_dias_habiles);
                $Empleados_Diarios = round($Horas_Dia / 8);
                $insertSql = DB::table('etapas')->insert([
                    'Pro_ID' => $proyecto->Pro_ID,
                    'Nombre' => $Nombre,
                    'Porcentaje_Esfuerzo' => 1,
                    'Fecha_Inicio' => $Fecha_Inicio_Etapa,
                    'Fecha_Fin' => $Fecha_Fin_Etapa,
                    'Empleados_Diarios' => $Empleados_Diarios,
                    'Horas' => $Horas_Etapa,
                    'Dias_Habiles' => $total_dias_habiles,
                    'Note' => $Note,
                ]);

                if ($insertSql) {
                    return response()->json([
                        'success' => true,
                        'data' => DB::table('etapas')->where('Pro_ID', $proyecto->Pro_ID)->get(),
                    ], 200);
                } else {
                    return response()->json([
                        'success' => false,
                        'data' => [],
                    ], 200);
                }
            }
        } else {
            $eliminar = DB::table('etapas')->where('Pro_ID', $proyecto->Pro_ID)->delete();
            //Stages:1st. 20 % days= 10% hours / 2nd. 60% days=80% hours / 3rd.20% days=10% hours
            $datediff = strtotime($Fecha_Fin_Etapa) - strtotime($Fecha_Inicio_Etapa);
            //$diff = strtotime($date2) - strtotime($date1);
            $totdias = round($datediff / (60 * 60 * 24));
            // echo date('Y-m-d', strtotime($date. ' + 5 days'));

            $f1x = $Fecha_Inicio_Etapa;
            $dias20p = round($totdias * 0.2);
            $f2x = date('Y-m-d', strtotime($f1x . ' + ' . $dias20p . ' days'));
            $horasf1 = round($Horas * 0.1);

            $dias60p = round($totdias * 0.6);
            $f3x = date('Y-m-d', strtotime($f2x . ' + 1 days'));
            $horasf3 = round($Horas * 0.8);
            $f4x = date('Y-m-d', strtotime($f3x . ' + ' . $dias60p . ' days'));

            $f5x = date('Y-m-d', strtotime($f4x . ' + 1 days'));
            $horasf5 = round($Horas * 0.1);
            $f6x = $Fecha_Fin_Etapa;

            /////////// record
            $Nombre = "1st.stage:(" . $totdias . " Total Days in the job)";

            $Horas_Etapa = $horasf1;
            $Fecha_Inicio_Etapa = $f1x;
            $Fecha_Fin_Etapa = $f2x;

            //$Horas_Etapa=$Horas;
            $total_dias_habiles = $this->Dias_Habiles($Fecha_Inicio_Etapa, $Fecha_Fin_Etapa);
            //echo "<bR>$Fecha_Inicio_Etapa***$Fecha_Fin_Etapa***$total_dias_habiles";
            $Horas_Dia = ($total_dias_habiles != 0) ? round($Horas_Etapa / $total_dias_habiles) : 0;
            $Empleados_Diarios = round($Horas_Dia / 8);

            DB::table('etapas')->insert([
                'Pro_ID' => $proyecto->Pro_ID,
                'Nombre' => $Nombre,
                'Porcentaje_Esfuerzo' => 1,
                'Fecha_Inicio' => $Fecha_Inicio_Etapa,
                'Fecha_Fin' => $Fecha_Fin_Etapa,
                'Empleados_Diarios' => $Empleados_Diarios,
                'Horas' => $Horas_Etapa,
                'Dias_Habiles' => $total_dias_habiles,
                'Note' => $Note,
            ]);
            ///end record
            /////////// record
            $Nombre = "2nd.stage:(" . $totdias . " Total Days in the job)";

            $Horas_Etapa = $horasf3;
            $Fecha_Inicio_Etapa = $f3x;
            $Fecha_Fin_Etapa = $f4x;

            //$Horas_Etapa=$Horas;
            $total_dias_habiles = $this->Dias_Habiles($Fecha_Inicio_Etapa, $Fecha_Fin_Etapa);
            //echo "<bR>$Fecha_Inicio_Etapa***$Fecha_Fin_Etapa***$total_dias_habiles";
            $Horas_Dia = ($total_dias_habiles != 0) ? round($Horas_Etapa / $total_dias_habiles) : 0;

            $Empleados_Diarios = round($Horas_Dia / 8);

            DB::table('etapas')->insert([
                'Pro_ID' => $proyecto->Pro_ID,
                'Nombre' => $Nombre,
                'Porcentaje_Esfuerzo' => 1,
                'Fecha_Inicio' => $Fecha_Inicio_Etapa,
                'Fecha_Fin' => $Fecha_Fin_Etapa,
                'Empleados_Diarios' => $Empleados_Diarios,
                'Horas' => $Horas_Etapa,
                'Dias_Habiles' => $total_dias_habiles,
                'Note' => $Note,
            ]);
            ///end record
            /////////// record
            $Nombre = "3rd.stage:(" . $totdias . " Total Days in the job)";
            //.$dias20p."  20pdias ".$f6x." F6 / ";
            //$f2x=$Fecha_Inicio_Etapa+(($totdias*.2)*60*60*24);
            //$f3x=$f2x;

            $Horas_Etapa = $horasf5;
            $Fecha_Inicio_Etapa = $f5x;
            $Fecha_Fin_Etapa = $f6x;

            //$Horas_Etapa=$Horas;
            $total_dias_habiles = $this->Dias_Habiles($Fecha_Inicio_Etapa, $Fecha_Fin_Etapa);

            //echo "<bR>$Fecha_Inicio_Etapa***$Fecha_Fin_Etapa***$total_dias_habiles";
            $Horas_Dia = ($total_dias_habiles) ? round($Horas_Etapa / $total_dias_habiles) : 0;
            $Empleados_Diarios = round($Horas_Dia / 8);

            DB::table('etapas')->insert([
                'Pro_ID' => $proyecto->Pro_ID,
                'Nombre' => $Nombre,
                'Porcentaje_Esfuerzo' => 1,
                'Fecha_Inicio' => $Fecha_Inicio_Etapa,
                'Fecha_Fin' => $Fecha_Fin_Etapa,
                'Empleados_Diarios' => $Empleados_Diarios,
                'Horas' => $Horas_Etapa,
                'Dias_Habiles' => $total_dias_habiles,
                'Note' => $Note,
            ]);

            ///end record
            return response()->json([
                'success' => true,
                'data' => DB::table('etapas')->where('Pro_ID', $proyecto->Pro_ID)->get(),
            ], 200);
        }
    }

    //funcion que devuelve el �ltimo d�a de un mes y a�o dados
    public function Dias_Habiles($Fecha_Inicio, $Fecha_Fin)
    {
        $feriados = array("1-1", "13-2", "13-4", "26-5", "02-11", "25-12");

        $comienzo = new DateTime($Fecha_Inicio);
        $final = new DateTime($Fecha_Fin);

        $diasHabiles = 0;
        for ($i = $comienzo; $i <= $final; $i->modify('+1 day')) {
            $Dia_Semana = $i->format("w");
            $Dia = $i->format("d");
            $Mes = $i->format("m");
            if ($Dia_Semana != 'Saturday' && $Dia_Semana != 'Sunday') {
                $feriado = $Dia . "-" . $Mes;
                if (!in_array($feriado, $feriados)) {
                    $diasHabiles++;
                }
            }
        }

        return $diasHabiles;
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
    public function update(Request $request)
    {
        $text = "";
        $valor = "";
        switch (true) {
            case $request->Fecha_Inicio:
                $text = "Fecha_Inicio";
                $valor = ($request->Fecha_Inicio) ? date('Y-m-d', strtotime($request->Fecha_Inicio)) : null;
                break;
            case $request->Fecha_Fin:
                $text = "Fecha_Fin";
                $valor = ($request->Fecha_Fin) ? date('Y-m-d', strtotime($request->Fecha_Fin)) : null;
                break;
            case $request->Horas:
                $text = "Horas";
                $valor = $request->Horas;
                break;
            case $request->Adi1:
                $text = "Adi1";
                $valor = $request->Adi1;
                break;
            case $request->Adi2:
                $text = "Adi2";
                $valor = $request->Adi2;
                break;
            case $request->Adi3:
                $text = "Adi3";
                $valor = $request->Adi3;
                break;
            case $request->Adi4:
                $text = "Adi4";
                $valor = $request->Adi4;
                break;
            case $request->Adi5:
                $text = "Adi5";
                $valor = $request->Adi5;
                break;
        }
        $insert = Proyecto::where('Codigo', $request->Codigo)->update([
            $text => $valor,
        ]);
        if ($insert) {
            return response()->json([
                'success' => true,
            ], 200);
        }
        return response()->json([
            'success' => false,
        ], 200);
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
