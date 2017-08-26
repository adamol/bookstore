<?php

namespace Tests\Feature;

use App\Book;
use Tests\TestCase;
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
            'title' => 'A really awesome book',
            'description' => 'Lorem ipsum dolar sit amet',
            'author' => 'John Doe',
            'category' => 'fantasi'
        ]);


        $response = $this->get("books/{$book->id}");

        $response->assertSee('A really awesome book');
        $response->assertSee('Lorem ipsum dolar sit amet');
        $response->assertSee('John Doe');
        $response->assertSee('fantasi');
    }


    /** @test */
    function can_view_a_books_listing()
    {
        $bookA = Book::create([
            'title' => 'Book A',
            'description' => 'Lorem ipsum dolar sit amet',
            'author' => 'John Doe',
            'category' => 'fantasi'
        ]);
        $bookB = Book::create([
            'title' => 'Book B',
            'description' => 'Lorem ipsum dolar sit amet',
            'author' => 'Jane Doe',
            'category' => 'fantasi'
        ]);


        $response = $this->get("books");

        $response->assertSee('Book A');
        $response->assertSee('John Doe');
        $response->assertSee('Book B');
        $response->assertSee('Jane Doe');
    }

    /** @test */
    function books_can_be_filtered_by_category()
    {
        $fantasiA = Book::create([
            'title' => 'Fantasi A',
            'description' => 'Lorem ipsum dolar sit amet',
            'author' => 'John Doe',
            'category' => 'fantasi'
        ]);
        $thriller = Book::create([
            'title' => 'Some Thriller',
            'description' => 'Lorem ipsum dolar sit amet',
            'author' => 'Jane Doe',
            'category' => 'thriller'
        ]);
        $fantasiB = Book::create([
            'title' => 'Fantasi B',
            'description' => 'Lorem ipsum dolar sit amet',
            'author' => 'Jane Doe',
            'category' => 'fantasi'
        ]);


        $response = $this->get("books?category=fantasi");

        $response->assertSee('Fantasi A');
        $response->assertSee('Fantasi B');
        $response->assertDontSee('Some Thriller');
    }

    /** @test */
    function books_can_be_filtered_by_author()
    {
        $janesFirst = Book::create([
            'title' => 'Janes First',
            'description' => 'Lorem ipsum dolar sit amet',
            'author' => 'Jane Doe',
            'category' => 'fantasi'
        ]);
        $johnsBook = Book::create([
            'title' => 'Johns Book',
            'description' => 'Lorem ipsum dolar sit amet',
            'author' => 'John Doe',
            'category' => 'fantasi'
        ]);
        $janesSecond = Book::create([
            'title' => 'Janes Second',
            'description' => 'Lorem ipsum dolar sit amet',
            'author' => 'Jane Doe',
            'category' => 'fantasi'
        ]);


        $response = $this->get('books?author=jane_doe');

        $response->assertSee('Janes First');
        $response->assertSee('Janes Second');
        $response->assertDontSee('Johns Book');
    }
}
