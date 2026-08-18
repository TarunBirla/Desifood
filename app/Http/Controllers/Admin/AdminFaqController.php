<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\Request;

class AdminFaqController extends Controller
{
    public function index()
    {
        $faqs = Faq::orderBy('order', 'asc')->get();
        return view('admin.faqs.index', compact('faqs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'question' => 'required|string|max:255',
            'answer' => 'required|string',
            'order' => 'nullable|integer',
        ]);

        Faq::create([
            'question' => $request->question,
            'answer' => $request->answer,
            'order' => $request->order ?? 0,
            'is_active' => $request->has('is_active') ? (bool)$request->is_active : true,
        ]);

        return back()->with('success', 'FAQ added successfully!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'question' => 'required|string|max:255',
            'answer' => 'required|string',
            'order' => 'nullable|integer',
        ]);

        $faq = Faq::findOrFail($id);
        $faq->update([
            'question' => $request->question,
            'answer' => $request->answer,
            'order' => $request->order ?? 0,
            'is_active' => $request->has('is_active') ? (bool)$request->is_active : false,
        ]);

        return back()->with('success', 'FAQ updated successfully!');
    }

    public function destroy($id)
    {
        $faq = Faq::findOrFail($id);
        $faq->delete();
        return back()->with('success', 'FAQ deleted successfully!');
    }
}
