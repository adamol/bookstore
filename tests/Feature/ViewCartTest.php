<?php

namespace Tests\Feature;

use App\Book;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ViewCartTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    function users_see_items_in_session_when_viewing_their_cart()
    {
        $bookA = factory(Book::class)->create(['title' => 'Book A']);
        $bookB = factory(Book::class)->create(['title' => 'Book B']);
        $bookC = factory(Book::class)->create(['title' => 'Book C']);

        $response = $this->withSession([
            'cart.books' => implode(',', [$bookA, $bookB, $bookC])
        ])->get('cart');


        $response->assertSee('Book A');
        $response->assertSee('Book B');
        $response->assertSee('Book C');
    }
}
