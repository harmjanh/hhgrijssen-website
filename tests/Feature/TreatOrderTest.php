<?php

namespace Tests\Feature;

use App\Actions\SendTreatOrderNotifications;
use App\Models\TreatOrder;
use App\Notifications\TreatOrderConfirmation;
use App\Notifications\TreatOrderReceived;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class TreatOrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_view_order_form(): void
    {
        $this->get(route('treat-orders.create'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('TreatOrders/Create')
                ->has('prices')
                ->has('units')
                ->where('prices.payment_fee', 0.35));
    }

    public function test_order_requires_at_least_one_product(): void
    {
        $this->post(route('treat-orders.store'), [
            'name' => 'Jan de Vries',
            'email' => 'jan@example.com',
            'phone' => '0612345678',
            'snoeprollen_quantity' => 0,
            'stroopwafels_quantity' => 0,
        ])->assertSessionHasErrors('snoeprollen_quantity');

        $this->assertDatabaseCount('treat_orders', 0);
    }

    public function test_total_amount_includes_payment_fee(): void
    {
        $this->assertSame(24.35, TreatOrder::calculateTotalAmount(2, 1));
        $this->assertSame(7.35, TreatOrder::calculateTotalAmount(1, 0));
    }

    public function test_order_page_is_accessible_before_deadline(): void
    {
        Carbon::setTestNow('2026-07-06 12:00:00');

        $this->get(route('treat-orders.create'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page->component('TreatOrders/Create'));

        Carbon::setTestNow();
    }

    public function test_order_page_is_closed_after_deadline(): void
    {
        Carbon::setTestNow('2026-07-07 00:00:01');

        $this->get(route('treat-orders.create'))
            ->assertRedirect(route('treat-orders.closed'));

        $this->get(route('treat-orders.closed'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page->component('TreatOrders/Closed'));

        Carbon::setTestNow();
    }

    public function test_orders_cannot_be_submitted_after_deadline(): void
    {
        Carbon::setTestNow('2026-07-07 10:00:00');

        $this->post(route('treat-orders.store'), [
            'name' => 'Jan de Vries',
            'email' => 'jan@example.com',
            'phone' => '0612345678',
            'snoeprollen_quantity' => 1,
            'stroopwafels_quantity' => 0,
        ])->assertRedirect(route('treat-orders.closed'));

        $this->assertDatabaseCount('treat_orders', 0);

        Carbon::setTestNow();
    }

    public function test_paid_order_sends_confirmation_emails(): void
    {
        Notification::fake();

        $order = TreatOrder::create([
            'name' => 'Jan de Vries',
            'email' => 'jan@example.com',
            'phone' => '0612345678',
            'snoeprollen_quantity' => 1,
            'stroopwafels_quantity' => 0,
            'total_amount' => 7.35,
            'status' => 'paid',
        ]);

        app(SendTreatOrderNotifications::class)->execute($order);

        Notification::assertSentOnDemand(
            TreatOrderConfirmation::class,
            fn ($notification, $channels, $notifiable) => $notifiable->routes['mail'] === 'jan@example.com'
        );

        Notification::assertSentOnDemand(
            TreatOrderReceived::class,
            fn ($notification, $channels, $notifiable) => $notifiable->routes['mail'] === config('treats.notification_email')
        );
    }
}
