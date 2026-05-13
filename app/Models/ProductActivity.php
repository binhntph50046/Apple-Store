<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductActivity extends Model
{
    use SoftDeletes;

    protected $table = 'product_activities';

    protected $fillable = [
        'product_id',
        'user_id',
        'action',
        'description',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent'
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getFormattedDescriptionAttribute()
    {
        $description = $this->description;
        
        if ($this->old_values && $this->new_values) {
            $changes = [];
            foreach ($this->new_values as $key => $newValue) {
                if (isset($this->old_values[$key]) && $this->old_values[$key] !== $newValue) {
                    $changes[] = "{$key}: {$this->old_values[$key]} → {$newValue}";
                }
            }
            if (!empty($changes)) {
                $description .= " (" . implode(', ', $changes) . ")";
            }
        }
        
        return $description;
    }
}
