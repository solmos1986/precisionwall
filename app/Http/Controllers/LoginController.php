<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest')->except('logout', 'auto_login', 'auto_login_submittals');
        $this->middleware('guest', ['only' => 'Showloginform']);
    }
    public function Showloginform()
    {
        return view('auth.login');
    }

    public function auto_login($id)
    {
        $credentials = $this->validate(request(), [
            'username' => 'required|string',
            'password' => 'required|string',
        ]);
        $user = User::where('Usuario', request()->input('username'))
            ->where('Password', request()->input('password'))
            ->first();
        $remember_me = request()->has('remember') ? true : false;
    
        if (empty($user)) {
            return redirect(route('showloginform'))->with('flash', 'This data is incorrect');
        } else {
            if (Auth::loginUsingId($user->Empleado_ID, $remember_me)) {
                // Authentication passed...
                
                switch (request()->input('redirrect')) {
                    case 'reportDaily':
                        return redirect(route('daily_report_detail.create', ['id' => request()->input('actividad')]));
                        break;
                        
                    default:
                        return redirect(route('notas.proyecto.lis.proyecto', ['id' => $id]));
                        break;
                }
            }
        }
    }

    public function auto_login_submittals()
    {
        $proyecto_id = request()->proyecto_id;
        $credentials = $this->validate(request(), [
            'username' => 'required|string',
            'password' => 'required|string',
        ]);
        $user = User::where('Usuario', request()->input('username'))
            ->where('Password', request()->input('password'))
            ->first();
        $remember_me = request()->has('remember') ? true : false;

        if (empty($user)) {
            return redirect(route('showloginform'))->with('flash', 'This data is incorrect');
        } else {
            if (Auth::loginUsingId($user->Empleado_ID, $remember_me)) {
                // Authentication passed...
                return redirect(route('submittals.list', ['proyecto_id' => $proyecto_id]));
            } else {
                return redirect(route('submittals.list', ['proyecto_id' => $proyecto_id]));
            }
        }
    }
    public function login()
    {
        $credentials = $this->validate(request(), [
            'username' => 'required|string',
            'password' => 'required|string',
        ]);
        $user = User::where('Usuario', request()->input('username'))
            ->where('Password', request()->input('password'))
            ->first();
        $remember_me = request()->has('remember') ? true : false;
        if (empty($user)) {
            return redirect(route('showloginform'))->with('flash', 'This data is incorrect');
        } elseif (Auth::loginUsingId($user->Empleado_ID, $remember_me)) {
            // Authentication passed...
            return redirect(route('listar.actividades'));
        } else {
            return redirect(route('showloginform'))->with('flash', 'This data is incorrect');
        }
    }
    public function logout()
    {
        if (Auth::check()) {
            Auth::logout();
            request()->session()->flush();
            request()->session()->invalidate();
        }
        return redirect(route('showloginform'));
    }
}
