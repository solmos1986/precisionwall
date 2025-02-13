<?php

namespace App\Http\Controllers;

use DataTables;
use Validator;
use App\Evaluaciones;
use App\Personal_evaluaciones;
use App\Personal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Model\Form_Respuesta;
use App\Respuesta_Personal_Evaluacion;
use App\Model\Form_Seccion;
use App\Model\Form_Pregunta;
use \stdClass;

class Personal_evaluacionesController extends Controller
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
    public function list_evaluar()
    {
        if (request()->ajax()) {
            $evaluacion = Evaluaciones::select(
                'evaluaciones.evaluacion_id',
                'evaluaciones.note',
                DB::raw("(SELECT COUNT(personal_evaluaciones.Empleado_ID )FROM personal_evaluaciones WHERE personal_evaluaciones.evaluacion_id=evaluaciones.evaluacion_id and personal_evaluaciones.estado='1') as personal_evaluar"),
                DB::raw('DATE_FORMAT(evaluaciones.fecha_asignacion, "%m/%d/%Y") as fecha_asignacion')
            )
            ->where('evaluaciones.estado', '1')
            ->where('evaluaciones.foreman_id', auth()->user()->Empleado_ID)
            ->orderBy('evaluaciones.fecha_asignacion', 'asc')
            ->get();
            //dd(auth()->user()->Empleado_ID);
            return Datatables::of($evaluacion)
            ->addIndexColumn()
            ->addColumn('acciones', function ($evaluacion) {
                $button ="<a href='" . route('list.personal.evaluar', ['id' => $evaluacion->evaluacion_id]) . "'><i class='fas fa-eye ms-text-primary'></i></a>";

                return $button;
            })
            ->addColumn('ver_usuarios', function ($evaluacion) {
                $html =  "<span class='badge badge-success'> $evaluacion->personal_evaluar assigned users </span>";
                return $html;
            })
            ->addColumn('status', function ($evaluacion) {
                if (date('Y-m-d', strtotime($evaluacion->fecha_asignacion))==date('Y-m-d')) {
                    $html =  "<span class='badge badge-success'> pending </span>";
                } else {
                    $html =  "<span class='badge badge-success'> time out </span>";
                }
                return $html;
            })
            ->rawColumns(['ver_usuarios','acciones','status'])
            ->make(true);
        }
        return view('panel.evaluacion-formulario.evaluar.list');
    }
    public function index($id)
    {
        $evaluacion = Evaluaciones::select(
            'evaluaciones.evaluacion_id',
            'evaluaciones.note',
            'form_formulario.titulo',
            DB::raw('CONCAT(personal.Nombre," ",personal.Apellido_Paterno," ",personal.Apellido_Materno) AS nombre_foreman'),
            DB::raw('DATE_FORMAT(evaluaciones.fecha_asignacion, "%m/%d/%Y") as fecha_asignacion')
        )
        ->where('evaluaciones.estado', '1')
        ->where('evaluaciones.evaluacion_id', $id)
        ->join('form_formulario', 'form_formulario.formulario_id', 'evaluaciones.formulario_id')
        ->join('personal', 'personal.Empleado_ID', 'evaluaciones.foreman_id')
        ->orderBy('evaluaciones.fecha_asignacion', 'asc')
        ->firstOrFail();
       
        if (request()->ajax()) {
            $personal = Personal_evaluaciones::select(
                'personal_evaluaciones.personal_evaluaciones_id',
                'personal_evaluaciones.estado_formulario',
                'personal.Empleado_ID',
                'personal.Nick_Name',
                DB::raw('CONCAT(personal.Nombre," ",personal.Apellido_Paterno," ",personal.Apellido_Materno) AS nombre_completo'),
                'personal.Cargo'
            )
            ->join('personal', 'personal.Empleado_ID', 'personal_evaluaciones.Empleado_ID')
            ->where('personal_evaluaciones.evaluacion_id', $evaluacion->evaluacion_id)
            ->where('personal_evaluaciones.estado', '1')
            ->where('personal.status', '1')
            ->orderBy('personal.Nombre', 'ASC')
            ->get();
            return Datatables::of($personal)
                ->addIndexColumn()
                ->addColumn('acciones', function ($personal) {
                    $button ="<a href='" . route('show.complete.form', ['id' => $personal->personal_evaluaciones_id]) . "'  target='_blank'><i class='fas fa-eye ms-text-primary' title='View'></i></a>";
                    $button .= "<i class='far fa-file ms-text-primary resultado' data-id='$personal->personal_evaluaciones_id' title='View note'></i>";
                    $button .= "<i class='fa fa-trash-alt ms-text-danger delete cursor-pointer' data-id='$personal->personal_evaluaciones_id' title='Delete'></i>";
                    return $button;
                })
                ->addColumn('status', function ($personal) {
                    if ($personal->estado_formulario=='1') {
                        $html =  "<span class='badge badge-success'> complete </span>";
                    } else {
                        $html =  "<span class='badge badge-success'> incomplete </span>";
                    }
                    return $html;
                })
                ->rawColumns(['acciones','status'])
                ->make(true);
        }
        return view('panel.evaluacion-formulario.evaluaciones.list-personal-evaluacion', compact('evaluacion'));
    }

    public function lista_personal($id)
    {
        $evaluacion = Evaluaciones::select(
            'evaluaciones.evaluacion_id',
            'evaluaciones.note',
            'form_formulario.titulo',
            DB::raw('CONCAT(personal.Nombre," ",personal.Apellido_Paterno," ",personal.Apellido_Materno) AS nombre_foreman'),
            DB::raw('DATE_FORMAT(evaluaciones.fecha_asignacion, "%m/%d/%Y") as fecha_asignacion')
        )
        ->where('evaluaciones.estado', '1')
        ->where('evaluaciones.evaluacion_id', $id)
        ->join('personal', 'personal.Empleado_ID', 'evaluaciones.foreman_id')
        ->join('form_formulario', 'form_formulario.formulario_id', 'evaluaciones.formulario_id')
        ->orderBy('evaluaciones.fecha_asignacion', 'asc')
        ->firstOrFail();
       
        if (request()->ajax()) {
            $personal = Personal_evaluaciones::select(
                'personal_evaluaciones.estado_formulario',
                'personal_evaluaciones.evaluacion_id',
                'personal.Empleado_ID',
                'personal.Nick_Name',
                'evaluaciones.fecha_asignacion',
                DB::raw('CONCAT(personal.Nombre," ",personal.Apellido_Paterno," ",personal.Apellido_Materno) AS nombre_completo'),
                'personal.Cargo'
            )
            ->join('personal', 'personal.Empleado_ID', 'personal_evaluaciones.Empleado_ID')
            ->join('evaluaciones', 'evaluaciones.evaluacion_id', 'personal_evaluaciones.evaluacion_id')
            ->where('personal_evaluaciones.evaluacion_id', $evaluacion->evaluacion_id)
            ->where('personal.status', '1')
            ->orderBy('personal.Nombre', 'ASC')
            ->get();
            return Datatables::of($personal)
                ->addIndexColumn()
                ->addColumn('acciones', function ($personal) {
                    $button = "";
                    //validar un tiempo de expiracion
                    if ((date('Y-m-d', strtotime($personal->fecha_asignacion))==date('Y-m-d')) && ($personal->estado_formulario=='0')) {
                        $button = "<a href='" . route('show.evaluar', ['id' => $personal->Empleado_ID,'eval' => $personal->evaluacion_id]) . "'><i class='fas fa-pencil-alt ms-text-warning' title='Edit'></i></a>";
                    }
                    return $button;
                })
                ->addColumn('status', function ($personal) {
                    if ($personal->estado_formulario=='1') {
                        $html =  "<span class='badge badge-success'> complete </span>";
                    } else {
                        $html =  "<span class='badge badge-success'> incomplete </span>";
                    }
                    return $html;
                })
                ->rawColumns(['acciones','status'])
                ->make(true);
        }
        return view('panel.evaluacion-formulario.evaluar.list-personal', compact('evaluacion'));
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
        //escarbando en el request
        $Empleado_ID=$request->Empleado_ID;
        foreach ($request->secciones as $seccion) {
            foreach ($seccion["preguntas"] as $pregunta) {
                foreach ($pregunta["respuestas"] as $respuesta) {
                    if ($respuesta["val"]===null) {
                        $insert_respuesta=Respuesta_Personal_Evaluacion::insertGetId([
                            'form_respuesta_id'=>$respuesta["form_respuesta_id"],
                            'personal_evaluaciones_id'=>$request->personal_evaluaciones_id,
                            'respuesta'=>"ok",
                            'estado'=>"1"
                        ]);
                    } else {
                        $insert_respuesta=Respuesta_Personal_Evaluacion::insertGetId([
                            'form_respuesta_id'=>$respuesta["form_respuesta_id"],
                            'personal_evaluaciones_id'=>$request->personal_evaluaciones_id,
                            'respuesta'=>$respuesta["val"],
                            'estado'=>"1"
                        ]);
                    }
                }
            }
        }
        $update_evaluacion=Personal_evaluaciones::where('personal_evaluaciones_id', $request->personal_evaluaciones_id)->update([
            'estado_formulario'=>'1'
        ]);
        return response()->json(['success' => ["form saved successfully"]]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id, $eval)
    {
        $personal = Personal_evaluaciones::select(
            'personal_evaluaciones.personal_evaluaciones_id',
            'personal.Empleado_ID',
            DB::raw('CONCAT(personal.Nombre," ",personal.Apellido_Paterno," ",personal.Apellido_Materno) AS nombre_completo'),
            'personal_evaluaciones.evaluacion_id',
            'evaluaciones.formulario_id'
        )
        ->where('personal_evaluaciones.Empleado_ID', $id)
        ->where('personal_evaluaciones.evaluacion_id', $eval)
        ->join('personal', 'personal.Empleado_ID', 'personal_evaluaciones.Empleado_ID')
        ->join('evaluaciones', 'evaluaciones.evaluacion_id', 'personal_evaluaciones.evaluacion_id')
        ->where('personal_evaluaciones.estado_formulario', '0')
        ->firstOrFail();
        $formularioId =$personal->formulario_id;
        return view('panel.evaluacion-formulario.evaluar.show', compact('formularioId', 'personal'));
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
        $eliminar=Personal_evaluaciones::findOrFail($id)
        ->where('personal_evaluaciones_id', $id)->update([
            "estado"=>"0"
        ]);
        return response()->json(["success"=>"evaluation delete successfully"]);
    }
    

    /**
     * obtener_datos tipo box y escalar
     *
     * @param  mixed $seccion_id
     * @param  mixed $personal_evaluado_id
     * @return void
     */
    public function view_resultado($id)
    {
        $personal_datos=Evaluaciones::select(
            DB::raw('CONCAT(personal.Nombre," ",personal.Apellido_Paterno," ",personal.Apellido_Materno) AS nombre_completo'),
            'form_formulario.titulo',
            'evaluaciones.note',
            DB::raw('DATE_FORMAT(evaluaciones.fecha_asignacion, "%m/%d/%Y") as fecha_asignacion'),
            'form_formulario.formulario_id',
            'personal_evaluaciones.estado_formulario'
        )
        ->where('personal_evaluaciones.personal_evaluaciones_id', $id)
        ->where('personal_evaluaciones.estado', '1')
        ->join('personal_evaluaciones', 'personal_evaluaciones.evaluacion_id', 'evaluaciones.evaluacion_id')
        ->join('personal', 'personal_evaluaciones.Empleado_ID', 'personal.Empleado_ID')
        ->join('form_formulario', 'form_formulario.formulario_id', 'evaluaciones.formulario_id')
        ->firstOrFail();

        $secciones=Form_Seccion::select(
            'form_seccion.subtitulo',
            'form_seccion.descripcion',
            'form_seccion.form_seccion_id'
        )->where('form_formulario.formulario_id', $personal_datos->formulario_id)
        ->join('form_formulario', 'form_formulario.formulario_id', 'form_seccion.formulario_id')->get();
        $resultado=new stdClass();
        $resultado->nombre=$personal_datos->nombre_completo;
        $resultado->titulo=$personal_datos->titulo;
        $resultado->fecha=$personal_datos->fecha_asignacion;
        //dd($secciones);
        foreach ($secciones as $seccion) {
            $res_seccion=new stdClass();
            $res_seccion->subtitulo=$seccion->subtitulo;
            $res_seccion->descripcion=$seccion->descripcion;
            $res_seccion->preguntas=$this->obtener_datos($seccion->form_seccion_id, $id);
            //verificando si se puede cuantificar
            if ($res_seccion->preguntas[0]->respuesta=="cuantificable") {
                $res_seccion->tipo=$res_seccion->preguntas[0]->tipo;
            } else {
                $res_seccion->tipo=$res_seccion->preguntas[0]->tipo;
            }
            $res_seccion->promedio=$this->promedio($res_seccion->preguntas, count($res_seccion->preguntas), $res_seccion->tipo);
            $resultado->secciones[]=$res_seccion;
        }
        return response()->json($resultado, 200);
    }
    
    private function obtener_datos($seccion_id, $personal_evaluado_id)
    {
        $resultado=[];
        $preguntas=Form_Pregunta::where('form_seccion_id', $seccion_id)->where('estado', '1')->get();
        foreach ($preguntas as $pregunta) {
            //capturando preguntas
            $res=new stdClass();
            $res->pregunta=$pregunta->pregunta;
            $res->tipo=$pregunta->tipo;
            //verificar si el campo es box o escalar
            if ($pregunta->tipo=='box'|| $pregunta->tipo=='escala') {
                //extraendo respuesta del personal
                $respuestas=Form_Respuesta::where('form_pregunta_id', $pregunta->form_pregunta_id)->where('estado', '1')->get();
                foreach ($respuestas as $respuesta) {
                    $respuestas_personal_evaluaciones=Respuesta_Personal_Evaluacion::select(
                        'form_respuestas.valor',
                        'form_respuestas.val'
                    )
                    ->where('respuestas_personal_evaluaciones.personal_evaluaciones_id', $personal_evaluado_id)
                    ->where('form_respuestas.form_respuesta_id', $respuesta->form_respuesta_id)
                    ->join('form_respuestas', 'form_respuestas.form_respuesta_id', 'respuestas_personal_evaluaciones.form_respuesta_id')
                    ->pluck('form_respuestas.valor', 'form_respuestas.val')->first();
                    if ($respuestas_personal_evaluaciones) {
                        $res->val=$respuestas_personal_evaluaciones;
                    }
                    $res->respuestas[]=$respuesta->val;
                }
                $res->respuesta="cuantificable";
            } else {
                $res->respuesta="no cuantificable";
            }
            $resultado[]=$res;
        }
        return $resultado;
    }
    /**
     * promedio saca promedio de puntos
     *
     * @param  mixed $puntos
     * @param  mixed $preguntas
     * @return integer
     */
    private function promedio($puntos, $cant_preguntas, $tipo_seccion)
    {
        $promedio=0;
        //evitando campos q no se pueda cuantificar
        try {
            switch ($tipo_seccion) {
                case 'box':
                    foreach ($puntos as $pregunta) {
                        $promedio=intval($promedio)+intval($pregunta->val);
                    }
                    $promedio=round($promedio/$cant_preguntas);
                    return $this->evaluandoBox($promedio);
                    break;
                case 'escala':
                    foreach ($puntos as $pregunta) {
                        $promedio=intval($promedio)+intval($pregunta->val);
                    }
                    $promedio=round($promedio/$cant_preguntas);
                    return $this->evaluandoEscalar($promedio);
                    break;
                default:
                    return "No calculate";
                    break;
            }
        } catch (\Throwable $th) {
            return "No calculate";
        }
    }
    private function evaluandoEscalar($promedio)
    {
        if ($promedio<=4) {
            return $promedio="Poor";
        }
        if ($promedio<=7) {
            return $promedio="Regular";
        }
        if ($promedio<=9) {
            return $promedio="Good";
        }
        if ($promedio==10) {
            return $promedio="Excellent";
        }
    }
    private function evaluandoBox($promedio)
    {
        if ($promedio==1) {
            return $promedio="Never";
        }
        if ($promedio==2) {
            return $promedio="Sometimes";
        }
        if ($promedio==3) {
            return $promedio="Often";
        }
        if ($promedio==4) {
            return $promedio="Always";
        }
    }
}
