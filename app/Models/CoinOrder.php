<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CoinOrder extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'name',
        'email',
        'silver_coins',
        'gold_coins',
        'total_amount',
        'payment_id',
        'status',
        'pickup_moment_id',
    ];

    protected $casts = [
        'total_amount' => 'float',
        'silver_coins' => 'integer',
        'gold_coins' => 'integer',
    ];

    /**
     * Get the user that owns the coin order.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the pickup moment for this coin order.
     */
    public function pickupMoment(): BelongsTo
    {
        return $this->belongsTo(PickupMoment::class);
    }

    /**
     * Calculate the total amount for the order.
     */
    public static function calculateTotalAmount(int $silverCoins, int $goldCoins): float
    {
        $silverCoinPrice = config('coins.prices.silver_coin');
        $goldCoinPrice = config('coins.prices.gold_coin');
        $paymentFee = config('coins.prices.payment_fee');

        return ($silverCoins * $silverCoinPrice) + ($goldCoins * $goldCoinPrice) + $paymentFee;
    }
}
