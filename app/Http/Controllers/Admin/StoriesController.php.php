<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Story;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class StoriesController extends Controller
{
    public function __construct()
    {
        $this->middleware('module.access', ['only' => ['index', 'show', 'create', 'store', 'update', 'edit', 'destroy']]);
    }

    public function index()
    {
        return view('admin.stories.index', ['stories' => Story::latest()->get()]);
    }

    public function create()
    {
        return view('admin.stories.create');
    }

    public function store(Request $request)
    {
        $stories = $request->validate([
            'media' => 'required|file|mimes:jpeg,png,jpg,gif|max:20480',
            'expires_at' => 'nullable|date',
        ]);

        if ($request->hasFile('media')) {
            $stories['media_path'] = $this->resizeImage($request->file('media'), 'story');
        }
        $stories['media_type'] = 'image';
        $stories['user_id'] = Auth::id();
        $stories['expires_at'] = $request->expires_at ?? now()->addHours(24);

        Story::create($stories);
        Cache::forget('stories');
        return to_route('admin.stories.index')->with('success', 'Story created successfully');
    }

    public function destroy($id)
    {
        $story = Story::findOrFail($id);
        $story->delete();

        return to_route('admin.stories.index')->with('success', 'Story deleted successfully');
    }

    // EXACT COPY of BrandsController resizeImage (just changed 'brands' to 'stories')
    public function resizeImage($photo, $title)
    {
        $image = $photo;
        $imageName = Str::slug($title, '-') . '-' . time() . '.' . $image->getClientOriginalExtension();
        $manager = new ImageManager(new Driver);
        $img = $manager->read($image);
        $img->save(storage_path('app/public/stories/' . $imageName));

        return $imageName;
    }
}
