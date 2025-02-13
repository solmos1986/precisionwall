<?php

namespace App\Http\Controllers\visitReport;

use App\Exports\reportVisitReport\VisitReport;
use App\Http\Controllers\Controller;
use DataTables;
use DB;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Excel;
use PDF;
use \stdClass;

class ReportsController extends Controller
{
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
        $status = DB::table('estatus')->select('estatus.*')->get();
        $proyectos = DB::table('proyectos')
            ->select('proyectos.*')
            ->get();
        return view('panel.goal.reports.list', compact('status', 'proyectos'));
    }
    /*
     *datatable
     */
    public function datatable(Request $request)
    {
        $proyectos = DB::table('proyectos')
            ->select(
                'proyectos.*',
                'estatus.Nombre_Estatus as Nombre_Estatus',
                DB::raw("CONCAT(COALESCE(project_manager.Nombre,''),' ',COALESCE(project_manager.Apellido_Paterno,''),' ',COALESCE(project_manager.Apellido_Materno,'')) as nombre_project_manager"),
                DB::raw("CONCAT( foreman.Nombre,' ', foreman.Apellido_Paterno,' ',foreman.Apellido_Materno) as nombre_foreman"),
            )
            ->when(!is_null($request->query('multiselect_project')), function ($query) use ($request) {
                return $query->whereIn('proyectos.Pro_ID', explode(',', $request->query('multiselect_project')));
            })
            ->when($request->query('status'), function ($query) use ($request) {
                return $query->where('proyectos.Estatus_ID', $request->query('status'));
            })
            ->when($request->query('from_date'), function ($query) use ($request) {
                return $query->whereBetween('informe_proyecto.Fecha', [date('Y-m-d', strtotime($request->query('from_date'))), date('Y-m-d', strtotime($request->query('to_date')))]);
            })
            ->when($request->query('filtro') == 'null' ? false : true, function ($query) use ($request) {
                switch ($request->query('cargo')) {
                    case 'foreman':
                        return $query->where('proyectos.Foreman_ID', $request->query('filtro'));
                        break;
                    case 'pm':
                        return $query->where('proyectos.Manager_ID', $request->query('filtro'));
                        break;
                    case 'super':
                        return $query->where('proyectos.Coordinador_Obra_ID', $request->query('filtro'));
                        break;
                    case 'APM':
                        return $query->where('proyectos.Manager_ID', $request->query('filtro'));
                        break;
                    default:
                        # code...
                        break;
                }
            })
            ->where('informe_proyecto.delete_informe_proyecto', 1)
            ->join('personal as project_manager', 'project_manager.Empleado_ID', 'proyectos.Manager_ID')
            ->join('personal as foreman', 'foreman.Empleado_ID', 'proyectos.Foreman_ID')
            ->join('estatus', 'estatus.Estatus_ID', 'proyectos.Estatus_ID')
            ->join('informe_proyecto', 'informe_proyecto.Pro_ID', 'proyectos.Pro_ID')
            ->orderBy('proyectos.Nombre', 'ASC')
            ->get();
        return Datatables::of($proyectos)
            ->addIndexColumn()
            ->addColumn('acciones', function ($data) {
                $button = "
                <i class='flaticon-pdf ms-text-primary cursor-pointer descarga_pdf' title='Download PDF' data-proyecto_id='$data->Pro_ID'></i>
                <i class='far fa-file-image ms-text-primary cursor-pointer descarga_pdf_image' title='Download PDF with Image' data-proyecto_id='$data->Pro_ID'></i>
                <i class='flaticon-excel ms-text-primary cursor-pointer descarga_excel'  title='Download Excel' data-proyecto_id='$data->Pro_ID'></i>
                ";
                return $button;
            })
            ->addColumn('check', function ($data) {
                $button = "
                <label class='ms-checkbox-wrap ms-checkbox-info'>
                        <input type='checkbox' value='$data->Pro_ID' class='proyectos' style='opacity: 1;' data-proyecto='$data->Pro_ID'>
                        <i class='ms-checkbox-check'></i>
                      </label>";
                return $button;
            })
            ->rawColumns(['acciones', 'check'])
            ->make(true);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function view_pdf(Request $request)
    {
        $proyectos = DB::table('proyectos')
            ->select(
                'proyectos.*',
                DB::raw("CONCAT( proyectos.Estado,' ', proyectos.Ciudad,' ',proyectos.Calle,' ', proyectos.Numero) as dirrecion"),
                'proyectos.Nombre as nombre_proyecto',
                'empresas.Codigo as codigo_empresa',
                'proyectos.Codigo as codigo_proyecto',
                'empresas.Nombre as nombre_empresa'
            )
            ->whereIn('proyectos.Pro_ID', explode(',', $request->query('proyectos')))
            ->join('empresas', 'empresas.Emp_ID', 'proyectos.Emp_ID')
            ->get();

        foreach ($proyectos as $key => $proyecto) {
            $visit_reports = DB::table('informe_proyecto')
                ->select(
                    'informe_proyecto.*',
                    DB::raw("DATE_FORMAT(informe_proyecto.Fecha , '%m/%d/%Y' ) as Fecha"),
                    'proyectos.Nombre as nombre_proyecto',
                    'empresas.Codigo as codigo_empresa',
                    'proyectos.Codigo as codigo_proyecto',
                    'empresas.Nombre as nombre_empresa',
                    DB::raw("CONCAT(COALESCE(personal.Nombre,''), ' ',  COALESCE(personal.Apellido_Paterno,''), ' ',  COALESCE(personal.Apellido_Materno,'')) as nombre_empleado"),
                )
                ->when(!auth()->user()->verificarRol([1]), function ($query) {
                    return $query->where('informe_proyecto.Empleado_ID', auth()->user()->Empleado_ID);
                })
                ->where('informe_proyecto.Pro_ID', $proyecto->Pro_ID)
                ->where('informe_proyecto.delete_informe_proyecto', '1')
                ->join('proyectos', 'proyectos.Pro_ID', 'informe_proyecto.Pro_ID')
                ->join('empresas', 'empresas.Emp_ID', 'proyectos.Emp_ID')
                ->join('personal', 'personal.Empleado_ID', 'informe_proyecto.Empleado_ID')
                ->orderBy('informe_proyecto.Fecha', 'DESC')
                ->get();
            foreach ($visit_reports as $key => $visit_report) {
                $images = DB::table('goal_imagen')
                    ->select('goal_imagen.*')
                    ->where('id_informe_proyecto', $visit_report->Informe_ID)
                    ->limit(8)
                    ->get()->toArray();
                //autocompletar  imagenes
                if (count($images) < 4) {
                    $relleno = 4 - count($images);
                    for ($i = 0; $i < $relleno; $i++) {
                        $img = new stdClass();
                        $img->imagen = ' ';
                        $images[] = $img;
                    }
                } else {
                    if (count($images) < 8) {
                        $relleno = 8 - count($images);
                        for ($i = 0; $i < $relleno; $i++) {
                            $img = new stdClass();
                            $img->imagen = ' ';
                            $images[] = $img;
                        }
                    }
                }
                $images;
                $visit_report->images = $images;
            }
            $proyecto->visit_reports = $visit_reports;
        }
        //validar report
        if ($imagen = $request->query('imagen') == "true") {
            $pdf = PDF::loadView('panel.goal.reports.view-pdf', compact('proyectos', 'imagen'))->setPaper('a4', 'landscape')->setWarnings(false);
            return $pdf->stream("Field Visit Reports DPF-IMG " . date('m-d-Y') . " .pdf");
        } else {
            $pdf = PDF::loadView('panel.goal.reports.pdf', compact('proyectos', 'imagen'))->setPaper('a4', 'landscape')->setWarnings(false);
            return $pdf->stream("Field Visit Reports DPF " . date('m-d-Y') . ".pdf");
        }
    }
    public function descarga_pdf(Request $request)
    {
        $proyectos = DB::table('proyectos')
            ->select(
                'proyectos.*',
                DB::raw("CONCAT( proyectos.Estado,' ', proyectos.Ciudad,' ',proyectos.Calle,' ', proyectos.Numero) as dirrecion"),
                'proyectos.Nombre as nombre_proyecto',
                'empresas.Codigo as codigo_empresa',
                'proyectos.Codigo as codigo_proyecto',
                'empresas.Nombre as nombre_empresa'
            )
            ->whereIn('proyectos.Pro_ID', explode(',', $request->query('proyectos')))
            ->join('empresas', 'empresas.Emp_ID', 'proyectos.Emp_ID')
            ->get();

        foreach ($proyectos as $key => $proyecto) {
            $visit_reports = DB::table('informe_proyecto')
                ->select(
                    'informe_proyecto.*',
                    DB::raw("DATE_FORMAT(informe_proyecto.Fecha , '%m/%d/%Y' ) as Fecha"),
                    'proyectos.Nombre as nombre_proyecto',
                    'empresas.Codigo as codigo_empresa',
                    'proyectos.Codigo as codigo_proyecto',
                    'empresas.Nombre as nombre_empresa',
                    DB::raw("CONCAT(COALESCE(personal.Nombre,''), ' ',  COALESCE(personal.Apellido_Paterno,''), ' ',  COALESCE(personal.Apellido_Materno,'')) as nombre_empleado"),
                )
                ->when(!auth()->user()->verificarRol([1]), function ($query) {
                    return $query->where('informe_proyecto.Empleado_ID', auth()->user()->Empleado_ID);
                })
                ->where('informe_proyecto.Pro_ID', $proyecto->Pro_ID)
                ->where('informe_proyecto.delete_informe_proyecto', '1')
                ->join('proyectos', 'proyectos.Pro_ID', 'informe_proyecto.Pro_ID')
                ->join('empresas', 'empresas.Emp_ID', 'proyectos.Emp_ID')
                ->join('personal', 'personal.Empleado_ID', 'informe_proyecto.Empleado_ID')
                ->orderBy('informe_proyecto.Fecha', 'DESC')
                ->get();
            foreach ($visit_reports as $key => $visit_report) {
                $images = DB::table('goal_imagen')
                    ->select('goal_imagen.*')
                    ->where('id_informe_proyecto', $visit_report->Informe_ID)
                    ->limit(8)
                    ->get()->toArray();
                //autocompletar  imagenes
                if (count($images) < 4) {
                    $relleno = 4 - count($images);
                    for ($i = 0; $i < $relleno; $i++) {
                        $img = new stdClass();
                        $img->imagen = ' ';
                        $images[] = $img;
                    }
                } else {
                    if (count($images) < 8) {
                        $relleno = 8 - count($images);
                        for ($i = 0; $i < $relleno; $i++) {
                            $img = new stdClass();
                            $img->imagen = ' ';
                            $images[] = $img;
                        }
                    }
                }
                $images;
                $visit_report->images = $images;
            }
            $proyecto->visit_reports = $visit_reports;
        }
        //validar report
        if ($imagen = $request->query('imagen') == "true") {
            $pdf = PDF::loadView('panel.goal.reports.view-pdf', compact('proyectos', 'imagen'))->setPaper('a4', 'landscape')->setWarnings(false);
            return $pdf->download("Field Visit Reports DPF-IMG " . date('m-d-Y') . " .pdf");
        } else {
            $pdf = PDF::loadView('panel.goal.reports.pdf', compact('proyectos', 'imagen'))->setPaper('a4', 'landscape')->setWarnings(false);
            return $pdf->download("Field Visit Reports DPF " . date('m-d-Y') . ".pdf");
        }
    }
    public function view_excel(Request $request)
    {

        $visit_reports = DB::table('informe_proyecto')
            ->select(
                //'informe_proyecto.*',
                'empresas.Codigo as codigo_empresa',
                'proyectos.Codigo as codigo_proyecto',
                'proyectos.Nombre as nombre_proyecto',
                'informe_proyecto.Codigo',
                DB::raw("DATE_FORMAT(informe_proyecto.Fecha , '%m/%d/%Y' ) as Fecha"),
                DB::raw("CONCAT(COALESCE(personal.Nombre,''), ' ',  COALESCE(personal.Apellido_Paterno,''), ' ',  COALESCE(personal.Apellido_Materno,'')) as nombre_empleado"),
                'informe_proyecto.email_send',
                'informe_proyecto.descargas',
                'informe_proyecto.Drywall_comments',
            )
            ->whereIn('proyectos.Pro_ID', explode(',', $request->query('proyectos')))
            ->where('informe_proyecto.delete_informe_proyecto', '1')
            ->join('proyectos', 'proyectos.Pro_ID', 'informe_proyecto.Pro_ID')
            ->join('empresas', 'empresas.Emp_ID', 'proyectos.Emp_ID')
            ->join('personal', 'personal.Empleado_ID', 'informe_proyecto.Empleado_ID')
            ->orderBy('informe_proyecto.Fecha', 'DESC')
            ->get()->toArray();
        /*   foreach ($visit_reports as $key => $visit_report) {
        $images = DB::table('goal_imagen')
        ->select('goal_imagen.*')
        ->where('id_informe_proyecto', $visit_report->Informe_ID)
        ->limit(8)
        ->get()->toArray();
        //autocompletar  imagenes
        if (count($images) < 4) {
        $relleno = 4 - count($images);
        for ($i = 0; $i < $relleno; $i++) {
        $img = new stdClass();
        $img->imagen = ' ';
        $images[] = $img;
        }
        } else {
        if (count($images) < 8) {
        $relleno = 8 - count($images);
        for ($i = 0; $i < $relleno; $i++) {
        $img = new stdClass();
        $img->imagen = ' ';
        $images[] = $img;
        }
        }
        }
        $images;
        $visit_report->images = $images;
        } */
        //dd($visit_reports);
        $test = [];
        $proyecto = '';
        return $this->excel->download(new VisitReport($visit_reports, $proyecto), "Field Visit Reports Excel " . date('m-d-Y') . ".xlsx");
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
