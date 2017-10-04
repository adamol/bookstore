<?php

namespace Tests\Feature;

use Mail;
use App\Order;
use App\Book;
use Tests\TestCase;
use App\Billing\PaymentGateway;
use App\Facades\InventoryCode;
use App\Billing\FakePaymentGateway;
use App\Mail\OrderConfirmationEmail;
use App\Facades\OrderConfirmationNumber;
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
        Mail::fake();
        OrderConfirmationNumber::shouldReceive('generate')->andReturn('ORDERCONFIRMATION1234');
        InventoryCode::shouldReceive('generateFor')->andReturn('ITEMA', 'ITEMB');

        $bookA = factory(Book::class)->create(['price' => 1000])->addInventory(2);
        $bookB = factory(Book::class)->create(['price' => 1500])->addInventory(1);

        $fakeCart = [
            'cart.books' => [
                ['book_id' => $bookA->id, 'quantity' => 2],
                ['book_id' => $bookB->id, 'quantity' => 1],
            ]
        ];

        $response = $this->withSession($fakeCart)->json('POST', 'orders', [
            'payment_token' => $this->fakePaymentGateway->validTestToken($this->fakePaymentGateway::TEST_CARD_NUMBER),
            'email' =>  'john@example.com'
        ]);

        $response->assertJson([
            'confirmation_number' => 'ORDERCONFIRMATION1234',
            'email' => 'john@example.com',
            'amount' => 3500,
            'inventory_items' => [
                ['code' => 'ITEMA'],
                ['code' => 'ITEMB']
            ]
        ]);

        $this->assertEquals(3500, $this->fakePaymentGateway->totalCharges());
        $order = Order::where('email', 'john@example.com')->first();
        $this->assertEquals(3500, $order->amount);
        $this->assertEquals(3, $order->inventoryItems()->count());

        Mail::assertSent(OrderConfirmationEmail::class, function($mail) use ($order) {
            return $mail->hasTo('john@example.com') && $mail->order->id == $order->id;
        });
    }

    /** @test */
    function two_customers_cannot_purchase_the_same_inventory_item()
    {
        $book = factory(Book::class)->create(['price' => 1000])->addInventory(1);
        $fakeCart = [
            'cart.books' => [
                ['book_id' => $book->id, 'quantity' => 1]
            ]
        ];

        $this->fakePaymentGateway->beforeFirstCharge(function($paymentGateway) use ($book, $fakeCart) {

            $this->withSession($fakeCart)->post('orders', [
                'payment_token' => $paymentGateway->validTestToken(),
                'email' =>  'john@example.com'
            ]);

            $this->assertNull(Order::where('email', 'john@example.com')->first());
            $this->assertEquals(0, $paymentGateway->totalCharges());
        });

        $fakeCart = ['cart.books' => [['book_id' => $book->id, 'quantity' => 1]]];

        $response = $this->withSession($fakeCart)->post('orders', [
            'payment_token' => $this->fakePaymentGateway->validTestToken(),
            'email' =>  'jane@example.com'
        ]);

        $this->assertNotNull(Order::where('email', 'jane@example.com')->first());
        $this->assertEquals(1000, $this->fakePaymentGateway->totalCharges());
    }

    /** @test */
    function an_email_is_required()
    {
        $bookA = factory(Book::class)->create(['price' => 1000])->addInventory(2);
        $bookB = factory(Book::class)->create(['price' => 1500])->addInventory(1);

        $fakeCart = [
            'cart.books' => [
                ['book_id' => $bookA->id, 'quantity' => 2],
                ['book_id' => $bookB->id, 'quantity' => 1],
            ]
        ];

        $response = $this->withSession($fakeCart)->json('POST', 'orders', [
            'payment_token' => 'TESTTOKEN1234'
        ]);

        $response->assertStatus(422);
        $this->assertArrayHasKey('email', $response->json());
    }

    /** @test */
    function an_email_must_be_a_valid_email()
    {
        $bookA = factory(Book::class)->create(['price' => 1000])->addInventory(2);
        $bookB = factory(Book::class)->create(['price' => 1500])->addInventory(1);

        $fakeCart = [
            'cart.books' => [
                ['book_id' => $bookA->id, 'quantity' => 2],
                ['book_id' => $bookB->id, 'quantity' => 1],
            ]
        ];

        $response = $this->withSession($fakeCart)->json('POST', 'orders', [
            'payment_token' => 'TESTTOKEN1234',
            'email' => 'not-a-valid-email'
        ]);

        $response->assertStatus(422);
        $this->assertArrayHasKey('email', $response->json());
    }

    /** @test */
    function a_payment_token_is_required()
    {
        $bookA = factory(Book::class)->create(['price' => 1000])->addInventory(2);
        $bookB = factory(Book::class)->create(['price' => 1500])->addInventory(1);

        $fakeCart = [
            'cart.books' => [
                ['book_id' => $bookA->id, 'quantity' => 2],
                ['book_id' => $bookB->id, 'quantity' => 1],
            ]
        ];

        $response = $this->withSession($fakeCart)->json('POST', 'orders', [
            'email' => 'john@example.com'
        ]);

        $response->assertStatus(422);
        $this->assertArrayHasKey('payment_token', $response->json());
    }

    /** @test */
    function the_payment_token_must_be_valid()
    {
        $bookA = factory(Book::class)->create(['price' => 1000])->addInventory(2);
        $bookB = factory(Book::class)->create(['price' => 1500])->addInventory(1);

        $fakeCart = [
            'cart.books' => [
                ['book_id' => $bookA->id, 'quantity' => 2],
                ['book_id' => $bookB->id, 'quantity' => 1],
            ]
        ];

        $response = $this->withSession($fakeCart)->json('POST', 'orders', [
            'email' => 'john@example.com',
            'payment_token' => 'NOTVALIDTOKEN'
        ]);

        $response->assertStatus(422);
        $this->assertArrayHasKey('payment_token', $response->json());
    }

    /** @test */
    function session_must_have_items_to_purchase()
    {
        $response = $this->json('POST', 'orders', [
            'email' => 'john@example.com',
            'payment_token' => $this->fakePaymentGateway->validTestToken()
        ]);

        $response->assertStatus(422);
        $this->assertArrayHasKey('cart', $response->json());
    }

    /** @test */
    function a_customer_cannot_purchase_more_books_than_exist_in_inventory()
    {
        $book = factory(Book::class)->create()->addInventory(1);

        $fakeCart = ['cart.books' => [['book_id' => $book->id, 'quantity' => 2]]];

        $response = $this->withSession($fakeCart)->post('orders', [
            'email' => 'john@example.com',
            'payment_token' => $this->fakePaymentGateway->validTestToken()
        ]);

        $this->assertEquals(0, $this->fakePaymentGateway->totalCharges());
        $response->assertStatus(422);
        $this->assertArrayHasKey('inventory', $response->json());
    }
}
