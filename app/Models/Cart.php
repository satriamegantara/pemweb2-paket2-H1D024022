<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'guest_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForGuest(Builder $query, string $guestId): Builder
    {
        return $query->where('guest_id', $guestId)->whereNull('user_id');
    }

    public function addItem(int $productId, int $qty = 1): CartItem
    {
        $existing = $this->items()->where('product_id', $productId)->first();

        if ($existing) {
            $newQty = min($existing->qty + $qty, Product::find($productId)?->stock ?? $existing->qty);
            $existing->update(['qty' => $newQty]);
            return $existing;
        }

        return $this->items()->create([
            'product_id' => $productId,
            'qty' => min($qty, Product::find($productId)?->stock ?? $qty),
        ]);
    }

    public function updateItem(int $productId, int $qty): CartItem
    {
        $item = $this->items()->where('product_id', $productId)->firstOrFail();
        $item->update(['qty' => min($qty, Product::find($productId)?->stock ?? $qty)]);
        return $item;
    }

    public function removeItem(int $productId): void
    {
        $this->items()->where('product_id', $productId)->delete();
    }

    public function clear(): void
    {
        $this->items()->delete();
    }

    public function mergeFromGuest(string $guestId): void
    {
        $guestCart = Cart::forGuest($guestId)->first();

        if (!$guestCart) {
            return;
        }

        foreach ($guestCart->items as $item) {
            $existing = $this->items()->where('product_id', $item->product_id)->first();

            if ($existing) {
                $newQty = min($existing->qty + $item->qty, Product::find($item->product_id)?->stock ?? $existing->qty);
                $existing->update(['qty' => $newQty]);
            } else {
                $this->items()->create([
                    'product_id' => $item->product_id,
                    'qty' => $item->qty,
                ]);
            }
        }

        $guestCart->items()->delete();
        $guestCart->delete();
    }
}
