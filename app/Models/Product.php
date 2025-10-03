<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'product_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'short_description',
        'price',
        'discount_price',
        'stock',
        'quantity',
        'sku',
        'category_id',
        'requires_prescription',
        'is_active',
        'unit',
        'specifications',
        'weight',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'price' => 'decimal:2',
        'discount_price' => 'decimal:2',
        'weight' => 'decimal:2',
        'requires_prescription' => 'boolean',
        'is_active' => 'boolean',
        'specifications' => 'array',
    ];

    /**
     * The attributes that should be appended to arrays.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'final_price',
        'discount_percentage',
        'primary_image_url',
        'is_available',
        'is_on_sale',
    ];

    /**
     * Get the category that owns the product.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id', 'category_id');
    }

    /**
     * Get all images for this product.
     */
    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class, 'product_id', 'product_id')
                    ->orderBy('sort_order', 'asc');
    }

    /**
     * Get the primary image for this product.
     */
    public function primaryImage()
    {
        return $this->hasOne(ProductImage::class, 'product_id', 'product_id')
                    ->where('is_primary', true);
    }

    /**
     * Scope a query to only include active products.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include available products.
     */
    public function scopeAvailable($query)
    {
        return $query->where('stock', 'available');
    }

    /**
     * Scope a query to only include products on sale.
     */
    public function scopeOnSale($query)
    {
        return $query->whereNotNull('discount_price')
                    ->where('discount_price', '>', 0);
    }

    /**
     * Scope a query to only include products that require prescription.
     */
    public function scopeRequiresPrescription($query)
    {
        return $query->where('requires_prescription', true);
    }

    /**
     * Scope a query to only include products that don't require prescription.
     */
    public function scopeNoRequiresPrescription($query)
    {
        return $query->where('requires_prescription', false);
    }

    /**
     * Scope a query to filter by category.
     */
    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    /**
     * Scope a query to search products by name or description.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', '%' . $search . '%')
              ->orWhere('description', 'like', '%' . $search . '%')
              ->orWhere('short_description', 'like', '%' . $search . '%');
        });
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Get the final price (considering discount).
     */
    public function getFinalPriceAttribute(): float
    {
        return $this->discount_price ?? $this->price;
    }

    /**
     * Get the discount percentage.
     */
    public function getDiscountPercentageAttribute(): ?int
    {
        if (!$this->discount_price || $this->discount_price >= $this->price) {
            return null;
        }

        return round((($this->price - $this->discount_price) / $this->price) * 100);
    }

    /**
     * Get the primary image URL.
     */
    public function getPrimaryImageUrlAttribute(): string
    {
        $primaryImage = $this->primaryImage;
        
        if ($primaryImage && $primaryImage->image_path) {
            return asset('storage/' . $primaryImage->image_path);
        }
        
        return asset('images/default-product-thumb.svg');
    }

    /**
     * Check if product is available.
     */
    public function getIsAvailableAttribute(): bool
    {
        return $this->is_active && $this->stock === 'available';
    }

    /**
     * Check if product is on sale.
     */
    public function getIsOnSaleAttribute(): bool
    {
        return $this->discount_price && $this->discount_price < $this->price;
    }

    /**
     * Alias for is_on_sale (for backward compatibility).
     */
    public function getIsOnDiscountAttribute(): bool
    {
        return $this->is_on_sale;
    }

    /**
     * Get formatted price.
     */
    public function getFormattedPriceAttribute(): string
    {
        return 'Rp ' . number_format($this->price, 0, ',', '.');
    }

    /**
     * Get formatted final price.
     */
    public function getFormattedFinalPriceAttribute(): string
    {
        return 'Rp ' . number_format($this->final_price, 0, ',', '.');
    }

    /**
     * Get formatted discount price.
     */
    public function getFormattedDiscountPriceAttribute(): ?string
    {
        if (!$this->discount_price) {
            return null;
        }
        
        return 'Rp ' . number_format($this->discount_price, 0, ',', '.');
    }

    /**
     * Get formatted savings amount.
     */
    public function getFormattedSavingsAttribute(): ?string
    {
        if (!$this->is_on_sale) {
            return null;
        }
        
        $savings = $this->price - $this->discount_price;
        return 'Rp ' . number_format($savings, 0, ',', '.');
    }

    /**
     * Get unit label.
     */
    public function getUnitLabelAttribute(): string
    {
        $units = [
            'pcs' => 'Pcs',
            'box' => 'Box',
            'botol' => 'Botol',
            'strip' => 'Strip',
            'tube' => 'Tube',
            'sachet' => 'Sachet',
        ];

        return $units[$this->unit] ?? ucfirst($this->unit);
    }

    /**
     * Get stock status label.
     */
    public function getStockStatusLabelAttribute(): string
    {
        return $this->stock === 'available' ? 'Tersedia' : 'Habis';
    }

    /**
     * Get prescription requirement label.
     */
    public function getPrescriptionLabelAttribute(): string
    {
        return $this->requires_prescription ? 'Perlu Resep Dokter' : 'Tanpa Resep';
    }
}