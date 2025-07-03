<?php

namespace App\Http\Controllers\Service;

use App\Http\Controllers\Controller;
use App\Models\Service\Service;
use App\Traits\CommonTrait;
use Illuminate\Http\Request;

class ServiceController extends Controller
{   
    use CommonTrait;

    public function index()
    {
        try {
            $services = Service::with('category')->get();
            return $this->sendResponse($services, 'Services fetched successfully');
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), [], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $service = Service::create($request->all());
            return $this->sendResponse($service, 'Service created successfully');
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), [], 500);
        }
    }

    public function show(Service $service)
    {
        try {
            $service = Service::with('category')->find($service->id);
            return $this->sendResponse($service, 'Service fetched successfully');
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), [], 500);
        }
    }

    public function update(Request $request, $id)
    {
        // dd($id);
        try {
            $service = Service::find($id);
            $service->update($request->all());
            return $this->sendResponse($service, 'Service updated successfully');
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), [], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $service = Service::find($id);
            $service->delete();
            return $this->sendResponse(null, 'Service deleted successfully');
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), [], 500);
        }
    }
}
