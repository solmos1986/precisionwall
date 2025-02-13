<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Razon_Trabajo;
use DataTables;
use Validator;


class RazontrabajoController extends Controller
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
            $data = Razon_Trabajo::select('razontrabajo.*')
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
        return view('panel.RazonTrabajo.list');
      
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('panel.RazonTrabajo.new');

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
            'tipo_r'    =>  'required',
            'descripcion_r'     =>  'required'
        );

        $error = Validator::make($request->all(), $rules);

        if($error->fails())
        {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        $form_data = array(
            'tipo'        =>  $request->tipo_r,
            'descripcion'         =>  $request->descripcion_r,
            'descripcion_traduccion' => $request->descripcion_r_t,
        );

        Razon_Trabajo::create($form_data);

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
            $data = Razon_Trabajo::findOrFail($id);
            return response()->json(['result' => $data]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Razon_Trabajo  $sample_data
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Razon_Trabajo $sample_data)
    {
        $rules = array(
            'tipo_r'        =>  'required',
            'descripcion_r'         =>  'required'
        );

        $error = Validator::make($request->all(), $rules);

        if($error->fails())
        {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        $form_data = array(
            'tipo'    =>  $request->tipo_r,
            'descripcion'     =>  $request->descripcion_r,
            'descripcion_traduccion' => $request->descripcion_r_t,
        );

        Razon_Trabajo::whereId($request->hidden_id)->update($form_data);

        return response()->json(['success' => 'Updated successfully']);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $data = Razon_Trabajo::findOrFail($id);
        $data->delete();

        return response()->json(['success' => 'Reason removed correctly']);
    }
}