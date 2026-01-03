<?php

namespace App\Console\Commands;

use App\Models\CoinOrder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Mollie\Laravel\Facades\Mollie;

class CheckMolliePaymentStatusCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mollie:check-payment-status
                            {--all : Check all orders with payment_id, not just pending ones}
                            {--order-id= : Check a specific order by ID}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check the payment status of coin orders at Mollie and update the database';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Checking Mollie payment statuses...');

        // Check if Mollie API key is configured
        $mollieKey = env('MOLLIE_KEY');
        if (empty($mollieKey)) {
            $this->error('Mollie API key is not configured. Please set MOLLIE_KEY in your .env file.');
            return Command::FAILURE;
        }

        // Build query
        $query = CoinOrder::whereNotNull('payment_id');

        // If specific order ID is provided
        if ($this->option('order-id')) {
            $query->where('id', $this->option('order-id'));
        } elseif (!$this->option('all')) {
            // By default, only check pending orders
            $query->where('status', 'pending');
        }

        $orders = $query->get();

        if ($orders->isEmpty()) {
            $this->info('No orders found to check.');
            return Command::SUCCESS;
        }

        $this->info("Found {$orders->count()} order(s) to check.");

        $updated = 0;
        $errors = 0;

        foreach ($orders as $order) {
            try {
                $this->line("Checking order #{$order->id} (Payment ID: {$order->payment_id})...");

                // Get payment status from Mollie
                $payment = Mollie::api()->payments->get($order->payment_id);

                // Determine new status based on Mollie payment status
                $newStatus = $this->determineStatus($payment);

                // Update if status has changed
                if ($newStatus && $newStatus !== $order->status) {
                    $order->update(['status' => $newStatus]);
                    $this->info("  ✓ Updated order #{$order->id} status from '{$order->status}' to '{$newStatus}'");
                    $updated++;
                } else {
                    $this->line("  - Order #{$order->id} status unchanged: {$order->status}");
                }
            } catch (\Mollie\Api\Exceptions\ApiException $e) {
                $this->error("  ✗ Error checking order #{$order->id}: {$e->getMessage()}");
                Log::error('Mollie API error in check payment status command', [
                    'orderId' => $order->id,
                    'paymentId' => $order->payment_id,
                    'error' => $e->getMessage(),
                ]);
                $errors++;
            } catch (\Exception $e) {
                $this->error("  ✗ Unexpected error checking order #{$order->id}: {$e->getMessage()}");
                Log::error('Unexpected error in check payment status command', [
                    'orderId' => $order->id,
                    'paymentId' => $order->payment_id,
                    'error' => $e->getMessage(),
                ]);
                $errors++;
            }
        }

        $this->newLine();
        $this->info("Summary:");
        $this->line("  - Checked: {$orders->count()} order(s)");
        $this->line("  - Updated: {$updated} order(s)");
        if ($errors > 0) {
            $this->line("  - Errors: {$errors} order(s)");
        }

        return Command::SUCCESS;
    }

    /**
     * Determine the status based on Mollie payment object.
     *
     * @param \Mollie\Api\Resources\Payment $payment
     * @return string|null
     */
    protected function determineStatus($payment): ?string
    {
        if ($payment->isPaid()) {
            return 'paid';
        }

        if ($payment->isFailed()) {
            return 'failed';
        }

        if ($payment->isExpired()) {
            return 'failed';
        }

        if ($payment->isCanceled()) {
            return 'failed';
        }

        // If payment is still pending, return null to keep current status
        if ($payment->isPending()) {
            return null;
        }

        // For any other status, keep as pending
        return 'pending';
    }
}
