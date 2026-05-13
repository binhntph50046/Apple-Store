<?php

namespace App\Events;

use App\Models\Product;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProductCreated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $product;

    public function __construct(Product $product)
    {
        $this->product = $product;
    }

    public function broadcastOn()
    {
        return new Channel('public.products');
    }

    public function broadcastAs()
    {
        return 'product.created';
    }

    public function broadcastWith()
    {
        $defaultVariant = $this->product->variants->first();
        $defaultImage = asset('uploads/default/default.jpg');
        
        if ($defaultVariant && $defaultVariant->images) {
            $images = $defaultVariant->images;
            if (is_array($images) && !empty($images[0])) {
                $defaultImage = asset($images[0]);
            } elseif (is_string($images)) {
                $decoded = json_decode($images, true);
                if (is_array($decoded) && !empty($decoded[0])) {
                    $defaultImage = asset($decoded[0]);
                }
            }
        }

        return [
            'id' => $this->product->id,
            'name' => $this->product->name,
            'slug' => $this->product->slug,
            'category_name' => $this->product->category->name ?? 'N/A',
            'image' => $defaultImage,
            'price' => $defaultVariant ? number_format($defaultVariant->selling_price) : '0',
            'discount_price' => $defaultVariant && $defaultVariant->discount_price ? number_format($defaultVariant->discount_price) : null,
            'rating' => $this->product->reviews->avg('rating') ?? 0,
            'views' => $this->product->views,
            'created_at' => $this->product->created_at->diffForHumans(),
            'url' => route('product.detail', $this->product->slug)
        ];
    }

    public function broadcastVia()
    {
        return ['pusher'];
    }
}
