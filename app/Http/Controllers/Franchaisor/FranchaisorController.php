<?php

namespace App\Http\Controllers\Franchaisor;

use App\Http\Controllers\Controller;
use App\Models\Franchaisor\Franchaisor;
use App\Models\Franchaisor\FranchaisorFile;
use App\Traits\CommonTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FranchaisorController extends Controller
{
    use CommonTrait;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'brand_name' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone_number' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'industry' => 'required|string|max:255',
            'investment' => 'required|numeric',
            'timeframe' => 'required|string|max:255',
            'joined_at' => 'required|date',
            'end_at' => 'required|date',
            'cover_images' => 'file|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'logo' => 'file|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'brief_heading' => 'required|string|max:255',
            'brief_description' => 'required|string|max:255',
            'brief_country_of_region' => 'required|string|max:255',
            'brief_available' => 'required|string|max:255',
            'brief_business_type' => 'required|string|max:255',
            'brief_min_investment' => 'required|numeric',
            'details1_heading' => 'required|string|max:255',
            'details1_description' => 'required|string|max:255',
            'details2_heading' => 'required|string|max:255',
            'details2_description' => 'required|string|max:255',
            'brief_gallary_images' => 'file|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'brief_video' => 'file|mimes:mp4,mov,avi,wmv,flv,mpeg,mpg,m4v,3gp,3g2,mj2,webm,mkv|max:2048',
            'details1_images' => 'file|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'details2_images' => 'file|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $franchaisor = new Franchaisor();
        $franchaisor->brand_name = $request->brand_name;
        $franchaisor->name = $request->name;
        $franchaisor->position = $request->position;
        $franchaisor->email = $request->email;
        $franchaisor->phone_number = $request->phone_number;
        $franchaisor->address = $request->address;
        $franchaisor->industry = $request->industry;
        $franchaisor->investment = $request->investment;
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
        $franchaisor->save();

        if ($request->hasFile('logo')) {
            $logo = $request->file('logo');
            $path = Storage::put('franchaisors', $logo);
            $franchaisor->logo_path = $path;
            $franchaisor->save();
        }

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
            }
        }

        if ($request->hasFile('brief_gallary_images')) {
            $briefGallaryImages = $request->file('brief_gallary_images');
            foreach ($briefGallaryImages as $image) {
                $path = Storage::put('franchaisors', $image);
                $franchaisorFile = new FranchaisorFile();
                $franchaisorFile->file_path = $path;
                $franchaisorFile->file_type = $image->getClientOriginalExtension();
                $franchaisorFile->type = 'brief';
                $franchaisorFile->franchaisor_id = $franchaisor->id;
                $franchaisorFile->save();
            }
        }

        if ($request->hasFile('brief_video')) {
            $briefVideo = $request->file('brief_video');
            $path = Storage::put('franchaisors', $briefVideo);
            $franchaisorFile = new FranchaisorFile();
            $franchaisorFile->file_path = $path;
            $franchaisorFile->file_type = $briefVideo->getClientOriginalExtension();
            $franchaisorFile->type = 'brief';
            $franchaisorFile->franchaisor_id = $franchaisor->id;
            $franchaisorFile->save();
        }

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
        }

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
        }
        return $this->sendResponse(['franchaisor' => $franchaisor, 'message' => 'Franchaisor created successfully']);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $franchaisor = Franchaisor::find($id);
        $franchaisor->files = FranchaisorFile::where('franchaisor_id', $id)->get();
        $franchaisor->cover_images = FranchaisorFile::where('franchaisor_id', $id)->where('type', 'cover')->get();
        $franchaisor->brief_gallary_images = FranchaisorFile::where('franchaisor_id', $id)->where('type', 'brief')->get();
        $franchaisor->brief_video = FranchaisorFile::where('franchaisor_id', $id)->where('type', 'brief')->first();
        $franchaisor->details1_images = FranchaisorFile::where('franchaisor_id', $id)->where('type', 'details1')->get();
        $franchaisor->details2_images = FranchaisorFile::where('franchaisor_id', $id)->where('type', 'details2')->get();
        return $this->sendResponse($franchaisor);
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
        $request->validate([
            'brand_name' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone_number' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'industry' => 'required|string|max:255',
            'investment' => 'required|numeric',
            'timeframe' => 'required|string|max:255',
            'joined_at' => 'required|date',
            'end_at' => 'required|date',
            'cover_images' => 'file|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'logo' => 'file|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'brief_heading' => 'required|string|max:255',
            'brief_description' => 'required|string|max:255',
            'brief_country_of_region' => 'required|string|max:255',
            'brief_available' => 'required|string|max:255',
            'brief_business_type' => 'required|string|max:255',
            'brief_min_investment' => 'required|numeric',
            'details1_heading' => 'required|string|max:255',
            'details1_description' => 'required|string|max:255',
            'details2_heading' => 'required|string|max:255',
            'details2_description' => 'required|string|max:255',
            'brief_gallary_images' => 'file|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'brief_video' => 'file|mimes:mp4,mov,avi,wmv,flv,mpeg,mpg,m4v,3gp,3g2,mj2,webm,mkv|max:2048',
            'details1_images' => 'file|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'details2_images' => 'file|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $franchaisor = Franchaisor::find($id);
        $franchaisor->brand_name = $request->brand_name;
        $franchaisor->name = $request->name;
        $franchaisor->position = $request->position;
        $franchaisor->email = $request->email;
        $franchaisor->phone_number = $request->phone_number;
        $franchaisor->address = $request->address;
        $franchaisor->industry = $request->industry;
        $franchaisor->investment = $request->investment;
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
        $franchaisor->save();

        if ($request->hasFile('logo')) {
            $logo = $request->file('logo');
            $path = Storage::put('franchaisors', $logo);
            $franchaisor->logo_path = $path;
            $franchaisor->save();
        }

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
            }
        }

        if ($request->hasFile('brief_gallary_images')) {
            $briefGallaryImages = $request->file('brief_gallary_images');
            foreach ($briefGallaryImages as $image) {
                $path = Storage::put('franchaisors', $image);
                $franchaisorFile = new FranchaisorFile();
                $franchaisorFile->file_path = $path;
                $franchaisorFile->file_type = $image->getClientOriginalExtension();
                $franchaisorFile->type = 'brief';
                $franchaisorFile->franchaisor_id = $franchaisor->id;
                $franchaisorFile->save();
            }
        }   

        if ($request->hasFile('brief_video')) {
            $briefVideo = $request->file('brief_video');
            $path = Storage::put('franchaisors', $briefVideo);
            $franchaisorFile = new FranchaisorFile();
            $franchaisorFile->file_path = $path;
            $franchaisorFile->file_type = $briefVideo->getClientOriginalExtension();
            $franchaisorFile->type = 'brief';
            $franchaisorFile->franchaisor_id = $franchaisor->id;
            $franchaisorFile->save();
        }

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
        }

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
        }
        return $this->sendResponse(['franchaisor' => $franchaisor, 'message' => 'Franchaisor updated successfully']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $franchaisor = Franchaisor::find($id);
        $franchaisor->delete();
        return $this->sendResponse(['message' => 'Franchaisor deleted successfully']);
    }
}
