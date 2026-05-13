<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Specification;
use Illuminate\Http\Request;

class ProductSpecificationController
{
    /**
     * Hiển thị danh sách tài nguyên.
     */
    public function index(Request $request)
    {
        $query = Specification::query();

        if ($request->has('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }

        if ($request->has('category_id')) {
            $query->whereJsonContains('category_ids', $request->category_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $specifications = $query->paginate(10);
        $categories = Category::where('type', 1)->get();

        return view('admin.specifications.index', compact('specifications', 'categories'));
    }

    /**
     * Hiển thị form để tạo tài nguyên mới.
     */
    public function create()
    {
        $categories = Category::where('type', 1)->get();
        return view('admin.specifications.create', compact('categories'));
    }

    /**
     * Lưu trữ tài nguyên mới được tạo.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_ids' => 'required|array',
            'category_ids.*' => 'exists:categories,id',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive'
        ]);

        $specification = Specification::create([
            'name' => $request->name,
            'description' => $request->description,
            'category_ids' => $request->category_ids,
            'status' => $request->status
        ]);

        return redirect()
            ->route('admin.specifications.index')
            ->with('success', 'Thông số kỹ thuật đã được tạo thành công.');
    }

    /**
     * Hiển thị tài nguyên được chỉ định.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Hiển thị form để chỉnh sửa tài nguyên được chỉ định.
     */
    public function edit(Specification $specification)
    {
        $categories = Category::where('type', 1)->get();
        return view('admin.specifications.edit', compact('specification', 'categories'));
    }

    /**
     * Cập nhật tài nguyên được chỉ định trong bộ nhớ.
     */
    public function update(Request $request, Specification $specification)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_ids' => 'required|array',
            'category_ids.*' => 'exists:categories,id',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive'
        ]);

        $specification->update([
            'name' => $request->name,
            'description' => $request->description,
            'category_ids' => $request->category_ids,
            'status' => $request->status
        ]);

        return redirect()
            ->route('admin.specifications.index')
            ->with('success', 'Thông số kỹ thuật đã được cập nhật thành công.');
    }

    /**
     * Xóa tài nguyên được chỉ định khỏi bộ nhớ.
     */
    public function destroy(Specification $specification)
    {
        $specification->delete();

        return redirect()
            ->route('admin.specifications.index')
            ->with('success', 'Thông số kỹ thuật đã được xóa thành công.');
    }

    public function trash()
    {
        $specifications = Specification::onlyTrashed()
            ->orderBy('deleted_at', 'desc')
            ->paginate(10);

        return view('admin.specifications.trash', compact('specifications'));
    }

    public function restore($id)
    {
        $specification = Specification::withTrashed()->findOrFail($id);
        $specification->restore();

        return redirect()
            ->route('admin.specifications.trash')
            ->with('success', 'Thông số kỹ thuật đã được khôi phục thành công.');
    }
}
