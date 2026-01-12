<?php

namespace App\Http\Controllers;

use App\Http\Requests\CoinOrderRequest;
use App\Models\CoinOrder;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;
use Mollie\Laravel\Facades\Mollie;
use Barryvdh\DomPDF\Facade\Pdf;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class CoinOrderController extends Controller
{
    use AuthorizesRequests;

    /**
     * Show the coin order form.
     */
    public function create(): Response
    {
        $user = Auth::user();

        // Calculate available pickup moments based on Wednesday deadline
        // A pickup moment is available until (and including) the Wednesday before it
        // After Wednesday, it's closed and the next pickup moment should be selected
        $today = now()->startOfDay();

        $pickupMoments = \App\Models\PickupMoment::where('active', true)
            ->orderBy('date')
            ->get()
            ->filter(function ($moment) use ($today) {
                // Calculate the Wednesday before the pickup moment
                $pickupDate = \Carbon\Carbon::parse($moment->date)->startOfDay();
                $wednesdayBefore = $pickupDate->copy()->previous(\Carbon\Carbon::WEDNESDAY);

                // The pickup moment is available if today is on or before the Wednesday before it
                return $today->lte($wednesdayBefore);
            })
            ->values()
            ->map(function ($moment) {
                return [
                    'id' => $moment->id,
                    'date' => $moment->date->toDateString(),
                ];
            });

        $prices = config('coins.prices');

        return Inertia::render('CoinOrders/Create', [
            'user' => [
                'name' => $user->name,
                'email' => $user->email,
            ],
            'prices' => [
                'silver_coin' => (float) $prices['silver_coin'],
                'gold_coin' => (float) $prices['gold_coin'],
                'payment_fee' => (float) $prices['payment_fee'],
            ],
            'pickupMoments' => $pickupMoments,
        ]);
    }

    /**
     * Store a new coin order and redirect to Mollie payment.
     */
    public function store(CoinOrderRequest $request): RedirectResponse|SymfonyResponse
    {
        $user = Auth::user();
        $data = $request->validated();

        // Check if the selected pickup moment meets the Wednesday deadline
        // A pickup moment is available until (and including) the Wednesday before it
        // After Wednesday, it's closed and the next pickup moment should be selected
        $selectedPickupMoment = \App\Models\PickupMoment::find($data['pickup_moment_id']);
        $today = now()->startOfDay();
        $pickupMomentId = $data['pickup_moment_id'];

        if ($selectedPickupMoment) {
            $pickupDate = \Carbon\Carbon::parse($selectedPickupMoment->date)->startOfDay();
            $wednesdayBefore = $pickupDate->copy()->previous(\Carbon\Carbon::WEDNESDAY);

            // If today is after the Wednesday before the pickup moment, find the next available one
            if ($today->gt($wednesdayBefore)) {
                $nextPickupMoment = \App\Models\PickupMoment::where('active', true)
                    ->orderBy('date')
                    ->get()
                    ->filter(function ($moment) use ($today) {
                        $momentDate = \Carbon\Carbon::parse($moment->date)->startOfDay();
                        $momentWednesdayBefore = $momentDate->copy()->previous(\Carbon\Carbon::WEDNESDAY);
                        return $today->lte($momentWednesdayBefore);
                    })
                    ->first();

                if ($nextPickupMoment) {
                    $pickupMomentId = $nextPickupMoment->id;
                }
            }
        }

        // Calculate total amount
        $totalAmount = CoinOrder::calculateTotalAmount(
            $data['silver_coins'],
            $data['gold_coins']
        );

        // Create the coin order
        $coinOrder = CoinOrder::create([
            'user_id' => $user->id,
            'name' => $data['name'],
            'email' => $data['email'],
            'silver_coins' => $data['silver_coins'],
            'gold_coins' => $data['gold_coins'],
            'total_amount' => $totalAmount,
            'status' => 'pending',
            'pickup_moment_id' => $pickupMomentId,
        ]);

        // Check if Mollie API key is configured
        $mollieKey = env('MOLLIE_KEY');
        if (empty($mollieKey)) {
            Log::error('Mollie API key is not configured. Please set MOLLIE_KEY in your .env file.');

            $coinOrder->update([
                'status' => 'failed',
            ]);

            return redirect()
                ->route('coin-orders.create')
                ->withErrors(['error' => 'De betalingsservice is momenteel niet beschikbaar. Neem contact op met de beheerder.']);
        }

        try {
            // Create Mollie payment
            $payment = Mollie::api()->payments->create([
                'amount' => [
                    'currency' => 'EUR',
                    'value' => number_format($totalAmount, 2, '.', ''),
                ],
                'description' => "Collectemunten HHG Rijssen #{$coinOrder->id}",
                'redirectUrl' => route('coin-orders.success', $coinOrder),
                'webhookUrl' => route('coin-orders.webhook'),
                'metadata' => [
                    'order_id' => $coinOrder->id,
                ],
            ]);

            // Update the coin order with the payment ID
            $coinOrder->update([
                'payment_id' => $payment->id,
            ]);

            // Redirect to Mollie payment page (external redirect, bypass Inertia)
            if (request()->header('X-Inertia')) {
                return Inertia::location($payment->getCheckoutUrl());
            }

            return redirect($payment->getCheckoutUrl());
        } catch (\Mollie\Api\Exceptions\ApiException $e) {
            Log::error('Mollie API error: ' . $e->getMessage(), [
                'userId' => $user->id,
                'orderId' => $coinOrder->id,
                'exception' => $e,
            ]);

            $coinOrder->update([
                'status' => 'failed',
            ]);

            return redirect()
                ->route('coin-orders.create')
                ->withErrors(['error' => 'Er is een fout opgetreden bij het aanmaken van de betaling. Probeer het later opnieuw of neem contact op met de beheerder.']);
        } catch (\Exception $e) {
            Log::error('Unexpected error creating Mollie payment: ' . $e->getMessage(), [
                'userId' => $user->id,
                'orderId' => $coinOrder->id,
                'exception' => $e,
            ]);

            $coinOrder->update([
                'status' => 'failed',
            ]);

            return redirect()
                ->route('coin-orders.create')
                ->withErrors(['error' => 'Er is een onverwachte fout opgetreden. Probeer het later opnieuw.']);
        }
    }

    /**
     * Handle successful payment.
     */
    public function success(CoinOrder $coinOrder): Response
    {
        $user = Auth::user();
        if ($user->id !== $coinOrder->user_id) {
            abort(403, 'Unauthorized action.');
        }

        $this->authorize('view', $coinOrder);

        $coinOrder->load('pickupMoment');

        return Inertia::render('CoinOrders/Success', [
            'order' => $coinOrder,
        ]);
    }

    /**
     * Handle Mollie webhook.
     */
    public function webhook()
    {
        try {
            $paymentId = request()->input('id');

            if (empty($paymentId)) {
                Log::error('Mollie webhook called without payment ID');
                return response()->json(['error' => 'Payment ID is required'], 400);
            }

            $payment = Mollie::api()->payments->get($paymentId);
            $orderId = $payment->metadata->order_id ?? null;

            if (!$orderId) {
                Log::error('Mollie webhook: Payment metadata missing order_id', ['paymentId' => $paymentId]);
                return response()->json(['error' => 'Order ID not found in payment metadata'], 400);
            }

            $coinOrder = CoinOrder::findOrFail($orderId);

            if ($payment->isPaid()) {
                $coinOrder->update([
                    'status' => 'paid',
                ]);
            } elseif ($payment->isFailed()) {
                $coinOrder->update([
                    'status' => 'failed',
                ]);
            }

            return response()->json(['status' => 'ok']);
        } catch (\Mollie\Api\Exceptions\ApiException $e) {
            Log::error('Mollie API error in webhook: ' . $e->getMessage(), [
                'exception' => $e,
            ]);
            return response()->json(['error' => 'Mollie API error'], 500);
        } catch (\Exception $e) {
            Log::error('Unexpected error in Mollie webhook: ' . $e->getMessage(), [
                'exception' => $e,
            ]);
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }

    /**
     * Display a listing of the user's coin orders.
     */
    public function index(): Response
    {
        $this->authorize('viewAny', CoinOrder::class);

        $user = Auth::user();
        $orders = $user->coinOrders()
            ->with('pickupMoment')
            ->latest()
            ->get();

        $prices = config('coins.prices');

        return Inertia::render('CoinOrders/Index', [
            'orders' => $orders,
            'prices' => [
                'silver_coin' => (float) $prices['silver_coin'],
                'gold_coin' => (float) $prices['gold_coin'],
                'payment_fee' => (float) $prices['payment_fee'],
            ],
        ]);
    }

    /**
     * Download the coin order as a PDF.
     */
    public function download(CoinOrder $coinOrder)
    {
        $user = Auth::user();
        if ($user->id !== $coinOrder->user_id) {
            abort(403, 'Unauthorized action.');
        }

        $this->authorize('view', $coinOrder);

        // Load the user relationship to access address information
        $coinOrder->load(['user', 'pickupMoment']);

        $pdf = PDF::loadView('pdf.coin-order', [
            'order' => $coinOrder,
            'user' => $coinOrder->user,
        ]);

        // Save the PDF to a temporary file
        $tempFile = tempnam(sys_get_temp_dir(), 'pdf_');
        file_put_contents($tempFile, $pdf->output());

        // Return the file as a download
        return response()->download($tempFile, "bestelling-{$coinOrder->id}.pdf", [
            'Content-Type' => 'application/pdf',
        ])->deleteFileAfterSend(true);
    }
}
