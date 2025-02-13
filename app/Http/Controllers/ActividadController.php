<?php

namespace App\Http\Controllers;

use App\Actividad;
use App\Personal;
use DataTables;
use Illuminate\Http\Request;

class ActividadController extends Controller
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
    public function index(Request $request)
    {
        if ($request->ajax()) {
            if (!empty($request->from_date)) {
                $data = Actividad::selectRaw("actividades.Actividad_ID,
                actividades.Fecha,
                proyectos.*,
                tipo_actividad.Actividad_Nombre,
                empresas.Codigo as empresa,
                CONCAT(f.Nombre, ' ', f.Apellido_Paterno, ' ',  f.Apellido_Materno) as Foreman,
                CONCAT(l.Nombre, ' ', l.Apellido_Paterno, ' ',  l.Apellido_Materno) as Lead,
                CONCAT(c_o.Nombre, ' ',  c_o.Apellido_Paterno, ' ',  c_o.Apellido_Materno) as Coordinador_Obra,
                CONCAT(c.Nick_Name) as Pwtsuper")
                    ->where('Fecha', $request->from_date)
                    ->when(!auth()->user()->verificarRol([1]), function ($query) {
                        return $query->where(function ($q) {
                            $q->where('actividad_personal.Empleado_ID', auth()->user()->Empleado_ID)
                                ->orWhere('proyectos.Foreman_ID', auth()->user()->Empleado_ID)
                                ->orWhere('proyectos.Lead_ID', auth()->user()->Empleado_ID)
                                ->orWhere('proyectos.Coordinador_Obra_ID', auth()->user()->Empleado_ID)
                                ->orWhere('proyectos.Coordinador_ID', auth()->user()->Empleado_ID);
                        });
                    })
                    ->join('tipo_actividad', 'actividades.Tipo_Actividad_ID', 'tipo_actividad.Tipo_Actividad_ID')
                    ->join('proyectos', 'proyectos.Pro_ID', 'actividades.Pro_ID')
                    ->join('empresas', 'empresas.Emp_ID', 'proyectos.Emp_ID')
                    ->join('actividad_personal', 'actividad_personal.Actividad_ID', 'actividades.Actividad_ID')
                    ->leftJoin('personal as f', 'f.Empleado_ID', 'proyectos.Foreman_ID')
                    ->leftJoin('personal as l', 'l.Empleado_ID', 'proyectos.Lead_ID')
                    ->leftJoin('personal as c', 'c.Empleado_ID', 'proyectos.Coordinador_ID')
                    ->leftJoin('personal as c_o', 'c_o.Empleado_ID', 'proyectos.Coordinador_Obra_ID')
                    ->distinct('actividades.Actividad_ID')
                    ->get();
            } else {
                $data = Actividad::selectRaw("actividades.Actividad_ID,
                actividades.Fecha,
                proyectos.*,
                tipo_actividad.Actividad_Nombre,
                empresas.Codigo as empresa,
                CONCAT(f.Nombre, ' ', f.Apellido_Paterno, ' ',  f.Apellido_Materno) as Foreman,
                CONCAT(l.Nombre, ' ', l.Apellido_Paterno, ' ',  l.Apellido_Materno) as Lead,
                CONCAT(c_o.Nombre, ' ',  c_o.Apellido_Paterno, ' ',  c_o.Apellido_Materno) as Coordinador_Obra,
                CONCAT(c.Nick_Name) as Pwtsuper")
                    ->when(!auth()->user()->isAdmin(), function ($query) {
                        return $query->where(function ($q) {
                            $q->where('actividad_personal.Empleado_ID', auth()->user()->Empleado_ID)
                                ->orWhere('proyectos.Foreman_ID', auth()->user()->Empleado_ID)
                                ->orWhere('proyectos.Lead_ID', auth()->user()->Empleado_ID)
                                ->orWhere('proyectos.Coordinador_Obra_ID', auth()->user()->Empleado_ID)
                                ->orWhere('proyectos.Coordinador_ID', auth()->user()->Empleado_ID);
                        });
                    })
                    ->join('tipo_actividad', 'actividades.Tipo_Actividad_ID', 'tipo_actividad.Tipo_Actividad_ID')
                    ->join('proyectos', 'proyectos.Pro_ID', 'actividades.Pro_ID')
                    ->join('empresas', 'empresas.Emp_ID', 'proyectos.Emp_ID')
                    ->join('actividad_personal', 'actividad_personal.Actividad_ID', 'actividades.Actividad_ID')
                    ->leftJoin('personal as f', 'f.Empleado_ID', 'proyectos.Foreman_ID')
                    ->leftJoin('personal as l', 'l.Empleado_ID', 'proyectos.Lead_ID')
                    ->leftJoin('personal as c', 'c.Empleado_ID', 'proyectos.Coordinador_ID')
                    ->leftJoin('personal as c_o', 'c_o.Empleado_ID', 'proyectos.Coordinador_Obra_ID')
                    ->distinct('actividades.Actividad_ID')
                    ->get();
            }

            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('direccion', function ($data) {
                    $text = "$data->Numero $data->Calle, $data->Ciudad, $data->Estado $data->Zip_Code";
                    return $text;
                })
                ->addColumn('acciones', function ($data) {
                    $button = '<a href="' . route('listar.tickets', ['id' => $data->Actividad_ID]) . '"><i class="fas fa-clipboard-list ms-text-primary"></i></a>';
                    return $button;
                })
                ->addColumn('empleados', function ($data) {
                    $empleados = Personal::selectRaw("CONCAT(COALESCE(personal.Nombre,''), ' ',  COALESCE(personal.Apellido_Paterno,''), ' ',  COALESCE(personal.Apellido_Materno,'')) as empleado")
                        ->where('ap.Actividad_ID', $data->Actividad_ID)
                        ->join('actividad_personal as ap', 'ap.Empleado_ID', 'personal.Empleado_ID')
                        ->get();
                    $list = [];
                    foreach ($empleados as $val) {
                        $list[] = $val->empleado;
                    }
                    return json_encode($list);
                })
                ->editColumn('Fecha', function ($data) {
                    return $data->Fecha ? date('m-d-Y', strtotime($data->Fecha)) : null;
                })
                ->rawColumns(['acciones'])
                ->make(true);
        }
        return view('panel.actividad.list');
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
        //
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
