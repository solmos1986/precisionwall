<?php

namespace App\Http\Controllers;

use App\Actividad;
use App\ContactoProyecto;
use App\Exports\reportTicket\resume_Ticket;
use App\Material;
use App\Personal;
use App\Razon_Trabajo;
use App\Ticket;
use App\Tipo_trabajo;
use DataTables;
use DB;
use File;
use Illuminate\Http\Request;
use Image;
use Maatwebsite\Excel\Excel;
use Mail;
use PDF;
use Validator;
use \stdClass;

class TicketController extends Controller
{
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
    public function index($id, Request $request)
    {
        if (request()->ajax()) {
            $data = Ticket::select(
                'proyectos.*',
                'ticket.*',
                'personal.Usuario as username',
                DB::raw("CONVERT(ticket.num,char) as num"),
            )
                ->where('actividad_id', $id)
                ->where('ticket.estado', 'creado')
                ->when(!auth()->user()->verificarRol([1,10]), function ($query) {
                    return $query->where(function ($q) {
                        $q->where('ticket.empleado_id', auth()->user()->Empleado_ID)
                            ->orWhere('proyectos.Foreman_ID', auth()->user()->Empleado_ID)
                            ->orWhere('proyectos.Lead_ID', auth()->user()->Empleado_ID)
                            ->orWhere('proyectos.Coordinador_Obra_ID', auth()->user()->Empleado_ID)
                            ->orWhere('proyectos.Coordinador_ID', auth()->user()->Empleado_ID);
                    });
                })
                ->join('proyectos', 'ticket.proyecto_id', 'proyectos.Pro_ID')
                ->join('personal', 'personal.Empleado_ID', 'ticket.empleado_id')
                ->orderBy('ticket.fecha_ticket', 'ASC')
                ->get();
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('firma_cliente', function ($data) {
                    $html = ($data->firma_cliente) ? '<span class="badge badge-success">Yes</span>' : '<span class="badge badge-danger">No</span>';
                    return $html;
                })
                ->addColumn('firma_foreman', function ($data) {
                    $html = ($data->firma_foreman) ? '<span class="badge badge-success">Yes</span>' : '<span class="badge badge-danger">No</span>';
                    return $html;
                })
                ->addColumn('acciones', function ($data) {
                    $data->subempresa = $data->Nombre . " - TICKET#" . $data->num;
                    $button = "
                        <a href='" . route('show.ticket', ['id' => $data->ticket_id]) . "'><i class='fas fa-eye ms-text-primary'></i></a>
                        <a href='" . route('edit.ticket', ['id' => $data->ticket_id]) . "'><i class='fas fa-pencil-alt ms-text-warning'></i></a>
                        <i class='far fa-trash-alt ms-text-danger delete cursor-pointer' data-id='$data->ticket_id' title='Delete'></i>
                        <a href='" . route('pdf.ticket', ['id' => $data->ticket_id]) . "'><i class='fas fa-file-download ms-text-success'></i></a>
                        <i class='fas fa-envelope ms-text-secondary cursor-pointer send-mail' data-num='$data->subempresa' data-id='$data->ticket_id' data-project='$data->proyecto_id' data-project='$data->nombre' title='Send Mail'></i>
                        ";
                    return $button;
                })
                ->addColumn('inicio', function ($data) {
                    $button = "<i class='fas fa-images text-info upload_image cursor-pointer' data-image='inicio' data-id='$data->ticket_id'></i>";
                    return $button;
                })
                ->addColumn('d_num', function ($data) {
                    if ($data->delete == 1) {
                        $datt = $data->num . "-void";
                    } else {
                        $datt = $data->num;
                    }
                    return $datt;
                })
                ->addColumn('final', function ($data) {
                    $button = "<i class='fas fa-images text-info upload_image cursor-pointer' data-image='final' data-id='$data->ticket_id'></i>";
                    return $button;
                })

                ->editColumn('fecha_ticket', function ($data) {
                    return $data->fecha_ticket ? date('m-d-Y', strtotime($data->fecha_ticket)) : null;
                })
                ->rawColumns(['acciones', 'firma_cliente', 'firma_foreman', 'inicio', 'final'])
                ->make(true);
        }
        $proyecto = Actividad::where('actividades.Actividad_ID', $id)
            ->join('proyectos', 'actividades.Pro_ID', 'proyectos.Pro_ID')
            ->first();

        return view('panel.ticket.list', compact('id', 'proyecto'));
    }

    public function index2(Request $request)
    {
        if (request()->ajax()) {
            $data = Ticket::select(
                'proyectos.*',
                'ticket.*',
                DB::raw("CONCAT( personal.Nick_Name) as username"),
                DB::raw("CONVERT(ticket.num,char) as numero_ticket"),
            )
            //filtrando si hay proyectos
                ->when(!empty(request()->proyecto), function ($q) {
                    return $q->where('proyectos.Nombre', 'like', '%' . request()->proyecto . '%');
                })
            //filtrando por descripcion
                ->when(!empty(request()->descripcion), function ($q) {
                    return $q->where('ticket.descripcion', 'like', '%' . request()->descripcion . '%');
                })
            //filtrando por fecha
                ->when(!empty(request()->from_date), function ($q) {
                    $from = date('Y-m-d', strtotime(request()->from_date));
                    $to = date('Y-m-d', strtotime(request()->to_date));
                    return $q->whereBetween('ticket.fecha_ticket', [$from, $to]);
                })
            //filtro de descargas de archivos
                ->when(!is_null($request->query('uso_descarga')), function ($q) use ($request) {
                    return $q->where('ticket.Descarga', '=', $request->uso_descarga);
                })
            //fin de filtro de descargas de archivos
                ->when(!auth()->user()->verificarRol([1,10]), function ($query) {
                    return $query->where(function ($q) {
                        $q->where('ticket.empleado_id', auth()->user()->Empleado_ID)
                        /* ->orWhere('proyectos.Foreman_ID', auth()->user()->Empleado_ID)
                    ->orWhere('proyectos.Lead_ID', auth()->user()->Empleado_ID)
                    ->orWhere('proyectos.Coordinador_Obra_ID', auth()->user()->Empleado_ID)
                    ->orWhere('proyectos.Coordinador_ID', auth()->user()->Empleado_ID) */    ;
                    });
                })
                ->where('ticket.estado', 'creado')
                ->join('proyectos', 'ticket.proyecto_id', 'proyectos.Pro_ID')
                ->join('personal', 'personal.Empleado_ID', 'ticket.empleado_id')
                ->orderBy('ticket.fecha_ticket', 'ASC')
                ->get();
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('firma_cliente', function ($data) {
                    $html = ($data->firma_cliente) ? '<span class="badge badge-success">Yes</span>' : '<span class="badge badge-danger">No</span>';
                    return $html;
                })
                ->addColumn('firma_foreman', function ($data) {
                    $html = ($data->firma_foreman) ? '<span class="badge badge-success">Yes</span>' : '<span class="badge badge-danger">No</span>';
                    return $html;
                })
                ->addColumn('acciones', function ($data) {
                    if ($data->delete == 1) {
                        $data->numero_ticket = $data->numero_ticket . "-void";
                    } else {
                        $data->numero_ticket = $data->numero_ticket;
                    }
                    $data->subempresa = $data->Nombre . " -TICKET#" . $data->numero_ticket;
                    /* validar uso */
                    $uso_descarga = $this->get_cantidad_uso($data->ticket_id, 'descarga');
                    $uso_email = $this->get_cantidad_uso_email($data, 'email');
                    /* validar uso */
                    $button = "
                    <div class='icon-badge-group'>
                        <a href='" . route('show.ticket.btn') . "' class='show_ticket'  data-id='$data->ticket_id' ><i class='fas fa-eye ms-text-primary'></i></a>
                        <a href='" . route('edit.ticket', ['id' => $data->ticket_id]) . "'><i class='fas fa-pencil-alt ms-text-warning'></i></a>
                        <i class='far fa-trash-alt ms-text-danger delete cursor-pointer' data-id='$data->ticket_id' title='Delete'></i>
                        $uso_descarga
                        $uso_email
                    </div>
                        ";
                    return $button;
                })
                ->addColumn('inicio', function ($data) {
                    $button = "<i class='fas fa-images text-info upload_image cursor-pointer' data-image='inicio' data-id='$data->ticket_id'></i>";
                    return $button;
                })
                ->addColumn('final', function ($data) {
                    $button = "<i class='fas fa-images text-info upload_image cursor-pointer' data-image='final' data-id='$data->ticket_id'></i>";
                    return $button;
                })
                ->addColumn('d_num', function ($data) {
                    if ($data->delete == 1) {
                        $datt = $data->numero_ticket . "-void";
                    } else {
                        $datt = $data->numero_ticket;
                    }
                    return $datt;
                })
                ->addColumn('check_email', function ($data) {
                    $data->subempresa = $data->Nombre . " -TICKET#" . $data->numero_ticket;
                    $button = "<input type='checkbox' value='$data->ticket_id' data-num=' $data->subempresa' data-proyecto='$data->Pro_ID'>";

                    return $button;
                })
                ->editColumn('fecha_ticket', function ($data) {
                    return $data->fecha_ticket ? date('l m/d/Y', strtotime($data->fecha_ticket)) : null;
                })
                ->rawColumns(['acciones', 'd_num', 'firma_cliente', 'firma_foreman', 'inicio', 'final', 'check_email'])
                ->make(true);
        }

        return view('panel.ticket.my-list');
    }

    public function get_config_mail($id)
    {
        $config = DB::table('configuration')->select('body_ticket_email', 'title_ticket_email')->find(1);
        $emails = DB::table('proyectos')
            ->selectRaw("
        f.email as Foreman_mail,
        l.email as Lead_mail,
        c_o.email as Coordinador_Obra_mail,
        c.email as Pwtsuper_mail")
            ->where('Pro_ID', $id)
            ->leftJoin('personal as f', 'f.Empleado_ID', 'proyectos.Foreman_ID')
            ->leftJoin('personal as l', 'l.Empleado_ID', 'proyectos.Lead_ID')
            ->leftJoin('personal as c', 'c.Empleado_ID', 'proyectos.Coordinador_ID')
            ->leftJoin('personal as c_o', 'c_o.Empleado_ID', 'proyectos.Coordinador_Obra_ID')
            ->first();

        $email_contac = ContactoProyecto::select('email')->where('Pro_ID', $id)
            ->where('tipo_contacto.nombre', 'ticket')
            ->join('tipo_contacto', 'tipo_contacto.id_tipo_contacto', 'contacto_proyecto.tipo_contacto')
            ->join('personal', 'personal.Empleado_ID', 'contacto_proyecto.Empleado_ID')
            ->get();

        return response()->json([
            'config' => $config,
            'emails' => $emails,
            'email_contac' => $email_contac,
        ]);
    }
    public function all_email(Request $request, $id)
    {
        $proyecto = DB::table('proyectos')->where('proyectos.Pro_ID', $id)->first();

        if (!isset($request->searchTerm)) {
            $all_email = DB::table('personal')
                ->select(
                    'personal.Empleado_ID',
                    'personal.email',
                    'personal.Nick_Name',
                    'empresas.Nombre'
                )
                ->where('personal.Aux5', 'LIKE', 'F%')
                ->join('empresas', 'empresas.Emp_ID', 'personal.Emp_ID')
                ->where(function ($query) use ($proyecto) {
                    $query->orWhere('personal.Emp_ID', $proyecto->Emp_ID)
                        ->orWhere('personal.Emp_ID', '6');
                })
                ->orderBy('empresas.Nombre')
                ->orderBy('personal.email')
                ->get();
        } else {
            $all_email = DB::table('personal')
                ->select(
                    'personal.Empleado_ID',
                    'personal.email',
                    'personal.Nick_Name',
                    'empresas.Nombre'
                )
                ->where(function ($query) use ($proyecto, $request) {
                    $query->orWhere('personal.Nick_Name', 'LIKE', "%$request->searchTerm%")
                        ->orWhere('personal.email', 'LIKE', "%$request->searchTerm%")
                        ->orWhere('empresas.Nombre', 'LIKE', "%$request->searchTerm%");
                })
                ->where('personal.Aux5', 'LIKE', 'F%')
                ->join('empresas', 'empresas.Emp_ID', 'personal.Emp_ID')
                ->where(function ($query) use ($proyecto) {
                    $query->orWhere('personal.Emp_ID', $proyecto->Emp_ID)
                        ->orWhere('personal.Emp_ID', '6');
                })
                ->orderBy('empresas.Nombre')
                ->orderBy('personal.email')
                ->get();
        }
        $data = [];
        foreach ($all_email as $row) {
            if ($row->email != null && $row->email != '') {
                $data[] = array(
                    "id" => $row->Empleado_ID,
                    "text" => "$row->email / $row->Nick_Name / $row->Nombre",
                    "email" => $row->email,
                );
            }
        }
        return response()->json($data);
    }
    public function multiple_post_config_mail(Request $request)
    {
        $config = DB::table('configuration')->select('body_ticket_email', 'title_ticket_email')->find(1);
        $emails = DB::table('proyectos')
            ->selectRaw("
        f.email as Foreman_mail,
        l.email as Lead_mail,
        c_o.email as Coordinador_Obra_mail,
        c.email as Pwtsuper_mail")
            ->whereIn('Pro_ID', $request->proyecto)
            ->leftJoin('personal as f', 'f.Empleado_ID', 'proyectos.Foreman_ID')
            ->leftJoin('personal as l', 'l.Empleado_ID', 'proyectos.Lead_ID')
            ->leftJoin('personal as c', 'c.Empleado_ID', 'proyectos.Coordinador_ID')
            ->leftJoin('personal as c_o', 'c_o.Empleado_ID', 'proyectos.Coordinador_Obra_ID')
            ->first();
        $email_contac = ContactoProyecto::select('email')->where('Pro_ID', $request->proyecto)
            ->where('tipo_contacto.nombre', 'ticket')
            ->join('tipo_contacto', 'tipo_contacto.id_tipo_contacto', 'contacto_proyecto.tipo_contacto')
            ->join('personal', 'personal.Empleado_ID', 'contacto_proyecto.Empleado_ID')
            ->get();

        return response()->json([
            'config' => $config,
            'emails' => $emails,
            'email_contac' => $email_contac,
        ]);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id)
    {
        $proyecto = Actividad::select('actividades.*', 'empresas.Codigo as empresa', 'proyectos.*')->where('Actividad_ID', $id)
            ->join('proyectos', 'actividades.Pro_ID', 'proyectos.Pro_ID')
            ->join('empresas', 'empresas.Emp_ID', 'proyectos.Emp_ID')
            ->first();

        $ticket = Ticket::select('ticket_id')->where('estado', 'pendiente')
            ->where('actividad_id', $proyecto->Actividad_ID)
            ->where('proyecto_id', $proyecto->Pro_ID)
            ->where('empleado_id', auth()->user()->Empleado_ID)
            ->pluck('ticket_id')->first();

        if (!$ticket) {
            $ticket = Ticket::insertGetId([
                'actividad_id' => $proyecto->Actividad_ID,
                'proyecto_id' => $proyecto->Pro_ID,
                'empleado_id' => auth()->user()->Empleado_ID,
                'estado' => 'pendiente',
            ]);
        }

        $n_ticket = Ticket::where('estado', 'creado')->where('proyecto_id', $proyecto->Pro_ID)->count() + 1;
        $address = trim("$proyecto->Ciudad, $proyecto->Zip_Code, $proyecto->Calle");
        $foreman = Personal::where('Empleado_ID', $proyecto->Foreman_ID)->first();
        $foreman_name = (empty($foreman)) ? "" : trim($foreman->Nombre . $foreman->Apellido_Paterno . $foreman->Apellido_Materno);

        return view('panel.ticket.new', compact('id', 'proyecto', 'ticket', 'n_ticket', 'foreman_name', 'address'));
    }

    public function get_materiales(Request $request, $id)
    {
        if (!isset($request->searchTerm)) {
            $materiales = Material::select('materiales.*')
                ->where(function ($q) use ($id) {
                    $q->where('materiales.Pro_ID', $id)
                        ->Orwhere('Cat_ID', 7);
                })
                ->leftJoin('actividades', 'materiales.Pro_ID', 'actividades.Pro_ID')
                ->distinct('materiales.Mat_ID')
                ->get();
        } else {
            $materiales = Material::select('materiales.*')
                ->where('Denominacion', 'like', '%' . $request->searchTerm . '%')
                ->where(function ($q) use ($id) {
                    $q->where('materiales.Pro_ID', $id)
                        ->Orwhere('Cat_ID', 7);
                })
                ->leftJoin('actividades', 'materiales.Pro_ID', 'actividades.Pro_ID')
                ->distinct('materiales.Mat_ID')
                ->get();
        }
        $data = [];
        foreach ($materiales as $row) {
            $data[] = array(
                "id" => $row->Mat_ID,
                "text" => "$row->Denominacion - $row->Unidad_Medida",
                "Unidad_Medida" => $row->Unidad_Medida,
            );
        }
        return response()->json($data);
    }
    public function get_class_workers(Request $request)
    {
        if (!isset($request->searchTerm)) {
            $tipo_trabajo = Tipo_trabajo::all();
        } else {
            $tipo_trabajo = Tipo_trabajo::where('nombre', 'like', '%' . $request->searchTerm . '%')->get();
        }
        $data = [];
        foreach ($tipo_trabajo as $row) {
            $data[] = array(
                "id" => $row->id,
                "text" => $row->nombre,
            );
        }
        return response()->json($data);
    }
    public function get_empleoyes(Request $request)
    {
        if (!isset($request->searchTerm)) {
            $tipo_trabajo = Personal::selectRaw("Empleado_ID, CONCAT(COALESCE(personal.Nombre,''), ' ',  COALESCE(personal.Apellido_Paterno,''), ' ',  COALESCE(personal.Apellido_Materno,'')) as Foreman, personal.email")
                ->get();
        } else {
            $tipo_trabajo = Personal::selectRaw("Empleado_ID, CONCAT(COALESCE(personal.Nombre,''), ' ',  COALESCE(personal.Apellido_Paterno,''), ' ',  COALESCE(personal.Apellido_Materno,'')) as Foreman, personal.email")
                ->whereRaw("CONCAT(COALESCE(personal.Nombre,''), ' ',  COALESCE(personal.Apellido_Paterno,''), ' ',  COALESCE(personal.Apellido_Materno,'')) like '%$request->searchTerm%'")->get();
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
    public function get_razon(Request $request, $tipo, $id)
    {
        $data = [];
        if ($tipo != "where") {
            if (!isset($request->searchTerm)) {
                $razon = Razon_Trabajo::where('tipo', $tipo)->get();
            } else {
                $razon = Razon_Trabajo::where('tipo', $tipo)->where('descripcion', 'like', '%' . $request->searchTerm . '%')->get();
            }
            foreach ($razon as $row) {
                $data[] = array(
                    "id" => $row->id,
                    "text" => ($row->descripcion_traduccion) ? "$row->descripcion // $row->descripcion_traduccion" : $row->descripcion,
                    "descripcion" => $row->descripcion,
                );
            }
        } else {
            if (!isset($request->searchTerm)) {
                $razon = DB::table('area_control')
                    ->select('area_control.Nombre as area', 'edificios.Nombre as edificio', 'floor.Nombre as floor')
                    ->where('area_control.Pro_ID', $id)
                    ->leftJoin('floor', 'area_control.Floor_ID', 'floor.Floor_ID')
                    ->join('edificios', 'floor.Edificio_ID', 'edificios.Edificio_ID')
                    ->get();
            } else {
                $razon = DB::table('area_control')
                    ->select('area_control.Nombre as area', 'edificios.Nombre as edificio', 'floor.Nombre as floor')
                    ->where('area_control.Pro_ID', $id)
                    ->where('floor.Nombre', 'like', '%' . $request->searchTerm . '%')
                    ->Orwhere('area_control.Nombre', 'like', '%' . $request->searchTerm . '%')
                    ->Orwhere('edificios.Nombre', 'like', '%' . $request->searchTerm . '%')
                    ->join('floor', 'area_control.Floor_ID', 'floor.Floor_ID')
                    ->join('edificios', 'floor.Edificio_ID', 'edificios.Edificio_ID')
                    ->get();
            }
            foreach ($razon as $row) {
                $data[] = array(
                    "id" => $row->area,
                    "text" => "$row->edificio $row->floor $row->area",
                );
            }
        }

        return response()->json($data);
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $id)
    {
        $rules = array(
            'ticket_id' => 'required',
            'foreman_name' => 'required',
            'date_work' => 'required|date_format:m/d/Y',
            'input_signature_fore' => 'nullable',
            'horario' => 'required',
            'descripcion' => 'required',
            'n_material' => 'nullable|array',
            'material_id' => 'nullable|array',
            'n_workers' => 'nullable|array',
            'class_id' => 'nullable|array',
            'reg_hours' => 'nullable|array',
            'premium_hours' => 'nullable|array',
            'out_hours' => 'nullable|array',
            'supername' => 'nullable',
            'input_signature_super' => 'nullable',
            'date_super' => 'nullable|date_format:m/d/Y',
            'empleado_id' => 'required',
            'images_inicio' => 'nullable',
            'images_inicio.*' => 'mimes:jpg,jpeg,png',
            'images_final' => 'nullable',
            'images_final.*' => 'mimes:jpg,jpeg,png',
            'pco' => 'nullable',
        );

        $error = Validator::make($request->all(), $rules);

        if (request()->ajax() === true) {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        $query = Actividad::where('Actividad_ID', $id)->first();
        $name_img_client = "";
        $name_img_foremant = "";
        if (!auth()->user()->verificarRol([1,10])) {
            $empleado_id = auth()->user()->Empleado_ID;
        } else {
            $empleado_id = $request->empleado_id;
        }

        if (!empty($request->input_signature_fore)) {
            $name_img_foremant = "signature-foreman-" . time() . ".jpg";
            $path = public_path() . '/signatures/empleoye/' . $name_img_foremant;
            Image::make(file_get_contents($request->input_signature_fore))->save($path);
        }
        if (!empty($request->input_signature_super)) {
            $name_img_client = "signature-client-" . time() . ".jpg";
            $path = public_path() . '/signatures/client/' . $name_img_client;
            Image::make(file_get_contents($request->input_signature_super))->save($path);
        }

        $ticket = Ticket::find($request->ticket_id);
        $n_ticket = Ticket::where('estado', 'creado')->where('proyecto_id', $ticket->proyecto_id)->count() + 1;
        $ticket->update([
            'num' => $n_ticket,
            'horario' => $request->horario,
            'descripcion' => $request->descripcion,
            'foreman_name' => $request->foreman_name,
            'superintendent_name' => $request->supername,
            'firma_cliente' => $name_img_client,
            'firma_foreman' => $name_img_foremant,
            'estado' => "creado",
            'fecha_ticket' => ($request->date_work) ? date('Y-m-d', strtotime($request->date_work)) : null,
            'fecha_finalizado' => ($request->date_super) ? date('Y-m-d', strtotime($request->date_super)) : null,
            'pco' => $request->pco,
            'actividad_id' => $query->Actividad_ID,
            'proyecto_id' => $query->Pro_ID,
            'empleado_id' => $empleado_id,
            'delete' => 0,
        ]);

        if ($ticket->ticket_id) {
            if ($request->material_id) {
                foreach ($request->material_id as $key => $val) {
                    DB::table('ticket_material')->insert([
                        'cantidad' => $request->n_material[$key],
                        'material_id' => $val,
                        'ticket_id' => $ticket->ticket_id,
                    ]);
                }
            }
            if ($request->class_id) {
                foreach ($request->class_id as $key => $val) {
                    DB::table('ticket_trabajadores')->insert([
                        'profesion_id' => $val,
                        'n_worker' => $request->n_workers[$key],
                        'reg_hours' => $request->reg_hours[$key],
                        'premium_hours' => $request->premium_hours[$key],
                        'out_hours' => $request->out_hours[$key],
                        'prepaid_hours' => $request->prepaid_hours[$key],
                        'ticket_id' => $ticket->ticket_id,
                    ]);
                }
            }
            if ($request->is_mail == true) {
                $this->sendmailticket($request, $ticket->ticket_id);
            }
            return redirect(route('listar.mis.tickets'))->with('success', 'The ticket has been updated');
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
        $ticket = Ticket::select('ticket.*', 'proyectos.*', 'empresas.Nombre as empresa')->where('ticket_id', $id)
            ->join('proyectos', 'proyectos.Pro_ID', 'ticket.proyecto_id')
            ->join('empresas', 'proyectos.Emp_ID', 'empresas.Emp_ID')
            ->first();
        $address = trim("$ticket->Ciudad, $ticket->Zip_Code, $ticket->Calle");
        $materiales = DB::table('ticket_material')
            ->where('ticket_id', $id)
            ->join('materiales', 'materiales.Mat_ID', 'ticket_material.material_id')
            ->get();
        $trabajadores = DB::table('ticket_trabajadores')->where('ticket_id', $id)
            ->join('tipo_trabajo', 'tipo_trabajo.id', 'ticket_trabajadores.profesion_id')
            ->get();
        $horas_all = DB::table('ticket_trabajadores')->where('ticket.ticket_id', '<', $id)
            ->where('ticket.proyecto_id', $ticket->proyecto_id)
            ->join('ticket', 'ticket_trabajadores.ticket_id', 'ticket.ticket_id')
            ->join('tipo_trabajo', 'tipo_trabajo.id', 'ticket_trabajadores.profesion_id')
            ->get();
        $t_reg_hours = 0;
        $t_premium_hours = 0;
        $t_out_hours = 0;
        $t_prepaid_hours = 0;
        $ticket->subempresa = $ticket->Nombre . " - TICKET#0" . $ticket->num;
        foreach ($horas_all as $val) {
            $t_reg_hours += $val->reg_hours * $val->n_worker;
            $t_premium_hours += $val->premium_hours * $val->n_worker;
            $t_out_hours += $val->out_hours * $val->n_worker;
            $t_prepaid_hours += $val->prepaid_hours * $val->n_worker;
        }

        $img_start = DB::table('ticket_imagen')->where('ticket_id', $id)->where('tipo', 'inicio')->get();
        $img_final = DB::table('ticket_imagen')->where('ticket_id', $id)->where('tipo', 'final')->get();

        return view('panel.ticket.view', compact('id', 'ticket', 'address', 'materiales', 'trabajadores', 'img_start', 'img_final', 't_reg_hours', 't_premium_hours', 't_out_hours', 't_prepaid_hours'));
    }

    public function show_btn(Request $request)
    {
        $ticket = explode(', ', $request->query('tickets'));
        $id = $request->query('view');
        $ticket = Ticket::select(
            'ticket.*',
            'proyectos.*',
            'empresas.Nombre as empresa',
            DB::raw("CONVERT(ticket.num,char) as num"),
        )->where('ticket_id', $id)
            ->join('proyectos', 'proyectos.Pro_ID', 'ticket.proyecto_id')
            ->join('empresas', 'proyectos.Emp_ID', 'empresas.Emp_ID')
            ->first();
        if ($ticket->delete == 1) {
            $ticket->num = $ticket->num . "-void";
        } else {
            $ticket->num = $ticket->num;
        }
        $address = trim("$ticket->Ciudad, $ticket->Zip_Code, $ticket->Calle");
        $materiales = DB::table('ticket_material')
            ->where('ticket_id', $id)
            ->join('materiales', 'materiales.Mat_ID', 'ticket_material.material_id')
            ->get();
        $trabajadores = DB::table('ticket_trabajadores')->where('ticket_id', $id)
            ->join('tipo_trabajo', 'tipo_trabajo.id', 'ticket_trabajadores.profesion_id')
            ->get();
        $horas_all = DB::table('ticket_trabajadores')->where('ticket.ticket_id', '<', $id)
            ->where('ticket.proyecto_id', $ticket->proyecto_id)
            ->join('ticket', 'ticket_trabajadores.ticket_id', 'ticket.ticket_id')
            ->join('tipo_trabajo', 'tipo_trabajo.id', 'ticket_trabajadores.profesion_id')
            ->get();
        $t_reg_hours = 0;
        $t_premium_hours = 0;
        $t_out_hours = 0;
        $t_prepaid_hours = 0;
        $ticket->subempresa = $ticket->Nombre . " - TICKET#0" . $ticket->num;
        foreach ($horas_all as $val) {
            $t_reg_hours += $val->reg_hours * $val->n_worker;
            $t_premium_hours += $val->premium_hours * $val->n_worker;
            $t_out_hours += $val->out_hours * $val->n_worker;
            $t_prepaid_hours += $val->prepaid_hours * $val->n_worker;
        }

        $img_start = DB::table('ticket_imagen')->where('ticket_id', $id)->where('tipo', 'inicio')->get()->toArray();
        $img_final = DB::table('ticket_imagen')->where('ticket_id', $id)->where('tipo', 'final')->get()->toArray();

        return view('panel.ticket.view_btn', compact('id', 'ticket', 'address', 'materiales', 'trabajadores', 'img_start', 'img_final', 't_reg_hours', 't_premium_hours', 't_out_hours', 't_prepaid_hours'));
    }
    public function pdf($id, $view = false)
    {
        $ticket = Ticket::select(
            'ticket.*',
            'proyectos.*',
            'empresas.Nombre as empresa',
            DB::raw("CONVERT(ticket.num,char) as num"),
        )->where('ticket_id', $id)
            ->join('proyectos', 'proyectos.Pro_ID', 'ticket.proyecto_id')
            ->join('empresas', 'proyectos.Emp_ID', 'empresas.Emp_ID')
            ->first();
        if ($ticket->delete == 1) {
            $ticket->num = $ticket->num . "-void";
        } else {
            $ticket->num = $ticket->num;
        }
        $address = trim("$ticket->Ciudad, $ticket->Zip_Code, $ticket->Calle");
        $materiales = DB::table('ticket_material')
            ->where('ticket_id', $id)
            ->join('materiales', 'materiales.Mat_ID', 'ticket_material.material_id')
            ->get();
        $trabajadores = DB::table('ticket_trabajadores')->where('ticket_id', $id)
            ->join('tipo_trabajo', 'tipo_trabajo.id', 'ticket_trabajadores.profesion_id')
            ->get();

        $horas_all = DB::table('ticket_trabajadores')->where('ticket.ticket_id', '<', $id)
            ->where('ticket.proyecto_id', $ticket->proyecto_id)
            ->join('ticket', 'ticket_trabajadores.ticket_id', 'ticket.ticket_id')
            ->join('tipo_trabajo', 'tipo_trabajo.id', 'ticket_trabajadores.profesion_id')
            ->get();
        $t_reg_hours = 0;
        $t_premium_hours = 0;
        $t_out_hours = 0;
        $t_prepaid_hours = 0;
        $ticket->subempresa = substr(str_replace(' ', ' ', strtoupper($ticket->Nombre)), 0, 15);
        foreach ($horas_all as $val) {
            $t_reg_hours += $val->reg_hours * $val->n_worker;
            $t_premium_hours += $val->premium_hours * $val->n_worker;
            $t_out_hours += $val->out_hours * $val->n_worker;
            $t_prepaid_hours += $val->prepaid_hours * $val->n_worker;
        }

        $img_start = DB::table('ticket_imagen')->where('ticket_id', $id)->where('tipo', 'inicio')->get()->toArray();
        $img_final = DB::table('ticket_imagen')->where('ticket_id', $id)->where('tipo', 'final')->get()->toArray();

        $pdf = PDF::loadView('panel.ticket.pdf', compact('ticket', 'address', 'materiales', 'trabajadores', 'img_start', 'img_final', 't_reg_hours', 't_premium_hours', 't_out_hours', 't_prepaid_hours'))->setPaper('letter')->setWarnings(false);

        if ($view === true) {
            return $pdf;
        }

        $this->añadir_cantidad_uso($ticket->ticket_id, 'descarga');
        return $pdf->download("$ticket->subempresa-TICKET#" . $ticket->num . ".pdf");
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $ticket = Ticket::select('ticket.*', 'proyectos.*', 'empresas.Codigo as empresa')->where('ticket_id', $id)
            ->join('proyectos', 'proyectos.Pro_ID', 'ticket.proyecto_id')
            ->join('empresas', 'proyectos.Emp_ID', 'empresas.Emp_ID')
            ->first();
        $address = trim("$ticket->Ciudad, $ticket->Zip_Code, $ticket->Calle");
        $materiales = DB::table('ticket_material')
            ->where('ticket_id', $id)
            ->join('materiales', 'materiales.Mat_ID', 'ticket_material.material_id')
            ->get();
        $trabajadores = DB::table('ticket_trabajadores')->where('ticket_id', $id)
            ->join('tipo_trabajo', 'tipo_trabajo.id', 'ticket_trabajadores.profesion_id')
            ->get();
        $ticket->fecha_ticket = ($ticket->fecha_ticket) ? date("m/d/Y", strtotime($ticket->fecha_ticket)) : null;
        $ticket->fecha_finalizado = ($ticket->fecha_finalizado) ? date("m/d/Y", strtotime($ticket->fecha_finalizado)) : null;

        return view('panel.ticket.edit', compact('ticket', 'address', 'materiales', 'trabajadores', 'id'));
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
            'foreman_name' => 'required',
            'date_work' => 'required|date_format:m/d/Y',
            'input_signature_fore' => 'nullable',
            'horario' => 'required',
            'descripcion' => 'required',
            'n_material' => 'nullable|array',
            'material_id' => 'nullable|array',
            'n_workers' => 'nullable|array',
            'class_id' => 'nullable|array',
            'reg_hours' => 'nullable|array',
            'out_hours' => 'nullable|array',
            'total_out_hours' => 'nullable|array',
            'supername' => 'nullable',
            'input_signature_super' => 'nullable',
            'date_super' => 'nullable|date_format:m/d/Y',
            'pco' => 'nullable',
        );

        $error = Validator::make($request->all(), $rules);

        if (request()->ajax() === true) {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        $ticket = Ticket::find($id);

        $name_img_client = "";
        $name_img_foremant = "";

        if (!empty($request->input_signature_fore)) {
            $image_path = public_path() . "/signatures/empleoye/" . $ticket->firma_foreman;
            if (File::exists($image_path) && $ticket->firma_foreman) {
                File::delete($image_path);
            }
            $name_img_foremant = "signature-foreman-" . time() . ".jpg";
            $path = public_path() . '/signatures/empleoye/' . $name_img_foremant;
            Image::make(file_get_contents($request->input_signature_fore))->save($path);
        } else {
            $name_img_foremant = ($ticket->firma_foreman) ? $ticket->firma_foreman : null;
        }
        if (!empty($request->input_signature_super)) {
            $image_path = public_path() . "/signatures/client/" . $ticket->firma_cliente;
            if (File::exists($image_path) && $ticket->firma_cliente) {
                File::delete($image_path);
            }
            $name_img_client = "signature-client-" . time() . ".jpg";
            $path = public_path() . '/signatures/client/' . $name_img_client;
            Image::make(file_get_contents($request->input_signature_super))->save($path);
        } else {
            $name_img_client = ($ticket->firma_cliente) ? $ticket->firma_cliente : null;
        }

        $ticket->update([
            'horario' => $request->horario,
            'descripcion' => $request->descripcion,
            'foreman_name' => $request->foreman_name,
            'superintendent_name' => $request->supername,
            'firma_cliente' => $name_img_client,
            'firma_foreman' => $name_img_foremant,
            'fecha_ticket' => ($request->date_work) ? date('Y-m-d', strtotime($request->date_work)) : null,
            'fecha_finalizado' => ($request->date_super) ? date('Y-m-d', strtotime($request->date_super)) : null,
            'pco' => $request->pco,
        ]);

        if ($ticket) {
            if ($request->material_id) {
                DB::table('ticket_material')->where('ticket_id', $id)->delete();
                foreach ($request->material_id as $key => $val) {
                    DB::table('ticket_material')->insert([
                        'cantidad' => $request->n_material[$key],
                        'material_id' => $val,
                        'ticket_id' => $id,
                    ]);
                }
            }
            if ($request->class_id) {
                DB::table('ticket_trabajadores')->where('ticket_id', $id)->delete();
                foreach ($request->class_id as $key => $val) {
                    DB::table('ticket_trabajadores')->insert([
                        'profesion_id' => $val,
                        'n_worker' => $request->n_workers[$key],
                        'reg_hours' => $request->reg_hours[$key],
                        'premium_hours' => $request->premium_hours[$key],
                        'out_hours' => $request->out_hours[$key],
                        'prepaid_hours' => $request->prepaid_hours[$key],
                        'ticket_id' => $id,
                    ]);
                }
            }

            if ($request->is_mail == true) {
                $this->sendmailticket($request, $id);
            }
            //return redirect(route('listar.tickets', ['id' => $ticket->actividad_id]))->with('success', 'The ticket has been updated');

            return redirect(route('listar.mis.tickets'))->with('success', 'The ticket has been updated');
        }
        return "error";
    }
    public function update_signature(Request $request, $id)
    {
        $ticket = Ticket::find($id);
        $name_img_client = "";
        $name_img_foremant = "";
        $foreman_name = "";
        $superintendent_name = "";

        $data = $request->validate([
            'signature' => 'required',
            'type' => 'required',
            'nombre' => 'nullable',
        ]);

        if ($data['type'] == "empleoye") {
            $foreman_name = ($data['nombre']) ? $data['nombre'] : $ticket->foreman_name;
            $image_path = public_path() . "/signatures/empleoye/$ticket->firma_foreman";
            if (File::exists($image_path) && $ticket->firma_foreman) {
                File::delete($image_path);
            }
            $name_img_foremant = "signature-foreman-" . time() . ".jpg";
            $path = public_path() . "/signatures/empleoye/$name_img_foremant";
            Image::make(file_get_contents($request->signature))->save($path);
        } else {
            $name_img_foremant = ($ticket->firma_foreman) ? $ticket->firma_foreman : null;
        }
        if ($data['type'] == "client") {
            $superintendent_name = ($data['nombre']) ? $data['nombre'] : $ticket->superintendent_name;
            $image_path = public_path() . "/signatures/client/$ticket->firma_cliente";
            if (File::exists($image_path) && $ticket->firma_cliente) {
                File::delete($image_path);
            }
            $name_img_client = "signature-client-" . time() . ".jpg";
            $path = public_path() . "/signatures/client/$name_img_client";
            Image::make(file_get_contents($request->signature))->save($path);
        } else {
            $name_img_client = ($ticket->firma_cliente) ? $ticket->firma_cliente : null;
        }

        $ticket->update([
            'firma_cliente' => $name_img_client,
            'firma_foreman' => $name_img_foremant,
            'foreman_name' => $foreman_name,
            'superintendent_name' => $superintendent_name,
        ]);

        if ($ticket) {
            return response()->json([
                'success' => 'The ticket has been updated',
            ]);
        }
        return response()->json([
            'error' => 'The ticket has been updated',
        ]);
    }

    public function upload_image($id, $type, Request $request, $nombre_camp = null)
    {
        $campo = ($nombre_camp) ? $nombre_camp : 'images';

        $preview = $config = $errors = [];
        $this->validate($request, [
            $campo => 'required',

        ]);
        if ($request->hasFile($campo)) {
            $allowedfileExtension = ['jpg', 'png', 'jpeg'];
            $files = $request->file($campo);
            foreach ($files as $file) {
                $filename = $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension();
                $fileSize = $file->getSize();
                $check = in_array($extension, $allowedfileExtension);

                if ($check) {
                    $name_img = "$type-image-$id-" . uniqid() . time() . "." . $extension;
                    $path = public_path() . '/uploads/' . $name_img;
                    if ($fileSize > 1500000) {
                        $actual_image = Image::make(file_get_contents($file))->setFileInfoFromPath($file);
                        $height = $actual_image->height() / 4;
                        $width = $actual_image->width() / 4;
                        $actual_image->resize($width, $height)->orientate()->save($path);
                        $fileSize = $actual_image->filesize();
                    } else {
                        Image::make(file_get_contents($file))->setFileInfoFromPath($file)->orientate()->save($path);
                    }
                    $insert = DB::table('ticket_imagen')->insertGetId([
                        'imagen' => $name_img,
                        'tipo' => $type,
                        'ticket_id' => $id,
                        'caption' => $filename,
                        'size' => $fileSize,
                    ]);
                    if ($insert) {
                        $newFileUrl = url('/') . '/uploads/' . $name_img;
                        $preview[] = $newFileUrl;
                        $config[] = [
                            'key' => $insert,
                            'caption' => $filename,
                            'size' => $fileSize,
                            'downloadUrl' => $newFileUrl, // the url to download the file
                            'url' => url("delete_image/$id/$type/ticket"), // server api to delete the file based on key
                        ];
                    } else {
                        $errors[] = $fileName;
                    }
                } else {
                    $errors[] = $filename;
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

    public function delete_image($id, $type, Request $request)
    {
        $query = DB::table('ticket_imagen')
            ->where('t_imagen_id', $request->key)
            ->where('tipo', $type)
            ->where('ticket_id', $id);
        $imagen = $query->first();
        if ($imagen) {
            $path = public_path() . '/uploads/' . $imagen->imagen;
            if (File::exists($path) && $imagen->imagen) {
                File::delete($path);
            }
            $query->delete();
            return response()->json([
                'success' => 'Successfully removed the image',
            ]);
        }
        return response()->json([
            'error' => 'Error, the image could not be deleted',
        ]);
    }

    public function get_images($id, $type)
    {
        $images = DB::table('ticket_imagen')
            ->where('ticket_id', $id)
            ->where('tipo', $type)
            ->get();
        $list = [];
        if ($images) {
            foreach ($images as $val) {
                $newFileUrl = url('/') . '/uploads/' . $val->imagen;
                $list['initialPreview'][] = $newFileUrl;
                $list['initialPreviewConfig'][] = [
                    'caption' => $val->caption,
                    'size' => $val->size,
                    'downloadUrl' => $newFileUrl,
                    'url' => url("delete_image/$id/$type/ticket"),
                    'key' => $val->t_imagen_id,
                ];
            }
        }
        return response()->json($list);
    }

    public function sendmailticket(Request $request, $id)
    {
        $rules = array(
            'to' => 'required|emails',
            'cc' => 'nullable|emails',
            'title_m' => 'required',
            'body_m' => 'required',
        );

        $messages = [
            'to.required' => 'The "TO" field is required',
            'cc.required' => 'The "CC" field is required',
        ];

        $error = Validator::make($request->all(), $rules, $messages);

        if ($error->fails()) {
            return response()->json(['errors' => $error->errors()->all()]);
        }
        $pdf = $this->pdf($id, true);

        $data = [];

        $to = explode(', ', $request->to);
        $cc = explode(', ', $request->cc);

        $ticket = Ticket::select(
            'ticket.*',
            'proyectos.*',
            'empresas.Nombre as empresa',
            DB::raw("CONVERT(ticket.num,char) as num"),
        )->where('ticket_id', $id)
            ->join('proyectos', 'proyectos.Pro_ID', 'ticket.proyecto_id')
            ->join('empresas', 'proyectos.Emp_ID', 'empresas.Emp_ID')
            ->first();
        if ($ticket->delete == 1) {
            $ticket->num = $ticket->num . "-void";
        } else {
            $ticket->num = $ticket->num;
        }
        $ticket->subempresa = substr(str_replace(' ', ' ', strtoupper($ticket->Nombre)), 0, 15);
        if ($request->to || $request->cc) {
            Mail::send([], $data, function ($message) use ($data, $pdf, $id, $request, $to, $cc, $ticket) {
                if ($request->to) {
                    $message->to($to);
                }
                if ($request->cc) {
                    $message->cc($cc);
                }
                $message->subject($request->title_m);
                $message->attachData($pdf->output(), "$ticket->subempresa-TICKET#" . $ticket->num . ".pdf", [
                    'mime' => 'application/pdf',
                ]);
                $message->setBody($request->body_m);
            });
            // check for failures
            if (Mail::failures()) {
                return response()->json(['errors' => ['An error occurred while sending the email, please try again']]);
            }
            // otherwise everything is okay ...
            $this->añadir_cantidad_uso($ticket->ticket_id, 'email');
            return response()->json([
                'success' => 'Success in sending the mail',
            ]);
        }
        return response()->json(['errors' => ['Error sending mail']]);
    }
    public function get_mails(Request $request)
    {
        $query = Personal::select('email')
            ->where('email', '!=', null)
            ->where('email', 'like', "%" . $request->get('query') . "%")
            ->distinct('email')
            ->get();
        $data = array();
        foreach ($query as $val) {
            $array = explode(',', $val->email);
            foreach ($array as $var) {
                $data[] = $var;
            }
        }
        return $data;
    }

    public function sendMultipleMailTicket(Request $request)
    {
        $rules = array(
            'to' => 'required|emails',
            'cc' => 'nullable|emails',
            'body_m' => 'required',
            'title_m' => 'required',
        );

        $messages = [
            'to.required' => 'The "TO" field is required',
            'cc.required' => 'The "CC" field is required',
        ];

        $error = Validator::make($request->all(), $rules, $messages);

        if ($error->fails()) {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        $data = [];

        $to = explode(', ', $request->to);
        $cc = explode(', ', $request->cc);

        if ($request->to || $request->cc) {
            Mail::send([], $data, function ($message) use ($data, $request, $to, $cc) {
                if ($request->to) {
                    $message->to($to);
                }
                if ($request->cc) {
                    $message->cc($cc);
                }
                $message->subject($request->title_m);
                foreach ($request->tickets as $ticket_id) {
                    $pdf = $this->pdf($ticket_id, true);
                    $ticket = Ticket::select(
                        'ticket.*',
                        'proyectos.*',
                        'empresas.Nombre as empresa',
                        DB::raw("CONVERT(ticket.num,char) as num"),
                    )->where('ticket_id', $ticket_id)
                        ->join('proyectos', 'proyectos.Pro_ID', 'ticket.proyecto_id')
                        ->join('empresas', 'proyectos.Emp_ID', 'empresas.Emp_ID')
                        ->first();
                    if ($ticket->delete == 1) {
                        $ticket->num = $ticket->num . "-void";
                    } else {
                        $ticket->num = $ticket->num;
                    }
                    $this->añadir_cantidad_uso($ticket_id, 'email');
                    $ticket->subempresa = substr(str_replace(' ', ' ', strtoupper($ticket->Nombre)), 0, 15) . "-TICKET#" . $ticket->num;
                    $message->attachData($pdf->output(), "$ticket->subempresa.pdf", [
                        'mime' => 'application/pdf',
                    ]);
                }
                $message->setBody($request->body_m);
            });

            // check for failures
            if (Mail::failures()) {
                return response()->json(['errors' => ['An error occurred while sending the email, please try again']]);
            }

            // otherwise everything is okay ...
            return response()->json([
                'success' => 'Success in sending the mail',
            ]);
        }
        return response()->json(['errors' => ['Error sending mail']]);
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $data = Ticket::findOrFail($id);
        $data->update(['delete' => 1]);

        return response()->json(['success' => 'Ticket deleted successfully ']);
    }
    private function añadir_cantidad_uso($ticket_id, $tipo)
    {
        $verificar = DB::table('ticket')
            ->where('ticket.ticket_id', $ticket_id)
            ->first();

        switch ($tipo) {
            case 'email':
                $increment = DB::table('ticket')
                    ->where('ticket.ticket_id', $ticket_id)
                    ->update([
                        'email_send' => ($verificar->email_send) + 1,
                    ]);
                break;
            case 'descarga':
                $increment = DB::table('ticket')
                    ->where('ticket.ticket_id', $ticket_id)
                    ->update([
                        'Descarga' => ($verificar->Descarga) + 1,
                    ]);
                break;

            default:
                # code...
                break;
        }
    }
    private function get_cantidad_uso($ticket_id, $tipo)
    {
        $descarga = DB::table('ticket')
            ->where('ticket.ticket_id', $ticket_id)
            ->first();
        return $render_descarga = "
            <a href='" . route('pdf.ticket', ['id' => $ticket_id]) . "' class='load_descargar'>
                <div class='icon-badge-container mr-1' >
                    <i class='fas fa-file-download ms-text-success ' title='Download'></i>
                    <div class='icon-badge'>$descarga->Descarga</div>
                </div>
            </a>";

    }
    private function get_cantidad_uso_email($ticket, $tipo)
    {
        $email = DB::table('ticket')
            ->where('ticket.ticket_id', $ticket->ticket_id)
            ->first();
        //if ($email->email_send > 0) {
        return $render_descarga = "
        <a href='#'>
            <div class='icon-badge-container mr-1 cursor-pointer send-mail' data-num='$ticket->subempresa' data-id='$ticket->ticket_id' data-project='$ticket->proyecto_id' data-nombre='$ticket->Nombre' title='Send Mail'>
                <i class='fas fa-envelope ms-text-secondary cursor-pointer send-mail' data-num='$ticket->subempresa' data-id='$ticket->ticket_id' data-project='$ticket->proyecto_id' data-nombre='$ticket->Nombre' title='Send Mail'></i>
                <div class='icon-badge'>$ticket->email_send</div>
            </div>
        </a>";

    }
    /*report ticket */
    private function reconstruir_consulta(Request $request)
    {
        $tickets_id = Ticket::select(
            'ticket.*'
        )
        //filtrando si hay proyectos
            ->when(!empty(request()->proyecto), function ($q) {
                return $q->where('proyectos.Nombre', 'like', '%' . request()->proyecto . '%');
            })
        //filtrando por descripcion
            ->when(!empty(request()->descripcion), function ($q) {
                return $q->where('ticket.descripcion', 'like', '%' . request()->descripcion . '%');
            })
        //filtrando por fecha
            ->when(!empty(request()->from_date), function ($q) {
                $from = date('Y-m-d', strtotime(request()->from_date));
                $to = date('Y-m-d', strtotime(request()->to_date));
                return $q->whereBetween('ticket.fecha_ticket', [$from, $to]);
            })
        //filtro de descargas de archivos
            ->when(!is_null($request->query('uso_descarga')), function ($q) use ($request) {
                return $q->where('ticket.Descarga', '=', $request->uso_descarga);
            })
            ->where('ticket.estado', 'creado')
            ->join('proyectos', 'ticket.proyecto_id', 'proyectos.Pro_ID')
            ->join('personal', 'personal.Empleado_ID', 'ticket.empleado_id')
            ->orderBy('ticket.fecha_ticket', 'ASC')
            ->pluck('ticket.ticket_id');

        $proyectos = Ticket::select(
            'ticket.*',
            'proyectos.Nombre as nombre_empresa',
            'empresas.*'
        )
            ->join('proyectos', 'proyectos.Pro_ID', 'ticket.proyecto_id')
            ->join('empresas', 'empresas.Emp_ID', 'proyectos.Emp_ID')
            ->whereIn('ticket.ticket_id', $tickets_id)
            ->groupBy('ticket.proyecto_id')
            ->get();

        foreach ($proyectos as $key => $proyecto) {
            $total_proyecto_numero = 0;
            $total_proyecto_reg_hrs = 0;
            $total_proyecto_premium_hrs = 0;
            $total_proyecto_out_hours_hrs = 0;
            $total_proyecto_prepaid_hrs = 0;

            $tickets = Ticket::select(
                'ticket.*',
                'proyectos.Nombre as nombre_empresa',
                'personal.Nick_Name',
                DB::raw("DATE_FORMAT(ticket.fecha_ticket , '%a %m/%d/%Y' ) as fecha_ticket"),
            )
                ->join('proyectos', 'proyectos.Pro_ID', 'ticket.proyecto_id')
                ->join('personal', 'personal.Empleado_ID', 'ticket.empleado_id')
                ->whereIn('ticket.ticket_id', $tickets_id)
                ->where('ticket.proyecto_id', $proyecto->proyecto_id)
                ->orderBy('ticket.fecha_ticket', 'ASC')
                ->get();

            foreach ($tickets as $i => $ticket) {
                $trabajadores = DB::table('ticket_trabajadores')
                    ->where('ticket_trabajadores.ticket_id', $ticket->ticket_id)
                    ->get();
                /*contar horas */
                $total_reg_hrs = 0;
                $total_premium_hrs = 0;
                $total_out_hours_hrs = 0;
                $total_prepaid_hrs = 0;
                foreach ($trabajadores as $j => $trabajador) {
                    $total_reg_hrs += $trabajador->reg_hours * $trabajador->n_worker;
                    $total_premium_hrs += $trabajador->premium_hours * $trabajador->n_worker;
                    $total_out_hours_hrs += $trabajador->out_hours * $trabajador->n_worker;
                    $total_prepaid_hrs += $trabajador->prepaid_hours * $trabajador->n_worker;
                }
                $ticket->total_numero = ($i + 1);
                $ticket->total_reg_hrs = $total_reg_hrs;
                $ticket->total_premium_hrs = $total_premium_hrs;
                $ticket->total_out_hours_hrs = $total_out_hours_hrs;
                $ticket->total_prepaid_hrs = $total_prepaid_hrs;

                $proyecto->total_proyecto_numero = $ticket->total_numero;
                $proyecto->total_proyecto_reg_hrs += $ticket->total_reg_hrs;
                $proyecto->total_proyecto_premium_hrs += $ticket->total_premium_hrs;
                $proyecto->total_proyecto_out_hours_hrs += $ticket->total_out_hours_hrs;
                $proyecto->total_proyecto_prepaid_hrs += $ticket->total_prepaid_hrs;
            }
            $proyecto->tickets = $tickets;
        }
        return $proyectos;
    }
    public function report_ticket(Request $request)
    {
        /* controller */
        $proyectos = $this->reconstruir_consulta($request);
        //dd($proyectos);
        $fecha_inicio = date('m/d/Y', strtotime(request()->from_date));
        $fecha_fin = date('m/d/Y', strtotime(request()->to_date));
        $pdf = PDF::loadView('panel.ticket.report.report', compact('proyectos', 'fecha_inicio', 'fecha_fin'))->setPaper('a4', 'landscape')->setWarnings(false);
        return $pdf->stream("Summary Ticket " . $fecha_inicio . " - " . $fecha_fin . ".pdf");
    }
    public function report_excel(Request $request)
    {
        $proyectos = $this->reconstruir_consulta($request);
        $resultado = [];
        //recostruir informacion
        foreach ($proyectos as $key => $proyecto) {
            foreach ($proyecto->tickets as $key => $ticket) {
                //dd($ticket);
                $data = new stdClass();
                $data->total_numero = $ticket->total_numero;
                $data->nombre_empresa = $proyecto->nombre_empresa;
                $data->Nombre = $proyecto->Nombre;
                /*tickets*/
                $data->total_numero = $ticket->total_numero;
                $data->fecha_ticket = $ticket->fecha_ticket;
                $data->num = $ticket->num;
                $data->descripcion = $ticket->descripcion;
                $data->horario = $ticket->horario;
                $data->Nick_Name = $ticket->Nick_Name;
                $data->total_reg_hrs = $ticket->total_reg_hrs;
                $data->total_premium_hrs = $ticket->total_premium_hrs;
                $data->total_out_hours_hrs = $ticket->total_out_hours_hrs;
                $data->total_prepaid_hrs = $ticket->total_prepaid_hrs;
                $resultado[] = $data;
            }

            $data = new stdClass();
            $data->total_proyecto_numero = $proyecto->total_proyecto_numero;
            $data->nombre_empresa = '';
            $data->Nombre = '';
            /*tickets*/
            $data->fecha_ticket = '';
            $data->num = '';
            $data->descripcion = '';
            $data->horario = '';
            $data->Nick_Name = '';
            $data->total_proyecto_reg_hrs = $proyecto->total_proyecto_reg_hrs;
            $data->total_proyecto_premium_hrs = $proyecto->total_proyecto_premium_hrs;
            $data->total_proyecto_out_hours_hrs = $proyecto->total_proyecto_out_hours_hrs;
            $data->total_proyecto_prepaid_hrs = $proyecto->total_proyecto_prepaid_hrs;
            $resultado[] = $data;

            $data = new stdClass();
            $data->total_proyecto_numero = '';
            $data->nombre_empresa = '';
            $data->Nombre = '';
            /*tickets*/
            $data->fecha_ticket = '';
            $data->num = '';
            $data->descripcion = '';
            $data->horario = '';
            $data->Nick_Name = '';
            $data->total_proyecto_reg_hrs = '';
            $data->total_proyecto_premium_hrs = '';
            $data->total_proyecto_out_hours_hrs = '';
            $data->total_proyecto_prepaid_hrs = '';
            $resultado[] = $data;
        }
        $fecha_inicio = date('m/d/Y', strtotime(request()->from_date));
        $fecha_fin = date('m/d/Y', strtotime(request()->to_date));
        return $this->excel->download(new resume_Ticket($resultado, $fecha_inicio, $fecha_fin), "Summary Tickes " . date('m-d-Y') . ".xlsx");
    }
}
