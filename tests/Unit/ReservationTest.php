<?php

namespace Tests\Unit;

use App\Book;
use App\Order;
use Tests\TestCase;
use App\Reservation;
use App\InventoryItem;
use App\Facades\InventoryCode;
use App\Billing\FakePaymentGateway;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ReservationTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    function amount_returns_the_sum_of_the_prices_of_the_books()
    {
        $books = collect([
            (object) ['price' => 1000],
            (object) ['price' => 2000],
            (object) ['price' => 500],
            (object) ['price' => 1500],
        ]);

        $reservation = new Reservation($books, 'john@example.com');

        $this->assertEquals(5000, $reservation->amount());
    }

    /** @test */
    function email_returns_the_email_for_the_reservation()
    {
        $reservation = new Reservation(collect([]), 'john@example.com');

        $this->assertEquals('john@example.com', $reservation->email());
    }

    /** @test */
    function a_reservation_can_be_completed()
    {
        $paymentGateway = new FakePaymentGateway;

        $bookA = factory(Book::class)->create(['price' => 1000]);
        $bookB = factory(Book::class)->create(['price' => 2000]);

        $items = [
            factory(InventoryItem::class)->create(['book_id' => $bookA->id]),
            factory(InventoryItem::class)->create(['book_id' => $bookB->id])
        ];

        $reservation = new Reservation(collect($items), 'john@example.com');

        $order = $reservation->complete($paymentGateway, $paymentGateway->validTestToken());

        $this->assertEquals(3000, $order->amount);
        $this->assertEquals('john@example.com', $order->email);
        $this->assertEquals(2, $order->inventoryItems()->count());
    }

    /** @test */
    function items_can_be_claimed_for_an_order()
    {
        InventoryCode::shouldReceive('generateFor')
            ->once()
            ->andReturn('ITEMCODE');
        $order = factory(Order::class)->create();

        $item = factory(InventoryItem::class)->create();

        $item->claimFor($order);

        $this->assertNotNull($order->fresh()->inventoryItems);
        $this->assertEquals('ITEMCODE', $item->fresh()->code);
    }
}
