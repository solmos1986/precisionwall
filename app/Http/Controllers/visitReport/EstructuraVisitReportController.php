<?php

namespace App\Http\Controllers\visitReport;

use App\Http\Controllers\Controller;
use App\Material;
use DataTables;
use DB;
use Illuminate\Http\Request;
use Validator;

/* use DataTables;

use PDF;
use \stdClass; */

class EstructuraVisitReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function datatable()
    {
        $superficies = DB::table('estimado_superficie')
            ->orderBy('estimado_superficie.id', 'DESC')
            ->get();

        return Datatables::of($superficies)
            ->addIndexColumn()
            ->addColumn('acciones', function ($data) {
                $button = "
                <i class='fas fa-pencil-alt ms-text-warning cursor-pointer edit-superficie' title='Edit Surface' data-superficie_id='$data->id'></i>
                <i class='far fa-trash-alt ms-text-danger delete-superficie cursor-pointer' data-superficie_id='$data->id' title='Delete Surface'></i>
                ";
                return $button;
            })
            ->addColumn('miselaneo', function ($data) {
                $button = "";
                if ($data->miselaneo == 'y') {
                    $button = "
                    <span class='badge badge-pill badge-primary'>Yes</span>
                    ";
                }
                return $button;
            })
            ->rawColumns(['acciones', 'miselaneo'])
            ->make(true);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $status = DB::table('estatus')->select('estatus.*')->get();
        $proyectos = DB::table('proyectos')
            ->select('proyectos.*')
            ->get();
        return view('panel.goal.estructura.list', compact('status', 'proyectos'));
    }

    //superficies
    public function list_superficies($id)
    {
        return response()->json([
            'status' => 'ok',
            'data' => [
                'superficies' => $this->view_superficie($id),
                'proyecto_id' => $id,
            ],
        ], 200);
    }
    public function save_superficie(Request $request)
    {
        $rules = array(
            'nombre' => 'required',
            'codigo' => 'required',
            'descripcion' => 'nullable',
            'proyecto_id' => 'required',
        );
        $messages = [
            'nombre.required' => "The Name field is required",
            'codigo.required' => "The Code field is required",
        ];
        $error = Validator::make($request->all(), $rules, $messages);
        if ($error->errors()->all()) {
            return response()->json([
                'status' => 'errors',
                'message' => $error->errors()->all(),
            ]);
        }
        $superficie_id = DB::table('visit_report_superficie')
            ->insertGetId([
                'nombre' => $request->nombre,
                'codigo' => $request->codigo,
                'descripcion' => $request->descripcion == null ? '' : $request->descripcion,
                'proyecto_id' => $request->proyecto_id,
            ]);
        return response()->json([
            'status' => 'ok',
            'data' => [
                'superficies' => $this->view_superficie($request->proyecto_id),
                'proyecto_id' => $request->proyecto_id,
            ],
            'message' => 'Surface saved successfully',
        ], 200);
    }
    public function update_superficie(Request $request, $id)
    {
        $rules = array(
            'nombre' => 'required',
            'codigo' => 'required',
            'descripcion' => 'nullable',
            'proyecto_id' => 'required',
        );
        $messages = [
            'nombre.required' => "The Name field is required",
            'codigo.required' => "The Code field is required",
        ];
        $error = Validator::make($request->all(), $rules, $messages);
        if ($error->errors()->all()) {
            return response()->json([
                'status' => 'errors',
                'message' => $error->errors()->all(),
            ]);
        }
        $superficie_id = DB::table('visit_report_superficie')
            ->where('visit_report_superficie.id', $id)
            ->update([
                'nombre' => $request->nombre,
                'codigo' => $request->codigo,
                'descripcion' => $request->descripcion == null ? '' : $request->descripcion,
            ]);
        return response()->json([
            'status' => 'ok',
            'data' => [
                'superficies' => $this->view_superficie($request->proyecto_id),
                'proyecto_id' => $request->proyecto_id,
            ],
            'message' => 'Successfully modified surface',
        ], 200);
    }
    public function delete_superficie(Request $request, $id)
    {
        $proyecto = DB::table('visit_report_superficie')
            ->where('visit_report_superficie.id', $id)
            ->first();
        $superficie_id = DB::table('visit_report_superficie')
            ->where('visit_report_superficie.id', $id)
            ->delete();
        return response()->json([
            'status' => 'ok',
            'data' => [
                'superficies' => $this->view_superficie($proyecto->proyecto_id),
                'proyecto_id' => $proyecto->proyecto_id,
            ],
            'message' => 'Successfully delete surface',
        ], 200);
    }
    private function view_superficie($proyecto_id)
    {
        $lista_superficies = DB::table('visit_report_superficie')
            ->select(
                'visit_report_superficie.*'
            )
            ->where('visit_report_superficie.proyecto_id', $proyecto_id)
            ->get();
        return $lista_superficies;
    }

    //materiales
    public function list_materiales($id)
    {
        $superficie = DB::table('estimado_superficie')
            ->where('estimado_superficie.id', $id)
            ->first();
        $metodos = DB::table('estimado_estandar')
            ->where('estimado_estandar.estimado_superficie_id', $id)
            ->get();
        return response()->json([
            'status' => 'ok',
            'data' => [
                'materiales' => $this->view_materiales($id),
                'superficie_id' => $id,
                'superficie' => $superficie,
                'estandares' => $metodos,
            ],
        ], 200);
    }

    private function validar_array($array, $validador)
    {
        $resultado = true;
        for ($i = 0; $i < count($array); $i++) {
            if ($array[$i] == null || $array[$i] < 0) {
                $resultado = false;
                break;
            }
        }
        return $resultado;
    }
    public function save_material(Request $request)
    {
        //dd($request->all());
        $rules = array(
            'material_id' => 'array|required',
            'quantity' => 'array|required',
        );
        $messages = [
            'material_id.array' => "Add materials and complete changes",
            'material_id.required' => "Add materials and complete changes",
            'quantity.array' => "Quantity required",
            'quantity.required' => "Quantity required",
        ];
        $error = Validator::make($request->all(), $rules, $messages);
        if ($error->errors()->all()) {
            return response()->json([
                'status' => 'errors',
                'message' => $error->errors()->all(),
            ]);
        } else {
            if ($this->validar_array($request->quantity, 0) == false) {
                return response()->json([
                    "status" => "errors",
                    "message" => ['the quantity of materials must be greater than 0'],
                ], 200);
            }
            $add_material = DB::table('visit_report_material')
                ->where('visit_report_material.superficie_id', $request->view_superficie_id)
                ->delete();
            foreach ($request->material_id as $key => $material_id) {
                $add_material = DB::table('visit_report_material')
                    ->insertGetId([
                        'material_id' => $material_id,
                        'nota' => '',
                        'cantidad' => $request->quantity[$key],
                        'superficie_id' => $request->view_superficie_id,
                    ]);
            }
            return response()->json([
                'status' => 'ok',
                'data' => [
                    'materiales' => $this->view_materiales($request->superficie_id),
                    'superficie_id' => $request->superficie_id,
                ],
                'message' => 'Material saved correctly',
            ], 200);
        }

    }
    private function view_materiales($superficie_id)
    {
        $lista_materiales = DB::table('visit_report_material')
            ->select(
                'visit_report_material.*',
                'materiales.Denominacion',
                'materiales.Unidad_Medida',
                'categoria_material.Nombre as nombre_categoria'
            )
            ->join('materiales', 'materiales.Mat_ID', 'visit_report_material.material_id')
            ->join('categoria_material', 'categoria_material.Cat_ID', 'materiales.Cat_ID')
            ->where('visit_report_material.superficie_id', $superficie_id)
            ->get();
        return $lista_materiales;
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
        //dd($request->all());
        $herramientas = DB::table('visit_material_herramienta')
            ->insert([
                'material_id' => $request->nuevo_material,
                'cantidad' => $request->nuevo_quantity,
                'superficie_id' => $request->nuevo_superficie_id,
                'proyecto_id' => $request->nuevo_proyecto_id,
            ]);
        $lista = $this->view_superficie($request->nuevo_superficie_id);

        return response()->json([
            'status' => 'ok',
            'data' => [
                'list_materiales' => $lista,
                'superficie_id' => $request->nuevo_superficie_id,
            ],
            'message' => 'data',
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
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
                "Pro_ID" => $row->Pro_ID,
            );
        }
        return response()->json($data);
    }
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
    //select
    public function select_proyectos(Request $request)
    {
        if (!isset($request->searchTerm)) {
            $proyectos = DB::table('proyectos')
                ->select(
                    'proyectos.*',
                    'proyectos.Codigo',
                    'empresas.Codigo as empresa',
                    'empresas.Emp_ID',
                    DB::raw("CONCAT( proyectos.Estado,' ', proyectos.Ciudad,' ',proyectos.Calle,' ', proyectos.Numero) as dirrecion")
                )
                ->join('empresas', 'proyectos.Emp_ID', 'empresas.Emp_ID')
                ->distinct('proyectos.Pro_ID')
                ->get();
        } else {
            $proyectos = DB::table('proyectos')
                ->select(
                    'proyectos.*',
                    'proyectos.Codigo',
                    'empresas.Codigo as empresa',
                    'empresas.Emp_ID',
                    DB::raw("CONCAT( proyectos.Estado,' ', proyectos.Ciudad,' ',proyectos.Calle,' ', proyectos.Numero) as dirrecion")
                )
                ->where('proyectos.Nombre', 'like', '%' . $request->searchTerm . '%')
                ->orderBy('proyectos.Estatus_ID', 'ASC')
                ->join('empresas', 'proyectos.Emp_ID', 'empresas.Emp_ID')
                ->get();
        }
        $data = [];
        foreach ($proyectos as $row) {
            $data[] = array(
                "id" => $row->Pro_ID,
                "text" => $row->Nombre,
                "emp" => $row->empresa,
                "emp_id" => $row->Emp_ID,
                "Codigo" => $row->Codigo,
                "dirrecion" => $row->dirrecion,
            );
        }
        return response()->json($data);
    }
    public function delete_material($id)
    {
        $herramientas = DB::table('visit_report_material')
            ->where('visit_report_material.id', $id)
            ->delete();
        return response()->json([
            'status' => 'ok',
            'message' => 'Material removed correctly',
        ], 200);
    }
}
