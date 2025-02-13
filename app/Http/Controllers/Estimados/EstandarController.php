<?php

namespace App\Http\Controllers\Estimados;

use App\Http\Controllers\Controller;
use DB;
use Illuminate\Http\Request;
use Storage;
use Validator;

class EstandarController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function list_standar($id)
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
        return response()->json($lista, 200);
    }
    public function index()
    {
        //
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

    private function actualizar_lista($estimado_superficie_id)
    {
        $lista = DB::table('estimado_estandar')
            ->select(
                'estimado_estandar.*',
                DB::raw("CONCAT(estimado_estandar.sov_id,' - ', estimado_estandar.Nom_Sov) as Nom_Sov"),
            )
            ->where('estimado_estandar.estimado_superficie_id', $estimado_superficie_id)
            ->get();
        foreach ($lista as $key => $standar) {
            $lista[$key]->metodos = DB::table('estimado_metodo')
                ->where('estimado_metodo.estimado_estandar_id', $standar->id)
                ->get()->toArray();
        }
        return $lista;
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
            'nombre_tarea' => 'required|string',
            'cost_code' => 'required|string',
            'descripcion' => 'nullable',
            'sov_id' => 'required',
            'nombre_sov_id' => 'required|string',
        );
        $messages = [
            'nombre_tarea.required' => "The Name field is required",
            'cost_code.required' => "The Cost code field is required",
            'nombre_sov_id.required' => "The Sum SOV Id last name field is required",
        ];
        $error = Validator::make($request->all(), $rules, $messages);
        if ($error->errors()->all()) {
            return response()->json([
                'status' => 'errors',
                'message' => $error->errors()->all(),
            ]);
        } else {
            /* if ($this->validar_codigo($request->cost_code)) {
                return response()->json([
                    'status' => 'errors',
                    'message' => ['the Code already exists'],
                ]);
            } */
            $standar = DB::table('estimado_estandar')
                ->insertGetId([
                    'nombre' => $request->nombre_tarea,
                    'codigo' => $request->cost_code,
                    'descripcion' => $request->descripcion,
                    'sov_id' => $request->sov_id,
                    'Nom_Sov' => $request->nombre_sov_id,
                    'estimado_superficie_id' => $request->estandar_superficie_id,
                ]);
            $obtener_superficie = DB::table('estimado_estandar')
                ->where('estimado_estandar.id', $standar)
                ->first();
            $data = $this->actualizar_lista($obtener_superficie->estimado_superficie_id);
            return response()->json([
                'status' => 'ok',
                'data' => $data,
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
        $estandar = DB::table('estimado_estandar')
            ->where('estimado_estandar.id', $id)
            ->first();
        if ($estandar) {
            return response()->json([
                'status' => 'ok',
                'data' => $estandar,
                'message' => 'Get one task',
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
            'nombre_tarea' => 'required|string',
            'cost_code' => 'required|string',
            'descripcion' => 'nullable',
            'sov_id' => 'required',
            'nombre_sov_id' => 'required|string',
        );

        $messages = [
            'nombre_tarea.required' => "The Name field is required",
            'cost_code.required' => "The Cost code field is required",
            'nombre_sov_id.required' => "The Sum SOV Id last name field is required",
        ];

        $error = Validator::make($request->all(), $rules, $messages);
        if ($error->errors()->all()) {
            return response()->json([
                'status' => 'errors',
                'message' => $error->errors()->all(),
            ]);
        } else {
           /*  if ($this->validar_codigo($request->cost_code, $id)) {
                return response()->json([
                    'status' => 'errors',
                    'message' => ['the Code already exists'],
                ]);
            } */
            $estandar = DB::table('estimado_estandar')
                ->where('estimado_estandar.id', $id)
                ->update([
                    'nombre' => $request->nombre_tarea,
                    'codigo' => $request->cost_code,
                    'descripcion' => $request->descripcion,
                    'sov_id' => $request->sov_id,
                    'Nom_Sov' => $request->nombre_sov_id,
                ]);
            $obtener_superficie = DB::table('estimado_estandar')
                ->where('estimado_estandar.id', $id)
                ->first();
            $data = $this->actualizar_lista($obtener_superficie->estimado_superficie_id);
            return response()->json([
                'status' => 'ok',
                'data' => $data,
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
    public function destroy($id)
    {
        $obtener_superficie = DB::table('estimado_estandar')
            ->where('estimado_estandar.id', $id)
            ->first();
        $estandar = DB::table('estimado_estandar')
            ->where('estimado_estandar.id', $id)
            ->delete();
        $data = $this->actualizar_lista($obtener_superficie->estimado_superficie_id);
        if ($estandar) {
            return response()->json([
                'status' => 'ok',
                'data' => $data,
                'message' => 'Remove Successfully',
            ], 200);
        } else {
            return response()->json([
                'status' => 'errors',
                'message' => 'Error de servidor',
            ], 200);
        }
    }
    private function validar_nombre($nombre, $estandar_id = false)
    {
        $validar = DB::table('estimado_estandar')
            ->when($estandar_id, function ($query) use ($estandar_id) {
                return $query->where('estimado_estandar.id', '<>', $estandar_id);
            })
            ->where('estimado_estandar.nombre', $nombre)
            ->get();
        if (count($validar) > 0) {
            return true;
        } else {
            return false;
        }

    }
    private function validar_codigo($codigo, $estandar_id = false)
    {
        $validar = DB::table('estimado_estandar')
            ->when($estandar_id, function ($query) use ($estandar_id) {
                return $query->where('estimado_estandar.id', '<>', $estandar_id);
            })
            ->where('estimado_estandar.codigo', $codigo)
            ->get();
        if (count($validar) > 0) {
            return true;
        } else {
            return false;
        }
    }
}
