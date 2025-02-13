<?php

namespace App\Http\Controllers\Api;

use App\ContactoProyecto;
use App\Http\Controllers\Controller;
use DB;
use Illuminate\Http\Request;
use Image;
use Mail;
use PDF;
use Validator;

class VisitReportController extends Controller
{
    public function list_visit_report(Request $request)
    {
        $list_visit_report = DB::table('informe_proyecto')->select(
            'informe_proyecto.Pro_ID as proyecto_id',
            'informe_proyecto.Codigo as codigo',
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
        //filtrando si hay proyectos
            ->when(!empty(request()->proyecto), function ($q) {
                return $q->where('proyectos.Nombre', 'like', '%' . request()->proyecto . '%');
            })
        //filtrando por codigo visit report
            ->when(!empty(request()->codigo), function ($q) {
                return $q->where('informe_proyecto.Codigo', 'like', '%' . request()->codigo . '%');
            })
        //filtrando por fecha
            ->when(!empty(request()->from_date), function ($q) {
                $from = date('Y-m-d', strtotime(request()->from_date));
                $to = date('Y-m-d', strtotime(request()->to_date));
                return $q->whereBetween('informe_proyecto.fecha', [$from, $to]);
            })
        //filtro de usuario
            ->where('informe_proyecto.Empleado_ID', $request->empleado_id)
        ///
            ->where('informe_proyecto.delete_informe_proyecto', '1')
            ->join('proyectos', 'proyectos.Pro_ID', 'informe_proyecto.Pro_ID')
            ->join('empresas', 'empresas.Emp_ID', 'proyectos.Emp_ID')
            ->join('personal', 'personal.Empleado_ID', 'informe_proyecto.Empleado_ID')
            ->orderBy('informe_proyecto.Informe_ID', 'DESC')
            ->get();
        return response()->json($list_visit_report, 200);
    }

    public function get_proyecto()
    {
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
        return response()->json($proyectos, 200);
    }
    public function get_areas(Request $request)
    {
        $floor = $where = DB::table('area_control')
            ->select('area_control.*')
            ->where('area_control.Pro_ID', $request->proyecto_id)
            ->leftJoin('floor', 'area_control.Floor_ID', 'floor.Floor_ID')
            ->join('edificios', 'floor.Edificio_ID', 'edificios.Edificio_ID')
            ->get()
            ->toArray();
        return response()->json($floor, 200);
    }
    /* preguntas */
    public function get_problema(Request $request)
    {
        $problema = DB::table('goal_problem')
            ->select('id', 'descripcion')
            ->get()
            ->toArray();
        return response()->json($problema, 200);
    }
    public function get_consecuencia(Request $request)
    {
        //return response()->json(!empty($request->problema_id), 200, );
        //dd(!empty($request->problema_id));
        $consecuencias = DB::table('goal_consecuencia')
            ->select('id', 'descripcion')
            ->when(!empty($request->problema_id), function ($query) use ($request) {
                return $query->where('goal_consecuencia.goal_problem_id', $request->problema_id);
            })
            ->get()
            ->toArray();
        return response()->json($consecuencias, 200);
    }
    public function get_solucion(Request $request)
    {
        $solucion = DB::table('goal_solucion')
            ->select('id', 'descripcion')
            ->when(!empty($request->consecuencia_id), function ($query) {
                return $query->where('goal_consecuencia.goal_problem_id', $request->consecuencia_id);
            })
            ->get()
            ->toArray();
        return response()->json($solucion, 200);
    }
    public function save_visit_report(Request $request)
    {
        //dd($request->all());
        $codigo = DB::table('informe_proyecto')
            ->join('personal', 'personal.Empleado_ID', 'informe_proyecto.Empleado_ID')
            ->join('proyectos', 'proyectos.Pro_ID', 'informe_proyecto.Pro_ID')
            ->where('proyectos.Pro_ID', $request->proyecto_id)
            ->where('informe_proyecto.estado', 'creado')
            ->count() + 1;
        $codigo = $this->detector_codigo($codigo);
        $visit_report = DB::table('informe_proyecto')
            ->insertGetId([
                'Pro_ID' => $request->proyecto_id,
                'Codigo' => $codigo,
                'Empleado_ID' => $request->empleado_id,
                'Fecha' => date('Y-m-d', strtotime($request->fecha)),
                'Drywall_comments' => $request->comentarios,
                'delete_informe_proyecto' => 1,
                'estado' => 'creado',
            ]);
        /*  añadir imagenes */
        if ($request->hasFile('images')) {
            $this->saveImagesFile($request, 'images', 'images', $visit_report);
        }
        return response()->json('ok', 200);
    }
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
    private function saveImagesFile(Request $request, $requestTipo, $tipo, $visit_report)
    {
        $files = $request->file($tipo);
        $allowedfileExtension = ['jpg', 'png', 'jpeg'];
        $files = $request->file($requestTipo);
        foreach ($files as $file) {
            $filename = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
            $fileSize = $file->getSize();
            $check = in_array($extension, $allowedfileExtension);
            if ($check) {
                $name_img = "$tipo-image-$visit_report-" . uniqid() . time() . "." . $extension;
                $path = public_path() . '/uploads/' . $name_img;
                if ($fileSize > 1500000) {
                    $actual_image = Image::make(file_get_contents($file));
                    $height = $actual_image->height() / 4;
                    $width = $actual_image->width() / 4;
                    $actual_image->resize($width, $height)->save($path);
                    $fileSize = $actual_image->filesize();
                } else {
                    Image::make(file_get_contents($file))->save($path);
                }
                $insert = DB::table('goal_imagen')
                    ->insert([
                        'imagen' => $name_img,
                        'tipo' => $tipo,
                        'caption' => $filename,
                        'size' => $fileSize,
                        'id_informe_proyecto' => $visit_report,
                    ]);
            }
        }
    }
    public function get_images($id)
    {
        $images = DB::table('goal_imagen')
            ->where('id_informe_proyecto', $id)
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
                    'url' => url("delete_image/$id/ticket"),
                    'key' => $val->t_imagen_id,
                ];
            }
        }
        return response()->json($list);
    }
    public function get_visit_report($id)
    {
        $visit_report = DB::table('informe_proyecto')
            ->select(
                'informe_proyecto.Informe_ID as informe_id',
                'proyectos.Nombre as nombre_proyecto',
                'empresas.Nombre as nombre_empresa',
                'proyectos.Emp_ID as empresa_id',
                'proyectos.Codigo as codigo_proyecto',
                DB::raw("CONCAT(COALESCE(proyectos.Nombre,''), ' ',  COALESCE(proyectos.Ciudad,''), ' ',  COALESCE(proyectos.Calle,''), ' ',  COALESCE(proyectos.Numero,'')) as dirrecion"),
                DB::raw("CONCAT(COALESCE(personal.Nombre,''), ' ',  COALESCE(personal.Apellido_Paterno,''), ' ',  COALESCE(personal.Apellido_Materno,'')) as nombre_empleado"),
                'informe_proyecto.Pro_ID as proyecto_id',
                'informe_proyecto.Codigo as codigo',
                'informe_proyecto.Empleado_ID as empleado_id',
                DB::raw('DATE_FORMAT(informe_proyecto.Fecha , "%m/%d/%Y" ) as fecha'),
                'informe_proyecto.Drywall_comments as comentarios',
                'informe_proyecto.estado as estado',
                'informe_proyecto.delete_informe_proyecto as delete',
                DB::raw('DATE_FORMAT(proyectos.Fecha_Inicio , "%m/%d/%Y" ) as fecha_inicio'),
                DB::raw('DATE_FORMAT(proyectos.Fecha_Fin , "%m/%d/%Y") as fecha_fin'),
                'personal.Usuario as username',
            )
            ->where('informe_proyecto.Informe_ID', $id)
            ->join('proyectos', 'proyectos.Pro_ID', 'informe_proyecto.Pro_ID')
            ->join('empresas', 'empresas.Emp_ID', 'proyectos.Emp_ID')
            ->join('personal', 'personal.Empleado_ID', 'informe_proyecto.Empleado_ID')
            ->first();
        $images = DB::table('goal_imagen')
            ->select(
                't_imagen_id',
                'imagen',
                'tipo',
                'id_informe_proyecto',
                'caption',
                'size'
            )
            ->where('goal_imagen.id_informe_proyecto', $id)
            ->get();
        $visit_report->modo = 'online';
        $visit_report->images = $this->descomponerImage($images);
        return response()->json($visit_report, 200);
    }
    public function update_visit_report(Request $request, $id)
    {
        $visit_report = DB::table('informe_proyecto')
            ->where('informe_proyecto.Informe_ID', $id)
            ->update([
                'Drywall_comments' => $request->comentarios,
            ]);
        if ($request->hasFile('images')) {
            DB::table('goal_imagen')->where('id_informe_proyecto', $id)->where('tipo', "images")->delete();
            $this->saveImagesFile($request, 'images', 'images', $id);
        }
        return response()->json(["status" => "ok"], 200);
    }
    public function delete_visit_report(Request $request, $id)
    {
        DB::table('informe_proyecto')
            ->where('Informe_ID', $id)
            ->update(['delete_informe_proyecto' => '0']);
        return response()->json(['success' => 'Report deleting successfully']);
    }

    private function descomponerImage($arrayImages)
    {
        $images = [];
        foreach ($arrayImages as $value) {
            $path = public_path() . '/uploads/' . $value->imagen;
            $extencion = pathinfo($path, PATHINFO_EXTENSION);
            $image = base64_encode(file_get_contents($path));
            $images[] = array(
                "filepath" => $value->caption,
                "webviewPath" => "data:image/$extencion;base64, $image",
                "tipo" => 'image',
            );
        }
        return $images;
    }
    public function get_config_mail($id, $proyect = "")
    {
        $config = DB::table('configuration')->select('body_ticket_email', 'title_ticket_email')->find(1);
        if ($proyect == "") {
            $proyect = DB::table('informe_proyecto')->select('Pro_ID')
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
    public function sendmailgoal(Request $request, $id, $part = "all")
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
        $error = Validator::make($request->all(), $rules);
        if (request()->ajax() === true) {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        if ($part == "all") {
            $pdf = $this->pdf($id, true);
        } else {
            $pdf = $this->pwt($id, true);
        }
        $error = Validator::make($request->all(), $rules, $messages);
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

        $pdf = $this->pdf($id, true);

        $data = [];

        $to = $request->to;
        $cc = $request->cc;
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

            // otherwise everything is okay ...
            return response()->json([
                'success' => 'Success in sending the mail',
            ]);
        }
        return response()->json(['errors' => ['Error sending mail']]);
    }

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

        return $pdf->download("$report_goal->subempresa-VisitReport$report_goal->Codigo.pdf");
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
        return $pdf->download("report num_$id.pdf");
    }
}
