<?php

namespace App\Http\Controllers;

use DataTables;
use DB;
use Illuminate\Http\Request;
use Validator;

class TipoOrdenStatus extends Controller
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
        return view('panel.tipo_orden_status.list');
    }

    public function datatable_status()
    {
        $status = DB::table('tipo_orden_estatus')
            ->where('estado', 1)
            ->get();
        return Datatables::of($status)
            ->addIndexColumn()
            ->addColumn('color', function ($data) {
                return "<h5><span class='badge badge-$data->color'>$data->color</span></h5>";
            })
            ->addColumn('acciones', function ($data) {
                $button = "
                <i class='fas fa-pencil-alt ms-text-warning cursor-pointer edit_status' data-id='$data->id'  title='Edit'></i>
                <i class='far fa-trash-alt ms-text-danger cursor-pointer delete_status' data-id='$data->id' title='Delete'></i>
                ";
                return $button;
            })
            ->rawColumns(['acciones', 'color'])
            ->make(true);
    }
    /*private function estatus_color_ban($status_id)
    {
    $color = DB::table('tipo_orden_estatus')
    ->where('tipo_orden_estatus.id', $status_id)
    ->first();
    return "<h5><span class='badge badge-$tipo_orden_estatus->color'>$tipo_orden_estatus->nombre</span></h5>";
    }*/

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
        $rules = array(
            'new_code' => 'required',
            'new_name' => 'required',
            'new_color' => 'required',
        );
        $messages = [
            'new_code.required' => "The code field is required",
            'new_name.required' => 'The name date field is required',
            'new_color.required' => 'The color date field is required',
        ];
        $error = Validator::make($request->all(), $rules, $messages);
        if ($error->errors()->all()) {
            return response()->json([
                "status" => "errors",
                "message" => $error->errors()->all()]);
        }
        $status = DB::table('tipo_orden_estatus')
            ->insertGetId([
                'codigo' => $request->new_code,
                'nombre' => $request->new_name,
                'color' => $request->new_color,
                'estado' => 1,
            ]);
        return response()->json([
            "status" => "ok",
            "message" => 'Successfully created',
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

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $status = DB::table('tipo_orden_estatus')
            ->where('tipo_orden_estatus.id', $id)
            ->first();
        return response()->json($status, 200);
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
            'edit_code' => 'required',
            'edit_name' => 'required',
            'edit_color' => 'required',
        );
        $messages = [
            'edit_code.required' => "The code field is required",
            'edit_name.required' => 'The name date field is required',
            'edit_color.required' => 'The color date field is required',
        ];
        $error = Validator::make($request->all(), $rules, $messages);
        if ($error->errors()->all()) {
            return response()->json([
                "status" => "errors",
                "message" => $error->errors()->all()]);
        }
        $status = DB::table('tipo_orden_estatus')
            ->where('tipo_orden_estatus.id', $id)
            ->update([
                'codigo' => $request->edit_code,
                'nombre' => $request->edit_name,
                'color' => $request->edit_color,
            ]);
        return response()->json([
            "status" => "ok",
            "message" => 'Successfully modified',
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $verificar = DB::table('tipo_orden')
            ->where('tipo_orden.estatus_id', $id)
            ->get()->toArray();
        if (empty($verificar)) {
            $estatus = DB::table('tipo_orden_estatus')
                ->where('tipo_orden_estatus.id', $id)
                ->delete();
            return response()->json([
                "status" => "ok",
                "message" => 'Removed successfully',
            ], 200);
        } else {
            return response()->json([
                "status" => "error",
                "message" => ['status is in use'],
            ], 200);
        }
    }
}
