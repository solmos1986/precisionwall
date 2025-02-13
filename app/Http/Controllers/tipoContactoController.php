<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DataTables;
use Validator;
use Illuminate\Support\Facades\DB;
class tipoContactoController extends Controller
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
        if(request()->ajax()){
            $tipo_contacto = DB::table('tipo_contacto')
            ->where('estado','1')
            ->get();
            return Datatables::of($tipo_contacto)
            ->addIndexColumn()
            ->addColumn('acciones', function($tipo_contacto){
                $button = "<i id='$tipo_contacto->id_tipo_contacto' class='fas fa-pencil-alt ms-text-warning edit cursor-pointer' title='Edit'></i>";
                $button .= "<i class='far fa-trash-alt ms-text-danger delete cursor-pointer' data-id='$tipo_contacto->id_tipo_contacto' title='Delete'></i>";
                    return $button;
                })
                ->rawColumns(['acciones'])
                ->make(true);
        }
        return view('panel.tipo_contacto.list');
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
        $rules = array(
            'name' => 'required',
            'descripcion' => 'nullable',
        );

        $error = Validator::make($request->all(), $rules);
        if($error->fails())
        {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        $tipo_contacto=DB::table('tipo_contacto')->insertGetId(
            array(
                'nombre'=>$request->name,
                'descripcion'=>$request->descripcion,
                'estado'=>'1'
            )
        );

        return response()->json(['success' => 'New contact type created.']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
       $tipo_contacto=DB::table('tipo_contacto')
       ->where('id_tipo_contacto',$id)
       ->where('estado','1')
       ->first();
      
       return response()->json(['result' => $tipo_contacto]);
       
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
        $rules = array(
            'name' => 'required',
            'descripcion' => 'nullable',
        );

        $error = Validator::make($request->all(), $rules);
        if($error->fails())
        {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        $tipo_contacto=DB::table('tipo_contacto')
        ->where('tipo_contacto.id_tipo_contacto', $id)
        ->update(
            array(
                'nombre'=>$request->name,
                'descripcion'=>$request->descripcion
            )
        );

        return response()->json(['success' => 'Updated successfully.']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $tipo_contacto=DB::table('tipo_contacto')
        ->where('tipo_contacto.id_tipo_contacto', $id)
        ->update(
            array(
                'estado'=>'0'
            )
        );

        return response()->json(['success' => 'Deleted successfully.']);
    }
}
