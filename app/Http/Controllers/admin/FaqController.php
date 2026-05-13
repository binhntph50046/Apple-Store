<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\Request;

class FaqController
{
    public function index(Request $request)
{
    $query = Faq::query();

    if ($request->filled('question')) {
        $query->where('question', 'LIKE', '%' . $request->question . '%');
    }

    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    $faqs = $query->paginate(10);
    return view('admin.faqs.index', compact('faqs'));
}

    public function create()
    {
        return view('admin.faqs.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'question' => 'required|string|max:255',
            'answer' => 'required|string',
            'keywords' => 'nullable|string',
            'status' => 'in:active,inactive'
        ]);

        Faq::create($request->all());
        return redirect()->route('admin.faqs.index')->with('success', 'FAQ đã được tạo thành công.');
    }

    public function show(Faq $faq)
    {
        return view('admin.faqs.show', compact('faq'));
    }

    public function edit(Faq $faq)
    {
        return view('admin.faqs.edit', compact('faq'));
    }

    public function update(Request $request, Faq $faq)
    {
        $request->validate([
            'question' => 'required|string|max:255',
            'answer' => 'required|string',
            'keywords' => 'nullable|string',
            'status' => 'in:active,inactive'
        ]);

        $faq->update($request->all());
        return redirect()->route('admin.faqs.index')->with('success', 'FAQ đã được cập nhật thành công.');
    }

    public function destroy(Faq $faq)
    {
        $faq->delete();
        return redirect()->route('admin.faqs.index')->with('success', 'FAQ đã được xóa thành công.');
    }

    public function trash()
    {
        $faqs = Faq::onlyTrashed()->paginate(10);
        return view('admin.faqs.trash', compact('faqs'));
    }

    public function restore($id)
    {
        $faq = Faq::withTrashed()->findOrFail($id);
        $faq->restore();
        return redirect()->route('admin.faqs.index')->with('success', 'FAQ đã được khôi phục thành công.');
    }

    public function forceDelete($id)
    {
        $faq = Faq::withTrashed()->findOrFail($id);
        $faq->forceDelete();
        return redirect()->route('admin.faqs.trash')->with('success', 'FAQ đã được xóa vĩnh viễn.');
    }
}