<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Personal;
use App\User;
use \stdClass;
use Validator;

class AuthController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        $rules = array(
            'username' => 'required|string',
            'password' => 'required|string'
            );
        $messages=[
                'username.required'=>"The 'username' field is required",
                'password.required'=>"The 'password' field is required",
            ];
        //validando
        $error = Validator::make($request->all(), $rules, $messages);
      
        if ($error->errors()->all()) {
            $user= new stdClass();
            $user->status="error";
            $user->message="Error check your data";
            return response()->json($user, 200);
        } else {
            $user = User::
            select('personal.*')
            ->selectRaw("CONCAT(Nombre, ' ', Apellido_Paterno, ' ', Apellido_Materno) as nombre_completo")
            ->where('Usuario', request()->input('username'))
            ->where('Password', request()->input('password'))
            ->first();
            if (empty($user)) {
                //error
                $user= new stdClass();
                $user->status="error";
                $user->message="Error check your data";
                return response()->json($user, 200);
            } else {
                $user->status="ok";
                $user->message="welcome";
                $token = $user->createToken('constructora')->accessToken;
                $user->token=$token;
                return response()->json($user, 200);
            }
        }
    }

    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return response()->json($request->user());
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
