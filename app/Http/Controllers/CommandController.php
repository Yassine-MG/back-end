<?php

namespace App\Http\Controllers;

use App\Models\Command;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Freelancer;
use App\Models\Service;

class CommandController extends Controller
{
    public function store(Request $request)
    {
        // Validate the incoming request data
        $validatedData = $request->validate([
            'project_name' => 'required|string',
            'description' => 'required|string',
            'service_id' => 'required|exists:services,id',
            'files' => 'nullable|array',
            'files.*' => 'file|mimes:png,jpg,jpeg,gif|max:10240', // Adjust the file validation rules as needed
            // Add any other validation rules you need
        ]);

        $userId = Auth::id();
        $validatedData['user_id'] = $userId;

        // Upload files and store their paths
        if ($request->hasFile('files')) {
            $uploadedFiles = [];
            foreach ($request->file('files') as $file) {
                $uploadedPath = $file->store('public/files'); // Store files in the public disk under the 'files' directory
                $uploadedFiles[] = Storage::url($uploadedPath); // Get the public URL for the uploaded file
            }
            $validatedData['files'] = json_encode($uploadedFiles);
        }

        // Create a new Command instance with the validated data
        $command = Command::create($validatedData);

        // Return a response or redirect as needed
        return response()->json(['message' => 'Command created successfully']);
    }


    public function getCommandsRelatedToFreelancerService()
    {
        $userId = Auth::id();
    
        // Check if the user has a freelancer account
        $freelancer = Freelancer::where('user_id', $userId)->first();
    
        if (!$freelancer) {
            return response()->json(['message' => 'User is not a freelancer'], 403);
        }
    
        // Get the freelancer's services
        $services = $freelancer->services()->pluck('id')->toArray();
    
        // Fetch the commands where service_id matches the freelancer's services and user_id matches the freelancer
        $commands = Command::whereIn('service_id', $services)
            ->with('user') // Include the user details in the commands
            ->with('service')
            ->get();
    
        // Return the commands as a JSON response
        return response()->json($commands);
    }

    public function getCommandsOfCustomers()
    {
        $userId = Auth::id();
    
        // Fetch the commands where service_id matches the freelancer's services and user_id matches the freelancer
        $commands = Command::where('user_id', $userId)
            ->with('user') // Include the user details in the commands
            ->with(['service' => function ($query) {
                $query->with(['freelancer' => function ($query) {
                    $query->with('user');
                }]);
            }])
            ->get();
    
        // Return the commands as a JSON response
        return response()->json($commands);
    }


    public function update(Request $request, Command $command)
    {
        $userId = Auth::id();
        $freelancer = Freelancer::where('user_id', $userId)->first();
  
        // Check if the command belongs to the logged-in user
        if (!$freelancer) {
            return response()->json(['message' => 'User is not a freelancer'], 403);
        }
    
  
        // Validate the incoming request data
        $validatedData = $request->validate([
            'delivery_date' => 'nullable|date',
            'status' => 'required|in:Accepte,Refuse',
        ]);
  
        // Update the command with the validated data
        $command->update([
            'delivery_date' => $validatedData['delivery_date'],
            'status' => $validatedData['status'],
        ]);
        $command->save();
        // Return a response or redirect as needed
        return response()->json(['message' => 'Command updated successfully']);
    }



    public function uploadFiles(Request $request, $id)
    {
        $userId = Auth::id();
        $freelancer = Freelancer::where('user_id', $userId)->first();
    
        $command = Command::findOrFail($id);
        $serviceId = $command->service_id;
        $services = Service::findOrFail($serviceId);
        $freelancerCommandId = $services->freelancer_id;
    
        // Check if the authenticated user is the freelancer associated with the command
        if ($freelancer->id !== $freelancerCommandId) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    
        // Check if the files were uploaded successfully
        if ($request->hasFile('files')) {
            $files = $request->file('files');
    
            foreach ($files as $file) {
                // Store the file in the storage/app/public directory
                $path = $file->store('products');
    
                // Add the file path to the delivery_product attribute of the command
                $command->delivery_product = $path;
                $command->save();
            }
    
            return response()->json(['message' => 'Files uploaded successfully']);
        }
    
        return response()->json(['error' => 'No files uploaded'], 400);
    }
}
