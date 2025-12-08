<?php

namespace App\Http\Controllers;

use App\Http\Requests\CoinOrderRequest;
use App\Models\CoinOrder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;
use Mollie\Laravel\Facades\Mollie;
use Barryvdh\DomPDF\Facade\Pdf;

class CoinOrderController extends Controller
{
    /**
     * Show the coin order form.
     */
    public function create(): Response
    {
        $user = Auth::user();

        return Inertia::render('CoinOrders/Create', [
            'user' => [
                'name' => $user->name,
                'email' => $user->email,
            ],
            'prices' => config('coins.prices'),
        ]);
    }

    /**
     * Store a new coin order and redirect to Mollie payment.
     */
    public function store(CoinOrderRequest $request): RedirectResponse
    {
        $user = Auth::user();
        $data = $request->validated();

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
        ]);


        // Create Mollie payment
        $payment = Mollie::api()->payments->create([
            'amount' => [
                'currency' => 'EUR',
                'value' => number_format($totalAmount, 2, '.', ''),
            ],
            'description' => "Coin Order #{$coinOrder->id}",
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

        // Redirect to Mollie payment page
        return redirect($payment->getCheckoutUrl());
    }

    /**
     * Handle successful payment.
     */
    public function success(CoinOrder $coinOrder): Response
    {
        return Inertia::render('CoinOrders/Success', [
            'order' => $coinOrder,
        ]);
    }

    /**
     * Handle Mollie webhook.
     */
    public function webhook(): RedirectResponse
    {
        $paymentId = request()->input('id');
        $payment = Mollie::api()->payments->get($paymentId);
        $orderId = $payment->metadata->order_id;

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

        return redirect()->back();
    }

    /**
     * Display a listing of the user's coin orders.
     */
    public function index(): Response
    {
        $user = Auth::user();
        $orders = $user->coinOrders()
            ->latest()
            ->get();

        return Inertia::render('CoinOrders/Index', [
            'orders' => $orders,
            'prices' => config('coins.prices'),
        ]);
    }

    /**
     * Download the coin order as a PDF.
     */
    public function download(CoinOrder $coinOrder)
    {
        // Load the user relationship to access address information
        $coinOrder->load('user');

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
