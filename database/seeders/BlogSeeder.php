<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Blog;
use App\Models\Category;
use Illuminate\Support\Str;

class BlogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
          // Lấy danh sách các category có type = 2 (blog categories)
          $blogCategoryIds = Category::where('type', 2)->pluck('id')->toArray();

          // Nếu chưa có category blog nào thì bỏ qua
          if (empty($blogCategoryIds)) {
              $this->command->warn('Chưa có category nào với type = 2, bỏ qua BlogSeeder.');
              return;
          }
  
          $blogs = [
              [
                  'title' => 'Làm thế nào để học Laravel hiệu quả?',
                  'slug' => Str::slug('Làm thế nào để học Laravel hiệu quả?'),
                  'content' => 'Nội dung chi tiết về cách học Laravel nhanh và dễ hiểu...',
                  'image' => 'blog1.jpg',
                  'author_id' => 1,
                  'category_id' => $blogCategoryIds[array_rand($blogCategoryIds)],
                  'status' => 'active',
              ],
              [
                  'title' => 'Top 10 tips để trở thành Backend Developer',
                  'slug' => Str::slug('Top 10 tips để trở thành Backend Developer'),
                  'content' => 'Bạn cần học gì để trở thành lập trình viên backend?',
                  'image' => 'blog2.jpg',
                  'author_id' => 2,
                  'category_id' => $blogCategoryIds[array_rand($blogCategoryIds)],
                  'status' => 'active',
              ],
              [
                  'title' => 'Laravel 12 có gì mới?',
                  'slug' => Str::slug('Laravel 12 có gì mới?'),
                  'content' => 'Khám phá các tính năng nổi bật của Laravel phiên bản mới nhất...',
                  'image' => 'blog3.jpg',
                  'author_id' => rand(1, 2),
                  'category_id' => $blogCategoryIds[array_rand($blogCategoryIds)],
                  'status' => 'inactive',
              ],
          ];
  
          foreach ($blogs as $blog) {
              Blog::create($blog);
          }
    }
}
