<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use DB;
use File;
use Illuminate\Http\Request;
use Image;
use \stdClass;

class Sincronizacion extends Controller
{
    private $version = 'version 1';
    public function index(Request $request)
    {
        $fecha_sistema = DB::table('configuration')->selectRaw('now() as fecha')->where('id', 1)->first();
        $fecha_sistema = $fecha_sistema->fecha;
        /*construcion de informacion*/
        $sync = new stdClass();
        $sync->codigo = $this->version;
        $sync->fecha = $fecha_sistema;
        /* modulos */
        $sync->tickets = $this->ticket($request->fecha, $fecha_sistema);
        $sync->tickets_materiales = $this->tickets_materiales($request->fecha, $fecha_sistema);
        $sync->tickets_trabajadores = $this->tickets_trabajadores($request->fecha, $fecha_sistema);
        $sync->actividades = $this->ticket_actividades($request->fecha, $fecha_sistema);
        $sync->imagesTicket = $this->ticket_imagenes($request->fecha, $fecha_sistema);
        /*AUTH personal */
        $sync->personal = $this->personal($request->fecha, $fecha_sistema);
        /* visit report */
        $sync->visit_report = $this->visit_report($request->fecha, $fecha_sistema);
        /* asistencia */
        $sync->registro_diarios = $this->registro_diario($request->fecha, $fecha_sistema);
        $sync->asistencia_actividad = $this->asistencia_actividad($request->fecha, $fecha_sistema);
        $sync->registro_diario_actividad = $this->registro_diario_actividad($request->fecha, $fecha_sistema);
        return response()->json($sync, 200);
    }
    /*personal */
    private function personal($fecha_aplicacion, $fecha_sistema)
    {
        $personas = $this->personal_db($fecha_aplicacion, $fecha_sistema, true);
        $persona = new stdClass();
        $persona->eliminado = $this->identificarEliminados($this->eliminados($fecha_aplicacion, $fecha_sistema, 'personal'), 'personal', 'eliminado');
        $persona->nuevo = $this->descomponerTipo($personas, "nuevo");
        $persona->editado = $this->descomponerTipo($personas, "editado");
        return $persona;
    }

    /* ticket */
    private function ticket_actividades($fecha_aplicacion, $fecha_sistema)
    {
        $ticket_actividades = $this->ticket_actividades_db($fecha_aplicacion, $fecha_sistema, true);
        $ticket_actividad = new stdClass();
        $ticket_actividad->eliminado = $this->identificarEliminados($this->eliminados($fecha_aplicacion, $fecha_sistema, 'actividades'), 'actividades', 'eliminado');
        $ticket_actividad->nuevo = $this->descomponerTipo($ticket_actividades, "nuevo");
        $ticket_actividad->editado = $this->descomponerTipo($ticket_actividades, "editado");
        return $ticket_actividad;
    }
    private function ticket_imagenes($fecha_aplicacion, $fecha_sistema)
    {
        $ticket_imagenes = $this->ticket_imagenes_db($fecha_aplicacion, $fecha_sistema, true);
        $ticket_imagen = new stdClass();
        $ticket_imagen->eliminado = $this->identificarEliminados($this->eliminados($fecha_aplicacion, $fecha_sistema, 'ticket_imagen'), 'ticket_imagen', 'eliminado');
        $ticket_imagen->nuevo = $this->descomponerTipo($ticket_imagenes, "nuevo");
        $ticket_imagen->editado = $this->descomponerTipo($ticket_imagenes, "editado");
        return $ticket_imagen;
    }
    private function tickets_trabajadores($fecha_aplicacion, $fecha_sistema)
    {
        $ticket_trabajadores = $this->tickets_trabajadores_db($fecha_aplicacion, $fecha_sistema, true);
        $ticket_trabajador = new stdClass();
        $ticket_trabajador->eliminado = $this->identificarEliminados($this->eliminados($fecha_aplicacion, $fecha_sistema, 'ticket_trabajadores'), 'ticket_trabajadores', 'eliminado');
        $ticket_trabajador->nuevo = $this->descomponerTipo($ticket_trabajadores, "nuevo");
        $ticket_trabajador->editado = $this->descomponerTipo($ticket_trabajadores, "editado");
        return $ticket_trabajador;
    }
    private function tickets_materiales($fecha_aplicacion, $fecha_sistema)
    {
        $ticket_materiales = $this->tickets_materiales_db($fecha_aplicacion, $fecha_sistema, true);
        $ticket_material = new stdClass();

        $ticket_material->eliminado = $this->identificarEliminados($this->eliminados($fecha_aplicacion, $fecha_sistema, 'ticket_material'), 'ticket_material', 'eliminado');
        $ticket_material->nuevo = $this->descomponerTipo($ticket_materiales, "nuevo");
        $ticket_material->editado = $this->descomponerTipo($ticket_materiales, "editado");
        return $ticket_material;
    }
    private function ticket($fecha_aplicacion, $fecha_sistema)
    {
        $tickets = $this->ticket_db($fecha_aplicacion, $fecha_sistema, true);
        $ticket = new stdClass();

        $ticket->eliminado = $this->identificarEliminados($this->eliminados($fecha_aplicacion, $fecha_sistema, 'ticket'), 'ticket', 'eliminado');
        $ticket->nuevo = $this->descomponerTipo($tickets, "nuevo");
        $ticket->editado = $this->descomponerTipo($tickets, "editado");
        return $ticket;
    }
    /* personal */
    private function personal_db($fecha_aplicacion, $fecha_sistema)
    {
        $persona = DB::table('personal')->selectRaw(
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
            ->whereBetween('appsync.fecha', [$fecha_aplicacion, $fecha_sistema])
            ->where('appsync.tabla', 'personal')
            ->join('appsync', 'appsync.id', 'personal.Empleado_ID')
            ->get();
        return $persona;
    }
    /* ticket */
    private function ticket_actividades_db($fecha_aplicacion, $fecha_sistema)
    {
        $actividad = DB::table('actividades')->selectRaw("
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
            ->whereBetween('appsync.fecha', [$fecha_aplicacion, $fecha_sistema])
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
        return $actividad;
    }
    private function ticket_imagenes_db($fecha_aplicacion, $fecha_sistema)
    {
        $tickets_images = DB::table('ticket_imagen')
            ->select(
                'appsync.evento',
                'ticket_imagen.t_imagen_id',
                'ticket_imagen.ticket_id',
                'ticket_imagen.imagen',
                'ticket_imagen.tipo',
                'ticket_imagen.caption'
            )
            ->whereBetween('appsync.fecha', [$fecha_aplicacion, $fecha_sistema])
            ->where('appsync.tabla', 'ticket_imagen')
            ->Join('appsync', 'appsync.id', 'ticket_imagen.t_imagen_id')
            ->get();
        return $tickets_images;
    }
    private function tickets_trabajadores_db($fecha_inicial, $fecha_final)
    {
        $tickets_trabajadores = DB::table('ticket_trabajadores')->selectRaw(
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
            ->whereBetween('appsync.fecha', [$fecha_inicial, $fecha_final])
            ->where('appsync.tabla', 'ticket_trabajadores')
            ->Join('appsync', 'appsync.id', 'ticket_trabajadores.id')
            ->join('tipo_trabajo', 'tipo_trabajo.id', 'ticket_trabajadores.profesion_id')
            ->get();

        return $tickets_trabajadores;
    }
    private function tickets_materiales_db($fecha_inicial, $fecha_final)
    {
        $tickets_material = DB::table('ticket_material')
            ->select(
                'appsync.evento',
                'materiales.Denominacion as descripcion',
                'ticket_material.id as id_ticket_material',
                'ticket_material.ticket_id',
                'ticket_material.material_id as id_material',
                'materiales.Unidad_Medida as unidad_medida',
                'ticket_material.cantidad'
            )
            ->whereBetween('appsync.fecha', [$fecha_inicial, $fecha_final])
            ->where('appsync.tabla', 'ticket_material')
            ->Join('appsync', 'appsync.id', 'ticket_material.id')
            ->join('materiales', 'ticket_material.material_id', 'materiales.Mat_ID')
            ->get();
        return $tickets_material;
    }

    private function ticket_db($fecha_inicial, $fecha_final)
    {
        $ticket = DB::table('ticket')->selectRaw(
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
            ->whereBetween('appsync.fecha', [$fecha_inicial, $fecha_final])
            ->where('appsync.tabla', 'ticket')
            ->join('appsync', 'appsync.id', 'ticket.ticket_id')
            ->join('proyectos', 'ticket.proyecto_id', 'proyectos.Pro_ID')
            ->join('personal', 'personal.Empleado_ID', 'ticket.empleado_id')
            ->join('empresas', 'empresas.Emp_ID', 'proyectos.Emp_ID')
            ->get();
        $ticket = $this->descomponerFirmas($ticket);
        return $ticket;
    }
    private function descomponerFirmas($tickets)
    {
        foreach ($tickets as $value) {
            $value->signarute_foreman = $this->Base64($value->signarute_foreman, 'empleoye');
            $value->signature_superintendente = $this->Base64($value->signature_superintendente, 'client');
        }
        return $tickets;
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
    /* asistencia */
    private function registro_diario($fecha_aplicacion, $fecha_sistema)
    {
        $registro_diario = $this->registro_diario_db($fecha_aplicacion, $fecha_sistema, true);
        $List_registro_diario = new stdClass();
        $List_registro_diario->eliminado = $this->identificarEliminados($this->eliminados($fecha_aplicacion, $fecha_sistema, 'registro_diario'), 'registro_diario', 'eliminado');
        $List_registro_diario->nuevo = $this->descomponerTipo($registro_diario, "nuevo");
        $List_registro_diario->editado = $this->descomponerTipo($registro_diario, "editado");
        return $List_registro_diario;
    }
    private function asistencia_actividad($fecha_aplicacion, $fecha_sistema)
    {
        //$registro_diario = $this->asistencia_actividad_db($fecha_aplicacion, $fecha_sistema, true);
        $list_asistencia_actividad = new stdClass();
        $list_asistencia_actividad->eliminado = []; //$this->identificarEliminados($this->eliminados($fecha_aplicacion, $fecha_sistema, 'registro_diario'), 'registro_diario', 'eliminado');
        $list_asistencia_actividad->nuevo = []; //$this->descomponerTipo($registro_diario, "nuevo");
        $list_asistencia_actividad->editado = []; //$this->descomponerTipo($registro_diario, "editado");
        return $list_asistencia_actividad;
    }
    private function registro_diario_actividad($fecha_aplicacion, $fecha_sistema)
    {
        //$registro_diario = $this->asistencia_actividad_db($fecha_aplicacion, $fecha_sistema, true);
        $list_registro_diario_actividad = new stdClass();
        $list_registro_diario_actividad->eliminado = []; //$this->identificarEliminados($this->eliminados($fecha_aplicacion, $fecha_sistema, 'registro_diario'), 'registro_diario', 'eliminado');
        $list_registro_diario_actividad->nuevo = []; //$this->descomponerTipo($registro_diario, "nuevo");
        $list_registro_diario_actividad->editado = []; //$this->descomponerTipo($registro_diario, "editado");
        return $list_registro_diario_actividad;
    }
    private function registro_diario_db($fecha_inicial, $fecha_final)
    {

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
            ->whereBetween('appsync.fecha', [$fecha_inicial, $fecha_final])
            ->where('appsync.tabla', 'registro_diario')
            ->Join('appsync', 'appsync.id', 'registro_diario.Reg_ID')
            ->addSelect('appsync.evento')
            ->join('personal', 'registro_diario.Empleado_ID', 'personal.Empleado_ID')
            ->orderBy('registro_diario.Reg_ID', 'DESC')
            ->groupBy('registro_diario.Reg_ID')
            ->get();

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
            $registros_diario[$key] = $registro;
            $registros_diario[$key]->modo = 'online';
        }

        $registros_diario = $this->descomponerAsistenciaSync($registros_diario);

        return $registros_diario;
    }
    public function descomponerAsistenciaSync($registroDiario)
    {
        foreach ($registroDiario as $key => $value) {

            $registroDiario[$key] = $value;
            $registroDiario[$key]->foto_ingreso = $this->obtenerImagesAsistencia($value->foto_ingreso);
            $registroDiario[$key]->foto_salida = $this->obtenerImagesAsistencia($value->foto_salida);
        }
        return $registroDiario;
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

                if ($fileSize < 100000) {
                    $actual_image = Image::make($masArriba);
                    $height = $actual_image->height() / 4;
                    $width = $actual_image->width() / 4;
                    $actual_image->resize($width, $height);
                    $image = base64_encode(file_exists($actual_image));
                    return "data:image/$extencion;base64, $image";
                } else {
                    $image = base64_encode(file_get_contents($masArriba));
                    return "data:image/$extencion;base64, $image";
                }
            } catch (\Throwable $th) {
                return "data:image/$extencion;base64, $image";
            }
        }
    }
    private function eliminados($fecha_aplicacion, $fecha_sistema, $tabla)
    {
        $eliminados = DB::table('appsync')->select(
            'appsync.id',
            'appsync.evento',
            'appsync.tabla',
        )
            ->where('appsync.tabla', $tabla)
            ->where('appsync.evento', 'eliminado')
            ->whereBetween('appsync.fecha', [$fecha_aplicacion, $fecha_sistema])
            ->get();
        return $eliminados;
    }
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
    /* data */
    private function visit_report($fecha_aplicacion, $fecha_sistema)
    {
        $visit_report = $this->visit_report_db($fecha_aplicacion, $fecha_sistema, true);

        $report_visit = new stdClass();
        $report_visit->eliminado = $this->identificarEliminados($this->eliminados($fecha_aplicacion, $fecha_sistema, 'visit_report'), 'visit_report', 'eliminado');
        $report_visit->nuevo = $this->descomponerTipo($visit_report, "nuevo");
        $report_visit->editado = $this->descomponerTipo($visit_report, "editado");
        return $report_visit;
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
                return $query->whereBetween('appsync.fecha', [$fecha_inicial, $fecha_final])
                    ->where('appsync.tabla', 'visit_report')
                    ->Join('appsync', 'appsync.id', 'informe_proyecto.Informe_ID')
                    ->addSelect('appsync.evento');
            })
            ->when(!$sincronizacion, function ($query) use ($fecha_inicial, $fecha_final) {
                return $query->whereBetween('informe_proyecto.Fecha', [$fecha_inicial, $fecha_final])
                    ->groupBy('informe_proyecto.informe_id');
            })
            ->get();

        foreach ($visit_report as $key => $report) {
            $visit_report[$key]->modo = 'online';

            $images = DB::table('goal_imagen')
                ->select(
                    't_imagen_id',
                    'imagen',
                    'tipo',
                    'id_informe_proyecto',
                    'caption',
                    'size'
                )
                ->where('goal_imagen.id_informe_proyecto', $report->informe_id)
                ->get();
            $visit_report[$key]->images = $this->descomponerImage($images);
        }
        return $visit_report;
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

}
