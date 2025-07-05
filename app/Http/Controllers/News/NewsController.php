<?php

namespace App\Http\Controllers\News;

use App\Http\Controllers\Controller;
use App\Models\News\News;
use App\Traits\CommonTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class NewsController extends Controller
{
    use CommonTrait;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $news = News::all();
        $news->map(function ($news) {
            $news->news_image = Storage::url($news->news_image);
            return $news;
        });
        return $this->sendResponse($news, 'News fetched successfully');
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
            $data = $request->only(['news_image', 'author', 'date', 'headline', 'concise_description', 'sub_headline', 'description']);
            if ($request->hasFile('news_image')) {
                $news_image = $request->file('news_image');
                $path = Storage::put('news', $news_image);
                $data['news_image'] = $path;
            }
            $news = News::create($data);
            return $this->sendResponse($news, 'News created successfully');
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
            $news = News::find($id);
            $news->news_image = Storage::url($news->news_image);
            return $this->sendResponse($news, 'News fetched successfully');
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
            $news = News::find($id);
            $news->update($request->all());
            return $this->sendResponse($news, 'News updated successfully');
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), [], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // dd($id);
        try {
            $data = $request->only(['news_image', 'author', 'date', 'headline', 'concise_description', 'sub_headline', 'description']);
            $news = News::find($id);
            if ($request->hasFile('news_image')) {
                $news_image = $request->file('news_image');
                $path = Storage::put('news', $news_image);
                $data['news_image'] = $path;
            }
            $news->update($data);
            return $this->sendResponse($news, 'News updated successfully');
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
            $news = News::find($id);
            if (!$news) {
                return $this->sendError('News not found', [], 404);
            }
            if ($news->news_image) {
                Storage::delete($news->news_image);
            }
            $news->delete();
            return $this->sendResponse(null, 'News deleted successfully');
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), [], 500);
        }
    }
}
