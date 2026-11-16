<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Color;
use Illuminate\Http\Request;

class ColorController extends Controller
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
        return view('admin.colors.index', ['colors' => Color::all()]);
    }

    /**
     * Show the form for creating a new resource.
     */
     public function create()
    {
        // 👇 Must be 'color', not 'colors'
        return view('admin.colors.form', ['color' => new Color()]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $colors = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);
    Color::create($colors);

        return to_route('colors.index')->with('success', 'Success store colors');
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
public function edit(Color $color)
{
  return view('admin.colors.form', ['colors' => new Color]);

}


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, colors $colors)
    {
        $n = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);
        $colors->update($n);

        return to_route('colors.index')->with('success', 'success update color');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(colors $colors)
    {
        $colors->delete();

        return to_route('colors.index')->with('success', 'color deleted successfully');
    }
}
