<?php

namespace App\Http\Controllers\Order;

use App\Http\Controllers\Controller;
use DB;
use Illuminate\Http\Request;
use Storage;

class OrdenTransferenciaController extends Controller
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
        return view('panel.orden_tranferencia.index');
    }

    public function dataTable()
    {
        $tranferencias = DB::table('pedidos')
            ->select(
                DB::raw("CONCAT(COALESCE(personal.Nombre,''), ' ',COALESCE(personal.Apellido_Paterno,''), ' ',COALESCE(personal.Apellido_Materno,'')) as nombre_completo"),
                DB::raw('"modificado" as estado'),
                DB::raw('"Ordered" as estado_po'),
                'tipo_orden.fecha_order',
                'tipo_orden.fecha_entrega',
                'tipo_orden.creado_por',
                'enviar.Nombre as enviar',
                'recibir.Nombre as recibir',
                DB::raw("CONCAT(materiales.Denominacion,' - ', materiales.Unidad_Medida) as Denominacion"),
                'pedidos.Ped_ID',
                'pedidos.PO',
                'pedidos.Pro_ID',
                'pedidos.Ven_ID',
                'pedidos.To_ID',
                'pedidos_material.Cantidad',
                'pedidos_material.Mat_ID',
                'pedidos_material.Ped_Mat_ID',
                'pedidos_material.Aux1 as note',
                'pro.Nombre as pertenece',
                DB::raw('0 as cant_warehouse'),
                DB::raw('0 as cant_proyecto')
            )
            ->join('tipo_orden', 'tipo_orden.id', 'pedidos.tipo_orden_id')
            ->join('personal', 'personal.Empleado_ID', 'tipo_orden.creado_por')
            ->join('pedidos_material', 'pedidos_material.Ped_ID', 'pedidos.Ped_ID')
            ->join('materiales', 'materiales.Mat_ID', 'pedidos_material.Mat_ID')
            ->join('proyectos as enviar', 'enviar.Pro_ID', 'pedidos.Ven_ID')
            ->join('proyectos as recibir', 'recibir.Pro_ID', 'pedidos.To_ID')
            ->join('proyectos as pro', 'pro.Pro_ID', 'pedidos.To_ID')
            ->when(!auth()->user()->verificarRol([1, 10]), function ($query) {
                return $query->where(function ($q) {
                    $q->where('tipo_orden.creado_por', auth()->user()->Empleado_ID);
                });
            })
            ->where('enviar.Emp_ID', '<>', 119)
            ->where('recibir.Emp_ID', '<>', 119)
            ->orderBy('tipo_orden.fecha_order', 'Desc')
            ->orderBy('pedidos.Ped_ID', 'Desc')
            ->get();
        foreach ($tranferencias as $key => $tranferencia) {
            $cant_warehouse = DB::table('tipo_movimiento_material_pedido')
                ->select(
                    DB::raw('SUM(COALESCE(tipo_movimiento_material_pedido.ingreso,0)) - SUM(COALESCE(tipo_movimiento_material_pedido.egreso,0)) as cant_warehouse'),
                )
                ->where('material_id', $tranferencia->Mat_ID)
                ->where('Pro_id_ubicacion', 1)
                ->first();
            $tranferencia->cant_warehouse = $cant_warehouse->cant_warehouse;
            $cant_proyecto = DB::table('tipo_movimiento_material_pedido')
                ->select(
                    DB::raw('SUM(COALESCE(tipo_movimiento_material_pedido.ingreso,0)) - SUM(COALESCE(tipo_movimiento_material_pedido.egreso,0)) as cant_proyecto'),
                )
                ->where('material_id', $tranferencia->Mat_ID)
                ->where('Pro_id_ubicacion', $tranferencia->To_ID == 1 ? $tranferencia->Ven_ID : $tranferencia->To_ID)
                ->first();
            $tranferencia->cant_proyecto = $cant_proyecto->cant_proyecto;
        }
        return response()->json([
            'status' => 'ok',
            'message' => "Lista tranfenrecia",
            'data' => $tranferencias,
        ]);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function proyectos_from(Request $request)
    {
        if (!isset($request->searchTerm)) {
            $proyectos = $proyectos = DB::table('proyectos')
                ->select('proyectos.*')
                ->where('proyectos.Pro_ID', '<>', 119)
                ->get();
        } else {
            $proyectos = DB::table('proyectos')
                ->select('proyectos.*')
                ->where('proyectos.Nombre', 'like', '%' . $request->searchTerm . '%')
            //->where('proyectos.Pro_ID', '<>', 119)
                ->get();
        }
        $data = [];
        foreach ($proyectos as $row) {
            $data[] = array(
                "id" => $row->Pro_ID,
                "text" => $row->Nombre,
            );
        }
        return response()->json($data);
    }

    public function proyectos_to(Request $request)
    {
        if (!isset($request->searchTerm)) {
            $proyectos = $proyectos = DB::table('proyectos')
                ->select('proyectos.*')
                ->where('proyectos.Pro_ID', '<>', 119)
                ->get();
        } else {
            $proyectos = DB::table('proyectos')
                ->select('proyectos.*')
                ->where('proyectos.Nombre', 'like', '%' . $request->searchTerm . '%')
            //->where('proyectos.Pro_ID', '<>', 119)
                ->get();
        }
        $data = [];
        foreach ($proyectos as $row) {
            $data[] = array(
                "id" => $row->Pro_ID,
                "text" => $row->Nombre,
            );
        }
        return response()->json($data);
    }

    public function pedidos(Request $request)
    {
        if (!isset($request->searchTerm)) {
            $proyectos = $proyectos = DB::table('pedidos')
                ->select('pedidos.*')
                ->orderBy('pedidos.Ped_ID', 'DESC')
                ->get();
        } else {
            $proyectos = DB::table('pedidos')
                ->select('pedidos.*')
                ->where('pedidos.PO', 'like', '%' . $request->searchTerm . '%')
                ->orderBy('pedidos.Ped_ID', 'DESC')
                ->get();
        }
        $data = [];
        foreach ($proyectos as $row) {
            $data[] = array(
                "id" => $row->Ped_ID,
                "text" => $row->PO,
            );
        }
        return response()->json($data);
    }

    public function material(Request $request)
    {
        if (!isset($request->searchTerm)) {
            $materiales = DB::table('materiales')->select('materiales.*', 'categoria_material.*')
                ->Join('proyectos', 'proyectos.Pro_ID', 'materiales.Pro_ID')
                ->Join('categoria_material', 'categoria_material.Cat_ID', 'materiales.Cat_ID')
                ->where(function ($query) use ($request) {
                    $query->where('materiales.Pro_ID', $request->Ven_ID)
                        ->orWhere('proyectos.Pro_ID', $request->To_ID);
                })
                ->distinct('materiales.Mat_ID')
                ->orderBy('categoria_material.Cat_ID')
                ->orderBy('proyectos.Pro_ID', 'DESC')
                ->orderBy('materiales.Denominacion')
                ->get();
        } else {
            $materiales = DB::table('materiales')->select('materiales.*', 'categoria_material.*')
                ->where('Denominacion', 'like', '%' . $request->searchTerm . '%')
                ->Join('proyectos', 'proyectos.Pro_ID', 'materiales.Pro_ID')
                ->Join('categoria_material', 'categoria_material.Cat_ID', 'materiales.Cat_ID')
                ->where(function ($query) use ($request) {
                    $query->where('materiales.Pro_ID', $request->Ven_ID)
                        ->orWhere('proyectos.Pro_ID', $request->To_ID);
                })
                ->orderBy('categoria_material.Cat_ID')
                ->distinct('materiales.Mat_ID')
                ->orderBy('proyectos.Pro_ID', 'DESC')
                ->orderBy('materiales.Denominacion')
                ->get();
        }
        $data = [];

        foreach ($materiales as $key => $material) {
            $cant_warehouse = DB::table('tipo_movimiento_material_pedido')
                ->select(
                    DB::raw('SUM(COALESCE(tipo_movimiento_material_pedido.ingreso,0)) - SUM(COALESCE(tipo_movimiento_material_pedido.egreso,0)) as cant_warehouse'),
                )
                ->where('material_id', $material->Mat_ID)
                ->where('Pro_id_ubicacion', 1)
                ->first();
            $material->cant_warehouse = $cant_warehouse->cant_warehouse;
            $cant_proyecto = DB::table('tipo_movimiento_material_pedido')
                ->select(
                    DB::raw('SUM(COALESCE(tipo_movimiento_material_pedido.ingreso,0)) - SUM(COALESCE(tipo_movimiento_material_pedido.egreso,0)) as cant_proyecto'),
                )
                ->where('material_id', $material->Mat_ID)
                ->where('Pro_id_ubicacion', $request->To_ID == 1 ? $request->Ven_ID : $request->To_ID)
                ->first();
            $material->cant_proyecto = $cant_proyecto->cant_proyecto;
        }

        foreach ($materiales as $row) {
            $data[] = array(
                "id" => $row->Mat_ID,
                "text" => "$row->Denominacion - $row->Unidad_Medida",
                "Unidad_Medida" => $row->Unidad_Medida,
                "tipo_id" => $row->Cat_ID,
                "tipo_nombre" => $row->Nombre,
                "cant_warehouse" => $row->cant_warehouse,
                "cant_proyecto" => $row->cant_proyecto,
            );
        }
        return response()->json($data);
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        foreach ($request->formulario as $key => $pedido) {

            $verificar = DB::table('tipo_orden')
                ->select(
                    'pedidos.*'
                )
                ->join('pedidos', 'tipo_orden.id', 'pedidos.tipo_orden_id')
                ->whereDate('tipo_orden.fecha_entrega', date('Y-m-d', strtotime($pedido['fecha_entrega'])))
                ->where('tipo_orden.creado_por', $pedido['creado_por'])
                ->whereDate('pedidos.Fecha', 'like', date('Y-m-d', strtotime($pedido['fecha_order'])))
                ->where('pedidos.Ven_ID', $pedido['Ven_ID'])
                ->where('pedidos.To_ID', $pedido['To_ID'])
                ->orderBy('pedidos.Ped_ID', 'DESC')
                ->first();
            if ($verificar) {
                //si existe orden
                $pedido['Ped_ID'] = $verificar->Ped_ID;
                $this->orden_nuevo_material($pedido, $verificar);
            } else {
                //no existe
                $this->crear_orden($pedido);
            }
        }

        return response()->json([
            "status" => "ok",
            "message" => 'Registered Successfully',
            "data" => null,
        ], 200);
    }

    public function crear_orden($pedido)
    {
        $obtener_proyecto = DB::table('proyectos')
            ->select(
                'empresas.Codigo as codigo_empresa',
                'empresas.Nombre as nombre_empresa',
                'proyectos.*'
            )
            ->join('empresas', 'empresas.Emp_ID', 'proyectos.Emp_ID')
            ->where('Pro_ID', $pedido['To_ID'])
            ->first();
        $obtener_ultima_orden = DB::table('tipo_orden')
            ->orderBy('num', 'DESC')
            ->first();

        $insertPreOrder = DB::table('tipo_orden')
            ->insertGetId([
                'proyecto_id' => $pedido['To_ID'],
                'estatus_id' => 14,
                'num' => $obtener_ultima_orden->num,
                'nota' => 'Fast transfer',
                'nombre_trabajo' => $obtener_proyecto->Nombre,
                'estado' => 'creado',
                'fecha_order' => date('Y-m-d H:i:s', strtotime($pedido['fecha_order'])),
                'fecha_entrega' => date('Y-m-d H:i:s', strtotime($pedido['fecha_entrega'])),
                'creado_por' => auth()->user()->Empleado_ID,
                'eliminado' => 0,
            ]);
        //create pre-orden- materiales
        $insertOrderMateriales = DB::table('tipo_orden_materiales')
            ->insertGetId([
                'material_id' => $pedido['Mat_ID'],
                'tipo_orden_id' => $insertPreOrder,
                'tipo_orden_material_id' => 1,
                'cant_ordenada' => $pedido['Cantidad'],
                'cant_registrada' => $pedido['Cantidad'],
                'estado' => 1,
                'nota_material' => $pedido['note'],
            ]);

        //pedido
        $PCO = DB::table('pedidos')
            ->join('proyectos', 'proyectos.Pro_ID', 'pedidos.Pro_ID')
            ->where('proyectos.Pro_ID', $pedido['To_ID'])
            ->count() + 1;

        $insertPedido = DB::table('pedidos')
            ->insertGetId([
                'Pro_ID' => $pedido['To_ID'],
                'Ven_ID' => $pedido['Ven_ID'],
                'To_ID' => $pedido['To_ID'],
                'Fecha' => date('Y-m-d H:i:s', strtotime($pedido['fecha_order'])),
                'OperatorID' => 1,
                'Note' => 'Please deliver at ' . $obtener_proyecto->codigo_empresa . ', ' . $obtener_proyecto->Nombre . ' ' . $obtener_proyecto->Calle . ' ' . $obtener_proyecto->Ciudad . ' ' . $obtener_proyecto->Estado . ' ' . $obtener_proyecto->Zip_Code,
                'PO' => $obtener_proyecto->Codigo . '-' . $PCO,
                'PO_Corr' => $PCO,
                'status_id' => 14,
                'tipo_orden_id' => $insertPreOrder,
            ]);
        //pedido materiales
        $insertPedidoMaterial = DB::table('pedidos_material')
            ->insertGetId([
                'Ped_ID' => $insertPedido,
                'Mat_ID' => $pedido['Mat_ID'],
                'Task_ID' => null,
                'Actividad_ID' => null,
                'Cantidad' => $pedido['Cantidad'],
                'Cantidad_Recibida' => null,
                'Cantidad_Usada' => null,
                'Aux1' => $pedido['note'],
            ]);

        $insertMovimientoPedido = DB::table('tipo_movimiento_pedido')
            ->insertGetId([
                'Ped_ID' => $insertPedido,
                'fecha' => date('Y-m-d H:i:s', strtotime($pedido['fecha_entrega'])),
                'fecha_espera' => date('Y-m-d H:i:s', strtotime($pedido['fecha_entrega'])),
                'nota' => 'Please deliver at ' . $obtener_proyecto->codigo_empresa . ', ' . $obtener_proyecto->Nombre . ' ' . $obtener_proyecto->Calle . ' ' . $obtener_proyecto->Ciudad . ' ' . $obtener_proyecto->Estado . ' ' . $obtener_proyecto->Zip_Code,
            ]);

        $insertMovimientoMaterialesPedido = DB::table('tipo_movimiento_material_pedido')
            ->insertGetId([
                'estatus_id' => 14,
                'Ped_Mat_ID' => $insertPedidoMaterial,
                'material_id' => $pedido['Mat_ID'],
                'fecha' => date('Y-m-d H:i:s', strtotime($pedido['fecha_entrega'])),
                'fecha_espera' => date('Y-m-d H:i:s', strtotime($pedido['fecha_entrega'])),
                'nota' => 'materials received',
                'estado' => 1,
                'egreso' => $pedido['Cantidad'],
                'ingreso' => 0,
                'Pro_id_ubicacion' => $pedido['Ven_ID'],
            ]);

        $insertMovimientoMaterialesPedido = DB::table('tipo_movimiento_material_pedido')
            ->insertGetId([
                'estatus_id' => 14,
                'Ped_Mat_ID' => $insertPedidoMaterial,
                'material_id' => $pedido['Mat_ID'],
                'fecha' => date('Y-m-d H:i:s', strtotime($pedido['fecha_entrega'])),
                'fecha_espera' => date('Y-m-d H:i:s', strtotime($pedido['fecha_entrega'])),
                'nota' => 'materials received',
                'estado' => 1,
                'egreso' => 0,
                'ingreso' => $pedido['Cantidad'],
                'Pro_id_ubicacion' => $pedido['To_ID'],
            ]);
    }

    public function orden_nuevo_material($pedido, $data)
    {
        $insertPedidoMaterial = DB::table('pedidos_material')
            ->insertGetId([
                'Ped_ID' => $pedido['Ped_ID'],
                'Mat_ID' => $pedido['Mat_ID'],
                'Task_ID' => null,
                'Actividad_ID' => null,
                'Cantidad' => $pedido['Cantidad'],
                'Cantidad_Recibida' => null,
                'Cantidad_Usada' => null,
                'Aux1' => $pedido['note'],
            ]);
        $insertOrderMateriales = DB::table('tipo_orden_materiales')
            ->insertGetId([
                'material_id' => $pedido['Mat_ID'],
                'tipo_orden_id' => $data->tipo_orden_id,
                'tipo_orden_material_id' => 1,
                'cant_ordenada' => $pedido['Cantidad'],
                'cant_registrada' => $pedido['Cantidad'],
                'estado' => 1,
                'nota_material' => $pedido['note'],
            ]);
        $movimiento = DB::table('tipo_movimiento_material_pedido')
            ->select(
                DB::raw('max(nro_movimiento) as nro_movimiento')
            )
            ->where("Ped_Mat_ID", $insertPedidoMaterial)
            ->first();
        $insertMovimientoMaterialesPedido = DB::table('tipo_movimiento_material_pedido')
            ->insertGetId([
                'estatus_id' => 14,
                'Ped_Mat_ID' => $insertPedidoMaterial,
                'material_id' => $pedido['Mat_ID'],
                'fecha' => date('Y-m-d H:i:s', strtotime($pedido['fecha_entrega'])),
                'fecha_espera' => date('Y-m-d H:i:s', strtotime($pedido['fecha_entrega'])),
                'nota' => 'materials received',
                'estado' => 1,
                'egreso' => $pedido['Cantidad'],
                'ingreso' => 0,
                'Pro_id_ubicacion' => $pedido['Ven_ID'],
                "nro_movimiento" => ($movimiento->nro_movimiento != null) ? $movimiento->nro_movimiento + 1 : 1,
            ]);

        $insertMovimientoMaterialesPedido = DB::table('tipo_movimiento_material_pedido')
            ->insertGetId([
                'estatus_id' => 14,
                'Ped_Mat_ID' => $insertPedidoMaterial,
                'material_id' => $pedido['Mat_ID'],
                'fecha' => date('Y-m-d H:i:s', strtotime($pedido['fecha_entrega'])),
                'fecha_espera' => date('Y-m-d H:i:s', strtotime($pedido['fecha_entrega'])),
                'nota' => 'materials received',
                'estado' => 1,
                'egreso' => 0,
                'ingreso' => $pedido['Cantidad'],
                'Pro_id_ubicacion' => $pedido['To_ID'],
                "nro_movimiento" => ($movimiento->nro_movimiento != null) ? $movimiento->nro_movimiento + 1 : 1,
            ]);
    }
    public function update(Request $request)
    {
        foreach ($request->formulario as $key => $pedido) {

            $obtener_proyecto = DB::table('proyectos')
                ->select(
                    'empresas.Codigo as codigo_empresa',
                    'empresas.Nombre as nombre_empresa',
                    'proyectos.*'
                )
                ->join('empresas', 'empresas.Emp_ID', 'proyectos.Emp_ID')
                ->where('Pro_ID', $pedido['To_ID'])
                ->first();

            $updatePedido = DB::table('pedidos')
                ->where('Ped_ID', $pedido['Ped_ID'])
                ->update([
                    'Pro_ID' => $pedido['To_ID'],
                    'Ven_ID' => $pedido['Ven_ID'],
                    'To_ID' => $pedido['To_ID'],
                    'Fecha' => date('Y-m-d H:i:s', strtotime($pedido['fecha_order'])),
                    'status_id' => 14,
                ]);

            $updatePedidoMaterial = DB::table('pedidos_material')
                ->where('Ped_ID', $pedido['Ped_ID'])
                ->update([
                    'Mat_ID' => $pedido['Mat_ID'],
                    'Cantidad' => $pedido['Cantidad'],
                    'Aux1' => $pedido['note'],
                ]);

            //obtener tipo order
            $obtener_tipo_orden = DB::table('pedidos')
                ->where('Ped_ID', $pedido['Ped_ID'])
                ->first();

            //update pre-orden- materiales
            if ($obtener_tipo_orden) {
                $update_tipo_orden = DB::table('tipo_orden')
                    ->where('id', $obtener_tipo_orden->tipo_orden_id)
                    ->update([
                        'proyecto_id' => $pedido['To_ID'],
                        'estatus_id' => 14,
                        'nombre_trabajo' => $obtener_proyecto->Nombre,
                        'fecha_order' => date('Y-m-d H:i:s', strtotime($pedido['fecha_order'])),
                        'fecha_entrega' => date('Y-m-d H:i:s', strtotime($pedido['fecha_entrega'])),
                    ]);
                $updateOrderMateriales = DB::table('tipo_orden_materiales')
                    ->where('tipo_orden_id', $obtener_tipo_orden->tipo_orden_id)
                    ->update([
                        'material_id' => $pedido['Mat_ID'],
                        'cant_ordenada' => $pedido['Cantidad'],
                        'cant_registrada' => $pedido['Cantidad'],
                        'nota_material' => $pedido['note'],
                    ]);
            }
            //update movimiento pedido
            $updateMovimientoPedido = DB::table('tipo_movimiento_pedido')
                ->where('Ped_ID', $pedido['Ped_ID'])
                ->update([
                    'fecha' => date('Y-m-d H:i:s', strtotime($pedido['fecha_entrega'])),
                    'fecha_espera' => date('Y-m-d H:i:s', strtotime($pedido['fecha_entrega'])),
                    'nota' => 'Please deliver at ' . $obtener_proyecto->codigo_empresa . ', ' . $obtener_proyecto->Nombre . ' ' . $obtener_proyecto->Calle . ' ' . $obtener_proyecto->Ciudad . ' ' . $obtener_proyecto->Estado . ' ' . $obtener_proyecto->Zip_Code,
                ]);
            //obtener movimientos
            $obtener_tipo_movimiento_material_pedido = DB::table('tipo_movimiento_material_pedido')
                ->where('tipo_movimiento_material_pedido.Ped_Mat_ID', $pedido['Ped_Mat_ID'])
                ->get();
            foreach ($obtener_tipo_movimiento_material_pedido as $key => $value) {
                $tipo_movimiento_material_pedido_imagen = DB::table('tipo_movimiento_material_pedido_imagen')
                    ->where('tipo_movimiento_material_pedido_imagen.tipo_movimiento_material_pedido_id', $value->id)
                    ->delete();
            }
            $tipo_movimiento_material_pedido = DB::table('tipo_movimiento_material_pedido')
                ->where('tipo_movimiento_material_pedido.Ped_Mat_ID', $pedido['Ped_Mat_ID'])
                ->delete();

            $movimiento = DB::table('tipo_movimiento_material_pedido')
                ->select(
                    DB::raw('max(nro_movimiento) as nro_movimiento')
                )
                ->where("Ped_Mat_ID", $pedido['Ped_Mat_ID'])
                ->first();
            $insertMovimientoMaterialesPedido = DB::table('tipo_movimiento_material_pedido')
                ->insertGetId([
                    'estatus_id' => 14,
                    'Ped_Mat_ID' => $pedido['Ped_Mat_ID'],
                    'material_id' => $pedido['Mat_ID'],
                    'fecha' => date('Y-m-d H:i:s', strtotime($pedido['fecha_entrega'])),
                    'fecha_espera' => date('Y-m-d H:i:s', strtotime($pedido['fecha_entrega'])),
                    'nota' => 'materials received',
                    'estado' => 1,
                    'egreso' => $pedido['Cantidad'],
                    'ingreso' => 0,
                    'Pro_id_ubicacion' => $pedido['Ven_ID'],
                    "nro_movimiento" => ($movimiento->nro_movimiento != null) ? $movimiento->nro_movimiento : 1,
                ]);

            $insertMovimientoMaterialesPedido = DB::table('tipo_movimiento_material_pedido')
                ->insertGetId([
                    'estatus_id' => 14,
                    'Ped_Mat_ID' => $pedido['Ped_Mat_ID'],
                    'material_id' => $pedido['Mat_ID'],
                    'fecha' => date('Y-m-d H:i:s', strtotime($pedido['fecha_entrega'])),
                    'fecha_espera' => date('Y-m-d H:i:s', strtotime($pedido['fecha_entrega'])),
                    'nota' => 'materials received',
                    'estado' => 1,
                    'egreso' => 0,
                    'ingreso' => $pedido['Cantidad'],
                    'Pro_id_ubicacion' => $pedido['To_ID'],
                    "nro_movimiento" => ($movimiento->nro_movimiento != null) ? $movimiento->nro_movimiento : 1,
                ]);
        }

        return response()->json([
            "status" => "ok",
            "message" => 'Registered Successfully',
            "data" => null,
        ], 200);
    }

    public function verificar_orden(Request $request)
    {
        $verificar = DB::table('tipo_orden')
            ->select('pedidos.*')
            ->join('pedidos', 'tipo_orden.id', 'pedidos.tipo_orden_id')
            ->whereDate('tipo_orden.fecha_entrega', date('Y-m-d', strtotime($request->fecha_entrega)))
            ->where('tipo_orden.creado_por', $request->creado_por)
            ->whereDate('pedidos.Fecha', 'like', date('Y-m-d', strtotime($request->fecha_order)))
            ->where('pedidos.Ven_ID', $request->Ven_ID)
            ->where('pedidos.To_ID', $request->To_ID)
            ->orderBy('pedidos.Ped_ID', 'DESC')
            ->first();
        $obtener_proyecto = DB::table('proyectos')
            ->select(
                'empresas.Codigo as codigo_empresa',
                'empresas.Nombre as nombre_empresa',
                'proyectos.*'
            )
            ->join('empresas', 'empresas.Emp_ID', 'proyectos.Emp_ID')
            ->where('Pro_ID', $request->To_ID)
            ->first();
        if ($verificar) {
            return response()->json([
                "status" => "ok",
                "message" => 'Registered Successfully',
                "data" => [
                    "PO" => $verificar->PO,
                    "estado_po" => "Ordered",
                ],
            ], 200);
        } else {
            $PCO = DB::table('pedidos')
                ->join('proyectos', 'proyectos.Pro_ID', 'pedidos.Pro_ID')
                ->where('proyectos.Pro_ID', $request->To_ID)
                ->count() + 1;
            return response()->json([
                "status" => "ok",
                "message" => 'Registered Successfully',
                "data" => [
                    "PO" => $obtener_proyecto->Codigo . '-' . $PCO,
                    "estado_po" => "New",
                    "fecha_actual" => date('Y-m-d', strtotime($request->fecha_orden)),
                ],
            ], 200);
        }
    }
    public function delete_material($id)
    {
        $verificar = DB::table('pedidos_material')
            ->join('pedidos', 'pedidos.Ped_ID', 'pedidos_material.Ped_ID')
            ->where('pedidos_material.Ped_Mat_ID', $id)
            ->first();
        if ($verificar) {
            $material_pedido = DB::table('pedidos_material')->where('Ped_Mat_ID', $id)->delete();
            $tipo_orden_material_pedido = DB::table('tipo_orden_materiales')
                ->where('tipo_orden_materiales.material_id', $verificar->Mat_ID)
                ->where('tipo_orden_materiales.cant_ordenada', $verificar->Cantidad)
                ->where('tipo_orden_materiales.tipo_orden_id', $verificar->tipo_orden_id)
                ->delete();
            $obtener_tipo_movimiento_material_pedido = DB::table('tipo_movimiento_material_pedido')
                ->where('tipo_movimiento_material_pedido.Ped_Mat_ID', $id)
                ->get();
            foreach ($obtener_tipo_movimiento_material_pedido as $key => $value) {
                $tipo_movimiento_material_pedido_imagen = DB::table('tipo_movimiento_material_pedido_imagen')
                    ->where('tipo_movimiento_material_pedido_imagen.tipo_movimiento_material_pedido_id', $value->id)
                    ->delete();
            }
            $tipo_movimiento_material_pedido = DB::table('tipo_movimiento_material_pedido')
                ->where('tipo_movimiento_material_pedido.Ped_Mat_ID', $id)
                ->delete();
        }
        return response()->json([
            "status" => "ok",
            "message" => 'Removed successfully',
            "data" => null,
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

    public function destroy($id)
    {
        //
    }
}
