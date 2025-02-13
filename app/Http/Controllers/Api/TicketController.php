<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Ticket;
use \stdClass;
use App\Personal;
use App\Razon_Trabajo;
use App\Material;
use App\Actividad;
use App\Ticket_material;
use Image;
use App\Tipo_trabajo;
use Validator;
use App\ContactoProyecto;
use DB;
use Mail;
use PDF;

class TicketController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id, Request $request)
    {
        $data = Ticket::selectRaw(
            "ticket.ticket_id,
            ticket.actividad_id,
            CONCAT(personal.Nombre,' ',personal.Apellido_Paterno,' ',personal.Apellido_Materno) as assignTicket,
            ticket.empleado_id,
            empresas.Codigo as general_contractor,
            proyectos.Codigo as proyect,
            proyectos.Nombre as proyect_nombre,
            CONCAT(proyectos.Calle, ', ', proyectos.Ciudad, ', ',  proyectos.Estado,' ', proyectos.Zip_Code) as proyect_address, 
            ticket.fecha_ticket as date_work,
            ticket.foreman_name,
            ticket.pco,
            ticket.Num,
            ticket.horario,
            ticket.descripcion,
            proyectos.Pro_Id as id_proyecto,
            ticket.superintendent_name as superintendente_name,
            ticket.fecha_finalizado as finish_date,
            ticket.firma_foreman as signarute_foreman,
            ticket.firma_cliente as signature_superintendente"
        )
                ->where('actividad_id', $id)
                ->where('delete', 0)
                ->where('ticket.estado', 'creado')
                /*->when(!auth()->guard('api')->user()->checkRol(['administrador']), function ($query) {
                    return $query->where(function ($q) {
                        $q->where('ticket.empleado_id', auth()->guard('api')->user()->Empleado_ID)
                            ->orWhere('proyectos.Foreman_ID', auth()->guard('api')->user()->Empleado_ID)
                            ->orWhere('proyectos.Lead_ID', auth()->guard('api')->user()->Empleado_ID)
                            ->orWhere('proyectos.Coordinador_Obra_ID', auth()->guard('api')->user()->Empleado_ID)
                            ->orWhere('proyectos.Coordinador_ID', auth()->guard('api')->user()->Empleado_ID);
                    });
                })*/
                ->join('proyectos', 'ticket.proyecto_id', 'proyectos.Pro_ID')
                ->join('personal', 'personal.Empleado_ID', 'ticket.empleado_id')
                ->join('empresas', 'empresas.Emp_ID', 'proyectos.Emp_ID')
                ->get();


        $proyecto = Actividad::select('empresas.Codigo as empresa', 'actividades.*', 'proyectos.*')
        ->selectRaw("CONCAT(proyectos.Calle, ', ', proyectos.Ciudad, ', ',  proyectos.Estado,' ', proyectos.Zip_Code) as dirrecion")
        ->where('actividades.Actividad_ID', $id)
        ->join('proyectos', 'actividades.Pro_ID', 'proyectos.Pro_ID')
        ->join('empresas', 'empresas.Emp_ID', 'proyectos.Emp_ID')
        ->first();
        $proyecto->tickets=$data;
        return response()->json($data, 200);
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
    public function store(Request $request, $id)
    {
        //return response()->json($request->imagesFinal, 200);
        $rules = array(
            'foreman_name' => 'required',
            'id_proyecto' => 'required',
            'date_work' => 'required|date_format:m/d/Y',
            //signature
            'signarute_foreman' => 'nullable',
            'signature_superintendente' => 'nullable',
            'horario' => 'required',
            'descripcion' => 'required',
            'materiales' => 'nullable|array',
            'workers' => 'nullable|array',
            'superintendente_name' => 'nullable',
            'finish_date' => 'nullable|date_format:m/d/Y',
            'empleado_id' => 'required',
            'imagesInicio' => 'nullable',
            'imagesFinal' => 'nullable',
            'pco' => 'nullable',
        );

        $request->materiales=json_decode($request->materiales);
        $request->workers=json_decode($request->workers);

        $query = Actividad::where('Actividad_ID', $id)->first();
        $name_img_client = "";
        $name_img_foremant = "";
        if (!empty($request->signarute_foreman)) {
            $name_img_foremant = "signature-foreman-" . time() . ".png";
            $path = public_path() . '/signatures/empleoye/' . $name_img_foremant;
            Image::make(file_get_contents($request->signarute_foreman))->save($path);
        }
        if (!empty($request->signature_superintendente)) {
            $name_img_client = "signature-client-" . time() . ".png";
            $path = public_path() . '/signatures/client/' . $name_img_client;
            Image::make(file_get_contents($request->signature_superintendente))->save($path);
        }
   
        $empleado_id = $request->empleado_id;
        
        $n_ticket = Ticket::where('estado', 'creado')->where('proyecto_id', $request->id_proyecto)->count() + 1;
        $ticket=Ticket::insertGetId([
            'num' => $n_ticket,
            'horario' => $request->horario,
            'descripcion' => $request->descripcion,
            'foreman_name' => $request->foreman_name,
            'superintendent_name' => $request->superintendente_name,
            'firma_cliente' => $name_img_client,
            'firma_foreman' => $name_img_foremant,
            'estado' => "creado",
            'fecha_ticket' => ($request->date_work) ? date('Y-m-d', strtotime($request->date_work)) : null,
            'fecha_finalizado' => ($request->finish_date) ? date('Y-m-d', strtotime($request->finish_date)) : null,
            'pco'              => $request->pco,
            'actividad_id' => $query->Actividad_ID,
            'proyecto_id' => $query->Pro_ID,
            'empleado_id' => $empleado_id,
            'delete' => 0,
        ]);
        if (!empty($request->materiales)) {
            foreach ($request->materiales as $val) {
                DB::table('ticket_material')->insert([
                    'cantidad' => $val->cantidad,
                    'material_id' =>$val->id_material,
                    'ticket_id' => $ticket,
                ]);
            }
        }
        if (!empty($request->workers)) {
            foreach ($request->workers as $val) {
                DB::table('ticket_trabajadores')->insert([
                    'profesion_id' => $val->id_class_worker,
                    'n_worker' => $val->n_worker,
                    'reg_hours' =>  $val->reg_hours,
                    'premium_hours' =>  $val->premium_hours,
                    'out_hours' =>  $val->out_hours,
                    'prepaid_hours' =>  $val->prepaid_hours,
                    'ticket_id' => $ticket
                ]);
            }
        }
        
        if ($request->hasFile('imagesFinal')) {
            $this->saveImagesFile($request, 'imagesFinal', 'final', $ticket);
        }
        if ($request->hasFile('imagesInicio')) {
            $this->saveImagesFile($request, 'imagesInicio', 'inicio', $ticket);
        }
        return response()->json('ok', 200);
    }
    private function saveImagesFile(Request $request, $requestTipo, $tipo, $ticket)
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
                $name_img = "$tipo-image-$ticket-" . uniqid() . time() . "." . $extension;
                $path = public_path() . '/uploads/' . $name_img;
                if ($fileSize > 1500000) {
                    $actual_image = Image::make(file_get_contents($file));
                    $height = $actual_image->height()/4;
                    $width = $actual_image->width()/4;
                    $actual_image->resize($width, $height)->save($path);
                    $fileSize = $actual_image->filesize();
                } else {
                    Image::make(file_get_contents($file))->save($path);
                }

                $insert = DB::table('ticket_imagen')->insertGetId([
                    'imagen' => $name_img,
                    'tipo' => $tipo,
                    'ticket_id' => $ticket,
                    'caption' => $filename,
                    'size' => $fileSize,
                ]);
            }
        }
    }

    public function saveImages($images64, $type, $id)
    {
        foreach ($images64 as $value) {
            $name=  "$type-image-$id-" . uniqid() . time() . ".jpg";
            $path = public_path() . '/uploads/' . $name;
            $imagen=Image::make(file_get_contents($value["webviewPath"]));//controlar peso de archivo
            $height = $imagen->height()/4;
            $width = $imagen->width()/4;
            $imagen->resize($width, $height)->save($path);

            $insert = DB::table('ticket_imagen')->insertGetId([
                'imagen' => $name,
                'tipo' => $type,
                'ticket_id' => $id,
                'caption' => $value["filepath"],
                'size' => 350000,
            ]);
        }
    }
    public function get_class_workers()
    {
        $tipo_trabajo = Tipo_trabajo::select('id as id_trabajo', 'nombre', 'descripcion')
        ->get();
        return response()->json($tipo_trabajo, 200);
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
        $ticket = Ticket::selectRaw(
            "ticket.ticket_id,
            ticket.actividad_id,
            CONCAT(personal.Nombre,' ',personal.Apellido_Paterno,' ',personal.Apellido_Materno) as assignTicket,
            ticket.empleado_id,
            empresas.Codigo as general_contractor,
            proyectos.Codigo as proyect,
            proyectos.Nombre as proyect_nombre,
            CONCAT(proyectos.Calle, ', ', proyectos.Ciudad, ', ',  proyectos.Estado,' ', proyectos.Zip_Code) as proyect_address, 
            ticket.fecha_ticket as date_work,
            ticket.foreman_name,
            ticket.pco,
            ticket.Num,
            ticket.horario,
            ticket.descripcion,
            proyectos.Pro_Id as id_proyecto,
            ticket.superintendent_name as superintendente_name,
            ticket.fecha_finalizado as finish_date,
            ticket.firma_foreman as signarute_foreman,
            ticket.firma_cliente as signature_superintendente"
        )
        //->where('actividad_id', $id)
        ->where('delete', 0)
        ->where('ticket.estado', 'creado')
        ->where('ticket.ticket_id', $id)
        ->join('proyectos', 'ticket.proyecto_id', 'proyectos.Pro_ID')
        ->join('personal', 'personal.Empleado_ID', 'ticket.empleado_id')
        ->join('empresas', 'empresas.Emp_ID', 'proyectos.Emp_ID')
        ->first();

        $materiales=Ticket_material::select(
            "ticket_material.id",
            "ticket_material.ticket_id",
            "materiales.Denominacion as descripcion",
            "materiales.Unidad_Medida as unidad_medida",
            "ticket_material.material_id as id_material",
            "ticket_material.cantidad"
        )
        ->where('ticket_material.ticket_id', $ticket->ticket_id)
        ->join('materiales', 'materiales.Mat_ID', 'ticket_material.material_id')
        ->get();
        //firmas
        try {
            $ticket->signarute_foreman=$this->Base64($ticket->signarute_foreman, 'empleoye');
        } catch (\Throwable $th) {
            $ticket->signarute_foreman='';
        }
        try {
            $ticket->signature_superintendente=$this->Base64($ticket->signature_superintendente, 'client');
        } catch (\Throwable $th) {
            $ticket->signature_superintendente='';
        }
       
        $trabajadores=DB::table('ticket_trabajadores')->selectRaw(
            "
            ticket_trabajadores.n_worker,
            tipo_trabajo.nombre as n_class_worker,
            ticket_trabajadores.profesion_id as id_class_worker,
            ticket_trabajadores.reg_hours,
            (ticket_trabajadores.n_worker*ticket_trabajadores.reg_hours) as total_reg_hours,
            ticket_trabajadores.premium_hours,
            (ticket_trabajadores.n_worker*ticket_trabajadores.premium_hours) as total_premium_hours,
            ticket_trabajadores.out_hours,
            (ticket_trabajadores.n_worker*ticket_trabajadores.out_hours) as total_out_hours,
            ticket_trabajadores.prepaid_hours,
            (ticket_trabajadores.n_worker*ticket_trabajadores.prepaid_hours) as total_prepaid_hours"
        )
        ->where('ticket.ticket_id', $id)
        ->join('tipo_trabajo', 'tipo_trabajo.id', 'ticket_trabajadores.profesion_id')
        ->join('ticket', 'ticket.ticket_id', 'ticket_trabajadores.ticket_id')
        ->get();

        $images = DB::table('ticket_imagen')
        ->select(
            'imagen',
            'tipo',
            'caption'
        )
        ->where('ticket_id', $id)
        ->get();

        $ticket->imagesInicio=$this->descomponerImage($images, "inicio");
        $ticket->imagesFinal=$this->descomponerImage($images, "final");
        $ticket->materiales=$materiales;
        $ticket->workers=$trabajadores;
        return response()->json($ticket, 200);
    }

    private function descomponerImage($arrayImages, $tipo)
    {
        $images=[];
        foreach ($arrayImages as $value) {
            if ($value->tipo==$tipo) {
                $path =  public_path().'/uploads/'.$value->imagen;
                $extencion = pathinfo($path, PATHINFO_EXTENSION);
                $image = base64_encode(file_get_contents($path));
                $images[]=array(
                    "filepath"=>$value->caption,
                    "webviewPath"=>"data:image/$extencion;base64, $image",
                    "tipo"=>$value->tipo
                );
            }
        }
        return $images;
    }
    private function Base64($firma, $tipoFirma)
    {
        $path =  public_path().'/signatures/'.$tipoFirma.'/'.$firma;
        $extencion = pathinfo($path, PATHINFO_EXTENSION);
        $image = base64_encode(file_get_contents($path));
        return "data:image/$extencion;base64, $image";
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
            'id_proyecto' => 'required',
            'date_work' => 'required|date_format:m/d/Y',
            //signature
            'signarute_foreman' => 'nullable',
            'signature_superintendente' => 'nullable',
            'horario' => 'required',
            'descripcion' => 'required',
            'materiales' => 'nullable|array',
            'workers' => 'nullable|array',
            'superintendente_name' => 'nullable',
            'finish_date' => 'nullable|date_format:m/d/Y',
            'imagesInicio' => 'nullable',
            'imagesFinal' => 'nullable',
            'pco' => 'nullable',
        );
        
        $request->materiales=json_decode($request->materiales);
        $request->workers=json_decode($request->workers);

        $query = Actividad::where('Actividad_ID', $id)->first();
        //manipulacion de  firma
        $name_img_client = "";
        $name_img_foremant = "";
        if (!empty($request->signarute_foreman)) {
            $name_img_foremant = "signature-foreman-" . time() . ".png";
            $path = public_path() . '/signatures/empleoye/' . $name_img_foremant;
            Image::make(file_get_contents($request->signarute_foreman))->save($path);
        }
        if (!empty($request->signature_superintendente)) {
            $name_img_client = "signature-client-" . time() . ".png";
            $path = public_path() . '/signatures/client/' . $name_img_client;
            Image::make(file_get_contents($request->signature_superintendente))->save($path);
        }


        $ticket = Ticket::find($id);
        $ticket->update([
            'horario' => $request->horario,
            'descripcion' => $request->descripcion,
            'foreman_name' => $request->foreman_name,
            'superintendent_name' => $request->superintendente_name,
            'firma_cliente' => $name_img_client,
            'firma_foreman' => $name_img_foremant,
            'estado' => "creado",
            'fecha_ticket' => ($request->date_work) ? date('Y-m-d', strtotime($request->date_work)) : null,
            'fecha_finalizado' => ($request->finish_date) ? date('Y-m-d', strtotime($request->finish_date)) : null,
            'pco'              => $request->pco,
            'delete' => 0,
        ]);
        if (!empty($request->materiales)) {
            DB::table('ticket_material')->where('ticket_id', $id)->delete();
            foreach ($request->materiales as $val) {
                DB::table('ticket_material')->insert([
                    'cantidad' => $val->cantidad,
                    'material_id' =>$val->id_material,
                    'ticket_id' => $ticket->ticket_id,
                ]);
            }
        } else {
            DB::table('ticket_material')->where('ticket_id', $id)->delete();
        }
        if (!empty($request->workers)) {
            DB::table('ticket_trabajadores')->where('ticket_id', $id)->delete();
            foreach ($request->workers as $val) {
                DB::table('ticket_trabajadores')->insert([
                    'profesion_id' => $val->id_class_worker,
                    'n_worker' => $val->n_worker,
                    'reg_hours' =>  $val->reg_hours,
                    'premium_hours' =>  $val->premium_hours,
                    'out_hours' =>  $val->out_hours,
                    'prepaid_hours' =>  $val->prepaid_hours,
                    'ticket_id' => $ticket->ticket_id
                ]);
            }
        } else {
            DB::table('ticket_trabajadores')->where('ticket_id', $id)->delete();
        }
      
        if ($request->hasFile('imagesFinal')) {
            DB::table('ticket_imagen')->where('ticket_id', $id)->where('tipo', "final")->delete();
            
            $this->saveImagesFile($request, 'imagesFinal', 'final', $ticket->ticket_id, );
        }
        if ($request->hasFile('imagesInicio')) {
            DB::table('ticket_imagen')->where('ticket_id', $id)->where('tipo', "inicio")->delete();
            $this->saveImagesFile($request, 'imagesInicio', 'inicio', $ticket->ticket_id, );
        }
        return response()->json('actualizado', 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $ticket = Ticket::find($id);
        $ticket->update([
            'delete' => 1
        ]);
    }
    public function allPersonal()
    {
        $personal = Personal::selectRaw(
            "Empleado_ID as empleado_id, CONCAT(COALESCE(personal.Nombre,''), ' ',  COALESCE(personal.Apellido_Paterno,''), ' ',  COALESCE(personal.Apellido_Materno,'')) as descripcion"
        )
        ->where('status', 1)
        ->get();
        return response()->json($personal, 200);
    }
    /**
     * question
     * metodo para devolver todas la preguntas
     * @param  mixed $tipo
     * @return void
     */
    public function question($tipo)
    {
        if ($tipo==="where") {
            $data=$this->where();
        } else {
            $data=Razon_Trabajo::select('id as id_question', 'descripcion', 'descripcion_traduccion')
            ->where('tipo', $tipo)->get();
        }
        return response()->json($data, 200);
    }
    public function where()
    {
        $where = DB::table('area_control')
        ->select("area_control.Area_ID as area_id")
        ->selectRaw(
            "CONCAT(area_control.Nombre, ', ', edificios.Nombre, ', ',  floor.Nombre) as descripcion"
        )
        ->leftJoin('floor', 'area_control.Floor_ID', 'floor.Floor_ID')
        ->join('edificios', 'floor.Edificio_ID', 'edificios.Edificio_ID')
        ->get();
        return $where;
    }
    public function materiales($id)
    {
        $materiales = Material::select('materiales.Mat_ID as id_material', 'materiales.Unidad_Medida as unidad_medida', 'materiales.Denominacion as denominacion')
        ->where(function ($q) use ($id) {
            $q->where('materiales.Pro_ID', $id)
                ->Orwhere('Cat_ID', 7);
        })
        ->leftJoin('actividades', 'materiales.Pro_ID', 'actividades.Pro_ID')
        ->distinct('materiales.Mat_ID')
        ->get();
        return response()->json($materiales, 200);
    }
    public function get_mail($id)
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

    public function sendmailticket(Request $request, $id)
    {
        $rules = array(
            'to' => 'array',
            'cc' => 'array',
            'title_m' => 'required',
            'body_m' => 'required',
        );

        $messages =  [
            'to.required'=> 'The "TO" field is required',
            'cc.required' => 'The "CC" field is required',
        ];

        $error = Validator::make($request->all(), $rules, $messages);

        if ($error->fails()) {
            return response()->json(['errors' => $error->errors()->all()]);
        }
        
        $pdf = $this->pdf($id, true);

        $data = [];

        $to = $request->to;
        $cc = $request->cc;
        if ($request->to || $request->cc) {
            Mail::send([], $data, function ($message) use ($data, $pdf, $id, $request, $to, $cc) {
                if ($request->to) {
                    $message->to($to);
                }
                if ($request->cc) {
                    $message->cc($cc);
                }
                $message->subject($request->title_m);
                $message->attachData($pdf->output(), "ticket-$id.pdf", [
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
                'success' => 'Success in sending the mail'
            ]);
        }
        return response()->json(['errors' => ['Error sending mail']]);
    }
    public function pdf($id, $view = false)
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
        return $pdf->download("ticket-$ticket->num.pdf");
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
                    if ($fileSize > 5000) {
                        $actual_image = Image::make(file_get_contents($file));
                        $height = $actual_image->height()/4;
                        $width = $actual_image->width()/4;
                        $actual_image->resize($width, $height)->save($path);
                        $fileSize = $actual_image->filesize();
                    } else {
                        Image::make(file_get_contents($file))->save($path);
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

    /* data de where  */
    //datatable list activities
}
