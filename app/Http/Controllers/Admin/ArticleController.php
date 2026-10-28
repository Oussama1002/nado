<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ArticleController extends Controller
{
    public function index()
    {
        $articles = Article::orderBy('created_at', 'desc')->paginate(15);
        return view('admin.articles.index', compact('articles'));
    }

    public function create()
    {
        return view('admin.articles.create');
    }

    public function store(Request $request)
    {
        $data = $this->validateArticle($request);
        $data['slug'] = $this->uniqueSlug($data['title']);

        if ($request->hasFile('image')) {
            $data['image'] = $this->storeImage($request->file('image'));
        }

        $data['is_active']  = $request->has('is_active') ? 1 : 0;
        $data['is_premium'] = $request->has('is_premium') ? 1 : 0;

        Article::create($data);

        return redirect()->route('admin.articles.index')
            ->with('success', 'Article créé avec succès.');
    }

    public function edit($id)
    {
        $article = Article::findOrFail($id);
        return view('admin.articles.edit', compact('article'));
    }

    public function update(Request $request, $id)
    {
        $article = Article::findOrFail($id);
        $data = $this->validateArticle($request, $article->id);

        if ($data['title'] !== $article->title) {
            $data['slug'] = $this->uniqueSlug($data['title'], $article->id);
        }

        if ($request->hasFile('image')) {
            if ($article->image && Storage::disk('public')->exists($article->image)) {
                Storage::disk('public')->delete($article->image);
            }
            $data['image'] = $this->storeImage($request->file('image'));
        }

        $data['is_active']  = $request->has('is_active') ? 1 : 0;
        $data['is_premium'] = $request->has('is_premium') ? 1 : 0;

        $article->update($data);

        return redirect()->route('admin.articles.index')
            ->with('success', 'Article mis à jour.');
    }

    public function destroy($id)
    {
        $article = Article::findOrFail($id);
        if ($article->image && Storage::disk('public')->exists($article->image)) {
            Storage::disk('public')->delete($article->image);
        }
        $article->delete();

        return back()->with('success', 'Article supprimé.');
    }

    public function toggleActive($id)
    {
        $article = Article::findOrFail($id);
        $article->is_active = ! $article->is_active;
        $article->save();

        return back()->with('success', 'Statut mis à jour.');
    }

    /**
     * Handle inline image uploads from the TinyMCE editor (drag & drop / file picker).
     * Stores the file and returns { "location": "<public url>" } as TinyMCE expects.
     */
    public function uploadImage(Request $request)
    {
        $request->validate([
            'file' => 'required|image|mimes:jpg,jpeg,png,webp,gif|max:4096',
        ]);

        $path = $request->file('file')->store('articles/content', 'public');

        return response()->json([
            'location' => asset('storage/' . $path),
        ]);
    }

    private function validateArticle(Request $request, $ignoreId = null): array
    {
        return $request->validate([
            'title'            => 'required|string|max:255',
            'excerpt'          => 'nullable|string|max:500',
            'content'          => 'required|string',
            'image'            => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'meta_title'       => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
        ]);
    }

    private function uniqueSlug(string $title, $ignoreId = null): string
    {
        $base = Str::slug($title) ?: 'article';
        $slug = $base;
        $i = 2;
        while (Article::where('slug', $slug)
                ->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))
                ->exists()) {
            $slug = $base . '-' . $i++;
        }
        return $slug;
    }

    private function storeImage($file): string
    {
        return $file->store('articles', 'public');
    }
}
