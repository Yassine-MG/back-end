<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Service;
use App\Models\Freelancer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class ServiceController extends Controller
{
    public function show($id)
    {
        $service = Service::find($id);
    
        if (!$service) {
            return response()->json(['error' => 'Service not found'], 404);
        }
    
        $owner = Freelancer::where('id', $service->freelancer_id)->with('user')->first();
    
        if (!$owner) {
            return response()->json(['error' => 'Owner not found'], 404);
        }
    
        $user = $owner->user;
    
        return response()->json([
            'service' => $service,
            'freelancer' => $owner,
            'user' => $user
        ]);
    }


    public function displayinprofile()
    {
        $user = Auth::user();
        $services = Service::where('freelancer_id', $user->freelancer->id)->get();
        return response()->json(['services' => $services , 'user'=>$user]);
    }




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
                $service->freelancer_id = auth()->user()->freelancer->id;
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
            'offer_name' => 'required|string|max:55',
            'details' => 'required|string|max:200',
            'price' => 'required|string',
            'delevery' => 'required|string',
        ];

        $service = Service::findOrFail($id);

        $user = Auth::user();
        if ($service->freelancer_id == $user->freelancer->id) {
            $validatedData = $request->validate($rules);
    
            // Update the service details
            $service->update($validatedData);
            $service->save();
    
            $dynamicInputs = [];
    
            if ($request->has('dynamicInputs')) {
                foreach ($request->input('dynamicInputs') as $input) {
                    if (isset($input['value'])) {
                        $dynamicInputs[] = [
                            'name' => $input['name'],
                            'value' => $input['value'],
                        ];
                    }
                }
            }
    
            // Save the dynamic inputs in the services table
            $service->dynamic_inputs = $dynamicInputs;
            $service->save();
    
            return response()->json(['message' => 'Service updated successfully', 'user' => $user, 'service' => $service]);
            
        }

    }
    
    public function uploadPictures(Request $request, $id)
    {
        // Check if the user is a freelancer
        if (Auth::user()->role !== 'Freelancer') {
            return response()->json(['message' => 'You are not a freelancer']);
        }
    
        // Validate the incoming request
        $rules = [
            'pictures' => 'sometimes|array|max:3', // Validate that pictures is an array with a maximum of 3 elements
            'pictures.*' => 'image|mimes:jpeg,png,jpg|max:4000',// Validate each picture file
            'video' => 'sometimes|file|mimes:mp4|max:50000', // Validate the video file
        ];
    
        $validatedData = $request->validate($rules);
        // Find the service and the authenticated user
        $service = Service::findOrFail($id);
        $user = Auth::user();
        // Process and store the uploaded pictures
        $pictures = [];
        if (isset($validatedData['pictures'])) {
            foreach ($validatedData['pictures'] as $index => $picture) {
                $path = $picture->store('public/media/pictures');
                $pictures[$index] = str_replace('public/', '/storage/', $path);
            }
        }
    
        // Save the picture paths in the service model
        $service->image1 = isset($pictures[0]) ? $pictures[0] : null;
        $service->image2 = isset($pictures[1]) ? $pictures[1] : null;
        $service->image3 = isset($pictures[2]) ? $pictures[2] : null;
    
        // Save the video path in the service model
        $service->video = $request->hasFile('video') ? str_replace('public/', '/storage/', $request->file('video')->store('public/media/videos')) : null;
    
        $service->save();
    
        return response()->json(['message' => 'Pictures and video uploaded successfully', 'user' => $user, 'service' => $service]);
    }

    public function searchServices(Request $request)
    {
        $searchQuery = $request->query('search');
        $category = $request->query('category');
        $price = $request->query('price');
        $delevery = $request->query('delevery');
    
        $query = Service::query();
    
        if ($searchQuery) {
            $query->where('title', 'like', "%$searchQuery%");
        }
    
        if ($category && $category !== 'All categories') {
            $query->where('category', $category);
        }
    
        if ($price && $price !== 'default') {
            if ($price === 'low_to_high') {
                $query->orderBy('price', 'asc');
            } elseif ($price === 'high_to_low') {
                $query->orderBy('price', 'desc');
            }
        }
        if ($delevery && $delevery !== 'All') {
            $query->where('delevery', '=', $delevery); // Modify the where clause
        }
    
        $services = $query->with('freelancer.user')
            ->orderBy('title', 'desc')
            ->get();
    
        $suggestedServices = Service::where('title', 'like', "$searchQuery%")
            ->orderBy('title', 'asc')
            ->get();
    
        return response()->json(['services' => $services, 'suggestedServices' => $suggestedServices]);
    }
    


        public function editservice(Request $request, $id)
        {
            // Find the service by ID
            $service = Service::findOrFail($id);

            // Get the authenticated user
            $user = auth()->user();

            // Find the freelancer profile associated with the user
            $freelancer = Freelancer::where('user_id', $user->id)->first();

            // Check if the authenticated user is the owner of the service
            if ($freelancer && $service->freelancer_id === $freelancer->id) {
                // Update the service with the new data
                $service->update($request->all());

                // Return the updated service as a response
                return response()->json($service);
            }

            // Return an error response if the user is not authorized
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        public function updatePrice(Request $request, $id)
        {
            // Retrieve the service by its ID
            $service = Service::findOrFail($id);

            // Update the service with the new data
            $service->offer_name = $request->input('offer_name');
            $service->details = $request->input('details');
            $service->price = $request->input('price');
            $service->delevery = $request->input('delevery');

            // Save the updated service
            $service->save();

            // Optionally, you can return a response with the updated service
            return response()->json(['service' => $service]);
        }

        


        public function getService($id)
        {
            try {
                $service = Service::findOrFail($id);
                return response()->json($service);
            } catch (\Exception $e) {
                return response()->json(['error' => 'Service not found'], 404);
            }
        }


        public function deleteService($id)
        {
            $service = Service::findOrFail($id);
            $service->delete();
            
            // Optionally, you can return a response or perform any additional actions
        }


        public function deleteSelected(Request $request)
        {
            $serviceIds = $request->input('data');

            if (is_array($serviceIds) && count($serviceIds) > 0) {
                // Delete the selected services
                Service::whereIn('id', $serviceIds)->delete();

                return response()->json(['message' => 'Selected services deleted successfully']);
            } else {
                return response()->json(['message' => 'No service IDs provided or invalid input']);
            }
        }

        public function searchServiceHomepage(Request $request)
        {
            $query = $request->query('query');
            $selectedCategory = $request->query('category');
            $selectedSubcategory = $request->query('subcategory');
        
            $servicesQuery = Service::where('title', 'like', '%' . $query . '%');
        
            if ($selectedCategory) {
                $servicesQuery->where('category', $selectedCategory);
            }
        
            if ($selectedSubcategory) {
                $selectedSubcategory = is_array($selectedSubcategory) ? $selectedSubcategory : [$selectedSubcategory];
        
                $servicesQuery->where(function ($query) use ($selectedSubcategory) {
                    foreach ($selectedSubcategory as $subcategory) {
                        $query->orWhereJsonContains('skills', $subcategory);
                    }
                });
            }
        
            $filteredServices = $servicesQuery->get();
            $filteredServices->load('freelancer.user');
            return response()->json(['results' => $filteredServices]);
        }


        public function countServicesInCategory(Request $request) {
            $categories = ['Developer', 'Data', 'Writing & Translation', 'Business', 'Video & Animation', 'Designer'];
    
            $icons = [
                'Developer' => 'bi bi-laptop',
                'Data' => 'bi bi-database',
                'Writing & Translation' => 'bi bi-pencil',
                'Business' => 'bi bi-briefcase',
                'Video & Animation' => 'bi bi-camera-video',
                'Designer' => 'bi bi-palette',
            ];
        
            $counts = [];
        
            foreach ($categories as $category) {
                $count = Service::where('category', $category)->count();
                $counts[] = [
                    'category' => $category,
                    'count' => $count,
                    'icon' => $icons[$category] // Retrieve the icon based on the category
                ];
            }
        
            return response()->json($counts);
        }


        public function retrieveBestServices(Request $request){
            $services = DB::table('services')
                        ->orderBy('like', 'desc')
                        ->limit(3)
                        ->get();
            
            return response()->json($services);
        }
    }


