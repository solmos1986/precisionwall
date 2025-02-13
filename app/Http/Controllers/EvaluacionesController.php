<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DataTables;
use App\Evaluaciones;
use App\Personal_evaluaciones;
use App\Model\Form_Formulario;
use App\Personal;
use Validator;
use Illuminate\Support\Facades\DB;
use \stdClass;
use Mail;

class EvaluacionesController extends Controller
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
        $personal = Personal::select(
            'personal.Empleado_ID',
            DB::raw('CONCAT(personal.Nombre," ",personal.Apellido_Paterno," ",personal.Apellido_Materno) AS name_personal')
        )
        ->join('empresas', 'empresas.Emp_ID', 'personal.Emp_ID')
        ->where('personal.status', '1')
        ->orderBy('personal.Nombre', 'ASC')
        ->get();
        if (request()->ajax()) {
            $evaluacion = Evaluaciones::select(
                'evaluaciones.evaluacion_id',
                'evaluaciones.note',
                DB::raw("(SELECT COUNT(personal_evaluaciones.Empleado_ID )FROM personal_evaluaciones WHERE personal_evaluaciones.evaluacion_id=evaluaciones.evaluacion_id and personal_evaluaciones.estado='1') as personal_evaluar"),
                DB::raw('CONCAT(personal.Nombre," ",personal.Apellido_Paterno," ",personal.Apellido_Materno) AS foreman_id'),
                DB::raw('DATE_FORMAT(evaluaciones.fecha_asignacion, "%m/%d/%Y") as fecha_asignacion')
            )
            ->where('evaluaciones.estado', '1')
            ->join('personal', 'personal.Empleado_ID', 'evaluaciones.foreman_id')
            ->orderBy('evaluaciones.fecha_asignacion', 'asc')
            ->get();
            return Datatables::of($evaluacion)
                ->addIndexColumn()
                ->addColumn('acciones', function ($evaluacion) {
                    $button ="<a href='" . route('detail-list.personal_evaluationes', ['id' => $evaluacion->evaluacion_id]) . "'><i class='fas fa-eye ms-text-primary'></i></a>";
                    $button .= "<a href='#'><i class='fas fa-pencil-alt ms-text-warning edit' data-id='$evaluacion->evaluacion_id' title='Edit'></i></a>";
                    $button .= "<i class='far fa-trash-alt ms-text-danger delete cursor-pointer' data-id='$evaluacion->evaluacion_id' title='Delete'></i>";
                    
                    return $button;
                })
                ->addColumn('ver_usuarios', function ($evaluacion) {
                    $html =  "<span class='badge badge-success'> $evaluacion->personal_evaluar assigned users </span>";
                    return $html;
                })
                ->rawColumns(['ver_usuarios','acciones'])
                ->make(true);
        }
        return view('panel.evaluacion-formulario.evaluaciones.list', compact('personal'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = array(
            'new_foreman'=>'required',
            'new_select_personal' => 'required',
            'note' => 'nullable',
            'new_formulario' => 'required',
            'new_fecha_asignacion' => 'required|date_format:m/d/Y',
            'email' => 'nullable'
        );
        $messages=[
            'new_foreman.required'=>"The 'select foreman' field is required",
            'new_select_personal.required'=>"The 'select staff' field is required",
            'note.required'=>"The 'Note' field is required",
            'new_fecha_asignacion.required'=>"The 'Start date:' field is required",
            'new_fecha_asignacion.date_format'=>"Invalid date format must be month / day / year",
            'new_formulario.required'=>"The 'Select form' field is required",
        ];
        //validando
        $error = Validator::make($request->all(), $rules, $messages);
        if (request()->ajax() === true) {
            if ($error->errors()->all()) {
                return response()->json(['errors' => $error->errors()->all()]);
            }
        }
        $evaluacion = Evaluaciones::insertGetId([
            "foreman_id"=>$request->new_foreman,
            "note"=>$request->note,
            "formulario_id"=>$request->new_formulario,
            "fecha_asignacion"=>date('Y-m-d', strtotime($request->new_fecha_asignacion)),
            "estado"=>"1"
        ]);
        $personal=Personal::select('email')->where('Empleado_ID', $request->new_foreman)->first();
        
        if ($personal->Cargo!=='null') {
            $this->SendMail($personal->Empleado_ID, $request->new_fecha_asignacion);
        }
        foreach ($request->new_select_personal as $value) {
            $personal_evaluacion = Personal_evaluaciones::insert([
                "evaluacion_id"=>$evaluacion,
                "Empleado_ID"=>$value,
                "estado_formulario"=>'0',
                "estado"=>'1'
            ]);
        }
        
        return response()->json(["success"=>"Evaluations successfully created"]);
    }
    public function get_foreman(Request $request)
    {
        $data = [];
        if (!isset($request->searchTerm)) {
            $foreman = Personal::select(
                'personal.Empleado_ID',
                DB::raw('CONCAT(personal.Nombre," ",personal.Apellido_Paterno," ",personal.Apellido_Materno) AS name_personal'),
                'personal.Nick_Name'
            )
            ->where('personal.status', '1')
            ->orderBy('name_personal', 'ASC')
            ->get();
        } else {
            $foreman = Personal::select(
                'personal.Empleado_ID',
                DB::raw('CONCAT(personal.Nombre," ",personal.Apellido_Paterno," ",personal.Apellido_Materno) AS name_personal'),
                'personal.Nick_Name'
            )
            ->where('personal.status', '1')
            ->where('nombre', 'like', '%' . $request->searchTerm . '%')
            ->orderBy('name_personal', 'ASC')
            ->get();
        }
        foreach ($foreman as $row) {
            $data[] = array(
                    "id" => $row->Empleado_ID,
                    "text" => $row->name_personal
                );
        }
        return response()->json($data);
    }

    public function get_formulario(Request $request)
    {
        $data = [];
        if (!isset($request->searchTerm)) {
            $formulario = Form_Formulario::select(
                'formulario_id',
                'titulo'
            )
            ->orderBy('formulario_id', 'ASC')
            ->where('estado', '1')
            ->get();
        } else {
            $formulario = Form_Formulario::select(
                'formulario_id',
                'titulo'
            )
            ->where('titulo', 'like', '%' . $request->searchTerm . '%')
            ->where('estado', '1')
            ->orderBy('formulario_id', 'ASC')
            ->get();
        }
        foreach ($formulario as $row) {
            $data[] = array(
                    "id" => $row->formulario_id,
                    "text" => $row->titulo
                );
        }
        return response()->json($data);
    }

    public function get_personal()
    {
        $personal = Personal::select(
            'personal.Empleado_ID',
            DB::raw('CONCAT(personal.Nombre," ",personal.Apellido_Paterno," ",personal.Apellido_Materno) AS name_personal')
        )
        ->join('empresas', 'empresas.Emp_ID', 'personal.Emp_ID')
        ->where('personal.status', '1')
        ->orderBy('personal.Nombre', 'ASC')
        ->get();
        return response()->json($personal);
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
        $evaluacion = Evaluaciones::
        select(
            'evaluaciones.evaluacion_id',
            DB::raw('DATE_FORMAT(evaluaciones.fecha_asignacion, "%m/%d/%Y") as fecha_asignacion'),
            'evaluaciones.note',
            'form_formulario.formulario_id',
            'form_formulario.titulo',
            'evaluaciones.foreman_id',
            DB::raw('CONCAT(personal.Nombre," ",personal.Apellido_Paterno," ",personal.Apellido_Materno) AS name_personal')
        )
        ->where('evaluacion_id', $id)
        ->join('form_formulario', 'form_formulario.formulario_id', 'evaluaciones.formulario_id')
        ->join('personal', 'personal.Empleado_ID', 'evaluaciones.foreman_id')
        ->where('personal.status', '1')
        ->firstOrFail();

        $personal=Personal_evaluaciones::
        select(
            'personal.Empleado_ID'
        )
        ->join('personal', 'personal.Empleado_ID', 'personal_evaluaciones.Empleado_ID')
        ->where('personal_evaluaciones.evaluacion_id', $evaluacion->evaluacion_id)
        ->where('personal.status', '1')->get();

        $resultado=new stdClass();
        $resultado->evaluacion_id=$evaluacion->evaluacion_id;
        $resultado->fecha_asignacion=$evaluacion->fecha_asignacion;
        $resultado->note=$evaluacion->note;
        $resultado->formulario_id=$evaluacion->formulario_id;
        $resultado->titulo=$evaluacion->titulo;
        $resultado->foreman_id=$evaluacion->foreman_id;
        $resultado->name_personal=$evaluacion->name_personal;
        $resultado->personal=$personal;
        return response()->json($resultado);
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
        $rules = array(
            'edit_evaluacion_id'=>'required',
            'edit_foreman'=>'required',
            'edit_select_personal' => 'required',
            'edit_note' => 'nullable',
            'edit_formulario' => 'required',
            'edit_fecha_asignacion' => 'required|date_format:m/d/Y',
        );
        $messages=[
            'new_foreman.required'=>"The 'select foreman' field is required",
            'new_select_personal.required'=>"The 'select staff' field is required",
            'note.required'=>"The 'Note' field is required",
            'new_fecha_asignacion.required'=>"The 'Start date:' field is required",
            'new_fecha_asignacion.date_format'=>"Invalid date format must be month / day / year",
            'new_formulario.required'=>"The 'Select form' field is required",
        ];
        //validando
        $error = Validator::make($request->all(), $rules, $messages);
        if (request()->ajax() === true) {
            if ($error->errors()->all()) {
                return response()->json(['errors' => $error->errors()->all()]);
            }
        }
        $evaluacion = Evaluaciones::where('evaluacion_id', $request->edit_evaluacion_id)->update([
            "foreman_id"=>$request->edit_foreman,
            "note"=>$request->edit_note,
            "formulario_id"=>$request->edit_formulario,
            "fecha_asignacion"=>date('Y-m-d', strtotime($request->edit_fecha_asignacion)),
            "estado"=>"1"
        ]);
        $personal_evaluacion = Personal_evaluaciones::where('evaluacion_id', $request->edit_evaluacion_id)->delete();
        foreach ($request->edit_select_personal as $value) {
            $personal_evaluacion = Personal_evaluaciones::insert([
                "evaluacion_id"=>$evaluacion,
                "Empleado_ID"=>$value,
                "estado_formulario"=>'0',
                "estado"=>'1'
            ]);
        }
        return response()->json(["success"=>"Evaluations modified successfully"]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $eliminar= Evaluaciones::findOrFail($id)
        ->where('evaluacion_id', $id)->update([
            "estado"=>"0"
        ]);
        return response()->json(["success"=>"Evaluations delete successfully"]);
    }
    private function SendMail($email, $fecha_asignacion)
    {
        $email="stivenlovera@gmail.com";
        if ($email!==null) {
            Mail::send([], [], function ($message) use ($email, $fecha_asignacion) {
                $message->to($email);
                $message->subject('Has pending evaluations');
                
                $message->setBody('<img style="height: 30%" src="http://sof77.com/app/img/logo.png"><h3>Precision wall tech inc.</h3> <p>Has pending evaluations to be carried out on '.$fecha_asignacion.' <a href="http://zuna.esy.es/public/list-evaluations-pendient">here.</a> </p>', 'text/html');
            });
            // check for failures
            if (Mail::failures()) {
                return response()->json(['errors' => ['An error occurred while sending the email, please try again']]);
            }
    
            // otherwise everything is okay ...
            return response()->json([
                'success' => 'Success in sending the mail'
            ]);
        }
    }
}
