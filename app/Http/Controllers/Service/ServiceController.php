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
        $services = Service::with('category')->get();
        return $this->sendResponse($services, 'Services fetched successfully');
    }

    public function store(Request $request)
    {
        // dd($request->all());
        $service = Service::create($request->all());
        return $this->sendResponse($service, 'Service created successfully');
    }

    public function show(Service $service)
    {
        return $this->sendResponse($service, 'Service fetched successfully');
    }

    public function update(Request $request, Service $service)
    {
        $service->update($request->all());
        return $this->sendResponse($service, 'Service updated successfully');
    }

    public function destroy(Service $service)
    {
        $service->delete();
        return $this->sendResponse(null, 'Service deleted successfully');
    }
}
