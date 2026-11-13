<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ListingQuestion;
use Illuminate\Http\Request;

/**
 * Modération des Q&A d'annonces (mirror DemandeAdminController).
 */
class ListingQuestionController extends Controller
{
    public function index(Request $request)
    {
        $q = ListingQuestion::with(['annonce', 'asker'])->latest();
        if ($status = $request->get('status')) {
            $q->where('status', $status);
        }

        return view('admin.community.questions', [
            'questions' => $q->paginate(20)->withQueryString(),
            'status'    => $status,
        ]);
    }

    public function approve(ListingQuestion $question)
    {
        $question->update(['status' => 'approved']);
        return back()->with('success', 'Question approuvée ✅');
    }

    public function reject(ListingQuestion $question)
    {
        $question->update(['status' => 'rejected']);
        return back()->with('success', 'Question rejetée.');
    }

    public function destroy(ListingQuestion $question)
    {
        $question->delete();
        return back()->with('success', 'Question supprimée.');
    }
}
