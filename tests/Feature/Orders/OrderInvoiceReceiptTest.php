<?php

namespace Tests\Feature\Orders;

use App\Domains\Billing\Services\InvoiceService;
use App\Domains\Billing\Services\ReceiptService;
use App\Domains\Orders\Models\Order;
use App\Domains\Users\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class OrderInvoiceReceiptTest extends TestCase
{
    use RefreshDatabase;

    private function paidOrder(User $user): Order
    {
        return Order::create([
            'order_number' => 'TEST-' . uniqid(),
            'user_id' => $user->id,
            'total_amount' => 20,
            'discount_amount' => 0,
            'final_amount' => 20,
            'currency' => 'USD',
            'status' => 'completed',
            'payment_status' => 'paid',
            'paid_at' => now(),
        ]);
    }

    public function test_order_list_exposes_null_invoice_and_receipt_ids_when_none_issued(): void
    {
        $user = User::factory()->create();
        $this->paidOrder($user);

        Sanctum::actingAs($user);

        $this->getJson('/api/v1/orders')
            ->assertOk()
            ->assertJsonPath('data.data.0.invoice_id', null)
            ->assertJsonPath('data.data.0.receipt_id', null);
    }

    public function test_order_show_exposes_invoice_and_receipt_ids_once_issued(): void
    {
        $user = User::factory()->create();
        $order = $this->paidOrder($user);

        app(ReceiptService::class)->issue($order->fresh());
        app(InvoiceService::class)->issue($order->fresh());

        Sanctum::actingAs($user);

        $response = $this->getJson("/api/v1/orders/{$order->id}")
            ->assertOk();

        $this->assertNotNull($response->json('data.invoice_id'));
        $this->assertNotNull($response->json('data.invoice_number'));
        $this->assertNotNull($response->json('data.receipt_id'));
        $this->assertNotNull($response->json('data.receipt_number'));
    }
}
