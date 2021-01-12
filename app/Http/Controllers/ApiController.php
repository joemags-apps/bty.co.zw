<?php

namespace App\Http\Controllers;

use App\Models\Short;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ApiController extends Controller
{
    public function randomId($len = 6){

        $uid = Str::random($len);
        $validator = Validator::make(['uid'=>$uid],['uid'=>'unique:shorts,uid']);
        if($validator->fails()){
             return $this->randomId();
        }
        return $uid;
   }

    public function login(Request $request){

        $user = User::whereEmail($request->email)->first();

        $check = Hash::check($request->password, $user->password);

        if (!isset($user) || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => "Unauthorized {$check} {$user->password} {$request->password}"
            ], 401);
        }

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token
        ], 201);
    }

    public function register(Request $request){

        $validator = Validator::make(
            $request->only(['name', 'email', 'password']), [
                'name' => 'required',
                'email' => 'required|email|unique:users,email',
                'password' => 'required'
            ]);

        if($validator->fails()){
            return response()->json(['message' => 'Missing parameters or email was taken'], 422);
       }
        if (!env('REG_ENABLED')) {
            return response()->json(['message' => 'Registration is disabled. Contact support'], 401);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);


        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token
        ], 201);
    }

    public function short(Request $request){

        if(!isset($request->url)) {
            return response()->json(['message' => 'Missing parameter URL'], 422);
        }

        $validator = Validator::make(['url'=>$request->url],['url'=>'required|url']);

        if($validator->fails()){
            return response()->json(['message' => 'Provided data not a URL'], 422);
       }

        $shortened = Str::contains($request->url, route('main'));

        if($shortened){
            return response()->json(['message' => 'URL already shortened'], 422);
        }

        $code = $this->randomId();

        $link = Short::firstOrCreate(['url' => $request->url], [
            'code' => $code
        ]);

        return response()->json([
            'short' => $link->short
        ], 200, [], JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT);
    }
}
