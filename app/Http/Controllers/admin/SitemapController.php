<?php

namespace App\Http\Controllers\Admin;

use App\Models\Product;
use App\Models\Category;
use App\Models\Blog;
use App\Models\ProductVariant;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;

class SitemapController
{
    public function __construct()
    {
        // Đặt múi giờ cho Asia/Ho_Chi_Minh
        date_default_timezone_set('Asia/Ho_Chi_Minh');
    }

    public function index()
    {
        return view('admin.sitemap.index');
    }

    public function generate()
    {
        $sitemap = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $sitemap .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        // Thêm trang chủ
        $sitemap .= $this->addUrl(url('/'), '1.0', 'daily');

        // Thêm trang cửa hàng
        $sitemap .= $this->addUrl(url('/shop'), '0.9', 'daily');

        // Thêm sản phẩm
        $products = Product::where('status', 'active')->get();
        foreach ($products as $product) {
            $sitemap .= $this->addUrl(
                url('/product/' . $product->slug),
                '0.8',
                'weekly',
                $product->updated_at
            );
        }

        // Thêm biến thể sản phẩm
        $variants = ProductVariant::where('status', 'active')->get();
        foreach ($variants as $variant) {
            $product = Product::find($variant->product_id);
            if ($product && $product->status === 'active') {
                $sitemap .= $this->addUrl(
                    url('/product/' . $product->slug . '/' . $variant->slug),
                    '0.7',
                    'weekly',
                    $variant->updated_at
                );
            }
        }

            // Thêm danh mục
        $categories = Category::where('status', 'active')->where('type', 1)->get();
        foreach ($categories as $category) {
            $sitemap .= $this->addUrl(
                url('/shop/' . $category->slug),
                '0.8',
                'weekly',
                $category->updated_at
            );
        }

        // Add blogs
        $blogs = Blog::where('status', 'active')->get();
        foreach ($blogs as $blog) {
            $sitemap .= $this->addUrl(
                url('/blog/' . $blog->slug),
                '0.6',
                'monthly',
                $blog->updated_at
            );
        }

        // Thêm các trang tĩnh khác
        $sitemap .= $this->addUrl(url('/about'), '0.7', 'monthly');
        $sitemap .= $this->addUrl(url('/contact'), '0.7', 'monthly');
        $sitemap .= $this->addUrl(url('/faq'), '0.6', 'monthly');
        $sitemap .= $this->addUrl(url('/blog'), '0.8', 'weekly');
        $sitemap .= $this->addUrl(url('/location'), '0.5', 'monthly');

        $sitemap .= '</urlset>';

        // Lưu sitemap với định dạng XML chính xác
        $response = Response::make($sitemap, 200);
        $response->header('Content-Type', 'application/xml');
        File::put(public_path('sitemap.xml'), $response->getContent());

        return redirect()->back()->with('success', 'Sitemap đã được tạo thành công!');
    }

    private function addUrl($url, $priority = '0.5', $changefreq = 'monthly', $lastmod = null)
    {
        $url = str_replace('&', '&amp;', $url);
        
        if ($lastmod) {
            // Chuyển đổi sang múi giờ Việt Nam
            $lastmod = Carbon::parse($lastmod)->setTimezone('Asia/Ho_Chi_Minh');
        } else {
            $lastmod = Carbon::now()->setTimezone('Asia/Ho_Chi_Minh');
        }
        
        return "\t<url>\n" .
            "\t\t<loc>$url</loc>\n" .
            "\t\t<lastmod>" . $lastmod->format('Y-m-d\TH:i:sP') . "</lastmod>\n" .
            "\t\t<changefreq>$changefreq</changefreq>\n" .
            "\t\t<priority>$priority</priority>\n" .
            "\t</url>\n";
    }

    public function view()
    {
        $sitemapPath = public_path('sitemap.xml');
        if (!File::exists($sitemapPath)) {
            abort(404);
        }

        return response(File::get($sitemapPath))
            ->header('Content-Type', 'application/xml')
            ->header('X-Robots-Tag', 'noindex')
            ->header('Cache-Control', 'public, max-age=3600');
    }
} 