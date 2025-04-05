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
        'blue_coins',
        'red_coins',
        'total_amount',
        'payment_id',
        'status',
    ];

    protected $casts = [
        'total_amount' => 'float',
        'blue_coins' => 'integer',
        'red_coins' => 'integer',
    ];

    /**
     * Get the user that owns the coin order.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Calculate the total amount for the order.
     */
    public static function calculateTotalAmount(int $blueCoins, int $redCoins): float
    {
        $blueCoinPrice = config('coins.prices.blue_coin');
        $redCoinPrice = config('coins.prices.red_coin');
        $paymentFee = config('coins.prices.payment_fee');

        return ($blueCoins * $blueCoinPrice) + ($redCoins * $redCoinPrice) + $paymentFee;
    }
}
