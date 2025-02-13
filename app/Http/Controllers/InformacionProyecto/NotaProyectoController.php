<?php

namespace App\Http\Controllers\InformacionProyecto;

use App\Http\Controllers\Controller;
use DataTables;
use DB;
use File;
use Illuminate\Http\Request;
use Image;
use PDF;
use Storage;
use Validator;
use \stdClass;

class NotaProyectoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function select_proyectos(Request $request)
    {
        $data = [];
        if (!isset($request->searchTerm)) {
            $proyectos = DB::table('proyectos')
                ->select(
                    'proyectos.*',
                    'empresas.Nombre as nombre_empresa',
                    'proyect_manager.Empleado_ID as proyecto_manager_id',
                    DB::raw("CONCAT(COALESCE(proyect_manager.Nombre,''),' ',COALESCE(proyect_manager.Apellido_Paterno,''),' ',COALESCE(proyect_manager.Apellido_Materno,'')) as proyecto_manager"),
                    'asistente_proyecto_manager.Empleado_ID as asistente_proyecto_manager_id',
                    DB::raw("CONCAT(COALESCE(asistente_proyecto_manager.Nombre,''),' ',COALESCE(asistente_proyecto_manager.Apellido_Paterno,''),' ',COALESCE(asistente_proyecto_manager.Apellido_Materno,'')) as asistente_proyecto_manager"),
                    'lead.Empleado_ID as lead_id',
                    DB::raw("CONCAT(COALESCE(lead.Nombre,''),' ',COALESCE(lead.Apellido_Paterno,''),' ',COALESCE(lead.Apellido_Materno,'')) as lead"),
                    'foreman.Empleado_ID as foreman_id',
                    DB::raw("CONCAT(COALESCE(foreman.Nombre,''),' ',COALESCE(foreman.Apellido_Paterno,''),' ',COALESCE(foreman.Apellido_Materno,'')) as foreman"),
                )
                ->join('empresas', 'empresas.Emp_ID', 'proyectos.Emp_ID')
                ->leftJoin('personal as proyect_manager', 'proyect_manager.Empleado_ID', 'proyectos.Manager_ID')
                ->leftJoin('personal as lead', 'lead.Empleado_ID', 'proyectos.Lead_ID')
                ->leftJoin('personal as foreman', 'foreman.Empleado_ID', 'proyectos.Foreman_ID')
                ->leftJoin('personal as asistente_proyecto_manager', 'asistente_proyecto_manager.Empleado_ID', 'proyectos.Asistant_Proyect_ID')
                ->get();
        } else {
            $proyectos = DB::table('proyectos')
                ->select(
                    'proyectos.*',
                    'empresas.Nombre as nombre_empresa',
                    'proyect_manager.Empleado_ID as proyecto_manager_id',
                    DB::raw("CONCAT(COALESCE(proyect_manager.Nombre,''),' ',COALESCE(proyect_manager.Apellido_Paterno,''),' ',COALESCE(proyect_manager.Apellido_Materno,'')) as proyecto_manager"),
                    'asistente_proyecto_manager.Empleado_ID as asistente_proyecto_manager_id',
                    DB::raw("CONCAT(COALESCE(asistente_proyecto_manager.Nombre,''),' ',COALESCE(asistente_proyecto_manager.Apellido_Paterno,''),' ',COALESCE(asistente_proyecto_manager.Apellido_Materno,'')) as asistente_proyecto_manager"),
                    'lead.Empleado_ID as lead_id',
                    DB::raw("CONCAT(COALESCE(lead.Nombre,''),' ',COALESCE(lead.Apellido_Paterno,''),' ',COALESCE(lead.Apellido_Materno,'')) as lead"),
                    'foreman.Empleado_ID as foreman_id',
                    DB::raw("CONCAT(COALESCE(foreman.Nombre,''),' ',COALESCE(foreman.Apellido_Paterno,''),' ',COALESCE(foreman.Apellido_Materno,'')) as foreman"),
                )
                ->join('empresas', 'empresas.Emp_ID', 'proyectos.Emp_ID')
                ->leftJoin('personal as proyect_manager', 'proyect_manager.Empleado_ID', 'proyectos.Manager_ID')
                ->leftJoin('personal as lead', 'lead.Empleado_ID', 'proyectos.Lead_ID')
                ->leftJoin('personal as foreman', 'foreman.Empleado_ID', 'proyectos.Foreman_ID')
                ->leftJoin('personal as asistente_proyecto_manager', 'asistente_proyecto_manager.Empleado_ID', 'proyectos.Asistant_Proyect_ID')
                ->where('proyectos.Nombre', 'like', '%' . $request->searchTerm . '%')->get();
        }
        foreach ($proyectos as $proyecto) {
            $data[] = array(
                "id" => $proyecto->Pro_ID,
                "text" => $proyecto->Nombre,
                "codigo" => $proyecto->Codigo,
                "empresa" => $proyecto->nombre_empresa,
                "proyecto_manager_id" => $proyecto->proyecto_manager_id,
                "proyecto_manager" => $proyecto->proyecto_manager,
                "asistente_proyecto_manager" => $proyecto->asistente_proyecto_manager,
                "asistente_proyecto_manager_id" => $proyecto->asistente_proyecto_manager_id,
                "foreman_id" => $proyecto->foreman_id,
                "foreman" => $proyecto->foreman,
                "lead_id" => $proyecto->lead_id,
                "lead" => $proyecto->lead,
            );
        }
        return response()->json($data);
    }

    /*VISTA DE ADMIN CONFIGURACION Y MANEJO DE CRUD  ROL COMO ADMINISTRADOR */
    public function index_admin()
    {
        return view('panel.proyectos.notas');
    }
    public function datatable_admin(Request $request)
    {
        $notas = DB::table('proyectos_nota')
            ->select(
                'proyectos_nota.*',
                DB::raw('DATE_FORMAT(proyectos_nota.fecha_entrega, "%W %d %m %Y") as fecha_entrega'),
                'proyectos.nombre as nombre_proyecto',
                'proyectos.Codigo as codigo_proyecto',
            )
        //restriccion por proyecto
            ->when(!auth()->user()->verificarRol([1]) == 1 ? true : false, function ($q) use ($request) {
                //dd('es admin');
                return $q->where(function ($q) {
                    $q->orWhere('proyectos.Foreman_ID', '=', auth()->user()->Empleado_ID)
                        ->orWhere('proyectos.Project_Manager_ID', '=', auth()->user()->Empleado_ID)
                        ->orWhere('proyectos.Lead_ID', '=', auth()->user()->Empleado_ID)
                        ->orWhere('proyectos.Asistant_Proyect_ID', '=', auth()->user()->Empleado_ID);
                });
            })
            ->when(!is_null($request->query('buscar')), function ($q) use ($request) {
                return $q->where('proyectos_nota.nota', 'like', '%' . $request->query('buscar') . '%');
            })
            ->when(!is_null($request->query('from_date')) && !is_null($request->query('to_date')), function ($q) use ($request) {
                $from = date('Y-m-d', strtotime($request->query('from_date')));
                $to = date('Y-m-d', strtotime($request->query('to_date')));
                return $q->whereBetween('proyectos_nota.fecha_entrega', [$from, $to]);
            })
            ->join('proyectos', 'proyectos.Pro_ID', 'proyectos_nota.proyecto_id')
            ->join('empresas', 'empresas.Emp_ID', 'proyectos.Emp_ID')
            ->get();
        return Datatables::of($notas)
            ->addIndexColumn()
            ->addColumn('images', function ($data) {
                $button = "
                <i class='fas fa-images text-info upload_image cursor-pointer files_nota' data-id='$data->id' title='Files Note'></i>
            ";
                return $button;
            })
            ->addColumn('acciones', function ($data) {
                $button = "";
                if (auth()->user()->verificarRol([1]) == 1 ? true : false) {
                    $button .= "
                    <a href=" . route('notas.proyecto.show', ['id' => $data->id]) . " >
                        <i class='fas fa-eye text-info upload_image cursor-pointer view_nota' data-id='$data->id' title='View Note'></i>
                    </a>
                    <a href=" . route('notas.proyecto.pdf', ['id' => $data->id]) . " class='load_descargar'>
                        <i class='fas fa-file-download ms-text-success' title='Download'></i>
                    </a>
                    <i class='fas fa-pencil-alt ms-text-warning cursor-pointer edit_nota' data-id='$data->id' title='Edit Note'></i>
                    <i class='far fa-trash-alt ms-text-danger cursor-pointer delete_nota' data-id='$data->id' title='Delete Note'></i>
                    ";
                }
                return $button;
            })
            ->rawColumns(['acciones', 'images'])
            ->make(true);
    }

    /*VISTA DE PROYECTO NO INDEXADO EN MENU SOLO UN PROYECTO*/
    public function index_proyecto($id)
    {
        $proyecto = DB::table('proyectos')
            ->where('proyectos.Pro_ID', $id)
            ->first();
        return view('panel.proyectos.notas_proyecto', compact('proyecto'));
    }
    public function datatable_proyecto(Request $request, $id)
    {
        $notas = DB::table('proyectos_nota')
            ->select(
                'proyectos_nota.*',
                DB::raw('DATE_FORMAT(proyectos_nota.fecha_entrega, "%W %d %m %Y") as fecha_entrega'),
                'proyectos.nombre as nombre_proyecto',
                'proyectos.Codigo as codigo_proyecto',
            )
        //restriccion por proyecto
            ->when(!auth()->user()->verificarRol([1]) == 1 ? true : false, function ($q) use ($request) {
                //dd('es admin');
                return $q->where(function ($q) {
                    $q->orWhere('proyectos.Foreman_ID', '=', auth()->user()->Empleado_ID)
                        ->orWhere('proyectos.Project_Manager_ID', '=', auth()->user()->Empleado_ID)
                        ->orWhere('proyectos.Lead_ID', '=', auth()->user()->Empleado_ID)
                        ->orWhere('proyectos.Asistant_Proyect_ID', '=', auth()->user()->Empleado_ID);
                });
            })
            ->when(!is_null($request->query('buscar')), function ($q) use ($request) {
                return $q->where('proyectos_nota.nota', 'like', '%' . $request->query('buscar') . '%');
            })
            ->when(!is_null($request->query('from_date')) && !is_null($request->query('to_date')), function ($q) use ($request) {
                $from = date('Y-m-d', strtotime($request->query('from_date')));
                $to = date('Y-m-d', strtotime($request->query('to_date')));
                return $q->whereBetween('proyectos_nota.fecha_entrega', [$from, $to]);
            })
            ->where('proyectos_nota.proyecto_id', $id)
            ->join('proyectos', 'proyectos.Pro_ID', 'proyectos_nota.proyecto_id')
            ->join('empresas', 'empresas.Emp_ID', 'proyectos.Emp_ID')
            ->get();
        return Datatables::of($notas)
            ->addIndexColumn()
            ->addColumn('images', function ($data) {
                $button = "
                <i class='fas fa-images text-info upload_image cursor-pointer files_nota' data-id='$data->id' title='Files Note'></i>
            ";
                return $button;
            })
            ->addColumn('acciones', function ($data) {
                $button = "
                    <a href=" . route('notas.proyecto.show', ['id' => $data->id]) . " >
                        <i class='fas fa-eye text-info upload_image cursor-pointer view_nota' data-id='$data->id' title='View Note'></i>
                    </a>
                    <a href=" . route('notas.proyecto.pdf', ['id' => $data->id]) . " class='load_descargar'>
                        <i class='fas fa-file-download ms-text-success' title='Download'></i>
                    </a>";
                //<a href=" . route('notas.proyecto.show', ['id' => $data->id]) . "><i class='fas fa-file-download ms-text-success'  title='Download Note'></i></a>
                return $button;
            })
            ->rawColumns(['acciones', 'images'])
            ->make(true);
    }
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
            'fecha_entrega' => 'nullable',
            'proyecto_id' => 'required',
            'pm' => 'required',
            'apm' => 'nullable',
            'note' => 'required',
        );
        $messages = [
            'proyecto_id.required' => "The project field is required",
            'pm.required' => "The check PM field is required",
            'note.required' => "The note field is required",
        ];

        $error = Validator::make($request->all(), $rules, $messages);
        if (count($error->errors()->all()) > 0) {
            return response()->json([
                'status' => 'error',
                'message' => $error->errors()->all(),
            ]);
        }
        $codigo = DB::table('proyectos_nota')
            ->join('proyectos', 'proyectos.Pro_ID', 'proyectos_nota.proyecto_id')
            ->where('proyectos.Pro_ID', $request->proyecto_id)
            ->orderBy('proyectos_nota.id', 'DESC')
            ->first();
        if ($codigo) {
            $convert_to_int = (str_replace('0', '', substr($codigo->codigo, -4, 4)));
        }else{
            $convert_to_int = (str_replace('0', '', substr('#PN00000', -4, 4)));
        }
        $proximoCodigo = strlen($convert_to_int) + 1;

        $codigo = $this->detector_codigo($proximoCodigo);
        $proyectos_nota = DB::table('proyectos_nota')->insertGetId([
            'codigo' => $codigo,
            'fecha_registro' => date('Y-m-d H:i:s', strtotime($request->fecha_registro)),
            'fecha_entrega' => date('Y-m-d H:i:s', strtotime($request->fecha_entrega)),
            'nota' => $request->note,
            'creado_por' => auth()->user()->Empleado_ID,
            'proyecto_id' => $request->proyecto_id,
            'project_manager_id' => $request->pm == null ? '' : $request->pm,
            'asistente_project_manager_id' => $request->apm == null ? '' : $request->apm,
            'foreman_id' => $request->foreman == null ? '' : $request->foreman,
            'lead_id' => $request->lead == null ? '' : $request->lead,
        ]);
        $this->upload_image($request, $proyectos_nota, 'nota_files');
        return response()->json([
            'status' => 'ok',
            'message' => 'Nota saved successfully',
        ]);
    }
    /*contador de nota */
    public function detector_codigo($cantidad)
    {
        $code = "";
        switch (strlen($cantidad)) {
            case 1:
                $code = "#PN0000$cantidad";
                break;
            case 2:
                $code = "#PN000$cantidad";
                break;
            case 3:
                $code = "#PN00$cantidad";
                break;
            case 4:
                $code = "#PN0$cantidad";
                break;
            default:
                break;
        }
        return $code;
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $proyecto = DB::table('proyectos_nota')
            ->select(
                'proyectos_nota.*',
                'empresas.Nombre as nombre_empresa',
                'proyectos.Codigo as codigo_proyecto',
                'proyectos.Nombre as nombre_proyecto',
                DB::raw("CONCAT(COALESCE(proyectos.Ciudad,''), ' ',  COALESCE(proyectos.Calle,''), ' ',  COALESCE(proyectos.Numero,'')) as dirrecion"),
                DB::raw("CONCAT(COALESCE(personal.Nombre,''), ' ',  COALESCE(personal.Apellido_Paterno,''), ' ',  COALESCE(personal.Apellido_Materno,'')) as nombre_empleado")
            )
            ->where('proyectos_nota.id', $id)
            ->join('proyectos', 'proyectos.Pro_ID', 'proyectos_nota.proyecto_id')
            ->join('empresas', 'proyectos.Emp_ID', 'empresas.Emp_ID')
            ->join('personal', 'proyectos_nota.creado_por', 'personal.Empleado_ID')
            ->first();
        $images = DB::table('proyecto_nota_imagenes')
            ->where('proyecto_nota_imagenes.proyectos_nota_id', $id)
            ->get()
            ->toArray();
        return view('panel.proyectos.reports.nota_project', compact('proyecto', 'images'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $admin = "si";
        if (!auth()->user()->verificarRol([1])) {
            $admin = "no";
        }
        $nota = DB::table('proyectos_nota')
            ->select(
                'proyectos_nota.*',
                DB::raw('DATE_FORMAT(proyectos_nota.fecha_entrega, "%m/%d/%Y %H:%i:%s") as fecha_entrega'),
                'proyectos.Pro_ID',
                'proyectos.Codigo',
                'proyectos.Nombre',
                'empresas.Nombre as nombre_empresa',
                DB::raw("CONCAT(COALESCE(proyect_manager.Nombre,''),' ',COALESCE(proyect_manager.Apellido_Paterno,''),' ',COALESCE(proyect_manager.Apellido_Materno,'')) as project_manager"),
                DB::raw("CONCAT(COALESCE(asistente_proyecto_manager.Nombre,''),' ',COALESCE(asistente_proyecto_manager.Apellido_Paterno,''),' ',COALESCE(asistente_proyecto_manager.Apellido_Materno,'')) as asistente_project_manager"),
                DB::raw("CONCAT(COALESCE(lead.Nombre,''),' ',COALESCE(lead.Apellido_Paterno,''),' ',COALESCE(lead.Apellido_Materno,'')) as lead"),
                DB::raw("CONCAT(COALESCE(foreman.Nombre,''),' ',COALESCE(foreman.Apellido_Paterno,''),' ',COALESCE(foreman.Apellido_Materno,'')) as foreman"),
            )
            ->where('proyectos_nota.id', $id)
            ->join('proyectos', 'proyectos.Pro_ID', 'proyectos_nota.proyecto_id')
            ->join('empresas', 'empresas.Emp_ID', 'proyectos.Emp_ID')
            ->leftJoin('personal as proyect_manager', 'proyect_manager.Empleado_ID', 'proyectos.Manager_ID')
            ->leftJoin('personal as lead', 'lead.Empleado_ID', 'proyectos.Lead_ID')
            ->leftJoin('personal as foreman', 'foreman.Empleado_ID', 'proyectos.Foreman_ID')
            ->leftJoin('personal as asistente_proyecto_manager', 'asistente_proyecto_manager.Empleado_ID', 'proyectos.Asistant_Proyect_ID')
            ->first();
        return response()->json([
            'status' => 'ok',
            'data' => [
                'admin' => $admin,
                'nota' => $nota,
                'files' => $this->get_images($nota->id),
            ],
        ]);
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
            'fecha_entrega' => 'nullable',
            'proyecto_id' => 'required',
            'pm' => 'required',
            'apm' => 'nullable',
            'note' => 'required',
        );
        $messages = [
            'proyecto_id.required' => "The project field is required",
            'pm.required' => "The check PM field is required",
            'note.required' => "The note field is required",
        ];
        $error = Validator::make($request->all(), $rules, $messages);
        if (count($error->errors()->all()) > 0) {
            return response()->json([
                'status' => 'error',
                'message' => $error->errors()->all(),
            ]);
        }
        $proyectos_nota = DB::table('proyectos_nota')
            ->where('proyectos_nota.id', $id)
            ->update([
                'fecha_entrega' => date('Y-m-d H:i:s', strtotime($request->fecha_entrega)),
                'nota' => $request->note,
                'creado_por' => auth()->user()->Empleado_ID,
                'proyecto_id' => $request->proyecto_id,
                'project_manager_id' => $request->pm == null ? '' : $request->pm,
                'asistente_project_manager_id' => $request->apm == null ? '' : $request->apm,
            ]);
        $this->upload_image($request, $id, 'nota_files');
        return response()->json([
            'status' => 'ok',
            'message' => 'Nota modified successfully',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $nota = DB::table('proyectos_nota')
            ->where('proyectos_nota.id', $id)
            ->delete();
        if ($nota) {
            return response()->json([
                'status' => 'ok',
                'message' => 'Successfully deleted',
            ], 200);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => ['An error occurred'],
            ], 200);
        }

    }
    /*
     *otros
     */
    public function upload_image(Request $request, $id, $nombre_campo = 'nota_files')
    {

        $allowedfileExtension = ['jpg', 'png', 'jpeg', 'pdf', 'docx', 'doc', 'xlsx', 'xls', 'csv'];
        if ($request->hasfile($nombre_campo)) {

            foreach ($request->file($nombre_campo) as $file) {
                $filename = $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension();
                $fileSize = $file->getSize();
                $check = in_array(strtolower($extension), $allowedfileExtension);
                if ($check) {

                    if ($extension == 'jpg' || $extension == 'png' || $extension == 'jpeg') {
                        $name_img = "$id-" . uniqid() . time() . "." . $extension;
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
                        $insert = DB::table('proyecto_nota_imagenes')->insertGetId([
                            'nombre' => $name_img,
                            'tipo' => $extension,
                            'proyectos_nota_id' => $id,
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
                                'url' => url("project-notas/delete_file/$id"), // server api to delete the file based on key
                            ];

                        } else {
                            $errors[] = $fileName;
                        }
                    } else {
                        $name_img = "$id-" . uniqid() . time() . "." . $extension;
                        //$path = public_path() . '/docs/' . $name_img;
                        $file->move(public_path('docs'), $name_img);

                        $insert = DB::table('proyecto_nota_imagenes')->insertGetId([
                            'nombre' => $name_img,
                            'tipo' => $extension,
                            'proyectos_nota_id' => $id,
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
                                'url' => url("project-notas/delete_file/$id"), // server api to delete the file based on key
                            ];
                        } else {
                            $errors[] = $fileName;
                        }
                    }
                }
            }
        }
    }

    public function upload_image_async(Request $request, $id, $nombre_campo = 'modal_nota_files')
    {
        $allowedfileExtension = ['jpg', 'png', 'jpeg', 'pdf', 'docx', 'doc', 'xlsx', 'xls', 'csv'];
        $preview = $config = $errors = [];
        if ($request->hasfile($nombre_campo)) {
            foreach ($request->file($nombre_campo) as $file) {
                $filename = $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension();
                $fileSize = $file->getSize();
                $check = in_array($extension, $allowedfileExtension);

                if ($check) {
                    if ($extension == 'jpg' || $extension == 'png' || $extension == 'jpeg') {
                        $name_img = "$id-" . uniqid() . time() . "." . $extension;
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
                        //dd("con conversion");
                        $insert = DB::table('proyecto_nota_imagenes')->insertGetId([
                            'nombre' => $name_img,
                            'tipo' => $extension,
                            'proyectos_nota_id' => $id,
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
                                'url' => url("project-notas/delete_file/$id"), // server api to delete the file based on key
                            ];

                        } else {
                            $errors[] = $fileName;
                        }
                    } else {
                        $name_img = "$id-" . uniqid() . time() . "." . $extension;
                        //$path = public_path() . '/docs/' . $name_img;
                        $file->move(public_path('docs'), $name_img);
                        //dd("sin conversion");
                        $insert = DB::table('proyecto_nota_imagenes')->insertGetId([
                            'nombre' => $name_img,
                            'tipo' => $extension,
                            'proyectos_nota_id' => $id,
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
                                'url' => url("project-notas/delete_file/$id"), // server api to delete the file based on key
                            ];
                        } else {
                            $errors[] = $fileName;
                        }
                    }
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
    public function get_images($id)
    {
        $archivo = DB::table('proyecto_nota_imagenes')
            ->where('proyecto_nota_imagenes.proyectos_nota_id', $id)
            ->get();
        $list = new stdClass();
        $list->initialPreview = [];
        $list->initialPreviewConfig = [];
        if ($archivo) {
            foreach ($archivo as $val) {
                $newFileUrl = url('/') . '/docs/' . $val->nombre;
                $list->initialPreview[] = $newFileUrl;
                $list->initialPreviewConfig[] = [
                    'caption' => $val->caption,
                    'size' => $val->size,
                    'downloadUrl' => $newFileUrl,
                    'url' => url("project-notas/delete_image/$id"),
                    'key' => $val->id,
                ];
            }
        }
        return $list;
    }
    public function delete_image($id, Request $request)
    {
        $query = DB::table('proyecto_nota_imagenes')
            ->where('id', $request->key)
            ->where('proyecto_nota_imagenes.proyectos_nota_id', $id);
        $archivo = $query->first();
        if ($archivo) {
            $path = public_path() . '/uploads/' . $archivo->nombre;
            if (File::exists($path) && $archivo->nombre) {
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
    public function pdf($id, $view = false)
    {
        $proyecto = DB::table('proyectos_nota')
            ->select(
                'proyectos_nota.*',
                'empresas.Nombre as nombre_empresa',
                'proyectos.Codigo as codigo_proyecto',
                'proyectos.Nombre as nombre_proyecto',
                DB::raw("CONCAT(COALESCE(proyectos.Ciudad,''), ' ',  COALESCE(proyectos.Calle,''), ' ',  COALESCE(proyectos.Numero,'')) as dirrecion"),
                DB::raw("CONCAT(COALESCE(personal.Nombre,''), ' ',  COALESCE(personal.Apellido_Paterno,''), ' ',  COALESCE(personal.Apellido_Materno,'')) as nombre_empleado")
            )
            ->where('proyectos_nota.id', $id)
            ->join('proyectos', 'proyectos.Pro_ID', 'proyectos_nota.proyecto_id')
            ->join('empresas', 'proyectos.Emp_ID', 'empresas.Emp_ID')
            ->join('personal', 'proyectos_nota.creado_por', 'personal.Empleado_ID')
            ->first();
        $images = DB::table('proyecto_nota_imagenes')
            ->select(
                'proyecto_nota_imagenes.*',
                'proyecto_nota_imagenes.nombre as imagen'
            )
            ->where('proyecto_nota_imagenes.proyectos_nota_id', $id)
            ->get()
            ->toArray();
        //dd($proyecto);
        $pdf = PDF::loadView('panel.proyectos.reports.nota_project_pdf', ['proyecto' => $proyecto, 'images' => $images])->setPaper('letter')->setWarnings(false);
        if ($view === true) {
            return $pdf;
        }

        return $pdf->download("project note $proyecto->codigo.pdf");
    }
}
