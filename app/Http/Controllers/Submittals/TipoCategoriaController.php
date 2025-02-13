<?php

namespace App\Http\Controllers\Submittals;

use App\Http\Controllers\Controller;
use DataTables;
use DB;
use Illuminate\Http\Request;

class TipoCategoriaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('panel.submittals.tipoSubmittas');
    }
    public function datatable()
    {
        $data = DB::table('categoria_material')->orderBy('Cat_ID')->get();
        return Datatables::of($data)
            ->addIndexColumn()
            ->make(true);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
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
        $insert = DB::table('categoria_material')->insertGetId([
            'Nombre' => $request->Nombre,
            'Descripcion' => $request->Descripcion == null ? '' : $request->Descripcion,
        ]);
        return response()->json([
            'status' => 'ok',
            'message' => 'Saved successfully',
            'data' => null,
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
        $update = DB::table('categoria_material')
            ->where('Cat_ID', $id)
            ->update([
                'Nombre' => $request->Nombre,
                'Descripcion' => $request->Descripcion == null ? '' : $request->Descripcion,
            ]);
        return response()->json([
            'status' => 'ok',
            'message' => 'successfully modified',
            'data' => null,
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
        $verificar = DB::table('materiales')
            ->where('Cat_ID', $id)
            ->get();
        if (count($verificar) <= 0) {
            $detele = DB::table('categoria_material')->where('Cat_ID', $id)->delete();
            return response()->json([
                'status' => 'ok',
                'message' => 'successfully removed',
                'data' => null,
            ], 200);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Cannot be deleted because it is in use',
                'data' => null,
            ], 200);
        }
    }
}
