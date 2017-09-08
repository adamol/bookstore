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
    public function can_format_multiple_author_names()
    {
        $book = factory(Book::class)->create();
        $book->authors()->attach(
            factory(Author::class)->create(['name' => 'John Doe'])
        );
        $book->authors()->attach(
            factory(Author::class)->create(['name' => 'Jane Doe'])
        );

        $this->assertEquals('John Doe, Jane Doe', $book->author_names);
    }

    /** @test */
    public function can_get_a_formatted_price()
    {
        $book = factory(Book::class)->create(['price' => 1000]);

        $this->assertEquals('10.00', $book->formatted_price);
    }

    /** @test */
    public function can_format_multiple_category_names()
    {
        $book = factory(Book::class)->create();
        $book->categories()->attach(
            factory(Category::class)->create(['name' => 'fantasi'])
        );
        $book->categories()->attach(
            factory(Category::class)->create(['name' => 'thriller'])
        );

        $this->assertEquals('Fantasi, Thriller', $book->category_names);
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
}
