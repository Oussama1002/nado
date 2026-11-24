<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Publication;
use Illuminate\Http\Request;

class PublicationController extends Controller
{
    public function index()
    {
        $publications = Publication::orderBy('position')->get();
        return view('admin.publications.index', compact('publications'));
    }

    public function create()
    {
        return view('admin.publications.form', ['publication' => new Publication()]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'image' => 'nullable|image',
            'placement' => 'required|in:left-banner,right-banner,center-banner,first-banner,second-banner,third-banner,leftondetail-banner,rightonindexenchere-banner,vente-hero,ae-expertise,reprise-hero,front-estimation',
            'content' => 'nullable|string',
            'status' => 'nullable|boolean',
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('publications', 'public');
            // Check if file really exists
            if (!\Storage::disk('public')->exists($path)) {
                dd("File not found at path: $path");
            }
            $data['image'] = $path;
        }
        

        $data['position'] = Publication::max('position') + 1;
        $data['status'] = $request->has('status') ? $request->status : true;

        Publication::create($data);

        return redirect()->route('admin.publications.index')->with('success', 'Publication ajoutée.');
    }

    public function edit(Publication $publication)
    {
        return view('admin.publications.form', compact('publication'));
    }

    public function update(Request $request, Publication $publication)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'image' => 'nullable|image',
            'placement' => 'required|in:left-banner,right-banner,center-banner,first-banner,second-banner,third-banner,leftondetail-banner,rightonindexenchere-banner,vente-hero,ae-expertise,reprise-hero,front-estimation',
            'status' => 'nullable|boolean',
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('publications', 'public');
        }

        $data['status'] = $request->has('status') ? $request->status : $publication->status;

        $publication->update($data);

        return redirect()->route('admin.publications.index')->with('success', 'Publication mise à jour.');
    }

    public function destroy(Publication $publication)
    {
        $publication->delete();
        return redirect()->route('admin.publications.index')->with('success', 'Publication supprimée.');
    }

    public function reorder(Request $request)
    {
        foreach ($request->positions as $index => $id) {
            Publication::where('id', $id)->update(['position' => $index]);
        }

        return response()->json(['success' => true]);
    }
}
