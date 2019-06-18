<?php

namespace App\Http\Controllers;

use App\User;
use App\Views\AfiliadoAlDiaActivoView;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Validator;

use Carbon\Carbon;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('jwt', ['except' => ['login','register','recovery']]);
    }
    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $nro_afiliado = $request->input('nro_afiliado');
        $password = $request->input('password');
        if (!$token = auth()->attempt(['nro_afiliado'=> $nro_afiliado,'password'=>$password])) {
            return response()->json(['error' => 'Usuario o clave incorrectos'], 401);
        }
        $recovery = false;
        if( preg_match("/^\d+$/", $password) ) {
            $recovery = true;
        }

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => auth()->factory()->getTTL(),
            'user' => auth()->user(),
            'recovery' => $recovery,
        ]);
    }
    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(auth()->user(),200);
    }
    public function payload()
    {
        return response()->json(auth()->payload());
    }
    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();
        return response()->json(['message' => 'Deslogeo satisfactoriamente']);
    }

    /*
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(),[
          'email' => 'required | email',
          'password' => 'required',
          'c_password' => 'required | same:password',
          'matricula' => 'required',
        ]);
        if($validator->fails()){
          return response()->json(['error'=>$validator->errors()],401);
        }
        $email = $request->input('email');
        $password = $request->input('password');
        $matricula = $request->input('matricula');
        $afiliado = AfiliadoAlDiaActivoView::where('id','like',$matricula)->first();
        if(!$afiliado){
            return response()->json([
                'error' => 'El matriculado no se encuentra o no tiene su matricula al dia.',
            ],401);
        }
        $user = User::where('id_matricula',$matricula)->first();
        if($user){
            return response()->json([
                'error' => 'El matriculado ya se encuentra registrado.',
            ],401);
        }
        try {
          $user = new User;
          $user->email = $email;
          $user->id_matricula = $matricula;
          $user->web_claveAPP = Hash::make($password);
          $user->fecha_alta = Carbon::now();
          $user->save();
        } catch(\Illuminate\Database\QueryException $e){
            $errorCode = $e->errorInfo[1];
            if($errorCode == '1062'){
                return response()->json(['error'=>'Email Duplicado'],401);
            }
        }
        $token = auth()->attempt(['email'=> $email,'password'=>$password]);
        return $this->respondWithToken($token);
    }
    */
    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }
    /*
    public function recovery(Request $request){
        $validator = Validator::make($request->all(),[
          'email' => 'required',
        ]);
        if($validator->fails()){
          return response()->json(['error'=>$validator->errors()],401);
        }
        $email = $request->input('email');
        $user = User::where('email',$email)->first();
        if(!$user){
            return response()->json([
                'error' => 'El correo no esta registrado.',
            ],401);
        }
        $password = sprintf("%06d", mt_rand(1, 999999));
        Mail::send('mails.auth',[
          'password' => $password,
          'email' => 'copaipa.salta@gmail.com'
        ], function($message) use ($user){
            $message->to($user->email)->subject("Correo de recuperación");
        });

        if (Mail::failures()) {
            return response()->json([
                'error' => 'El correo no pudo ser enviado.',
            ],401);
        } else {
            $enviado = true;
            $user->web_claveAPP = Hash::make($password);
            $user->save();
        }
        return response()->json([
            'mensaje' => 'El correo fue enviado con exito.',
        ],200);
    }
    */
    /*
    public function password(Request $request){
        $validator = Validator::make($request->all(),[
          'password' => 'required',
          'n_password' => 'required',
          'c_password' => 'required | same:n_password',
        ]);
        if($validator->fails()){
          return response()->json(['error'=>$validator->errors()],401);
        }
        $user = auth()->user();
        $password = $request->input('password');
        $n_password = $request->input('n_password');
        $c_password = $request->input('c_password');
        if (!(Hash::check($password, $user->web_claveAPP))) {
            return response()->json([
                'error' => 'La contraseña actual no es la correcta.',
            ],401);
        }

        if(strcmp($password, $n_password) == 0){
            return response()->json([
                'error' => 'La contraseña actual no es la correcta.',
            ],401);
        }
        $user->web_claveAPP = Hash::make($n_password);
        $user->save();
        return response()->json([
            'mensaje' => 'La contraseña fue modificada con exito.',
        ],200);
    }
    */
    /**
     * Get the token array structure.
     *
     * @param string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => auth()->factory()->getTTL(),
            'user' => auth()->user(),
        ]);
    }


}