<?php

namespace App\Http\Controllers\Api;

use App\Actividad;
use App\ContactoProyecto;
use App\Http\Controllers\Controller;
use App\Material;
use App\Personal;
use App\Razon_Trabajo;
use App\Ticket;
use App\Tipo_trabajo;
use DB;
use File;
use Illuminate\Http\Request;
use Image;
use \stdClass;

class AppDatabase extends Controller
{
    private $version = 'version 1';
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function createDataBase()
    {
        /* obtencion de fechas */
        $fecha = DB::table('configuration')->selectRaw('now() as fecha')->where('id', 1)->first();
        $fecha = $fecha->fecha;
        $fecha_inicial = date('Y-m-d', strtotime($fecha . "- 5 day"));
        $fecha_final = date('Y-m-d', strtotime($fecha . "+10 day"));

        $rol = DB::table('roles_app')
            ->select(
                'roles_app.id as rol_id',
                'roles_app.nombre'
            )
            ->get();

        $config = DB::table('configuration')->select('body_ticket_email', 'title_ticket_email')->find(1);
        $emails = DB::table('proyectos')
            ->selectRaw("
            proyectos.Pro_ID,
        f.email as Foreman_mail,
        l.email as Lead_mail,
        c_o.email as Coordinador_Obra_mail,
        c.email as Pwtsuper_mail")
        //->where('Pro_ID', $id)
            ->leftJoin('personal as f', 'f.Empleado_ID', 'proyectos.Foreman_ID')
            ->leftJoin('personal as l', 'l.Empleado_ID', 'proyectos.Lead_ID')
            ->leftJoin('personal as c', 'c.Empleado_ID', 'proyectos.Coordinador_ID')
            ->leftJoin('personal as c_o', 'c_o.Empleado_ID', 'proyectos.Coordinador_Obra_ID')
            ->get();
        $email_contac = ContactoProyecto::select('email', 'contacto_proyecto.Pro_ID')
            ->where('tipo_contacto.nombre', 'ticket')
            ->join('tipo_contacto', 'tipo_contacto.id_tipo_contacto', 'contacto_proyecto.tipo_contacto')
            ->join('personal', 'personal.Empleado_ID', 'contacto_proyecto.Empleado_ID')
            ->get();

        $app = new stdClass();

        /*registro diario*/
        $app->registro_diario = $this->module_registro_diario($fecha_inicial, $fecha_final);
        /* ticket */
        $app->ticket = $this->module_ticket($fecha_inicial, $fecha_final);
        /*report visit */
        $app->personal = $this->module_personal();
        $app->visit_report = $this->module_visit_report($fecha_inicial, $fecha_final);
        $app->proyectos = $this->module_proyectos($fecha_inicial, $fecha_final);
        /*version */
        $app->codigo = $this->version;
        $app->fecha = $fecha;
        return response()->json($app, 200);
    }

    private function module_ticket($fecha_inicial, $fecha_final)
    {
        $ticket = new stdClass();
        $ticket->actividades = $this->actividades($fecha_inicial, $fecha_final);
        $ticket->actividad_personal = $this->actividad_personal($ticket->actividades);
        $tickets = $this->ticket($ticket->actividades);
        $tickets = $this->descomponerFirmas($tickets);
        $ticket->tickets = $tickets;
        $ticket->why = $why = Razon_Trabajo::select('id', 'descripcion as text', 'descripcion', 'descripcion_traduccion')
            ->where('tipo', 'why')->get();
        $ticket->what = $what = Razon_Trabajo::select('id as id_question', 'descripcion', 'descripcion_traduccion')
            ->where('tipo', 'what')->get();
        $ticket->tipo_trabajo = $tipo_trabajo = Tipo_trabajo::select('id as id_trabajo', 'nombre', 'descripcion')
            ->get();
        $ticket->tickets_materiales = $this->tickets_material($ticket->tickets);
        $ticket->tickets_trabajadores = $this->tickets_trabajadores($ticket->tickets);
        /*$app->rol = $rol;
        $app->config = $config;
        $app->emails = $emails;
        $app->email_contac = $email_contac;
        $app->ticket_materiales = $tickets_material;
        $app->tickets_trabajadores = $tickets_trabajadores; */
        return $ticket;
    }
    private function module_registro_diario($fecha_inicial, $fecha_final)
    {
        $registro_diario = new stdClass();
        $registro_diario->asistencia_actividad = $this->asistencia_actividad($fecha_inicial, $fecha_final);
        //$registro_diario->asistencia_actividad_personal = $this->asistencia_actividad_personal($registro_diario->asistencia_actividad);
        $registro_diario->registro_diario = $this->registro_diario($registro_diario->asistencia_actividad);
        $registro_diario->registro_diario_actividad = $this->registro_diario_actividad($registro_diario->registro_diario);
        $registro_diario->asistencia_areas = $this->asistencia_areas($registro_diario->registro_diario);
        $registro_diario->asistencia_task = $this->asistencia_task($registro_diario->asistencia_areas);
        return $registro_diario;
    }
    private function asistencia_task($areas)
    {
        $areas_id = [];
        foreach ($areas as $key => $area) {
            $areas_id[] = $area->area_id;
        }
        $task = DB::table('task')
            ->selectRaw(
                "task.Task_ID as task_id,
        task.Nombre as nombre,
        task.Area_ID"
            )
            ->whereIn('task.Area_ID', $areas_id)
            ->groupBy('task.Task_ID')
            ->get();
        return $task;
    }
    private function asistencia_areas($proyectos)
    {
        $proyectos_id = [];
        foreach ($proyectos as $key => $proyecto) {
            $proyectos_id[] = $proyecto->proyecto_id;
        }
        $area_control = DB::table('area_control')
            ->selectRaw(
                "area_control.area_ID as area_id,
            area_control.Nombre as nombre,
            area_control.pro_ID,
            area_control.Floor_ID
            "
            )
            ->whereIn('area_control.pro_ID', $proyectos_id)
            ->groupBy('area_control.area_ID')
            ->get();
        return $area_control;
    }
    private function registro_diario_actividad($resgistro_diario)
    {
        $registro_diario_id = [];
        foreach ($resgistro_diario as $key => $resgistro) {
            $registro_diario_id[] = $resgistro->registro_diario_id;
        }
        $actividadesPersonal = DB::table('actividad_personal')
            ->selectRaw(
                "registro_diario_actividad.RDA_ID as registro_diario_actividad_id,
            actividad_personal.Actividad_ID as actividad_id,
            registro_diario.Reg_ID as registro_diario_id,
            registro_diario.Pro_ID as proyecto_id,
            actividad_personal.HContract as hora_worker,
            actividad_personal.HTM as hora_htm,
            registro_diario.Hora_Ingreso as hora_ingreso,
            registro_diario.Fecha_Hingreso as fecha_ingreso,
            registro_diario.Hora_Salida as hora_salida,
            registro_diario.Fecha_Hsalida as fecha_salida,
            actividad_personal.Empleado_ID as empleado_id,
            registro_diario_actividad.Verificado_Foreman as verificar_foreman,
            registro_diario_actividad.Task_ID as task_id,
            personal.Nick_Name as nickname,
            CONCAT(COALESCE(personal.Nombre,''), ' ',  COALESCE(personal.Apellido_Paterno,''), ' ',  COALESCE(personal.Apellido_Materno,'')) as nombre,
            actividad_personal.Note as note"
            )
            ->Join('personal', 'actividad_personal.Empleado_ID', 'personal.Empleado_ID')
            ->Join('registro_diario', function ($q) {
                $q->on('registro_diario.Actividad_Id', '=', 'actividad_personal.Actividad_ID')
                    ->on('registro_diario.Empleado_ID', '=', 'actividad_personal.Empleado_ID');
            })
            ->Join('registro_diario_actividad', 'registro_diario_actividad.Reg_ID', 'registro_diario.Reg_ID')
            ->whereIn('registro_diario_actividad.Reg_ID', $registro_diario_id)
            ->get();

        $actividadesPersonal = $this->detalle_registro($actividadesPersonal);
        return $actividadesPersonal;
    }
    private function registro_diario($actividades)
    {
        $actividades_id = [];
        foreach ($actividades as $key => $actividad) {
            $actividades_id[] = $actividad->Actividad_ID;
        }
        $registros_diario = DB::table('registro_diario')
            ->selectRaw(
                "
            CONCAT(COALESCE(personal.Nombre,''), ' ',  COALESCE(personal.Apellido_Paterno,''), ' ',  COALESCE(personal.Apellido_Materno,'')) as nombre,
            personal.Nick_Name as nickname,
            registro_diario.Hora_Salida as hora_salida,
            registro_diario.Fecha_Hsalida as fecha_salida,
            registro_diario.Hora_Ingreso as hora_ingreso,
            registro_diario.Fecha_Hingreso as fecha_ingreso,
            registro_diario.Fecha as fecha,
            registro_diario.Reg_ID as registro_diario_id,
            registro_diario.actividad_Id as actividad_id,
            registro_diario.Pro_ID as proyecto_id,
            registro_diario.Empleado_ID as empleado_id,
            registro_diario.Foto_Ingreso as foto_ingreso,
            registro_diario.Foto_Salida foto_salida,
            registro_diario.Pregunta_IN as pregunta_in,
            registro_diario.Pregunta_OUT as pregunta_out,
            registro_diario.Clave_Digitada_In as clave_in,
            registro_diario.Clave_Digitada_Out as clave_out
            "
            )
            ->whereIn('registro_diario.Actividad_ID', $actividades_id)
            ->join('personal', 'registro_diario.Empleado_ID', 'personal.Empleado_ID')
            ->orderBy('registro_diario.Reg_ID', 'DESC')
            ->groupBy('registro_diario.Reg_ID')
            ->get();
        $this->descomponerAsistenciaSync($registros_diario);
        foreach ($registros_diario as $key => $registro) {
            $floor = DB::table('edificios')
                ->selectRaw(
                    "floor.Floor_ID as floor_id,
            floor.Nombre as nombre"
                )
                ->Join('floor', 'edificios.Edificio_ID', 'floor.Edificio_ID')
                ->where('edificios.Pro_ID', $registro->proyecto_id)
                ->first();
            try {
                $registros_diario[$key]->floor_id = $floor->floor_id;
                $registros_diario[$key]->floor = $floor->nombre;
            } catch (\Throwable $th) {
                $registros_diario[$key]->floor_id = '';
                $registros_diario[$key]->floor = '';
            }
            $registros_diario[$key]->modo = 'online';
        }
        return $registros_diario;
    }
    private function asistencia_actividad($fecha_inicial, $fecha_final)
    {
        $listActividades = DB::table('proyectos')
            ->select(
                'actividades.*',
                'proyectos.Pro_ID',
                'empresas.Codigo as empresa',
                'proyectos.Codigo',
                'proyectos.Nombre',
                'proyectos.Foreman_ID',
                'proyectos.Lead_ID',
                'em1.Celular as celular_foreman',
                DB::raw("CONCAT(proyectos.Numero, ', ', proyectos.Calle, ', ', proyectos.Ciudad, ', ',  proyectos.Estado,' ', proyectos.Zip_Code) as dirrecion"),
                DB::raw("CONCAT(em1.Nombre, ' ', em1.Apellido_Paterno, ' ',  em1.Apellido_Materno) as Foreman"),
                DB::raw("CONCAT(em2.Nombre, ' ',  em2.Apellido_Paterno, ' ',  em2.Apellido_Materno) as Cordinador"),
                DB::raw("CONCAT(em3.Nombre, ' ',  em3.Apellido_Paterno, ' ',  em3.Apellido_Materno) as Manager"),
                DB::raw("CONCAT(em4.Nombre, ' ',  em4.Apellido_Paterno, ' ',  em4.Apellido_Materno) as Project_Manager"),
                DB::raw("CONCAT(em5.Nombre, ' ',  em5.Apellido_Paterno, ' ',  em5.Apellido_Materno) as Coordinador_Obra"),
                DB::raw("CONCAT(em6.Nombre, ' ',  em6.Apellido_Paterno, ' ',  em6.Apellido_Materno) as asistente_proyecto"),
            )
            ->join('actividades', 'actividades.Pro_ID', 'proyectos.Pro_ID')
            ->join('empresas', 'empresas.Emp_ID', 'proyectos.Emp_ID')
            ->leftJoin('personal as em1', 'em1.Empleado_ID', 'proyectos.Foreman_ID')
            ->leftJoin('personal as em2', 'em2.Empleado_ID', 'proyectos.Coordinador_ID')
            ->leftJoin('personal as em3', 'em3.Empleado_ID', 'proyectos.Manager_ID')
            ->leftJoin('personal as em4', 'em4.Empleado_ID', 'proyectos.Project_Manager_ID')
            ->leftJoin('personal as em5', 'em5.Empleado_ID', 'proyectos.Coordinador_Obra_ID')
            ->leftJoin('personal as em6', 'em6.Empleado_ID', 'proyectos.Asistant_Proyect_ID')
        /*  ->where(function ($query) use ($request) {
        return $query->where('proyectos.Foreman_ID', $request->foreman_id)->orWhere('proyectos.Lead_ID', $request->foreman_id);
        }) */
            ->whereBetween('actividades.Fecha', [$fecha_inicial, $fecha_final])
            ->orderBy('proyectos.Pro_ID')
            ->get();
        foreach ($listActividades as $key => $actividades) {
            $actividades_personal = DB::table('actividad_personal')
                ->select(
                    'personal.Nick_Name'
                )
                ->join('personal', 'personal.Empleado_ID', 'actividad_personal.Empleado_ID')
                ->where('actividad_personal.Actividad_ID', $actividades->Actividad_ID)
                ->pluck('personal.Nick_Name')->toArray();
            //dd($actividades_personal);
            $personal = implode(",", $actividades_personal);
            $listActividades[$key]->personal = $personal;
            $listActividades[$key]->modo = 'online';
        }
        return $listActividades;
    }
    private function asistencia_actividad_personal($actividades)
    {
        $resultado = [];
        foreach ($actividades as $key => $actividad) {
            $actividades_personal = DB::table('actividad_personal')
                ->select(
                    'personal.Nick_Name',
                    'actividad_personal.*'
                )
                ->join('personal', 'personal.Empleado_ID', 'actividad_personal.Empleado_ID')
                ->where('actividad_personal.Actividad_ID', $actividad->Actividad_ID)
                ->get();
            foreach ($actividades_personal as $key => $actividad_personal) {
                $actividades_personal[$key]->modo = 'online';
            }
            $resultado = $this->unir_arrays($actividades_personal, $resultado);
        }
        return $resultado;
    }
    public function asistencia_report_personal($registros_diarios, $fecha_inicial, $fecha_final)
    {
        $registro_diario_id = [];
        foreach ($registros_diarios as $key => $registro_diario) {
            $registro_diario_id[] = $registro_diario->registro_diario_id;
        }
        $actividadesPersonal = DB::table('actividad_personal')
            ->selectRaw(
                "registro_diario_actividad.RDA_ID as registro_diario_actividad_id,
            actividad_personal.Actividad_ID as actividad_id,
            registro_diario.Reg_ID as registro_diario_id,
            registro_diario.Pro_ID as proyecto_id,
            actividad_personal.HContract as hora_worker,
            actividad_personal.HTM as hora_htm,
            registro_diario.Hora_Ingreso as hora_ingreso,
            registro_diario.Fecha_Hingreso as fecha_ingreso,
            registro_diario.Hora_Salida as hora_salida,
            registro_diario.Fecha_Hsalida as fecha_salida,
            actividad_personal.Empleado_ID as empleado_id,
            registro_diario_actividad.Verificado_Foreman as verificar_foreman,
            registro_diario_actividad.Task_ID as task_id,
            personal.Nick_Name as nickname,
            CONCAT(COALESCE(personal.Nombre,''), ' ',  COALESCE(personal.Apellido_Paterno,''), ' ',  COALESCE(personal.Apellido_Materno,'')) as nombre,
            actividad_personal.Note as note"
            )
            ->Join('personal', 'actividad_personal.Empleado_ID', 'personal.Empleado_ID')
            ->Join('registro_diario', function ($q) {
                $q->on('registro_diario.Actividad_Id', '=', 'actividad_personal.Actividad_ID')
                    ->on('registro_diario.Empleado_ID', '=', 'actividad_personal.Empleado_ID');
            })
            ->Join('registro_diario_actividad', 'registro_diario_actividad.Reg_ID', 'registro_diario.Reg_ID')
            ->whereIn('registro_diario_actividad.RDA_ID', $registro_diario_id)
            ->get();
        $actividadesPersonal = $this->detalle_registro($actividadesPersonal);
        return $actividadesPersonal;
    }

    private function detalle_registro($actividadesPersonal)
    {
        $data = [];
        foreach ($actividadesPersonal as $key => $value) {
            $resultado = new stdClass();
            $resultado->registro_diario_actividad_id = $value->registro_diario_actividad_id;
            $resultado->registro_diario_id = $value->registro_diario_id;
            $resultado->proyecto_id = $value->proyecto_id;
            $resultado->actividad_id = $value->actividad_id;
            $resultado->hora_worker = $value->hora_worker;
            $resultado->hora_htm = $value->hora_htm;
            $resultado->hora_ingreso = $value->hora_ingreso;
            $resultado->fecha_ingreso = $value->fecha_ingreso;
            $resultado->hora_salida = $value->hora_salida;
            $resultado->fecha_salida = $value->fecha_salida;
            $resultado->empleado_id = $value->empleado_id;
            $resultado->nickname = $value->nickname;
            $resultado->nombre = $value->nombre;
            $resultado->note = $value->note;

            try {
                $floor = DB::table('edificios')
                    ->selectRaw(
                        "floor.Floor_ID as floor_id,
            floor.Nombre as nombre"
                    )
                    ->Join('floor', 'edificios.Edificio_ID', 'floor.Edificio_ID')
                    ->where('edificios.Pro_ID', $value->proyecto_id)
                    ->first();

                $resultado->floor_id = $floor->floor_id;
                $resultado->floor = $floor->nombre;
            } catch (\Throwable $th) {
                $resultado->floor_id = 0;
                $resultado->floor = 'error';
            }
            $resultado->task_id = $value->task_id;
            if ($value->verificar_foreman == '0' && $value->verificar_foreman == null) {
                $resultado->verificar_foreman = false;
            } else {
                $resultado->verificar_foreman = true;
            }
            $resultado->modo = 'online';
            $data[] = $resultado;
        }
        return $data;
    }

    /* ticket */
    private function tickets_trabajadores($tickets)
    {
        $ticket_ids = [];
        foreach ($tickets as $key => $ticket) {
            $ticket_ids[] = $ticket->ticket_id;
        }
        $tickets_trabajadores = DB::table('ticket_trabajadores')->selectRaw(
            "ticket_trabajadores.id as id_ticket_material,
            ticket_trabajadores.ticket_id,
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
            ->whereIn('ticket_trabajadores.ticket_id', $ticket_ids)
            ->join('tipo_trabajo', 'tipo_trabajo.id', 'ticket_trabajadores.profesion_id')
            ->join('ticket', 'ticket.ticket_id', 'ticket_trabajadores.ticket_id')
            ->get();

        return $tickets_trabajadores;
    }
    private function tickets_material($tickets)
    {
        $ticket_ids = [];
        foreach ($tickets as $key => $ticket) {
            $ticket_ids[] = $ticket->ticket_id;
        }
        $tickets_material = DB::table('ticket_material')
            ->select(
                'materiales.Denominacion as descripcion',
                'ticket_material.id as id_ticket_material',
                'ticket_material.ticket_id',
                'ticket_material.material_id as id_material',
                'materiales.Unidad_Medida as unidad_medida',
                'ticket_material.cantidad'
            )
            ->whereIn('ticket_material.ticket_id', $ticket_ids)
            ->join('materiales', 'ticket_material.material_id', 'materiales.Mat_ID')
            ->get();
        return $tickets_material;
    }
    private function actividades($fecha_inicial, $fecha_final)
    {
        $actividades = Actividad::selectRaw("
        actividades.Actividad_ID,
        actividades.Fecha,
        proyectos.Pro_ID,
        proyectos.Codigo,
        proyectos.Nombre,
        CONCAT(proyectos.Calle, ', ', proyectos.Ciudad, ', ',  proyectos.Estado,' ', proyectos.Zip_Code) as dirrecion,
        tipo_actividad.Actividad_Nombre,
        empresas.Codigo as empresa,
        CONCAT(f.Nombre, ' ', f.Apellido_Paterno, ' ',  f.Apellido_Materno) as Foreman,
        proyectos.Foreman_ID as foreman_id,
        CONCAT(l.Nombre, ' ', l.Apellido_Paterno, ' ',  l.Apellido_Materno) as Lead,
        CONCAT(c_o.Nombre, ' ',  c_o.Apellido_Paterno, ' ',  c_o.Apellido_Materno) as Coordinador_Obra,
        CONCAT(c.Nick_Name) as Pwtsuper")
            ->whereBetween('actividades.Fecha', [$fecha_inicial, $fecha_final])
            ->join('tipo_actividad', 'actividades.Tipo_Actividad_ID', 'tipo_actividad.Tipo_Actividad_ID')
            ->join('proyectos', 'proyectos.Pro_ID', 'actividades.Pro_ID')
            ->join('empresas', 'empresas.Emp_ID', 'proyectos.Emp_ID')
            ->join('actividad_personal', 'actividad_personal.Actividad_ID', 'actividades.Actividad_ID')
            ->leftJoin('personal as f', 'f.Empleado_ID', 'proyectos.Foreman_ID')
            ->leftJoin('personal as l', 'l.Empleado_ID', 'proyectos.Lead_ID')
            ->leftJoin('personal as c', 'c.Empleado_ID', 'proyectos.Coordinador_ID')
            ->leftJoin('personal as c_o', 'c_o.Empleado_ID', 'proyectos.Coordinador_Obra_ID')
            ->distinct('actividades.Actividad_ID')
            ->get();
        return $actividades;
    }
    private function actividad_personal($actividades)
    {
        $actividades_ids = [];
        foreach ($actividades as $key => $value) {
            $actividades_ids[] = $value->Actividad_ID;
        }
        $actividades_personal = DB::table('actividad_personal')
            ->whereIn('actividad_personal.Actividad_ID', $actividades_ids)
            ->get();
        return $actividades_personal;
    }
    private function ticket($actividades)
    {
        $actividades_ids = [];
        foreach ($actividades as $key => $value) {
            $actividades_ids[] = $value->Actividad_ID;
        }
        $tickets = Ticket::selectRaw(
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
            ->whereIn('ticket.actividad_id', $actividades_ids)
            ->join('proyectos', 'ticket.proyecto_id', 'proyectos.Pro_ID')
            ->join('personal', 'personal.Empleado_ID', 'ticket.empleado_id')
            ->join('empresas', 'empresas.Emp_ID', 'proyectos.Emp_ID')
            ->get();
        return $tickets;
    }
    /* modulo report visit */
    private function module_personal()
    {
        $personal = Personal::selectRaw(
            "Empleado_ID as empleado_id,
            CONCAT(COALESCE(personal.Nombre,''), ' ',  COALESCE(personal.Apellido_Paterno,''), ' ',  COALESCE(personal.Apellido_Materno,'')) as descripcion,
            Usuario as usuario,
            Password as password,
            Cargo as cargo,
            Emp_ID as empresa_id,
            email,
            Rol_ID as rol,
            R1 as respuesta1,
            R2 as respuesta2,
            R3 as respuesta3,
            P1 as pregunta1,
            P2 as pregunta2,
            P3 as pregunta3
            "
        )
            ->where('status', 1)
            ->get();
        return $personal;
    }
    private function module_visit_report($fecha_inicial, $fecha_final)
    {
        $visit_report = new stdClass();
        $visit_report->visit_report = $this->visit_report_db($fecha_inicial, $fecha_final);
        $visit_report->all_problema = $this->all_problema();
        $visit_report->all_consecuencia = $this->all_consecuencia();
        $visit_report->all_solucion = $this->all_solucion();
        $visit_report->all_images_report_visit = $this->all_images_visit_report($visit_report->visit_report);
        return $visit_report;
    }

    private function Base64($firma, $tipoFirma)
    {
        try {
            $path = public_path() . '/signatures/' . $tipoFirma . '/' . $firma;
            $extencion = pathinfo($path, PATHINFO_EXTENSION);
            $image = base64_encode(file_get_contents($path));
            return "data:image/$extencion;base64, $image";
        } catch (\Throwable $th) {
            return "";
        }
    }

    public function descomponerAsistencia($registroDiario)
    {
        $resultado = [];
        foreach ($registroDiario as $value) {
            $registro_diario = new stdClass();

            $registro_diario->modo = "online";
            $registro_diario->nombre = $value->nombre;
            $registro_diario->nickname = $value->nickname;
            $registro_diario->hora_salida = $value->hora_salida;
            $registro_diario->hora_ingreso = $value->hora_ingreso;
            $registro_diario->fecha_salida = $value->fecha_salida;
            $registro_diario->fecha_ingreso = $value->fecha_ingreso;
            $registro_diario->fecha = $value->fecha;
            $registro_diario->registro_diario_id = $value->registro_diario_id;
            $registro_diario->actividad_id = $value->actividad_id;
            $registro_diario->proyecto_id = $value->proyecto_id;
            $registro_diario->empleado_id = $value->empleado_id;
            $registro_diario->foto_ingreso = $this->obtenerImagesAsistencia($value->foto_ingreso);
            $registro_diario->foto_salida = $this->obtenerImagesAsistencia($value->foto_salida);
            $registro_diario->pregunta_in = $value->pregunta_in;
            $registro_diario->pregunta_out = $value->pregunta_out;
            $registro_diario->clave_in = $value->clave_in;
            $registro_diario->clave_out = $value->clave_out;
            $resultado[] = $registro_diario;
        }
        return $resultado;
    }
    public function descomponerAsistenciaSync($registroDiario)
    {
        foreach ($registroDiario as $key => $value) {
            $registro_diario[$key] = $value;
            $registro_diario[$key]->foto_ingreso = $this->obtenerImagesAsistencia($value->foto_ingreso);
            $registro_diario[$key]->foto_salida = $this->obtenerImagesAsistencia($value->foto_salida);
        }
        return $registro_diario;
    }
    private function obtenerImagesAsistencia($image)
    {
        if ($image === null || $image === '') {
            return "";
        } else {
            $path = base_path();
            try {
                $masArriba = dirname($path) . "/pwt/fotos/" . $image;
                $extencion = pathinfo($masArriba, PATHINFO_EXTENSION);
                $fileSize = File::size($masArriba);
                $file = File::get($masArriba);
                if ($fileSize > 1500000) {
                    $actual_image = Image::make($masArriba);
                    $height = $actual_image->height() / 4;
                    $width = $actual_image->width() / 4;
                    $actual_image->resize($width, $height);
                    $image = base64_encode(file_exists($actual_image));
                    return response()->json($image, 200);
                } else {
                    $image = base64_encode(file_get_contents($masArriba));
                    return "data:image/$extencion;base64, $image";
                }
            } catch (\Throwable $th) {
                return "data:image/$extencion;base64, $image";
            }
        }
    }

    private function descomponerImageTicket($arrayImages, $tipo)
    {
        $images = [];
        foreach ($arrayImages as $value) {
            if ($value->tipo == $tipo) {
                try {
                    $path = public_path() . '/uploads/' . $value->imagen;
                    $extencion = pathinfo($path, PATHINFO_EXTENSION);
                    $image = base64_encode(file_get_contents($path));
                    $fileSize = File::size($path);
                    $file = File::get($path);

                    if ($fileSize > 1500000) {
                        $actual_image = Image::make($path);
                        $height = $actual_image->height() / 4;
                        $width = $actual_image->width() / 4;
                        $actual_image->resize($width, $height);
                        $image = base64_encode(file_exists($actual_image));
                        $images[] = array(
                            "modo" => 'online',
                            "filepath" => $value->caption,
                            "webviewPath" => "data:image/$extencion;base64, $image",
                            "ticket_id" => $value->ticket_id,
                            "t_imagen_id" => $value->t_imagen_id,
                            "tipo" => $value->tipo,
                        );
                    } else {
                        $image = base64_encode(file_get_contents($path));
                        $images[] = array(
                            "modo" => 'online',
                            "filepath" => $value->caption,
                            "webviewPath" => "data:image/$extencion;base64, $image",
                            "ticket_id" => $value->ticket_id,
                            "t_imagen_id" => $value->t_imagen_id,
                            "tipo" => $value->tipo,
                        );

                    }

                } catch (\Throwable $th) {
                }
            }
        }
        return $images;
    }
    private function descomponerImages($arrayImages)
    {
        $images = [];
        foreach ($arrayImages as $value) {
            try {
                $path = public_path() . '/uploads/' . $value->imagen;
                $extencion = pathinfo($path, PATHINFO_EXTENSION);
                $image = base64_encode(file_get_contents($path));
                $images[] = array(
                    "filepath" => $value->caption,
                    "webviewPath" => "data:image/$extencion;base64, $image",
                    "id_informe_proyecto" => $value->ticket_id,
                    "t_imagen_id" => $value->t_imagen_id,
                );
            } catch (\Throwable $th) {
            }
        }
        return $images;
    }
    private function descomponerFirmas($tickets)
    {
        foreach ($tickets as $value) {
            $value->signarute_foreman = $this->Base64($value->signarute_foreman, 'empleoye');
            $value->signature_superintendente = $this->Base64($value->signature_superintendente, 'client');
        }
        return $tickets;
    }
    public function images_ticket($tickets)
    {
        $images_ticket = DB::table('ticket_imagen')
            ->select(
                'ticket_imagen.t_imagen_id',
                'ticket_imagen.ticket_id',
                'ticket_imagen.imagen',
                'ticket_imagen.tipo',
                'ticket_imagen.caption'
            )
            ->where('ticket.delete', '0')
            ->whereIn('ticket.ticket_id', $tickets)
            ->join('ticket', 'ticket.ticket_id', 'ticket_imagen.ticket_id')
            ->orderBy('ticket.ticket_id', 'DESC')
            ->get();
        $images = new stdClass();
        $images->inicio = $this->descomponerImageTicket($images_ticket, "inicio");
        $images->final = $this->descomponerImageTicket($images_ticket, "final");
        return $images;
    }

    public function images_visit_report()
    {
        $images_ticket = DB::table('goal_imagen')
            ->select(
                'goal_imagen.t_imagen_id',
                'goal_imagen.id_informe_proyecto',
                'goal_imagen.imagen',
                'goal_imagen.tipo',
                'goal_imagen.caption'
            )
            ->where('informe_proyecto.delete_informe_proyecto', '1')
            ->join('informe_proyecto', 'informe_proyecto.Informe_ID', 'goal_imagen.id_informe_proyecto')
            ->limit(50)
            ->orderBy('goal_imagen.t_imagen_id', 'DESC')
            ->get();
        $images_ticket = $this->descomponerImages($images_ticket);
        return $images_ticket;
    }
    public function loadImages()
    {
        $fecha = DB::table('configuration')->selectRaw('now() as fecha')->where('id', 1)->first();
        $fecha = $fecha->fecha;
        $fecha_inicial = date('Y-m-d', strtotime($fecha . "- 5 day"));
        $fecha_final = date('Y-m-d', strtotime($fecha . "+10 day"));

        $ticket = new stdClass();
        $ticket->actividades = $this->actividades($fecha_inicial, $fecha_final);
        $ticket->actividad_personal = $this->actividad_personal($ticket->actividades);
        $ticket->tickets = $this->ticket($ticket->actividades);

        foreach ($ticket->tickets as $key => $value) {
            $ticket->tickets[$key] = $value->ticket_id;
        }
        $imagenes = new stdClass();
        $imagenes->ticket = $this->images_ticket($ticket->tickets);
        //$imagenes->report_visit = $this->images_visit_report();
        return response()->json($imagenes, 200);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function sincronizacion(Request $request)
    {
        $fecha = DB::table('configuration')->selectRaw('now() as fecha')->where('id', 1)->first();
        $fecha = $fecha->fecha;
        $sync = new stdClass();
        $ticket = Ticket::selectRaw(
            "appsync.evento,
            ticket.ticket_id,
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
            ->whereBetween('appsync.fecha', [$request->fecha, $fecha])
            ->where('appsync.tabla', 'ticket')
            ->join('appsync', 'appsync.id', 'ticket.ticket_id')
            ->join('proyectos', 'ticket.proyecto_id', 'proyectos.Pro_ID')
            ->join('personal', 'personal.Empleado_ID', 'ticket.empleado_id')
            ->join('empresas', 'empresas.Emp_ID', 'proyectos.Emp_ID')
            ->get();
        $ticket = $this->descomponerFirmas($ticket);

        $actividad = Actividad::selectRaw("
        appsync.evento,
        actividades.Actividad_ID,
        actividades.Fecha,
        proyectos.Pro_ID,
        proyectos.Codigo,
        proyectos.Nombre,
        CONCAT(proyectos.Calle, ', ', proyectos.Ciudad, ', ',  proyectos.Estado,' ', proyectos.Zip_Code) as dirrecion,
        tipo_actividad.Actividad_Nombre,
        empresas.Codigo as empresa,
        CONCAT(f.Nombre, ' ', f.Apellido_Paterno, ' ',  f.Apellido_Materno) as Foreman,
        proyectos.Foreman_ID as foreman_id,
        CONCAT(l.Nombre, ' ', l.Apellido_Paterno, ' ',  l.Apellido_Materno) as Lead,
        CONCAT(c_o.Nombre, ' ',  c_o.Apellido_Paterno, ' ',  c_o.Apellido_Materno) as Coordinador_Obra,
        CONCAT(c.Nick_Name) as Pwtsuper")
            ->whereBetween('appsync.fecha', [$request->fecha, $fecha])
            ->where('appsync.tabla', 'actividades')
            ->join('appsync', 'appsync.id', 'actividades.Actividad_ID')
            ->join('tipo_actividad', 'actividades.Tipo_Actividad_ID', 'tipo_actividad.Tipo_Actividad_ID')
            ->join('proyectos', 'proyectos.Pro_ID', 'actividades.Pro_ID')
            ->join('empresas', 'empresas.Emp_ID', 'proyectos.Emp_ID')
        //->join('actividad_personal', 'actividad_personal.Actividad_ID', 'actividades.Actividad_ID')
            ->leftJoin('personal as f', 'f.Empleado_ID', 'proyectos.Foreman_ID')
            ->leftJoin('personal as l', 'l.Empleado_ID', 'proyectos.Lead_ID')
            ->leftJoin('personal as c', 'c.Empleado_ID', 'proyectos.Coordinador_ID')
            ->leftJoin('personal as c_o', 'c_o.Empleado_ID', 'proyectos.Coordinador_Obra_ID')
        //->distinct('actividades.Actividad_ID')
            ->get();

        $registro_diario = DB::table('registro_diario')
            ->selectRaw(
                "
            appsync.evento,
            CONCAT(COALESCE(personal.Nombre,''), ' ',  COALESCE(personal.Apellido_Paterno,''), ' ',  COALESCE(personal.Apellido_Materno,'')) as nombre,
            personal.Nick_Name as nickname,
            registro_diario.Hora_Salida as hora_salida,
            registro_diario.Fecha_Hsalida as fecha_salida,
            registro_diario.Hora_Ingreso as hora_ingreso,
            registro_diario.Fecha_Hingreso as fecha_ingreso,
            registro_diario.Fecha as fecha,
            registro_diario.Reg_ID as registro_diario_id,
            registro_diario.actividad_Id as actividad_id,
            registro_diario.Pro_ID as proyecto_id,
            registro_diario.Empleado_ID as empleado_id,
            registro_diario.Foto_Ingreso as foto_ingreso,
            registro_diario.Foto_Salida foto_salida,
            registro_diario.Pregunta_IN as pregunta_in,
            registro_diario.Pregunta_OUT as pregunta_out,
            registro_diario.Clave_Digitada_In as clave_in,
            registro_diario.Clave_Digitada_Out as clave_out
            "
            )
            ->whereBetween('appsync.fecha', [$request->fecha, $fecha])
            ->where('appsync.tabla', 'registro_diario')
            ->join('appsync', 'appsync.id', 'registro_diario.Reg_ID')
            ->Join('personal', 'registro_diario.Empleado_ID', 'personal.Empleado_ID')
            ->get();
        $registro_diario = $this->descomponerAsistenciaSync($registro_diario);

        $persona = Personal::selectRaw(
            "appsync.evento,
            Empleado_ID as empleado_id,
            CONCAT(COALESCE(personal.Nombre,''), ' ',  COALESCE(personal.Apellido_Paterno,''), ' ',  COALESCE(personal.Apellido_Materno,'')) as descripcion,
            personal.Usuario as usuario,
            personal.Password as password,
            personal.Cargo as cargo,
            personal.Emp_ID as empresa_id,
            personal.email,
            personal.Rol_ID as rol,
            personal.R1 as respuesta1,
            personal.R2 as respuesta2,
            personal.R3 as respuesta3,
            personal.P1 as pregunta1,
            personal.P2 as pregunta2,
            personal.P3 as pregunta3
            "
        )
            ->whereBetween('appsync.fecha', [$request->fecha, $fecha])
            ->where('appsync.tabla', 'personal')
            ->join('appsync', 'appsync.id', 'personal.Empleado_ID')
            ->get();

        $area_control = DB::table('area_control')
            ->selectRaw(
                "appsync.id as area_id,
            appsync.evento,
            CONCAT(area_control.Nombre, ', ', edificios.Nombre, ', ',  floor.Nombre) as descripcion"
            )
            ->whereBetween('appsync.fecha', [$request->fecha, $fecha])
            ->where('appsync.tabla', 'area_control')
            ->leftJoin('floor', 'area_control.Floor_ID', 'floor.Floor_ID')
            ->join('edificios', 'floor.Edificio_ID', 'edificios.Edificio_ID')
            ->rightJoin('appsync', 'appsync.id', 'area_control.Area_ID')
            ->distinct('area_id')
            ->get();

        $why = Razon_Trabajo::select(
            'razontrabajo.id as id_question',
            'razontrabajo.descripcion',
            'razontrabajo.descripcion_traduccion',
            'appsync.evento'
        )
            ->where('appsync.tabla', 'razon_trabajo')
            ->whereBetween('appsync.fecha', [$request->fecha, $fecha])
            ->where('razontrabajo.tipo', 'why')
            ->Join('appsync', 'appsync.id', 'razontrabajo.id')
            ->distinct('id_question')
            ->get();

        $what = Razon_Trabajo::select(
            'razontrabajo.id as id_question',
            'razontrabajo.descripcion',
            'razontrabajo.descripcion_traduccion',
            'appsync.evento'
        )
            ->where('appsync.tabla', 'razon_trabajo')
            ->whereBetween('appsync.fecha', [$request->fecha, $fecha])
            ->where('razontrabajo.tipo', 'what')
            ->Join('appsync', 'appsync.id', 'razontrabajo.id')
            ->distinct('id_question')
            ->get();

        $material = Material::select(
            'appsync.evento',
            'materiales.Mat_ID as id',
            'materiales.Unidad_Medida as unidad_medida',
            'materiales.Denominacion as text'
        )
            ->where('appsync.tabla', 'materiales')
            ->whereBetween('appsync.fecha', [$request->fecha, $fecha])
            ->leftJoin('actividades', 'materiales.Pro_ID', 'actividades.Pro_ID')
            ->Join('appsync', 'appsync.id', 'materiales.Mat_ID')
            ->distinct('materiales.Mat_ID')
            ->get();

        $tipo_trabajo = Tipo_trabajo::select(
            'appsync.evento',
            'tipo_trabajo.id as id_trabajo',
            'tipo_trabajo.nombre',
            'tipo_trabajo.descripcion'
        )
            ->where('appsync.tabla', 'tipo_trabajo')
            ->whereBetween('appsync.fecha', [$request->fecha, $fecha])
            ->Join('appsync', 'appsync.id', 'tipo_trabajo.id')
            ->distinct('id_trabajo')
            ->get();

        $tickets_images = DB::table('ticket_imagen')
            ->select(
                'appsync.evento',
                'ticket_imagen.t_imagen_id',
                'ticket_imagen.ticket_id',
                'ticket_imagen.imagen',
                'ticket_imagen.tipo',
                'ticket_imagen.caption'
            )
            ->whereBetween('appsync.fecha', [$request->fecha, $fecha])
            ->where('appsync.tabla', 'ticket_imagen')
            ->Join('appsync', 'appsync.id', 'ticket_imagen.t_imagen_id')
            ->get();

        $tickets_material = DB::table('ticket_material')->
            select(
            'appsync.evento',
            'materiales.Denominacion as descripcion',
            'ticket_material.id as id_ticket_material',
            'ticket_material.ticket_id',
            'ticket_material.material_id as id_material',
            'materiales.Unidad_Medida as unidad_medida',
            'ticket_material.cantidad'
        )
            ->whereBetween('appsync.fecha', [$request->fecha, $fecha])
            ->where('appsync.tabla', 'ticket_material')
            ->Join('appsync', 'appsync.id', 'ticket_material.id')
            ->join('materiales', 'ticket_material.material_id', 'materiales.Mat_ID')
            ->get();

        $tickets_trabajador = DB::table('ticket_trabajadores')->selectRaw(
            "appsync.evento,
            ticket_trabajadores.ticket_id,
            ticket_trabajadores.id as id_ticket_trabajadores,
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
            ->whereBetween('appsync.fecha', [$request->fecha, $fecha])
            ->where('appsync.tabla', 'ticket_trabajadores')
            ->Join('appsync', 'appsync.id', 'ticket_trabajadores.id')
            ->join('tipo_trabajo', 'tipo_trabajo.id', 'ticket_trabajadores.profesion_id')
            ->get();

        $eliminados = DB::table('appsync')->select(
            'appsync.id',
            'appsync.evento',
            'appsync.tabla',
        )
            ->whereBetween('appsync.fecha', [$request->fecha, $fecha])
            ->get();

        /*personal   */
        $personal = new stdClass();
        $personal->nuevo = $this->descomponerTipo($persona, "nuevo");
        $personal->editado = $this->descomponerTipo($persona, "editado");
        $personal->eliminado = $this->identificarEliminados($eliminados, 'personal', 'eliminado');

        $tickets = new stdClass();
        $tickets->eliminado = $this->descomponerTipo($ticket, "eliminado");
        $tickets->nuevo = $this->descomponerTipo($ticket, "nuevo");
        $tickets->editado = $this->descomponerTipo($ticket, "editado");

        $actividades = new stdClass();
        $actividades->nuevo = $this->descomponerTipo($actividad, "nuevo");
        $actividades->editado = $this->descomponerTipo($actividad, "editado");
        $actividades->eliminado = $this->identificarEliminados($eliminados, 'actividades', 'eliminado');

        $areas_control = new stdClass();
        $areas_control->eliminado = $this->descomponerTipo($area_control, "eliminado");
        $areas_control->nuevo = $this->descomponerTipo($area_control, "nuevo");
        $areas_control->editado = $this->descomponerTipo($area_control, "editado");

        $whats = new stdClass();
        $whats->eliminado = $this->descomponerTipo($what, "eliminado");
        $whats->nuevo = $this->descomponerTipo($what, "nuevo");
        $whats->editado = $this->descomponerTipo($what, "editado");

        $whys = new stdClass();
        $whys->eliminado = $this->descomponerTipo($why, "eliminado");
        $whys->nuevo = $this->descomponerTipo($why, "nuevo");
        $whys->editado = $this->descomponerTipo($why, "editado");

        $materiales = new stdClass();
        $materiales->eliminado = $this->descomponerTipo($material, "eliminado");
        $materiales->nuevo = $this->descomponerTipo($material, "nuevo");
        $materiales->editado = $this->descomponerTipo($material, "editado");

        $tipo_trabajos = new stdClass();
        $tipo_trabajos->eliminado = $this->descomponerTipo($tipo_trabajo, "eliminado");
        $tipo_trabajos->nuevo = $this->descomponerTipo($tipo_trabajo, "nuevo");
        $tipo_trabajos->editado = $this->descomponerTipo($tipo_trabajo, "editado");
        /*images */
        $imagesTicket = new stdClass();
        $imagesTicket->eliminado = $this->identificarEliminados($eliminados, 'ticket_imagen', "eliminado");
        $imagesTicket->nuevo = $this->descomponerTipoImages($tickets_images, "nuevo");
        $imagesTicket->editado = $this->descomponerTipoImages($tickets_images, "editado");

        $tickets_materiales = new stdClass();
        $tickets_materiales->eliminado = $this->identificarEliminados($eliminados, 'ticket_material', 'eliminado');
        $tickets_materiales->nuevo = $this->descomponerTipo($tickets_material, "nuevo");
        $tickets_materiales->editado = $this->descomponerTipo($tickets_material, "editado");

        $tickets_trabajadores = new stdClass();
        $tickets_trabajadores->eliminado = $this->identificarEliminados($eliminados, 'ticket_trabajadores', 'eliminado');
        $tickets_trabajadores->nuevo = $this->descomponerTipo($tickets_trabajador, "nuevo");
        $tickets_trabajadores->editado = $this->descomponerTipo($tickets_trabajador, "editado");

        $registro_diarios = new stdClass();
        $registro_diarios->eliminado = $this->identificarEliminados($eliminados, 'registro_diario', 'eliminado');
        $registro_diarios->nuevo = $this->descomponerTipo($registro_diario, "nuevo");
        $registro_diarios->editado = $this->descomponerTipo($registro_diario, "editado");

        $sync->tickets = $tickets;
        $sync->actividades = $actividades;
        $sync->personal = $personal;
        $sync->areas_controls = $areas_control;
        $sync->fecha = $fecha;
        $sync->whats = $whats;
        $sync->whys = $whys;
        $sync->materiales = $materiales;
        $sync->tipo_trabajos = $tipo_trabajos;
        $sync->imagesTicket = $imagesTicket;
        $sync->tickets_materiales = $tickets_materiales;
        $sync->tickets_trabajadores = $tickets_trabajadores;
        $sync->registro_diarios = $registro_diarios;
        //$sync->imagesInicio=$this->descomponerImage($tickets_images, "inicio");
        //$sync->imagesFinal=$this->descomponerImage($tickets_images, "final");

        $sync->codigo = $this->version;

        return response()->json($sync, 200);
    }

    private function visit_report($fecha_aplicacion, $fecha)
    {
        $visit_report = $this->visit_report_db($fecha_aplicacion, $fecha);

        $report_visit = new stdClass();
        $report_visit->eliminado = $this->identificarEliminados($eliminados, 'registro_diario', 'eliminado');
        $report_visit->nuevo = $this->descomponerTipo($visit_report, "nuevo");
        $report_visit->editado = $this->descomponerTipo($visit_report, "editado");
    }
    //verificar elementos
    private function descomponerTipo($array, $tipo)
    {
        $resultado = [];
        foreach ($array as $value) {
            if ($value->evento == $tipo) {
                $resultado[] = $value;
            }
        }
        return $resultado;
    }
    private function descomponerTipoImages($array, $tipo)
    {
        $resultado = [];
        foreach ($array as $value) {
            if ($value->evento == $tipo) {
                try {
                    $path = public_path() . '/uploads/' . $value->imagen;
                    $extencion = pathinfo($path, PATHINFO_EXTENSION);
                    $image = base64_encode(file_get_contents($path));
                    $resultado[] = array(
                        "filepath" => $value->caption,
                        "webviewPath" => "data:image/$extencion;base64, $image",
                        "ticket_id" => $value->ticket_id,
                        "t_imagen_id" => $value->t_imagen_id,
                        "tipo" => $value->tipo,
                    );
                } catch (\Throwable $th) {
                }
            }
        }
        return $resultado;
    }
    /*elimancion fisica */
    private function identificarEliminados($datos, $tabla, $tipo)
    {
        $resultado = [];
        foreach ($datos as $value) {
            if ($value->tabla == $tabla) {
                if ($value->evento == $tipo) {
                    $resultado[] = $value->id;
                }
            }
        }
        return $resultado;
    }

    public function get_materiales()
    {
        $materiales = Material::select(
            "materiales.Mat_ID as id_material",
            "materiales.Denominacion as denominacion",
            "materiales.Unidad_Medida as unidad_medida"
        )
            ->get();
        return response()->json($materiales, 200);
    }
    public function get_areas_controls()
    {
        $where = DB::table('area_control')
            ->select("area_control.Area_ID as area_id")
            ->selectRaw(
                "CONCAT(area_control.Nombre, ', ', edificios.Nombre, ', ',  floor.Nombre) as descripcion"
            )
        //->where('area_control.Pro_ID', $id)
            ->leftJoin('floor', 'area_control.Floor_ID', 'floor.Floor_ID')
            ->join('edificios', 'floor.Edificio_ID', 'edificios.Edificio_ID')
            ->get();
        return response()->json($where, 200);
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    private function mail()
    {
        $config = DB::table('configuration')->select('body_ticket_email', 'title_ticket_email')->find(1);
        $emails = DB::table('proyectos')
            ->selectRaw("
            proyectos.Pro_ID,
        f.email as Foreman_mail,
        l.email as Lead_mail,
        c_o.email as Coordinador_Obra_mail,
        c.email as Pwtsuper_mail")
        //->where('Pro_ID', $id)
            ->leftJoin('personal as f', 'f.Empleado_ID', 'proyectos.Foreman_ID')
            ->leftJoin('personal as l', 'l.Empleado_ID', 'proyectos.Lead_ID')
            ->leftJoin('personal as c', 'c.Empleado_ID', 'proyectos.Coordinador_ID')
            ->leftJoin('personal as c_o', 'c_o.Empleado_ID', 'proyectos.Coordinador_Obra_ID')
            ->get();
        $email_contac = ContactoProyecto::select('email', 'contacto_proyecto.Pro_ID')
        //->where('Pro_ID', $id)
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
    /* public function get_report_personal(Request $request)
    {
    $actividadesPersonal = DB::table('actividad_personal')
    ->selectRaw(
    "registro_diario_actividad.RDA_ID as registro_diario_actividad_id,
    actividad_personal.Actividad_ID as actividad_id,
    registro_diario.Reg_ID as registro_diario_id,
    registro_diario.Pro_ID as proyecto_id,
    actividad_personal.HContract as hora_worker,
    actividad_personal.HTM as hora_htm,
    registro_diario.Hora_Ingreso as hora_ingreso,
    registro_diario.Fecha_Hingreso as fecha_ingreso,
    registro_diario.Hora_Salida as hora_salida,
    registro_diario.Fecha_Hsalida as fecha_salida,
    actividad_personal.Empleado_ID as empleado_id,
    registro_diario_actividad.Verificado_Foreman as verificar_foreman,
    registro_diario_actividad.Task_ID as task_id,
    personal.Nick_Name as nickname,
    CONCAT(COALESCE(personal.Nombre,''), ' ',  COALESCE(personal.Apellido_Paterno,''), ' ',  COALESCE(personal.Apellido_Materno,'')) as nombre,
    actividad_personal.Note as note"
    )
    ->Join('personal', 'actividad_personal.Empleado_ID', 'personal.Empleado_ID')
    ->Join('registro_diario', function ($q) {
    $q->on('registro_diario.Actividad_Id', '=', 'actividad_personal.Actividad_ID')
    ->on('registro_diario.Empleado_ID', '=', 'actividad_personal.Empleado_ID');
    })
    ->Join('registro_diario_actividad', 'registro_diario_actividad.Reg_ID', 'registro_diario.Reg_ID')
    ->limit(2000)
    ->orderBy('registro_diario_actividad.RDA_ID', 'DESC')
    ->get();

    $actividadesPersonal = $this->detalle_registro($actividadesPersonal);

    return response()->json($actividadesPersonal, 200);
    }

    private function detalle_registro($actividadesPersonal)
    {
    $data = [];
    foreach ($actividadesPersonal as $key => $value) {
    $resultado = new stdClass();
    $resultado->modo = "online";
    $resultado->registro_diario_actividad_id = $value->registro_diario_actividad_id;
    $resultado->registro_diario_id = $value->registro_diario_id;
    $resultado->proyecto_id = $value->proyecto_id;
    $resultado->actividad_id = $value->actividad_id;
    $resultado->hora_worker = $value->hora_worker;
    $resultado->hora_htm = $value->hora_htm;
    $resultado->hora_ingreso = $value->hora_ingreso;
    $resultado->fecha_ingreso = $value->fecha_ingreso;
    $resultado->hora_salida = $value->hora_salida;
    $resultado->fecha_salida = $value->fecha_salida;
    $resultado->empleado_id = $value->empleado_id;
    $resultado->nickname = $value->nickname;
    $resultado->nombre = $value->nombre;
    $resultado->note = $value->note;
    $resultado->task_id = $value->task_id;
    if ($value->verificar_foreman == '0' && $value->verificar_foreman == null) {
    $resultado->verificar_foreman = false;
    } else {
    $resultado->verificar_foreman = true;
    }

    $data[] = $resultado;
    }

    return $data;
    } */

    public function area_control(Request $request)
    {
        $area_control = DB::table('area_control')
            ->selectRaw(
                "area_control.area_ID as area_id,
            area_control.Pro_ID as proyecto_id,
            area_control.Floor_ID as floor_id,
            area_control.Nombre as nombre"
            )
            ->get();
        return response()->json($area_control, 200);
    }

    public function area_task(Request $request)
    {
        $task = DB::table('task')
            ->selectRaw(
                "task.Task_ID as task_id,
            task.Nombre as nombre,
            task.Pro_ID as proyecto_id,
            task.Floor_ID as floor_id,
            task.Area_ID as area_id"
            )
            ->get();
        return response()->json($task, 200);
    }

    private function unir_arrays($array_entrada, $array_salida)
    {
        foreach ($array_entrada as $key => $array) {
            $array_salida[] = $array;
        }
        return $array_salida;
    }
    private function module_proyectos($fecha_inicial, $fecha_final)
    {
        $fecha = DB::table('configuration')->selectRaw('now() as fecha')->where('id', 1)->first();
        $fecha = $fecha->fecha;
        $fecha_inicial = date('Y-m-d', strtotime($fecha . "- 180 day"));
        $fecha_final = date('Y-m-d', strtotime($fecha . "+10 day"));

        $resultado = new stdClass();
        $proyectos = DB::table('proyectos')
            ->select(
                'Pro_ID',
                'Emp_ID',
                'Tipo_ID',
                'Nombre',
                'Estado',
                'Ciudad',
                'Zip_Code',
                'Calle',
                'Numero',
                'Contratista_General',
                DB::raw('DATE_FORMAT(Fecha_Inicio , "%m/%d/%Y" ) as Fecha_Inicio'),
                DB::raw('DATE_FORMAT(Fecha_Fin, "%m/%d/%Y" ) as Fecha_Fin'),
                'Project_Manager_ID',
                'Coordinador_Obra_ID',
                'Foreman_ID',
                'Lead_ID',
                'Coordinador_ID',
                'Manager_ID',
                'Estatus_ID',
                'Codigo',
                'Asistant_Proyect_ID'
            )
            ->whereBetween('proyectos.Fecha_Inicio', [$fecha_inicial, $fecha_final])
            ->orWhereBetween('proyectos.Fecha_Fin', [$fecha_inicial, $fecha_final])
            ->get();
        foreach ($proyectos as $key => $proyecto) {
            $proyectos[$key]->modo = 'online';
        }
        $resultado->proyectos = $proyectos;
        $resultado->area_control = $this->all_area_control($proyectos);
        return $resultado;
    }
    private function all_area_control($proyectos)
    {
        $areas = [];
        foreach ($proyectos as $key => $proyecto) {
            $floors = $where = DB::table('area_control')
                ->select('area_control.*')
                ->where('area_control.Pro_ID', $proyecto->Pro_ID)
                ->leftJoin('floor', 'area_control.Floor_ID', 'floor.Floor_ID')
                ->join('edificios', 'floor.Edificio_ID', 'edificios.Edificio_ID')
                ->get()
                ->toArray();
            foreach ($floors as $i => $floor) {
                $floors[$i]->modo = 'online';
            }
            $areas = $this->unir_arrays($floors, $areas);
        }

        return $areas;
    }
    private function visit_report_db($fecha_inicial, $fecha_final, $sincronizacion = false)
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
            ->join('proyectos', 'proyectos.Pro_ID', 'informe_proyecto.Pro_ID')
            ->join('empresas', 'empresas.Emp_ID', 'proyectos.Emp_ID')
            ->join('personal', 'personal.Empleado_ID', 'informe_proyecto.Empleado_ID')
            ->when($sincronizacion, function ($query) use ($fecha_inicial, $fecha_final) {
                return $query->whereBetween('appsync.fecha', [fecha_inicial, $fecha_final])
                    ->where('appsync.tabla', 'ticket_material')
                    ->Join('appsync', 'appsync.id', 'ticket_material.id');
            })
            ->when(!$sincronizacion, function ($query) use ($fecha_inicial, $fecha_final) {
                return $query->whereBetween('informe_proyecto.Fecha', [$fecha_inicial, $fecha_final]);
            })
            ->groupBy('informe_proyecto.informe_id')
            ->get();
        foreach ($visit_report as $key => $report) {
            $visit_report[$key]->modo = 'online';
        }
        return $visit_report;
    }

    private function images_visit_report_db($visit_report)
    {
        $visit_report = DB::table('informe_proyecto')
            ->select(
                'Informe_ID as informe_id',
                'Pro_ID as proyecto_id',
                'Codigo as codigo',
                'Empleado_ID as empleado_id',
                'Fecha as fecha',
                'Drywall_comments as cometarios',
                'estado as estado',
                'delete_informe_proyecto as delete'
            )
            ->whereBetween('informe_proyecto.Fecha', [date('Y-m-d', strtotime($fecha . "- 2 day")), date('Y-m-d', strtotime($fecha . "+10 day"))])
            ->get();
        return $visit_report;
    }
    private function all_problema()
    {
        $problemas = DB::table('goal_problem')
            ->select(
                'id as problema_id',
                'descripcion',
                'estado'
            )
            ->get()->toArray();
        foreach ($problemas as $key => $problema) {
            $problemas[$key]->modo = 'online';
        }
        return $problemas;
    }
    private function all_where()
    {
        $problemas = DB::table('goal_problem')
            ->select(
                'id as problema_id',
                'descripcion',
                'estado'
            )
            ->get()->toArray();
        foreach ($problemas as $key => $problema) {
            $problemas[$key]->modo = 'online';
        }
        return $problemas;
    }
    private function all_consecuencia()
    {
        $consecuencias = DB::table('goal_consecuencia')
            ->select(
                'id as consecuencia_id',
                'descripcion',
                'estado',
                'goal_problem_id'
            )
            ->get()->toArray();
        foreach ($consecuencias as $key => $consecuencia) {
            $consecuencias[$key]->modo = 'online';
        }
        return $consecuencias;
    }
    private function all_solucion()
    {
        $soluciones = DB::table('goal_solucion')
            ->select(
                'id as solucion_id',
                'descripcion',
                'estado',
                'goal_consecuencia_id'
            )
            ->get()->toArray();
        foreach ($soluciones as $key => $solucion) {
            $soluciones[$key]->modo = 'online';
        }
        return $soluciones;
    }
    private function all_images_visit_report($array_visit_report)
    {
        $resultado = [];
        foreach ($array_visit_report as $key => $visitreport) {
            $images_visit_report = DB::table('goal_imagen')
                ->where('id_informe_proyecto', $visitreport->informe_id)
                ->get();

            $images = $this->descomponer_images_visit_report($images_visit_report, 'images');
            $resultado = $this->unir_arrays($images, $resultado);
        }
        return $resultado;
    }
    private function descomponer_images_visit_report($array_images, $tipo)
    {
        $images = [];
        foreach ($array_images as $value) {
            if ($value->tipo == $tipo) {
                try {
                    $path = public_path() . '/uploads/' . $value->imagen;
                    $extencion = pathinfo($path, PATHINFO_EXTENSION);
                    $image = base64_encode(file_get_contents($path));
                    $images[] = array(
                        "modo" => 'online',
                        "filepath" => $value->caption,
                        "webviewPath" => "data:image/$extencion;base64, $image",
                        "informe_id" => $value->id_informe_proyecto,
                        "t_imagen_id" => $value->t_imagen_id,
                    );
                } catch (\Throwable $th) {
                }
            }
        }

        return $images;
    }

}
