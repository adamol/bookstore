<?php

namespace Tests\Feature;

use App\Book;
use App\Author;
use App\Category;
use Tests\TestCase;
use App\InventoryItem;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ViewBooksTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    function can_view_a_single_book()
    {
        $book = Book::create([
            'title'       => 'A really awesome book',
            'description' => 'Lorem ipsum dolar sit amet',
            'price'       => 1000,
            'author_id'   => factory(Author::class)->create(['name' => 'John Doe'])->id,
            'category_id' => factory(Category::class)->create(['name' => 'Fantasi'])->id
        ])->addInventory(3);

        $response = $this->get("books/{$book->id}");

        $response->assertSee('A really awesome book');
        $response->assertSee('Lorem ipsum dolar sit amet');
        $response->assertSee('John Doe');
        $response->assertSee('Fantasi');
        $response->assertSee('3 in stock');
        $response->assertSee('10.00£');
    }


    /** @test */
    function can_view_a_books_listing()
    {
        $bookA = Book::create([
            'title'       => 'Book A',
            'description' => 'Lorem ipsum dolar sit amet',
            'price'       => 1000,
            'author_id'   => factory(Author::class)->create(['name' => 'John Doe'])->id,
            'category_id' => factory(Category::class)->create()->id
        ]);
        $bookB = Book::create([
            'title'       => 'Book B',
            'description' => 'Lorem ipsum dolar sit amet',
            'price'       => 1500,
            'author_id'   => factory(Author::class)->create(['name' => 'Jane Doe'])->id,
            'category_id' => factory(Category::class)->create()->id
        ]);

        $response = $this->get("books");

        $response->assertSee('Book A');
        $response->assertSee('John Doe');
        $response->assertSee('10.00£');
        $response->assertSee('Book B');
        $response->assertSee('Jane Doe');
        $response->assertSee('15.00£');
    }

    /** @test */
    function books_can_be_filtered_by_category()
    {
        $fantasi  = Category::create(['name' => 'fantasi']);
        $thriller = Category::create(['name' => 'thriller']);

        factory(Book::class)->create(['title' => 'Fantasi A',     'category_id' => $fantasi->id]);
        factory(Book::class)->create(['title' => 'Some Thriller', 'category_id' => $thriller->id]);
        factory(Book::class)->create(['title' => 'Fantasi B',     'category_id' => $fantasi->id]);

        $response = $this->get("books?category=fantasi");

        $response->assertSee('Fantasi A');
        $response->assertSee('Fantasi B');
        $response->assertDontSee('Some Thriller');
    }

    /** @test */
    function books_can_be_filtered_by_author()
    {
        $jane = Author::create(['name' => 'Jane Doe']);
        $john = Author::create(['name' => 'John Doe']);

        factory(Book::class)->create(['title' => 'Janes First',  'author_id' => $jane->id]);
        factory(Book::class)->create(['title' => 'Johns Book',   'author_id' => $john->id]);
        factory(Book::class)->create(['title' => 'Janes Second', 'author_id' => $jane->id]);


        $response = $this->get('books?author=jane_doe');

        $response->assertSee('Janes First');
        $response->assertSee('Janes Second');
        $response->assertDontSee('Johns Book');
    }

    /** @test */
    function books_can_be_filtered_by_author_and_category()
    {
        $jane = Author::create(['name' => 'Jane Doe']);
        $john = Author::create(['name' => 'John Doe']);

        $fantasi  = Category::create(['name' => 'fantasi']);
        $thriller = Category::create(['name' => 'thriller']);

        factory(Book::class)->create([
            'title' => 'Janes Thriller',
            'author_id' => $jane->id,
            'category_id' => $thriller->id
        ]);
        factory(Book::class)->create([
            'title' => 'Johns Thriller',
            'author_id' => $john->id,
            'category_id' => $thriller->id
        ]);
        factory(Book::class)->create([
            'title' => 'Janes Fantasi',
            'author_id' => $jane->id,
            'category_id' => $fantasi->id
        ]);


        $response = $this->get('books?author=jane_doe&category=thriller');

        $response->assertSee('Janes Thriller');
        $response->assertDontSee('Janes Fantasi');
        $response->assertDontSee('Johns Thriller');
    }
}
