<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\MasterData\Bulan;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

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

    public function username()
    {
        return 'nip';
    }

    /**
     * Log the user out of the application.
     *
     * @param Request $request
     * @return Response
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
     * @param Request $request
     * @param mixed $user
     * @return mixed
     */
    protected function authenticated(Request $request, $user)
    {

        if (!$user->role()->first()) {
            $this->guard()->logout();
            $request->session()->invalidate();

            return $this->loggedOut($request) ?: redirect('/login')->with('message', 'Role pegawai belum ditentukan');

        } else {
            $jabatan = $user->id_jabatan ? $user->jabatan()->first()->jabatan : 'Administrator';
            $tempat_lahir = $user->tempat_lahir ? $user->tempat_lahir : 'Administrator';
            $tanggal_lahir = $user->tanggal_lahir ? $user->tanggal_lahir : '1990-01-01';
            $agama = $user->id_agama ? $user->agama()->get()->first()->agama : '';
            $bulan_lahir = ucfirst(Bulan::find((int)date('m', strtotime(($user->tanggal_lahir ? $user->tanggal_lahir : 1))))->nama_bulan);

            $request->session()->put('user', collect([
                'nama' => $user->nama,
                'nip' => $user->nip,
                'foto' => $user->foto,
                'jabatan' => $jabatan,
                'tempat_lahir' => $tempat_lahir,
                'tanggal_lahir' => $tanggal_lahir,
                'bulan_lahir' => $bulan_lahir,
                'agama' => $agama
            ]));
        }
    }

}
