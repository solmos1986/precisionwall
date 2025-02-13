<?php

namespace App\Http\Controllers\cardex;

use App\Http\Controllers\Controller;
use App\Personal;
use DataTables;
use DB;
use Illuminate\Http\Request;

class noAuthController extends Controller
{
    public function no_auth_index()
    {
        return view('panel.cardex_personal.no_auth_list');
    }
    public function no_auth_datatable()
    {

        $personal = Personal::select(
            'personal.Empleado_ID',
            'personal.Numero',
            DB::raw("CONCAT(COALESCE(personal.Nombre,''),' ',COALESCE(personal.Apellido_Paterno,''),' ',COALESCE(personal.Apellido_Materno,'')) as Nombre"),
            'personal.Nick_Name',
            'tipo_personal.nombre as nombre_tipo',
            'personal.Cargo',
            'personal.email',
            'empresas.Nombre as nombre_empresa'
        )
        //filtrando nickname
            ->when(!empty(request()->personas), function ($q) {
                return $q->whereIn('personal.Empleado_ID', explode(',', request()->personas));
            })
        //filtrando nickname
            ->when(!empty(request()->eventos), function ($q) {
                return $q->leftjoin('movimientos_eventos', 'movimientos_eventos.Empleado_ID', 'personal.Empleado_ID')
                    ->leftjoin('evento', 'evento.cod_evento', 'movimientos_eventos.cod_evento')
                    ->where('movimientos_eventos.estado', '1')
                    ->where('evento.estado', '1')
                    ->whereIn('movimientos_eventos.cod_evento', explode(',', request()->eventos));
            })
            ->join('empresas', 'empresas.Emp_ID', 'personal.Emp_ID')
            ->leftjoin('tipo_personal', 'tipo_personal.id', 'personal.tipo_personal_id')
            ->leftjoin('cargo_personal', 'cargo_personal.id', 'personal.cargo_personal_id')
            ->where('personal.status', '1')
            ->groupBy('personal.Empleado_ID')
            ->orderBy('personal.Nick_Name', 'ASC')
            ->get();

        return Datatables::of($personal)
            ->addIndexColumn()
            ->addColumn('acciones', function ($personal) {
                $button = "
                        <a href='" . route('edit.cardex', ['id' => $personal->Empleado_ID]) . "'><i class='fas fa-user-tag ms-text-primary cursor-pointer' title='Show employee'></i></a>
                        <i class='far fa-trash-alt ms-text-danger delete cursor-pointer' data-id='$personal->Empleado_ID' title='Delete'></i>
                        ";
                return $button;
            })
        /*  */
            ->addColumn('eventos', function ($personal) {
                $eventos = DB::table('movimientos_eventos')
                    ->select(
                        'evento.nombre',
                    )
                    ->join('evento', 'evento.cod_evento', 'movimientos_eventos.cod_evento')
                    ->where('movimientos_eventos.Empleado_ID', $personal->Empleado_ID)
                    ->where('evento.estado', 1)
                    ->where('movimientos_eventos.estado', '1')
                    ->get()->toArray();

                $button = "";
                foreach ($eventos as $key => $evento) {
                    $button .= "<span class='badge badge-info m-1' style='font-size: 85%'>$evento->nombre</span>";
                }
                return $button;
            })
            ->addColumn('check', function ($personal) {
                $button = "
                        <label class='ms-checkbox-wrap ms-checkbox-info'>
                                <input type='checkbox' value='$personal->Empleado_ID' class='persona' style='opacity: 1;' data-proyecto='$personal->Empleado_ID'>
                                <i class='ms-checkbox-check'></i>
                              </label>";
                return $button;
            })
            ->rawColumns(['acciones', 'eventos', 'check'])
            ->make(true);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
