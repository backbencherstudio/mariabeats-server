<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use App\Models\Partner\PartnerLogo;
use App\Models\Partner\Partners;
use App\Traits\CommonTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PartnerController extends Controller
{
    use CommonTrait;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $partners = Partners::with('logos')->orderBy('id', 'desc')->get();
            $partners = $partners->map(function ($item) {
                if ($item->logos) {
                    $item->logos = $item->logos->map(function ($logo) {
                        $logo->logo = Storage::url($logo->logo);
                        return $logo;
                    });
                }
                return $item;
            });
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
            // Create partner
            $partner = Partners::create($request->except(['brand_logos']));
            
            // Handle logo uploads - get all brand_logos files
            if ($request->hasFile('brand_logos')) {
                $files = $request->file('brand_logos');
                
                // Handle both single file and multiple files
                if (!is_array($files)) {
                    $files = [$files];
                }
                
                foreach ($files as $file) {
                    if ($file && $file->isValid()) {
                        $path = Storage::put('partners', $file);
                        PartnerLogo::create([
                            'partner_id' => $partner->id,
                            'logo' => $path
                        ]);
                    }
                }
            }
            
            // Load logos for response
            $partner->load('logos');
            $partner->logos = $partner->logos->map(function ($logo) {
                $logo->logo = Storage::url($logo->logo);
                return $logo;
            });
            
            return $this->sendResponse($partner, 'Partners created successfully');
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
            $partners = Partners::with('logos')->find($id);
            if ($partners->logos) {
                $partners->logos = $partners->logos->map(function ($logo) {
                    $logo->logo = Storage::url($logo->logo);
                    return $logo;
                });
            }
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
            
            // Update partner data
            $partners->update($request->except(['brand_logos']));
            
            // Handle existing logo IDs - keep only the ones in the request
            if ($request->has('existing_logos')) {
                $existingLogosData = json_decode($request->existing_logos, true);
                $existingLogoIds = collect($existingLogosData)->pluck('id')->toArray();
                
                // Get all logos of the partner
                $allPartnerLogos = $partners->logos;
                
                // Check which logos should be deleted (not in the provided array)
                foreach ($allPartnerLogos as $logo) {
                    if (!in_array($logo->id, $existingLogoIds)) {
                        // Delete file from storage
                        Storage::delete($logo->logo);
                        // Delete database record
                        $logo->delete();
                    }
                }
            }
            
            // Handle new logo uploads - get all brand_logos files
            if ($request->hasFile('brand_logos')) {
                $files = $request->file('brand_logos');
                
                // Handle both single file and multiple files
                if (!is_array($files)) {
                    $files = [$files];
                }
                
                foreach ($files as $file) {
                    if ($file && $file->isValid()) {
                        $path = Storage::put('partners', $file);
                        PartnerLogo::create([
                            'partner_id' => $partners->id,
                            'logo' => $path
                        ]);
                    }
                }
            }
            
            // Load logos for response
            $partners->load('logos');
            $partners->logos = $partners->logos->map(function ($logo) {
                $logo->logo = Storage::url($logo->logo);
                return $logo;
            });
            
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

    /**
     * Remove the specified resource from storage.
     */
    public function deletePartnerLogo(string $id)
    {
        // dd($id);
        try {
            $partnerLogo = PartnerLogo::find($id);
            Storage::delete($partnerLogo->logo);
            $partnerLogo->delete();
            return $this->sendResponse($partnerLogo, 'Partner logo deleted successfully');
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), [], 500);
        }
    }
}
