<?php

namespace App\Http\Controllers;

use App\Imports\Import_to_text;
use DataTables;
use DB;
use File;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Storage;
use Validator;
use \stdClass;

class ImportFileController extends Controller
{
    public function __construct(Excel $excel)
    {
        $this->excel = $excel;
        $this->middleware('auth');
    }
    public function datatable_import(Request $reqest)
    {

        $data = $reqest->data == null ? [] : $reqest->data;
        return Datatables::of($data)
            ->addIndexColumn()
            ->addColumn('acciones', function ($data) {
                $button = "
                <a ><i class='fas fa-pencil-alt ms-text-warning'></i></a>
                ";
                return $button;
            })
            ->rawColumns(['acciones'])
            ->make(true);
    }
    /* modulo de usbida de archivos */
    public function leer_excel(Request $request)
    {
        //guardando archivo
        try {
            $data = [];
            if (!$request->hasFile('doc_excel')) {
                return response()->json(['error' => 'no hay archivo'], 200);
            } else {
                $data = Excel::toArray(new Import_to_text, request()->file('doc_excel'));
                $resultados = new stdClass();
                $resultados->titulo = $this->titulos($data[0]);
                $resultados->data = $data[0];
                $resultados->data = $this->separar($resultados->data);

                $resultados->data = $this->separacion_area($resultados->data, $resultados->data);
                $superficies = $this->obtener_superficies();

                $this->comparar_superficie($resultados->data, $superficies);

                $import = $this->insert_database($resultados->data);

                //add constante
                $constante = DB::table('estimado_gene_info')->first();

                $this->update_constante($import, $constante->labor_cost, $constante->index_prod);
                $import = $this->calculando($import);
                //consulta
                $data = $this->actualizar_tabla($import->estimado);
                //$this->copia_imports($import->estimado);
                //constnte
                return response()->json([
                    'status' => 'ok',
                    'data' => $data,
                    'message' => 'Import Successfully',
                ], 200);
            }
        } catch (\Throwable $th) {
            DB::table('estimado_temporal')->delete();
            return response()->json([
                'status' => 'error',
                'data' => 'error',
                'message' => 'Error',
            ], 200);

        }
    }
    private function update_constante($estimado_id, $labor_cost, $index_prod)
    {
        $update = DB::table('estimado')
            ->where('estimado.id', $estimado_id)
            ->update([
                'labor_cost' => $labor_cost,
                'index_prod' => $index_prod,
            ]);
    }
    private function obtener_totales($estimado_id)
    {
        $totales = DB::table('estimado')
            ->where('estimado.id', $estimado_id)
            ->first();
        return $totales;
    }
    private function copia_imports($estimado_id)
    {
        $list_imports = DB::table('estimado_use')
            ->select(
                'estimado_use.estimado_use_import_id as id'
            )
            ->join('estimado', 'estimado.id', 'estimado_use.estimado_id')
            ->where('estimado.id', $estimado_id)
            ->get()->pluck('id');

        foreach ($list_imports as $key => $importacion) {
            $import = DB::table('estimado_use_import')
                ->select(
                    'estimado_use_import.*'
                )
                ->where('estimado_use_import.id', $importacion)
                ->first();
            $import_original = DB::table('estimado_use_import_original')
                ->insertGetId([
                    'area' => $import->area,
                    'cost_code' => $import->cost_code,
                    'area_description' => $import->area_description,
                    'cc_descripcion' => $import->cc_descripcion,
                    'cc_butdget_qty' => $import->cc_butdget_qty,
                    'um' => $import->um,
                    'of_coast' => $import->of_coast,
                    'pwt_prod_rate' => $import->pwt_prod_rate,
                    'estimate_hours' => $import->estimate_hours,
                    'estimate_labor_cost' => $import->estimate_labor_cost,
                    'material_or_equipment_unit_cost' => $import->material_or_equipment_unit_cost,
                    'material_spread_rate_per_unit' => $import->material_spread_rate_per_unit,
                    'mat_qty_or_galon' => $import->mat_qty_or_galon,
                    'mat_um' => $import->mat_um,
                    'material_cost' => $import->material_cost,
                    'buscontract_cost' => $import->buscontract_cost,
                    'equipament_cost' => $import->equipament_cost,
                    'estado' => $import->estado,
                    'other_cost' => $import->other_cost,
                    'price_total' => $import->price_total,
                    'price_each' => $import->price_each,
                    'estimado_superficie_id' => $import->estimado_superficie_id,
                    'estimado_use_id' => $import->estimado_use_id,
                ]);
            $relacion = DB::table('estimado_use_metodo')
                ->where('estimado_use_metodo.estimado_use_import_id', $import->id)
                ->first();
            $import = DB::table('estimado_use_metodo_original')
                ->insertGetId([
                    'estimado_use_import_id' => $import_original,
                    'estimado_metodo_id' => $relacion->estimado_metodo_id,
                    'estimado_estandar_id' => $relacion->estimado_estandar_id,
                    'estado' => 'y',
                ]);
            //add a estimado
            $import = DB::table('estimado_use')
                ->where('estimado_use.estimado_use_import_id', $importacion)
                ->update([
                    'estimado_use_import_original' => $import_original,
                ]);
        }
    }
    private function obtener_import($data)
    {
        $imports = DB::table('estimado_use_import')
            ->select(
                'estimado_use_import.nombre_area',
                'estimado_use_import.area',
                'estimado_use_import.id',
            )
            ->whereIn('estimado_use_import.id', $data->imports)
            ->groupBy('estimado_use_import.nombre_area')->get();

        foreach ($imports as $j => $import) {
            $codigos = DB::table('estimado_use')
                ->select(
                    'estimado_use_import.*',
                    'estimado_metodo.procedimiento',
                    'estimado_superficie.miselaneo'
                )
                ->join('estimado_use_import', 'estimado_use_import.id', 'estimado_use.estimado_use_import_id')
                ->join('estimado_use_metodo', 'estimado_use_metodo.estimado_use_import_id', 'estimado_use_import.id')
                ->join('estimado_metodo', 'estimado_metodo.id', 'estimado_use_metodo.estimado_metodo_id')
                ->join('estimado_superficie', 'estimado_superficie.id', 'estimado_use_import.estimado_superficie_id')
                ->whereIn('estimado_use_import.id', $data->imports)
                ->where('estimado_use_import.nombre_area', $import->nombre_area)
                ->get();
            foreach ($codigos as $key => $import_use) {
                $standares = DB::table('estimado_use_metodo')
                    ->select(
                        'estimado_use_metodo.id as estimado_use_metodo_id',
                        'estimado_metodo.*',
                        'estimado_superficie.id as estimado_superficie_id',
                        'estimado_estandar.id as estimado_estandar_id',
                        'estimado_estandar.nombre as nombre_estandar',
                        'estimado_estandar.codigo as codigo_tarea',
                        'estimado_metodo.id as estimado_metodo_id',
                        'estimado_metodo.nombre as nombre_metodo',
                        'estimado_use_import.id as estimado_use_import_id',
                    )
                    ->join('estimado_use_import', 'estimado_use_import.id', 'estimado_use_metodo.estimado_use_import_id')
                    ->join('estimado_superficie', 'estimado_superficie.id', 'estimado_use_import.estimado_superficie_id')
                    ->join('estimado_estandar', 'estimado_estandar.id', 'estimado_use_metodo.estimado_estandar_id')
                    ->join('estimado_metodo', 'estimado_use_metodo.estimado_metodo_id', 'estimado_metodo.id')
                    ->where('estimado_use_metodo.estimado_use_import_id', $import_use->id)
                    ->get();

                foreach ($standares as $i => $metodo) {
                    $metodos = DB::table('estimado_metodo')
                        ->where('estimado_metodo.estimado_estandar_id', $metodo->estimado_estandar_id)
                        ->get();

                    $standares[$i]->metodos = $metodos;
                }
                $codigos[$key]->tareas = $standares;
            }

            $imports[$j]->superficies = $codigos;
        }
        return $imports;
    }
    private function insert_database($imports)
    {
        $nueva_importacion = DB::table('estimado')
            ->insertGetId([
                'fecha' => date('Y-m-d'),
                'descripcion' => '',
                'estado' => 'activo',
            ]);
        $llaves = [];
        foreach ($imports as $key => $import) {
            try {
                foreach ($import->superficies as $i => $superficie) {
                    $guardar_import = DB::table('estimado_use_import')->insertGetId([
                        'area' => $import->area,
                        'cost_code' => $superficie->codigo,
                        'cc_butdget_qty' => $import->Qty,
                        'price_each' => $import->Price_Each,
                        'price_total' => round(($import->Price_Total / count($import->superficies)), 2),
                        'estado' => 'activo',
                        'estimado_superficie_id' => $superficie->estimado_superficie_id,
                        'nombre_area' => $import->area,
                        'pwt_prod_rate' => $superficie->rate_hour,
                        'mark_up' => $superficie->mark_up,
                        'default_import' => 'y',
                    ]);
                    $detect_standars = DB::table('estimado_use_metodo')
                        ->insert([
                            'estimado_metodo_id' => $superficie->estimado_metodo_id,
                            'estimado_estandar_id' => $superficie->estimado_estandar_id,
                            'estimado_use_import_id' => $guardar_import,
                            'estado' => 'y',
                        ]);
                    $llaves[] = $guardar_import;
                    $areas_import = DB::table('estimado_use')
                        ->insertGetId([
                            'estimado_use_import_id' => $guardar_import,
                            'estimado_id' => $nueva_importacion,
                        ]);
                }
            } catch (\Throwable $th) {
            }
        }
        return $nueva_importacion;
    }
    private function calculando($estimado_id)
    {
        $list_imports = DB::table('estimado_use')
            ->select(
                'estimado_use.estimado_use_import_id as id'
            )
            ->join('estimado', 'estimado.id', 'estimado_use.estimado_id')
            ->where('estimado.id', $estimado_id)
            ->get()->pluck('id');

        foreach ($list_imports as $key => $importacion) {
            /*identificar tipo de procedimiento */
            $verificador = DB::table('estimado_use_metodo')
                ->select(
                    'estimado_metodo.procedimiento'
                )
                ->join('estimado_metodo', 'estimado_metodo.id', 'estimado_use_metodo.estimado_metodo_id')
                ->where('estimado_use_metodo.estimado_use_import_id', $importacion)
                ->first();
            //dump($verificador->procedimiento);
            switch ($verificador->procedimiento) {
                case 'Only Material':
                    $this->procedimiento_materiales($importacion, $estimado_id);
                    break;
                case 'Only Installation':
                    $this->procedimiento_instalacion($importacion, $estimado_id);
                    break;
                default:
                    $this->procedimiento_estimados($importacion, $estimado_id);
                    break;
            }
        }
        $resultado = new stdClass();
        $resultado->imports = $list_imports;
        $resultado->estimado = $estimado_id;

        return $resultado;
    }
    private function titulos($resultado)
    {
        return $resultado[0];
    }

    private function obtener_superficies()
    {
        $superficies = DB::table('estimado_superficie')
            ->get()->toArray();
        return $superficies;
    }
    private function info_default()
    {
        $superficies = DB::table('estimado_superficie')
            ->select(
                'estimado_superficie.codigo',
                DB::raw("CONCAT(estimado_superficie.nombre,' ',estimado_estandar.nombre) as nombre_descripcion"),
                'estimado_metodo.nombre as nombre_metodo',
                'estimado_superficie.id as estimado_superficie_id',
                'estimado_estandar.id as estimado_estandar_id',
                'estimado_metodo.id as estimado_metodo_id',
                'estimado_metodo.*',
            )
            ->join('estimado_estandar', 'estimado_estandar.estimado_superficie_id', 'estimado_superficie.id')
            ->join('estimado_metodo', 'estimado_metodo.estimado_estandar_id', 'estimado_estandar.id')
            ->where('estimado_metodo.defauld', 'y')
            ->where('estimado_superficie.codigo', $codigo)
            ->get()->toArray();
        return $superficies;
    }
    private function info($codigo)
    {
        $superficies = DB::table('estimado_superficie')
            ->select(

                DB::raw("CONCAT(estimado_superficie.nombre,' ',estimado_estandar.nombre) as nombre_descripcion"),
                'estimado_metodo.nombre as nombre_metodo',
                'estimado_superficie.id as estimado_superficie_id',
                'estimado_estandar.id as estimado_estandar_id',
                'estimado_metodo.id as estimado_metodo_id',
                'estimado_metodo.*',
                'estimado_estandar.codigo',
            )
            ->join('estimado_estandar', 'estimado_estandar.estimado_superficie_id', 'estimado_superficie.id')
            ->join('estimado_metodo', 'estimado_metodo.estimado_estandar_id', 'estimado_estandar.id')
            ->where('estimado_metodo.defauld', 'y')
            ->where('estimado_superficie.codigo', $codigo)
            ->get()->toArray();

        return $superficies;
    }

    private function comparar_superficie($nuevos, $superficies)
    {
        $coincidencias = [];
        $codigo = '';
        foreach ($nuevos as $i => $nuevo) {
            if (count($this->info($nuevo->cost_code)) > 0) {
                $nuevo->superficies = $this->info($nuevo->cost_code);
            } else {
                $nuevo->cost_code = $this->miselaneos();
                $nuevo->superficies = $this->info($this->miselaneos());

            }
        }
        return $coincidencias;
    }

    private function separar($data)
    {
        $list_data = [];
        foreach ($data as $key => $value) {
            if ($key > 0) {
                $list_import = new stdClass();
                $list_import->Name = $value[0];
                $list_import->Description = $value[1];
                $list_import->Qty = $value[2];
                $list_import->Units = $value[3];
                $list_import->Cost_Each = $value[4];
                $list_import->Markup = $value[5];
                $list_import->Price_Each = $value[6];
                $list_import->Price_Total = $value[7];
                $list_import->Color = $value[8];
                $list_data[$key] = $list_import;
            }
        }
        $list_data = $this->separacion_columnas($list_data);

        return $list_data;

    }
    private function separacion_columnas($datos)
    {
        $resultado = [];
        foreach ($datos as $key => $dato) {
            $valor = explode(' ', $dato->Name);
            //obtener data de nombre
            $superficie = substr(str_replace(' ', ' ', strtoupper($dato->Name)), 11, 30);
            if (is_numeric($valor[0])) {
                $dato->cost_code = $valor[0];
                $dato->area = $valor[1];
                $dato->superficie = '';
                $dato->metodo = $this->buscar_data($valor, 2);
                $resultado[] = $dato;
            } else {
                if (($valor[0] != '')) {
                    $dato->cost_code = $this->miselaneos();
                    $dato->area = $valor[0];
                    $dato->superficie = '';
                    $dato->metodo = $this->buscar_data($valor, 2);
                    $resultado[] = $dato;
                }

            }
        }
        return $resultado;
    }
    private function miselaneos()
    {
        $data = DB::table('estimado_superficie')
            ->where('estimado_superficie.miselaneo', 'y')
            ->first();
        return $data->codigo;
    }
    private function buscar_data($data, $pocision)
    {
        $valor = '';
        foreach ($data as $key => $value) {
            if ($key == $pocision) {
                $valor = $valor . $value;
            }
        }
        return $valor;
    }

    private function separacion_area($valores, $superficies)
    {

        $temporal = [];
        foreach ($valores as $key => $value) {
            $tes = DB::table('estimado_temporal')->insertGetId([
                'Name' => $value->Name,
                'Description' => $value->Description,
                'Qty' => $value->Qty,
                'Cost_Each' => $value->Cost_Each,
                'Markup' => $value->Markup,
                'Price_Each' => $value->Price_Each,
                'Price_Total' => $value->Price_Total,
                'Color' => $value->Color,
                'cost_code' => $value->cost_code,
                'area' => $value->area,
                'superficie' => $value->superficie,
                'metodo' => $value->metodo,
            ]);
            $temporal[] = $tes;
        }
        $aux = DB::table('estimado_temporal')->select(
            'area',
            'cost_code',
            'Name',
            'Description',
            'Cost_Each',
            'Markup',
            'Price_Each',
            'Price_Total',
            'Color',
            'cost_code',
            'area',
            'superficie',
            'metodo',
            DB::raw("SUM(Qty) as Qty"),
            DB::raw("SUM(Price_Total) as Price_Total") //nuevo
        )
            ->groupBy('area')->groupBy('cost_code')->get();
        foreach ($aux as $key => $value) {
            round($aux[$key]->Qty, 2);
            $aux[$key]->Price_Each = round($value->Price_Total / $value->Qty, 2);
            $aux[$key]->Price_Total = $value->Price_Total;
        }
        DB::table('estimado_temporal')->whereIn('estimado_temporal.id', $temporal)->delete();
        return $aux;
    }

    private function unir_arrays($array_entrada, $array_salida)
    {
        foreach ($array_entrada as $key => $array) {
            $array_salida[] = $array;
        }
        return $array_salida;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user_name = auth()->user()->Nombre . ' ' . auth()->user()->Apellido_Paterno . ' ' . auth()->user()->Apellido_Materno;
        $user_id = auth()->user()->Empleado_ID;
        $labor_cost = DB::table('estimado_gene_info')->get();
        return view('panel.proyectos.import_brake_down', compact('user_name', 'user_id', 'labor_cost'));
    }
    private function select_export_txt($estimado_id)
    {
        $list_imports = DB::table('estimado_use')
            ->select(
                'estimado_use.estimado_use_import_id as id'
            )
            ->join('estimado', 'estimado.id', 'estimado_use.estimado_id')
            ->where('estimado.id', $estimado_id)
            ->get()->pluck('id');
        //variaacion para  crear espacios
        $grupos = DB::table('estimado_use_import')
            ->whereIn('estimado_use_import.id', $list_imports)
            ->groupBy('nombre_area')->get();
        $resultado = [];

        foreach ($grupos as $key => $value) {
            $export = DB::table('estimado_use_import')->select(
                'estimado_use_import.id',
            )
                ->whereIn('estimado_use_import.id', $list_imports)
                ->where('estimado_use_import.nombre_area', $value->nombre_area)
                ->orderBy('estimado_use_import.nombre_area', 'ASC')
                ->get()->toArray();

            $valores = [];
            foreach ($export as $key => $value) {
                $valores[] = $value->id;
            }
            /* agrupar terminos */
            $grupos = DB::table('estimado_use_import')
                ->select(
                    'estimado_use_import.nombre_area',
                    'estimado_use_import.nombre_area as areas_repetido',
                    'estimado_use_import.cost_code',
                    'estimado_use_import.um',
                    DB::raw("CONCAT(estimado_use_import.area_description) as nombre_descripcion"),
                    DB::raw("sum(estimado_use_import.cc_butdget_qty) as cc_butdget_qty"),
                    DB::raw("sum(estimado_use_import.of_coast) as of_coast"),
                    DB::raw("sum(estimado_use_import.pwt_prod_rate) as pwt_prod_rate"),
                    DB::raw("sum(estimado_use_import.estimate_hours) as estimate_hours"),
                    DB::raw("sum(estimado_use_import.estimate_labor_cost) as estimate_labor_cost"),
                    DB::raw("sum(estimado_use_import.material_or_equipment_unit_cost) as material_or_equipment_unit_cost"),
                    DB::raw("sum(estimado_use_import.material_spread_rate_per_unit) as material_spread_rate_per_unit"),
                    DB::raw("sum(estimado_use_import.mat_qty_or_galon) as mat_qty_or_galon"),
                    DB::raw("sum(estimado_use_import.material_cost) as material_cost"),
                    DB::raw("sum(estimado_use_import.price_total) as price_total"),
                    DB::raw("sum(estimado_use_import.price_each) as price_each"),
                    'estimado_metodo.cod_category_labor',
                    'estimado_metodo.cod_category_material',
                    'estimado_metodo.procedimiento',
                    'estimado_metodo.id as estimado_metodo_id'
                )
                ->join('estimado_superficie', 'estimado_superficie.id', 'estimado_use_import.estimado_superficie_id')
                ->join('estimado_use_metodo', 'estimado_use_metodo.estimado_use_import_id', 'estimado_use_import.id')
                ->join('estimado_estandar', 'estimado_estandar.id', 'estimado_use_metodo.estimado_estandar_id')
                ->join('estimado_metodo', 'estimado_use_metodo.estimado_metodo_id', 'estimado_metodo.id')
                ->whereIn('estimado_use_import.id', $valores)
                ->groupBy('estimado_use_import.cost_code')->get();
            $resultado[] = $grupos;
        }
        return $resultado;
    }
    public function load_descarga_txt(Request $request)
    {
        $estimado_id = $request->query('imports');
        //informacion de proyectos
        $proyecto = DB::table('estimado')
            ->select(
                'estimado.*',
                'proyectos.Nombre',
                'proyectos.Codigo',
                DB::raw("CONCAT(personal.Nombre,' ',personal.Apellido_Paterno,' ',personal.Apellido_Materno) as user_name"),
            )
            ->join('personal', 'personal.Empleado_ID', 'estimado.usuario_id')
            ->join('proyectos', 'proyectos.Pro_ID', 'estimado.proyecto_id')
            ->where('estimado.id', $estimado_id)
            ->first();

        $resultado = $this->select_export_txt($estimado_id);
        //dd($resultado);
        $constructor = '#,"Imported","' . $proyecto->user_name . '","on ' . date('m/d/Y', strtotime($proyecto->fecha)) . '","at ' . date('h:iA', strtotime($proyecto->fecha)) . '"' . " \n";
        $constructor .= "*,$proyecto->Codigo,$proyecto->Nombre\n";
        foreach ($resultado as $i => $areas) {
            $constructor .= "E," . "A" . $this->zerofill(($i + 1), 2) . "," . $areas[0]->nombre_area . "\n";
            foreach ($areas as $j => $area) {
                $constructor .= "P,$area->cost_code,$area->nombre_descripcion,," . date('mdY', strtotime($proyecto->fecha)) . ",$area->cc_butdget_qty,$area->um\n";
                $constructor .= "C,$area->cost_code,,$area->cod_category_labor," . date('mdY', strtotime($proyecto->fecha)) . ",$area->estimate_hours,,$area->estimate_labor_cost\n";
                $constructor .= "C,$area->cost_code,,$area->cod_category_material," . date('mdY', strtotime($proyecto->fecha)) . ",,,$area->material_cost\n";
            }
        }
        Storage::disk('public')->put('export.txt', $constructor);
        $file = public_path() . "/docs/export.txt";
        $headers = array(
            'Content-Type: application/txt',
        );
        $proyecto->Nombre = substr(str_replace(' ', ' ', strtoupper($proyecto->Nombre)), 0, 15);
        return response()->download($file, "$proyecto->Nombre For Timberline txt " . date('m-d-Y') . ".txt", $headers);
    }
    public function load_descarga_txt_stp(Request $request)
    {
        $estimado_id = $request->query('imports');
        //informacion de proyectos
        $proyecto = DB::table('estimado')
            ->select(
                'estimado.*',
                'proyectos.Nombre',
                'proyectos.Codigo',
                DB::raw("CONCAT(personal.Nombre,' ',personal.Apellido_Paterno,' ',personal.Apellido_Materno) as user_name"),
            )
            ->join('personal', 'personal.Empleado_ID', 'estimado.usuario_id')
            ->join('proyectos', 'proyectos.Pro_ID', 'estimado.proyecto_id')
            ->where('estimado.id', $estimado_id)
            ->first();
        $resultado = $this->select_export_txt($estimado_id);
        $constructor = "";
        foreach ($resultado as $i => $areas) {
            foreach ($areas as $j => $areas) {
                $constructor .= "NL:      $proyecto->Codigo,$proyecto->Nombre,01,=,01,=, A" . $this->zerofill(($i + 1), 2) . ",$areas->nombre_area,$areas->cost_code,$areas->nombre_descripcion, $areas->estimate_hours\n";
            }
        }

        Storage::disk('public')->put('export-STP.txt', $constructor);
        //PDF file is stored under project/public/download/info.pdf
        $file = public_path() . "/docs/export-STP.txt";

        $headers = array(
            'Content-Type: application/txt',
        );
        $proyecto->Nombre = substr(str_replace(' ', ' ', strtoupper($proyecto->Nombre)), 0, 15);
        return response()->download($file, "$proyecto->Nombre For STP txt " . date('m-d-Y') . ".txt", $headers);
    }
    public function update_metodo(Request $request, $id)
    {
        $estimado_id = $this->modificar_metodo($request->estimado_metodo_id, $id);
        $this->calculando($estimado_id);
        $lista = $this->actualizar_tabla($estimado_id);
        //$this->copia_imports($estimado_id);
        if ($estimado_id) {
            return response()->json([
                'status' => 'ok',
                'data' => $lista,
                'message' => 'Modify Successfully',
            ], 200);
        } else {
            return response()->json([
                'status' => 'errors',
                'message' => 'Error de servidor',
            ], 200);
        }

    }
    public function duplicar_area(Request $request, $id)
    {
        $identificar_area = DB::table('estimado_use_import')
            ->where('estimado_use_import.id', $request->estimado_use_import)
            ->first();

        $copi_area = DB::table('estimado_use_import')
            ->select('estimado_use_import.id')
            ->join('estimado_use', 'estimado_use.estimado_use_import_id', 'estimado_use_import.id')
            ->where('estimado_use_import.area', $identificar_area->area)
            ->where('estimado_use.estimado_id', $request->estimado_id)
            ->get();
        for ($i = -1; $i < $request->numero_copias; $i++) {
            //use import por area
            for ($j = 0; $j < count($copi_area); $j++) {
                $obtener_info = DB::table('estimado_use_metodo')
                    ->select(
                        'estimado_metodo.*',
                        'estimado_estandar.nombre as nombre_estandar',
                        'estimado_use_metodo.id as estimado_use_metodo_id',
                        'estimado_use_import.*',
                        'estimado_use_import.cost_code',
                        'estimado_use_import.cc_butdget_qty',
                        'estimado_use_import.price_each',
                        'estimado_use_import.id as estimado_use_import_id'
                    )
                    ->where('estimado_use_import.id', $copi_area[$j]->id)
                    ->join('estimado_use_import', 'estimado_use_import.id', 'estimado_use_metodo.estimado_use_import_id')
                    ->join('estimado_metodo', 'estimado_metodo.estimado_estandar_id', 'estimado_use_metodo.estimado_estandar_id')
                    ->join('estimado_estandar', 'estimado_estandar.id', 'estimado_use_metodo.estimado_estandar_id')
                    ->first();

                //cantidad de tareas
                $cant_tareas = DB::table('estimado_use_import')
                    ->join('estimado_superficie', 'estimado_superficie.id', 'estimado_use_import.estimado_superficie_id')
                    ->join('estimado_estandar', 'estimado_estandar.estimado_superficie_id', 'estimado_superficie.id')
                    ->where('estimado_use_import.id', $copi_area[$j]->id)
                    ->get()
                    ->toArray();
                if ($i == -1) {
                    $obtener_info->area = $obtener_info->area;
                } else {
                    $obtener_info->area = $obtener_info->area . ' copy-' . ($i + 1);
                }

                $this->dividir_import_crear_import($request->numero_copias, $request, $obtener_info, $obtener_info->area, count($cant_tareas));
            }
        }

        foreach ($copi_area as $key => $value) {
            DB::table('estimado_use_import')
                ->where('estimado_use_import.id', $value->id)
                ->delete();
            DB::table('estimado_use_metodo')
                ->where('estimado_use_metodo.estimado_use_import_id', $value->id)
                ->delete();
            DB::table('estimado_use')
                ->where('estimado_use.estimado_use_import_id', $value->id)
                ->delete();

        }
        //$this->calculando($request->estimado_id);
        //consulta
        $list_imports = DB::table('estimado_use')
            ->select(
                'estimado_use.estimado_use_import_id as id'
            )
            ->join('estimado', 'estimado.id', 'estimado_use.estimado_id')
            ->where('estimado.id', $request->estimado_id, )
            ->get()->pluck('id');
        $data = new stdClass();
        $data->imports = $list_imports;

        $data = $this->actualizar_tabla($request->estimado_id);
        return response()->json([
            'status' => 'ok',
            'data' => $data,
            'message' => 'Duplicado Successfully',
        ], 200);
    }
    private function dividir_import_crear_import($cantidad_copias, $request, $obtener_info, $nombre_area, $cant_tareas)
    {
        $constante = DB::table('estimado')
            ->where('estimado.id', $request->estimado_id)
            ->first();

        $index_prod = $constante->index_prod * 100;
        $diferencia_prod = 100 - $index_prod;
        $guardar_import = DB::table('estimado_use_import')->insertGetId([
            'area' => $nombre_area,
            'nombre_area' => $nombre_area,
            'cost_code' => $obtener_info->cost_code,
            'area_description' => $obtener_info->area_description,
            'cc_descripcion' => $obtener_info->cc_descripcion,
            'cc_butdget_qty' => round(($obtener_info->cc_butdget_qty / ($request->numero_copias + 1)), 4),
            'um' => $obtener_info->um,
            'of_coast' => $obtener_info->of_coast,
            'pwt_prod_rate' => round(($obtener_info->pwt_prod_rate) / ($request->numero_copias + 1), 4),
            'estimate_hours' => round(($obtener_info->estimate_hours / ($request->numero_copias + 1)), 4),
            'estimate_labor_cost' => round(($obtener_info->estimate_labor_cost / ($request->numero_copias + 1)), 4),
            'material_or_equipment_unit_cost' => $obtener_info->material_or_equipment_unit_cost,
            'material_spread_rate_per_unit' => $obtener_info->material_spread_rate_per_unit,
            'mat_qty_or_galon' => $obtener_info->mat_qty_or_galon,
            'mat_um' => $obtener_info->mat_um,
            'material_cost' => round(($obtener_info->material_cost / ($request->numero_copias + 1)), 4),
            'buscontract_cost' => $obtener_info->buscontract_cost,
            'equipament_cost' => $obtener_info->equipament_cost,
            'other_cost' => $obtener_info->other_cost,
            'estado' => 'activo',
            'estimado_superficie_id' => $obtener_info->estimado_superficie_id,
            'price_each' => $obtener_info->price_each,
            'price_total' => round((($obtener_info->price_total) / ($request->numero_copias + 1)), 4),
            'default_import' => $obtener_info->default_import,
            'mark_up' => $obtener_info->mark_up,
        ]);

        $obtener_areas = DB::table('estimado_use_metodo')
            ->where('estimado_use_metodo.estimado_use_import_id', $obtener_info->estimado_use_import_id)
            ->get();

        foreach ($obtener_areas as $key => $value) {
            $create_copia = DB::table('estimado_use_metodo')
                ->insert([
                    'estimado_use_import_id' => $guardar_import,
                    'estimado_metodo_id' => $value->estimado_metodo_id,
                    'estimado_estandar_id' => $value->estimado_estandar_id,
                    'estado' => $value->estado,
                    'estimado_use_id' => $value->estimado_use_id,
                ]);
        }
        ///add importacion
        $areas_import = DB::table('estimado_use')
            ->insertGetId([
                'estimado_use_import_id' => $guardar_import,
                'estimado_id' => $request->estimado_id,
            ]);
        //dd($constante->index_prod,$request->estimado_id );
        $alter_index = DB::table('estimado')
            ->where('estimado.id', $request->estimado_id)
            ->update([
                'index_prod' => round(($constante->index_prod / ($request->numero_copias + 1)), 4),
            ]);
    }
    public function actualizar_tabla($importacion)
    {
        $list_imports = DB::table('estimado_use')
            ->select(
                'estimado_use.estimado_use_import_id as id'
            )
            ->join('estimado', 'estimado.id', 'estimado_use.estimado_id')
            ->where('estimado.id', $importacion)
            ->get()->pluck('id');
        $data = new stdClass();
        $data->imports = $list_imports;
        $data->imports = $this->obtener_import($data);
        //totales
        $estimado_id = $this->actualizar_totales($importacion);
        $data->totales = $this->obtener_totales($importacion);
        return $data;
    }
    public function eliminar_area(Request $request, $id)
    {
        $identificar_area = DB::table('estimado_use_import')
            ->where('estimado_use_import.id', $request->estimado_use_import_id)
            ->first();

        $delete_area = DB::table('estimado_use_import')
            ->select(
                'estimado_use_import.id',
                'estimado_use_import.area',
                'estimado_use_import.cost_code',
            )
            ->join('estimado_use', 'estimado_use.estimado_use_import_id', 'estimado_use_import.id')
            ->where('estimado_use_import.area', $identificar_area->area)
            ->where('estimado_use.estimado_id', $request->estimado_id)
            ->get();
        $resultado = $this->buscar_area_eliminar($delete_area, $request->estimado_id);
        foreach ($delete_area as $key => $value) {
            $eliminar_area = DB::table('estimado_use_import')
                ->where('estimado_use_import.id', $value->id)
                ->delete();
            $eliminar_area_relacion = DB::table('estimado_use')
                ->where('estimado_use.estimado_use_import_id', $value->id)
                ->delete();
        }
        $this->restaurar_valores($resultado);
        $this->calculando($request->estimado_id);
        $estimado_id = $this->actualizar_totales($request->estimado_id);
        $lista = $this->actualizar_tabla($estimado_id);
        if ($eliminar_area) {
            return response()->json([
                'status' => 'ok',
                'data' => $lista,
                'message' => 'Delete Successfully',
            ], 200);
        } else {
            return response()->json([
                'status' => 'errors',
                'message' => 'Error de servidor',
            ], 200);
        }
    }
    private function buscar_area_eliminar($imports_id, $estimado_id)
    {
        $nombre = explode(' ', $imports_id[0]->area);
        if (count($nombre) > 1) {
            $resultado = [];
            foreach ($this->copias as $key => $copia) {
                $grupos = DB::table('estimado_use_import')
                    ->join('estimado_use', 'estimado_use.estimado_use_import_id', 'estimado_use_import.id')
                    ->where('estimado_use_import.area', ($nombre[0] . $this->copias[$key]))
                    ->where('estimado_use.estimado_id', $estimado_id)
                    ->get()->toArray();
                if (count($grupos) > 0) {
                    $resultado[] = $grupos;
                }
            }
        }
        $response = new stdClass();
        $response->areas = $resultado;
        $response->multiplicador = count($resultado);
        return $response;
    }
    private function restaurar_valores($data)
    {
        foreach ($data->areas as $key => $areas) {
            foreach ($areas as $key => $area) {
                $cc_butdget_qty = ($area->cc_butdget_qty * $data->multiplicador) / ($data->multiplicador - 1);
                $price_total = ($area->price_total * $data->multiplicador) / ($data->multiplicador - 1);
                DB::table('estimado_use_import')
                    ->where('estimado_use_import.id', $area->id)
                    ->update([
                        'cc_butdget_qty' => round($cc_butdget_qty, 4),
                        'price_total' => round($price_total, 4),
                    ]);
            }
        }
    }
    private function modificar_metodo($metodo_id, $estimado_use_import_id)
    {
        $update_relaciones = DB::table('estimado_use_metodo')
            ->where('estimado_use_metodo.estimado_use_import_id', $estimado_use_import_id)
            ->update([
                'estimado_metodo_id' => $metodo_id,
            ]);

        $metodo = DB::table('estimado_metodo')
            ->where('estimado_metodo.id', $metodo_id)
            ->first();

        //update import
        $import = DB::table('estimado_use_import')
            ->where('estimado_use_import.id', $estimado_use_import_id)
            ->update([
                'default_import' => 'y',
            ]);
        //estimado
        $estimado = DB::table('estimado_use')
            ->where('estimado_use.estimado_use_import_id', $estimado_use_import_id)
            ->first();

        return $estimado->estimado_id;
    }
    private function actualizar_totales($estimate_id)
    {
        $list_imports = DB::table('estimado_use')
            ->select(
                'estimado_use.estimado_use_import_id as id'
            )
            ->join('estimado', 'estimado.id', 'estimado_use.estimado_id')
            ->where('estimado.id', $estimate_id)
            ->get()->pluck('id');
        //variables para suma de totales
        $total = new stdClass();
        $total->horas_estimadas = 0;
        $total->estimate_labor_cost = 0;
        $total->material_cost = 0;
        $total->price_total = 0;
        $total->total_cost = 0;
        $total->mark_up = 0;
        $total->sub_contract = 0;
        $total->equipo = 0;
        foreach ($list_imports as $key => $importacion) {
            $import = DB::table('estimado_use_import')
                ->select(
                    'estimado_use_import.area',
                    'estimado_superficie.codigo',
                    'estimado_superficie.nombre as nombre_superficie',
                    DB::raw("CONCAT(estimado_superficie.nombre,' ',estimado_estandar.nombre) as nombre_descripcion"),
                    'estimado_metodo.unidad_medida',
                    'estimado_metodo.num_coast',
                    'estimado_use_import.estimado_superficie_id',
                    'estimado_use_import.cc_butdget_qty',
                    'estimado_metodo.rate_hour',
                    'estimado_metodo.materal_spread',
                    'estimado_metodo.material_cost_unit',
                    'estimado_metodo.material_unit_med',
                    //totales
                    'estimado_use_import.estimate_hours',
                    'estimado_use_import.estimate_labor_cost',
                    'estimado_use_import.material_cost',
                    'estimado_use_import.price_total'
                )
                ->join('estimado_use_metodo', 'estimado_use_metodo.estimado_use_import_id', 'estimado_use_import.id')
                ->join('estimado_superficie', 'estimado_superficie.id', 'estimado_use_import.estimado_superficie_id')
                ->join('estimado_estandar', 'estimado_estandar.id', 'estimado_use_metodo.estimado_estandar_id')
                ->join('estimado_metodo', 'estimado_use_metodo.estimado_metodo_id', 'estimado_metodo.id')
                ->where('estimado_use_import.id', $importacion)
                ->first();
            //suma de totales
            $total->horas_estimadas += $import->estimate_hours;
            $total->estimate_labor_cost += $import->estimate_labor_cost;
            $total->material_cost += $import->material_cost;
            $total->price_total += $import->price_total;
        }

        //modificacion en estimados
        $total_cost = ($total->estimate_labor_cost + $total->material_cost + $total->sub_contract + $total->equipo);
        $update_estimado = DB::table('estimado')
            ->where('estimado.id', $estimate_id)
            ->update([
                'estimado.estimated_hours' => $total->horas_estimadas,
                'estimado.estimated_labor_hours' => $total->estimate_labor_cost,
                'estimado.material_cost' => $total->material_cost,
                'estimado.price_total' => $total->price_total,
                /*totales */
                'estimado.total_cost' => $total_cost,
                'estimado.mark_up' => ((1 - ($total_cost / $total->price_total)) * 100),
            ]);
        return $estimate_id;
    }
    public function cambio_constante(Request $request)
    {
        $update = DB::table('estimado')
            ->where('estimado.id', $request->estimado_id)
            ->update([
                'labor_cost' => $request->labor_cost,
            ]);
        $this->calculando($request->estimado_id);
        $data = $this->actualizar_tabla($request->estimado_id);
        return response()->json([
            'status' => 'ok',
            'data' => $data,
            'message' => 'Modify Cosnt Successfully',
        ], 200);
    }
    public function cambio_index_prod(Request $request)
    {
        $this->update_prod_rate($request->estimado_id, $request->index_prod);
        $this->calculando($request->estimado_id);
        $data = $this->actualizar_tabla($request->estimado_id);
        return response()->json([
            'status' => 'ok',
            'data' => $data,
            'message' => 'Modify Cosnt Successfully',
        ], 200);
    }
    private function update_prod_rate($estimado_id, $index_prod)
    {
        $index_prod_entero = $index_prod * 100;
        $diferencia_prod = 100 - $index_prod_entero;

        $list_imports = DB::table('estimado_use')
            ->select(
                'estimado_use.estimado_use_import_id as id'
            )
            ->join('estimado', 'estimado.id', 'estimado_use.estimado_id')
            ->where('estimado.id', $estimado_id)
            ->get()->pluck('id');

        foreach ($list_imports as $key => $importacion) {
            $import = DB::table('estimado_use_import')
                ->select(
                    'estimado_use_import.pwt_prod_rate as pwt_prod_rate'
                )
                ->join('estimado_use', 'estimado_use.estimado_use_import_id', 'estimado_use_import.id')
                ->where('estimado_use.estimado_use_import_id', $importacion)
                ->first();

            if ($import->pwt_prod_rate > 1) {

                $update = DB::table('estimado_use_import')
                    ->where('estimado_use_import.id', $importacion)
                    ->update([
                        'pwt_prod_rate' => round(((($import->pwt_prod_rate / $index_prod_entero) * $diferencia_prod) + $import->pwt_prod_rate), 4),
                    ]);
            }
        }
        //update const
        $update = DB::table('estimado')
            ->where('estimado.id', $estimado_id)
            ->update([
                'index_prod' => $index_prod,
            ]);

    }
    public function edit_import($id)
    {
        $import = DB::table('estimado_use_import')
            ->where('estimado_use_import.id', $id)
            ->first();
        return response()->json($import, 200);
    }
    public function update_import(Request $request, $id)
    {
        $import = DB::table('estimado_use_import')
            ->where('estimado_use_import.id', $id)
            ->update([
                'cc_butdget_qty' => $request->CC_budget_QTY,
                'um' => $request->um,
                'of_coast' => $request->of_coast,
                'pwt_prod_rate' => $request->pwt_pro_rate,
                'estimate_hours' => $request->estimate_hours,
                'estimate_labor_cost' => $request->estimate_labor_hours,
                'material_or_equipment_unit_cost' => $request->material_or_equipment_unit_cost,
                'material_spread_rate_per_unit' => $request->material_spread_rate_per_unit,
                'mat_qty_or_galon' => $request->mat_qty_or_galon,
                'mat_um' => $request->mat_um,
                'material_cost' => $request->material_cost,
                'price_total' => $request->preci_total,
                'mark_up' => $request->mark_up,
                'default_import' => 'n',
                'buscontract_cost' => $request->sub_contrac_cost,
                'equipament_cost' => $request->equipment_cost,
                'other_cost' => $request->other_cost,
                //prueba
                //'porcentaje' => $request->porcentaje,
            ]);
        $use_import = DB::table('estimado_use')
            ->where('estimado_use.estimado_use_import_id', $id)
            ->first();
        $this->calculando($use_import->estimado_id);
        /* extra */
        $import = DB::table('estimado_use_import')
            ->where('estimado_use_import.id', $id)
            ->update([
                'estimate_hours' => $request->estimate_hours,
            ]);
        $data = $this->actualizar_tabla($use_import->estimado_id);
        return response()->json([
            'status' => 'ok',
            'data' => $data,
            'message' => 'Modify Const Successfully',
        ], 200);
    }
    private $copias = [
        ' ',
        ' copy-1',
        ' copy-2',
        ' copy-3',
        ' copy-4',
        ' copy-5',
        ' copy-6',
        ' copy-7',
        ' copy-8',
        ' copy-9',
        ' copy-10',
        ' copy-11',
        ' copy-12',
        ' copy-13',
        ' copy-14',
        ' copy-15',
        ' copy-16',
        ' copy-17',
        ' copy-18',
        ' copy-19',
        ' copy-20',
    ];

    public function obtener_area(Request $request)
    {
        $data = DB::table('estimado_use_import')
            ->where('estimado_use_import.nombre_area', $request->nombre_area)
            ->where('estimado_use.estimado_id', $request->estimado_id)
            ->join('estimado_use', 'estimado_use.estimado_use_import_id', 'estimado_use_import.id')
            ->get();
        return response()->json([
            'nombre_area' => $data[0]->nombre_area,
        ], 200);
    }
    public function validate_modificar_area(Request $request)
    {
        $areas = DB::table('estimado_use_import')
            ->where('estimado_use_import.nombre_area', $request->nombre_area_anterior)
            ->where('estimado_use.estimado_id', $request->estimado_id)
            ->join('estimado_use', 'estimado_use.estimado_use_import_id', 'estimado_use_import.id')
            ->get();
        if ($this->verificar_nombre_area($request->estimado_id, $request->nombre_area)) {
            return response()->json([
                'status' => 'ok',
                'data' => 'no existe',
                'message' => 'new area',
            ], 200);
        } else {
            return response()->json([
                'status' => 'ok',
                'data' => 'existe',
                'message' => 'Area already exists',
            ], 200);
        }
        foreach ($areas as $key => $area) {
            $update = DB::table('estimado_use_import')
                ->where('estimado_use_import.area', $area->area)
                ->update([
                    'nombre_area' => $request->nombre_area,
                ]);
        }
        $data = $this->actualizar_tabla($request->estimado_id);
        return response()->json([
            'status' => 'ok',
            'data' => $data,
            'message' => 'Name changed successfully',
        ], 200);
    }
    public function modificar_area(Request $request)
    {
        $areas = DB::table('estimado_use_import')
            ->where('estimado_use_import.nombre_area', $request->nombre_area_anterior)
            ->where('estimado_use.estimado_id', $request->estimado_id)
            ->join('estimado_use', 'estimado_use.estimado_use_import_id', 'estimado_use_import.id')
            ->get();
        foreach ($areas as $key => $area) {
            $update = DB::table('estimado_use_import')
                ->where('estimado_use_import.area', $area->area)
                ->update([
                    'nombre_area' => $request->nombre_area,
                ]);
        }
        $data = $this->actualizar_tabla($request->estimado_id);
        return response()->json([
            'status' => 'ok',
            'data' => $data,
            'message' => 'Name changed successfully',
        ], 200);
    }
    private function verificar_nombre_area($estimado_id, $nombre_area)
    {
        $areas = DB::table('estimado_use_import')
            ->where('estimado_use_import.nombre_area', $nombre_area)
            ->where('estimado_use.estimado_id', $estimado_id)
            ->join('estimado_use', 'estimado_use.estimado_use_import_id', 'estimado_use_import.id')
            ->get();
        //dd($areas);
        if (count($areas) > 0) {
            //ya exite nombre
            return false;
        } else {
            return true;
        }
    }
    private function procedimiento_estimados($estimado_use_import_id, $estimado_id)
    {
        $import_verficar = DB::table('estimado_use_import')
            ->select('estimado_use_import.default_import')
            ->where('estimado_use_import.id', $estimado_use_import_id)
            ->first();
        //dd('si default', $import_verficar);
        if ($import_verficar->default_import == 'y') {

            $import = DB::table('estimado_use_import')
                ->select(
                    'estimado_use_import.area',
                    'estimado_superficie.codigo',
                    'estimado_estandar.nombre as nombre_estandar',
                    DB::raw("CONCAT(estimado_superficie.nombre,' ',estimado_estandar.nombre) as nombre_descripcion"),
                    'estimado_metodo.unidad_medida',
                    'estimado_metodo.num_coast',
                    'estimado_use_import.estimado_superficie_id',
                    'estimado_use_import.cc_butdget_qty',
                    'estimado_use_import.price_each',
                    'estimado_use_import.price_total',
                    //'estimado_use_import.pwt_prod_rate as rate_hour',
                    'estimado_metodo.rate_hour',
                    'estimado_metodo.materal_spread',
                    'estimado_metodo.material_cost_unit',
                    'estimado_metodo.material_unit_med',
                    //'estimado_use_import.porcentaje',
                )
                ->join('estimado_use_metodo', 'estimado_use_metodo.estimado_use_import_id', 'estimado_use_import.id')
                ->join('estimado_superficie', 'estimado_superficie.id', 'estimado_use_import.estimado_superficie_id')
                ->join('estimado_estandar', 'estimado_estandar.id', 'estimado_use_metodo.estimado_estandar_id')
                ->join('estimado_metodo', 'estimado_use_metodo.estimado_metodo_id', 'estimado_metodo.id')
                ->where('estimado_use_import.id', $estimado_use_import_id)
                ->first();
        } else {
            //dd('no default',$import_verficar);
            $import = DB::table('estimado_use_import')
                ->select(
                    'estimado_use_import.area',
                    'estimado_superficie.codigo',
                    'estimado_estandar.nombre as nombre_estandar',
                    DB::raw("CONCAT(estimado_superficie.nombre,' ',estimado_estandar.nombre) as nombre_descripcion"),
                    'estimado_use_import.um as unidad_medida',
                    'estimado_use_import.of_coast as num_coast',
                    'estimado_use_import.estimado_superficie_id',
                    'estimado_use_import.cc_butdget_qty',
                    'estimado_use_import.price_each',
                    'estimado_use_import.price_total',
                    'estimado_use_import.pwt_prod_rate as rate_hour',
                    //'estimado_metodo.rate_hour',
                    'estimado_use_import.material_spread_rate_per_unit as materal_spread',
                    'estimado_use_import.material_or_equipment_unit_cost as material_cost_unit',
                    'estimado_use_import.mat_um as material_unit_med',
                    //'estimado_use_import.porcentaje',
                )
                ->join('estimado_use_metodo', 'estimado_use_metodo.estimado_use_import_id', 'estimado_use_import.id')
                ->join('estimado_superficie', 'estimado_superficie.id', 'estimado_use_import.estimado_superficie_id')
                ->join('estimado_estandar', 'estimado_estandar.id', 'estimado_use_metodo.estimado_estandar_id')
                ->join('estimado_metodo', 'estimado_use_metodo.estimado_metodo_id', 'estimado_metodo.id')
                ->where('estimado_use_import.id', $estimado_use_import_id)
                ->first();
        }
        $constante = DB::table('estimado')
            ->where('estimado.id', $estimado_id)
            ->first();

        //calculo
        if ($import->rate_hour == 0) {
            $horas_estimadas = 0;
        } else {
            if ($import->rate_hour > 1) {

                $import->rate_hour = ($import->rate_hour * floatval($constante->index_prod));
                //dd($import_verficar->default_import,$import->rate_hour,floatval($constante->index_prod) );
                $horas_estimadas = round($import->cc_butdget_qty / ($import->rate_hour), 2);
            } else {
                $import->rate_hour = ($import->rate_hour);

                $horas_estimadas = round($import->cc_butdget_qty / ($import->rate_hour), 2);
            }
        }
        $estimate_labor_cost = round($constante->labor_cost * $horas_estimadas, 2);
        if ($import->materal_spread == 0) {
            $mat_qty_or_galon = 0;
        } else {
            $mat_qty_or_galon = round($import->cc_butdget_qty / $import->materal_spread, 2);
        }
        $material_cost = round($import->material_cost_unit * $mat_qty_or_galon, 2);

        $update = DB::table('estimado_use_import')
            ->where('estimado_use_import.id', $estimado_use_import_id)
            ->update([
                'estimate_hours' => $horas_estimadas,
                'estimate_labor_cost' => $estimate_labor_cost,
                'mat_qty_or_galon' => $mat_qty_or_galon,
                'material_cost' => $material_cost,
                'area_description' => $import->nombre_estandar,
                'cc_descripcion' => $import->nombre_descripcion,
                'um' => $import->unidad_medida,
                'of_coast' => $import->num_coast,
                'pwt_prod_rate' => $import->rate_hour,
                'material_or_equipment_unit_cost' => $import->material_cost_unit,
                'material_spread_rate_per_unit' => $import->materal_spread,
                'mat_um' => $import->material_unit_med,
                'estado' => 'y',
                'estimado_superficie_id' => $import->estimado_superficie_id,
                //'precio_segun_avance' => ($import->price_total * $import->porcentaje),
            ]);
    }
    private function procedimiento_materiales($estimado_use_import_id, $estimado_id)
    {
        $import = DB::table('estimado_use_import')
            ->select(
                'estimado_use_import.area',
                'estimado_superficie.codigo',
                'estimado_estandar.nombre as nombre_estandar',
                DB::raw("CONCAT(estimado_superficie.nombre,' ',estimado_estandar.nombre) as nombre_descripcion"),
                'estimado_metodo.unidad_medida',
                'estimado_metodo.num_coast',
                'estimado_use_import.estimado_superficie_id',
                'estimado_use_import.cc_butdget_qty',
                'estimado_use_import.price_each',
                'estimado_use_import.price_total',
                //'estimado_use_import.pwt_prod_rate as rate_hour',
                'estimado_metodo.rate_hour',
                'estimado_metodo.materal_spread',
                'estimado_metodo.material_cost_unit',
                'estimado_metodo.material_unit_med',
                'estimado_use_import.mark_up',
                'estimado_use_import.price_total',
                //'estimado_use_import.porcentaje',
            )
            ->join('estimado_use_metodo', 'estimado_use_metodo.estimado_use_import_id', 'estimado_use_import.id')
            ->join('estimado_superficie', 'estimado_superficie.id', 'estimado_use_import.estimado_superficie_id')
            ->join('estimado_estandar', 'estimado_estandar.id', 'estimado_use_metodo.estimado_estandar_id')
            ->join('estimado_metodo', 'estimado_use_metodo.estimado_metodo_id', 'estimado_metodo.id')
            ->where('estimado_use_import.id', $estimado_use_import_id)
            ->first();
        $constante = DB::table('estimado')
            ->where('estimado.id', $estimado_id)
            ->first();
        //calculo

        $material_cost = $import->price_total * $import->mark_up;

        /*  if ($import->materal_spread == 0) {
        $mat_qty_or_galon = 0;
        } else {
        $mat_qty_or_galon = round($import->cc_butdget_qty / $import->materal_spread, 2);
        }
        $material_cost = round($import->material_cost_unit * $mat_qty_or_galon, 2); */

        $update = DB::table('estimado_use_import')
            ->where('estimado_use_import.id', $estimado_use_import_id)
            ->update([
                'material_cost' => $material_cost,
                'estimate_hours' => '-',
                'estimate_labor_cost' => '-',
                'mat_qty_or_galon' => '-',
                'area_description' => $import->nombre_estandar,
                'cc_descripcion' => $import->nombre_descripcion,
                'um' => $import->unidad_medida,
                'of_coast' => '-',
                //'pwt_prod_rate' => ($import->rate_hour * floatval($constante->index_prod)),
                'material_or_equipment_unit_cost' => '-',
                'material_spread_rate_per_unit' => '-',
                'mat_um' => $import->material_unit_med,
                'estado' => 'y',
                'estimado_superficie_id' => $import->estimado_superficie_id,
                //'precio_segun_avance' => ($import->price_total * $import->porcentaje),
            ]);
    }
    private function procedimiento_instalacion($estimado_use_import_id, $estimado_id)
    {
        $import = DB::table('estimado_use_import')
            ->select(
                'estimado_use_import.area',
                'estimado_superficie.codigo',
                'estimado_estandar.nombre as nombre_estandar',
                DB::raw("CONCAT(estimado_superficie.nombre,' ',estimado_estandar.nombre) as nombre_descripcion"),
                'estimado_metodo.unidad_medida',
                'estimado_metodo.num_coast',
                'estimado_use_import.estimado_superficie_id',
                'estimado_use_import.cc_butdget_qty',
                'estimado_use_import.price_each',
                'estimado_use_import.price_total',
                //'estimado_use_import.pwt_prod_rate as rate_hour',
                'estimado_metodo.rate_hour',
                'estimado_metodo.materal_spread',
                'estimado_metodo.material_cost_unit',
                'estimado_metodo.material_unit_med',
                'estimado_use_import.mark_up',
                'estimado_use_import.price_total',
                //'estimado_use_import.porcentaje',
            )
            ->join('estimado_use_metodo', 'estimado_use_metodo.estimado_use_import_id', 'estimado_use_import.id')
            ->join('estimado_superficie', 'estimado_superficie.id', 'estimado_use_import.estimado_superficie_id')
            ->join('estimado_estandar', 'estimado_estandar.id', 'estimado_use_metodo.estimado_estandar_id')
            ->join('estimado_metodo', 'estimado_use_metodo.estimado_metodo_id', 'estimado_metodo.id')
            ->where('estimado_use_import.id', $estimado_use_import_id)
            ->first();
        $constante = DB::table('estimado')
            ->where('estimado.id', $estimado_id)
            ->first();
        //calculo prueba
        $horas_estimadas = ($import->price_total * $import->mark_up) / $constante->labor_cost;
        $estimate_labor_cost = $import->price_total * $import->mark_up;

        /* if ($import->materal_spread == 0) {
        $mat_qty_or_galon = 0;
        } else {
        $mat_qty_or_galon = round($import->cc_butdget_qty / $import->materal_spread, 2);
        }
        $material_cost = round($import->material_cost_unit * $mat_qty_or_galon, 2); */

        $update = DB::table('estimado_use_import')
            ->where('estimado_use_import.id', $estimado_use_import_id)
            ->update([
                'estimate_hours' => $horas_estimadas,
                'estimate_labor_cost' => $estimate_labor_cost,
                'material_cost' => '-',
                'mat_qty_or_galon' => '-',
                'material_cost' => '-',
                'area_description' => $import->nombre_estandar,
                'cc_descripcion' => $import->nombre_descripcion,
                'um' => $import->unidad_medida,
                'of_coast' => '-',
                //'pwt_prod_rate' => ($import->rate_hour * floatval($constante->index_prod)),
                'material_or_equipment_unit_cost' => '-',
                'material_spread_rate_per_unit' => '-',
                'mat_um' => $import->material_unit_med,
                'estado' => 'y',
                'estimado_superficie_id' => $import->estimado_superficie_id,
                //'precio_segun_avance' => ($import->price_total * $import->porcentaje),
            ]);
    }
    public function get_import_project(Request $request, $id)
    {
        $import = DB::table('estimado')
            ->select(
                'estimado.*',
                'proyectos.Nombre',
                'proyectos.Codigo'
            )
            ->leftjoin('proyectos', 'proyectos.Pro_ID', 'estimado.proyecto_id')
            ->where('estimado.id', $id)
            ->first();
        return response()->json($import, 200);
    }
    public function save_import_project(Request $request)
    {
        $rules = array(
            'estimado_id' => 'required|string',
            'proyecto_id' => 'required|string',
            'descripcion' => 'nullable',
            'user_id' => 'required|string',
        );
        $messages = [
            'estimado_id.required' => "The Import is required",
            'proyecto_id.required' => "The Project field is required",
            'user_id.required' => "The User field is required",
        ];
        $error = Validator::make($request->all(), $rules, $messages);
        if ($error->errors()->all()) {
            return response()->json([
                'status' => 'errors',
                'message' => $error->errors()->all(),
            ]);
        } else {
            $estimado = DB::table('estimado')
                ->where('estimado.id', $request->estimado_id)
                ->update([
                    'proyecto_id' => $request->proyecto_id,
                    'usuario_id' => $request->user_id,
                    'descripcion' => $request->description,
                    'fecha' => date('Y-m-d H:i:s', strtotime($request->fecha_registro)),
                ]);
            if ($estimado) {
                return response()->json([
                    'status' => 'ok',
                    'message' => 'Registered Successfully',
                ], 200);
            } else {
                return response()->json([
                    'status' => 'errors',
                    'message' => 'Error de servidor',
                ], 200);
            }
        }
    }

    public function datatable_historial_import($id)
    {
        $estimados = DB::table('estimado')
            ->select(
                'estimado.*',
                DB::raw('DATE_FORMAT(estimado.fecha , "%m/%d/%Y %H:%i:%s %p") as fecha'),
                DB::raw('CONCAT(personal.Nombre ," ",personal.Apellido_Paterno ," ",personal.Apellido_Materno ) as usuario'),
                'proyectos.Nombre'
                ///concadenar nombre y mostrar en la vista
            )
            ->where('estimado.usuario_id', $id)
            ->join('personal', 'personal.Empleado_ID', 'estimado.usuario_id')
            ->join('proyectos', 'proyectos.Pro_ID', 'estimado.proyecto_id')
            ->orderBy('estimado.id', 'DESC')
            ->get();
        return Datatables::of($estimados)
            ->addIndexColumn()
            ->addColumn('acciones', function ($data) {
                $button = "
                <i class='far fa-trash-alt ms-text-danger delete_estimado cursor-pointer' data-estimado_id='$data->id' title='Delete Estimate'></i>";
                return $button;
            })
            ->addColumn('export', function ($data) {
                if ($data->import_proyecto == 'y') {
                    $button = "
                    <i class='fa fa-edit ms-text-primary export_estimado cursor-pointer'  data-estimado_id='$data->id' title='Export Estimate'></i>
                    <i class='fa fa-database ms-text-danger ' data-estimado_id='$data->id' title='Imported into database'></i>
                ";
                } else {
                    $button = "
                    <i class='fa fa-edit ms-text-primary export_estimado cursor-pointer'  data-estimado_id='$data->id' title='Export Estimate'></i>
                    ";
                }
                return $button;
            })
            ->rawColumns(['acciones', 'export'])
            ->make(true);
    }
    public function delete_historial_import($id)
    {
        $estimado = $this->delete_import($id);
        /*delete todos los no guardados */
        $estimados_eliminar = DB::table('estimado')
            ->where('estimado.proyecto_id', 0)
            ->get()
            ->pluck('id');

        foreach ($estimados_eliminar as $key => $delete) {
            $this->delete_import($delete);
        }
        if ($estimado) {
            return response()->json([
                'status' => 'ok',
                'message' => 'Delete Successfully',
            ], 200);
        } else {
            return response()->json([
                'status' => 'errors',
                'message' => 'Error de servidor',
            ], 200);
        }
    }
    private function delete_import($id)
    {
        $estimado = DB::table('estimado')
            ->where('estimado.id', $id)
            ->delete();

        $ids_estimado_use = DB::table('estimado_use')
            ->where('estimado_use.estimado_id', $id)
            ->get()
            ->pluck('estimado_use_import_id');

        $delete_estimado_use_import = DB::table('estimado_use_import')
            ->whereIn('estimado_use_import.id', $ids_estimado_use)
            ->delete();

        $estimado_use = DB::table('estimado_use')
            ->where('estimado_use.estimado_id', $id)
            ->delete();

        return $estimado;
    }
    public function export_historial_import($id)
    {
        $data = $this->actualizar_tabla($id);
        return response()->json([
            'status' => 'ok',
            'data' => $data,
            'message' => 'Delete Successfully',
        ], 200);

    }
    private function zerofill($valor, $longitud)
    {
        return str_pad($valor, $longitud, '0', STR_PAD_LEFT);
    }
   
}
