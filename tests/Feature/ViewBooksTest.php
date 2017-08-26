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
        $response->assertSee('Fantasi');
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
        $response->assertSee('Book B');
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
}
