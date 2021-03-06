<?php

namespace Tests\Unit;

use App\Book;
use Tests\TestCase;
use App\ShoppingCart;
use App\InventoryItem;
use App\Exceptions\EmptyCartException;
use App\Exceptions\NotEnoughInventoryException;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ShoppingCartTest extends TestCase
{
    use DatabaseMigrations;

    protected $cart;

    public function setUp()
    {
        parent::setUp();

        $this->cart = new ShoppingCart;
    }

    /** @test */
    function add_pushes_information_to_the_session()
    {
        $book = factory(Book::class)->create();
        factory(InventoryItem::class, 2)->create(['book_id' => $book->id]);

        $this->cart->add($book->id, 2);

        $this->assertEquals([
            'book_id' => $book->id,
            'quantity' => 2
        ], session()->get('cart.books')[0]);
    }

    /** @test */
    function trying_to_add_more_items_than_in_inventory_throws_exception()
    {
        $book = factory(Book::class)->create();
        factory(InventoryItem::class, 1)->create(['book_id' => $book->id]);

        try {
            $this->cart->add($book->id, 2);

            $this->fail('Added item to shopping cart without enough inventory');
        } catch (NotEnoughInventoryException $e) {
            $this->assertNull(session()->get('cart.books'));
        }
    }

    /** @test */
    function get_retrieves_information_from_the_session()
    {
        $book = factory(Book::class)->create();
        factory(InventoryItem::class, 1)->create(['book_id' => $book->id]);

        session()->push('cart.books', ['book_id' => 1, 'quantity' => 1]);

        $cartItem = $this->cart->get()->first();
        $this->assertEquals([
            0 => [
                'book_id' => $book->id,
                'quantity' => 1
            ]
        ], $this->cart->get()->toArray());
    }

    /** @test */
    function the_current_sessions_items_can_be_reserved_for_a_given_email()
    {
        $book = factory(Book::class)->create(['price' => 1000]);
        factory(InventoryItem::class, 2)->create(['book_id' => $book->id]);

        $this->cart->add($book->id, 2);

        $reservation = $this->cart->reserveFor('john@example.com');

        $this->assertEquals('john@example.com', $reservation->email());
        $this->assertEquals(2000, $reservation->amount());
    }

    /** @test */
    function cannot_reserve_for_an_email_with_empty_cart()
    {
        try {
            $this->cart->reserveFor('john@example.com');

            $this->fail('Was able to create a reservation without items in the cart');
        } catch (EmptyCartException $e) {

        }
    }

    /** @test */
    function cannot_reserve_more_books_than_exist_in_inventory()
    {
        $book = factory(Book::class)->create()->addInventory(1);

        session()->push('cart.books', [
            'book_id'  => $book->id,
            'quantity' => 2
        ]);

        try {
            $this->cart->reserveFor('john@example.com');

            $this->fail('Reserved more books than exist in inventory');
        } catch (NotEnoughInventoryException $e) {
            $this->assertEquals(
                0,
                InventoryItem::where('book_id', $book->id)->whereNotNull('reserved_at')->count()
            );
        }
    }

    /** @test */
    function cannot_reserve_already_reserved_item()
    {
        $book = factory(Book::class)->create();
        factory(InventoryItem::class)->create([
            'book_id' => $book->id, 'reserved_at' => \Carbon\Carbon::now()
        ]);
        session()->push('cart.books', [
            'book_id'  => $book->id,
            'quantity' => 1
        ]);

        try {
            $this->cart->reserveFor('john@example.com');

            $this->fail('Reserved more books than exist in inventory');
        } catch (NotEnoughInventoryException $e) {
            $this->assertEquals(
                0,
                InventoryItem::where('book_id', $book->id)->whereNull('reserved_at')->count()
            );
        }
    }
}
