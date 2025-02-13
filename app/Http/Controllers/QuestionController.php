<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DataTables;
use Validator;
use App\Question;
use App\SubQuestion;

class QuestionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (request()->ajax()) {
            $data = Question::all();
                
            return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('options', function($data){
                        $options = SubQuestion::where($data->sub_question_id)->count();

                        return $options;
                        
                    })    
                    ->addColumn('acciones', function($data){
                        $button = "<a href=".route('edit.questions',['id' => $data->question_id])."><i class='fas fa-pencil-alt ms-text-warning edit cursor-pointer' title='Edit'></i></a>";
                        $button .= "<i class='far fa-trash-alt ms-text-danger delete cursor-pointer' data-id='$data->question_id' title='Delete'></i>";

                        return $button;
                        
                    })
                    ->rawColumns(['acciones'])
                    ->make(true);
        }
        return view('panel.evaluacion.list_questions');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('panel.evaluacion.new_questions');
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
            'nombre' => 'required',
            'descripcion' => 'nullable',
            't_input' => 'required',
        );

        $error = Validator::make($request->all(), $rules);

        if (request()->ajax() === true) {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        $question = Question::insertGetId([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            't_input' => $request->t_input,
        ]);
            
        if ($question) {
            if ($request->title) {
                foreach ($request->title as $key => $val) {
                    SubQuestion::create([
                        'titulo' => $val,
                        'question_id' => $question,
                    ]);
                }
            }
        }
        return redirect(route('list.questions'))->with('success', 'New question has been created');

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $question = Question::find($id);
        $options = SubQuestion::where('question_id',$id);

        return view('panel.evaluacion.edit_questions',compact('question','options'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $question = Question::find($id);
        $options = SubQuestion::where('question_id',$id)->get();

        return view('panel.evaluacion.edit_questions',compact('question','options','id'));
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
            'nombre' => 'required',
            'descripcion' => 'nullable',
            't_input' => 'required',
        );

        $error = Validator::make($request->all(), $rules);

        if (request()->ajax() === true) {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        $question = Question::findOrFail($id)->update([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            't_input' => $request->t_input,
        ]);

        if ($question) {
            if ($request->title) {
            SubQuestion::where('question_id',$id)->delete();
                foreach ($request->title as $key => $val) {
                    SubQuestion::create([
                        'titulo' => $val,
                        'question_id' => $id,
                    ]);
                }
            }
        }
        return redirect(route('list.questions'))->with('success', 'question has been update');

    }

    public function get_questions(Request $request)
    {
        if (!isset($request->searchTerm)) {
            $questions = Question::selectRaw("question.question_id as id, question.nombre as text, question.t_input as inputs, COUNT(sub_question.sub_question_id) as count_question")
            ->join('sub_question','question.question_id','sub_question.question_id')->distinct('question.question_id')->get();
        } else {
            $questions = Question::selectRaw("question.question_id as id, question.nombre as text, question.t_input as inputs, COUNT(sub_question.sub_question_id) as count_question")
            ->join('sub_question','question.question_id','sub_question.question_id')->distinct('question.question_id')
            ->where('text', 'like', '%' . $request->searchTerm . '%')
            ->get();
        }
        return response()->json($questions);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $subQuestion = SubQuestion::where('question_id',$id)->delete();
        $question = Question::findOrFail($id)->delete();

        return response()->json(['success' => 'Deleted successfully.']);
    }
}