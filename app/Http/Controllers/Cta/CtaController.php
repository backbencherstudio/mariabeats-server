<?php

namespace App\Http\Controllers\Cta;

use App\Http\Controllers\Controller;
use App\Models\Cta\Cta;
use Illuminate\Http\Request;
use App\Traits\CommonTrait;
use Illuminate\Support\Facades\Storage;

class CtaController extends Controller
{
    use CommonTrait;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $ctas = Cta::all();
            foreach($ctas as $cta){
                $cta->bg_image = Storage::url($cta->bg_image);
                $cta->secondary_image = Storage::url($cta->secondary_image);
            }
            return $this->sendResponse($ctas, 'CTAs fetched successfully');
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), [], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        try {
            $ctas = Cta::all();
            return $this->sendResponse($ctas, 'CTAs fetched successfully');
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), [], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $data = $request->only(['headline', 'description', 'button_text', 'button_link']);
            if ($request->hasFile('bg_image')) {
                $bg_image = $request->file('bg_image');
                $path = Storage::put('cta', $bg_image);
                // dd($path);
                $data['bg_image'] = $path;
            }
            if ($request->hasFile('secondary_image')) {
                $secondary_image = $request->file('secondary_image');
                $path = Storage::put('cta', $secondary_image);
                $data['secondary_image'] = $path;
            }

            $cta = Cta::create($data);
            return $this->sendResponse($cta, 'CTA created successfully');
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
            $cta = Cta::find($id);
            return $this->sendResponse($cta, 'CTA fetched successfully');
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
            $cta = Cta::find($id);
            $cta->update($request->all());
            return $this->sendResponse($cta, 'CTA updated successfully');
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
            $data = $request->only(['headline', 'description', 'button_text', 'button_link']);
            $cta = Cta::find($id);
            if ($request->hasFile('bg_image')) {
                $bg_image = $request->file('bg_image');
                $path = Storage::put('cta', $bg_image);
                $data['bg_image'] = $path;
            }
            if ($request->hasFile('secondary_image')) {
                $secondary_image = $request->file('secondary_image');
                $path = Storage::put('cta', $secondary_image);
                $data['secondary_image'] = $path;
            }
            // dd($data);
            $cta->update($data);
            return $this->sendResponse($cta, 'CTA updated successfully');
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
            $cta = Cta::find($id);
            if ($cta->bg_image) {
                Storage::delete($cta->bg_image);
            }
            if ($cta->secondary_image) {
                Storage::delete($cta->secondary_image);
            }
            $cta->delete();
            return $this->sendResponse($cta, 'CTA deleted successfully');
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), [], 500);
        }
    }
}
