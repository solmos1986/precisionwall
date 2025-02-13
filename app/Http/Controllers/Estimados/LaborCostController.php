<?php

namespace App\Http\Controllers\Estimados;

use App\Http\Controllers\Controller;
use DataTables;
use DB;
use Illuminate\Http\Request;

class LaborCostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $estimados = DB::table('estimado_gene_info')
            ->select(
                'estimado_gene_info.*',
            )
            ->get();
        return Datatables::of($estimados)
            ->addIndexColumn()
            ->addColumn('acciones', function ($data) {
                $button = "
                    <i class='fas fa-pencil-alt ms-text-warning cursor-pointer edit_labor_cost' data-labor_cost_id='$data->id' title='Edit Labor Cost'></i>
                    <i class='far fa-trash-alt ms-text-danger delete_labor_cost cursor-pointer' data-labor_cost_id='$data->id' title='Delete Labor Cost'></i>
                ";
                return $button;
            })
            ->rawColumns(['acciones'])
            ->make(true);
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
        $labor_cost = DB::table('estimado_gene_info')
            ->insertGetId([
                'labor_cost' => $request->labor_cost,
                'descripcion' => $request->labor_cost_descripcion,
                'estado' => 'y',
            ]);
        if ($labor_cost) {
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
        $labor_cost = DB::table('estimado_gene_info')
            ->where('estimado_gene_info.id', $id)
            ->first();
        if ($labor_cost) {
            return response()->json([
                'status' => 'ok',
                'data' => $labor_cost,
                'message' => 'Registered Successfully',
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
        $labor_cost = DB::table('estimado_gene_info')
            ->where('estimado_gene_info.id', $id)
            ->update([
                'labor_cost' => $request->labor_cost,
                'descripcion' => $request->labor_cost_descripcion,
            ]);
        if ($labor_cost) {
            return response()->json([
                'status' => 'ok',
                'message' => 'Update Successfully',
            ], 200);
        } else {
            return response()->json([
                'status' => 'errors',
                'message' => 'Error de servidor',
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
        $labor_cost = DB::table('estimado_gene_info')
            ->where('estimado_gene_info.id', $id)
            ->delete();
        if ($labor_cost) {
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
}
