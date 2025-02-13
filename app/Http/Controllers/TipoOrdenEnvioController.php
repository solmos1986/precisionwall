<?php

namespace App\Http\Controllers;

use DataTables;
use DB;
use Illuminate\Http\Request;
use Validator;
use \stdClass;

class TipoOrdenEnvioController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function create_tranferencia(Request $request, $id)
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
    /* verificando si es request deliver */
    private function create_envio(Request $request, $orden_pedido_id)
    {
        $orden_envio = DB::table('tipo_tranferencia_envio')
            ->insertGetId([
                //"sub_empleoye_id" => $request->new_delivery_sub_employee,
                "fecha_actividad" => date('Y-m-d H:i:s', strtotime($request->transferencia_fecha_segimiento_vendor)),
                //"nota" => $request->new_delivery_nota,
                "estatus_id" => $request->transferencia_proveedor_status,
                "pedido_id" => $orden_pedido_id,
                "estado" => 1,
            ]);
    }
    /* actualizacion de status */
    /*   private function update_status_orden_automatico($order_id)
    {
    if ($this->verificar_cantidad_ordenada_requerida($order_id)) {
    $orden = DB::table('tipo_orden')
    ->where('tipo_orden.id', $order_id)
    ->update([
    'estatus_id' => 3,
    ]);
    } else {
    $orden = DB::table('tipo_orden')
    ->where('tipo_orden.id', $order_id)
    ->update([
    'estatus_id' => 13,
    ]);
    }
    } */
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
    public function store_tranferencia_envio(Request $request)
    {
        $rules = array(
            'transferencia_to_vendor' => 'required',
            'transferencia_materiales_id' => 'required',
            'transferencia_orden_proyecto_id' => 'required',
            'transferencia_orden_id' => 'required',
            'transferencia_proveedor_status' => 'required',
            'transferencia_tipo_orden_materiales' => 'required',
            'transferencia_cantidad_ordenada' => 'required',
            'transferencia_from_vendedor' => 'required',
            'transferencia_pco_vendor' => 'required',
            'transferencia_pco_corr' => 'required',
            'transferencia_fecha_entrega_vendor' => 'nullable',
            'transferencia_fecha_segimiento_vendor' => 'required',
            'transferencia_nota_vendor' => 'required',
        );
        $messages = [
            'transferencia_to_vendor.required' => "The To field is required",
            'transferencia_proveedor_status.required' => "The status field is required",
            'transferencia_orden_proyecto_id.required' => "The proyecto_id field is required",
            'transferencia_tipo_orden_materiales.required' => 'The select materials is required',
            'transferencia_cantidad_ordenada.required' => 'The select materials is required',
            'transferencia_from_vendedor.required' => 'The Vendor is required',
            'transferencia_pco_vendor.required' => 'The pco is required',
            'transferencia_fecha_entrega_vendor.required' => 'The Requested delivery date is required',
            'transferencia_fecha_segimiento_vendor.required' => 'The Tracking date is required',
            'transferencia_nota_vendor.required' => 'The Note is required',
        ];
        $error = Validator::make($request->all(), $rules, $messages);
        if ($error->errors()->all()) {
            return response()->json([
                "status" => "errors",
                "message" => $error->errors()->all()]);
        }

        $orden_pedido = DB::table('pedidos')
            ->insertGetId([
                "Pro_ID" => $request->transferencia_orden_proyecto_id,
                "Ven_ID" => $request->transferencia_from_vendedor,
                "To_ID" => $request->transferencia_to_vendor,
                "Fecha" => date('Y-m-d H:i:s', strtotime($request->transferencia_fecha_entrega_vendor)),
                "OperatorID" => 1,
                "Note" => $request->transferencia_nota_vendor,
                "status_id" => $request->transferencia_proveedor_status,
                "PO" => $request->transferencia_pco_vendor,
                "PO_Corr" => $request->transferencia_pco_corr,
                "tipo_orden_id" => $request->transferencia_orden_id,
            ]);
        /* verificar si es deliver requset */
        if ($request->transferencia_proveedor_status == 7 || $request->transferencia_proveedor_status == 12) {
            $this->create_envio($request, $orden_pedido);
        }
        /* fin verificar */
        foreach ($request->transferencia_tipo_orden_materiales as $key => $value) {
            $solicitud_orden_vendor = DB::table('pedidos_material')
                ->insertGetId([
                    "Ped_ID" => $orden_pedido,
                    "Mat_ID" => $request->transferencia_materiales_id[$key],
                    "Aux1" => $request->transferencia_materiales_nota[$key],
                    "Cantidad" => $request->transferencia_cantidad_ordenada[$key],
                ]);
            /*reducciones de cantidad */
            $cantidad = DB::table('tipo_orden_materiales')
                ->select(
                    'tipo_orden_materiales.*'
                )
                ->where('tipo_orden_materiales.id', $request->transferencia_tipo_orden_materiales[$key])
                ->first();
            if ($cantidad->cant_ordenada > 0) {
                $material_ordenado = DB::table('tipo_orden_materiales')
                    ->where('tipo_orden_materiales.id', $request->transferencia_tipo_orden_materiales[$key])
                    ->update([
                        "cant_ordenada" => ($cantidad->cant_ordenada - $request->transferencia_cantidad_ordenada[$key]),
                    ]);
            }
            /*fin */
        }
        /*insertando movimiento*/
        $movimiento_orden = DB::table('tipo_movimiento_pedido')
            ->insert([
                "Ped_ID" => $orden_pedido,
                "fecha" => date('Y-m-d H:i:s', strtotime($request->transferencia_fecha_entrega_vendor)),
                "fecha_espera" => date('Y-m-d H:i:s', strtotime($request->transferencia_fecha_segimiento_vendor)),
                "nota" => $request->transferencia_nota_vendor,
            ]);
        $this->update_status_orden_automatico($request->transferencia_orden_id);
        return response()->json([
            "status" => "ok",
            "message" => 'Registered Successfully',
        ], 200);
    }
    public function update_deliver(Request $request, $id)
    {
        $rules = array(
            'tipo_asignar_envio_id' => 'required',
            'asignar_delivery_sub_employee' => 'required',
            'asignar_delivery_status' => 'required',
            'asignar_delivery_nota' => 'nullable',
        );
        $messages = [
            'asignar_delivery_sub_employee.required' => "The Sub empleoye field is required",
            'asignar_delivery_status.required' => "The Status delivery field is required",
            'asignar_delivery_nota.required' => "The Note delivery field is required",
        ];
        $error = Validator::make($request->all(), $rules, $messages);
        if ($error->errors()->all()) {
            return response()->json([
                "status" => "errors",
                "message" => $error->errors()->all()]);
        }
        $orden_envio_delivery = DB::table('tipo_tranferencia_envio')
            ->where('tipo_tranferencia_envio.id', $request->tipo_asignar_envio_id)
            ->where('tipo_tranferencia_envio.pedido_id', $id)
            ->update([
                "sub_empleoye_id" => $request->asignar_delivery_sub_employee,
                "nota" => $request->asignar_delivery_nota,
                "estatus_id" => $request->asignar_delivery_status,
            ]);
        return response()->json([
            "status" => "ok",
            "data" => $orden_envio_delivery,
            "message" => 'Registered Successfully',
        ], 200);
    }
    /* PROVEEDORES */
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
    private function total_proyecto($material_id, $proyecto_id)
    {
        $verificar_proyecto = DB::table('tipo_movimiento_material_pedido')
            ->select('tipo_movimiento_material_pedido.*')
            ->join('pedidos_material', 'pedidos_material.Ped_Mat_ID', 'tipo_movimiento_material_pedido.Ped_Mat_ID')
            ->join('pedidos', 'pedidos.Ped_ID', 'pedidos_material.Ped_ID')
            ->where('pedidos.Pro_ID', $proyecto_id)
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
        $status = DB::table('tipo_orden_estatus')->where('estado', 1)->orderBy('nombre', 'ASC')->get();
        $proveedores = DB::table('proyectos')
            ->select(
                'proyectos.Pro_ID',
                'proyectos.Nombre'
            )
            ->where('proyectos.Emp_ID', 119)
            ->orderBy('Nombre', 'DESC')
            ->get();
        return view('panel.tipo_orden.delivery.list_admin', compact('status', 'proveedores'));
    }
    public function datatable(Request $request)
    {
        $status = explode(',', $request->query('status'));
        //dd($status);
        $order = $request->query('order');
        $pedido = DB::table('tipo_tranferencia_envio')
            ->select(
                'tipo_tranferencia_envio.*',
                DB::raw('DATE_FORMAT(tipo_tranferencia_envio.fecha_actividad , "%m/%d/%Y %H:%i:%s") as fecha_actividad'),
                DB::raw("CONCAT(proyectos.Calle,' ', proyectos.Numero,' ',proyectos.Ciudad,' ',proyectos.Estado,' ', proyectos.Zip_Code) as address"),
                DB::raw("CONCAT(COALESCE(personal.Nombre, ''), ' ', COALESCE(personal.Apellido_Paterno, ''), ' ', COALESCE(personal.Apellido_Materno, '')) as sub_empleoye"),
                'proyectos.Nombre as nombre_proyecto',
                'pedidos.PO',
                'tipo_tranferencia_envio.pedido_id',
                'tipo_orden_estatus.id as status_id',
                'tipo_orden_estatus.nombre as nombre_status',
                'orden_envio.id as envio_status_id',
                'orden_envio.nombre as envio_nombre_status',
                'orden_envio.color as envio_color',
                'tipo_orden_estatus.color'
            )
            ->join('pedidos', 'pedidos.Ped_ID', 'tipo_tranferencia_envio.pedido_id')
            ->join('tipo_orden', 'tipo_orden.id', 'pedidos.tipo_orden_id')
            ->leftJoin('personal', 'personal.Empleado_ID', 'tipo_tranferencia_envio.sub_empleoye_id')
            ->join('proyectos', 'proyectos.Pro_ID', 'tipo_orden.proyecto_id')
            ->join('tipo_orden_estatus', 'tipo_orden_estatus.id', 'pedidos.status_id')
            ->join('tipo_orden_estatus as orden_envio', 'orden_envio.id', 'tipo_tranferencia_envio.estatus_id')
            ->when($request->query('from_date'), function ($query) use ($request) {
                return $query->where('tipo_tranferencia_envio.fecha_actividad', '>=', date('Y-m-d H:i:s', strtotime($request->query('from_date'))))
                    ->where('tipo_tranferencia_envio.fecha_actividad', '<=', date('Y-m-d', strtotime($request->query('to_date'))) . '  23:59:59');
            })
            ->when($request->query('status'), function ($query) use ($status) {
                return $query->whereIn('tipo_tranferencia_envio.estatus_id', $status)
                    ->orderBy('tipo_tranferencia_envio.fecha_actividad', 'DESC');
            })
        /*  ->when($order[0]['column'] == '4', function ($query) {

        return $query->orderBy('tipo_tranferencia_envio.fecha_actividad', 'DESC');
        }) */
            ->orderBy('tipo_tranferencia_envio.id', 'DESC')
            ->get();

        return Datatables::of($pedido)
            ->addIndexColumn()
            ->addColumn('acciones', function ($data) {
                /* verfificacion de roles */
                $button = "
                <i class='fas fa-exchange-alt ms-text-secondary cursor-pointer asignar_deliver' data-pedido_id='$data->pedido_id'  title='Transfer'></i>";
                return $button;
            })
            ->addColumn('envio_status', function ($data) {
                return $html = "<h5><span class='badge badge-$data->envio_color'>$data->envio_nombre_status</span></h5>";
            })
            ->addColumn('status', function ($data) {
                return $html = "<h5><span class='badge badge-$data->color'>$data->nombre_status</span></h5>";
            })
            ->rawColumns(['acciones', 'status', 'envio_status'])
            ->toJson();
    }

    public function asignar_deliver($id)
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
            $materiales[$key]->total_proyecto = $this->total_proyecto($material->material_id, $sub_orden->proyecto_id);
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
                DB::raw("CONCAT( COALESCE( personal.Nombre, ' ', personal.Apellido_Paterno, ' ', personal.Apellido_Materno)) as nombre_delivery")
            )
            ->where('tipo_tranferencia_envio.pedido_id', $sub_orden->Ped_ID)
            ->join('personal', 'personal.Empleado_ID', 'tipo_tranferencia_envio.sub_empleoye_id')
            ->first();
        //dd($verificando_deliver, $sub_orden);
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
    public function update_tranferencia_envio(Request $request, $id)
    {
        $rules = array(
            'tipo_tranferencia_envio_id' => 'required',
            'transferencia_delivery_sub_employee' => 'required',
            'transferencia_delivery_status' => 'required',
            'transferencia_delivery_nota' => 'nullable',
        );
        $messages = [
            'transferencia_delivery_sub_employee.required' => "The Sub empleoye field is required",
            'transferencia_delivery_status.required' => "The Status delivery field is required",
            'transferencia_delivery_nota.required' => "The Note delivery field is required",
        ];
        $error = Validator::make($request->all(), $rules, $messages);
        if ($error->errors()->all()) {
            return response()->json([
                "status" => "errors",
                "message" => $error->errors()->all()]);
        }
        $orden_envio_delivery = DB::table('tipo_tranferencia_envio')
            ->where('tipo_tranferencia_envio.id', $request->tipo_tranferencia_envio_id)
            ->where('tipo_tranferencia_envio.pedido_id', $id)
            ->update([
                "sub_empleoye_id" => $request->transferencia_delivery_sub_employee,
                "nota" => $request->transferencia_delivery_nota,
                "estatus_id" => $request->transferencia_delivery_status,
            ]);
        return response()->json([
            "status" => "ok",
            "data" => $orden_envio_delivery,
            "message" => 'Registered Successfully',
        ], 200);
    }
    private function tabla_verificacion_orden($id, $id_materiales)
    {
        $materiales = DB::table('tipo_orden_materiales')
            ->select(
                'tipo_orden_materiales.id',
                'materiales.Denominacion',
                'materiales.Unidad_Medida',
                'materiales.Cat_ID',
                'tipo_orden_materiales.cant_ordenada',
                'tipo_orden_materiales.nota_material',
                'tipo_orden_materiales.cant_registrada',
                'tipo_orden_materiales.tipo_orden_id',
                'materiales.Mat_Id as material_id',
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
            $rest_materiales->total_proyecto = $this->total_proyecto($data->material_id, $data->proyecto_id);
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
}
