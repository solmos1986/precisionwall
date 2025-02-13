<?php

namespace App\Http\Controllers\Submittals;

use App\Exports\reportSubmittals\resumeSubmittals;
use App\Http\Controllers\Controller;
use DataTables;
use DB;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Excel;
use \stdClass;

/* use Image;
use PDF;
use Validator;
 */
class SubmittalsController extends Controller
{
    private $excel;
    public function __construct(Excel $excel)
    {
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
        //parametros exterior
        $proyecto_id = request()->proyecto_id;
        $status_materiales = DB::table('categoria_material')->select('categoria_material.*')->get();
        $status_proyecto = DB::table('estatus')->select('estatus.*')->get();
        $proyectos = DB::table('proyectos')
            ->select('proyectos.*')
        /* ->where('proyectos.Estatus_ID',1) */
            ->get();
        return view('panel.submittals.list', compact('proyectos', 'status_proyecto', 'status_materiales', 'proyecto_id'));
    }

    public function datatable(Request $request)
    {
        //dd($request->query());
        $data = DB::table('materiales')->select(
            'proyectos.Codigo',
            'materiales.*',
            'proyectos.Nombre as nombre_proyecto',
            DB::raw('DATE_FORMAT(materiales.Fecha_from_vendor , "%m/%d/%Y") as Fecha_from_vendor'),
            DB::raw('DATE_FORMAT(materiales.Fecha_to_vendor , "%m/%d/%Y") as Fecha_to_vendor'),
            DB::raw('DATE_FORMAT(materiales.Fecha_from_gc , "%m/%d/%Y") as Fecha_from_gc'),
            DB::raw('DATE_FORMAT(materiales.Fecha_to_gc , "%m/%d/%Y") as Fecha_to_gc'),
            'categoria_material.Nombre as nombre_categoria',
            'vendedor.Nombre as nombre_proveedor',
            //DB::raw('SUM(pedidos_material.Cantidad) as cantidad_ordenada')
        )
            ->when((request()->date_from_vendor), function ($query) {
                //dump('aki date_from_vendor');
                return $query->where('materiales.Fecha_from_vendor', '=', date('Y-m-d', strtotime(request()->date_from_vendor)));
            })
            ->when((request()->date_to_vendor), function ($query) {
                //dump('aki date_to_vendor');
                return $query->where('materiales.Fecha_to_vendor', '=', date('Y-m-d', strtotime(request()->date_to_vendor)));
            })
            ->when((request()->date_from_gc), function ($query) {
                //dump('aki date_from_gc');
                return $query->where('materiales.Fecha_from_gc', '=', date('Y-m-d', strtotime(request()->date_from_gc)));
            })
            ->when((request()->date_to_gc), function ($query) {
                //dump('aki date_to_gc');
                return $query->where('materiales.Fecha_to_gc', '=', date('Y-m-d', strtotime(request()->date_to_gc)));
            })
            ->when(request()->status_submittals, function ($query) {
                $status = explode(',', request()->status_submittals);
                //dump('aki status_submittals',$status);
                return $query->whereIn('materiales.Cat_ID', $status);
            })
            ->when($request->query('proyectos'), function ($query) use ($request) {
                $proyectos = explode(',', $request->query('proyectos'));
                //dump('aki proyectos');
                return $query->whereIn('proyectos.Pro_ID', $proyectos);
            })
            ->when($request->query('status_proyecto'), function ($query) use ($request) {
                $proyectos_status = explode(',', request()->status_proyecto);
                //dump('aki proyectos_status',$proyectos_status);
                return $query->whereIn('proyectos.Estatus_ID', $proyectos_status);
            })
            ->join('proyectos', 'proyectos.Pro_ID', 'materiales.Pro_ID')
            ->join('estatus', 'proyectos.Estatus_ID', 'estatus.Estatus_ID')
            ->join('categoria_material', 'categoria_material.Cat_ID', 'materiales.Cat_ID')
        //->leftJoin('pedidos_material', 'pedidos_material.Mat_ID', 'materiales.Mat_ID')
            ->leftJoin('proyectos as vendedor', function ($query) {
                $query->on('vendedor.Pro_ID', '=', 'materiales.Ven_ID')
                    ->where('vendedor.Emp_ID', '=', '119');
            })
            ->orderBy('proyectos.Nombre', 'asc')
            ->groupBy('materiales.Mat_ID')
            ->get();
        foreach ($data as $key => $value) {
            $total_ordenado = DB::table('pedidos_material')
                ->select(
                    DB::raw('SUM(pedidos_material.Cantidad) as cantidad_ordenada')
                )
                ->join('pedidos', 'pedidos.Ped_ID', 'pedidos_material.Ped_ID')
                ->where('pedidos.Pro_ID', $value->Pro_ID)
                ->where('pedidos_material.Mat_ID', $value->Mat_ID)
                ->first();
            $value->cantidad_ordenada = $total_ordenado->cantidad_ordenada;
        }

        
        return Datatables::of($data)
            ->addIndexColumn()
            ->addColumn('actions', function ($data) {
                $button = "
                <i class='fas fa-pencil-alt ms-text-warning view_proyecto cursor-pointer' data-id='$data->Pro_ID'></i>";
                return $button;
            })
            ->addColumn('cantidad_ordenada', function ($data) {
                /* $total_ordenado = DB::table('pedidos_material')
            ->select(
            DB::raw('SUM(pedidos_material.Cantidad) as cantidad_ordenada')
            )
            ->join('pedidos', 'pedidos.Ped_ID', 'pedidos_material.Ped_ID')
            ->where('pedidos.Pro_ID', $data->Pro_ID)
            ->where('pedidos_material.Mat_ID', $data->Mat_ID)
            ->first();
            return $total_ordenado->cantidad_ordenada; */
              /*   $total_ordenado = DB::table('pedidos_material')
                    ->select(
                        DB::raw('SUM(pedidos_material.Cantidad) as cantidad_ordenada')
                    )
                    ->where('pedidos_material.Mat_ID', $data->Mat_ID)
                    ->first();
                return $total_ordenado->cantidad_ordenada; */
                return $data->cantidad_ordenada;
            })
            ->rawColumns(['actions','cantidad_ordenada'])
            ->make(true);
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
        $materiales = DB::table('materiales')->insertGetId([
            'Pro_ID' => $request->Pro_ID,
            'Ven_ID' => $request->Ven_ID,
            'Cat_ID' => $request->Cat_ID,
            'Denominacion' => $request->Denominacion,
            'Nombre_Generico' => $request->Nombre_Generico,
            'Area_donde_va' => $request->Area_donde_va,
            'Unidad_Medida' => $request->Unidad_Medida,
            'Cantidad' => $request->Cantidad,
            'Precio_Unitario' => $request->Precio_Unitario,
            'Precio' => $request->Precio,
            'Aux1' => $request->Aux1,
            'Aux2' => $request->Aux2,
            'Aux3' => $request->Aux3,
            'Fecha_Registro' => date('Y-m-d'),
            'Fecha_Envio' => $request->Fecha_Envio,
            'Fecha_Recibido' => $request->Fecha_Recibido,
            'Fecha_from_vendor' => $request->Fecha_from_vendor,
            'Fecha_to_vendor' => $request->Fecha_to_vendor,
            'note_vendor' => $request->note_vendor,
            'Fecha_from_gc' => $request->Fecha_from_gc,
            'Fecha_to_gc' => $request->Fecha_to_gc,
            'note_gc' => $request->note_gc,
        ]);
        return response()->json([
            'status' => 'ok',
            'message' => 'successfully modified',
            'data' => null,
        ], 200);
    }

    public function select_tipo_materiales(Request $request)
    {
        if (!isset($request->searchTerm)) {
            $proyectos = DB::table('categoria_material')
                ->select(
                    'categoria_material.*'
                )
                ->orderBy('categoria_material.Cat_ID', 'ASC')
                ->get();
        } else {
            $proyectos = DB::table('categoria_material')
                ->select(
                    'categoria_material.*'
                )
                ->where('categoria_material.Nombre', 'like', '%' . $request->searchTerm . '%')
                ->orderBy('categoria_material.Cat_ID', 'ASC')
                ->get();
        }
        $data = [];
        foreach ($proyectos as $row) {
            $data[] = array(
                "id" => $row->Cat_ID,
                "text" => $row->Nombre,
            );
        }
        return response()->json($data);
    }
    public function select_proveedor(Request $request)
    {
        if (!isset($request->searchTerm)) {
            $proyectos = DB::table('proyectos')
                ->select(
                    'proyectos.*'
                )
                ->where('Emp_ID', 119)
                ->orderBy('proyectos.Nombre', 'ASC')
                ->get();
        } else {
            $proyectos = DB::table('proyectos')
                ->select(
                    'proyectos.*'
                )
                ->where('Emp_ID', 119)
                ->where('proyectos.Nombre', 'like', '%' . $request->searchTerm . '%')
                ->orderBy('proyectos.Nombre', 'ASC')
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
        $materiales = DB::table('materiales')
            ->where('Mat_ID', $id)->update(
            [
                'Pro_ID' => $request->Pro_ID,
                'Ven_ID' => $request->Ven_ID,
                'Cat_ID' => $request->Cat_ID,
                'Denominacion' => $request->Denominacion,
                'Nombre_Generico' => $request->Nombre_Generico,
                'Area_donde_va' => $request->Area_donde_va,
                'Unidad_Medida' => $request->Unidad_Medida,
                'Cantidad' => $request->Cantidad,
                'Precio_Unitario' => $request->Precio_Unitario,
                'Precio' => $request->Precio,
                'Aux1' => $request->Aux1,
                'Aux2' => $request->Aux2,
                'Aux3' => $request->Aux3,
                'Fecha_Envio' => $request->Fecha_Envio,
                'Fecha_Recibido' => $request->Fecha_Recibido,
                'Fecha_from_vendor' => $request->Fecha_from_vendor == null ? null : date('Y-m-d', strtotime($request->Fecha_from_vendor)),
                'Fecha_to_vendor' => $request->Fecha_to_vendor == null ? null : date('Y-m-d', strtotime($request->Fecha_to_vendor)),
                'note_vendor' => $request->note_vendor,
                'Fecha_from_gc' => $request->Fecha_from_gc == null ? null : date('Y-m-d', strtotime($request->Fecha_from_gc)),
                'Fecha_to_gc' => $request->Fecha_to_gc == null ? null : date('Y-m-d', strtotime($request->Fecha_to_gc)),
                'note_gc' => $request->note_gc,
            ]);
        return response()->json([
            'status' => 'ok',
            'message' => 'successfully modified',
            'data' => null,
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $materiales = DB::table('tipo_orden_materiales')
            ->where('material_id', $id)
            ->get();
        $pedidos = DB::table('pedidos_material')
            ->where('Mat_ID', $id)
            ->get();
        //dd(count($materiales), count($pedidos));
        if (count($materiales) == 0 && count($pedidos) == 0) {
            $delete = DB::table('materiales')
                ->where('Mat_ID', $id)
                ->delete();

            return response()->json([
                'status' => 'ok',
                'message' => 'Successfully removed',
                'data' => null,
            ], 200);

        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Cannot be deleted because it is in use',
                'data' => null,
            ], 200);
        }
    }
    private function reconstruir_consulta(Request $request)
    {
        $datos = DB::table('materiales')->select(
            'proyectos.Codigo',
            'proyectos.Nombre as nombre_proyecto',
            'materiales.Denominacion',
            'materiales.Unidad_Medida',
            'categoria_material.Nombre as nombre_categoria',
            'vendedor.Nombre as nombre_proveedor',
            'materiales.Cantidad',
            'materiales.Precio_Unitario',
            DB::raw('DATE_FORMAT(materiales.Fecha_from_vendor , "%m/%d/%Y") as Fecha_from_vendor'),
            DB::raw('DATE_FORMAT(materiales.Fecha_to_vendor , "%m/%d/%Y") as Fecha_to_vendor'),
            'materiales.note_vendor',
            DB::raw('DATE_FORMAT(materiales.Fecha_from_gc , "%m/%d/%Y") as Fecha_from_gc'),
            DB::raw('DATE_FORMAT(materiales.Fecha_to_gc , "%m/%d/%Y") as Fecha_to_gc'),
            'materiales.note_gc'
        )
            ->when((request()->date_from_vendor), function ($query) {
                //dump('aki date_from_vendor');
                return $query->where('materiales.Fecha_from_vendor', '=', date('Y-m-d', strtotime(request()->date_from_vendor)));
            })
            ->when((request()->date_to_vendor), function ($query) {
                //dump('aki date_to_vendor');
                return $query->where('materiales.Fecha_to_vendor', '=', date('Y-m-d', strtotime(request()->date_to_vendor)));
            })
            ->when((request()->date_from_gc), function ($query) {
                //dump('aki date_from_gc');
                return $query->where('materiales.Fecha_from_gc', '=', date('Y-m-d', strtotime(request()->date_from_gc)));
            })
            ->when((request()->date_to_gc), function ($query) {
                //dump('aki date_to_gc');
                return $query->where('materiales.Fecha_to_gc', '=', date('Y-m-d', strtotime(request()->date_to_gc)));
            })
            ->when(request()->status_submittals, function ($query) {
                $status = explode(',', request()->status_submittals);
                //dump('aki status_submittals',$status);
                return $query->whereIn('materiales.Cat_ID', $status);
            })
            ->when($request->query('proyectos'), function ($query) use ($request) {
                $proyectos = explode(',', $request->query('proyectos'));
                //dump('aki proyectos');
                return $query->whereIn('proyectos.Pro_ID', $proyectos);
            })
            ->when($request->query('status_proyecto'), function ($query) use ($request) {
                $proyectos_status = explode(',', request()->status_proyecto);
                //dump('aki proyectos_status',$proyectos_status);
                return $query->whereIn('proyectos.Estatus_ID', $proyectos_status);
            })
            ->join('proyectos', 'proyectos.Pro_ID', 'materiales.Pro_ID')
            ->join('estatus', 'proyectos.Estatus_ID', 'estatus.Estatus_ID')
            ->join('categoria_material', 'categoria_material.Cat_ID', 'materiales.Cat_ID')
            ->join('vendedor', 'vendedor.Ven_ID', 'materiales.Ven_ID')
            ->orderBy('materiales.Pro_ID', 'asc')
            ->orderBy('materiales.Cat_ID', 'asc')
            ->groupBy('materiales.Mat_ID')
            ->get()->toArray();

        return $datos;
    }
    public function report_excel(Request $request)
    {
        $proyectos = $this->reconstruir_consulta($request);

        $resultado = [];
        //recostruir informacion
        $division = (count($proyectos) > 0) ? $proyectos[0]->nombre_proyecto : "";
        $contador = 1;
        foreach ($proyectos as $key => $proyecto) {
            $data = new stdClass();
            if ($division != $proyecto->nombre_proyecto) {
                //construir saltos
                $blank = new stdClass();
                $blank->Codigo = "";
                $blank->nombre_proyecto = "";
                $blank->num = "";
                $blank->Denominacion = "";
                $blank->Unidad_Medida = "";
                $blank->nombre_categoria = "";
                $blank->nombre_proveedor = "";
                $blank->Cantidad = "";
                $blank->Precio_Unitario = "";
                $blank->Fecha_from_vendor = "";
                $blank->Fecha_to_vendor = "";
                $blank->note_vendor = "";
                $blank->Fecha_from_gc = "";
                $blank->Fecha_to_gc = "";
                $blank->note_gc = "";

                $resultado[] = $blank;
                $contador = 1;
                $division = $proyecto->nombre_proyecto;
            }
            $data->Codigo = $proyecto->Codigo;
            $data->nombre_proyecto = $proyecto->nombre_proyecto;
            $data->num = $contador;
            $data->Denominacion = $proyecto->Denominacion;
            $data->Unidad_Medida = $proyecto->Unidad_Medida;
            $data->nombre_categoria = $proyecto->nombre_categoria;
            $data->nombre_proveedor = $proyecto->nombre_proveedor;
            $data->Cantidad = $proyecto->Cantidad;
            $data->Precio_Unitario = $proyecto->Precio_Unitario;
            $data->Fecha_from_vendor = $proyecto->Fecha_from_vendor;
            $data->Fecha_to_vendor = $proyecto->Fecha_to_vendor;
            $data->note_vendor = $proyecto->note_vendor;
            $data->Fecha_from_gc = $proyecto->Fecha_from_gc;
            $data->Fecha_to_gc = $proyecto->Fecha_to_gc;
            $data->note_gc = $proyecto->note_gc;
            $resultado[] = $data;

            $division = $proyecto->nombre_proyecto;
            $contador++;

        }
        return $this->excel->download(new resumeSubmittals($resultado, date('m-d-Y'), date('m-d-Y')), "Summary Submittals " . date('m-d-Y') . ".xlsx");
    }
}
