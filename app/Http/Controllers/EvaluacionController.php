<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DataTables;
use App\Evaluacion;
use App\HowAreas;
use Validator;

class EvaluacionController extends Controller
{
    public function __construct()
    {  
        $this->middleware('auth');   
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (request()->ajax()) {
            $data = Evaluacion::withCount('questions')->get();
            
            return Datatables::of($data)
                ->addIndexColumn()
                    ->addColumn('acciones', function($data){
                        $button = "<a href='".route('edit.evaluations',['id' => $data->evaluation_form_id])."'><i class='fas fa-pencil-alt ms-text-warning' title='Edit'></i></a>";
                        $button .= "<i class='far fa-trash-alt ms-text-danger delete cursor-pointer' data-id='$data->evaluation_form_id' title='Delete'></i>";

                        return $button;
                        
                    })
                    ->rawColumns(['acciones'])
                    ->make(true);
        }
        return view('panel.evaluacion.list');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $areas = HowAreas::all();
        return view('panel.evaluacion.new',compact('areas'));
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
            'title' => 'required',
            'description' => 'nullable',
            'areas' => 'required',
            'questions' => 'required',
        );

        $error = Validator::make($request->all(), $rules);

        if (request()->ajax() === true) {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        $evaluacion = Evaluacion::create([
            'titulo' => $request->title,
            'descripcion' => $request->description,
        ]);

        $evaluacion->questions()->attach($request->questions);
        $evaluacion->areas()->attach($request->areas);
            
        return redirect(route('list.evaluations'))->with('success', 'a new registered assessment');

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
        $evaluacion = Evaluacion::find($id);
        $areas = HowAreas::all();

        return view('panel.evaluacion.edit',compact('evaluacion','areas','id'));
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
            'title' => 'required',
            'description' => 'nullable',
            'areas' => 'required',
            'questions' => 'required',
        );

        $error = Validator::make($request->all(), $rules);

        if (request()->ajax() === true) {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        $evaluacion = Evaluacion::find($id);
        $evaluacion->update([
            'titulo' => $request->title,
            'descripcion' => $request->description,
        ]);
        if (isset($request->questions) && !empty($request->questions)) {
            $evaluacion->questions()->detach();
            $evaluacion->questions()->attach($request->questions);
        }else{
            $evaluacion->questions()->detach();
        }
        if (isset($request->areas) && !empty($request->areas)) {
            $evaluacion->areas()->detach();
            $evaluacion->areas()->sync($request->areas);
        }else {
            $evaluacion->areas()->detach();
        }
        
            
        return redirect(route('list.evaluations'))->with('success', 'a new registered assessment');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $evaluacion = Evaluacion::findOrFail($id);
        $evaluacion->questions()->detach();
        $evaluacion->areas()->detach();
        $evaluacion->delete();

        return response()->json(['success' => 'deleted successfully ']);
    }
}