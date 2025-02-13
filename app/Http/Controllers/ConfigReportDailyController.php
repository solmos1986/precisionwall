<?php

namespace App\Http\Controllers;

use DataTables;
use DB;
use Illuminate\Http\Request;

class ConfigReportDailyController extends Controller
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

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id)
    {
        $proyecto = DB::table('proyectos')->select('empresas.Codigo as empresa', 'proyectos.*')
            ->where('proyectos.Pro_Id', $id)
            ->join('empresas', 'empresas.Emp_ID', 'proyectos.Emp_ID')
            ->first();

        $address = trim("$proyecto->Ciudad, $proyecto->Zip_Code, $proyecto->Calle");
        $foreman = DB::table('personal')->where('Empleado_ID', $proyecto->Foreman_ID)->first();
        $foreman_name = (empty($foreman)) ? "" : trim($foreman->Nombre . $foreman->Apellido_Paterno . $foreman->Apellido_Materno);

        //test extraccion de informacion
        $report_daily = DB::table('report_daily')->get();
        foreach ($report_daily as $key => $report) {
            $opciones = DB::table('opcion')->where('report_daily_id', $report->id)->get();
            //dd($opciones);
            foreach ($opciones as $key => $opcion) {
                $valores = DB::table('valor')->where('opcion_id', $opcion->id)->get();
                $opcion->valores = $valores;
            }
            $report->opciones = $opciones;
        }
        return view('panel.config_daily_report.create', compact('proyecto', 'address', 'foreman', 'foreman_name', 'report_daily'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function dataTable()
    {
        $report_daily = DB::table('report_daily')->get();
        //añadiendo sus de

        foreach ($report_daily as $key => $value) {
            $report_daily_opcion = DB::table('report_daily_opcion')
                ->where('report_daily_opcion.report_daily_id', $value->id)
                ->get();
            $cadena = "";
            foreach ($report_daily_opcion as $key => $option) {
                $cadena .= "$option->opcion, ";
            }
            $value->detalle = $cadena;
        }
        return Datatables::of($report_daily)
            ->addColumn('actions', function ($data) {
                $button = "
            <i class='fas fa-pencil-alt ms-text-warning editar_modal_option cursor-pointer mr-0' data-id='$data->id' title='Edit Option'></i>
            <i class='far fa-trash-alt ms-text-danger delete_modal_option cursor-pointer' data-id='$data->id' title='Delete Option'></i>
            ";
                return $button;
            })
            ->rawColumns(['actions'])
            ->addIndexColumn()
            ->make(true);
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $delete = DB::table('report_daily_project')->where('report_daily_project.Pro_ID', $request->proyect_id)->delete();

        foreach ($request->options as $key => $option) {
            $insert = DB::table('report_daily_project')->insertGetId([
                'Pro_ID' => $request->proyect_id,
                'report_daily_opcion_id' => $option,
                'used' => 'yes',
            ]);
        }
        return response()->json([
            'status' => 'success',
            'data' => null,
            'message' => 'Successfully',
        ], 200);
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
        $proyecto = DB::table('proyectos')->select('empresas.Codigo as empresa', 'proyectos.*')
            ->where('proyectos.Pro_Id', $id)
            ->join('empresas', 'empresas.Emp_ID', 'proyectos.Emp_ID')
            ->first();

        $address = trim("$proyecto->Ciudad, $proyecto->Zip_Code, $proyecto->Calle");
        $foreman = DB::table('personal')->where('Empleado_ID', $proyecto->Foreman_ID)->first();
        $foreman_name = (empty($foreman)) ? "" : trim($foreman->Nombre . $foreman->Apellido_Paterno . $foreman->Apellido_Materno);
        ///refracto
        $ids = DB::table('report_daily_project')
            ->where('report_daily_project.Pro_ID', $id)
            ->get()
            ->pluck('report_daily_opcion_id')
            ->toArray();

        $report_daily = DB::table('report_daily')
            ->select('report_daily.*')
            ->join('report_daily_opcion', 'report_daily_opcion.report_daily_id', 'report_daily.id')
            ->whereIn('report_daily_opcion.id', $ids)
            ->groupBy('report_daily.id')
            ->get();

        /*  dd($report_daily); */
        foreach ($report_daily as $key => $value) {
            $report_daily_opcion = DB::table('report_daily_project')
                ->select(
                    'report_daily_opcion.*',
                    'report_daily_project.used'
                )
                ->rightJoin('report_daily_opcion', 'report_daily_opcion.id', 'report_daily_project.report_daily_opcion_id')
                ->whereIn('report_daily_opcion.id', $ids)
                ->groupBy('report_daily_opcion.id')
                ->get();
            foreach ($report_daily_opcion as $key => $option) {
                    $report_daily_valor = DB::table('report_daily_valor')
                        ->where('report_daily_valor.report_daily_opcion_id', $option->id)
                        ->get();

                    $cadena = "";
                    foreach ($report_daily_valor as $key => $valor) {
                        $cadena .= "$valor->valor, ";
                    }
                    $option->valores = $cadena;
            }
            $value->options = $report_daily_opcion;
            $value->detalle = $cadena;
        }
        return view('panel.config_daily_report.edit', compact('proyecto', 'address', 'foreman', 'foreman_name', 'report_daily'));
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
