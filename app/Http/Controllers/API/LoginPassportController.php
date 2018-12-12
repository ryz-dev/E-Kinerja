<?php

namespace App\Http\Controllers\Api;

use App\Helper\ApiResponseFormat;
use App\Models\MasterData\Pegawai;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Psr\Http\Message\ServerRequestInterface;
use \Laravel\Passport\Http\Controllers\AccessTokenController as ATC;
use Illuminate\Support\Facades\Hash;
use Lcobucci\JWT\Parser;

class LoginPassportController extends ATC {

    public function getLogin(ServerRequestInterface $request) {
        // helper format JSON
        $format = new ApiResponseFormat();

        // get data fro header
        $email = $request->getHeader("username");
        $pass = $request->getHeader("password");

        if (($email[0] == "") || ($email[0] == null)) {
            return response()->json($format->formatResponseWithPages("Username Harus Diisi", [], $format->STAT_REQUIRED()), $format->STAT_REQUIRED());
        } else if (($pass[0] == "") || ($pass[0] == null)) {
            return response()->json($format->formatResponseWithPages("Password Harus Diisi", [], $format->STAT_REQUIRED()), $format->STAT_REQUIRED());
        }

        // create custom parsedBody
        $additionalData = [
            "grant_type" => "password",
            "client_id" => env("CLIENT_ID", 99),
            "client_secret" => env("CLIENT_SECRET", 123456789),
            "username" => $email[0],
            "password" => $pass[0],
            'scope' => '*',
        ];

        // custom parsedBody
        $req = $request->withParsedBody($additionalData);

        // execute
        try {

            //get username (default is :email) and password
            $username = $req->getParsedBody()['username'];
            $password = $req->getParsedBody()['password'];

            //get user and check valid Login
            //change to 'email' if you want
            $user = Pegawai::with('jabatan','role')->where('nip', '=', $username)->first();
            if (!$user) {
                return response()->json($format->formatResponseWithPages("Username/NIP tidak ditemukan", [], $format->STAT_NOT_FOUND()), $format->STAT_NOT_FOUND());
            } else if (!Hash::check($password, $user->password)) {
                return response()->json($format->formatResponseWithPages("Password tidak sesuai", [], $format->STAT_NOT_FOUND()), $format->STAT_NOT_FOUND());
            }
            $user['nama_agama'] = $user->agama->agama;
            $user = $user->toArray();
            unset($user['remember_token'],$user['detail_uri'],$user['delete_uri'],$user['edit_uri'],$user['update_uri']);

            //generate token
            $tokenResponse = parent::issueToken($req);

            //convert response to json string
            $content = $tokenResponse->getContent();

            //convert json to array
            $data = json_decode($content, true);

            if (isset($data["error"])) {
                return response()->json($format->formatResponseWithPages($data["error"].",".$data["message"], [], $format->STAT_UNAUTHORIZED()), $format->STAT_UNAUTHORIZED());
            }

            //add user to issueToken
            $resultLogin = collect($data);
            $resultLogin->put('user', $user);

            // result Login TRUE
            return response()->json($format->formatResponseWithPages("Login berhasil", $resultLogin, $format->STAT_OK()), $format->STAT_OK());
        } catch (Exception $e) {

            //return error message
            return response()->json($format->formatResponseWithPages("Internal Server Error", [], $format->INTERNAL_SERVER_ERROR()), $format->INTERNAL_SERVER_ERROR());
        }
    }

    public function getLogout(Request $request) {
        try {
            $value = $request->bearerToken();
            $id = (new Parser())->parse($value)->getHeader('jti');
            $token = $request->user()->tokens->find($id);
            $token->revoke();

            // helper format JSON
            $format = new ApiResponseFormat();

            // result Logout TRUE
            return response()->json($format->formatResponseWithPages("Logout berhasil", [], $format->STAT_OK()), $format->STAT_OK());
        } catch (Exception $e) {

            //return error message
            return response()->json($format->formatResponseWithPages("Internal Server Error", [], $format->INTERNAL_SERVER_ERROR()), $format->INTERNAL_SERVER_ERROR());
        }
    }

    public function getRefresh(ServerRequestInterface $request) {
        // helper format JSON
        $format = new ApiResponseFormat();

        // get data from header
        $refresh = $request->getHeader("refreshtoken");

        if (($refresh[0] == "") || ($refresh[0] == null)) {
            return response()->json($format->formatResponseWithPages("Refresh Token Harus Diisi", [], $format->STAT_REQUIRED()), $format->STAT_REQUIRED());
        }

        // create custom parsedBody
        $additionalData = [
            "grant_type" => "refresh_token",
            "client_id" => env("CLIENT_ID", ""),
            "client_secret" => env("CLIENT_SECRET", ""),
            "refresh_token" => $refresh[0],
            'scope' => '*',
        ];

        // custom parsedBody
        $req = $request->withParsedBody($additionalData);

        // execute
        try {

            //get user data from token
            $user = Auth::user();

            //generate token
            $tokenResponse = parent::issueToken($req);

            //convert response to json string
            $content = $tokenResponse->getContent();

            //convert json to array
            $data = json_decode($content, true);

            if (isset($data["error"])) {
                return response()->json($format->formatResponseWithPages($data["error"].",".$data["message"], [], $format->STAT_UNAUTHORIZED()), $format->STAT_UNAUTHORIZED());
            }
            $user = $user->toArray();
            unset($user['remember_token'],$user['detail_uri'],$user['delete_uri'],$user['edit_uri'],$user['update_uri']);
            //add user to issueToken
            $resultLogin = collect($data);
            $resultLogin->put('user', $user);

            // result Refresh TRUE
            return response()->json($format->formatResponseWithPages("Refresh Token berhasil", $resultLogin, $format->STAT_OK()), $format->STAT_OK());
        } catch (Exception $e) {
            
            //return error message
            return response()->json($format->formatResponseWithPages("Internal Server Error", [], $format->INTERNAL_SERVER_ERROR()), $format->INTERNAL_SERVER_ERROR());
        }
    }
    
    public function getStatus() {
        try {
            
            // helper format JSON
            $format = new ApiResponseFormat();
            
            // result Login TRUE
            return response()->json($format->formatResponseWithPages("Token Aktif", [], $format->STAT_OK()), $format->STAT_OK());
        } catch (Exception $e) {
            
            //return error message
            return response()->json($format->formatResponseWithPages("Internal Server Error", [], $format->INTERNAL_SERVER_ERROR()), $format->INTERNAL_SERVER_ERROR());
        }
    }
    
    public function getChangePassword(Request $r){
        $r->validate([
            'old_password' => 'required|string',
            'new_password' => 'required|string'
        ]);

        $user = auth('api')->user();
        
        if ($r->input('old_password') === $r->input('new_password')) {
            return response()->json([
                'diagnostic' => [
                    'code' => '403',
                    'message' => 'Kata sandi lama dan kata sandi baru tidak boleh sama!'
                ]
            ]);
        }

        if (!Hash::check($r->input('old_password'), $user->password)) {
            return response()->json([
                'diagnostic' => [
                    'code' => '403',
                    'message' => 'Kata sandi lama tidak sesuai!'
                ]
            ]);
        } else {
            $user = auth('api')->user();
            $user->password = bcrypt($r->input('new_password'));
            $user->save();
            return response()->json([
                'diagnostic' => [
                    'code' => '200',
                    'message' => 'Kata sandi berhasil diubah'
                ]
            ]);
        }
    }
}
