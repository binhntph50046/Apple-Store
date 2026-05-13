<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HasSlug;

class Product extends Model
{
    use SoftDeletes, HasSlug;

    protected $table = 'products';

    protected $fillable = [
        'name', 'slug', 'description', 'content', 'category_id',
        'warranty_months', 'is_featured', 'status', 
        'views', 'total_sold'
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'warranty_months' => 'integer'
    ];

    // Thêm vào appends để accessor tự động được gọi
    protected $appends = ['default_variant_image'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function specifications()
    {
        return $this->hasMany(ProductSpecification::class);
    }

    public function reviews()
    {
        return $this->hasMany(ProductReview::class);
    }

    public function activities()
    {
        return $this->hasMany(ProductActivity::class)->latest();
    }

    public function defaultVariant()
    {
        return $this->hasOne(ProductVariant::class)->where('is_default', 1);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Accessor để lấy ảnh đầu tiên của default variant
     */
    public function getDefaultVariantImageAttribute()
    {
        // Ưu tiên lấy từ default variant
        if ($this->defaultVariant) {
            $images = $this->parseImages($this->defaultVariant->images);
            if (!empty($images)) {
                return $images[0];
            }
        }

        // Fallback: lấy từ variant đầu tiên có ảnh
        if ($this->variants && $this->variants->isNotEmpty()) {
            foreach ($this->variants as $variant) {
                $images = $this->parseImages($variant->images);
                if (!empty($images)) {
                    return $images[0];
                }
            }
        }

        return null;
    }

    /**
     * Helper method để parse images từ JSON hoặc array
     */
    private function parseImages($images)
    {
        if (empty($images)) {
            return [];
        }

        // Nếu là JSON string, decode nó
        if (is_string($images)) {
            $decoded = json_decode($images, true);
            return is_array($decoded) ? $decoded : [];
        }

        // Nếu đã là array
        if (is_array($images)) {
            return $images;
        }

        return [];
    }

    /**
     * Alias accessor cho backward compatibility
     */
    public function getVariantImageAttribute()
    {
        return $this->default_variant_image;
    }

    /**
     * Cập nhật total_sold một cách an toàn, tránh giá trị âm
     */
    public function safeUpdateTotalSold($quantity, $operation = 'increment')
    {
        $currentTotalSold = $this->total_sold ?? 0;
        
        if ($operation === 'increment') {
            $newTotalSold = $currentTotalSold + $quantity;
        } else {
            $newTotalSold = max(0, $currentTotalSold - $quantity);
        }
        
        $this->update(['total_sold' => $newTotalSold]);
        return $this;
    }

    /**
     * Tăng total_sold một cách an toàn
     */
    public function safeIncrementTotalSold($quantity = 1)
    {
        return $this->safeUpdateTotalSold($quantity, 'increment');
    }

    /**
     * Giảm total_sold một cách an toàn
     */
    public function safeDecrementTotalSold($quantity = 1)
    {
        return $this->safeUpdateTotalSold($quantity, 'decrement');
    }
}