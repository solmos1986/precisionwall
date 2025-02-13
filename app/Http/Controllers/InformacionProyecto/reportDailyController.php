<?php

namespace App\Http\Controllers\InformacionProyecto;

use App\Http\Controllers\Controller;
use DB;
use Illuminate\Http\Request;
use PDF;
use Validator;

class reportDailyController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

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
    public function proyecto(Request $request)
    {
        $proyectos = explode(',', $request->query('projects'));
        $proyecto = DB::table('proyectos')->whereIn('proyectos.Pro_ID', $proyectos)->get();
        if ($proyecto) {
            return response()->json([
                'status' => 'ok',
                'data' => [
                    'proyectos' => $proyecto,
                ],
                'message' => '',
            ], 200);
        } else {
            return response()->json([
                'status' => 'ok',
                'message' => 'error ',
            ], 200);
        }

    }
    private function validador(Request $request)
    {
        $rules = array(
            'fecha_inicio' => 'required',
            'fecha_fin' => 'required',
        );
        $messages = [
            'fecha_fin.required' => "The From date: field is required",
            'fecha_inicio.required' => "The To date: field is required",
        ];

        $error = Validator::make($request->all(), $rules, $messages);
        if ($error->errors()->all()) {
            return response()->json([
                'status' => 'errors',
                'message' => $error->errors()->all(),
            ]);
        }
    }
    public function view_report(Request $request)
    {
        $proyectos_id = explode(',', $request->query('proyectos'));
        $from_date = $request->query('fecha_inicio');
        $to_date = $request->query('fecha_fin');
        $personal = $request->query('personal');

        $this->validador($request);

        $proyectos = DB::table('proyectos')
            ->select(
                'proyectos.*',
                'empresas.Nombre as nombre_empresa'
            )
            ->join('empresas', 'proyectos.Emp_ID', 'empresas.Emp_ID')
            ->whereIn('proyectos.Pro_ID', $proyectos_id)
            ->get();
        foreach ($proyectos as $key => $proyecto) {
            $registro_diarios = DB::table('registro_diario')
                ->select(
                    DB::raw('DATE_FORMAT(registro_diario.Fecha , "%W %d %M %Y") as descripcion_fecha'),
                    'registro_diario.Fecha'
                )
                ->where('registro_diario.Pro_ID', $proyecto->Pro_ID)
                ->whereBetween('registro_diario.Fecha', [date('Y-m-d', strtotime($from_date)), date('Y-m-d', strtotime($to_date))])
                ->groupBy('registro_diario.Fecha')
                ->orderBy('registro_diario.Fecha', 'DESC')
                ->get();

            //verificando tipo de de consulta
            if ($personal == 'true') {
                $proyecto->registro_diarios = $this->report_personal($registro_diarios, $proyecto);
            } else {
                $proyecto->registro_diarios = $this->report_resumen($registro_diarios, $proyecto);

            }
        }
        //dd($proyectos);
        //armando deacuerdo al filtro
        if ($personal == 'true') {
            $pdf = PDF::loadView('panel.informacion_proyecto.report.daily-pdf-personal', compact('proyectos', 'from_date', 'to_date'))->setPaper('letter')->setWarnings(false);
        } else {
            $pdf = PDF::loadView('panel.informacion_proyecto.report.daily-pdf', compact('proyectos', 'from_date', 'to_date'))->setPaper('letter')->setWarnings(false);
        }

        return $pdf->stream("View repor Daiy.pdf");
    }
    /*deacuerdo a filtro */
    private function report_resumen($registro_diarios, $proyecto)
    {
        foreach ($registro_diarios as $key => $registro_diario) {
            $registro_diario->registro_diario_actividad = DB::table('registro_diario')
                ->select(
                    'registro_diario.*',
                    'proyectos.Codigo',
                    'proyectos.Nombre',
                    'task.Nombre as nombre_tarea',
                    'task.Task_ID',
                    'task.Horas_Estimadas as Horas_Estimadas',
                    'task.Last_Per_Recorded as Last_Per_Recorded',
                    'area_control.nombre as nombre_area',
                    DB::raw('SUM(registro_diario_actividad.Horas_Contract) as Horas_Contract_total'),
                    'registro_diario_actividad.Horas_Contract',
                    'registro_diario_actividad.RDA_ID',
                    DB::raw('count(registro_diario.Empleado_ID) as cantidad_personas'),
                )
                ->join('proyectos', 'proyectos.Pro_ID', 'registro_diario.Pro_ID')
                ->join('registro_diario_actividad', 'registro_diario_actividad.Reg_ID', 'registro_diario.Reg_ID')
                ->join('task', 'task.Task_ID', 'registro_diario_actividad.Task_ID')
                ->join('area_control', 'area_control.Area_ID', 'task.Area_ID')
                ->where('registro_diario.Pro_ID', $proyecto->Pro_ID)
                ->where('registro_diario.Fecha', $registro_diario->Fecha)
                ->orderBy('area_control.nombre', 'ASC')
                ->groupBy('registro_diario_actividad.Task_ID')
                ->get();

            $total_Horas_Contract = 0;
            foreach ($registro_diario->registro_diario_actividad as $key => $registro_diario_actividad) {
                //armando informacion requerida
                $total_used = DB::table('registro_diario_actividad')
                    ->select(
                        DB::raw('SUM(registro_diario_actividad.Horas_Contract) as Horas_Contract'),
                    )
                    ->where('registro_diario_actividad.Task_ID', $registro_diario_actividad->Task_ID)
                    ->first();
                //obteniedo porcentaje requerida
                $porcentaje_completado = DB::table('percentage_complete')
                    ->where('percentage_complete.Date_Recorded', $registro_diario_actividad->Fecha)
                    ->where('percentage_complete.Pro_ID', $registro_diario_actividad->Pro_ID)
                    ->where('percentage_complete.Task_ID', $registro_diario_actividad->Task_ID)
                    ->first();
                //totales
                $registro_diario_actividad->total_used = $total_used;

                $registro_diario_actividad->porcentaje = $porcentaje_completado == null ? '' : $porcentaje_completado->Per_Recorded . "%";
                $registro_diario_actividad->note = $porcentaje_completado == null ? '' : $porcentaje_completado->Note;

                $total_Horas_Contract += $registro_diario_actividad->Horas_Contract_total;
                $registro_diario_actividad->total_Horas_Contract = $total_Horas_Contract;

                $daily_report = DB::table('report_daily_detalle')
                    ->where('report_daily_detalle.actividad_id', $registro_diario_actividad->Actividad_Id)
                    ->first();
                if ($daily_report != null) {
                    $daily_report->images = DB::table('report_daily_detalle_image')
                        ->where('report_daily_detalle_image.report_daily_detalle_id', $daily_report->id)
                        ->get()->toArray();
                }
                $registro_diario_actividad->daily_report = $daily_report;
            }

        }
        return $registro_diarios;

    }
    private function report_personal($registro_diarios, $proyecto)
    {
        foreach ($registro_diarios as $key => $registro_diario) {
            $registro_diario->registro_diario_actividad = DB::table('registro_diario')
                ->select(
                    'registro_diario.*',
                    'proyectos.Codigo',
                    'proyectos.Nombre',
                    'task.Nombre as nombre_tarea',
                    'task.Task_ID',
                    'task.Horas_Estimadas as Horas_Estimadas',
                    'task.Last_Per_Recorded as Last_Per_Recorded',
                    'area_control.nombre as nombre_area',
                    'registro_diario_actividad.Horas_Contract',
                    'registro_diario_actividad.Detalles',
                    'registro_diario_actividad.RDA_ID',
                    'personal.Nick_Name'
                )
                ->join('personal', 'personal.Empleado_ID', 'registro_diario.Empleado_ID')
                ->join('proyectos', 'proyectos.Pro_ID', 'registro_diario.Pro_ID')
                ->join('registro_diario_actividad', 'registro_diario_actividad.Reg_ID', 'registro_diario.Reg_ID')
                ->join('task', 'task.Task_ID', 'registro_diario_actividad.Task_ID')
                ->join('area_control', 'area_control.Area_ID', 'task.Area_ID')
                ->where('registro_diario.Pro_ID', $proyecto->Pro_ID)
                ->where('registro_diario.Fecha', $registro_diario->Fecha)
                ->orderBy('area_control.nombre', 'ASC')
                ->get();
            $total_Horas_Contract = 0;
            foreach ($registro_diario->registro_diario_actividad as $key => $registro_diario_actividad) {
                $total_used = DB::table('registro_diario_actividad')
                    ->select(
                        DB::raw('SUM(registro_diario_actividad.Horas_Contract) as Horas_Contract'),
                    )
                    ->where('registro_diario_actividad.Task_ID', $registro_diario_actividad->Task_ID)
                    ->first();
                $registro_diario_actividad->total_used = $total_used;

                $total_Horas_Contract += $registro_diario_actividad->Horas_Contract;
                $registro_diario_actividad->total_Horas_Contract = $total_Horas_Contract;

                $daily_report = DB::table('report_daily_detalle')
                    ->where('report_daily_detalle.actividad_id', $registro_diario_actividad->Actividad_Id)
                    ->first();
                if ($daily_report != null) {
                    $daily_report->images = DB::table('report_daily_detalle_image')
                        ->where('report_daily_detalle_image.report_daily_detalle_id', $daily_report->id)
                        ->get()->toArray();
                }
                $registro_diario_actividad->daily_report = $daily_report;
            }
        }
        return $registro_diarios;
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
