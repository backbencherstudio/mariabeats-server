<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;
use App\Models\Home\HomeContents as HomeContentsModel;
use App\Traits\CommonTrait;
use Illuminate\Http\Request;

class HomeContents extends Controller
{
    use CommonTrait;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $homeContents = HomeContentsModel::orderBy('id', 'desc')->get();
            return $this->sendResponse($homeContents, 'Home Contents fetched successfully');
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), [], 500);
        }
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
        try {
            dd($request->all());
            if ($request->hasFile('video')) {
                $video = $request->file('video');
                $videoName = time() . '.' . $video->getClientOriginalExtension();
                $video->move(public_path('uploads/home-contents'), $videoName);
                $request->merge(['video_url' => $videoName]);
            }
            $homeContents = HomeContentsModel::create($request->all());
            return $this->sendResponse($homeContents, 'Home Contents created successfully');
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), [], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $homeContents = HomeContentsModel::find($id);
            return $this->sendResponse($homeContents, 'Home Contents fetched successfully');
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), [], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, string $id)
    {
        try {
            $homeContents = HomeContentsModel::find($id);
            $homeContents->update($request->all());
            return $this->sendResponse($homeContents, 'Home Contents updated successfully');
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), [], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $homeContents = HomeContentsModel::find($id);
            $homeContents->update($request->all());
            return $this->sendResponse($homeContents, 'Home Contents updated successfully');
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), [], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $homeContents = HomeContentsModel::find($id);
            $homeContents->delete();
            return $this->sendResponse($homeContents, 'Home Contents deleted successfully');
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), [], 500);
        }
    }
}
