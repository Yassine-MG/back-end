<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Freelancer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Mail;
use GuzzleHttp\Client;


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
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);
    
        // Check if the email address has a user in the database
        $user = User::where('email', $request->email)->first();
    
        if (!$user) {
            return response()->json(['message' => 'Email address not found'], 404);
        }
    
        // Generate the password reset token and save it for the user
        $token = $this->broker()->createToken($user);
    
        // Send the password reset email using Mailtrap API
        try {
            $client = new Client();
    
            $response = $client->request('POST', 'https://mailtrap.io/api/v1/inboxes/{inbox_id}/messages', [
                'headers' => [
                    'Api-Token' => 'd96230262509caa5d5ccfac23335f3c7',
                ],
                'json' => [
                    'subject' => 'Password Reset',
                    'to' => [
                        [
                            'email' => $user->email,
                            'name' => $user->name,
                        ],
                    ],
                    'html' => view('emails.reset-link-email', [
                        'token' => $token,
                        'email' => $user->email,
                    ])->render(),
                ],
            ]);
    
            if ($response->getStatusCode() === 200) {
                return response()->json(['message' => 'Email sent successfully'], 200);
            } else {
                return response()->json(['message' => 'Error sending email'], 400);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    protected function broker()
    {
        return Password::broker();
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users,email,$id',
            'password' => ['required', 'confirmed', 'string', 'min:8'],
        ]);
    
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors(), 'status' => 204]);
        }
    
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password), // Use Hash::make() to hash the password
        ]);
    
        $response = [
            'user' => $user,
        ];
    
        return response()->json(['message' => 'successfully created', 'status' => 200, $response, 201]);
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
        $user->fill($request->except('photo'))->save();

        if ($request->hasFile('photo')) {
            $photo = $request->file('photo');
            $photoPath = $photo->store('profile_pictures', 'public');

            $user->photo = $photoPath;
            $user->save();
        }

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


    public function userInProfile($id)
    {
        $user = User::find($id);

        if ($user) {
            return response()->json([
                'user' => $user,
            ]);
        } else {
            return response()->json(['message' => 'User not found']);
        }
    }


    public function getServices($id)
{
    $freelancer = Freelancer::where('user_id', $id)->first();

    if ($freelancer) {
        $services = $freelancer->services;
        return response()->json([
            'services' => $services,
        ]);
    } else {
        return response()->json(['message' => 'Freelancer not found']);
    }
}
}
