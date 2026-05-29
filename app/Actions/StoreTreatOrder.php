<?php

namespace App\Actions;

use App\Models\TreatOrder;

class StoreTreatOrder
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function execute(array $data): TreatOrder
    {
        $snoeprollenQuantity = (int) $data['snoeprollen_quantity'];
        $stroopwafelsQuantity = (int) $data['stroopwafels_quantity'];

        return TreatOrder::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'snoeprollen_quantity' => $snoeprollenQuantity,
            'stroopwafels_quantity' => $stroopwafelsQuantity,
            'total_amount' => TreatOrder::calculateTotalAmount($snoeprollenQuantity, $stroopwafelsQuantity),
            'status' => 'pending',
            'remarks' => $data['remarks'] ?? null,
        ]);
    }
}
