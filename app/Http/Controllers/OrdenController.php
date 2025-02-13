<?php

namespace App\Http\Controllers;

use App\ContactoProyecto;
use App\Material;
use App\Orden;
use App\Personal;
use DataTables;
use DB;
use File;
use Illuminate\Http\Request;
use Image;
use Mail;
use PDF;
use Validator;

class OrdenController extends Controller
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
    public function index(Request $request)
    {
        if (request()->ajax()) {
            $data = Orden::select(
                'orden.*',
                'personal.Usuario as username',
                'proyectos.Nombre as proyecto',
                'proyectos.Codigo',
                DB::raw("CONCAT(COALESCE(empresas.Codigo,''),' - ',COALESCE(sub_empleoye.Nombre,''), ' ',  COALESCE(sub_empleoye.Apellido_Paterno,''), ' ',  COALESCE(sub_empleoye.Apellido_Materno)) as empresa"),
            )
                ->when(!auth()->user()->verificarRol([1, 3]), function ($query) {
                    return $query->where('empleado_id', auth()->user()->Empleado_ID);
                })
                ->when(auth()->user()->verificarRol([3]), function ($query) {
                    return $query->where('orden.sub_empleoye_id', auth()->user()->Empleado_ID);
                })
            //filtros fecha
                ->when(!empty(request()->from_date), function ($q) {
                    $from = date('Y-m-d', strtotime(request()->from_date));
                    $to = date('Y-m-d', strtotime(request()->to_date));
                    return $q->whereBetween('orden.date_order', [$from, $to]);
                })
            //filtros proyecto
                ->when(!empty(request()->proyecto), function ($q) {
                    return $q->where('proyectos.Nombre', 'like', '%' . request()->proyecto . '%');
                })

                ->where('delete', 0)
                ->where('orden.estado', 'creado')
                ->join('empresas', 'empresas.Emp_ID', 'orden.sub_contractor')
                ->join('proyectos', 'proyectos.Pro_ID', 'orden.proyecto_id')
                ->join('personal', 'personal.Empleado_ID', 'orden.created_by')
                ->join('personal as sub_empleoye', 'sub_empleoye.Empleado_ID', 'orden.sub_empleoye_id')
                ->get();

            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('firma_installer', function ($data) {
                    $html = ($data->firma_installer) ? '<span class="badge badge-success">Yes</span>' : '<span class="badge badge-danger">No</span>';
                    return $html;
                })
                ->addColumn('firma_foreman', function ($data) {
                    $html = ($data->firma_foreman) ? '<span class="badge badge-success">Yes</span>' : '<span class="badge badge-danger">No</span>';
                    return $html;
                })
                ->addColumn('acciones', function ($data) {
                    /* validar uso */
                    $uso_descarga = $this->get_cantidad_uso($data->id, 'descarga');
                    $uso_email = $this->get_cantidad_uso_email($data, 'email');
                    /* validar uso */
                    $button = "
                        <div class='icon-badge-group m-0'>
                        <a class='show_orden_wc' href='" . route('show.orden') . "' data-id='$data->id' ><i class='fas fa-eye ms-text-primary' title='View PDF'></i></a>
                        <a href='" . route('edit.orden', ['id' => $data->id]) . "'><i class='fas fa-pencil-alt ms-text-warning' title='Edit orden'></i></a>
                        <i class='far fa-trash-alt ms-text-danger delete cursor-pointer' data-id='$data->id' title='Delete'></i>
                            $uso_descarga
                            $uso_email
                        </div>
                            ";

                    return $button;
                })
                ->addColumn('inicio', function ($data) {
                    $button = "<i class='fas fa-images text-info upload_image cursor-pointer' data-image='inicio' data-id='$data->id'></i>";
                    return $button;
                })
                ->addColumn('final', function ($data) {
                    $button = "<i class='fas fa-images text-info upload_image cursor-pointer' data-image='final' data-id='$data->id'></i>";
                    return $button;
                })
                ->editColumn('date_work', function ($data) {
                    return $data->date_work ? date('m-d-Y', strtotime($data->date_work)) : null;
                })
                ->addColumn('check_email', function ($data) {
                    $data->subempresa = $data->proyecto . "- ORDERWC#" . $data->num;
                    $button = "<input type='checkbox' style='transform: scale(1.5);' value='$data->id' data-num='$data->subempresa' data-proyecto='$data->proyecto_id'>";

                    return $button;
                })
                ->rawColumns(['acciones', 'firma_installer', 'firma_foreman', 'inicio', 'final', 'date_work', 'check_email'])
                ->make(true);
        }
        return view('panel.orden.list');
    }
    private function get_cantidad_uso($orden_id, $tipo)
    {
        $descarga = DB::table('orden')
            ->where('orden.id', $orden_id)
            ->first();

        return $render_descarga = "
            <a href='" . route('pdf.orden', ['id' => $descarga->id]) . "' class='load_descargar'>
                <div class='icon-badge-container mr-1' >
                    <i class='fas fa-file-download ms-text-success ' title='Download'></i>
                    <div class='icon-badge'>$descarga->descargas</div>
                </div>
            </a>";

    }
    private function get_cantidad_uso_email($orden, $tipo)
    {
        $email = DB::table('orden')
            ->where('orden.id', $orden->id)
            ->first();
        return $render_descarga = "
                <a href='#'>
                    <div class='icon-badge-container mr-1 cursor-pointer send-mail' data-id='$orden->id' data-proyecto='$orden->id' data-num='$orden->num' data-nombre='$orden->nombre_proyecto' data-project='$orden->proyecto_id' title='Send Mail'>
                    <i class='fas fa-envelope ms-text-secondary cursor-pointer send-mail' data-num='$orden->num' data-id='$orden->id' data-num='$orden->num' data-project='$orden->proyecto_id'  data-nombre='$orden->nombre_proyecto'  title='Send Mail'></i>
                        <div class='icon-badge'>$email->email_send</div>
                    </div>
                </a>";
    }
    /*
     *calcular el usos
     */
    private function añadir_cantidad_uso($orden_id, $tipo)
    {
        $verificar = DB::table('orden')
            ->where('orden.id', $orden_id)
            ->first();

        switch ($tipo) {
            case 'email':
                $increment = DB::table('orden')
                    ->where('orden.id', $orden_id)
                    ->update([
                        'email_send' => ($verificar->email_send) + 1,
                    ]);
                break;
            case 'descarga':
                $increment = DB::table('orden')
                    ->where('orden.id', $orden_id)
                    ->update([
                        'descargas' => ($verificar->descargas) + 1,
                    ]);
                break;

            default:
                # code...
                break;
        }
    }
    public function get_config_mail($id)
    {
        $orden = DB::table('orden')->join('proyectos', 'proyectos.Pro_ID', 'orden.proyecto_id')->where('proyecto_id', $id)->first();
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
            ->where('tipo_contacto.nombre', 'orden')
            ->join('tipo_contacto', 'tipo_contacto.id_tipo_contacto', 'contacto_proyecto.tipo_contacto')
            ->join('personal', 'personal.Empleado_ID', 'contacto_proyecto.Empleado_ID')
            ->get();
        $config->title_ticket_email = $orden->Nombre . " - ORDER-WC#" . $orden->num;

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
    public function create()
    {
        $orden = Orden::select('id')
            ->where('created_by', auth()->user()->Empleado_ID)
            ->where('estado', 'pendiente')
            ->pluck('id')->first();
        if (!$orden) {
            $orden = Orden::insertGetId([
                'created_by' => auth()->user()->Empleado_ID,
                'estado' => 'pendiente',
            ]);
        }
        $proyectos = DB::table('proyectos')->get();
        $n_orden = Orden::where('estado', 'creado')->count() + 1;

        return view('panel.orden.new', compact('orden', 'proyectos', 'n_orden'));
    }
    public function get_materiales(Request $request)
    {
        if (!isset($request->searchTerm)) {
            $materiales = Material::select('materiales.*')
                ->distinct('materiales.Mat_ID')
                ->get();
        } else {
            $materiales = Material::select('materiales.*')
                ->where('Denominacion', 'like', '%' . $request->searchTerm . '%')
                ->distinct('materiales.Mat_ID')
                ->get();
        }
        $data = [];
        foreach ($materiales as $row) {
            $data[] = array(
                "id" => $row->Mat_ID,
                "text" => $row->Denominacion,
                "Unidad_Medida" => $row->Unidad_Medida,

            );
        }
        return response()->json($data);
    }

    public function get_proyects(Request $request)
    {
        if (!isset($request->searchTerm)) {
            $proyectos = DB::table('proyectos')
                ->select('proyectos.*', 'empresas.Codigo as empresa', 'empresas.Emp_ID')
                ->join('empresas', 'proyectos.Emp_ID', 'empresas.Emp_ID')
                ->distinct('proyectos.Pro_ID')
                ->orderBy('proyectos.Estatus_ID', 'ASC')
                ->orderBy('proyectos.Nombre', 'ASC')
                ->get();
        } else {
            $proyectos = DB::table('proyectos')
                ->select('proyectos.*', 'empresas.Codigo as empresa', 'empresas.Emp_ID')
                ->where('proyectos.Nombre', 'like', '%' . $request->searchTerm . '%')
                ->join('empresas', 'proyectos.Emp_ID', 'empresas.Emp_ID')
                ->orderBy('proyectos.Estatus_ID', 'ASC')
                ->orderBy('proyectos.Nombre', 'ASC')
                ->distinct('proyectos.Pro_ID')
                ->get();
        }
        //dd($proyectos);
        $data = [];
        foreach ($proyectos as $row) {
            $data[] = array(
                "id" => $row->Pro_ID,
                "text" => $row->Nombre,
                "emp" => $row->empresa,
                "emp_id" => $row->Emp_ID,
            );
        }
        return response()->json($data);
    }
    public function get_empresas(Request $request)
    {
        if (!isset($request->searchTerm)) {
            $empresas = DB::table('empresas')
                ->get();
        } else {
            $empresas = DB::table('empresas')
                ->where('Nombre', 'like', '%' . $request->searchTerm . '%')
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

    public function get_empleoyes(Request $request, $id)
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

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = array(
            'proyect' => 'required',
            'job_name' => 'required',
            'sub_contractor' => 'required',
            'sub_empleoye_id' => 'required',
            'date_order' => 'nullable|date_format:m/d/Y',
            'date_work' => 'nullable|date_format:m/d/Y',
            'fecha_firm_installer' => 'nullable|date_format:m/d/Y',
            'fecha_firm_foreman' => 'nullable|date_format:m/d/Y',
            'input_signature_insta' => 'nullable',
            'input_signature_fore' => 'nullable',
            'material_id' => 'nullable|array',
            'q_ordered' => 'nullable|array',
            'q_job_site' => 'nullable|array',
            'q_installed' => 'nullable|array',
            'd_installed' => 'nullable|array',
            'q_remaining_wc' => 'nullable|array',
            'remaining_wc_stored' => 'nullable|array',
            'images_inicio' => 'nullable',
            'images_inicio.*' => 'mimes:jpg,jpeg,png',
            'images_final' => 'nullable',
            'images_final.*' => 'mimes:jpg,jpeg,png',
        );

        $error = Validator::make($request->all(), $rules);

        if (request()->ajax() === true) {
            return response()->json(['errors' => $error->errors()->all()]);
        }
        $name_img_insta = "";
        $name_img_foreman = "";
        $n_orden = Orden::where('estado', 'creado')->count() + 1;

        if (!empty($request->input_signature_fore)) {
            $name_img_foreman = "signature-foreman-" . time() . ".jpg";
            $path = public_path() . "/signatures/empleoye/$name_img_foreman";
            Image::make(file_get_contents($request->input_signature_fore))->save($path);
        }
        if (!empty($request->input_signature_insta)) {
            $name_img_insta = "signature-client-" . time() . ".jpg";
            $path = public_path() . "/signatures/install/$name_img_insta";
            Image::make(file_get_contents($request->input_signature_insta))->save($path);
        }
        /*data de empresa */
        $empresa = DB::table('proyectos')->where('proyectos.Pro_ID', $request->proyect)->first();

        $orden = Orden::find($request->orden_id);
        $orden->update([
            'num' => $n_orden,
            'job_name' => $request->job_name,
            'sub_contractor' => $empresa->Emp_ID,
            'proyecto_id' => $request->proyect,
            'sub_empleoye_id' => $request->sub_empleoye_id,
            'date_order' => ($request->date_order) ? date('Y-m-d', strtotime($request->date_order)) : null,
            'date_work' => ($request->date_work) ? date('Y-m-d', strtotime($request->date_work)) : null,
            'created_by' => auth()->user()->Empleado_ID,
            'fecha_firm_installer' => ($request->fecha_firm_installer) ? date('Y-m-d', strtotime($request->fecha_firm_installer)) : null,
            'fecha_firm_foreman' => ($request->fecha_firm_foreman) ? date('Y-m-d', strtotime($request->fecha_firm_foreman)) : null,
            'firma_installer' => $name_img_insta,
            'firma_foreman' => $name_img_foreman,
            'estado' => 'creado',
        ]);

        if ($orden->id) {
            if ($request->material_id) {
                foreach ($request->material_id as $key => $val) {
                    DB::table('orden_material')->insert([
                        'material_id' => $val,
                        'q_ordered' => $request->q_ordered[$key],
                        'q_job_site' => $request->q_job_site[$key],
                        'q_installed' => $request->q_installed[$key],
                        'd_installed' => ($request->d_installed[$key]) ? date('Y-m-d', strtotime($request->d_installed[$key])) : null,
                        'q_remaining_wc' => $request->q_remaining_wc[$key],
                        'remaining_wc_stored' => $request->remaining_wc_stored[$key],
                        'orden_id' => $orden->id,
                    ]);
                }
            }
            if ($request->is_mail == true) {
                $this->sendmailorden($request, $orden->id);
            }
            return redirect(route('listar.ordenes'))->with('success', 'New order has been created');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $orders = explode(', ', $request->query('orders'));
        $id = $request->query('view');
        $orden = Orden::selectRaw("orden.*,
        proyectos.*,
        empresas.Codigo as empresa,
        CONCAT(COALESCE(personal.Nombre,''), ' ',  COALESCE(personal.Apellido_Paterno,''), ' ',  COALESCE(personal.Apellido_Materno,'')) as sub_employe,
        CONCAT(COALESCE(cre.Nombre,''), ' ',  COALESCE(cre.Apellido_Paterno,''), ' ',  COALESCE(cre.Apellido_Materno,'')) as creator")
            ->join('empresas', 'empresas.Emp_ID', 'orden.sub_contractor')
            ->join('personal', 'personal.Empleado_ID', 'orden.sub_empleoye_id')
            ->join('personal as cre', 'orden.created_by', 'cre.Empleado_ID')
            ->join('proyectos', 'proyectos.Pro_ID', 'orden.proyecto_id')
            ->where('id', $id)
            ->first();

        $materiales = DB::table('orden_material')
            ->where('orden_id', $id)
            ->join('materiales', 'materiales.Mat_ID', 'orden_material.material_id')
            ->get();
        $img_start = DB::table('orden_imagen')->where('orden_id', $id)->where('tipo', 'inicio')->get()->toArray();
        $img_final = DB::table('orden_imagen')->where('orden_id', $id)->where('tipo', 'final')->get()->toArray();

        return view('panel.orden.view', compact('orden', 'materiales', 'id', 'img_start', 'img_final'));
    }

    public function show_modal($id)
    {
        $orden = Orden::selectRaw("orden.*,
        proyectos.*,
        DATE_FORMAT(orden.date_order, '%m/%d/%Y') as date_order,
        DATE_FORMAT(orden.date_work, '%m/%d/%Y') as date_work,
        empresas.Codigo as empresa,
        CONCAT(COALESCE(personal.Nombre,''), ' ',  COALESCE(personal.Apellido_Paterno,''), ' ',  COALESCE(personal.Apellido_Materno,'')) as sub_employe,
        CONCAT(COALESCE(cre.Nombre,''), ' ',  COALESCE(cre.Apellido_Paterno,''), ' ',  COALESCE(cre.Apellido_Materno,'')) as creator")
            ->join('empresas', 'empresas.Emp_ID', 'orden.sub_contractor')
            ->join('personal', 'personal.Empleado_ID', 'orden.sub_empleoye_id')
            ->join('personal as cre', 'orden.created_by', 'cre.Empleado_ID')
            ->join('proyectos', 'proyectos.Pro_ID', 'orden.proyecto_id')
            ->where('id', $id)
            ->first();

        $materiales = DB::table('orden_material')
            ->where('orden_id', $id)
            ->join('materiales', 'materiales.Mat_ID', 'orden_material.material_id')
            ->get();
        $orden->materiales = $materiales;
        $img_start = DB::table('orden_imagen')->where('orden_id', $id)->where('tipo', 'inicio')->get();
        $img_final = DB::table('orden_imagen')->where('orden_id', $id)->where('tipo', 'final')->get();
        return response()->json($orden, 200);
    }

    public function pdf($id, $view = false)
    {
        $orden = Orden::selectRaw("orden.*,
        proyectos.*,
        empresas.Codigo as empresa,
        CONCAT(COALESCE(personal.Nombre,''), ' ',  COALESCE(personal.Apellido_Paterno,''), ' ',  COALESCE(personal.Apellido_Materno,'')) as sub_employe,
        CONCAT(COALESCE(cre.Nombre,''), ' ',  COALESCE(cre.Apellido_Paterno,''), ' ',  COALESCE(cre.Apellido_Materno,'')) as creator")
            ->join('empresas', 'empresas.Emp_ID', 'orden.sub_contractor')
            ->join('personal', 'personal.Empleado_ID', 'orden.sub_empleoye_id')
            ->join('personal as cre', 'orden.created_by', 'cre.Empleado_ID')
            ->join('proyectos', 'proyectos.Pro_ID', 'orden.proyecto_id')
            ->where('id', $id)
            ->first();

        $materiales = DB::table('orden_material')
            ->where('orden_id', $id)
            ->join('materiales', 'materiales.Mat_ID', 'orden_material.material_id')
            ->get();
        $img_start = DB::table('orden_imagen')->where('orden_id', $id)->where('tipo', 'inicio')->get()->toArray();
        $img_final = DB::table('orden_imagen')->where('orden_id', $id)->where('tipo', 'final')->get()->toArray();

        $pdf = PDF::loadView('panel.orden.pdf', compact('orden', 'materiales', 'id', 'img_start', 'img_final'))->setPaper('letter')->setWarnings(false);
        if ($view === true) {
            return $pdf;
        }
        $this->añadir_cantidad_uso($orden->id, 'descarga');
        $orden->subempresa = substr(str_replace(' ', ' ', strtoupper($orden->Nombre)), 0, 15) . " - ORDER-WC#" . $orden->num;
        return $pdf->download("$orden->subempresa.pdf");
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $orden = Orden::selectRaw("orden.*,
        proyectos.*,
        empresas.Codigo as empresa,
        CONCAT(COALESCE(personal.Nombre,''), ' ',  COALESCE(personal.Apellido_Paterno,''), ' ',  COALESCE(personal.Apellido_Materno,''), ' - ', COALESCE(ep.Nombre,'')) as sub_employe,
        CONCAT(COALESCE(cre.Nombre,''), ' ',  COALESCE(cre.Apellido_Paterno,''), ' ',  COALESCE(cre.Apellido_Materno,'')) as creator")
            ->join('empresas', 'empresas.Emp_ID', 'orden.sub_contractor')
            ->join('personal', 'personal.Empleado_ID', 'orden.sub_empleoye_id')
            ->join('personal as cre', 'orden.created_by', 'cre.Empleado_ID')
            ->join('empresas as ep', 'ep.Emp_ID', 'personal.Emp_ID')
            ->join('proyectos', 'proyectos.Pro_ID', 'orden.proyecto_id')
            ->where('id', $id)
            ->first();
        $materiales = DB::table('orden_material')
            ->where('orden_id', $id)
            ->join('materiales', 'materiales.Mat_ID', 'orden_material.material_id')
            ->get();
        $orden->date_order = ($orden->date_order) ? date("m/d/Y", strtotime($orden->date_order)) : null;
        $orden->date_work = ($orden->date_work) ? date("m/d/Y", strtotime($orden->date_work)) : null;
        $orden->fecha_firm_installer = ($orden->fecha_firm_installer) ? date("m/d/Y", strtotime($orden->fecha_firm_installer)) : null;
        $orden->fecha_firm_foreman = ($orden->fecha_firm_foreman) ? date("m/d/Y", strtotime($orden->fecha_firm_foreman)) : null;

        return view('panel.orden.edit', compact('orden', 'materiales', 'id'));
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
        //dd($request->all());
        $rules = array(
            'proyect' => 'nullable',
            'job_name' => 'required',
            'sub_contractor' => 'required',
            'sub_empleoye_id' => 'nullable',
            'date_order' => 'nullable|date_format:m/d/Y',
            'date_work' => 'nullable|date_format:m/d/Y',
            'fecha_firm_installer' => 'nullable|date_format:m/d/Y',
            'fecha_firm_foreman' => 'nullable|date_format:m/d/Y',
            'input_signature_insta' => 'nullable',
            'input_signature_fore' => 'nullable',
            'material_id' => 'nullable|array',
            'q_ordered' => 'nullable|array',
            'q_job_site' => 'nullable|array',
            'q_installed' => 'nullable|array',
            'd_installed' => 'nullable|array',
            'q_remaining_wc' => 'nullable|array',
            'remaining_wc_stored' => 'nullable|array',
        );

        $error = Validator::make($request->all(), $rules);

        if (request()->ajax() === true) {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        $orden = Orden::find($id);

        $name_img_insta = "";
        $name_img_foreman = "";

        if ($request->input_signature_fore) {
            $image_path = public_path() . "/signatures/empleoye/$orden->firma_foreman";
            if (File::exists($image_path) && $orden->firma_foreman) {
                File::delete($image_path);
            }
            $name_img_foreman = "signature-foreman-" . time() . ".jpg";
            $path = public_path() . "/signatures/empleoye/$name_img_foreman";
            Image::make(file_get_contents($request->input_signature_fore))->save($path);
        } else {
            $name_img_foreman = ($orden->firma_foreman) ? $orden->firma_foreman : null;
        }
        if ($request->input_signature_insta) {
            $image_path = public_path() . "/signatures/install/$orden->firma_installer";
            if (File::exists($image_path) && $orden->firma_installer) {
                File::delete($image_path);
            }
            $name_img_insta = "signature-install-" . time() . ".jpg";
            $path = public_path() . '/signatures/install/' . $name_img_insta;
            Image::make(file_get_contents($request->input_signature_insta))->save($path);
        } else {
            $name_img_insta = ($orden->firma_installer) ? $orden->firma_installer : null;
        }
        /*data de empresa */
        $empresa = DB::table('proyectos')->where('proyectos.Pro_ID', $request->proyect)->first();
        //update
        if (auth()->user()->verificarRol([1])) {
            $orden->update([
                'job_name' => $request->job_name,
                'sub_contractor' => $empresa->Emp_ID,
                'proyecto_id' => $request->proyect,
                'sub_empleoye_id' => $request->sub_empleoye_id,
                'date_order' => ($request->date_order) ? date('Y-m-d', strtotime($request->date_order)) : null,
                'date_work' => ($request->date_work) ? date('Y-m-d', strtotime($request->date_work)) : null,
                'fecha_firm_installer' => ($request->fecha_firm_installer) ? date('Y-m-d', strtotime($request->fecha_firm_installer)) : null,
                'fecha_firm_foreman' => ($request->fecha_firm_foreman) ? date('Y-m-d', strtotime($request->fecha_firm_foreman)) : null,
                'firma_installer' => $name_img_insta,
                'firma_foreman' => $name_img_foreman,
            ]);
        } else {
            $orden->update([
                'date_order' => ($request->date_order) ? date('Y-m-d', strtotime($request->date_order)) : null,
                'date_work' => ($request->date_work) ? date('Y-m-d', strtotime($request->date_work)) : null,
                'fecha_firm_installer' => ($request->fecha_firm_installer) ? date('Y-m-d', strtotime($request->fecha_firm_installer)) : null,
                'fecha_firm_foreman' => ($request->fecha_firm_foreman) ? date('Y-m-d', strtotime($request->fecha_firm_foreman)) : null,
                'firma_installer' => $name_img_insta,
                'firma_foreman' => $name_img_foreman,
            ]);
        }

        if ($orden) {
            if (auth()->user()->verificarRol([1])) {
                DB::table('orden_material')->where('orden_id', $id)->delete();
                foreach ($request->material_id as $key => $val) {
                    DB::table('orden_material')->insert([
                        'material_id' => $val,
                        'q_ordered' => $request->q_ordered[$key],
                        'q_job_site' => $request->q_job_site[$key],
                        'q_installed' => $request->q_installed[$key],
                        'd_installed' => ($request->d_installed[$key]) ? date('Y-m-d', strtotime($request->d_installed[$key])) : null,
                        'q_remaining_wc' => $request->q_remaining_wc[$key],
                        'remaining_wc_stored' => $request->remaining_wc_stored[$key],
                        'orden_id' => $id,
                    ]);
                }
            } else {
                DB::table('orden_material')->where('orden_id', $id)->delete();
                foreach ($request->material_id as $key => $val) {
                    DB::table('orden_material')->insert([
                        'material_id' => $val,
                        'q_ordered' => $request->q_ordered[$key],
                        'q_job_site' => $request->q_job_site[$key],
                        'q_installed' => $request->q_installed[$key],
                        'd_installed' => ($request->d_installed[$key]) ? date('Y-m-d', strtotime($request->d_installed[$key])) : null,
                        'q_remaining_wc' => $request->q_remaining_wc[$key],
                        'remaining_wc_stored' => $request->remaining_wc_stored[$key],
                        'orden_id' => $id,
                    ]);
                }
            }
            if ($request->is_mail == true) {
                $this->sendmailorden($request, $id);
            }
            return redirect(route('listar.ordenes'))->with('success', 'The order has been updated');
        }
    }

    public function update_signature(Request $request, $id)
    {
        $orden = Orden::find($id);
        $name_img_client = "";
        $name_img_foremant = "";

        $data = $request->validate([
            'signature' => 'required',
            'type' => 'required',
        ]);

        if ($data['type'] == "empleoye") {
            $image_path = public_path() . "/signatures/empleoye/$orden->firma_foreman";
            if (File::exists($image_path) && $orden->firma_foreman) {
                File::delete($image_path);
            }
            $name_img_foremant = "signature-foreman-" . time() . ".jpg";
            $path = public_path() . "/signatures/empleoye/$name_img_foremant";
            Image::make(file_get_contents($request->signature))->save($path);
        } else {
            $name_img_foremant = ($orden->firma_foreman) ? $orden->firma_foreman : null;
        }
        if ($data['type'] == "installer") {
            $image_path = public_path() . "/signatures/install/$orden->firma_installer";
            if (File::exists($image_path) && $orden->firma_installer) {
                File::delete($image_path);
            }
            $name_img_insta = "signature-install-" . time() . ".jpg";
            $path = public_path() . "/signatures/install/$name_img_insta";
            Image::make(file_get_contents($request->signature))->save($path);
        } else {
            $name_img_insta = ($orden->firma_installer) ? $orden->firma_installer : null;
        }

        $orden->update([
            'firma_installer' => $name_img_insta,
            'firma_foreman' => $name_img_foremant,
        ]);

        if ($orden) {
            return response()->json([
                'success' => 'The orden has been updated',
            ]);
        }
        return response()->json([
            'error' => 'The orden has been updated',
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
                    $insert = DB::table('orden_imagen')->insertGetId([
                        'imagen' => $name_img,
                        'tipo' => $type,
                        'orden_id' => $id,
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
        $query = DB::table('orden_imagen')
            ->where('o_imagen_id', $request->key)
            ->where('tipo', $type)
            ->where('orden_id', $id);
        $imagen = $query->first();
        if ($imagen) {
            $path = public_path() . '/uploads/' . $imagen->imagen;
            if (File::exists($path) && $imagen->imagen) {
                File::delete($path);
            }
            $query->delete();
            return response()->json([
                'success' => 'successfully removed the image',
            ]);
        }
        return response()->json([
            'error' => 'error, the image could not be deleted',
        ]);
    }

    public function get_images($id, $type)
    {
        $images = DB::table('orden_imagen')
            ->where('orden_id', $id)
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
                    'url' => url("delete_image/$id/$type/orden"),
                    'key' => $val->o_imagen_id,
                ];
            }
        }
        return response()->json($list);
    }

    public function sendmailorden(Request $request, $id)
    {
        $rules = array(
            'to.*' => 'required|email',
            'cc.*' => 'required|email',
            'title_m' => 'required',
            'body_m' => 'required',
        );

        $error = Validator::make($request->all(), $rules);

        if ($error->fails()) {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        $pdf = $this->pdf($id, true);

        $data = [];

        $to = explode(', ', $request->to);
        $cc = explode(', ', $request->cc);

        $orden = DB::table('orden')->select(
            'orden.*',
            'proyectos.*',
            'empresas.Nombre as empresa',
            DB::raw("CONVERT(orden.num,char) as num"),
        )->where('orden.id', $id)
            ->join('proyectos', 'proyectos.Pro_ID', 'orden.proyecto_id')
            ->join('empresas', 'proyectos.Emp_ID', 'empresas.Emp_ID')
            ->first();
        $orden->subempresa = substr(str_replace(' ', ' ', strtoupper($orden->Nombre)), 0, 15);
        if ($request->to || $request->cc) {
            Mail::send([], $data, function ($message) use ($data, $pdf, $id, $request, $to, $cc, $orden) {

                if ($request->to) {
                    $message->to($to);
                }
                if ($request->cc) {
                    $message->cc($cc);
                }
                $message->subject($request->title_m);
                $orden->subempresa = substr(str_replace(' ', ' ', strtoupper($orden->empresa)), 0, 15) . " - ORDER-WC#" . $orden->num;
                $message->attachData($pdf->output(), "$orden->subempresa.pdf", [
                    'mime' => 'application/pdf',
                ]);
                $message->setBody($request->body_m);
            });
            // check for failures
            if (Mail::failures()) {
                return response()->json(['errors' => ['An error occurred while sending the email, please try again']]);
            }
            $this->añadir_cantidad_uso($orden->id, 'email');
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
        $data = Orden::findOrFail($id);
        $data->update(['delete' => 1]);

        return response()->json(['success' => 'WC Installation command removed successfully']);
    }
    /*multiple email */
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
            ->where('tipo_contacto.nombre', 'orden')
            ->join('tipo_contacto', 'tipo_contacto.id_tipo_contacto', 'contacto_proyecto.tipo_contacto')
            ->join('personal', 'personal.Empleado_ID', 'contacto_proyecto.Empleado_ID')
            ->get();

        return response()->json([
            'config' => $config,
            'emails' => $emails,
            'email_contac' => $email_contac,
        ]);
    }
    public function sendMultipleMailOrder(Request $request)
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
                foreach ($request->orders as $order_id) {
                    $pdf = $this->pdf($order_id, true);
                    $orden = DB::table('orden')->select(
                        'orden.*',
                        'proyectos.*',
                        'empresas.Nombre as empresa',
                        DB::raw("CONVERT(orden.num,char) as num"),
                    )->where('orden.id', $order_id)
                        ->join('proyectos', 'proyectos.Pro_ID', 'orden.proyecto_id')
                        ->join('empresas', 'proyectos.Emp_ID', 'empresas.Emp_ID')
                        ->first();

                    $this->añadir_cantidad_uso($order_id, 'email');
                    $orden->subempresa = substr(str_replace(' ', ' ', strtoupper($orden->empresa)), 0, 15) . " - ORDER-WC#" . $orden->num;
                    $message->attachData($pdf->output(), "$orden->subempresa.pdf", [
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
}
