<?php

namespace App\Http\Controllers\Category;

use App\Http\Controllers\Controller;
use App\Models\Category\Category;
use App\Traits\CommonTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    use CommonTrait;

    public function index()
    {
        try {
            $categories = Category::with('services')->get();
            return $this->sendResponse($categories, 'Categories fetched successfully');
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), [], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $category = Category::create($request->all());
            return $this->sendResponse($category, 'Category created successfully');
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), [], 500);
        }
    }

    public function show(Category $category)
    {
        try {
            $category = Category::with('services')->find($category->id);
            return $this->sendResponse($category, 'Category fetched successfully');
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), [], 500);
        }
    }
    
    public function update(Request $request, Category $category)
    {
        try {
            $category->update($request->all());
            return $this->sendResponse($category, 'Category updated successfully');
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), [], 500);
        }
    }
    
    public function destroy(Category $category)
    {
        try {
            $category->delete();
            return $this->sendResponse(null, 'Category deleted successfully');
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), [], 500);
        }
    }
}
