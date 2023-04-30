<?php

// namespace App\Http\Controllers;

// use App\Models\User;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Auth;
// use Illuminate\Support\Facades\Validator;

// class AuthController extends Controller
// {
//     public function login(Request $request)
//     {
//         $validatedData = Validator::make($request->all(), [
//             'email' => 'required',
//             'password' => 'required',
//         ]);

//         if ($validatedData->fails()) {
//             return response()->json(['errors' => $validator->errors(),'status'=>204]);
//         }
//         $credentials = $validatedData->validated();

//         if (Auth::attempt($credentials)) {
//             $request->session()->regenerate();
//             return response()->json(['message'=>'loged in successfully','status'=>200]);
//         }else{
//             return response()->json(['error' => 'Your email or Your password is wrong'], 401);
//         }
//         // return back()->with('error', 'The provided credentials do not match our records.')->withInput();
//     }

//     // public function logout(Request $request)
//     // {
//     //     Auth::logout();

//     //     $request->session()->invalidate();

//     //     $request->session()->regenerateToken();

//     //     return redirect()->route('view.home')->with('success', 'You are logged Out.');
//     // }

// }
