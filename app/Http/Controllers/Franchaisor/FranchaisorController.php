<?php

namespace App\Http\Controllers\Franchaisor;

use App\Http\Controllers\Controller;
use App\Models\Address\Country;
use App\Models\Franchaisor\Franchaisor;
use App\Models\Franchaisor\FranchaisorCountries;
use App\Models\Franchaisor\FranchaisorRequest;
use App\Models\Franchaisor\FranchaisorFile;
use App\Traits\CommonTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use App\Lib\Data\SojebData;

class FranchaisorController extends Controller
{
    use CommonTrait;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $brief_min_investment = $request->input('brief_min_investment');


            $query = Franchaisor::query();

            // Apply filters based on query parameters
            if ($request->has('duration')) {
                $dateLimit = now()->subDays($request->query('duration', 7)); // Default 7 days
                $query->where('created_at', '>=', $dateLimit);
            }

            if ($request->has('start_date') && $request->has('end_date')) {
                $query->whereBetween('joined_at', [
                    Carbon::parse($request->query('start_date'))->format('Y-m-d 00:00:00'),
                    Carbon::parse($request->query('end_date'))->format('Y-m-d 23:59:59')
                ]);
            } elseif ($request->has('start_date')) {
                $query->where('joined_at', '=', Carbon::parse($request->query('start_date'))->format('Y-m-d 00:00:00'));
            } elseif ($request->has('end_date')) {
                $query->where('end_at', '=', Carbon::parse($request->query('end_date'))->format('Y-m-d 23:59:59'));
            }

            if ($request->has('industry')) {
                $query->where('industry', 'like', '%' . $request->query('industry') . '%');
            }

            if ($request->has('location_id')) {
                $query->where('address', (int) $request->query('location_id'));
            }

            if ($request->has('brand_name')) {
                $query->where('brand_name', 'like', '%' . $request->query('brand_name') . '%');
            }

            if ($request->has('brief_min_investment')) {
                $query->where('brief_min_investment', 'like', '%' . $brief_min_investment . '%');
            }

            $franchaisors = $query->get();

            foreach ($franchaisors as $franchaisor) {
                if ($franchaisor->logo_path) {
                    $franchaisor->logo_path = Storage::url($franchaisor->logo_path);
                }
                $cover_images = FranchaisorFile::where('franchaisor_id', $franchaisor->id)->where('type', 'cover')->get();
                $franchaisor->cover_images = $cover_images->map(function ($file) {
                    if ($file->file_path && $file->file_path != null) {
                        return [
                            'id' => $file->id,
                            'file_path' => Storage::url($file->file_path),
                            'file_type' => $file->file_type,
                            'type' => $file->type,
                        ];
                    }
                });
                $brief_gallary_images = FranchaisorFile::where('franchaisor_id', $franchaisor->id)->where('type', 'brief')->get();
                $franchaisor->brief_gallary_images = $brief_gallary_images->map(function ($file) {
                    if ($file->file_path && $file->file_path != null) {
                        return [
                            'id' => $file->id,
                            'file_path' => Storage::url($file->file_path),
                            'file_type' => $file->file_type,
                            'type' => $file->type,
                        ];
                    }
                });
                $brief_video = FranchaisorFile::where('franchaisor_id', $franchaisor->id)->where('type', 'brief_video')->first();
                if ($brief_video && $brief_video->file_path && $brief_video->file_path != null) {
                    $franchaisor->brief_video = [
                        'id' => $brief_video->id,
                        'file_path' => Storage::url($brief_video->file_path),
                        'file_type' => $brief_video->file_type,
                        'type' => $brief_video->type,
                    ];
                }
                $details1_images = FranchaisorFile::where('franchaisor_id', $franchaisor->id)->where('type', 'details1')->get();
                $franchaisor->details1_images = $details1_images->map(function ($file) {
                    if ($file->file_path && $file->file_path != null) {
                        return [
                            'id' => $file->id,
                            'file_path' => Storage::url($file->file_path),
                            'file_type' => $file->file_type,
                            'type' => $file->type,
                        ];
                    }
                });
                $details2_images = FranchaisorFile::where('franchaisor_id', $franchaisor->id)->where('type', 'details2')->get();
                $franchaisor->details2_images = $details2_images->map(function ($file) {
                    if ($file->file_path) {
                        return [
                            'id' => $file->id,
                            'file_path' => Storage::url($file->file_path),
                            'file_type' => $file->file_type,
                            'type' => $file->type,
                        ];
                    }
                });
                $interested_countries = FranchaisorCountries::where('franchaisor_id', $franchaisor->id)->pluck('country_id');
                $franchaisor->interested_countries = Country::whereIn('id', $interested_countries)->get();
                $franchaisor->location = Country::where('id', (int) $franchaisor->address)->first();
            }

            return $this->sendResponse($franchaisors);
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), 500);
        }
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create() {}

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request->all());
        $validator = Validator::make($request->all(), [
            'brand_name' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone_number' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'interested_countries' => 'required',
            'industry' => 'required|string|max:255',
            // 'investment' => 'required|numeric',
            'timeframe' => 'required|string|max:255',
            'joined_at' => 'required|date',
            'end_at' => 'required|date',
            // 'cover_images' => 'file|mimes:jpeg,png,jpg,gif,svg|max:2048',
            // 'logo' => 'file|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'brief_heading' => 'required|string|max:255',
            'brief_description' => 'required|string|max:255',
            'brief_country_of_region' => 'required|string|max:255',
            'brief_available' => 'required|string|max:255',
            'brief_business_type' => 'required|string|max:255',
            'brief_min_investment' => 'required|numeric',
            // 'details1_heading' => 'required|string|max:255',
            // 'details1_description' => 'required|string|max:255',
            // 'details2_heading' => 'required|string|max:255',
            // 'details2_description' => 'required|string|max:255',
            // 'brief_gallary_images' => 'file|mimes:jpeg,png,jpg,gif,svg|max:2048',
            // 'brief_video' => 'file',
            // 'details1_images' => 'file|mimes:jpeg,png,jpg,gif,svg|max:2048',
            // 'details2_images' => 'file|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        //create franchaisor
        $franchaisor = new Franchaisor();
        $franchaisor->brand_name = $request->brand_name;
        $franchaisor->name = $request->name;
        $franchaisor->position = $request->position;
        $franchaisor->email = $request->email;
        $franchaisor->phone_number = $request->phone_number;
        $franchaisor->address = $request->location;
        $franchaisor->industry = $request->industry;
        // $franchaisor->investment = $request->investment;
        $franchaisor->timeframe = $request->timeframe;
        $franchaisor->joined_at = $request->joined_at;
        $franchaisor->end_at = $request->end_at;
        $franchaisor->brief_heading = $request->brief_heading;
        $franchaisor->brief_description = $request->brief_description;
        $franchaisor->brief_country_of_region = $request->brief_country_of_region;
        $franchaisor->brief_available = $request->brief_available;
        $franchaisor->brief_business_type = $request->brief_business_type;
        $franchaisor->brief_min_investment = $request->brief_min_investment;
        $franchaisor->details1_heading = $request->details1_heading;
        $franchaisor->details1_description = $request->details1_description;
        $franchaisor->details2_heading = $request->details2_heading;
        $franchaisor->details2_description = $request->details2_description;
        // $franchaisor->save();


        //create logo
        if ($request->hasFile('logo')) {
            $logo = $request->file('logo');
            $path = Storage::put('franchaisors', $logo);
            $franchaisor->logo_path = $path;
        }
        $franchaisor->save();

        //create franchaisor countries 
        foreach ($request->interested_countries as $country) {
            $franchaisorCountry = new FranchaisorCountries();
            $franchaisorCountry->franchaisor_id = $franchaisor->id;
            $franchaisorCountry->country_id = $country;
            $franchaisorCountry->save();
        }


        //create cover images
        if ($request->hasFile('cover_images')) {
            $coverImages = $request->file('cover_images');
            foreach ($coverImages as $image) {
                $path = Storage::put('franchaisors', $image);
                $franchaisorFile = new FranchaisorFile();
                $franchaisorFile->file_path = "$path";
                $franchaisorFile->file_type = $image->getClientOriginalExtension();
                $franchaisorFile->type = 'cover';
                $franchaisorFile->franchaisor_id = $franchaisor->id;
                $franchaisorFile->save();
            }
        }

        //create brief gallary images
        if ($request->hasFile('brief_gallery_images')) {
            $briefGalleryImages = $request->file('brief_gallery_images');
            foreach ($briefGalleryImages as $image) {
                $path = Storage::put('franchaisors', $image);
                $franchaisorFile = new FranchaisorFile();
                $franchaisorFile->file_path = "$path";
                $franchaisorFile->file_type = $image->getClientOriginalExtension();
                $franchaisorFile->type = 'brief';
                $franchaisorFile->franchaisor_id = $franchaisor->id;
                $franchaisorFile->save();
            }
        }

        //create brief video
        if ($request->hasFile('brief_video')) {
            $briefVideo = $request->file('brief_video');
            $path = Storage::put('franchaisors', $briefVideo);
            $franchaisorFile = new FranchaisorFile();
            $franchaisorFile->file_path = "$path";
            $franchaisorFile->file_type = $briefVideo->getClientOriginalExtension();
            $franchaisorFile->type = 'brief_video';
            $franchaisorFile->franchaisor_id = $franchaisor->id;
            $franchaisorFile->save();
        }

        //create details1 images
        if ($request->hasFile('details1_images')) {
            $details1Images = $request->file('details1_images');
            foreach ($details1Images as $image) {
                $path = Storage::put('franchaisors', $image);
                $franchaisorFile = new FranchaisorFile();
                $franchaisorFile->file_path = "$path";
                $franchaisorFile->file_type = $image->getClientOriginalExtension();
                $franchaisorFile->type = 'details1';
                $franchaisorFile->franchaisor_id = $franchaisor->id;
                $franchaisorFile->save();
            }
        }

        //create details2 images
        if ($request->hasFile('details2_images')) {
            $details2Images = $request->file('details2_images');
            foreach ($details2Images as $image) {
                $path = Storage::put('franchaisors', $image);
                $franchaisorFile = new FranchaisorFile();
                $franchaisorFile->file_path = "$path";
                $franchaisorFile->file_type = $image->getClientOriginalExtension();
                $franchaisorFile->type = 'details2';
                $franchaisorFile->franchaisor_id = $franchaisor->id;
                $franchaisorFile->save();
            }
        }


        return $this->sendResponse(['franchaisor' => $franchaisor, 'message' => 'Franchaisor created successfully']);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $franchaisor = Franchaisor::find($id);
            // $franchaisor->files = FranchaisorFile::where('franchaisor_id', $id)->get();
            $franchaisor->logo_path = Storage::url($franchaisor->logo_path);
            $cover_images = FranchaisorFile::where('franchaisor_id', $id)->where('type', 'cover')->get();
            // check if cover_images is not empty
            if ($cover_images->isNotEmpty()) {
                $franchaisor->cover_images = $cover_images->map(function ($file) {
                    return [
                        'id' => $file->id,
                        'file_path' => Storage::url($file->file_path),
                        'file_type' => $file->file_type,
                        ];
                    });
            }
            $brief_gallary_images = FranchaisorFile::where('franchaisor_id', $id)->where('type', 'brief')->get();
            if ($brief_gallary_images->isNotEmpty()) {
                $franchaisor->brief_gallary_images = $brief_gallary_images->map(function ($file) {
                    return [
                        'id' => $file->id,
                        'file_path' => Storage::url($file->file_path),
                        'file_type' => $file->file_type,
                        ];
                    });
            }
            $brief_video = FranchaisorFile::where('franchaisor_id', $id)->where('type', 'brief_video')->first();
            if ($brief_video) {
                $franchaisor->brief_video = [
                    'id' => $brief_video->id,
                    'file_path' => Storage::url($brief_video->file_path),
                    'file_type' => $brief_video->file_type,
                ];
            }
            $details1_images = FranchaisorFile::where('franchaisor_id', $id)->where('type', 'details1')->get();
            if ($details1_images->isNotEmpty()) {
                $franchaisor->details1_images = $details1_images->map(function ($file) {
                    return [
                        'id' => $file->id,
                        'file_path' => Storage::url($file->file_path),
                        'file_type' => $file->file_type,
                        'type' => $file->type,
                    ];
                });
            }
            $details2_images = FranchaisorFile::where('franchaisor_id', $id)->where('type', 'details2')->get();
            if ($details2_images->isNotEmpty()) {
                $franchaisor->details2_images = $details2_images->map(function ($file) {
                    return [
                    'id' => $file->id,
                    'file_path' => Storage::url($file->file_path),
                    'file_type' => $file->file_type,
                    'type' => $file->type,
                    ];
                });
            }
            $franchaisor->interested_countries = FranchaisorCountries::where('franchaisor_id', $id)->get();
            $franchaisor->location = Country::where('id', (int) $franchaisor->address)->first();
            return $this->sendResponse($franchaisor);
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), 500);
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
    public function update(Request $request, string $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'brand_name' => 'string|max:255',
                'name' => 'string|max:255',
                'position' => 'string|max:255',
                'email' => 'string|email|max:255|unique:users',
                'phone_number' => 'string|max:255',
                'location' => 'string|max:255',
                // 'interested_countries' => 'required',
                'industry' => 'string|max:255',
                'timeframe' => 'string|max:255',
                'joined_at' => 'date',
                'end_at' => 'date',
                // 'cover_images' => 'file|mimes:jpeg,png,jpg,gif,svg|max:2048',
                // 'logo' => 'file|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'brief_heading' => 'string|max:255',
                'brief_description' => 'string|max:255',
                'brief_country_of_region' => 'string|max:255',
                'brief_available' => 'string|max:255',
                'brief_business_type' => 'string|max:255',
                'brief_min_investment' => 'numeric',
                // 'details1_images' => 'file|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'details1_heading' => 'string|max:255',
                'details1_description' => 'string|max:255',
                // 'details2_images' => 'file|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'details2_heading' => 'string|max:255',
                'details2_description' => 'string|max:255',
                // 'brief_gallary_images' => 'file|mimes:jpeg,png,jpg,gif,svg|max:2048',
                // 'brief_video' => 'file|mimes:mp4,mov,avi,wmv,flv,mpeg,mpg,m4v,3gp,3g2,mj2,webm,mkv|max:2048',
            ]);

            if ($validator->fails()) {
                return $this->sendError('Validation Error.', $validator->errors());
            }

            //update franchaisor
            $franchaisor = Franchaisor::find($id);
            if ($request->brand_name) {
                $franchaisor->brand_name = $request->brand_name;
            }
            if ($request->name) {
                $franchaisor->name = $request->name;
            }
            if ($request->position) {
                $franchaisor->position = $request->position;
            }
            if ($request->email) {
                $franchaisor->email = $request->email;
            }
            if ($request->phone_number) {
                $franchaisor->phone_number = $request->phone_number;
            }
            if ($request->location) {
                $franchaisor->address = $request->location;
            }
            if ($request->industry) {
                $franchaisor->industry = $request->industry;
            }
            if ($request->timeframe) {
                $franchaisor->timeframe = $request->timeframe;
            }
            if ($request->joined_at) {
                $franchaisor->joined_at = $request->joined_at;
            }
            if ($request->end_at) {
                $franchaisor->end_at = $request->end_at;
            }
            if ($request->brief_heading) {
                $franchaisor->brief_heading = $request->brief_heading;
            }
            if ($request->brief_description) {
                $franchaisor->brief_description = $request->brief_description;
            }
            if ($request->brief_country_of_region) {
                $franchaisor->brief_country_of_region = $request->brief_country_of_region;
            }
            if ($request->brief_available) {
                $franchaisor->brief_available = $request->brief_available;
            }
            if ($request->brief_business_type) {
                $franchaisor->brief_business_type = $request->brief_business_type;
            }
            if ($request->brief_min_investment) {
                $franchaisor->brief_min_investment = $request->brief_min_investment;
            }
            if ($request->details1_heading) {
                $franchaisor->details1_heading = $request->details1_heading;
            }
            if ($request->details1_description) {
                $franchaisor->details1_description = $request->details1_description;
            }
            $franchaisor->save();

            //update franchaisor countries
            if ($request->interested_countries) {
                FranchaisorCountries::where('franchaisor_id', $id)->delete();
                foreach ($request->interested_countries as $country) {
                    $franchaisorCountry = new FranchaisorCountries();
                    $franchaisorCountry->franchaisor_id = $franchaisor->id;
                    $franchaisorCountry->country_id = $country;
                    $franchaisorCountry->save();
                }
            }


            //update logo
            if ($request->hasFile('logo')) {
                $logo = $request->file('logo');
                $path = Storage::put('franchaisors', $logo);
                $franchaisor->logo_path = $path;
                $franchaisor->save();
            }

            // Step 1: Upload new files and collect their IDs
            $uploadedImageIds = [];

            if ($request->hasFile('cover_images')) {
                $coverImages = $request->file('cover_images');

                foreach ($coverImages as $image) {
                    $path = Storage::put('franchaisors', $image);

                    $franchaisorFile = new FranchaisorFile();
                    $franchaisorFile->file_path = $path;
                    $franchaisorFile->file_type = $image->getClientOriginalExtension();
                    $franchaisorFile->type = 'cover';
                    $franchaisorFile->franchaisor_id = $franchaisor->id;
                    $franchaisorFile->save();

                    // Store the newly uploaded file's ID
                    $uploadedImageIds[] = $franchaisorFile->id;
                }
            }

            // Step 2: Process existing cover_images from payload
            $cleanImageIds = [];

            if ($request->input('cover_images')) {
                // convert this string [1,2,3] to array
                if (is_array($request->input('cover_images'))) {
                    $coverImages = $request->input('cover_images');
                } else {
                    $coverImages = json_decode($request->input('cover_images'));
                }

                // Step 3: Fetch existing files
                $existingFiles = FranchaisorFile::where('franchaisor_id', $id)
                    ->where('type', 'cover')
                    ->get();

                // Step 4: Delete only files that are **not in the cleanImageIds or uploadedImageIds**
                foreach ($existingFiles as $file) {
                    if (!in_array((int) $file->id, $coverImages) && !in_array((int) $file->id, $uploadedImageIds)) {
                        Storage::delete($file->file_path);
                        $file->delete();
                    }
                }
            }

            //update brief gallary images
            $uploadedBriefGalleryImageIds = [];
            if ($request->hasFile('brief_gallery_images')) {
                $briefGalleryImages = $request->file('brief_gallery_images');
                foreach ($briefGalleryImages as $image) {
                    $path = Storage::put('franchaisors', $image);
                    $franchaisorFile = new FranchaisorFile();
                    $franchaisorFile->file_path = $path;
                    $franchaisorFile->file_type = $image->getClientOriginalExtension();
                    $franchaisorFile->type = 'brief';
                    $franchaisorFile->franchaisor_id = $franchaisor->id;
                    $franchaisorFile->save();
                }
                $uploadedBriefGalleryImageIds[] = $franchaisorFile->id;
            } 
            
            $cleanImageIds = [];
            if ($request->input('brief_gallery_images')) {
                if (is_array($request->input('brief_gallery_images'))) {
                    $briefGalleryImages = $request->input('brief_gallery_images');
                } else {
                    $briefGalleryImages = json_decode($request->input('brief_gallery_images'));
                }

                $existingBriefGalleryFiles = FranchaisorFile::where('franchaisor_id', $id)
                    ->where('type', 'brief')
                    ->get();

                foreach ($existingBriefGalleryFiles as $file) {
                    if (!in_array((int) $file->id, $briefGalleryImages) && !in_array((int) $file->id, $uploadedBriefGalleryImageIds)) {
                        Storage::delete($file->file_path);
                        $file->delete();
                    }
                }
            }

            //update brief video
            if ($request->hasFile('brief_video')) {
                $existingFile = FranchaisorFile::where('franchaisor_id', $id)
                    ->where('type', 'brief_video')
                    ->first();
                if ($existingFile) {
                    Storage::delete($existingFile->file_path);
                    $existingFile->delete();
                }
                $briefVideo = $request->file('brief_video');
                $path = Storage::put('franchaisors', $briefVideo);
                $franchaisorFile = new FranchaisorFile();
                $franchaisorFile->file_path = $path;
                $franchaisorFile->file_type = $briefVideo->getClientOriginalExtension();
                $franchaisorFile->type = 'brief_video';
                $franchaisorFile->franchaisor_id = $franchaisor->id;
                $franchaisorFile->save();
            }

            // do here same as cover images
            //update details1 images
            $uploadedDetails1ImageIds = [];
            if ($request->hasFile('details1_images')) {
                $details1Images = $request->file('details1_images');
                foreach ($details1Images as $image) {
                    $path = Storage::put('franchaisors', $image);
                    $franchaisorFile = new FranchaisorFile();
                    $franchaisorFile->file_path = $path;
                    $franchaisorFile->file_type = $image->getClientOriginalExtension();
                    $franchaisorFile->type = 'details1';
                    $franchaisorFile->franchaisor_id = $franchaisor->id;
                    $franchaisorFile->save();
                }
                $uploadedDetails1ImageIds[] = $franchaisorFile->id;
            } 
            $cleanImageIds = [];
            if ($request->input('details1_images')) {
                if (is_array($request->input('details1_images'))) {
                    $details1Images = $request->input('details1_images');
                } else {
                    $details1Images = json_decode($request->input('details1_images'));
                }

                $existingDetails1Files = FranchaisorFile::where('franchaisor_id', $id)
                    ->where('type', 'details1')
                    ->get();

                foreach ($existingDetails1Files as $file) {
                    if (!in_array((int) $file->id, $details1Images) && !in_array((int) $file->id, $uploadedDetails1ImageIds)) {
                        Storage::delete($file->file_path);
                        $file->delete();
                    }
                }
            }

            //update details2 images
            $uploadedDetails2ImageIds = [];
            if ($request->hasFile('details2_images')) {
                $details2Images = $request->file('details2_images');
                foreach ($details2Images as $image) {
                    $path = Storage::put('franchaisors', $image);
                    $franchaisorFile = new FranchaisorFile();
                    $franchaisorFile->file_path = $path;
                    $franchaisorFile->file_type = $image->getClientOriginalExtension();
                    $franchaisorFile->type = 'details2';
                    $franchaisorFile->franchaisor_id = $franchaisor->id;
                    $franchaisorFile->save();
                }
                $uploadedDetails2ImageIds[] = $franchaisorFile->id;
            }
            $cleanImageIds = [];
            if ($request->input('details2_images')) {
                if (is_array($request->input('details2_images'))) {
                    $details2Images = $request->input('details2_images');
                } else {
                    $details2Images = json_decode($request->input('details2_images'));
                }

                $existingDetails2Files = FranchaisorFile::where('franchaisor_id', $id)
                    ->where('type', 'details2')
                    ->get();

                foreach ($existingDetails2Files as $file) {
                    if (!in_array((int) $file->id, $details2Images) && !in_array((int) $file->id, $uploadedDetails2ImageIds)) {
                        Storage::delete($file->file_path);
                        $file->delete();
                    }
                }
            }
            return $this->sendResponse(['franchaisor' => $franchaisor, 'message' => 'Franchaisor updated successfully']);
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), 500);
        }
    }

    public function exportData(Request $request)
    {
        $fileName = 'franchaisors.csv';
        $franchisorsData = Franchaisor::all();

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function () use ($franchisorsData) {
            $handle = fopen('php://output', 'w');

            // CSV headers
            $fields = ['ID', 'Name', 'Company Name', 'Email', 'Industry', 'Based On', 'Interested Expansion', 'Timeframe', 'Date of Request', 'Ending Date'];
            fputcsv($handle, $fields);

            foreach ($franchisorsData as $data) {
                $expansion = FranchaisorCountries::with('country')
                    ->where('franchaisor_id', $data->id)
                    ->get();
                $expansion = $expansion->map(function ($item) {
                    return $item->country->name;
                });
                $expansion = $expansion->implode(', ');

                fputcsv($handle, [
                    $data->id,
                    $data->name,
                    $data->brand_name,
                    $data->email,
                    $data->industry,
                    $data->address,
                    $expansion,
                    $data->timeframe,
                    $data->joined_at,
                    $data->end_at
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, Response::HTTP_OK, $headers);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $franchaisor = Franchaisor::find($id);
            $franchaisor->delete();
            return $this->sendResponse(['message' => 'Franchaisor deleted successfully']);
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), 500);
        }
    }

    // contact us
    public function franchaisorRequest(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'nullable|string|max:255',
                'brand_name' => 'nullable|string|max:255',
                'email' => 'nullable|string|email|max:255',
                'phone_number' => 'nullable|string|max:255',
                'country' => 'nullable|string|max:255',
                'subject' => 'nullable|string|max:255',
                'message' => 'nullable|string|max:255',
            ]);

            if ($validator->fails()) {
                return $this->sendError('Validation Error.', $validator->errors());
            }

            $franchaisorRequest = new FranchaisorRequest();
            $franchaisorRequest->name = $request->name;
            $franchaisorRequest->brand_name = $request->brand_name;
            $franchaisorRequest->email = $request->email;
            $franchaisorRequest->phone_number = $request->phone_number;
            $franchaisorRequest->country = $request->country;
            $franchaisorRequest->subject = $request->subject;
            $franchaisorRequest->message = $request->message;
            $franchaisorRequest->save();

            return $this->sendResponse(['franchaisorRequest' => $franchaisorRequest, 'message' => 'Franchaisor request sent successfully']);
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), 500);
        }
    }

    public function franchaisorRequests(Request $request)
    {
        try {
            $franchaisorRequests = FranchaisorRequest::query()
                ->where('status', '!=', 'rejected')
                ->get();



            return $this->sendResponse($franchaisorRequests);
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), 500);
        }
    }

    public function showFranchaisorRequest(Request $request, string $id)
    {
        try {
            $franchaisorRequest = FranchaisorRequest::find($id);

            if (!$franchaisorRequest) {
                return $this->sendError('Franchaisor request not found');
            }

            return $this->sendResponse($franchaisorRequest);
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), 500);
        }
    }


    public function franchaisorRequestUpdate(Request $request, string $id)
    {
        try {
            $franchaisorRequest = FranchaisorRequest::find($id);
            $franchaisorRequest->status = $request->status;
            $franchaisorRequest->save();
            return $this->sendResponse(['franchaisorRequest' => $franchaisorRequest, 'message' => 'Franchaisor request updated successfully']);
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), 500);
        }
    }

    public function franchaisorRequestDelete(Request $request, string $id)
    {
        try {
            $franchaisorRequest = FranchaisorRequest::find($id);
            $franchaisorRequest->delete();
            return $this->sendResponse(['franchaisorRequest' => $franchaisorRequest, 'message' => 'Franchaisor request deleted successfully']);
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), 500);
        }
    }
}
