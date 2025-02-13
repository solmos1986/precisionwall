<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;

class ConfigDailyReportOptionController extends Controller
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
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //validacion
        $insert_report_daily = DB::table('report_daily')->insertGetId([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
        ]);
        if (count($request->options) > 0) {
            foreach ($request->options as $key => $option) {
                $insert_report_daily_opcion = DB::table('report_daily_opcion')->insertGetId([
                    'opcion' => $option['option'],
                    'report_daily_id' => $insert_report_daily,
                ]);
                try {
                    foreach ($option['valores'] as $key => $valor) {
                        $insert = DB::table('report_daily_valor')->insertGetId([
                            'valor' => $valor['valor'],
                            'report_daily_opcion_id' => $insert_report_daily_opcion,
                        ]);
                    }
                } catch (\Throwable $th) {

                }
            }
        } else {
            return response()->json([
                'status' => 'error',
                'data' => null,
                'message' => 'The options cannot be empty',
            ], 200);
        }
        //todo ok
        return response()->json([
            'status' => 'success',
            'data' => null,
            'message' => 'Registered Successfully',
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showOption(Request $request)
    {
        $report_daily = DB::table('report_daily')->whereIn('report_daily.id', $request->options)->get();
        //añadiendo sus de hijos
        foreach ($report_daily as $key => $value) {
            $report_daily_opcion = DB::table('report_daily_opcion')
                ->where('report_daily_opcion.report_daily_id', $value->id)
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
        return response()->json([
            'status' => 'success',
            'data' => $report_daily,
            'message' => 'Response option',
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $report_daily = DB::table('report_daily')
            ->where('report_daily.id', $id)
            ->first();

        $options = DB::table('report_daily_opcion')
            ->where('report_daily_opcion.report_daily_id', $report_daily->id)
            ->get();
        foreach ($options as $key => $option) {
            $values = DB::table('report_daily_valor')
                ->where('report_daily_valor.report_daily_opcion_id', $option->id)
                ->get();
            $option->valores = $values;
        }
        $report_daily->options = $options;

        return response()->json([
            'status' => 'success',
            'data' => $report_daily,
            'message' => 'Response option',
        ], 200);
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
        //validacion
        $update_report_daily = DB::table('report_daily')->where('report_daily.id',$id)->update([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
        ]);
        if (count($request->options) > 0) {
            //eliminar hijos
            $verificando = DB::table('report_daily_opcion')->where('report_daily_opcion.report_daily_id', $id)->get();
            foreach ($verificando as $key => $verificar) {
                $delete = DB::table('report_daily_valor')->where('report_daily_valor.id', $verificar->id)->delete();
            }
            $report_daily_opcion_delete = DB::table('report_daily_opcion')->where('report_daily_opcion.report_daily_id', $id)->delete();

            foreach ($request->options as $key => $option) {
                $insert_report_daily_opcion = DB::table('report_daily_opcion')->insertGetId([
                    'opcion' => $option['option'],
                    'report_daily_id' => $id,
                ]);
                try {
                    foreach ($option['valores'] as $key => $valor) {
                        $insert = DB::table('report_daily_valor')->insertGetId([
                            'valor' => $valor['valor'],
                            'report_daily_opcion_id' => $insert_report_daily_opcion,
                        ]);
                    }
                } catch (\Throwable $th) {

                }
            }
        } else {
            return response()->json([
                'status' => 'error',
                'data' => null,
                'message' => 'The options cannot be empty',
            ], 200);
        }
        //todo ok
        return response()->json([
            'status' => 'success',
            'data' => null,
            'message' => 'Update Successfully',
        ], 200);
    }

    private function delete_cascada($report_daily_id)
    {
        $verificando = DB::table('report_daily_opcion')->where('report_daily_opcion.report_daily_id', $report_daily_id)->get();
        foreach ($verificando as $key => $verificar) {
            $delete = DB::table('report_daily_valor')->where('report_daily_valor.id', $verificar->id)->delete();
        }
        $report_daily_opcion_delete = DB::table('report_daily_opcion')->where('report_daily_opcion.report_daily_id', $report_daily_id)->get();
        $report_daily_delete = DB::table('report_daily')->where('report_daily.id', $report_daily_id)->delete();
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->delete_cascada($id);
        return response()->json([
            'status' => 'success',
            'data' => null,
            'message' => 'Delete Successfully',
        ], 200);
    }
}
