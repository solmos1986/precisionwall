<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\Form_Formulario;
use App\Model\Form_Respuesta;
use App\Model\Form_Pregunta;
use App\Model\Form_Seccion;
use App\Respuesta_Personal_Evaluacion;
use Illuminate\Support\Facades\DB;
use Validator;
use DataTables;
use \stdClass;

class FormularioController extends Controller
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
        //evaluacaiones
        $formularios=Form_Formulario::where('estado', '1')->get();
        if (request()->ajax()) {
            return Datatables::of($formularios)
                ->addIndexColumn()
                ->addColumn('acciones', function ($formularios) {
                    $button = "
                        <a href='" . route('show.form', ['id' => $formularios->formulario_id]) . "'><i class='fas fa-eye ms-text-primary'></i></a>
                        <a href='" . route('panel.evaluacion-formulario.formulario.edit', ['id' => $formularios->formulario_id]) . "'><i class='fas fa-pencil-alt ms-text-warning edit'></i></a>
                        <i class='far fa-trash-alt ms-text-danger delete cursor-pointer' data-id='$formularios->formulario_id' title='Delete'></i>";
                        
                    return $button;
                })
                
                ->rawColumns(['acciones'])
                ->make(true);
        }
        return view('panel.evaluacion-formulario.evaluacion.list');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('panel.evaluacion-formulario.formulario.new');
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
        $rules = array(
            'title'=>'required|string',
            'description' => 'nullable',
            'secciones' => 'required',
        );
        $messages=[
            'title.required'=>"The 'title' field is required",
            'title.string'=>"The 'title' field is required",
            'description.required'=>"The 'description' field is required",
            'secciones.required' =>"There is no content in the form",
        ];

        //validando
        $error = Validator::make($request->all(), $rules, $messages);
        if (request()->ajax() === true) {
            if ($error->errors()->all()) {
                return response()->json(['errors' => $error->errors()->all()]);
            }
        }

        //insertando formulario
        $formulario=Form_Formulario::insertGetId([
            "titulo"=>$request->title,
            "descripcion"=>$request->description,
            "estado"=>'1',
            "fecha_creacion"=>date('Y-m-d'),
            "Empleado_ID"=>auth()->user()->Empleado_ID
        ]);
        //insertando campo compocision

        foreach ($request->secciones as $seccion) {
            $formSeccion=Form_Seccion::insertGetId([
                    "descripcion"=>$seccion["descripcion"],
                    "subtitulo"=>$seccion["subtitulo"],
                    "formulario_id"=>$formulario,
                    "estado"=>"1"
                ]);
            foreach ($seccion["preguntas"] as $pregunta) {
                $formPregunta=Form_Pregunta::insertGetId([
                        "pregunta"=>$pregunta["pregunta"],
                        "tipo"=>$pregunta["tipo"],
                        "form_seccion_id"=>$formSeccion,
                        "estado"=>"1"
                    ]);
                foreach ($pregunta["respuestas"] as $respuesta) {
                    $formRespuesta=Form_Respuesta::insertGetId([
                            "val"=>$respuesta["val"],
                            "valor"=>$respuesta["valor"],
                            "form_pregunta_id"=>$formPregunta,
                            "estado"=>"1"
                        ]);
                }
            }
        }
        return response()->json(['success' => ["form saved successfully"]]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $formulario=Form_Formulario::where('formulario_id', $id)->where('estado', '1')->firstOrFail();
        $res_formulario= new stdClass();
        if (request()->ajax()) {
            $secciones=Form_Seccion::where('formulario_id', $formulario->formulario_id)->where('estado', '1')->get();
            foreach ($secciones as $seccion) {
                $res_seccion= new stdClass();
                $preguntas=Form_Pregunta::where('form_seccion_id', $seccion->form_seccion_id)->where('estado', '1')->get();
                foreach ($preguntas as $pregunta) {
                    $res_pregunta= new stdClass();
                    $respuestas=Form_Respuesta::where('form_pregunta_id', $pregunta->form_pregunta_id)->where('estado', '1')->get();
                    foreach ($respuestas as $respuesta) {
                        $res_respuesta=new stdClass();
                        $res_respuesta->form_respuesta_id=$respuesta->form_respuesta_id;
                        $res_respuesta->val=$respuesta->val;
                        $res_respuesta->valor=$respuesta->valor;
                        $res_respuesta->form_pregunta_id=$respuesta->form_pregunta_id;
                        $res_respuesta->estado=$respuesta->estado;
                        $res_respuesta->dato="";
                        $res_pregunta->respuestas[]=$res_respuesta;
                    }
                    $res_pregunta->form_pregunta_id=$pregunta->form_pregunta_id;
                    $res_pregunta->pregunta=$pregunta->pregunta;
                    $res_pregunta->tipo=$pregunta->tipo;
                    $res_pregunta->form_seccion_id=$pregunta->form_seccion_id;
                    $res_pregunta->estado=$pregunta->estado;
                    $res_seccion->preguntas[]=$res_pregunta;
                }
                $res_seccion->form_seccion_id=$seccion->form_seccion_id;
                $res_seccion->descripcion=$seccion->descripcion;
                $res_seccion->subtitulo=$seccion->subtitulo;
                $res_seccion->formulario_id=$seccion->formulario_id;
                $res_seccion->estado=$seccion->estado;
                $res_formulario->secciones[]=$res_seccion;
            }
           
            $res_formulario->formulario_id=$formulario->formulario_id;
            $res_formulario->titulo=$formulario->titulo;
            $res_formulario->fecha_creacion=$formulario->fecha_creacion;
            $res_formulario->Empleado_ID=$formulario->Empleado_ID;
            $res_formulario->descripcion=$formulario->descripcion;
            $res_formulario->estado=$formulario->estado;

            return response()->json($res_formulario, 200);
        }
        $formularioId=$formulario->formulario_id;
      
        return view('panel.evaluacion-formulario.evaluacion.show', compact('formularioId'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $formulario=Form_Formulario::where('formulario_id', $id)->where('estado', '1')->first();
        
        $res_formulario= new stdClass();
        $secciones=Form_Seccion::where('formulario_id', $formulario->formulario_id)->where('estado', '1')->get();
        
        foreach ($secciones as $seccion) {
            $res_seccion= new stdClass();
            $preguntas=Form_Pregunta::where('form_seccion_id', $seccion->form_seccion_id)->where('estado', '1')->get();
        
            foreach ($preguntas as $pregunta) {
                $res_pregunta= new stdClass();
                $respuestas=Form_Respuesta::where('form_pregunta_id', $pregunta->form_pregunta_id)->where('estado', '1')->get();
                foreach ($respuestas as $respuesta) {
                    $res_respuesta=new stdClass();
                    $res_respuesta->form_respuesta_id=$respuesta->form_respuesta_id;
                    $res_respuesta->val=$respuesta->val;
                    $res_respuesta->valor=$respuesta->valor;
                    $res_respuesta->form_pregunta_id=$respuesta->form_pregunta_id;
                    $res_respuesta->estado=$respuesta->estado;
                    $res_respuesta->dato="";
                    $res_pregunta->respuestas[]=$res_respuesta;
                }
                $res_pregunta->form_pregunta_id=$pregunta->form_pregunta_id;
                $res_pregunta->pregunta=$pregunta->pregunta;
                $res_pregunta->tipo=$pregunta->tipo;
                $res_pregunta->form_seccion_id=$pregunta->form_seccion_id;
                $res_pregunta->estado=$pregunta->estado;
                $res_seccion->preguntas[]=$res_pregunta;
            }
            $res_seccion->form_seccion_id=$seccion->form_seccion_id;
            $res_seccion->descripcion=$seccion->descripcion;
            $res_seccion->subtitulo=$seccion->subtitulo;
            $res_seccion->formulario_id=$seccion->formulario_id;
            $res_seccion->estado=$seccion->estado;
            $res_formulario->secciones[]=$res_seccion;
        }
           
        $res_formulario->formulario_id=$formulario->formulario_id;
        $res_formulario->titulo=$formulario->titulo;
        $res_formulario->fecha_creacion=$formulario->fecha_creacion;
        $res_formulario->Empleado_ID=$formulario->Empleado_ID;
        $res_formulario->descripcion=$formulario->descripcion;
        $res_formulario->estado=$formulario->estado;

        $formularioId=$formulario->formulario_id;
        
        return view('panel.evaluacion-formulario.formulario.edit', compact('res_formulario'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Form_Formulario  $id)
    {
        $rules = [
            'title'=> 'required|string',
            'description' => 'nullable',
            'secciones' => 'required',
        ];

        $messages=[
            'title.required'=>"The 'title' field is required",
            'title.string'=>"The 'title' field is required",
            'description.required'=>"The 'description' field is required",
            'secciones.required' =>"There is no content in the form",
        ];
        //validando
        $error = Validator::make($request->all(), $rules, $messages);

        if (request()->ajax() === true) {
            if ($error->errors()->all()) {
                return response()->json(['errors' => $error->errors()->all()]);
            }
        }
        if (Form_Formulario::where('titulo', $request->title)->exists()) {
            return response()->json(['errors' => ["Change the name of the form, there is already a similar one"]]);
        }

        //insertando formulario
        $formulario=Form_Formulario::insertGetId([
            "titulo"=>$request->title,
            "descripcion"=>$request->description,
            "estado"=>'1',
            "fecha_creacion"=>date('Y-m-d'),
            "Empleado_ID"=>auth()->user()->Empleado_ID
        ]);

        foreach ($request->secciones as $seccion) {
            $formSeccion=Form_Seccion::insertGetId([
                    "descripcion"=>$seccion["descripcion"],
                    "subtitulo"=>$seccion["subtitulo"],
                    "formulario_id"=>$formulario,
                    "estado"=>"1"
                ]);
            foreach ($seccion["preguntas"] as $pregunta) {
                $formPregunta=Form_Pregunta::insertGetId([
                        "pregunta"=>$pregunta["pregunta"],
                        "tipo"=>$pregunta["tipo"],
                        "form_seccion_id"=>$formSeccion,
                        "estado"=>"1"
                    ]);
                foreach ($pregunta["respuestas"] as $respuesta) {
                    $formRespuesta=Form_Respuesta::insertGetId([
                            "val"=>$respuesta["val"],
                            "valor"=>$respuesta["valor"],
                            "form_pregunta_id"=>$formPregunta,
                            "estado"=>"1"
                        ]);
                }
            }
        }
        return response()->json(['success' => ["form saved successfully"]]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $eliminar= Form_Formulario::findOrFail($id)
        ->where('formulario_id', $id)->update([
            "estado"=>"0"
        ]);
        return response()->json(["success"=>"Form delete successfully"]);
    }
    public function mostrar_form_completado($id)
    {
        $formulario=Form_Formulario::select(
            'form_formulario.formulario_id',
            DB::raw('DATE_FORMAT(evaluaciones.fecha_asignacion, "%m/%d/%Y") as fecha_asignacion'),
            'form_formulario.titulo',
            'form_formulario.Empleado_ID',
            'form_formulario.descripcion',
            'form_formulario.estado',
            DB::raw('CONCAT(personal.Nombre," ",personal.Apellido_Paterno," ",personal.Apellido_Materno) AS nombre_completo')
        )
        ->where('personal_evaluaciones.personal_evaluaciones_id', $id)
        ->where('form_formulario.estado', '1')
        ->join('evaluaciones', 'evaluaciones.formulario_id', 'form_formulario.formulario_id')
        ->join('personal_evaluaciones', 'personal_evaluaciones.evaluacion_id', 'evaluaciones.evaluacion_id')
        ->join('personal', 'personal.Empleado_ID', 'personal_evaluaciones.Empleado_ID')
        ->firstOrFail();
        $res_formulario= new stdClass();
        //if (request()->ajax()) {
        $secciones=Form_Seccion::where('formulario_id', $formulario->formulario_id)->where('estado', '1')->get();
        foreach ($secciones as $seccion) {
            $res_seccion= new stdClass();
            $preguntas=Form_Pregunta::where('form_seccion_id', $seccion->form_seccion_id)->where('estado', '1')->get();
            foreach ($preguntas as $pregunta) {
                $res_pregunta= new stdClass();
                $respuestas=Form_Respuesta::where('form_pregunta_id', $pregunta->form_pregunta_id)->where('estado', '1')->get();
                foreach ($respuestas as $respuesta) {
                    $res_respuesta=new stdClass();
                    $respuestas_personal_evaluaciones=Respuesta_Personal_Evaluacion::select('respuesta')
                    ->where('form_respuesta_id', $respuesta->form_respuesta_id)
                    ->where('personal_evaluaciones_id', $id)
                    ->where('estado', '1')->pluck('respuesta')->first();
                    //dd($respuestas_personal_evaluaciones);
                    $res_respuesta->respuesta=$respuestas_personal_evaluaciones;
                    $res_respuesta->form_respuesta_id=$respuesta->form_respuesta_id;
                    $res_respuesta->val=$respuesta->val;
                    $res_respuesta->valor=$respuesta->valor;
                    $res_respuesta->form_pregunta_id=$respuesta->form_pregunta_id;
                    $res_respuesta->estado=$respuesta->estado;
                    $res_pregunta->respuestas[]=$res_respuesta;
                }
                $res_pregunta->form_pregunta_id=$pregunta->form_pregunta_id;
                $res_pregunta->pregunta=$pregunta->pregunta;
                $res_pregunta->tipo=$pregunta->tipo;
                $res_pregunta->form_seccion_id=$pregunta->form_seccion_id;
                $res_pregunta->estado=$pregunta->estado;
                $res_seccion->preguntas[]=$res_pregunta;
            }
            $res_seccion->form_seccion_id=$seccion->form_seccion_id;
            $res_seccion->descripcion=$seccion->descripcion;
            $res_seccion->subtitulo=$seccion->subtitulo;
            $res_seccion->formulario_id=$seccion->formulario_id;
            $res_seccion->estado=$seccion->estado;
            $res_formulario->secciones[]=$res_seccion;
        }
           
        $res_formulario->formulario_id=$formulario->formulario_id;
        $res_formulario->titulo=$formulario->titulo;
        $res_formulario->fecha_asignacion=$formulario->fecha_asignacion;
        $res_formulario->Empleado_ID=$formulario->Empleado_ID;
        $res_formulario->descripcion=$formulario->descripcion;
        $res_formulario->estado=$formulario->estado;
        $res_formulario->nombre_completo=$formulario->nombre_completo;
        //dd($res_formulario);
        $formularioId=$formulario->formulario_id;

        return view('panel.evaluacion-formulario.evaluar.show-complete', compact('res_formulario'));
    }
}
