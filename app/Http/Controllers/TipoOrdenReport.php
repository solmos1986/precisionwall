<?php

namespace App\Http\Controllers;

use App\Exports\orden_proyecto_total_detalle_Export;
use App\Exports\orden_proyecto_total_Export;
use DB;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Excel;
use PDF;

class TipoOrdenReport extends Controller
{
    public function __construct(Excel $excel)
    {
        ///inject libreria
        $this->excel = $excel;
        $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $status_proyectos = DB::table('estatus')->get();
        $status_orden = DB::table('tipo_orden_estatus')->get();
        return view('panel.tipo_orden_report.index', compact('status_proyectos', 'status_orden'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function get_status(Request $request)
    {
        $status = DB::table('tipo_orden_estatus')
            ->when($request->searchTerm, function ($query) {
                return $query->where('tipo_orden_estatus.nombre', 'like', '%' . $request->searchTerm . '%');
            })
            ->get();
        $data = [];
        foreach ($status as $row) {
            $data[] = array(
                "id" => $row->id,
                "text" => $row->nombre,
                "email" => $row->color,
            );
        }
        return response()->json($data);

    }
    public function get_proyectos(Request $request)
    {
        $proyectos = DB::table('proyectos')
            ->select(
                'proyectos.*',
                'proyectos.Nombre as nombre',
            )
            ->when($request->status, function ($query) use ($request) {
                return $query->whereIn('proyectos.Estatus_ID', $request->status);
            })
            ->get();
        return response()->json($proyectos, 200);
    }
    public function get_materiales(Request $request)
    {
        //add warehouse
        $materiales = DB::table('materiales')
            ->select(
                'materiales.Denominacion as denominacion',
                'materiales.Mat_ID as material_id',
            )
            ->when($request->tipo_material, function ($query) use ($request) {
                if ($request->tipo_material == 'material') {
                    return $query->whereIn('materiales.Cat_ID', [1, 2, 3, 4, 5, 6, 7, 10, 11]);
                } else {
                    return $query->whereIn('materiales.Cat_ID', [8]);
                }
            })
            ->when($request->proyectos, function ($query) use ($request) {
                $proyectos = $request->proyectos;
                $proyectos[] = '1'; //add warehouse en materiales
                return $query->whereIn('materiales.Pro_ID', $proyectos);
            })
            ->when(!$request->proyectos, function ($query) use ($request) {
                return $query->where('materiales.Pro_ID', 1);
            })
            ->orderBy('materiales.Denominacion')
            ->groupBy('materiales.Mat_ID')
            ->get();
        return response()->json($materiales, 200);
    }

    public function view_pdf(Request $request)
    {
        //dd($proyectos);
        $view = $request->query('view');
        $detalle = $request->query('detalle');
        $fecha_inicio = $request->query('fecha_inicio');
        $fecha_fin = $request->query('fecha_fin');
        $proyectos = $this->obtenerMaterialesDetalle($request);
        if (($view == "view_proyecto") && ($request->query('proyectos'))) {
            if ($detalle == 'true') {

                $pdf = PDF::loadView(
                    'panel.tipo_orden_report.proyectos_material_detalle-pdf',
                    compact(
                        'proyectos',
                        'fecha_inicio',
                        'fecha_fin'
                    ))->setPaper('a4', 'landscape')->setWarnings(false);
                return $pdf->stream("Material and Equipment Report from " . date('m-d-Y', strtotime($fecha_inicio)) . " to " . date('m-d-Y', strtotime($fecha_inicio)) . " detail.pdf");
            } else {

                $pdf = PDF::loadView(
                    'panel.tipo_orden_report.proyectos_material-pdf',
                    compact(
                        'proyectos',
                        'fecha_inicio',
                        'fecha_fin'
                    ))->setPaper('a4', 'letter')->setWarnings(false);
                return $pdf->stream("Material and Equipment Report from " . date('m-d-Y', strtotime($fecha_inicio)) . " to " . date('m-d-Y', strtotime($fecha_fin)) . ".pdf");
            }
        } else {
            $pdf = PDF::loadView(
                'panel.tipo_orden_report.material-pdf',
                compact(
                    'proyectos',
                    'detalle',
                    'fecha_inicio',
                    'fecha_fin'
                ))->setPaper('a4', 'landscape')->setWarnings(false);
            return $pdf->stream("Order Report");
        }
    }
    private function total_materiales(Request $request)
    {
        $proyectos_id = explode(',', $request->query('proyectos'));
        $materiales_id = explode(',', $request->query('materiales'));
        $materiales = DB::table('tipo_movimiento_material_pedido')
            ->select(
                'tipo_movimiento_material_pedido.id',
                'materiales.Denominacion',
                'categoria_material.Nombre',
                'materiales.Cat_ID',
                'tipo_movimiento_material_pedido.material_id',
                'tipo_movimiento_material_pedido.Pro_id_ubicacion',
                'tipo_movimiento_material_pedido.estatus_id',
            )
            ->join('materiales', 'materiales.Mat_ID', 'tipo_movimiento_material_pedido.material_id')
            ->join('categoria_material', 'categoria_material.Cat_ID', 'materiales.Cat_ID')
            ->join('proyectos', 'proyectos.Pro_ID', 'tipo_movimiento_material_pedido.Pro_id_ubicacion')
            ->when($request->query('materiales'), function ($query) use ($materiales_id) {
                return $query->whereIn('tipo_movimiento_material_pedido.material_id', $materiales_id)
                    ->groupBy('tipo_movimiento_material_pedido.material_id');
            })
            ->when(
                $request->query('fecha_inicio') && $request->query('fecha_fin'),
                function ($query) use ($request) {
                    return $query->whereBetween('tipo_movimiento_material_pedido.fecha', [
                        date('Y-m-d H:i:s', strtotime($request->query('fecha_inicio'))),
                        date('Y-m-d', strtotime($request->query('fecha_fin'))) . ' 23:59:59',
                    ]);
                })
            ->get();

        foreach ($materiales as $key => $material) {
            $materiales[$key] = $material;
            $materiales[$key]->proyectos = $this->detalle_proyectos($material->material_id, $request);
            $materiales[$key]->total_material = count($materiales[$key]->proyectos);
            $materiales[$key]->total = 0;
        }
        //total de materiales en materiales
        foreach ($materiales as $key => $material) {
            foreach ($material->proyectos as $key => $total) {
                $material->total += $total->total;
            }
        }
        return $materiales;
    }
    private function total_proyectos(Request $request)
    {
        $proyectos_id = explode(',', $request->query('proyectos'));
        $materiales_id = explode(',', $request->query('materiales'));
        $proyectos = DB::table('tipo_orden')
            ->select(
                'tipo_orden.proyecto_id',
                'empresas.Nombre as nombre_empresa',
                'proyectos.*',
                'estatus.Nombre_Estatus'
            )
            ->join('proyectos', 'proyectos.Pro_ID', 'tipo_orden.proyecto_id')
            ->join('empresas', 'empresas.Emp_ID', 'proyectos.Emp_ID')
            ->join('estatus', 'estatus.Estatus_ID', 'proyectos.Estatus_ID')
            ->whereIn('tipo_orden.proyecto_id', $proyectos_id)
            ->whereIn('proyectos.Estatus_ID', explode(',', $request->query('status')))
            ->groupBy('tipo_orden.proyecto_id')
            ->get();
        foreach ($proyectos as $key => $proyecto) {
            $materiales = DB::table('tipo_orden')
                ->select(
                    'tipo_orden.id',
                    'materiales.Denominacion',
                    'tipo_orden_materiales.material_id'
                )
                ->join('tipo_orden_materiales', 'tipo_orden.id', 'tipo_orden_materiales.tipo_orden_id')
                ->join('materiales', 'tipo_orden_materiales.material_id', 'materiales.Mat_ID')
                ->whereIn('tipo_orden_materiales.material_id', $materiales_id)
                ->whereIn('tipo_orden.estatus_id', explode(',', $request->query('status_orden')))
                ->where('tipo_orden.proyecto_id', $proyecto->proyecto_id)
                ->when($request->query('fecha_inicio') && $request->query('fecha_fin'),
                    function ($query) use ($request) {
                        return $query->whereBetween('tipo_orden.fecha_order', [
                            date('Y-m-d H:i:s', strtotime($request->query('fecha_inicio'))),
                            date('Y-m-d', strtotime($request->query('fecha_fin'))) . ' 23:59:59',
                        ]);
                    })
                ->groupBy('tipo_orden_materiales.material_id')
                ->get();
            $proyecto->materiales = $materiales;

            foreach ($materiales as $key => $material) {
                //conteo de totales
                $material->total_registrado = 0;
                $material->total_ordenado = 0;
                $material->total_warehouse = 0;
                $material->total_proyecto = 0;
                //
                $materiales_orden = DB::table('tipo_orden')
                    ->select(
                        'tipo_orden.id',
                        'materiales.Denominacion',
                        'tipo_orden_materiales.material_id'
                    )
                    ->join('tipo_orden_materiales', 'tipo_orden.id', 'tipo_orden_materiales.tipo_orden_id')
                    ->join('materiales', 'tipo_orden_materiales.material_id', 'materiales.Mat_ID')
                    ->where('tipo_orden_materiales.material_id', $material->material_id)
                    ->whereIn('tipo_orden.estatus_id', explode(',', $request->query('status_orden')))
                    ->where('tipo_orden.proyecto_id', $proyecto->proyecto_id)
                    ->when($request->query('fecha_inicio') && $request->query('fecha_fin'),
                        function ($query) use ($request) {
                            return $query->whereBetween('tipo_orden.fecha_order', [
                                date('Y-m-d H:i:s', strtotime($request->query('fecha_inicio'))),
                                date('Y-m-d', strtotime($request->query('fecha_fin'))) . ' 23:59:59',
                            ]);
                        })
                    ->get();
                $material->materiales_orden = $materiales_orden;
                foreach ($materiales_orden as $key => $material_orden) {
                    $order_materiales = DB::table('tipo_orden_materiales')
                        ->select(
                            'tipo_orden_materiales.*',
                            'tipo_orden.fecha_order',
                            DB::raw("DATE_FORMAT(tipo_orden.fecha_order , '%m/%d/%Y %H:%i:%s' ) as fecha_order"),
                        )
                        ->join('tipo_orden', 'tipo_orden_materiales.tipo_orden_id', 'tipo_orden.id')
                        ->where('tipo_orden_materiales.tipo_orden_id', $material_orden->id)
                        ->where('tipo_orden_materiales.material_id', $material_orden->material_id)
                        ->get();
                    $material_orden->ordenes = $order_materiales;
                    foreach ($order_materiales as $key => $order_material) {
                        //conteo
                        $material->total_registrado += $order_material->cant_registrada;
                        //
                        $pedidos = DB::table('pedidos')
                            ->select(
                                'pedidos.*',
                                'comprador.Nombre as comprador',
                                'vendedor.Nombre as vendedor',
                                'tipo_orden_estatus.nombre as nombre_status'
                            )
                            ->join('proyectos as vendedor', 'pedidos.Ven_ID', 'vendedor.Pro_ID')
                            ->join('proyectos as comprador', 'pedidos.To_ID', 'comprador.Pro_ID')
                            ->join('tipo_orden_estatus', 'tipo_orden_estatus.id', 'pedidos.status_id')
                            ->where('pedidos.tipo_orden_id', $order_material->tipo_orden_id)
                            ->get();
                        $order_material->pedidos = $pedidos;
                        $enWarehouse = 0;
                        $enProyecto = 0;
                        $enReturn = 0;
                        foreach ($pedidos as $key => $pedido) {
                            $pedido_materiales = DB::table('pedidos_material')
                                ->where('pedidos_material.Ped_ID', $pedido->Ped_ID)
                                ->get();
                            $pedido->pedido_materiales = $pedido_materiales;
                            foreach ($pedido_materiales as $key => $pedido_material) {
                                //conteo
                                if ($pedido->Ven_ID != $pedido->Pro_ID && $pedido->Ven_ID != 1) {
                                    $material->total_ordenado += $pedido_material->Cantidad;
                                }
                                /*  if ($pedido->Pro_ID == $pedido->Ven_ID && $pedido->To_ID == 1) {
                                # code...
                                } else {
                                $material->total_ordenado += $pedido_material->Cantidad;
                                } */
                                //
                                $movimientos = DB::table('tipo_movimiento_material_pedido')
                                    ->select(
                                        'tipo_movimiento_material_pedido.*',
                                        'proyectos.Nombre as proyecto_ubicacion'
                                    )
                                    ->where('tipo_movimiento_material_pedido.Ped_Mat_ID', $pedido_material->Ped_Mat_ID)
                                    ->join('proyectos', 'tipo_movimiento_material_pedido.Pro_id_ubicacion', 'proyectos.Pro_ID')
                                    ->get();
                                $pedido_material->movimientos = $movimientos;
                                foreach ($movimientos as $key => $movimiento) {
                                    //de proveedor a proyecto
                                    if (($pedido->Ven_ID != 1) && ($movimiento->Pro_id_ubicacion == $pedido->To_ID)) {
                                        $enProyecto = $movimiento->ingreso - $movimiento->egreso;

                                    }
                                    //de proveedor a warehouse
                                    if (($pedido->Ven_ID != 1) && ($movimiento->Pro_id_ubicacion == 1)) {
                                        $enWarehouse = $movimiento->ingreso - $movimiento->egreso;
                                        $enProyecto = 0;
                                    }
                                    //de warehouse a proyecto
                                    if (($pedido->Ven_ID == 1) && ($pedido->To_ID == $pedido->Pro_ID)) {
                                        if ($movimiento->Pro_id_ubicacion == 1) {
                                            $enWarehouse = $movimiento->ingreso - $movimiento->egreso;
                                        } else {
                                            $enWarehouse = 0;
                                            $enProyecto = $movimiento->ingreso - $movimiento->egreso;
                                        }
                                    }
                                    //de proyecto a warehouse
                                    if (($pedido->Ven_ID == $pedido->Pro_ID) && ($pedido->To_ID == 1)) {
                                        if ($movimiento->Pro_id_ubicacion == 1) {
                                            $enWarehouse = $movimiento->ingreso - $movimiento->egreso;
                                            $enProyecto = 0;
                                        } else {
                                            $enWarehouse = 0;
                                            $enProyecto = $movimiento->ingreso - $movimiento->egreso;
                                        }
                                    }
                                    $movimiento->enWarehouse = $enWarehouse;
                                    $movimiento->enProyecto = $enProyecto;
                                    $movimiento->enReturn = $enReturn;
                                    //conteo
                                    $material->total_warehouse += $enWarehouse;
                                    $material->total_proyecto += $enProyecto;
                                    //
                                }
                            }
                        }
                    }
                }
            }
        }
        return $proyectos;
    }
    private function estructura_pedidos()
    {
        foreach ($proyectos as $key => $proyecto) {
            $ordenes = DB::table('tipo_orden')
                ->select(
                    'tipo_orden.*',
                    'tipo_orden_estatus.nombre'
                )
                ->join('tipo_orden_estatus', 'tipo_orden_estatus.id', 'tipo_orden.estatus_id')
                ->where('tipo_orden.proyecto_id', $proyecto->Pro_ID)
                ->get();
            $proyecto->ordenes = $ordenes;
            foreach ($ordenes as $key => $orden) {
                $pedidos = DB::table('pedidos')
                    ->join('tipo_movimiento_pedido', 'tipo_movimiento_pedido.Ped_ID', 'pedidos.Ped_ID')
                    ->where('pedidos.tipo_orden_id', $orden->id)
                    ->get();
                $orden->pedidos = $pedidos;
                foreach ($pedidos as $key => $pedido) {
                    $pedidos_materiales = DB::table('pedidos_material')
                        ->where('pedidos_material.Ped_ID', $pedido->Ped_ID)
                    //->whereIn('pedidos_material.Mat_ID', $materiales_id)
                        ->get();
                    $pedido->pedidos_materiales = $pedidos_materiales;
                    foreach ($pedidos_materiales as $key => $pedido_material) {
                        $movimiento_material_pedido = DB::table('tipo_movimiento_material_pedido')
                            ->where('tipo_movimiento_material_pedido.Ped_Mat_ID', $pedido_material->Ped_Mat_ID)
                            ->get();
                        $pedido_material->movimiento_material_pedido = $movimiento_material_pedido;
                    }
                }
            }
        }
    }
    private function obtener_cantidad_ordenada($proyecto_id, $materiales_id, Request $request)
    {
        $total_ordenado = DB::table('tipo_movimiento_material_pedido')
            ->join('pedidos_material', 'tipo_movimiento_material_pedido.Ped_Mat_ID', 'pedidos_material.Ped_Mat_ID')
            ->join('pedidos', 'pedidos.Ped_ID', 'pedidos_material.Ped_ID')
            ->whereIn('pedidos_material.Mat_ID', $materiales_id)
            ->whereIn('pedidos.Pro_ID', $proyecto_id)
            ->groupBy('pedidos_material.Ped_Mat_ID')
            ->get();
        return $total_ordenado;
    }
    private function detalle_proyectos($material_id, Request $request)
    {
        $proyectos_id = explode(',', $request->query('proyectos'));
        $detalles_proyectos = DB::table('tipo_movimiento_material_pedido')
            ->select(
                'proyectos.Nombre',
                'proyectos.Codigo',
                'tipo_movimiento_material_pedido.material_id',
                'tipo_movimiento_material_pedido.Pro_id_ubicacion',
            )
            ->join('proyectos', 'proyectos.Pro_ID', 'tipo_movimiento_material_pedido.Pro_id_ubicacion')
            ->where('tipo_movimiento_material_pedido.material_id', $material_id)
            ->when($request->query('proyectos'), function ($query) use ($proyectos_id) {
                return $query->whereIn('tipo_movimiento_material_pedido.Pro_id_ubicacion', $proyectos_id);
            })
            ->when(
                $request->query('fecha_inicio') && $request->query('fecha_fin'),
                function ($query) use ($request) {
                    return $query->whereBetween('tipo_movimiento_material_pedido.fecha', [
                        date('Y-m-d H:i:s', strtotime($request->query('fecha_inicio'))),
                        date('Y-m-d', strtotime($request->query('fecha_fin'))) . ' 23:59:59',
                    ]);
                })
            ->groupBy('tipo_movimiento_material_pedido.Pro_id_ubicacion')
            ->orderBy('proyectos.Nombre')
            ->get();

        foreach ($detalles_proyectos as $key => $material) {
            $detalles_proyectos[$key] = $material;
            $detalles_proyectos[$key]->detalle = $this->detalles($material->Pro_id_ubicacion, $material->material_id, $request);
            $detalles_proyectos[$key]->total_egreso = 0;
            $detalles_proyectos[$key]->total_ingreso = 0;
            foreach ($material->detalle as $i => $item) {
                $detalles_proyectos[$key]->total_egreso += $item->egreso;
                $detalles_proyectos[$key]->total_ingreso += $item->ingreso;
            }
            $detalles_proyectos[$key]->total = ($detalles_proyectos[$key]->total_ingreso - $detalles_proyectos[$key]->total_egreso);
        }

        return $detalles_proyectos;
    }
    private function detalle_materiales($proyecto_id, Request $request)
    {
        $materiales_id = explode(',', $request->query('materiales'));
        $detalles_materiales = DB::table('tipo_movimiento_material_pedido')
            ->select(
                'tipo_movimiento_material_pedido.material_id',
                'materiales.Denominacion',
                'categoria_material.Nombre',
                'materiales.Cat_ID',
                'tipo_movimiento_material_pedido.Pro_id_ubicacion',
            )
            ->join('materiales', 'materiales.Mat_ID', 'tipo_movimiento_material_pedido.material_id')
            ->join('categoria_material', 'categoria_material.Cat_ID', 'materiales.Cat_ID')
            ->where('tipo_movimiento_material_pedido.Pro_id_ubicacion', $proyecto_id)
            ->when($request->query('materiales'), function ($query) use ($materiales_id) {
                return $query->whereIn('tipo_movimiento_material_pedido.material_id', $materiales_id);
            })
            ->when(
                $request->query('fecha_inicio') && $request->query('fecha_fin'),
                function ($query) use ($request) {
                    return $query->whereBetween('tipo_movimiento_material_pedido.fecha', [
                        date('Y-m-d H:i:s', strtotime($request->query('fecha_inicio'))),
                        date('Y-m-d', strtotime($request->query('fecha_fin'))) . ' 23:59:59',
                    ]);
                })
            ->groupBy('materiales.Mat_ID')
            ->orderBy('materiales.Denominacion')
            ->get();
        $total_ordenado = DB::table('pedidos_material')
            ->select(
                'pedidos_material.*',
                'pedidos.Pro_ID'
            )
            ->join('pedidos', 'pedidos.Ped_ID', 'pedidos_material.Ped_ID')
            ->where('pedidos.Pro_ID', $proyecto_id)
            ->whereIn('pedidos_material.Mat_ID', $materiales_id)
            ->get();
        $resultado = 0;
        foreach ($total_ordenado as $key => $ordenado) {
            $resultado += $ordenado->Cantidad;
        }

        foreach ($detalles_materiales as $key => $material) {
            $detalles_materiales[$key] = $material;
            $detalles_materiales[$key]->detalle = $this->detalles($material->Pro_id_ubicacion, $material->material_id, $request);
            $detalles_materiales[$key]->total_egreso = 0;
            $detalles_materiales[$key]->total_ingreso = 0;
            $detalles_materiales[$key]->cantidad_ordenada = $resultado;
            foreach ($material->detalle as $i => $item) {
                $detalles_materiales[$key]->total_egreso += $item->egreso;
                $detalles_materiales[$key]->total_ingreso += $item->ingreso;
            }
            $detalles_materiales[$key]->total = ($detalles_materiales[$key]->total_ingreso - $detalles_materiales[$key]->total_egreso);
        }
        return $detalles_materiales;
    }

    private function detalles($proyecto_id, $material_id, Request $request)
    {
        $detalles = DB::table('tipo_movimiento_material_pedido')
            ->select(
                'tipo_movimiento_material_pedido.ingreso',
                'tipo_movimiento_material_pedido.egreso',
                'materiales.Denominacion',
                'materiales.Cat_ID',
                'tipo_movimiento_material_pedido.Pro_id_ubicacion',
                DB::raw('DATE_FORMAT(tipo_movimiento_material_pedido.fecha, "%m/%d/%Y %H:%i:%s") as fecha'),
                'tipo_movimiento_material_pedido.estatus_id',
                'proyecto_vendor.Nombre as nombre_vendor',
                'tipo_orden_estatus.nombre as nombre_status',
                'pedidos.PO',
                'pedidos.To_ID',
                'proyecto_to.Nombre as nombre_to',
/*                 'pedidos_material.Cantidad',
'pedidos.Pro_ID' */
            )
            ->join('materiales', 'materiales.Mat_ID', 'tipo_movimiento_material_pedido.material_id')
            ->join('pedidos_material', 'pedidos_material.Ped_Mat_ID', 'tipo_movimiento_material_pedido.Ped_Mat_ID')
            ->join('pedidos', 'pedidos.Ped_ID', 'pedidos_material.Ped_ID')
            ->join('proyectos as proyecto_vendor', 'proyecto_vendor.Pro_ID', 'pedidos.Ven_ID')
            ->join('proyectos as proyecto_to', 'proyecto_to.Pro_ID', 'pedidos.To_ID')
            ->join('proyectos', 'proyectos.Pro_ID', 'tipo_movimiento_material_pedido.Pro_id_ubicacion')
            ->join('proyectos as proyecto_material', 'proyecto_material.Pro_ID', 'pedidos.Pro_ID') // llamada de logica
            ->join('tipo_orden_estatus', 'tipo_orden_estatus.id', 'tipo_movimiento_material_pedido.estatus_id')
            ->where('proyecto_material.Pro_ID', $proyecto_id)
            ->orwhere('tipo_movimiento_material_pedido.Pro_id_ubicacion', $proyecto_id)

            ->when(
                $request->query('fecha_inicio') && $request->query('fecha_fin'),
                function ($query) use ($request) {
                    return $query->whereBetween('tipo_movimiento_material_pedido.fecha', [
                        date('Y-m-d H:i:s', strtotime($request->query('fecha_inicio'))),
                        date('Y-m-d', strtotime($request->query('fecha_fin'))) . ' 23:59:59',
                    ]);
                })
            ->where('tipo_movimiento_material_pedido.material_id', $material_id)
            ->orderBy('tipo_movimiento_material_pedido.fecha', 'asc')
            ->groupBy('tipo_movimiento_material_pedido.id')
            ->get();

        return $detalles;
    }

    public function download_pdf(Request $request)
    {
        //$proyectos = $this->obtenerOrdenes($request);
        $view = $request->query('view');
        $detalle = $request->query('detalle');
        $fecha_inicio = $request->query('fecha_inicio');
        $fecha_fin = $request->query('fecha_fin');
        $proyectos = $this->obtenerMaterialesDetalle($request);
        //dd($proyectos);
        if (($view == "view_proyecto") && ($request->query('proyectos'))) {
            if ($detalle == 'true') {
                $pdf = PDF::loadView('panel.tipo_orden_report.proyectos_material_detalle-pdf', compact('proyectos', 'detalle', 'fecha_inicio', 'fecha_fin'))->setPaper('a4', 'landscape')->setWarnings(false);
                return $pdf->download("Material and Equipment Report from " . date('m-d-Y', strtotime($fecha_inicio)) . " to " . date('m-d-Y', strtotime($fecha_fin)) . " detail.pdf");
            } else {
                $pdf = PDF::loadView('panel.tipo_orden_report.proyectos_material-pdf', compact('proyectos', 'detalle', 'fecha_inicio', 'fecha_fin'))->setPaper('a4', 'letter')->setWarnings(false);
                return $pdf->download("Material and Equipment Report from " . date('m-d-Y', strtotime($fecha_inicio)) . " to " . date('m-d-Y', strtotime($fecha_fin)) . ".pdf");
            }
        } else {

        }
    }
    public function excel_pdf(Request $request)
    {
        $proyectos_id = explode(',', $request->query('proyectos'));
        $materiales_id = explode(',', $request->query('materiales'));
        $detalle = $request->query('detalle');
        $view = $request->query('view');
        $fecha_inicio = $request->query('fecha_inicio');
        $fecha_fin = $request->query('fecha_fin');

        $proyectos = $this->obtenerMaterialesDetalle($request);
        if (($view == "view_proyecto") && ($request->query('proyectos'))) {
            //dd($proyectos);
            if ($detalle == 'true') {
                return $this->excel->download(new orden_proyecto_total_detalle_Export($proyectos, $detalle, $fecha_inicio, $fecha_fin), "Material and Equipment Report from " . date('m-d-Y', strtotime($fecha_inicio)) . " to " . date('m-d-Y', strtotime($fecha_fin)) . " detail.xlsx");
            } else {
                return $this->excel->download(new orden_proyecto_total_Export($proyectos, $detalle, $fecha_inicio, $fecha_fin), "Material and Equipment Report from " . date('m-d-Y', strtotime($fecha_inicio)) . " to " . date('m-d-Y', strtotime($fecha_fin)) . ".xlsx");
            }

        } else {

        }
    }
    /* nuevas consultas */
    public function listaMateriales(Request $request)
    {
        $proyectos_id = explode(',', $request->query('proyectos'));
        $materiales_id = explode(',', $request->query('materiales'));
        $view = $request->query('view');
        $detalle = $request->query('detalle');
        $fecha_inicio = $request->query('fecha_inicio');
        $fecha_fin = $request->query('fecha_fin');

        $material_proyecto = [];
        //dd($proyectos_id);
        foreach ($proyectos_id as $key => $proyecto) {
            $materiales = DB::table('materiales')->select('materiales.*', 'categoria_material.*')
                ->Join('proyectos', 'proyectos.Pro_ID', 'materiales.Pro_ID')
                ->Join('pedidos_material', 'pedidos_material.Mat_ID', 'materiales.Mat_ID')
                ->Join('pedidos', 'pedidos.Ped_ID', 'pedidos_material.Ped_ID')
                ->Join('categoria_material', 'categoria_material.Cat_ID', 'materiales.Cat_ID')
                ->where(function ($query) use ($request, $proyecto) {
                    $query->where('materiales.Pro_ID', $proyecto);
                    //->orWhere('proyectos.Pro_ID', $);
                })
                ->whereDate('pedidos.Fecha', '>=', date('Y-m-d', strtotime($request->query('fecha_inicio'))))
                ->whereDate('pedidos.Fecha', '<=', date('Y-m-d', strtotime($request->query('fecha_fin'))))
                ->distinct('materiales.Mat_ID')
                ->orderBy('categoria_material.Cat_ID')
                ->orderBy('proyectos.Pro_ID', 'DESC')
                ->orderBy('materiales.Denominacion')
                ->groupBy('pedidos.Ped_ID')
                ->get();
            $material_proyecto[] = $materiales;
        }

        //dd($material_proyecto);
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
                ->where('Pro_id_ubicacion', $proyectos_id)
                ->first();
            $material->cant_proyecto = $cant_proyecto->cant_proyecto;
        }
    }

    public function obtenerOrdenes(Request $request)
    {
        //$this->obtenerMateriales($request);
        $proyectos_id = explode(',', $request->query('proyectos'));
        $materiales_id = explode(',', $request->query('materiales'));
        $view = $request->query('view');
        $detalle = $request->query('detalle');
        $fecha_inicio = $request->query('fecha_inicio');
        $fecha_fin = $request->query('fecha_fin');

        $proveedores = DB::table('proyectos')
            ->select(
                'proyectos.Pro_ID'
            )
            ->where('proyectos.Emp_ID', 119)
            ->get()
            ->pluck('Pro_ID')
            ->toArray();

        $proyectos = DB::table('proyectos')
            ->select(
                'empresas.Codigo as codigo_empresa',
                'empresas.Nombre as nombre_empresa',
                'estatus.Nombre_Estatus as nombre_estatus',
                'proyectos.*'
            )
            ->join('empresas', 'empresas.Emp_ID', 'proyectos.Emp_ID')
            ->join('estatus', 'estatus.Estatus_ID', 'proyectos.Estatus_ID')
            ->whereIn('proyectos.Pro_ID', $proyectos_id)
            ->get()
            ->toArray();
        foreach ($proyectos as $key => $proyecto) {
            $materiales = DB::table('pedidos_material')
                ->select(
                    'pedidos_material.Ped_Mat_ID',
                    'pedidos.Ped_ID',
                    'pedidos.PO',
                    DB::raw('DATE_FORMAT(pedidos.Fecha, "%m/%d/%Y") as Fecha'),
                    'pedidos.Pro_ID',
                    'pedidos.Ven_ID',
                    'pedidos.To_ID',
                    'pedidos_material.Cantidad',
                    'pedidos.tipo_orden_id',
                    'materiales.Mat_ID',
                    'materiales.Denominacion',
                    'materiales.Cat_ID',
                    'categoria_material.Nombre'
                )
                ->join('pedidos', 'pedidos.Ped_ID', 'pedidos_material.Ped_ID')
                ->join('materiales', 'materiales.Mat_ID', 'pedidos_material.Mat_ID')
                ->join('categoria_material', 'categoria_material.Cat_ID', 'materiales.Cat_ID')
                ->where('pedidos.Pro_ID', $proyecto->Pro_ID)
                ->whereIn('pedidos_material.Mat_ID', $materiales_id)
                ->whereDate('pedidos.Fecha', '>=', date('Y-m-d', strtotime($fecha_inicio)))
                ->whereDate('pedidos.Fecha', '<=', date('Y-m-d', strtotime($fecha_fin)))
                ->groupBy('pedidos_material.Mat_ID')
                ->orderBy('pedidos_material.Mat_ID')
                ->get()
                ->toArray();
            foreach ($materiales as $key => $material) {
                $pedidos = DB::table('pedidos_material')
                    ->select(
                        DB::raw('tipo_orden_estatus.nombre as nombre_estatus'),
                        'pedidos_material.Ped_Mat_ID',
                        'pedidos.Ped_ID',
                        'pedidos.PO',
                        DB::raw('DATE_FORMAT(pedidos.Fecha, "%m/%d/%Y") as Fecha'),
                        'pedidos.Pro_ID',
                        'pedidos.Ven_ID',
                        'pedidos.To_ID',
                        'pedidos_material.Cantidad',
                        'pedidos.tipo_orden_id',
                        'materiales.Mat_ID',
                        'materiales.Denominacion',
                        'materiales.Cat_ID',
                        'categoria_material.Nombre'
                    )
                    ->join('pedidos', 'pedidos.Ped_ID', 'pedidos_material.Ped_ID')
                    ->join('materiales', 'materiales.Mat_ID', 'pedidos_material.Mat_ID')
                    ->join('categoria_material', 'categoria_material.Cat_ID', 'materiales.Cat_ID')
                    ->leftJoin('tipo_orden_estatus', 'tipo_orden_estatus.id', 'pedidos.status_id')
                    ->where('pedidos.Pro_ID', $material->Pro_ID)
                    ->where('pedidos_material.Mat_ID', $material->Mat_ID)
                    ->whereDate('pedidos.Fecha', '>=', date('Y-m-d', strtotime($fecha_inicio)))
                    ->whereDate('pedidos.Fecha', '<=', date('Y-m-d', strtotime($fecha_fin)))
                    ->groupBy('pedidos.Ped_ID')
                    ->orderBy('pedidos_material.Mat_ID')
                    ->get()
                    ->toArray();
                //dd($material);
                foreach ($pedidos as $key => $pedido) {
                    //movimientos
                    $movimiento_material_pedido = DB::table('tipo_movimiento_material_pedido')
                        ->select(
                            'pedidos_material.Ped_Mat_ID',
                            'pedidos_material.Cantidad',
                            'pedidos_material.Ped_ID',
                            'pedidos_material.Mat_ID',
                            'tipo_movimiento_material_pedido.Pro_id_ubicacion',
                            'tipo_movimiento_material_pedido.ingreso',
                            'tipo_movimiento_material_pedido.egreso',
                            DB::raw('tipo_orden_estatus.nombre as nombre_estatus'),
                            'proyectos.Nombre',
                            /* DB::raw('SUM(tipo_movimiento_material_pedido.ingreso - tipo_movimiento_material_pedido.egreso) as cantidad') */
                        )
                        ->join('pedidos_material', 'pedidos_material.Ped_Mat_ID', 'tipo_movimiento_material_pedido.Ped_Mat_ID')
                        ->join('tipo_orden_estatus', 'tipo_orden_estatus.id', 'tipo_movimiento_material_pedido.estatus_id')
                        ->join('proyectos', 'proyectos.Pro_ID', 'tipo_movimiento_material_pedido.Pro_id_ubicacion')
                        ->where('pedidos_material.Ped_ID', $pedido->Ped_ID)
                        ->where('pedidos_material.Mat_ID', $material->Mat_ID)
                        ->get()
                        ->toArray();
                    $pre_orden = DB::table('tipo_orden')
                        ->select(
                            'tipo_orden_materiales.cant_registrada',
                            DB::raw('tipo_orden_estatus.nombre as nombre_estatus'),
                            DB::raw('DATE_FORMAT(tipo_orden.fecha_order, "%m/%d/%Y") as fecha_order')
                        )
                        ->join('tipo_orden_materiales', 'tipo_orden_materiales.tipo_orden_id', 'tipo_orden.id')
                        ->join('tipo_orden_estatus', 'tipo_orden_estatus.id', 'tipo_orden.estatus_id')
                        ->where('tipo_orden.id', $pedido->tipo_orden_id)
                        ->whereIn('tipo_orden_materiales.material_id', $materiales_id)
                        ->first();
                    $pedido->movimientos = $movimiento_material_pedido;
                    $pedido->pre_orden = $pre_orden;
                }
                $material->pedidos = $pedidos;
            }
            $proyecto->materiales = $materiales;
        }

        //aplique contadores por area
        foreach ($proyectos as $key => $proyecto) {
            foreach ($proyecto->materiales as $key => $material) {
                $material_total_warehouse = 0;
                $material_total_proyecto = 0;
                $material_total_proveedor = 0;
                $material_pre_orden = 0;
                $material_cantidad_pedido = 0;
                foreach ($material->pedidos as $key => $pedido) {
                    $total_warehouse = 0;
                    $total_proyecto = 0;
                    $total_proveedor = 0;
                    foreach ($pedido->movimientos as $i => $movimiento) {

                        //movimientos en warehouse
                        if ($movimiento->Pro_id_ubicacion == 1) {
                            $movimiento->enWarehouse = (intval($movimiento->ingreso) - intval($movimiento->egreso));
                            $movimiento->enWarehouseNombre = $movimiento->Nombre;
                            $total_warehouse += $movimiento->enWarehouse;
                        } else {
                            $movimiento->enWarehouse = 0;
                            $movimiento->enWarehouseNombre = '';
                            $total_warehouse += $movimiento->enWarehouse;
                        }
                        if ($movimiento->Pro_id_ubicacion == $pedido->To_ID) {
                            $movimiento->enProyecto = (intval($movimiento->ingreso) - intval($movimiento->egreso));
                            $movimiento->enProyectoNombre = $movimiento->Nombre;
                            $total_proyecto += $movimiento->enProyecto;
                        } else {
                            $movimiento->enProyecto = 0;
                            $movimiento->enProyectoNombre = '';
                            $total_proyecto += $movimiento->enProyecto;
                        }
                        if (array_filter($proveedores, function ($proveedores) use ($movimiento) {
                            return $proveedores == $movimiento->Pro_id_ubicacion;
                        }) > 0) {
                            $movimiento->enProveedor = (intval($movimiento->ingreso) - intval($movimiento->egreso));
                            $movimiento->enProveedorNombre = $movimiento->Nombre;
                            $total_proveedor += $movimiento->enProveedor;
                        } else {
                            $movimiento->enProveedor = 0;
                            $movimiento->enProveedorNombre = '';
                            $total_proveedor += $movimiento->enProveedor;
                        }
                    }
                    $pedido->total_warehouse = $total_warehouse;
                    $pedido->total_proyecto = $total_proyecto;
                    $pedido->total_proveedor = $total_proveedor;

                    //totales por material
                    if ($pedido->pre_orden) {
                        $material_pre_orden += $pedido->pre_orden->cant_registrada;
                    } else {
                        $material_pre_orden += 0;
                    }
                    $material_cantidad_pedido += $pedido->Cantidad;
                    $material_total_warehouse += $pedido->total_warehouse;
                    $material_total_proyecto += $pedido->total_proyecto;
                    $material_total_proveedor += $pedido->total_proveedor;
                }
                $material->material_total_warehouse = $material_total_warehouse;
                $material->material_total_proyecto = $material_total_proyecto;
                $material->material_total_proveedor = $material_total_proveedor;
                $material->material_pre_orden = $material_pre_orden;
                $material->material_cantidad_pedido = $material_cantidad_pedido;
            }
        }
        //dd($proyectos);
        return $proyectos;
    }
    public function obtenerMateriales(Request $request)
    {
        $proyectos_id = explode(',', $request->query('proyectos'));
        $materiales_id = explode(',', $request->query('materiales'));
        $view = $request->query('view');
        $detalle = $request->query('detalle');
        $fecha_inicio = $request->query('fecha_inicio');
        $fecha_fin = $request->query('fecha_fin');
        $resultado = [];
        foreach ($proyectos_id as $key => $proyecto) {
            $materiales = DB::table('materiales')
                ->join('pedidos_material', 'pedidos_material.Mat_ID', 'materiales.Mat_ID')
                ->join('pedidos', 'pedidos.Ped_ID', 'pedidos_material.Ped_ID')
                ->join('categoria_material', 'categoria_material.Cat_ID', 'materiales.Cat_ID')
                ->whereIn('materiales.Mat_ID', $materiales_id)
                ->where(function ($query) use ($proyecto) {
                    $query->where('pedidos.Pro_ID', $proyecto)
                        ->orWhere('pedidos.Ven_ID', $proyecto);
                })
                ->groupBy('materiales.Mat_ID')
                ->orderBy('materiales.Denominacion')
                ->get()
                ->toArray();
            $data = DB::table('proyectos')
                ->select(
                    'empresas.Codigo as codigo_empresa',
                    'empresas.Nombre as nombre_empresa',
                    'estatus.Nombre_Estatus as nombre_estatus',
                    'proyectos.*'
                )
                ->join('empresas', 'empresas.Emp_ID', 'proyectos.Emp_ID')
                ->join('estatus', 'estatus.Estatus_ID', 'proyectos.Estatus_ID')
                ->where('proyectos.Pro_ID', $proyecto)
                ->first();

            foreach ($materiales as $key => $material) {
                $warehouse = DB::table('pedidos_material')
                    ->select(
                        'proyectos.Nombre',
                        DB::raw('SUM(COALESCE(tipo_movimiento_material_pedido.ingreso,0)-COALESCE(tipo_movimiento_material_pedido.egreso,0)) as cantidad_registrada')
                    )
                    ->join('pedidos', 'pedidos.Ped_ID', 'pedidos_material.Ped_ID')
                    ->join('tipo_movimiento_material_pedido', 'tipo_movimiento_material_pedido.Ped_Mat_ID', 'pedidos_material.Ped_Mat_ID')
                    ->join('proyectos', 'proyectos.Pro_ID', 'tipo_movimiento_material_pedido.Pro_id_ubicacion')
                    ->whereDate('pedidos.Fecha', '>=', date('Y-m-d', strtotime($fecha_inicio)))
                    ->whereDate('pedidos.Fecha', '<=', date('Y-m-d', strtotime($fecha_fin)))
                    ->where('pedidos_material.Mat_ID', $material->Mat_ID)
                    ->where('tipo_movimiento_material_pedido.Pro_id_ubicacion', 1)
                    ->groupBy('proyectos.Pro_ID')
                    ->get();
                $material->warehouse = $warehouse;
                $total_warehouse = 0;
                foreach ($warehouse as $key => $valor) {
                    $total_warehouse += $valor->cantidad_registrada;
                }
                $material->total_warehouse = $total_warehouse;
                $movimientos = DB::table('pedidos_material')
                    ->select(
                        'proyectos.Nombre',
                        DB::raw('SUM(COALESCE(tipo_movimiento_material_pedido.ingreso,0)-COALESCE(tipo_movimiento_material_pedido.egreso,0)) as cantidad_registrada')
                    )
                    ->join('pedidos', 'pedidos.Ped_ID', 'pedidos_material.Ped_ID')
                    ->join('tipo_movimiento_material_pedido', 'tipo_movimiento_material_pedido.Ped_Mat_ID', 'pedidos_material.Ped_Mat_ID')
                    ->join('proyectos', 'proyectos.Pro_ID', 'tipo_movimiento_material_pedido.Pro_id_ubicacion')
                    ->whereDate('pedidos.Fecha', '>=', date('Y-m-d', strtotime($fecha_inicio)))
                    ->whereDate('pedidos.Fecha', '<=', date('Y-m-d', strtotime($fecha_fin)))
                    ->where('pedidos_material.Mat_ID', $material->Mat_ID)
                    ->when((intval($proyecto) == 1 ? true : false), function ($query) use ($proyecto, $data) {
                        //dump('verificacion', $data->Nombre, intval($proyecto));
                        return $query->where('tipo_movimiento_material_pedido.Pro_id_ubicacion', '!=', intval($proyecto));
                    })
                    ->when((intval($proyecto) != 1 ? true : false), function ($query) use ($proyecto, $data) {
                        //dump('verificacion', $data->Nombre, intval($proyecto));
                        return $query->where('tipo_movimiento_material_pedido.Pro_id_ubicacion', intval($proyecto));
                    })
                    ->groupBy('proyectos.Pro_ID')
                    ->get();
                $total_proyecto = 0;
                foreach ($movimientos as $key => $valor) {
                    $total_proyecto += $valor->cantidad_registrada;
                }
                $material->proyecto = $movimientos;
                $material->total_proyecto = $total_proyecto;
            }
            /////
            $data->materiales = $materiales;
            $resultado[] = $data;
        }
        return $resultado;
    }
    public function obtenerMaterialesDetalle(Request $request)
    {
        $proyectos_id = explode(',', $request->query('proyectos'));
        $materiales_id = explode(',', $request->query('materiales'));
        $view = $request->query('view');
        $detalle = $request->query('detalle');
        $fecha_inicio = $request->query('fecha_inicio');
        $fecha_fin = $request->query('fecha_fin');
        $resultado = [];
        foreach ($proyectos_id as $p => $proyecto) {
            $materiales = DB::table('materiales')
                ->join('pedidos_material', 'pedidos_material.Mat_ID', 'materiales.Mat_ID')
                ->join('pedidos', 'pedidos.Ped_ID', 'pedidos_material.Ped_ID')
                ->join('categoria_material', 'categoria_material.Cat_ID', 'materiales.Cat_ID')
                ->whereDate('pedidos.Fecha', '>=', date('Y-m-d', strtotime($fecha_inicio)))
                ->whereDate('pedidos.Fecha', '<=', date('Y-m-d', strtotime($fecha_fin)))
                ->where(function ($query) use ($proyecto) {
                    $query->where('pedidos.Pro_ID', $proyecto)
                        ->orWhere('pedidos.Ven_ID', $proyecto);
                })
                ->where('pedidos.tipo_orden_id', '!=', null)
                ->whereIn('pedidos_material.Mat_ID', $materiales_id)
                ->groupBy('materiales.Mat_ID')
                ->orderBy('materiales.Denominacion')
                ->get()
                ->toArray();
            $data = DB::table('proyectos')
                ->select(
                    'empresas.Codigo as codigo_empresa',
                    'empresas.Nombre as nombre_empresa',
                    'estatus.Nombre_Estatus as nombre_estatus',
                    'proyectos.*'
                )
                ->join('empresas', 'empresas.Emp_ID', 'proyectos.Emp_ID')
                ->join('estatus', 'estatus.Estatus_ID', 'proyectos.Estatus_ID')
                ->where('proyectos.Pro_ID', $proyecto)
                ->first();

            foreach ($materiales as $key => $material) {
                $pedidos = DB::table('pedidos_material')
                    ->select(
                        'pedidos.*',
                        'pedidos_material.*',
                        'proyectos.Nombre as nombre_proyecto'
                    )
                    ->join('pedidos', 'pedidos.Ped_ID', 'pedidos_material.Ped_ID')
                    ->join('proyectos', 'proyectos.Pro_ID', 'pedidos.Pro_ID')
                    ->whereDate('pedidos.Fecha', '>=', date('Y-m-d', strtotime($fecha_inicio)))
                    ->whereDate('pedidos.Fecha', '<=', date('Y-m-d', strtotime($fecha_fin)))
                    ->where('pedidos_material.Mat_ID', $material->Mat_ID)
                    ->where(function ($query) use ($proyecto) {
                        $query->where('pedidos.Pro_ID', $proyecto)
                            ->orWhere('pedidos.Ven_ID', $proyecto);
                    })
                    ->where('pedidos.tipo_orden_id', '!=', null)
                    ->orderBy('pedidos.Fecha', 'DESC')
                    ->groupBy('pedidos.Ped_ID')
                    ->get();
                $proyectos_total = DB::table('pedidos_material')
                    ->select(
                        'pedidos.Pro_ID',
                        'proyectos.Nombre as nombre_proyecto'
                    )
                    ->join('pedidos', 'pedidos.Ped_ID', 'pedidos_material.Ped_ID')
                    ->join('proyectos', 'proyectos.Pro_ID', 'pedidos.Pro_ID')
                    ->whereDate('pedidos.Fecha', '>=', date('Y-m-d', strtotime($fecha_inicio)))
                    ->whereDate('pedidos.Fecha', '<=', date('Y-m-d', strtotime($fecha_fin)))
                    ->where('pedidos_material.Mat_ID', $material->Mat_ID)
                    ->where(function ($query) use ($proyecto) {
                        $query->where('pedidos.Pro_ID', $proyecto)
                            ->orWhere('pedidos.Ven_ID', $proyecto);
                    })
                    ->where('pedidos.Pro_ID', '!=', 1)
                    ->where('pedidos.tipo_orden_id', '!=', null)
                    ->orderBy('pedidos.Fecha', 'DESC')
                    ->groupBy('pedidos.Pro_ID')
                    ->get();
                $total_warehouse = 0;
                $total_proyecto = 0;
                $total_cantidad = 0;
                foreach ($pedidos as $key => $pedido) {
                    $movimientos = DB::table('tipo_movimiento_material_pedido')
                        ->select(
                            DB::raw("(COALESCE(tipo_movimiento_material_pedido.ingreso,0) - COALESCE(tipo_movimiento_material_pedido.egreso,0)) as total"),
                            'proyectos.Nombre',
                            'tipo_movimiento_material_pedido.Pro_id_ubicacion'
                        )
                        ->join('pedidos_material', 'pedidos_material.Ped_Mat_ID', 'tipo_movimiento_material_pedido.Ped_Mat_ID')
                        ->join('proyectos', 'proyectos.Pro_ID', 'tipo_movimiento_material_pedido.Pro_id_ubicacion')
                        ->where('pedidos_material.Ped_ID', $pedido->Ped_ID)
                        ->where('pedidos_material.Mat_ID', $material->Mat_ID)
                        ->get();
                    /*  if ($p == 5) {
                    dd($materiales, $data, $proyecto, $pedidos, $movimientos);
                    } */
                    $m_warehouse = 0;
                    $m_proyecto = 0;
                    foreach ($movimientos as $i => $movimiento) {
                        if ($movimiento->Pro_id_ubicacion == 1) {
                            $m_warehouse += intval($movimiento->total);
                            $movimiento->total_proyecto = 0;
                            $movimiento->total_warehouse = intval($movimiento->total);
                            $m_proyecto += 0;
                        } else {
                            $m_warehouse += 0;
                            $movimiento->total_warehouse = 0;
                            $movimiento->total_proyecto = intval($movimiento->total);
                            $m_proyecto += intval($movimiento->total);
                        }
                    }

                    $pedido->total_warehouse = $m_warehouse;
                    $pedido->total_proyecto = $m_proyecto;

                    $total_warehouse += $pedido->total_warehouse;
                    $total_proyecto += $pedido->total_proyecto;
                    $total_cantidad += $pedido->Cantidad;

                    $pedido->movimientos = $movimientos;
                    //preorden
                    $pre_orden = DB::table('tipo_orden')
                        ->join('tipo_orden_materiales', 'tipo_orden_materiales.tipo_orden_id', 'tipo_orden.id')
                        ->join('tipo_orden_estatus', 'tipo_orden_estatus.id', 'tipo_orden.estatus_id')
                        ->where('tipo_orden_materiales.material_id', $material->Mat_ID)
                        ->where('tipo_orden.id', $pedido->tipo_orden_id)
                        ->first();
                    $pedido->pre_orden = $pre_orden;
                }
                $material->total_warehouse = $total_warehouse;
                $material->total_proyecto = $total_proyecto;
                $material->total_cantidad = $total_cantidad;

                ///totales
                $por_proyecto = 0;
                foreach ($proyectos_total as $key => $total) {
                    foreach ($pedidos as $key => $pedido) {
                        if ($pedido->Pro_ID != 1) {
                            $por_proyecto += $pedido->total_proyecto;
                        }
                    }
                    $total->por_proyecto = $por_proyecto;
                }
                $material->proyectos_total = $proyectos_total;
                $material->pedidos = $pedidos;
            }
            /////
            $data->materiales = $materiales;
            $resultado[] = $data;
        }
        //dd($resultado);
        return $resultado;
    }
}
