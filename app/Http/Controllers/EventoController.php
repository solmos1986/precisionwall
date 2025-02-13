<?php

namespace App\Http\Controllers;

use App\Empresas;
use App\Evento;
use App\Movimiento_evento;
use App\Personal;
use App\Tipo_evento;
use DataTables;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Validator;
use \stdClass;

class EventoController extends Controller
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
        $tipo_eventos = Tipo_evento::where('estado', '1')->get();
        $cargo = Personal::select('Cargo')
            ->groupByRaw('Cargo')
        //->where('Cargo', '!=', '')
            ->orderBy('Cargo', 'ASC')
            ->get();
        $company = Empresas::select(
            'Emp_ID',
            'Codigo',
            'Nombre'
        )
            ->orderBy('Emp_ID', 'ASC')
            ->get();

        $eventos = Evento::select(
            'evento.cod_evento',
            'evento.nombre',
            'evento.descripcion',
            'evento.duracion_day',
            'evento.note',
            DB::raw("(SELECT COUNT(personal_eventos.Empleado_ID )FROM personal_eventos WHERE personal_eventos.cod_evento=evento.cod_evento) as users"),
            'evento.access_pers',
            'evento.report_alert',
            'tipo_evento.nombre as nombre_tipo'
        )
            ->join('tipo_evento', 'evento.tipo_evento_id', 'tipo_evento.tipo_evento_id')
            ->join('personal_eventos', 'evento.cod_evento', 'personal_eventos.cod_evento')
            ->where('evento.estado', '1')
            ->orderBy('evento.nombre', 'ASC')
            ->distinct()
            ->get();
        if (request()->ajax()) {
            return Datatables::of($eventos)
                ->addIndexColumn()
                ->addColumn('duracion_day', function ($eventos) {
                    $html = "<span class='badge badge-success' style='font-size: 85%' > $eventos->duracion_day days </span>";
                    return $html;
                })
                ->addColumn('users', function ($eventos) {
                    $html = "<span class='badge badge-success' style='font-size: 85%' > $eventos->users users </span>";
                    return $html;
                })
                ->addColumn('ver_usuarios', function ($eventos) {
                    $html = "<a href='" . route('cardex.list_personal.evento', ['id' => $eventos->cod_evento]) . "'><span class='badge badge-outline-primary' style='font-size: 85%' >view registered employees</span></a>";
                    return $html;
                })
                ->addColumn('access_pers', function ($eventos) {
                    $html = ($eventos->access_pers) == 'y' ? '<span class="badge badge-success" style="font-size: 85%" >Yes</span>' : '<span class="badge badge-danger">No</span>';
                    return $html;
                })
                ->addColumn('report_alert', function ($eventos) {
                    $html = "<span class='badge badge-success' style='font-size: 85%' > $eventos->report_alert days before </span>";
                    return $html;
                })
                ->addColumn('acciones', function ($eventos) {
                    $button = "
                        <a href='#'><i id='$eventos->cod_evento' class='fas fa-pencil-alt ms-text-warning edit'></i></a>
                        <i class='far fa-trash-alt ms-text-danger delete cursor-pointer' data-id='$eventos->cod_evento' title='Delete'></i>";
                    return $button;
                })

                ->rawColumns(['acciones', 'access_pers', 'report_alert', 'duracion_day', 'users', 'ver_usuarios'])
                ->make(true);
        }
        return view('panel.cardex_personal.evento.list', compact('tipo_eventos', 'company', 'cargo'));
    }
    /**
     * createAll
     *  Este metodo permite crear crear certificados multiples personales
     * @param  mixed $request
     * @return array[object]
     */
    //multiselect filtros
    public function getCargo(Request $request)
    {
        //dd($request->query('cargo'));
        $personal = Personal::select(
            DB::raw("CONCAT(COALESCE(personal.Nombre,''), ' ',  COALESCE(personal.Apellido_Paterno,''), ' ',  COALESCE(personal.Apellido_Materno,'')) as nombre_empleado"),
            'personal.Empleado_ID',
            'personal.Nick_Name',
            'personal.Numero',
        )->when(!empty(request()->cargo), function ($q) {
            //dump('cargo');
            return $q->join('cargo_personal', 'personal.cargo_personal_id', 'cargo_personal.id')
                ->whereIn('cargo_personal.id', explode(',', request()->cargo));
        })->when(!empty(request()->company), function ($q) {
            //dump('company');
            return $q->whereIn('personal.Emp_ID', explode(',', request()->company));
        })->when(!empty(request()->evento), function ($q) {
            //dump('evento');
            return $q->leftjoin('movimientos_eventos', 'movimientos_eventos.Empleado_ID', 'personal.Empleado_ID')
                ->leftjoin('evento', 'evento.cod_evento', 'movimientos_eventos.cod_evento')
                ->whereIn('movimientos_eventos.cod_evento', explode(',', request()->evento))
                ->where('evento.estado', '1')
                ->where('movimientos_eventos.estado', '1');
        })->orderBy('personal.Nick_Name', 'ASC')
            ->get();

        return response()->json($personal);
    }

    public function createAll(Request $request)
    {
        //dd($request->all());
        $rules = array(
            'personal' => 'required|string',
            'tipo_evento' => 'required',
            'name' => 'required',
            'fecha_inicio' => 'required',
            'fecha_fin' => 'required',
            'note' => 'required',
            'description' => 'nullable',
            'visible' => 'required',
            'day_alert' => 'required|integer|min:1',
        );

        $messages = [
            'personal.required' => "The 'select staff' field is required",
            'tipo_evento.required' => "The 'type event' field is required",
            'name.required' => "The 'name' field is required",
            'fecha_inicio.required' => "The 'start date' last name field is required",
            'fecha_fin.required' => "The 'end date' field is required",
            'note.required' => "The 'note' field is required",
            'visible.required' => "The 'visible to' field is required",
            'day_alert.required' => "The 'days of anticipation' field is required",
            'day_alert.min' => "The field 'days in advance' must be greater than '0'",
        ];
        //validacion de de datos
        $error = Validator::make($request->all(), $rules, $messages);
        if (request()->ajax() === true) {
            if ($error->errors()->all()) {
                return response()->json(['errors' => $error->errors()->all()]);
            } else {
                ///verificando rago de fechas
                if (date('Y-m-d', strtotime($request->fecha_fin)) < date('Y-m-d', strtotime($request->fecha_inicio))) {
                    return response()->json(['errors' => ['check date range']]);
                }
            }
        }
        //trabajando dias
        $fecha1 = new DateTime(date('Y-m-d', strtotime($request->fecha_inicio)));
        $fecha2 = new DateTime(date('Y-m-d', strtotime($request->fecha_fin)));
        $diff = $fecha1->diff($fecha2);
        //save
        $evento = Evento::insertGetId([
            "nombre" => $request->name,
            "descripcion" => $request->description,
            "duracion_day" => $diff->days,
            "note" => $request->note,
            "access_code" => $request->visible,
            "access_pers" => "y",
            "report_alert" => $request->day_alert,
            "tipo_evento_id" => $request->tipo_evento,
            "estado" => "1",
        ]);
        //insertando datos en terceras tablas
        foreach ($request->personal as $value) {
            Movimiento_evento::insert([
                "cod_evento" => $evento,
                "Empleado_ID" => $value,
                "start_date" => date('Y-m-d', strtotime($request->fecha_inicio)),
                "exp_date" => date('Y-m-d', strtotime($request->fecha_fin)),
                "note" => " ",
                "raise_from" => " ",
                "raise_to" => " ",
            ]);
        }

        return response()->json(["success" => "Event successfully created"]);
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
        $rules = array(
            'name' => 'required',
            'description' => 'nullable',
            'duracion_day' => 'required|integer|min:1',
            'note' => 'nullable',
            'access_code' => 'required',
            'access_pers' => 'required',
            'day_alert' => 'required|integer|min:1',
            'tipo_evento' => 'required',
        );
        $messages = [
            'name.required' => "The 'name' field is required",
            'duracion_day.required' => "The 'days of duration' field is required",
            'duracion_day.integer' => "The 'days of duration' field must be a number",
            'duracion_day.min' => "The 'days of duration' field must be greater than one",
            'fecha_inicio.required' => "The 'start date' last name field is required",
            'fecha_fin.required' => "The 'end date' field is required",
            'note.required' => "The 'note' field is required",
            'access_code.required' => "The 'select visibility' field is required",
            'access_pers.required' => "The 'update this event' field is required",
            'day_alert.required' => "The 'days of anticipation' field is required",
            'day_alert.integer' => "The 'days of anticipation' field must be a number",
            'day_alert.min' => "The 'days of anticipation' field must be greater than one",
            'tipo_evento' => "The 'event type' field is required",
        ];
        //validacion de de datos
        $error = Validator::make($request->all(), $rules, $messages);
        if ($error->errors()->all()) {
            return response()->json(['errors' => $error->errors()->all()]);
        } else {
            $evento = Evento::insertGetId([
                "nombre" => $request->name,
                "descripcion" => $request->description,
                "duracion_day" => $request->duracion_day,
                "note" => $request->note,
                "access_pers" => $request->access_pers,
                "report_alert" => $request->day_alert,
                "tipo_evento_id" => $request->tipo_evento,
                "estado" => "1",
            ]);
            foreach ($request->access_code as $personal) {
                $accessUser = DB::table('personal_eventos')
                    ->insert([
                        "cod_evento" => $evento,
                        "Empleado_ID" => $personal,
                    ]);
            }
            return response()->json(["success" => "Event successfully created"]);
        }
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
        $resultado = new stdClass();
        if (request()->ajax() === true) {
            $eventos = Evento::select(
                'evento.cod_evento',
                'evento.nombre',
                'evento.descripcion',
                'evento.duracion_day',
                'evento.note',
                'evento.access_pers',
                'evento.report_alert',
                'tipo_evento.tipo_evento_id',
                'tipo_evento.nombre as nombre_tipo',
                'personal.Empleado_ID',
                'personal.Nombre',
                'personal.Emp_ID',
                'personal.Cargo'
            )
                ->join('tipo_evento', 'evento.tipo_evento_id', 'tipo_evento.tipo_evento_id')
                ->join('personal_eventos', 'evento.cod_evento', 'personal_eventos.cod_evento')
                ->join('personal', 'personal_eventos.Empleado_ID', 'personal.Empleado_ID')
                ->where('evento.estado', '1')
                ->where('evento.cod_evento', $id)
                ->orderBy('evento.cod_evento', 'ASC')
                ->get();
            $users = [];
            //construyendo resp.
            foreach ($eventos as $value) {
                $resultado->cod_evento = $value->cod_evento;
                $resultado->nombre = $value->nombre;
                $resultado->descripcion = $value->descripcion;
                $resultado->duracion_day = $value->duracion_day;
                $resultado->note = $value->note;
                $resultado->access_pers = $value->access_pers;
                $resultado->report_alert = $value->report_alert;
                $resultado->tipo_evento_id = $value->tipo_evento_id;
                $resultado->nombre_tipo = $value->nombre_tipo;
                $users[] = [
                    "Empleado_ID" => $value->Empleado_ID,
                    "Nombre" => $value->Nombre,
                    "Emp_ID" => $value->Emp_ID,
                    "Cargo" => $value->Cargo,
                ];
            }
            $resultado->users = $users;
        }

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
            'cod_evento' => 'required',
            'name' => 'required',
            'description' => 'nullable',
            'duracion_day' => 'required|integer|min:1',
            'note' => 'nullable',
            'access_code' => 'required',
            'access_pers' => 'required',
            'day_alert' => 'required|integer|min:1',
            'tipo_evento' => 'required',
        );
        $messages = [
            'name.required' => "The 'name' field is required",
            'duracion_day.required' => "The 'days of duration' field is required",
            'duracion_day.integer' => "The 'days of duration' field must be a number",
            'duracion_day.min' => "The 'days of duration' field must be greater than one",
            'fecha_inicio.required' => "The 'start date' last name field is required",
            'fecha_fin.required' => "The 'end date' field is required",
            'note.required' => "The 'note' field is required",
            'access_code.required' => "The 'select staff' field is required",
            'access_pers.required' => "The 'update this event' field is required",
            'day_alert.required' => "The 'days of anticipation' field is required",
            'day_alert.integer' => "The 'days of anticipation' field must be a number",
            'day_alert.min' => "The 'days of anticipation' field must be greater than one",
            'tipo_evento' => "The 'event type' field is required",
        ];
        //validacion de de datos
        $error = Validator::make($request->all(), $rules, $messages);
        if ($error->errors()->all()) {
            return response()->json(['errors' => $error->errors()->all()]);
        } else {
            $evento = Evento::where('cod_evento', $id)->first();
            $evento->update([
                "nombre" => $request->name,
                "descripcion" => $request->description,
                "duracion_day" => $request->duracion_day,
                "note" => $request->note,
                "access_pers" => $request->access_pers,
                "report_alert" => $request->day_alert,
                "tipo_evento_id" => $request->tipo_evento,
                "estado" => "1",
            ]);
            //dd($evento->cod_evento);
            $users = DB::table('personal_eventos')->where('cod_evento', $evento->cod_evento)->delete();
            foreach ($request->access_code as $personal) {
                $accessUser = DB::table('personal_eventos')
                    ->insert([
                        "cod_evento" => $evento->cod_evento,
                        "Empleado_ID" => $personal,
                    ]);
            }
            return response()->json(["success" => "Successfully modified event"]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $verificar = Evento::where('evento.cod_evento', $id)
            ->where('movimientos_eventos.estado', '1')
            ->join('movimientos_eventos', 'movimientos_eventos.cod_evento', 'evento.cod_evento')
            ->get()
            ->toArray();
        if (empty($verificar)) {
            $evento = Evento::where('cod_evento', $id)->update([
                "estado" => "0",
            ]);
            return response()->json(["success" => "Successfully delete event"]);
        } else {
            return response()->json(["error" => "It cannot be deleted because there are registered users"]);
        }
    }
    public function list_personal($id)
    {
        $evento = Evento::select('evento.*', 'tipo_evento.nombre as tipo_evento')
            ->where('evento.cod_evento', $id)
            ->where('evento.estado', 1)
            ->join('tipo_evento', 'evento.tipo_evento_id', 'tipo_evento.tipo_evento_id')
            ->firstOrFail();

        //dd($evento, $id);
        $personal = Movimiento_evento::select(
            'personal.Numero',
            'personal.Empleado_ID',
            'personal.Nick_Name',
            DB::raw("CONCAT(COALESCE(personal.Nombre,''),' ',COALESCE(personal.Apellido_Paterno,''),' ',COALESCE(personal.Apellido_Materno,'')) as nombre_completo"),
            'personal.Cargo',
            DB::raw('CONCAT(personal.Nombre," ",personal.Apellido_Paterno," ",personal.Apellido_Materno) AS nombre_completo'),
            DB::raw('DATE_FORMAT(movimientos_eventos.start_date, "%m/%d/%Y") as start_date '),
            DB::raw('DATE_FORMAT(movimientos_eventos.exp_date, "%m/%d/%Y") as exp_date '),
            'movimientos_eventos.note'
        )
            ->where('movimientos_eventos.cod_evento', $evento->cod_evento)
            ->where('movimientos_eventos.estado', '1')
            ->where('personal.status', '1')
            ->join('personal', 'movimientos_eventos.Empleado_ID', 'personal.Empleado_ID')
            ->get();

        if (request()->ajax()) {
            return Datatables::of($personal)
                ->addIndexColumn()

                ->addColumn('acciones', function ($personal) {
                    $button = "<a href='" . route('edit.cardex', ['id' => $personal->Empleado_ID]) . "'><i class='fas fa-user-tag'></i></a>";
                    return $button;
                })

                ->rawColumns(['acciones', 'start_date', 'exp_date'])
                ->make(true);
        }
        return view('panel.cardex_personal.evento.list_personal', compact('evento'));
    }
}
