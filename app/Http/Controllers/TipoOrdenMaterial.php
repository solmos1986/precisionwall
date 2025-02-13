<?php

namespace App\Http\Controllers;

use App\Material;
use DataTables;
use DB;
use Illuminate\Http\Request;

class TipoOrdenMaterial extends Controller
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
    private function total_ordenada($material_id, $proyecto_id)
    {
        $verificar_cantidad_ordenada = DB::table('pedidos_material')
            ->selectRaw("SUM(pedidos_material.Cantidad) AS cantidad_estimada")
            ->join('pedidos', 'pedidos.Ped_ID', 'pedidos_material.Ped_ID')
            ->where('pedidos_material.Mat_ID', $material_id)
            ->where('pedidos.Pro_ID', $proyecto_id)
            ->get();
        $resultado = 0;
        foreach ($verificar_cantidad_ordenada as $key => $cantidad) {
            $resultado += $cantidad->cantidad_estimada;
        }
        return $resultado;
    }
    public function select_material(Request $request, $id)
    {
        if (!isset($request->searchTerm)) {
            $materiales = Material::select('materiales.*', 'categoria_material.*')
                ->Join('proyectos', 'proyectos.Pro_ID', 'materiales.Pro_ID')
                ->Join('categoria_material', 'categoria_material.Cat_ID', 'materiales.Cat_ID')
                ->where(function ($query) use ($id) {
                    $query->where('materiales.Pro_ID', $id)
                        ->orWhere('proyectos.Pro_ID', 1);
                })
                ->distinct('materiales.Mat_ID')
                ->orderBy('categoria_material.Cat_ID')
                ->orderBy('proyectos.Pro_ID', 'DESC')
                ->orderBy('materiales.Denominacion')
                ->get();
        } else {
            $materiales = Material::select('materiales.*', 'categoria_material.*')
                ->where('Denominacion', 'like', '%' . $request->searchTerm . '%')
                ->Join('proyectos', 'proyectos.Pro_ID', 'materiales.Pro_ID')
                ->Join('categoria_material', 'categoria_material.Cat_ID', 'materiales.Cat_ID')
                ->where(function ($query) use ($id) {
                    $query->where('materiales.Pro_ID', $id)
                        ->orWhere('proyectos.Pro_ID', 1);
                })
                ->orderBy('categoria_material.Cat_ID')
                ->distinct('materiales.Mat_ID')
                ->orderBy('proyectos.Pro_ID', 'DESC')
                ->orderBy('materiales.Denominacion')
                ->get();
        }
        $data = [];
        foreach ($materiales as $row) {
            $data[] = array(
                "id" => $row->Mat_ID,
                "text" => "$row->Denominacion - $row->Unidad_Medida",
                "Unidad_Medida" => $row->Unidad_Medida,
                "tipo_id" => $row->Cat_ID,
                "tipo_nombre" => $row->Nombre,
            );
        }
        return response()->json($data);
    }
    public function select_equipo(Request $request, $id)
    {
        if (!isset($request->searchTerm)) {
            $materiales = Material::select('materiales.*')
                ->where('Cat_ID', 7)
                ->Orwhere('Cat_ID', 8)
                ->distinct('materiales.Mat_ID')
                ->get();
        } else {
            $materiales = Material::select('materiales.*')
                ->where('Denominacion', 'like', '%' . $request->searchTerm . '%')
                ->where('Cat_ID', 7)
                ->Orwhere('Cat_ID', 8)
                ->distinct('materiales.Mat_ID')
                ->get();
        }
        $data = [];
        foreach ($materiales as $row) {
            $data[] = array(
                "id" => $row->Mat_ID,
                "text" => "$row->Denominacion - $row->Unidad_Medida",
                "Unidad_Medida" => $row->Unidad_Medida,
            );
        }
        return response()->json($data);
    }

    public function index()
    {
        return view('panel.tipo_orden.materiales.list');
    }

    public function datatable()
    {
        $materiales = DB::table('materiales')
            ->select(
                'materiales.Mat_ID',
                'tipo_movimiento_material_pedido.id',
                'materiales.Denominacion',
                'materiales.Unidad_Medida',
                DB::raw('SUM(tipo_movimiento_material_pedido.egreso) AS egreso'),
                DB::raw('SUM(tipo_movimiento_material_pedido.ingreso) AS ingreso'),
                'materiales.Cat_ID',
                'proyectos.Nombre as ubicacion_proyecto',
                'proyecto_material.Nombre as proyecto_nombre',
                'proyectos.Pro_ID as proyecto_id',
                'pedidos_material.Cantidad',
                'pedidos.Pro_ID as proyecto_pedido'
            )
            ->leftjoin('tipo_movimiento_material_pedido', 'tipo_movimiento_material_pedido.material_id', 'materiales.Mat_ID')
            ->leftjoin('proyectos', 'proyectos.Pro_ID', 'tipo_movimiento_material_pedido.Pro_id_ubicacion')
            ->join('proyectos as proyecto_material', 'proyecto_material.Pro_ID', 'materiales.Pro_ID')
            ->join('pedidos_material', 'pedidos_material.Ped_Mat_ID', 'tipo_movimiento_material_pedido.Ped_Mat_ID')
            ->join('pedidos', 'pedidos.Ped_ID', 'pedidos_material.Ped_ID')
        //filtrando si hay proyectos
            ->when(!empty(request()->proyecto), function ($q) {
                //busqueda por pedidos realizados
                return $q
                    ->where('proyectos.Nombre', 'like', '%' . request()->proyecto . '%');
            })
        //filtrando si hay material
            ->when(!empty(request()->material), function ($q) {
                return $q->where('materiales.Denominacion', 'like', '%' . request()->material . '%');
            })
            ->groupBy('materiales.Mat_ID')
            ->groupBy('tipo_movimiento_material_pedido.Pro_id_ubicacion')
            ->orderBy('materiales.Cat_ID', 'ASC')
            ->get();

        return Datatables::of($materiales)
            ->addIndexColumn()
            ->addColumn('total', function ($data) {
                return $total = ($data->ingreso - $data->egreso);
            })
            ->addColumn('total_ordenada', function ($data) {
                return $this->total_ordenada($data->Mat_ID, $data->proyecto_pedido);
            })
            ->rawColumns(['total'])
            ->make(true);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function datatable_movimiento($id)
    {
        $pedido = DB::table('pedidos_material')
            ->select(
                'pedidos.*'
            )
            ->join('pedidos', 'pedidos.Ped_ID', 'pedidos_material.Ped_ID')
            ->where('pedidos_material.Ped_Mat_ID', $id)
            ->groupBy('pedidos.Ped_ID')
            ->first();
        $materiales = DB::table('materiales')
            ->select(
                'materiales.Mat_ID as material_id',
                'materiales.Denominacion',
                'materiales.Unidad_Medida',
                'tipo_movimiento_material_pedido.*',
                'tipo_orden_estatus.nombre as nombre_status',
                DB::raw('SUM(tipo_movimiento_material_pedido.ingreso) as total'),
                'proyectos.Nombre as nombre_ubicacion',
                DB::raw('DATE_FORMAT(tipo_movimiento_material_pedido.fecha , "%m/%d/%Y %H:%i:%s") as fecha'),
                DB::raw('DATE_FORMAT(tipo_movimiento_material_pedido.fecha_espera , "%m/%d/%Y %H:%i:%s") as fecha_espera')
            )
            ->join('pedidos_material', 'pedidos_material.Mat_ID', 'materiales.Mat_ID')
            ->join('pedidos', 'pedidos.Ped_ID', 'pedidos_material.Ped_ID')
            ->join('tipo_movimiento_material_pedido', 'tipo_movimiento_material_pedido.Ped_Mat_ID', 'pedidos_material.Ped_Mat_ID')
            ->join('proyectos', 'proyectos.Pro_ID', 'tipo_movimiento_material_pedido.Pro_id_ubicacion')
            ->join('tipo_orden_estatus', 'tipo_orden_estatus.id', 'tipo_movimiento_material_pedido.estatus_id')
            ->where('pedidos_material.Ped_Mat_ID', $id)
        //->whereRaw('pedidos.Pro_ID = tipo_movimiento_material_pedido.Pro_id_ubicacion')
            ->groupBy('tipo_movimiento_material_pedido.nro_movimiento')
            ->orderBy('tipo_movimiento_material_pedido.nro_movimiento', 'ASC')
            ->get();
        $pedido->materiales = $materiales;
        return response()->json([
            'status' => 'ok',
            'message' => 'Detalle traking',
            'data' => $pedido,
        ], 200);
    }
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
        //dd($request->all());
        foreach ($request->Ped_Mat_ID as $key => $value) {
            // modificar ingreso
            DB::table('tipo_movimiento_material_pedido')
                ->where('tipo_movimiento_material_pedido.Ped_Mat_ID', $value)
                ->where('tipo_movimiento_material_pedido.nro_movimiento', $request->nro_movimiento[$key])
                ->where('tipo_movimiento_material_pedido.egreso', '!=', 0)
                ->where('tipo_movimiento_material_pedido.egreso', '<>', null)
                ->update([
                    'egreso' => $request->movimiento_material_recepcion_cantidad[$key],
                ]);
            // modificar egreso
            DB::table('tipo_movimiento_material_pedido')
                ->where('tipo_movimiento_material_pedido.Ped_Mat_ID', $value)
                ->where('tipo_movimiento_material_pedido.nro_movimiento', $request->nro_movimiento[$key])
                ->where('tipo_movimiento_material_pedido.ingreso', '!=', 0)
                ->where('tipo_movimiento_material_pedido.ingreso', '<>', null)
                ->update([
                    'ingreso' => $request->movimiento_material_recepcion_cantidad[$key],
                ]);
        }
        return response()->json([
            "status" => "ok",
            "message" => 'modified Successfully',
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, $material_pedido_id)
    {
        //dd($id, $material_pedido_id);
        $delete_movimiento = DB::table('tipo_movimiento_material_pedido')
            ->where('tipo_movimiento_material_pedido.Ped_Mat_ID', $material_pedido_id)
            ->where('tipo_movimiento_material_pedido.nro_movimiento', $id)
            ->delete();

        $pedido = DB::table('pedidos_material')
            ->select('pedidos_material.*')
            ->join('tipo_movimiento_material_pedido', 'pedidos_material.Ped_Mat_ID', 'tipo_movimiento_material_pedido.Ped_Mat_ID')
            ->where('tipo_movimiento_material_pedido.Ped_Mat_ID', $material_pedido_id)
            ->first();
        if (!is_null($pedido)) {
            $this->verificar_pedido($pedido->Ped_ID);
        } else {
            $this->restaturar_status($material_pedido_id);
        }
        if ($delete_movimiento) {
            return response()->json([
                "status" => "ok",
                "message" => 'Delete Successfully',
            ], 200);
        } else {
            return response()->json([
                "status" => "errors",
                "message" => ['Error inesperado'],
            ], 200);
        }
    }
    /*verificar pedidos con movimientos */
    private function restaturar_status($material_pedido_id)
    {
        $actualizando = DB::table('pedidos')
            ->join('pedidos_material', 'pedidos_material.Ped_ID', 'pedidos.Ped_ID')
            ->where('pedidos_material.Ped_Mat_ID', $material_pedido_id)
            ->update([
                'status_id' => 3,
            ]);
    }
    private function verificar_pedido($pedido_id)
    {
        $pedidos = DB::table('pedidos_material')
            ->select(
                'pedidos_material.Ped_Mat_ID',
                'pedidos_material.Cantidad'
            )
            ->where('pedidos_material.Ped_ID', $pedido_id)
            ->get();
        $resultado = [];

        foreach ($pedidos as $keyP => $pedido) {

            $material_pedido = DB::table('tipo_movimiento_material_pedido')
                ->select(
                    'tipo_movimiento_material_pedido.ingreso'
                )
                ->join('pedidos_material', 'pedidos_material.Ped_Mat_ID', 'tipo_movimiento_material_pedido.Ped_Mat_ID')
                ->where('pedidos_material.Ped_Mat_ID', $pedido->Ped_Mat_ID)
                ->get();

            $total = 0;
            /* suma de ingresos */
            foreach ($material_pedido as $key => $value) {
                $total += $value->ingreso;
            }
            $pedidos[$keyP]->ingreso = $total;
            $resultado[] = $pedidos[$keyP];
        }
        $verificar = 'no completado';
        foreach ($resultado as $key => $value) {
            if ($value->ingreso >= $value->Cantidad) {
                $verificar = 'completado';
            } else {
                if ($value->ingreso != 0) {
                    $verificar = 'parcial recibido';
                    break;
                } else {
                    $verificar = 'no completado';
                }
            }
        }
        switch ($verificar) {
            case 'parcial recibido':
                $actualizando = DB::table('pedidos')
                    ->where('pedidos.Ped_ID', $pedido_id)
                    ->update([
                        'status_id' => 11,
                    ]);
                break;
            case 'completado':
                $actualizando = DB::table('pedidos')
                    ->where('pedidos.Ped_ID', $pedido_id)
                    ->update([
                        'status_id' => 14,
                    ]);
                break;
            case 'no completado':
                $actualizando = DB::table('pedidos')
                    ->where('pedidos.Ped_ID', $pedido_id)
                    ->update([
                        'status_id' => 3,
                    ]);
                break;
            default:
                # code...
                break;
        }
    }
}
