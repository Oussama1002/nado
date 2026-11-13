<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Modele;
use App\Models\ModelGuide;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * CRUD admin des guides modèles (mirror Admin\ArticleController).
 */
class ModelGuideController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:admin', 'role:admin']);
    }

    public function index()
    {
        return view('admin.community.guides-index', [
            'guides' => ModelGuide::with(['brand', 'modele'])->latest()->paginate(20),
        ]);
    }

    public function create()
    {
        return view('admin.community.guide-form', [
            'guide'  => new ModelGuide(),
            'brands' => Brand::orderBy('name')->get(),
            'modeles' => Modele::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);
        $data['slug'] = $data['slug'] ?: Str::slug($data['title']);
        $data['image'] = $this->uploadImage($request) ?? null;
        ModelGuide::create($data);

        return to_route('admin.community.guides.index')->with('success', 'Guide créé ✅');
    }

    public function edit(ModelGuide $guide)
    {
        return view('admin.community.guide-form', [
            'guide'  => $guide,
            'brands' => Brand::orderBy('name')->get(),
            'modeles' => Modele::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, ModelGuide $guide)
    {
        $data = $this->validateData($request, $guide->id);
        $data['slug'] = $data['slug'] ?: Str::slug($data['title']);
        if ($img = $this->uploadImage($request)) {
            $data['image'] = $img;
        }
        $guide->update($data);

        return to_route('admin.community.guides.index')->with('success', 'Guide mis à jour ✅');
    }

    public function destroy(ModelGuide $guide)
    {
        $guide->delete();
        return back()->with('success', 'Guide supprimé.');
    }

    private function validateData(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'brand_id'         => ['nullable', 'exists:brands,id'],
            'modele_id'        => ['nullable', 'exists:models,id'],
            'slug'             => ['nullable', 'string', 'max:180'],
            'title'            => ['required', 'string', 'max:180'],
            'title_ar'         => ['nullable', 'string', 'max:180'],
            'intro'            => ['nullable', 'string', 'max:1000'],
            'intro_ar'         => ['nullable', 'string', 'max:1000'],
            'body_fr'          => ['nullable', 'string'],
            'body_ar'          => ['nullable', 'string'],
            'meta_title'       => ['nullable', 'string', 'max:180'],
            'meta_description' => ['nullable', 'string', 'max:300'],
            'is_active'        => ['nullable', 'boolean'],
        ]) + ['is_active' => $request->boolean('is_active')];
    }

    private function uploadImage(Request $request): ?string
    {
        if (! $request->hasFile('image')) {
            return null;
        }
        return $request->file('image')->store('guides', 'public');
    }
}
