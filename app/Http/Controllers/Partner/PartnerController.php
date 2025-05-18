<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use App\Models\Partner\Partner as PartnerPartner;
use App\Models\Partner\Partners;
use App\Traits\CommonTrait;
use Illuminate\Http\Request;

class PartnerController extends Controller
{
    use CommonTrait;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $partners = Partners::orderBy('id', 'desc')->get();
            return $this->sendResponse($partners, 'Partners fetched successfully');
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
            $partners = Partners::create($request->all());
            return $this->sendResponse($partners, 'Partners created successfully');
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
            $partners = Partners::find($id);
            return $this->sendResponse($partners, 'Partners fetched successfully');
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
            $partners = Partners::find($id);
            $partners->update($request->all());
            return $this->sendResponse($partners, 'Partners updated successfully');
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
            $partners = Partners::find($id);
            $partners->update($request->all());
            return $this->sendResponse($partners, 'Partners updated successfully');
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
            $partners = Partners::find($id);
            $partners->delete();
            return $this->sendResponse($partners, 'Partners deleted successfully');
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), [], 500);
        }
    }
}
