<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DataTables;
use DB;
use Validator;
use App\ContactoProyecto;
use App\Personal;

class ContactoProyectoController extends Controller
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
            $data = DB::table('proyectos')->select('proyectos.*','empresas.Codigo')
            ->join('empresas','proyectos.Emp_ID','empresas.Emp_ID')
            ->get();
            
            return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('direccion', function($data){
                        $text = "$data->Numero $data->Calle, $data->Ciudad, $data->Estado $data->Zip_Code";
                        return $text;
                    })
                    ->addColumn('num_contac', function($data){

                        return ContactoProyecto::where('Pro_ID',$data->Pro_ID)->count();
                    })
                    ->addColumn('acciones', function($data){
                        $button = '<a href="'.route('listar.contactos',['id'=>$data->Pro_ID]).'"><i class="far fa-address-book ms-text-primary"></i></a>';
                        return $button;
                    })
                    ->rawColumns(['acciones'])
                    ->make(true);
        }
        return view('panel.contacto_proyecto.list');
    }
    public function indexContacto($id)
    {
        $tipo_contacto=DB::table('tipo_contacto')
        ->where('tipo_contacto.estado','1')
        ->get();

        if(request()->ajax()){
            $data = ContactoProyecto::selectRaw("
            personal.Empleado_ID,
            personal.Nick_Name,
            personal.Usuario,
            personal.email,
            empresas.Nombre as empresa, 
            tipo_contacto.nombre as nombre_tipo,
            CONCAT(COALESCE(personal.Nombre,''), ' ',  COALESCE(personal.Apellido_Paterno,''), ' ',  COALESCE(personal.Apellido_Materno,'')) as personal_nombre,
            contacto_proyecto.*")
            ->join('personal','personal.Empleado_ID','contacto_proyecto.Empleado_ID')
            ->join('empresas','personal.Emp_ID','empresas.Emp_ID')
            ->join('tipo_contacto','tipo_contacto.id_tipo_contacto','contacto_proyecto.tipo_contacto')
            ->where('contacto_proyecto.Pro_ID',$id)
            ->get();
            
            return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('nombres', function($data){
                        $text = "$data->Nombre $data->Apellido_Paterno $data->Apellido_Materno";
                        return $text;
                    })
                    ->addColumn('acciones', function($data){
                        $button = "<i id='$data->id' class='fas fa-pencil-alt ms-text-warning edit cursor-pointer' title='Edit'></i>";
                        $button .= "<i class='far fa-trash-alt ms-text-danger delete cursor-pointer' data-id='$data->id' title='Delete'></i>";
                            return $button;
                    })
                    ->rawColumns(['acciones'])
                    ->make(true);
        }
        $proyecto = DB::table('proyectos')->where('Pro_ID',$id)->first();
        return view('panel.contacto_proyecto.list_contacto',compact('id','proyecto','tipo_contacto'));
    }

    public function get_empleoyes(Request $request, $id)
    {
        if (!isset($request->searchTerm)) {
            $tipo_trabajo = Personal::selectRaw("Empleado_ID, CONCAT(COALESCE(personal.Nombre,''), ' ',  COALESCE(personal.Apellido_Paterno,''), ' ',  COALESCE(personal.Apellido_Materno,''), ' - ', COALESCE(empresas.Nombre,'')) as Foreman, personal.email")
            ->join('empresas','empresas.Emp_ID','personal.Emp_ID')
            ->where(function ($q) use ($id) {
                    $q->where('empresas.Emp_ID', $id)
                        ->Orwhere('empresas.Emp_ID', 6);
                })
            ->get();
        } else {
            $tipo_trabajo = Personal::selectRaw("Empleado_ID, CONCAT(COALESCE(personal.Nombre,''), ' ',  COALESCE(personal.Apellido_Paterno,''), ' ',  COALESCE(personal.Apellido_Materno,''), ' - ', COALESCE(empresas.Nombre,'')) as Foreman, personal.email")
            ->join('empresas','empresas.Emp_ID','personal.Emp_ID')
            ->whereRaw("CONCAT(COALESCE(personal.Nombre,''), ' ',  COALESCE(personal.Apellido_Paterno,''), ' ',  COALESCE(personal.Apellido_Materno,''), ' - ', COALESCE(empresas.Nombre,'')) like '%$request->searchTerm%'")
            ->where(function ($q) use ($id) {
                    $q->where('empresas.Emp_ID', $id)
                        ->Orwhere('empresas.Emp_ID', 6);
                })
            ->get();
        }
        $data = [];
        foreach ($tipo_trabajo as $row) {
            $data[] = array(
                "id" => $row->Empleado_ID,
                "text" => $row->Foreman,
                "email" => $row->email,
            );
        }
        return response()->json($data);
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
    public function store(Request $request, $id)
    {
        //dd($request->tipo);
        $rules = array(
            'empleado_id'    =>  'required',
            'tipo'     =>  'required',
            'email_p'     =>  'required|email',
        );

        $error = Validator::make($request->all(), $rules);

        if($error->fails())
        {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        $form_data = array(
            'Pro_ID'        =>  $id,
            'Empleado_ID'         =>  $request->empleado_id,
            'tipo_contacto'         =>  $request->tipo
        );
      
        ContactoProyecto::create($form_data);
        Personal::find($request->empleado_id)->update([
            'email' => $request->email_p
        ]);

        return response()->json(['success' => 'Contact registered correctly.']);
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
            $data = ContactoProyecto::selectRaw("
            personal.*, 
            empresas.Emp_ID as empresa_id,
            tipo_contacto.id_tipo_contacto,
            CONCAT(COALESCE(personal.Nombre,''), ' ',  COALESCE(personal.Apellido_Paterno,''), ' ',  COALESCE(personal.Apellido_Materno,''), ' - ', COALESCE(empresas.Nombre,'')) as nombre_completo,
             contacto_proyecto.*")
            ->where('contacto_proyecto.id',$id)
            ->join('personal','contacto_proyecto.Empleado_ID','personal.Empleado_ID')
            ->join('empresas','personal.Emp_ID','empresas.Emp_ID')
            ->join('tipo_contacto','tipo_contacto.id_tipo_contacto','contacto_proyecto.tipo_contacto')
            ->first();
            
            return response()->json([
                'result' => $data
            ]);
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
            'empleado_id'    =>  'required',
            'tipo'     =>  'required',
            'email_p'     =>  'required|email',
        );

        $error = Validator::make($request->all(), $rules);

        if($error->fails())
        {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        $form_data = array(
            'Pro_ID'        =>  $id,
            'Empleado_ID'         =>  $request->empleado_id,
            'tipo_contacto'         =>  $request->tipo
        );

        ContactoProyecto::where('id',$request->hidden_id)->update($form_data);
        Personal::find($request->empleado_id)->update([
            'email' => $request->email_p
        ]);

        return response()->json(['success' => 'Contact successfully updated.']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $data = ContactoProyecto::findOrFail($id);
        $data->delete();
        return response()->json(['success' => 'Contact successfully removed.']);
    }
}