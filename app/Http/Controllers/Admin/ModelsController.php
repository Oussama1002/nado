<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ModelsRequest;
use App\Models\Brand;
use App\Models\Modele; // ✅ correct model
use Illuminate\Support\Facades\Auth;

class ModelsController extends Controller
{
    public function __construct()
    {
        $this->middleware('module.access', ['only' => ['index', 'show', 'create', 'store', 'update', 'edit', 'destroy']]);
    }

    public function index()
    {
        return view('admin.models.index', ['models' => Modele::all()]);
    }

    public function create()
    {
        return view('admin.models.form', ['model' => new Modele, 'brands' => Brand::all()]);
    }

    public function store(ModelsRequest $request)
    {
        $data = $request->validated();
        $data['ip'] = $request->ip();
        $data['user_agent'] = $request->header('User-Agent');
        $data['user_id'] = Auth::id();
        Modele::create($data); // ✅ corrected

        return to_route('models.index')->with('success', 'Success store model');
    }

    public function show(string $id)
    {
        //
    }

    public function edit(Modele $model) // ✅ corrected type
    {
        return view('admin.models.form', [
            'model' => $model,
            'brands' => Brand::all(),
        ]);
    }

    public function update(ModelsRequest $request, Modele $model) // ✅ corrected
    {
        $data = $request->validated();
        $model->update($data);

        return to_route('models.index')->with('success', 'success update Model');
    }

    public function destroy(Modele $model) // ✅ corrected
    {
        $model->delete();

        return to_route('models.index')->with('success', 'Model deleted successfully');
    }

    public function getmodels($id)
    {
        $modele = Modele::orderBy('name', 'asc')
            ->select('id', 'name')
            ->where('brand_id', $id)
            ->get();

        return response()->json($modele);
    }
}
