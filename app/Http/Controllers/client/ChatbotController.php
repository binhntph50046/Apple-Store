<?php

namespace App\Http\Controllers\client;

use Illuminate\Http\Request;
use App\Models\Faq;
use Illuminate\Support\Facades\DB;

class ChatbotController
{
    public function ask(Request $request)
    {
        $msg = trim($request->input('text', ''));
        if ($msg === '') {
            return response()->json(['answer' => 'No input']);
        }

        // Trả lời cứng nếu hỏi "bạn là ai"
        if (mb_stripos($msg, 'bạn là ai') !== false) {
            return response()->json([
                'answer' => 'Tôi là nhân viên của tập đoàn Apple Store, luôn sẵn sàng hỗ trợ bạn mọi thắc mắc về sản phẩm và dịch vụ. Bạn muốn hỏi gì không?'
            ]);
        }

        // 1. Tìm trong bảng faqs
        $row = DB::table('faqs')
            ->where('question', 'like', "%$msg%")
            ->orWhere('keywords', 'like', "%$msg%")
            ->first();

        if ($row) {
            return response()->json(['answer' => $row->answer]);
        }

        // 2. Nếu không có, chỉ lấy dữ liệu từ các bảng bạn muốn gửi cho AI
        $tables = [
            'faqs' => '*',
            'categories' => '*',
            'product_variants' => '*',
            'products' => ['name', 'slug', 'description'],
            'vouchers' => ['description'],
            'flash_sale_items' => ['product_variant_id', 'count', 'discount'],
            'flash_sales' => ['name']
        ];
        $all_data_text = "";
        foreach ($tables as $table => $fields) {
            $all_data_text .= "Bảng: $table\n";
            if ($fields === '*') {
                $rows = DB::table($table)->get();
            } else {
                $rows = DB::table($table)->select($fields)->get();
            }
            foreach ($rows as $r) {
                $all_data_text .= json_encode($r, JSON_UNESCAPED_UNICODE) . "\n";
            }
            $all_data_text .= "\n";
        }

        // Gọi Gemini API
        $api_key = "AIzaSyC7bDHaBORo63DHUPL-PiILtmul8YQiOaU";
        $url = "https://generativelanguage.googleapis.com/v1/models/gemini-2.0-flash:generateContent?key=$api_key";
        $prompt = "Bạn là nhân viên tư vấn chuyên nghiệp, luôn đặt nhu cầu khách hàng lên hàng đầu. 
            Khi trả lời, hãy luôn bắt đầu bằng 'Dạ thưa anh/chị,' hoặc 'Dạ vâng, thưa anh/chị,' và trả lời thật lịch sự, chu đáo. 
            Chỉ trả lời đúng sản phẩm mà khách hàng hỏi, không liệt kê các sản phẩm khác. 
            Khi liệt kê sản phẩm, hãy sử dụng định dạng danh sách HTML <ul><li></li></ul> (không dùng dấu sao *). 
            Mỗi sản phẩm phải tạo liên kết có dạng: 
            <a href=\"http://127.0.0.1:8000/product/{slug}\" target=\"_blank\">{tên sản phẩm}</a>.
            Khi nào khách hàng muốn nhắn tin trực tiếp với admin, hãy cung cấp liên kết chat trực tiếp với admin như sau còn không thì không hiện:
            <a href=\"/chat\" target=\"_blank\">Nhấn vào đây để chat trực tiếp với admin</a>.
            Dữ liệu:
            $all_data_text

            Hãy trả lời NGẮN GỌN, trực tiếp cho câu hỏi sau: \"$msg\". 
            Chỉ trả lời nội dung, không cần giải thích, không nhắc lại câu hỏi, không nói dựa vào bảng nào.
            ";

        $data = [
            "contents" => [
                [
                    "parts" => [
                        ["text" => $prompt]
                    ]
                ]
            ]
        ];
        $options = [
            "http" => [
                "header"  => "Content-type: application/json\r\n",
                "method"  => "POST",
                "content" => json_encode($data),
                "timeout" => 10
            ]
        ];
        $context  = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        $json = json_decode($result, true);
        if (isset($json['candidates'][0]['content']['parts'][0]['text'])) {
            $answer = $json['candidates'][0]['content']['parts'][0]['text'];
            // Kiểm tra nếu câu trả lời không liên quan (ví dụ: chứa "tôi không thể" hoặc không có thông tin cụ thể)
            if (stripos($answer, 'chưa hỗ trợ') !== false || stripos($answer, 'không hỗ trợ') !== false) {
                $liveChatLink = '<a href="/chat" target="_blank">Nhấn vào đây để chat trực tiếp với admin</a>';
                return response()->json(['answer' => 'Dạ thưa anh/chị, tôi chưa tìm thấy câu trả lời phù hợp. ' . $liveChatLink]);
            }
            return response()->json(['answer' => $answer]);
        } else {
            $liveChatLink = '<a href="/chat" target="_blank">Nhấn vào đây để chat trực tiếp với admin</a>';
            return response()->json(['answer' => 'Dạ thưa anh/chị, tôi chưa tìm thấy câu trả lời. ' . $liveChatLink]);
        }
    }
}
