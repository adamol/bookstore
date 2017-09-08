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
            'price'       => 1000
        ]);
        $book->authors()->attach(
            Author::create(['name' => 'John Doe'])
        );
        $book->categories()->attach(
            Category::create(['name' => 'Fantasi'])
        );
        factory(InventoryItem::class, 3)->create(['book_id' => $book->id]);


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
            'price'       => 1000
        ])->authors()->attach(Author::create(['name' => 'John Doe']));
        $bookB = Book::create([
            'title'       => 'Book B',
            'description' => 'Lorem ipsum dolar sit amet',
            'price'       => 1500
        ])->authors()->attach(Author::create(['name' => 'Jane Doe']));


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
        $fantasi->books()->attach(
            factory(Book::class)->create(['title' => 'Fantasi A'])
        );
        $thriller->books()->attach(
            factory(Book::class)->create(['title' => 'Some Thriller'])
        );
        $fantasi->books()->attach(
            factory(Book::class)->create(['title' => 'Fantasi B'])
        );

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
        $jane->books()->attach(
            factory(Book::class)->create(['title' => 'Janes First'])
        );
        $john->books()->attach(
            factory(Book::class)->create(['title' => 'Johns Book'])
        );
        $jane->books()->attach(
            factory(Book::class)->create(['title' => 'Janes Second'])
        );


        $response = $this->get('books?author=jane_doe');

        $response->assertSee('Janes First');
        $response->assertSee('Janes Second');
        $response->assertDontSee('Johns Book');
    }
}
