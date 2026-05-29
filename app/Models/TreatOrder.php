<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TreatOrder extends Model
{
    use HasFactory;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'snoeprollen_quantity',
        'stroopwafels_quantity',
        'total_amount',
        'payment_id',
        'status',
        'remarks',
    ];

    protected $casts = [
        'total_amount' => 'float',
        'snoeprollen_quantity' => 'integer',
        'stroopwafels_quantity' => 'integer',
    ];

    public static function calculateTotalAmount(int $snoeprollenQuantity, int $stroopwafelsQuantity): float
    {
        $prices = config('treats.prices');

        return ($snoeprollenQuantity * $prices['snoeprollen'])
            + ($stroopwafelsQuantity * $prices['stroopwafels'])
            + $prices['payment_fee'];
    }

    public static function isOrderingOpen(): bool
    {
        $deadline = config('treats.order_deadline');

        if (empty($deadline)) {
            return true;
        }

        return now()->lte(Carbon::parse($deadline)->endOfDay());
    }

    public static function orderDeadlineFormatted(): string
    {
        return Carbon::parse(config('treats.order_deadline'))->locale('nl')->isoFormat('D MMMM YYYY');
    }

    public static function pickupDateFormatted(): string
    {
        return Carbon::parse(config('treats.pickup_date'))->locale('nl')->isoFormat('D MMMM YYYY');
    }

    /**
     * @return list<string>
     */
    public function orderLineDescriptions(): array
    {
        $lines = [];

        if ($this->snoeprollen_quantity > 0) {
            $lines[] = $this->snoeprollen_quantity.'× 10 snoeprollen (€'.number_format(
                $this->snoeprollen_quantity * config('treats.prices.snoeprollen'),
                2,
                ',',
                '.'
            ).')';
        }

        if ($this->stroopwafels_quantity > 0) {
            $lines[] = $this->stroopwafels_quantity.'× 3 pakjes Markus stroopwafel (€'.number_format(
                $this->stroopwafels_quantity * config('treats.prices.stroopwafels'),
                2,
                ',',
                '.'
            ).')';
        }

        return $lines;
    }
}
