<?php

namespace Tests\Unit;

use App\Book;
use App\Author;
use App\Category;
use Tests\TestCase;
use App\InventoryItem;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class BookTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function can_get_a_formatted_price()
    {
        $book = factory(Book::class)->create(['price' => 1000]);

        $this->assertEquals('10.00', $book->formatted_price);
    }

    /** @test */
    function add_inventory_adds_items_to_the_inventory()
    {
        $book = factory(Book::class)->create();

        $book->addInventory(3);

        $this->assertEquals(3, $book->inventoryItems()->count());
    }

    /** @test */
    function inventory_quantity_only_shows_non_reserved_items()
    {
        $book = factory(Book::class)->create()->addInventory(3);
        InventoryItem::first()->reserve();

        $this->assertEquals(2, $book->inventory_quantity);
    }

    /** @test */
    function formatted_inventory_quantity()
    {
        $bookA = factory(Book::class)->create();

        factory(InventoryItem::class, 2)->create(['book_id' => $bookA->id]);

        $this->assertEquals('2 in stock', $bookA->formatted_inventory_quantity);

        $bookB = factory(Book::class)->create();

        $this->assertEquals('out of stock', $bookB->formatted_inventory_quantity);
    }

    /** @test */
    function multiple_categories_can_be_formatted_separated_by_comma()
    {
        $bookA = factory(Book::class)->create();
        $bookA->categories()->attach(factory(Category::class)->create(['name' => 'A']));
        $bookA->categories()->attach(factory(Category::class)->create(['name' => 'B']));

        $bookB = factory(Book::class)->create();
        $bookB->categories()->attach(factory(Category::class)->create(['name' => 'C']));
        $bookC = factory(Book::class)->create();

        $this->assertEquals('A, B', $bookA->formatted_categories);
        $this->assertEquals('C', $bookB->formatted_categories);
        $this->assertEquals('', $bookC->formatted_categories);
    }
}
