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
        $categories = Category::with('services')->get();
        return $this->sendResponse($categories, 'Categories fetched successfully');
    }

    public function store(Request $request)
    {
        $category = Category::create($request->all());
        return $this->sendResponse($category, 'Category created successfully');
    }

    public function show(Category $category)
    {
        return $this->sendResponse($category, 'Category fetched successfully');
    }
    
    public function update(Request $request, Category $category)
    {
        $category->update($request->all());
        return $this->sendResponse($category, 'Category updated successfully');
    }
    
    public function destroy(Category $category)
    {
        $category->delete();
        return $this->sendResponse(null, 'Category deleted successfully');
    }
}
