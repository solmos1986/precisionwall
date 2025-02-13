<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Personal;
use App\RegistroDiario;
use DB;
use File;
use Illuminate\Http\Request;
use Image;
use \stdClass;

class AsistenciaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function personalActividad(Request $request)
    {
        $personal = RegistroDiario::selectRaw(
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
            ->Join('personal', 'registro_diario.Empleado_ID', 'personal.Empleado_ID')
            ->where('registro_diario.Actividad_ID', $request->actividad_id)
            ->where('registro_diario.Fecha', $request->fecha)
            ->orderBy('nombre', 'ASC')
            ->get();
        $registro_diario = $this->descomponerAsistencia($personal);
        return response()->json($registro_diario, 200);
    }

    public function SaveRegistroDiarioCheck(Request $request)
    {
        $resultado = [];
        if ($request->hasFile("foto_salida")) {
            $registro_diario = RegistroDiario::where('registro_diario.Reg_ID', $request->registro_diario_id)
                ->first();

            if ($registro_diario && $registro_diario->Foto_Salida === null) {
                $registro_salida = RegistroDiario::where('registro_diario.Reg_ID', $request->registro_diario_id)
                    ->update([
                        "Pregunta_OUT" => $request->pregunta_salida,
                        "Clave_Digitada_Out" => $request->respuesta_salida,
                        "Hora_Salida" => $request->hora_salida,
                        "Fecha_Hsalida" => $request->fecha_salida,
                        "Foto_Salida" => $this->verificarImage($request, "foto_salida"),
                        //pass  Usuario_Pass
                    ]);
                $resultado[] = "salida";
            }
        }
        if ($request->hasFile("foto_ingreso")) {
            $registro_diario = RegistroDiario::where('registro_diario.Reg_ID', $request->registro_diario_id)
                ->first();
            if ($registro_diario && $registro_diario->Foto_Ingreso === null) {
                $registro_ingreso = RegistroDiario::where('registro_diario.Reg_ID', $request->registro_diario_id)
                    ->update([
                        "Pregunta_IN" => $request->pregunta_ingreso,
                        "Clave_Digitada_In" => $request->respuesta_ingreso,
                        "Hora_Ingreso" => $request->hora_ingreso,
                        "Fecha_Hingreso" => $request->fecha_ingreso,
                        "Foto_Ingreso" => $this->verificarImage($request, "foto_ingreso"),
                        //pass  Usuario_Pass
                    ]);
                $resultado[] = "entrada";
            }
        }
        return response()->json($resultado, 200);
    }
    private function verificarImage(Request $request, $tipo)
    {
        try {
            if ($request->hasFile($tipo)) {
                $allowedfileExtension = ['jpg', 'png', 'jpeg'];
                $files = $request->file($tipo);
                foreach ($files as $file) {
                    $filename = $file->getClientOriginalName();
                    $extension = $file->getClientOriginalExtension();
                    $fileSize = $file->getSize();
                    $check = in_array($extension, $allowedfileExtension);
                    if ($check) {
                        $name_img = uniqid() . time() . "." . $extension;
                        $path = base_path();
                        $masArriba = dirname($path) . "/pwt/fotos/" . $name_img;

                        Image::make(file_get_contents($file))->save($masArriba);
                    }
                    return $name_img;
                }
            } else {
                return null;
            }
        } catch (\Throwable $th) {
            return null;
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
            $floor = DB::table('edificios')
                ->selectRaw(
                    "floor.Floor_ID as floor_id,
                floor.Nombre as nombre"
                )
                ->Join('floor', 'edificios.Edificio_ID', 'floor.Edificio_ID')
                ->where('edificios.Pro_ID', $value->proyecto_id)
                ->first();
            $registro_diario->floor_id = $floor->floor_id;
            $registro_diario->floor = $floor->nombre;
            $resultado[] = $registro_diario;
        }
        return $resultado;
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

    public function loginCheckInOut(Request $request)
    {
        $personal = Personal::selectRaw(
            "Empleado_ID as empleado_id,
            CONCAT(COALESCE(personal.Nombre,''), ' ',  COALESCE(personal.Apellido_Paterno,''), ' ',  COALESCE(personal.Apellido_Materno,'')) as descripcion,
            Usuario as usuario,
            Password as password,
            Cargo as cargo,
            Emp_ID as empresa_id,
            email,
            Rol_ID as rol
            "
        )
            ->where('status', 1)
            ->where('usuario', $request->usuario)
            ->where('password', $request->contraseña)
            ->first();
        if (!$personal) {
            $personal = false;
        }
        return response()->json($personal, 200);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function get_new_report_personal(Request $request)
    {
        $actividadesPersonal = DB::table('actividad_personal')
            ->selectRaw(
                "
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
            personal.Nick_Name as nickname,
            CONCAT(COALESCE(personal.Nombre,''), ' ',  COALESCE(personal.Apellido_Paterno,''), ' ',  COALESCE(personal.Apellido_Materno,'')) as nombre,
            actividad_personal.Note as note"
            )
            ->Join('personal', 'actividad_personal.Empleado_ID', 'personal.Empleado_ID')
            ->Join('registro_diario', function ($q) {
                $q->on('registro_diario.Actividad_Id', '=', 'actividad_personal.Actividad_ID')
                    ->on('registro_diario.Empleado_ID', '=', 'actividad_personal.Empleado_ID');
            })
            ->where('actividad_personal.Actividad_ID', $request->actividad_id)
            ->get();

        foreach ($actividadesPersonal as $key => $value) {
            $floor = DB::table('edificios')
                ->selectRaw(
                    "floor.Floor_ID as floor_id,
            floor.Nombre as nombre"
                )
                ->Join('floor', 'edificios.Edificio_ID', 'floor.Edificio_ID')
                ->where('edificios.Pro_ID', $value->proyecto_id)
                ->first();
            $actividadesPersonal[$key]->floor_ID = $floor->floor_id;
            $actividadesPersonal[$key]->floor = $floor->nombre;
        }
        return response()->json($actividadesPersonal, 200);
    }
    public function get_report_personal(Request $request)
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
            ->where('actividad_personal.Actividad_ID', $request->actividad_id)
            ->get();
        $floor = DB::table('edificios')
            ->selectRaw(
                "floor.Floor_ID as floor_id,
            floor.Nombre as nombre"
            )
            ->Join('floor', 'edificios.Edificio_ID', 'floor.Edificio_ID')
            ->where('edificios.Pro_ID', $request->proyecto_id)
            ->first();

        $actividadesPersonal = $this->detalle_registro($actividadesPersonal, $floor);
        return response()->json($actividadesPersonal, 200);
    }

    private function detalle_registro($actividadesPersonal, $floor)
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
            $resultado->floor_id = $floor->floor_id;
            $resultado->task_id = $value->task_id;
            $resultado->floor = $floor->nombre;
            if ($value->verificar_foreman == '0' && $value->verificar_foreman == null) {
                $resultado->verificar_foreman = false;
            } else {
                $resultado->verificar_foreman = true;
            }
            $data[] = $resultado;
        }
        return $data;
    }
    public function area_control(Request $request)
    {
        $area_control = DB::table('area_control')
            ->selectRaw(
                "area_control.area_ID as area_id,
            area_control.Nombre as nombre"
            )
            ->where('area_control.Floor_ID', $request->floor_id)
            ->where('area_control.pro_ID', $request->proyecto_id)
            ->get();
        return response()->json($area_control, 200);
    }
    public function area_task(Request $request)
    {
        $task = DB::table('task')
            ->selectRaw(
                "task.Task_ID as task_id,
            task.Nombre as nombre
            "
            )
            ->where('task.Area_ID', $request->area_id)
            ->get();
        return response()->json($task, 200);
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function store_report_personal(Request $request)
    {
        $data = [];
        foreach ($request->all() as $key => $value) {
            $verificando = DB::table('registro_diario_actividad')
                ->where('registro_diario_actividad.Reg_ID', $value["registro_diario_id"])
                ->first();
            if ($verificando->Horas_Contract == '0' && $verificando->Task_ID == null) {
                $registro_diario_actividad = DB::table('registro_diario_actividad')
                    ->where('registro_diario_actividad.Reg_ID', $value["registro_diario_id"])
                    ->update([
                        "task_id" => $value["task_id"],
                        "Horas_Contract" => $value["hora_worker"],
                        "Verificado_Foreman" => $value["verificar_foreman"],
                        "Actividad_ID" => $value["actividad_id"],
                        "Empleado_ID" => $value["empleado_id"],
                        "Detalles" => $value["note"],
                    ]);
                $actividad_personal = DB::table('actividad_personal')
                    ->where('actividad_personal.Actividad_ID', $value["actividad_id"])
                    ->where('actividad_personal.Empleado_ID', $value["empleado_id"])
                    ->update([
                        "HContract" => $value["hora_worker"],
                        //"Note" => $value["note"],
                    ]);
                $data[] = $value["empleado_id"];
                return response()->json([
                    'status' => 200,
                    'message' => 'Registered Successfully',
                    'data' => [],
                ], 200);
            } else {
                return response()->json([
                    'status' => 200,
                    'message' => 'Has already been registered',
                    'data' => [],
                ], 200);
            }
        }

    }
    public function store(Request $request)
    {
        //
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
        //
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
        //
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
}
