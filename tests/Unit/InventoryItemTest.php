<?php

namespace Tests\Unit;

use App\Book;
use Tests\TestCase;
use App\InventoryItem;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class InventoryItemTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    function an_inventory_item_can_be_reserved()
    {
        $item = factory(InventoryItem::class)->create();

        $item->reserve();

        $this->assertNotNull($item->fresh()->reserved_at);
    }

    /** @test */
    function the_price_of_an_inventory_item_can_be_retrieved()
    {
        $book = factory(Book::class)->create(['price' => 1000]);
        $item = factory(InventoryItem::class)->create(['book_id' => $book->id]);

        $this->assertEquals(1000, $item->price);
    }
}
