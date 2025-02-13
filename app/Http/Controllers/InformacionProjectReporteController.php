<?php

namespace App\Http\Controllers;

use App\Exports\InformeProyecto\ReportComparacion;
use DataTables;
use DB;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Excel;
use PDF;
use stdClass;

class InformacionProjectReporteController extends Controller
{
    private $excel;

    public function __construct(Excel $excel)
    {
        $this->excel = $excel;
        $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $proyectos = DB::table('proyectos')->get();
        return view('panel.informacion_proyecto.report', compact('proyectos'));
    }

    public function data_table(Request $request)
    {
        $data = DB::table('proyectos')->select(
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
        /* ->when((request()->from_date && request()->to_date), function ($query) {
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
        }) */
            ->when($request->query('proyectos'), function ($query) use ($request) {
                $proyectos = explode(',', $request->query('proyectos'));
                return $query->whereIn('proyectos.Pro_ID', $proyectos);
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
            ->make(true);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function exportDoc(Request $request)
    {
        $proyectos = $request->query('proyectos');
        $comparacion = DB::select(
            "
                select
                `proyectos`.`Nombre` as `nombre_proyecto`,
                `task`.`Tas_IDT`,
                `task`.`Nombre`,
                ROUND(
                    SUM(
                        COALESCE(task.Horas_Estimadas, 0)
                    ),
                    2
                ) as horas_estimadas,
                ROUND(
                    SUM(
                        COALESCE(
                            registro_diario_actividad.horas_Contract,
                            0
                        )
                    ),
                    2
                ) as horas_usadas,
                ROUND(
                    SUM(
                        COALESCE(task.cc_butdget_qty, 0)
                    ),
                    2
                ) as horas_cc_butdget_qty,
                COUNT(task.`Task_ID`) as cantidad,
                ROUND( (
                        COALESCE(
                            SUM(
                                COALESCE(task.cc_butdget_qty, 0)
                            ) / SUM(
                                COALESCE(
                                    registro_diario_actividad.horas_Contract,
                                    0
                                )
                            ),
                            0
                        )
                    ),
                    2
                ) as actual_porcentaje_rate,
                ROUND( (
                        COALESCE(
                            SUM(
                                COALESCE(task.cc_butdget_qty, 0)
                            ) / SUM(
                                COALESCE(task.Horas_Estimadas, 0)
                            ),
                            0
                        )
                    ),
                    2
                ) as estimate_producction_rate
            from `task`
                left join (SELECT sum(rda.Horas_Contract) as Horas_Contract, rda.`Task_ID` FROM registro_diario_actividad as rda GROUP BY rda.`Task_ID`) as registro_diario_actividad on `registro_diario_actividad`.`Task_ID` = `task`.`Task_ID`
                inner join `proyectos` on `proyectos`.`Pro_ID` = `task`.`Pro_ID`
            where
                `task`.`Pro_ID` in (" . $proyectos . ")
            group by
                `task`.`Tas_IDT`,
                `task`.`Pro_ID`
            order by `task`.`Tas_IDT` asc;
            "
        );

        //dd($comparacion);
        //contador totales por grupo
        $resultado = [];
        $aux = '';

        $total = new stdClass;
        $total->nombre_proyecto = 'Total';
        $total->Tas_IDT = '';
        $total->Nombre = '';
        $total->horas_estimadas = 0;
        $total->horas_usadas = 0;
        $total->horas_cc_butdget_qty = 0;
        $total->cantidad = 0;
        $total->estimate_producction_rate = 0;
        $total->actual_porcentaje_rate = 0;

        foreach ($comparacion as $i => $value) {

            if ($aux == $value->Tas_IDT) {
                $total->horas_estimadas += $value->horas_estimadas;
                $total->horas_usadas += $value->horas_usadas;
                $total->horas_cc_butdget_qty += $value->horas_cc_butdget_qty;
                $total->cantidad += $value->cantidad;
                $total->estimate_producction_rate += $value->estimate_producction_rate;
                $total->actual_porcentaje_rate += $value->actual_porcentaje_rate;
                $total->estimate_producction_rate = round(($total->horas_estimadas == 0 ? 0 : ($total->horas_cc_butdget_qty / $total->horas_estimadas)), 2);
                $total->actual_porcentaje_rate = round(($total->horas_usadas == 0 ? 0 : ($total->horas_cc_butdget_qty / $total->horas_usadas)), 2);

                $resultado[] = $value;
                if ((count($comparacion) - 1) == $i) {
                    $resultado[] = $total;
                }
            } else {
                if (0 != $i) {
                    $resultado[] = $total;
                }

                $total = new stdClass;
                $total->nombre_proyecto = 'Total';
                $total->Tas_IDT = '';
                $total->Nombre = '';
                $total->horas_estimadas = 0;
                $total->horas_usadas = 0;
                $total->horas_cc_butdget_qty = 0;
                $total->cantidad = 0;
                $total->estimate_producction_rate = 0;
                $total->actual_porcentaje_rate = 0;

                $total->horas_estimadas += $value->horas_estimadas;
                $total->horas_usadas += $value->horas_usadas;
                $total->horas_cc_butdget_qty += $value->horas_cc_butdget_qty;
                $total->cantidad += $value->cantidad;
                $total->estimate_producction_rate += $value->estimate_producction_rate;
                $total->actual_porcentaje_rate += $value->actual_porcentaje_rate;
                $total->estimate_producction_rate = round(($total->horas_estimadas == 0 ? 0 : ($total->horas_cc_butdget_qty / $total->horas_estimadas)), 2);
                $total->actual_porcentaje_rate = round(($total->horas_usadas == 0 ? 0 : ($total->horas_cc_butdget_qty / $total->horas_usadas)), 2);

                $resultado[] = $value;
            }
            $aux = $value->Tas_IDT;
        }
        //dd($resultado);
        return $resultado;
    }

    public function export_pdf(Request $request)
    {
        $proyectos = $this->exportDoc($request);
        $pdf = PDF::loadView('panel.informacion_proyecto.report.report_compare_pdf', compact('proyectos'))->setPaper('letter')->setWarnings(false);
        return $pdf->stream("Producción analisis " . date('m-d-Y') . ".pdf");
    }

    public function export_excel(Request $request)
    {
        $proyectos = $this->exportDoc($request);
        return $this->excel->import(new ReportComparacion($proyectos), public_path() . '/plantilla/' . 'report_compare.xlsx')->download(new ReportComparacion($proyectos), "Producción analisis " . date('m-d-Y') . ".xlsx");
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
