<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductImage extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'product_id',
        'image_path',
        'is_primary',
    ];

    protected function casts(): array
    {
        return [
            'is_primary' => 'boolean',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function setAsPrimary(): void
    {
        if (!$this->product->primary_image_id) {
            $this->product->update(['primary_image_id' => $this->id]);
        }
        $this->product->images()->whereKeyNot($this->id)->update(['is_primary' => false]);
        $this->update(['is_primary' => true]);
    }

    public static function findFirstImageOrCreate(Product $product): ?ProductImage
    {
        return $product->images()->first();
    }
}
