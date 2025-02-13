<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;
use stdClass;

class ImportFileStructure extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    private function calculo_horas_estimadas($tareas)
    {
        foreach ($tareas as $key => $tarea) {
            $registro_diario_actividad = DB::table('registro_diario_actividad')
                ->select(
                    DB::raw("SUM(registro_diario_actividad.Horas_Contract) as horas_usadas"),
                )
                ->where('Task_ID', $tarea->Task_ID)
                ->first();
            /*   $update_tarea = DB::table('task')
            ->where('task.Task_ID', $tarea->Task_ID)
            ->update([
            'Total_HCode' => $registro_diario_actividad->horas_usadas,
            ]); */
            $tarea->horas_usadas = round($registro_diario_actividad->horas_usadas, 2);
        }
        return $tareas;
    }
    private function obtener_totales($estimado_id)
    {
        $totales = DB::table('estimado')
            ->where('estimado.id', $estimado_id)
            ->first();
        return $totales;
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
    public function export_to_excel($estimado_id)
    {
        $list_imports = DB::table('estimado_use')
            ->select(
                'estimado_use.estimado_use_import_id as id'
            )
            ->join('estimado', 'estimado.id', 'estimado_use.estimado_id')
            ->where('estimado.id', $estimado_id)
            ->get()->pluck('id');

        //informacion de proyectos
        $proyecto = DB::table('estimado')
            ->select(
                'estimado.*',
                'proyectos.Nombre',
                'proyectos.Codigo'
            )
            ->join('proyectos', 'proyectos.Pro_ID', 'estimado.proyecto_id')
            ->where('estimado.id', $estimado_id)
            ->first();
        //variacion para crear espacios
        $grupos_import = DB::table('estimado_use_import')
            ->select(
                'estimado_use_import.area',
                'estimado_use_import.nombre_area',
                'estimado_use_import.nombre_area as areas_repetido',
                DB::raw("sum(estimado_use_import.estimate_hours) as estimate_hours"),
            )
            ->whereIn('estimado_use_import.id', $list_imports)
            ->groupBy('nombre_area')->get();

        foreach ($grupos_import as $key => $value) {
            $export = DB::table('estimado_use_import')->select(
                'estimado_use_import.id',
                'estimado_use_import.nombre_area',
                'estimado_use_import.nombre_area as areas_repetido',
                'estimado_use_import.cost_code',
                DB::raw("CONCAT(estimado_superficie.nombre,' ',estimado_estandar.nombre) as nombre_descripcion"),
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
            $resultado = [];
            $grupos = DB::table('estimado_use_import')
                ->select(
                    'estimado_use_import.id',
                    'estimado_use_import.nombre_area',
                    'estimado_use_import.nombre_area as area',
                    'estimado_use_import.cost_code',
                    DB::raw("CONCAT(estimado_use_import.area_description) as nombre_descripcion"),
                    DB::raw("sum(estimado_use_import.cc_butdget_qty) as cc_butdget_qty"),
                    'estimado_use_import.um',
                    DB::raw("estimado_use_import.of_coast as of_coast"),
                    DB::raw("estimado_use_import.pwt_prod_rate as pwt_prod_rate"),
                    DB::raw("sum(estimado_use_import.estimate_hours) as estimate_hours"),
                    DB::raw("sum(estimado_use_import.estimate_labor_cost) as estimate_labor_cost"),
                    DB::raw("estimado_use_import.material_or_equipment_unit_cost as material_or_equipment_unit_cost"),
                    DB::raw("estimado_use_import.material_spread_rate_per_unit as material_spread_rate_per_unit"),
                    DB::raw("sum(estimado_use_import.mat_qty_or_galon) as mat_qty_or_galon"),
                    'estimado_use_import.mat_um',
                    DB::raw("sum(estimado_use_import.material_cost) as material_cost"),
                    DB::raw("sum(estimado_use_import.price_total) as price_total"),
                    'estimado_estandar.sov_id',
                    'estimado_estandar.Nom_Sov',
                    DB::raw("sum(estimado_use_import.buscontract_cost) as buscontract_cost"),
                    DB::raw("sum(estimado_use_import.equipament_cost) as equipament_cost"),
                    DB::raw("sum(estimado_use_import.other_cost) as other_cost"),
                    //DB::raw("sum(estimado_use_import.porcentaje) as porcentaje"),
                )
                ->join('estimado_superficie', 'estimado_superficie.id', 'estimado_use_import.estimado_superficie_id')
                ->join('estimado_use_metodo', 'estimado_use_metodo.estimado_use_import_id', 'estimado_use_import.id')
                ->join('estimado_estandar', 'estimado_estandar.id', 'estimado_use_metodo.estimado_estandar_id')
                ->join('estimado_metodo', 'estimado_use_metodo.estimado_metodo_id', 'estimado_metodo.id')
                ->whereIn('estimado_use_import.id', $valores)
                ->groupBy('estimado_use_import.cost_code')->get();
            //add codigo area
            foreach ($grupos as $j => $grupo) {
                $grupo->area = "A" . $this->zerofill(($key + 1), 2);
            }
            $resultado = $this->unir_arrays($grupos, $resultado);
            $grupos_import[$key]->imports = $resultado;
        }
        return $grupos_import;
    }
    private function unir_arrays($array_entrada, $array_salida)
    {
        foreach ($array_entrada as $key => $array) {
            $array_salida[] = $array;
        }
        return $array_salida;
    }

    public function import_database($id)
    {
        //create edificio =  01
        //create floor     =  01
        //*importacion similar a export excel
        $valores_repetidos = []; //variable contenedor de repetidos
        $imports = $this->export_to_excel($id);
        //dd($imports);
        //proyectos
        $proyecto = DB::table('estimado')
            ->select(
                'floor.*',
                'estimado.*'
            )
            ->join('proyectos', 'proyectos.Pro_ID', 'estimado.proyecto_id')
            ->join('floor', 'floor.Pro_ID', 'estimado.proyecto_id')
            ->where('estimado.id', $id)
            ->first();

        /*creacion de eficio por defaul */
        $crear_edificio_default = DB::table('edificios')->insertGetId([
            'Edi_IDT' => '01',
            'Nombre' => '=',
            'Descripcion' => '',
            'Horas_Estimadas' => 0,
            'Material_Estimado' => 0,
            'Porcentaje' => 0,
            'Pro_ID' => $proyecto->Pro_ID,
            'Aux1' => '',
            'Aux2' => '',
        ]);
        /* pendiente modificar el floor q contiene esto import*/

        //*modificando estimado
        $this->actualizacion_import_proyect($proyecto, $id);
        //*Recorre el import verficando area
        foreach ($imports as $key => $import) {
            $validate_areas = DB::table('area_control')
                ->where('area_control.Floor_ID', $proyecto->Floor_ID)
                ->where('area_control.Nombre', $import->nombre_area)
                ->first();

            //*busca si area ya exite de lo contrario crea area
            if ($validate_areas) {
                //*Recorre tarea
                foreach ($import->imports as $i => $import_area) {
                    $validate_tareas = DB::table('task')
                        ->select(
                            'area_control.Nombre',
                            'task.Task_ID as id',
                            'task.Tas_IDT',
                            'task.Nombre as nombre_descripcion',
                            'task.Horas_Estimadas as estimate_hours',
                        )
                        ->join('area_control', 'area_control.Area_ID', 'task.Area_ID')
                        ->where('task.Area_ID', $validate_areas->Area_ID)
                        ->where('task.Tas_IDT', $import_area->cost_code)
                        ->first();
                    //* verifica si exite area de lo contrario crea
                    if ($validate_tareas) {
                        //*almacena y prepara para responder con repedidos
                        $repetidos = new stdClass();
                        //nuevo
                        $validate_tareas->tipo = 'old';
                        $import_area->tipo = 'new';

                        $repetidos->area = $validate_tareas;
                        $repetidos->duplicados[] = $import_area;
                        $repetidos->duplicados[] = $validate_tareas;
                        $valores_repetidos[] = $repetidos;
                    } else {
                        $tarea = DB::table('task')
                            ->insert([
                                'Pro_ID' => $proyecto->Pro_ID,
                                'Floor_ID' => $proyecto->Floor_ID,
                                'Area_ID' => $validate_areas->Area_ID,
                                'Tas_IDT' => $import_area->cost_code, ///
                                'NumAct' => "$import_area->area $import_area->cost_code", ///
                                'ActAre' => $import_area->area, ///
                                'ActTas' => $import_area->cost_code, ///
                                'Nombre' => $import_area->nombre_descripcion, //
                                'Horas_Estimadas' => $import_area->estimate_hours, //
                                'sov_descripcion' => $import_area->Nom_Sov, //
                                'sov_id' => $import_area->sov_id,
                                'precio_segun_avance' => ($import_area->price_total * 0), //
                                'precio_total' => $import_area->price_total,
                                'import_id' => $import_area->id,
                                'um' => $import_area->um,
                                'of_coast' => $import_area->of_coast,
                                'cc_butdget_qty' => $import_area->cc_butdget_qty,
                                'pwt_prod_rate' => $import_area->pwt_prod_rate,
                                'estimate_labor_cost' => $import_area->estimate_labor_cost,
                                'material_or_equipment_unit_cost' => $import_area->material_or_equipment_unit_cost,
                                'material_spread_rate_per_unit' => $import_area->material_spread_rate_per_unit,
                                'material_qty_or_gallons_unit' => $import_area->mat_qty_or_galon,
                                'mat_um' => $import_area->mat_um,
                                'material_cost' => $import_area->material_cost,
                                'subcontract_cost' => $import_area->buscontract_cost,
                                'equipment_cost' => $import_area->equipament_cost,
                                'other_cost' => $import_area->other_cost,
                            ]);
                    }
                }
            } else {
                $crear_area = DB::table('area_control')
                    ->insertGetId([
                        'Are_IDT' => $import->imports[0]->area,
                        'Pro_ID' => $proyecto->Pro_ID,
                        'Floor_ID' => $proyecto->Floor_ID,
                        'Nombre' => $import->nombre_area,
                    ]);
                //dd($import->imports[0]->area, $proyecto->Pro_ID, $proyecto->Floor_ID, $import->nombre_area);
                foreach ($import->imports as $j => $import_area) {
                    $tarea = DB::table('task')
                        ->insert([
                            'Pro_ID' => $proyecto->Pro_ID,
                            'Floor_ID' => $proyecto->Floor_ID,
                            'Area_ID' => $crear_area,
                            'Tas_IDT' => $import_area->cost_code, ///
                            'NumAct' => "$import_area->area $import_area->cost_code", ///
                            'ActAre' => $import_area->area, ///
                            'ActTas' => $import_area->cost_code, ///
                            'Nombre' => $import_area->nombre_descripcion, //
                            'Horas_Estimadas' => $import_area->estimate_hours, //
                            'sov_descripcion' => $import_area->Nom_Sov, //
                            'sov_id' => $import_area->sov_id,
                            'precio_segun_avance' => ($import_area->price_total * 0), //
                            'precio_total' => $import_area->price_total,
                            'import_id' => $import_area->id,
                            'um' => $import_area->um,
                            'of_coast' => $import_area->of_coast,
                            'cc_butdget_qty' => $import_area->cc_butdget_qty,
                            'pwt_prod_rate' => $import_area->pwt_prod_rate,
                            'estimate_labor_cost' => $import_area->estimate_labor_cost,
                            'material_or_equipment_unit_cost' => $import_area->material_or_equipment_unit_cost,
                            'material_spread_rate_per_unit' => $import_area->material_spread_rate_per_unit,
                            'material_qty_or_gallons_unit' => $import_area->mat_qty_or_galon,
                            'mat_um' => $import_area->mat_um,
                            'material_cost' => $import_area->material_cost,
                            'subcontract_cost' => $import_area->buscontract_cost,
                            'equipment_cost' => $import_area->equipament_cost,
                            'other_cost' => $import_area->other_cost,
                        ]);
                }
            }
        }
        return response()->json([
            'status' => 'ok',
            'data' => $valores_repetidos,
            'message' => 'Successfully loaded',
        ], 200);
    }
    private function actualizacion_import_proyect($proyecto, $estimado_id)
    {
        $estimados = DB::table('estimado')
            ->where('estimado.proyecto_id', $proyecto->proyecto_id)
            ->get();
        foreach ($estimados as $key => $estimado) {
            $update_estimado = DB::table('estimado')
                ->where('estimado.id', $estimado->id)
                ->update([
                    'import_proyecto' => 'n',
                ]);
        }
        $update_estimado = DB::table('estimado')
            ->where('estimado.id', $estimado_id)
            ->where('proyecto_id', $proyecto->proyecto_id)
            ->update([
                'import_proyecto' => 'y',
            ]);
    }
    public function update_import_database(Request $request)
    {
        //dd($request->all());
        foreach ($request->task_id as $key => $task) {
            $verificador_task = DB::table('task')
                ->where('task.Task_ID', $task)
                ->first();
            //funcion de reagrupamiento en task
            $area = DB::table('estimado_use_import')
                ->select(
                    'estimado_use_import.cost_code',
                    'estimado_use.estimado_id',
                    'estimado_use_import.nombre_area',
                )
                ->join('estimado_use', 'estimado_use.estimado_use_import_id', 'estimado_use_import.id')
                ->where('estimado_use_import.id', $request->import_id[$key])->first();
            $area_tarea = DB::table('estimado_use_import')
                ->select(
                    'estimado_use_import.cost_code',
                    DB::raw("sum(estimado_use_import.price_total) as price_total")
                )
                ->where('estimado_use.estimado_id', $area->estimado_id)
                ->where('estimado_use_import.nombre_area', $area->nombre_area)
                ->where('estimado_use_import.cost_code', $area->cost_code)
                ->join('estimado_use', 'estimado_use.estimado_use_import_id', 'estimado_use_import.id')
                ->groupBy('estimado_use_import.cost_code')
                ->first();
            //fin de funcion de reagrupamiento
            $import = DB::table('estimado_use_import')
                ->where('estimado_use_import.id', $request->import_id[$key])
                ->first();
            $task = DB::table('task')
                ->where('task.Task_ID', $task)
                ->update([
                    'Nombre' => $request->nombre_area[$key],
                    'Horas_Estimadas' => $request->horas_estimadas[$key],
                    'import_id' => $request->import_id[$key],
                    'NumAct' => $request->area_code[$key] . " " . $verificador_task->Tas_IDT, ///
                    'ActAre' => $request->area_code[$key], ///
                    'ActTas' => $verificador_task->Tas_IDT, ///
                    'sov_descripcion' => $request->sov_descripcion[$key], //
                    'sov_id' => $request->sov_id[$key],
                    'precio_segun_avance' => ($area_tarea->price_total * ($verificador_task->Last_Per_Recorded / 100)), //
                    'precio_total' => $area_tarea->price_total,
                ]);
        }
        return response()->json([
            'status' => 'ok',
            'message' => 'Successfully update',
        ], 200);
    }
    public function index()
    {
        $status = DB::table('estatus')->select('estatus.*')->get();
        $proyectos = DB::table('proyectos')
            ->select('proyectos.*')
        /* ->where('proyectos.Estatus_ID',1) */
            ->get();
        return view('panel.proyectos.see_final_SOV', compact('status', 'proyectos'));
    }
    public function import_sov_proyect(Request $request)
    {
        $data = [];
        switch ($request->tipo) {
            case 'proyecto':
                $data = $this->obtener_table_proyecto($request->id);
                break;
            case 'edificio':
                $data = $this->obtener_table_edificio($request->id);
                break;
            case 'floor':
                $data = $this->obtener_table_floor($request->id);
                break;

            default:
                # code...
                break;
        }

        return response()->json([
            'status' => 'ok',
            'data' => $data,
            'message' => 'Successfully',
        ], 200);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function obtener_table_proyecto($proyecto_id)
    {
        $edificios = DB::table('edificios')
            ->where('edificios.Pro_ID', $proyecto_id)
            ->orderBy('edificios.Nombre', 'ASC')
            ->get();
        foreach ($edificios as $key => $edificio) {
            //totales
            $total_edificio_precio_segun_avance = 0;
            $total_edificio_horas_estimadas = 0;
            $total_edificio_precio_total = 0;
            $total_edificio_horas_usadas = 0;

            $floors = DB::table('floor')
                ->where('floor.Edificio_ID', $edificio->Edificio_ID)
                ->orderBy('floor.Nombre', 'ASC')
                ->get();
            foreach ($floors as $key => $floor) {
                //totales
                $total_floor_precio_segun_avance = 0;
                $total_floor_horas_estimadas = 0;
                $total_floor_precio_total = 0;
                $total_floor_horas_usadas = 0;

                $areas_control = DB::table('area_control')
                    ->where('area_control.Floor_ID', $floor->Floor_ID)
                    ->orderBy('area_control.Nombre', 'ASC')
                    ->get();
                foreach ($areas_control as $key => $area) {
                    //totales
                    $total_area_precio_segun_avance = 0;
                    $total_area_horas_estimadas = 0;
                    $total_area_precio_total = 0;
                    $total_area_horas_usadas = 0;

                    //validar si hay porcentaje
                    $tareas = DB::table('task')
                        ->select(
                            'task.*',
                            DB::raw('DATE_FORMAT(task.Last_Date_Per_Recorded , "%m/%d/%Y") as Last_Date_Per_Recorded')
                        )
                        ->where('task.Area_ID', $area->Area_ID)
                        ->get();
                    foreach ($tareas as $key => $tarea) {
                        //actualizando precio_segun_avance
                        $update = DB::table('task')
                            ->where('task.Task_ID', $tarea->Task_ID)
                            ->update([
                                'precio_segun_avance' => ($tarea->precio_total * ($tarea->Last_Per_Recorded / 100)),
                            ]);
                    }
                    $tareas = DB::table('task')
                        ->select(
                            'task.*',
                            DB::raw('DATE_FORMAT(task.Last_Date_Per_Recorded , "%m/%d/%Y") as Last_Date_Per_Recorded')
                        )
                        ->where('task.Area_ID', $area->Area_ID)
                        ->orderBy('task.sov_id', 'ASC')
                        ->get();
                    //fin de validar porcentaje
                    //*horas estimada
                    $tareas = $this->calculo_horas_estimadas($tareas);

                    //suma de totales
                    foreach ($tareas as $key => $tarea) {
                        $total_area_precio_segun_avance += $tarea->precio_segun_avance;
                        $total_area_horas_estimadas += $tarea->Horas_Estimadas;
                        $total_area_precio_total += $tarea->precio_total;
                        $total_area_horas_usadas += $tarea->horas_usadas;
                        //porcentaje horas usadas
                        if ($tarea->Horas_Estimadas > 0) {
                            $tarea->porcentaje_horas_usadas = ((round(($tarea->horas_usadas / $tarea->Horas_Estimadas), 2) * 100) . '%');
                        } else {
                            $tarea->porcentaje_horas_usadas = 0;
                        }

                    }
                    $area->total_area_precio_segun_avance = round($total_area_precio_segun_avance, 2);
                    $area->total_area_horas_estimadas = round($total_area_horas_estimadas, 2);
                    $area->total_area_precio_total = round($total_area_precio_total, 2);
                    $area->total_area_horas_usadas = round($total_area_horas_usadas, 2);
                    $area->total_area_horas_usadas = round($total_area_horas_usadas, 2);

                    $area->task = $tareas;
                }
                //suma de totales
                foreach ($areas_control as $key => $area) {
                    $total_floor_precio_segun_avance += $area->total_area_precio_segun_avance;
                    $total_floor_precio_total += $area->total_area_precio_total;
                    $total_floor_horas_estimadas += $area->total_area_horas_estimadas;
                    $total_floor_horas_usadas += $area->total_area_horas_usadas;
                }
                $floor->total_floor_precio_segun_avance = round($total_floor_precio_segun_avance, 2);
                $floor->total_floor_horas_estimadas = round($total_floor_horas_estimadas, 2);
                $floor->total_floor_precio_total = round($total_floor_precio_total, 2);
                $floor->total_floor_horas_usadas = round($total_floor_horas_usadas, 2);

                $floor->areas = $areas_control;
            }
            //suma de totales
            foreach ($floors as $key => $floor) {
                $total_edificio_precio_segun_avance += $floor->total_floor_precio_segun_avance;
                $total_edificio_horas_estimadas += $floor->total_floor_horas_estimadas;
                $total_edificio_precio_total += $floor->total_floor_precio_total;
                $total_edificio_horas_usadas += $floor->total_floor_horas_usadas;
            }
            $edificio->total_edificio_precio_segun_avance = round($total_edificio_precio_segun_avance, 2);
            $edificio->total_edificio_horas_estimadas = round($total_edificio_horas_estimadas, 2);
            $edificio->total_edificio_precio_total = round($total_edificio_precio_total, 2);
            $edificio->total_edificio_horas_usadas = round($total_edificio_horas_usadas, 2);

            $edificio->floors = $floors;
        }
        return $edificios;
    }
    public function obtener_table_edificio($edificio_id)
    {
        $floors = DB::table('floor')
            ->where('floor.Edificio_ID', $edificio_id)
            ->orderBy('floor.Nombre', 'ASC')
            ->get();
        foreach ($floors as $key => $floor) {
            //totales
            $total_floor_precio_segun_avance = 0;
            $total_floor_horas_estimadas = 0;
            $total_floor_precio_total = 0;
            $total_floor_horas_usadas = 0;

            $areas_control = DB::table('area_control')
                ->where('area_control.Floor_ID', $floor->Floor_ID)
                ->orderBy('area_control.Nombre', 'ASC')
                ->get();
            foreach ($areas_control as $key => $area) {
                //totales
                $total_area_precio_segun_avance = 0;
                $total_area_horas_estimadas = 0;
                $total_area_precio_total = 0;
                $total_area_horas_usadas = 0;

                //validar si hay porcentaje
                $tareas = DB::table('task')
                    ->select(
                        'task.*',
                        DB::raw('DATE_FORMAT(task.Last_Date_Per_Recorded , "%m/%d/%Y") as Last_Date_Per_Recorded')
                    )
                    ->where('task.Area_ID', $area->Area_ID)
                    ->get();
                foreach ($tareas as $key => $tarea) {
                    //actualizando precio_segun_avance
                    $update = DB::table('task')
                        ->where('task.Task_ID', $tarea->Task_ID)
                        ->update([
                            'precio_segun_avance' => ($tarea->precio_total * ($tarea->Last_Per_Recorded / 100)),
                        ]);
                }
                $tareas = DB::table('task')
                    ->select(
                        'task.*',
                        DB::raw('DATE_FORMAT(task.Last_Date_Per_Recorded , "%m/%d/%Y") as Last_Date_Per_Recorded')
                    )
                    ->where('task.Area_ID', $area->Area_ID)
                    ->orderBy('task.sov_id', 'ASC')
                    ->get();
                //fin de validar porcentaje
                //*horas estimada
                $tareas = $this->calculo_horas_estimadas($tareas);
                //suma de totales
                foreach ($tareas as $key => $tarea) {
                    $total_area_precio_segun_avance += $tarea->precio_segun_avance;
                    $total_area_horas_estimadas += $tarea->Horas_Estimadas;
                    $total_area_precio_total += $tarea->precio_total;
                    $total_area_horas_usadas += $tarea->horas_usadas;
                    //porcentaje horas usadas
                    if ($tarea->Horas_Estimadas > 0) {
                        $tarea->porcentaje_horas_usadas = ((round(($tarea->horas_usadas / $tarea->Horas_Estimadas), 2) * 100) . '%');
                    } else {
                        $tarea->porcentaje_horas_usadas = 0;
                    }
                }
                $area->total_area_precio_segun_avance = round($total_area_precio_segun_avance, 2);
                $area->total_area_horas_estimadas = round($total_area_horas_estimadas, 2);
                $area->total_area_precio_total = round($total_area_precio_total, 2);
                $area->total_area_horas_usadas = round($total_area_horas_usadas, 2);

                $area->task = $tareas;
            }
            //suma de totales
            foreach ($areas_control as $key => $area) {
                $total_floor_precio_segun_avance += $area->total_area_precio_segun_avance;
                $total_floor_horas_estimadas += $area->total_area_horas_estimadas;
                $total_floor_precio_total += $area->total_area_precio_total;
                $total_floor_horas_usadas += $area->total_area_horas_usadas;
            }
            $floor->total_floor_precio_segun_avance = round($total_floor_precio_segun_avance, 2);
            $floor->total_floor_horas_estimadas = round($total_floor_horas_estimadas, 2);
            $floor->total_floor_precio_total = round($total_floor_precio_total, 2);
            $floor->total_floor_horas_usadas = round($total_floor_horas_usadas, 2);

            $floor->areas = $areas_control;
        }
        return $floors;
    }
    public function obtener_table_floor($floor_id)
    {
        $areas_control = DB::table('area_control')
            ->where('area_control.Floor_ID', $floor_id)
            ->orderBy('area_control.Nombre', 'ASC')
            ->get();
        foreach ($areas_control as $key => $area) {
            //totales
            $total_area_precio_segun_avance = 0;
            $total_area_horas_estimadas = 0;
            $total_area_precio_total = 0;
            $total_area_horas_usadas = 0;

            //validar si hay porcentaje
            $tareas = DB::table('task')
                ->select(
                    'task.*',
                    DB::raw('DATE_FORMAT(task.Last_Date_Per_Recorded , "%m/%d/%Y") as Last_Date_Per_Recorded')
                )
                ->where('task.Area_ID', $area->Area_ID)
                ->get();
            foreach ($tareas as $key => $tarea) {
                //actualizando precio_segun_avance
                $update = DB::table('task')
                    ->where('task.Task_ID', $tarea->Task_ID)
                    ->update([
                        'precio_segun_avance' => ($tarea->precio_total * ($tarea->Last_Per_Recorded / 100)),
                    ]);
            }
            $tareas = DB::table('task')
                ->select(
                    'task.*',
                    DB::raw('DATE_FORMAT(task.Last_Date_Per_Recorded , "%m/%d/%Y") as Last_Date_Per_Recorded')
                )
                ->orderBy('task.sov_id', 'ASC')
                ->where('task.Area_ID', $area->Area_ID)
                ->get();
            //*horas estimada
            $tareas = $this->calculo_horas_estimadas($tareas);
            //suma de totales
            foreach ($tareas as $key => $tarea) {
                $total_area_precio_segun_avance += $tarea->precio_segun_avance;
                $total_area_horas_estimadas += $tarea->Horas_Estimadas;
                $total_area_precio_total += $tarea->precio_total;
                $total_area_horas_usadas += $tarea->horas_usadas;
                //porcentaje horas usadas
                if ($tarea->Horas_Estimadas > 0) {
                    $tarea->porcentaje_horas_usadas = ((round(($tarea->horas_usadas / $tarea->Horas_Estimadas), 2) * 100) . '%');
                } else {
                    $tarea->porcentaje_horas_usadas = 0;
                }
            }
            $area->total_area_precio_segun_avance = round($total_area_precio_segun_avance, 2);
            $area->total_area_horas_estimadas = round($total_area_horas_estimadas, 2);
            $area->total_area_precio_total = round($total_area_precio_total, 2);
            $area->total_area_horas_usadas = round($total_area_horas_usadas, 2);

            $area->task = $tareas;
        }
        return $areas_control;
    }

    public function import_sov_task_update(Request $request)
    {
        switch ($request->input) {
            case 'horas_estimadas':
                return $this->modificando_horas_estimadas($request->task_id, $request->valor, $request->tipo, $request->id);
                break;
            case 'porcentaje':
                return $this->modificando_porcentaje($request->task_id, $request->valor, $request->tipo, $request->id);
                break;
            case 'precio_total':
                return $this->modificando_precio_total($request->task_id, $request->valor, $request->tipo, $request->id);
                break;
            case 'sov_id':
                return $this->modificando_sov_id($request->task_id, $request->valor, $request->tipo, $request->id);
                break;
            case 'sov_descripcion':
                return $this->modificando_sov_descripcion($request->task_id, $request->valor, $request->tipo, $request->id);
                break;
            case 'cc_butdget_qty':
                return $this->modificando_cc_butget_ytq($request->task_id, $request->valor, $request->tipo, $request->id);
                break;
            case 'um':
                return $this->modificando_um($request->task_id, $request->valor, $request->tipo, $request->id);
                break;
            default:
                # code...
                break;
        }
    }
    private function elegir_vista($tipo, $id)
    {
        $data = [];
        switch ($tipo) {
            case 'proyecto':
                $data = $this->obtener_table_proyecto($id);
                break;
            case 'edificio':
                $data = $this->obtener_table_edificio($id);
                break;
            case 'floor':
                $data = $this->obtener_table_floor($id);
                break;
            default:
                # code...
                break;
        }
        return $data;
    }
    private function modificando_horas_estimadas($task_id, $valor, $tipo, $id)
    {
        $task = DB::table('task')
            ->select(
                'task.*',
                'floor.Edificio_ID'
            )
            ->join('area_control', 'area_control.Area_ID', 'task.Area_ID')
            ->join('floor', 'floor.Floor_ID', 'area_control.Floor_ID')
            ->where('task.Task_ID', $task_id)
            ->first();
        $update = DB::table('task')
            ->where('task.Task_ID', $task_id)
            ->update([
                'Horas_Estimadas' => $valor,
            ]);
        //devolucion de vista
        $data = $this->elegir_vista($tipo, $id);
        if ($update) {
            return response()->json([
                'status' => 'ok',
                'data' => $data,
                'message' => 'Estimated Hours Successfully modified',
            ], 200);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Ocurio un error',
            ], 200);
        }
    }
    private function modificando_porcentaje($task_id, $valor, $tipo, $id)
    {
        $task = DB::table('task')
            ->select(
                'task.*',
                'floor.Edificio_ID'
            )
            ->join('area_control', 'area_control.Area_ID', 'task.Area_ID')
            ->join('floor', 'floor.Floor_ID', 'area_control.Floor_ID')
            ->where('task.Task_ID', $task_id)
            ->first();
        $this->cambios_porcentaje($task);
        $update = DB::table('task')
            ->where('task.Task_ID', $task_id)
            ->update([
                'Last_Date_Per_Recorded' => date('Y-m-d'),
                'Last_Per_Recorded' => $valor,
                'precio_segun_avance' => ($task->precio_total * ($valor / 100)),
                'Usr' => auth()->user()->Nombre . ' ' . auth()->user()->Apellido_Paterno . '' . auth()->user()->Apellido_Materno,
            ]);
        //datos de tabla
        $data = $this->elegir_vista($tipo, $id);
        //modificando campos extras
        if ($update) {
            return response()->json([
                'status' => 'ok',
                'data' => $data,
                'message' => 'Percentage Successfully modified',
            ], 200);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Ocurio un error',
            ], 200);
        }
    }
    private function cambios_porcentaje($tarea)
    {
        $tareas = DB::table('task')
            ->where('task.Task_ID', $tarea->Task_ID)
            ->update([
                'B_Last_Date' => $tarea->Last_Date_Per_Recorded,
                'B_Last_Percentage' => $tarea->Last_Per_Recorded,
                'BUsr' => $tarea->Usr,
            ]);
    }
    private function modificando_precio_total($task_id, $valor, $tipo, $id)
    {
        $task = DB::table('task')
            ->select(
                'task.*',
                'floor.Edificio_ID'
            )
            ->join('area_control', 'area_control.Area_ID', 'task.Area_ID')
            ->join('floor', 'floor.Floor_ID', 'area_control.Floor_ID')
            ->where('task.Task_ID', $task_id)
            ->first();
        $update = DB::table('task')
            ->where('task.Task_ID', $task_id)
            ->update([
                'precio_total' => $valor,
                'precio_segun_avance' => ($valor * ($task->Last_Per_Recorded / 100)),
            ]);
        $data = $this->elegir_vista($tipo, $id);
        if ($update) {
            return response()->json([
                'status' => 'ok',
                'data' => $data,
                'message' => 'Price total Successfully modified',
            ], 200);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Ocurio un error',
            ], 200);
        }
    }
    private function modificando_sov_id($task_id, $valor, $tipo, $id)
    {
        $task = DB::table('task')
            ->select(
                'task.*',
                'floor.Edificio_ID'
            )
            ->join('area_control', 'area_control.Area_ID', 'task.Area_ID')
            ->join('floor', 'floor.Floor_ID', 'area_control.Floor_ID')
            ->where('task.Task_ID', $task_id)
            ->first();

        $update = DB::table('task')
            ->where('task.Task_ID', $task_id)
            ->update([
                'sov_id' => $valor,
            ]);
        $data = $this->elegir_vista($tipo, $id);
        if ($update) {
            return response()->json([
                'status' => 'ok',
                'data' => $data,
                'message' => 'Sov Id Successfully modified',
            ], 200);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Ocurio un error',
            ], 200);
        }
    }
    private function modificando_sov_descripcion($task_id, $valor, $tipo, $id)
    {
        $task = DB::table('task')
            ->select(
                'task.*',
                'floor.Edificio_ID'
            )
            ->join('area_control', 'area_control.Area_ID', 'task.Area_ID')
            ->join('floor', 'floor.Floor_ID', 'area_control.Floor_ID')
            ->where('task.Task_ID', $task_id)
            ->first();
        $update = DB::table('task')
            ->where('task.Task_ID', $task_id)
            ->update([
                'sov_descripcion' => $valor,
            ]);
        $data = $this->elegir_vista($tipo, $id);
        if ($update) {
            return response()->json([
                'status' => 'ok',
                'data' => $data,
                'message' => 'Sov Description Successfully modified',
            ], 200);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Ocurio un error',
            ], 200);
        }
    }
    public function lista_floors(Request $request)
    {
        $floors = DB::table('floor')->where('floor.Floor_ID', $request->floor_id)
            ->get();
    }
    public function create()
    {
        //
    }
    private function modificando_um($task_id, $valor, $tipo, $id)
    {
        $task = DB::table('task')
            ->select(
                'task.*',
                'floor.Edificio_ID'
            )
            ->join('area_control', 'area_control.Area_ID', 'task.Area_ID')
            ->join('floor', 'floor.Floor_ID', 'area_control.Floor_ID')
            ->where('task.Task_ID', $task_id)
            ->first();

        $update = DB::table('task')
            ->where('task.Task_ID', $task_id)
            ->update([
                'um' => $valor,
            ]);
        $data = $this->elegir_vista($tipo, $id);
        if ($update) {
            return response()->json([
                'status' => 'ok',
                'data' => $data,
                'message' => 'Um Successfully modified',
            ], 200);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Ocurio un error',
            ], 200);
        }
    }
    private function modificando_cc_butget_ytq($task_id, $valor, $tipo, $id)
    {
        $task = DB::table('task')
            ->select(
                'task.*',
                'floor.Edificio_ID'
            )
            ->join('area_control', 'area_control.Area_ID', 'task.Area_ID')
            ->join('floor', 'floor.Floor_ID', 'area_control.Floor_ID')
            ->where('task.Task_ID', $task_id)
            ->first();

        $update = DB::table('task')
            ->where('task.Task_ID', $task_id)
            ->update([
                'cc_butdget_qty' => $valor,
            ]);
        $data = $this->elegir_vista($tipo, $id);
        if ($update) {
            return response()->json([
                'status' => 'ok',
                'data' => $data,
                'message' => 'cc butdget qty Successfully modified',
            ], 200);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Ocurio un error',
            ], 200);
        }
    }
    private function zerofill($valor, $longitud)
    {
        return str_pad($valor, $longitud, '0', STR_PAD_LEFT);
    }
    //estimado
    public function add_estimado_import_project(Request $request)
    {
        $imports = $this->export_to_excel($request->estimado_id);
        $estimado = DB::table('estimado')
            ->join('proyectos', 'proyectos.Pro_ID', 'estimado.proyecto_id')
            ->where('id', $request->estimado_id)->first();
        //
        $tareas_afectadas = $this->tareas_afectas($imports, $estimado);
        // areas del proyecto
        $tareas = DB::table('area_control')
            ->select(
                'area_control.Are_IDT',
                'area_control.Nombre as nombre_area',
                'task.*'
            )
            ->join('task', 'task.Area_ID', 'area_control.Area_ID')
            ->where('area_control.Pro_ID', $estimado->proyecto_id)
            ->get();
        foreach ($tareas as $key => $tarea) {
            $tarea->estado = 'No affected';
            foreach ($tareas_afectadas as $key => $tareas_afectada) {
                if ($tareas_afectada->Task_ID == $tarea->Task_ID) {
                    $tarea->estado = 'affected';
                    break;
                }
            }
        }
        return response()->json([
            'status' => 'ok',
            'message' => 'Tareas encontradas',
            'data' => [
                'proyecto' => $estimado,
                'tareas' => $tareas,
            ],
        ], 200);
    }
    public function tareas_afectas($imports, $estimado)
    {
        $tareas_afectadas = [];
        foreach ($imports as $key => $import) {
            foreach ($import->imports as $i => $import_area) {
                $area_control = DB::table('area_control')
                    ->select(
                        'area_control.Are_IDT',
                        'area_control.Nombre as nombre_area',
                        'task.*'
                    )
                    ->join('task', 'task.Area_ID', 'area_control.Area_ID')
                    ->where('area_control.Pro_ID', $estimado->proyecto_id)
                    ->where('area_control.Are_IDT', $import_area->area)
                    ->where('task.Tas_IDT', $import_area->cost_code)
                    ->first();
                if ($area_control) {
                    $tareas_afectadas[] = $area_control;
                }
            }
        }
        return $tareas_afectadas;
    }

    public function save_estimado_import_project(Request $request)
    {
        $imports = $this->export_to_excel($request->estimado_id);
        $estimado = DB::table('estimado')
            ->select(
                'estimado.*'
            )
            ->join('proyectos', 'proyectos.Pro_ID', 'estimado.proyecto_id')
            ->where('id', $request->estimado_id)->first();
        //
        foreach ($imports as $key => $import) {
            foreach ($import->imports as $i => $import_area) {
                $area_control = DB::table('area_control')
                    ->select(
                        'area_control.Are_IDT',
                        'area_control.Nombre as nombre_area',
                        'task.*'
                    )
                    ->join('task', 'task.Area_ID', 'area_control.Area_ID')
                    ->where('area_control.Pro_ID', $estimado->proyecto_id)
                    ->where('area_control.Are_IDT', $import_area->area)
                    ->where('task.Tas_IDT', $import_area->cost_code)
                    ->first();
                if ($area_control) {
                    //dd($estimado, $area_control,$import_area);
                    $update = DB::table('task')
                        ->where('Task_ID', $area_control->Task_ID)
                        ->update([
                            'import_id' => $estimado->id,
                            'sov_id' => $import_area->sov_id,
                            'sov_descripcion' => $import_area->Nom_Sov,
                            'precio_total' => $import_area->price_total,
                            'cc_butdget_qty' => $import_area->cc_butdget_qty,
                            'um' => $import_area->um,
                            'of_coast' => $import_area->of_coast,
                            'pwt_prod_rate' => $import_area->pwt_prod_rate,
                            'estimate_labor_cost' => $import_area->estimate_labor_cost,
                            'material_or_equipment_unit_cost' => $import_area->material_or_equipment_unit_cost,
                            'material_spread_rate_per_unit' => $import_area->material_spread_rate_per_unit,
                            'material_qty_or_gallons_unit' => $import_area->mat_qty_or_galon,
                            'mat_um' => $import_area->mat_um,
                            'material_cost' => $import_area->material_cost,
                            'subcontract_cost' => $import_area->buscontract_cost,
                            'equipment_cost' => $import_area->equipament_cost,
                            'other_cost' => $import_area->other_cost,
                        ]);
                }
            }
        }
        return response()->json([
            'status' => 'ok',
            'message' => 'Successfully modified tasks',
        ], 200);
    }
}
