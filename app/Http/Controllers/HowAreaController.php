<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DataTables;
use Validator;
use App\HowAreas;

class HowAreaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (request()->ajax()) {
            $data = HowAreas::all();
                
            return Datatables::of($data)
                ->addIndexColumn()
                    ->addColumn('acciones', function($data){
                        $button = "<i class='fas fa-pencil-alt ms-text-warning edit cursor-pointer' title='Edit' data-id='$data->how_areas_id'></i>";
                        $button .= "<i class='far fa-trash-alt ms-text-danger delete cursor-pointer' data-id='$data->how_areas_id' title='Delete'></i>";

                        return $button;
                        
                    })
                    ->rawColumns(['acciones'])
                    ->make(true);
        }
        return view('panel.evaluacion.list_how_areas');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if ($request->ajax()) {
            $rules = array(
                'nombre' => 'required',
                'descripcion' => 'nullable',
            );

            $error = Validator::make($request->all(), $rules);
            if($error->fails())
            {
                return response()->json(['errors' => $error->errors()->all()]);
            }

            $areas = HowAreas::insertGetId([
                'nombre' => $request->nombre,
                'descripcion' => $request->descripcion,
            ]);

            return response()->json(['success' => 'New evaluation area created.']);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $areas = HowAreas::find($id);
      
        return response()->json(['result' => $areas]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        dd($request->all());
        if ($request->ajax()) {
            $rules = array(
                'nombre' => 'required',
                'descripcion' => 'nullable',
            );

            $error = Validator::make($request->all(), $rules);
            if($error->fails())
            {
                return response()->json(['errors' => $error->errors()->all()]);
            }

            $areas = HowAreas::findOrFail($request->hidden_id)->update([
                'nombre' => $request->nombre,
                'descripcion' => $request->descripcion,
            ]);

            return response()->json(['success' => 'Updated successfully.']);
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
        $areas = HowAreas::findOrFail($id)->delete();

        return response()->json(['success' => 'Deleted successfully.']);
    }
}