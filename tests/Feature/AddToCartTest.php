<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\ShoppingCart;
use App\Category;
use App\Author;
use App\Book;

class AddToCartTest extends testcase
{
    use DatabaseMigrations;

    /** @test */
    function a_book_can_be_added_to_the_cart()
    {
        $book = factory(Book::class)->create();
        $book->authors()->attach(factory(Author::class)->create());
        $book->categories()->attach(factory(Category::class)->create());

        $this->post('cart', [
            'book_id' => $book->id,
            'quantity' => 2
        ]);

        $cartItem = ShoppingCart::get()->first();
        $this->assertTrue($cartItem instanceof Book);
        $this->assertEquals($book->id, $cartItem->id);
        $this->assertEquals(2, $cartItem->quantity);
    }

    /** @test */
    function a_book_can_not_be_added_if_there_isnt_enough_inventory()
    {

    }
}
