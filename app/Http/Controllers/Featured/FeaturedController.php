<?php

namespace App\Http\Controllers\Featured;

use App\Http\Controllers\Controller;
use App\Models\Featured\Featured;
use App\Models\Franchaisor\Franchaisor;
use Illuminate\Http\Request;
use App\Traits\CommonTrait;
use Illuminate\Support\Facades\Storage;

class FeaturedController extends Controller
{
    use CommonTrait;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $featured = Featured::with('franchaisor')->get();
            $featured = $featured->map(function ($item) {
                if ($item->franchaisor->logo_path && $item->franchaisor->logo_path != null) {
                    $item->franchaisor->logo_path = Storage::url($item->franchaisor->logo_path);
                }
                return $item;
            });

            return $this->sendResponse(['featured' => $featured, 'message' => 'Featured fetched successfully']);
        } catch (\Throwable $th) {
            return $this->sendError($th->getMessage());
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
            $franchaisor_id = $request->input('franchaisor_id');

            $franchaisor = Franchaisor::find($franchaisor_id);

            if (!$franchaisor) {
                return $this->sendError('Franchaisor not found');
            }

            $featured = new Featured();
            $featured->franchaisor_id = $franchaisor_id;
            $featured->save();

            return $this->sendResponse(['featured' => $featured, 'message' => 'Featured created successfully']);
        } catch (\Throwable $th) {
            return $this->sendError($th->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $featured = Featured::find($id);
            if (!$featured) {
                return $this->sendError('Featured not found');
            }
            $featured->delete();
            return $this->sendResponse(['featured' => $featured, 'message' => 'Featured deleted successfully']);
        } catch (\Throwable $th) {
            return $this->sendError($th->getMessage());
        }
    }
}
