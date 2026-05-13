<?php

namespace App\Http\Controllers\client;

use Illuminate\Http\Request;
use App\Models\Faq;
use Illuminate\Support\Facades\View;


class FaqsController 
{
    public function index()
    {
        // Lấy tất cả các câu hỏi thường gặp
        $faqs = Faq::where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->get();
        
        View::share('faq', $faqs);
        return view('client.faq.index', compact('faqs'));
    }
    public function ask(Request $request)
    {
        $question = $request->input('question');

        // Tìm câu hỏi giống nhất (có thể dùng LIKE hoặc fulltext search)
        $faq = Faq::where('question', 'like', '%' . $question . '%')->first();

        if ($faq) {
            return response()->json(['answer' => $faq->answer]);
        }

        // Nếu không tìm thấy, fallback sang AI hoặc trả về câu mặc định
        return response()->json(['answer' => 'Xin lỗi, tôi chưa hiểu rõ câu hỏi.']);
    }
}
