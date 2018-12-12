<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/input-kinerja';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function username(){
        return 'nip';
    }

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        $this->guard()->logout();

        $request->session()->invalidate();

        return $this->loggedOut($request) ?: redirect('/login');
    }

    /**
     * The user has been authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function authenticated(Request $request, $user)
    {
        $request->session()->put('user',collect([
            'nama' => $user->nama,
            'nip' => $user->nip,
            'foto' => $user->foto,
            'jabatan' => $user->jabatan()->first()->jabatan,
            'tempat_lahir' => $user->tempat_lahir,
            'tanggal_lahir' => $user->tanggal_lahir,
            'bulan_lahir' => ucfirst(\App\Models\MasterData\Bulan::find((int)date('m', strtotime($user->tanggal_lahir)))->nama_bulan),
            'agama' => $user->agama()->get()->first()->agama
        ]));
    }
    
}
