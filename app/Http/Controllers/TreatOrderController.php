<?php

namespace App\Http\Controllers;

use App\Actions\SendTreatOrderNotifications;
use App\Actions\StoreTreatOrder;
use App\Http\Requests\TreatOrderRequest;
use App\Models\Page;
use App\Models\TreatOrder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;
use Mollie\Laravel\Facades\Mollie;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class TreatOrderController extends Controller
{
    public function closed(): Response
    {
        return Inertia::render('TreatOrders/Closed', [
            'pages' => $this->getPages(),
            'orderDeadline' => TreatOrder::orderDeadlineFormatted(),
            'pickupDate' => TreatOrder::pickupDateFormatted(),
        ]);
    }

    public function create(): Response
    {
        $prices = config('treats.prices');

        return Inertia::render('TreatOrders/Create', [
            'pages' => $this->getPages(),
            'prices' => [
                'snoeprollen' => (float) $prices['snoeprollen'],
                'stroopwafels' => (float) $prices['stroopwafels'],
                'payment_fee' => (float) $prices['payment_fee'],
            ],
            'units' => config('treats.units'),
        ]);
    }

    public function store(TreatOrderRequest $request, StoreTreatOrder $storeTreatOrder): RedirectResponse|SymfonyResponse
    {
        $data = $request->validated();
        unset($data['website']);

        $order = $storeTreatOrder->execute($data);

        if (! filled(config('services.mollie.key'))) {
            Log::error('Mollie API key is not configured. Please set MOLLIE_KEY in your .env file.');

            $order->update(['status' => 'failed']);

            return redirect()
                ->route('treat-orders.create')
                ->withErrors(['error' => 'De betalingsservice is momenteel niet beschikbaar. Neem contact op met de beheerder.']);
        }

        try {
            $payment = Mollie::api()->payments->create([
                'amount' => [
                    'currency' => 'EUR',
                    'value' => number_format($order->total_amount, 2, '.', ''),
                ],
                'description' => "Snoep en stroopwafelactie HHG Rijssen #{$order->id}",
                'redirectUrl' => route('treat-orders.success', $order),
                'webhookUrl' => route('treat-orders.webhook'),
                'metadata' => [
                    'order_id' => $order->id,
                    'order_type' => 'treat',
                ],
            ]);

            $order->update([
                'payment_id' => $payment->id,
            ]);

            if ($request->header('X-Inertia')) {
                return Inertia::location($payment->getCheckoutUrl());
            }

            return redirect($payment->getCheckoutUrl());
        } catch (\Mollie\Api\Exceptions\ApiException $e) {
            Log::error('Mollie API error: '.$e->getMessage(), [
                'orderId' => $order->id,
                'exception' => $e,
            ]);

            $order->update(['status' => 'failed']);

            return redirect()
                ->route('treat-orders.create')
                ->withErrors(['error' => 'Er is een fout opgetreden bij het aanmaken van de betaling. Probeer het later opnieuw of neem contact op met de beheerder.']);
        } catch (\Exception $e) {
            Log::error('Unexpected error creating Mollie payment: '.$e->getMessage(), [
                'orderId' => $order->id,
                'exception' => $e,
            ]);

            $order->update(['status' => 'failed']);

            return redirect()
                ->route('treat-orders.create')
                ->withErrors(['error' => 'Er is een onverwachte fout opgetreden. Probeer het later opnieuw.']);
        }
    }

    public function success(TreatOrder $treatOrder): Response
    {
        return Inertia::render('TreatOrders/Success', [
            'pages' => $this->getPages(),
            'order' => $treatOrder,
        ]);
    }

    public function webhook()
    {
        try {
            $paymentId = request()->input('id');

            if (empty($paymentId)) {
                Log::error('Mollie treat order webhook called without payment ID');

                return response()->json(['error' => 'Payment ID is required'], 400);
            }

            $payment = Mollie::api()->payments->get($paymentId);
            $orderId = $payment->metadata->order_id ?? null;

            if (!$orderId || ($payment->metadata->order_type ?? null) !== 'treat') {
                Log::error('Mollie treat order webhook: invalid metadata', ['paymentId' => $paymentId]);

                return response()->json(['error' => 'Order ID not found in payment metadata'], 400);
            }

            $order = TreatOrder::findOrFail($orderId);

            if ($payment->isPaid()) {
                $wasAlreadyPaid = $order->status === 'paid';
                $order->update(['status' => 'paid']);

                if (! $wasAlreadyPaid) {
                    app(SendTreatOrderNotifications::class)->execute($order);
                }
            } elseif ($payment->isFailed() || $payment->isExpired() || $payment->isCanceled()) {
                $order->update(['status' => 'failed']);
            }

            return response()->json(['status' => 'ok']);
        } catch (\Mollie\Api\Exceptions\ApiException $e) {
            Log::error('Mollie API error in treat order webhook: '.$e->getMessage(), [
                'exception' => $e,
            ]);

            return response()->json(['error' => 'Mollie API error'], 500);
        } catch (\Exception $e) {
            Log::error('Unexpected error in treat order webhook: '.$e->getMessage(), [
                'exception' => $e,
            ]);

            return response()->json(['error' => 'Internal server error'], 500);
        }
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection<int, Page>
     */
    private function getPages()
    {
        return Page::select(['id', 'title', 'slug'])
            ->with(['children' => function ($query) {
                $query->where('exclude_from_navigation', false)
                    ->active()
                    ->orderBy('sort_order');
            }])
            ->active()
            ->whereNull('parent_id')
            ->where('exclude_from_navigation', false)
            ->where('requires_authentication', false)
            ->orderBy('sort_order')
            ->get();
    }
}
