<?php

namespace App\Http\Controllers;

use DataTables;
use DB;
use File;
use Illuminate\Http\Request;
use Image;
use Mail;
use PDF;
use Validator;

class DailyReportDetailController extends Controller
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
    public function dataTableProject($id)
    {
        $registro_diario = $this->resumen_reporte_diario($id);
        return Datatables::of($registro_diario)
            ->addIndexColumn()
            ->make(true);
    }
    public function dataTable($id)
    {
        $actividades = DB::table('actividades')
            ->select(
                'actividades.*',
                'report_daily_detalle.*',
                DB::raw('DATE_FORMAT(actividades.Fecha , "%m/%d/%Y") as Fecha'),
                'tipo_actividad.*',
                'personal.Usuario'
            )
            ->join('tipo_actividad', 'tipo_actividad.Tipo_Actividad_ID', 'actividades.Tipo_Actividad_ID')
            ->leftJoin('report_daily_detalle', 'report_daily_detalle.actividad_id', 'actividades.Actividad_ID')
            ->leftjoin('personal', 'personal.Empleado_ID', 'report_daily_detalle.empleado_id')
            ->when((request()->from_date && request()->to_date), function ($query) {
                return $query->whereBetween('actividades.Fecha', [date('Y-m-d', strtotime(request()->from_date)), date('Y-m-d', strtotime(request()->to_date))]);
            })
            ->where('actividades.Pro_ID', $id)
            ->orderBy('actividades.Fecha', 'DESC')
            ->get();
        return Datatables::of($actividades)
            ->addIndexColumn()
            ->addColumn('status', function ($data) {
                if ($data->estado != null) {
                    if ($data->estado == 'completed') {
                        $button = "
                            <span class='badge badge-success'>$data->estado</span>
                        ";
                        return $button;
                    } else {
                        $button = "
                            <span class='badge badge-danger'>$data->estado</span>
                            ";
                        return $button;
                    }

                } else {
                    $button = "";
                }
            })
            ->addColumn('acciones', function ($data) {
                $button = "
                    <i data-id='$data->Actividad_ID' class='fas fa-file-download ms-text-success cursor-pointer m-0' id='open_modal_view' data-admin='" . route('daily_report_detail.pdf_admin', ['id' => $data->Actividad_ID]) . "' data-cliente='" . route('daily_report_detail.pdf_cliente', ['id' => $data->Actividad_ID]) . "' title='Download' ></i>
                    <a data-id='$data->Actividad_ID' href='#' id='open_modal_view' data-admin='" . route('daily_report_detail.show_admin') . "' data-cliente='" . route('daily_report_detail.show_cliente') . "'><i class='fas fa-eye ms-text-primary m-0' title='View daily report' ></i></a>
                    <a  href='" . route('daily_report_detail.edit', ['id' => $data->Actividad_ID]) . "'><i class='fas fa-pencil-alt ms-text-warning m-0'></i></a>
                    ";
                return $button;
            })
            ->rawColumns(['acciones', 'status'])
            ->make(true);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {
        $proyecto = DB::table('proyectos')
            ->where('Pro_ID', $id)
            ->first();
        return view('panel.daily_report_detail.list', compact('proyecto'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id)
    {
        $proyecto = DB::table('proyectos')->select(
            'empresas.Codigo as empresa',
            'proyectos.*',
            'actividades.*',
            DB::raw("DATE_FORMAT(actividades.Fecha , '%W %d, %M %Y' ) as actividad_fecha"),
        )
            ->where('actividades.Actividad_ID', $id)
            ->join('empresas', 'empresas.Emp_ID', 'proyectos.Emp_ID')
            ->join('actividades', 'actividades.Pro_ID', 'proyectos.Pro_ID')
            ->first();
        $address = trim("$proyecto->Ciudad, $proyecto->Zip_Code, $proyecto->Calle");
        $foreman = DB::table('personal')->where('Empleado_ID', $proyecto->Foreman_ID)->first();
        $foreman_name = (empty($foreman)) ? "" : trim($foreman->Nombre . $foreman->Apellido_Paterno . $foreman->Apellido_Materno);

        $options_id = DB::table('report_daily_project')
            ->where('report_daily_project.Pro_ID', $proyecto->Pro_ID)
            ->get()
            ->pluck('opcion_id');

        //test extraccion de informacion
        $report_daily = DB::table('report_daily_project')
            ->select('report_daily.*')
            ->where('report_daily_project.Pro_ID', $proyecto->Pro_ID)
            ->join('report_daily_opcion', 'report_daily_opcion.id', 'report_daily_project.report_daily_opcion_id')
            ->join('report_daily', 'report_daily.id', 'report_daily_opcion.report_daily_id')
            ->groupBy('report_daily.id')
            ->get();

        //create detalle iniclal
        $daily_report_detail = $this->validate_exist_daily_report($proyecto->Pro_ID, $id);
        $campos_images = explode(',', $daily_report_detail->question);
        array_pop($campos_images);
        //dd($daily_report_detail,explode(',',$daily_report_detail->question));
        return view('panel.daily_report_detail.create', compact('proyecto', 'address', 'foreman', 'foreman_name', 'report_daily', 'campos_images', 'daily_report_detail'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $id)
    {
        $update = DB::table('report_daily_detalle')
            ->where('report_daily_detalle.actividad_id', $id)
            ->update([
                "actividad_id" => $id,
                "detalle" => $request->detalle,
                "fecha" => date('Y-m-d'),
                "empleado_id" => auth()->user()->Empleado_ID,
                "estado" => "completed",
            ]);
        return response()->json([
            'status' => 'success',
            'data' => null,
            'message' => 'Register Successfully',
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show_admin(Request $request)
    {
        $id = $request->query('view');
        $proyecto = DB::table('proyectos')
            ->select(
                'empresas.Codigo as empresa',
                'proyectos.*',
                'actividades.*',
                DB::raw("DATE_FORMAT(actividades.Fecha , '%W %d, %M %Y' ) as actividad_fecha"),
                DB::raw("DATE_FORMAT(actividades.Fecha , '%d/%m/%Y' ) as fecha"),
            )

            ->where('actividades.Actividad_ID', $id)
            ->join('empresas', 'empresas.Emp_ID', 'proyectos.Emp_ID')
            ->join('actividades', 'actividades.Pro_ID', 'proyectos.Pro_ID')
            ->first();
        $address = trim("$proyecto->Ciudad, $proyecto->Zip_Code, $proyecto->Calle");
        $foreman = DB::table('personal')->where('Empleado_ID', $proyecto->Foreman_ID)->first();
        $foreman_name = (empty($foreman)) ? "" : trim($foreman->Nombre . $foreman->Apellido_Paterno . $foreman->Apellido_Materno);

        $resumen = $this->resumen_reporte_diario($id);
        //create detalle iniclal
        $daily_report_detail = $this->validate_exist_daily_report($proyecto->Pro_ID, $id);

        $img = DB::table('report_daily_detalle_image')->where('report_daily_detalle_image.report_daily_detalle_id', $daily_report_detail->id)->get()->toArray();
        return view('panel.daily_report_detail.view', compact('resumen', 'proyecto', 'foreman_name', 'daily_report_detail', 'address', 'img'));
    }
    public function show_cliente(Request $request)
    {
        $id = $request->query('view');
        $proyecto = DB::table('proyectos')
            ->select(
                'empresas.Codigo as empresa',
                'proyectos.*',
                'actividades.*',
                DB::raw("DATE_FORMAT(actividades.Fecha , '%W %d, %M %Y' ) as actividad_fecha"),
                DB::raw("DATE_FORMAT(actividades.Fecha , '%d/%m/%Y' ) as fecha"),
            )

            ->where('actividades.Actividad_ID', $id)
            ->join('empresas', 'empresas.Emp_ID', 'proyectos.Emp_ID')
            ->join('actividades', 'actividades.Pro_ID', 'proyectos.Pro_ID')
            ->first();
        $address = trim("$proyecto->Ciudad, $proyecto->Zip_Code, $proyecto->Calle");
        $foreman = DB::table('personal')->where('Empleado_ID', $proyecto->Foreman_ID)->first();
        $foreman_name = (empty($foreman)) ? "" : trim($foreman->Nombre . $foreman->Apellido_Paterno . $foreman->Apellido_Materno);

        $resumen = $this->resumen_reporte_diario($id);
        //create detalle iniclal
        $daily_report_detail = $this->validate_exist_daily_report($proyecto->Pro_ID, $id);

        $img = DB::table('report_daily_detalle_image')->where('report_daily_detalle_image.report_daily_detalle_id', $daily_report_detail->id)->get()->toArray();
        return view('panel.daily_report_detail.view_cliente', compact('resumen', 'proyecto', 'foreman_name', 'daily_report_detail', 'address', 'img'));
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $proyecto = DB::table('proyectos')->select(
            'empresas.Codigo as empresa',
            'proyectos.*',
            'actividades.*',
            DB::raw("DATE_FORMAT(actividades.Fecha , '%W %d, %M %Y' ) as actividad_fecha"),
        )
            ->where('actividades.Actividad_ID', $id)
            ->join('empresas', 'empresas.Emp_ID', 'proyectos.Emp_ID')
            ->join('actividades', 'actividades.Pro_ID', 'proyectos.Pro_ID')
            ->first();
        $address = trim("$proyecto->Ciudad, $proyecto->Zip_Code, $proyecto->Calle");
        $foreman = DB::table('personal')->where('Empleado_ID', $proyecto->Foreman_ID)->first();
        $foreman_name = (empty($foreman)) ? "" : trim($foreman->Nombre . $foreman->Apellido_Paterno . $foreman->Apellido_Materno);

        //create detalle iniclal
        $daily_report_detail = $this->validate_exist_daily_report($proyecto->Pro_ID, $id);
        $campos_images = explode(',', $daily_report_detail->question);
        array_pop($campos_images);
        return view('panel.daily_report_detail.edit', compact('proyecto', 'address', 'foreman', 'foreman_name', 'campos_images', 'daily_report_detail'));
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
        dd($request->all());
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
    public function pdf_admin($id, $view = false)
    {
        $proyecto = DB::table('proyectos')
            ->select(
                'empresas.Codigo as empresa',
                'proyectos.*',
                'actividades.*',
                DB::raw("DATE_FORMAT(actividades.Fecha , '%W %d, %M %Y' ) as actividad_fecha"),
            )

            ->where('actividades.Actividad_ID', $id)
            ->join('empresas', 'empresas.Emp_ID', 'proyectos.Emp_ID')
            ->join('actividades', 'actividades.Pro_ID', 'proyectos.Pro_ID')
            ->first();
        $address = trim("$proyecto->Ciudad, $proyecto->Zip_Code, $proyecto->Calle");
        $foreman = DB::table('personal')->where('Empleado_ID', $proyecto->Foreman_ID)->first();
        $foreman_name = (empty($foreman)) ? "" : trim($foreman->Nombre . $foreman->Apellido_Paterno . $foreman->Apellido_Materno);

        $resumen = $this->resumen_reporte_diario($id);
        //create detalle iniclal
        $daily_report_detail = $this->validate_exist_daily_report($proyecto->Pro_ID, $id);

        $img = DB::table('report_daily_detalle_image')->where('report_daily_detalle_image.report_daily_detalle_id', $daily_report_detail->id)->get()->toArray();
        $pdf = PDF::loadView('panel.daily_report_detail.pdf_admin', compact('resumen', 'proyecto', 'foreman_name', 'daily_report_detail', 'address', 'img'))->setPaper('letter')->setWarnings(false);
        if ($view === true) {
            return $pdf;
        }
        return $pdf->download("Daily Report Admin $proyecto->actividad_fecha.pdf");
    }
    public function pdf_cliente($id, $view = false)
    {
        $proyecto = DB::table('proyectos')
            ->select(
                'empresas.Codigo as empresa',
                'proyectos.*',
                'actividades.*',
                DB::raw("DATE_FORMAT(actividades.Fecha , '%W %d, %M %Y' ) as actividad_fecha"),
            )

            ->where('actividades.Actividad_ID', $id)
            ->join('empresas', 'empresas.Emp_ID', 'proyectos.Emp_ID')
            ->join('actividades', 'actividades.Pro_ID', 'proyectos.Pro_ID')
            ->first();
        $address = trim("$proyecto->Ciudad, $proyecto->Zip_Code, $proyecto->Calle");
        $foreman = DB::table('personal')->where('Empleado_ID', $proyecto->Foreman_ID)->first();
        $foreman_name = (empty($foreman)) ? "" : trim($foreman->Nombre . $foreman->Apellido_Paterno . $foreman->Apellido_Materno);

        $resumen = $this->resumen_reporte_diario($id);
        //create detalle iniclal
        $daily_report_detail = $this->validate_exist_daily_report($proyecto->Pro_ID, $id);

        $img = DB::table('report_daily_detalle_image')->where('report_daily_detalle_image.report_daily_detalle_id', $daily_report_detail->id)->get()->toArray();
        $pdf = PDF::loadView('panel.daily_report_detail.pdf_cliente', compact('resumen', 'proyecto', 'foreman_name', 'daily_report_detail', 'address', 'img'))->setPaper('letter')->setWarnings(false);
        if ($view === true) {
            return $pdf;
        }
        return $pdf->download("Daily Report Client $proyecto->actividad_fecha.pdf");
    }
    public function upload_image($id, Request $request)
    {
        $campo = 'images';

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
                    $name_img = "image-$id-" . uniqid() . time() . "." . $extension;
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
                    $insert = DB::table('report_daily_detalle_image')->insertGetId([
                        'imagen' => $name_img,
                        'report_daily_detalle_id' => $id,
                        'caption' => $filename,
                        'size' => $fileSize,
                        'referencia' => $request->referencia,
                        'estado' => 'registrado',
                    ]);
                    if ($insert) {
                        $newFileUrl = url('/') . '/uploads/' . $name_img;
                        $preview[] = $newFileUrl;
                        $config[] = [
                            'key' => $insert,
                            'caption' => $filename,
                            'size' => $fileSize,
                            'downloadUrl' => $newFileUrl, // the url to download the file
                            'url' => url("daily-report-detail/image/$insert/delete"), // server api to delete the file based on key
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
    public function delete_image($id, Request $request)
    {
        $query = DB::table('report_daily_detalle_image')
            ->where('id', $request->key);
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

    public function get_images($id)
    {
        $images = DB::table('report_daily_detalle_image')
            ->where('report_daily_detalle_id', $id)
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
                    'url' => url("/daily-report-detail/image/$id/delete"),
                    'key' => $val->id,
                    'referencia' => $val->referencia,
                ];
            }
        }
        return response()->json($list);
    }
    ///funciones
    public function resumen_reporte_diario($actividad_id)
    {
        $resumen = DB::table('registro_diario')
            ->select(
                'registro_diario.*',
                'proyectos.Codigo',
                'proyectos.Nombre',
                'task.Nombre as nombre_tarea',
                'task.Task_ID',
                'task.Horas_Estimadas as Horas_Estimadas',
                'task.Last_Per_Recorded as Last_Per_Recorded',
                'area_control.nombre as nombre_area',
                DB::raw('SUM(registro_diario_actividad.Horas_Contract) as Horas_Contract_total'),
                'registro_diario_actividad.Horas_Contract',
                'registro_diario_actividad.RDA_ID',
                DB::raw('count(registro_diario.Empleado_ID) as cantidad_personas'),
            )
            ->join('proyectos', 'proyectos.Pro_ID', 'registro_diario.Pro_ID')
            ->join('registro_diario_actividad', 'registro_diario_actividad.Reg_ID', 'registro_diario.Reg_ID')
            ->join('task', 'task.Task_ID', 'registro_diario_actividad.Task_ID')
            ->join('area_control', 'area_control.Area_ID', 'task.Area_ID')
            ->where('registro_diario.Actividad_Id', $actividad_id)
            ->orderBy('area_control.nombre', 'ASC')
            ->groupBy('registro_diario_actividad.Task_ID')
            ->get();
        $total_Horas_Contract = 0;
        foreach ($resumen as $key => $registro_diario_actividad) {
            //armando informacion requerida
            $total_used = DB::table('registro_diario_actividad')
                ->select(
                    DB::raw('SUM(registro_diario_actividad.Horas_Contract) as Horas_Contract'),
                )
                ->where('registro_diario_actividad.Task_ID', $registro_diario_actividad->Task_ID)
                ->first();
            //obteniedo porcentaje requerida
            $porcentaje_completado = DB::table('percentage_complete')
                ->where('percentage_complete.Date_Recorded', $registro_diario_actividad->Fecha)
                ->where('percentage_complete.Pro_ID', $registro_diario_actividad->Pro_ID)
                ->where('percentage_complete.Task_ID', $registro_diario_actividad->Task_ID)
                ->first();
            //totales
            $registro_diario_actividad->total_used = $total_used->Horas_Contract;

            $registro_diario_actividad->porcentaje = $porcentaje_completado == null ? '' : $porcentaje_completado->Per_Recorded . "%";
            $registro_diario_actividad->note = $porcentaje_completado == null ? '' : $porcentaje_completado->Note;

            $total_Horas_Contract += $registro_diario_actividad->Horas_Contract_total;
            $registro_diario_actividad->total_Horas_Contract = $total_Horas_Contract;
        }
        return $resumen;
    }
    public function validate_exist_daily_report($proyecto_id, $actividad_id)
    {
        $report_daily = DB::table('report_daily_project')
            ->select('report_daily.*')
            ->where('report_daily_project.Pro_ID', $proyecto_id)
            ->join('report_daily_opcion', 'report_daily_opcion.id', 'report_daily_project.report_daily_opcion_id')
            ->join('report_daily', 'report_daily.id', 'report_daily_opcion.report_daily_id')
            ->groupBy('report_daily.id')
            ->get();
        $cadena = "";
        $question = "";
        if (count($report_daily) <= 0) {
            $option_default = DB::table('report_daily_opcion')->where('report_daily_opcion.report_daily_id', 1)->get();
            foreach ($option_default as $key => $default) {
                $insert = DB::table('report_daily_project')->insertGetId([
                    'Pro_ID' => $proyecto_id,
                    'report_daily_opcion_id' => $default->id,
                    'used' => 'yes',
                ]);
            }

            $report_daily = DB::table('report_daily_project')
                ->select('report_daily.*')
                ->where('report_daily_project.Pro_ID', $proyecto_id)
                ->join('report_daily_opcion', 'report_daily_opcion.id', 'report_daily_project.report_daily_opcion_id')
                ->join('report_daily', 'report_daily.id', 'report_daily_opcion.report_daily_id')
                ->groupBy('report_daily.id')
                ->get();
        }

        foreach ($report_daily as $key => $report) {
            $opciones = DB::table('report_daily_opcion')
                ->join('report_daily_project', 'report_daily_project.report_daily_opcion_id', 'report_daily_opcion.id')
                ->where('report_daily_project.Pro_ID', $proyecto_id)
                ->get();
            $cadena .= "$report->nombre \n";
            //dd($opciones,$report->id);
            foreach ($opciones as $i => $opcion) {
                $valores = DB::table('report_daily_valor')->where('report_daily_opcion_id', $opcion->id)->get();
                if (count($valores) > 0) {
                    $cadena .= "      $opcion->opcion \n";
                } else {
                    $cadena .= "      $opcion->opcion: \n"; //finaliza
                }
                $question .= "$opcion->opcion,";
                foreach ($valores as $key => $valor) {
                    $cadena .= "              $valor->valor :\n";
                }
                $opcion->valores = $valores;
            }
            $report->opciones = $opciones;

        }

        $resultado = DB::table('report_daily_detalle')
            ->leftJoin('personal', 'report_daily_detalle.empleado_id', 'personal.Emp_ID')
            ->where('report_daily_detalle.actividad_id', $actividad_id)
            ->first();
        if ($resultado == null) {
            //crea si no existe
            $insert = DB::table('report_daily_detalle')
                ->insertGetId([
                    "actividad_id" => $actividad_id,
                    "fecha" => date('Y-m-d'),
                    "detalle" => $cadena,
                    "question" => $question,
                    "empleado_id" => 0,
                    "estado" => "pending",
                ]);
            $resultado = DB::table('report_daily_detalle')
                ->leftJoin('personal', 'report_daily_detalle.empleado_id', 'personal.Emp_ID')
                ->where('id', $insert)
                ->first();
            return $resultado;

        } else {
            //modificar si esta en pendiente
            if ($resultado->estado == 'pending') {
                $update = DB::table('report_daily_detalle')
                    ->where('report_daily_detalle.id', $resultado->id)
                    ->update([
                        "actividad_id" => $actividad_id,
                        "fecha" => date('Y-m-d'),
                        "detalle" => $cadena,
                        "question" => $question,
                        "empleado_id" => 0,
                        "estado" => "pending",
                    ]);
                $resultado = DB::table('report_daily_detalle')
                    ->leftJoin('personal', 'report_daily_detalle.empleado_id', 'personal.Emp_ID')
                    ->where('id', $resultado->id)
                    ->first();
                return $resultado;
            } else {
                return $resultado;
            }

        }

    }

    //email
    public function sendmail(Request $request, $id, $tipo)
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
        if ($tipo == 'admin') {
            $pdf = $this->pdf_admin($id, true);
        } else {
            $pdf = $this->pdf_cliente($id, true);
        }

        $data = [];

        $to = explode(', ', $request->to);
        $cc = explode(', ', $request->cc);

        $report_daily_detalle = DB::table('report_daily_detalle')->select(
            'report_daily_detalle.*',
            'actividades.Fecha as fecha_actividad',
            'proyectos.*',
            'empresas.Nombre as empresa'
        )->where('report_daily_detalle.actividad_id', $id)
            ->join('actividades', 'actividades.Actividad_ID', 'report_daily_detalle.actividad_id')
            ->join('proyectos', 'actividades.Pro_ID', 'proyectos.Pro_ID')
            ->join('empresas', 'proyectos.Emp_ID', 'empresas.Emp_ID')
            ->first();

        $report_daily_detalle->subempresa = substr(str_replace(' ', ' ', strtoupper($report_daily_detalle->Nombre)), 0, 15);
        if ($request->to || $request->cc) {
            Mail::send([], $data, function ($message) use ($data, $pdf, $id, $request, $to, $cc, $report_daily_detalle) {
                if ($request->to) {
                    $message->to($to);
                }
                if ($request->cc) {
                    $message->cc($cc);
                }
                $message->subject($request->title_m);
                $message->attachData($pdf->output(), "$report_daily_detalle->subempresa-Daily report " . $report_daily_detalle->fecha_actividad . ".pdf", [
                    'mime' => 'application/pdf',
                ]);
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
