<?php

namespace App\Http\Controllers\admin;

use App\Models\ProductVariant;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProductVariantController
{
    public function index(Request $request)
    {
        $query = ProductVariant::with(['product', 'attributeValues']);

        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }
        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Thêm dòng này để sắp xếp biến thể mới nhất lên đầu
        $query->orderBy('created_at', 'desc');

        $variants = $query->paginate(20)->appends($request->query());
        $trashCount = ProductVariant::onlyTrashed()->count();
        $products = Product::select('id', 'name')->orderBy('name')->get();
        return view('admin.variants.index', compact('variants', 'trashCount', 'products'));
    }

    public function trash()
    {
        $variants = ProductVariant::onlyTrashed()->with(['product', 'attributeValues'])->paginate(20);
        return view('admin.variants.trash', compact('variants'));
    }

    public function restore($id)
    {
        try {
            DB::beginTransaction();

            $variant = ProductVariant::onlyTrashed()->findOrFail($id);

            // Kiểm tra xem biến thể này có phải là biến thể mặc định ban đầu không
            $wasDefault = $variant->is_default;

            // Khôi phục biến thể và các combinations
            $variant->restore();
            $variant->combinations()->withTrashed()->restore();

            // Nếu biến thể này là mặc định ban đầu, đặt lại nó làm mặc định
            if ($wasDefault) {
                // Bỏ mặc định của biến thể hiện tại (nếu có)
                ProductVariant::where('product_id', $variant->product_id)
                    ->where('id', '!=', $variant->id)
                    ->where('is_default', 1)
                    ->update(['is_default' => 0]);

                // Đặt lại biến thể này làm mặc định
                $variant->update(['is_default' => 1]);
            }

            DB::commit();
            return redirect()->route('admin.variants.trash')
                ->with('success', 'Khôi phục biến thể thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi khi khôi phục biến thể: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Có lỗi xảy ra khi khôi phục biến thể.');
        }
    }

    public function update(Request $request, ProductVariant $variant)
    {
        $request->validate([
            'purchase_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'status' => 'required|in:active,inactive',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp',
        ]);

        $variant->purchase_price = $request->purchase_price;
        $variant->selling_price = $request->selling_price;
        $variant->stock = $request->stock;
        $variant->status = $request->status;

        // Xử lý ảnh mới nếu có
        if ($request->hasFile('images')) {
            $images = [];
            foreach ($request->file('images') as $img) {
                $filename = time() . '_' . uniqid() . '_' . $img->getClientOriginalName();
                $img->move(public_path('uploads/products'), $filename);
                $images[] = 'uploads/products/' . $filename;
            }
            $variant->images = $images;
        }
        $variant->save();
        return redirect()->route('admin.variants.index')->with('success', 'Cập nhật biến thể thành công!');
    }

    public function destroy(ProductVariant $variant)
    {
        try {
            DB::beginTransaction();

            $productId = $variant->product_id;

            // Kiểm tra xem biến thể này có phải là mặc định không
            if ($variant->is_default) {
                // Tìm một biến thể khác còn tồn tại của cùng sản phẩm
                $newDefaultVariant = ProductVariant::where('product_id', $variant->product_id)
                    ->where('id', '!=', $variant->id)
                    ->whereNull('deleted_at')
                    ->first();

                if ($newDefaultVariant) {
                    // Đặt biến thể mới làm mặc định
                    $newDefaultVariant->update(['is_default' => 1]);
                }
            }

            // Xóa mềm các combinations và biến thể
            $variant->combinations()->delete();
            $variant->delete();

            // Kiểm tra nếu không còn biến thể nào của sản phẩm này (chỉ tính biến thể chưa bị xóa mềm)
            $remainingVariants = ProductVariant::where('product_id', $productId)
                ->whereNull('deleted_at')
                ->count();

            if ($remainingVariants === 0) {
                // Xóa mềm luôn sản phẩm
                $product = Product::find($productId);
                if ($product) {
                    $product->delete();
                }
            }

            DB::commit();
            return redirect()->route('admin.variants.index')
                ->with('success', 'Đã xóa biến thể thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi khi xóa biến thể: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Có lỗi xảy ra khi xóa biến thể.');
        }
    }
}
