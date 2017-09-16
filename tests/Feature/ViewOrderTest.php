<?php

namespace Tests\Feature;

use App\Book;
use App\Order;
use App\Author;
use Tests\TestCase;
use App\InventoryItem;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ViewOrderTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    function customers_can_view_their_orders()
    {
        $order = Order::create([
            'email'               => 'john@example.com',
            'amount'              => 5000,
            'confirmation_number' => 'ORDERCONFIRMATION1234',
            'card_last_four'      => '1881'
        ]);
        $author = factory(Author::class)->create([ 'name' => 'Some Author' ]);
        $book   = factory(Book::class)->create([
            'title' => 'A Book', 'author_id' => $author->id
        ]);
        $itemA  = factory(InventoryItem::class)->create([
            'code' => 'ITEMA' , 'order_id' => $order->id, 'book_id' => $book->id
        ]);
        $itemB  = factory(InventoryItem::class)->create([
            'code' => 'ITEMB' , 'order_id' => $order->id, 'book_id' => $book->id
        ]);

        $response = $this->get('orders/ORDERCONFIRMATION1234');

        $response->assertSee('Some Author');
        $response->assertSee('A Book');
        $response->assertSee('**** **** **** 1881');
        $response->assertSee('ORDERCONFIRMATION1234');
        $response->assertSee('ITEMA');
        $response->assertSee('ITEMB');
    }
}
