<?php

namespace Tests\Unit;

use App\Book;
use Tests\TestCase;
use App\ShoppingCart;
use App\InventoryItem;
use App\Exceptions\NotEnoughInventory;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ShoppingCartTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    function add_pushes_information_to_the_session()
    {
        $book = factory(Book::class)->create();
        factory(InventoryItem::class, 2)->create(['book_id' => $book->id]);

        ShoppingCart::add($book->id, 2);

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
            ShoppingCart::add($book->id, 2);

            $this->fail('Added item to shopping cart without enough inventory');
        } catch (NotEnoughInventory $e) {
            $this->assertNull(session()->get('cart.books'));
        }
    }

    /** @test */
    function get_retrieves_information_from_the_session()
    {
        $book = factory(Book::class)->create();
        factory(InventoryItem::class, 1)->create(['book_id' => $book->id]);

        session()->push('cart.books', ['book_id' => 1, 'quantity' => 1]);

        $this->assertTrue(ShoppingCart::get()->first() instanceof Book);
        $this->assertEquals($book->id, ShoppingCart::get()->first()->id);
    }
}
