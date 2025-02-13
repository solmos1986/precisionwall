<?php

namespace App\Http\Controllers;

use DataTables;
use DB;
use Illuminate\Http\Request;
use Validator;
use \stdClass;

class TipoOrdenPedidoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    /* funcion para verificar materiales seleccionados */
    private function verificando_registro_material($materiales_id)
    {
        $verificar = DB::table('tipo_orden_materiales')
            ->whereIn('id', $materiales_id)
            ->get();
        $resultado = [];
        foreach ($verificar as $key => $material) {
            if ($material->cant_ordenada != 0) {
                $resultado[] = $material->id;
            }
        }
        return $resultado;
    }
    /* funciones de verificacion */
    private function verificar_existe_sub_order($order_id)
    {
        $verficar = DB::table('pedidos')
            ->where('pedidos.tipo_orden_id', $order_id)
            ->count();
        if ($verficar > 0) {
            return true;
        } else {
            return false;
        }
    }
    private function restaurar_order($order_id)
    {

        $verficar = DB::table('tipo_orden')
            ->where('tipo_orden.id', $order_id)
            ->update([
                'estatus_id' => 1,
            ]);
    }
    private function verificar_cantidad_ordenada_requerida($order_id)
    {
        $verficar = DB::table('tipo_orden_materiales')
            ->where('tipo_orden_materiales.tipo_orden_id', $order_id)
            ->get();
        $resultado = false;
        foreach ($verficar as $key => $value) {
            /*  verificar si es la cantidad ordenada en menor a la registrada */
            if ($value->cant_ordenada >= $value->cant_registrada) {
                break;
                return $resultado;
            } else {
                if ($value->cant_ordenada == 0) {
                    return $resultado = true;
                } else {
                    break;
                    return $resultado;
                }
            }
        }
    }

    private function update_estatus_requerimientos($order_id, $status_id)
    {
        $orden = DB::table('tipo_orden')
            ->where('tipo_orden.id', $order_id)
            ->update([
                'estatus_id' => $status_id,
            ]);
    }
    private function update_status_orden_automatico($order_id)
    {
        /* segunda opcion */
        $pedidos = DB::table('pedidos')
            ->select(
                'pedidos.status_id'
            )
            ->where('tipo_orden_id', $order_id)
            ->get();
        /* primera validacion  si el requerimiento es parcial*/
        if (!$this->verificar_cantidad_ordenada_requerida($order_id)) {
            $this->update_estatus_requerimientos($order_id, 13);
        } else {
            $this->update_estatus_requerimientos($order_id, 3);
        }
        $status = 1;
        foreach ($pedidos as $key => $pedido) {
            if ($pedido->status_id == 7) {
                $status = 7;
                break;
            }
            if ($pedido->status_id == 11) {
                $status = 11;
                break;
            }
            if ($pedido->status_id == 12) {
                $status = 12;
                break;
            }
            if ($pedido->status_id == 3) {
                $status = 3;
            }
            if ($pedido->status_id == 14) {
                $status = 14;
            }
        }
        $this->update_estatus_requerimientos($order_id, $status);

    }
    /* fin funciones de verificacion */
    public function datatable_order_sub_order($id)
    {
        $pedido = DB::table('pedidos')
            ->select(
                'pedidos.*',
                DB::raw('DATE_FORMAT(pedidos.Fecha , "%m/%d/%Y %H:%i:%s") as Fecha'),
                'from.Nombre as from',
                'to.Nombre as to',
                'tipo_orden_estatus.id as status_id',
                'tipo_orden_estatus.nombre as nombre_status',
                'tipo_orden_estatus.color'
            )
            ->join('proyectos as from', 'from.Pro_ID', 'pedidos.Ven_ID')
            ->leftJoin('proyectos as to', 'to.Pro_ID', 'pedidos.Pro_ID')
            ->join('tipo_orden_estatus', 'tipo_orden_estatus.id', 'pedidos.status_id')
            ->where('pedidos.tipo_orden_id', $id)
            ->get();
        return Datatables::of($pedido)
            ->addIndexColumn()
            ->addColumn('acciones', function ($data) {
                /* verfificacion de roles */
                $acceso_total = "";
                if (!auth()->user()->verificarRol([7])) {
                    $acceso_total = "
                    <i class='fas fa-envelope ms-text-secondary cursor-pointer view-mail' data-pedido_id='$data->Ped_ID' data-orden_id='$data->tipo_orden_id' title='View email'></i>
                    <i class='fas fa-pencil-alt ms-text-warning cursor-pointer edit_sub_orden' data-pedido_id='$data->Ped_ID'  title='Edit'></i>
                    <i class='far fa-trash-alt ms-text-danger cursor-pointer delete_sub_orden' data-pedido_id='$data->Ped_ID' title='Delete'></i>
                   ";
                }
                if ($data->status_id == 7 || $data->status_id == 12) {
                    $button = "
                <i class='fas fa-inbox ms-text-secondary cursor-pointer create_recepcion' data-pedido_id='$data->Ped_ID' title='Receive materials'></i>
                <!--i class='fas fa-history ms-text-secondary cursor-pointer create_seguimiento'  title='Register traking'></i-->
                <i class='fas fa-exchange-alt ms-text-secondary cursor-pointer asignar_deliver' data-pedido_id='$data->Ped_ID'  title='Transfer'></i>
                $acceso_total
                ";
                } else {
                    $button = "
                    <i class='fas fa-inbox ms-text-secondary cursor-pointer create_recepcion' data-pedido_id='$data->Ped_ID' title='Receive materials'></i>
                    <!--i class='fas fa-history ms-text-secondary cursor-pointer create_seguimiento'  title='Register traking'></i-->
                    $acceso_total
                    ";
                }
                return $button;
            })
            ->addColumn('status', function ($data) {
                return $html = "<h5><span class='badge badge-$data->color'>$data->nombre_status</span></h5>";
            })
            ->rawColumns(['acciones', 'status'])
            ->make(true);
    }
    public function show_orden(Request $request, $id)
    {
        $rules = array(
            'id_materiales' => 'required',
        );
        $messages = [
            'id_materiales.required' => "The selection of materials is required",
        ];
        $error = Validator::make($request->all(), $rules, $messages);
        if ($error->errors()->all()) {
            return response()->json([
                "status" => "errors",
                "message" => $error->errors()->all()]);
        }
        /* verificando materiales */
        $request->id_materiales = $this->verificando_registro_material($request->id_materiales);
        if (empty($request->id_materiales)) {
            return response()->json([
                "status" => "errors",
                "message" => [' materials already ordered'],
            ]);
        }
        /*fin verificando materiales */
        $orden = DB::table('tipo_orden')
            ->select(
                'tipo_orden.*',
                'proyectos.Codigo'
            )
            ->where('tipo_orden.id', $id)
            ->join('proyectos', 'proyectos.Pro_ID', 'tipo_orden.proyecto_id')
            ->first();
        $materiales_solicitud = $this->tabla_verificacion_orden($orden->id, $request->id_materiales);
        $sub_order_pco = DB::table('pedidos')
            ->join('proyectos', 'proyectos.Pro_ID', 'pedidos.Pro_ID')
            ->where('proyectos.Pro_ID', $orden->proyecto_id)
            ->count() + 1;
        $orden->pco = "$orden->Codigo-$sub_order_pco";
        $orden->po_corr = $sub_order_pco;
        $to = $this->proveedores($orden->proyecto_id);
        $status = DB::table('tipo_orden_estatus')->where('estado', 1)->orderBy('nombre', 'ASC')->get();
        return response()->json([
            "status" => "ok",
            "orden" => $orden,
            "materiales" => $materiales_solicitud,
            "from" => $to,
            "to" => $to,
            "status" => $status,
            "message" => 'verificado',
        ], 200);
    }
    public function store_sub_orden(Request $request)
    {
        $rules = array(
            'new_to_vendor' => 'required',
            'new_materiales_id' => 'required',
            'new_orden_proyecto_id' => 'required',
            'new_orden_id' => 'required',
            'new_proveedor_status' => 'required',
            'new_tipo_orden_materiales' => 'required',
            'new_cantidad_ordenada' => 'required',
            'new_from_vendedor' => 'required',
            'new_pco_vendor' => 'required',
            'new_pco_corr' => 'required',
            'new_fecha_entrega_vendor' => 'nullable',
            'new_fecha_segimiento_vendor' => 'required',
            'new_nota_vendor' => 'required',
        );
        $messages = [
            'new_to_vendor.required' => "The To field is required",
            'new_proveedor_status.required' => "The status field is required",
            'new_orden_proyecto_id.required' => "The proyecto_id field is required",
            'new_tipo_orden_materiales.required' => 'The select materials is required',
            'new_cantidad_ordenada.required' => 'The select materials is required',
            'new_from_vendedor.required' => 'The Vendor is required',
            'new_pco_vendor.required' => 'The pco is required',
            'new_fecha_entrega_vendor.required' => 'The Requested delivery date is required',
            'new_fecha_segimiento_vendor.required' => 'The Tracking date is required',
            'new_nota_vendor.required' => 'The Note is required',
        ];
        $error = Validator::make($request->all(), $rules, $messages);
        if ($error->errors()->all()) {
            return response()->json([
                "status" => "errors",
                "message" => $error->errors()->all()]);
        }

        $orden_pedido = DB::table('pedidos')
            ->insertGetId([
                "Pro_ID" => $request->new_orden_proyecto_id,
                "Ven_ID" => $request->new_from_vendedor,
                "To_ID" => $request->new_to_vendor,
                "Fecha" => date('Y-m-d H:i:s', strtotime($request->new_fecha_entrega_vendor)),
                "OperatorID" => 1,
                "Note" => $request->new_nota_vendor,
                "status_id" => $request->new_proveedor_status,
                "PO" => $request->new_pco_vendor,
                "PO_Corr" => $request->new_pco_corr,
                "tipo_orden_id" => $request->new_orden_id,
            ]);
        /* fin verificar */
        foreach ($request->new_tipo_orden_materiales as $key => $value) {
            $solicitud_orden_vendor = DB::table('pedidos_material')
                ->insertGetId([
                    "Ped_ID" => $orden_pedido,
                    "Mat_ID" => $request->new_materiales_id[$key],
                    "Aux1" => $request->new_materiales_nota[$key],
                    "Cantidad" => $request->new_cantidad_ordenada[$key],
                ]);
            /*reducciones de cantidad */
            $cantidad = DB::table('tipo_orden_materiales')
                ->select(
                    'tipo_orden_materiales.*'
                )
                ->where('tipo_orden_materiales.id', $request->new_tipo_orden_materiales[$key])
                ->first();
            if ($cantidad->cant_ordenada > 0) {
                $material_ordenado = DB::table('tipo_orden_materiales')
                    ->where('tipo_orden_materiales.id', $request->new_tipo_orden_materiales[$key])
                    ->update([
                        "cant_ordenada" => ($cantidad->cant_ordenada - $request->new_cantidad_ordenada[$key]),
                    ]);
            }
            /*fin */
        }
        /* verificar sub order */
        $sub_orden = DB::table('pedidos')
            ->where('pedidos.Ped_ID', $orden_pedido)
            ->update([
                'status_id' => 3,
            ]);
        /* if ($this->verificar_cantidad_ordenada_requerida($request->new_orden_id)) {
        $sub_orden = DB::table('pedidos')
        ->where('pedidos.Ped_ID', $orden_pedido)
        ->update([
        'status_id' => 3,
        ]);
        } else {
        $sub_orden = DB::table('pedidos')
        ->where('pedidos.Ped_ID', $orden_pedido)
        ->update([
        'status_id' => 13,
        ]);
        } */
        /*insertando movimiento*/
        $movimiento_orden = DB::table('tipo_movimiento_pedido')
            ->insert([
                "Ped_ID" => $orden_pedido,
                "fecha" => date('Y-m-d H:i:s', strtotime($request->new_fecha_entrega_vendor)),
                "fecha_espera" => date('Y-m-d H:i:s', strtotime($request->new_fecha_segimiento_vendor)),
                "nota" => $request->new_nota_vendor,
            ]);
        $this->update_status_orden_automatico($request->new_orden_id);
        return response()->json([
            "status" => "ok",
            "message" => 'Registered Successfully',
        ], 200);
    }
    /* verificando si es request deliver */
    private function create_envio(Request $request, $orden_pedido_id)
    {
        $orden_envio = DB::table('tipo_tranferencia_envio')
            ->insertGetId([
                //"sub_empleoye_id" => $request->new_delivery_sub_employee,
                "fecha_actividad" => date('Y-m-d H:i:s', strtotime($request->edit_fecha_segimiento_vendor)),
                //"nota" => $request->new_delivery_nota,
                "estatus_id" => $request->edit_proveedor_status,
                "pedido_id" => $orden_pedido_id,
                "estado" => 1,
            ]);
    }
    private function update_envio(Request $request, $orden_pedido_id)
    {
        if ($request->edit_proveedor_status == 7 || $request->edit_proveedor_status == 12) {
            /* validar */
            $verificar = DB::table('tipo_tranferencia_envio')
                ->where('tipo_tranferencia_envio.pedido_id', $request->edit_pedido_id)
                ->first();
            if ($verificar) {
                $orden_envio_delivery = DB::table('tipo_tranferencia_envio')
                    ->where('tipo_tranferencia_envio.id', $verificar->id)
                    ->where('tipo_tranferencia_envio.pedido_id', $request->edit_pedido_id)
                    ->update([
                        //"sub_empleoye_id" => $request->new_delivery_sub_employee,
                        "fecha_actividad" => date('Y-m-d H:i:s', strtotime($request->edit_fecha_segimiento_vendor)),
                        //"nota" => $request->new_delivery_nota,
                        "estatus_id" => $request->edit_proveedor_status,
                        "pedido_id" => $orden_pedido_id,
                        "estado" => 1,
                    ]);
            } else {
                $this->create_envio($request, $orden_pedido_id);
            }
        } else {
            $this->delete_delivery($request->edit_pedido_id);
        }
        /* fin de modificar delivery si exite */
    }
    public function edit_sub_orden($id)
    {
        $sub_orden = DB::table('pedidos')
            ->select(
                'pedidos.Ped_ID',
                'pedidos.Pro_ID',
                'pedidos.Ven_ID',
                'pedidos.Fecha',
                'pedidos.Note',
                'pedidos.PO',
                'pedidos.PO_Corr',
                'pedidos.To_ID',
                'tipo_orden_estatus.nombre as nombre_status',
                'tipo_orden_estatus.id as status_id',
                'tipo_orden.nombre_trabajo as nombre_trabajo',
                'tipo_orden.num as num',
                'tipo_orden.id as tipo_orden_id',
                'tipo_orden.proyecto_id'
            )
            ->where('pedidos.Ped_ID', $id)
            ->join('tipo_orden_estatus', 'tipo_orden_estatus.id', 'pedidos.status_id')
            ->join('tipo_orden', 'tipo_orden.id', 'pedidos.tipo_orden_id')
            ->first();

        $ultimo_movimiento = DB::table('tipo_movimiento_pedido')
            ->select(
                DB::raw('DATE_FORMAT(tipo_movimiento_pedido.fecha , "%m/%d/%Y %H:%i:%s") as fecha'),
                DB::raw('DATE_FORMAT(tipo_movimiento_pedido.fecha_espera , "%m/%d/%Y %H:%i:%s") as fecha_espera'),
                'tipo_movimiento_pedido.nota'
            )
            ->join('pedidos', 'pedidos.Ped_ID', 'tipo_movimiento_pedido.Ped_ID')
            ->where('pedidos.Ped_ID', $sub_orden->Ped_ID)
            ->orderBy('tipo_movimiento_pedido.id', 'DESC')
            ->first();
        $to = $this->proveedores($sub_orden->proyecto_id);
        $materiales = DB::table('pedidos_material')
            ->select(
                'materiales.Denominacion',
                'materiales.Unidad_Medida',
                'materiales.Mat_ID as material_id',
                'materiales.Cat_ID',
                'pedidos_material.Cantidad',
                'pedidos_material.Aux1',
                'pedidos_material.Ped_Mat_ID'
            )
            ->join('materiales', 'materiales.Mat_ID', 'pedidos_material.Mat_ID')
            ->where('pedidos_material.Ped_ID', $sub_orden->Ped_ID)
            ->get();

        $status = DB::table('tipo_orden_estatus')->where('estado', 1)->orderBy('nombre', 'ASC')->get();
        foreach ($materiales as $key => $material) {
            $tipo_orden_material = DB::table('tipo_orden_materiales')
                ->select(
                    'tipo_orden_materiales.cant_ordenada',
                    'tipo_orden_materiales.cant_registrada',
                    'tipo_orden_materiales.id'
                )
                ->where('tipo_orden_materiales.material_id', $material->material_id)
                ->where('tipo_orden_materiales.tipo_orden_id', $sub_orden->tipo_orden_id)
                ->orWhere('tipo_orden_materiales.nota_material', $material->Aux1)
                ->first();
            //dd($tipo_orden_material);
            $materiales[$key]->cant_registrada = $tipo_orden_material->cant_registrada;
            $materiales[$key]->cant_ordenada = $tipo_orden_material->cant_ordenada;
            $materiales[$key]->id = $tipo_orden_material->id;

            //si aux es null
            $materiales[$key]->Denominacion = "$material->Denominacion" . " / " . ($material->Aux1 == 'null' ? '' : $material->Aux1);
            $materiales[$key]->total_proyecto = $this->total_proyecto($material->Cat_ID, $material->material_id, $sub_orden->proyecto_id);
            $materiales[$key]->total_proveedor = $this->total_proveedor($sub_orden->proyecto_id, $material->material_id);
            $materiales[$key]->total_ordenado = $this->total_ordenado($material->material_id);
            $materiales[$key]->total_usado = $this->total_usado($material->material_id);
            $materiales[$key]->total_warehouse = $this->total_warehouse($material->material_id, $sub_orden->proyecto_id, $material->Cat_ID);
            $materiales[$key] = $material;
        }
        /* verificando si hay deliver */
        $verificando_deliver = DB::table('tipo_tranferencia_envio')
            ->select(
                'tipo_tranferencia_envio.*',
                DB::raw("CONCAT(COALESCE(personal.Nombre,''), ' ',  COALESCE(personal.Apellido_Paterno,''), ' ',  COALESCE(personal.Apellido_Materno)) as nombre_delivery")
            )
            ->where('tipo_tranferencia_envio.pedido_id', $sub_orden->Ped_ID)
            ->join('personal', 'personal.Empleado_ID', 'tipo_tranferencia_envio.sub_empleoye_id')
            ->first();
        if (is_null($verificando_deliver)) {
            $verificando_deliver = new stdClass();
            $verificando_deliver->delivery = false;
        } else {
            $verificando_deliver->delivery = true;
        }
        /* verificando si hay deliver */
        return response()->json([
            "sub_orden" => $sub_orden,
            "from" => $to,
            "to" => $to,
            "status" => $status,
            "delivery" => $verificando_deliver,
            "materiales" => $materiales,
            "ultimo_movimiento" => $ultimo_movimiento,
        ], 200);
    }
    public function update_sub_orden(Request $request, $id)
    {
        $rules = array(
            'edit_materiales_pedido' => 'required',
            'edit_to_vendor' => 'required',
            'edit_materiales_id' => 'required',
            'edit_orden_proyecto_id' => 'required',
            'edit_orden_id' => 'required',
            'edit_pedido_id' => 'required',
            'edit_proveedor_status' => 'required',
            'edit_tipo_orden_materiales' => 'required',
            'edit_cantidad_ordenada' => 'required',
            'edit_from_vendedor' => 'required',
            'edit_pco_vendor' => 'required',
            'edit_pco_corr' => 'required',
            'edit_fecha_entrega_vendor' => 'nullable',
            'edit_fecha_segimiento_vendor' => 'required',
            'edit_nota_vendor' => 'required',
        );
        $messages = [
            'edit_to_vendor.required' => "The To field is required",
            'edit_proveedor_status.required' => "The status field is required",
            'edit_orden_proyecto_id.required' => "The proyecto_id field is required",
            'edit_tipo_orden_materiales.required' => 'The select materials is required',
            'edit_cantidad_ordenada.required' => 'The select materials is required',
            'edit_from_vendedor.required' => 'The Vendor is required',
            'edit_pco_vendor.required' => 'The pco is required',
            'edit_fecha_entrega_vendor.required' => 'The Requested delivery date is required',
            'edit_fecha_segimiento_vendor.required' => 'The Tracking date is required',
            'edit_nota_vendor.required' => 'The Note is required',
        ];
        $error = Validator::make($request->all(), $rules, $messages);
        if ($error->errors()->all()) {
            return response()->json([
                "status" => "errors",
                "message" => $error->errors()->all()]);
        }
        /*fin */
        $verificar = DB::table('pedidos_material')
            ->select('tipo_movimiento_material_pedido.*')
            ->join('tipo_movimiento_material_pedido', 'tipo_movimiento_material_pedido.Ped_Mat_ID', 'pedidos_material.Ped_Mat_ID')
            ->where('pedidos_material.Ped_ID', $request->edit_pedido_id)
            ->get()->toArray();

        /*  validar si es posible un update */
        if (count($verificar) <= 0) {
            $orden_pedido = DB::table('pedidos')
                ->where('pedidos.Ped_ID', $request->edit_pedido_id)
                ->update([
                    "Pro_ID" => $request->edit_orden_proyecto_id,
                    "Ven_ID" => $request->edit_from_vendedor,
                    "Fecha" => date('Y-m-d H:i:s', strtotime($request->edit_fecha_entrega_vendor)),
                    "OperatorID" => 1,
                    "To_ID" => $request->edit_to_vendor,
                    "Note" => $request->edit_nota_vendor,
                    "status_id" => $request->edit_proveedor_status,
                    "PO" => $request->edit_pco_vendor,
                    "tipo_orden_id" => $request->edit_orden_id,
                ]);
            /* añadir modificacion */
            $this->update_envio($request, $request->edit_pedido_id);
            /* modificando materiales */
            foreach ($request->edit_materiales_pedido as $key => $value) {
                $solicitud_orden_vendor = DB::table('pedidos_material')
                    ->where('pedidos_material.Ped_Mat_ID', $value)
                    ->update([
                        "Cantidad" => $request->edit_cantidad_ordenada[$key],
                    ]);
                $movimiento_orden = DB::table('tipo_movimiento_material_pedido')
                    ->where('tipo_movimiento_material_pedido.Ped_Mat_ID', $value)
                    ->update([
                        "estatus_id" => $request->edit_proveedor_status,
                        "fecha" => date('Y-m-d H:i:s', strtotime($request->edit_fecha_entrega_vendor)),
                        "fecha_espera" => date('Y-m-d H:i:s', strtotime($request->edit_fecha_segimiento_vendor)),
                        "nota" => $request->edit_nota_vendor,
                        "Pro_id_ubicacion" => $request->edit_to_vendor,
                    ]);
                /*reducciones de cantidad */
                $cantidad = DB::table('tipo_orden_materiales')
                    ->select(
                        'tipo_orden_materiales.*'
                    )
                    ->where('tipo_orden_materiales.id', $request->edit_tipo_orden_materiales[$key])
                    ->first();
                $material_ordenado = DB::table('tipo_orden_materiales')
                    ->where('tipo_orden_materiales.id', $request->edit_tipo_orden_materiales[$key])
                    ->update([
                        "cant_ordenada" => ($cantidad->cant_registrada - $request->edit_cantidad_ordenada[$key]),
                    ]);
                /*fin */
            }
            //$this->update_status_orden_automatico($request->edit_orden_id);
            return response()->json([
                "status" => "ok",
                "message" => 'Successfully modified',
            ], 200);
        } else {
            return response()->json([
                "status" => "errors",
                "message" => ['only the status will be modified', 'Cannot be modified there is a record of movements'],
            ], 200);
        }
    }
    /*delete movimiento delivery */
    private function delete_delivery($pedido_id)
    {
        $orden_envio = DB::table('tipo_tranferencia_envio')
            ->where('tipo_tranferencia_envio.pedido_id', $pedido_id)
            ->delete();
    }
    public function delete_sub_orden($id)
    {
        $sub_order = DB::table('pedidos')
            ->where('Ped_ID', $id)
            ->first();
        $verificar = DB::table('pedidos_material')
            ->select('tipo_movimiento_material_pedido.*')
            ->join('tipo_movimiento_material_pedido', 'tipo_movimiento_material_pedido.Ped_Mat_ID', 'pedidos_material.Ped_Mat_ID')
            ->where('pedidos_material.Ped_ID', $id)
            ->get()->toArray();

        if (count($verificar) <= 1) {
            $materiales = DB::table('pedidos_material')
                ->select(
                    'materiales.Denominacion',
                    'tipo_orden_materiales.id',
                    'tipo_orden_materiales.tipo_orden_id as order_id',
                    'tipo_orden_materiales.material_id',
                    'pedidos_material.Ped_ID',
                    'pedidos_material.Cantidad',
                    'tipo_orden_materiales.cant_registrada',
                    'tipo_orden_materiales.cant_ordenada',
                    'pedidos_material.Ped_Mat_ID'
                )
                ->join('materiales', 'materiales.Mat_ID', 'pedidos_material.Mat_ID')
                ->join('tipo_orden_materiales', 'tipo_orden_materiales.material_id', 'materiales.Mat_ID')
                ->where('pedidos_material.Ped_ID', $id)
                ->where('tipo_orden_materiales.tipo_orden_id', $sub_order->tipo_orden_id)
                ->get();
            //busqueda de informacion
            if ($this->verificar_order_to_vendor($sub_order->Ven_ID, $sub_order->To_ID) == 'ingreso') {
                foreach ($materiales as $key => $material) {
                    /*restaurando cantidades *///analizar datos peddien a reversion de cantidad asignada
                    $material_ordenado = DB::table('tipo_orden_materiales')
                        ->where('tipo_orden_materiales.id', $material->id)
                        ->update([
                            "cant_ordenada" => ($material->cant_ordenada + $material->Cantidad),
                        ]);
                    $delete_material_pedido = DB::table('pedidos_material')
                        ->where('pedidos_material.Ped_Mat_ID', $material->Ped_Mat_ID)
                        ->delete();
                }
            }

            $this->delete_delivery($id);
            $delete_pedido = DB::table('pedidos')
                ->where('pedidos.Ped_ID', $id)
                ->delete();

            /* cambio de estatus a orden */
            if ($this->verificar_existe_sub_order($materiales[0]->order_id)) {
                $this->update_status_orden_automatico($materiales[0]->order_id);
            } else {
                $this->restaurar_order($materiales[0]->order_id);
            }
            return response()->json([
                "status" => "ok",
                "message" => 'Removed successfully',
            ], 200);
        } else {
            return response()->json([
                "status" => "error",
                "message" => 'Materials cannot be removed.',
            ], 200);
        }
    }
    private function verificar_order_to_vendor($from, $to)
    {
        /* caso uno de proveedor a wharehouse */
        if ($this->verificar_proveedor($from) && $this->verificar_wharehouse($to)) {
            return 'ingreso';
        }
        /* caso uno de proveedor a proyecto */
        if ($this->verificar_proveedor($from) && $this->verificar_proyecto($to)) {
            return 'ingreso';
        }
        /* caso uno de wharehouse a proyecto */
        if ($this->verificar_wharehouse($from) && $this->verificar_proyecto($to)) {
            return 'egreso ingreso';
        }
        /* caso uno de proyecto a wharehouse */
        if ($this->verificar_proyecto($from) && $this->verificar_wharehouse($to)) {
            return 'egreso ingreso';
        }
        /* caso uno de wharehouse a proveedor */
        if ($this->verificar_wharehouse($from) && $this->verificar_proveedor($to)) {
            return 'egreso';
        }
        /* caso uno de proyecto a proveedor */
        if ($this->verificar_proyecto($from) && $this->verificar_proveedor($to)) {
            return 'egreso';
        }
        /* caso uno de proyecto a proyecto */
        if ($this->verificar_proyecto($from) && $this->verificar_proyecto($to)) {
            return 'egreso ingreso';
        }
    }
    private function verificar_proveedor($from)
    {
        $verificar_proveeedor = false;
        $proveedores = DB::table('proyectos')
            ->select(
                'proyectos.Pro_ID'
            )
            ->where('proyectos.Emp_ID', 119)
            ->get();
        foreach ($proveedores as $key => $proveedor) {
            if ($proveedor->Pro_ID == $from) {
                $verificar_proveeedor = true;
                break;
            }
        }
        return $verificar_proveeedor;
    }
    private function verificar_wharehouse($from)
    {
        $verificar_wharehouse = false;
        if ($from == '1') {
            $verificar_wharehouse = true;
        }
        return $verificar_wharehouse;
    }
    private function verificar_proyecto($from)
    {
        $verificar_proyecto = false;
        $proveedores = DB::table('proyectos')
            ->select(
                'proyectos.Pro_ID'
            )
            ->where('proyectos.Emp_ID', '<>', 119)
            ->where('proyectos.Pro_ID', '<>', 1)
            ->get();
        foreach ($proveedores as $key => $proveedor) {
            if ($proveedor->Pro_ID == $from) {
                $verificar_proyecto = true;
                break;
            }
        }
        return $verificar_proyecto;
    }
    private function proveedores($proyecto_id)
    {
        $providers = DB::table('proyectos')
            ->select(
                'proyectos.Pro_ID'
            )
            ->where('proyectos.Emp_ID', 119)
            ->get();
        $data = [];
        foreach ($providers as $key => $value) {
            $data[] = $value->Pro_ID;
        }
        $data[] = $proyecto_id;
        $data[] = 1; //warehouse
        $to = DB::table('proyectos')
            ->select(
                'proyectos.Pro_ID as id',
                'proyectos.Nombre as nombre',
                'proyectos.Nombre as nombre_proyecto',
                'empresas.Codigo',
                DB::raw("CONCAT(proyectos.Calle,' ', proyectos.Numero,' ',proyectos.Ciudad,' ',proyectos.Estado,' ', proyectos.Zip_Code) as address")
            )
            ->whereIn('proyectos.Pro_ID', $data)
            ->join('empresas', 'empresas.Emp_ID', 'proyectos.Emp_ID')
            ->get();
        return $to;
    }
    private function tabla_verificacion_orden($id, $id_materiales)
    {
        $materiales = DB::table('tipo_orden_materiales')
            ->select(
                'tipo_orden_materiales.id',
                'materiales.Denominacion',
                'materiales.Unidad_Medida',
                'tipo_orden_materiales.cant_ordenada',
                'tipo_orden_materiales.nota_material',
                'tipo_orden_materiales.cant_registrada',
                'tipo_orden_materiales.tipo_orden_id',
                'materiales.Mat_Id as material_id',
                'materiales.Cat_ID',
                'tipo_orden.proyecto_id'
            )
            ->where('tipo_orden_materiales.tipo_orden_id', $id)
            ->whereIn('tipo_orden_materiales.id', $id_materiales)
            ->join('materiales', 'materiales.Mat_Id', 'tipo_orden_materiales.material_id')
            ->join('tipo_orden', 'tipo_orden.id', 'tipo_orden_materiales.tipo_orden_id')
            ->distinct()
            ->get()->toArray();
        $resultado = [];
        foreach ($materiales as $key => $data) {
            $rest_materiales = new stdClass();
            $rest_materiales->id = $data->id;
            $rest_materiales->nota_material = $data->nota_material;
            $rest_materiales->Denominacion = "$data->Denominacion / $data->nota_material";
            $rest_materiales->Unidad_Medida = $data->Unidad_Medida;
            $rest_materiales->cant_ordenada = $data->cant_ordenada;
            $rest_materiales->cant_registrada = $data->cant_registrada;
            $rest_materiales->tipo_orden_id = $data->tipo_orden_id;
            $rest_materiales->material_id = $data->material_id;
            $rest_materiales->proyecto_id = $data->proyecto_id;
            $rest_materiales->total_warehouse = $this->total_warehouse($data->material_id, $data->proyecto_id, $data->Cat_ID);
            $rest_materiales->total_proyecto = $this->total_proyecto($data->Cat_ID, $data->material_id, $data->proyecto_id);
            $rest_materiales->total_proveedor = $this->total_proveedor($data->proyecto_id, $data->material_id);
            $rest_materiales->total_estimado = $this->total_estimado($data->proyecto_id);
            $rest_materiales->total_recibido = $this->total_recibido($data->material_id);
            $rest_materiales->total_ordenado = $this->total_ordenado($data->material_id);
            $rest_materiales->total_usado = $this->total_usado($data->material_id);
            $rest_materiales->status_orden = $this->status_orden($data->material_id);
            $resultado[] = $rest_materiales;
        }
        return $resultado;
    }
    /*data  materiales */
    private function total_warehouse($material_id, $proyecto_id, $categoria_material)
    {
        if ($categoria_material == 8) {
            $equipo = true;
        } else {
            $equipo = false;
        }
        $verificar_warehouse = DB::table('tipo_movimiento_material_pedido')
            ->select('tipo_movimiento_material_pedido.*')
            ->join('pedidos_material', 'pedidos_material.Ped_Mat_ID', 'tipo_movimiento_material_pedido.Ped_Mat_ID')
            ->join('pedidos', 'pedidos.Ped_ID', 'pedidos_material.Ped_ID')
            ->when($equipo, function ($q) use ($proyecto_id) {
                return $q->where('pedidos.Pro_ID', $proyecto_id);
            })
            ->where('pedidos_material.Mat_ID', $material_id)
            ->where('tipo_movimiento_material_pedido.Pro_id_ubicacion', 1)
            ->get();
        $total_warehouse = 0;
        foreach ($verificar_warehouse as $key => $warehouse) {
            $total_warehouse += $warehouse->ingreso - $warehouse->egreso;
        }
        return $total_warehouse;
    }
    private function total_proyecto($categoria_material, $material_id, $proyecto_id)
    {
        if ($categoria_material == 8) {
            $equipo = true;
        } else {
            $equipo = false;
        }
        $verificar_proyecto = DB::table('tipo_movimiento_material_pedido')
            ->select('tipo_movimiento_material_pedido.*')
            ->join('pedidos_material', 'pedidos_material.Ped_Mat_ID', 'tipo_movimiento_material_pedido.Ped_Mat_ID')
            ->join('pedidos', 'pedidos.Ped_ID', 'pedidos_material.Ped_ID')
            ->when($equipo, function ($q) use ($proyecto_id) {
                return $q->where('pedidos.Pro_ID', $proyecto_id);
            })
        //->where('pedidos.Pro_ID', $proyecto_id)
            ->where('pedidos_material.Mat_ID', $material_id)
            ->where('tipo_movimiento_material_pedido.Pro_id_ubicacion', $proyecto_id)
            ->get();
        $total_proyecto = 0;
        foreach ($verificar_proyecto as $key => $proyecto) {
            $total_proyecto += $proyecto->ingreso - $proyecto->egreso;
        }
        return $total_proyecto;
    }
    private function total_proveedor($proyecto_id, $material_id)
    {
        $proveedores = DB::table('proyectos')
            ->where('proyectos.Emp_ID', 119)
            ->orderBy('Nombre', 'DESC')
            ->get();
        $total_proyecto = "";
        foreach ($proveedores as $key => $value) {
            $verificar_proveedores = DB::table('tipo_movimiento_material_pedido')
                ->select(
                    'tipo_movimiento_material_pedido.*',
                    'proyectos.Nombre'
                )
                ->join('pedidos_material', 'pedidos_material.Ped_Mat_ID', 'tipo_movimiento_material_pedido.Ped_Mat_ID')
                ->join('pedidos', 'pedidos.Ped_ID', 'pedidos_material.Ped_ID')
                ->join('proyectos', 'proyectos.Pro_ID', 'tipo_movimiento_material_pedido.Pro_id_ubicacion')
                ->where('pedidos.Pro_ID', $proyecto_id)
                ->where('pedidos_material.Mat_ID', $material_id)
                ->where('tipo_movimiento_material_pedido.Pro_id_ubicacion', $value->Pro_ID)
                ->get();
            $total_proveedor = 0;
            foreach ($verificar_proveedores as $key => $proveedor) {
                $total_proveedor += ($proveedor->ingreso - $proveedor->egreso);
            }
            /*  validar si es 0 no mostrar*/
            if ($total_proveedor > 0) {
                $total_proyecto .= $total_proveedor . "  " . $value->Nombre . "<br>";
            }
        }
        return $total_proyecto;
    }
    private function total_ordenado($material_id)
    {
        $verificar_cantidad_ordenada = DB::table('pedidos_material')
            ->selectRaw("SUM(pedidos_material.Cantidad) AS cantidad_estimada")
            ->where('pedidos_material.Mat_ID', $material_id)
            ->get();
        $resultado = 0;
        foreach ($verificar_cantidad_ordenada as $key => $cantidad) {
            $resultado += $cantidad->cantidad_estimada;
        }
        return $resultado;
    }
    private function total_estimado($proyecto_id)
    {
        $cantidad_estimada = DB::table('materiales')
            ->select('materiales.*')
            ->where('materiales.Mat_ID', $proyecto_id)
            ->get();
        $resultado = 0;
        foreach ($cantidad_estimada as $key => $estimado) {
            $resultado += $estimado->Cantidad;
        }
        return $resultado;
    }
    private function total_recibido($material_id)
    {
        $verificar_cantidad_recibida = DB::table('pedidos_material')
            ->selectRaw("SUM(pedidos_material.Cantidad_Recibida) AS Cantidad_Recibida")
            ->where('pedidos_material.Mat_ID', $material_id)
            ->get();
        $resultado = 0;
        foreach ($verificar_cantidad_recibida as $key => $cantidad) {
            $resultado += $cantidad->Cantidad_Recibida;
        }
        return $resultado;
    }
    private function total_usado($material_id)
    {
        $verificar_cantidad_usada = DB::table('pedidos_material')
            ->selectRaw("SUM(pedidos_material.Cantidad_Usada) AS Cantidad_Usada")
            ->where('pedidos_material.Mat_ID', $material_id)
            ->get();
        $resultado = 0;
        foreach ($verificar_cantidad_usada as $key => $cantidad) {
            $resultado += $cantidad->Cantidad_Usada;
        }
        return $resultado;
    }
    private function status_orden($material_id)
    {
        $verificar_cantidad_ordenada = DB::table('tipo_orden_materiales')
            ->select(
                'tipo_orden_materiales.*'
            )
            ->join('tipo_orden', 'tipo_orden.id', 'tipo_orden_materiales.tipo_orden_id')
            ->join('materiales', 'materiales.Mat_ID', 'tipo_orden_materiales.material_id')
            ->join('pedidos_material', 'pedidos_material.Mat_ID', 'materiales.Mat_ID')
            ->join('tipo_movimiento_material_pedido', 'tipo_movimiento_material_pedido.Ped_Mat_ID', 'pedidos_material.Ped_Mat_ID')
            ->join('pedidos', 'pedidos.Ped_ID', 'pedidos_material.Ped_ID')
            ->where('materiales.Mat_ID', $material_id)
            ->where('tipo_movimiento_material_pedido.estatus_id', 3)
            ->get()->toArray();
        if (empty($verificar_cantidad_ordenada)) {
            return false;
            //"<h5><span class='badge badge-danger'>no ordenado</span></h5>";
        } else {
            return false;
            //"<h5><span class='badge badge-success'>ordenado</span></h5>";
        }
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
