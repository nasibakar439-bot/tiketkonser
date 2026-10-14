<?php

namespace Tests\Feature;

use App\Models\events;
use App\Models\orderDetails;
use App\Models\orders;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerOrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_page_displays_available_events(): void
    {
        $event = events::create([
            'name' => 'Konser Indie',
            'category' => 'Musik',
            'price' => 75000,
            'description' => 'Konser musik independen.',
        ]);

        $this->get(route('customer.index'))
            ->assertOk()
            ->assertSee($event->name)
            ->assertSee('Jumlah Tiket')
            ->assertSee('Semua Event');
    }

    public function test_customer_can_order_event_tickets(): void
    {
        $event = events::create([
            'name' => 'Festival Musik',
            'category' => 'Festival',
            'price' => 50000,
            'description' => 'Festival musik tahunan.',
        ]);

        $this->from(route('customer.index'))
            ->post(route('customer.checkout'), [
                'customer_name' => 'Budi',
                'customer_email' => 'budi@example.com',
                'items' => [$event->id => 2],
            ])
            ->assertRedirect(route('customer.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('orders', [
            'customer_name' => 'Budi',
            'customer_email' => 'budi@example.com',
            'total_price' => 100000,
            'status' => 'Pending',
        ]);

        $this->assertDatabaseHas('order_details', [
            'event_id' => $event->id,
            'quantity' => 2,
            'subtotal' => 100000,
        ]);
    }

    public function test_admin_dashboard_displays_ticket_order_details(): void
    {
        $event = events::create([
            'name' => 'Konser Senja',
            'category' => 'Musik',
            'price' => 60000,
            'description' => 'Konser musik sore.',
        ]);
        $order = orders::create([
            'customer_name' => 'Sari',
            'customer_email' => 'sari@example.com',
            'total_price' => 120000,
            'status' => 'Pending',
        ]);
        orderDetails::create([
            'order_id' => $order->id,
            'event_id' => $event->id,
            'quantity' => 2,
            'subtotal' => 120000,
        ]);

        $this->actingAs(User::factory()->create())
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('sari@example.com')
            ->assertSee('Konser Senja')
            ->assertSee('x2 tiket')
            ->assertDontSee('Meja');
    }
}
