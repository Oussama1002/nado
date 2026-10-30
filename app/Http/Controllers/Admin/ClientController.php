<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Client;

use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:admin');
    }
    public function create()
    {
        return view('admin.clients.create'); // you'll need to create this Blade file
    }
    public function index()
    {
        // Les clients "flotte&enchere" ont leur propre liste côté Auto Enchère.
        $clients = Client::where('is_approved', true)
            ->where('type', '!=', 'flotte&enchere')
            ->latest()
            ->get();
        return view('admin.clients.index', compact('clients'));
    }

    /** Liste dédiée des clients Auto Enchère (type flotte&enchere). */
    public function encheresClients()
    {
        $clients = Client::where('type', 'flotte&enchere')
            ->latest()
            ->get();
        return view('admin.clients.encheres', compact('clients'));
    }
    public function approval()
    {
        $unapprovedClients = Client::where('is_approved', false)->latest()->get();
        return view('admin.clients.approval', compact('unapprovedClients'));
    }
    public function verify($id)
{
    $client = Client::find($id);

    if (!$client) {
        return response()->json(['error' => 'Client non trouvé'], 404);
    }

    return response()->json([
        'verified' => true,
        'client' => $client
    ]);
}

    public function toggleVerification(Request $request, $id)
{
    $client = Client::findOrFail($id);

    if ($request->has('verified') && $request->verified == '1') {
        $client->email_verified_at = now();
    } else {
        $client->email_verified_at = null;
    }

    $client->save();

    return response()->json(['success' => true]);
}
public function updateType(Request $request, $id)
{
    $client = Client::findOrFail($id);

    $request->validate([
        'type' => 'required|in:pro,particulier,flotte&enchere',
    ]);

    $client->type = $request->type;
    $client->save();

    return response()->json(['success' => true, 'type' => $client->type]);
}

    public function approve($id)
    {
        $client = Client::findOrFail($id);
        $client->is_approved = true;
        $client->save();
    
        return back()->with('success', 'Client approuvé avec succès.');
    }
    public function show($id)
    {
        $client = Client::findOrFail($id);
        return view('admin.clients.show', compact('client'));
    }
    
    public function destroy($id)
    {
        Client::findOrFail($id)->delete();
        return back()->with('success', 'Client supprimé.');
    }
    public function store(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:clients',
        'password' => 'required|string|min:6|confirmed',
        'type' => 'required|string',
        'phone' => 'required|string|max:20',
        'company' => 'nullable|string|max:255',
        'logo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
    ]);

    // Upload logo si présent
    $logoPath = null;
    if ($request->hasFile('logo')) {
        $logoPath = $request->file('logo')->store('logos', 'public');
    }

    $client = Client::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => bcrypt($request->password),
        'type' => $request->type,
        'phone' => $request->phone,
        'company' => $request->company,
        'logo' => $logoPath,
        'is_approved' => true, // ✅ créé directement approuvé si admin
    ]);

    return redirect()->route('admin.clients.index')->with('success', 'Client créé avec succès.');
}

}
