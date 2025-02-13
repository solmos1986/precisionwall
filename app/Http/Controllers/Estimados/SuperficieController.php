<?php

namespace App\Http\Controllers\Estimados;

use App\Exports\estimadoCompletadoExport;
use App\Exports\estimadoExport;
use App\Exports\estimadoExportSov;
use App\Exports\reportsAvances\estimadoExportEdificioSov;
use App\Exports\reportsAvances\estimadoExportFloorSov;
use App\Http\Controllers\Controller;
use DataTables;
use DB;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Excel;
use Storage;
use Validator;
use \stdClass;

class SuperficieController extends Controller
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
    public function datatable_superficie(Request $request)
    {
        $superficies = DB::table('estimado_superficie')
            ->orderBy('estimado_superficie.id', 'DESC')
            ->get();

        return Datatables::of($superficies)
            ->addIndexColumn()
            ->addColumn('acciones', function ($data) {
                $button = "
                <i class='fas ffa fa-wrench ms-text-primary cursor-pointer view_material' title='See tools' data-superficie_id='$data->id'></i>
                <i class='fas fa-pencil-alt ms-text-warning cursor-pointer edit-superficie' title='Edit Surface' data-superficie_id='$data->id'></i>
                <i class='far fa-trash-alt ms-text-danger delete-superficie cursor-pointer' data-superficie_id='$data->id' title='Delete Surface'></i>
                ";
                return $button;
            })
            ->addColumn('miselaneo', function ($data) {
                $button = "";
                if ($data->miselaneo == 'y') {
                    $button = "
                    <span class='badge badge-pill badge-primary'>Yes</span>
                    ";
                }
                return $button;
            })
            ->rawColumns(['acciones', 'miselaneo'])
            ->make(true);
    }
    public function index()
    {
        return view('panel.proyectos.surface');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function list_superficie_standares($id)
    {
        $list_standar = DB::table('estimado_estandar')
            ->select(
                'estimado_estandar.*',
                DB::raw("CONCAT(estimado_estandar.sov_id,' - ', estimado_estandar.Nom_Sov) as Nom_Sov"),
            )
            ->where('estimado_estandar.estimado_superficie_id', $id)
            ->get();
        if ($list_standar) {
            return response()->json([
                'status' => 'ok',
                'data' => $list_standar,
                'message' => 'Registered Successfully',
            ], 200);
        } else {
            return response()->json([
                'status' => 'errors',
                'message' => 'Error de servidor',
            ], 200);
        }
    }
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = array(
            'nombre_surface' => 'required|string',
            'codigo_surface' => 'required|string',
            'descripcion' => 'nullable',
        );

        $messages = [
            'nombre_surface.required' => "The Name field is required",
            'codigo_surface.required' => "The Code field is required",
        ];

        $error = Validator::make($request->all(), $rules, $messages);
        if ($error->errors()->all()) {
            return response()->json([
                'status' => 'errors',
                'message' => $error->errors()->all(),
            ]);
        } else {
            if ($this->validar_nombre($request->nombre_surface)) {
                return response()->json([
                    'status' => 'errors',
                    'message' => ['the name already exists'],
                ]);
            }
            if ($this->validar_codigo($request->codigo_surface)) {
                return response()->json([
                    'status' => 'errors',
                    'message' => ['the Code already exists'],
                ]);
            }
            if ($request->miscellaneous) {
                if ($this->validar_miselaneos($request->codigo_surface)) {
                    $superficie = DB::table('estimado_superficie')
                        ->insertGetId([
                            'nombre' => $request->nombre_surface,
                            'codigo' => $request->codigo_surface,
                            'miselaneo' => 'y',
                        ]);

                } else {
                    return response()->json([
                        'status' => 'errors',
                        'message' => ['miselaneo was already selected'],
                    ]);
                }

            } else {
                $superficie = DB::table('estimado_superficie')
                    ->insertGetId([
                        'nombre' => $request->nombre_surface,
                        'codigo' => $request->codigo_surface,
                        'miselaneo' => 'n',
                    ]);
            }

            return response()->json([
                'status' => 'ok',
                'data' => $superficie,
                'message' => 'Registered Successfully',
            ], 200);
        }
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
        $superficie = DB::table('estimado_superficie')
            ->where('estimado_superficie.id', $id)
            ->first();
        if ($superficie) {
            return response()->json([
                'status' => 'ok',
                'data' => $superficie,
                'message' => 'Get one surface',
            ], 200);
        } else {
            return response()->json([
                'status' => 'errors',
                'message' => 'Error de servidor',
            ], 200);
        }
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
        $rules = array(
            'nombre_surface' => 'required|string',
            'codigo_surface' => 'required|string',
            'descripcion' => 'nullable',
        );

        $messages = [
            'nombre_surface.required' => "The Name field is required",
            'codigo_surface.required' => "The Code field is required",
        ];

        $error = Validator::make($request->all(), $rules, $messages);
        if ($error->errors()->all()) {
            return response()->json([
                'status' => 'errors',
                'message' => $error->errors()->all(),
            ]);
        } else {
            if ($this->validar_nombre($request->nombre_surface, $id)) {
                return response()->json([
                    'status' => 'errors',
                    'message' => ['the name already exists'],
                ]);
            }
            if ($this->validar_codigo($request->codigo_surface, $id)) {
                return response()->json([
                    'status' => 'errors',
                    'message' => ['the Code already exists'],
                ]);
            }
            if ($request->miscellaneous) {
                if ($this->validar_miselaneos()) {

                    $superficie = DB::table('estimado_superficie')
                        ->where('estimado_superficie.id', $id)
                        ->update([
                            'nombre' => $request->nombre_surface,
                            'codigo' => $request->codigo_surface,
                            'miselaneo' => $request->miscellaneous == 'y' ? 'y' : 'n',
                        ]);
                } else {
                    return response()->json([
                        'status' => 'errors',
                        'message' => ['miselaneo was already selected'],
                    ]);
                }
            } else {
                $superficie = DB::table('estimado_superficie')
                    ->where('estimado_superficie.id', $id)
                    ->update([
                        'nombre' => $request->nombre_surface,
                        'codigo' => $request->codigo_surface,
                        'miselaneo' => $request->miscellaneous == 'y' ? 'y' : 'n',
                    ]);
            }
            return response()->json([
                'status' => 'ok',
                'data' => $superficie,
                'message' => 'Registered Successfully',
            ], 200);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    private function validar_miselaneos()
    {
        $validar = DB::table('estimado_superficie')
            ->where('estimado_superficie.miselaneo', 'y')
            ->get();
        if (count($validar) > 0) {
            return false;
        } else {
            //no hay  miselaneo
            return true;
        }
    }
    private function validar_nombre($nombre, $superficie_id = false)
    {
        $validar = DB::table('estimado_superficie')
            ->when($superficie_id, function ($query) use ($superficie_id) {
                return $query->where('estimado_superficie.id', '<>', $superficie_id);
            })
            ->where('estimado_superficie.nombre', $nombre)
            ->get();
        if (count($validar) > 0) {
            return true;
        } else {
            return false;
        }

    }
    private function validar_codigo($codigo, $superficie_id = false)
    {
        $validar = DB::table('estimado_superficie')
            ->when($superficie_id, function ($query) use ($superficie_id) {
                return $query->where('estimado_superficie.id', '<>', $superficie_id);
            })
            ->where('estimado_superficie.codigo', $codigo)
            ->get();
        if (count($validar) > 0) {
            return true;
        } else {
            return false;
        }
    }
    public function destroy($id)
    {
        $superficie = DB::table('estimado_superficie')
            ->where('estimado_superficie.id', $id)
            ->delete();
        if ($superficie) {
            return response()->json([
                'status' => 'ok',
                'data' => $superficie,
                'message' => 'Remove Successfully',
            ], 200);
        } else {
            return response()->json([
                'status' => 'errors',
                'message' => 'Error de servidor',
            ], 200);
        }
    }

    public function export_excel(Request $request)
    {
        $import = $request->query('imports');
        $list_imports = DB::table('estimado_use')
            ->select(
                'estimado_use.estimado_use_import_id as id'
            )
            ->join('estimado', 'estimado.id', 'estimado_use.estimado_id')
            ->where('estimado.id', $import)
            ->get()->pluck('id');

        //informacion de proyectos
        $proyecto = DB::table('estimado')
            ->select(
                'estimado.*',
                'proyectos.Nombre',
                'proyectos.Codigo'
            )
            ->join('proyectos', 'proyectos.Pro_ID', 'estimado.proyecto_id')
            ->where('estimado.id', $import)
            ->first();
        //variaacion para  crear espacios
        $grupos = DB::table('estimado_use_import')
            ->whereIn('estimado_use_import.id', $list_imports)
            ->groupBy('nombre_area')->get();
        $resultado = [];

        foreach ($grupos as $key => $value) {
            $export = DB::table('estimado_use_import')->select(
                'estimado_use_import.id',
                'estimado_use_import.nombre_area',
                'estimado_use_import.nombre_area as areas_repetido',
                'estimado_use_import.cost_code',
                DB::raw("CONCAT(estimado_superficie.nombre,' ',estimado_estandar.nombre,' ',estimado_metodo.nombre) as nombre_descripcion"),
                'estimado_use_import.cc_butdget_qty',
                'estimado_use_import.um',
                'estimado_use_import.of_coast',
                'estimado_use_import.pwt_prod_rate',
                'estimado_use_import.estimate_hours',
                'estimado_use_import.estimate_labor_cost',
                'estimado_use_import.material_or_equipment_unit_cost',
                'estimado_use_import.material_spread_rate_per_unit',
                'estimado_use_import.mat_qty_or_galon',
                'estimado_use_import.mat_um',
                'estimado_use_import.material_cost',
                'estimado_use_import.buscontract_cost',
                'estimado_use_import.equipament_cost',
                'estimado_use_import.other_cost'
            )
                ->join('estimado_superficie', 'estimado_superficie.id', 'estimado_use_import.estimado_superficie_id')
                ->join('estimado_use_metodo', 'estimado_use_import.id', 'estimado_use_metodo.estimado_use_import_id')
                ->join('estimado_estandar', 'estimado_estandar.id', 'estimado_use_metodo.estimado_estandar_id')
                ->join('estimado_metodo', 'estimado_use_metodo.estimado_metodo_id', 'estimado_metodo.id')
                ->whereIn('estimado_use_import.id', $list_imports)
                ->where('estimado_use_import.nombre_area', $value->nombre_area)
                ->orderBy('estimado_use_import.area', 'ASC')
                ->get()->toArray();

            $valores = [];
            foreach ($export as $z => $value) {
                $valores[] = $value->id;
            }
            /* agrupar terminos */
            $grupos = DB::table('estimado_use_import')
                ->select(
                    'estimado_use_import.nombre_area',
                    'estimado_use_import.nombre_area as areas_repetido',
                    DB::raw("CONCAT(estimado_use_import.cost_code,' ') as cost_code"),
                    DB::raw("CONCAT(estimado_use_import.area_description) as nombre_descripcion"),
                    DB::raw("sum(estimado_use_import.cc_butdget_qty) as cc_butdget_qty"),
                    'estimado_use_import.um',
                    DB::raw("sum(estimado_use_import.of_coast) as of_coast"),
                    DB::raw("sum(estimado_use_import.pwt_prod_rate) as pwt_prod_rate"),
                    DB::raw("sum(estimado_use_import.estimate_hours) as estimate_hours"),
                    DB::raw("sum(estimado_use_import.estimate_labor_cost) as estimate_labor_cost"),
                    DB::raw("sum(estimado_use_import.material_or_equipment_unit_cost) as material_or_equipment_unit_cost"),
                    DB::raw("sum(estimado_use_import.material_spread_rate_per_unit) as material_spread_rate_per_unit"),
                    DB::raw("sum(estimado_use_import.mat_qty_or_galon) as mat_qty_or_galon"),
                    'estimado_use_import.mat_um',
                    DB::raw("sum(estimado_use_import.material_cost) as material_cost"),
                    DB::raw("sum(estimado_use_import.price_total) as price_total"),
                )
                ->join('estimado_superficie', 'estimado_superficie.id', 'estimado_use_import.estimado_superficie_id')
                ->join('estimado_use_metodo', 'estimado_use_metodo.estimado_use_import_id', 'estimado_use_import.id')
                ->join('estimado_estandar', 'estimado_estandar.id', 'estimado_use_metodo.estimado_estandar_id')
                ->join('estimado_metodo', 'estimado_use_metodo.estimado_metodo_id', 'estimado_metodo.id')
                ->whereIn('estimado_use_import.id', $valores)
                ->groupBy('estimado_use_import.cost_code')->get();
            //add codigo area
            foreach ($grupos as $j => $grupo) {
                $grupo->nombre_area = "A" . $this->zerofill(($key + 1), 2);
            }
            $espacio = new stdClass();
            $espacio->are = " ";
            $espacio->areas_repetid = " ";
            $espacio->cost_cod = " ";
            $espacio->nombre_descripcio = " ";
            $espacio->cc_butdget_qt = " ";
            $espacio->um = " ";
            $espacio->of_coas = " ";
            $espacio->pwt_prod_rat = " ";
            $espacio->estimate_hour = " ";
            $espacio->estimate_labor_cos = " ";
            $espacio->material_or_equipment_unit_cos = " ";
            $espacio->material_spread_rate_per_uni = " ";
            $espacio->mat_qty_or_galo = " ";
            $espacio->mat_um = " ";
            $espacio->buscontract_cos = " ";
            $espacio->equipament_cost = " ";
            $espacio->other_cost = " ";
            $espacio->price_total = " ";
            $resultado = $this->unir_arrays($grupos, $resultado);
            $resultado = $this->unir_arrays([$espacio], $resultado);
        }
        //IMPRIR DEACUERDO A CALCULOS
        $proyecto->Nombre = substr(str_replace(' ', ' ', strtoupper($proyecto->Nombre)), 0, 15);
        return $this->excel->download(new estimadoExport($resultado, $proyecto), "$proyecto->Nombre Excel " . date('m-d-Y') . ".xlsx");
    }
    private function unir_arrays($array_entrada, $array_salida)
    {
        foreach ($array_entrada as $key => $array) {
            $array_salida[] = $array;
        }
        return $array_salida;
    }

    public function export_excel_sov(Request $request)
    {
        $import = $request->query('imports');
        $list_imports = DB::table('estimado_use')
            ->select(
                'estimado_use.estimado_use_import_id as id'
            )
            ->join('estimado', 'estimado.id', 'estimado_use.estimado_id')
            ->where('estimado.id', $import)
            ->get()->pluck('id');
        //informacion de proyectos
        $proyecto = DB::table('estimado')
            ->select(
                'estimado.*',
                'proyectos.Nombre',
                'proyectos.Codigo'
            )
            ->join('proyectos', 'proyectos.Pro_ID', 'estimado.proyecto_id')
            ->where('estimado.id', $import)
            ->first();
        //variaacion para  crear espacios
        $grupos = DB::table('estimado_use_import')
            ->whereIn('estimado_use_import.id', $list_imports)
            ->groupBy('nombre_area')->get();

        $resultado = [];
        $total = 0;
        $totalporcentaje1 = 0;
        $totalporcentaje2 = 0;
        foreach ($grupos as $key => $value) {
            $grupoTotal = 0;
            $grupoPorcentaje1 = 0;
            $grupoPorcentaje2 = 0;
            $export = DB::table('estimado_use_import')->select(
                'estimado_use_import.nombre_area',
                'estimado_use_import.nombre_area as areas_repetido',
                DB::raw("CONCAT(estimado_estandar.Nom_Sov) as sov"),
                DB::raw("SUM(estimado_use_import.price_total) as price_total"),
            )
                ->join('estimado_superficie', 'estimado_superficie.id', 'estimado_use_import.estimado_superficie_id')
                ->join('estimado_use_metodo', 'estimado_use_import.id', 'estimado_use_metodo.estimado_use_import_id')
                ->join('estimado_estandar', 'estimado_estandar.id', 'estimado_use_metodo.estimado_estandar_id')
                ->join('estimado_metodo', 'estimado_use_metodo.estimado_metodo_id', 'estimado_metodo.id')
                ->whereIn('estimado_use_import.id', $list_imports)
                ->where('estimado_use_import.nombre_area', $value->nombre_area)

                ->groupBy('estimado_estandar.Nom_Sov')
                ->orderBy('estimado_use_import.area', 'ASC')
                ->get()->toArray();
            foreach ($export as $i => $val) {
                $val->nombre_area = "A" . $this->zerofill(($key + 1), 2);
                $val->price_total = round($val->price_total, 2);
                $val->porcentaje1 = round((round($val->price_total, 2) * 0.80), 2); //menos el 20%
                $val->porcentaje2 = round((round($val->price_total, 2) * 0.75), 2); //menos el 25%
                /*suma de totales */
                $total += $val->price_total;
                /*suma por grupo */
                $grupoTotal += $val->price_total;
                $grupoPorcentaje1 += $val->porcentaje1;
                $grupoPorcentaje2 += $val->porcentaje2;
            }
            $subtotal = new stdClass();
            $subtotal->are = $val->nombre_area;
            $subtotal->areas_repetido = $val->areas_repetido;
            $subtotal->cost_cod = "Materials";
            $subtotal->price_total = "";
            $subtotal->porcentaje1 = round(($grupoTotal * 0.2), 2);
            $subtotal->porcentaje2 = round(($grupoTotal * 0.25), 2);
            /*total porcentaje */
            $totalporcentaje1 += $subtotal->porcentaje1;
            $totalporcentaje2 += $subtotal->porcentaje2;

            $espacio = new stdClass();
            $espacio->are = " ";
            $espacio->areas_repetido = " ";
            $espacio->cost_cod = " ";
            $espacio->price_total = " ";
            $espacio->porcentaje1 = " ";
            $espacio->porcentaje2 = " ";

            if ($key == (count($grupos) - 1)) {
                $espacio->cost_cod = "Total:";
                $espacio->price_total = round($total, 2);
                $espacio->porcentaje1 = round($totalporcentaje1, 2);
                $espacio->porcentaje2 = round($totalporcentaje2, 2);
            }
            $resultado = $this->unir_arrays($export, $resultado);
            $resultado = $this->unir_arrays([$subtotal, $espacio], $resultado);
        }
        //dd($resultado);
        //IMPRIMIR DEACUERDO A CALCULOS
        $proyecto->Nombre = substr(str_replace(' ', ' ', strtoupper($proyecto->Nombre)), 0, 15);
        return $this->excel->download(new estimadoExportSov($resultado, $proyecto), "SOV $proyecto->Nombre Excel " . date('m-d-Y') . ".xlsx");
    }
    public function export_excel_estimado_completado_no(Request $request)
    {
        $import = $request->query('estimado_id');
        $list_imports = DB::table('estimado_use')
            ->select(
                'estimado_use.estimado_use_import_id as id'
            )
            ->join('estimado', 'estimado.id', 'estimado_use.estimado_id')
            ->where('estimado.id', $import)
            ->get()->pluck('id');
        //informacion de proyectos
        $proyecto = DB::table('estimado')
            ->select(
                'estimado.*',
                'proyectos.Nombre',
                'proyectos.Codigo'
            )
            ->join('proyectos', 'proyectos.Pro_ID', 'estimado.proyecto_id')
            ->where('estimado.id', $import)
            ->first();
        //variaacion para  crear espacios
        $grupos = DB::table('estimado_use_import')
            ->whereIn('estimado_use_import.id', $list_imports)
            ->groupBy('nombre_area')->get();

        $resultado = [];
        $total = 0;
        foreach ($grupos as $key => $value) {
            $export = DB::table('estimado_use_import')->select(
                'estimado_use_import.nombre_area',
                'estimado_use_import.nombre_area as areas_repetido',
                DB::raw("CONCAT(estimado_estandar.sov_id,' ',estimado_estandar.Nom_Sov) as sov"),
                DB::raw("SUM(estimado_use_import.estimate_hours) as estimate_hours"),
                DB::raw("SUM(estimado_use_import.price_total) as price_total"),
                DB::raw("SUM(estimado_use_import.precio_segun_avance) as precio_segun_avance"),
            )
                ->join('estimado_superficie', 'estimado_superficie.id', 'estimado_use_import.estimado_superficie_id')
                ->join('estimado_use_metodo', 'estimado_use_import.id', 'estimado_use_metodo.estimado_use_import_id')
                ->join('estimado_estandar', 'estimado_estandar.id', 'estimado_use_metodo.estimado_estandar_id')
                ->join('estimado_metodo', 'estimado_use_metodo.estimado_metodo_id', 'estimado_metodo.id')
                ->whereIn('estimado_use_import.id', $list_imports)
                ->where('estimado_use_import.nombre_area', $value->nombre_area)
                ->groupBy('estimado_estandar.Nom_Sov')
                ->orderBy('estimado_use_import.area', 'ASC')
                ->get()->toArray();
            foreach ($export as $i => $val) {
                $val->nombre_area = "A" . ($key + 1);
                $val->estimate_hours = round($val->estimate_hours, 2);
                $total += $val->estimate_hours;
                $export[$i]->porcentaje_completado = ((round(($val->precio_segun_avance / ($val->price_total == 0 ? 1 : $val->price_total)), 2)) * 100) . '%';
                unset($export[$i]->price_total);
                unset($export[$i]->precio_segun_avance);
            }
            $espacio = new stdClass();
            $espacio->are = " ";
            $espacio->areas_repetido = " ";
            $espacio->cost_cod = " ";
            $espacio->estimate_hours = " ";

            if ($key == (count($grupos) - 1)) {
                $espacio->cost_cod = "Total:";
                $espacio->price_total = round($total, 2);
            }
            $resultado = $this->unir_arrays($export, $resultado);
            $resultado = $this->unir_arrays([$espacio], $resultado);

        }

        //IMPRIR DEACUERDO A CALCULOS
        return $this->excel->download(new estimadoCompletadoExport($resultado, $proyecto), 'export estimate sov.xlsx');
    }
    //filtro de precio 0 y sov null o vacio
    private function verificar_tipo_filtro($no_sov_code, $no_precio, $resultado)
    {
        if ($no_sov_code == 'true' && $no_precio == 'true') {
            $resultado = $this->verificar_sov_code($resultado);
            //dd($resultado);
            $resultado = $this->verificar_ceros($resultado);
        } else {
            if ($no_sov_code == 'true') {
                $resultado = $this->verificar_sov_code($resultado);
            }
            if ($no_precio == 'true') {
                $resultado = $this->verificar_ceros($resultado);
            }
        }
        return $resultado;
    }
    public function export_excel_estimado_completado(Request $request)
    {
        $tipo = $request->query('tipo');
        $id = $request->query('id');
        $no_sov_code = $request->query('no_sov_code');
        $no_precio = $request->query('no_precio');
        //
        $obtener_proyecto;
        $resultado = [];
        switch ($tipo) {
            case 'proyecto':
                $resultado = $this->proyecto($id);
                $resultado = $this->verificar_tipo_filtro($no_sov_code, $no_precio, $resultado);
                $obtener_proyecto = DB::table('proyectos')
                    ->select(
                        'proyectos.*',
                    )
                    ->where('proyectos.Pro_ID', $id)
                    ->first();
                return $this->excel->download(new estimadoExportEdificioSov($resultado, $obtener_proyecto), "SOV $obtener_proyecto->Nombre " . date('m-d-Y') . ".xlsx");
                break;
            case 'edificio':
                $resultado = $this->edificio($id);
                $resultado = $this->verificar_tipo_filtro($no_sov_code, $no_precio, $resultado);
                $obtener_proyecto = DB::table('edificios')
                    ->select(
                        'proyectos.*',
                        'edificios.Nombre as nombre_edificio',
                    )
                    ->join('proyectos', 'proyectos.Pro_ID', 'edificios.Pro_ID')
                    ->where('edificios.Edificio_ID', $id)
                    ->first();
                return $this->excel->download(new estimadoExportFloorSov($resultado, $obtener_proyecto), "SOV $obtener_proyecto->Nombre - $obtener_proyecto->nombre_edificio " . date('m-d-Y') . ".xlsx");
                break;
            case 'floor':
                $resultado = $this->floor($id);
                $resultado = $this->verificar_tipo_filtro($no_sov_code, $no_precio, $resultado);
                $obtener_proyecto = DB::table('floor')
                    ->select(
                        'proyectos.*',
                        'floor.Nombre as nombre_floor'
                    )
                    ->join('proyectos', 'proyectos.Pro_ID', 'floor.Pro_ID')
                    ->where('floor.Floor_ID', $id)
                    ->first();
                $obtener_proyecto->Nombre = substr(str_replace(' ', ' ', strtoupper($obtener_proyecto->Nombre)), 0, 15);

                return $this->excel->download(new estimadoCompletadoExport($resultado, $obtener_proyecto), "SOV $obtener_proyecto->Nombre - $obtener_proyecto->nombre_floor " . date('m-d-Y') . ".xlsx");
                break;
            case 'area_control':
                $resultado = $this->area_control($id);
                break;

            default:
                # code...
                break;
        }
        //return $this->excel->download(new estimadoCompletadoExport($resultado, $obtener_proyecto), 'export estimate sov.xlsx');
    }

    private function proyecto($proyecto_id)
    {
        $edificios = DB::table('edificios')
            ->where('edificios.Pro_ID', $proyecto_id)
            ->orderBy('edificios.Nombre', 'ASC')
            ->get();
        foreach ($edificios as $key => $edificio) {
            $floors = DB::table('floor')
                ->where('floor.Edificio_ID', $edificio->Edificio_ID)
                ->orderBy('floor.Nombre', 'ASC')
                ->get();
            foreach ($floors as $key => $floor) {
                $areas_control = DB::table('area_control')
                    ->where('area_control.Floor_ID', $floor->Floor_ID)
                    ->orderBy('area_control.Nombre', 'ASC')
                    ->get();
                foreach ($areas_control as $key => $area) {
                    $tareas = DB::table('task')
                        ->select(
                            'edificios.Nombre as nombre_edificio',
                            'floor.Nombre as nombre_floor',
                            'area_control.Are_IDT',
                            'area_control.Nombre',
                            'task.sov_id',
                            DB::raw("CONCAT(task.sov_descripcion) as sov"),
                            DB::raw("SUM(task.precio_total) as precio_total"),
                            DB::raw("SUM(task.Last_Per_Recorded) as Last_Per_Recorded"),
                            DB::raw('DATE_FORMAT(task.Last_Date_Per_Recorded , "%m/%d/%Y") as Last_Date_Per_Recorded'),
                            DB::raw("SUM(task.precio_segun_avance) as precio_segun_avance"),
                        )
                        ->join('area_control', 'area_control.Area_ID', 'task.Area_ID')
                        ->join('floor', 'floor.Floor_ID', 'area_control.Floor_ID')
                        ->join('edificios', 'edificios.Edificio_ID', 'floor.Edificio_ID')
                        ->where('task.Area_ID', $area->Area_ID)
                        ->groupBy('task.sov_id')
                        ->orderBy('task.sov_id', 'ASC')
                        ->get();
                    $area->task = $tareas;
                }
                $floor->areas = $areas_control;
            }
            $edificio->floors = $floors;
        }
        //dd($edificios);
        $edificios = $this->reorganizar_proyecto($edificios);

        return $edificios;
    }
    private function edificio($edificio_id)
    {
        $floors = DB::table('floor')
            ->where('floor.Edificio_ID', $edificio_id)
            ->orderBy('floor.Nombre', 'ASC')
            ->get();
        foreach ($floors as $key => $floor) {
            $areas_control = DB::table('area_control')
                ->where('area_control.Floor_ID', $floor->Floor_ID)
                ->orderBy('area_control.Nombre', 'ASC')
                ->get();
            foreach ($areas_control as $key => $area) {
                $tareas = DB::table('task')
                    ->select(
                        'floor.Nombre as nombre_floor',
                        'area_control.Are_IDT',
                        'area_control.Nombre',
                        'task.sov_id',
                        DB::raw("CONCAT(task.sov_descripcion) as sov"),
                        DB::raw("SUM(task.precio_total) as precio_total"),
                        DB::raw("SUM(task.Last_Per_Recorded) as Last_Per_Recorded"),
                        DB::raw('DATE_FORMAT(task.Last_Date_Per_Recorded , "%m/%d/%Y") as Last_Date_Per_Recorded'),
                        DB::raw("SUM(task.precio_segun_avance) as precio_segun_avance"),
                    )
                    ->join('area_control', 'area_control.Area_ID', 'task.Area_ID')
                    ->join('floor', 'floor.Floor_ID', 'area_control.Floor_ID')
                    ->where('task.Area_ID', $area->Area_ID)
                    ->groupBy('task.sov_id')
                    ->orderBy('task.sov_id', 'ASC')
                    ->get();
                $area->task = $tareas;
            }
            $floor->areas = $areas_control;
        }
        $floors = $this->reorganizar_edificio($floors);
        return $floors;
    }
    private function floor($floor_id)
    {
        $areas_control = DB::table('area_control')
            ->where('area_control.Floor_ID', $floor_id)
            ->orderBy('area_control.Nombre', 'ASC')
            ->get();
        foreach ($areas_control as $key => $area) {
            $tareas = DB::table('task')
                ->select(
                    'area_control.Are_IDT',
                    'area_control.Nombre',
                    'task.sov_id',
                    DB::raw("CONCAT(task.sov_descripcion) as sov"),
                    DB::raw("SUM(task.precio_total) as precio_total"),
                    DB::raw("SUM(task.Last_Per_Recorded) as Last_Per_Recorded"),
                    DB::raw('DATE_FORMAT(task.Last_Date_Per_Recorded , "%m/%d/%Y") as Last_Date_Per_Recorded'),
                    DB::raw("SUM(task.precio_segun_avance) as precio_segun_avance"),
                )
                ->join('area_control', 'area_control.Area_ID', 'task.Area_ID')
                ->where('task.Area_ID', $area->Area_ID)
                ->groupBy('task.sov_id')
                ->orderBy('task.sov_id', 'ASC')
                ->get();
            $area->task = $tareas;
        }

        $areas_control = $this->reorganizar_floor($areas_control);
        return $areas_control;
    }
    private function reorganizar_proyecto($edificios)
    {
        $resultado = [];
        $total = 0;
        foreach ($edificios as $h => $edificio) {
            foreach ($edificio->floors as $f => $floor) {
                foreach ($floor->areas as $i => $area) {
                    foreach ($area->task as $j => $task) {
                        $task->Last_Per_Recorded = (round(($task->precio_segun_avance / ($task->precio_total == 0 ? 1 : $task->precio_total)), 2));
                        $total += $task->precio_segun_avance;
                        $resultado[] = $task;
                    }
                    $espacio = new stdClass();
                    $espacio->Edificio = " ";
                    $espacio->Are_IDT = " ";
                    $espacio->Nombre = " ";
                    $espacio->Horas_Estimadas = " ";
                    $espacio->sov_id = " ";
                    $espacio->sov = " ";
                    $espacio->precio_total = " ";
                    $espacio->Last_Per_Recorded = " ";
                    $espacio->Last_Date_Per_Recorded = " ";
                    $espacio->precio_segun_avance = " ";

                    //totales
                    if ($i == (count($floor->areas) - 1)) {
                        $espacio->Last_Date_Per_Recorded = "Total:";
                        $espacio->precio_segun_avance = round($total, 2);
                    }
                    $resultado[] = $espacio;
                }
            }
        }

        return $resultado;
    }
    private function verificar_ceros($resultados)
    {
        $filtrar = [];
        foreach ($resultados as $key => $value) {
            //dump(intval($value->precio_total));
            if (intval($value->precio_total) > 0 || !$value->sov_id == '') {
                $filtrar[] = $value;
            }
        }
        return $filtrar;
    }
    private function verificar_sov_code($resultados)
    {
        $filtrar = [];
        foreach ($resultados as $key => $value) {
            //dump(($value));
            if (!$value->sov_id == '' || !$value->sov_id == null) {
                $filtrar[] = $value;
            }
        }
        return $filtrar;
    }

    private function reorganizar_edificio($floors)
    {
        $resultado = [];
        $total = 0;
        foreach ($floors as $f => $floor) {
            foreach ($floor->areas as $j => $area) {
                foreach ($area->task as $i => $task) {
                    //rempleempla el valor actual
                    $task->Last_Per_Recorded = (round(($task->precio_segun_avance / ($task->precio_total == 0 ? 1 : $task->precio_total)), 2));
                    //unset($task[$i]->Horas_Estimadas);
                    $total += $task->precio_segun_avance;
                    $resultado[] = $task;
                }
                $espacio = new stdClass();
                $espacio->Floor = " ";
                $espacio->Are_IDT = " ";
                $espacio->Nombre = " ";
                $espacio->cod_sov = " ";
                $espacio->sov = " ";
                $espacio->precio_total = " ";
                $espacio->Last_Per_Recorded = " ";
                $espacio->Last_Date_Per_Recorded = " ";
                $espacio->precio_segun_avance = " ";
                //totales
                if ($j == (count($floor->areas) - 1)) {
                    $espacio->Last_Date_Per_Recorded = "Total:";
                    $espacio->precio_segun_avance = round($total, 2);
                }
                $resultado[] = $espacio;
            }
        }
        return $resultado;
    }
    private function reorganizar_floor($areas)
    {
        $resultado = [];
        $total = 0;
        foreach ($areas as $j => $area) {
            foreach ($area->task as $i => $task) {
                //rempleempla el valor actual
                $task->Last_Per_Recorded = (round(($task->precio_segun_avance / ($task->precio_total == 0 ? 1 : $task->precio_total)), 2));
                //unset($task[$i]->Horas_Estimadas);
                $total += $task->precio_segun_avance;
                $resultado[] = $task;
            }
            $espacio = new stdClass();
            $espacio->Are_IDT = " ";
            $espacio->Nombre = " ";
            $espacio->cod_sov = " ";
            $espacio->sov = " ";
            $espacio->precio_total = " ";
            $espacio->Last_Per_Recorded = " ";
            $espacio->Last_Date_Per_Recorded = " ";
            $espacio->precio_segun_avance = " ";
            //totales
            if ($j == (count($areas) - 1)) {
                $espacio->Last_Date_Per_Recorded = "Total:";
                $espacio->precio_segun_avance = round($total, 2);
            }
            $resultado[] = $espacio;
        }
        return $resultado;
    }
    private function zerofill($valor, $longitud)
    {
        return str_pad($valor, $longitud, '0', STR_PAD_LEFT);

    }
}
