<?php

namespace App\Http\Controllers\Estimados;

use App\Http\Controllers\Controller;
use DataTables;
use DB;
use Illuminate\Http\Request;
use Storage;
use Validator;

class MetodoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function datatable()
    {
        $superficies = DB::table('estimado_superficie')
            ->orderBy('estimado_superficie.id', 'DESC')
            ->get();
        return Datatables::of($superficies)
            ->addIndexColumn()
            ->addColumn('acciones', function ($data) {
                $button = "
            <i class='fas fa-pencil-alt ms-text-warning cursor-pointer edit-superficie' title='Edit Surface' data-superficie_id='$data->id'></i>
            <i class='far fa-trash-alt ms-text-danger delete-superficie cursor-pointer' data-superficie_id='$data->id' title='Delete Surface'></i>
            ";
                return $button;
            })
            ->rawColumns(['acciones'])
            ->make(true);
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
            'nombre_metodo' => 'required|string',
            'unidad_medida' => 'required|string',
            'materal_spread' => 'required',
            'material_cost_unit' => 'required',
            'material_unit_med' => 'required',
            'num_coast' => 'required',
            'rate_hour' => 'required',
            'default' => 'nullable',
        );
        $messages = [
            'nombre_metodo.required' => "The Name field is required",
            'unidad_medida.required' => "The Unit Med field is required",
            'materal_spread.required' => "The M. Spread field is required",
            'material_cost_unit.required' => "The M. Cost Unit field is required",
            'material_unit_med.required' => "The M. Unit Med field is required",
            'num_coast.required' => "The Num. Coast  field is required",
            'rate_hour.required' => "The Rate Hours field is required",

        ];
        $error = Validator::make($request->all(), $rules, $messages);
        if ($error->errors()->all()) {
            return response()->json([
                'status' => 'errors',
                'message' => $error->errors()->all(),
            ]);
        } else {
            if (!is_null($request->default)) {
                if ($this->verificar_default($request->metodo_estandar_id)) {
                    return response()->json([
                        'status' => 'errors',
                        'message' => ['a default already exists'],
                    ]);
                } else {
                    $metodo = DB::table('estimado_metodo')
                        ->insert([
                            'nombre' => $request->nombre_metodo,
                            'unidad_medida' => $request->unidad_medida,
                            'materal_spread' => $request->materal_spread,
                            'material_cost_unit' => $request->material_cost_unit,
                            'material_unit_med' => $request->material_unit_med,
                            'num_coast' => $request->num_coast,
                            'rate_hour' => $request->rate_hour,
                            'mark_up' => $request->mark_up,
                            'defauld' => $request->default == 'y' ? 'y' : 'n',
                            'estimado_estandar_id' => $request->metodo_estandar_id,
                            'procedimiento' => $request->process,
                            'cod_category_labor' => $request->cod_category_labor,
                            'cod_category_material' => $request->cod_category_material,

                        ]);
                    return response()->json([
                        'status' => 'ok',
                        'data' => $metodo,
                        'message' => 'Registered Successfully',
                    ], 200);
                }
            } else {
                $metodo = DB::table('estimado_metodo')
                    ->insert([
                        'nombre' => $request->nombre_metodo,
                        'unidad_medida' => $request->unidad_medida,
                        'materal_spread' => $request->materal_spread,
                        'material_cost_unit' => $request->material_cost_unit,
                        'material_unit_med' => $request->material_unit_med,
                        'num_coast' => $request->num_coast,
                        'rate_hour' => $request->rate_hour,
                        'defauld' => $request->default == 'y' ? 'y' : 'n',
                        'estimado_estandar_id' => $request->metodo_estandar_id,
                        'mark_up' => $request->mark_up,
                        'procedimiento' => $request->process,
                        'cod_category_labor' => $request->cod_category_labor,
                        'cod_category_material' => $request->cod_category_material,
                    ]);
                $obtener_superficie = DB::table('estimado_estandar')
                    ->where('estimado_estandar.id', $request->metodo_estandar_id)
                    ->first();
                return response()->json([
                    'status' => 'ok',
                    'data' => $this->actualizar_lista($obtener_superficie->estimado_superficie_id),
                    'message' => 'Registered Successfully',
                ], 200);
            }

        }
    }
    private function actualizar_lista($id)
    {
        $lista = DB::table('estimado_estandar')
            ->select(
                'estimado_estandar.*',
                DB::raw("CONCAT(estimado_estandar.sov_id,' - ', estimado_estandar.Nom_Sov) as Nom_Sov"),
            )
            ->where('estimado_estandar.estimado_superficie_id', $id)
            ->get();
        foreach ($lista as $key => $standar) {
            $lista[$key]->metodos = DB::table('estimado_metodo')
                ->where('estimado_metodo.estimado_estandar_id', $standar->id)
                ->get()->toArray();
        }
        return $lista;
    }
    private function verificar_default($estimado_estandar_id, $estimado_metodo_id = false)
    {
        $metodos = DB::table('estimado_metodo')
            ->when($estimado_metodo_id, function ($query) use ($estimado_metodo_id) {
                return $query->where('estimado_metodo.id', '<>', $estimado_metodo_id);
            })
            ->where('estimado_metodo.estimado_estandar_id', $estimado_estandar_id)
            ->where('estimado_metodo.defauld', 'y')
            ->get();
        if (count($metodos) > 0) {
            return true;
        } else {
            return false;
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
        $metodo = DB::table('estimado_metodo')
            ->where('estimado_metodo.id', $id)
            ->first();
        if ($metodo) {
            return response()->json([
                'status' => 'ok',
                'data' => $metodo,
                'message' => 'Editar Successfully',
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
            'nombre_metodo' => 'required|string',
            'unidad_medida' => 'required|string',
            'materal_spread' => 'required',
            'material_cost_unit' => 'required',
            'material_unit_med' => 'required',
            'num_coast' => 'required',
            'rate_hour' => 'required',
            'default' => 'nullable',
        );
        $messages = [
            'nombre_metodo.required' => "The Name field is required",
            'unidad_medida.required' => "The Unit Med field is required",
            'materal_spread.required' => "The M. Spread field is required",
            'material_cost_unit.required' => "The M. Cost Unit field is required",
            'material_unit_med.required' => "The M. Unit Med field is required",
            'num_coast.required' => "The Num. Coast  field is required",
            'rate_hour.required' => "The Rate Hours field is required",

        ];
        $error = Validator::make($request->all(), $rules, $messages);
        if ($error->errors()->all()) {
            return response()->json([
                'status' => 'errors',
                'message' => $error->errors()->all(),
            ]);
        } else {
            $obtener_superficie = DB::table('estimado_estandar')
                ->where('estimado_estandar.id', $request->metodo_estandar_id)
                ->first();
            if (!is_null($request->default)) {
                if ($this->verificar_default($request->metodo_estandar_id, $id)) {
                    return response()->json([
                        'status' => 'errors',
                        'message' => ['a default already exists'],
                    ]);
                } else {
                    $metodo = DB::table('estimado_metodo')
                        ->where('estimado_metodo.id', $id)
                        ->update([
                            'nombre' => $request->nombre_metodo,
                            'unidad_medida' => $request->unidad_medida,
                            'materal_spread' => $request->materal_spread,
                            'material_cost_unit' => $request->material_cost_unit,
                            'material_unit_med' => $request->material_unit_med,
                            'num_coast' => $request->num_coast,
                            'rate_hour' => $request->rate_hour,
                            'mark_up' => $request->mark_up,
                            'defauld' => $request->default == 'y' ? 'y' : 'n',
                            'procedimiento' => $request->process,
                            'cod_category_labor' => $request->cod_category_labor,
                            'cod_category_material' => $request->cod_category_material,
                        ]);
                    return response()->json([
                        'status' => 'ok',
                        'data' => $this->actualizar_lista($obtener_superficie->estimado_superficie_id),
                        'message' => 'Registered Successfully',
                    ], 200);
                }
            } else {
                $metodo = DB::table('estimado_metodo')
                    ->where('estimado_metodo.id', $id)
                    ->update([
                        'nombre' => $request->nombre_metodo,
                        'unidad_medida' => $request->unidad_medida,
                        'materal_spread' => $request->materal_spread,
                        'material_cost_unit' => $request->material_cost_unit,
                        'material_unit_med' => $request->material_unit_med,
                        'num_coast' => $request->num_coast,
                        'rate_hour' => $request->rate_hour,
                        'mark_up' => $request->mark_up,
                        'defauld' => $request->default == 'y' ? 'y' : 'n',
                        'procedimiento' => $request->process,
                        'cod_category_labor' => $request->cod_category_labor,
                        'cod_category_material' => $request->cod_category_material,
                    ]);
                return response()->json([
                    'status' => 'ok',
                    'data' => $this->actualizar_lista($obtener_superficie->estimado_superficie_id),
                    'message' => 'Registered Successfully',
                ], 200);
            }

        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $obtener_superficie = DB::table('estimado_estandar')
            ->join('estimado_metodo', 'estimado_metodo.estimado_estandar_id', 'estimado_estandar.id')
            ->where('estimado_metodo.id', $id)
            ->first();
        $metodo = DB::table('estimado_metodo')
            ->where('estimado_metodo.id', $id)
            ->delete();
        if ($metodo) {
            return response()->json([
                'status' => 'ok',
                'data' => $this->actualizar_lista($obtener_superficie->estimado_superficie_id),
                'message' => 'Delete Successfully',
            ], 200);
        } else {

            return response()->json([
                'status' => 'errors',
                'message' => 'Error de servidor',
            ], 200);
        }
    }
}
