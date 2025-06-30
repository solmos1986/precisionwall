<?php

namespace App\Http\Controllers;

use App\Model\orden\TipoOrden;
use App\Personal;
use DataTables;
use DB;
use File;
use Illuminate\Http\Request;
use Image;
use Validator;
use \stdClass;

class TipoOrdenController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function get_deliverys(Request $request, $id)
    {
        if (!isset($request->searchTerm)) {
            $tipo_trabajo = Personal::selectRaw("Empleado_ID, CONCAT(COALESCE(personal.Nombre,''), ' ',  COALESCE(personal.Apellido_Paterno,''), ' ',  COALESCE(personal.Apellido_Materno,''), ' - ', COALESCE(empresas.Nombre,'')) as Foreman, personal.email")
                ->join('empresas', 'empresas.Emp_ID', 'personal.Emp_ID')
                ->where(function ($q) use ($id) {
                    $q->where('empresas.Emp_ID', $id)
                        ->Orwhere('empresas.Emp_ID', 6);
                })
                ->get();
        } else {
            $tipo_trabajo = Personal::selectRaw("Empleado_ID, CONCAT(COALESCE(personal.Nombre,''), ' ',  COALESCE(personal.Apellido_Paterno,''), ' ',  COALESCE(personal.Apellido_Materno,''), ' - ', COALESCE(empresas.Nombre,'')) as Foreman, personal.email")
                ->join('empresas', 'empresas.Emp_ID', 'personal.Emp_ID')
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
    public function index_delivery()
    {
        $status = DB::table('tipo_orden_estatus')
            ->whereIn('tipo_orden_estatus.id', [8, 7, 6, 12])
            ->get();
        return view('panel.tipo_orden.delivery.list', compact('status'));
    }
    public function list_delivery(Request $request)
    {
        $data_orden_actividad = DB::table('tipo_tranferencia_envio')
            ->select(
                'tipo_tranferencia_envio.*',
                'tipo_tranferencia_envio.estatus_id as estatus_id',
                'tipo_orden_estatus.nombre as nombre_estatus',
                'tipo_orden_estatus.color',
                DB::raw("CONCAT( proyectos.Estado,' ', proyectos.Ciudad,' ',proyectos.Calle,' ', proyectos.Numero) as address"),
                'proyectos.Nombre as proyecto',
                DB::raw('DATE_FORMAT(tipo_tranferencia_envio.fecha_actividad , "%m/%d/%Y %H:%i:%s") as fecha_actividad'),
                'tipo_orden.id as order_id',
                'tipo_orden.num',
                'tipo_orden.nombre_trabajo',
                'tipo_tranferencia_envio.firma_entrega',
                'tipo_tranferencia_envio.firma_foreman',
                'pedidos.po'
            )
            ->join('tipo_orden_estatus', 'tipo_orden_estatus.id', 'tipo_tranferencia_envio.estatus_id')
            ->join('pedidos', 'pedidos.Ped_ID', 'tipo_tranferencia_envio.pedido_id')
            ->join('proyectos', 'proyectos.Pro_ID', 'pedidos.Pro_ID')
            ->join('tipo_orden', 'tipo_orden.id', 'pedidos.tipo_orden_id')
            ->when($request->query('from_date'), function ($query) use ($request) {
                return $query->whereBetween(
                    'tipo_tranferencia_envio.fecha_actividad',
                    [
                        date('Y-m-d H:i:s', strtotime($request->query('from_date'))),
                        date('Y-m-d', strtotime($request->query('to_date'))) . ' 23:59:59',
                    ]);
            })
            ->when($request->query('status'), function ($query) use ($request) {
                return $query->where('tipo_tranferencia_envio.estatus_id', $request->query('status'));
            })
            ->where('tipo_tranferencia_envio.sub_empleoye_id', auth()->user()->Empleado_ID)
            ->orderBy('tipo_tranferencia_envio.id', 'DESC')
            ->get();
        $resultado = array();
        foreach ($data_orden_actividad as $key => $value) {
            /* status */
            $value->color = "<h5><span class='badge badge-$value->color'>$value->nombre_estatus</span></h5>";
            /* fecha */
            if ($value->fecha_actividad > $request->fecha) {
                $value->fecha_actividad = "<h5><span class='badge badge-success'>$value->fecha_actividad</span></h5>";
            } else {
                $value->fecha_actividad = "<h5><span class='badge badge-danger'>$value->fecha_actividad</span></h5>";
            }
            $resultado[] = $value;
        }
        return response()->json($resultado, 200);
    }
    public function show_modal_delivery($id)
    {
        $orden = DB::table('tipo_tranferencia_envio')
            ->select(
                DB::raw("DATE_FORMAT(tipo_tranferencia_envio.fecha_actividad, '%m/%d/%Y %H:%i:%s') as fecha_actividad"),
                DB::raw("CONCAT( proyectos.Estado,' ', proyectos.Ciudad,' ',proyectos.Calle,' ', proyectos.Numero) as address"),
                'proyectos.Nombre as proyecto',
                'tipo_orden_estatus.id as estatus_id',
                'tipo_orden.id as order_id',
                'tipo_orden.num',
                'tipo_orden.nombre_trabajo',
                'delivery.Nick_Name as nombre_delivery',
                'tipo_tranferencia_envio.firma_entrega',
                'tipo_tranferencia_envio.firma_foreman',
                'pedidos.PO as pco_pedido',
                'tipo_tranferencia_envio.pedido_id',
                'tipo_tranferencia_envio.id'
            )
            ->where('tipo_tranferencia_envio.sub_empleoye_id', auth()->user()->Empleado_ID)
            ->where('tipo_tranferencia_envio.pedido_id', $id)
            ->join('tipo_orden_estatus', 'tipo_orden_estatus.id', 'tipo_tranferencia_envio.estatus_id')
            ->join('pedidos', 'pedidos.Ped_ID', 'tipo_tranferencia_envio.pedido_id')
            ->join('proyectos', 'proyectos.Pro_ID', 'pedidos.Pro_ID')
            ->join('tipo_orden', 'tipo_orden.id', 'pedidos.tipo_orden_id')
            ->join('personal as delivery', 'delivery.Empleado_ID', 'tipo_tranferencia_envio.sub_empleoye_id')
            ->first();
        $materiales = DB::table('pedidos_material')
            ->select(
                'materiales.Denominacion',
                'materiales.Unidad_Medida',
                'pedidos_material.Cantidad as cant_ordenada'
            )
            ->where('pedidos.Ped_ID', $id)
            ->join('pedidos', 'pedidos.Ped_ID', 'pedidos_material.Ped_ID')
            ->join('materiales', 'materiales.Mat_ID', 'pedidos_material.Mat_ID')
            ->get();
        $orden->materiales = $materiales;
        return response()->json($orden, 200);
    }
    public function update_modal_delivery_express(Request $request, $id)
    {
        $envio = DB::table('tipo_tranferencia_envio')
            ->where('tipo_tranferencia_envio.id', $id)
            ->where('tipo_tranferencia_envio.sub_empleoye_id', auth()->user()->Empleado_ID)
            ->first();
        /* envio */
        $imagen = DB::table('tipo_tranferencia_envio')
            ->where('tipo_tranferencia_envio.id', $id)
            ->where('tipo_tranferencia_envio.sub_empleoye_id', auth()->user()->Empleado_ID)
            ->update([
                'fecha_entrega' => date('Y-m-d H:i:s', strtotime($request->fecha)),
                'fecha_foreman' => date('Y-m-d H:i:s', strtotime($request->fecha)),
                'estatus_id' => 6,
            ]);

        /* registrando movimientos */
        $material_pedidos = DB::table('pedidos_material')
            ->select(
                'pedidos_material.*',
                'pedidos.Ven_ID',
                'pedidos.Pro_ID',
                'pedidos.To_ID'
            )
            ->where('pedidos_material.Ped_ID', $envio->pedido_id)
            ->join('pedidos', 'pedidos.Ped_ID', 'pedidos_material.Ped_ID')
            ->get();
        foreach ($material_pedidos as $key => $value) {
            //egreso
            $movimiento = DB::table('tipo_movimiento_material_pedido')
                ->select(
                    DB::raw('max(nro_movimiento) as nro_movimiento')
                )
                ->where("Ped_Mat_ID", $value->Ped_Mat_ID)
                ->first();
            $movimiento_de = DB::table('tipo_movimiento_material_pedido')
                ->insertGetId([
                    "estatus_id" => 4,
                    "Ped_Mat_ID" => $value->Ped_Mat_ID,
                    "material_id" => $value->Mat_ID,
                    "fecha" => date('Y-m-d H:i:s', strtotime($request->fecha)),
                    "fecha_espera" => date('Y-m-d H:i:s', strtotime($request->fecha)),
                    "nota" => 'transferred materials',
                    "estado" => 1,
                    "egreso" => $value->Cantidad,
                    "ingreso" => 0,
                    "Pro_id_ubicacion" => $value->Ven_ID,
                    "nro_movimiento" =>($movimiento->nro_movimiento != null) ? $movimiento->nro_movimiento + 1 : 1
                ]);
            //ingreso
            $movimiento_a = DB::table('tipo_movimiento_material_pedido')
                ->insertGetId([
                    "estatus_id" => 4,
                    "Ped_Mat_ID" => $value->Ped_Mat_ID,
                    "material_id" => $value->Mat_ID,
                    "fecha" => date('Y-m-d H:i:s', strtotime($request->fecha)),
                    "fecha_espera" => date('Y-m-d H:i:s', strtotime($request->fecha)),
                    "nota" => 'materials received',
                    "estado" => 1,
                    "ingreso" => $value->Cantidad,
                    "egreso" => 0,
                    "Pro_id_ubicacion" => $value->To_ID,
                    "nro_movimiento" =>($movimiento->nro_movimiento != null) ? $movimiento->nro_movimiento + 1 : 1
                ]);

        }
        /*actualizacion update de envio de de materiales o pedido */
        $update_pedido = DB::table('pedidos')
            ->where('pedidos.Ped_ID', $envio->pedido_id)
            ->update([
                'status_id' => 14,
            ]);
        $obtener_orden = DB::table('pedidos')
            ->where('pedidos.Ped_ID', $envio->pedido_id)
            ->first();
        $this->update_status_orden_automatico($obtener_orden->tipo_orden_id);
        /*fin */
        return response()->json([
            "status" => "ok",
            "message" => 'Registered Successfully',
        ], 200);
    }
    public function update_modal_delivery(Request $request, $id)
    {
        $rules = array(
            'status' => 'required',
        );
        $messages = [
            'status.required' => "The status field is required",
        ];
        $error = Validator::make($request->all(), $rules, $messages);
        if ($error->errors()->all()) {
            return response()->json([
                'status' => 'errors',
                'message' => $error->errors()->all(),
            ]);
        }
        $envio = DB::table('tipo_tranferencia_envio')
            ->where('tipo_tranferencia_envio.id', $id)
            ->where('tipo_tranferencia_envio.sub_empleoye_id', auth()->user()->Empleado_ID)
            ->first();
            
        /*verificando firmas */
        $name_img_install = "";
        $name_img_foreman = "";
        if ($envio->firma_entrega == null && $envio->firma_foreman == null) {
            if ($request->signature_install) {
                $image_path = public_path() . "/signatures/install/$envio->firma_entrega";
                if (File::exists($image_path) && $envio->firma_entrega) {
                    File::delete($image_path);
                }
                $name_img_install = "signature-order-foreman-" . time() . ".jpg";
                $path = public_path() . "/signatures/install/$name_img_install";
                Image::make(file_get_contents($request->signature_install))->save($path);
            } else {
                $name_img_install = ($envio->firma_entrega) ? $envio->firma_entrega : null;
            }
            if ($request->signature_foreman) {
                $image_path = public_path() . "/signatures/install/$envio->firma_foreman";
                if (File::exists($image_path) && $envio->firma_foreman) {
                    File::delete($image_path);
                }
                $name_img_foreman = "signature-order-install-" . time() . ".jpg";
                $path = public_path() . '/signatures/install/' . $name_img_foreman;
                Image::make(file_get_contents($request->signature_foreman))->save($path);
            } else {
                $name_img_foreman = ($envio->firma_foreman) ? $envio->firma_foreman : null;
            }
            $imagen = DB::table('tipo_tranferencia_envio')
                ->where('tipo_tranferencia_envio.id', $id)
                ->where('tipo_tranferencia_envio.sub_empleoye_id', auth()->user()->Empleado_ID)
                ->update([
                    'firma_entrega' => $name_img_install,
                    'firma_foreman' => $name_img_foreman,
                    'fecha_entrega' => date('Y-m-d H:i:s', strtotime($request->fecha)),
                    'fecha_foreman' => date('Y-m-d H:i:s', strtotime($request->fecha)),
                    'estatus_id' => $request->status,
                ]);
        } else {

        }
        /*actualizacion update de envio de de materiales o pedido */
        $update_pedido = DB::table('pedidos')
            ->where('pedidos.Ped_ID', $envio->pedido_id)
            ->update([
                'status_id' => 14,
            ]);
        $obtener_orden = DB::table('pedidos')
            ->where('pedidos.Ped_ID', $envio->pedido_id)
            ->first();
        $this->update_status_orden_automatico($obtener_orden->tipo_orden_id);
        /*fin */
        return response()->json([
            "status" => "ok",
            "message" => 'Registered Successfully',
        ], 200);
    }
    public function list_delivery_save(Request $request, $id)
    {
        $imagen = DB::table('tipo_tranferencia_envio')
            ->where('tipo_tranferencia_envio.pedido_id', $id)
            ->where('tipo_tranferencia_envio.sub_empleoye_id', auth()->user()->Empleado_ID)
            ->update([
                'estatus_id' => 6,
            ]);
        $material_pedidos = DB::table('pedidos_material')
            ->select(
                'pedidos_material.*',
                'pedidos.Ven_ID',
                'pedidos.Pro_ID'
            )
            ->where('pedidos_material.Ped_ID', $id)
            ->join('pedidos', 'pedidos.Ped_ID', 'pedidos_material.Ped_ID')
            ->get();
        foreach ($material_pedidos as $key => $value) {
            //egreso
            $movimiento_de = DB::table('tipo_movimiento_material_pedido')
                ->insertGetId([
                    "estatus_id" => 6,
                    "Ped_Mat_ID" => $value->Ped_Mat_ID,
                    "material_id" => $value->Mat_ID,
                    "fecha" => date('Y-m-d H:i:s', strtotime($request->fecha)),
                    "fecha_espera" => date('Y-m-d H:i:s', strtotime($request->fecha)),
                    "nota" => 'transferred materials',
                    "estado" => 1,
                    "egreso" => $value->Cantidad,
                    "Pro_id_ubicacion" => $value->Ven_ID,
                ]);
            //ingreso
            $movimiento_a = DB::table('tipo_movimiento_material_pedido')
                ->insertGetId([
                    "estatus_id" => 6,
                    "Ped_Mat_ID" => $value->Ped_Mat_ID,
                    "material_id" => $value->Mat_ID,
                    "fecha" => date('Y-m-d H:i:s', strtotime($request->fecha)),
                    "fecha_espera" => date('Y-m-d H:i:s', strtotime($request->fecha)),
                    "nota" => 'materials received',
                    "estado" => 1,
                    "ingreso" => $value->Cantidad,
                    "Pro_id_ubicacion" => $value->Pro_ID,
                ]);
        }
        return response()->json([
            "status" => "ok",
            "message" => 'Registered Successfully',
        ], 200);
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
    /*fin funciones de verificacion */
    public function list_order()
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
        $vendors = DB::table('vendedor')
            ->orderBy('Nombre', 'DESC')
            ->get();
        return view('panel.tipo_orden.list', compact('status', 'proveedores', 'vendors'));
    }

    public function datatable_order(Request $request)
    {
        $status = explode(',', $request->query('status'));
        $data = TipoOrden::select(
            'tipo_orden.*',
            'tipo_orden_estatus.id as status_id',
            'tipo_orden_estatus.nombre as status',
            'tipo_orden_estatus.color',
            'personal.Usuario as username',
            'proyectos.Nombre as proyecto',
            DB::raw("CONCAT( proyectos.Estado,' ', proyectos.Ciudad,' ',proyectos.Calle,' ', proyectos.Numero) as address"),
            DB::raw("GROUP_CONCAT( pedidos.PO,',') as pedidos"),
            DB::raw("GROUP_CONCAT( estado_orden.nombre,',') as estado_pedidos"),
            DB::raw("GROUP_CONCAT( estado_orden.color,',') as estado_colores")
        )
            ->when($request->query('status'), function ($query) use ($status) {
                return $query->whereIn('tipo_orden.estatus_id', $status)
                    ->orderBy('tipo_orden.fecha_entrega', 'DESC');
            })
            ->where('eliminado', 0)
            ->where('tipo_orden.estado', 'creado')
            ->join('proyectos', 'proyectos.Pro_ID', 'tipo_orden.proyecto_id')
            ->join('personal', 'personal.Empleado_ID', 'tipo_orden.creado_por')
            ->join('tipo_orden_estatus', 'tipo_orden_estatus.id', 'tipo_orden.estatus_id')
            ->leftJoin('pedidos', 'pedidos.tipo_orden_id', 'tipo_orden.id')
            ->leftJoin('tipo_orden_estatus as estado_orden', 'estado_orden.id', 'pedidos.status_id')
            ->groupBy('tipo_orden.id')
            ->get();
        // dd($data );
        return Datatables::of($data)
            ->addIndexColumn()
            ->addColumn('acciones', function ($data) {
                /* verfificacion de roles */
                $acceso_total = "";
                if (!auth()->user()->verificarRol([7])) {
                    $acceso_total = "<i class='fas fa-pencil-alt ms-text-warning cursor-pointer edit_orden' data-id='$data->id'  title='Edit'></i>
                    <i class='far fa-trash-alt ms-text-danger cursor-pointer delete_orden' data-id='$data->id' title='Delete'></i>";
                }
                $button = "
                    <a href='#'><i class='fas fa-eye ms-text-primary show_orden'  title='View'></i></a>
                    $acceso_total
                    <a href='#'><i class='fas fa-file-download ms-text-success'></i></a>
                    ";
                return $button;
            })
            ->addColumn('po', function ($data) {
                $resultado = "";
                $ordenes = explode(',', $data->pedidos);
                $estado_ordenes = explode(',', $data->estado_pedidos);
                $estado_colores = explode(',', $data->estado_colores);
                foreach ($ordenes as $i => $orden) {
                    $resultado .= "<span class='badge badge-$estado_colores[$i] m-1'><p style='margin-bottom: 0rem; color:#ffffff'>$orden</p>$estado_ordenes[$i]</span>";
                }
                return $resultado;
            })
            ->addColumn('date_work', function ($data) {
                $data->fecha_entrega = date('Y-m-d H:i:s', strtotime($data->fecha_entrega));
                $fecha_actual = date('Y-m-d H:i:s');
                if ($fecha_actual >= $data->fecha_entrega && $data->estatus_id != 14 && $data->estatus_id != 4 && $data->estatus_id != 6) {
                    $html = "<h5><span class='badge badge-danger'>$data->fecha_entrega</span></h5>";
                } else {
                    if ($data->estatus_id == 14 || $data->estatus_id == 4 || $data->estatus_id == 6) {
                        $html = "<h5><span class='badge badge-success'>$data->fecha_entrega</span></h5>";
                    } else {
                        $html = "<h5><span class='badge badge-warning'>$data->fecha_entrega</span></h5>";
                    }
                }
                return $html;
            })
            ->addColumn('status', function ($data) {
                return $html = "
                    <h5><span class='badge badge-$data->color'>$data->status</span></h5>";
            })
            ->rawColumns(['acciones', 'date_work', 'status', 'po'])
            ->make(true);
    }

    public function datatable_order_materiales($id)
    {
        $materiales = DB::table('tipo_orden_materiales')
            ->select(
                'tipo_orden_materiales.id',
                'materiales.Denominacion',
                'materiales.Cat_ID',
                'tipo_orden_materiales.nota_material',
                'materiales.Unidad_Medida',
                'tipo_orden_materiales.cant_ordenada',
                'tipo_orden_materiales.cant_registrada',
                'tipo_orden_materiales.tipo_orden_id',
                'materiales.Mat_Id as material_id',
                'tipo_orden.proyecto_id'
            )
            ->where('tipo_orden_materiales.tipo_orden_id', $id)
            ->join('materiales', 'materiales.Mat_Id', 'tipo_orden_materiales.material_id')
            ->join('tipo_orden', 'tipo_orden.id', 'tipo_orden_materiales.tipo_orden_id')
            ->distinct()
            ->get();
        return Datatables::of($materiales)
            ->addIndexColumn()
            ->addColumn('Denominacion', function ($data) {
                return "$data->Denominacion / $data->nota_material";
            })
            ->addColumn('check', function ($data) {
                $button = "<input class='big-checkbox id_materiales' type='checkbox' value='$data->id' >";
                return $button;
            })
            ->addColumn('acciones', function ($data) {
                $button = "
                <i class='fas fa-envelope ms-text-secondary cursor-pointer view-mail' data-vendedor_id='$data->id' data-orden_id='$data->id' title='View email'></i>
                ";
                return $button;
            })
            ->addColumn('total_warehouse', function ($data) {
                if ($data->Cat_ID == 8) {
                    $equipo = true;
                } else {
                    $equipo = false;
                }
                $verificar_warehouse = DB::table('tipo_movimiento_material_pedido')
                    ->select('tipo_movimiento_material_pedido.*')
                    ->join('pedidos_material', 'pedidos_material.Ped_Mat_ID', 'tipo_movimiento_material_pedido.Ped_Mat_ID')
                    ->join('pedidos', 'pedidos.Ped_ID', 'pedidos_material.Ped_ID')
                    ->when($equipo, function ($q) use ($data) {
                        return $q->where('pedidos.Pro_ID', $data->proyecto_id);
                    })
                //->where('pedidos.Pro_ID', $data->proyecto_id)
                    ->where('pedidos_material.Mat_ID', $data->material_id)
                    ->where('tipo_movimiento_material_pedido.Pro_id_ubicacion', 1)
                    ->get();
                //dd($data->proyecto_id, $data->material_id);
                $total_warehouse = 0;
                foreach ($verificar_warehouse as $key => $warehouse) {
                    $total_warehouse += ($warehouse->ingreso - $warehouse->egreso);
                }
                return $total_warehouse;
            })
            ->addColumn('total_proyecto', function ($data) {
                $verificar_proyecto = DB::table('tipo_movimiento_material_pedido')
                    ->select('tipo_movimiento_material_pedido.*')
                    ->join('pedidos_material', 'pedidos_material.Ped_Mat_ID', 'tipo_movimiento_material_pedido.Ped_Mat_ID')
                    ->join('pedidos', 'pedidos.Ped_ID', 'pedidos_material.Ped_ID')
                //->where('pedidos.Pro_ID', $data->proyecto_id)
                    ->where('pedidos_material.Mat_ID', $data->material_id)
                    ->where('tipo_movimiento_material_pedido.Pro_id_ubicacion', $data->proyecto_id)
                    ->get();
                $total_proyecto = 0;
                foreach ($verificar_proyecto as $key => $proyecto) {
                    $total_proyecto += ($proyecto->ingreso - $proyecto->egreso);
                }
                return $total_proyecto;
            })
            ->addColumn('total_proveedor', function ($data) {
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
                        ->where('pedidos.Pro_ID', $data->proyecto_id)
                        ->where('pedidos_material.Mat_ID', $data->material_id)
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
            })
            ->addColumn('cantidad_ordenada', function ($data) {
                $verificar_cantidad_ordenada = DB::table('pedidos_material')
                    ->selectRaw("SUM(pedidos_material.Cantidad) AS cantidad_estimada")
                    ->join('pedidos', 'pedidos.Ped_ID', 'pedidos_material.Ped_ID')
                    ->where('pedidos_material.Mat_ID', $data->material_id)
                    ->where('pedidos.Pro_ID', $data->proyecto_id)
                    ->get();
                $resultado = 0;
                foreach ($verificar_cantidad_ordenada as $key => $cantidad) {
                    $resultado += $cantidad->cantidad_estimada;
                }
                return $resultado;
            })

            ->addColumn('cantidad_usada', function ($data) {
                $verificar_cantidad_usada = DB::table('pedidos_material')
                    ->selectRaw("SUM(pedidos_material.Cantidad_Usada) AS Cantidad_Usada")
                    ->where('pedidos_material.Mat_ID', $data->material_id)
                    ->get();
                $resultado = 0;
                foreach ($verificar_cantidad_usada as $key => $cantidad) {
                    $resultado += $cantidad->Cantidad_Usada;
                }
                return $resultado;
            })
            ->addColumn('status', function ($data) {
                $verificar_cantidad_ordenada = DB::table('tipo_orden_materiales')
                    ->select(
                        'tipo_orden_materiales.*'
                    )
                    ->join('tipo_orden', 'tipo_orden.id', 'tipo_orden_materiales.tipo_orden_id')
                    ->join('materiales', 'materiales.Mat_ID', 'tipo_orden_materiales.material_id')
                    ->join('pedidos_material', 'pedidos_material.Mat_ID', 'materiales.Mat_ID')
                    ->join('tipo_movimiento_material_pedido', 'tipo_movimiento_material_pedido.Ped_Mat_ID', 'pedidos_material.Ped_Mat_ID')
                    ->join('pedidos', 'pedidos.Ped_ID', 'pedidos_material.Ped_ID')
                    ->where('materiales.Mat_ID', $data->material_id)
                    ->where('tipo_movimiento_material_pedido.estatus_id', 3)
                    ->get()->toArray();
                if (empty($verificar_cantidad_ordenada)) {
                    return "<h5><span class='badge badge-danger'>no ordenado</span></h5>";
                } else {
                    return "<h5><span class='badge badge-success'>ordenado</span></h5>";
                }
            })
            ->rawColumns([
                'Denominacion',
                'check',
                'total_proyecto',
                'total_warehouse',
                'total_proveedor',
                'cantidad_estimada',
                'cantidad_ordenada',
                'cantidad_usada',
            ])
            ->make(true);
    }

    public function datatable_materiales($order_id, $pedido_id)
    {
        $pedidos = DB::table('pedidos')
            ->select(
                'pedidos_material.*',
                'pedidos.Pro_ID',
                'materiales.Denominacion'
            )
            ->where('pedidos.Ped_ID', $pedido_id)
            ->join('pedidos_material', 'pedidos_material.Ped_ID', 'pedidos.Ped_ID')
            ->join('materiales', 'materiales.Mat_ID', 'pedidos_material.Mat_ID')
            ->get();
        return Datatables::of($pedidos)
            ->addColumn('Denominacion', function ($data) {
                return $data->Denominacion . " / " . ($data->Aux1 == "null" ? '' : $data->Aux1);
            })
            ->addIndexColumn()
            ->addColumn('cantidad_recibida', function ($data) {
                $cantidades = DB::table('tipo_movimiento_material_pedido')
                    ->select('tipo_movimiento_material_pedido.*')
                    ->join('pedidos_material', 'pedidos_material.Ped_Mat_ID', 'tipo_movimiento_material_pedido.Ped_Mat_ID')
                    ->join('pedidos', 'pedidos.Ped_ID', 'pedidos_material.Ped_ID')
                    ->where('tipo_movimiento_material_pedido.Ped_Mat_ID', $data->Ped_Mat_ID)
                    ->where('pedidos.Pro_ID', $data->Pro_ID)
                    ->where('pedidos_material.Mat_ID', $data->Mat_ID)
                    ->get();
                $total = 0;
                foreach ($cantidades as $key => $cantidad) {
                    $total += $cantidad->ingreso;
                }
                return $total;
            })
            ->addColumn('acciones', function ($data) {
                $button = "
                <i class='fas fa-pencil-alt ms-text-warning cursor-pointer view_movimiento_material' data-id='$data->Ped_Mat_ID' title='View email'></i>
                ";
                return $button;
            })
            ->addColumn('status', function ($data) {
                $ultimo_status_material = DB::table('tipo_movimiento_material_pedido')
                    ->select(
                        'tipo_movimiento_material_pedido.*',
                        'tipo_orden_estatus.nombre as nombre_estatus',
                        'tipo_orden_estatus.color'
                    )
                    ->join('tipo_orden_estatus', 'tipo_orden_estatus.id', 'tipo_movimiento_material_pedido.estatus_id')
                    ->join('pedidos_material', 'pedidos_material.Ped_Mat_ID', 'tipo_movimiento_material_pedido.Ped_Mat_ID')
                    ->where('tipo_movimiento_material_pedido.Ped_Mat_ID', $data->Ped_Mat_ID)
                    ->orderBy('tipo_movimiento_material_pedido.id', 'DESC')
                    ->first();
                if (is_null($ultimo_status_material)) {
                    $button = 'No records';
                } else {
                    $button = "<h5><span class='badge badge-$ultimo_status_material->color'>$ultimo_status_material->nombre_estatus</span></h5>";
                }
                return $button;
            })

            ->rawColumns(['Denominacion', 'cantidad_recibida', 'acciones', 'status'])
            ->make(true);
    }
    public function datatable_materiales_movimientos($movimiento_id)
    {
        $movimientos = DB::table('tipo_orden_materiales_movimiento_vendedor')
            ->select(
                'tipo_orden_materiales_movimiento_vendedor.id',
                'tipo_orden_materiales_movimiento_vendedor.nota',
                'tipo_orden_materiales_movimiento_vendedor.cantidad',
                DB::raw('DATE_FORMAT(tipo_orden_materiales_movimiento_vendedor.fecha_registro , "%m/%d/%Y %H:%i:%s") as fecha_registro'),
                DB::raw('DATE_FORMAT(tipo_orden_materiales_movimiento_vendedor.fecha_espera , "%m/%d/%Y %H:%i:%s") as fecha_espera'),
                'proyectos.Nombre as lugar_entrega'
            )
            ->where('tipo_orden_materiales_movimiento_vendedor.tipo_orden_materiales_sub_orden_id', $movimiento_id)
            ->where('tipo_orden_materiales_movimiento_vendedor.estado', 1)
            ->leftJoin('proyectos', 'proyectos.Pro_ID', 'tipo_orden_materiales_movimiento_vendedor.lugar_entrega')
            ->orderBy('tipo_orden_materiales_movimiento_vendedor.id', 'DESC')
            ->get();
        return Datatables::of($movimientos)
            ->addIndexColumn()
            ->make(true);
    }
    public function show_material_movimiento($id)
    {
        $movimientos = DB::table('tipo_orden_materiales_sub_orden')
            ->select(
                'tipo_orden_materiales_sub_orden.id',
                'tipo_orden.proyecto_id as proyecto_id'
            )
            ->where('tipo_orden_materiales_sub_orden.id', $id)
            ->join(
                'tipo_orden_materiales',
                'tipo_orden_materiales.id',
                'tipo_orden_materiales_sub_orden.tipo_orden_materiales_id'
            )
            ->join(
                'tipo_orden',
                'tipo_orden.id',
                'tipo_orden_materiales.tipo_orden_id'
            )
            ->orderBy('tipo_orden_materiales_sub_orden.id', 'DESC')
            ->first();
        return response()->json($movimientos, 200);
    }
    public function store_material_movimiento(Request $request, $id)
    {
        $rules = array(
            'sub_orden_id' => 'required',
            'movimiento_nota' => 'required',
            'fecha_registro_movimiento' => 'required',
            'fecha_espera_movimiento' => 'required',
            'lugar_entrega_movimiento' => 'nullable',
            'cantidad_recibida' => 'nullable',
        );
        $messages = [
            'movimiento_nota.required' => "The Note field is required",
            'fecha_espera_movimiento.required' => 'The Waiting date field is required',
        ];
        $error = Validator::make($request->all(), $rules, $messages);
        if ($error->errors()->all()) {
            return response()->json([
                "status" => "errors",
                "message" => $error->errors()->all()]);
        }
        $movimiento = DB::table('tipo_orden_materiales_movimiento_vendedor')
            ->insertGetId([
                'tipo_orden_materiales_sub_orden_id' => $request->sub_orden_id,
                'nota' => $request->movimiento_nota,
                'fecha_registro' => date('Y-m-d H:i:s', strtotime($request->fecha_registro_movimiento)),
                'fecha_espera' => date('Y-m-d H:i:s', strtotime($request->fecha_espera_movimiento)),
                'lugar_entrega' => $request->lugar_entrega_movimiento,
                'cantidad' => $request->cantidad_recibida,
                'estado' => 1,
            ]);
        return response()->json([
            "status" => "ok",
            "message" => 'Registered Successfully',
        ], 200);
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
    public function view_email(Request $request)
    {
        $dirrecion = DB::table('proyectos')
            ->select(
                DB::raw("CONCAT(proyectos.Calle,' ', proyectos.Numero,' ',proyectos.Estado,' ',proyectos.Ciudad) as address")
            )
            ->where('proyectos.Pro_ID', 1)
            ->first();
        $orden = DB::table('tipo_orden')
            ->select(
                'proyectos.Codigo',
                'proyectos.Nombre',
                'proyectos.Pro_ID',
                DB::raw("CONCAT( proyectos.Calle,' ', proyectos.Numero,' ',proyectos.Ciudad,', ',proyectos.Estado,' ',proyectos.Zip_Code) as address"),
                DB::raw("CONCAT( gc_super.Nombre,' ', gc_super.Apellido_Paterno,' ',gc_super.Apellido_Materno) as gc_super"),
                'gc_super.Celular as gc_super_celular',
                'foreman.Nick_Name as foreman_Nick_Name',
                'foreman.Celular as foreman_celular',
                'lead.Nick_Name as lead_Nick_Name',
                'lead.Celular as lead_celular'
            )
            ->join('proyectos', 'proyectos.Pro_ID', 'tipo_orden.proyecto_id')
            ->leftjoin('personal as gc_super', 'gc_super.Empleado_ID', 'proyectos.Coordinador_Obra_ID')
            ->leftjoin('personal as foreman', 'foreman.Empleado_ID', 'proyectos.Foreman_ID')
            ->leftjoin('personal as lead', 'lead.Empleado_ID', 'proyectos.Lead_ID')
            ->where('tipo_orden.id', $request->orden_id)
            ->first();

        $sub_orden = DB::table('pedidos')
            ->select(
                DB::raw('DATE_FORMAT(pedidos.Fecha , "%m/%d/%Y %H:%i:%s") as fecha_registro'),
                'pedidos.PO',
                'pedidos.note',
                'pedidos.Ped_ID'
            )
            ->where('pedidos.tipo_orden_id', $request->orden_id)
            ->where('pedidos.Ped_ID', $request->pedido_id)
            ->first();
        $materiales = DB::table('pedidos_material')
            ->select(
                'pedidos_material.*',
                'materiales.Denominacion',
                'materiales.Unidad_Medida'
            )
            ->where('pedidos_material.Ped_ID', $sub_orden->Ped_ID)
            ->join('materiales', 'materiales.Mat_Id', 'pedidos_material.Mat_Id')
            ->get();
        return response()->json([
            "status" => "ok",
            "orden" => $orden,
            "sub_orden" => $sub_orden,
            "materiales" => $materiales,
            "dirrecion" => $dirrecion,
            "message" => 'verificado',
        ], 200);
    }
    private function validar_material($materiales_id)
    {
        $validar = DB::table('tipo_orden_materiales_sub_orden')
            ->where('tipo_orden_materiales_sub_orden.tipo_orden_materiales_id', $materiales_id)
            ->first();
        if ($validar) {
            return false;
        } else {
            return true;
        }
    }
    public function create_order()
    {
        $status = DB::table('tipo_orden_estatus')
            ->get();
        return response()->json($status, 200);
    }
    public function store_order(Request $request)
    {
        $rules = array(
            'new_orden_status' => 'required',
            'new_proyect' => 'required',
            'new_job_name' => 'required',
            'new_date_order' => 'date_format:m/d/Y H:i:s|required',
            'new_date_work' => 'date_format:m/d/Y H:i:s|required',
            'new_orden_nota' => 'nullable',
            'new_cantidad' => 'required|array',
            'new_material_id' => 'required|array',
        );
        $messages = [
            'new_orden_status.required' => "The status field is required",
            'new_proyect.required' => "The proyect field is required",
            'new_job_name.required' => "The name proyect field is required",
            'new_date_order.required' => 'The order date field is required',
            'new_date_work.required' => 'The work date field is required',
            'new_material_id.required' => 'The select materials is required',
            'new_cantidad.required' => 'The select quantity is required',
        ];
        $error = Validator::make($request->all(), $rules, $messages);
        if ($error->errors()->all()) {
            return response()->json([
                "status" => "errors",
                "message" => $error->errors()->all(),
            ]);
        } else {
            if ($this->validar_array($request->new_material_id, null) == false) {
                return response()->json([
                    "status" => "errors",
                    "message" => ['you must select a material'],
                ], 200);
            }
            if ($this->validar_array($request->new_cantidad, 0) == false) {
                return response()->json([
                    "status" => "errors",
                    "message" => ['the quantity of materials must be greater than 0'],
                ], 200);
            }
        }
        /*guardando orden */
        $n_orden = TipoOrden::where('estado', 'creado')->count() + 1;
        $orden = DB::table('tipo_orden')
            ->insertGetId([
                'proyecto_id' => $request->new_proyect,
                'estatus_id' => $request->new_orden_status,
                'num' => $n_orden,
                'nota' => $request->new_orden_nota,
                'nombre_trabajo' => $request->new_job_name,
                'estado' => 'creado',
                'fecha_order' => date('Y-m-d H:i:s', strtotime($request->new_date_order)),
                'fecha_entrega' => date('Y-m-d H:i:s', strtotime($request->new_date_work)),
                'creado_por' => auth()->user()->Empleado_ID,
                'eliminado' => 0,
            ]);
        foreach ($request->new_material_id as $key => $value) {
            $materiales = DB::table('tipo_orden_materiales')
                ->insertGetId([
                    'material_id' => $value,
                    'tipo_orden_id' => $orden,
                    'nota_material' => $request->new_nota[$key] != null ? $request->new_nota[$key] : null,
                    'tipo_orden_material_id' => 1,
                    'cant_ordenada' => $request->new_cantidad[$key],
                    'cant_registrada' => $request->new_cantidad[$key],
                    'estado' => 1,
                ]);
        }
        return response()->json([
            "status" => "ok",
            "message" => 'Registered Successfully',
        ], 200);
    }
    public function edit_orden($id)
    {
        $orden = DB::table('tipo_orden')
            ->select(
                'tipo_orden.*',
                'proyectos.Pro_ID',
                'proyectos.Nombre as nombre_proyecto',
                DB::raw("CONCAT( personal.Nombre,' ', personal.Apellido_Paterno,' ',personal.Apellido_Materno) as creado_por")
            )
            ->where('tipo_orden.id', $id)
            ->join('proyectos', 'proyectos.Pro_ID', 'tipo_orden.proyecto_id')
            ->join('personal', 'personal.Empleado_ID', 'tipo_orden.creado_por')
            ->first();
        $materiales = DB::table('tipo_orden_materiales')
            ->select(
                'materiales.Denominacion',
                'tipo_orden_materiales.*',
                'tipo_orden_material.nombre',
                'categoria_material.Nombre',
                'categoria_material.Cat_ID',
                'materiales.Unidad_Medida'
            )
            ->where('tipo_orden_materiales.tipo_orden_id', $id)
            ->join('materiales', 'materiales.Mat_ID', 'tipo_orden_materiales.material_id')
            ->join('categoria_material', 'categoria_material.Cat_ID', 'materiales.Cat_ID')
            ->join('tipo_orden_material', 'tipo_orden_material.id', 'tipo_orden_materiales.tipo_orden_material_id')
            ->get();
        return response()->json([
            "status" => "ok",
            "orden" => $orden,
            "materiales" => $materiales,
            "message" => 'Ok',
        ], 200);
    }
    public function update_orden(Request $request, $id)
    {
        $rules = array(
            'edit_orden_status' => 'required',
            'edit_orden_materiales_id' => 'nullable',
            'edit_proyect' => 'required',
            'edit_job_name' => 'required',
            'edit_date_work' => 'date_format:m/d/Y H:i:s|required',
            'edit_orden_nota' => 'nullable',
            'edit_cantidad' => 'required|array',
            'edit_material_id' => 'required|array',
        );
        $messages = [
            'edit_orden_status.required' => "The status field is required",
            'edit_proyect.required' => "The proyect field is required",
            'edit_job_name.required' => "The name proyect field is required",
            'edit_date_work.required' => 'The work date field is required',
            'edit_material_id.required' => 'The select materials is required',
            'edit_cantidad.required' => 'The select quantity is required',
        ];
        $error = Validator::make($request->all(), $rules, $messages);
        if ($error->errors()->all()) {
            return response()->json([
                "status" => "errors",
                "message" => $error->errors()->all(),
            ]);
        } else {
            if ($this->validar_array($request->edit_material_id, null) == false) {
                return response()->json([
                    "status" => "errors",
                    "message" => ['you must select a material'],
                ], 200);
            }
            if ($this->validar_array($request->edit_cantidad, 0) == false) {
                return response()->json([
                    "status" => "errors",
                    "message" => ['the quantity of materials must be greater than 0'],
                ], 200);
            }
        }
        //actualizando
        $update_orden = DB::table('tipo_orden')
            ->where('tipo_orden.id', $id)
            ->update([
                'proyecto_id' => $request->edit_proyect,
                'estatus_id' => $request->edit_orden_status,
                'nota' => $request->edit_orden_nota,
                'nombre_trabajo' => $request->edit_job_name,
                'fecha_entrega' => date('Y-m-d H:i:s', strtotime($request->edit_date_work)),
            ]);
        $verificar = DB::table('tipo_orden_materiales')
            ->where('tipo_orden_materiales.tipo_orden_id', $id)
            ->join('pedidos', 'pedidos.tipo_orden_id', 'tipo_orden_materiales.tipo_orden_id')
            ->get()->toArray();
        if (empty($verificar)) {
            DB::table('tipo_orden_materiales')
                ->where('tipo_orden_materiales.tipo_orden_id', $id)
                ->delete();
            foreach ($request->edit_material_id as $key => $value) {
                /*   $update_materiales = DB::table('tipo_orden_materiales')
                ->where('tipo_orden_materiales.tipo_orden_id', $id)
                ->where('tipo_orden_materiales.id', $value)
                ->update([
                'material_id' => $request->edit_material_id[$key],
                'nota_material' => $request->edit_nota[$key],
                'cant_ordenada' => $request->edit_cantidad[$key],
                'cant_registrada' => $request->edit_cantidad[$key],
                ]); */
                $insert_materiales = DB::table('tipo_orden_materiales')
                    ->where('tipo_orden_materiales.tipo_orden_id', $id)
                    ->insert([
                        'material_id' => $request->edit_material_id[$key],
                        'tipo_orden_id' => $id,
                        'nota_material' => $request->edit_nota[$key] != null ? $request->edit_nota[$key] : null,
                        'tipo_orden_material_id' => 1,
                        'cant_ordenada' => $request->edit_cantidad[$key],
                        'cant_registrada' => $request->edit_cantidad[$key],
                        'estado' => 1,
                    ]);
                /*  dd($request->edit_orden_materiales_id, $request->edit_material_id); */
            }
            return response()->json([
                "status" => "ok",
                "message" => 'Successfully modified',
            ], 200);
        } else {
            return response()->json([
                "status" => "incomplete",
                "message" => 'You cannot modify the materials there are orders',
            ], 200);
        }
    }
    public function delete_orden(Request $request, $id)
    {
        $verificar = DB::table('tipo_orden_materiales')
            ->where('tipo_orden_materiales.tipo_orden_id', $id)
            ->join('pedidos', 'pedidos.tipo_orden_id', 'tipo_orden_materiales.tipo_orden_id')
            ->get()->toArray();
        if (empty($verificar)) {
            /* eliminacion fisica de materiales-orden */
            $materiales_orden = DB::table('tipo_orden_materiales')
                ->where('tipo_orden_id', $id)->delete();
            /* eliminacion de orden de campo */
            $orden = DB::table('tipo_orden')
                ->where('id', $id)->delete();

            return response()->json([
                "status" => "ok",
                "message" => 'Successfully removed',
            ], 200);
        } else {
            return response()->json([
                "status" => "errors",
                "message" => 'Cannot be deleted exists orders',
            ], 200);
        }
    }
    private function validar_array($array, $validador)
    {
        $resultado = true;
        foreach ($array as $key => $value) {
            if ($value == $validador) {
                $resultado = false;
                break;
            }
        }
        return $resultado;
    }
    private function añadir_material_almacen($materiales_id, $proyecto_id)
    {
        $verificar = DB::table('tipo_almacen')
            ->where('tipo_almacen.proyecto_id', $proyecto_id)
            ->first();
        if ($verificar) {
            foreach ($materiales_id as $key => $value) {
                $material_almacen = DB::table('tipo_almacen_materiales')
                    ->insert([
                        "material_id" => $value,
                        "almacen_id" => $verificar->id,
                        "estado" => 1,
                    ]);
            }
        }

    }
    public function show_segimiento_material(Request $request, $id)
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

        $to = DB::table('proyectos')
            ->select(
                'proyectos.Pro_ID as id',
                'proyectos.Nombre as nombre'
            )
            ->whereIn('proyectos.Pro_ID', [$orden->proyecto_id, 1, 1654, 1655])
            ->get();
        return response()->json([
            "status" => "ok",
            "orden" => $orden,
            "materiales" => $materiales_solicitud,
            "to" => $to,
            "message" => 'verificado',
        ], 200);
    }
    /*data  materiales */
    private function total_warehouse($material_id, $proyecto_id)
    {
        $verificar_warehouse = DB::table('tipo_movimiento_material_pedido')
            ->select('tipo_movimiento_material_pedido.*')
            ->join('pedidos_material', 'pedidos_material.Ped_Mat_ID', 'tipo_movimiento_material_pedido.Ped_Mat_ID')
            ->join('pedidos', 'pedidos.Ped_ID', 'pedidos_material.Ped_ID')
            ->where('pedidos.Pro_ID', $proyecto_id)
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
        } else {
            return false;
        }
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
            $rest_materiales->total_warehouse = $this->total_warehouse($data->material_id, $data->proyecto_id);
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
    public function get_materiales_recojer($id)
    {
        $verificar_proyecto = DB::table('tipo_movimiento_material_pedido')
            ->select(
                'materiales.Mat_ID',
                'materiales.Cat_ID',
                'materiales.Denominacion',
                'materiales.Unidad_Medida',
                'categoria_material.Nombre',
                'tipo_movimiento_material_pedido.ingreso',
                'tipo_movimiento_material_pedido.egreso',
                'tipo_movimiento_material_pedido.Pro_id_ubicacion',
                'tipo_movimiento_material_pedido.Ped_Mat_ID'
            )
            ->join('pedidos_material', 'pedidos_material.Ped_Mat_ID', 'tipo_movimiento_material_pedido.Ped_Mat_ID')
            ->join('materiales', 'materiales.Mat_ID', 'pedidos_material.Mat_ID')
            ->join('categoria_material', 'categoria_material.Cat_ID', 'materiales.Cat_ID')
            ->where('materiales.Cat_ID', 8)
            ->where('tipo_movimiento_material_pedido.Pro_id_ubicacion', $id)
            ->groupBy('materiales.Mat_ID')
            ->get();
        //dd($verificar_proyecto);
        $filtro = [];
        foreach ($verificar_proyecto as $key => $value) {
            $total = 0;
            $verificar_cantidades = DB::table('tipo_movimiento_material_pedido')
                ->where('tipo_movimiento_material_pedido.material_id', $value->Mat_ID)
                ->where('tipo_movimiento_material_pedido.Pro_id_ubicacion', $id)
                ->get();
            //calculo de ingresos
            $ingresos = 0;
            $egresos = 0;
            $filtrado = [];
            foreach ($verificar_cantidades as $index => $value) {
                $ingresos += $value->ingreso;
                $egresos += $value->egreso;
            }
            $total = ($ingresos - $egresos);
            //fin
            if ($total > 0) {
                $verificar_proyecto[$key]->total = $total;
                $filtro[] = $verificar_proyecto[$key];
            }
        }
        return $filtro;
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
}
