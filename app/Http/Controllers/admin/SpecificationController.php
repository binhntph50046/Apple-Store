<?php

namespace App\Http\Controllers\Admin;

use App\Models\Category;
use App\Models\Specification;
use Illuminate\Http\Request;

class SpecificationController
{
    public function index(Request $request)
    {
        $categories = Category::where('type', 1)->get();
        $query = Specification::query();
        
        // Tìm kiếm theo tên
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%$search%");
        }
        
        // Lọc theo danh mục
        if ($request->filled('category_id')) {
            $query->whereJsonContains('category_ids', $request->category_id);
        }
        
        $specifications = $query->paginate(10)->appends($request->all());
        return view('admin.specifications.index', compact('specifications', 'categories'));
    }

    public function create()
    {
        $categories = Category::where('type', 1)->get();
        return view('admin.specifications.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:specifications,name',
            'category_ids' => 'required|array',
            'category_ids.*' => 'exists:categories,id',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive'
        ]);
        $specification = Specification::create($request->only(['name', 'description', 'status', 'category_ids']));
        return redirect()->route('admin.specifications.index')->with('success', 'Thông số kỹ thuật đã được tạo thành công.');
    }

    public function edit(Specification $specification)
    {
        $categories = Category::where('type', 1)->get();
        return view('admin.specifications.edit', compact('specification', 'categories'));
    }

    public function update(Request $request, Specification $specification)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:specifications,name,' . $specification->id,
            'category_ids' => 'required|array',
            'category_ids.*' => 'exists:categories,id',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive'
        ]);
        $specification->update($request->only(['name', 'description', 'status', 'category_ids']));
        return redirect()->route('admin.specifications.index')->with('success', 'Thông số kỹ thuật đã được cập nhật thành công.');
    }

    public function destroy(Specification $specification)
    {
        $specification->delete();
        return redirect()->route('admin.specifications.index')->with('success', 'Thông số kỹ thuật đã được xóa thành công.');
    }

    public function trash()
    {
        $trashedSpecifications = Specification::onlyTrashed()->paginate(10);
        return view('admin.specifications.trash', compact('trashedSpecifications'));
    }

    public function restore($id)
    {
        $specification = Specification::withTrashed()->findOrFail($id);
        $specification->restore();
        return redirect()->route('admin.specifications.trash')->with('success', 'Thông số kỹ thuật đã được khôi phục thành công.');
    }
}
