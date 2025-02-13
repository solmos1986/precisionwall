<?php

namespace App\Http\Controllers\cardex;

use App\Http\Controllers\Controller;
use DataTables;
use DB;
use Illuminate\Http\Request;
use Validator;

class TipoPersonalController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tipos_personal = DB::table('tipo_personal')
            ->orderBy('id', 'DESC')
            ->get();
        return Datatables::of($tipos_personal)
            ->addIndexColumn()
            ->addColumn('acciones', function ($tipos_personal) {
                $button = "
                <i class='fas fa-pencil-alt ms-text-warning edit_tipe_employee cursor-pointer' data-id='$tipos_personal->id' title='Edit Type of Employee'></i>
                <i class='far fa-trash-alt ms-text-danger delete_tipe_employee cursor-pointer' data-id='$tipos_personal->id' title='Delete Type of Employee'></i>
            ";
                return $button;
            })
            ->rawColumns(['acciones'])
            ->make(true);
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
            'name_tipo_personal' => 'required',
            'description_tipo_personal' => 'nullable',
        );
        $messages = [
            'name_tipo_personal.required' => "The name field is required",
        ];
        //dd($request->all());
        $error = Validator::make($request->all(), $rules, $messages);
        if (count($error->errors()->all()) > 0) {
            return response()->json([
                'status' => 'error',
                'message' => $error->errors()->all(),
            ]);
        }
        $cargo = DB::table('tipo_personal')->insert([
            'nombre' => $request->name_tipo_personal,
            'descripcion' => $request->description_tipo_personal,
        ]);
        if ($cargo) {
            return response()->json([
                'status' => 'ok',
                'message' => 'Registered Successfully',
            ], 200);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => ['An error occurred'],
            ], 200);
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
        $cargo = DB::table('tipo_personal')
            ->where('id', $id)
            ->first();
        if ($cargo) {
            return response()->json([
                'status' => 'ok',
                'data' => $cargo,
                'message' => '',
            ], 200);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => ['An error occurred'],
            ], 200);
        }
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
            'name_tipo_personal' => 'required',
            'description_tipo_personal' => 'nullable',
        );
        $messages = [
            'name_tipo_personal.required' => "The name field is required",
        ];
        //dd($request->all());
        $error = Validator::make($request->all(), $rules, $messages);
        if (count($error->errors()->all()) > 0) {
            return response()->json([
                'status' => 'error',
                'message' => $error->errors()->all(),
            ]);
        }
        $cargo = DB::table('tipo_personal')
            ->where('id', $id)
            ->update([
                'nombre' => $request->name_tipo_personal,
                'descripcion' => $request->description_tipo_personal,
            ]);
        if ($cargo) {
            return response()->json([
                'status' => 'ok',
                'message' => ['Successfully modified'],
            ], 200);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => ['An error occurred'],
            ], 200);
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
        $cargo = DB::table('tipo_personal')
            ->where('id', $id)
            ->get();

        if (count($cargo) > 0) {
            $cargo = DB::table('tipo_personal')
                ->where('id', $id)
                ->delete();
            if ($cargo) {
                return response()->json([
                    'status' => 'ok',
                    'message' => 'Successfully deleted',
                ], 200);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => ['An error occurred'],
                ], 200);
            }
        } else {
            return response()->json([
                'status' => 'error',
                'message' => ['There are registered personnel with this type of employee'],
            ], 200);
        }
    }
}
