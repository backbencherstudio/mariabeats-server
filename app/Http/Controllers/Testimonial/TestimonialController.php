<?php

namespace App\Http\Controllers\Testimonial;

use App\Http\Controllers\Controller;
use App\Models\Testimonial\Testimonials;
use App\Traits\CommonTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TestimonialController extends Controller
{
    use CommonTrait;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $testimonials = Testimonials::orderBy('id', 'desc')->get();
            $testimonials->map(function ($testimonial) {
                $testimonial->image_url = Storage::url($testimonial->image_url);
                return $testimonial;
            });
            return $this->sendResponse($testimonials, 'Testimonials fetched successfully');
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
            if ($request->allFiles()) {
                $allFiles = $request->allFiles();
                foreach ($allFiles as $key => $file) {
                    $path = Storage::put('testimonials', $file);
                    $request->merge(['image_url' => $path]);
                }
            }
            $testimonials = Testimonials::create($request->all());
            return $this->sendResponse($testimonials, 'Testimonials created successfully');
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
            $testimonials = Testimonials::find($id);
            return $this->sendResponse($testimonials, 'Testimonials fetched successfully');
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
            $testimonials = Testimonials::find($id);
            $testimonials->update($request->all());
            return $this->sendResponse($testimonials, 'Testimonials updated successfully');
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
            $testimonials = Testimonials::find($id);
            if ($request->allFiles()) {
                $allFiles = $request->allFiles();
                foreach ($allFiles as $key => $file) {
                    $path = Storage::put('testimonials', $file);
                    $request->merge(['image_url' => $path]);
                }
            }
            $testimonials->update($request->all());
            return $this->sendResponse($testimonials, 'Testimonials updated successfully');
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
            $testimonials = Testimonials::find($id);
            // dd($testimonials);
            $testimonials->delete();
            return $this->sendResponse($testimonials, 'Testimonials deleted successfully');
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), [], 500);
        }
    }
}
