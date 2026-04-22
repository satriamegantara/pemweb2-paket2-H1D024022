<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SoftDeletes;

    public const STATUS_MENUNGGU_PEMBAYARAN = 'menunggu_pembayaran';
    public const STATUS_DIBAYAR = 'dibayar';
    public const STATUS_DIPROSES = 'diproses';
    public const STATUS_DIKIRIM = 'dikirim';
    public const STATUS_SELESAI = 'selesai';
    public const STATUS_DIBATALKAN = 'dibatalkan';

    protected $fillable = [
        'order_code',
        'buyer_id',
        'total_amount',
        'shipping_address',
        'payment_proof',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'total_amount' => 'decimal:2',
        ];
    }

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * @return array<string, array<int, string>>
     */
    public static function allowedTransitions(): array
    {
        return [
            self::STATUS_MENUNGGU_PEMBAYARAN => [self::STATUS_DIBAYAR, self::STATUS_DIBATALKAN],
            self::STATUS_DIBAYAR => [self::STATUS_DIPROSES, self::STATUS_DIBATALKAN],
            self::STATUS_DIPROSES => [self::STATUS_DIKIRIM],
            self::STATUS_DIKIRIM => [self::STATUS_SELESAI],
            self::STATUS_SELESAI => [],
            self::STATUS_DIBATALKAN => [],
        ];
    }

    public function canTransitionTo(string $next): bool
    {
        return in_array($next, self::allowedTransitions()[$this->status] ?? [], true);
    }
}
