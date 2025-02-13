<?php

namespace App\Http\Controllers;

use App\Evento;
use App\Movimiento_evento;
use App\Personal;
use DataTables;
use File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Image;
use Storage;
use Validator;
use \stdClass;

class MovimientoEventoController extends Controller
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

    public function index()
    {
        //
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
    public function get_event(Request $request)
    {
        $data = [];
        if (!isset($request->searchTerm)) {
            $eventos = Evento::where('evento.estado', '1')
                ->where('tipo_evento.estado', '1')
                ->select(
                    'evento.*',
                    'tipo_evento.nombre as tipo_nombre'
                )
                ->join('tipo_evento', 'tipo_evento.tipo_evento_id', 'evento.tipo_evento_id')
                ->orderBy('nombre', 'ASC')
                ->get();
        } else {
            $eventos = Evento::where('evento.estado', '1')
                ->where('tipo_evento.estado', '1')
                ->select(
                    'evento.*',
                    'tipo_evento.nombre as tipo_nombre'
                )
                ->join('tipo_evento', 'tipo_evento.tipo_evento_id', 'evento.tipo_evento_id')
                ->where('nombre', 'like', '%' . $request->searchTerm . '%')->get();
        }
        foreach ($eventos as $row) {
            $data[] = array(
                "id" => $row->cod_evento,
                "text" => $row->nombre,
                "duracion_day" => $row->duracion_day,
                "report_alert" => $row->report_alert,
                "tipo_evento" => $row->tipo_nombre,
            );
        }
        return response()->json($data);
    }

    public function createAll(Request $request)
    {
        //dd($request->all());
        $rules = array(
            'personal' => 'required',
            'event' => 'required',
            'fecha_inicio' => 'required|date_format:m/d/Y',
            'fecha_fin' => 'required|date_format:m/d/Y',
            'note' => 'nullable',
            'docs' => 'nullable',
        );

        $messages = [
            'personal.required' => "The 'select Employees' field is required",
            'event.required' => "The 'event' field is required",
            'fecha_inicio.required' => "The 'start date' last name field is required",
            'fecha_inicio.date_format' => "Invalid date format must be month / day / year",
            'fecha_fin.required' => "The 'start date' last name field is required",
            'fecha_fin.date_format' => "Invalid date format must be month / day / year",
            'note.required' => "The 'note' field is required",
        ];
        //validacion de de datos
        $error = Validator::make($request->all(), $rules, $messages);
        if (request()->ajax() === true) {
            if ($error->errors()->all()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $error->errors()->all(),
                ]);
            }
        }
        $personas = explode(',', $request->personal);
        $movimientos = [];
        //insertando datos en terceras tablas
        foreach ($personas as $value) {
            $movimiento_evento = Movimiento_evento::insertGetId([
                "cod_evento" => $request->event,
                "Empleado_ID" => $value,
                "start_date" => date('Y-m-d', strtotime($request->fecha_inicio)),
                "exp_date" => date('Y-m-d', strtotime($request->fecha_fin)),
                "note" => $request->note,
                "raise_from" => "",
                "raise_to" => "",
                //"doc_pdf" => $url_archivo,
                "estado" => "1",
            ]);
            $movimientos[] = $movimiento_evento;
        }
        //guardando archivo
        if ($request->docs) {
            if ($request->file('docs')) {
                $files = $this->upload_image_multiple($request, 'docs');
                foreach ($files as $key => $file) {
                    foreach ($movimientos as $key => $id) {
                        $insert = DB::table('movimientos_eventos_archivos')->insertGetId([
                            'imagen' => $file->name_img,
                            'tipo' => $file->type,
                            'movimientos_eventos_id' => $id,
                            'caption' => $file->filename,
                            'size' => $file->fileSize,
                        ]);
                    }
                }

            }
        }
        return response()->json([
            'status' => 'ok',
            'message' => 'Event assigned to many successful employees',
        ]);

    }

    /**
     * validar_fecha_espanol mes / dia / año
     *  evalua y procesa para verificar una fecha por input
     * @param  mixed $fecha
     * @return boolean
     */
    public function validar_fecha_espanol($fecha)
    {
        $valores = explode('/', $fecha);
        if (count($valores) == 3 && checkdate($valores[0], $valores[1], $valores[2])) {
            if (strlen($valores[2]) === 4) {
                return true;
            }
        }
        return false;
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
            'new_Empleado_ID' => 'required',
            'new_event' => 'required',
            'new_fecha_inicio' => 'required|date_format:m/d/Y',
            'new_fecha_fin' => 'required|date_format:m/d/Y',
            'new_note' => 'required',
            'new_docs' => 'nullable',
        );

        $messages = [
            'new_Empleado_ID.required' => "The 'select Employee' field is required",
            'new_event.required' => "The 'event' field is required",
            'new_fecha_inicio.required' => "The 'start date' last name field is required",
            'new_fecha_inicio.date_format' => "Invalid date format must be month / day / year",
            'new_fecha_fin.required' => "The 'start end' last name field is required",
            'new_fecha_fin.date_format' => "Invalid date end format must be month / day / year",
            'new_note.required' => "The 'note' field is required",
        ];
        //validacion de de datos
        $error = Validator::make($request->all(), $rules, $messages);
        if (request()->ajax() === true) {
            if ($error->errors()->all()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $error->errors()->all(),
                ]);
            }
        }

        $evento_dias = Evento::select('duracion_day')
            ->where('cod_evento', $request->new_event)
            ->pluck('duracion_day')->first();

        //dd($url_archivo);
        $movimiento_evento = Movimiento_evento::insertGetId([
            "cod_evento" => $request->new_event,
            "Empleado_ID" => $request->new_Empleado_ID,
            "start_date" => date('Y-m-d', strtotime($request->new_fecha_inicio)),
            "exp_date" => date('Y-m-d', strtotime($request->new_fecha_fin)),
            "note" => $request->new_note,
            "raise_from" => "",
            "raise_to" => "",
            //"doc_pdf" => $url_archivo,
            "estado" => "1",
        ]);
        //guardando archivo
        $this->upload_image($request, $movimiento_evento, 'new_docs');
        return response()->json([
            'status' => 'ok',
            'message' => 'Event saved successfully',
        ]);
    }

    public function upload($file)
    {
        $filename = uniqid() . time() . '_' . $file->getClientOriginalName();

        $path = public_path();
        // File upload location
        $location = 'docs';

        // Upload file
        $file->move(public_path('docs'), $filename);

        return $filename;
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $eventos = explode(',', $request->query('evento'));
        $user = Personal::select(
            'personal.Nick_Name',
            DB::raw('CONCAT(personal.Nombre," ",personal.Apellido_Paterno," ",personal.Apellido_Materno) AS nombre_completo'),
        )
            ->where('Empleado_ID', $id)
            ->first();
        $movimiento = Movimiento_evento::select(
            'evento.nombre',
            'movimientos_eventos.note',
            DB::raw('DATE_FORMAT(movimientos_eventos.start_date, "%m/%d/%Y") as start_date '),
            DB::raw('DATE_FORMAT(movimientos_eventos.exp_date, "%m/%d/%Y") as exp_date '),
            'evento.duracion_day'
        )
            ->where('Empleado_ID', $id)
            ->where('movimientos_eventos.estado', '1')
            ->whereIn('evento.cod_evento', $eventos)
            ->orderBy('movimientos_eventos.exp_date', 'DESC')
            ->join('evento', 'evento.cod_evento', 'movimientos_eventos.cod_evento')->get();

        return response()->json([
            "user" => $user->Nick_Name,
            "nombre" => $user->nombre_completo,
            "eventos" => $movimiento,
        ], 200);
    }
    public function show_data_table($id)
    {
        $data = Movimiento_evento::select(
            'movimientos_eventos.movimientos_eventos_id',
            'evento.nombre',
            'movimientos_eventos.note',
            'tipo_evento.nombre as type_event',
            DB::raw('DATE_FORMAT(movimientos_eventos.start_date, "%m/%d/%Y") as start_date'),
            DB::raw('DATE_FORMAT(movimientos_eventos.exp_date, "%m/%d/%Y") as exp_date'),
            'evento.duracion_day'
        )->where('Empleado_ID', $id)
            ->where('movimientos_eventos.estado', '1')
            ->join('evento', 'evento.cod_evento', 'movimientos_eventos.cod_evento')
            ->join('tipo_evento', 'tipo_evento.tipo_evento_id', 'evento.tipo_evento_id')
            ->orderBy('movimientos_eventos.exp_date', 'Desc')
            ->get();

        return Datatables::of($data)
            ->addIndexColumn()
            ->addColumn('acciones', function ($data) {
                $button = "
                <i class='fas fa-pencil-alt ms-text-warning cursor-pointer edit_evento' data-id='$data->movimientos_eventos_id' title='Edit event'></i>
                <i class='far fa-trash-alt ms-text-danger cursor-pointer delete_evento' data-id='$data->movimientos_eventos_id' title='Delete'></i>
                ";
                return $button;
            })
            ->rawColumns(['acciones'])
            ->make(true);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = new stdClass();

        $movimiento_evento = Movimiento_evento::select(
            'movimientos_eventos.Empleado_ID',
            'movimientos_eventos.cod_evento',
            DB::raw('DATE_FORMAT(movimientos_eventos.exp_date, "%m/%d/%Y") as exp_date '),
            'movimientos_eventos.movimientos_eventos_id',
            'movimientos_eventos.note',
            DB::raw('DATE_FORMAT(movimientos_eventos.start_date, "%m/%d/%Y") as start_date '),
            'movimientos_eventos.doc_pdf',
            'evento.nombre',
            'evento.duracion_day',
            'evento.report_alert',
            'tipo_evento.nombre as tipo_evento'
        )->where('movimientos_eventos_id', $id)
            ->join('evento', 'movimientos_eventos.cod_evento', 'evento.cod_evento')
            ->join('tipo_evento', 'tipo_evento.tipo_evento_id', 'evento.tipo_evento_id')
            ->first();
        return response()->json([
            'status' => 'ok',
            'data' => [
                'movimiento' => $movimiento_evento,
                'files' => $this->get_images(
                    $movimiento_evento->movimientos_eventos_id,
                    'doc'
                ),
            ],
        ]);
    }
    /*
     *imagen
     */
    public function get_images($id, $type)
    {
        $images = DB::table('movimientos_eventos_archivos')
            ->where('movimientos_eventos_id', $id)
            ->where('tipo', $type)
            ->get();
        $list = new stdClass();
        $list->initialPreview = [];
        $list->initialPreviewConfig = [];
        if ($images) {
            foreach ($images as $val) {
                $newFileUrl = url('/') . '/docs/' . $val->imagen;
                $list->initialPreview[] = $newFileUrl;
                $list->initialPreviewConfig[] = [
                    'caption' => $val->caption,
                    'size' => $val->size,
                    'downloadUrl' => $newFileUrl,
                    'url' => url("delete_image/$id/$type/cardex"),
                    'key' => $val->m_imagen_id,
                ];
            }
        }
        return $list;
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
            'edit_movimientos_eventos' => 'required',
            'edit_Empleado_ID' => 'required',
            'edit_event' => 'required',
            'edit_fecha_inicio' => 'required|date_format:m/d/Y',
            'edit_fecha_fin' => 'required|date_format:m/d/Y',
            'edit_note' => 'required',
            'edit_docs' => 'nullable',
        );

        $messages = [
            'edit_movimientos_eventos.required' => "The 'edit_movimientos_eventos' field is required",
            'edit_Empleado_ID.required' => "The 'select Employee' field is required",
            'edit_event.required' => "The 'event' field is required",
            'edit_fecha_inicio.required' => "The 'start date' last name field is required",
            'edit_fecha_inicio.date_format' => "Invalid date format must be month / day / year",
            'edit_fecha_fin.required' => "The 'start date' last name field is required",
            'edit_fecha_fin.date_format' => "Invalid date format must be month / day / year",
            'edit_note.required' => "The 'note' field is required",
        ];
        //validacion de de datos
        $error = Validator::make($request->all(), $rules, $messages);
        if (request()->ajax() === true) {
            if ($error->errors()->all()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $error->errors()->all(),
                ]);
            }
        }
        $evento_dias = Evento::select('duracion_day')
            ->where('cod_evento', $request->edit_event)
            ->pluck('duracion_day')->first();
        //guardando archivo
        $movimiento_evento = Movimiento_evento::where('movimientos_eventos_id', $id);
        $movimiento_evento->update([
            "cod_evento" => $request->edit_event,
            "Empleado_ID" => $request->edit_Empleado_ID,
            "start_date" => date('Y-m-d', strtotime($request->edit_fecha_inicio)),
            "exp_date" => date('Y-m-d', strtotime($request->edit_fecha_fin)),
            "note" => $request->edit_note,
            "raise_from" => "",
            "raise_to" => "",
        ]);
        $this->upload_image($request, $id, 'edit_docs');
        return response()->json([
            'status' => 'ok',
            'message' => ' Modified event successfully',
        ]);
    }
    public function upload_image(Request $request, $id, $nombre_campo)
    {
        $type = 'doc';
        $allowedfileExtension = ['jpg', 'png', 'jpeg', 'pdf', 'docx', 'doc', 'xlsx', 'xls', 'csv'];
        if ($request->hasfile($nombre_campo)) {
            foreach ($request->file($nombre_campo) as $file) {
                $filename = $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension();
                $fileSize = $file->getSize();
                $check = in_array($extension, $allowedfileExtension);

                if ($check) {
                    if ($extension == 'jpg' || $extension == 'png' || $extension == 'jpeg') {
                        $name_img = "$type-doc-$id-" . uniqid() . time() . "." . $extension;
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
                        $insert = DB::table('movimientos_eventos_archivos')->insertGetId([
                            'imagen' => $name_img,
                            'tipo' => $type,
                            'movimientos_eventos_id' => $id,
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
                                'url' => url("delete_image/$id/$type/cardex"), // server api to delete the file based on key
                            ];

                        } else {
                            $errors[] = $fileName;
                        }
                    } else {
                        $name_img = "$type-doc-$id-" . uniqid() . time() . "." . $extension;
                        //$path = public_path() . '/docs/' . $name_img;
                        $file->move(public_path('docs'), $name_img);

                        $insert = DB::table('movimientos_eventos_archivos')->insertGetId([
                            'imagen' => $name_img,
                            'tipo' => $type,
                            'movimientos_eventos_id' => $id,
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
                                'url' => url("delete_image/$id/$type/cardex"), // server api to delete the file based on key
                            ];
                        } else {
                            $errors[] = $fileName;
                        }
                    }
                }
            }
        }
    }
    public function upload_image_multiple(Request $request, $nombre_campo)
    {
        $type = 'doc';
        $allowedfileExtension = ['jpg', 'png', 'jpeg', 'pdf', 'docx', 'doc', 'xlsx', 'xls', 'csv'];
        $archivos = [];
        if ($request->hasfile($nombre_campo)) {
            foreach ($request->file($nombre_campo) as $file) {
                $filename = $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension();
                $fileSize = $file->getSize();
                $check = in_array($extension, $allowedfileExtension);

                if ($check) {
                    if ($extension == 'jpg' || $extension == 'png' || $extension == 'jpeg') {
                        $name_img = "$type-doc--" . uniqid() . time() . "." . $extension;
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
                        $image = new stdClass();
                        $image->name_img = $name_img;
                        $image->type = $type;
                        $image->filename = $filename;
                        $image->fileSize = $fileSize;
                        $archivos[] = $image;
                    } else {
                        $name_img = "$type-doc-" . uniqid() . time() . "." . $extension;
                        //$path = public_path() . '/docs/' . $name_img;
                        $file->move(public_path('docs'), $name_img);

                        $image = new stdClass();
                        $image->name_img = $name_img;
                        $image->type = $type;
                        $image->filename = $filename;
                        $image->fileSize = $fileSize;
                        $archivos[] = $image;
                    }
                }
            }
        }
        return $archivos;
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $evento = Movimiento_evento::where('movimientos_eventos_id', $id)->update([
            "estado" => "0",
        ]);
        if ($evento) {
            return response()->json([
                'status' => 'ok',
                'message' => 'Deleted successfully',
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred',
            ]);
        }

    }
}
