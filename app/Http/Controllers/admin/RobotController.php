<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;

class RobotController
{
    public function index()
    {
        $robotsPath = public_path('robots.txt');
        $robotsData = [
            'user_agent' => '*',
            'allow' => [
                '/',
                '/shop',
                '/product/',
                '/blog',
                '/about',
                '/contact',
                '/faq',
                '/location',
                '/sitemap.xml'
            ],
            'disallow' => [
                '/admin/',
                '/login',
                '/register',
                '/password/',
                '/cart',
                '/checkout',
                '/profile/',
                '/order/',
                '/wishlist',
                '/compare',
                '/search',
                '/api/',
                '/uploads/',
                '/storage/',
                '/vendor/',
                '/node_modules/',
                '/.env',
                '/composer.json',
                '/composer.lock',
                '/package.json',
                '/package-lock.json',
                '/yarn.lock',
                '/.git/',
                '/.gitignore',
                '/README.md',
                '/artisan',
                '/bootstrap/',
                '/config/',
                '/database/',
                '/resources/',
                '/routes/',
                '/tests/',
                '/app/',
                '/public/uploads/avatar/',
                '/public/uploads/users/',
                '/public/uploads/returns/'
            ],
            'sitemap' => url('sitemap.xml')
        ];
        
        if (File::exists($robotsPath)) {
            $content = File::get($robotsPath);
            $lines = explode("\n", $content);
            
            $robotsData['allow'] = [];
            $robotsData['disallow'] = [];
            
            foreach ($lines as $line) {
                $line = trim($line);
                if (empty($line)) continue;
                
                if (strpos($line, 'User-agent:') === 0) {
                    $robotsData['user_agent'] = trim(substr($line, 11));
                }
                elseif (strpos($line, 'Allow:') === 0) {
                    $robotsData['allow'][] = trim(substr($line, 6));
                }
                elseif (strpos($line, 'Disallow:') === 0) {
                    $robotsData['disallow'][] = trim(substr($line, 9));
                }
                elseif (strpos($line, 'Sitemap:') === 0) {
                    $robotsData['sitemap'] = trim(substr($line, 8));
                }
            }
        }
        
        return view('admin.robots.index', compact('robotsData'));
    }

    protected function validatePath($path)
    {
        // Đường dẫn phải bắt đầu bằng /
        if (!str_starts_with($path, '/')) {
            return false;
        }

        // Path should not contain spaces
        if (str_contains($path, ' ')) {
            return false;
        }

        // Đường dẫn chỉ được chứa ký tự URL hợp lệ
        if (!preg_match('/^[\/\-_.a-zA-Z0-9*]+$/', $path)) {
            return false;
        }

        return true;
    }

    protected function createBackup()
    {
        $robotsPath = public_path('robots.txt');
        if (File::exists($robotsPath)) {
            $backupPath = public_path('robots.txt.backup-' . Carbon::now()->format('Y-m-d-His'));
            File::copy($robotsPath, $backupPath);
            return $backupPath;
        }
        return null;
    }

    public function update(Request $request)
    {
        $request->validate([
            'user_agent' => 'required|string|max:50',
            'allow' => 'nullable|string',
            'disallow' => 'nullable|string',
            'sitemap' => 'required|url'
        ]);

        // Process allow rules
        $allowRules = array_filter(
            array_map('trim', explode("\n", $request->allow ?? '')),
            function($rule) {
                return !empty($rule);
            }
        );

        // Process disallow rules
        $disallowRules = array_filter(
            array_map('trim', explode("\n", $request->disallow ?? '')),
            function($rule) {
                return !empty($rule);
            }
        );

        // Validate paths
        foreach ($allowRules as $rule) {
            if (!$this->validatePath($rule)) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['allow' => 'Đường dẫn không hợp lệ: ' . $rule]);
            }
        }

        foreach ($disallowRules as $rule) {
            if (!$this->validatePath($rule)) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['disallow' => 'Đường dẫn không hợp lệ: ' . $rule]);
            }
        }

        // Tạo backup trước khi thay đổi
        $backupPath = $this->createBackup();

        // Xây dựng nội dung robots.txt
        $content = "User-agent: " . $request->user_agent . "\n\n";

        // Thêm quy tắc Allow
        foreach ($allowRules as $rule) {
            $content .= "Allow: " . $rule . "\n";
        }

        // Thêm quy tắc Disallow
        foreach ($disallowRules as $rule) {
            $content .= "Disallow: " . $rule . "\n";
        }

        // Thêm Sitemap
        $content .= "\nSitemap: " . $request->sitemap . "\n";

        // Lưu vào robots.txt
        File::put(public_path('robots.txt'), $content);

        $message = 'Nội dung robots.txt đã được cập nhật thành công!';
        if ($backupPath) {
            $message .= ' Bản sao lưu được tạo tại: ' . basename($backupPath);
        }

        return redirect()->route('admin.robots.index')->with('success', $message);
    }
} 