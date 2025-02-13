<?php

namespace App\Http\Controllers;

use DB;
use File;
use Illuminate\Http\Request;
use Image;
use Validator;

class TipoOrdenMovimientoMaterialController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    /* verificar materiales recibidos */
    private function verificar_materiales_recibidos($pedido_id)
    {
        $materiales_solicitud = DB::table('pedidos_material')
            ->select(
                'pedidos_material.Ped_Mat_ID',
                'pedidos_material.Mat_ID',
                'pedidos_material.Aux1',
                'pedidos_material.Cantidad',
                'materiales.Denominacion',
                'materiales.Unidad_Medida'
            )
            ->where('pedidos_material.Ped_ID', $pedido_id)
            ->join('materiales', 'materiales.Mat_ID', 'pedidos_material.Mat_ID')
            ->get();
        $resultado = [];
        foreach ($materiales_solicitud as $keyMP => $value) {
            $verificar = DB::table('tipo_movimiento_material_pedido')
                ->where('tipo_movimiento_material_pedido.Ped_Mat_ID', $value->Ped_Mat_ID)
                ->get();
            $total = 0;
            foreach ($verificar as $key => $value) {
                $total += $value->ingreso;
            }
            $materiales_solicitud[$keyMP]->recibido = $total;
            if ($materiales_solicitud[$keyMP]->recibido == $materiales_solicitud[$keyMP]->Cantidad) {

            } else {
                $resultado[] = $materiales_solicitud[$keyMP];
            }
            /*aki produce error */
        };
        return $resultado;
    }
    /*fin verificar materiales recibidos */

    /*verificar pedidos con movimientos */
    private function verificar_pedido($pedido_id)
    {
        $pedidos = DB::table('pedidos_material')
            ->select(
                'pedidos_material.Ped_Mat_ID',
                'pedidos_material.Cantidad'
            )
            ->where('pedidos_material.Ped_ID', $pedido_id)
            ->get();
        $resultado = [];

        foreach ($pedidos as $keyP => $pedido) {
            $material_pedido = DB::table('tipo_movimiento_material_pedido')
                ->select(
                    'tipo_movimiento_material_pedido.ingreso'
                )
                ->join('pedidos_material', 'pedidos_material.Ped_Mat_ID', 'tipo_movimiento_material_pedido.Ped_Mat_ID')
                ->where('pedidos_material.Ped_Mat_ID', $pedido->Ped_Mat_ID)
                ->get();

            $total = 0;
            /* suma de ingresos */
            foreach ($material_pedido as $key => $value) {
                $total += $value->ingreso;
            }

            $pedidos[$keyP]->ingreso = $total;
            $resultado[] = $pedidos[$keyP];
        }
        $verificar = 'no completado';
        foreach ($resultado as $key => $value) {
            if ($value->ingreso >= $value->Cantidad) {
                $verificar = 'completado';
            } else {
                if ($value->ingreso != 0) {
                    $verificar = 'parcial recibido';
                    break;
                } else {
                    $verificar = 'no completado';

                }
            }
        }
        switch ($verificar) {
            case 'parcial recibido':
                $actualizando = DB::table('pedidos')
                    ->where('pedidos.Ped_ID', $pedido_id)
                    ->update([
                        'status_id' => 11,
                    ]);
                break;
            case 'completado':
                $actualizando = DB::table('pedidos')
                    ->where('pedidos.Ped_ID', $pedido_id)
                    ->update([
                        'status_id' => 14,
                    ]);
                break;
            case 'no completado':
                $actualizando = DB::table('pedidos')
                    ->where('pedidos.Ped_ID', $pedido_id)
                    ->update([
                        'status_id' => 3,
                    ]);
                break;
            default:
                # code...
                break;
        }
    }

    public function show_recepcion_material(Request $request, $id)
    {
        $sub_orden = DB::table('pedidos')
            ->select(
                DB::raw('DATE_FORMAT(pedidos.Fecha , "%m/%d/%Y %H:%i:%s") as fecha_registro'),
                'pedidos.PO',
                'pedidos.note',
                'pedidos.Ped_ID',
                'pedidos.Ven_ID',
                'pedidos.To_ID',
                'pedidos.tipo_orden_id',
                'proyectos.Nombre as nombre_vendedor',
                'tipo_orden_estatus.nombre as nombre status',
                'tipo_orden_estatus.id as status_id'
            )
            ->where('pedidos.Ped_ID', $id)
            ->join('proyectos', 'proyectos.Pro_ID', 'pedidos.Ven_ID')
            ->join('tipo_orden_estatus', 'tipo_orden_estatus.id', 'pedidos.status_id')
            ->first();

        $orden = DB::table('tipo_orden')
            ->select(
                'tipo_orden.id',
                'tipo_orden.num',
                'proyectos.Codigo',
                'proyectos.Nombre as nombre_trabajo',
                'proyectos.Pro_ID',
                DB::raw("CONCAT( proyectos.Estado,' ', proyectos.Ciudad,' ',proyectos.Calle,' ', proyectos.Numero) as address")
            )
            ->join('proyectos', 'proyectos.Pro_ID', 'tipo_orden.proyecto_id')
            ->where('tipo_orden.id', $sub_orden->tipo_orden_id)
            ->first();
        $materiales_solicitud = $this->verificar_materiales_recibidos($id);
        $to = $this->proveedores($orden->Pro_ID);
        $status = DB::table('tipo_orden_estatus')->where('estado', 1)->orderBy('nombre', 'ASC')->get();

        return response()->json([
            "status" => "ok",
            "orden" => $orden,
            "sub_orden" => $sub_orden,
            "materiales" => $materiales_solicitud,
            "to" => $to,
            "status" => $status,
            "message" => 'verificado'
        ], 200);

    }
    public function store_recepcion_material(Request $request)
    {
        $rules = array(
            'new_recepcion_sub_orden' => 'required',
            'new_recepcion_orden_id' => 'required',
            'new_recepcion_from_vendor' => 'required',
            'new_recepcion_materiales_pedido' => 'required',
            'new_materiales_id' => 'required',
            'new_cantidad_recibida' => 'required',
            'new_status_material_recepcion' => 'required',
            'new_nota_recepcion' => 'required',
            'new_nota_recepcion_sub_orden' => 'required',
            'new_recepcion_to_vendor' => 'required',
            'new_fecha_recepcion_vendor' => 'required',
            'new_fecha_recepcion_traking' => 'required',
        );
        $messages = [
            'new_cantidad_recibida.required' => "The Received amount field is required",
            'new_status_material_recepcion.required' => "The status field is required",
            'new_nota_recepcion.required' => "The Note field is required",
            'new_recepcion_to_vendor.required' => "The To field is required",
            'new_fecha_recepcion_vendor.required' => "The Requested delivery date field is required",
            'new_fecha_recepcion_traking.required' => "The Tracking  field is required",
        ];
        $error = Validator::make($request->all(), $rules, $messages);
        if ($error->errors()->all()) {
            return response()->json([
                "status" => "errors",
                "message" => $error->errors()->all()]);
        }

        foreach ($request->new_cantidad_recibida as $key => $value) {
            $movimiento = DB::table('tipo_movimiento_material_pedido')
                ->select(
                    DB::raw('max(nro_movimiento) as nro_movimiento')
                )
                ->where("Ped_Mat_ID", $request->new_recepcion_materiales_pedido[$key])
                ->first();
            $this->egreso(
                $request->new_status_material_recepcion[$key],
                $request->new_recepcion_materiales_pedido[$key],
                $request->new_materiales_id[$key],
                $request->new_fecha_recepcion_vendor,
                $request->new_fecha_recepcion_traking,
                $request->new_nota_recepcion[$key],
                $request->new_cantidad_recibida[$key],
                $request->new_recepcion_from_vendor,
                ($movimiento->nro_movimiento != null) ? $movimiento->nro_movimiento + 1 : 1
            );
            $this->ingreso(
                $request->new_status_material_recepcion[$key],
                $request->new_recepcion_materiales_pedido[$key],
                $request->new_materiales_id[$key],
                $request->new_fecha_recepcion_vendor,
                $request->new_fecha_recepcion_traking,
                $request->new_nota_recepcion[$key],
                $request->new_cantidad_recibida[$key],
                $request->new_recepcion_to_vendor[$key],
                ($movimiento->nro_movimiento != null) ? $movimiento->nro_movimiento + 1 : 1
            );
        }
        $proveedores = DB::table('pedidos')
            ->where('pedidos.Ped_ID', $request->new_recepcion_sub_orden)
            ->update([
                'status_id' => $request->new_segimiento_vendor_status,
            ]);
        $this->verificar_pedido($request->new_recepcion_sub_orden);
        $this->update_status_orden_automatico($request->new_recepcion_orden_id);
        return response()->json([
            "status" => "ok",
            "message" => 'Registered Successfully',
        ], 200);
    }
    private function verificar_order_to_vendor($status_id, $from, $to)
    {
        /* caso uno de proveedor a wharehouse */
        if ($this->verificar_proveedor($from) && $this->verificar_wharehouse($to)) {
            return 'ingreso';
        }
        /* caso uno de proveedor a proveedor */
        if ($this->verificar_proveedor($from) && $this->verificar_proveedor($to)) {
            return 'ingreso';
        }
        /* caso uno de proveedor a proyecto */
        if ($this->verificar_proveedor($from) && $this->verificar_proyecto($to)) {
            return 'ingreso';
        }
        /* caso uno de wharehouse a proyecto */
        if ($this->verificar_wharehouse($from) && $this->verificar_proyecto($to)) {
            return 'egreso ingreso';
        }
        /* caso uno de proyecto a wharehouse */
        if ($this->verificar_proyecto($from) && $this->verificar_wharehouse($to)) {
            return 'egreso ingreso';
        }
        /* caso uno de wharehouse a proveedor */
        if ($this->verificar_wharehouse($from) && $this->verificar_proveedor($to)) {
            return 'egreso';
        }
        /* caso uno de proyecto a proveedor */
        if ($this->verificar_proyecto($from) && $this->verificar_proveedor($to)) {
            return 'egreso';
        }
        /* caso uno de proyecto a proyecto */
        if ($this->verificar_proyecto($from) && $this->verificar_proyecto($to)) {
            return 'egreso ingreso';
        }
    }
    private function verificar_proveedor($from)
    {
        $verificar_proveeedor = false;
        $proveedores = DB::table('proyectos')
            ->select(
                'proyectos.Pro_ID'
            )
            ->where('proyectos.Emp_ID', 119)
            ->get();
        foreach ($proveedores as $key => $proveedor) {
            if ($proveedor->Pro_ID == $from) {
                $verificar_proveeedor = true;
                break;
            }
        }
        return $verificar_proveeedor;
    }
    private function verificar_wharehouse($from)
    {
        $verificar_wharehouse = false;
        if ($from == '1') {
            $verificar_wharehouse = true;
        }
        return $verificar_wharehouse;
    }
    private function verificar_proyecto($from)
    {
        $verificar_proyecto = false;
        $proveedores = DB::table('proyectos')
            ->select(
                'proyectos.Pro_ID'
            )
            ->where('proyectos.Emp_ID', '<>', 119)
            ->where('proyectos.Pro_ID', '<>', 1)
            ->get();
        foreach ($proveedores as $key => $proveedor) {
            if ($proveedor->Pro_ID == $from) {
                $verificar_proyecto = true;
                break;
            }
        }
        return $verificar_proyecto;
    }
    private function insert_movimiento_pedido($pedido_id, $fecha, $fecha_espera, $nota)
    {
        DB::table('tipo_movimiento_pedido')
            ->where('tipo_movimiento_pedido.Ped_ID', $pedido_id)
            ->insert([
                'fecha' => $fecha,
                'fecha_espera' => $fecha_espera,
                'nota' => $nota,
            ]);
    }
    private function proveedores($proyecto_id)
    {
        $providers = DB::table('proyectos')
            ->select(
                'proyectos.Pro_ID'
            )
            ->where('proyectos.Emp_ID', 119)
            ->get();
        $data = [];
        foreach ($providers as $key => $value) {
            $data[] = $value->Pro_ID;
        }
        $data[] = $proyecto_id;
        $data[] = 1; //warehouse
        $to = DB::table('proyectos')
            ->select(
                'proyectos.Pro_ID as id',
                'proyectos.Nombre as nombre',
                'proyectos.Nombre as nombre_proyecto',
                'empresas.Codigo',
                DB::raw("CONCAT(proyectos.Calle,' ', proyectos.Numero,' ',proyectos.Ciudad,' ',proyectos.Estado,' ', proyectos.Zip_Code) as address")
            )
            ->whereIn('proyectos.Pro_ID', $data)
            ->join('empresas', 'empresas.Emp_ID', 'proyectos.Emp_ID')
            ->get();
        return $to;
    }
    /*ingreso*/
    private function ingreso(
        $status_id,
        $material_pedido_id,
        $material_id,
        $fecha,
        $fecha_espera,
        $nota,
        $ingreso,
        $Pro_ubicacion,
        $nro_movimiento
    ) {
        $movimiento = DB::table('tipo_movimiento_material_pedido')
            ->insertGetId([
                "estatus_id" => $status_id,
                "Ped_Mat_ID" => $material_pedido_id,
                "material_id" => $material_id,
                "fecha" => date('Y-m-d H:i:s', strtotime($fecha)),
                "fecha_espera" => date('Y-m-d H:i:s', strtotime($fecha_espera)),
                "nota" => $nota,
                "estado" => 1,
                "ingreso" => $ingreso,
                "egreso" => 0,
                "Pro_id_ubicacion" => $Pro_ubicacion,
                "nro_movimiento" => $nro_movimiento,
            ]);
    }
    /*egreso*/
    private function egreso(
        $status_id,
        $material_pedido_id,
        $material_id,
        $fecha,
        $fecha_espera,
        $nota,
        $egreso,
        $Pro_ubicacion,
        $nro_movimiento
    ) {

        $movimiento = DB::table('tipo_movimiento_material_pedido')
          
            ->insertGetId([
                "estatus_id" => $status_id,
                "Ped_Mat_ID" => $material_pedido_id,
                "material_id" => $material_id,
                "fecha" => date('Y-m-d H:i:s', strtotime($fecha)),
                "fecha_espera" => date('Y-m-d H:i:s', strtotime($fecha_espera)),
                "nota" => $nota,
                "estado" => 1,
                "egreso" => $egreso,
                "ingreso" => 0,
                "Pro_id_ubicacion" => $Pro_ubicacion,
                "nro_movimiento" => $nro_movimiento,
            ]);
    }
    private function update_status_orden_automatico($order_id)
    {
        /* segunda opcion */
        $pedidos = DB::table('pedidos')
            ->select(
                'pedidos.status_id'
            )
            ->where('tipo_orden_id', $order_id)
            ->get();
        /* primera validacion  si el requerimiento es parcial*/
        if (!$this->verificar_cantidad_ordenada_requerida($order_id)) {
            $this->update_estatus_requerimientos($order_id, 13);
        } else {
            $this->update_estatus_requerimientos($order_id, 3);
        }
        $status = 1;
        foreach ($pedidos as $key => $pedido) {
            if ($pedido->status_id == 7) {
                $status = 7;
                break;
            }
            if ($pedido->status_id == 11) {
                $status = 11;
                break;
            }
            if ($pedido->status_id == 12) {
                $status = 12;
                break;
            }
            if ($pedido->status_id == 3) {
                $status = 3;
            }
            if ($pedido->status_id == 14) {
                $status = 14;
            }
        }
        $this->update_estatus_requerimientos($order_id, $status);

    }
    private function verificar_cantidad_ordenada_requerida($order_id)
    {
        $verficar = DB::table('tipo_orden_materiales')
            ->where('tipo_orden_materiales.tipo_orden_id', $order_id)
            ->get();
        $resultado = false;
        foreach ($verficar as $key => $value) {
            /*  verificar si es la cantidad ordenada en menor a la registrada */
            if ($value->cant_ordenada >= $value->cant_registrada) {
                break;
                return $resultado;
            } else {
                if ($value->cant_ordenada == 0) {
                    return $resultado = true;
                } else {
                    break;
                    return $resultado;
                }
            }
        }
    }

    private function update_estatus_requerimientos($order_id, $status_id)
    {
        $orden = DB::table('tipo_orden')
            ->where('tipo_orden.id', $order_id)
            ->update([
                'estatus_id' => $status_id,
            ]);
    }

    public function get_images($id)
    {
        $images = DB::table('tipo_pedido_imagen')
            ->where('tipo_pedido_imagen.Ped_ID', $id)
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
                    'url' => url("sub-order/delete-images/$val->id"),
                    'key' => $val->id,
                ];
            }
        }
        return response()->json($list);
    }
    public function delete_image($id, Request $request)
    {
        $imagen = DB::table('tipo_pedido_imagen')
            ->where('id', $id)
            ->first();
        // dd($imagen);
        if ($imagen) {
            $path = public_path() . '/uploads/' . $imagen->imagen;
            if (File::exists($path) && $imagen->imagen) {
                File::delete($path);
            }
            $delete = DB::table('tipo_pedido_imagen')
                ->where('id', $id)
                ->delete();
            return response()->json([
                'success' => 'Successfully removed the image',
            ]);
        }

        return response()->json([
            'error' => 'Error, the image could not be deleted',
        ]);
    }
    public function upload_image($id, Request $request, $nombre_camp = null)
    {
        $campo = ($nombre_camp) ? $nombre_camp : 'recibir';

        $preview = $config = $errors = [];

        if ($request->hasFile($campo)) {
            //dd('es valido', $check);
            $allowedfileExtension = ['jpg', 'png', 'jpeg'];
            $files = $request->file($campo);
            foreach ($files as $file) {
                $filename = $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension();
                $fileSize = $file->getSize();
                $check = in_array($extension, $allowedfileExtension);
                //dd('es valido', $check);
                if ($check) {
                    $name_img = "sub-orden-image-$id-" . uniqid() . time() . "." . $extension;
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
                    $insert_pedido_imagen = DB::table('tipo_pedido_imagen')
                        ->insertGetId([
                            "imagen" => $name_img,
                            "Ped_ID" => $id,
                            "caption" => $filename,
                            "size" => $fileSize,
                        ]);
                    if ($insert_pedido_imagen) {
                        $newFileUrl = url('/') . '/uploads/' . $name_img;
                        $preview[] = $newFileUrl;
                        $config[] = [
                            'key' => $insert_pedido_imagen,
                            'caption' => $filename,
                            'size' => $fileSize,
                            'downloadUrl' => $newFileUrl, // the url to download the file
                            'url' => url("/sub-order/delete-images/$insert_pedido_imagen"), // server api to delete the file based on key
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
}
