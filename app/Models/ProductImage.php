<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductImage extends Model
{
    use HasFactory;

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'image_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'product_id',
        'image_path',
        'is_primary',
        'sort_order',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_primary' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * The attributes that should be appended to arrays.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'image_url',
        'thumbnail_url',
    ];

    /**
     * Get the product that owns the image.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }

    /**
     * Scope a query to only include primary images.
     */
    public function scopePrimary($query)
    {
        return $query->where('is_primary', true);
    }

    /**
     * Scope a query to order images by sort_order.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order', 'asc');
    }

    /**
     * Get the full image URL.
     */
    public function getImageUrlAttribute(): string
    {
        if ($this->image_path) {
            return asset('storage/' . $this->image_path);
        }
        
        return asset('images/default-product-thumb.svg');
    }

    /**
     * Get the thumbnail image URL.
     */
    public function getThumbnailUrlAttribute(): string
    {
        if ($this->image_path) {
            // Assuming thumbnails are stored in a 'thumbnails' subdirectory
            $thumbnailPath = str_replace('products/', 'products/thumbnails/', $this->image_path);
            return asset('storage/' . $thumbnailPath);
        }
        
        return asset('images/products/default-product-thumb.svg');
    }

    /**
     * Get the image file name.
     */
    public function getFileNameAttribute(): ?string
    {
        if ($this->image_path) {
            return basename($this->image_path);
        }
        
        return null;
    }

    /**
     * Get the image file extension.
     */
    public function getFileExtensionAttribute(): ?string
    {
        if ($this->image_path) {
            return pathinfo($this->image_path, PATHINFO_EXTENSION);
        }
        
        return null;
    }

    /**
     * Set this image as primary and unset others.
     */
    public function setPrimary(): void
    {
        // Unset all other primary images for this product
        static::where('product_id', $this->product_id)
              ->where('image_id', '!=', $this->image_id)
              ->update(['is_primary' => false]);
        
        // Set this image as primary
        $this->update(['is_primary' => true]);
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // When creating a new image, if it's set as primary,
        // make sure no other images for the same product are primary
        static::creating(function ($image) {
            if ($image->is_primary) {
                static::where('product_id', $image->product_id)
                      ->update(['is_primary' => false]);
            }
        });

        // When updating an image to primary,
        // make sure no other images for the same product are primary
        static::updating(function ($image) {
            if ($image->is_primary && $image->isDirty('is_primary')) {
                static::where('product_id', $image->product_id)
                      ->where('image_id', '!=', $image->image_id)
                      ->update(['is_primary' => false]);
            }
        });
    }
}