<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;
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
        $user = User::create([
            'name'=>$request->name,
            'email'=>$request->email,
            'password'=>bcrypt($request->password),
        ]);
        $response =[
            'user'=>$user,
        ];
        return response()->json(['message'=>'successfully created','status'=>200,$response,201]);
    }

    /**
     * Display the specified resource.
     */
 public function show(Request $request)
{
    $token = str_replace('Bearer ', '', $request->header('Authorization'));
    $user = PersonalAccessToken::findToken($token)->tokenable;

    if ($user) {
        return response()->json([
            'user' => $user,
        ]);
    } else {
        return response()->json(['message' => 'User not authenticated']);
    }
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
    public function update(Request $request, int $id)
{
    $request->validate([
        'name'=>'required',
        'email'=>'required',
        'description'=>'nullable',
        'Language'=>'nullable',
        'skills'=>'nullable',
        'countries'=>'nullable',
    ]);
    // if(User::user()->id !== $id){
    //     return response()->json([
    //         'message'=>'id Error'
    //     ]);
    // }
    $token = str_replace('Bearer ', '', $request->header('Authorization'));
    // $user = PersonalAccessToken::findToken($token)->tokenable;
    try{
        $user = PersonalAccessToken::findToken($token)->tokenable;
        $user->fill($request->all())->save();
        return response()->json([
            'message'=>'User Updated Successfully!!',
            'user'=>$user
        ]);
    }catch(\Exception $e){
        \Log::error($e->getMessage());
        return response()->json([
            'message'=>'This email has already been taken'
        ],500);
    }

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
            if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
                $user = Auth::user();
                $token = $user->createToken('authToken')->plainTextToken;
                return response()->json(['access_token' => $token,'user'=>$user]);
            }
            // $user = User::find(Auth::id());
            // $token = $user->createToken('authToken')->plainTextToken;
            // // return response()->json(['access_token' => $token,'token_type'=>'Bearer','user'=>$user]);
            // // $token = $user->createToken('authToken')->accessToken;
            // return response()->json(['access_token' => $token,'user'=>$user]);
    }

    public function logout(Request $request)
    {
        // $token = str_replace('Bearer ', '', $request->header('Authorization'));
        // $user = PersonalAccessToken::findToken($token)->tokenable;
        $user = Auth::user();
        if ($user) {
                $user->tokens()->delete();
        }
        return response()->json(['message' => 'Successfully logged out']);
    }



    public function checkAuthStatus() {
        return response()->json([
            'authenticated' => auth()->check()
        ]);
    }


    public function freelancer(Request $request, $id) {
        $user = User::find($id);
        $freelancer = $user->freelancer();
        return response()->json(['freelancer' => $user]);
    }


}
