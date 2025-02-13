<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Tipo_trabajo;
use DataTables;
use Validator;

class TipoController extends Controller
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
            $data = Tipo_trabajo::select('tipo_trabajo.*')
            ->get();
            
            return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('acciones', function($data){

                        $button = "<i id='$data->id' class='fas fa-pencil-alt ms-text-warning edit cursor-pointer' title='Edit'></i>";
                        $button .= "<i class='far fa-trash-alt ms-text-danger delete cursor-pointer' data-id='$data->id' title='Delete'></i>";

                        return $button;
                    })
                    ->rawColumns(['acciones'])
                    ->make(true);
        }
        return view('panel.tipo_trabajo.list');
      
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('panel.Razontrabajo.new');

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
            'nombre_p'    =>  'required',
        );

        $error = Validator::make($request->all(), $rules);

        if($error->fails())
        {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        $form_data = array(
            'nombre'        =>  $request->nombre_p,
            'descripcion'         =>  $request->descripcion_p
        );

        Tipo_trabajo::create($form_data);

        return response()->json(['success' => 'Registered Successfully.']);
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
        if(request()->ajax())
        {
            $data = Tipo_trabajo::findOrFail($id);
            return response()->json(['result' => $data]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Tipo_trabajo  $sample_data
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Tipo_trabajo $sample_data)
    {
        $rules = array(
            'nombre_p'        =>  'required',
        );

        $error = Validator::make($request->all(), $rules);

        if($error->fails())
        {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        $form_data = array(
            'nombre'    =>  $request->nombre_p,
            'descripcion'     =>  $request->descripcion_p
        );

        Tipo_trabajo::whereId($request->hidden_id)->update($form_data);

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
        $data = Tipo_trabajo::findOrFail($id);
        $data->delete();
        return response()->json(['success' => 'Job type successfully removed.']);
    }
}

