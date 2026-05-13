<?php

namespace App\Http\Controllers\client;

use App\Models\Product;
use App\Models\ProductSpecification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CompareController
{
    public function index(Request $request)
    {
        $productIds = $request->input('products', []);
        
        // Xử lý trường hợp products là string (từ GET query parameters)
        if (is_string($productIds)) {
            $productIds = explode(',', $productIds);
        }
        
        // Kiểm tra số lượng sản phẩm (2-4 sản phẩm)
        if (count($productIds) < 2 || count($productIds) > 4) {
            return view('client.compare.index', [
                'comparison' => [
                    'products' => collect([]),
                    'specs' => []
                ],
                'aiAdvice' => 'Vui lòng chọn từ 2 đến 4 sản phẩm để so sánh.'
            ]);
        }

        $products = Product::with(['specifications.specification', 'variants'])
            ->whereIn('id', $productIds)
            ->get();

        // Validate category
        if ($products->count() > 1) {
            $firstCategory = $products->first()->category_id;
            $allSameCategory = $products->every(function ($product) use ($firstCategory) {
                return $product->category_id === $firstCategory;
            });

            if (!$allSameCategory) {
                return view('client.compare.index', [
                    'comparison' => [
                        'products' => collect([]),
                        'specs' => []
                    ],
                    'aiAdvice' => 'Vui lòng chỉ so sánh các sản phẩm trong cùng một danh mục.'
                ]);
            }
        }

        // Lấy tất cả các loại thông số kỹ thuật liên quan
        $allSpecs = ProductSpecification::whereIn('product_id', $productIds)
            ->with('specification')
            ->get();

        $specNames = $allSpecs->pluck('specification.name')->unique();
        $specs = [];
        foreach ($specNames as $name) {
            foreach ($products as $product) {
                $spec = $product->specifications->firstWhere('specification.name', $name);
                $specs[$name][$product->id] = $spec ? $spec->value : null;
            }
        }

        // Chuẩn bị dữ liệu gửi cho AI
        $prompt = $this->buildPrompt($products, $specs);
        $aiAdvice = $this->callGemini($prompt);

        return view('client.compare.index', [
            'comparison' => [
                'products' => $products,
                'specs' => $specs
            ],
            'aiAdvice' => $aiAdvice
        ]);
    }

    private function buildPrompt($products, $specs)
    {
        $prompt = "Tôi muốn so sánh " . count($products) . " sản phẩm sau dựa trên các thông số kỹ thuật. Hãy phân tích ưu nhược điểm từng sản phẩm và đưa ra lời khuyên ngắn gọn khoảng 2-3 câu nên mua sản phẩm nào phù hợp nhất cho khách hàng phổ thông, nên mua sản phẩm này vì điều gì...\n\n";
        foreach ($products as $index => $product) {
            $prompt .= "**Sản phẩm " . ($index + 1) . ": {$product->name}**\n";
            
            // Lấy giá chính xác từ biến thể sản phẩm, ưu tiên giá khuyến mãi
            $price = 0;
            if ($product->variants->isNotEmpty()) {
                $variant = $product->variants->firstWhere('is_default', true) ?? $product->variants->first();
                if ($variant) {
                    $price = $variant->discount_price ?: $variant->selling_price;
                }
            }
            
            $prompt .= "Giá: " . number_format($price, 0, ',', '.') . " VNĐ\n";
            
            foreach ($specs as $specName => $values) {
                $value = $values[$product->id] ?? 'N/A';
                $prompt .= "- {$specName}: {$value}\n";
            }
            $prompt .= "\n";
        }
        $prompt .= "Hãy trả lời ngắn gọn, dễ hiểu bằng tiếng Việt. Tập trung vào những điểm khác biệt quan trọng nhất.";
        return $prompt;
    }

    private function callGemini($prompt)
    {
        $apiKey = env('GEMINI_API_KEY');
        $url = 'https://generativelanguage.googleapis.com/v1/models/gemini-1.5-flash:generateContent?key=' . $apiKey;
        $response = Http::withHeaders([
            'Content-Type' => 'application/json'
        ])->timeout(30)->post($url, [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt]
                    ]
                ]
            ],
            'generationConfig' => [
                'maxOutputTokens' => 500,
                'temperature' => 0.7
            ]
        ]);
        if ($response->successful()) {
            $result = $response->json();
            return $result['candidates'][0]['content']['parts'][0]['text'] ?? 'Không có phản hồi từ Gemini.';
        }
        return 'Không có phản hồi từ Gemini.';
    }

    private function getDefaultAdvice()
    {
        return "Dựa trên thông số kỹ thuật, bạn nên cân nhắc các yếu tố sau khi lựa chọn sản phẩm:
                \n- Cấu hình và hiệu năng
                \n- Giá cả và khuyến mãi
                \n- Thời gian bảo hành
                \n- Đánh giá từ người dùng
                \nHãy so sánh kỹ các thông số bên trên và chọn sản phẩm phù hợp nhất với nhu cầu sử dụng và ngân sách của bạn.";
    }
}