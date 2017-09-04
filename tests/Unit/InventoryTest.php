<?php

namespace Tests\Unit;

use App\Book;
use App\Inventory;
use Tests\TestCase;
use App\InventoryItem;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class InventoryTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    function inventory_items_can_be_reserved_for_a_collection_of_books()
    {
        $bookA = factory(Book::class)->create()->addInventory(3);
        $bookA->quantity = 2;
        $bookB = factory(Book::class)->create()->addInventory(3);
        $bookB->quantity = 1;
        $bookC = factory(Book::class)->create()->addInventory(3);
        $bookC->quantity = 3;

        Inventory::reserveBooks(collect([$bookA, $bookB, $bookC]));

        $this->assertEquals(2, InventoryItem::where('book_id', $bookA->id)->whereNotNull('reserved_at')->count());
        $this->assertEquals(1, InventoryItem::where('book_id', $bookB->id)->whereNotNull('reserved_at')->count());
        $this->assertEquals(3, InventoryItem::where('book_id', $bookC->id)->whereNotNull('reserved_at')->count());
    }

    /** @test */
    function cannot_reserve_more_books_than_exist_in_inventory()
    {
        $book = factory(Book::class)->create()->addInventory(1);
        $book->quantity = 2;

        try {
            Inventory::reserveBooks(collect([$book]));

            $this->fail('Reserved more books than exist in inventory');
        } catch (\Exception $e) {
            $this->assertEquals(0, InventoryItem::where('book_id', $book->id)->whereNotNull('reserved_at')->count());
        }
    }

    /** @test */
    function find_for_returns_inventory_items_for_book()
    {
        $book = factory(Book::class)->create()->addInventory(2);

        $items = Inventory::findFor($book, 2);

        $this->assertEquals(2, $items->count());
    }
}
