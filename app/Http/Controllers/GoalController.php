<?php

namespace App\Http\Controllers;

use App\ContactoProyecto;
use App\Informe_proyecto;
use DataTables;
use File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Image;
use Mail;
use PDF;
use Validator;

class GoalController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    /**
     * Listar goals.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //dd($request->query('Codigo'));
        if (request()->ajax()) {
            $data = DB::table('informe_proyecto')->select(
                'informe_proyecto.Pro_ID',
                'informe_proyecto.Codigo',
                'informe_proyecto.Empleado_ID',
                'proyectos.Codigo as codigo_proyecto',
                'informe_proyecto.Informe_ID as informe_id',
                'informe_proyecto.Fecha as fecha',
                DB::raw('DATE_FORMAT(informe_proyecto.fecha , "%m/%d/%Y") as fecha'),
                'proyectos.Nombre as nombre_proyecto',
                'proyectos.estado as estado',
                DB::raw('DATE_FORMAT(proyectos.Fecha_Inicio , "%m/%d/%Y" ) as fecha_inicio'),
                DB::raw('DATE_FORMAT(proyectos.Fecha_Fin , "%m/%d/%Y") as fecha_fin'),
                'empresas.Nombre as nombre_empresa',
                'personal.Usuario as username'
            )
            //filtrando por fecha
                ->when(!empty(request()->from_date), function ($q) {
                    $from = date('Y-m-d', strtotime(request()->from_date));
                    $to = date('Y-m-d', strtotime(request()->to_date));
                    return $q->whereBetween('informe_proyecto.fecha', [$from, $to]);
                })
            //filtrando si hay proyectos
                ->when(!empty(request()->proyecto), function ($q) {
                    //dd('proyecto');
                    return $q->where('proyectos.Nombre', 'like', '%' . request()->proyecto . '%');
                })
            //filtrando por codigo visit report
                ->when(!empty($request->query('codigo')), function ($q) use ($request) {
                    //dd('codigo');
                    return $q->where('informe_proyecto.Codigo', 'like', '%' . $request->query('codigo') . '%');
                })
                ->when(!empty($request->query('comentario')), function ($q) use ($request) {
                    //dd('codigo');
                    return $q->where('informe_proyecto.Drywall_comments', 'like', '%' . $request->query('comentario') . '%');
                })
                ->when(!auth()->user()->verificarRol([1]) && !auth()->user()->verificarRol([10]), function ($query) {
                    return $query->where('informe_proyecto.Empleado_ID', auth()->user()->Empleado_ID);
                })
                ->where('informe_proyecto.delete_informe_proyecto', '1')
                ->join('proyectos', 'proyectos.Pro_ID', 'informe_proyecto.Pro_ID')
                ->join('empresas', 'empresas.Emp_ID', 'proyectos.Emp_ID')
                ->join('personal', 'personal.Empleado_ID', 'informe_proyecto.Empleado_ID')
                ->orderBy('informe_proyecto.fecha', 'DESC')
                ->get();
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('acciones', function ($data) {
                    /* validar uso */
                    $uso_descarga = $this->get_cantidad_uso($data->informe_id, 'descarga');
                    $uso_email = $this->get_cantidad_uso_email($data, 'email');
                    /* validar uso */
                    $button = "
                    <div class='icon-badge-group m-0'>
                        <a href='" . route('show.goal.btn') . "' class='show_report' data-id='$data->informe_id'><i class='fas fa-eye ms-text-primary'></i></a>
                        <a href='" . route('edit.goal', ['id' => $data->informe_id]) . "'><i class='fas fa-pencil-alt ms-text-warning'></i></a>
                        <i class='far fa-trash-alt ms-text-danger delete cursor-pointer' data-id='$data->informe_id' title='Delete'></i>
                        $uso_descarga
                        $uso_email
                    </div>
                        ";
                    return $button;
                })
                ->addColumn('images', function ($data) {
                    $button = "<i class='fas fa-images text-info upload_image cursor-pointer' data-image='images' data-id='$data->informe_id'></i>";
                    return $button;
                })
                ->rawColumns(['acciones', 'images'])
                ->make(true);
        }
        return view('panel.goal.list');
    }
    /**
     * configuracion de mail
     */

    public function get_config_mail($id, $proyect = "")
    {
        $config = DB::table('configuration')->select('body_ticket_email', 'title_ticket_email')->find(1);
        if ($proyect == "") {
            $proyect = Informe_proyecto::select('Pro_ID')
                ->where('Informe_ID', $id)
                ->pluck('Pro_ID')->first();
        }
        $emails = DB::table('proyectos')
            ->selectRaw("
        f.email as Foreman_mail,
        l.email as Lead_mail,
        c_o.email as Coordinador_Obra_mail,
        c.email as Pwtsuper_mail")
            ->where('Pro_ID', $proyect)
            ->leftJoin('personal as f', 'f.Empleado_ID', 'proyectos.Foreman_ID')
            ->leftJoin('personal as l', 'l.Empleado_ID', 'proyectos.Lead_ID')
            ->leftJoin('personal as c', 'c.Empleado_ID', 'proyectos.Coordinador_ID')
            ->leftJoin('personal as c_o', 'c_o.Empleado_ID', 'proyectos.Coordinador_Obra_ID')
            ->first();
        $email_contac = ContactoProyecto::select('email')->where('Pro_ID', $proyect)
            ->where('tipo_contacto.nombre', 'ticket')
            ->join('tipo_contacto', 'tipo_contacto.id_tipo_contacto', 'contacto_proyecto.tipo_contacto')
            ->join('personal', 'personal.Empleado_ID', 'contacto_proyecto.Empleado_ID')
            ->get();

        $goal = DB::table('informe_proyecto')
            ->select(
                'informe_proyecto.*',
                'proyectos.Nombre',
                'proyectos.Codigo as codigo_proyecto'
            )
            ->where('informe_proyecto.Informe_ID', $id)
            ->join('proyectos', 'proyectos.Pro_ID', 'informe_proyecto.Pro_ID')
            ->first();
        $goal->subempresa = substr(str_replace(' ', '', strtoupper($goal->Nombre)), 0, 5);

        return response()->json([
            'config' => $config,
            'informe_proyecto' => strval($id),
            'emails' => $emails,
            'email_contac' => $email_contac,
            'subempresa' => "$goal->subempresa-VisitReport$goal->Codigo",
        ]);
    }

    /**
     * formulario crear goal.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $Informe_ID = Informe_proyecto::select('Informe_ID')
            ->where('estado', 'pendiente')
            ->pluck('Informe_ID')->first();
        if (!$Informe_ID) {
            $Informe_ID = Informe_proyecto::insertGetId([
                'Empleado_ID' => auth()->user()->Empleado_ID,
                'estado' => 'pendiente',
            ]);
        }
        return view('panel.goal.new', compact('Informe_ID'));
    }

    /**
     * guardar goal en la bd.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = array(
            'Informe_ID' => 'required',
            'Pro_ID' => 'required',
            'Empleado_ID' => 'nullable',
            'Fecha' => 'nullable',
            'Drywall_comments' => 'nullable',
        );

        $error = Validator::make($request->all(), $rules);
        if (request()->ajax() === true) {
            return response()->json(['errors' => $error->errors()->all()]);
        }
        $codigo = Informe_proyecto::join('personal', 'personal.Empleado_ID', 'informe_proyecto.Empleado_ID')
            ->join('proyectos', 'proyectos.Pro_ID', 'informe_proyecto.Pro_ID')
            ->where('proyectos.Pro_ID', $request->Pro_ID)
            ->where('informe_proyecto.estado', 'creado')
            ->count() + 1;
        $codigo = $this->detector_codigo($codigo);
        $informe_proyect = Informe_proyecto::find($request->Informe_ID);
        $modificando = DB::table('informe_proyecto')
            ->where('Informe_ID', $request->Informe_ID)
            ->update([
                'Pro_ID' => $request->Pro_ID,
                'Codigo' => $codigo,
                'Empleado_ID' => auth()->user()->Empleado_ID,
                'Fecha' => $request->Fecha,
                'Drywall_comments' => $request->new_comentarios,
                'delete_informe_proyecto' => 1,
                'estado' => 'creado',
            ]);
        if ($request->is_mail != "false") {
            if ($request->is_mail == "all") {
                $this->sendmailgoal($request, $request->Informe_ID);
            } else {
                $this->sendmailgoal($request, $request->Informe_ID, "part");
            }
        }
        return redirect(route('list.goal', ['id' => $informe_proyect->Informe_ID]))->with('success', 'New project report created');
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
            $allowedfileExtension = ['jpg', 'png', 'jpeg'];
            $files = $request->file($campo);

            foreach ($files as $file) {

                $filename = $file->getClientOriginalName();
                //$file = $this->rotar_imagen($file, $filename);
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
                    $insert = DB::table('goal_imagen')->insertGetId([
                        'imagen' => $name_img,
                        'tipo' => $type,
                        'id_informe_proyecto' => $id,
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
                            'url' => url("delete_goal/$id/$type/goal"), // server api to delete the file based on key
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

    /**
     * eliminar imagen del goal y eliminarlo del directorio
     */
    public function detector_codigo($cantidad)
    {
        $code = "";
        switch (strlen($cantidad)) {
            case 1:
                $code = "#VR0000$cantidad";
                break;
            case 2:
                $code = "#VR000$cantidad";
                break;
            case 3:
                $code = "#VR00$cantidad";
                break;
            case 4:
                $code = "#VR0$cantidad";
                break;
            default:
                break;
        }
        return $code;
    }
    public function delete_image($id, $type, Request $request)
    {
        $query = DB::table('goal_imagen')
            ->where('t_imagen_id', $request->key)
            ->where('tipo', $type)
            ->where('id_informe_proyecto', $id);
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

    /**
     * obtener las imagenes del goal
     */
    public function get_images($id, $type)
    {
        $images = DB::table('goal_imagen')
            ->where('id_informe_proyecto', $id)
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
                    'url' => url("delete_goal/$id/$type/goal"),
                    'key' => $val->t_imagen_id,
                ];
            }
        }
        return response()->json($list);
    }
    /**
     * mostar el goal.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $goal = DB::table('informe_proyecto')
            ->selectRaw("informe_proyecto.*,
        proyectos.Nombre as nombre_proyecto,
        CONCAT(COALESCE(proyectos.Nombre,''), ' ',  COALESCE(proyectos.Ciudad,''), ' ',  COALESCE(proyectos.Calle,''), ' ',  COALESCE(proyectos.Numero,'')) as dirrecion,
        CONCAT(COALESCE(personal.Nombre,''), ' ',  COALESCE(personal.Apellido_Paterno,''), ' ',  COALESCE(personal.Apellido_Materno,'')) as nombre_empleado,
        empresas.Nombre as nombre_empresa,
        empresas.Codigo")
            ->where('Informe_ID', $id)
            ->join('proyectos', 'proyectos.Pro_ID', 'informe_proyecto.Pro_ID')
            ->join('empresas', 'empresas.Emp_ID', 'proyectos.Emp_ID')
            ->join('personal', 'personal.Empleado_ID', 'informe_proyecto.Empleado_ID')
            ->first();
        $images = DB::table('goal_imagen')
            ->select('goal_imagen.*')
            ->where('id_informe_proyecto', $id)
            ->get();

        return view('panel.goal.view', compact('goal', 'images'));
    }
    public function show_btn(Request $request)
    {
        $id = $request->query('view');
        $goal = DB::table('informe_proyecto')
            ->selectRaw("
            informe_proyecto.*,
            DATE_FORMAT(informe_proyecto.Fecha , '%m/%d/%Y' ) as Fecha,
            proyectos.Nombre as nombre_proyecto,
            empresas.Codigo as codigo_empresa,
            proyectos.Codigo as codigo_proyecto,
            empresas.Nombre as nombre_empresa,
            CONCAT( proyectos.Estado,' ', proyectos.Ciudad,' ',proyectos.Calle,' ', proyectos.Numero) as dirrecion,
            CONCAT(COALESCE(personal.Nombre,''), ' ',  COALESCE(personal.Apellido_Paterno,''), ' ',  COALESCE(personal.Apellido_Materno,'')) as nombre_empleado")
            ->where('Informe_ID', $id)
            ->join('proyectos', 'proyectos.Pro_ID', 'informe_proyecto.Pro_ID')
            ->join('empresas', 'empresas.Emp_ID', 'proyectos.Emp_ID')
            ->join('personal', 'personal.Empleado_ID', 'informe_proyecto.Empleado_ID')
            ->first();
        $images = DB::table('goal_imagen')
            ->select('goal_imagen.*')
            ->where('id_informe_proyecto', $id)
            ->get()->toArray();
        $goal->subempresa = $goal->nombre_proyecto . " - VisitReport" . $goal->Codigo;
        //dd($images);
        return view('panel.goal.view', compact('goal', 'images'));
    }
    /**
     * obtener las preguntas del goal where, etc
     */
    public function get_where_goal(Request $request, $tipo, $id)
    {
        $data = [];
        if ($tipo != "where") {
            if (!isset($request->searchTerm)) {
                $where = DB::table('floor')->select('Flo_ID', 'Nombre')->where('Pro_ID', $id)->get();
                return response()->json($razon);
            } else {
                $where = DB::table('floor')->select('Flo_ID', 'Nombre')->where('Nombre', 'like', '%' . $request->searchTerm . '%')->get();
                return response()->json($razon);
            }
            foreach ($where as $row) {
                $data[] = array(
                    "id" => $row->Flo_ID,
                    "text" => $row->Nombre,
                );
            }
        } else {
            if (!isset($request->searchTerm)) {
                $where = DB::table('area_control')
                    ->select('area_control.Nombre as area', 'edificios.Nombre as edificio', 'floor.Nombre as floor')
                    ->where('area_control.Pro_ID', $id)
                    ->leftJoin('floor', 'area_control.Floor_ID', 'floor.Floor_ID')
                    ->join('edificios', 'floor.Edificio_ID', 'edificios.Edificio_ID')
                    ->get();
            } else {
                $where = DB::table('area_control')
                    ->select('area_control.Nombre as area', 'edificios.Nombre as edificio', 'floor.Nombre as floor')
                    ->where('area_control.Pro_ID', $id)
                    ->where('floor.Nombre', 'like', '%' . $request->searchTerm . '%')
                    ->Orwhere('area_control.Nombre', 'like', '%' . $request->searchTerm . '%')
                    ->Orwhere('edificios.Nombre', 'like', '%' . $request->searchTerm . '%')
                    ->join('floor', 'area_control.Floor_ID', 'floor.Floor_ID')
                    ->join('edificios', 'floor.Edificio_ID', 'edificios.Edificio_ID')
                    ->get();
            }
            foreach ($where as $row) {
                $data[] = array(
                    "id" => $row->area,
                    "text" => "$row->edificio $row->floor $row->area",
                );
            }
        }

        return response()->json($data);
    }
    /**
     * obtener las preguntas del goal problem, etc
     */
    public function goal_question(Request $request, $tipo)
    {
        $data = [];
        if ($tipo == "problem") {
            if (!isset($request->searchTerm)) {
                $razongoal = DB::table('goal_problem')
                    ->select('id', 'descripcion')
                    ->get();
            } else {
                $razongoal = DB::table('razongoal')
                    ->select('id', 'descripcion')
                    ->where('tipo', $tipo)
                    ->where('descripcion', 'like', '%' . $request->searchTerm . '%')->get();
            }
            foreach ($razongoal as $row) {
                $data[] = array(
                    "id" => $row->id,
                    "text" => $row->descripcion,
                );
            }
        }
        if ($tipo == "consequence") {
            if (!isset($request->searchTerm)) {
                $razongoal = DB::table('goal_consecuencia')
                    ->select('id', 'descripcion')
                    ->get();
            } else {
                $razongoal = DB::table('razongoal')
                    ->select('id', 'descripcion')
                    ->where('descripcion', 'like', '%' . $request->searchTerm . '%')->get();
            }
            foreach ($razongoal as $row) {
                $data[] = array(
                    "id" => $row->id,
                    "text" => $row->descripcion,
                );
            }
        }
        if ($tipo == "solution") {
            if (!isset($request->searchTerm)) {
                $razongoal = DB::table('goal_solucion')
                    ->select('id', 'descripcion')
                    ->get();
            } else {
                $razongoal = DB::table('goal_solucion')
                    ->select('id', 'descripcion')
                    ->where('descripcion', 'like', '%' . $request->searchTerm . '%')->get();
            }
            foreach ($razongoal as $row) {
                $data[] = array(
                    "id" => $row->id,
                    "text" => $row->descripcion,
                );
            }
        }
        return response()->json($data);
    }
    public function goal_buscar(Request $request, $tipo)
    {
        $data = [];
        if ($tipo == "problem") {
            if (!isset($request->searchTerm)) {
                $razongoal = DB::table('goal_problem')
                    ->select('id', 'descripcion')
                    ->get();
            } else {
                $razongoal = DB::table('goal_problem')
                    ->select('id', 'descripcion')
                    ->where('descripcion', 'like', '%' . $request->searchTerm . '%')->get();
            }
            foreach ($razongoal as $row) {
                $data[] = array(
                    "id" => $row->id,
                    "text" => $row->descripcion,
                );
            }
        }
        if ($tipo == "consequence") {
            if (!isset($request->searchTerm)) {
                $razongoal = DB::table('goal_consecuencia')
                    ->select('id', 'descripcion')
                    ->get();
            } else {
                $razongoal = DB::table('goal_consecuencia')
                    ->select('id', 'descripcion')
                    ->where('descripcion', 'like', '%' . $request->searchTerm . '%')->get();
            }
            foreach ($razongoal as $row) {
                $data[] = array(
                    "id" => $row->id,
                    "text" => $row->descripcion,
                );
            }
        }
        if ($tipo == "solution") {
            if (!isset($request->searchTerm)) {
                $razongoal = DB::table('goal_solucion')
                    ->select('id', 'descripcion')
                    ->get();
            } else {
                $razongoal = DB::table('goal_solucion')
                    ->select('id', 'descripcion')
                    ->where('descripcion', 'like', '%' . $request->searchTerm . '%')->get();
            }
            foreach ($razongoal as $row) {
                $data[] = array(
                    "id" => $row->id,
                    "text" => $row->descripcion,
                );
            }
        }
        return response()->json($data);
    }
    /**
     * mostart el goal para editar.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $informe_proyect = DB::table('informe_proyecto')
            ->selectRaw("informe_proyecto.*,
            DATE_FORMAT(informe_proyecto.Fecha , '%m/%d/%Y' ) as Fecha,
            informe_proyecto.Fecha as fechaCreacion,
            proyectos.Nombre as nombre_proyecto,
            empresas.Codigo as codigo_empresa,
            proyectos.Codigo as codigo_proyecto,
            CONCAT( proyectos.Estado,' ', proyectos.Ciudad,' ',proyectos.Calle,' ', proyectos.Numero) as dirrecion,
            CONCAT(COALESCE(personal.Nombre,''), ' ',  COALESCE(personal.Apellido_Paterno,''), ' ',  COALESCE(personal.Apellido_Materno,'')) as nombre_empleado,
            personal.Nick_Name")
            ->join('proyectos', 'proyectos.Pro_ID', 'informe_proyecto.Pro_ID')
            ->join('empresas', 'empresas.Emp_ID', 'proyectos.Emp_ID')
            ->join('personal', 'personal.Empleado_ID', 'informe_proyecto.Empleado_ID')
            ->where('Informe_ID', $id)
            ->first();

        $imges = DB::table('goal_imagen')->where('id_informe_proyecto', $id)->where('tipo', 'images')->get();
        //modificion de fechas
        $informe_proyect->Date_Check_coming = ($informe_proyect->Date_Check_coming) ? date("m/d/Y", strtotime($informe_proyect->Date_Check_coming)) : null;
        $informe_proyect->Date_Check_framing = ($informe_proyect->Date_Check_framing) ? date("m/d/Y", strtotime($informe_proyect->Date_Check_framing)) : null;
        $informe_proyect->Date_Check_hanging = ($informe_proyect->Date_Check_hanging) ? date("m/d/Y", strtotime($informe_proyect->Date_Check_hanging)) : null;
        $informe_proyect->Date_estimate = ($informe_proyect->Date_estimate) ? date("m/d/Y", strtotime($informe_proyect->Date_estimate)) : null;
        $informe_proyect->Date_actual = ($informe_proyect->Date_actual) ? date("m/d/Y", strtotime($informe_proyect->Date_actual)) : null;
        //dd($informe_proyect->Pro_ID, $informe_proyect->Fecha);
        $actividades = $this->getActividad($informe_proyect->Pro_ID, $informe_proyect->Fecha);
        //dd($actividades);
        return view('panel.goal.edit', compact('informe_proyect', 'imges', 'id', 'actividades'));
    }

    public function getActividad($proyecto_id, $fecha)
    {
        $actividades = DB::table('registro_diario')
            ->select(
                DB::raw("'update' as 'estado'"),
                'actividades.Actividad_ID',
                'actividades.Fecha',
                'personal.Empleado_ID',
                'personal.Nick_Name',
                'actividades.Hora',
                'registro_diario.Hora_Ingreso',
                'registro_diario.Hora_Salida',
                'registro_diario_actividad.Horas_Contract',
                'registro_diario_actividad.Horas_TM',
                'registro_diario_actividad.Detalles',
                'proyectos.Pro_ID',
                'proyectos.Nombre as nombre_proyecto',
                'edificios.Nombre as nombre_edificio',
                'edificios.Edificio_ID',
                'floor.Nombre as nombre_floor',
                'floor.Floor_ID',
                'area_control.Nombre as nombre_area',
                'area_control.Area_ID',
                'task.Task_ID',
                'task.Tas_IDT',
                DB::raw("CONCAT(task.Nombre) as nombre_tarea"),
                'registro_diario_actividad.Verificado_Foreman',
                'registro_diario_actividad.RDA_ID',
                'registro_diario.Reg_ID'
            )
            ->join('registro_diario_actividad', 'registro_diario.Reg_ID', 'registro_diario_actividad.Reg_ID')
            ->leftJoin('task', 'registro_diario_actividad.Task_ID', 'task.Task_ID')
            ->leftJoin('area_control', 'area_control.Area_ID', 'task.Area_ID')
            ->leftJoin('floor', 'floor.Floor_ID', 'area_control.Floor_ID')
            ->leftJoin('edificios', 'edificios.Edificio_ID', 'floor.Edificio_ID')
            ->leftJoin('personal', 'personal.Empleado_ID', 'registro_diario.Empleado_ID')
            ->leftJoin('actividades', 'registro_diario.Actividad_ID', 'actividades.Actividad_ID')
            ->leftJoin('proyectos', 'proyectos.Pro_ID', 'actividades.Pro_ID')
            ->where(DB::raw('substring(proyectos.Codigo, 1, 3)'), '<', 900)
            ->where('task.Nombre', 'like', '%super%')
        //->where('registro_diario.Fecha', date("Y-m-d", strtotime($fecha)))
            ->where('registro_diario.Pro_ID', $proyecto_id)
        //filtro verificar si es admin
        //->where('registro_diario.Empleado_ID', auth()->user()->Empleado_ID)
            ->when(!auth()->user()->verificarRol([1]) && !auth()->user()->verificarRol([10]), function ($query) {
                return $query->where('registro_diario.Empleado_ID', auth()->user()->Empleado_ID);
            })
            ->get();
        //dd($actividades);
        return $actividades;
    }

    ///verificar checkbox
    private function verificar_check($req)
    {
        if (!isset($req)) {
            return '0';
        } else {
            return '1';
        }
    }
    /**
     * actualizar el goal.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //dd($request->all());
        $rules = array(
            'Informe_ID' => 'required',
            'edit_Pro_ID' => 'required',
            'Empleado_ID' => 'nullable',
            'Fecha' => 'nullable',
            'edit_comentarios' => 'nullable',
        );

        $error = Validator::make($request->all(), $rules);
        if (request()->ajax() === true) {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        $informe_proyect = $query = DB::table('informe_proyecto')
            ->where('Informe_ID', $id)->first();
        $query = DB::table('informe_proyecto')
            ->where('Informe_ID', $id)
            ->update([
                'Drywall_comments' => $request->edit_comentarios,
            ]);
        if ($query) {
            if ($request->images) {
                $this->upload_image($query, "images", $request, "images");
            }
        }
        if ($request->is_mail != "false") {
            if ($request->is_mail == "all") {
                $this->sendmailgoal($request, $request->Informe_ID);
            } else {
                $this->sendmailgoal($request, $request->Informe_ID, "part");
            }
        }
        return redirect(route('list.goal', ['id' => $informe_proyect->Informe_ID]))->with('success', 'Modified project report created');
    }

    /**
     * eliminar el goal con una eliminacion logica.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        DB::table('informe_proyecto')
            ->where('Informe_ID', $id)
            ->update(['delete_informe_proyecto' => '0']);
        return response()->json(['success' => 'Report deleting successfully']);
    }

    /**
     * descargar pdf goal completo
     */
    public function pdf($id, $view = false)
    {
        $report_goal = DB::table('informe_proyecto')
            ->selectRaw("
          informe_proyecto.*,
            DATE_FORMAT(informe_proyecto.Fecha , '%m/%d/%Y' ) as Fecha,
            proyectos.Nombre as nombre_proyecto,
            empresas.Codigo as codigo_empresa,
            proyectos.Codigo as codigo_proyecto,
            empresas.Nombre as nombre_empresa,
            CONCAT( proyectos.Estado,' ', proyectos.Ciudad,' ',proyectos.Calle,' ', proyectos.Numero) as dirrecion,
            CONCAT(COALESCE(personal.Nombre,''), ' ',  COALESCE(personal.Apellido_Paterno,''), ' ',  COALESCE(personal.Apellido_Materno,'')) as nombre_empleado
        ")
            ->where('Informe_ID', $id)
            ->join('proyectos', 'proyectos.Pro_ID', 'informe_proyecto.Pro_ID')
            ->join('empresas', 'empresas.Emp_ID', 'proyectos.Emp_ID')
            ->join('personal', 'personal.Empleado_ID', 'informe_proyecto.Empleado_ID')
            ->first();
        $images = DB::table('goal_imagen')
            ->select('goal_imagen.*')
            ->where('id_informe_proyecto', $id)
            ->get()->toArray();
        $pdf = PDF::loadView('panel.goal.pdf', ['goal' => $report_goal, 'images' => $images])->setPaper('letter')->setWarnings(false);
        if ($view === true) {
            return $pdf;
        }

        $report_goal->subempresa = substr(str_replace(' ', '', strtoupper($report_goal->nombre_proyecto)), 0, 5);
        $this->añadir_cantidad_uso($report_goal->Informe_ID, 'descarga');
        return $pdf->download("$report_goal->subempresa-VisitReport$report_goal->Codigo.pdf");
    }

    /*
     *calcular el usos
     */
    private function añadir_cantidad_uso($informe_id, $tipo)
    {
        $verificar = DB::table('informe_proyecto')
            ->where('informe_proyecto.Informe_ID', $informe_id)
            ->first();

        switch ($tipo) {
            case 'email':
                $increment = DB::table('informe_proyecto')
                    ->where('informe_proyecto.Informe_ID', $informe_id)
                    ->update([
                        'email_send' => ($verificar->email_send) + 1,
                    ]);
                break;
            case 'descarga':
                $increment = DB::table('informe_proyecto')
                    ->where('informe_proyecto.Informe_ID', $informe_id)
                    ->update([
                        'descargas' => ($verificar->descargas) + 1,
                    ]);
                break;

            default:
                # code...
                break;
        }
    }
    private function get_cantidad_uso($informe_id, $tipo)
    {
        $descarga = DB::table('informe_proyecto')
            ->where('informe_proyecto.Informe_ID', $informe_id)
            ->first();

        return $render_descarga = "
            <a href='" . route('pdf.goal', ['id' => $descarga->Informe_ID]) . "' class='load_descargar'>
                <div class='icon-badge-container mr-1' >
                    <i class='fas fa-file-download ms-text-success ' title='Download'></i>
                    <div class='icon-badge'>$descarga->descargas</div>
                </div>
            </a>";
    }
    private function get_cantidad_uso_email($visit_report, $tipo)
    {
        $email = DB::table('informe_proyecto')
            ->where('informe_proyecto.Informe_ID', $visit_report->informe_id)
            ->first();
        return $render_descarga = "
                <a href='#'>
                    <div class='icon-badge-container mr-1 cursor-pointer send-mail' data-id='$visit_report->informe_id' data-proyecto='$visit_report->Pro_ID' data-nombre='$visit_report->nombre_proyecto' title='Send Mail'>
                        <i class='fas fa-envelope ms-text-secondary cursor-pointer send-mail' data-id='$visit_report->informe_id' data-proyecto='$visit_report->Pro_ID' title='Send Mail'></i>
                        <div class='icon-badge'>$email->email_send</div>
                    </div>
                </a>";
    }
    /**
     * descargar pdf goal solo parte de general contractor
     */
    public function pwt($id, $view = false)
    {
        $report_goal = DB::table('informe_proyecto')
            ->selectRaw("informe_proyecto.*,
        proyectos.Nombre as nombre_proyecto,
        proyectos.Pro_ID,
        CONCAT(COALESCE(proyectos.Nombre,''), ' ',  COALESCE(proyectos.Ciudad,''), ' ',  COALESCE(proyectos.Calle,''), ' ',  COALESCE(proyectos.Numero,'')) as dirrecion,
        CONCAT(COALESCE(personal.Nombre,''), ' ',  COALESCE(personal.Apellido_Paterno,''), ' ',  COALESCE(personal.Apellido_Materno,'')) as nombre_empleado,
        empresas.Nombre as nombre_empresa,
        empresas.Codigo")
            ->where('Informe_ID', $id)
            ->join('proyectos', 'proyectos.Pro_ID', 'informe_proyecto.Pro_ID')
            ->join('empresas', 'empresas.Emp_ID', 'proyectos.Emp_ID')
            ->join('personal', 'personal.Empleado_ID', 'informe_proyecto.Empleado_ID')
            ->first();
        $images = DB::table('goal_imagen')
            ->select('goal_imagen.*')
            ->where('id_informe_proyecto', $id)
            ->get()->toArray();
        $pdf = PDF::loadView('panel.goal.pwt', ['goal' => $report_goal, 'images' => $images])->setPaper('letter')->setWarnings(false);
        if ($view === true) {
            return $pdf;
        }
        return $pdf->download("visit report $report_goal->codigo.pdf");
    }
    /**
     * obtener razon goal
     */
    public function option(Request $request)
    {
        $rules = array(
            'new_question_tipo' => 'required',
            'new_question_description' => 'required',
        );
        $messages = [
            'new_question_tipo.required' => "The type field is required",
            'new_question_description.required' => "The description field is required",
        ];
        $error = Validator::make($request->all(), $rules);
        if ($error->fails()) {
            return response()->json(['errors' => $error->errors()->all()]);
        }
        switch ($request->new_question_tipo) {
            case 'problem':
                $razongoal = DB::table('goal_problem')->insert(
                    array(
                        'descripcion' => $request->new_question_description,
                        'estado' => 1,
                    )
                );
                break;
            case 'consequence':
                $consecuencias = array(
                    'select2_problem' => 'required',
                );
                $messages = [
                    'select2_problem.required' => "The problem field is required",
                ];
                $error = Validator::make($request->all(), $consecuencias, $messages);
                if ($error->fails()) {
                    return response()->json(['errors' => $error->errors()->all()]);
                }
                $razongoal = DB::table('goal_consecuencia')->insert(
                    array(
                        'descripcion' => $request->new_question_description,
                        'goal_problem_id' => $request->select2_problem,
                        'estado' => 1,
                    )
                );
                break;
            case 'solution':
                $solucion = array(
                    'select2_consequences' => 'required',
                );
                $messages = [
                    'select2_consequences.required' => "The consequences field is required",
                ];
                $error = Validator::make($request->all(), $solucion, $messages);
                if ($error->fails()) {
                    return response()->json(['errors' => $error->errors()->all()]);
                }
                $razongoal = DB::table('goal_solucion')->insert(
                    array(
                        'descripcion' => $request->new_question_description,
                        'goal_consecuencia_id' => $request->select2_consequences,
                        'estado' => 1,
                    )
                );
                break;

            default:
                # code...
                break;
        }

        return response()->json(['success' => 'Registered Successfully.']);
    }

    public function sendmailgoal(Request $request, $id, $part = "all")
    {
        $rules = array(
            'to' => 'required|emails',
            'cc' => 'nullable|emails',
            'title_m' => 'required',
            'body_m' => 'required',
        );

        $error = Validator::make($request->all(), $rules);

        if ($error->fails()) {
            return response()->json(['errors' => $error->errors()->all()]);
        }
        if ($part == "all") {
            $pdf = $this->pdf($id, true);
        } else {
            $pdf = $this->pwt($id, true);
        }
        $data = [];

        $to = explode(', ', $request->to);
        $cc = explode(', ', $request->cc);
        $goal = DB::table('informe_proyecto')
            ->select(
                'informe_proyecto.*',
                'proyectos.Nombre',
                'proyectos.Codigo as codigo_proyecto'
            )
            ->where('informe_proyecto.Informe_ID', $id)
            ->join('proyectos', 'proyectos.Pro_ID', 'informe_proyecto.Pro_ID')
            ->first();
        $goal->subempresa = substr(str_replace(' ', '', strtoupper($goal->Nombre)), 0, 5);
        if ($request->to || $request->cc) {
            Mail::send([], $data, function ($message) use ($data, $pdf, $goal, $request, $to, $cc) {
                if ($request->to) {
                    $message->to($to);
                }
                if ($request->cc) {
                    $message->cc($cc);
                }
                $message->subject($request->title_m);
                $message->attachData($pdf->output(), "$goal->subempresa-VisitReport$goal->Codigo.pdf", [
                    'mime' => 'application/pdf',
                ]);
                $message->setBody($request->body_m);
            });
            // check for failures
            if (Mail::failures()) {
                return response()->json(['errors' => ['An error occurred while sending the email, please try again']]);
            }
            $this->añadir_cantidad_uso($goal->Informe_ID, 'email');
            // otherwise everything is okay ...
            return response()->json([
                'success' => 'Success in sending the mail',
            ]);
        }
        return response()->json(['errors' => ['Error sending mail']]);
    }
    public function get_proyects(Request $request)
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
    /*  lista de preguntas y repuestas */
    public function list_preguntas()
    {
        return view('panel.goal.lista_preguntas.list');
    }
    public function datatable_preguntas()
    {
        $data = DB::table('goal_problem')->select(
            'goal_problem.id as goal_problem_id',
            'goal_problem.descripcion as descripcion_problema',
            'goal_consecuencia.id as goal_consecuencia_id',
            'goal_consecuencia.descripcion as descripcion_consecuencia',
            'goal_solucion.id as goal_solucion_id',
            'goal_solucion.descripcion as descripcion_solucion'
        )
        //->where('goal_solucion.estado', 1)
            ->leftJoin('goal_consecuencia', 'goal_consecuencia.goal_problem_id', '=', 'goal_problem.id')

        /* ->where('goal_problem.estado', 1)
        ->where('goal_consecuencia.estado', 1) */
            ->leftJoin('goal_solucion', 'goal_solucion.goal_consecuencia_id', 'goal_consecuencia.id')
            ->get();

        return Datatables::of($data)
            ->addIndexColumn()
            ->addColumn('acciones_problema', function ($data) {
                $button = "
                <i class='fas fa-pencil-alt ms-text-warning edit cursor-pointer' data-tipo='problema' data-id='$data->goal_problem_id' title='Edit problem'></i>
                <i class='far fa-trash-alt ms-text-danger delete cursor-pointer' data-tipo='problema' data-id='$data->goal_problem_id' title='Delete problem'></i>
                ";
                return $button;
            })
            ->addColumn('acciones_consecuencia', function ($data) {
                $button = "
                <i class='far fa-file ms-text-primary create cursor-pointer' data-tipo='consecuencia' data-id='$data->goal_consecuencia_id' data-id_problem='$data->goal_problem_id' title='Create consequence'></i>
                <i class='fas fa-pencil-alt ms-text-warning edit cursor-pointer' data-tipo='consecuencia' data-id='$data->goal_consecuencia_id' title='Edit consequence'></i>
                <i class='far fa-trash-alt ms-text-danger delete cursor-pointer' data-tipo='consecuencia' data-id='$data->goal_consecuencia_id' title='Delete consequence'></i>
                ";
                return $button;
            })
            ->addColumn('acciones_solucion', function ($data) {
                $button = "
                <i class='far fa-file ms-text-primary create cursor-pointer' data-tipo='solucion' data-id='$data->goal_solucion_id' data-id_consequence='$data->goal_consecuencia_id' title='Create solution'></i>
                <i class='fas fa-pencil-alt ms-text-warning edit cursor-pointer' data-tipo='solucion' data-id='$data->goal_solucion_id' title='Edit solution'></i>
                <i class='far fa-trash-alt ms-text-danger delete cursor-pointer' data-tipo='solucion' data-id='$data->goal_solucion_id' title='Delete solution'></i>
                ";
                return $button;
            })
            ->rawColumns(['acciones_problema', 'acciones_consecuencia', 'acciones_solucion'])
            ->make(true);
    }

    public function store_option(Request $request)
    {
        $rules = array(
            'descripcion' => 'required',
        );
        $messages = [
            'descripcion.required' => "The description field is required",
        ];
        $error = Validator::make($request->all(), $rules, $messages);

        if ($error->errors()->all()) {
            return response()->json([
                'status' => 'errors',
                'message' => $error->errors()->all(),
            ]);
        }
        /* validador */
        switch ($request->tipo) {
            case 'problem':
                $nuevo_problem = DB::table('goal_problem')
                    ->insert([
                        'descripcion' => $request->descripcion,
                        'estado' => 1,
                    ]);
                break;
            case 'consecuencia':
                $rules = array(
                    'id' => 'required',
                );
                $messages = [
                    'id.required' => "record consequence",
                ];
                $error = Validator::make($request->all(), $rules, $messages);
                if ($error->errors()->all()) {
                    return response()->json([
                        'status' => 'errors',
                        'message' => $error->errors()->all(),
                    ]);
                }
                $nuevo_cosecuencia = DB::table('goal_consecuencia')
                    ->insert([
                        'descripcion' => $request->descripcion,
                        'estado' => 1,
                        'goal_problem_id' => $request->id,
                    ]);
                break;
            case 'solucion':
                $rules = array(
                    'id' => 'required',
                );
                $messages = [
                    'id.required' => "record consequence",
                ];
                $error = Validator::make($request->all(), $rules, $messages);
                if ($error->errors()->all()) {
                    return response()->json([
                        'status' => 'errors',
                        'message' => $error->errors()->all(),
                    ]);
                }
                $nuevo_solucion = DB::table('goal_solucion')
                    ->insert([
                        'descripcion' => $request->descripcion,
                        'estado' => 1,
                        'goal_consecuencia_id' => $request->id,
                    ]);
                break;
            default:
                # code...
                break;
        }
        return response()->json([
            "status" => "ok",
            "message" => 'Registered Successfully',
        ], 200);
    }

    public function edit_option(Request $request, $id)
    {
        switch ($request->tipo) {
            case 'problema':
                $nuevo_problem = DB::table('goal_problem')
                    ->where('goal_problem.id', $id)
                    ->first();
                return response()->json($nuevo_problem, 200);
                break;
            case 'consecuencia':
                $nuevo_cosecuencia = DB::table('goal_consecuencia')
                    ->where('goal_consecuencia.id', $id)
                    ->first();
                return response()->json($nuevo_cosecuencia, 200);
                break;
            case 'solucion':
                $nuevo_solucion = DB::table('goal_solucion')
                    ->where('goal_solucion.id', $id)
                    ->first();
                return response()->json($nuevo_solucion, 200);
                break;
            default:
                # code...
                break;
        }
    }
    public function update_option(Request $request, $id)
    {
        $rules = array(
            'descripcion' => 'required',
        );
        $messages = [
            'descripcion.required' => "The description field is required",
        ];
        $error = Validator::make($request->all(), $rules, $messages);

        if ($error->errors()->all()) {
            return response()->json([
                'status' => 'errors',
                'message' => $error->errors()->all(),
            ]);
        }
        switch ($request->tipo) {
            case 'problema':
                $nuevo_problem = DB::table('goal_problem')
                    ->where('goal_problem.id', $id)
                    ->update([
                        'descripcion' => $request->descripcion,
                    ]);
                break;
            case 'consecuencia':
                $nuevo_cosecuencia = DB::table('goal_consecuencia')
                    ->where('goal_consecuencia.id', $id)
                    ->update([
                        'descripcion' => $request->descripcion,
                    ]);
                break;
            case 'solucion':
                $nuevo_solucion = DB::table('goal_solucion')
                    ->where('goal_solucion.id', $id)
                    ->update([
                        'descripcion' => $request->descripcion,
                    ]);
                break;
            default:
                # code...
                break;
        }
        return response()->json([
            "status" => "ok",
            "message" => 'successfully modified',
        ], 200);
    }
    public function delete_option(Request $request, $id)
    {
        switch ($request->tipo) {
            case 'problema':
                $nuevo_problem = DB::table('goal_problem')
                    ->where('goal_problem.id', $id)
                    ->delete();
                break;
            case 'consecuencia':
                $nuevo_cosecuencia = DB::table('goal_consecuencia')
                    ->where('goal_consecuencia.id', $id)
                    ->delete();
                break;
            case 'solucion':
                $nuevo_solucion = DB::table('goal_solucion')
                    ->where('goal_solucion.id', $id)
                    ->delete();

                break;
            default:
                # code...
                break;
        }
        return response()->json([
            "status" => "ok",
            "message" => 'successfully delete',
        ], 200);
    }
    public function select_task_by_proyecto(Request $request, $id)
    {
        if (!isset($request->searchTerm)) {
            $proyectos = DB::table('task')
                ->select(
                    'task.*'
                )
                ->where('task.Nombre', 'like', '%super%') //super
                ->where('task.Pro_ID', $id)
                ->get();
        } else {
            $proyectos = DB::table('task')
                ->select(
                    'task.*'
                )
                ->where('task.Nombre', 'like', '%super%') //super
                ->where('task.Nombre', 'like', '%' . $request->searchTerm . '%')
                ->where('task.Pro_ID', $id)
                ->get();
        }
        $data = [];
        foreach ($proyectos as $row) {
            $data[] = array(
                "id" => $row->Task_ID,
                "text" => $row->Nombre,
                "ActTas" => $row->ActTas,
            );
        }
        return response()->json($data);
    }
    public function tarea_verificar($id)
    {
        $tarea = DB::table('task')
            ->select(
                'task.*'
            )
            ->where('task.Nombre', 'like', '%super%') //super
            ->where('task.Pro_ID', $id)
            ->first();
        $tareas_detectadas = $this->getActividad($id, '');
        return response()->json([
            "status" => "ok",
            "message" => 'verificado',
            "data" => [
                'tarea' => $tarea,
                'actividades' => $tareas_detectadas,
            ],
        ], 200);
    }
}
