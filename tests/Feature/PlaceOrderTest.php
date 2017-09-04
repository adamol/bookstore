<?php

namespace Tests\Feature;

use App\Book;
use Tests\TestCase;
use App\Billing\PaymentGateway;
use App\Billing\FakePaymentGateway;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PlaceOrderTest extends TestCase
{
    use DatabaseMigrations;

    public function setUp()
    {
        parent::setUp();

        $this->fakePaymentGateway = new FakePaymentGateway;
        $this->app->instance(PaymentGateway::class, $this->fakePaymentGateway);
    }

    /** @test */
    function a_customer_can_purchase_the_items_in_their_cart()
    {
        $bookA = factory(Book::class)->create(['price' => 1000])->addInventory(2);
        $bookB = factory(Book::class)->create(['price' => 1500])->addInventory(1);

        $fakeCart = [
            'cart.books' => [
                ['book_id' => $bookA->id, 'quantity' => 2],
                ['book_id' => $bookB->id, 'quantity' => 1],
            ]
        ];

        $this->withSession($fakeCart)->post('orders');

        $this->assertEquals([2500], $this->fakePaymentGateway->charges());
    }

    /** @test */
    function a_customer_cannot_purchase_more_books_than_exist_in_inventory()
    {
        $book = factory(Book::class)->create()->addInventory(1);

        $fakeCart = ['cart.books' => [['book_id' => $book->id, 'quantity' => 2]]];

        $this->withSession($fakeCart)->post('orders');

        $this->assertEquals([], $this->fakePaymentGateway->charges());
    }

    /** @test */
    function two_customers_cannot_purchase_the_same_inventory_item()
    {
        $book = factory(Book::class)->create()->addInventory(1);

        $this->fakePaymentGateway->beforeFirstCharge(function() {
            $fakeCart = ['cart.books' => [['book_id' => $book->id, 'quantity' => 1]]];

            $this->withSession($fakeCart)->post('orders');

            // should work
        });

        $fakeCart = ['cart.books' => [['book_id' => $book->id, 'quantity' => 1]]];

        $this->withSession($fakeCart)->post('orders');

        // should error out
    }
}
