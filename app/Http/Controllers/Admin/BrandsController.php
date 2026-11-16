<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\BrandsRequest;
use App\Models\Brand;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class BrandsController extends Controller
{
    public function __construct()
    {
        $this->middleware('module.access', ['only' => ['index', 'show', 'create', 'store', 'update', 'edit', 'destroy']]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.brands.index', ['brands' => Brand::all()]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.brands.form', ['brand' => new Brand()]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(BrandsRequest $request)
    {
        $brands = $request->validated();
        if ($request->hasFile('logo')) {
            $brands['logo'] = $this->resizeImage($request->file('logo'), $request->name);
        }
        $brands['ip'] = $request->ip();
        $brands['user_agent'] = $request->header('User-Agent');
        $brands['user_id'] = Auth::id();
        $brands = Brands::create($brands);
        Cache::forget('brands');

        return to_route('brands.index')->with('success', 'Success store brand');
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
    public function edit(Brands $brand)
    {
        return view('admin.brands.form', [
            'brand' => $brand,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(BrandsRequest $request, Brands $brand)
    {
        $n = $request->validated();
        if ($request->hasFile('logo')) {
            $n['logo'] = $this->resizeImage($request->file('logo'), $request->name);
        }
        $brand->update($n);
        Cache::forget('brands');

        return to_route('brands.index')->with('success', 'success update brand');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Brands $brand)
    {
        $brand->delete();

        return to_route('brands.index')->with('success', 'Brand deleted successfully');
    }

    public function resizeImage($photo, $title)
    {
        $image = $photo;
        $imageName = Str::slug($title, '-').'-'.time().'.'.$image->getClientOriginalExtension();
        $manager = new ImageManager(new Driver);
        $img = $manager->read($image);
        $img->save(storage_path('app/public/brands/'.$imageName));

        return $imageName;
    }
}
