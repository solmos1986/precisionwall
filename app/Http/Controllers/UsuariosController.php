<?php

namespace App\Http\Controllers;

use App\Personal;
use DataTables;
use DB;
use Illuminate\Http\Request;
use Validator;

class UsuariosController extends Controller
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
    public function indexUserRoles()
    {
        $empresas = DB::table('empresas')->get();
        if (request()->ajax()) {
            $data = Personal::selectRaw("
            personal.Empleado_ID,
            personal.Nick_Name,
            personal.Usuario,
            personal.email,
            personal.Rol_ID,
            empresas.Nombre as empresa,
            CONCAT(COALESCE(personal.Nombre,''), ' ',  COALESCE(personal.Apellido_Paterno,''), ' ',  COALESCE(personal.Apellido_Materno,'')) as personal_nombre")
                ->join('empresas', 'personal.Emp_ID', 'empresas.Emp_ID')
                ->get();
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('permisos', function ($data) {
                    $rol = '';
                    $roles = DB::table('roles_app')
                        ->select('roles_app.*')
                        ->join('rol_personal', 'rol_personal.roles_app_id', 'roles_app.id')
                        ->where('rol_personal.Empleado_ID', $data->Empleado_ID)
                        ->get();
                    foreach ($roles as $val) {
                        $rol .= "<span class='badge badge-primary'>$val->nombre</span>";
                    }
                    return $rol;
                })
                ->addColumn('acciones', function ($data) {
                    $button = "<i id='$data->Empleado_ID' class='fas fa-pencil-alt ms-text-warning edit cursor-pointer' title='Edit'></i>";
                    return $button;
                })
                ->rawColumns(['acciones', 'permisos'])
                ->make(true);
        }
        $roles = DB::table('roles_app')->get();
        return view('panel.usuarios.list-roles', compact('roles', 'empresas'));
    }

    public function index()
    {
        return view('panel.ticket.list');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function alltickets()
    {
        return view('panel.usuarios.alltickets');
    }

    public function allprojects()
    {
        return view('panel.usuarios.allprojects');
    }

    public function create()
    {
        return view('panel.usuarios.add');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
    public function editUser($id)
    {
        if (request()->ajax()) {
            $data = Personal::selectRaw("personal.*,
            empresas.Emp_ID as empresa_id")
                ->join('empresas', 'personal.Emp_ID', 'empresas.Emp_ID')
                ->where('personal.Empleado_ID', $id)
                ->first();
            try {
                $roles = DB::table('roles_app')->select('id')->whereIn('id', json_decode($data->Rol_ID))->get();
                $array = $roles->map(function ($obj) {
                    return $obj->id;
                })->toArray();
            } catch (\Throwable $th) {
                $array = [];
            }

            return response()->json([
                'result' => $data,
                'roles' => $array,
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
    public function updateUser(Request $request, $id)
    {
        $rules = array(
            'Nombre' => 'required',
            'Apellido_Paterno' => 'required',
            'Apellido_Materno' => 'nullable',
            'Nick_Name' => 'nullable',
            'Fecha_Nacimiento' => 'nullable|date',
            'email.*' => 'nullable|email',
            'rol_id.*' => 'required',
            'empresa_id' => 'required',
        );

        $error = Validator::make($request->all(), $rules);

        if ($error->fails()) {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        $form_data = array(
            'Nombre' => $request->Nombre,
            'Apellido_Paterno' => $request->Apellido_Paterno,
            'Apellido_Materno' => $request->Apellido_Materno,
            'Nick_Name' => $request->Nick_Name,
            'Fecha_Nacimiento' => $request->Fecha_Nacimiento,
            'email' => $request->email,
            'Telefono' => $request->Telefono,
            'Celular' => $request->Celular,
            'Usuario' => $request->Usuario,
            'Password' => $request->Password,
            'Rol_ID' => json_encode($request->rol_id),
            'Emp_ID' => $request->empresa_id,
        );

        $personal = Personal::find($request->hidden_id)->update($form_data);
        $removeRolPersona = DB::table('rol_personal')
            ->where('Empleado_ID', $request->hidden_id)
            ->delete();
        foreach ($request->rol_id as $key => $value) {
            $insertRolPersona = DB::table('rol_personal')
                ->where('Empleado_ID', $request->hidden_id)
                ->insertGetId([
                    'Empleado_ID' => $request->hidden_id,
                    'roles_app_id' => $value,
                ]);
        }

        if ($personal) {
            return response()->json(['success' => 'user updated successfully']);
        }

        return response()->json(['success' => 'the user could not be updated']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
