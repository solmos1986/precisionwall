<?php

namespace App\Http\Controllers;

use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RolPermisos extends Controller
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
        return view('panel.permisos_rol.index');
    }
    public function dataTable()
    {
        $roles = DB::table('roles_app')
            ->select(
                'roles_app.id',
                'roles_app.nombre',
            )
            ->get();
        return Datatables::of($roles)
            ->addIndexColumn()
            ->addColumn('modulos', function ($roles) {
                $modulos = DB::table('modulo')
                    ->select('modulo.nombre_modulo')
                    ->join('rol_modulo', 'rol_modulo.modulo_id', 'modulo.modulo_id')
                    ->where('rol_modulo.roles_app_id', $roles->id)
                    ->pluck('nombre_modulo')->toArray();
                return implode(',', $modulos);
            })
            ->rawColumns(['modulos'])
            ->make(true);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function obtenerTodosModulosEdit($id)
    {
        $modulos = DB::table('modulo')->get();
        foreach ($modulos as $key => $modulo) {
            $verificarModulo = DB::table('rol_modulo')
                ->where('rol_modulo.roles_app_id', $id)
                ->where('rol_modulo.modulo_id', $modulo->modulo_id)
                ->first();

            if ($verificarModulo) {
                $modulo->verificado = true;
                $sub_modulos = DB::table('sub_modulo')
                    ->where('sub_modulo.modulo_id', $modulo->modulo_id)
                    ->get();
                foreach ($sub_modulos as $key => $sub_modulo) {
                    $verificarSubModulo = DB::table('rol_sub_modulo')
                        ->where('rol_sub_modulo.sub_modulo_id', $sub_modulo->sub_modulo_id)
                        ->where('rol_sub_modulo.rol_modulo_id', $verificarModulo->rol_modulo_id)
                        ->first();
                    if ($verificarSubModulo) {
                        $sub_modulo->verificado = true;
                    } else {
                        $sub_modulo->verificado = false;
                    }
                }
                $modulo->sub_modulos = $sub_modulos;
            } else {
                $modulo->verificado = false;
                $sub_modulos = DB::table('sub_modulo')
                    ->where('sub_modulo.modulo_id', $modulo->modulo_id)
                    ->get();
                foreach ($sub_modulos as $key => $sub_modulo) {
                    $sub_modulo->verificado = false;
                }
                $modulo->sub_modulos = $sub_modulos;
            }
        }
        return $modulos;
    }

    public function obtenerTodosModulosCreate()
    {
        $modulos = DB::table('modulo')->get();
        foreach ($modulos as $key => $modulo) {
            $modulo->verificado = false;
            $sub_modulos = DB::table('sub_modulo')
                ->where('sub_modulo.modulo_id', $modulo->modulo_id)
                ->get();
            foreach ($sub_modulos as $key => $sub_modulo) {
                $sub_modulo->verificado = false;
            }
            $modulo->sub_modulos = $sub_modulos;
        }
        return $modulos;
    }

    public function create()
    {
        $modulos = $this->obtenerTodosModulosCreate();
        return response()->json([
            'status' => 'ok',
            'message' => 'Modulos y sub modulos',
            'data' => [
                'modulos' => $modulos,
            ],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rol_id = $this->createRol($request->rol['nombre_rol']);
        foreach ($request->modulos as $i => $modulo) {
            if ($request->modulos[$i]['verificado'] == 'true') {
                //dump('Modulos', $request->modulos[$i]['modulo_id']);
                $inserRolModulo = DB::table('rol_modulo')->insertGetId([
                    'roles_app_id' => $rol_id,
                    'modulo_id' => $request->modulos[$i]['modulo_id'],
                ]);
                if (isset($request->modulos[$i]['sub_modulos'])) {
                    foreach ($modulo['sub_modulos'] as $key => $sub_modulo) {
                        if ($sub_modulo['verificado'] == 'true') {
                            //dump($sub_modulo);
                            $inserRolSubModulo = DB::table('rol_sub_modulo')->insertGetId([
                                'rol_modulo_id' => $inserRolModulo,
                                'sub_modulo_id' => $sub_modulo['sub_modulo_id'],
                            ]);
                        }
                    }
                }
            }
        }
        return response()->json([
            'status' => 'ok',
            'message' => 'Registered Successfully',
            'data' => null,
        ]);
    }
    public function createRol($nombre_rol)
    {
        $insert_rol = DB::table('roles_app')->insertGetId([
            'nombre' => $nombre_rol,
        ]);
        return $insert_rol;
    }
    public function updateRol($nombre_rol, $id)
    {
        $insert_rol = DB::table('roles_app')
            ->where('roles_app.id', $id)->update([
            'nombre' => $nombre_rol,
        ]);
        return $id;
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
        $rol = DB::table('roles_app')
            ->where('roles_app.id', $id)
            ->first();
        $modulos = $this->obtenerTodosModulosEdit($id);
        return response()->json([
            'status' => 'ok',
            'message' => 'Modulos y sub modulos',
            'data' => [
                'modulos' => $modulos,
                'rol' => $rol,
            ],
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $rol_id = $this->updateRol($request->rol['nombre_rol'], $request->rol['rol_id']);
        $RolModulos = DB::table('rol_modulo')
            ->select('rol_modulo_id')
            ->where('rol_modulo.roles_app_id', $request->rol['rol_id'])
            ->pluck('rol_modulo_id')
            ->toArray();

        $deleteRolModulos = DB::table('rol_modulo')
            ->where('rol_modulo.roles_app_id', $request->rol['rol_id'])
            ->delete();
        $deleteRolModulos = DB::table('rol_sub_modulo')
            ->whereIn('rol_sub_modulo.rol_modulo_id', $RolModulos)
            ->delete();
        foreach ($request->modulos as $i => $modulo) {
            if ($request->modulos[$i]['verificado'] == 'true') {
                //dump('Modulos', $request->modulos[$i]['modulo_id']);
                $inserRolModulo = DB::table('rol_modulo')->insertGetId([
                    'roles_app_id' => $rol_id,
                    'modulo_id' => $request->modulos[$i]['modulo_id'],
                ]);
                if (isset($request->modulos[$i]['sub_modulos'])) {
                    foreach ($modulo['sub_modulos'] as $key => $sub_modulo) {
                        if ($sub_modulo['verificado'] == 'true') {
                            //dump($sub_modulo);
                            $inserRolSubModulo = DB::table('rol_sub_modulo')->insertGetId([
                                'rol_modulo_id' => $inserRolModulo,
                                'sub_modulo_id' => $sub_modulo['sub_modulo_id'],
                            ]);
                        }
                    }
                }
            }
        }
        return response()->json([
            'status' => 'ok',
            'message' => 'Modified Successfully',
            'data' => null,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $RolModulos = DB::table('rol_personal')
            ->select('rol_personal_id')
            ->where('rol_personal.roles_app_id', $id)
            ->pluck('rol_personal_id')
            ->toArray();
        if (count($RolModulos) > 0) {
            return response()->json([
                'status' => 'error',
                'message' => 'There are people using this role',
                'data' => null,
            ]);
        } else {
            $deleteRolModulos = DB::table('rol_modulo')
                ->where('rol_modulo.roles_app_id', $id)
                ->delete();
            $deleteRolModulos = DB::table('rol_sub_modulo')
                ->whereIn('rol_sub_modulo.rol_modulo_id', $RolModulos)
                ->delete();
            $deleteRol = DB::table('roles_app')
                ->where('roles_app.id', $id)
                ->delete();
            return response()->json([
                'status' => 'ok',
                'message' => 'Removed Successfully',
                'data' => null,
            ]);

        }
    }
}
