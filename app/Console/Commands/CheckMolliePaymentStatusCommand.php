<?php

namespace App\Console\Commands;

use App\Actions\SendTreatOrderNotifications;
use App\Models\CoinOrder;
use App\Models\TreatOrder;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Mollie\Laravel\Facades\Mollie;

class CheckMolliePaymentStatusCommand extends Command
{
    /**
     * @var string
     */
    protected $signature = 'mollie:check-payment-status
                            {--all : Check all orders with payment_id, not just pending ones}
                            {--order-id= : Check a specific order by ID}
                            {--type=coin : Order type to check: coin or treat}';

    /**
     * @var string
     */
    protected $description = 'Check the payment status of orders at Mollie and update the database';

    public function handle(): int
    {
        $this->info('Checking Mollie payment statuses...');

        if (! filled(config('services.mollie.key'))) {
            $this->error('Mollie API key is not configured. Please set MOLLIE_KEY in your .env file.');

            return Command::FAILURE;
        }

        $type = $this->option('type');
        if (! in_array($type, ['coin', 'treat'], true)) {
            $this->error("Invalid type '{$type}'. Use 'coin' or 'treat'.");

            return Command::FAILURE;
        }

        $modelClass = $type === 'treat' ? TreatOrder::class : CoinOrder::class;

        $query = $modelClass::query()->whereNotNull('payment_id');

        if ($this->option('order-id')) {
            $query->where('id', $this->option('order-id'));
        } elseif (! $this->option('all')) {
            $query->where('status', 'pending');
        }

        /** @var Collection<int, Model> $orders */
        $orders = $query->get();

        if ($orders->isEmpty()) {
            $this->info('No orders found to check.');

            return Command::SUCCESS;
        }

        $this->info("Found {$orders->count()} {$type} order(s) to check.");

        $updated = 0;
        $errors = 0;

        foreach ($orders as $order) {
            try {
                $this->line("Checking order #{$order->id} (Payment ID: {$order->payment_id})...");

                $payment = Mollie::api()->payments->get($order->payment_id);
                $newStatus = $this->determineStatus($payment);

                if ($newStatus && $newStatus !== $order->status) {
                    $oldStatus = $order->status;
                    $order->update(['status' => $newStatus]);

                    if ($type === 'treat' && $newStatus === 'paid') {
                        app(SendTreatOrderNotifications::class)->execute($order);
                    }

                    $this->info("  ✓ Updated order #{$order->id} status from '{$oldStatus}' to '{$newStatus}'");
                    $updated++;
                } else {
                    $this->line("  - Order #{$order->id} status unchanged: {$order->status}");
                }
            } catch (\Mollie\Api\Exceptions\ApiException $e) {
                $this->error("  ✗ Error checking order #{$order->id}: {$e->getMessage()}");
                Log::error('Mollie API error in check payment status command', [
                    'orderId' => $order->id,
                    'paymentId' => $order->payment_id,
                    'type' => $type,
                    'error' => $e->getMessage(),
                ]);
                $errors++;
            } catch (\Exception $e) {
                $this->error("  ✗ Unexpected error checking order #{$order->id}: {$e->getMessage()}");
                Log::error('Unexpected error in check payment status command', [
                    'orderId' => $order->id,
                    'paymentId' => $order->payment_id,
                    'type' => $type,
                    'error' => $e->getMessage(),
                ]);
                $errors++;
            }
        }

        $this->newLine();
        $this->info('Summary:');
        $this->line("  - Checked: {$orders->count()} order(s)");
        $this->line("  - Updated: {$updated} order(s)");
        if ($errors > 0) {
            $this->line("  - Errors: {$errors} order(s)");
        }

        return Command::SUCCESS;
    }

    /**
     * @param  \Mollie\Api\Resources\Payment  $payment
     */
    protected function determineStatus($payment): ?string
    {
        if ($payment->isPaid()) {
            return 'paid';
        }

        if ($payment->isFailed() || $payment->isExpired() || $payment->isCanceled()) {
            return 'failed';
        }

        if ($payment->isPending()) {
            return null;
        }

        return 'pending';
    }
}
