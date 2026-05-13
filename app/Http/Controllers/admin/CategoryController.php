<?php

namespace App\Http\Controllers\Admin;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class CategoryController
{
    public function index(Request $request)
    {
        $query = Category::with('children')->whereNull('parent_id');

        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('category_id')) {
            $query->where('id', $request->category_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $categories = $query->orderBy('order', 'desc')->paginate(10);

        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('admin.categories.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:categories,id',
            'type' => 'required|in:1,2',
            'status' => 'required|in:active,inactive',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'icon' => 'nullable|string|max:255'
        ]);

        // Kiểm tra trùng tên với danh mục đã xóa mềm
        $existing = Category::withTrashed()
            ->where('slug', Str::slug($request->name))
            ->where('type', $request->type)
            ->first();

        if ($existing) {
            if ($existing->trashed()) {
                return redirect()->back()->withInput()->withErrors([
                    'name' => 'Tên danh mục này đang ở trong thùng rác. Vui lòng khôi phục hoặc chọn tên khác.'
                ]);
            } else {
                return redirect()->back()->withInput()->withErrors([
                    'name' => 'Tên danh mục này đã tồn tại.'
                ]);
            }
        }

        $data = $request->all();
        $data['slug'] = Str::slug($request->name);

        // Xử lý tải lên hình ảnh
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('uploads/categories'), $imageName);
            $data['image'] = 'uploads/categories/' . $imageName;
        }

        // Nếu không có danh mục cha (parent_id = null), thì lấy order lớn nhất của danh mục cha
        if (is_null($data['parent_id'])) {
            $maxOrder = Category::whereNull('parent_id')->max('order');  // Lấy giá trị order lớn nhất của các danh mục cha
            $data['order'] = $maxOrder + 1;  // Đặt order của danh mục mới là maxOrder + 1
        } else {
            // Nếu có danh mục cha, thì lấy order của danh mục cha và đặt order của danh mục con
            $parentCategory = Category::find($data['parent_id']);
            $data['order'] = $parentCategory->order;  // Gán order của danh mục con bằng order của danh mục cha
        }

        Category::create($data);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Danh mục được tạo thành công.');
    }

    public function show(string $id)
    {
        //
    }

    public function edit(string $id)
    {
        $category = Category::findOrFail($id);
        $categories = Category::whereNull('parent_id')
            ->where('id', '!=', $id)
            ->get();
        return view('admin.categories.edit', compact('category', 'categories'));
    }

    public function update(Request $request, string $id)
    {
        $category = Category::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:categories,id',
            'type' => 'required|in:1,2',
            'status' => 'required|in:active,inactive',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'icon' => 'nullable|string|max:255'
        ]);

        $data = $request->all();
        $data['slug'] = Str::slug($request->name);

        // Xử lý tải lên hình ảnh
        if ($request->hasFile('image')) {
            // Xóa hình ảnh cũ nếu tồn tại
            if ($category->image && file_exists(public_path($category->image))) {
                unlink(public_path($category->image));
            }

            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('uploads/categories'), $imageName);
            $data['image'] = 'uploads/categories/' . $imageName;
        }

        $category->update($data);

        // Cập nhật trạng thái cho tất cả danh mục con
        if ($data['status'] === 'inactive' || $data['status'] === 'active') {
            $this->updateChildrenStatus($category, $data['status']);
        }

        return redirect()->route('admin.categories.index')
            ->with('success', 'Danh mục được cập nhật thành công.');
    }

    private function updateChildrenStatus($category, $status)
    {
        foreach ($category->children as $child) {
            $child->update(['status' => $status]);
            $this->updateChildrenStatus($child, $status);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $category = Category::findOrFail($id);

        // Gọi hàm đệ quy xóa toàn bộ danh mục con
        $this->deleteRecursive($category);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Danh mục và tất cả các danh mục con của nó đã được xóa thành công.');
    }

    private function deleteRecursive($category)
    {
        foreach ($category->children as $child) {
            $this->deleteRecursive($child); // Đệ quy đến cấp con
        }

        $category->delete(); // Sau khi xóa hết con, mới xóa cha
    }

    public function trash()
    {
        $categories = Category::onlyTrashed()->with(['parent', 'children'])->paginate(10);
        return view('admin.categories.trash', compact('categories'));
    }

    public function restore(string $id)
    {
        // Tìm cả bản ghi đã xóa mềm lẫn chưa
        $category = Category::withTrashed()->findOrFail($id);

        // Nếu là danh mục con và cha đang bị soft-delete
        if ($category->parent_id) {
            $parent = Category::withTrashed()->find($category->parent_id);
            if ($parent && $parent->trashed()) {
                return redirect()
                    ->route('admin.categories.trash')
                    ->with('error', 'Vui lòng khôi phục danh mục cha trước khi khôi phục danh mục con.');
            }
        }

        // Nếu không có vấn đề gì, tiến hành khôi phục đệ quy
        $this->restoreRecursive($category);

        return redirect()
            ->route('admin.categories.trash')
            ->with('success', 'Danh mục và tất cả danh mục con đã được khôi phục thành công.');
    }


    private function restoreRecursive($category)
    {
        // Khôi phục danh mục hiện tại
        $category->restore();

        // Khôi phục tất cả các danh mục con đã bị xóa mềm
        $children = Category::onlyTrashed()->where('parent_id', $category->id)->get();

        foreach ($children as $child) {
            $this->restoreRecursive($child); // Gọi đệ quy để khôi phục các cấp con
        }
    }

    // public function forceDelete(string $id)
    // {
    //     $category = Category::onlyTrashed()->findOrFail($id);
    //     $category->forceDelete();
    //     return redirect()->route('admin.categories.trash')->with('success', 'Category deleted permanently.');
    // }

    public function changeOrder(Request $request)
    {
        $category = Category::find($request->category_id);

        if ($request->direction == 'up') {
            $categoryAbove = Category::where('order', '>', $category->order)
                ->orderBy('order', 'asc')
                ->first();

            if ($categoryAbove) {
                $tempOrder = $category->order;
                $category->order = $categoryAbove->order;
                $categoryAbove->order = $tempOrder;
                $category->save();
                $categoryAbove->save();
            }
        }

        if ($request->direction == 'down') {
            $categoryBelow = Category::where('order', '<', $category->order)
                ->orderBy('order', 'desc')
                ->first();

            if ($categoryBelow) {
                $tempOrder = $category->order;
                $category->order = $categoryBelow->order;
                $categoryBelow->order = $tempOrder;
                $category->save();
                $categoryBelow->save();
            }
        }

        return back()->with('success', 'Thứ tự danh mục đã được cập nhật thành công!');
    }

    public function getSpecifications(Category $category)
    {
        $specifications = \App\Models\Specification::where(function ($q) use ($category) {
            $q->whereJsonContains('category_ids', (string)$category->id)
                ->orWhereJsonContains('category_ids', (int)$category->id);
        })
            ->where('status', 'active')
            ->get(['id', 'name']);
        return response()->json($specifications);
    }

    public function getAttributes(Category $category)
    {
        $attributes = \App\Models\VariantAttributeType::where('status', 'active')
            ->whereJsonContains('category_ids', (string)$category->id)
            ->get(['id', 'name']);
        return response()->json($attributes);
    }
}
