<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(User::latest()->get());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'email'=>'required|email|unique:users,email,$id',
            'password'=>['required','confirmed','string',Password::min(8)->symbols()->numbers()],
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors(),'status'=>204]);
        }
        User::create([
            'name'=>$request->name,
            'email'=>$request->email,
            'password'=>bcrypt($request->password),
        ]);
        $user = User::find(Auth::id());
        $token = $user->createToken('authToken')->accessToken;
        return response()->json(['message'=>'successfully created','status'=>200]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
    
    public function login(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required',
        ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors(),'status'=>204]);
            }

            if (!Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
                return response()->json(['error' => 'Your email or Your password is wrong'], 401);
            }
            $user = User::find(Auth::id());
            $token = $user->createToken('authToken')->accessToken;

            return response()->json(['access_token' => $token]);
    }

    public function logout(Request $request)
    {
        $user = Auth::user();
        if ($user) {
            $user->token()->revoke();
        }
        return response()->json(['message' => 'Successfully logged out']);
    }



    public function checkAuthStatus() {
        return response()->json([
            'authenticated' => auth()->check()
        ]);
    }
}
