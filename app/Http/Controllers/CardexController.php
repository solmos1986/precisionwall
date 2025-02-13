<?php

namespace App\Http\Controllers;

use App\Empresas;
use App\Evento;
use App\Modules\UserModules\UserService;
use App\Movimiento_evento;
use App\Personal;
use App\Tipo_evento;
use DataTables;
use DateTime;
use File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Image;
use Validator;
use \stdClass;

class CardexController extends Controller
{
    private $userService;
    public function __construct()
    {
        $this->middleware('auth');
        $this->userService = new UserService;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index()
    {
        $eventos = Evento::where('estado', '1')->get();
        $cargos = DB::table('cargo_personal')
            ->orderBy('id', 'DESC')
            ->get();
        $tipos_personal = DB::table('tipo_personal')
            ->orderBy('id', 'DESC')
            ->get();
        $company = Empresas::select(
            'Emp_ID',
            'Codigo',
            'Nombre'
        )
            ->orderBy('Emp_ID', 'ASC')
            ->get();

        $personal = Personal::select(
            'personal.Empleado_ID',
            'personal.Numero',
            DB::raw("CONCAT(COALESCE(personal.Nombre,''),' ',COALESCE(personal.Apellido_Paterno,''),' ',COALESCE(personal.Apellido_Materno,'')) as Nombre"),
            'personal.Nick_Name',
            'tipo_personal.nombre as nombre_tipo',
            'personal.Cargo',
            'personal.email',
            'empresas.Nombre as nombre_empresa'
        )
        //filtrando Company
            ->when(!empty(request()->companies), function ($q) {
                return $q->whereIn('empresas.Emp_ID', explode(',', request()->companies));
            })
        //filtrando tipos
            ->when(!empty(request()->tipos), function ($q) {
                return $q->whereIn('personal.tipo_personal_id', explode(',', request()->tipos));
            })
        //filtrando cargo
            ->when(!empty(request()->cargos), function ($q) {
                return $q->whereIn('personal.cargo_personal_id', explode(',', request()->cargos));
            })
        //filtrando nickname
            ->when(!empty(request()->personas), function ($q) {
                return $q->whereIn('personal.Empleado_ID', explode(',', request()->personas));
            })
        //filtrando nickname
            ->when(!empty(request()->eventos), function ($q) {
                return $q->leftjoin('movimientos_eventos', 'movimientos_eventos.Empleado_ID', 'personal.Empleado_ID')
                    ->leftjoin('evento', 'evento.cod_evento', 'movimientos_eventos.cod_evento')
                    ->where('movimientos_eventos.estado', '1')
                    ->where('evento.estado', '1')
                    ->whereIn('movimientos_eventos.cod_evento', explode(',', request()->eventos));
            })
            ->join('empresas', 'empresas.Emp_ID', 'personal.Emp_ID')
            ->leftjoin('tipo_personal', 'tipo_personal.id', 'personal.tipo_personal_id')
            ->leftjoin('cargo_personal', 'cargo_personal.id', 'personal.cargo_personal_id')
            ->where('personal.status', '1')
            ->groupBy('personal.Empleado_ID')
            ->orderBy('personal.Nombre', 'ASC')
            ->get();
        if (request()->ajax()) {
            if (auth()->user()->verificarRol([1, 5, 10])) {
                return Datatables::of($personal)
                    ->addIndexColumn()
                    ->addColumn('acciones', function ($personal) {
                        $button = "
                        <a href='" . route('edit.cardex', ['id' => $personal->Empleado_ID]) . "'><i class='fas fa-user-tag ms-text-primary cursor-pointer' title='Show employee'></i></a>
                        <i class='far fa-trash-alt ms-text-danger delete cursor-pointer' data-id='$personal->Empleado_ID' title='Delete'></i>
                        ";
                        return $button;
                    })
                /*  */
                    ->addColumn('eventos', function ($personal) {
                        $eventos = DB::table('movimientos_eventos')
                            ->select(
                                'evento.nombre'
                            )
                            ->join('evento', 'evento.cod_evento', 'movimientos_eventos.cod_evento')
                            ->where('movimientos_eventos.Empleado_ID', $personal->Empleado_ID)
                            ->where('evento.estado', 1)
                            ->where('movimientos_eventos.estado', '1')
                            ->get()->toArray();

                        $button = "";
                        foreach ($eventos as $key => $evento) {
                            $button .= "<span class='badge badge-info m-1' style='font-size: 85%'>$evento->nombre</span>";
                        }
                        return $button;
                    })
                    ->addColumn('check', function ($personal) {
                        $button = "
                        <label class='ms-checkbox-wrap ms-checkbox-info'>
                                <input type='checkbox' value='$personal->Empleado_ID' class='persona' style='opacity: 1;' data-proyecto='$personal->Empleado_ID'>
                                <i class='ms-checkbox-check'></i>
                              </label>";
                        return $button;
                    })
                    ->rawColumns(['acciones', 'eventos', 'check'])
                    ->make(true);
            } else {
                return Datatables::of($personal)
                    ->addIndexColumn()
                    ->addColumn('acciones', function ($personal) {
                        $button = "
                        <a href='" . route('edit.cardex', ['id' => $personal->Empleado_ID]) . "'><i class='fas fa-user-tag cursor-pointer' title='Show employee'></i></a>
                        <i class='far fa-trash-alt ms-text-danger delete cursor-pointer' data-id='$personal->Empleado_ID' title='Delete'></i>
                        ";
                        return $button;
                    })
                    ->addColumn('eventos', function ($personal) {
                        $eventos = DB::table('movimientos_eventos')
                            ->select(
                                'evento.nombre'
                            )
                            ->join('evento', 'evento.cod_evento', 'movimientos_eventos.cod_evento')
                            ->where('movimientos_eventos.Empleado_ID', $personal->Empleado_ID)
                            ->where('evento.estado', 1)
                            ->get()->toArray();

                        $button = "";
                        foreach ($eventos as $key => $evento) {
                            $button .= "<span class='badge badge-info m-1' style='font-size: 85%'>$evento->nombre</span>";
                        }
                        return $button;
                    })
                    ->rawColumns(['acciones', 'eventos'])
                    ->make(true);
            }
        }
        return view('panel.cardex_personal.list', compact('personal', 'company', 'cargos', 'eventos', 'tipos_personal'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    //crear eventos a un grupo
    public function create()
    {
        $personal = DB::table('personal')
            ->select('Numero')
            ->orderByRaw('CHAR_LENGTH(Numero) DESC')
            ->orderBy('Numero', 'DESC')
            ->get();
        $numero;
        foreach ($personal as $key => $value) {
            if (is_numeric($value->Numero)) {
                $numero = $value->Numero;
                break;
            }
        }
        //incrementar
        $numero += 1;

        $cargos = DB::table('cargo_personal')->get();
        $tipos_usuarios = DB::table('tipo_personal')->get();
        return view('panel.cardex_personal.new', compact('tipos_usuarios', 'cargos', 'numero'));
    }

    /**
     * get_empresas
     * This method require Request  and  return array
     * @param  mixed $request
     * @return array
     */
    public function get_empresas(Request $request)
    {
        if (!isset($request->searchTerm)) {
            $empresas = Empresas::get();
        } else {
            $empresas = Empresas::where('Nombre', 'like', '%' . $request->searchTerm . '%')
                ->distinct('Nombre')
                ->get();
        }

        $data = [];
        foreach ($empresas as $row) {
            $data[] = array(
                "id" => $row->Emp_ID,
                "text" => $row->Nombre,
            );
        }
        return response()->json($data);
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
            'Emp_ID' => 'required',
            'Nombre' => 'required',
            'Apellido_Paterno' => 'nullable',
            'Apellido_Materno' => 'nullable',
            'Nick_Name' => 'required',
            'Estado' => 'required',
            'Ciudad' => 'nullable',
            'Zip_Code' => 'nullable',
            'Calle' => 'nullable',
            'Numero' => 'nullable',
            'Cargo' => 'nullable',
            'Numero_Seguro_Social' => 'nullable',
            'Fecha_Nacimiento' => 'nullable',
            'Numero_Licencia_Conducir' => 'nullable',
            'Numero_Permiso_Trabajo' => 'nullable',
            'Fecha_Expiracion_Trabajo' => 'nullable',
            'Numero_Residente' => 'nullable',
            'email' => 'nullable',
            'Telefono' => 'nullable',
            'Celular' => 'nullable',
            'Aux1' => 'nullable',
            'Aux2' => 'nullable',
            'Aux3' => 'nullable',
            'Aux4' => 'nullable',
            'Aux5' => 'nullable',
            'Usuario' => 'nullable',
            'Password' => 'nullable',
            'P1' => 'nullable',
            'R1' => 'nullable',
            'P2' => 'nullable',
            'R2' => 'nullable',
            'P3' => 'nullable',
            'R3' => 'nullable',
            'Indice_produccion' => 'nullable',
            'Nro_Bono' => 'nullable',
            'Spec_Bon1' => 'nullable',
            'Extra_Mon1' => 'nullable',
            'Benefit1' => 'nullable',
            'Benefit2' => 'nullable',
            'empresa' => 'nullable',
            'Rol_ID' => 'nullable',

        );
        $messages = [
            'Emp_ID.required' => "The company field is required",
            'Nombre.required' => "The name field is required",
            'Apellido_Paterno.required' => "The Last name field is required",
            'Apellido_Materno.required' => "The Mother's last name field is required",
            'Nick_Name.required' => "The nick name field is required",
            'Estado.required' => "The civil status field is required",
            'Cargo.required' => "The position field is required",
            'Fecha_Nacimiento.required' => "The birth date field is required",
        ];

        $error = Validator::make($request->all(), $rules, $messages);
        if (request()->ajax() === true) {
            return response()->json(['errors' => $error->errors()->all()]);
        }
        //verificacion
        $cargo = DB::table('cargo_personal')->where('id', $request->cargo_personal_id)->first();
        $tipo = DB::table('tipo_personal')->where('id', $request->tipo_personal_id)->first();
        //dd($request->all());
        $personal = Personal::insert([
            'Emp_ID' => $request->Emp_ID,
            'Nombre' => $request->Nombre,
            'Apellido_Paterno' => $request->Apellido_Paterno,
            'Apellido_Materno' => $request->Apellido_Materno,
            'Nick_Name' => $request->Nick_Name,
            'Estado' => $request->Estado,
            'Ciudad' => $request->Ciudad,
            'Zip_Code' => $request->Zip_Code,
            'Calle' => $request->Calle,
            'Numero' => $request->Numero,
            'Cargo' => $cargo->nombre,
            'Numero_Seguro_Social' => $request->Numero_Seguro_Social,
            'Fecha_Nacimiento' => $request->Fecha_Nacimiento == null ? '0000-00-00' : date('Y-m-d', strtotime($request->Fecha_Nacimiento)),
            'Numero_Licencia_Conducir' => $request->Numero_Licencia_Conducir,
            'Numero_Permiso_Trabajo' => $request->Numero_Permiso_Trabajo,
            'Fecha_Contratacion' => $request->Fecha_Contratacion == null ? '0000-00-00' : date('Y-m-d', strtotime($request->Fecha_Contratacion)),
            'Fecha_Expiracion_Trabajo' => $request->Fecha_Expiracion_Trabajo == null ? '0000-00-00' : date('Y-m-d', strtotime($request->Fecha_Expiracion_Trabajo)),
            'Numero_Residente' => $request->Numero_Residente,
            'email' => $request->email,
            'Telefono' => $request->Telefono,
            'Celular' => $request->Celular,
            'Aux1' => $request->Aux1,
            'Aux2' => $request->Aux2,
            'Aux3' => $request->Aux3,
            'Aux4' => $request->Aux4,
            'Aux5' => $tipo->nombre,
            'Usuario' => $request->Usuario,
            'Password' => $request->Password,
            'P1' => $request->P1,
            'R1' => $request->R1,
            'P2' => $request->P2,
            'R2' => $request->R2,
            'P3' => $request->P3,
            'R3' => $request->R3,
            'Indice_produccion' => $request->Indice_produccion == null ? '0.00' : $request->Indice_produccion,
            'Not_Bon' => $request->Not_Bon == null ? '' : $request->Not_Bon,
            'Nro_Bono' => $request->Nro_Bono == null ? '' : $request->Nro_Bono,
            'Spec_Bon1' => $request->Spec_Bon1,
            'Extra_Mon1' => $request->Extra_Mon1,
            'Benefit1' => $request->Benefit1,
            'Extra_Mon2' => $request->Extra_Mon2 == null ? '' : $request->Extra_Mon2,
            'Benefit2' => $request->Benefit2,
            'empresa' => $request->empresa,
            'Rol_ID' => '["4"]',
            'status' => "1",
            'cargo_personal_id' => $request->cargo_personal_id,
            'tipo_personal_id' => $request->tipo_personal_id,
        ]);

        return redirect()->route('list.cardex');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $cargos = DB::table('cargo_personal')->get();
        $tipos_usuarios = DB::table('tipo_personal')->get();
        $tipo_eventos = Tipo_evento::where('estado', '1')->get();
        //dd($tipo_eventos);
        $personal = Personal::select(
            'personal.*',
            DB::raw('DATE_FORMAT(personal.Fecha_Nacimiento, "%m/%d/%Y") as Fecha_Nacimiento'),
            DB::raw('DATE_FORMAT(personal.Fecha_Expiracion_Trabajo, "%m/%d/%Y") as Fecha_Expiracion_Trabajo'),
            DB::raw('DATE_FORMAT(personal.Fecha_Contratacion, "%m/%d/%Y") as Fecha_Contratacion'),
            'empresas.Nombre as nombre_empresa',
            'cargo_personal.id as cargo_personal_id',
            'cargo_personal.nombre as cargo_personal_nombre',
            'tipo_personal.nombre as tipo_personal_nombre'
        )
            ->join('empresas', 'empresas.Emp_ID', 'personal.Emp_ID')
            ->leftjoin('tipo_personal', 'tipo_personal.id', 'personal.tipo_personal_id')
            ->leftjoin('cargo_personal', 'cargo_personal.id', 'personal.cargo_personal_id')
            ->where('Empleado_ID', $id)
            ->firstOrFail();
        //dd($personal);

        $movimiento_evento = Movimiento_evento::select(
            'movimientos_eventos.movimientos_eventos_id',
            'movimientos_eventos.cod_evento',
            'movimientos_eventos.Empleado_ID',
            DB::raw('DATE_FORMAT(movimientos_eventos.start_date, "%m/%d/%Y") as start_date'),
            DB::raw('DATE_FORMAT(movimientos_eventos.exp_date, "%m/%d/%Y") as exp_date'),
            'movimientos_eventos.note as nota_movimiento',
            'movimientos_eventos.raise_from',
            'movimientos_eventos.raise_to',
            'movimientos_eventos.doc_pdf',
            'evento.*',
            'tipo_evento.nombre as nombre_tipo'
        )
            ->join('evento', 'movimientos_eventos.cod_evento', 'evento.cod_evento')
            ->join('tipo_evento', 'tipo_evento.tipo_evento_id', 'evento.tipo_evento_id')
            ->where('movimientos_eventos.Empleado_ID', $id)
            ->where('movimientos_eventos.estado', '1')
            ->orderBy('movimientos_eventos.cod_evento', 'DESC')
            ->get();
        foreach ($movimiento_evento as $key => $movimiento) {
            $movimiento->docs = DB::table('movimientos_eventos_archivos')
                ->where('movimientos_eventos_archivos.movimientos_eventos_id', $movimiento->movimientos_eventos_id)
                ->get();
            foreach ($movimiento->docs as $key => $doc) {
                $doc = $doc;
                $doc->ext = $this->extencion_file($doc->imagen);
            }
        }
        /* $doc=DB::table('users') */

        return view('panel.cardex_personal.edit', compact('personal', 'movimiento_evento', 'tipo_eventos', 'tipos_usuarios', 'cargos'));
    }
    public function extencion_file($cadena)
    {
        $ext = explode('.', $cadena);
        return $ext[1];
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
            'Emp_ID' => 'required',
            'Nombre' => 'required',
            'Apellido_Paterno' => 'nullable',
            'Apellido_Materno' => 'nullable',
            'Nick_Name' => 'required',
            'Estado' => 'nullable',
            'Ciudad' => 'nullable',
            'Zip_Code' => 'nullable',
            'Calle' => 'nullable',
            'Numero' => 'nullable',
            'Cargo' => 'nullable',
            'Numero_Seguro_Social' => 'nullable',
            'Fecha_Nacimiento' => 'nullable',
            'Numero_Licencia_Conducir' => 'nullable',
            'Numero_Permiso_Trabajo' => 'nullable',
            'Fecha_Expiracion_Trabajo' => 'nullable',
            'Numero_Residente' => 'nullable',
            'email' => 'nullable',
            'Telefono' => 'nullable',
            'Celular' => 'nullable',
            'Aux1' => 'nullable',
            'Aux2' => 'nullable',
            'Aux3' => 'nullable',
            'Aux4' => 'nullable',
            'Aux5' => 'nullable',
            'Usuario' => 'nullable',
            'Password' => 'nullable',
            'P1' => 'nullable',
            'R1' => 'nullable',
            'P2' => 'nullable',
            'R2' => 'nullable',
            'P3' => 'nullable',
            'R3' => 'nullable',
            'Indice_produccion' => 'nullable',
            'Nro_Bono' => 'nullable',
            'Spec_Bon1' => 'nullable',
            'Extra_Mon1' => 'nullable',
            'Benefit1' => 'nullable',
            'Benefit2' => 'nullable',
            'empresa' => 'nullable',
            'Rol_ID' => 'nullable',

        );
        $messages = [
            'Emp_ID.required' => "The company field is required",
            'Nombre.required' => "The name field is required",
            'Apellido_Paterno.required' => "The Last name field is required",
            'Apellido_Materno.required' => "The Mother's last name field is required",
            'Nick_Name.required' => "The nick name field is required",
            'Estado.required' => "The civil status field is required",
            'Cargo.required' => "The position field is required",
            'Fecha_Nacimiento.required' => "The birth date field is required",
        ];
        //dd($request->all());
        $error = Validator::make($request->all(), $rules, $messages);
        if (request()->ajax() === true) {
            return response()->json(['errors' => $error->errors()->all()]);
        }
        //verificacion
        $cargo = DB::table('cargo_personal')->where('id', $request->cargo_personal_id)->first();
        $tipo = DB::table('tipo_personal')->where('id', $request->tipo_personal_id)->first();
        $actualizar = Personal::where('Empleado_ID', $id);
        if (auth()->user()->verificarRol([1, 10])) {
            $actualizar->update([
                'Emp_ID' => $request->Emp_ID,
                'Nombre' => $request->Nombre,
                'Apellido_Paterno' => $request->Apellido_Paterno,
                'Apellido_Materno' => $request->Apellido_Materno,
                'Nick_Name' => $request->Nick_Name,
                'Estado' => $request->Estado,
                'Ciudad' => $request->Ciudad,
                'Zip_Code' => $request->Zip_Code,
                'Calle' => $request->Calle,
                'Numero' => $request->Numero,
                'Cargo' => $cargo->nombre,
                'Numero_Seguro_Social' => $request->Numero_Seguro_Social,
                'Fecha_Nacimiento' => $request->Fecha_Nacimiento == null ? '0000-00-00' : date('Y-m-d', strtotime($request->Fecha_Nacimiento)),
                'Numero_Licencia_Conducir' => $request->Numero_Licencia_Conducir,
                'Numero_Permiso_Trabajo' => $request->Numero_Permiso_Trabajo,
                'Fecha_Contratacion' => $request->Fecha_Contratacion == null ? '0000-00-00' : date('Y-m-d', strtotime($request->Fecha_Contratacion)),
                'Fecha_Expiracion_Trabajo' => $request->Fecha_Expiracion_Trabajo == null ? '0000-00-00' : date('Y-m-d', strtotime($request->Fecha_Expiracion_Trabajo)),
                'Numero_Residente' => $request->Numero_Residente,
                'email' => $request->email,
                'Telefono' => $request->Telefono,
                'Celular' => $request->Celular,
                'Aux1' => $request->Aux1,
                'Aux2' => $request->Aux2,
                'Aux3' => $request->Aux3,
                'Aux4' => $request->Aux4,
                'Aux5' => $tipo->nombre,
                'Usuario' => $request->Usuario,
                'Password' => $request->Password,
                'P1' => $request->P1,
                'R1' => $request->R1,
                'P2' => $request->P2,
                'R2' => $request->R2,
                'P3' => $request->P3,
                'R3' => $request->R3,
                'Indice_produccion' => $request->Indice_produccion,
                'Not_Bon' => $request->Not_Bon == null ? '' : $request->Not_Bon,
                'Nro_Bono' => $request->Nro_Bono,
                'Spec_Bon1' => $request->Spec_Bon1,
                'Extra_Mon1' => $request->Extra_Mon1,
                'Benefit1' => $request->Benefit1,
                'Extra_Mon2' => $request->Extra_Mon2 == null ? '' : $request->Extra_Mon2,
                'Benefit2' => $request->Benefit2,
                'empresa' => $request->empresa,
                'cargo_personal_id' => $request->cargo_personal_id,
                'tipo_personal_id' => $request->tipo_personal_id,
            ]);

        } else {
            $actualizar->update([
                'Emp_ID' => $request->Emp_ID,
                'Nombre' => $request->Nombre,
                'Apellido_Paterno' => $request->Apellido_Paterno,
                'Apellido_Materno' => $request->Apellido_Materno,
                'Nick_Name' => $request->Nick_Name,
                'Estado' => $request->Estado,
                'Ciudad' => $request->Ciudad,
                'Zip_Code' => $request->Zip_Code,
                'Calle' => $request->Calle,
                'Numero' => $request->Number,
                'Cargo' => $cargo->nombre,
                'Numero_Seguro_Social' => $request->Numero_Seguro_Social,
                'Fecha_Nacimiento' => $request->Fecha_Nacimiento == null ? '0000-00-00' : date('Y-m-d', strtotime($request->Fecha_Nacimiento)),
                'Numero_Licencia_Conducir' => $request->Numero_Licencia_Conducir,
                'Numero_Permiso_Trabajo' => $request->Numero_Permiso_Trabajo,
                'Fecha_Contratacion' => $request->Fecha_Contratacion == null ? '0000-00-00' : date('Y-m-d', strtotime($request->Fecha_Contratacion)),
                'Fecha_Expiracion_Trabajo' => $request->Fecha_Expiracion_Trabajo == null ? '0000-00-00' : date('Y-m-d', strtotime($request->Fecha_Expiracion_Trabajo)),
                'Numero_Residente' => $request->Numero_Residente,
                'email' => $request->email,
                'Telefono' => $request->Telefono,
                'Celular' => $request->Celular,
                'Aux1' => $request->Aux1,
                'Aux2' => $request->Aux2,
                'Aux3' => $request->Aux3,
                'Aux4' => $request->Aux4,
                'Aux5' => $tipo->nombre,
                'Usuario' => $request->Usuario,
                'Password' => $request->Password,
                'P1' => $request->P1,
                'R1' => $request->R1,
                'P2' => $request->P2,
                'R2' => $request->R2,
                'P3' => $request->P3,
                'R3' => $request->R3,
                'cargo_personal_id' => $request->cargo_personal_id,
                'tipo_personal_id' => $request->tipo_personal_id,
            ]);
        }

        return redirect()->route('list.cardex');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $verificar = personal::where('personal.Empleado_ID', $id)
            ->where('movimientos_eventos.estado', '1')
            ->join('movimientos_eventos', 'movimientos_eventos.Empleado_ID', 'personal.Empleado_ID')
            ->get()
            ->toArray();
        if (empty($verificar)) {
            $data = Personal::findOrFail($id);
            $data->update(['status' => 0]);
            return response()->json([
                'status' => 'ok',
                'message' => 'Employee removed successfully',
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                "message" => "It cannot be deleted is record",
            ]);
        }

    }
    /**
     * notificacion
     * Este metodo trae notificaciones desde la base de datos
     * @return array[object]
     */
    public function notificacion()
    {
        //$this->userService->alert()
        //ragno de 30 dias para notificar
        if (auth()->user()->verificarRol([1, 5, 10])) {
            $fecha_actual = date("Y-m-d");
            $fecha_fin = date("Y-m-d", strtotime($fecha_actual . "+ 30 days"));
            $eventos = Movimiento_evento::select('movimientos_eventos.*', 'evento.*', 'personal.Nick_Name', 'tipo_evento.nombre as nombre_tipo')->join('evento', 'movimientos_eventos.cod_evento', 'evento.cod_evento')
                ->join('personal', 'personal.Empleado_ID', 'movimientos_eventos.Empleado_ID')
                ->join('tipo_evento', 'tipo_evento.tipo_evento_id', 'evento.tipo_evento_id')
                ->whereBetween('movimientos_eventos.exp_date', [$fecha_actual, $fecha_fin])
                ->where('personal.status', '1')
                ->get();
            $resultado = $this->validarDiasNotificacion($eventos, $fecha_actual);

            return response()->json($resultado);
        } else {
            return response()->json([]);
        }
    }
    /**
     * validarDiasNotificacion
     * valida deacuerdo a un rango de fechas la alerta de reporte
     * @param  mixed $sqlArray
     * @param  mixed $fecha_actual
     * @return array[object]
     */
    private function validarDiasNotificacion($sqlArray, $fecha_actual)
    {
        $resultado = [];
        foreach ($sqlArray as $val) {
            //validando si la fecha este en la fecha actual
            $alerta = $val->report_alert;
            $fecha_alerta = date("Y-m-d", strtotime($val->exp_date . "- $alerta days"));
            //dd($fecha_actual, $alerta, $val->exp_date, $fecha_alerta);
            if ($fecha_actual >= $fecha_alerta) {

                //dias por expirar
                $date1 = new DateTime($fecha_actual); //recreando fechas
                $date2 = new DateTime($fecha_alerta); //recreando fechas
                //dd($fecha_actual, $fecha_alerta);
                $diff = $date1->diff($date2); //diferencia de fechas

                //construyendo respuesta
                $personal = new stdClass();
                $personal->nombre = "has an event to expire | $val->nombre staff $val->Nick_Name";
                $personal->expiracion = "Expires in $diff->days days ";
                $personal->Empleado_ID = $val->Empleado_ID;
                $resultado[] = $personal;
            }
        }
        return $resultado;
    }

    public function otras_opciones()
    {
        return view('panel.cardex_personal.otros.list');
    }
    public function reorganizar()
    {

        $cargos = DB::table('cargo_personal')->get();
        foreach ($cargos as $key => $cargo) {
            $verificar = DB::table('personal')
                ->where('personal.Cargo', $cargo->nombre)
                ->get();
            foreach ($verificar as $key => $value) {
                $personal = DB::table('personal')
                    ->where('personal.Empleado_ID', $value->Empleado_ID)
                    ->update([
                        'cargo_personal_id' => $cargo->id,
                    ]);
            }
        }

        $tipo_personal = DB::table('tipo_personal')->get();
        foreach ($tipo_personal as $key => $tipo) {
            $verificar = DB::table('personal')
                ->where('personal.Aux5', $tipo->nombre)
                ->get();
            foreach ($verificar as $key => $value) {
                $personal = DB::table('personal')
                    ->where('personal.Empleado_ID', $value->Empleado_ID)
                    ->update([
                        'tipo_personal_id' => $tipo->id,
                    ]);
            }
        }
    }
    /**
     * subir imagen de goal a la base de datos y guardar en un directorio
     */
    public function upload_image($id, $type, Request $request, $nombre_camp = null)
    {
        $campo = ($nombre_camp) ? $nombre_camp : 'images';

        $preview = $config = $errors = [];
        $this->validate($request, [
            $campo => 'required',
        ]);

        if ($request->hasFile($campo)) {
            $allowedfileExtension = ['jpg', 'png', 'jpeg', 'pdf', 'docx', 'doc', 'xlsx', 'xls', 'csv'];
            $files = $request->file($campo);

            foreach ($files as $file) {

                $filename = $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension();
                $fileSize = $file->getSize();
                $check = in_array($extension, $allowedfileExtension);
                //dd($extension, $allowedfileExtension);
                if ($check) {
                    if ($extension == 'jpg' || $extension == 'png' || $extension == 'jpeg') {
                        $name_img = "$type-doc-$id-" . uniqid() . time() . "." . $extension;
                        $path = public_path() . '/docs/' . $name_img;
                        if ($fileSize > 1500000) {
                            $actual_image = Image::make(file_get_contents($file))->setFileInfoFromPath($file);
                            $height = $actual_image->height() / 4;
                            $width = $actual_image->width() / 4;
                            $actual_image->resize($width, $height)->orientate()->save($path);

                            $fileSize = $actual_image->filesize();
                        } else {
                            Image::make(file_get_contents($file))->setFileInfoFromPath($file)->orientate()->save($path);
                        }
                        $insert = DB::table('movimientos_eventos_archivos')->insertGetId([
                            'imagen' => $name_img,
                            'tipo' => $type,
                            'movimientos_eventos_id' => $id,
                            'caption' => $filename,
                            'size' => $fileSize,
                        ]);
                        if ($insert) {
                            $newFileUrl = url('/') . '/docs/' . $name_img;
                            $preview[] = $newFileUrl;
                            $config[] = [
                                'key' => $insert,
                                'caption' => $filename,
                                'size' => $fileSize,
                                'downloadUrl' => $newFileUrl, // the url to download the file
                                'url' => url("delete_image/$id/$type/cardex"), // server api to delete the file based on key
                            ];

                        } else {
                            $errors[] = $fileName;
                        }
                    } else {
                        $name_img = "$type-doc-$id-" . uniqid() . time() . "." . $extension;
                        //$path = public_path() . '/docs/' . $name_img;
                        $file->move(public_path('docs'), $name_img);

                        $insert = DB::table('movimientos_eventos_archivos')->insertGetId([
                            'imagen' => $name_img,
                            'tipo' => $type,
                            'movimientos_eventos_id' => $id,
                            'caption' => $filename,
                            'size' => $fileSize,
                        ]);
                        if ($insert) {
                            $newFileUrl = url('/') . '/docs/' . $name_img;
                            $preview[] = $newFileUrl;
                            $config[] = [
                                'key' => $insert,
                                'caption' => $filename,
                                'size' => $fileSize,
                                'downloadUrl' => $newFileUrl, // the url to download the file
                                'url' => url("delete_image/$id/$type/cardex"), // server api to delete the file based on key
                            ];
                        } else {
                            $errors[] = $fileName;
                        }
                    }
                } else {

                }
            }
        }
        $out = ['initialPreview' => $preview, 'initialPreviewConfig' => $config, 'initialPreviewAsData' => true];
        if (!empty($errors)) {
            $img = count($errors) === 1 ? 'file "' . $errors[0] . '" ' : 'files: "' . implode('", "', $errors) . '" ';
            $out['error'] = 'Oh snap! We could not upload the ' . $img . 'now. Please try again later.';
        }
        return $out;
    }
    public function get_images($id, $type)
    {
        $images = DB::table('movimientos_eventos_archivos')
            ->where('movimientos_eventos_id', $id)
            ->where('tipo', $type)
            ->get();
        $list = new stdClass();
        $list->initialPreview = [];
        $list->initialPreviewConfig = [];
        if ($images) {
            foreach ($images as $val) {
                $newFileUrl = url('/') . '/docs/' . $val->imagen;
                $list->initialPreview[] = $newFileUrl;
                $list->initialPreviewConfig[] = [
                    'caption' => $val->caption,
                    'size' => $val->size,
                    'downloadUrl' => $newFileUrl,
                    'url' => url("delete_image/$id/$type/cardex"),
                    'key' => $val->m_imagen_id,
                ];
            }
        }
        return response()->json($list);
    }
    public function delete_image($id, $type, Request $request)
    {
        $query = DB::table('movimientos_eventos_archivos')
            ->where('m_imagen_id', $request->key)
            ->where('tipo', $type)
            ->where('movimientos_eventos_id', $id);
        $imagen = $query->first();
        if ($imagen) {
            $path = public_path() . '/uploads/' . $imagen->imagen;
            if (File::exists($path) && $imagen->imagen) {
                File::delete($path);
            }
            $query->delete();
            return response()->json([
                'success' => 'Successfully removed the file',
            ]);
        }
        return response()->json([
            'error' => 'Error, the image could not be deleted',
        ]);
    }
}
