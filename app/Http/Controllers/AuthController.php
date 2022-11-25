<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
    {
     $fields = $request->validate(
        ["name"=>"required|string",
        "email"=>"required|string|unique:users,email",
        "password"=>"required|string|confirmed"
        ]
     ); 
     try {
        $user = new User(); 
         $user->name = $request->name;
         $user->email = $request->email;
        $user->password = bcrypt(request('password'));
        $user->save();
        $token = $user->createToken('secret')->plainTextToken;
        return response()->json(["user"=>$user,"token"=>$token,], 201);
     } catch (\Throwable $th) {
        return response()->json($th,401);
     }
    }

    public function login(Request $request){
        try {
            $request->validate([
                "email"=>"required|string",
                "password"=>"required|string",
            ]);

            $user = User::where("email",$request->email)->first();
            if (!$user || !Hash::check($request->password, $user->password) ) {
                return response()->json(["message"=>"please check your login credentials"],401);
            }else{
                $token = $user->createToken('secret')->plainTextToken;
                return response()->json(["user"=>$user,"token"=>$token],200);
            }
        } catch (\Throwable $th) {
            return response()->json($th,401);
        }
    }
}
