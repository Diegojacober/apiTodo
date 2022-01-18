<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

use App\Models\User;

class AuthController extends Controller
{
    public function create(Request $request){
        $array = ['error' => ''];

        $rules = [
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:4'
        ];

        $validator = Validator::make($request->all(),$rules);
        if($validator->fails()){
            $array['error'] = $validator->errors();
            return json_encode($array);
        }
        $email = $request->input('email');
        $password = $request->input('password');

        //criando o novo usuário
        $newUser = new User();
        $newUser->email = $email;
        $newUser->password = password_hash($password,PASSWORD_DEFAULT);
        $newUser->token = '';
        $newUser->save();

        //logar o usuário recem criado

        return json_encode($array);
    }

    public function login(Request $request){
        $array = ['error' => ''];

        $creds = $request->only('email','password');
        
        if(Auth::attempt($creds)){
            $user = User::where('email',$creds['email'])->first();

            $item = md5(time() * rand(0,9999));
            $token = $user->createToken($item)->plainTextToken;

            $array['token'] = $token;
        }
        else{
            $array['error'] = 'E-mail ou senha incorretos!';
        }

        return json_encode($array);
    }
    public function logout(Request $request){
        $array = ['error' => ''];

       $user = $request->user();

       $user->tokens()->delete();

        return json_encode($array);
    }
}
