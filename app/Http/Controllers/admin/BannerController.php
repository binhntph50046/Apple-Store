<?php

namespace App\Http\Controllers\Admin;

use App\Models\Banner;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class BannerController
{
    /**
     * Hiển thị danh sách tài nguyên.
     */
    public function index()
    {
        //
        $banners = Banner::orderBy('order')->paginate(10);;
        return view('admin.banners.index', compact('banners'));
    }

    /**
     * Hiển thị form để tạo tài nguyên mới.
     */
    public function create()
    {
        // Lấy giá trị max order hiện tại, nếu không có banner nào thì mặc định là 0
        $maxOrder = Banner::max('order') ?: 0;
        return view('admin.banners.create', compact('maxOrder'));
    }

    /**
     * Lưu trữ tài nguyên mới được tạo.
     */
    public function store(Request $request)
    {
        // Validate dữ liệu đầu vào
        $request->validate([
            'title' => 'required|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif',
            'status' => 'required|in:active,inactive',
            'description' => 'nullable|string|max:255',
            'link' => 'nullable|url',
        ], [
            'title.required' => 'Tiêu đề là bắt buộc.',
            'title.max' => 'Tiêu đề không được vượt quá 255 ký tự.',

            'image.required' => 'Vui lòng chọn hình ảnh.',
            'image.image' => 'Tệp phải là hình ảnh.',
            'image.mimes' => 'Hình ảnh phải có định dạng: jpeg, png, jpg, gif.',

            'status.required' => 'Vui lòng chọn trạng thái.',
            'status.in' => 'Trạng thái không hợp lệ.',

            'description.max' => 'Mô tả không được vượt quá 255 ký tự.',

            'link.url' => 'Đường dẫn không hợp lệ.',
        ]);

        // Tính toán giá trị order cho banner mới
        $maxOrder = Banner::max('order');
        $newOrder = $maxOrder + 1; // Tạo order mới từ max order + 1

        // Tạo và lưu banner mới
        $banner = new Banner();
        $banner->title = $request->title;
        $banner->status = $request->status;
        $banner->order = $newOrder;
        $banner->link = $request->link;
        $banner->description = $request->description;

        // // Xử lý upload ảnh
        // if ($request->hasFile('image')) {
        //     // Lưu ảnh vào thư mục 'banners' trong storage
        //     $imagePath = $request->file('image')->store('banners', 'public');
        //     $banner->image = $imagePath;
        // }

        // Xử lý upload ảnh
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = uniqid() . '.' . $image->getClientOriginalExtension(); // tạo tên ảnh duy nhất
            $uploadPath = public_path('uploads/banners');

            // Tạo thư mục nếu chưa có
            if (!file_exists($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }

            // Di chuyển ảnh vào thư mục public/uploads/banners
            $image->move($uploadPath, $imageName);

            // Lưu đường dẫn tương đối vào DB (để dùng với asset())
            $banner->image = 'uploads/banners/' . $imageName;
        }


        // Lưu banner vào cơ sở dữ liệu
        $banner->save();

        // Chuyển hướng và thông báo thành công
        return redirect()->route('admin.banners.index')->with('success', 'Banner đã được tạo thành công');
    }

    /**
     * Hiển thị tài nguyên được chỉ định.
     */
    public function show(Banner $banner)
    {
        //
    }

    /**
     * Hiển thị form để chỉnh sửa tài nguyên được chỉ định.
     */
    public function edit(Banner $banner)
    {
        //
        return view('admin.banners.edit', compact('banner'));
    }

    /**
     * Cập nhật tài nguyên được chỉ định.
     */
    public function update(Request $request, Banner $banner)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif',
            'description' => 'nullable|string|max:255',
            'status' => 'required|in:active,inactive',
            'link' => 'nullable|url',
        ], [
            'title.required' => 'Tiêu đề là bắt buộc.',
            'title.max' => 'Tiêu đề không được vượt quá 255 ký tự.',

            'image.image' => 'Tệp phải là hình ảnh.',
            'image.mimes' => 'Hình ảnh phải có định dạng: jpeg, png, jpg, gif.',

            'description.max' => 'Mô tả không được vượt quá 255 ký tự.',

            'status.required' => 'Vui lòng chọn trạng thái.',
            'status.in' => 'Trạng thái không hợp lệ.',

            'link.url' => 'Đường dẫn không hợp lệ.',
        ]);

        $banner->title = $request->title;
        $banner->status = $request->status;
        $banner->link = $request->link;
        $banner->description = $request->description;

        if ($request->hasFile('image')) {
            // Xoá ảnh cũ nếu cần
            if ($banner->image && file_exists(public_path($banner->image))) {
                unlink(public_path($banner->image));
            }

            $image = $request->file('image');
            $imageName = uniqid() . '.' . $image->getClientOriginalExtension(); // Tạo tên ảnh duy nhất
            $uploadPath = public_path('uploads/banners');

            // Tạo thư mục nếu chưa tồn tại
            if (!file_exists($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }

            $image->move($uploadPath, $imageName); // Di chuyển ảnh đến thư mục public/uploads/banners

            $banner->image = 'uploads/banners/' . $imageName; // Lưu đường dẫn tương đối
        }


        $banner->save();

        return redirect()->route('admin.banners.index')->with('success', 'Banner đã được cập nhật thành công');
    }

    /**
     * Xóa tài nguyên được chỉ định.
     */
    public function destroy(Banner $banner)
    {
        // Xóa ảnh nếu tồn tại
        if ($banner->image && Storage::disk('public')->exists($banner->image)) {
            Storage::disk('public')->delete($banner->image);
        }

        $banner->delete();

        return redirect()->route('admin.banners.index')->with('success', 'Banner đã được xóa thành công');
    }
    public function moveUp(Banner $banner)
    {
        $prevBanner = Banner::where('order', '<', $banner->order)->orderBy('order', 'desc')->first();
        if ($prevBanner) {
            $temp = $banner->order;
            $banner->order = $prevBanner->order;
            $prevBanner->order = $temp;

            $banner->save();
            $prevBanner->save();
        }

        return redirect()->route('admin.banners.index')->with('success', 'Di chuyển lên thành công');
    }

    public function moveDown(Banner $banner)
    {
        $nextBanner = Banner::where('order', '>', $banner->order)->orderBy('order')->first();
        if ($nextBanner) {
            $temp = $banner->order;
            $banner->order = $nextBanner->order;
            $nextBanner->order = $temp;

            $banner->save();
            $nextBanner->save();
        }

        return redirect()->route('admin.banners.index')->with('success', 'Di chuyển xuống thành công');
    }
    public function updateOrder(Request $request)
    {
        $order = $request->input('order');
        foreach ($order as $id => $position) {
            Banner::where('id', $id)->update(['order' => $position]);
        }

        return response()->json(['success' => true]);
        // return redirect()->route('banners.index')->with('success', 'Thứ tự banner đã được cập nhật.');
    }
}
