<?php

namespace App\Http\Controllers;

use App\Mail\ContactFromMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('contact');
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
        $request->validate([
            'email'   => 'required|email|max:255',
            'name'    => 'required|string|max:255',
            'phone'   => 'required|string|max:255',
            'ville'   => 'nullable|string|max:255',
            'subject' => 'nullable|string|max:255',
            'message' => 'nullable|string|max:5000',
        ]);

        $data = [
            'name'    => $request->name,
            'email'   => $request->email,
            'phone'   => $request->phone,
            'ville'   => $request->ville,
            'subject' => $request->subject ?: 'Demande expert NADO',
            'message' => $request->message,
        ];

        Mail::to('contact@nado.ma')->send(new ContactFromMail($data));

        // The homepage "Expert NADO" popup submits via fetch and expects JSON,
        // so it can stay on the page and show a confirmation.
        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Votre demande a bien été envoyée. Un expert NADO vous recontactera rapidement.',
            ]);
        }

        return to_route('contactez-nous.index');
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
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
