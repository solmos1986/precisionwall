<?php

namespace App\Http\Controllers\visitReport;

use App\Http\Controllers\Controller;
use App\Model\orden\TipoOrden;
use DataTables;
use DB;
use Illuminate\Http\Request;
use Validator;

class ProjectVisitReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function datatable(Request $request)
    {
        $proyectos = DB::table('proyectos')
            ->select(
                'proyectos.*',
                'empresas.Nombre as nombre_empresa',
                'tipo_proyecto.Nombre_Tipo as tipo',
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
                return $query->whereBetween('proyectos.Fecha_Inicio', [date('Y-m-d', strtotime($request->query('from_date'))), date('Y-m-d', strtotime($request->query('to_date')))]);
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
            ->join('empresas', 'empresas.Emp_ID', 'proyectos.Emp_ID')
            ->join('tipo_proyecto', 'tipo_proyecto.Tipo_ID', 'proyectos.Tipo_ID')
            ->join('personal as project_manager', 'project_manager.Empleado_ID', 'proyectos.Manager_ID')
            ->join('personal as foreman', 'foreman.Empleado_ID', 'proyectos.Foreman_ID')
            ->orderBy('proyectos.Nombre', 'ASC')
            ->get();

        return Datatables::of($proyectos)
            ->addIndexColumn()
            ->addColumn('acciones', function ($data) {
                if (auth()->user()->verificarRol([1])) {
                    $button = "
                    <i class='fa fa-clipboard ms-text-primary cursor-pointer view_materiales' title='View Materials' data-proyecto_id='$data->Pro_ID'></i>
                    <i class='far fa-hospital ms-text-primary cursor-pointer view_report_superficio' title='View Orders' data-proyecto_id='$data->Pro_ID'></i>
                    ";
                } else {
                    $button = "<i class='fa fa-clipboard ms-text-primary cursor-pointer view_materiales' title='View Materials' data-proyecto_id='$data->Pro_ID'></i>";
                }
                return $button;
            })
            ->addColumn('materiales', function ($data) {
                if (auth()->user()->verificarRol([1])) {
                    $cantidad = DB::table('visit_report_orden')
                        ->select(
                            'visit_report_orden.*'
                        )
                        ->where('visit_report_orden.proyecto_id', $data->Pro_ID)
                        ->count();
                    $button = '<h5 class="m-0"><span class="badge badge-primary" style="background-color: #4eb0e9;">' . $cantidad . ' ordered</span></h5>';
                } else {
                    $cantidad = DB::table('visit_report_orden')
                        ->select(
                            'visit_report_orden.*'
                        )
                        ->where('visit_report_orden.proyecto_id', $data->Pro_ID)
                        ->where('visit_report_orden.creado_por', auth()->user()->Empleado_ID)
                        ->count();
                    $button = '<h5 class="m-0"><span class="badge badge-primary" style="background-color: #4eb0e9;">' . $cantidad . ' ordered</span></h5>';
                }
                return $button;
            })
            ->rawColumns(['acciones', 'materiales'])
            ->make(true);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $status = DB::table('estatus')->select('estatus.*')->get();
        return view('panel.goal.proyectos.list', compact('status'));
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
        //dd($request->all());
        $rules = array(
            'superficie_id' => 'array|required',
        );
        $messages = [
            'superficie_id.array' => "Select a surface",
            'superficie_id.required' => "Select a surface",
        ];
        $error = Validator::make($request->all(), $rules, $messages);
        if ($error->errors()->all()) {
            return response()->json([
                'status' => 'errors',
                'message' => $error->errors()->all(),
            ]);
        }

        $delete = DB::table('visit_report_view_material')
            ->where('visit_report_view_material.proyecto_id', $request->proyecto_id)
            ->where('visit_report_view_material.verificado', 'no')
            ->delete();
        foreach ($request->superficie_id as $key => $superficie) {
            $superficie_material = DB::table('visit_report_view_material')
                ->where('proyecto_id', $request->proyecto_id)
                ->where('superficie_id', $superficie)
                ->where('verificado', '!=', 'yes')
                ->insertGetId([
                    'proyecto_id' => $request->proyecto_id,
                    'superficie_id' => $superficie,
                    'verificado' => 'yes',
                ]);
        }
        return response()->json([
            'status' => 'ok',
            'data' => '',
            'message' => 'Changes saved',
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
        $list_superficies = $this->obtener_superficies_proyecto($id);
        $proyecto = DB::table('proyectos')
            ->select(
                'proyectos.*'
            )
            ->where('proyectos.Pro_ID', $id)
            ->first();
        //guardado de proyecto y superficie
        $materiales = DB::table('visit_report_view_material')
            ->where('proyecto_id', $id)
        /* ->where('verficado', 'no') */
            ->delete();
        foreach ($list_superficies as $key => $value) {
            $materiales = DB::table('visit_report_view_material')
                ->insert([
                    'proyecto_id' => $id,
                    'superficie_id' => $value,
                ]);
        }
        $list_superficies = $this->obtener_superficies_proyecto($id);
        $sugerencia = DB::table('estimado_superficie')
            ->whereIn('estimado_superficie.id', $list_superficies)
            ->get();

        //superficies
        $superficies = DB::table('visit_report_view_material')
            ->select(
                'estimado_superficie.*'
            )
            ->join('estimado_superficie', 'estimado_superficie.id', 'visit_report_view_material.superficie_id')
            ->where('visit_report_view_material.proyecto_id', $id)
        //->pluck('estimado_superficie.id');
            ->get();
        return response()->json([
            'status' => 'ok',
            'data' => [
                'sugerencia' => $sugerencia,
                'proyecto' => $proyecto,
                'superficies' => $superficies,
            ],
        ], 200);
    }

    private function obtener_superficies_proyecto($proyecto_id)
    {
        $tareas = DB::table('task')
            ->select('task.ActTas')
            ->where('task.Pro_ID', $proyecto_id)
            ->get();
        //dump($tareas);
        $list_estandares = [];
        foreach ($tareas as $key => $tarea) {
            $estandares = DB::table('estimado_estandar')
                ->select(
                    'estimado_superficie.id',
                )
                ->where('estimado_estandar.codigo', $tarea->ActTas)
                ->join('estimado_superficie', 'estimado_superficie.id', 'estimado_estandar.estimado_superficie_id')
                ->pluck('id');
            $list_estandares = $this->unir_arrays($estandares, $list_estandares);
        }
        $list_estandares = array_unique($list_estandares);
        return $list_estandares;
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
    /*
     *crear orden de materiales
     */
    private function unir_arrays($array_entrada, $array_salida)
    {
        foreach ($array_entrada as $key => $array) {
            $array_salida[] = $array;
        }
        return $array_salida;
    }
    public function crear_materiales($id)
    {
        $list_superficies = $this->obtener_superficies_proyecto($id);
        //guardado de proyecto y superficie
        $materiales = DB::table('visit_report_view_material')
            ->where('proyecto_id', $id)
        /* ->where('verficado', 'no') */
            ->delete();
        foreach ($list_superficies as $key => $value) {
            $materiales = DB::table('visit_report_view_material')
                ->insert([
                    'proyecto_id' => $id,
                    'superficie_id' => $value,
                ]);
        }

        $materiales = DB::table('visit_report_view_material')
            ->select(
                'materiales.*',
                DB::raw('MAX(visit_report_material.cantidad) AS cantidad_sugerida')
            )
            ->join('visit_report_material', 'visit_report_material.superficie_id', 'visit_report_view_material.superficie_id')
            ->join('materiales', 'materiales.Mat_ID', 'visit_report_material.material_id')
            ->where('visit_report_view_material.proyecto_id', $id)
            ->groupBy('materiales.Mat_ID')
            ->get();
        $proyecto = DB::table('proyectos')->where('proyectos.Pro_ID', $id)
            ->first();
        return response()->json([
            'status' => 'ok',
            'data' => [
                'materiales' => $materiales,
                'proyecto' => $proyecto,
                'user' => [
                    'nombre_completo' => auth()->user()->Nombre . ' ' . auth()->user()->Apellido_Paterno . ' ' . auth()->user()->Apellido_Materno,
                    'personal_ID' => auth()->user()->Empleado_ID,
                ],
            ],
        ], 200);
    }
    private function validar_array($array, $validador)
    {
        $resultado = true;
        foreach ($array as $key => $value) {
            if ($value != null) {
                if ($value > $validador) {
                    $resultado = false;
                    break;
                }
            }
        }
        return $resultado;
    }
    public function save_orden(Request $request)
    {
        $rules = array(
            'proyecto_id' => 'required',
            'fecha_registro' => 'required|string',
            'nombre_proyecto' => 'required|string',
            'fecha_envio' => 'date_format:m/d/Y H:i:s|required',
            'nota' => 'nullable',
            'material_id' => 'array|required',
        );

        $messages = [
            'proyecto_id.required' => "The project id is required",
            'fecha_registro.required' => "The Date recordisfield required",
            'nombre_proyecto.required' => "The Nombre Project field is required",
            'fecha_envio.required' => "The Request to Date field is required",
            'fecha_envio.date_format' => "The Request to Date format m/d/Y H:i:s",
            'material_id.required' => "Select a material",
        ];

        $error = Validator::make($request->all(), $rules, $messages);
        if ($error->errors()->all()) {
            return response()->json([
                'status' => 'errors',
                'message' => $error->errors()->all(),
            ]);
        } else {
            if ($this->validar_array($request->quantity, 0) == true) {
                return response()->json([
                    "status" => "errors",
                    "message" => ['the quantity of materials must be greater than 0'],
                ], 200);
            }
            $n_orden = TipoOrden::where('estado', 'creado')->count() + 1;
            $tipo_orden = DB::table('tipo_orden')->insertGetId([
                'proyecto_id' => $request->proyecto_id,
                'estatus_id' => 1,
                'num' => $n_orden,
                'nota' => $request->nota,
                'nombre_trabajo' => $request->nombre_proyecto,
                'estado' => 'creado',
                'fecha_order' => date('Y-m-d H:i:s', strtotime($request->fecha_registro)),
                'fecha_entrega' => date('Y-m-d H:i:s', strtotime($request->fecha_envio)),
                'creado_por' => auth()->user()->Empleado_ID,
                'eliminado' => 0,
            ]);
            $orden_visit_report = DB::table('visit_report_orden')->insertGetId([
                'proyecto_id' => $request->proyecto_id,
                'tipo_orden_id' => $tipo_orden,
                'creado_por' => auth()->user()->Empleado_ID,
            ]);
            $cantidad = [];
            foreach ($request->quantity as $key => $value) {
                if ($value != null) {
                    $cantidad[] = $value;
                }
            }
            if ($request->material_id) {
                foreach ($request->material_id as $key => $material_id) {
                    $materiales = DB::table('tipo_orden_materiales')
                        ->insertGetId([
                            'material_id' => $material_id,
                            'tipo_orden_id' => $tipo_orden,
                            'cant_ordenada' => $cantidad[$key] == null ? 0 : $cantidad[$key],
                            'tipo_orden_material_id' => 1,
                            'cant_registrada' => $cantidad[$key] == null ? 0 : $cantidad[$key],
                            'nota_material' => '',
                            'estado' => 1,
                        ]);
                }
            }
            return response()->json([
                'status' => 'ok',
                'data' => [

                ],
                'message' => 'Order created successfully',
            ], 200);
        }
    }
    public function datatable_orden($id)
    {
        $proyectos = DB::table('visit_report_orden')
            ->select(
                'visit_report_orden.*',
                'proyectos.Nombre as nombre_proyecto',
                'tipo_orden.num',
                'tipo_orden.nota',
                DB::raw("DATE_FORMAT(tipo_orden.fecha_order, '%m/%d/%Y %H:%i:%s') as fecha_order"),
                'tipo_orden_estatus.nombre as nombre_estatus',
                'personal.Usuario as username'
            )
            ->where('visit_report_orden.proyecto_id', $id)
            ->when(!auth()->user()->verificarRol([1]), function ($query) {
                return $query->where('visit_report_orden.creado_por', auth()->user()->Empleado_ID);
            })
            ->join('tipo_orden', 'tipo_orden.id', 'visit_report_orden.tipo_orden_id')
            ->join('tipo_orden_estatus', 'tipo_orden_estatus.id', 'tipo_orden.estatus_id')
            ->join('proyectos', 'proyectos.Pro_ID', 'visit_report_orden.proyecto_id')
            ->join('personal', 'personal.Empleado_ID', 'visit_report_orden.creado_por')
            ->orderBy('tipo_orden.num', 'DESC')
            ->get();

        return Datatables::of($proyectos)
            ->addIndexColumn()
            ->addColumn('acciones', function ($data) {
                $button = "
            <i class='fas fa-eye ms-text-primary cursor-pointer view_materiales' title='View Materials' data-proyecto_id='$data->id'></i>
            <i class='fas fa-pencil-alt ms-text-warning cursor-pointer view_report_superficio' title='View Materials' data-proyecto_id='$data->id'></i>
            ";
                return $button;
            })
            ->rawColumns(['acciones'])
            ->make(true);
    }
}
