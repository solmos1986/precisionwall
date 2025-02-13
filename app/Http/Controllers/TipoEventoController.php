<?php

namespace App\Http\Controllers;

use App\Tipo_evento;
use Illuminate\Http\Request;
use Validator;
use DataTables;

class TipoEventoController extends Controller
{
    public function obtener_tipo_evento(Request $request)
    {
        $data = [];
        if (!isset($request->searchTerm)) {
            $tipo_eventos=Tipo_evento::where('estado', '1')
            ->orderBy('nombre', 'ASC')
            ->get();
        } else {
            $tipo_eventos=Tipo_evento::where('estado', '1')
                ->where('nombre', 'like', '%' . $request->searchTerm . '%')
                ->get();
        }
        foreach ($tipo_eventos as $row) {
            $data[] = array(
                    "id" => $row->tipo_evento_id,
                    "text" => $row->nombre,
                );
        }
        
        return response()->json($data);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function list_tipo_evento()
    {
        $tipo_eventos=Tipo_evento::where('estado', '1')->get();
        return Datatables::of($tipo_eventos)
                ->addIndexColumn()
                ->addColumn('acciones', function ($tipo_eventos) {
                    $button = "
                        <i class='fas fa-pencil-alt ms-text-warning edit_tipo_evento cursor-pointer' data-id='$tipo_eventos->tipo_evento_id' title='Edit type event'></i>
                        <i class='far fa-trash-alt ms-text-danger delete cursor-pointer' data-id='$tipo_eventos->tipo_evento_id' title='Delete'></i>
                        ";
                    return $button;
                })
                ->rawColumns(['acciones'])
                ->make(true);
    }

    public function index()
    {
        return view('panel.cardex_personal.tipo_evento.list');
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
    public function store(Request $request)
    {
        $rules = array(
            'name'=>'required',
            'description' => 'nullable',
        );

        $messages=[
            'name.required'=>"The 'name' field is required",
        ];
        //validacion de de datos
        $error = Validator::make($request->all(), $rules, $messages);
        if (request()->ajax() === true) {
            if ($error->errors()->all()) {
                return response()->json(['errors' => $error->errors()->all()]);
            }
        }
        //save
        $evento=Tipo_evento::where('nombre', $request->name)->first();
        if ($evento) {
            return response()->json(['errors' => ["The name field already exists"]]);
        } else {
            $evento=Tipo_evento::insertGetId([
                "nombre"=>$request->name,
                "descripcion"=>$request->description,
                "estado"=>"1"
            ]);
            return response()->json(["success"=>["Event type successfully created"]]);
        }
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
        $tipo_evento = Tipo_evento::where('estado', '1')->findOrFail($id);
        return response()->json($tipo_evento);
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
            'edit_nombre'=>'required',
            'edit_descripcion' => 'nullable',
        );
        $messages=[
            'edit_nombre.required'=>"The 'name' field is required",
        ];
        $error = Validator::make($request->all(), $rules, $messages);
        if ($error->errors()->all()) {
            return response()->json(['errors' => $error->errors()->all()]);
        } else {
            $tipo_evento = Tipo_evento::where('estado', '1')->findOrFail($id);
            $tipo_evento->update([
                'nombre' =>  $request->edit_nombre,
                'descripcion' =>$request->edit_descripcion,
            ]);
            return response()->json(['success' => 'The type event has been updated']);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $verificar=Tipo_evento::where('tipo_evento.tipo_evento_id', $id)
        ->where('evento.estado', '1')
        ->join('evento', 'evento.tipo_evento_id', 'tipo_evento.tipo_evento_id')
        ->get()
        ->toArray();
        if (empty($verificar)) {
            $tipo_evento = Tipo_evento::findOrFail($id);
            $tipo_evento->update(['estado' => 0]);
            return response()->json(['success' => 'Type event removed successfully']);
        } else {
            return response()->json(["error"=>"It cannot be deleted because there are events registered"]);
        }
    }
}
