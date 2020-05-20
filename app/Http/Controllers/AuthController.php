<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;
use Hash;
use DB;
use Log;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('jwt.verify', ['except' => ['login','register']]);
    }

    public function register(REQUEST $request){
        if(User::where('email',$request->email)->first()){
            return response()->json(["error"=>"Email Invalid"],200);
        }
        $user = new User;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->save();
        return response()->json(["status"=>"success"],200);
    }

    public function login()
    {
        $credentials = request(['email', 'password']);

        if (! $token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }

    public function user()
    {
        return response()->json(auth()->user());
    }

    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }
    public function testSearch(Request $request){
        $filter = array(
            'id'       => isset($request->id) ? $request->id : false,
            'name'     => isset($request->name) ? $request->name : false,
            'fromdate' => isset($request->fromdate) ? $request->fromdate : false,
            'todate'   => isset($request->todate) ? $request->todate : false,
        );
        $where = array();
        if ($filter['id']){
            $where[] = "id = '$request->id'";
        }
         
        if ($filter['name']){
            $where[] = "name = '$request->name'";
        }
         
        if ($filter['fromdate']){
            $where[] = "created_at > '$request->fromdate'";
        }
         
        if ($filter['todate']){
            $where[] = "created_at < '$request->todate'";
        }
        $query = "SELECT * FROM users";
        if ($where){
            $query .= " WHERE ".implode(' AND ',$where);
        }
        $user = DB::select($query);
        return response()->json(['data' => $user]);
    }
}