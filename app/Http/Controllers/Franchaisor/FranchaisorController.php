<?php

namespace App\Http\Controllers\Franchaisor;

use App\Http\Controllers\Controller;
use App\Models\Franchaisor\Franchaisor;
use App\Models\Franchaisor\FranchaisorCountries;
use App\Models\Franchaisor\FranchaisorFile;
use App\Models\Franchaisor\FranchaisorRequest;
use App\Traits\CommonTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class FranchaisorController extends Controller
{
    use CommonTrait;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $franchaisors = Franchaisor::all();
        return $this->sendResponse($franchaisors);
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
        $franchaisor->save();

        //create franchaisor countries 
        foreach ($request->interested_countries as $country) {
            $franchaisorCountry = new FranchaisorCountries();
            $franchaisorCountry->franchaisor_id = $franchaisor->id;
            $franchaisorCountry->country_id = $country;
            $franchaisorCountry->save();
        }

        //create logo
        if ($request->hasFile('logo')) {
            $logo = $request->file('logo');
            $path = Storage::put('franchaisors', $logo);
            $franchaisor->logo_path = $path;
            $franchaisor->save();
        }

        //create cover images
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

        //create brief gallary images
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

        //create brief video
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

        //create details1 images
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

        //create details2 images
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
        $franchaisor->interested_countries = FranchaisorCountries::where('franchaisor_id', $id)->get();
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
        $validator = Validator::make($request->all(), [
            'brand_name' => 'string|max:255',
            'name' => 'string|max:255',
            'position' => 'string|max:255',
            'email' => 'string|email|max:255|unique:users',
            'phone_number' => 'string|max:255',
            'location' => 'string|max:255',
            'interested_countries' => 'required',
            'industry' => 'string|max:255',
            'timeframe' => 'string|max:255',
            'joined_at' => 'date',
            'end_at' => 'date',
            'cover_images' => 'file|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'logo' => 'file|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'brief_heading' => 'string|max:255',
            'brief_description' => 'string|max:255',
            'brief_country_of_region' => 'string|max:255',
            'brief_available' => 'string|max:255',
            'brief_business_type' => 'string|max:255',
            'brief_min_investment' => 'numeric',
            'details1_images' => 'file|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'details1_heading' => 'string|max:255',
            'details1_description' => 'string|max:255',
            'details2_images' => 'file|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'details2_heading' => 'string|max:255',
            'details2_description' => 'string|max:255',
            'brief_gallary_images' => 'file|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'brief_video' => 'file|mimes:mp4,mov,avi,wmv,flv,mpeg,mpg,m4v,3gp,3g2,mj2,webm,mkv|max:2048',
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

        //update cover images
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

        //update brief gallary images
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

        //update brief video
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

        //update details1 images
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

        //update details2 images
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

        $fields = ['ID', 'Name', 'Company Name', 'Email', 'Industry', 'Based On', 'Interested Expansion', 'Timeframe', 'Date of Request', 'Ending Date']; // CSV headers

        $handle = fopen('php://output', 'w');
        fputcsv($handle, $fields); // CSV headers

        foreach ($franchisorsData as $data) {
            $expansion = FranchaisorCountries::where('franchaisor_id', $data->id)->get();
            $expansion = $expansion->map(function ($item) {
                return $item->country->name;
            });
            $expansion = $expansion->implode(', ');
            fputcsv($handle, [$data->id, $data->name, $data->brand_name, $data->email, $data->industry, $data->address, $expansion, $data->timeframe, $data->joined_at, $data->end_at]);
        }

        fclose($handle);

        return response()->stream(
            function () use ($handle) {
                fpassthru($handle);
            },
            Response::HTTP_OK,
            $headers
        );
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

    public function franchaisorRequest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'phone_number' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'investment_amount' => 'required|numeric',
            'timeframe' => 'required|string|max:255',
            'preferred_location' => 'required|string|max:255',
            'message' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $franchaisorRequest = new FranchaisorRequest();
        $franchaisorRequest->name = $request->name;
        $franchaisorRequest->email = $request->email;
        $franchaisorRequest->phone_number = $request->phone_number;
        $franchaisorRequest->country = $request->country;
        $franchaisorRequest->investment_amount = $request->investment_amount;
        $franchaisorRequest->timeframe = $request->timeframe;
        $franchaisorRequest->preferred_location = $request->preferred_location;
        $franchaisorRequest->message = $request->message;
        $franchaisorRequest->save();
        return $this->sendResponse(['franchaisorRequest' => $franchaisorRequest, 'message' => 'Franchaisor request sent successfully']);
    }

    public function franchaisorRequests(Request $request)
    {
        $franchaisorRequests = FranchaisorRequest::all();
        return $this->sendResponse($franchaisorRequests);
    }
}
