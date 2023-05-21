<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ServiceController extends Controller
{
    public function store(Request $request)
    {
        if(Auth::user()->role != 'Freelancer'){
            return response()->json(['message' => 'You are not a freelancer']);
        }
           // Define validation rules for incoming data
            $rules = [
                'title' => 'required|string',
                'description' => 'required|string',
                'category' => 'required|string',
                'skills' => 'required|array',
                'tags' => 'string',
            ];

            // Validate incoming data
            $validatedData = $request->validate($rules);

            // Create a new Freelancer instance with the validated data
            $service = new Service($validatedData);
            
            // Set the user_id attribute of the new freelancer to the current user's ID
            if (Auth::check()) {
                $service->freelancer_id = auth()->user()->id;
            }
              // Add the selected services to the freelancer model
            $skills = $request->input('skills');
            $service->skills = json_encode($skills);
            
            $service->save();
            $user = Auth::user();

            // Return a success response
            return response()->json(['message' => 'Service created successfully', 'user'=>$user , 'service'=>$service]);
    }


    public function addprice(Request $request, $id)
    {
        if(Auth::user()->role != 'Freelancer'){
            return response()->json(['message' => 'You are not a freelancer']);
        }
        $rules = [
            'offer_name' => 'required|string',
            'details' => 'required|string',
            'price' => 'required|string',
            'delevery' => 'required|string',
        ];

        $service = Service::findOrFail($id);
        $user = Auth::user();
        if($service->freelancer_id == $user->id){
            $service->update($request->all());
            $service->save();
            return response()->json(['message' => 'Service updated successfully' , 'user'=>$user , 'service'=>$service]);
        }

    }
    
//     public function uploadPictures(Request $request, $id)
//     {
//         if (Auth::user()->role != 'Freelancer') {
//             return response()->json(['message' => 'You are not a freelancer']);
//         }

//         // Validate the incoming request
//         $rules = [
//             'pictures.*' => 'required|image|mimes:jpeg,png,jpg|max:2048' ,// Adjust the validation rules as per your requirements
//             'picture.*' => 'required|image|mimes:jpeg,png,jpg|max:2048' ,// Adjust the validation rules as per your requirements
//             'picture.*' => 'required|image|mimes:jpeg,png,jpg|max:2048' ,// Adjust the validation rules as per your requirements
//             'pictures.*' => 'required|image|mimes:jpeg,png,jpg|max:2048' ,// Adjust the validation rules as per your requirements
//         ];

//         $validatedData = $request->validate($rules);

//         $service = Service::findOrFail($id);
//         $user = Auth::user();

//         // Process and store the uploaded pictures
//         if ($service->freelancer_id == $user->id) {
//             $pictures = [];
//             foreach ($validatedData['pictures'] as $picture) {
//                 $path = $picture->store('services', 'public');
//                 $pictures[] = $path;
//             }

//             // Save the picture paths in the service model
//             $service->pictures = json_encode($pictures);
//             $service->save();

//             return response()->json(['message' => 'Pictures uploaded successfully', 'user' => $user, 'service' => $service]);
//         }
//     }


}
