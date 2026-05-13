<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Auth;
use App\Models\Blog;
use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Str;
use App\Models\User;



class BlogController
{
    //
    public function index(Request $request)
    {
        // Lấy query blog kèm quan hệ category và author
        $query = Blog::with(['category', 'author']);

        // Lọc theo danh mục
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Lọc theo tác giả (chỉ admin hoặc staff)
        if ($request->filled('author_id')) {
            $query->where('author_id', $request->author_id);
        }

        $blogs = $query->latest()->get(); // hoặc paginate(n) nếu có phân trang

        // Lấy dữ liệu dropdown
        $categories = Category::where('type', 2)->get();
        // Chỉ lấy user có role admin hoặc staff qua bảng trung gian roles
        $authors = User::whereHas('roles', function($q) {
            $q->whereIn('name', ['admin', 'staff']);
        })->get();

        return view('admin.blogs.index', compact('blogs', 'categories', 'authors'));
    }

    public function create()
    {
        $categories = Category::where('type', 2)->get();
        // Lấy user có role admin hoặc staff qua bảng trung gian
        $authors = User::whereHas('roles', function($q) {
            $q->whereIn('name', ['admin', 'staff']);
        })->get();
        return view('admin.blogs.create', compact('categories','authors'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'required|integer',
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|in:active,inactive',
            'author_id'=> 'required',
        ]);


        // // Xử lý ảnh nếu có
        // $imagePath = null;
        // if ($request->hasFile('image')) {
        //     $file     = $request->file('image');
        //     $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

        //     // Tạo thư mục nếu chưa tồn tại
        //     $destinationPath = public_path('uploads/blogs');
        //     if (!file_exists($destinationPath)) {
        //         mkdir($destinationPath, 0755, true);
        //     }

        //     // Di chuyển file vào public/upload/blogs
        //     $file->move($destinationPath, $filename);

        //     // Lưu đường dẫn tương đối (dùng để hiển thị ảnh)
        //     $imagePath = 'upload/blogs/' . $filename;
        // }


        // Xử lý tải lên hình ảnh vào public/uploads/blogs
        $imagePath = null;
        if ($request->hasFile('image')) {
            $file       = $request->file('image');
            $filename   = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $destPath   = public_path('uploads/blogs');
            if (!file_exists($destPath)) {
                mkdir($destPath, 0755, true);
            }
            $file->move($destPath, $filename);
            $imagePath = 'uploads/blogs/' . $filename;
        }


        // Tạo slug từ title
        $slug = Str::slug($request->title);

        // Lưu bài viết
        Blog::create([
            'title' => $request->title,
            'slug' => $slug,
            'category_id' => $request->category_id,
            'content' => $request->content,
            'image' => $imagePath, // Lưu đường dẫn ảnh
            'status' => $request->status, // Lưu trạng thái
            'author_id'=> $request->author_id,
        ]);

        return redirect()->route('admin.blogs.index')
            ->with('success', 'Tạo Thành công bài viết!');
    }
    public function show(Blog $blog)
    {
        // dd($blog);
        return view('admin.blogs.show', compact('blog'));
    }
    public function edit(Blog $blog)
    {
        $categories = Category::where('type', 2)->get();
        // Chỉ lấy user có role admin hoặc staff qua bảng trung gian roles
        $authors = User::whereHas('roles', function($q) {
            $q->whereIn('name', ['admin', 'staff']);
        })->get();
        return view('admin.blogs.edit', compact('blog', 'categories','authors'));
    }

    public function update(Request $request, Blog $blog)
    {
        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'category_id' => 'required|integer|exists:categories,id',
            'content'     => 'required|string',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status'      => 'required|in:active,inactive',
            'author_id'   => 'required',
        ]);

        // Xử lý tải lên hình ảnh mới nếu được cung cấp
        if ($request->hasFile('image')) {
            // Tùy chọn xóa hình ảnh cũ ở đây
            $file     = $request->file('image');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $destPath = public_path('uploads/blogs');
            if (!file_exists($destPath)) {
                mkdir($destPath, 0755, true);
            }
            $file->move($destPath, $filename);
            $data['image'] = 'uploads/blogs/' . $filename;
        }

        // Cập nhật slug nếu tiêu đề thay đổi
        if ($data['title'] !== $blog->title) {
            $data['slug'] = Str::slug($data['title']);
        }

        $blog->update($data);

        return redirect()->route('admin.blogs.index')
            ->with('success', 'Bài viết đã được cập nhật thành công!');
    }

    public function destroy(Blog $blog)
    {
        // Nếu có ảnh, xoá file ảnh cũ
        // if ($blog->image && file_exists(public_path($blog->image))) {
        //     @unlink(public_path($blog->image));
        // }

        // Xoá bản ghi
        $blog->delete();

        return redirect()
            ->route('admin.blogs.index')
            ->with('success', 'Bài viết đã được xóa thành công!');
    }

    public function trash()
    {
        $blogs = Blog::onlyTrashed()->with('category', 'author')->paginate(12);
        return view('admin.blogs.trash', data: compact('blogs'));
    }

    /**
     * Khôi phục một blog đã xóa mềm.
     */
    public function restore($id)
    {
        $blog = Blog::onlyTrashed()->findOrFail($id);
        $blog->restore();

        return redirect()->route('admin.blogs.index')
            ->with('success', 'Khôi phục bài viết thành công!');
    }

    /**
     * Xóa vĩnh viễn một blog.
     */
 
}
